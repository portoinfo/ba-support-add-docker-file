import store from '../../vue/client'
export const guardAnonymousAccess = function (to, from, next) {
    var interval = setInterval(function() {
        if ('user' in store.state) {
            if ('is_anonymous' in store.state.user) {
                clearInterval(interval);
                if (!store.state.user.is_anonymous) {
                    next();
                } else {
                    return next('/customer-chat');
                }
            }
        }
    })
}
