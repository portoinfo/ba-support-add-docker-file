/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
//imports

import Loading from './base/Loading';

const loading = new Loading();
loading.show();

document.getElementsByTagName('body')[0].onload = function(event) {
    loading.hide();
};

document.getElementsByTagName('body')[0].onbeforeunload = function(event) {
    loading.show();
};

import { Workbox } from 'workbox-window';

if ('serviceWorker' in navigator) {
    const wb = new Workbox('/service-worker.js');

    wb.register();
}

import Quill from 'quill';
import { quillEditor } from 'vue-quill-editor'
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'
import "quill-emoji/dist/quill-emoji.css";
import * as Emoji from "quill-emoji";
import { container, ImageExtend, QuillWatch } from 'quill-image-extend-module'
import BlotFormatter from 'quill-blot-formatter';
import ImageCompress from 'quill-image-compress';
import AutoLinks from 'quill-auto-links';
import Delta, { AttributeMap } from 'quill-delta';

import 'material-design-icons-iconfont/dist/material-design-icons.css'

import VueI18n from "vue-i18n";
import { BootstrapVue, BootstrapVueIcons } from "bootstrap-vue";
import { languages } from '../../static/translation/index.js';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import Vue from 'vue';
import Snotify from 'vue-snotify'; // 1. Import Snotify
import store from './store';
import VueMaterialIcon from 'vue-material-icon'
import VueTelInput from 'vue-tel-input'
import VueTheMask from 'vue-the-mask'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import VueChatScroll from 'vue-chat-scroll'
import InfiniteLoading from 'vue-infinite-loading';
import VueMoment from 'vue-moment'
import moment from 'moment-timezone'
import VueChartJs from 'vue-chartjs'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import Multiselect from 'vue-multiselect'

import VueLoading from 'vuejs-loading-plugin'

import linkify from 'vue-linkify'
import VueClazyLoad from 'vue-clazy-load'
import PulseLoader from 'vue-spinner/src/PulseLoader.vue'

import 'viewerjs/dist/viewer.css'
import Viewer from 'v-viewer'


import TextareaAutosize from 'vue-textarea-autosize'
Vue.use(TextareaAutosize)
import Gravatar from 'vue-gravatar';

import ScrollBar from '@morioh/v-smooth-scrollbar'
Vue.use(ScrollBar);

Vue.component('v-gravatar', Gravatar);

import LiquorTree from 'liquor-tree'
Vue.use(LiquorTree)

import VueLoading2 from 'vue-loading-template'
Vue.use(VueLoading2)


import excel from 'vue-excel-export';
Vue.use(excel);

import VueTextareaAutogrowDirective from 'vue-textarea-autogrow-directive'
Vue.use(VueTextareaAutogrowDirective)

Vue.prototype.$spinner = loading; // algum plugin ja atribui uma variavel com nome $loading

//import {notification} from './notification.js'

//Vue.prototype.$notification = notification;

Vue.use(Viewer)

Vue.directive('linkified', linkify)
Vue.use(InfiniteLoading, { /* options */ });
Vue.use(VueClazyLoad)
    // overwrite defaults
Vue.use(VueLoading, {
    dark: false, // default false
    text: '', // default 'Loading'
    //loading: true, // default false
    //customLoader: myVueComponent, // replaces the spinner and text with your own
    //background: 'rgb(255,255,255)', // set custom background
    //classes: ['myclass'] // array, object or string
})
Vue.directive(linkify);
Vue.use(VueChatScroll);
Vue.use(VueI18n);
Vue.use(BootstrapVue);
Vue.prototype.$isRTL = false;
Vue.use(Snotify);
Vue.use(VueMaterialIcon);
Vue.use(VueMoment, { moment, })
Vue.use(VueTelInput);
Vue.use(VueTheMask);
Vue.use(googleTranslate);
// const options = {
// 	toast: {
// 		position: SnotifyPosition.rightTop
// 	}
// }

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component(VueMaterialIcon.name, VueMaterialIcon)
Vue.component('multiselect', Multiselect)

