initSW();

function initSW() {
    if (!"serviceWorker" in navigator) {
        //service worker isn't supported
        return;
    }

    //don't use it here if you use service worker
    //for other stuff.
    if (!"PushManager" in window) {
        //push isn't supported
        return;
    }

    //register the service worker
    if ("serviceWorker" in navigator) {
        navigator.serviceWorker.register('sw.js', {
            scope: '.' // <--- THIS BIT IS REQUIRED
        })
            .then(() => {
                // console.log('serviceWorker registered!')
                initPush();
            })
            .catch((err) => {
            });
    }

}


function initPush() {
    if (!navigator.serviceWorker.ready) {
        return;
    }

    if (!("Notification" in window)) {
        return;
    }

    if (Notification.permission === 'granted') {
        subscribeUser();
    }
}

function requestPushPermission() {
    if (!("Notification" in window) || Notification.permission === 'denied') {
        return Promise.resolve(Notification.permission);
    }

    if (Notification.permission === 'granted') {
        subscribeUser();
        return Promise.resolve('granted');
    }

    return Notification.requestPermission().then((permissionResult) => {
        if (permissionResult === 'granted') {
            subscribeUser();
        }

        return permissionResult;
    });
}

window.TukiPassPushNotifications = window.TukiPassPushNotifications || {};
window.TukiPassPushNotifications.requestPermission = requestPushPermission;

function subscribeUser() {
    navigator.serviceWorker.ready
        .then((registration) => {
            const subscribeOptions = {
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(
                    vap_pub_key
                )
            };

            return registration.pushManager.subscribe(subscribeOptions);
        })
        .then((pushSubscription) => {
            // console.log('Received PushSubscription: ', JSON.stringify(pushSubscription));
            storePushSubscription(pushSubscription);
        })
        .catch(err => {
            // console.log(err);
        });
}

function urlBase64ToUint8Array(base64String) {
    var padding = '='.repeat((4 - base64String.length % 4) % 4);
    var base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    var rawData = window.atob(base64);
    var outputArray = new Uint8Array(rawData.length);

    for (var i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

function storePushSubscription(pushSubscription) {
    const token = document.querySelector('meta[name=csrf-token]').getAttribute('content');
    // console.log(mainurl + '/push');
    fetch(mainurl + '/push', {
        method: 'POST',
        body: JSON.stringify(pushSubscription),
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-Token': token
        }
    })
        .then((res) => {
            return res.json();
        })
        .then((res) => {
            // console.log(res)
        })
        .catch((err) => {
            // console.log(err)
        });
}


let deferredPrompt;

window.addEventListener('afterinstallprompt', (e) => {
    // Prevent Chrome 67 and earlier from automatically showing the prompt
    e.preventDefault();

});
