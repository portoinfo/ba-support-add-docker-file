import store from '../../vue/client.js'
export const guardPopup = function (to, from, next) {
    var interval = setInterval(function() {
        if ('isPopup' in store.state) {
            clearInterval(interval);
            if (!store.state.isPopup) {
                next();
            } else {
                const builderall_office = new URL(location.href).searchParams.get("builderall-office");
                if (builderall_office === '1') {
                    store.state.isOfficePopup = true;
                }
                return next({name: 'chat-opened', params: { id: 'popup' }});
            }
        }
    })
}