// vue-chartjs requires extend
for (let key in VueChartJs) {
    if (!['generateChart', 'mixins', 'render'].includes(key)) {
        let name = `${key.replace( /([A-Z])/g, " $1" ).trim().replace(' ', '-').toLocaleLowerCase()}-chart`

        Vue.component(name, {
            extends: VueChartJs[key],
            name: name,
            mixins: [VueChartJs.mixins.reactiveProp],
            props: [
                'options',
                'width',
                'height',
                'chart-id',
                'css-classes',
                'styles',
                'plugins'
            ],
            mounted() {
                // this.chartData is created in the mixin.
                // If you want to pass options please create a local options object
                this.renderChart(this.chartData, this.options)
            }
        })

    }
}

Vue.component('pagination', require('laravel-vue-pagination'));


Vue.component('base-timezone-selector', require('./components/tools/BaseTimezoneSelector.vue').default);

Vue.component('v-select', vSelect)
Vue.component('the-navbar', require('./components/layoutsAdmin/TheNavbar.vue').default);
Vue.component('the-sidebar', require('./components/layoutsAdmin/TheSidebar.vue').default);
Vue.component('login-ba-suporte', require('./components/layoutsAdmin/LoginBaSuporte.vue').default);
Vue.component('register-ba-suporte', require('./components/layoutsAdmin/RegisterBaSuporte.vue').default);
Vue.component('cookies-accept', require('./components/layoutsAdmin/cookies-accept.vue').default);
Vue.component('login-ba-suporte-c', require('./components/layoutsClient/LoginBaSuporteC.vue').default);
Vue.component('register-ba-suporte-c', require('./components/layoutsClient/RegisterBaSuporteC.vue').default);
Vue.component('user-block', require('./components/admin/users/user-block.vue').default);
Vue.component('change-password', require('./components/layoutsAdmin/ChangePassword.vue').default);

Vue.component('login-ba-telegram', require('./components/layoutsAdmin/LoginBaTelegram.vue').default);


Vue.component('example-component', require('./components/ExampleComponent.vue').default);


Vue.component('company-dashboard', require('./components/admin/company/CompanyDashboard.vue').default);
Vue.component('company-body', require('./components/admin/company/company-body.vue').default);
Vue.component('company-select', require('./components/admin/company/company-select.vue').default);
Vue.component('company-create-contact', require('./components/admin/company/company-create-contact.vue').default);
Vue.component('company-create', require('./components/admin/company/company-create.vue').default);
Vue.component('company-config', require('./components/admin/company/company-config.vue').default);
Vue.component('config-category', require('./components/admin/company/config/config-category.vue').default);
Vue.component('config-emails', require('./components/admin/company/config/config-emails.vue').default);
Vue.component('config-faq-robot', require('./components/admin/company/config/config-faq-robot.vue').default);
Vue.component('company-edit-controller', require('./components/admin/company/CompanyEditController.vue').default);
Vue.component('company-integration', require('./components/admin/company/company-integration.vue').default);


Vue.component('department-body', require('./components/admin/department/department-body.vue').default);
Vue.component('department-create', require('./components/admin/department/department-create.vue').default);
Vue.component('department-config', require('./components/admin/department/department-config.vue').default);
Vue.component('department-edit', require('./components/admin/department/department-edit.vue').default);
Vue.component('config-general', require('./components/admin/department/config/config-general.vue').default);
Vue.component('config-auto-answer', require('./components/admin/department/config/config-auto-answer.vue').default);
Vue.component('config-management', require('./components/admin/department/config/config-management.vue').default);
Vue.component('config-quantity-limitations', require('./components/admin/department/config/config-quantity-limitations.vue').default);
Vue.component('config-evaluation', require('./components/admin/department/config/config-evaluation.vue').default);
Vue.component('evaluation-options', require('./components/admin/department/config/evaluation-options.vue').default);
Vue.component('config-robot', require('./components/admin/department/config/config-robot.vue').default);
Vue.component('children-robot', require('./components/admin/department/config/children-robot.vue').default);
Vue.component('config-ticket', require('./components/admin/department/config/config-ticket.vue').default);
Vue.component('config-chat', require('./components/admin/department/config/config-chat.vue').default);
Vue.component('department-dashboard', require('./components/admin/department/DepartmentDashboard.vue').default);
Vue.component('create-department-controller', require('./components/admin/department/CreateDepartmentController.vue').default);

