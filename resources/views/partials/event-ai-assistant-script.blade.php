  <script>
    (function ($) {
      var root = $('#event-ai-assistant');
      if (!root.length) return;

      var csrf = $('meta[name="csrf-token"]').attr('content');
      var draftId = null;
      var lastDraft = null;
      var pollTimer = null;
      var progressTimer = null;
      var activeProcessType = null;
      var activeProgressStartedAtMs = null;
      var pendingDraftHydration = false;
      var lastHydratedDraftId = null;
      var autoStartAnalysis = new URLSearchParams(window.location.search).get('ai_action') === 'analyze_cover';
      var actionLabels = {
        analysis: '<i class="fas fa-search mr-1"></i>Analizar portada existente',
        draft: '<i class="fas fa-pen-nib mr-1"></i>Generar copy y SEO',
        apply: '<i class="fas fa-check mr-1"></i>Aplicar campos seleccionados'
      };
      var analysisInitiallyDisabled = root.find('[data-ai-action="analysis"]').is(':disabled');
      var hasCover = String(root.data('has-cover')) === '1' || !analysisInitiallyDisabled;
      var hasReview = false;
      var manualMode = false;
      var requiredBriefFields = $();

      function normalizeAiMultiselects() {
        root.find('.ai-assistant-multiselect').each(function () {
          var field = $(this);
          var selected = field.val() || [];

          if ($.fn.select2 && field.data('select2')) {
            try {
              field.select2('destroy');
            } catch (e) {
              field.next('.select2-container').remove();
            }
          }

          field.next('.select2-container').remove();
          field
            .removeClass('select2-hidden-accessible')
            .removeAttr('data-select2-id aria-hidden tabindex')
            .css({display: '', width: ''})
            .val(selected);
        });

        requiredBriefFields = root.find('[data-ai-required]');
      }

      normalizeAiMultiselects();
      setTimeout(normalizeAiMultiselects, 250);

      function setStatus(message, type) {
        var box = root.find('[data-ai-status]');
        box.removeClass('alert-light alert-info alert-success alert-warning alert-danger')
          .addClass('alert-' + (type || 'light'))
          .text(message);
      }

      function errorMessage(xhr, fallback) {
        return (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error)) || fallback;
      }

      function selectedValues(selector) {
        var value = root.find(selector).val();
        return Array.isArray(value) ? value : (value ? [value] : []);
      }

      function fieldHasValue(field) {
        var value = $(field).val();
        var minLength = parseInt($(field).attr('data-ai-min-length') || '1', 10);

        if (Array.isArray(value)) {
          return value.some(function (item) { return $.trim(item || '') !== ''; });
        }

        return $.trim(value || '').length >= minLength;
      }

      function missingBriefRequirements(includeAnalysis) {
        var missing = [];

        if (!hasCover) missing.push('portada');
        if (includeAnalysis && !hasReview) missing.push('análisis de portada');

        requiredBriefFields.each(function () {
          if (!fieldHasValue(this)) {
            missing.push($(this).attr('data-ai-label') || 'campo obligatorio');
          }
        });

        return uniqueItems(missing);
      }

      function updateRequirement(key, ready) {
        var item = root.find('[data-ai-requirement="' + key + '"]');
        item.toggleClass('is-ready', !!ready);
        item.toggleClass('is-missing', !ready);
      }

      function briefReady() {
        var ready = true;
        requiredBriefFields.each(function () {
          if (!fieldHasValue(this)) ready = false;
        });
        return ready;
      }

      function updateAiReadiness(response) {
        var contentAllowed = !response || !response.content_usage || response.content_usage.allowed;
        var canDraft = hasCover && hasReview && briefReady() && contentAllowed && !manualMode && !activeProcessType;
        var missing = missingBriefRequirements(true);

        updateRequirement('cover', hasCover);
        updateRequirement('analysis', hasReview);
        updateRequirement('brief', briefReady());

        root.find('[data-ai-readiness-text]').text(manualMode
          ? 'Modo manual activo. Podés volver a usar IA si querés mejorar SEO y descripción.'
          : (canDraft
            ? 'Listo: la IA va a usar la portada, el análisis y tu brief para generar copy y SEO.'
            : (missing.length
              ? 'Falta completar: ' + missing.join(', ') + '.'
              : (response && response.content_usage && response.content_usage.message
                ? response.content_usage.message
                : 'Hay un proceso IA en curso o el copy no está disponible en este momento.'))));

        root.find('[data-ai-action="draft"]').prop('disabled', !canDraft);
      }

      function selectedLabels(selector) {
        return root.find(selector).find('option:selected').map(function () {
          return $.trim($(this).text() || '');
        }).get().filter(Boolean);
      }

      function compactBrief() {
        var summary = []
          .concat(selectedLabels('[data-ai-community]').slice(0, 2))
          .concat(selectedLabels('[data-ai-age-range]').slice(0, 2))
          .concat(selectedLabels('[data-ai-interests]').slice(0, 2));

        root.addClass('is-brief-compact');
        root.find('[data-ai-brief-summary]').removeClass('d-none');
        root.find('[data-ai-brief-summary-text]').text(summary.length
          ? 'Enfoque usado: ' + summary.join(' · ') + '.'
          : 'La IA ya usó tus preferencias y la descripción breve.');
      }

      function expandBrief() {
        root.removeClass('is-brief-compact');
        root.find('[data-ai-brief-summary]').addClass('d-none');
      }

      function setManualMode(enabled) {
        manualMode = !!enabled;
        root.toggleClass('is-manual', manualMode);
        root.find('[data-ai-manual]').toggleClass('d-none', !manualMode);
        if (manualMode) {
          expandBrief();
          root.find('[data-ai-results]').addClass('d-none');
          root.find('[data-async-progress]').addClass('d-none');
          setStatus('Modo manual activado. Podés completar y guardar el evento sin usar IA.', 'light');
        } else {
          setStatus('Asistente IA activado. Analizá la portada y completá el brief para generar una propuesta.', 'light');
          if (hasReview) root.find('[data-ai-results]').removeClass('d-none');
        }
        root.find('[data-ai-action="analysis"]').prop('disabled', manualMode || analysisInitiallyDisabled);
        updateAiReadiness({});
      }

      function isRunningStatus(status) {
        return ['pending', 'running'].indexOf(status) !== -1;
      }

      function formatDuration(seconds) {
        seconds = Math.max(0, Number(seconds || 0));
        var minutes = Math.floor(seconds / 60);
        var remaining = seconds % 60;
        return minutes ? (minutes + 'm ' + String(remaining).padStart(2, '0') + 's') : (remaining + 's');
      }

      function startElapsedTimer() {
        clearInterval(progressTimer);
        progressTimer = setInterval(function () {
          if (!activeProgressStartedAtMs) return;
          var elapsed = Math.floor((Date.now() - activeProgressStartedAtMs) / 1000);
          root.find('[data-progress-elapsed]').text('Tiempo transcurrido: ' + formatDuration(elapsed));
        }, 1000);
      }

      function stopElapsedTimer() {
        clearInterval(progressTimer);
        progressTimer = null;
      }

      function renderProgress(run, type) {
        if (!run || !run.progress) return;

        var progress = run.progress;
        var status = run.status || 'running';
        var percent = progress.percent;
        var hasPercent = typeof percent === 'number' && !isNaN(percent);
        var panel = root.find('[data-async-progress]');
        var fill = panel.find('[data-progress-fill]');
        var bar = panel.find('[data-progressbar]');
        var stateClass = status === 'completed' ? 'is-success' : (status === 'failed' ? 'is-danger' : (progress.delayed ? 'is-warning' : ''));
        var message = progress.message || 'El proceso sigue activo. Normalmente tarda entre 20 segundos y 2 minutos.';

        if (progress.delayed && isRunningStatus(status)) {
          message = 'Esta tarea está tomando un poco más de tiempo, pero seguimos procesándola. No necesitás volver a iniciarla.';
        }
        if (status === 'failed' && progress.support_id) {
          message += ' Código de soporte: ' + progress.support_id + '.';
        }

        panel.removeClass('d-none is-success is-warning is-danger is-indeterminate').addClass(stateClass);
        panel.find('[data-progress-title]').text(progress.title || (type === 'draft' ? 'Generando copy y SEO' : 'Procesando'));
        panel.find('[data-progress-stage]').text(progress.stage || 'Procesando');
        panel.find('[data-progress-message]').text(message);
        panel.find('[data-progress-estimate]').text('Normalmente tarda entre 20 segundos y 2 minutos.');

        if (hasPercent) {
          percent = Math.max(0, Math.min(100, Math.round(percent)));
          panel.find('[data-progress-percent]').text(percent + '%' + (progress.is_estimated && status !== 'completed' ? ' estimado' : ''));
          fill.removeClass('progress-bar-striped progress-bar-animated').css('width', percent + '%');
          bar.attr('aria-valuenow', percent);
        } else {
          panel.addClass('is-indeterminate');
          panel.find('[data-progress-percent]').text('En curso');
          fill.addClass('progress-bar-striped progress-bar-animated').css('width', '100%');
          bar.removeAttr('aria-valuenow');
        }

        activeProgressStartedAtMs = Date.now() - (Number(progress.elapsed_seconds || 0) * 1000);
        panel.find('[data-progress-elapsed]').text('Tiempo transcurrido: ' + formatDuration(progress.elapsed_seconds || 0));

        if (isRunningStatus(status)) {
          startElapsedTimer();
        } else {
          stopElapsedTimer();
        }
      }

      function renderProgressFromStatus(response) {
        var analysis = response.analysis || null;
        var draftRun = response.draft && response.draft.run ? response.draft.run : null;
        var active = null;

        if (analysis && isRunningStatus(analysis.status)) {
          active = {type: 'analysis', run: analysis};
        } else if (draftRun && isRunningStatus(draftRun.status)) {
          active = {type: 'draft', run: draftRun};
        }

        if (active) {
          activeProcessType = active.type;
          renderProgress(active.run, active.type);
          syncProcessButtons(active.type, response);
          return true;
        }

        if (activeProcessType === 'analysis' && analysis && analysis.status === 'completed') {
          renderProgress(analysis, 'analysis');
          finishActiveProcess('analysis');
          scrollToAssistantResult('[data-ai-results]');
          return false;
        }

        if (activeProcessType === 'draft' && draftRun && draftRun.status === 'completed') {
          renderProgress(draftRun, 'draft');
          finishActiveProcess('draft');
          scrollToAssistantResult('[data-ai-draft]');
          return false;
        }

        if (analysis && analysis.status === 'failed' && (!response.review || activeProcessType === 'analysis')) {
          renderProgress(analysis, 'analysis');
          finishActiveProcess('analysis');
          syncProcessButtons(null, response);
          return false;
        }

        if (draftRun && draftRun.status === 'failed' && (activeProcessType === 'draft' || !response.draft.generated_payload)) {
          renderProgress(draftRun, 'draft');
          finishActiveProcess('draft');
          syncProcessButtons(null, response);
          return false;
        }

        syncProcessButtons(null, response);
        return false;
      }

      function finishActiveProcess(type) {
        activeProcessType = null;
        activeProgressStartedAtMs = null;
        syncProcessButtons(null, {});
      }

      function syncProcessButtons(type, response) {
        var analysisButton = root.find('[data-ai-action="analysis"]');
        var draftButton = root.find('[data-ai-action="draft"]');
        var applyButton = root.find('[data-ai-action="apply"]');
        var contentAllowed = !response.content_usage || response.content_usage.allowed;
        if (response.review) hasReview = true;
        var canDraft = hasCover && hasReview && briefReady() && contentAllowed && !manualMode;

        analysisButton.html(actionLabels.analysis).prop('disabled', analysisInitiallyDisabled || manualMode);
        draftButton.html(actionLabels.draft).prop('disabled', !canDraft);
        applyButton.html(actionLabels.apply).prop('disabled', false);

        if (type === 'analysis') {
          analysisButton.html('<i class="fas fa-spinner fa-spin mr-1"></i>Analizando portada...').prop('disabled', true);
          draftButton.prop('disabled', true);
        } else if (type === 'draft') {
          draftButton.html('<i class="fas fa-spinner fa-spin mr-1"></i>Generando copy...').prop('disabled', true);
          analysisButton.prop('disabled', true);
        } else if (type === 'apply') {
          applyButton.html('<i class="fas fa-spinner fa-spin mr-1"></i>Aplicando...').prop('disabled', true);
        }

        updateAiReadiness(response);
      }

      function scrollToAssistantResult(selector) {
        var target = root.find(selector);
        if (!target.length || target.hasClass('d-none')) return;

        $('html, body').animate({scrollTop: Math.max(target.offset().top - 90, 0)}, 450);
        target.addClass('ai-assistant-highlight');
        setTimeout(function () { target.removeClass('ai-assistant-highlight'); }, 2000);
      }

      function showLocalProgress(type, title, stage, message, percent) {
        activeProcessType = type;
        renderProgress({
          status: 'running',
          progress: {
            title: title,
            stage: stage,
            message: message,
            percent: percent,
            is_estimated: true,
            elapsed_seconds: 0,
            delayed: false
          }
        }, type);
        syncProcessButtons(type, {});
      }

      function renderUsage(analysisUsage, contentUsage) {
        if (!analysisUsage) return;
        if (analysisUsage.is_unlimited) {
          root.find('[data-ai-usage]').text(analysisUsage.message || 'Modo admin: IA sin límites para pruebas.');
          return;
        }
        var message = 'Análisis: ' + analysisUsage.remaining_event_runs + ' por evento · ' + analysisUsage.remaining_daily_runs + ' hoy';
        if (contentUsage) {
          message += ' | Copy: ' + contentUsage.remaining_event_runs + ' por evento · ' + contentUsage.remaining_daily_runs + ' hoy';
        }
        root.find('[data-ai-usage]').text(message);
      }

      function renderFacts(review) {
        var imageAnalysis = (((review || {}).canonical_event_facts || {}).image_analysis || {});
        var facts = imageAnalysis.extracted_fields || [];
        var sponsors = imageAnalysis.sponsors || [];
        var box = root.find('[data-ai-facts]');
        var guidance = root.find('[data-ai-guidance]');
        box.empty();
        guidance.empty();

        var visibleFields = facts.concat(sponsors).filter(function (field) {
          var value = $.trim(field.value || field.raw_text || '');
          var label = String(field.label || field.key || '').toLowerCase();
          return value && value !== '-' && label.indexOf('comparación') === -1 && label.indexOf('comparacion') === -1;
        });

        visibleFields.slice(0, 24).forEach(function (field) {
          $('<div class="ai-assistant-fact"></div>')
            .append('<div><strong>' + escapeHtml(field.label || field.key) + '</strong><br><small class="text-muted">' + fieldMeta(field) + '</small></div>')
            .append('<div class="ai-assistant-fact__value">' + escapeHtml(field.value || field.raw_text) + '</div>')
            .appendTo(box);
        });

        if (!visibleFields.length) {
          box.html('<div class="p-3 text-muted">Todavía no hay datos detectados.</div>');
        }

        renderGuidance(guidance, imageAnalysis);
      }

      function fieldMeta(field) {
        var confidence = Math.round((Number(field.confidence || 0)) * 100);
        var relation = String(field.category || '').toLowerCase();
        var label = 'detectado';

        if (relation.indexOf('diferencia') !== -1 || relation.indexOf('critica') !== -1 || relation.indexOf('crítica') !== -1 || field.needs_review) {
          label = 'conviene confirmar';
        } else if (relation.indexOf('compatible') !== -1) {
          label = 'compatible';
        } else if (relation.indexOf('complement') !== -1) {
          label = 'complementa';
        } else if (relation.indexOf('sponsor') !== -1 || relation.indexOf('marca') !== -1) {
          label = 'marca visible';
        } else if (relation.indexOf('coincid') !== -1) {
          label = 'coincide';
        }

        return (confidence > 0 ? confidence + '% · ' : '') + label;
      }

      function renderGuidance(target, imageAnalysis) {
        var found = imageAnalysis.found_information || [];
        var complementary = imageAnalysis.complementary_information || [];
        var suggestions = (imageAnalysis.optional_suggestions || []).concat(imageAnalysis.missing_information || [], imageAnalysis.warnings || []);
        var critical = (imageAnalysis.critical_differences || []).concat(imageAnalysis.conflicts || []);
        var html = '';

        html += guidanceSection('Información encontrada', found);
        html += guidanceSection('Información que complementa', complementary);
        html += guidanceSection('Sugerencias opcionales', suggestions);
        html += guidanceSection('Datos sensibles para confirmar', critical);

        if (!html) return;

        target.html(
          '<div class="alert alert-info mb-0 small"><strong>Información y sugerencias de la IA</strong>' + html + '</div>'
        );
      }

      function guidanceSection(title, items) {
        items = uniqueItems(items || []).slice(0, 5);
        if (!items.length) return '';

        return '<div class="mt-2"><span class="font-weight-bold">' + escapeHtml(title) + '</span><ul class="mb-0 pl-3">' +
          items.map(function (item) { return '<li>' + escapeHtml(item) + '</li>'; }).join('') +
          '</ul></div>';
      }

      function uniqueItems(items) {
        var seen = {};
        return items.filter(function (item) {
          if (!item) return false;
          var key = String(item).toLowerCase();
          if (seen[key]) return false;
          seen[key] = true;
          return true;
        });
      }

      function renderDraft(draft) {
        if (!draft || draft.status !== 'completed' || !draft.generated_payload) {
          return;
        }

        draftId = draft.id;
        lastDraft = draft.generated_payload;
        root.find('[data-ai-draft-title]').text(draft.generated_payload.content.public_title || 'Copy generado');
        root.find('[data-ai-draft-summary]').text(draft.generated_payload.content.short_description || '');
        root.find('[data-ai-audit]')
          .removeClass('badge-light badge-warning badge-success')
          .addClass(draft.needs_human_review ? 'badge-warning' : 'badge-success')
          .text(draft.needs_human_review ? 'Conviene revisar' : 'Listo para aplicar');
        root.find('[data-ai-results]').removeClass('d-none');
        root.find('[data-ai-draft]').removeClass('d-none');
        compactBrief();

        if (pendingDraftHydration && draft.id && lastHydratedDraftId !== draft.id) {
          hydrateFormFromDraft(selectedDraftFields());
          lastHydratedDraftId = draft.id;
          pendingDraftHydration = false;
          setStatus('Copy generado y copiado al formulario. Revisá los campos y guardá el evento cuando estés conforme.', 'success');
        }
      }

      function selectedDraftFields() {
        return root.find('[data-ai-field]:checked').map(function () { return this.value; }).get();
      }

      function hydrateFormFromDraft(fields) {
        if (!lastDraft) return;
        var content = lastDraft.content || {};
        var seo = lastDraft.seo || {};

        if (fields.indexOf('title') !== -1 && content.public_title) {
          $('input[name$="_title"]').first().val(content.public_title);
        }
        if (fields.indexOf('description') !== -1) {
          var description = buildDescriptionHtml(content);
          var descriptionField = $('textarea[name$="_description"]').first();
          descriptionField.val(description);
          var editorId = descriptionField.attr('id');
          var tiny = window.tinymce || window.tinyMCE;
          var tinyEditor = tiny && editorId ? tiny.get(editorId) : null;
          if (tinyEditor) {
            tinyEditor.setContent(description);
            tinyEditor.save();
          } else if (setTinyIframeContent(descriptionField.get(0), description)) {
            descriptionField.val(description);
          } else if ($.fn.summernote && descriptionField.next('.note-editor').length) {
            descriptionField.summernote('code', description);
          }
        }
        if (fields.indexOf('meta_description') !== -1 && (seo.google_short_description || seo.meta_description)) {
          $('textarea[name$="_meta_description"]').first().val(seo.google_short_description || seo.meta_description);
        }
        if (fields.indexOf('meta_keywords') !== -1) {
          var keywords = (seo.tags || []).concat(seo.secondary_keywords || []);
          if (keywords.length) {
            $('input[name$="_meta_keywords"]').first().val(keywords.join(','));
          }
        }
      }

      function escapeHtml(value) {
        return $('<div>').text(value || '').html();
      }

      function listHtml(items) {
        return (items || []).filter(Boolean).map(function (item) {
          return '<li>' + escapeHtml(item) + '</li>';
        }).join('');
      }

      function buildDescriptionHtml(content) {
        var html = '';
        if (content.short_description) html += '<p>' + escapeHtml(content.short_description) + '</p>';
        if (content.main_description) html += '<p>' + escapeHtml(content.main_description).replace(/\n/g, '<br>') + '</p>';
        if ((content.what_you_will_experience || []).length) {
          html += '<h3>Qué vas a vivir</h3><ul>' + listHtml(content.what_you_will_experience) + '</ul>';
        }
        if ((content.important_information || []).length) {
          html += '<h3>Información importante</h3><ul>' + listHtml(content.important_information) + '</ul>';
        }
        var seo = lastDraft && lastDraft.seo ? lastDraft.seo : {};
        if (seo.ai_search_summary) {
          html += '<h3>Resumen para buscadores e IA</h3><p>' + escapeHtml(seo.ai_search_summary).replace(/\n/g, '<br>') + '</p>';
        }
        if (lastDraft && Array.isArray(lastDraft.faq) && lastDraft.faq.length) {
          html += '<h3>Preguntas frecuentes</h3>' + lastDraft.faq.filter(function (item) {
            return item && item.question && item.answer;
          }).map(function (item) {
            return '<h4>' + escapeHtml(item.question) + '</h4><p>' + escapeHtml(item.answer).replace(/\n/g, '<br>') + '</p>';
          }).join('');
        }
        if (content.cta) html += '<p><strong>' + escapeHtml(content.cta) + '</strong></p>';
        return html;
      }

      function setTinyIframeContent(field, html) {
        if (!field || !field.id) return false;
        var frame = document.getElementById(field.id + '_ifr');
        var body = frame && frame.contentDocument ? frame.contentDocument.body : null;
        if (!body) return false;
        body.innerHTML = html;
        return true;
      }

      function loadStatus(scheduleNext) {
        $.get(root.data('status-url')).done(function (response) {
          renderUsage(response.usage, response.content_usage);
          hasReview = !!response.review;
          renderFacts(response.review);
          renderDraft(response.draft);
          var processActive = renderProgressFromStatus(response);
          var analysisStatus = response.analysis ? response.analysis.status : null;

          if (response.review) {
            root.find('[data-ai-results]').removeClass('d-none');
            var canGenerateDraft = hasCover && hasReview && briefReady() && (!response.content_usage || response.content_usage.allowed) && !processActive && !manualMode;
            root.find('[data-ai-action="draft"]').prop('disabled', !canGenerateDraft);
            root.find('[data-ai-draft-help]').text(canGenerateDraft
              ? 'Usá la generación cuando quieras completar descripción, SEO, tags y textos sociales.'
              : ((response.content_usage && response.content_usage.message) || 'Completá el brief para activar la generación de copy y SEO.'));
          }
          updateAiReadiness(response);

          if (response.analysis && ['pending', 'running'].indexOf(response.analysis.status) !== -1) {
            setStatus('Analizando portada y datos del evento...', 'info');
          } else if (response.analysis && response.analysis.status === 'failed') {
            setStatus(response.analysis.error_message || 'No se pudo analizar la portada.', 'danger');
          } else if (response.draft && ['pending', 'running'].indexOf(response.draft.status) !== -1) {
            setStatus('Generando copy, SEO y sugerencias...', 'info');
          } else if (response.draft && response.draft.status === 'failed') {
            setStatus('No se pudo generar el copy. Podés seguir editando manualmente.', 'danger');
          } else if (response.draft && response.draft.status === 'completed') {
            setStatus(lastHydratedDraftId === response.draft.id
              ? 'Copy generado y copiado al formulario. Revisá los campos y guardá el evento cuando estés conforme.'
              : 'Copy listo para revisar y aplicar.', response.draft.needs_human_review ? 'warning' : 'success');
          } else if (response.review) {
            setStatus('Análisis listo. Mirá los datos detectados y ajustá las preferencias antes de generar el copy.', 'success');
          }

          if (scheduleNext && processActive) {
            clearTimeout(pollTimer);
            pollTimer = setTimeout(function () { loadStatus(true); }, 3000);
          }

          if (autoStartAnalysis && !processActive && !response.review && ['pending', 'running'].indexOf(analysisStatus) === -1) {
            autoStartAnalysis = false;
            if (window.history && window.history.replaceState) {
              var cleanUrl = new URL(window.location.href);
              cleanUrl.searchParams.delete('ai_action');
              window.history.replaceState({}, document.title, cleanUrl.pathname + cleanUrl.search + cleanUrl.hash);
            }
            root.find('[data-ai-action="analysis"]:not(:disabled)').trigger('click');
          }
        }).fail(function (xhr) {
          setStatus(errorMessage(xhr, 'No pudimos consultar el estado del proceso IA.'), 'danger');
          if (activeProcessType) {
            root.find('[data-progress-message]').text('Perdimos conexión momentáneamente. No vuelvas a iniciar el proceso; intentaremos recuperar el estado.');
            clearTimeout(pollTimer);
            pollTimer = setTimeout(function () { loadStatus(true); }, 5000);
          }
        });
      }

      requiredBriefFields.on('input change', function () {
        expandBrief();
        updateAiReadiness({});
      });

      root.on('click', '[data-ai-skip]', function () {
        setManualMode(true);
      });

      root.on('click', '[data-ai-restore]', function () {
        setManualMode(false);
      });

      root.on('click', '[data-ai-edit-brief]', function () {
        expandBrief();
        updateAiReadiness({});
        scrollToAssistantResult('[data-ai-step="brief"]');
      });

      root.on('click', '[data-ai-action="analysis"]', function () {
        if (activeProcessType || manualMode) return;
        showLocalProgress('analysis', 'Analizando portada', 'Enviando portada al asistente IA', 'Estamos iniciando el análisis. Normalmente tarda entre 20 segundos y 2 minutos.', 0);
        setStatus('Enviando portada al asistente IA...', 'info');
        $.post(root.data('analysis-url'), {_token: csrf})
          .done(function () { loadStatus(true); })
          .fail(function (xhr) {
            renderProgress({
              status: 'failed',
              progress: {
                title: 'No se pudo iniciar el análisis',
                stage: 'Error al iniciar',
                message: errorMessage(xhr, 'No se pudo iniciar el análisis IA. Los datos del evento están seguros.'),
                percent: null,
                is_estimated: false,
                elapsed_seconds: 0,
                delayed: false
              }
            }, 'analysis');
            activeProcessType = null;
            syncProcessButtons(null, {});
            setStatus(errorMessage(xhr, 'No se pudo iniciar el análisis IA.'), 'danger');
            loadStatus(false);
          });
      });

      root.on('click', '[data-ai-action="draft"]', function () {
        if (activeProcessType || manualMode) return;
        var missing = missingBriefRequirements(true);
        if (missing.length) {
          setStatus('Completá estos pasos antes de generar copy y SEO: ' + missing.join(', ') + '.', 'warning');
          updateAiReadiness({});
          return;
        }
        pendingDraftHydration = true;
        showLocalProgress('draft', 'Generando copy y SEO', 'Preparando información', 'Estamos iniciando la generación. Normalmente tarda entre 20 segundos y 2 minutos.', 0);
        setStatus('Preparando generación de copy...', 'info');
        $.post(root.data('draft-url'), {
          _token: csrf,
          tone: root.find('[data-ai-tone]').val(),
          intensity: root.find('[data-ai-intensity]').val(),
          audience: {
            event_brief: root.find('[data-ai-audience]').val(),
            locations: selectedValues('[data-ai-audience-location]'),
            communities: selectedValues('[data-ai-community]'),
            age_ranges: selectedValues('[data-ai-age-range]'),
            interests: selectedValues('[data-ai-interests]'),
            language_style: root.find('[data-ai-language-style]').val(),
            description: root.find('[data-ai-audience]').val(),
            goal: root.find('[data-ai-goal]').val(),
            selling_angle: root.find('[data-ai-selling-angle]').val(),
            organizer_notes: root.find('[data-ai-notes]').val()
          }
        })
          .done(function () { loadStatus(true); })
          .fail(function (xhr) {
            renderProgress({
              status: 'failed',
              progress: {
                title: 'No se pudo iniciar el copy',
                stage: 'Error al iniciar',
                message: errorMessage(xhr, 'No se pudo generar el copy IA. Los datos del evento están seguros.'),
                percent: null,
                is_estimated: false,
                elapsed_seconds: 0,
                delayed: false
              }
            }, 'draft');
            activeProcessType = null;
            pendingDraftHydration = false;
            syncProcessButtons(null, {});
            setStatus(errorMessage(xhr, 'No se pudo generar el copy IA.'), 'danger');
            loadStatus(false);
          });
      });

      root.on('click', '[data-ai-action="apply"]', function () {
        if (!draftId || activeProcessType) return;
        var fields = selectedDraftFields();
        var url = root.data('apply-url').replace('__DRAFT__', draftId);

        showLocalProgress('apply', 'Aplicando contenido', 'Actualizando campos seleccionados', 'Estamos pasando el copy generado al formulario del evento.', 25);
        $.post(url, {_token: csrf, fields: fields})
          .done(function () {
            hydrateFormFromDraft(fields);
            renderProgress({
              status: 'completed',
              progress: {
                title: 'Contenido aplicado',
                stage: 'Completado',
                message: 'Campos aplicados. Revisalos y guardá el evento cuando estés conforme.',
                percent: 100,
                is_estimated: false,
                elapsed_seconds: 0,
                delayed: false
              }
            }, 'apply');
            activeProcessType = null;
            syncProcessButtons(null, {});
            setStatus('Campos aplicados. Revisalos y guardá el evento cuando estés conforme.', 'success');
          })
          .fail(function (xhr) {
            renderProgress({
              status: 'failed',
              progress: {
                title: 'No se pudo aplicar',
                stage: 'Error al aplicar',
                message: 'Los datos del evento están seguros. Podés intentarlo nuevamente.',
                percent: null,
                is_estimated: false,
                elapsed_seconds: 0,
                delayed: false
              }
            }, 'apply');
            activeProcessType = null;
            syncProcessButtons(null, {});
            setStatus(errorMessage(xhr, 'No se pudieron aplicar los campos.'), 'danger');
          });
      });

      $(window).on('beforeunload', function (event) {
        if (!activeProcessType) return undefined;
        event.preventDefault();
        event.returnValue = '';
        return '';
      });

      updateAiReadiness({});
      loadStatus(true);
    })(jQuery);
  </script>
