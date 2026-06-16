(function () {
    'use strict';

    var resetKey = 'tukipass-public-sw-reset-20260615';

    function clearCaches() {
        if (!('caches' in window)) {
            return Promise.resolve();
        }

        return caches.keys()
            .then(function (keys) {
                return Promise.all(keys.map(function (key) {
                    return caches.delete(key);
                }));
            })
            .catch(function () {});
    }

    function unregisterServiceWorkers() {
        if (!('serviceWorker' in navigator)) {
            return Promise.resolve();
        }

        return navigator.serviceWorker.getRegistrations()
            .then(function (registrations) {
                return Promise.all(registrations.map(function (registration) {
                    return registration.unregister();
                }));
            })
            .catch(function () {});
    }

    Promise.all([clearCaches(), unregisterServiceWorkers()])
        .then(function () {
            try {
                if (!sessionStorage.getItem(resetKey) && navigator.serviceWorker && navigator.serviceWorker.controller) {
                    sessionStorage.setItem(resetKey, '1');
                    window.location.reload();
                }
            } catch (e) {}
        });

    window.TukiPassPushNotifications = window.TukiPassPushNotifications || {};
    window.TukiPassPushNotifications.requestPermission = function () {
        return Promise.resolve('denied');
    };
})();
