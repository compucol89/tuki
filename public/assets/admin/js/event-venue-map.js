(function () {
  function initVenueMap(root) {
    if (!root || root.dataset.venueMapReady === '1') {
      return;
    }

    var mapEl = root.querySelector('.ev-venue-map');
    var latInput = root.querySelector('.js-venue-latitude');
    var lngInput = root.querySelector('.js-venue-longitude');
    var statusEl = root.querySelector('.js-venue-geocode-status');
    var searchBtn = root.querySelector('.js-venue-geocode-btn');
    var geocodeUrl = root.getAttribute('data-geocode-url');
    var defaultLang = root.getAttribute('data-default-lang') || 'es';

    if (!mapEl || !latInput || !lngInput || !geocodeUrl || typeof L === 'undefined') {
      return;
    }

    root.dataset.venueMapReady = '1';

    var defaultLat = parseFloat(latInput.value) || -34.6037;
    var defaultLng = parseFloat(lngInput.value) || -58.3816;
    var zoom = latInput.value && lngInput.value ? 15 : 5;

    var map = L.map(mapEl).setView([defaultLat, defaultLng], zoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap',
    }).addTo(map);

    var marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

    function setCoords(lat, lng, message) {
      latInput.value = String(lat);
      lngInput.value = String(lng);
      marker.setLatLng([lat, lng]);
      map.setView([lat, lng], 15);
      if (statusEl) {
        statusEl.textContent = message || '';
      }
    }

    marker.on('dragend', function () {
      var pos = marker.getLatLng();
      setCoords(pos.lat.toFixed(7), pos.lng.toFixed(7), 'Ubicación ajustada en el mapa.');
    });

    function buildAddressQuery() {
      var parts = ['_address', '_city', '_state', '_country'].map(function (suffix) {
        var el = document.querySelector('input[name="' + defaultLang + suffix + '"]');
        return el && el.value ? el.value.trim() : '';
      });
      return parts.filter(Boolean).join(', ');
    }

    function geocodeFromAddress() {
      var query = buildAddressQuery();
      if (!query) {
        if (statusEl) {
          statusEl.textContent = 'Completá dirección, ciudad y país primero.';
        }
        return;
      }

      if (statusEl) {
        statusEl.textContent = 'Buscando ubicación…';
      }
      if (searchBtn) {
        searchBtn.disabled = true;
      }

      fetch(geocodeUrl + '?q=' + encodeURIComponent(query), {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
      })
        .then(function (res) {
          return res.json().then(function (data) {
            return { ok: res.ok, data: data };
          });
        })
        .then(function (result) {
          if (!result.ok || !result.data.ok) {
            throw new Error(result.data.message || 'No encontramos esa dirección.');
          }
          setCoords(
            parseFloat(result.data.lat),
            parseFloat(result.data.lon),
            result.data.display_name || 'Ubicación encontrada.'
          );
        })
        .catch(function (err) {
          if (statusEl) {
            statusEl.textContent = err.message || 'No pudimos buscar la dirección.';
          }
        })
        .finally(function () {
          if (searchBtn) {
            searchBtn.disabled = false;
          }
          setTimeout(function () {
            map.invalidateSize();
          }, 100);
        });
    }

    if (searchBtn) {
      searchBtn.addEventListener('click', geocodeFromAddress);
    }

    setTimeout(function () {
      map.invalidateSize();
    }, 300);
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-venue-map]').forEach(initVenueMap);
  });
})();
