import store from '../../vue/client.js'
export const guardTicketRoute = function (to, from, next) {
    if (store.state.showTicketModule) {
        if(store.state.isPopup){
            var url = `${store.state.baseURL}/customer-ticket`;
            window.open(url, "_blank");
        }else{
            next();
        }
        
    }
}