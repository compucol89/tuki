@if(config('features.ai_images_enabled') && !empty($event->thumbnail))
<div class="ai-generate-wrap" style="margin: 1rem 0;">
    <button type="button" class="btn btn-primary" id="ai-generate-btn">
        Generar con IA
    </button>
    <small class="text-muted d-block mt-1">
        Se crearán 3 imágenes (cover, galería, OG) basadas en tu portada actual.
    </small>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('ai-generate-btn');
    if (!btn) return;

    btn.addEventListener('click', function() {
        if (!confirm('Se generarán hasta 3 imágenes con IA. Esto puede tardar 30-60 segundos. ¿Continuar?')) {
            return;
        }
        const self = this;
        self.disabled = true;
        self.innerHTML = 'Generando...';

        fetch('{{ route("organizer.events.ai-images.generate", $event->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({})
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + (data.message || data.error));
                self.disabled = false;
                self.innerHTML = 'Generar con IA';
                return;
            }
            pollStatus();
        })
        .catch(err => {
            alert('Error de red: ' + err.message);
            self.disabled = false;
            self.innerHTML = 'Generar con IA';
        });
    });

    function pollStatus() {
        fetch('{{ route("organizer.events.ai-images.status", $event->id) }}', {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            renderStatus(data);
            if (data.completed + data.failed < data.total) {
                setTimeout(pollStatus, 2000);
            } else {
                setTimeout(() => location.reload(), 1500);
            }
        });
    }

    function renderStatus(data) {
        const container = document.getElementById('ai-status-container');
        if (!container) return;
        let html = '<div class="ai-status-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem;margin-top:1rem;">';
        ['square', 'gallery', 'og'].forEach(fmt => {
            const f = data.formats[fmt] || { status: 'pending' };
            const statusEmoji = { pending: '...', running: 'Generando', completed: 'OK', failed: 'Error' }[f.status] || '?';
            html += '<div class="ai-status-card" style="padding:.5rem;border:1px solid #ddd;border-radius:4px;">' +
                '<strong>' + fmt + '</strong> ' + statusEmoji +
                (f.url ? '<br><a href="' + f.url + '" target="_blank">Ver</a>' : '') +
                (f.error ? '<br><small style="color:red">' + f.error + '</small>' : '') +
                '</div>';
        });
        html += '</div>';
        container.innerHTML = html;
    }
});
</script>
@endif
