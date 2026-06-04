@php
    $aiGenerateRoute = $aiGenerateRoute ?? route('organizer.events.ai-images.generate', $event->id);
    $aiStatusRoute = $aiStatusRoute ?? route('organizer.events.ai-images.status', $event->id);
    $aiApplyRoute = $aiApplyRoute ?? route('organizer.events.ai-images.apply', $event->id);
    $aiRegenerateRoute = $aiRegenerateRoute ?? route('organizer.events.ai-images.regenerate', [$event->id, '__FORMAT__']);
    $hasAiThumbnail = !empty($event->thumbnail);
@endphp

@if(config('features.ai_images_enabled'))
<style>
  .ai-generate-panel {
    margin: 1rem 0 1.25rem;
    padding: 1rem;
    border: 1px solid #e5eaf3;
    border-radius: 10px;
    background: #fff;
    box-shadow: 0 8px 24px rgba(30, 37, 50, .06);
  }
  .ai-generate-panel__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    flex-wrap: wrap;
  }
  .ai-generate-panel__title {
    margin: 0;
    color: #1e2532;
    font-weight: 700;
    font-size: .95rem;
  }
  .ai-generate-panel__hint {
    margin: .25rem 0 0;
    color: #6b7280;
    font-size: .82rem;
  }
  .ai-status-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: .75rem;
    margin-top: 1rem;
  }
  .ai-status-card {
    border: 1px solid #e5eaf3;
    border-radius: 8px;
    padding: .75rem;
    background: #f8fafc;
    min-height: 230px;
  }
  .ai-status-card.is-completed { background: #f8fff9; border-color: #bfe8ca; }
  .ai-status-card.is-running { background: #fffaf5; border-color: #fed7aa; }
  .ai-status-card.is-failed { background: #fff7f7; border-color: #fecaca; }
  .ai-status-card__top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: .5rem;
    min-height: 42px;
  }
  .ai-status-card__title {
    margin: 0;
    color: #1e2532;
    font-size: .9rem;
    font-weight: 700;
  }
  .ai-status-card__desc {
    margin: .15rem 0 0;
    color: #64748b;
    font-size: .75rem;
    line-height: 1.35;
  }
  .ai-status-card__badge {
    white-space: nowrap;
    border-radius: 999px;
    padding: .18rem .5rem;
    background: #e2e8f0;
    color: #475569;
    font-size: .7rem;
    font-weight: 700;
  }
  .ai-status-card__progress {
    height: 8px;
    margin: .75rem 0;
    overflow: hidden;
    border-radius: 999px;
    background: #e5e7eb;
  }
  .ai-status-card__bar {
    height: 100%;
    width: 0;
    border-radius: inherit;
    background: linear-gradient(90deg, #f97316, #22c55e);
    transition: width .35s ease;
  }
  .ai-status-card.is-running .ai-status-card__bar {
    background-size: 200% 100%;
    animation: aiBarPulse 1.1s linear infinite;
  }
  @keyframes aiBarPulse {
    from { background-position: 0 0; }
    to { background-position: 200% 0; }
  }
  .ai-status-card__preview {
    display: flex;
    align-items: center;
    justify-content: center;
    aspect-ratio: 16 / 10;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    background: #fff;
    overflow: hidden;
  }
  .ai-status-card__preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .ai-status-card__empty {
    color: #94a3b8;
    font-size: .78rem;
    text-align: center;
    padding: .5rem;
  }
  .ai-status-card__actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
    margin-top: .75rem;
    flex-wrap: wrap;
  }
  .ai-status-card__check {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    margin: 0;
    color: #1e2532;
    font-size: .78rem;
    font-weight: 700;
  }
  .ai-status-card__error {
    margin: .5rem 0 0;
    color: #b91c1c;
    font-size: .75rem;
  }
  .ai-apply-row {
    display: none;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    margin-top: 1rem;
    padding-top: .75rem;
    border-top: 1px solid #e5eaf3;
  }
  .ai-apply-row.is-visible { display: flex; }
  .ai-apply-row__copy {
    color: #64748b;
    font-size: .8rem;
  }
  @media (max-width: 991px) {
    .ai-status-grid { grid-template-columns: 1fr; }
    .ai-apply-row { align-items: stretch; flex-direction: column; }
  }
</style>

<div class="ai-generate-panel" data-ai-panel>
  <div class="ai-generate-panel__head">
    <div>
      <h5 class="ai-generate-panel__title">{{ __('Variantes seguras de la portada') }}</h5>
      <p class="ai-generate-panel__hint">
        @if($hasAiThumbnail)
          {{ __('Generá formatos desde la portada original, revisá la vista previa y aplicá solo las imágenes que quieras usar.') }}
        @else
          {{ __('Subí y guardá una imagen de portada para activar las variantes seguras.') }}
        @endif
      </p>
    </div>
    <button type="button" class="btn btn-primary btn-sm" id="ai-generate-btn" @if(!$hasAiThumbnail) disabled @endif>
      {{ __('Generar variantes') }}
    </button>
  </div>

  <div id="ai-status-container"></div>

  <div class="ai-apply-row" id="ai-apply-row">
    <span class="ai-apply-row__copy" id="ai-apply-copy">
      {{ __('Seleccioná las imágenes que quieras usar en el evento.') }}
    </span>
    <button type="button" class="btn btn-success btn-sm" id="ai-apply-btn" disabled>
      {{ __('Aplicar seleccionadas') }}
    </button>
  </div>
</div>

@if($hasAiThumbnail)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('ai-generate-btn');
    const container = document.getElementById('ai-status-container');
    const applyRow = document.getElementById('ai-apply-row');
    const applyBtn = document.getElementById('ai-apply-btn');
    const applyCopy = document.getElementById('ai-apply-copy');
    const formats = ['square', 'gallery', 'og'];
    const selected = new Set();
    const labels = {
        square: 'Cover / home',
        gallery: 'Galería',
        og: 'Redes sociales'
    };
    let pollTimer = null;

    if (!btn || !container || !applyBtn) return;

    btn.addEventListener('click', function() {
        if (!confirm('Se generarán hasta 3 variantes desde la portada original sin cambiar el contenido del flyer. ¿Continuar?')) {
            return;
        }
        selected.clear();
        setGeneratingState(true);

        fetch('{{ $aiGenerateRoute }}', {
            method: 'POST',
            headers: requestHeaders(),
            body: JSON.stringify({})
        })
        .then(parseJson)
        .then(data => {
            if (data.error) {
                alert(data.message || data.error);
                setGeneratingState(false);
                return;
            }
            pollStatus(true);
        })
        .catch(err => {
            alert('Error de red: ' + err.message);
            setGeneratingState(false);
        });
    });

    applyBtn.addEventListener('click', function() {
        const ids = Array.from(selected);
        if (!ids.length) return;
        applyBtn.disabled = true;
        applyBtn.textContent = 'Aplicando...';

        fetch('{{ $aiApplyRoute }}', {
            method: 'POST',
            headers: requestHeaders(),
            body: JSON.stringify({ generation_ids: ids })
        })
        .then(parseJson)
        .then(data => {
            if (data.error) {
                alert(data.message || data.error);
                updateApplyButton();
                return;
            }
            applyCopy.textContent = 'Imágenes aplicadas. Recargando para mostrar los cambios...';
            setTimeout(() => location.reload(), 900);
        })
        .catch(err => {
            alert('Error de red: ' + err.message);
            updateApplyButton();
        });
    });

    container.addEventListener('change', function(event) {
        const input = event.target.closest('[data-ai-select]');
        if (!input) return;

        if (input.checked) {
            selected.add(input.value);
        } else {
            selected.delete(input.value);
        }
        updateApplyButton();
    });

    container.addEventListener('click', function(event) {
        const button = event.target.closest('[data-ai-regenerate]');
        if (!button) return;

        const format = button.getAttribute('data-ai-regenerate');
        button.disabled = true;
        button.textContent = 'Regenerando...';
        selected.delete(button.getAttribute('data-generation-id'));

        fetch('{{ $aiRegenerateRoute }}'.replace('__FORMAT__', format), {
            method: 'POST',
            headers: requestHeaders(),
            body: JSON.stringify({})
        })
        .then(parseJson)
        .then(data => {
            if (data.error) {
                alert(data.message || data.error);
                return;
            }
            pollStatus(true);
        })
        .catch(err => alert('Error de red: ' + err.message));
    });

    pollStatus(false);

    function pollStatus(forcePolling) {
        clearTimeout(pollTimer);
        fetch('{{ $aiStatusRoute }}', { headers: { 'Accept': 'application/json' } })
            .then(parseJson)
            .then(data => {
                renderStatus(data);
                const isActive = formats.some(fmt => {
                    const status = data.formats && data.formats[fmt] ? data.formats[fmt].status : null;
                    return status === 'pending' || status === 'running';
                });
                setGeneratingState(isActive);
                if (isActive || forcePolling) {
                    pollTimer = setTimeout(() => pollStatus(false), 2500);
                }
            })
            .catch(() => {
                if (forcePolling) {
                    pollTimer = setTimeout(() => pollStatus(false), 4500);
                }
            });
    }

    function renderStatus(data) {
        const payload = data.formats || {};
        container.innerHTML = '';

        const grid = document.createElement('div');
        grid.className = 'ai-status-grid';
        formats.forEach(format => grid.appendChild(buildCard(format, payload[format] || { status: 'not_started' })));
        container.appendChild(grid);

        updateApplyButton();
    }

    function buildCard(format, item) {
        const status = item.status || 'not_started';
        const card = document.createElement('article');
        card.className = 'ai-status-card is-' + status;

        const top = document.createElement('div');
        top.className = 'ai-status-card__top';

        const titleWrap = document.createElement('div');
        const title = document.createElement('h6');
        title.className = 'ai-status-card__title';
        title.textContent = item.label || labels[format] || format;
        const desc = document.createElement('p');
        desc.className = 'ai-status-card__desc';
        desc.textContent = item.description || format;
        titleWrap.appendChild(title);
        titleWrap.appendChild(desc);

        const badge = document.createElement('span');
        badge.className = 'ai-status-card__badge';
        badge.textContent = statusText(status);
        top.appendChild(titleWrap);
        top.appendChild(badge);
        card.appendChild(top);

        const progress = document.createElement('div');
        progress.className = 'ai-status-card__progress';
        const bar = document.createElement('div');
        bar.className = 'ai-status-card__bar';
        bar.style.width = (item.progress || progressFor(status)) + '%';
        progress.appendChild(bar);
        card.appendChild(progress);

        const preview = document.createElement('div');
        preview.className = 'ai-status-card__preview';
        if (item.url) {
            const link = document.createElement('a');
            link.href = item.url;
            link.target = '_blank';
            link.rel = 'noopener';
            const image = document.createElement('img');
            image.src = item.url;
            image.alt = 'Vista previa ' + (labels[format] || format);
            link.appendChild(image);
            preview.appendChild(link);
        } else {
            const empty = document.createElement('span');
            empty.className = 'ai-status-card__empty';
            empty.textContent = emptyText(status);
            preview.appendChild(empty);
        }
        card.appendChild(preview);

        if (item.error) {
            const error = document.createElement('p');
            error.className = 'ai-status-card__error';
            error.textContent = item.error;
            card.appendChild(error);
        }

        const actions = document.createElement('div');
        actions.className = 'ai-status-card__actions';

        if (item.can_apply && item.id) {
            const label = document.createElement('label');
            label.className = 'ai-status-card__check';
            const input = document.createElement('input');
            input.type = 'checkbox';
            input.value = item.id;
            input.setAttribute('data-ai-select', '1');
            input.checked = selected.has(String(item.id));
            label.appendChild(input);
            label.appendChild(document.createTextNode('Usar esta'));
            actions.appendChild(label);
        } else {
            actions.appendChild(document.createElement('span'));
        }

        if (status === 'completed' || status === 'failed') {
            const regenerate = document.createElement('button');
            regenerate.type = 'button';
            regenerate.className = 'btn btn-outline-secondary btn-xs';
            regenerate.textContent = 'Regenerar';
            regenerate.setAttribute('data-ai-regenerate', format);
            if (item.id) {
                regenerate.setAttribute('data-generation-id', item.id);
            }
            actions.appendChild(regenerate);
        }

        card.appendChild(actions);
        return card;
    }

    function requestHeaders() {
        return {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        };
    }

    function parseJson(response) {
        return response.json();
    }

    function setGeneratingState(isGenerating) {
        btn.disabled = isGenerating;
        btn.textContent = isGenerating ? 'Generando...' : 'Generar variantes';
    }

    function updateApplyButton() {
        const count = selected.size;
        applyRow.classList.toggle('is-visible', count > 0);
        applyBtn.disabled = count === 0;
        applyBtn.textContent = count > 0 ? 'Aplicar seleccionadas (' + count + ')' : 'Aplicar seleccionadas';
        applyCopy.textContent = count > 0
            ? 'Vas a aplicar ' + count + ' imagen(es) al evento. Las no seleccionadas quedan solo como preview.'
            : 'Seleccioná las imágenes que quieras usar en el evento.';
    }

    function statusText(status) {
        return {
            not_started: 'Sin generar',
            pending: 'En cola',
            running: 'Generando',
            completed: 'Lista',
            failed: 'Error'
        }[status] || status;
    }

    function progressFor(status) {
        return {
            not_started: 0,
            pending: 12,
            running: 68,
            completed: 100,
            failed: 100
        }[status] || 0;
    }

    function emptyText(status) {
        return {
            not_started: 'Todavía no generada',
            pending: 'Esperando worker',
            running: 'Generando variante',
            failed: 'No se pudo generar'
        }[status] || 'Sin preview';
    }
});
</script>
@endif
@endif
