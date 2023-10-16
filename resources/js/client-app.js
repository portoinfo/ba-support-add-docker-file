require('./bootstrap')

window.Vue = require('vue');

import 'sweetalert2/dist/sweetalert2.min.css';
import 'viewerjs/dist/viewer.css'
import 'vuetify/dist/vuetify.min.css'

import ClientApp        from './components/client/app/ClientApp.vue'
import InfiniteLoading  from 'vue-infinite-loading';
import routes           from './components/client/routes/router.js'
import store            from './components/client/vue/client';
import Viewer           from 'v-viewer'
import Vue              from 'vue';
import VueChatScroll    from 'vue-chat-scroll'
import VueI18n          from "vue-i18n";
import VueRouter        from 'vue-router'
import VueSweetalert2   from 'vue-sweetalert2';
import Vuetify          from 'vuetify'

const swal_options = {
    closeButtonColor:   '#fa4b57',
    confirmButtonColor: '#0080FC',
};

Vue.use(InfiniteLoading, { /* options */ });
Vue.use(Viewer)
Vue.use(VueChatScroll)
Vue.use(VueI18n);
Vue.use(VueRouter)
Vue.use(VueSweetalert2, swal_options);
Vue.use(Vuetify)

/* COMPONENTS */
Vue.component('AuthTemplate',                   require('./components/client/auth/AuthTemplate.vue').default);
Vue.component('ChatBtnFilter',                  require('./components/client/chats/filters/ChatBtnFilter.vue').default);
Vue.component('ChatCard',                       require('./components/client/home/cards/ChatCard.vue').default);
Vue.component('ChatInfoCard',                   require('./components/client/home/cards/ChatInfoCard.vue').default);
Vue.component('ChatTextFilter',                 require('./components/client/chats/filters/ChatTextFilter.vue').default);
Vue.component('ClientChatList',                 require('./components/client/chats/ClientChatList.vue').default);
Vue.component('ClientChatOpenedHeader',         require('./components/client/chats/ClientChatOpenedHeader.vue').default);
Vue.component('ClientChatOpenedMobile',         require('./components/client/chats/mobile/ClientChatOpenedMobile.vue').default);
Vue.component('ClientChatTicketEditor',         require('./components/client/layouts/ClientChatTicketEditor.vue').default);
Vue.component('ClientEditProfileDialog',        require('./components/client/layouts/dialogs/ClientEditProfileDialog.vue').default);
Vue.component('ClientFooterToolbar',            require('./components/client/layouts/ClientFooterToolbar.vue').default);
Vue.component('ClientHeader',                   require('./components/client/layouts/ClientHeader.vue').default);
Vue.component('ClientLogin',                    require('./components/client/auth/ClientLogin.vue').default);
Vue.component('ClientMenuProfile',              require('./components/client/layouts/ClientMenuProfile.vue').default);
Vue.component('ClientPopupFooter',              require('./components/client/layouts/ClientPopupFooter.vue').default);
Vue.component('ClientPreferencesDialog',        require('./components/client/layouts/dialogs/ClientPreferencesDialog.vue').default);
Vue.component('ClientRegister',                 require('./components/client/auth/ClientRegister.vue').default);
Vue.component('ClientTicketAnswerDialog',       require('./components/client/tickets/dialogs/ClientTicketAnswerDialog.vue').default);
Vue.component('ClientTicketList',               require('./components/client/tickets/ClientTicketList.vue').default);
Vue.component('ClientTicketOpenedDetails',      require('./components/client/tickets/ClientTicketOpenedDetails.vue').default);
Vue.component('ClientTicketOpenedHeader',       require('./components/client/tickets/ClientTicketOpenedHeader.vue').default);
Vue.component('ClientTicketOpenedMobile',       require('./components/client/tickets/mobile/ClientTicketOpenedMobile.vue').default);
Vue.component('ClientToolbar',                  require('./components/client/layouts/ClientToolbar.vue').default);
Vue.component('MessageTypeEvent',               require('./components/client/chats/messages/MessageTypeEvent.vue').default);
Vue.component('MessageTypeEventTicket',         require('./components/client/tickets/messages/MessageTypeEvent.vue').default);
Vue.component('MessageTypeFaqRobot',            require('./components/client/chats/messages/MessageTypeFaqRobot.vue').default);
Vue.component('QueueChatPosition',              require('./components/client/chats/QueueChatPosition.vue').default);
Vue.component('RateChatDialog',                 require('./components/client/chats/dialogs/rateChatDialog.vue').default);
Vue.component('RateTicketDialog',               require('./components/client/tickets/dialogs/rateTicketDialog.vue').default);
Vue.component('ReceivedMessageTypeFile',        require('./components/client/chats/messages/ReceivedMessageTypeFile.vue').default);
Vue.component('ReceivedMessageTypeFileTicket',  require('./components/client/tickets/messages/ReceivedMessageTypeFile.vue').default);
Vue.component('ReceivedMessageTypeImageTicket', require('./components/client/tickets/messages/ReceivedMessageTypeImage.vue').default);
Vue.component('ReceivedMessageTypeRobot',       require('./components/client/chats/messages/ReceivedMessageTypeRobot.vue').default);
Vue.component('ReceivedMessageTypeRobotTicket', require('./components/client/tickets/messages/ReceivedMessageTypeRobot.vue').default);
Vue.component('ReceivedMessageTypeText',        require('./components/client/chats/messages/ReceivedMessageTypeText.vue').default);
Vue.component('ReceivedMessageTypeTextTicket',  require('./components/client/tickets/messages/ReceivedMessageTypeText.vue').default);
Vue.component('SentMessageTypeFile',            require('./components/client/chats/messages/SentMessageTypeFile.vue').default);
Vue.component('SentMessageTypeFileTicket',      require('./components/client/tickets/messages/SentMessageTypeFile.vue').default);
Vue.component('SentMessageTypeImageTicket',     require('./components/client/tickets/messages/SentMessageTypeImage.vue').default);
Vue.component('SentMessageTypeText',            require('./components/client/chats/messages/SentMessageTypeText.vue').default);
Vue.component('SentMessageTypeTextTicket',      require('./components/client/tickets/messages/SentMessageTypeText.vue').default);
Vue.component('TicketBtnFilter',                require('./components/client/tickets/filters/TicketBtnFilter.vue').default);
Vue.component('TicketCard',                     require('./components/client/home/cards/TicketCard.vue').default);
Vue.component('TicketInfoCard',                 require('./components/client/home/cards/TicketInfoCard.vue').default);
Vue.component('TicketTextFilter',               require('./components/client/tickets/filters/TicketTextFilter.vue').default);
Vue.component('createFastTicket',                 require('./components/client/create/createFastTicket.vue').default);
Vue.component('VGravatar',                      require('./components/tools/VGravatar.vue').default);
/* ** */

