import store from '../../vue/client.js'
export const guardChatRoute = function (to, from, next) {
    if (store.state.showChatModule) {
        next();
    }
}