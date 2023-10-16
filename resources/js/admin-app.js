require('./bootstrap');
window.Vue = require('vue');

import store from './components/admin/adminAll/vue/admin';
Vue.use(store);

import VueI18n from "vue-i18n";
Vue.use(VueI18n);

import VueRouter from 'vue-router'
Vue.use(VueRouter);

import { languages }    from '../../static/translation/index.js';
import routes           from './components/admin/adminAll/routes/router'

Vue.component('admin-all', require('./components/admin/adminAll/app/adminAll.vue').default);
Vue.component('admin-temporary', require('./components/admin/adminAll/app/adminTemporary.vue').default);


// Add a response interceptor
window.axios.interceptors.response.use(function(response) {
    return response;
}, function(error) {
    if (error.response.status) {
        if (error.response.status == 401 || error.response.status == 422) { //  Unauthorized
            if (error.response && error.response.data && error.response.data.redirect) {
                window.location.href = error.response.data.redirect;
            }
        }
    }
    return error;
});

import { calculateWaitingTime }             from './components/admin/adminAll/util/js/calculateWaitingTime';
import { UTCtoClientTZ }                    from './components/admin/adminAll/util/js/UTCtoClientTZ';
import { UTCtoClientTZ2 }                   from './components/admin/adminAll/util/js/UTCtoClientTZ2';
import { cleanEmail }                       from './components/admin/adminAll/util/js/cleanEmail';

Vue.prototype.$calculateWaitingTime         = calculateWaitingTime;
Vue.prototype.$UTCtoClientTZ                = UTCtoClientTZ;
Vue.prototype.$UTCtoClientTZ2               = UTCtoClientTZ2;
Vue.prototype.$cleanEmail                   = cleanEmail;

import { status } from './get-user-status'
Vue.prototype.$status = status

import { googleTranslate } from './google-translate';
Vue.prototype.$google = googleTranslate

const messages = Object.assign(languages);

export const i18n = new VueI18n({
    locale: document.querySelector('meta[name="user-lang"]').content,
    fallbackLocale: 'en_US',
    messages
});

export const router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes
})

const app = new Vue({
    el: '#admin-app',
    i18n,
    store,
    router,
    created() {
        store.dispatch('initStates');
    },
});