/* ICONS */
import angle_up_1           from './components/client/icons/AngleUp1.vue'
import attach_file          from './components/client/icons/AttachFile.vue'
import back                 from './components/client/icons/Back.vue'
import bell                 from './components/client/icons/Bell.vue'
import chat_active          from './components/client/icons/ChatActive.vue'
import chat_inactive        from './components/client/icons/ChatInactive.vue'
import dashboard_active     from './components/client/icons/DashboardActive.vue'
import dashboard_inactive   from './components/client/icons/DashboardInactive.vue'
import edit                 from './components/client/icons/Edit.vue'
import edit_profile         from './components/client/icons/EditProfile.vue'
import filter               from './components/client/icons/Filter.vue'
import filter_blue          from './components/client/icons/FilterBlue.vue'
import language             from './components/client/icons/Language.vue'
import logout_1             from './components/client/icons/Logout1.vue'
import menu_burger          from './components/client/icons/MenuBurger.vue'
import menu_burger_open     from './components/client/icons/MenuBurgerOpen.vue'
import search               from './components/client/icons/Search.vue'
import send                 from './components/client/icons/Send.vue'
import sun                  from './components/client/icons/Sun.vue'
import thumb_down           from './components/client/icons/ThumbDown.vue'
import thumb_up             from './components/client/icons/ThumbUp.vue'
import ticket_active        from './components/client/icons/TicketActive.vue'
import ticket_inactive      from './components/client/icons/TicketInactive.vue'
import calendar_today       from './components/client/icons/calendarToday.vue'
import calendar_today_d     from './components/client/icons/calendarTodayD.vue'
/* ** */