Vue.component('group-body', require('./components/admin/group/group-body.vue').default);
Vue.component('group-create', require('./components/admin/group/group-create.vue').default);
Vue.component('group-config', require('./components/admin/group/group-config.vue').default);
Vue.component('group-edit', require('./components/admin/group/group-edit.vue').default);
Vue.component('config-accesses-group', require('./components/admin/group/config/config-accesses-group.vue').default);
Vue.component('config-user-group', require('./components/admin/group/config/config-user-group.vue').default);

Vue.component('agents-body', require('./components/admin/agents/agents-body.vue').default);
Vue.component('agents-create', require('./components/admin/agents/agents-create.vue').default);
Vue.component('agents-edit', require('./components/admin/agents/agents-edit.vue').default);
Vue.component('agents-dashboard', require('./components/admin/agents/AgentsDashboard.vue').default);
Vue.component('agent-info-dashboard', require('./components/admin/agents/AgentInfoDashboard.vue').default);
Vue.component('create-agent-controller', require('./components/admin/agents/CreateAgentController.vue').default);

Vue.component('user-client-body', require('./components/admin/users/user-client-body.vue').default);
Vue.component('modal-block', require('./components/admin/users/ModalBlock.vue').default);

Vue.component('analyze-body', require('./components/admin/analyze/analyze-body.vue').default);

Vue.component('monitoring-body', require('./components/admin/monitoring/monitoring-body.vue').default);

Vue.component('home-admin-dashboard', require('./components/admin/HomeAdminDashboard.vue').default);
Vue.component('home-employee-dashboard', require('./components/employee/HomeEmployeeDashboard.vue').default);

Vue.component('tickets-body', require('./components/employee/tickets/tickets-body.vue').default);
Vue.component('ticket-create', require('./components/employee/tickets/ticket-create.vue').default);
Vue.component('filter-all', require('./components/employee/tickets/util/filter-all.vue').default);
Vue.component('ticket-information', require('./components/employee/tickets/ticket-information.vue').default);
Vue.component('ticket-ticket', require('./components/employee/tickets/ticket-ticket.vue').default);
Vue.component('ticket-answer', require('./components/employee/tickets/ticket-answer.vue').default);
Vue.component('ticket-view', require('./components/employee/tickets/ticket-view.vue').default);
Vue.component('ticket-client', require('./components/client_old/ticket/ticket-client.vue').default);
Vue.component('answer-ticket', require('./components/client_old/ticket/AnswerTicket.vue').default);
Vue.component('ticket-client-create', require('./components/client_old/ticket/ticket-client-create.vue').default);
Vue.component('ticket-client-open', require('./components/client_old/ticket/ticket-client-open.vue').default);
Vue.component('message-type-tk-message', require('./components/client_old/ticket/MessageTypeTkMessage.vue').default);

Vue.component('menu-filter', require('./components/employee/tickets/util/menu-filter.vue').default);
Vue.component('alert-not-department', require('./components/employee/tickets/util/alert-not-department.vue').default);

Vue.component('testevue', require('./components/testevue.vue').default);

Vue.component('full-ticket', require('./components/full-ticket/index.vue').default);
Vue.component('full-ticket2', require('./components/full-ticket/index2.vue').default);

Vue.component('table-queue-ticket', require('./components/full-ticket/tables/TableQueue.vue').default);
Vue.component('table-finished-ticket', require('./components/full-ticket/tables/TableFinished.vue').default);
Vue.component('table-progress-ticket', require('./components/full-ticket/tables/TableInProgress.vue').default);
Vue.component('table-overdue-ticket', require('./components/full-ticket/tables/TableOverdue.vue').default);
Vue.component('table-canceled-ticket', require('./components/full-ticket/tables/TableCanceled.vue').default);

Vue.component('ticket-ticket2', require('./components/full-ticket/ticket-ticket2.vue').default);
Vue.component('ticket-answer2', require('./components/full-ticket/ticket-answer2.vue').default);
Vue.component('ticket-view2', require('./components/full-ticket/ticket-view2.vue').default);
Vue.component('ticket-comments', require('./components/full-ticket/ticket-comments.vue').default);

