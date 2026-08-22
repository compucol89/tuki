@extends('organizer.layout')

@php
  $eventType = in_array(request()->input('type'), ['venue', 'online'], true) ? request()->input('type') : 'venue';
@endphp

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Add Event') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('organizer.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Event Management') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="{{ route('choose-event-type', ['language' => $defaultLang->code]) }}">{{ __('Choose Event Type') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Add Event') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="text-center py-4">
            <div class="event-wizard-shell__icon">
              <i class="fas fa-magic"></i>
            </div>
            <h4 class="event-wizard-shell__title">{{ __('Creá tu evento paso a paso') }}</h4>
            <p class="event-wizard-shell__text">
              {{ __('Un asistente te va a guiar por todo el proceso: portada, copy con IA, entradas, ubicación y publicación. En pocos minutos tu evento queda listo.') }}
            </p>
            <div class="event-wizard-shell__features">
              <span class="event-wizard-shell__feature"><i class="fas fa-robot"></i>{{ __('Extracción de datos con IA') }}</span>
              <span class="event-wizard-shell__feature"><i class="fas fa-pen-fancy"></i>{{ __('Copy generado con IA') }}</span>
              <span class="event-wizard-shell__feature"><i class="fas fa-ticket-alt"></i>{{ __('Entradas en un solo paso') }}</span>
              <span class="event-wizard-shell__feature"><i class="fas fa-mobile-alt"></i>{{ __('Fácil desde el celular') }}</span>
            </div>
            <button type="button" class="btn btn-primary btn-lg" id="ewOpenWizardBtn">
              <i class="fas fa-play-circle mr-1"></i>{{ __('Comenzar') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  @include('organizer.event.partials.create-wizard-modal')
@endsection

@section('style')
  @php
    $eventFormModernCss = 'assets/admin/css/event-form-modern.css';
    $eventFormModernCssVersion = is_file(public_path($eventFormModernCss)) ? '?v=' . substr(md5_file(public_path($eventFormModernCss)), 0, 12) : '';

    $eventWizardCss = 'assets/admin/css/event-wizard.css';
    $eventWizardCssVersion = is_file(public_path($eventWizardCss)) ? '?v=' . substr(md5_file(public_path($eventWizardCss)), 0, 12) : '';
  @endphp
  <link rel="stylesheet" href="{{ asset($eventFormModernCss) }}{{ $eventFormModernCssVersion }}">
  <link rel="stylesheet" href="{{ asset($eventWizardCss) }}{{ $eventWizardCssVersion }}">
@endsection

@section('script')
  @php
    $eventWizardJs = 'assets/admin/js/event-wizard.js';
    $eventWizardJsVersion = is_file(public_path($eventWizardJs)) ? '?v=' . substr(md5_file(public_path($eventWizardJs)), 0, 12) : '';
  @endphp
  <script>
    let languages = "{{ $languages }}";
    window.ewWizardConfig = {
      defaultCode: "{{ $defaultLang->code }}",
      eventType: "{{ $eventType }}",
      aiEnabled: {{ config('features.event_ai_assistant_enabled', false) ? 'true' : 'false' }},
      totalSteps: 6
    };
  </script>
  <script type="text/javascript" src="{{ asset('assets/admin/js/admin-partial.js') }}"></script>
  <script src="{{ asset('assets/admin/js/admin_dropzone.js') }}"></script>
  <script src="{{ asset($eventWizardJs) }}{{ $eventWizardJsVersion }}"></script>
  <script>
    function bindCoverAiCreateFlow() {
      const form = document.getElementById('eventForm');
      const thumbnailInput = document.querySelector('input[name="thumbnail"]');
      const emptyState = document.querySelector('[data-cover-ai-empty]');
      const readyState = document.querySelector('[data-cover-ai-ready]');
      const manualState = document.querySelector('[data-cover-ai-manual]');
      const restoreAiButton = document.querySelector('[data-cover-ai-restore]');
      const analyzeButton = document.querySelector('[data-cover-save-analyze]');
      const skipAiButton = document.querySelector('[data-cover-ai-skip]');
      const panel = document.getElementById('event-cover-ai-create');
      const statusBox = panel ? panel.querySelector('[data-create-ai-status]') : null;
      const progressPanel = panel ? panel.querySelector('[data-async-progress]') : null;
      const progressFill = panel ? panel.querySelector('[data-progress-fill]') : null;
      const progressBar = panel ? panel.querySelector('[data-progressbar]') : null;
      const results = panel ? panel.querySelector('[data-create-ai-results]') : null;
      const factsBox = panel ? panel.querySelector('[data-create-ai-facts]') : null;
      const guidanceBox = panel ? panel.querySelector('[data-create-ai-guidance]') : null;
      const summaryBox = panel ? panel.querySelector('[data-create-ai-summary]') : null;
      const applyButton = panel ? panel.querySelector('[data-create-ai-apply]') : null;
      const draftBox = panel ? panel.querySelector('[data-create-ai-draft]') : null;
      const draftTitle = panel ? panel.querySelector('[data-create-ai-draft-title]') : null;
      const draftSummary = panel ? panel.querySelector('[data-create-ai-draft-summary]') : null;
      const draftAudit = panel ? panel.querySelector('[data-create-ai-audit]') : null;
      const draftTitleOptions = panel ? panel.querySelector('[data-create-ai-title-options]') : null;
      const draftDescriptionPreview = panel ? panel.querySelector('[data-create-ai-description-preview]') : null;
      const draftPackagePreview = panel ? panel.querySelector('[data-create-ai-package-preview]') : null;
      const readinessText = panel ? panel.querySelector('[data-create-ai-readiness-text]') : null;
      const briefSummary = panel ? panel.querySelector('[data-create-ai-brief-summary]') : null;
      const briefSummaryText = panel ? panel.querySelector('[data-create-ai-brief-summary-text]') : null;
      const editBriefButton = panel ? panel.querySelector('[data-create-ai-edit-brief]') : null;
      const requiredPreferenceFields = panel ? Array.from(panel.querySelectorAll('[data-create-ai-required]')) : [];
      const preferenceFields = panel ? Array.from(panel.querySelectorAll('[name^="ai_"]')) : [];
      let active = false;
      let lastReview = null;
      let lastDraft = null;
      let progressTimer = null;
      let elapsedTimer = null;
      let startedAt = null;
      let manualMode = false;

      if (!form || !thumbnailInput) return;

      const toggleCoverState = function () {
        const hasCover = thumbnailInput.files && thumbnailInput.files.length > 0;

        if (emptyState) emptyState.classList.toggle('d-none', hasCover);
        if (readyState) readyState.classList.toggle('d-none', !hasCover);
        if (manualState) manualState.classList.toggle('d-none', !hasCover || !manualMode);
        if (panel) panel.classList.toggle('d-none', !hasCover || manualMode);
        updateAiReadiness();

      };

      thumbnailInput.addEventListener('change', toggleCoverState);
      thumbnailInput.addEventListener('change', function () {
        manualMode = false;
        lastReview = null;
        lastDraft = null;
        if (results) results.classList.add('d-none');
        if (draftBox) draftBox.classList.add('d-none');
        if (progressPanel) progressPanel.classList.add('d-none');
        expandBrief();
        setStatus('Portada lista. Completá la orientación del copy para activar la generación con IA.', 'light');
        updateAiReadiness();
      });

      if (analyzeButton) {
        analyzeButton.addEventListener('click', function (event) {
          event.preventDefault();
          analyzeTemporaryCover();
        });
      }

      if (applyButton) {
        applyButton.addEventListener('click', function () {
          applyDetectedFields();
        });
      }

      if (editBriefButton) {
        editBriefButton.addEventListener('click', function () {
          expandBrief();
          if (panel) panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
      }

      if (skipAiButton) {
        skipAiButton.addEventListener('click', function () {
          manualMode = true;
          lastReview = null;
          lastDraft = null;
          if (results) results.classList.add('d-none');
          if (draftBox) draftBox.classList.add('d-none');
          if (progressPanel) progressPanel.classList.add('d-none');
          expandBrief();
          toggleCoverState();
          setStatus('Modo manual activado. Podés completar el evento sin IA; el asistente queda disponible si querés mejorar SEO y descripción.', 'light');
        });
      }

      if (restoreAiButton) {
        restoreAiButton.addEventListener('click', function () {
          manualMode = false;
          toggleCoverState();
          setStatus('Asistente IA activado. Completá los pasos para generar una propuesta optimizada.', 'light');
          if (panel) panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
      }

      preferenceFields.forEach(function (field) {
        field.addEventListener('input', handlePreferenceChange);
        field.addEventListener('change', handlePreferenceChange);
      });

      function handlePreferenceChange() {
        expandBrief();
        if (lastReview || lastDraft) {
          lastReview = null;
          lastDraft = null;
          if (results) results.classList.add('d-none');
          if (draftBox) draftBox.classList.add('d-none');
          setStatus('Cambiaste la orientación del copy. Volvé a armar el evento con IA para usar estos datos.', 'light');
        }
        updateAiReadiness();
      }

      function analyzeTemporaryCover() {
        if (active) return;
        if (!panel || !thumbnailInput.files || !thumbnailInput.files.length) {
          setStatus('Subí una portada antes de analizarla con IA.', 'warning');
          return;
        }

        const missing = missingAiRequirements();
        if (missing.length) {
          setStatus('Completá estos datos antes de generar con IA: ' + missing.join(', ') + '.', 'warning');
          updateAiReadiness();
          return;
        }

        const file = thumbnailInput.files[0];
        const payload = buildAnalysisPayload(file);
        active = true;
        lastReview = null;
        panel.classList.remove('d-none');
        if (results) results.classList.add('d-none');
        if (draftBox) draftBox.classList.add('d-none');
        analyzeButton.disabled = true;
        analyzeButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Armando evento...';
        setStatus('Estamos leyendo la portada y preparando una propuesta editable. No recargues la página.', 'info');
        startProgress();

        $.ajax({
          url: panel.getAttribute('data-analysis-url'),
          method: 'POST',
          data: payload,
          processData: false,
          contentType: false
        }).done(function (response) {
          stopProgress();
          setProgress(100, 'Propuesta lista', 'Ya organizamos datos, copy, descripción y SEO para que puedas revisar.', 'success');
          lastReview = response.review || null;
          lastDraft = response.draft && response.draft.generated_payload ? response.draft.generated_payload : null;
          renderReview(lastReview, response.draft || null, response.draft_error || null);
          setStatus(lastDraft
            ? 'Propuesta lista. Revisá el copy, SEO y datos detectados antes de aplicar.'
            : 'Análisis listo. No se generó copy automático, pero podés usar los datos detectados como guía.', lastDraft ? 'success' : 'warning');
        }).fail(function (xhr) {
          stopProgress();
          setProgress(null, 'No se pudo analizar', errorMessage(xhr, 'No pudimos analizar la portada en este momento.'), 'danger');
          setStatus(errorMessage(xhr, 'No pudimos analizar la portada en este momento.'), 'danger');
        }).always(function () {
          active = false;
          analyzeButton.innerHTML = '<i class="fas fa-magic mr-1"></i>Armar evento con IA';
          updateAiReadiness();
        });
      }

      function missingAiRequirements() {
        const missing = [];
        if (!thumbnailInput.files || !thumbnailInput.files.length) {
          missing.push('portada');
        }
        requiredPreferenceFields.forEach(function (field) {
          if (!fieldHasValue(field)) {
            missing.push(field.getAttribute('data-create-ai-label') || 'campo obligatorio');
          }
        });
        return uniqueItems(missing);
      }

      function fieldHasValue(field) {
        if (!field) return false;
        if (field.multiple) {
          return Array.from(field.selectedOptions || []).some(function (option) {
            return $.trim(option.value || '') !== '';
          });
        }
        const value = $.trim(field.value || '');
        const minLength = parseInt(field.getAttribute('data-create-ai-min-length') || '1', 10);
        return value.length >= minLength;
      }

      function updateAiReadiness() {
        const hasCover = thumbnailInput.files && thumbnailInput.files.length > 0;
        const briefField = panel ? panel.querySelector('[data-create-ai-event-brief]') : null;
        const preferencesReady = requiredPreferenceFields.filter(function (field) {
          return !field.hasAttribute('data-create-ai-event-brief');
        }).every(fieldHasValue);
        const briefReady = fieldHasValue(briefField);
        const missing = missingAiRequirements();
        const ready = missing.length === 0;

        updateRequirementPill('cover', hasCover);
        updateRequirementPill('preferences', preferencesReady);
        updateRequirementPill('brief', briefReady);

        if (readinessText) {
          readinessText.textContent = manualMode
            ? 'Modo manual activo. Podés volver a usar IA desde el aviso de la portada.'
            : (ready
            ? 'Listo: la IA va a usar la portada, tus preferencias y tu descripción breve.'
            : 'Falta completar: ' + missing.join(', ') + '.');
        }

        if (analyzeButton) {
          analyzeButton.disabled = manualMode || active || !ready;
          if (!active) {
            analyzeButton.innerHTML = ready
              ? '<i class="fas fa-magic mr-1"></i>Armar evento con IA'
              : '<i class="fas fa-lock mr-1"></i>Completá la orientación';
          }
        }
      }

      function updateRequirementPill(key, ready) {
        const item = panel ? panel.querySelector('[data-create-ai-requirement="' + key + '"]') : null;
        if (!item) return;
        item.classList.toggle('is-ready', !!ready);
        item.classList.toggle('is-missing', !ready);
      }

      function compactBrief() {
        if (!panel) return;
        const labels = []
          .concat(selectedLabels('[data-create-ai-community]').slice(0, 2))
          .concat(selectedLabels('[data-create-ai-age-range]').slice(0, 2))
          .concat(selectedLabels('[data-create-ai-interests]').slice(0, 2));

        panel.classList.add('is-brief-compact');
        if (briefSummary) briefSummary.classList.remove('d-none');
        if (briefSummaryText) {
          briefSummaryText.textContent = labels.length
            ? 'Enfoque usado: ' + labels.join(' · ') + '.'
            : 'La IA ya usó la portada, tus preferencias y la descripción breve.';
        }
      }

      function expandBrief() {
        if (!panel) return;
        panel.classList.remove('is-brief-compact');
        if (briefSummary) briefSummary.classList.add('d-none');
      }

      function selectedLabels(selector) {
        const field = panel ? panel.querySelector(selector) : null;
        if (!field) return [];

        return Array.from(field.selectedOptions || []).map(function (option) {
          return $.trim(option.text || '');
        }).filter(Boolean);
      }

      function buildAnalysisPayload(file) {
        const payload = new FormData();
        const csrf = document.querySelector('meta[name="csrf-token"]');
        if (csrf) payload.append('_token', csrf.getAttribute('content'));
        payload.append('thumbnail', file);
        payload.append('generate_content', '1');

        form.querySelectorAll('input, textarea, select').forEach(function (field) {
          if (!field.name || field.type === 'file') return;
          if ((field.type === 'radio' || field.type === 'checkbox') && !field.checked) return;
          if (field.multiple) {
            Array.from(field.selectedOptions || []).forEach(function (option) {
              payload.append(field.name, option.value || '');
            });
            return;
          }
          payload.append(field.name, field.value || '');
        });

        return payload;
      }

      function startProgress() {
        startedAt = Date.now();
        let percent = 8;
        setProgress(percent, 'Preparando imagen', 'Validamos formato, tamaño y legibilidad de la portada.', 'info');
        clearInterval(progressTimer);
        clearInterval(elapsedTimer);

        progressTimer = setInterval(function () {
          percent = Math.min(percent + Math.floor(Math.random() * 7) + 3, 92);
          const stage = percent < 30 ? 'Leyendo textos del flyer' : (percent < 62 ? 'Detectando datos útiles' : 'Generando copy y SEO');
          const message = percent < 35
            ? 'Buscamos título, fecha, lugar, horarios, promos y datos relevantes.'
            : (percent < 70 ? 'Separamos información útil de marcas, logos y textos secundarios.' : 'Creamos una propuesta editable con descripción, tags y descripción corta para Google.');
          setProgress(percent, stage, message, 'info');
        }, 1800);

        elapsedTimer = setInterval(function () {
          const elapsed = Math.floor((Date.now() - startedAt) / 1000);
          if (progressPanel) {
            progressPanel.querySelector('[data-progress-elapsed]').textContent = 'Tiempo transcurrido: ' + formatDuration(elapsed);
          }
        }, 1000);
      }

      function stopProgress() {
        clearInterval(progressTimer);
        clearInterval(elapsedTimer);
        progressTimer = null;
        elapsedTimer = null;
      }

      function setProgress(percent, stage, message, state) {
        if (!progressPanel) return;
        progressPanel.classList.remove('d-none', 'is-success', 'is-danger', 'is-indeterminate');
        if (state === 'success') progressPanel.classList.add('is-success');
        if (state === 'danger') progressPanel.classList.add('is-danger');
        progressPanel.querySelector('[data-progress-title]').textContent = state === 'success' ? 'Evento preparado con IA' : 'Armando evento con IA';
        progressPanel.querySelector('[data-progress-stage]').textContent = stage;
        progressPanel.querySelector('[data-progress-message]').textContent = message;
        progressPanel.querySelector('[data-progress-estimate]').textContent = 'Normalmente tarda entre 30 segundos y 3 minutos.';

        if (typeof percent === 'number') {
          progressPanel.querySelector('[data-progress-percent]').textContent = Math.round(percent) + '% estimado';
          progressFill.style.width = Math.max(0, Math.min(100, percent)) + '%';
          progressBar.setAttribute('aria-valuenow', Math.round(percent));
        } else {
          progressPanel.classList.add('is-indeterminate');
          progressPanel.querySelector('[data-progress-percent]').textContent = 'Revisar';
          progressFill.style.width = '100%';
          progressBar.removeAttribute('aria-valuenow');
        }
      }

      function renderReview(review, draft, draftError) {
        const imageAnalysis = review && review.canonical_event_facts ? review.canonical_event_facts.image_analysis || {} : {};
        const facts = (imageAnalysis.extracted_fields || []).concat(imageAnalysis.sponsors || []).filter(function (field) {
          const value = $.trim(field.value || field.raw_text || '');
          const label = String(field.label || field.key || '').toLowerCase();
          return value && value !== '-' && label.indexOf('comparacion') === -1 && label.indexOf('comparación') === -1;
        });

        renderDraft(draft, draftError);
        if (summaryBox) summaryBox.textContent = lastDraft
          ? 'El asistente creó una propuesta editable con copy, descripción, palabras clave y descripción corta para Google.'
          : (draftError || imageAnalysis.summary || 'Encontramos información que puede ayudarte a completar el evento.');
        if (factsBox) {
          factsBox.innerHTML = '';
          facts.slice(0, 18).forEach(function (field) {
            const row = document.createElement('div');
            row.className = 'create-cover-ai-fact';
            row.innerHTML = '<div><strong>' + escapeHtml(field.label || field.key) + '</strong><br><small class="text-muted">' + fieldMeta(field) + '</small></div>'
              + '<div class="create-cover-ai-fact__value">' + escapeHtml(field.value || field.raw_text) + '</div>';
            factsBox.appendChild(row);
          });
          if (!facts.length) factsBox.innerHTML = '<div class="p-3 text-muted">No encontramos datos claros para aplicar automáticamente.</div>';
        }
        if (guidanceBox) renderGuidance(guidanceBox, imageAnalysis);
        if (results) results.classList.remove('d-none');
      }

      function applyDetectedFields() {
        if (!lastReview || !lastReview.canonical_event_facts) return;

        const imageAnalysis = lastReview.canonical_event_facts.image_analysis || {};
        const fields = (imageAnalysis.extracted_fields || []).concat(imageAnalysis.sponsors || []);
        const title = pickField(fields, [/titulo del evento/i, /título del evento/i, /nombre del evento/i, /event.*title/i], [/subtitulo/i, /subtítulo/i]);
        const address = pickField(fields, [/direccion/i, /dirección/i, /ubicacion/i, /ubicación/i]);
        const startTime = pickField(fields, [/horario de inicio/i, /hora de inicio/i, /^inicio$/i]);
        const endTime = pickField(fields, [/horario de cierre/i, /hora de cierre/i, /hora de fin/i, /^cierre$/i]);
        const dateValue = pickField(fields, [/fecha/i], [/promocion/i, /promoción/i]);

        if (lastDraft) {
          applyDraftFields(lastDraft);
        } else {
          setIfEmpty('input[name$="_title"]', title);
          setDescriptionIfEmpty(buildStarterDescription(imageAnalysis, title, address));
        }

        setIfEmpty('input[name$="_address"]', address);
        setIfEmpty('input[name$="_country"]', address ? 'Argentina' : '');
        setIfEmpty('input[name="start_time"]', parseTime(startTime));
        setIfEmpty('input[name="end_time"]', parseTime(endTime));
        setIfEmpty('input[name="start_date"]', parseDate(dateValue));
        setIfEmpty('input[name="end_date"]', parseDate(dateValue));
        setCategoryFromText([title, imageAnalysis.summary].concat(imageAnalysis.found_information || [], lastDraft && lastDraft.seo ? lastDraft.seo.tags || [] : []).join(' '));

        updateCreateChecklist();
        setStatus(lastDraft
          ? 'Aplicamos la propuesta seleccionada y los datos claros. Revisá y ajustá antes de guardar.'
          : 'Aplicamos los datos claros en campos vacíos. Revisalos y completá lo que falte antes de guardar.', 'success');
      }

      function renderDraft(draft, draftError) {
        if (!draftBox) return;
        lastDraft = draft && draft.generated_payload ? draft.generated_payload : lastDraft;

        if (!lastDraft) {
          draftBox.classList.add('d-none');
          if (draftError && summaryBox) summaryBox.textContent = draftError;
          return;
        }

        const content = lastDraft.content || {};
        draftBox.classList.remove('d-none');
        compactBrief();
        if (draftTitle) draftTitle.textContent = content.public_title || 'Propuesta generada';
        if (draftSummary) draftSummary.textContent = content.short_description || '';
        renderTitleOptions(content);
        renderDescriptionPreview(content);
        renderPackagePreview(lastDraft);
        if (draftAudit) {
          const needsReview = !!(draft && draft.needs_human_review);
          draftAudit.className = 'badge mb-2 mb-lg-0 ' + (needsReview ? 'badge-warning' : 'badge-success');
          draftAudit.textContent = needsReview ? 'Revisar antes de aplicar' : 'Listo para revisar';
        }
      }

      function applyDraftFields(draft) {
        const content = draft.content || {};
        const seo = draft.seo || {};
        const fields = selectedDraftFields();

        if (fields.indexOf('title') !== -1 && content.public_title) {
          setFieldValue('input[name$="_title"]', selectedTitleValue(content));
        }

        if (fields.indexOf('description') !== -1) {
          setDescriptionValue(buildDescriptionHtml(content));
        }

        if (fields.indexOf('meta_description') !== -1 && (seo.google_short_description || seo.meta_description)) {
          setFieldValue('textarea[name$="_meta_description"]', seo.google_short_description || seo.meta_description);
        }

        if (fields.indexOf('meta_keywords') !== -1) {
          const keywords = (seo.tags || []).concat(seo.secondary_keywords || []);
          setTagsValue('input[name$="_meta_keywords"]', keywords);
        }
      }

      function selectedDraftFields() {
        if (!panel) return [];
        return Array.from(panel.querySelectorAll('[data-create-ai-field]:checked')).map(function (field) {
          return field.value;
        });
      }

      function selectedTitleValue(content) {
        const selected = panel ? panel.querySelector('[data-create-ai-title-option]:checked') : null;
        return selected && selected.value ? selected.value : content.public_title;
      }

      function renderTitleOptions(content) {
        if (!draftTitleOptions) return;
        const options = uniqueItems([content.public_title].concat(content.title_options || [])).slice(0, 5);
        if (!options.length) {
          draftTitleOptions.innerHTML = '';
          return;
        }

        draftTitleOptions.innerHTML = '<div class="font-weight-bold small mb-2">Opciones de título</div>' + options.map(function (option, index) {
          return '<label class="d-block border rounded p-2 mb-2 bg-white">'
            + '<input type="radio" name="create_ai_title_option" data-create-ai-title-option value="' + escapeHtml(option) + '"' + (index === 0 ? ' checked' : '') + '> '
            + '<span>' + escapeHtml(option) + '</span>'
            + '</label>';
        }).join('');
      }

      function renderDescriptionPreview(content) {
        if (!draftDescriptionPreview) return;
        const html = buildDescriptionHtml(content);
        draftDescriptionPreview.innerHTML = html
          ? '<div class="font-weight-bold mb-2">Descripción que se aplicará</div>' + html
          : '<div class="text-muted">La IA no devolvió una descripción completa. Probá ajustar las preferencias y volver a armar el evento.</div>';
      }

      function renderPackagePreview(draft) {
        if (!draftPackagePreview) return;
        const seo = draft && draft.seo ? draft.seo : {};
        const social = draft && draft.social ? draft.social : {};
        const faq = draft && Array.isArray(draft.faq) ? draft.faq : [];
        const checklist = draft && Array.isArray(draft.review_checklist) ? draft.review_checklist : [];

        let html = '<div class="font-weight-bold mb-2">Paquete SEO, redes e IA</div>';
        if (seo.seo_title) html += '<p class="mb-1"><strong>SEO title:</strong> ' + escapeHtml(seo.seo_title) + '</p>';
        if (seo.ai_search_summary) html += '<p class="mb-1"><strong>Resumen IA (interno):</strong> ' + escapeHtml(seo.ai_search_summary) + '</p>';
        if (social.open_graph_title || social.open_graph_description) {
          html += '<p class="mb-1"><strong>Open Graph:</strong> ' + escapeHtml([social.open_graph_title, social.open_graph_description].filter(Boolean).join(' - ')) + '</p>';
        }
        if (faq.length) {
          html += '<div class="mt-2"><strong>FAQ:</strong><ul class="mb-1">' + faq.slice(0, 5).map(function (item) {
            return '<li>' + escapeHtml(item.question || '') + '</li>';
          }).join('') + '</ul></div>';
        }
        if (checklist.length) {
          html += '<div class="mt-2"><strong>Checklist humano:</strong><ul class="mb-0">' + checklist.slice(0, 8).map(function (item) {
            return '<li>' + escapeHtml(item.label || '') + ': ' + escapeHtml(item.note || '') + '</li>';
          }).join('') + '</ul></div>';
        }
        draftPackagePreview.innerHTML = html;
      }

      function setFieldValue(selector, value) {
        if (!value) return;
        const field = document.querySelector(selector);
        if (!field) return;
        field.value = value;
        field.dispatchEvent(new Event('input', { bubbles: true }));
        field.dispatchEvent(new Event('change', { bubbles: true }));
      }

      function setTagsValue(selector, values) {
        values = uniqueItems(values || []).slice(0, 14);
        if (!values.length) return;
        const field = document.querySelector(selector);
        if (!field) return;

        if ($.fn.tagsinput && $(field).data('tagsinput')) {
          $(field).tagsinput('removeAll');
          values.forEach(function (value) { $(field).tagsinput('add', value); });
        } else {
          field.value = values.join(',');
        }

        field.dispatchEvent(new Event('input', { bubbles: true }));
        field.dispatchEvent(new Event('change', { bubbles: true }));
      }

      function setIfEmpty(selector, value) {
        if (!value) return;
        const field = document.querySelector(selector);
        if (!field || $.trim(field.value || '') !== '') return;
        field.value = value;
        field.dispatchEvent(new Event('input', { bubbles: true }));
        field.dispatchEvent(new Event('change', { bubbles: true }));
      }

      function setDescriptionIfEmpty(value) {
        if (!value) return;
        const field = document.querySelector('textarea[name$="_description"]');
        if (!field || $.trim(field.value || '') !== '') return;
        const html = '<p>' + escapeHtml(value).replace(/\n/g, '<br>') + '</p>';
        setDescriptionValue(html);
      }

      function setDescriptionValue(html) {
        if (!html) return;
        const field = document.querySelector('textarea[name$="_description"]');
        if (!field) return;
        field.value = html;
        const tiny = window.tinymce || window.tinyMCE;
        const tinyEditor = tiny && field.id ? tiny.get(field.id) : null;
        if (tinyEditor) {
          tinyEditor.setContent(html);
          tinyEditor.save();
        } else if (setTinyIframeContent(field, html)) {
          field.value = html;
        } else if ($.fn.summernote && $(field).next('.note-editor').length) {
          $(field).summernote('code', html);
        }
        field.dispatchEvent(new Event('input', { bubbles: true }));
        field.dispatchEvent(new Event('change', { bubbles: true }));
      }

      function setTinyIframeContent(field, html) {
        if (!field || !field.id) return false;
        const frame = document.getElementById(field.id + '_ifr');
        const body = frame && frame.contentDocument ? frame.contentDocument.body : null;
        if (!body) return false;
        body.innerHTML = html;
        return true;
      }

      function buildDescriptionHtml(content) {
        let html = '';
        if (content.short_description) html += '<p>' + escapeHtml(content.short_description) + '</p>';
        if (content.main_description) html += '<p>' + escapeHtml(content.main_description).replace(/\n/g, '<br>') + '</p>';
        if ((content.what_you_will_experience || []).length) {
          html += '<h3>Qué vas a vivir</h3><ul>' + listHtml(content.what_you_will_experience) + '</ul>';
        }
        if ((content.important_information || []).length) {
          html += '<h3>Información importante</h3><ul>' + listHtml(content.important_information) + '</ul>';
        }
        const seo = lastDraft && lastDraft.seo ? lastDraft.seo : {};
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

      function listHtml(items) {
        return (items || []).filter(Boolean).map(function (item) {
          return '<li>' + escapeHtml(item) + '</li>';
        }).join('');
      }

      function setCategoryFromText(text) {
        const select = document.querySelector('select[name$="_category_id"]');
        if (!select || select.value) return;

        const normalizedText = normalizeText(text);
        let fallback = null;
        Array.from(select.options).forEach(function (option) {
          if (!option.value || option.disabled) return;
          const optionText = normalizeText(option.textContent || '');
          if (!fallback && /fiesta|show|concierto|musica|música|festival|rumba|reggaeton|boliche/.test(normalizedText) && /fiesta|show|concierto|musica|festival|evento/.test(optionText)) {
            fallback = option.value;
          }
          if (!select.value && optionText && normalizedText.indexOf(optionText) !== -1) {
            select.value = option.value;
          }
        });

        if (!select.value && fallback) select.value = fallback;
        if (select.value) select.dispatchEvent(new Event('change', { bubbles: true }));
      }

      function buildStarterDescription(imageAnalysis, title, address) {
        const lines = [];
        if (title) lines.push(title);
        (imageAnalysis.found_information || []).slice(0, 4).forEach(function (item) { if (item) lines.push(item); });
        (imageAnalysis.complementary_information || []).slice(0, 2).forEach(function (item) { if (item) lines.push(item); });
        if (address && !lines.join(' ').includes(address)) lines.push('Lugar: ' + address + '.');
        return lines.join('\n');
      }

      function pickField(fields, patterns, exclusions) {
        exclusions = exclusions || [];
        const field = fields.find(function (candidate) {
          const haystack = String((candidate.key || '') + ' ' + (candidate.label || '')).toLowerCase();
          const value = $.trim(candidate.value || candidate.raw_text || '');
          if (!value || value === '-') return false;
          if (exclusions.some(function (pattern) { return pattern.test(haystack); })) return false;
          return patterns.some(function (pattern) { return pattern.test(haystack); });
        });
        return field ? $.trim(field.value || field.raw_text || '') : '';
      }

      function parseDate(value) {
        value = $.trim(value || '');
        let match = value.match(/(\d{4})-(\d{2})-(\d{2})/);
        if (match) return match[1] + '-' + match[2] + '-' + match[3];
        match = value.match(/\b(\d{1,2})[\/.-](\d{1,2})[\/.-](\d{4})\b/);
        if (match) return match[3] + '-' + String(match[2]).padStart(2, '0') + '-' + String(match[1]).padStart(2, '0');
        match = value.match(/\b(\d{1,2})[\/.-](\d{1,2})[\/.-](\d{2})\b/);
        if (match) return '20' + match[3] + '-' + String(match[2]).padStart(2, '0') + '-' + String(match[1]).padStart(2, '0');

        const normalized = normalizeText(value);
        const months = {
          enero: 1,
          febrero: 2,
          marzo: 3,
          abril: 4,
          mayo: 5,
          junio: 6,
          julio: 7,
          agosto: 8,
          septiembre: 9,
          setiembre: 9,
          octubre: 10,
          noviembre: 11,
          diciembre: 12
        };
        const weekdays = {
          domingo: 0,
          lunes: 1,
          martes: 2,
          miercoles: 3,
          jueves: 4,
          viernes: 5,
          sabado: 6
        };

        match = normalized.match(/\b(\d{1,2})\s*(?:de\s*)?(enero|febrero|marzo|abril|mayo|junio|julio|agosto|septiembre|setiembre|octubre|noviembre|diciembre)(?:\s*(?:de\s*)?(\d{2,4}))?\b/);
        if (match) {
          const year = match[3] ? normalizeYear(match[3]) : inferYearForMonth(months[match[2]], parseInt(match[1], 10));
          return formatDateParts(year, months[match[2]], parseInt(match[1], 10));
        }

        const weekday = Object.keys(weekdays).find(function (dayName) {
          return normalized.indexOf(dayName) !== -1;
        });
        match = normalized.match(/\b(\d{1,2})\b/);
        if (match) {
          return inferUpcomingDay(parseInt(match[1], 10), weekday ? weekdays[weekday] : null);
        }
        return '';
      }

      function normalizeYear(value) {
        value = String(value || '');
        return value.length === 2 ? parseInt('20' + value, 10) : parseInt(value, 10);
      }

      function inferYearForMonth(month, day) {
        const today = new Date();
        const currentYear = today.getFullYear();
        const candidate = new Date(currentYear, month - 1, day);
        const startOfToday = new Date(currentYear, today.getMonth(), today.getDate());
        return candidate >= startOfToday ? currentYear : currentYear + 1;
      }

      function inferUpcomingDay(day, expectedWeekday) {
        if (!day || day < 1 || day > 31) return '';
        const today = new Date();
        const startOfToday = new Date(today.getFullYear(), today.getMonth(), today.getDate());
        for (let offset = 0; offset <= 12; offset++) {
          const candidate = new Date(today.getFullYear(), today.getMonth() + offset, day);
          if (candidate.getDate() !== day || candidate < startOfToday) continue;
          if (expectedWeekday !== null && candidate.getDay() !== expectedWeekday) continue;
          return formatDateParts(candidate.getFullYear(), candidate.getMonth() + 1, candidate.getDate());
        }
        return '';
      }

      function formatDateParts(year, month, day) {
        if (!year || !month || !day) return '';
        return String(year) + '-' + String(month).padStart(2, '0') + '-' + String(day).padStart(2, '0');
      }

      function parseTime(value) {
        value = $.trim(value || '').toLowerCase();
        const match = value.match(/\b(\d{1,2})(?::(\d{2}))?\s*(am|pm|hs|h)?\b/);
        if (!match) return '';
        let hour = parseInt(match[1], 10);
        const minute = match[2] || '00';
        const suffix = match[3] || '';
        if (suffix === 'pm' && hour < 12) hour += 12;
        if (suffix === 'am' && hour === 12) hour = 0;
        if (hour > 23) return '';
        return String(hour).padStart(2, '0') + ':' + minute;
      }

      function renderGuidance(target, imageAnalysis) {
        const items = []
          .concat(imageAnalysis.found_information || [])
          .concat(imageAnalysis.complementary_information || [])
          .concat(imageAnalysis.optional_suggestions || [])
          .concat(imageAnalysis.missing_information || [])
          .slice(0, 8);

        target.innerHTML = items.length
          ? '<div class="alert alert-info mb-0 small"><strong>Guía para completar el evento</strong><ul class="mb-0 mt-2 pl-3">' + items.map(function (item) { return '<li>' + escapeHtml(item) + '</li>'; }).join('') + '</ul></div>'
          : '';
      }

      function fieldMeta(field) {
        const confidence = Math.round((Number(field.confidence || 0)) * 100);
        const relation = String(field.category || '').toLowerCase();
        let label = 'detectado';
        if (field.needs_review || relation.indexOf('critica') !== -1 || relation.indexOf('crítica') !== -1) label = 'conviene confirmar';
        else if (relation.indexOf('compatible') !== -1) label = 'compatible';
        else if (relation.indexOf('complement') !== -1) label = 'complementa';
        else if (relation.indexOf('sponsor') !== -1 || relation.indexOf('marca') !== -1) label = 'marca visible';
        else if (relation.indexOf('coincid') !== -1) label = 'coincide';
        return (confidence > 0 ? confidence + '% · ' : '') + label;
      }

      function setStatus(message, type) {
        if (!panel || !statusBox) return;
        panel.classList.remove('d-none');
        statusBox.className = 'alert mb-3 alert-' + (type || 'light');
        if (type === 'light') statusBox.className += ' border';
        statusBox.textContent = message;
      }

      function errorMessage(xhr, fallback) {
        return (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error)) || fallback;
      }

      function escapeHtml(value) {
        return $('<div>').text(value || '').html();
      }

      function formatDuration(seconds) {
        seconds = Math.max(0, Number(seconds || 0));
        const minutes = Math.floor(seconds / 60);
        const remaining = seconds % 60;
        return minutes ? (minutes + 'm ' + String(remaining).padStart(2, '0') + 's') : (remaining + 's');
      }

      function normalizeText(value) {
        return String(value || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
      }

      function uniqueItems(items) {
        const seen = {};
        return (items || []).filter(function (item) {
          item = $.trim(item || '');
          if (!item) return false;
          const key = normalizeText(item);
          if (seen[key]) return false;
          seen[key] = true;
          return true;
        });
      }

      toggleCoverState();
    }

    bindCoverAiCreateFlow();
  </script>
@endsection

@section('variables')
  <script>
    "use strict";
    var storeUrl = "{{ route('organizer.event.imagesstore') }}";
    var removeUrl = "{{ route('organizer.event.imagermv') }}";
    var loadImgs = 0;
  </script>
@endsection