/* UTILITY FUNCTIONS && PROTOTYPES */
import { ct }                   from './components/client/util/custom-translate'
import { extractImages }        from './components/client/util/extract-images-from-content';
import { formatDate }           from './components/client/util/format-date.js';
import { formatDescription }    from './components/client/util/format-description';
import { formatEmail }          from './components/client/util/format-email';
import { formatSeconds }        from './components/client/util/format-seconds.js';
import { formatStatus }         from './components/client/util/format-status.js';
import { iOS }                  from './components/client/util/is_ios';
import { li }                   from './components/client/util/li.js';
import { loading }              from './components/client/util/loading.js';
import { notify }               from './components/client/util/notify';
import { replaceImageSize }     from './components/client/util/replace-content-image-size';
import { status }               from './components/client/util/get-user-status';
import { stripHtml }            from './components/client/util/strip-html.js';

Vue.prototype.$ct                   = ct;
Vue.prototype.$extractImages        = extractImages;
Vue.prototype.$formatDate           = formatDate;
Vue.prototype.$formatDescription    = formatDescription;
Vue.prototype.$formatEmail          = formatEmail;
Vue.prototype.$formatSeconds        = formatSeconds;
Vue.prototype.$formatStatus         = formatStatus;
Vue.prototype.$iOS                  = iOS;
Vue.prototype.$li                   = li;
Vue.prototype.$loading              = loading;
Vue.prototype.$notify               = notify;
Vue.prototype.$replaceImageSize     = replaceImageSize;
Vue.prototype.$status               = status;
Vue.prototype.$stripHtml            = stripHtml;
/* ** */

import { languages } from '../../static/translation/index.js';
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
    el: '#client-app',
    store,
    i18n,
    components: { ClientApp },
    router,
    vuetify: new Vuetify(
        {
            theme: {
                dark: false,
                themes: {
                    light: {
                        btnLight:               '#FFFFFF',
                        btnPrimary:             '#0080FC',
                        btnDanger:              '#FA4B57',
                        containerBackground:    '#f2f4f7',
                        containerBackground2:   '#f2f4f7',
                        iconPrimary:            '#0080FC',
                        input:                  '#FFFFFF',
                        page_backgroud:         '#E9EDF2',
                        select:                 '#F2F2F2',
                        white:                  '#FFFFFF',
                        whiteCard:              '#FFFFFF',
                    },
                    dark: {
                        btnLight:               '#1C1B1B',
                        btnPrimary:             '#1C1B1B',
                        btnDanger:              '#1C1B1B',
                        containerBackground:    '#333333',
                        containerBackground2:   '#20202A',
                        iconPrimary:            '#FFFFFF',
                        input:                  '#292935',
                        page_backgroud:         '#18171c',
                        select:                 '#292935',
                        white:                  '#20202A',
                        whiteCard:              '#333333',
                    },
                },
            },
            icons: {
                values: {
                    angle_up_1:         { component: angle_up_1 },
                    attach_file:        { component: attach_file },
                    back:               { component: back },
                    bell:               { component: bell },
                    chat_active:        { component: chat_active },
                    chat_inactive:      { component: chat_inactive },
                    dashboard_active:   { component: dashboard_active },
                    dashboard_inactive: { component: dashboard_inactive },
                    edit:               { component: edit },
                    edit_profile:       { component: edit_profile },
                    filter:             { component: filter },
                    filter_blue:        { component: filter_blue },
                    language:           { component: language },
                    logout_1:           { component: logout_1 },
                    menu_burger_open:   { component: menu_burger_open },
                    menu_burger:        { component: menu_burger },
                    search:             { component: search },
                    send:               { component: send },
                    sun:                { component: sun },
                    thumb_down:         { component: thumb_down },
                    thumb_up:           { component: thumb_up },
                    ticket_active:      { component: ticket_active },
                    ticket_inactive:    { component: ticket_inactive },
                    calendar_today:      { component: calendar_today },
                    calendar_today_d:      { component: calendar_today_d },
                },
            },
        }
    ),
    created () {
        localStorage.removeItem('content');
    },
})