Vue.component('tab-global', require('./components/tabs/index.vue').default);

/* begin of full chat components */
Vue.component('full-chat', require('./components/full-chat/Index.vue').default);
Vue.component('full-chat2', require('./components/full-chat/Index2.vue').default);
/* end of full chat components */
Vue.component('chat-body', require('./components/employee/chat/chat-body.vue').default);
Vue.component('page-title', require('./components/employee/chat/PageTitle.vue').default);
Vue.component('left-card', require('./components/employee/chat/LeftCard.vue').default);
Vue.component('right-card', require('./components/employee/chat/RightCard.vue').default);
Vue.component('chat-info', require('./components/employee/chat/ChatInfo.vue').default);
Vue.component('filter-departments', require('./components/employee/chat/FilterDepartments.vue').default);
Vue.component('filter-my-chats', require('./components/employee/chat/FilterMyChats.vue').default);
Vue.component('table-queue', require('./components/employee/chat/TableQueue.vue').default);
Vue.component('table-queue2', require('./components/employee/chat/TableQueue2.vue').default);
Vue.component('table-in-progress', require('./components/employee/chat/TableInProgress.vue').default);
Vue.component('table-in-progress2', require('./components/employee/chat/TableInProgress2.vue').default);
Vue.component('table-transferred', require('./components/employee/chat/TableTransferred.vue').default);
Vue.component('table-closed', require('./components/employee/chat/TableClosed.vue').default);
Vue.component('table-resolved', require('./components/employee/chat/TableResolved.vue').default);
Vue.component('table-resolved2', require('./components/employee/chat/TableResolved2.vue').default);
Vue.component('table-canceled', require('./components/employee/chat/TableCanceled.vue').default);
Vue.component('table-canceled2', require('./components/employee/chat/TableCanceled2.vue').default);
Vue.component('table-loading', require('./components/employee/chat/TableLoading.vue').default);
Vue.component('col-chat', require('./components/employee/chat/ColChat.vue').default);
Vue.component('col-chat2', require('./components/employee/chat/ColChat2.vue').default);
Vue.component('translator', require('./components/employee/chat/Translator.vue').default);
Vue.component('message-type-questionary', require('./components/employee/chat/MessageTypeQuestionary.vue').default);
Vue.component('message-type-event', require('./components/employee/chat/MessageTypeEvent.vue').default);
Vue.component('message-type-text', require('./components/employee/chat/MessageTypeText.vue').default);
Vue.component('message-type-faq-robot', require('./components/employee/chat/MessageTypeFaqRobot.vue').default);
Vue.component('message-type-image-agent', require('./components/employee/chat/MessageTypeImageAgent.vue').default);
Vue.component('message-type-file-agent', require('./components/employee/chat/MessageTypeFileAgent.vue').default);
Vue.component('message-type-open-agent', require('./components/employee/chat/MessageTypeOpenAgent.vue').default);
Vue.component('message-type-close-agent', require('./components/employee/chat/MessageTypeCloseAgent.vue').default);
Vue.component('message-type-robot', require('./components/employee/chat/MessagetypeRobot.vue').default);
Vue.component('button-text-color', require('./components/employee/chat/ButtonTextColor.vue').default);
Vue.component('button-text-bold', require('./components/employee/chat/ButtonTextBold.vue').default);
Vue.component('button-text-italic', require('./components/employee/chat/ButtonTextItalic.vue').default);
Vue.component('button-text-underlined', require('./components/employee/chat/ButtonTextUnderlined.vue').default);
Vue.component('button-attach-file', require('./components/employee/chat/ButtonAttachFile.vue').default);
Vue.component('button-quick-reply', require('./components/employee/chat/ButtonQuickReply.vue').default);
Vue.component('button-emoji', require('./components/employee/chat/ButtonEmoji.vue').default);
Vue.component('button-incognito', require('./components/employee/chat/ButtonIncognito.vue').default);
Vue.component('button-save-as', require('./components/employee/chat/ButtonSaveAs.vue').default);
Vue.component('button-action', require('./components/employee/chat/ButtonAction.vue').default);
Vue.component('button-insert-signature', require('./components/employee/chat/ButtonInsertSignature.vue').default);
Vue.component('button-send-message', require('./components/employee/chat/ButtonSendMessage.vue').default);
Vue.component('modal-client-history', require('./components/employee/chat/ModalClientHistory.vue').default);
Vue.component('modal-category', require('./components/employee/chat/ModalCategory.vue').default);
Vue.component('modal-database', require('./components/full-database/ModalDatabase.vue').default);


