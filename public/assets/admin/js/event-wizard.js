/* =========================================================
   TukiPass · Event Creation Wizard
   Stepper, gates, IA (extracción de flyer), auto-copy idiomas
   ========================================================= */
(function ($) {
  'use strict';

  var cfg = window.ewWizardConfig || {
    defaultCode: 'es',
    eventType: 'venue',
    aiEnabled: true,
    totalSteps: 6
  };

  var currentStep = 1;
  var maxVisited = 1;
  var step4Initialized = false;
  var extractState = { lastFacts: null, busy: false };
  var stepLabels = {
    1: 'Portada',
    2: 'Copy con IA',
    3: 'Entradas',
    4: cfg.eventType === 'venue' ? 'Ubicación y fotos' : 'Fotos',
    5: 'Publicar',
    6: 'Avanzado'
  };

  function byId(id) { return document.getElementById(id); }
  function qs(sel, root) { return (root || document).querySelector(sel); }
  function qsa(sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); }
  function escapeHtml(value) { return $('<div>').text(value || '').html(); }
  function stripTags(value) { return String(value || '').replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim(); }

  (function resolveCurrency() {
    var form = qs('#eventForm');
    if (form && form.dataset && form.dataset.currency) {
      cfg.currency = form.dataset.currency;
    }
  })();

  /* ---------- TinyMCE helpers ---------- */
  function tinyFor(field) {
    var tiny = window.tinymce || window.tinyMCE;
    return (tiny && field && field.id) ? tiny.get(field.id) : null;
  }

  function readFieldValue(field) {
    if (!field) return '';
    var editor = tinyFor(field);
    if (editor) return editor.getContent() || '';
    return field.value || '';
  }

  function writeFieldValue(field, value) {
    if (!field) return;
    var editor = tinyFor(field);
    if (editor) {
      editor.setContent(value || '');
      editor.save();
    } else {
      field.value = value || '';
    }
    field.dispatchEvent(new Event('input', { bubbles: true }));
    field.dispatchEvent(new Event('change', { bubbles: true }));
  }

  /* ---------- Estado ---------- */
  function isDirty() {
    var thumbnail = qs('input[name="thumbnail"]');
    var title = qs('input[name$="_title"]');
    var startDate = qs('input[name="start_date"]');
    return (thumbnail && thumbnail.files && thumbnail.files.length > 0)
      || (title && String(title.value || '').trim().length > 0)
      || (startDate && String(startDate.value || '').trim().length > 0);
  }

  /* ---------- Gates por paso ---------- */
  function gateStep1() {
    var errors = [];
    var thumbnail = qs('input[name="thumbnail"]');
    var title = qs('input[name$="_title"]');
    var category = qs('select[name$="_category_id"]');

    if (!thumbnail || !thumbnail.files || !thumbnail.files.length) {
      errors.push({ selector: 'input[name="thumbnail"]', message: 'Subí la imagen de portada para continuar.' });
    }
    if (!title || String(title.value || '').trim().length < 3) {
      errors.push({ selector: 'input[name$="_title"]', message: 'Escribí el título del evento.' });
    }
    if (!category || !category.value) {
      errors.push({ selector: 'select[name$="_category_id"]', message: 'Elegí una categoría.' });
    }

    var isSingle = (qs('input[name="date_type"]:checked') || {}).value !== 'multiple';
    if (isSingle) {
      ['start_date', 'start_time', 'end_date', 'end_time'].forEach(function (name) {
        var f = qs('input[name="' + name + '"]');
        if (!f || !f.value) {
          errors.push({ selector: 'input[name="' + name + '"]', message: 'Completá fecha y horario de inicio y fin.' });
        }
      });
    } else {
      var inputs = qsa('input[name="m_start_date[]"]');
      var ok = inputs.some(function (input, i) {
        return input.value
          && qsa('input[name="m_start_time[]"]')[i] && qsa('input[name="m_start_time[]"]')[i].value
          && qsa('input[name="m_end_date[]"]')[i] && qsa('input[name="m_end_date[]"]')[i].value
          && qsa('input[name="m_end_time[]"]')[i] && qsa('input[name="m_end_time[]"]')[i].value;
      });
      if (!ok) {
        errors.push({ selector: '#multiple_dates', message: 'Completá al menos una fecha del evento.' });
      }
    }
    return errors;
  }

  function descriptionPlainText() {
    var field = qs('textarea[name$="_description"]');
    if (!field) return '';
    return stripTags(readFieldValue(field));
  }

  function gateStep2() {
    var errors = [];
    if (descriptionPlainText().length < 30) {
      errors.push({
        selector: 'textarea[name$="_description"]',
        message: 'La descripción necesita al menos 30 caracteres. Generala con IA o escribila vos.'
      });
    }
    return errors;
  }

  function gateStep3() {
    var errors = [];
    var free = byId('free_ticket');
    var price = qs('input[name="price"]');

    if (cfg.eventType === 'online') {
      var meeting = qs('input[name="meeting_url"]');
      var isFree = free && free.checked;
      if (!isFree && (!price || !price.value || Number(price.value) <= 0)) {
        errors.push({ selector: 'input[name="price"]', message: 'Ingresá el precio de la entrada o marcá «Este evento es gratuito».' });
      }
      if (!meeting || String(meeting.value || '').trim() === '') {
        errors.push({ selector: 'input[name="meeting_url"]', message: 'Ingresá el enlace de acceso o meeting URL.' });
      }
    } else {
      var toggle = byId('ewVenueTicketToggle');
      var ticketsOn = !toggle || toggle.checked;
      if (ticketsOn) {
        var isFreeVenue = free && free.checked;
        if (!isFreeVenue && (!price || !price.value || Number(price.value) <= 0)) {
          errors.push({ selector: 'input[name="price"]', message: 'Ingresá el precio de la entrada, marcala como gratuita o desactivá la venta de entradas.' });
        }
      }
    }
    return errors;
  }

  function gateStep4() {
    var errors = [];
    if (cfg.eventType === 'venue') {
      ['_address', '_country', '_city'].forEach(function (suffix) {
        var f = qs('input[name="' + cfg.defaultCode + suffix + '"]');
        if (!f || !String(f.value || '').trim()) {
          errors.push({
            selector: 'input[name="' + cfg.defaultCode + suffix + '"]',
            message: suffix === '_address' ? 'Completá la dirección del evento.'
              : (suffix === '_country' ? 'Completá el país del evento.' : 'Completá la ciudad del evento.')
          });
        }
      });
    }
    return errors;
  }

  function gateFor(step) {
    if (step === 1) return gateStep1();
    if (step === 2) return gateStep2();
    if (step === 3) return gateStep3();
    if (step === 4) return gateStep4();
    return [];
  }

  /* ---------- Errores inline ---------- */
  function clearFieldErrors() {
    qsa('.event-wizard__field-error').forEach(function (el) { el.remove(); });
    qsa('.is-invalid').forEach(function (el) {
      el.classList.remove('is-invalid');
      el.removeAttribute('aria-invalid');
      el.removeAttribute('aria-describedby');
    });
  }

  function showFieldErrors(errors) {
    clearFieldErrors();
    errors.forEach(function (error) {
      var field = qs(error.selector);
      if (!field) return;
      var target = field.closest('.form-group') || field.parentElement;
      if (target && !target.querySelector('.event-wizard__field-error')) {
        var msg = document.createElement('span');
        msg.className = 'event-wizard__field-error';
        msg.setAttribute('role', 'alert');
        msg.textContent = error.message;
        target.appendChild(msg);
      }
      if (field.classList) {
        field.classList.add('is-invalid');
        field.setAttribute('aria-invalid', 'true');
        var errMsg = target ? target.querySelector('.event-wizard__field-error') : null;
        if (errMsg && !errMsg.id) errMsg.id = 'ew-field-error-' + Math.random().toString(36).slice(2, 9);
        if (errMsg) field.setAttribute('aria-describedby', errMsg.id);
      }
    });
  }

  /* ---------- Navegación ---------- */
  function refreshFooter() {
    var back = byId('ewBackBtn');
    var next = byId('ewNextBtn');
    var skip = byId('ewSkipAdvancedBtn');
    var submit = byId('EventSubmit');
    var hint = byId('ewStepHint');

    if (back) back.classList.toggle('d-none', currentStep === 1);
    if (skip) skip.classList.toggle('d-none', currentStep !== 6);
    if (next) {
      next.classList.toggle('d-none', currentStep >= 5);
      next.innerHTML = currentStep === 4
        ? 'Revisar y publicar<i class="fas fa-arrow-right ml-1"></i>'
        : 'Continuar<i class="fas fa-arrow-right ml-1"></i>';
    }
    if (submit) submit.classList.toggle('d-none', currentStep !== 5);
    if (hint) hint.textContent = 'Paso ' + currentStep + ' de ' + cfg.totalSteps;
  }

  function refreshStepper() {
    qsa('[data-wizard-step]').forEach(function (item) {
      var step = parseInt(item.getAttribute('data-wizard-step'), 10);
      item.classList.toggle('is-active', step === currentStep);
      item.classList.toggle('is-done', step < currentStep);
      var btn = qs('[data-wizard-go="' + step + '"]');
      if (btn) {
        btn.disabled = step > maxVisited && !(step === 6 && currentStep === 5);
        if (step === currentStep) {
          btn.setAttribute('aria-current', 'step');
        } else {
          btn.removeAttribute('aria-current');
        }
      }
    });
  }

  function refreshStepPanels() {
    qsa('[data-wizard-panel]').forEach(function (panel) {
      panel.classList.toggle('is-active', parseInt(panel.getAttribute('data-wizard-panel'), 10) === currentStep);
    });
  }

  function scrollBodyTop() {
    var body = qs('.event-wizard-modal .modal-body');
    if (body) body.scrollTop = 0;
  }

  function onEnterStep(step) {
    if (step === 4 && !step4Initialized) {
      step4Initialized = true;
      initStep4Widgets();
    }
    if (step === 5) updateReview();
    refreshStepper();
    refreshStepPanels();
    refreshFooter();
    scrollBodyTop();
  }

  function goToStep(step) {
    if (step < 1 || step > cfg.totalSteps) return;
    currentStep = step;
    maxVisited = Math.max(maxVisited, step);
    onEnterStep(step);
  }

  function advance() {
    var errors = gateFor(currentStep);
    if (errors.length) {
      showFieldErrors(errors);
      return;
    }
    clearFieldErrors();
    goToStep(currentStep + 1);
  }

  /* ---------- Paso 4: dropzone + mapa ---------- */
  function initStep4Widgets() {
    // Evita dispatch de resize global (dispara listeners ajenos); el mapa y
    // el dropzone se sincronizan con el evento propio 'ew:wizard-step4'.
    document.dispatchEvent(new CustomEvent('ew:wizard-step4'));

    var el = byId('my-dropzone');
    if (el && window.Dropzone && Dropzone.forElement) {
      var dz = Dropzone.forElement(el);
      if (dz) {
        var opts = dz.options;
        try { dz.destroy(); } catch (e) { /* noop */ }
        try { new Dropzone(el, opts); } catch (e) { /* noop */ }
      }
    }
  }

  /* ---------- Paso 5: resumen ---------- */
  function updateReview() {
    var cover = qs('[data-review-cover]');
    if (cover) {
      var preview = qs('.uploaded-img');
      if (preview) cover.src = preview.src;
    }

    var title = qs('[data-review-title]');
    var titleField = qs('input[name$="_title"]');
    if (title) {
      title.textContent = titleField && titleField.value ? titleField.value : 'Sin título';
      title.classList.toggle('is-empty', !titleField || !titleField.value);
    }

    var dates = qs('[data-review-dates]');
    if (dates) {
      var isSingle = (qs('input[name="date_type"]:checked') || {}).value !== 'multiple';
      if (isSingle) {
        var sd = qs('input[name="start_date"]');
        var st = qs('input[name="start_time"]');
        var ed = qs('input[name="end_date"]');
        var et = qs('input[name="end_time"]');
        var hasDates = sd && sd.value && st && st.value && ed && ed.value && et && et.value;
        dates.textContent = hasDates
          ? formatDateEs(sd.value) + ' · ' + st.value + ' a ' + formatDateEs(ed.value) + ' · ' + et.value
          : 'Sin fecha definida';
        dates.classList.toggle('is-empty', !hasDates);
      } else {
        var rows = qsa('input[name="m_start_date[]"]').filter(function (i) { return i.value; });
        dates.textContent = rows.length ? rows.length + ' fechas programadas' : 'Sin fechas definidas';
        dates.classList.toggle('is-empty', !rows.length);
      }
    }

    var tickets = qs('[data-review-tickets]');
    if (tickets) {
      var free = byId('free_ticket');
      var price = qs('input[name="price"]');
      if (cfg.eventType === 'venue') {
        var toggle = byId('ewVenueTicketToggle');
        if (toggle && !toggle.checked) {
          tickets.textContent = 'Sin entradas (se cargan después)';
          tickets.classList.add('is-empty');
        } else if (free && free.checked) {
          tickets.textContent = 'Evento gratuito';
          tickets.classList.remove('is-empty');
        } else if (price && price.value) {
          tickets.textContent = 'Entrada general · ' + (cfg.currency || '$') + ' ' + price.value;
          tickets.classList.remove('is-empty');
        } else {
          tickets.textContent = 'Sin precio definido';
          tickets.classList.add('is-empty');
        }
      } else {
        if (free && free.checked) {
          tickets.textContent = 'Evento gratuito';
        } else if (price && price.value) {
          tickets.textContent = 'Entrada · ' + (cfg.currency || '$') + ' ' + price.value;
        } else {
          tickets.textContent = 'Sin precio definido';
        }
        tickets.classList.toggle('is-empty', !(free && free.checked) && !(price && price.value));
      }
    }

    var location = qs('[data-review-location]');
    if (location) {
      if (cfg.eventType === 'venue') {
        var address = qs('input[name="' + cfg.defaultCode + '_address"]');
        var city = qs('input[name="' + cfg.defaultCode + '_city"]');
        var country = qs('input[name="' + cfg.defaultCode + '_country"]');
        var parts = [address && address.value, city && city.value, country && country.value].filter(Boolean);
        location.textContent = parts.length ? parts.join(', ') : 'Sin ubicación definida';
        location.classList.toggle('is-empty', !parts.length);
      } else {
        var meeting = qs('input[name="meeting_url"]');
        location.textContent = meeting && meeting.value ? meeting.value : 'Sin enlace de acceso';
        location.classList.toggle('is-empty', !meeting || !meeting.value);
      }
    }

    var description = qs('[data-review-description]');
    if (description) {
      var text = descriptionPlainText();
      description.textContent = text.length ? 'Lista · ' + text.length + ' caracteres' : 'Sin descripción';
      description.classList.toggle('is-empty', !text.length);
    }
  }

  function formatDateEs(value) {
    if (!value) return '';
    var parts = value.split('-');
    if (parts.length !== 3) return value;
    return parts[2] + '/' + parts[1] + '/' + parts[0];
  }

  /* ---------- Readiness del stepper ---------- */
  function updateCreateChecklist() {
    qsa('[data-wizard-step]').forEach(function (item) {
      var step = parseInt(item.getAttribute('data-wizard-step'), 10);
      if (step >= 1 && step <= 4 && step !== currentStep) {
        item.classList.toggle('is-done', gateFor(step).length === 0);
      }
    });

    var hint = byId('createEventWizardSubtitle');
    if (hint && currentStep >= 1 && currentStep <= 4) {
      var ready = gateFor(currentStep).length === 0;
      hint.textContent = ready
        ? 'Paso «' + stepLabels[currentStep] + '» listo. Podés continuar.'
        : 'Completá lo pedido en «' + stepLabels[currentStep] + '» para continuar.';
    }
  }

  /* ---------- Extracción IA (paso 1) ---------- */
  function collectFacts(response) {
    var review = response && response.review;
    var facts = review && review.canonical_event_facts ? review.canonical_event_facts.image_analysis || {} : {};
    return (facts.extracted_fields || []).concat(facts.sponsors || []).filter(function (field) {
      var value = $.trim(field.value || field.raw_text || '');
      var label = String(field.label || field.key || '').toLowerCase();
      return value && value !== '-' && label.indexOf('comparacion') === -1 && label.indexOf('comparación') === -1;
    });
  }

  function renderFacts(facts) {
    var list = qs('[data-ew-facts-list]');
    if (!list) return;
    list.innerHTML = '';
    facts.slice(0, 18).forEach(function (field) {
      var row = document.createElement('div');
      row.className = 'ew-ai-fact';
      row.innerHTML = '<div class="ew-ai-fact__label">' + escapeHtml(field.label || field.key) + '</div>'
        + '<div class="ew-ai-fact__value">' + escapeHtml(field.value || field.raw_text) + '</div>';
      list.appendChild(row);
    });
    if (!facts.length) {
      list.innerHTML = '<div class="p-3 text-muted">No encontramos datos claros para aplicar automáticamente.</div>';
    }
  }

  function normalizeText(value) {
    return String(value || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
  }

  function pickField(fields, patterns, exclusions) {
    exclusions = exclusions || [];
    var field = (fields || []).find(function (candidate) {
      var haystack = String((candidate.key || '') + ' ' + (candidate.label || '')).toLowerCase();
      var value = $.trim(candidate.value || candidate.raw_text || '');
      if (!value || value === '-') return false;
      if (exclusions.some(function (pattern) { return pattern.test(haystack); })) return false;
      return patterns.some(function (pattern) { return pattern.test(haystack); });
    });
    return field ? $.trim(field.value || field.raw_text || '') : '';
  }

  function parseDate(value) {
    value = $.trim(value || '');
    var match = value.match(/(\d{4})-(\d{2})-(\d{2})/);
    if (match) return match[1] + '-' + match[2] + '-' + match[3];
    match = value.match(/\b(\d{1,2})[\/.-](\d{1,2})[\/.-](\d{4})\b/);
    if (match) return match[3] + '-' + String(match[2]).padStart(2, '0') + '-' + String(match[1]).padStart(2, '0');
    match = value.match(/\b(\d{1,2})[\/.-](\d{1,2})[\/.-](\d{2})\b/);
    if (match) return '20' + match[3] + '-' + String(match[2]).padStart(2, '0') + '-' + String(match[1]).padStart(2, '0');

    var normalized = normalizeText(value);
    var months = { enero: 1, febrero: 2, marzo: 3, abril: 4, mayo: 5, junio: 6, julio: 7, agosto: 8, septiembre: 9, setiembre: 9, octubre: 10, noviembre: 11, diciembre: 12 };
    match = normalized.match(/\b(\d{1,2})\s*(?:de\s*)?(enero|febrero|marzo|abril|mayo|junio|julio|agosto|septiembre|setiembre|octubre|noviembre|diciembre)(?:\s*(?:de\s*)?(\d{2,4}))?\b/);
    if (match) {
      var year = match[3] ? normalizeYear(match[3]) : inferYearForMonth(months[match[2]], parseInt(match[1], 10));
      return formatDateParts(year, months[match[2]], parseInt(match[1], 10));
    }
    return '';
  }

  function normalizeYear(value) {
    value = String(value || '');
    return value.length === 2 ? parseInt('20' + value, 10) : parseInt(value, 10);
  }

  function inferYearForMonth(month, day) {
    var today = new Date();
    var currentYear = today.getFullYear();
    var candidate = new Date(currentYear, month - 1, day);
    var startOfToday = new Date(currentYear, today.getMonth(), today.getDate());
    return candidate >= startOfToday ? currentYear : currentYear + 1;
  }

  function formatDateParts(year, month, day) {
    if (!year || !month || !day) return '';
    return String(year) + '-' + String(month).padStart(2, '0') + '-' + String(day).padStart(2, '0');
  }

  function parseTime(value) {
    value = $.trim(value || '').toLowerCase();
    var match = value.match(/\b(\d{1,2})(?::(\d{2}))?\s*(am|pm|hs|h)?\b/);
    if (!match) return '';
    var hour = parseInt(match[1], 10);
    var minute = match[2] || '00';
    var suffix = match[3] || '';
    if (suffix === 'pm' && hour < 12) hour += 12;
    if (suffix === 'am' && hour === 12) hour = 0;
    if (hour > 23) return '';
    return String(hour).padStart(2, '0') + ':' + minute;
  }

  function setIfEmpty(selector, value) {
    if (!value) return;
    var field = qs(selector);
    if (!field || $.trim(field.value || '') !== '') return;
    field.value = value;
    field.dispatchEvent(new Event('input', { bubbles: true }));
    field.dispatchEvent(new Event('change', { bubbles: true }));
  }

  function setCategoryFromText(text) {
    var select = qs('select[name$="_category_id"]');
    if (!select || select.value) return;
    var normalizedText = normalizeText(text);
    var fallback = null;
    Array.from(select.options).forEach(function (option) {
      if (!option.value || option.disabled) return;
      var optionText = normalizeText(option.textContent || '');
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

  function applyFacts(facts) {
    var title = pickField(facts, [/titulo del evento/i, /título del evento/i, /nombre del evento/i, /event.*title/i], [/subtitulo/i, /subtítulo/i]);
    var address = pickField(facts, [/direccion/i, /dirección/i, /ubicacion/i, /ubicación/i]);
    var startTime = pickField(facts, [/horario de inicio/i, /hora de inicio/i, /^inicio$/i]);
    var endTime = pickField(facts, [/horario de cierre/i, /hora de cierre/i, /hora de fin/i, /^cierre$/i]);
    var dateValue = pickField(facts, [/fecha/i], [/promocion/i, /promoción/i]);

    setIfEmpty('input[name$="_title"]', title);
    setIfEmpty('input[name$="_address"]', address);
    setIfEmpty('input[name="start_time"]', parseTime(startTime));
    setIfEmpty('input[name="end_time"]', parseTime(endTime));
    setIfEmpty('input[name="start_date"]', parseDate(dateValue));
    setIfEmpty('input[name="end_date"]', parseDate(dateValue));
    setCategoryFromText([title, (facts.find(function (f) { return /summary|resumen/i.test(String(f.key || f.label || '')); }) || {}).value || ''].join(' '));
  }

  function initWizardExtract() {
    var area = qs('[data-ew-analysis-url]');
    if (!area) return;
    var url = area.getAttribute('data-ew-analysis-url');
    var btn = byId('ewAiExtractBtn');
    var statusEl = qs('[data-ew-extract-status]');
    var factsBox = qs('[data-ew-facts]');
    var factsList = qs('[data-ew-facts-list]');
    var applyBtn = qs('[data-ew-facts-apply]');
    var thumbnail = qs('input[name="thumbnail"]');

    function refresh() {
      var has = thumbnail && thumbnail.files && thumbnail.files.length > 0;
      if (btn) btn.disabled = !has || extractState.busy;
    }

    if (thumbnail) thumbnail.addEventListener('change', refresh);
    refresh();

    if (btn) {
      btn.addEventListener('click', function () {
        if (extractState.busy || !thumbnail || !thumbnail.files.length) return;
        extractState.busy = true;
        extractState.lastFacts = null;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span data-ew-extract-label>Leyendo la imagen…</span>';
        if (statusEl) statusEl.textContent = 'Estamos leyendo el flyer para detectar título, fecha, horarios y más. Puede tardar hasta un par de minutos.';
        if (factsBox) factsBox.classList.add('d-none');

        var payload = new FormData();
        var csrf = qs('meta[name="csrf-token"]');
        if (csrf) payload.append('_token', csrf.getAttribute('content'));
        payload.append('thumbnail', thumbnail.files[0]);
        payload.append('generate_content', '0');

        $.ajax({ url: url, method: 'POST', data: payload, processData: false, contentType: false })
          .done(function (response) {
            extractState.lastFacts = collectFacts(response);
            renderFacts(extractState.lastFacts);
            if (statusEl) {
              statusEl.textContent = extractState.lastFacts.length
                ? 'Listo. Revisá los datos detectados y aplicalos con un clic.'
                : 'No encontramos datos claros en la imagen. Completalos a mano, es rápido.';
            }
            if (factsBox) factsBox.classList.remove('d-none');
          })
          .fail(function (xhr) {
            var message = (xhr.responseJSON && xhr.responseJSON.message) || 'No pudimos analizar la imagen en este momento.';
            if (statusEl) statusEl.textContent = message;
            if (factsBox) factsBox.classList.remove('d-none');
            if (factsList) factsList.innerHTML = '<div class="p-3 text-muted">' + escapeHtml(message) + '</div>';
          })
          .always(function () {
            extractState.busy = false;
            btn.innerHTML = '<i class="fas fa-robot"></i><span data-ew-extract-label>Extraer datos de la imagen con IA</span>';
            refresh();
          });
      });
    }

    if (applyBtn) {
      applyBtn.addEventListener('click', function () {
        if (!extractState.lastFacts || !extractState.lastFacts.length) return;
        applyFacts(extractState.lastFacts);
        if (statusEl) statusEl.textContent = 'Datos aplicados. Revisalos y ajustá lo que haga falta.';
        updateCreateChecklist();
      });
    }
  }

  /* ---------- Auto-copy idiomas ---------- */
  function copyCategoryByName(source, target) {
    if (!source || !target) return;
    var name = source.selectedIndex >= 0 ? source.options[source.selectedIndex].text : '';
    if (!name) return;
    for (var i = 0; i < target.options.length; i++) {
      if (target.options[i].text === name) {
        target.value = target.options[i].value;
        return;
      }
    }
    var option = document.createElement('option');
    option.value = source.value || '';
    option.text = name;
    target.appendChild(option);
    target.value = option.value;
    target.dispatchEvent(new Event('change', { bubbles: true }));
  }

  function copyLangFromDefault(code, overwrite) {
    var textFields = ['title', 'description', 'meta_keywords', 'meta_description'];
    if (cfg.eventType === 'venue') {
      textFields = textFields.concat(['address', 'country', 'state', 'city', 'zip_code']);
    }

    textFields.forEach(function (suffix) {
      var target = qs('[name="' + code + '_' + suffix + '"]');
      var source = qs('[name="' + cfg.defaultCode + '_' + suffix + '"]');
      if (!target || !source) return;

      if (suffix === 'description') {
        var sourceText = stripTags(readFieldValue(source));
        var targetText = stripTags(readFieldValue(target));
        if (!overwrite && targetText.length >= 30) return;
        if (sourceText.length >= 30) writeFieldValue(target, readFieldValue(source));
        return;
      }

      if (overwrite || !$.trim(target.value || '')) {
        target.value = source.value || '';
        target.dispatchEvent(new Event('input', { bubbles: true }));
        target.dispatchEvent(new Event('change', { bubbles: true }));
      }
    });

    copyCategoryByName(
      qs('[name="' + cfg.defaultCode + '_category_id"]'),
      qs('[name="' + code + '_category_id"]')
    );
  }

  function autoCopyLanguages() {
    var syncOn = !byId('ewLangSync') || byId('ewLangSync').checked;
    qsa('[data-ew-clone-lang]').forEach(function (button) {
      copyLangFromDefault(button.getAttribute('data-ew-clone-lang'), syncOn);
    });
  }

  /* ---------- Submit final (captura antes de admin-main.js) ---------- */
  function validateAll() {
    var collected = [];
    [1, 2, 3, 4].forEach(function (step) {
      gateFor(step).forEach(function (error) {
        collected.push({ step: step, error: error });
      });
    });
    return collected;
  }

  function mapErrorMessageToStep(text) {
    var t = String(text || '').toLowerCase();
    if (/idioma/.test(t)) return 6;
    if (/titulo|categoria|title|category/.test(t)) return 1;
    if (/descripcion|description/.test(t)) return 2;
    if (/direccion|pais|ciudad|provincia|address|country|city|state|zip|latitud|longitud|ubicacion/.test(t)) return 4;
    if (/fecha|hora|date|time|thumbnail|portada/.test(t)) return 1;
    if (/precio|entrada|gratuito|price|pricing|ticket|discount|early|meeting|enlace|buy/.test(t)) return 3;
    return 5;
  }

  function watchServerErrors() {
    var list = qs('#eventErrors ul');
    if (!list || typeof MutationObserver === 'undefined') return;
    var observer = new MutationObserver(function () {
      if (!list.children.length) return;
      var first = list.children[0];
      var step = mapErrorMessageToStep(first.textContent || '');
      goToStep(step);
      scrollBodyTop();
    });
    observer.observe(list, { childList: true });
  }

  function initSubmitGate() {
    var btn = byId('EventSubmit');
    if (!btn) return;

    btn.addEventListener('click', function (e) {
      var failures = validateAll();
      if (failures.length) {
        e.stopImmediatePropagation();
        e.preventDefault();
        var step = failures[0].step;
        goToStep(step);
        showFieldErrors(failures.map(function (f) { return f.error; }));
        return;
      }
      clearFieldErrors();
      autoCopyLanguages();
    }, true);
  }

  /* ---------- Toggle de entradas (venue) ---------- */
  function initVenueTicketToggle() {
    var toggle = byId('ewVenueTicketToggle');
    if (!toggle) return;
    var fields = qs('.js-venue-ticket-fields');

    function refresh() {
      var on = toggle.checked;
      qsa('input, select', fields).forEach(function (field) {
        field.disabled = !on;
      });
      fields.classList.toggle('is-disabled', !on);
    }

    toggle.addEventListener('change', refresh);
    refresh();
  }

  /* ---------- Clonar idioma (botón) ---------- */
  function initLangCloneButtons() {
    qsa('[data-ew-clone-lang]').forEach(function (button) {
      button.addEventListener('click', function () {
        copyLangFromDefault(button.getAttribute('data-ew-clone-lang'), true);
      });
    });
  }

  /* ---------- Cierre del modal ---------- */
  function initModalClose() {
    var modalEl = byId('createEventWizard');
    if (!modalEl) return;
    var closeBtn = qs('.event-wizard__close');
    if (closeBtn) {
      closeBtn.addEventListener('click', function () {
        if (!isDirty() || window.confirm('¿Seguro que querés salir? Se va a perder lo que hayas cargado.')) {
          $(modalEl).modal('hide');
        }
      });
    }
  }

  /* ---------- Navegación buttons ---------- */
  function initNavButtons() {
    var next = byId('ewNextBtn');
    var back = byId('ewBackBtn');
    var skip = byId('ewSkipAdvancedBtn');
    var advancedLink = byId('ewAdvancedLinkBtn');

    if (next) next.addEventListener('click', advance);
    if (back) back.addEventListener('click', function () { goToStep(currentStep - 1); });
    if (advancedLink) advancedLink.addEventListener('click', function () { goToStep(6); });
    if (skip) skip.addEventListener('click', function () {
      autoCopyLanguages();
      goToStep(5);
    });

    qsa('[data-wizard-go]').forEach(function (button) {
      button.addEventListener('click', function () {
        var step = parseInt(button.getAttribute('data-wizard-go'), 10);
        if ((step <= maxVisited || (step === 6 && currentStep === 5)) && step !== currentStep) {
          clearFieldErrors();
          goToStep(step);
        }
      });
    });
  }

  /* ---------- Init ---------- */
  function init() {
    var modalEl = byId('createEventWizard');
    if (!modalEl) return;
    var openBtn = byId('ewOpenWizardBtn');

    function openWizard() {
      $(modalEl).modal({ backdrop: 'static', keyboard: false, show: true });
    }

    initNavButtons();
    initWizardExtract();
    initVenueTicketToggle();
    initLangCloneButtons();
    initSubmitGate();
    watchServerErrors();
    initModalClose();

    // Foco: trampa de Tab dentro del wizard + retorno al disparador al cerrar
    $(modalEl).on('keydown', function (e) {
      if (e.key !== 'Tab') return;
      var focusables = modalEl.querySelectorAll('button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])');
      if (!focusables.length) return;
      var first = focusables[0];
      var last = focusables[focusables.length - 1];
      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault();
        last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    });
    $(modalEl).on('shown.bs.modal', function () {
      modalEl.focus();
    });
    $(modalEl).on('hidden.bs.modal', function () {
      if (openBtn) openBtn.focus();
    });

    if (openBtn) {
      openBtn.addEventListener('click', openWizard);
    }

    var search = new URLSearchParams(window.location.search || '');
    var hasServerErrors = !!qs('#eventErrors li');
    if (search.get('wizard') === '1' || hasServerErrors) {
      openWizard();
    }

    var checklistTimer = null;
    var scheduleChecklist = function () {
      if (checklistTimer) clearTimeout(checklistTimer);
      checklistTimer = setTimeout(function () {
        updateCreateChecklist();
        if (currentStep === 5) updateReview();
      }, 250);
    };
    document.addEventListener('input', scheduleChecklist);
    document.addEventListener('change', scheduleChecklist);

    setTimeout(updateCreateChecklist, 300);
    refreshFooter();
  }

  window.updateCreateChecklist = updateCreateChecklist;

  $(function () { init(); });
})(jQuery);