Vue.component('robot-message-text', require('./components/employee/chat/robot/messages/messageText.vue').default);
Vue.component('robot-message-button', require('./components/employee/chat/robot/messages/messageButton.vue').default);

Vue.component('modal-translate', require('./components/employee/chat/ModalTranslate.vue').default);


Vue.component('navbar-client', require('./components/client_old/NavbarClient.vue').default);
Vue.component('sidebar-client', require('./components/client_old/SidebarClient.vue').default);
Vue.component('home-client', require('./components/client_old/HomeClient.vue').default);
Vue.component('chat-client', require('./components/client_old/ChatClient.vue').default);
Vue.component('beta-alert', require('./components/client_old/betaAlert.vue').default);
Vue.component('message-type-event-client', require('./components/client_old/MessageTypeEvent.vue').default);
Vue.component('message-type-text-received', require('./components/client_old/MessageTypeTextReceived.vue').default);
Vue.component('message-type-text-sent', require('./components/client_old/MessageTypeTextSent.vue').default);
Vue.component('message-type-question', require('./components/client_old/MessageTypeQuestion.vue').default);
Vue.component('message-type-image', require('./components/client_old/MessageTypeImage.vue').default);
Vue.component('message-type-file', require('./components/client_old/MessageTypeFile.vue').default);
Vue.component('message-type-open', require('./components/client_old/MessageTypeOpen.vue').default);
Vue.component('message-type-close', require('./components/client_old/MessageTypeClose.vue').default);
Vue.component('clip-loader', require('vue-spinner/src/ClipLoader.vue').default);

Vue.component('message-button', require('./components/client_old/robot/messages/MessageButton').default);
Vue.component('message-text', require('./components/client_old/robot/messages/MessageText').default);

Vue.component('chat-ticket-evaluation', require('./components/client_old/ChatTicketEvaluation.vue').default);


Vue.component('circular-bg-icon', require('./components/tools/dashboardTools/CircularBgIcon.vue').default);
Vue.component('summary-card', require('./components/tools/dashboardTools/SummaryCard.vue').default);
Vue.component('progress-card', require('./components/tools/dashboardTools/ProgressCard.vue').default);
Vue.component('time-card', require('./components/tools/dashboardTools/TimeCard.vue').default);
Vue.component('quantitative-card', require('./components/tools/dashboardTools/QuantitativeCard.vue').default);
Vue.component('qualitative-card', require('./components/tools/dashboardTools/QualitativeCard.vue').default);
Vue.component('burger-icon', require('./components/tools/BurgerIcon.vue').default);
Vue.component('gravatar', require('./components/tools/Gravatar.vue').default);
Vue.component('block-system', require('./components/tools/BlockSystem.vue').default);

Vue.component('filter-tickets-chats', require('./components/filter/index.vue').default);
Vue.component('graphic-category', require('./components/filter/graphicCategory.vue').default);

Vue.component('privacy-terms', require('./components/privacyTerms/TermsofUse.vue').default);

Vue.component('customer-service', require('./components/customer-service/CustomerService.vue').default);
Vue.component('sidebar-mobile', require('./components/customer-service/SidebarMobile.vue').default);



// Add a response interceptor
window.axios.interceptors.response.use(function(response) {
    console.log(response);
    return response;
}, function(error) {
    console.log(error);
    if (error.response.status) {
        if (error.response.status == 401 || error.response.status == 422) { //  Unauthorized
            if (error.response && error.response.data && error.response.data.redirect) {
                window.location.href = error.response.data.redirect;
            }
        }
    }
    return error;
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */


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

const app = new Vue({
    el: '#app',
    i18n,
    store,
    created() {
        store.dispatch('initStates');
    },
});
