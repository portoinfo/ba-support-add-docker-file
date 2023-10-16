<template>
    <div id="ba-hd__chat" class="ticket" :class="{
        'info-hidden': !show_info,
        'info-showed': show_info,
        'filter-hidden': !show_filter,
        'filter-showed': show_filter,
        'chat-showed': showChat,
        'chat-hidden': !showChat,
        'is_mobile': isMobile,
    }">
        <div v-show="!showChat" class="ticket-view">
            <div id="ba-hd__header" v-show="!(showChat && isMobile)">
                <div class="header_menu_mob position-relative h-100">
                    <b-badge v-if="isMobile && !showTableQueue && countOnQueue > 0" class="badge-ntf-queue" pill
                        variant="danger">{{ countOnQueue }}</b-badge>
                    <b-button id="popover-button-menu-monile" size="sm" variant="light" class="mr-3" v-show="isMobile">
                        <span class="bs-ico">&#xe5d2;</span>
                    </b-button>
                    <b-popover ref="popover_menu" target="popover-button-menu-monile" id="popover_menu"
                        placement="bottomright">
                        <tree :data="tree_items" ref="tree_mobile" @node:selected="onNodeSelected">
                            <span class="tree-text" slot-scope="{ node }" :class="{ 'tree-text-ml': node.data.ml }">
                                <template v-if="!node.data.icon">
                                    {{ node.text }}
                                    <template v-if="loading_counters">
                                        <b-spinner small label="Small Spinner" variant="primary"></b-spinner>
                                    </template>
                                    <template v-else>
                                        ({{ returnCountByStatus(node.data.id) }})
                                    </template>
                                </template>
                                <template v-else>
                                    <vue-material-icon :name="node.data.icon" :size="18" />
                                    {{ node.text }}
                                    <template v-if="loading_counters">
                                        <b-spinner small label="Small Spinner" variant="primary"></b-spinner>
                                    </template>
                                    <template v-else>
                                        ({{ returnCountByStatus(node.data.id) }})
                                    </template>
                                </template>
                            </span>
                        </tree>
                    </b-popover>
                </div>
                <div class="header_title d-table h-100">
                    <span class="ba-hd__title d-table-cell align-middle">
                        {{ $t("bs-tickets") }}
                    </span>
                    <span class="d-table-cell align-middle" v-if="isMobile && !showChat">
                        <b-badge pill variant="primary" class="ml-3">{{ current_table }}</b-badge>
                        <b-badge pill variant="light">{{ current_table_count }}</b-badge>
                    </span>
                </div>
                <div class="header_filters position-relative h-100">
                    <b-button size="sm" variant="light" @click="show_filter = true" v-if="!showChat && !isMobile">
                        <span class="bs-ico">&#xef4f;</span>
                        <b>{{ $t('bs-ticket-filter') }}</b>
                    </b-button>
                    <b-button v-b-toggle.sidebar-right-filter size="sm" variant="light" v-if="isMobile">
                        <span class="bs-ico">&#xef4f;</span>
                    </b-button>
                    <b-button size="sm" variant="light" @click="modalDatabase">
                        <i class="fa fa-database" aria-hidden="true"></i>
                    </b-button>
                    <b-sidebar v-if="isMobile" id="sidebar-right-filter" :title="$t('bs-ticket-filter')" right backdrop
                        shadow z-index="3" bg-variant="white" class="text-left"
                        @shown="$root.$emit('bv::hide::popover', 'popover_menu')">
                        <div class="pl-3 pr-3 pt-2">
                            <b-form-group v-if="full_list">
                                <b-form-checkbox id="checkbox-1" v-model="filter_my_tickets" name="checkbox-1"
                                    class="mt-2 fz-18">
                                    <b>{{ $t("bs-my-tickets") }}</b>
                                </b-form-checkbox>
                            </b-form-group>
                        </div>

                        <hr style="width:100%;text-align:left;margin-left:0">

                        <div class="pl-3 pr-3">
                            <label>
                                <b>{{ $t("bs-departments") }}</b>
                            </label>

                            <b-form-group v-slot="{ ariaDescribedby }">
                                <b-form-checkbox-group :options="departments" aria-describedby="ariaDescribedby"
                                    stacked v-model="aux_dept" @change="setDepts()">
                                </b-form-checkbox-group>
                            </b-form-group>
                        </div>

                        <hr style="width:100%;text-align:left;margin-left:0">

                        <div class="pl-3 pr-3">
                            <filter-all :is_admin="is_admin" :session_user="user"
                                :session_user_departments="user_departments_id" :session_user_cucd="session_user_cucd"
                                :session_user_company="cs" :session_user_permissions="restriction"></filter-all>
                        </div>
                    </b-sidebar>
                </div>
            </div>

            <div v-show="!isMobile" class="ba-hd__left-sidebar">
                <div class="ba-hd__card-content">
                    <tree :data="tree_items" ref="tree" @node:selected="onNodeSelected">
                        <span class="tree-text" slot-scope="{ node }" :class="{ 'tree-text-ml': node.data.ml }">
                            <template v-if="!node.data.icon">
                                {{ node.text }}
                                <template v-if="loading_counters">
                                    <b-spinner small label="Small Spinner" variant="primary"></b-spinner>
                                </template>
                                <template v-else>
                                    ({{ returnCountByStatus(node.data.id) }})
                                </template>
                            </template>
                            <template v-else>
                                <vue-material-icon :name="node.data.icon" :size="18" />
                                {{ node.text }}
                                <template v-if="loading_counters">
                                    <b-spinner small label="Small Spinner" variant="primary"></b-spinner>
                                </template>
                                <template v-else>
                                    ({{ returnCountByStatus(node.data.id) }})
                                </template>
                            </template>
                        </span>
                    </tree>
                    <b-list-group-item class="opt-new-ticket" @click="openCreateTicket()">
                        <i data-v-7c863152="" class="bbi bbi-message-more bbi-28"></i>
                        <span class="w-100 ml-2 text-left">{{ $t("bs-create-new-ticket") }}</span>
                    </b-list-group-item>

                </div>
            </div>

            <div id="ba-hd__main">
                <div class="ba-hd__card-content">
                    <table-queue-ticket v-if="showTableQueue" :chat_admin="admin"
                        :chat_queue_full_control="chat_queue_full_control" :setInfo="setInfo" :openChat="openChat"
                        :openMultipleTickets="openMultipleTickets" :getChatsOnQueue="getChatsOnQueue"
                        :chats_on_queue="chats_on_queue" :company_department="company_department"
                        :resetTable="resetTable" :hideOnSmall="hideOnSmall" :user="session_user"
                        :chat_number_info="chat.number" :chat_show_info="chat.show"
                        :loading2catchChat="loading2catchChat" v-on:orderTickets="orderTickets" />
                    <table-progress-ticket v-if="showTableInProgress" :setInfo="setInfo" :user="session_user"
                        :chat="chat" :chats="chats_in_progress" :getChatsInProgress="getChatsInProgress"
                        :company_department="company_department" :hideOnSmall="hideOnSmall" :openChat="openChat"
                        :resetTable="resetTable" :countResolved="countResolved" :footerActiveChat="footerActiveChat"
                        :chat_number_info="chat.number" :chat_show_info="chat.show" :overdue="false"
                        v-on:orderTickets="orderTickets" />

                    <table-progress-ticket v-if="showTableOverdue" :setInfo="setInfo" :user="session_user" :chat="chat"
                        :chats="chats_overdue" :getChatsInProgress="getChatsOverdue"
                        :company_department="company_department" :hideOnSmall="hideOnSmall" :openChat="openChat"
                        :resetTable="resetTable" :countResolved="countResolved" :footerActiveChat="footerActiveChat"
                        :chat_number_info="chat.number" :chat_show_info="chat.show" :overdue="true"
                        v-on:orderTickets="orderTickets" />

                    <table-finished-ticket v-if="showTableResolved" :setInfo="setInfo" :user="session_user" :chat="chat"
                        :chats="chats_resolved" :getChatsFinished="getChatsFinished"
                        :company_department="company_department" :hideOnSmall="hideOnSmall" :openChat="openChat"
                        :resetTable="resetTable" :countInProgress="countInProgress" :footerActiveChat="footerActiveChat"
                        :chat_number_info="chat.number" :chat_show_info="chat.show" v-on:orderTickets="orderTickets" />

                    <table-canceled-ticket v-if="showTableCanceled" :setInfo="setInfo" :user="session_user" :chat="chat"
                        :chats="chats_canceled" :getChatsCanceled="getChatsCanceled"
                        :company_department="company_department" :hideOnSmall="hideOnSmall" :openChat="openChat"
                        :resetTable="resetTable" :countCanceled="countCanceled" :footerActiveChat="footerActiveChat"
                        :chat_number_info="chat.number" :chat_show_info="chat.show" v-on:orderTickets="orderTickets" />
                </div>
            </div>

            <div v-show="!isMobile" class="ba-hd__right-sidebar">

                <ticket-comments
                    v-if="!(!chat.show && !showChat || (chat.show && info_minimized) && (showChat && info_minimized))"
                    :chat="chat" :info_minimized="info_minimized" :showChat="showChat" :user="user" />

                <div class="ba-hd__card-content">
                    <header class="pr-2 pl-2 pt-2">
                        <div style="display: grid; grid-template: auto / auto  auto;" v-if="chat.show">
                            <div class="text-left d-table">
                                <b v-if="!info_minimized" class="d-table-cell align-middle">{{
                                        $t('bs-ticket-information')
                                }}</b>
                            </div>
                            <div v-if="showChat" class="text-right">
                                <!-- <span  v-if="!info_minimized" class="bs-ico cursor-pointer" @click="info_minimized = true; rs_mouse = 'leave'">&#xe931;</span>
                                <span  v-else class="bs-ico cursor-pointer pt-1" @click="info_minimized = false; rs_mouse = 'over'">&#xe8f4;</span> -->
                            </div>
                            <div v-else class="text-right pt-1">
                                <!-- <span class="bs-ico cursor-pointer" @click="chat.show = false; rs_mouse = 'leave'">&#xe5cd;</span> -->
                            </div>
                        </div>
                    </header>
                    <div v-if="!chat.show && !showChat || (chat.show && info_minimized) && (showChat && info_minimized)"
                        class="h-100 w-100 d-table text-center no-data">
                        <span class="align-middle d-table-cell w-100">
                            <img class="m-auto d-block" src="images/icons/olho.svg" width="45px">
                            <br>
                            <span v-show="show_info" class="mr-3 ml-3">{{ $t('bs-no-client-information') }}</span>
                        </span>
                    </div>
                    <chat-info v-else :chat="chat" :showChat="showChat" :user="session_user"
                        :footerActiveChat="footerActiveChat" :openClientHistory="openClientHistory" :openCategory="openCategory" :chat_admin="admin"
                        :chat_queue_full_control="chat_queue_full_control" :chats_on_queue="chats_on_queue"
                        :chats_resolved="chats_resolved" :chats_canceled="chats_canceled" :openChat="openChat"
                        :restriction="session_user_permissions[0]">
                    </chat-info>
                </div>

            </div>

            <div v-show="!isMobile" v-if="show_filter && !showChat" class="ba-hd__right-sidebar2">
                <div class="ba-hd__card-content">
                    <header class="pr-2 pl-2">
                        <div style="display: grid; grid-template: auto / auto  auto;">
                            <div class="text-left d-table">
                                <b class="d-table-cell align-middle">{{ $t('bs-filters') }}</b>
                            </div>
                            <div class="text-right pt-1">
                                <span class="bs-ico cursor-pointer" @click="show_filter = false">&#xe5cd;</span>
                            </div>
                        </div>
                    </header>

                    <div class="pl-3 pr-3 pt-2">
                        <b-form-group v-if="full_list">
                            <b-form-checkbox id="checkbox-1" v-model="filter_my_tickets" name="checkbox-1"
                                class="mt-2 fz-18">
                                <b>{{ $t("bs-my-tickets") }}</b>
                            </b-form-checkbox>
                        </b-form-group>
                    </div>

                    <div class="pl-3">
                        <b-form-group v-if="full_list">
                            <b-form-checkbox id="checkbox-2" v-model="filter_not_category" name="checkbox-2"
                                class=" fz-18">
                                <b>{{$t('bs-not-categorized')}}</b>
                            </b-form-checkbox>
                        </b-form-group>
                    </div>

                    <hr style="width:100%;text-align:left;margin-left:0">

                    <div class="pl-3 pr-3 scroll">
                        <label>
                            <b>{{ $t("bs-departments") }}</b>
                        </label>

                        <div class="form-group" aria-describedby="ariaDescribedby">
                            <div class="form-check" v-for="dept in departments" :key="dept.id">
                                <input class="form-check-input" type="checkbox" :id="'dept_'+dept.id" :value="dept.id" v-model="aux_dept" @change="setDepts()">
                                <label class="form-check-label" :for="'dept_'+dept.id">
                                    {{ dept.name }}
                                </label>
                            </div>
                        </div>

                        <!-- <b-form-group v-slot="{ariaDescribedby}">
                            <b-form-checkbox-group
                                :options="departments"
                                :aria-describedby="ariaDescribedby"
                                stacked
                                v-model="aux_dept"
                                @change="setDepts()"
                            >
                            </b-form-checkbox-group>
                        </b-form-group> -->

                        <!-- <b-form-group v-slot="{ ariaDescribedby }">
                            <b-form-checkbox-group id="checkbox-group-2" v-model="aux_dept" stacked @change="setDepts()" 
                            :aria-describedby="ariaDescribedby" name="flavour-2">
                                <b-form-checkbox v-for="(item, index) in departments" :key="index" :value="item.value">
                                    {{ $t(item.name) }}
                                </b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-form-group> -->
                    </div>

                    <hr style="width:100%;text-align:left;margin-left:0">

                    <div class="pl-3 pr-3">
                        <filter-all :is_admin="is_admin" :session_user="user"
                            :session_user_departments="user_departments_id" :session_user_cucd="session_user_cucd"
                            :session_user_company="cs" :session_user_permissions="restriction"></filter-all>
                    </div>

                </div>
            </div>
        </div>

        <template v-if="showChat">
            <ticket-ticket2 :user="user" :restriction="restriction" :departments_config="session_user_departments" :is_admin="is_admin" :itemselected="chat"
                :clearActiveChat="clearActiveChat" :openClientHistory="openClientHistory" :openCategory="openCategory"
                :info_minimized="info_minimized" :rs_mouse="rs_mouse" :show_info="show_info" :showChat="showChat"
                :clientChatHistory="clientChatHistory" :clientTicketHistory="clientTicketHistory" :isMobile="isMobile"
                :shortcu_type="shortcu_type">
                <template slot="comments">
                    <ticket-comments :chat="chat" :info_minimized="info_minimized" :showChat="showChat" :user="user" />
                </template>
                <template slot="chat-info">
                    <chat-info :chat="chat" :showChat="showChat" :user="session_user"
                        :footerActiveChat="footerActiveChat" :openClientHistory="openClientHistory" :openCategory="openCategory" 
                        :chat_admin="admin" :chat_queue_full_control="chat_queue_full_control" :chats_on_queue="chats_on_queue"
                        :chats_resolved="chats_resolved" :chats_canceled="chats_canceled" :openChat="openChat"
                        :restriction="session_user_permissions[0]">
                    </chat-info>
                </template>
            </ticket-ticket2>
        </template>

        <b-sidebar v-if="isMobile && show_info" id="sidebar-right-info-2" :title="$t('bs-ticket-information')" right
            shadow backdrop z-index="999" bg-variant="white">
            <div class="sidebar-chat-info-ticket-mobile">
                <chat-info :chat="chat" :showChat="showChat" :user="session_user" :footerActiveChat="footerActiveChat"
                    :openClientHistory="openClientHistory" :openCategory="openCategory" :chat_admin="admin"
                    :chat_queue_full_control="chat_queue_full_control" :chats_on_queue="chats_on_queue"
                    :chats_resolved="chats_resolved" :chats_canceled="chats_canceled" :openChat="openChat"
                    :restriction="session_user_permissions[0]">
                    <template slot="modalComments">
                        <b-list-group-item class="pb-1 d-flex justify-content-between align-items-center">
                            <b-button size="sm" class="w-100" variant="outline-warning" v-b-modal.modal-comments-mobile>
                                {{ $t('bs-comments') }}</b-button>
                        </b-list-group-item>
                        <b-modal id="modal-comments-mobile"
                            :title="`${$t('bs-comments')} ${$t('bs-ticket')} #${chat.id}`" scrollable>
                            <ticket-comments :chat="chat" :info_minimized="info_minimized" :showChat="showChat"
                                :user="user" :isMobile="isMobile" />
                            <template #modal-footer="{ hide }">
                                <b-button size="sm" class="w-100 text-center" @click="hide('forget')" variant="light"
                                    v-b-modal.modal-add-comment>
                                    <span class="bs-ico mr-2" style="color: #00C38E;">&#xe266;</span>
                                    <span style="color: #333333; padding-top: 1px;">{{ $t('bs-add') }}</span>
                                </b-button>
                            </template>
                        </b-modal>
                    </template>
                </chat-info>
            </div>
        </b-sidebar>

        <template v-if="showCreateTicket" class="mobile">
            <ticket-create :user="user" :cs="cs" v-on:saveRow="saveRow" v-on:back="back"></ticket-create>
        </template>

        <b-button variant="primary" class="float-btn-ticket-create index" @click="openCreateTicket()"
            v-if="isMobile && !showChat">
            <span class="material-icons-outlined"
                style="font-size: 30px; position: relative; top: 2px;  left: 0px;">add</span>
        </b-button>

        <modal-client-history :chat="chat" :clientChatHistory="clientChatHistory"
            :clientTicketHistory="clientTicketHistory" :user="session_user" :isMobile="isMobile" />

        <modal-category v-if="showCategory" :user="user" :chat="chat" :filter_not_category="filter_not_category" cttype="TICKET"/>

        <modal-database v-if="showDatabase" :type="'TICKET'" :chat="chat" :user="user"/>

        <!-- modalDepartmentNot -->
        <alert-not-department title="chat"></alert-not-department>


        <div class="modal fade" id="selectypetranfer" tabindex="-1" aria-labelledby="selectypetranferLabel"
            aria-hidden="true" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content pt-2 mt-2">
                    <div class="modal-body">
                        <div v-if="!shortcu_type" class="row">
                            <div class="modal-header border-0 p-0 m-0 mb-3">
                                <center>
                                    <h5 class="modal-title" id="selectypetranferLabel">
                                        {{ $t('bs-select-the-option-you-want-to-change') }}:</h5>
                                </center>
                            </div>
                            <div class="col-12">
                                [1] - {{ $t('bs-department') }}
                            </div>
                            <div v-if="!showTableQueue" class="col-12">
                                [2] - {{ $t('bs-agents') }}
                            </div>
                            <div class="col-12 mb-3">
                                [3] - {{ $t('bs-cancel') }}
                            </div>
                        </div>

                        <div v-if="shortcu_type">
                            <div class="modal-header border-0 p-0 m-0 mb-3">
                                <center>
                                    <h5 class="modal-title" id="selectypetranferLabel">{{ $t('bs-select-a-department')
                                    }}:
                                    </h5>
                                </center>
                            </div>
                            <b-row cols="auto">
                                <b-col>
                                    <center v-for="(item, index) in listDepartment" :key="index" class="mb-1">
                                        <b-button block class="caret" variant="outline-secondary"
                                            @click="updateTicket(item)">{{ item.name }}</b-button>
                                    </center>
                                    <center class="mb-1">
                                        <b-button size="sm" variant="danger" @click="cancel">{{ $t('bs-cancel') }}
                                        </b-button>
                                    </center>
                                </b-col>
                            </b-row>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalHelps" tabindex="-1" aria-labelledby="modalHelpsLabel" aria-hidden="true"
            data-keyboard="false">
            <div class="modal-dialog ">
                <div class="modal-content pt-2 mt-2">
                    <div class="modal-header border-0 p-0 m-0 mb-1">
                        <h4 class="modal-title" id="modalHelpsLabel">
                            <center>{{ $t('bs-shortcut') }} - [H]</center>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="row ft-16">
                                <div class="col-12">
                                    <span>[Shift + O]</span> - {{ $t('bs-open-Pick-up-ticket') }}.
                                </div>
                                <div class="col-12">
                                    <span>[Shift + V]</span> - {{ $t('bs-view') }} {{ $t('bs-ticket') }}.
                                </div>
                                <div class="col-12">
                                    <span>[Shift + P]</span> - {{ $t('bs-change-ticket-status-to-in-progress') }}.
                                </div>
                                <div class="col-12">
                                    <span>[Shift + C]</span> - {{ $t('bs-change-ticket-status-to-closed') }}.
                                </div>
                                <div class="col-12">
                                    <span>[Shift + D]</span> - {{ $t('bs-to-delete-a-ticket') }}
                                </div>
                                <div class="col-12">
                                    <span>[Shift + T]</span> - {{ $t('bs-transfer') }}
                                </div>
                                <div class="col-12 mb-3">
                                    <span>[Esc]</span> - {{ $t('bs-to-go-back-to-ticket-screens') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import { VEmojiPicker } from "v-emoji-picker";
import { mapState, mapMutations } from "vuex";
import Treeselect from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'
import FilterAll from '../employee/tickets/util/filter-all.vue';
import TicketComments from './ticket-comments.vue';

export default {
    components: {
        VEmojiPicker,
        Treeselect,
        FilterAll,
        TicketComments,
    },
    data() {
        return {
            aux_dept: [],
            tree_items: [
                {
                    text: this.$t("bs-all-tickets"),
                    state: { expanded: true },
                    data: { id: 'all' },
                    children: [
                        {
                            text: this.$t("bs-active-s"),
                            state: { expanded: true },
                            data: { id: 'active' },
                            children: [
                                {
                                    text: this.$t("bs-in-queue"),
                                    state: { selected: false },
                                    data: { icon: "schedule", ml: 1, id: 'queue' }
                                },
                                {
                                    text: this.$t("bs-in-progress"),
                                    state: { selected: false },
                                    data: { icon: "question_answer", ml: 1, id: 'in_progress' }
                                },
                                {
                                    text: this.$t("bs-overdue"),
                                    state: { selected: false },
                                    data: { icon: "report_problem", ml: 1, id: 'overdue' }
                                },
                            ]
                        }
                    ]
                },
                {
                    text: this.$t("bs-finished-s"),
                    state: { selected: false },
                    data: { icon: "done_all", id: 'resolved' }
                },
                {
                    text: this.$t("bs-canceled-s"),
                    state: { selected: false },
                    data: { icon: "cancel", id: 'canceled' }
                }
            ],
            /** props */
            admin: this.session_user_permissions[0]["chat_admin"],
            chat_queue_full_control: this.session_user_permissions[0][
                "chat_queue_full_control"
            ],
            permissions: this.session_user_permissions[0],
            cucd: this.session_user_cucd,
            url_prefix: `${this.session_user_permissions[0]["chat_admin"]
                ? "full-chat-admin"
                : "full-chat"
                }`,
            /** chat */
            questionary: [],
            chat_history: [],
            chat: {
                show: false,
                id: "",
                number: "",
                companyDepartmentId: "",
                //comp_user_comp_depart_id_current: "",
                department: "",
                date: "",
                time: "",
                type: "",
                content: "",
                sideContent: "",
                client: {
                    id: "",
                    name: "",
                    email: "",
                    location: "",
                    browser: "",
                    so: "",
                    ip: ""
                }
            },
            // chat tabs of footer
            chatsFooter: Array(0),
            chatContent: Array(0),
            clientChatHistory: [],
            clientTicketHistory: [],
            footerActiveChat: false,
            /** file upload */
            file_exists: false,
            attachments: [],
            files: [],
            errors: [],
            extensions: [],
            extImages: ["jpg", "jpeg", "png", "bmp"],
            extDocuments: ["docx", "doc", "pdf", "xps", "txt", "odt", "svg"],
            extSpreadsheets: ["xlsx", "xls", "xlt", "csv", "ods"],
            extPresentation: ["pptx", "ppt", "pot", "ppsx", "pps", "odp"],
            /** show */
            showChat: false,
            showTableQueue: false,
            showTableInProgress: false,
            showTableTransferred: false,
            showTableClosed: false,
            showTableResolved: false,
            showTableCanceled: false,
            showTableOverdue: false,
            isMobile: false,
            hidden: false,
            hideOnSmall: false,
            //tables badge notifications
            notify: {
                onQueue: false,
                inProgress: false,
                transferred: false,
                closed: false,
                resolved: false,
                canceled: false
            },
            /** tables chats */
            chats_on_queue: [],
            chats_transferred: [],
            chats_closed: [],
            chats_resolved: [],
            chats_canceled: [],
            chats_in_progress: [],
            chats_overdue: [],
            /** keys */
            key_queue: 0,
            key_progress: 0,
            key_transferred: 0,
            key_closed: 0,
            key_resolved: 0,
            key_canceled: 0,
            //tables count
            countOnQueue: 0,
            countTransferred: 0,
            countClosed: 0,
            countResolved: 0,
            countCanceled: 0,
            countInProgress: 0,
            countOverdue: 0,
            /** message components */
            messageComponent: {
                EVENT: "message-type-event",
                TEXT: "message-type-text",
                OPEN: "message-type-open-agent",
                CLOSE: "message-type-close-agent",
                FILE: "message-type-file-agent",
                IMAGE: "message-type-image-agent"
            },
            // modals
            transfer_to_agent: false,
            departments_to_transfer: [],
            agents_to_transfer: [],
            selected_department: null,
            selected_agent: null,
            ticket_description: null,
            ticket_comments: null,
            // departments
            departments: [],
            company_department: [],
            // actions
            actions: {},
            take_on_chat: false,
            //incognito
            incognito_mode: false,
            incognito_id: null,
            //loading
            skip: 0,
            take: 30,
            // commands
            departmentCommands: Array(0),
            time_inactivityMessage: "",
            // timezone
            tz: "",
            country_bw: "",
            country_sys: this.session_user.language.split("_")[1],
            //
            info_chat_id: "",
            // status
            //online_users: Array(0),
            full_list: Boolean,
            rs_mouse: "",
            show_filter: false,
            // define the default value
            filter_dep_value: null,
            // define options
            filter_dep_options: [{
                id: 'a',
                label: 'a',
                children: [{
                    id: 'aa',
                    label: 'aa',
                }, {
                    id: 'ab',
                    label: 'ab',
                }],
            }, {
                id: 'b',
                label: 'b',
            }, {
                id: 'c',
                label: 'c',
            }],
            loading_messages: true,
            info_minimized: false,
            loading2catchChat: false,
            page: 1,
            chat_catched_id: "",
            dropdownActionWithKeyboardOpened: false,
            searchQuery: "",
            selected: false,
            showCreateTicket: false,
            isPreview: false,
            chatPreview: {},
            loading_counters: true,
            openingChat: false,
            shortcu_type: '',
            listDepartment: '',
            showComments: false,
            showCategory: false,
            showDatabase: false,
        };
    },
    props: {
        user: Object,
        cs: Object,
        is_admin: Number,
        user_departments_id: Array,
        session_user: Object,
        session_user_company: Object,
        session_user_cucd: Array,
        session_user_departments: Array,
        session_user_permissions: Array,
        restriction: Array,
    },
    created() {
        if (this.restriction && (this.restriction[0].ticket_admin || this.restriction[0].ticket_alllist)) {
            this.full_list = 1;
        } else {
            this.full_list = 0;
        }
        this.$store.state.cucd = this.session_user_cucd;

        this.$root.$refs.FullTicket2 = this;

        this.getCountry();
        localStorage.setItem("chatContent", JSON.stringify(this.chatContent));
        this.getDepartmentsByAgent();

        this.connectToChannels();
        if (
            localStorage.getItem("chatsFooter") &&
            localStorage.getItem("chatsFooter") !== "[]"
        ) {
            this.footerActiveChat = true;
        } else if (!localStorage.getItem("chatsFooter")) {
            this.footerActiveChat = false;
        }
        this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;

        this.getExtensions();


        window.addEventListener("resize", this.onResize);
    },
    mounted() {
        this.$root.$on('bv::dropdown::show', bvEvent => {
            if (bvEvent.componentId == "dropdown-action") {
                var el = document.getElementById('input');
                var input_focused = el === document.activeElement;
                if (input_focused) {
                    this.dropdownActionWithKeyboardOpened = true;
                    el.focus();
                } else {
                    this.dropdownActionWithKeyboardOpened = false;
                }
            }
        })

        this.$root.$on('bv::dropdown::hide', bvEvent => {
            if (bvEvent.componentId == "dropdown-action") {
                if (this.dropdownActionWithKeyboardOpened) {
                    bvEvent.preventDefault();
                    this.dropdownActionWithKeyboardOpened = false;
                }
            }
        })
        this.onResize();
        this.openRedirectedChat();
        window.addEventListener('keydown', this.executeShortcutActions);
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.executeShortcutActions);
    },
    methods: {
        modalDatabase(){
            this.showDatabase = !this.showDatabase;
        },
        executeShortcutActions(e) { //e.ctrlKey && 
            var vm = this;
            // VOLTAR
            if (e.key === 'Escape') {
                if (typeof vm.$root.$refs.TicketAnswer2 !== 'undefined') { //TicketView2
                    if (vm.$root.$refs.TicketAnswer2.showView) {
                        vm.$root.$refs.TicketAnswer2.showBody = true;
                        vm.$root.$refs.TicketAnswer2.showView = false;
                        return;
                    }
                }

                if (typeof vm.$root.$refs.TicketAnswer2 !== 'undefined') { //TicketAnswer2
                    if (vm.$root.$refs.TicketTicket2.showAnswer) {
                        vm.$root.$refs.TicketTicket2.showAnswer = !vm.$root.$refs.TicketTicket2.showAnswer;
                        return;
                    }
                }

                if (vm.showChat) { //TicketTicket2

                    vm.$root.$refs.TicketTicket2.clearAndShowTable();

                } else { //FullTicket2

                    vm.$root.$refs.FullTicket2.rs_mouse = 'leave';
                    vm.chat.show = false;
                    vm.chat.id = '';
                }
            }
            // VISUALIZAR - H
            if (!($("#selectypetranfer").data('bs.modal') || {})._isShown && !vm.showCreateTicket 
            && !vm.showComments && !vm.showDatabase && vm.$root.$refs.Tabs.chat_number_opened == '') {
                if (e.key === 'h' || e.key === 'H') {
                    if (vm.showChat) { //TicketTicket2
                    } else { //FullTicket2
                        if (($("#modalHelps").data('bs.modal') || {})._isShown) {
                            $("#modalHelps").modal("hide");
                        } else {
                            $("#modalHelps").modal("show");
                        }
                    }
                }
            }
            // DELETAR 
            if (!($("#selectypetranfer").data('bs.modal') || {})._isShown && !($("#modalHelps").data('bs.modal') || {})._isShown && !vm.showCreateTicket 
            && !vm.showComments && !vm.showDatabase && vm.$root.$refs.Tabs.chat_number_opened == '') {
                if (e.shiftKey && e.key === 'd' || e.shiftKey && e.key === 'D') {
                    if (!($("#selectypetranfer").data('bs.modal') || {})._isShown) {
                        if (vm.showChat) { //TicketTicket2
                            // console.log('TicketTicket2');

                            if (vm.chat.id) {
                                if (vm.restriction[0].ticket_delete == 1) {
                                    if (!vm.$root.$refs.TicketTicket2.showAnswer) {
                                        vm.$root.$refs.ChatInfo.deleteChatTicket(vm.chat.id, 'TICKET');
                                    }
                                } else {
                                    vm.$snotify.info(
                                        vm.$t("bs-not-allowed-to-delete-ticket") + "!",
                                        vm.$t("bs-info")
                                    );
                                }
                            } else {
                                vm.$snotify.info(
                                    vm.$t("bs-select-a-ticket-to-delete") + "!",
                                    vm.$t("bs-info")
                                );
                            }
                        } else { //FullTicket2
                            // console.log('FullTicket2');
                            if (vm.chat.id) {
                                if (vm.restriction[0].ticket_delete == 1) {
                                    vm.$root.$refs.ChatInfo.deleteChatTicket(vm.chat.id, 'TICKET');
                                } else {
                                    vm.$snotify.info(
                                        vm.$t("bs-not-allowed-to-delete-ticket") + "!",
                                        vm.$t("bs-info")
                                    );
                                }
                            } else {
                                vm.$snotify.info(
                                    vm.$t("bs-select-a-ticket-to-delete") + "!",
                                    vm.$t("bs-info")
                                );
                            }
                        }
                    }
                }
            }
            // ABRIR - TICKET
            if (!($("#selectypetranfer").data('bs.modal') || {})._isShown && !($("#modalHelps").data('bs.modal') || {})._isShown && !vm.showCreateTicket 
            && !vm.showComments && !vm.showDatabase && vm.$root.$refs.Tabs.chat_number_opened == '') {
                if (e.shiftKey && e.key === 'o' || e.shiftKey && e.key === 'O' || e.which == 13) {
                    if (vm.showChat) { //TicketTicket2
                        // console.log('TicketTicket2');
                    } else { //FullTicket2
                        // console.log('FullTicket2');
                        if (vm.chat.id) {
                            if (vm.restriction[0].ticket_open == 1 || vm.is_admin == 1 || vm.restriction[0].ticket_admin == 1) {
                                let index = [];
                                if (vm.showTableQueue) {
                                    index = vm.chats_on_queue.data.findIndex(
                                        (item) => item.id === vm.chat.id
                                    );
                                } else if (vm.showTableInProgress) {
                                    index = vm.chats_in_progress.data.findIndex(
                                        (item) => item.id === vm.chat.id
                                    );
                                } else if (vm.showTableOverdue) {
                                    index = vm.chats_overdue.data.findIndex(
                                        (item) => item.id === vm.chat.id
                                    );
                                } else if (vm.showTableResolved) {
                                    index = vm.chats_resolved.data.findIndex(
                                        (item) => item.id === vm.chat.id
                                    );
                                } else if (vm.showTableCanceled) {
                                    index = vm.chats_canceled.data.findIndex(
                                        (item) => item.id === vm.chat.id
                                    );
                                }
                                // console.log(vm.openingChat);
                                if (vm.openingChat == false) {
                                    if (index !== -1) {
                                        vm.openingChat = true;

                                        if (vm.restriction[0].ticket_open == 1 || vm.is_admin == 1 || vm.restriction[0].ticket_admin == 1) {

                                            vm.openingChat = false;
                                            if (vm.showTableQueue) {
                                                vm.openChat(vm.chats_on_queue.data[index]);
                                            } else if (vm.showTableInProgress) {
                                                vm.openChat(vm.chats_in_progress.data[index]);
                                            } else if (vm.showTableOverdue) {
                                                vm.openChat(vm.chats_overdue.data[index]);
                                            } else if (vm.showTableResolved) {
                                                vm.openChat(vm.chats_resolved.data[index]);
                                            } else if (vm.showTableCanceled) {
                                                vm.openChat(vm.chats_canceled.data[index]);
                                            }

                                        } else {
                                            vm.openingChat = false;
                                            vm.$snotify.info(
                                                vm.$t("bs-you-are-not-allowed-to-open-a-ticket") + "!",
                                                vm.$t("bs-info")
                                            );
                                        }
                                    }
                                }
                            } else {
                                vm.openingChat = false;
                                vm.$snotify.info(
                                    vm.$t("bs-you-are-not-allowed-to-open-a-ticket") + "!",
                                    vm.$t("bs-info")
                                );
                            }
                        } else {
                            vm.openingChat = false;
                            vm.$snotify.info(
                                vm.$t("bs-select-a-ticket") + "!",
                                vm.$t("bs-info")
                            );
                        }
                    }
                }
            }
            // COLOCAR EM PROGRESSO - p
            if (!($("#selectypetranfer").data('bs.modal') || {})._isShown && !($("#modalHelps").data('bs.modal') || {})._isShown && !vm.showCreateTicket 
            && !vm.showComments && !vm.showDatabase && vm.$root.$refs.Tabs.chat_number_opened == '') {
                if (e.shiftKey && e.key === 'p' || e.shiftKey && e.key === 'P') {
                    if (vm.showChat) { //TicketTicket2
                        // console.log('TicketTicket2');
                    } else { //FullTicket2
                        // console.log('FullTicket2');
                        if (vm.chat.id) {
                            if (vm.restriction[0].ticket_open == 1) {
                                //TRANSFORMAR ESSE TICKER EM PROGRESSO

                                let index = vm.chats_on_queue.data.findIndex(
                                    (item) => item.id === vm.chat.id
                                );

                                if (vm.openingChat == false) {
                                    if (index !== -1) {
                                        vm.openingChat = true;
                                        if (vm.restriction[0].ticket_open == 1 || vm.is_admin == 1 || vm.restriction[0].ticket_admin == 1) {
                                            if (
                                                (vm.showChat == false && vm.info_chat_id == vm.chats_on_queue.data[index].ticket_id) ||
                                                vm.chats_on_queue.data[index].ticket_id !== vm.vm.chats_on_queue.data[index].ticket_id ||
                                                (vm.showChat === false && vm.chats_on_queue.data[index].ticket_id !== vm.vm.chats_on_queue.data[index].ticket_id)
                                            ) {
                                                vm.clearActiveChat();

                                                vm.chats_on_queue.data[index].name = vm.chats_on_queue.data[index].name_created;
                                                vm.chats_on_queue.data[index].email = vm.chats_on_queue.data[index].email_created;
                                                vm.addFooterActiveChat(vm.chats_on_queue.data[index], 'ticket');

                                                vm.setInfo(vm.chats_on_queue.data[index], true).then(() => {
                                                    axios.post(`ticket-add-chat-admin`, {
                                                        itemselected: vm.chats_on_queue.data[index],
                                                        restrictions: JSON.parse(vm.chats_on_queue.data[index].settings)
                                                    }).then(function (response) {
                                                        if (response.data.success) {
                                                            vm.openingChat = false;
                                                        } else {
                                                            if (response.data.value == "not_opened_limity") {
                                                                vm.$snotify.info(vm.$t('bs-active-ticket-limit-exceeded'), vm.$t('bs-info'));
                                                            }
                                                            vm.openingChat = false;
                                                        }
                                                    }).catch(function (erro) {
                                                        console.log(erro);
                                                        vm.openingChat = false;
                                                        vm.$snotify.error(vm.$t('bs-error-trying-to-save'), vm.$t('bs-error'));
                                                    });
                                                })
                                            } else {
                                                vm.openingChat = false;
                                            }
                                        } else {
                                            vm.openingChat = false;
                                            vm.$snotify.info(
                                                vm.$t("bs-you-are-not-allowed-to-open-a-ticket") + "!",
                                                vm.$t("bs-info")
                                            );
                                        }
                                    }
                                }
                            } else {
                                vm.openingChat = false;
                                vm.$snotify.info(
                                    vm.$t("bs-not-allowed-to-transfer-ticket") + "!",
                                    vm.$t("bs-info")
                                );
                            }
                        } else {
                            vm.openingChat = false;
                            vm.$snotify.info(
                                vm.$t("bs-select-a-ticket-to-transfer") + "!",
                                vm.$t("bs-info")
                            );
                        }
                    }
                }
            }
            // FECHAR - this.restriction[0].ticket_close == 1 && this.restriction[0].ticket_resolved == 1
            if (!($("#selectypetranfer").data('bs.modal') || {})._isShown && !($("#modalHelps").data('bs.modal') || {})._isShown && !vm.showCreateTicket 
            && !vm.showComments && !vm.showDatabase && vm.$root.$refs.Tabs.chat_number_opened == '') {
                if (e.shiftKey && e.key === 'c' || e.shiftKey && e.key === 'C') {
                    if (vm.showChat) { //TicketTicket2
                        // console.log('TicketTicket2');
                    } else { //FullTicket2
                        // console.log('FullTicket2');
                        if (vm.chat.id) {
                            if (vm.restriction[0].ticket_close == 1) {
                                //TRANSFORMAR ESSE TICKER EM PROGRESSO
                                let index = vm.chats_in_progress.data.findIndex(
                                    (item) => item.id === vm.chat.id
                                );
                                if (vm.openingChat == false) {
                                    if (index !== -1) {
                                        vm.openingChat = true;
                                        axios.post(`update-ticket`, {
                                            type: 3,
                                            status: 'CLOSED',
                                            ticket: vm.chat.id,
                                            department: vm.chat.department_id,
                                            chat_id: vm.chat.chat_id_crypt,
                                            current_status: 'IN_PROGRESS',
                                        }).then(function (response) {
                                            if (response.data.success) {
                                                vm.openingChat = false;
                                                vm.$root.$refs.FullTicket2.rs_mouse = 'leave';
                                                vm.chat.show = false;
                                                vm.chat.id = '';
                                                vm.$snotify.success(
                                                    vm.$t("bs-update-saved-successfully"),
                                                    vm.$t("bs-success")
                                                );
                                            } else {
                                                vm.$snotify.error(vm.$t("bs-error-updating"), vm.$t("bs-error"));
                                            }
                                        }).catch(function () {
                                            console.log("FAILURE!!");
                                            vm.openingChat = false;
                                            vm.$snotify.error(vm.$t('bs-error-trying-to-save'), vm.$t('bs-error'));
                                        });
                                    }
                                }
                            } else {
                                vm.openingChat = false;
                                vm.$snotify.info(
                                    vm.$t("bs-you-are-not-allowed-to-close-a-ticket") + "!",
                                    vm.$t("bs-info")
                                );
                            }
                        } else {
                            vm.openingChat = false;
                            vm.$snotify.info(
                                vm.$t("bs-select-a-ticket") + "!",
                                vm.$t("bs-info")
                            );
                        }
                    }
                }
            }
            // VISUALIZAR - v
            if (!($("#selectypetranfer").data('bs.modal') || {})._isShown && !($("#modalHelps").data('bs.modal') || {})._isShown && !vm.showCreateTicket 
            && !vm.showComments && !vm.showDatabase && vm.$root.$refs.Tabs.chat_number_opened == '') {
                if (e.shiftKey && e.key === 'v' || e.shiftKey && e.key === 'V') {
                    if (vm.showChat) { //TicketTicket2
                        // console.log('TicketTicket2');
                    } else { //FullTicket2
                        // console.log('FullTicket2');
                        if (vm.chat.id) {
                            let index = vm.chats_on_queue.data.findIndex(
                                (item) => item.id === vm.chat.id
                            );

                            if (vm.openingChat == false) {
                                if (index !== -1) {
                                    vm.openingChat = true;
                                    vm.$root.$refs.TableQueue.preview(vm.chats_on_queue.data[index]);
                                    //ModalClientHistory.vue
                                }
                            }

                        } else {
                            vm.$snotify.info(
                                vm.$t("bs-select-a-ticket") + "!",
                                vm.$t("bs-info")
                            );
                        }
                    }
                }
            }
            // TRANFERIR
            if (!($("#modalHelps").data('bs.modal') || {})._isShown && !vm.showCreateTicket && !vm.showComments 
            && !vm.showComments && !vm.showDatabase && vm.$root.$refs.Tabs.chat_number_opened == '') {
                if (e.shiftKey && e.key === 't' || e.shiftKey && e.key === 'T') {
                    if (typeof vm.$root.$refs.TicketAnswer2 !== 'undefined') { //TicketView2
                        if (vm.$root.$refs.TicketAnswer2.showView) {
                            // console.log('TicketView2');
                            return;
                        }
                    }

                    if (typeof vm.$root.$refs.TicketAnswer2 !== 'undefined') { //TicketAnswer2
                        if (vm.$root.$refs.TicketTicket2.showAnswer) {
                            // console.log('TicketAnswer2');
                            return;
                        }
                    }

                    if (vm.showChat) { //TicketTicket2
                        // console.log('TicketTicket2');

                    } else { //FullTicket2
                        // console.log('FullTicket2');
                        vm.shortcu_type = "";
                        // console.log(vm.restriction[0]);
                        if (!vm.showTableResolved && !vm.showTableCanceled) {
                            if (vm.chat.id) {
                                if (vm.restriction[0].ticket_moved == 1 || vm.is_admin == 1 || vm.restriction[0].ticket_admin == 1) {
                                    if (($("#selectypetranfer").data('bs.modal') || {})._isShown) {
                                        $("#selectypetranfer").modal("hide");
                                    } else {
                                        $("#selectypetranfer").modal("show");
                                    }

                                } else {
                                    vm.$snotify.info(
                                        vm.$t("bs-not-allowed-to-transfer-ticket") + "!",
                                        vm.$t("bs-info")
                                    );
                                }
                            } else {
                                vm.$snotify.info(
                                    vm.$t("bs-select-a-ticket-to-transfer") + "!",
                                    vm.$t("bs-info")
                                );
                            }
                        }
                    }
                }
            }
            if (($("#selectypetranfer").data('bs.modal') || {})._isShown) {
                let index = [];
                switch (e.key) {
                    case '1':
                        // console.log('Tranferir para outro departamento');
                        if (vm.showTableQueue) {
                            index = vm.chats_on_queue.data.findIndex(
                                (item) => item.id === vm.chat.id
                            );
                        } else if (vm.showTableInProgress) {
                            index = vm.chats_in_progress.data.findIndex(
                                (item) => item.id === vm.chat.id
                            );
                        } else if (vm.showTableOverdue) {
                            index = vm.chats_overdue.data.findIndex(
                                (item) => item.id === vm.chat.id
                            );
                        }

                        if (index !== -1) {
                            vm.shortcu_type = "depart";
                            if (vm.showTableQueue) {
                                axios.get("tickets/get-department", {
                                    params: {
                                        is_vip: JSON.parse(vm.chat.builderall_account_data)
                                    }
                                }).then(function (r_resposta) {
                                    // console.log(r_resposta.data.result);
                                    for (let i = 0; i < r_resposta.data.result.length; i++) {
                                        r_resposta.data.result[i].name = vm.$t(r_resposta.data.result[i].name);
                                    }
                                    vm.listDepartment = r_resposta.data.result;
                                    let index2 = vm.listDepartment.findIndex(
                                        (item) => item.id === vm.chats_on_queue.data[index].department_id
                                    );
                                    if (index2 !== -1) {
                                        vm.listDepartment.splice(index2, 1);
                                    }
                                }).catch(function (error) {
                                    console.log(error);
                                });
                            } else if (vm.showTableInProgress) {
                                vm.openChat(vm.chats_in_progress.data[index]);
                                $("#selectypetranfer").modal("hide");
                            } else if (vm.showTableOverdue) {
                                vm.openChat(vm.chats_overdue.data[index]);
                                $("#selectypetranfer").modal("hide");
                            }
                        }
                        break;

                    case '2':
                        // console.log('Tranferir para outro atendente');
                        if (vm.shortcu_type == 'depart') {

                        } else {
                            if (vm.showTableInProgress) {
                                index = vm.chats_in_progress.data.findIndex(
                                    (item) => item.id === vm.chat.id
                                );
                            } else if (vm.showTableOverdue) {
                                index = vm.chats_overdue.data.findIndex(
                                    (item) => item.id === vm.chat.id
                                );
                            }

                            if (index !== -1) {
                                vm.shortcu_type = "agent";
                                if (vm.showTableInProgress) {
                                    vm.openChat(vm.chats_in_progress.data[index]);
                                } else if (vm.showTableOverdue) {
                                    vm.openChat(vm.chats_overdue.data[index]);
                                }
                            }

                            $("#selectypetranfer").modal("hide");
                        }

                        break;

                    case '3':
                    case 'ESCAPE':
                        // console.log('CANCEL');
                        $("#selectypetranfer").modal("hide");
                        break;

                    default:
                        // console.log(e.key);
                        if (e.key !== 't' && e.key !== 'T') {
                            vm.$snotify.info(
                                vm.$t("bs-comando não identificado") + "!",
                                vm.$t("bs-info")
                            );
                        }
                        break;
                }
            }
        },
        updateTicket(item) {
            var vm = this;
            axios.post("update-ticket", {
                type: 2,
                department: item,
                ticket: vm.chat.id,
                chat_id: vm.chat.chat_id_crypt,
            }).then(function (response) {
                //console.log(response.data.created);
                if (response.data.success) {
                    document.location.reload(true);
                    vm.$snotify.success(
                        vm.$t("bs-update-saved-successfully"),
                        vm.$t("bs-success"), {
                        position: "rightTop",
                    });
                } else {
                    vm.$snotify.error(vm.$t("bs-error-updating"), vm.$t("bs-error"), {
                        position: "rightTop",
                    });
                }
            }).catch(function () {
                console.log("FAILURE!!");
            });
        },
        cancel() {
            $("#selectypetranfer").modal("hide");
        },
        back() {
            this.showCreateTicket = false;
        },
        saveRow(item) {
            this.back();
        },
        ...mapMutations(["tabs"]),
        openCreateTicket() {

            if (this.restriction[0].ticket_admin == 1 || this.is_admin == 1) {
                this.showCreateTicket = true;
                return;
            }

            if (this.restriction[0].ticket_create == null || this.restriction[0].ticket_create == 0) {
                this.$snotify.info(this.$t("bs-you-are-not-allowed-to-create-ticket") + "!", this.$t("bs-info"), {
                    position: "rightTop",
                });
                return;
            }

            this.showCreateTicket = true;
        },
        setDepts() {
            var aux = [];
            var n = this.aux_dept;
            this.departments.forEach(e1 => {
                n.forEach(e2 => {
                    if (e1.id == e2) aux.push(e1);
                });
            });
            this.company_department = aux;
        },
        shownActions() {
            if (this.dropdownActionWithKeyboardOpened) {
                var el = document.getElementById('input');
                el.focus();
            }
        },
        onNodeSelected(node) {
            switch (node.data.id) {
                case 'queue':
                    this.showTableComponent('queue');
                    break;
                case 'in_progress':
                    this.showTableComponent('inProgress');
                    break;
                case 'overdue':
                    this.showTableComponent('overdue');
                    break;
                case 'resolved':
                    this.showTableComponent('resolved');
                    break;
                case 'canceled':
                    this.showTableComponent('canceled');
                    break;
            }
            this.$refs.popover_menu.$emit('close')
        },
        returnCountByStatus(id) {
            switch (id) {
                case 'all':
                    var count = Number(this.countOnQueue) +
                        Number(this.countInProgress) +
                        Number(this.countCanceled) +
                        Number(this.countResolved) +
                        Number(this.countOverdue);
                    break;
                case 'active':
                    var count = Number(this.countOnQueue) + Number(this.countInProgress) + Number(this.countOverdue);
                    break;
                case 'queue':
                    var count = Number(this.countOnQueue);
                    break;
                case 'in_progress':
                    var count = Number(this.countInProgress);
                    break;
                case 'overdue':
                    var count = Number(this.countOverdue);
                    break;
                case 'resolved':
                    var count = Number(this.countResolved);
                    break;
                case 'canceled':
                    var count = Number(this.countCanceled);
                    break;
            }

            if (count > 0) {
                return count;
            } else {
                return "0";
            }
        },
        openRedirectedChat() {
            let id = new URL(location.href).searchParams.get("id");
            if (id !== null) {
                this.getTicket(id).then((ticket) => {
                    this.openChat(ticket);
                    const url = new URL(window.location.href);
                    const params = new URLSearchParams(url.search.slice(1));
                    params.delete("id");
                    window.history.replaceState(
                        {},
                        "",
                        `${window.location.pathname}?module=ticket`
                    );
                });
            }
        },
        getTicket(id) {
            return new Promise((resolve, reject) => {
                var vm = this;
                axios.get('tickets/get-ticket', {
                    params: {
                        id: id
                    }
                })
                    .then(({ data }) => {
                        if (data.success) {
                            resolve(data.ticket);
                        }
                    })
            })
        },
        getCountry() {
            fetch("https://extreme-ip-lookup.com/json/")
                .then(res => res.json())
                .then(response => {
                    if (response.countryCode) {
                        this.country_bw = response.countryCode;
                    } else {
                        this.country_bw = Intl.DateTimeFormat()
                            .resolvedOptions()
                            .locale.split("-")[1]; //BR
                    }
                })
                .catch((data, status) => {
                    this.country_bw = Intl.DateTimeFormat()
                        .resolvedOptions()
                        .locale.split("-")[1]; //BR
                });
        },
        /** @SEND */
        sendMessage() {
            if (this.chat.content.trim() !== "") {
                this.sendContent();
            }
            if (this.files != "") {
                this.sendUpload();
            }
            this.chat.content = "";
            this.files = [];
        },
        sendContent() {
            let message = this.chat.content;
            /** if incognito mode is not set, send the message with the actual agent of the chat */
            if (!this.incognito_mode) {
                this.storeMessage(
                    this.chat.comp_user_comp_depart_id_current,
                    message
                );
                /** else if incognito id is set, send the message with the incognito agent of the chat */
            } else if (this.incognito_id) {
                this.storeMessage(this.incognito_id, message);
                /** else attach incognito agent to the chat department and send the message*/
            } else {
                const api = `company-user-company-department/add-agent-to-department`;
                axios
                    .post(api, {
                        company_department_id: this.chat.companyDepartmentId
                    })
                    .then(({ data }) => {
                        this.cucd = data.session_user_cucd;
                        this.incognito_id = data.cucdic;
                        this.storeMessage(data.cucdic, message);
                    });
            }
        },
        sendUpload() {
            const api = `chat/agent/upload`;
            const formData = new FormData();
            let files = this.files;

            for (var i = 0; i < files.length; i++) {
                let file = files[i];
                formData.append("files[" + i + "]", file);
            }

            formData.append("chat_id", this.chat.chat_id);
            formData.append("extImages", this.extImages);
            formData.append(
                "company_department_id",
                this.chat.companyDepartmentId
            );
            formData.append(
                "time_for_inactivity_message",
                this.time_inactivityMessage
            );
            formData.append("chat", this.chat);
            formData.append("company_id", this.session_user_company.id);

            if (this.incognito_mode) {
                formData.append("is_incognito", this.incognito_mode);
            }

            axios
                .post(api, formData, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                })
                .then(({ data }) => { });
        },
        storeMessage($cucdic, $message) {
            const api = `chat-history/agent/store`;
            axios
                .post(api, {
                    id: this.chat.chat_id,
                    type: "TEXT",
                    content: $message,
                    content_translated: null,
                    is_agent: true,
                    is_incognito: this.incognito_mode,
                    company_department_id: this.chat.companyDepartmentId,
                    company_user_company_department_id: $cucdic,
                    time_for_inactivity_message: this.time_inactivityMessage,
                    chat: this.chat,
                    company_id: this.session_user_company.id
                })
                .then(({ data }) => { });
            document.getElementById("input").focus();
        },
        handleFilesUpload() {
            // o atributo 'attachments' recebe os arquivos enviados pelo onchange do input de uploads
            this.attachments = this.$refs.attachments.files;
            // faço um laço para verificar cada arquivo valido e adiciona-lo ao array que será enviado para API
            Array.from(this.attachments).forEach(attachment => {
                // reverto a string e pego os primeiros caracteres antes do primeiro '.' na string
                let reverse_ext = attachment["name"]
                    .split("")
                    .reverse()
                    .join("")
                    .split(".", 1)
                    .toString();
                // pego a string gerada e reverto ela novamente, assim gerando a extensão do arquivo. Ex: jpg, png etc..
                let ext = reverse_ext
                    .split("")
                    .reverse()
                    .join("");
                // verifico se a extensao do arquivo estiver incluso nas extensões permitidas
                if (
                    this.extensions.includes(ext) ||
                    this.extensions.includes(ext.toLowerCase())
                ) {
                    // caso o array de arquivos validos for diferente de vazio..
                    if (this.files.length) {
                        // é feito um laço para verificar se o arquivo que está sendo enviado já está no array de arquivos válidos
                        this.files.forEach(file => {
                            // caso esteja, o atributo 'file_exists' é setado como true
                            file["name"] === attachment["name"]
                                ? (this.file_exists = true)
                                : "";
                        });
                    }
                    // verifico se o atributo file_exists é falso, isso indica que o arquivo não existe no array de arquivos válidos
                    if (
                        this.file_exists == false &&
                        this.files.length < 5 &&
                        attachment.size <= 5242880
                    ) {
                        // como ele não existe, ele é adicionado
                        this.files.push(attachment);
                    } else {
                        // o atributo 'file_exists' é setado como false para poder ser usado na verificação do arquivo que está na próxima posição do laço
                        this.file_exists = false;
                    }
                } else {
                    // caso a extensão do arquivo não seja valida, adiciono o nome desse arquivo ao atributo 'errors' que armazena os arquivos que não puderam ser adicionados
                    this.errors.push(attachment["name"]);
                }
                // caso algum arquivo tenha sido enviado com a extensão inválido, é disparado um alert para informar o ocorrido ao usuário.
                if (this.errors.length) {
                    Swal.fire({
                        heightAuto: false,
                        title: `${this.$t("bs-oops")}...`,
                        text:
                            this.$t("bs-invalid-file-format") +
                            " '" +
                            this.errors.join(", ") +
                            "'. " +
                            this.$t("bs-the-allowed-formats-are") +
                            this.extensions.join(", ") +
                            "."
                    });
                }
            });
            this.errors = [];
            setTimeout(function () {
                document.getElementById("input").focus();
            }, 0);
        },
        upload() {
            $("#attachments").click();
        },
        /** @GET */
        /*
        getChatHistory(id) {
            return new Promise((resolve, reject) => {
                var vm = this;
                axios
                    .get("ticket-chat-answer/agent/get-ticket-chat-answers", {
                        params: {
                            id: id,
                            reference: "chat_id"
                        }
                    })
                    .then(response => {
                        if (response.data.status) {
                            vm.questionary = response.data.result;
                        }
                    });
                axios
                    .get("chat-history/agent/get-chat-history", {
                        params: {
                            id: id
                        }
                    })
                    .then(({data}) => {
                        var itemsProcessed = 0;
                        data.forEach(element => {
                            vm.setChatHistory(element).then(() => {
                                itemsProcessed++;
                                if(itemsProcessed === data.length) {
                                    vm.loading_messages = false;
                                    resolve();
                                }
                            });
                        });
                    });
            });

        },
        */
        setChatHistory(message) {
            return new Promise((resolve, reject) => {
                if (this.chat_history.push(message)) {
                    resolve();
                } else {
                    reject();
                }
            });
        },
        orderTickets(statusTicket) {
            // console.log(JSON.parse(localStorage.getItem("orderTable")));
            if (statusTicket == 'QUEUE') {
                this.getChatsOnQueue();
            }
            if (statusTicket == 'IN_PROGRESS') {
                this.getChatsInProgress();
            }
            if (statusTicket == 'OVERDUE') {
                this.getChatsOverdue();
            }
            if (statusTicket == 'FINISHED') {
                this.getChatsFinished();
            }
            if (statusTicket == 'CANCELED') {
                this.getChatsCanceled();
            }
        },
        getChatsOnQueue(page = 1) {
            var order = null;
            var isActive = null;
            if (localStorage.getItem("orderTable") != null) {
                order = JSON.parse(localStorage.getItem("orderTable")).order;
                isActive = JSON.parse(localStorage.getItem("orderTable")).isActive;
            }

            var vm = this;
            return new Promise((resolve, reject) => {
                axios.get(`tickets/get-tickets-on-queue?page=${page}`, {
                    params: {
                        not_category: vm.filter_not_category ? 1 : 0,
                        departments: vm.filter_departments,
                        per_page: vm.pp_selected,
                        selected: vm.selected,
                        searchQuery: vm.searchQuery,
                        order: order,
                        isActive: isActive,
                    }
                })
                    .then(({ data }) => {
                        vm.chats_on_queue = data;
                        vm.countOnQueue = data.total;
                        resolve();
                    });
            });
        },
        getChatsInProgress(page = 1) {
            
            var order = null;
            var isActive = null;
            if (localStorage.getItem("orderTable") != null) {
                order = JSON.parse(localStorage.getItem("orderTable")).order;
                isActive = JSON.parse(localStorage.getItem("orderTable")).isActive;
            }

            var vm = this;
            return new Promise((resolve, reject) => {
                axios.get(`tickets/get-tickets-in-progress?page=${page}`, {
                    params: {
                        my_tickets: vm.filter_my_tickets ? 1 : 0,
                        not_category: vm.filter_not_category ? 1 : 0,
                        departments: vm.filter_departments,
                        per_page: vm.pp_selected,
                        overdue: false,
                        selected: vm.selected,
                        searchQuery: vm.searchQuery,
                        order: order,
                        isActive: isActive,
                    }
                })
                    .then(({ data }) => {
                        vm.chats_in_progress = data;
                        vm.countInProgress = data.total;
                        resolve();
                    });

            });
        },
        getChatsOverdue(page = 1) {
            var order = null;
            var isActive = null;
            if (localStorage.getItem("orderTable") != null) {
                order = JSON.parse(localStorage.getItem("orderTable")).order;
                isActive = JSON.parse(localStorage.getItem("orderTable")).isActive;
            }

            var vm = this;
            return new Promise((resolve, reject) => {
                axios.get(`tickets/get-tickets-in-progress?page=${page}`, {
                    params: {
                        my_tickets: vm.filter_my_tickets ? 1 : 0,
                        not_category: vm.filter_not_category ? 1 : 0,
                        departments: vm.filter_departments,
                        per_page: vm.pp_selected,
                        overdue: true,
                        selected: vm.selected,
                        searchQuery: vm.searchQuery,
                        order: order,
                        isActive: isActive,
                    }
                })
                    .then(({ data }) => {
                        vm.chats_overdue = data;
                        vm.countOverdue = data.total;
                        resolve();
                    });
            });
        },
        getChatsFinished(page = 1) {
            var order = null;
            var isActive = null;
            if (localStorage.getItem("orderTable") != null) {
                order = JSON.parse(localStorage.getItem("orderTable")).order;
                isActive = JSON.parse(localStorage.getItem("orderTable")).isActive;
            }

            var vm = this;
            const api = `tickets/get-tickets-finished?page=${page}`;
            axios
                .get(api, {
                    params: {
                        my_tickets: vm.filter_my_tickets ? 1 : 0,
                        not_category: vm.filter_not_category ? 1 : 0,
                        departments: vm.filter_departments,
                        per_page: vm.pp_selected,
                        selected: vm.selected,
                        searchQuery: vm.searchQuery,
                        order: order,
                        isActive: isActive,
                    }
                })
                .then(({ data }) => {
                    vm.chats_resolved = data;
                    vm.countResolved = data.total;
                });
        },
        getChatsCanceled(page = 1) {
            var order = null;
            var isActive = null;
            if (localStorage.getItem("orderTable") != null) {
                order = JSON.parse(localStorage.getItem("orderTable")).order;
                isActive = JSON.parse(localStorage.getItem("orderTable")).isActive;
            }

            var vm = this;
            const api = `tickets/get-tickets-canceled?page=${page}`;
            axios
                .get(api, {
                    params: {
                        my_tickets: vm.filter_my_tickets ? 1 : 0,
                        not_category: vm.filter_not_category ? 1 : 0,
                        departments: vm.filter_departments,
                        per_page: vm.pp_selected,
                        selected: vm.selected,
                        searchQuery: vm.searchQuery,
                        order: order,
                        isActive: isActive,
                    }
                })
                .then(({ data }) => {
                    vm.chats_canceled = data;
                    vm.countCanceled = data.total;
                });
        },
        getAgentTablesCount() {
            var vm = this;
            vm.loading_counters = true;
            const api = "/tickets/get-counter";
            axios
                .get(api, {
                    params: {
                        departments: vm.company_department,
                        my_chats: vm.filter_my_tickets ? 1 : 0,
                        not_category: vm.filter_not_category ? 1 : 0,
                        selected: vm.selected,
                        searchQuery: vm.searchQuery
                    }
                })
                .then(({ data }) => {
                    data.opened >= 1
                        ? (vm.countOnQueue = data.opened)
                        : (vm.countOnQueue = "");
                    data.in_progress >= 1
                        ? (vm.countInProgress = data.in_progress)
                        : (vm.countInProgress = "");
                    data.canceled >= 1
                        ? (vm.countCanceled = data.canceled)
                        : (vm.countCanceled = "");
                    data.overdue >= 1
                        ? (vm.countOverdue = data.overdue)
                        : (vm.countOverdue = "");

                    let finished = 0;
                    data.resolved >= 1
                        ? (finished = finished + data.resolved)
                        : (finished = finished);
                    data.closed >= 1
                        ? (finished = finished + data.closed)
                        : (finished = finished);
                    vm.countResolved = finished;
                })
                .finally(() => {
                    vm.loading_counters = false;
                })

        },
        getDepartmentsByAgent() {
            var vm = this;
            const prefix = "company-user-company-department";
            const api = `${vm.admin ? "get-departments-by-company" : prefix + "/get-department-by-agent"
                }`;

            axios.get(prefix + "/get-department-by-agent").then(({ data }) => {
                data.forEach(element => {
                    element.name = vm.$t(element.name);
                });
                let local_filter = JSON.parse(
                    localStorage.getItem("filter_departments_ticket")
                );
                if (local_filter) {
                    local_filter.forEach(e1 => {
                        let index = data.findIndex(e2 => e2.id === e1.id);

                        if (index !== -1) {
                            vm.company_department.push(e1);
                        }
                    });
                } else {
                    vm.company_department = data;
                }

                vm.departments = data;
                if (data == "") {
                    $("#modalDepartmentNot").modal("show");
                }

                //vm.showTableComponent("queue");
                // this.handleDepartmentsFilter();
                // this.getChatsInProgress();
                // this.getAgentTablesCount();
            });
        },
        getDepartmentComands(company_department_id) {
            return new Promise((resolve, reject) => {
                var vm = this;
                axios
                    .get("get-department-settings", {
                        params: {
                            id: company_department_id
                        }
                    })
                    .then(({ data }) => {
                        let settings = JSON.parse(data[0]["settings"]);
                        let commands = settings.commands;
                        commands.forEach(element => {
                            element.description = vm.$t(element.description);
                        });
                        let inactivityMessage =
                            settings.quant_limity.inactivityMessage;
                        let default_commands = vm.setDefaultCommands();
                        default_commands.forEach(element => {
                            commands.push(element);
                        });
                        vm.departmentCommands = commands;
                        vm.time_inactivityMessage = inactivityMessage;
                        resolve();
                    });
            });
        },
        /** @SET */
        setDefaultCommands() {
            let commands = Array;
            let company = this.session_user_company;
            commands = [
                {
                    command: "cl_name",
                    description: this.$t(this.chat.client.name),
                    status: null
                },
                {
                    command: "cl_email",
                    description: this.chat.client.email,
                    status: null
                },
                {
                    command: "company",
                    description: company.name,
                    status: null
                },
                {
                    command: "dept",
                    description: this.$t(this.chat.department),
                    status: null
                }
            ];
            return commands;
        },
        setMessageComponent(type) {
            return this.messageComponent[type];
        },
        setMessageProps(message, index) {
            return {
                comp_user_comp_depart_id_current: this.chat
                    .comp_user_comp_depart_id_current,
                message: this.chat_history[index],
                formatTime: this.UTCtoClientTZ2,
                index: index,
                chat_history: this.chat_history
            };
        },
        setInfo(info, clear) {
            return new Promise((resolve, reject) => {
                if (clear) {
                    let client = this.chat.client;
                    Object.keys(this.chat).forEach(k => (this.chat[k] = ""));
                    this.chat.client = client;
                    Object.keys(this.chat.client).forEach(
                        k => (this.chat.client[k] = "")
                    );
                }

                // info do chat
                this.info_chat_id = info.ticket_id;
                this.chat.id = info.id;
                this.chat.show = false;
                this.chat.chat_id = info.chat_id;
                this.chat.chat_id_crypt = info.chat_id_crypt;
                this.chat.ticket_id = info.ticket_id;
                this.chat.is_merged = info.is_merged;

                if (info.companyDepartmentId) {
                    this.chat.companyDepartmentId = info.companyDepartmentId;
                } else {
                    this.chat.companyDepartmentId = info.company_department_id;
                }
                this.chat.comp_user_comp_depart_id_current = info.company_user_company_department_id;
                this.chat.department = info.department;
                this.chat.department_id = info.department_id;
                this.chat.date = info.date;
                this.chat.time = info.time;
                this.chat.status = info.status;
                this.chat.type = info.type;
                this.chat.chat_type = info.chat_type;
                this.chat.created_at = info.created_at;
                this.chat.number = info.number;
                this.chat.dep_type = info.department_type;
                this.chat.department_type = info.department_type;
                // info do client
                this.chat.client.id = info.client_id;
                this.chat.id_created = info.id_created;
                this.chat.client.name = info.name_created;
                this.chat.name_created = info.name_created;
                this.chat.client.email = info.email_created;
                this.chat.email_created = info.email_created;
                this.chat.client.browser = info.user_agent;
                this.chat.user_agent = info.user_agent;
                this.chat.show = true;
                this.chat.client_id = info.client_id;
                this.chat.name = info.name_created;
                this.chat.email = info.email_created;
                this.chat.email_attendant = info.email;
                if (info.builderall_account_data != null) {
                    this.chat.client.builderall_account_data =
                        info.builderall_account_data;
                    this.chat.builderall_account_data =
                        info.builderall_account_data;
                    var ba_acct = JSON.parse(info.builderall_account_data);
                    this.chat.is_vip = ba_acct["is_vip"];
                } else {
                    this.chat.client.builderall_account_data = null;
                    this.chat.builderall_account_data = null;
                }
                this.rs_mouse = 'over';
                this.chat.comments = info.comments;
                this.chat.description = info.description;
                this.chat.settings = info.settings;
                resolve();
            });
        },
        /** @WEBSOCKETS */
        connectToChannels() {
            this.joinInQueueChannel();
            this.joinInProgressChannel();
            this.joinInClosedChannel();
            this.joinInResolvedChannel();
            this.joinInCanceledChannel();
            this.joinInDeletedChannel();
        },
        joinInQueueChannel() {
            /** begin */
            const channel = `ticket-queue`;
            const event = `TicketQueueUpdated`;
            /** join to the channel and listen events */
            Echo.leave(`${channel}.${this.session_user_company.id}`);
            Echo.join(`${channel}.${this.session_user_company.id}`).listen(
                event,
                e => {
                    this.employeeInQueueChannelActions(e.item);
                }
            );
            /** end */
        },
        joinInProgressChannel() {
            /** begin */
            const channel = `full-ticket.progress`;
            const event = `FullTicketInProgress`;
            /** join to the channel and listen events */
            Echo.leave(`${channel}.${this.session_user_company.id}`)
            Echo.join(`${channel}.${this.session_user_company.id}`).listen(
                event,
                e => {
                    /** if the user is a admin, we get the actions for him, else we get the actions of employee */
                    this.employeeProgressChannelActions(e.item);
                }
            );
            /** end */
        },
        joinInClosedChannel() {
            /** begin */
            const channel = `full-ticket.closed`;
            const event = `FullTicketClosed`;
            /** join to the channel and listen events */
            Echo.leave(`${channel}.${this.session_user_company.id}`)
            Echo.join(`${channel}.${this.session_user_company.id}`).listen(
                event,
                e => {
                    /** if the user is a admin, we get the actions for him, else we get the actions of employee */
                    this.employeeClosedChannelActions(e.item);
                }
            );
            /** end */
        },
        joinInResolvedChannel() {
            /** begin */
            const channel = `full-ticket.resolved`;
            const event = `FullTicketResolved`;
            /** join to the channel and listen events */
            Echo.leave(`${channel}.${this.session_user_company.id}`)
            Echo.join(`${channel}.${this.session_user_company.id}`).listen(
                event,
                e => {
                    this.employeeResolvedChannelActions(e.item);
                }
            );
            /** end */
        },
        joinInCanceledChannel() {
            /** begin */
            const channel = `full-ticket.canceled`;
            const event = `FullTicketCanceled`;
            /** join to the channel and listen events */
            Echo.leave(`${channel}.${this.session_user_company.id}`)
            Echo.join(`${channel}.${this.session_user_company.id}`).listen(
                event,
                e => {
                    this.employeeCanceledChannelActions(e.item);
                }
            );
            /** end */
        },
        joinInDeletedChannel() {
            /** begin */
            const channel = `chat-ticket-delete`;
            const event = `ChatTicketDelete`;
            /** join to the channel and listen events */
            Echo.leave(`${channel}.${this.session_user_company.id}`)
            Echo.join(`${channel}.${this.session_user_company.id}`).listen(
                event,
                e => {
                    if (e.item.type == 'TICKET') {
                        switch (e.item.status) {
                            case 'IN_PROGRESS':

                                this.employeeProgressChannelActions({
                                    action: 'splice',
                                    id: e.item.id,
                                });
                                break;

                            case 'RESOLVED':
                            case 'CLOSED':
                                this.employeeClosedChannelActions({
                                    action: 'splice',
                                    id: e.item.id,
                                });
                                break;

                            case 'OPENED':
                                this.employeeInQueueChannelActions({
                                    action: 'splice',
                                    id: e.item.id,
                                });
                                break;

                            case 'CANCELED':

                                if (this.chats_canceled.data) {
                                    let index = this.chats_canceled.data.findIndex(
                                        element => element.id === e.item.id
                                    );

                                    let current_page = this.chats_canceled.current_page;
                                    let last_page = this.chats_canceled.last_page;
                                    let per_page = this.chats_canceled.per_page;
                                    let data = this.chats_canceled.data;

                                    if (index !== -1) {
                                        if (data.length == 1 && current_page != 1) {
                                            this.getChatsCanceled(current_page - 1);
                                        } else if (data.length == per_page) {
                                            this.getChatsCanceled(current_page);
                                        } else if (data.length < per_page && current_page == 1) {
                                            this.chats_canceled.data.splice(index, 1);
                                        }
                                    } else {
                                        if (data.length == 1 && current_page != 1) {
                                            this.getChatsCanceled(current_page - 1);
                                        } else {
                                            this.getChatsCanceled(current_page);
                                        }
                                    }
                                }

                                if (this.countCanceled) {
                                    this.countCanceled--;
                                }

                                break;

                            default:
                                break;
                        }
                    }
                }
            );
            /** end */
        },
        employeeInQueueChannelActions(item) {
            switch (item.action) {
                /** add item to the queue */
                case "push":

                    /** only push if user belongs to the department of event item  */

                    /** push item to the queue  */

                    let i = this.filter_departments.findIndex(
                        f => f.id === item.department_id
                    );

                    if (i !== -1) {

                        if (this.countOnQueue) {
                            this.countOnQueue++;
                        } else {
                            this.countOnQueue = 1;
                        }

                        let current_page = this.chats_on_queue.current_page;
                        let last_page = this.chats_on_queue.last_page;
                        let per_page = this.chats_on_queue.per_page;
                        let data = this.chats_on_queue.data;

                        if (this.showTableQueue) {
                            this.getChatsOnQueue(current_page);
                        }
                    }

                    break;
                /** remove item from queue */
                case "splice":
                    /** find the position of event item on array */
                    let index = this.chats_on_queue.data.findIndex(
                        element => element.id === item.id
                    );

                    let current_page = this.chats_on_queue.current_page;
                    let last_page = this.chats_on_queue.last_page;
                    let per_page = this.chats_on_queue.per_page;
                    let data = this.chats_on_queue.data;

                    if (index !== -1) {
                        if (data.length == 1 && current_page != 1) {
                            this.getChatsOnQueue(current_page - 1);
                        } else if (data.length == per_page) {
                            this.getChatsOnQueue(current_page);
                        } else if (data.length < per_page && current_page == last_page) {
                            this.chats_on_queue.data.splice(index, 1);
                        }

                    } else {
                        if (data.length == 1 && current_page != 1) {
                            this.getChatsOnQueue(current_page - 1);
                        } else {
                            this.getChatsOnQueue(current_page);
                        }
                    }

                    if (this.countOnQueue) {
                        this.countOnQueue--;
                    }

                    break;
            }
        },
        employeeProgressChannelActions(item) {
            let my_chat = this.cucd.findIndex(
                c => c.company_user_company_department_id === item.company_user_company_department_id
            );

            // se o "meus chats" estiver marcado só passa oq é meu, se nao passa tudo normal
            if (!this.filter_my_tickets || (my_chat !== -1 && this.filter_my_tickets)) {
                switch (item.action) {
                    case "push":
                        let i = this.filter_departments.findIndex(
                            f => f.id === item.department_id
                        );

                        if (i !== -1) {
                            if (this.countInProgress) {
                                this.countInProgress++;
                            } else {
                                this.countInProgress = 1;
                            }

                            if (this.chats_in_progress.data) {
                                let current_page = this.chats_in_progress.current_page;
                                let last_page = this.chats_in_progress.last_page;
                                let per_page = this.chats_in_progress.per_page;
                                let data = this.chats_in_progress.data;

                                // if (data.length < per_page && current_page == 1) {
                                //     this.chats_in_progress.data.unshift(item);
                                // } else {
                                //     this.getChatsInProgress(current_page);
                                // }
                                if (this.showTableInProgress || this.showTableOverdue) {
                                    this.getChatsInProgress(current_page);
                                }
                            }

                            this.getChatsOverdue();
                        }
                        break;
                    case "splice":
                        //recupero o chat do evento e encontro a index dele no objeto
                        if (this.chats_in_progress.data) {

                            let index = this.chats_in_progress.data.findIndex(
                                element => element.id === item.id
                            );

                            let current_page = this.chats_in_progress.current_page;
                            let last_page = this.chats_in_progress.last_page;
                            let per_page = this.chats_in_progress.per_page;
                            let data = this.chats_in_progress.data;

                            if (index !== -1) {
                                if (data.length == 1 && current_page != 1) {
                                    this.getChatsInProgress(current_page - 1);
                                } else if (data.length == per_page) {
                                    this.getChatsInProgress(current_page);
                                } else if (data.length < per_page && current_page == 1) {
                                    this.chats_in_progress.data.splice(index, 1);
                                }
                            } else {
                                if (data.length == 1 && current_page != 1) {
                                    this.getChatsInProgress(current_page - 1);
                                } else {
                                    this.getChatsInProgress(current_page);
                                }
                            }
                        }

                        if (this.countInProgress) {
                            this.countInProgress--;
                        }

                        this.getChatsOverdue();

                        break;
                }
            }
        },
        employeeClosedChannelActions(item) {
            let my_chat = this.cucd.findIndex(
                c => c.company_user_company_department_id === item.company_user_company_department_id
            );

            // se o "meus chats" estiver marcado só passa oq é meu, se nao passa tudo normal
            if (
                !this.filter_my_tickets ||
                (my_chat !== -1 && this.filter_my_tickets)
            ) {
                switch (item.action) {
                    case "push":
                        let i = this.filter_departments.findIndex(
                            f => f.id === item.department_id
                        );

                        if (i !== -1) {
                            if (this.countResolved) {
                                this.countResolved++;
                            } else {
                                this.countResolved = 1;
                            }

                            if (this.chats_resolved.data) {
                                let current_page = this.chats_resolved.current_page;
                                let last_page = this.chats_resolved.last_page;
                                let per_page = this.chats_resolved.per_page;
                                let data = this.chats_resolved.data;

                                // if (data.length < per_page && current_page == 1) {
                                //     this.chats_resolved.data.push(item);
                                // } else {
                                //     this.getChatsFinished(current_page);
                                // }

                                if (this.showTableResolved) {
                                    this.getChatsFinished(current_page);
                                }
                            }
                        }
                        break;
                    case "splice":
                        //recupero o chat do evento e encontro a index dele no objeto
                        if (this.chats_resolved.data) {

                            let index = this.chats_resolved.data.findIndex(
                                element => element.id === item.id
                            );

                            let current_page = this.chats_resolved.current_page;
                            let last_page = this.chats_resolved.last_page;
                            let per_page = this.chats_resolved.per_page;
                            let data = this.chats_resolved.data;

                            if (index !== -1) {
                                if (data.length == 1 && current_page != 1) {
                                    this.getChatsFinished(current_page - 1);
                                } else if (data.length == per_page) {
                                    this.getChatsFinished(current_page);
                                } else if (data.length < per_page && current_page == 1) {
                                    this.chats_resolved.data.splice(index, 1);
                                }
                            } else {
                                if (data.length == 1 && current_page != 1) {
                                    this.getChatsFinished(current_page - 1);
                                } else {
                                    this.getChatsFinished(current_page);
                                }
                            }
                        }

                        if (this.countResolved) {
                            this.countResolved--;
                        }

                        break;
                }
            }
        },
        employeeResolvedChannelActions(item) {
            let my_chat = this.cucd.findIndex(
                c => c.company_user_company_department_id === item.company_user_company_department_id
            );

            // se o "meus chats" estiver marcado só passa oq é meu, se nao passa tudo normal
            if (
                !this.filter_my_tickets ||
                (my_chat !== -1 && this.filter_my_tickets)
            ) {
                switch (item.action) {
                    case "push":
                        let i = this.filter_departments.findIndex(
                            f => f.id === item.department_id
                        );

                        if (i !== -1) {
                            if (this.countResolved) {
                                this.countResolved++;
                            } else {
                                this.countResolved = 1;
                            }

                            if (this.chats_resolved.data) {
                                let current_page = this.chats_resolved.current_page;
                                let last_page = this.chats_resolved.last_page;
                                let per_page = this.chats_resolved.per_page;
                                let data = this.chats_resolved.data;

                                // if (data.length < per_page && current_page == 1) {
                                //     this.chats_resolved.data.unshift(item);
                                // } else {
                                //     this.getChatsFinished(current_page);
                                // }
                                if (this.showTableCanceled) {
                                    this.getChatsFinished(current_page);
                                }
                            }
                        }
                        break;
                    case "splice":
                        //recupero o chat do evento e encontro a index dele no objeto
                        if (this.chats_resolved.data) {
                            let index = this.chats_resolved.data.findIndex(
                                element => element.id === item.id
                            );

                            let current_page = this.chats_resolved.current_page;
                            let last_page = this.chats_resolved.last_page;
                            let per_page = this.chats_resolved.per_page;
                            let data = this.chats_resolved.data;

                            if (index !== -1) {
                                if (data.length == 1 && current_page != 1) {
                                    this.getChatsFinished(current_page - 1);
                                } else if (data.length == per_page) {
                                    this.getChatsFinished(current_page);
                                } else if (data.length < per_page && current_page == 1) {
                                    this.chats_resolved.data.splice(index, 1);
                                }
                            } else {
                                if (data.length == 1 && current_page != 1) {
                                    this.getChatsFinished(current_page - 1);
                                } else {
                                    this.getChatsFinished(current_page);
                                }
                            }
                        }

                        if (this.countResolved) {
                            this.countResolved--;
                        }

                        break;
                }
            }
        },
        employeeCanceledChannelActions(item) {
            let my_chat = this.cucd.findIndex(
                c => c.company_user_company_department_id === item.company_user_company_department_id
            );

            // se o "meus chats" estiver marcado só passa oq é meu, se nao passa tudo normal
            if (
                !this.filter_my_tickets ||
                (my_chat !== -1 && this.filter_my_tickets)
            ) {
                switch (item.action) {
                    case "push":

                        let i = this.filter_departments.findIndex(
                            f => f.id === item.department_id
                        );

                        if (i !== -1) {
                            if (this.countCanceled) {
                                this.countCanceled++;
                            } else {
                                this.countCanceled = 1;
                            }

                            if (this.chats_canceled.data) {
                                let current_page = this.chats_canceled.current_page;
                                let last_page = this.chats_canceled.last_page;
                                let per_page = this.chats_canceled.per_page;
                                let data = this.chats_canceled.data;

                                // if (data.length < per_page && current_page == 1) {
                                //     this.chats_canceled.data.unshift(item);
                                // } else {
                                //     this.getChatsCanceled(current_page);
                                // }
                                this.getChatsCanceled(current_page);
                            }
                        }

                        break;
                    case "splice":
                        //recupero o chat do evento e encontro a index dele no objeto
                        if (this.chats_canceled.data) {
                            let index = this.chats_canceled.data.findIndex(
                                element => element.id === item.id
                            );

                            let current_page = this.chats_canceled.current_page;
                            let last_page = this.chats_canceled.last_page;
                            let per_page = this.chats_canceled.per_page;
                            let data = this.chats_canceled.data;

                            if (index !== -1) {
                                if (data.length == 1 && current_page != 1) {
                                    this.getChatsCanceled(current_page - 1);
                                } else if (data.length == per_page) {
                                    this.getChatsCanceled(current_page);
                                } else if (data.length < per_page && current_page == 1) {
                                    this.chats_canceled.data.splice(index, 1);
                                }
                            } else {
                                if (data.length == 1 && current_page != 1) {
                                    this.getChatsCanceled(current_page - 1);
                                } else {
                                    this.getChatsCanceled(current_page);
                                }
                            }
                        }

                        if (this.countCanceled) {
                            this.countCanceled--;
                        }

                        break;
                }
            }
        },
        /** @STORAGE */
        getContentOnLocalStorage(number) {
            if (
                localStorage.getItem("chatContent") &&
                localStorage.getItem("chatContent") !== "[]"
            ) {
                let index = this.chatContent.findIndex(
                    item => item.number === number
                );
                if (index !== -1) {
                    var content = this.chatContent[index].content;
                    if (this.chat.number == number) {
                        this.chat.content = content;
                    }
                }
            }
        },
        putContentOnLocalStorage(chat) {
            const storagedChats = JSON.parse(
                localStorage.getItem("chatContent")
            );
            let i = 0;
            let data = [];
            let exists = false;

            if (localStorage.getItem("chatContent")) {
                for (
                    let index = 1;
                    index <= Object.keys(storagedChats).length;
                    index++
                ) {
                    data[i] = storagedChats[i];
                    if (storagedChats[i]["number"] === chat.number) {
                        exists = true;
                        storagedChats[i]["content"] = chat.content;
                        localStorage.setItem(
                            "chatContent",
                            JSON.stringify(storagedChats)
                        );
                        this.chatContent = storagedChats;
                    }
                    i++;
                }
                if (!exists) {
                    data[i] = chat;
                    localStorage.setItem("chatContent", JSON.stringify(data));
                    this.chatContent = data;
                }
            } else {
                data[0] = chat;
                localStorage.setItem("chatContent", JSON.stringify(data));
                this.chatContent = data;
            }
        },
        addFooterActiveChat(chat, type) {
            chat.action = "add";
            axios
                .post("tabs", {
                    chat: chat,
                    type: type
                })
                .then(response => { });
        },
        addFooterActiveChatWhenReceivingMessage(event) {
            switch (event.action) {
                case "update":
                    let index2 = this.$store.state.chatsFooter.findIndex(
                        item => item.chat_id === event.chat_id
                    );

                    if (index2 !== -1) {
                        if (event.agent_answered) {
                            // se foi o atendente
                            this.$store.state.chatsFooter[index2].answered = 0;
                            let old = this.$store.state.chatsFooter;
                            localStorage.setItem(
                                "chatsFooter",
                                JSON.stringify(old)
                            );
                            this.$store.state.chatsFooter = [];
                            this.$store.state.chatsFooter = old;
                        } else {
                            // se foi o cliente
                            this.$store.state.chatsFooter[index2].answered = 1;
                            let old = this.$store.state.chatsFooter;
                            localStorage.setItem(
                                "chatsFooter",
                                JSON.stringify(old)
                            );
                            this.$store.state.chatsFooter = [];
                            this.$store.state.chatsFooter = old;
                        }
                    } else {
                        delete event["company_user_company_department_id"];
                        delete event["content"];
                        delete event["id"];
                        delete event["subsidiary_id"];
                        delete event["email_verified_at"];
                        delete event["phone"];
                        delete event["language"];
                        delete event["hash_code"];
                        delete event["can_create_company"];
                        delete event["updated_at"];
                        delete event["deleted_at"];
                        delete event["created_by"];
                        delete event["updated_by"];
                        delete event["deleted_by"];
                        delete event["action"];
                        delete event["user_agent_id"];
                        delete event["agent_answered"];
                        event.type = "DEFAULT";
                        this.addFooterActiveChat(event, 'ticket');
                    }
            }
        },
        /** @OTHERS */
        openChat(chat) {
            // console.log(chat);
            if (this.restriction[0].ticket_open == 1 || this.is_admin == 1 || this.restriction[0].ticket_admin == 1) {
                if (
                    (this.showChat == false && this.info_chat_id == chat.ticket_id) ||
                    chat.ticket_id !== this.chat.ticket_id ||
                    (this.showChat === false && chat.ticket_id !== this.chat.ticket_id)
                ) {
                    this.clearActiveChat();
                    this.setInfo(chat, true).then(() => {
                        this.openChatActions(this.chat);
                    })
                }
            } else {
                this.$snotify.info(
                    this.$t("bs-you-are-not-allowed-to-open-a-ticket") + "!",
                    this.$t("bs-info")
                );
            }
        },
        openChatActions(chat) {
            var vm = this;
            vm.loading_messages = true;
            axios.post(`ticket-add-chat-admin`, {
                itemselected: chat,
                restrictions: JSON.parse(chat.settings)
            }).then(function (response) {
                if (response.data.success) {
                    if (response.data.value == "opened") {
                        vm.chat.update_status_in_progress = response.data.update_status_in_progress;
                        vm.chat.email = response.data.email;
                        chat.email_attendant = response.data.email;
                    }
                    // console.log(vm.chat);
                    // console.log(chat);
                    vm.showChatComponent();
                    vm.addFooterActiveChat(chat, 'ticket');
                } else {
                    vm.loading_messages = false;
                    if (response.data.value == "not_opened_limity") {
                        vm.$snotify.info(vm.$t('bs-active-ticket-limit-exceeded'), vm.$t('bs-info'));
                    }
                }
            }).catch(function (erro) {
                vm.loading_messages = false;
                vm.$snotify.error(vm.$t('bs-error-trying-to-save'), vm.$t('bs-error'));
            });
        },
        openMultipleTickets(chat) {
            var vm = this;
            vm.loading_messages = true;
            vm.$loading(true);
            if (vm.restriction[0].ticket_open == 1 || vm.is_admin == 1 || vm.restriction[0].ticket_admin == 1) {
                axios.post(`ticket-add-chat-admin`, {
                    itemselected: chat,
                    restrictions: JSON.parse(chat.settings)
                }).then(function (response) {
                    if (response.data.success) {
                        if (response.data.value == "opened") {
                            chat.update_status_in_progress = response.data.update_status_in_progress;
                            chat.name = chat.name_created;
                            chat.email = chat.email_created;
                            chat.answered = 0;
                        }
                        // vm.showChatComponent();
                        // console.log(chat);
                        vm.$loading(false);
                        vm.addFooterActiveChat(chat, 'ticket');
                    } else {
                        vm.loading_messages = false;
                        vm.$loading(false);
                        if (response.data.value == "not_opened_limity") {
                            vm.$snotify.info(vm.$t('bs-active-ticket-limit-exceeded'), vm.$t('bs-info'));
                        }
                    }
                }).catch(function (erro) {
                    vm.$loading(false);
                    vm.loading_messages = false;
                    vm.$snotify.error(vm.$t('bs-error-trying-to-save'), vm.$t('bs-error'));
                });


            } else {
                this.$snotify.info(
                    this.$t("bs-you-are-not-allowed-to-open-a-ticket") + "!",
                    this.$t("bs-info")
                );
            }
        },
        chatScrollTop() {
            var chat = document.getElementById("chat-main")
            if (chat) {
                chat.scrollTop = chat.scrollHeight - chat.clientHeight;
            }
        },
        setIncognitoMode() {
            let index = this.cucd.findIndex(item => item.company_department_id === this.chat.companyDepartmentId);

            if (index !== -1) {
                if (this.cucd[index].company_user_company_department_id !== this.chat.comp_user_comp_depart_id_current) {
                    this.take_on_chat = true;
                    this.incognito_mode = true;
                } else {
                    this.take_on_chat = false;
                    this.incognito_mode = false;
                }
                this.incognito_id = this.cucd[index].company_user_company_department_id;
            } else {
                this.take_on_chat = true;
                this.incognito_mode = true;
            }
        },
        getExtensions() {
            this.extensions = this.extImages.concat(
                this.extDocuments,
                this.extSpreadsheets,
                this.extPresentation
            );
        },
        handleDepartmentsFilter() {
            /** if the variable is empty, I put all departments in it */
            if (!this.company_department.length) {
                this.company_department = this.departments;
            }
            /** set filter_departments variable */
            this.filter_departments = this.company_department;
            //this.getChatsOnQueue();
            this.showTableQueue = false;
            this.showTableComponent('queue');
            if (this.showTableQueue) {
                this.showTableQueue = false;
                this.showTableComponent('queue');
            } else if (this.showTableInProgress) {
                this.showTableInProgress = false;
                this.showTableComponent('inProgress');
            } else if (this.showTableOverdue) {
                this.showTableOverdue = false;
                this.showTableComponent('overdue');
            } else if (this.showTableResolved) {
                this.showTableResolved = false;
                this.showTableComponent('resolved');
            } else if (this.showTableCanceled) {
                this.showTableCanceled = false;
                this.showTableComponent('canceled');
            }
            /** delete settings prop for each department in filter variable, this way will not send settings to the route when the filters changes */
            this.filter_departments.forEach(element => {
                delete element.settings;
            });

            localStorage.setItem(
                "filter_departments_ticket",
                JSON.stringify(this.company_department)
            );
        },
        changeTableKeys() {
            /** update keys */
            this.key_queue = this.key_queue += 1;
            this.key_progress = this.key_progress += 1;
            this.key_transferred = this.key_transferred += 1;
            this.key_closed = this.key_closed += 1;
            this.key_resolved = this.key_resolved += 1;
            this.key_canceled = this.key_canceled += 1;
        },
        openClientHistory(id, merged = false) {
            var vm = this;
            var api = 'client/get-client-history';

            if(merged){
                api = `client/get-client-history-merged`;
            }

            axios
                .get(api, {
                    params: {
                        client_id: id
                    }
                })
                .then(({ data }) => {
                    var itemsProcessed = 0;

                    var clientChatHistory = [];
                    var clientTicketHistory = [];

                    data.forEach(element => {
                        itemsProcessed++;

                        if (
                            element.type === "TICKET" ||
                            element.type === "CHANGED_TO_TICKET"
                        ) {
                            clientTicketHistory.push(element);
                        } else {
                            clientChatHistory.push(element);
                        }

                        if (itemsProcessed === data.length) {

                            vm.clientTicketHistory = clientTicketHistory;
                            vm.clientChatHistory = clientChatHistory;

                        }
                    });
                    $("#modalClientHistory").modal("show");
                });
        },
        openCategory() {
            this.showCategory = true;
            this.$store.state.showAlertCategory = false;
        },
        activeIncognito() {
            if (!this.take_on_chat) {
                this.incognito_mode = !this.incognito_mode;
            } else {
                this.incognito_mode = true;
            }
        },
        concatEmoji(emoji) {
            this.chat.content = this.chat.content.concat(emoji.data);
            document.getElementById("input").focus();
        },
        LI(value) {
            if (value == null) {
                return "LI";
            }
            return value.substr(0, 2);
        },
        onResize(e) {
            if ($(window).width() <= 1120) {
                var sidebar_is_mini = this.$store.state.sidebar_is_mini;
                if (this.show_info && this.show_filter && sidebar_is_mini) {
                    this.$store.commit("toggleSidebarMini");
                }
            }
            if ($(window).width() <= 992) {
                this.isMobile = true;
            } else {
                this.isMobile = false;
                this.$root.$emit('bv::hide::popover')
            }
        },
        nameWithLang({ name, status }) {
            return `${name} — [${status}]`;
        },
        /** @NOTIFICATIONS */
        notifyQueue() {
            if (!this.showTableQueue) {
                this.notify.onQueue = true;
            }
        },
        notifyInProgress() {
            if (!this.showTableInProgress) {
                this.notify.inProgress = true;
            }
            //this.notify.audio.play();
        },
        notifyTransferred() {
            if (!this.showTableTransferred) {
                this.notify.transferred = true;
            }
        },
        notifyClosed() {
            if (!this.showTableClosed) {
                this.notify.closed = true;
            }
        },
        notifyResolved() {
            if (!this.showTableResolved) {
                this.notify.resolved = true;
            }
        },
        notifyCanceled() {
            if (!this.showTableCanceled) {
                this.notify.canceled = true;
            }
        },
        /** @SHOW */
        showModalAtendent() {
            if (this.selected_department !== null) {
                var vm = this;
                vm.$loading(true);
                axios
                    .get("user-auth/get-agents-by-department", {
                        params: {
                            company_department_id: vm.selected_department.id
                        }
                    })
                    .then(response => {
                        if (response.data.agents.length) {
                            vm.agents_to_transfer = response.data.agents;

                            $("#modalDepartment").modal("hide");
                            $("#modalAtendent").modal("show");
                            vm.$loading(false);
                        } else {
                            Swal.fire({
                                heightAuto: false,
                                icon: "error",
                                title: vm.$t("bs-error"),
                                text: vm.$t(
                                    "bs-no-active-attendants-in-the-department"
                                )
                            });
                            vm.$loading(false);
                        }
                    });
            }
        },
        treeUnselect() {
            let item = this.$refs.tree.findAll({ state: { selected: true } })
            item.unselect();

        },
        showTableComponent(table) {
            if (this.hideOnSmall) {
                this.hideOnSmall = !this.hideOnSmall;
            }
            this.hidden = true;
            switch (table) {
                case "inProgress":
                    if (!this.showTableInProgress) {
                        this.treeUnselect();
                        this.getChatsInProgress().then(() => {
                            // if(this.chats_in_progress.data.length != 0){
                            //      this.$root.$refs.TableInProgress.callSetInfo(this.chats_in_progress.data[0]);
                            // }
                        })
                        this.chats_in_progress = {};
                        this.showTableInProgress = true;
                        this.showTableQueue = false;
                        this.showTableTransferred = false;
                        this.showTableClosed = false;
                        this.showTableResolved = false;
                        this.showTableCanceled = false;
                        this.showTableOverdue = false;
                        this.notify.inProgress = false;
                        let selection = this.$refs.tree.findAll({ text: this.$t("bs-in-progress") })
                        selection.select(true)
                    }
                    break;
                case "overdue":
                    if (!this.showTableOverdue) {
                        this.treeUnselect();
                        this.getChatsOverdue().then(() => {
                            // if(this.chats_overdue.data.length != 0){
                            //     this.$root.$refs.TableInProgress.callSetInfo(this.chats_overdue.data[0]);
                            // }
                        })
                        this.chats_overdue = {};
                        this.showTableInProgress = false;
                        this.showTableQueue = false;
                        this.showTableTransferred = false;
                        this.showTableClosed = false;
                        this.showTableResolved = false;
                        this.showTableCanceled = false;
                        this.showTableOverdue = true;
                        this.notify.inProgress = false;
                        let selection = this.$refs.tree.findAll({ text: this.$t("bs-overdue") })
                        selection.select(true)
                    }
                    break;
                case "queue":
                    if (!this.showTableQueue) {
                        this.treeUnselect();
                        this.getChatsOnQueue().then(() => {
                            //  if(this.chats_on_queue.data.length != 0){
                            //     this.$root.$refs.TableQueue.callSetInfo(this.chats_on_queue.data[0]);
                            // }
                        });
                        this.chats_on_queue = {};
                        this.showTableQueue = true;
                        this.showTableInProgress = false;
                        this.showTableTransferred = false;
                        this.showTableClosed = false;
                        this.showTableResolved = false;
                        this.showTableCanceled = false;
                        this.showTableOverdue = false;
                        this.notify.onQueue = false;
                        let selection = this.$refs.tree.findAll({ text: this.$t("bs-in-queue") })
                        selection.select(true)
                    }
                    break;
                case "transferred":
                    if (!this.showTableTransferred) {
                        this.chats_transferred = {};
                        this.showTableTransferred = true;
                        this.showTableInProgress = false;
                        this.showTableQueue = false;
                        this.showTableClosed = false;
                        this.showTableResolved = false;
                        this.showTableCanceled = false;
                        this.showTableOverdue = false;
                        this.notify.transferred = false;
                    }
                    break;
                case "closed":
                    if (!this.showTableClosed) {
                        this.chats_closed = {};
                        this.showTableTransferred = false;
                        this.showTableInProgress = false;
                        this.showTableQueue = false;
                        this.showTableClosed = true;
                        this.showTableResolved = false;
                        this.showTableCanceled = false;
                        this.showTableOverdue = false;
                        this.notify.closed = false;
                    }
                    break;
                case "resolved":
                    if (!this.showTableResolved) {
                        this.treeUnselect();
                        this.getChatsFinished();
                        this.chats_resolved = {};
                        this.showTableTransferred = false;
                        this.showTableInProgress = false;
                        this.showTableQueue = false;
                        this.showTableClosed = false;
                        this.showTableResolved = true;
                        this.showTableCanceled = false;
                        this.showTableOverdue = false;
                        this.notify.resolved = false;
                        let selection = this.$refs.tree.findAll({ text: this.$t("bs-finished-s") })
                        selection.select(true)
                    }
                    break;
                case "canceled":
                    if (!this.showTableCanceled) {
                        this.treeUnselect();
                        this.getChatsCanceled();
                        this.chats_canceled = {};
                        this.showTableTransferred = false;
                        this.showTableInProgress = false;
                        this.showTableQueue = false;
                        this.showTableClosed = false;
                        this.showTableResolved = false;
                        this.showTableCanceled = true;
                        this.showTableOverdue = false;
                        this.notify.canceled = false;
                        let selection = this.$refs.tree.findAll({ text: this.$t("bs-canceled-s") })
                        selection.select(true)
                    }
                    break;
            }
            this.clearActiveChat();
        },
        showChatComponent() {
            this.chat_history = [];
            this.showTableQueue = false;
            this.showTableInProgress = false;
            this.showTableTransferred = false;
            this.showTableClosed = false;
            this.showTableResolved = false;
            this.showTableCanceled = false;
            this.showChat = true;
            this.hideOnSmall = false;
        },
        backToMenu() {
            this.clearActiveChat();
            this.hideOnSmall = !this.hideOnSmall;
            this.hidden = true;
            this.showTableQueue = false;
            this.showTableInProgress = false;
            this.showTableTransferred = true;
            this.showTableClosed = false;
            this.showTableResolved = false;
            this.showTableCanceled = false;
            //this.showTableQueue = true;
            //this.showTableComponent('queue');
        },
        /** @CLEAR */
        clearActiveChat() {
            return new Promise((resolve, reject) => {
                if (this.chat.chat_id != "" && this.chat.chat_id != undefined) {
                    this.putContentOnLocalStorage({
                        number: this.chat.number,
                        content: this.chat.content
                    });
                }
                Echo.leave(`chat.${this.chat.chat_id}`);
                Echo.leave(`chat-status-changer.${this.chat.chat_id}`);
                this.showChat = false;
                let client = this.chat.client;
                Object.keys(this.chat).forEach(k => (this.chat[k] = ""));
                this.chat.client = client;
                Object.keys(this.chat.client).forEach(
                    k => (this.chat.client[k] = "")
                );
                this.incognito_mode = false;
                this.incognito_id = null;
                this.chat.show = false;
                this.info_minimized = false;
                this.rs_mouse = 'leave'
                this.chat_catched_id = ""
                resolve();
            });
        },
        clearAllChats() {
            /** cleat actual chat */
            this.clearActiveChat();
            /** reset skip */
            this.skip = 0;
            /** clear chats */
            this.chats_on_queue = {};
            this.chats_in_progress = {};
            this.chats_overdue = {};
            this.chats_transferred = {};
            this.chats_closed = {};
            this.chats_resolved = {};
            this.chats_canceled = {};
        },
        resetTable(ref) {
            this.skip = 0;
            switch (ref) {
                case "tableQueue":
                    //limpar array
                    this.chats_on_queue = {};
                    break;
                case "tableInProgress":
                    //limpar array
                    this.chats_in_progress = {};
                    break;
                case "tableOverdue":
                    //limpar array
                    this.chats_overdue = {};
                    break;
                case "tableClosed":
                    this.chats_closed = {};
                    break;
                case "tableResolved":
                    this.chats_resolved = {};
                    break;
                case "tableTransferred":
                    //limpar o array
                    this.chats_transferred = {};
                    break;
                case "tableCanceled":
                    this.chats_canceled = {};
                    break;
            }
        },
        /** @TIMEZONE */
        UTCtoClientTZ(h, tz) {
            try {
                let h_format = moment(h, "YYYY-MM-DD HH:mm:ss").format(
                    "YYYY-MM-DD HH:mm:ss"
                );
                let datetime = h_format.split(" ");
                let date = datetime[0];
                let time = datetime[1];
                let date_split = date.split("-");
                let time_split = time.split(":");
                let year = date_split[0];
                let month = date_split[1];
                let day = date_split[2];
                let hour = time_split[0];
                let minute = time_split[1];
                let second = time_split[2];
                var month_integer = parseInt(month, 10);
                if (month_integer >= 1) {
                    month_integer--;
                }
                let dateUTC = new Date(
                    Date.UTC(year, month_integer, day, hour, minute, second)
                );
                let converted_time = dateUTC.toLocaleString("pt-BR", {
                    timeZone: tz
                });
                return moment(converted_time, "DD/MM/YYYY HH:mm:ss").format(
                    "YYYY-MM-DD HH:mm:ss"
                );
            } catch (error) {
                return h;
            }
        },
        UTCtoClientTZ2(h) {
            try {
                let h_format = moment(h, "YYYY-MM-DD HH:mm:ss").format(
                    "YYYY-MM-DD HH:mm:ss"
                );
                let datetime = h_format.split(" ");
                let date = datetime[0];
                let time = datetime[1];
                let date_split = date.split("-");
                let time_split = time.split(":");
                let year = date_split[0];
                let month = date_split[1];
                let day = date_split[2];
                let hour = time_split[0];
                let minute = time_split[1];
                let second = time_split[2];
                var month_integer = parseInt(month, 10);
                if (month_integer >= 1) {
                    month_integer--;
                }
                let dateUTC = new Date(
                    Date.UTC(year, month_integer, day, hour, minute, second)
                );
                let converted_time = dateUTC.toLocaleString("pt-BR", {
                    timeZone: this.tz
                });

                var mt = require("moment-timezone");
                return mt(converted_time, "DD/MM/YYYY HH:mm:ss")
                    .tz(this.tz)
                    .locale(this.session_user.language)
                    .format("LT");
            } catch (error) {
                return h;
            }
        },
        calculateWaitingTime(d, c) {
            var moment = require("moment-timezone");
            var startDateTime = moment
                .tz(d, Intl.DateTimeFormat().resolvedOptions().timeZone)
                .toDate();
            var startStamp = startDateTime.valueOf();

            var newDate = new Date();
            var newStamp = newDate.getTime();

            var timer;

            var diff_0 = false;

            function updateClock() {
                newDate = new Date();
                newStamp = newDate.getTime();
                var diff = Math.round((newStamp - startStamp) / 1000);

                // var d = Math.floor(diff / (24 * 60 * 60));
                // diff = diff - d * 24 * 60 * 60;
                var h = Math.floor(diff / (60 * 60));
                diff = diff - h * 60 * 60;
                var m = Math.floor(diff / 60);
                diff = diff - m * 60;
                var s = diff;

                if (h < 10) {
                    h = "0" + h;
                }

                if (m < 10) {
                    m = "0" + m;
                }

                if (s < 10) {
                    s = "0" + s;
                }

                if (document.getElementById(c) !== null) {
                    document.getElementById(c).innerHTML = h + ":" + m + ":" + s;
                }
            }

            setInterval(updateClock, 1000);
        },
    },
    computed: {
        current_table() {
            if (this.showTableQueue) {
                return this.$t("bs-in-queue");
            } else if (this.showTableInProgress) {
                return this.$t("bs-in-progress")
            } else if (this.showTableOverdue) {
                return this.$t("bs-overdue")
            } else if (this.showTableResolved) {
                return this.$t("bs-finished-s")
            } else if (this.showTableCanceled) {
                return this.$t("bs-lost-s")
            }
        },
        current_table_count() {
            if (this.showTableQueue) {
                return this.countOnQueue;
            } else if (this.showTableInProgress) {
                return this.countInProgress;
            } else if (this.showTableOverdue) {
                return this.countOverdue;
            } else if (this.showTableResolved) {
                return this.countResolved;
            } else if (this.showTableCanceled) {
                return this.countCanceled;
            }
        },
        pp_selected: {
            get() {
                return this.$store.getters.getPerPage;
            },
            set(value) {
                this.$store.commit("setPerPage", value);
            }
        },
        tabs_length() {
            return this.$store.state.chatsFooter.length;
        },
        computedChat: function () {
            return JSON.parse(JSON.stringify(this.chat)); // copy object and remove reactivity
        },
        filter_my_tickets: {
            get() {
                return this.$store.state.filter_my_tickets;
            },
            set(value) {
                this.$store.commit("updateMyTicketsFilter", value);
            }
        },
        filter_not_category: {
            get() {
                return this.$store.state.filter_not_category;
            },
            set(value) {
                this.$store.commit("updateFilterNotCategory", value);
            }
        },
        filter_departments: {
            get() {
                return this.$store.state.filter_departments_ticket;
            },
            set(value) {
                this.$store.commit("updateChatFilterDepartmentsticket", value);
            }
        },
        in_progress() {
            if (this.chats_in_progress.data) {
                return this.chats_in_progress;
            }
        },
        overdue() {
            if (this.chats_overdue.data) {
                return this.chats_overdue
            }
        },
        queue() {
            if (this.chats_on_queue.data) {
                return this.chats_on_queue;
            }
        },
        resolved() {
            if (this.chats_resolved.data) {
                return this.chats_resolved;
            }
        },
        canceled() {
            if (this.chats_canceled.data) {
                return this.chats_canceled;
            }
        },
        show_info() {

            if (this.rs_mouse == 'over') {
                return true;
            } else if (this.rs_mouse == 'leave') {
                return false;
            } else if (this.chat.show) {
                return true;
            }

        },
    },
    watch: {
        company_department(n, o) {
            this.aux_dept = [];
            this.company_department.forEach(element => {
                this.aux_dept.push(element.id);
            });
            /** handle departments filter*/
            this.handleDepartmentsFilter();
            this.$store.commit("getChatsInProgress", this.url_prefix);
            this.getAgentTablesCount();
            /** clear all chats */
            this.clearAllChats();
            /** change table keys */
            this.changeTableKeys();
        },
        filter_not_category() {
            /** cleat actual chat */
            this.clearActiveChat();
            /** reset skip */
            this.skip = 0;
            /** clear chats */
            this.$store.commit("getChatsInProgress", this.url_prefix);
            this.chats_transferred = [];
            this.chats_closed = [];
            this.chats_resolved = [];
            this.chats_canceled = [];
            /** change table keys */
            this.changeTableKeys();
            this.getAgentTablesCount();

            if (this.showTableQueue == true) {
                this.getChatsOnQueue();
            } else if (this.showTableInProgress == true) {
                this.getChatsInProgress();
            } else if (this.showTableOverdue == true) {
                this.getChatsOverdue();
            } else if (this.showTableClosed == true) {
                this.getChatsFinished();
            } else if (this.showTableResolved == true) {
                this.getChatsFinished();
            } else if (this.showTableCanceled == true) {
                this.getChatsCanceled();
            }
        },
        filter_my_tickets() {
            /** cleat actual chat */
            this.clearActiveChat();
            /** reset skip */
            this.skip = 0;
            /** clear chats */
            this.$store.commit("getChatsInProgress", this.url_prefix);
            this.chats_transferred = [];
            this.chats_closed = [];
            this.chats_resolved = [];
            this.chats_canceled = [];
            /** change table keys */
            this.changeTableKeys();
            this.getAgentTablesCount();

            if (this.showTableQueue == true) {
                this.getChatsOnQueue();
            } else if (this.showTableInProgress == true) {
                this.getChatsInProgress();
            } else if (this.showTableOverdue == true) {
                this.getChatsOverdue();
            } else if (this.showTableClosed == true) {
                this.getChatsFinished();
            } else if (this.showTableResolved == true) {
                this.getChatsFinished();
            } else if (this.showTableCanceled == true) {
                this.getChatsCanceled();
            }
        },
        "$store.state.chatsFooter": function () {
            if (
                localStorage.getItem("chatsFooter") &&
                localStorage.getItem("chatsFooter") !== "[]"
            ) {
                this.footerActiveChat = true;
            } else {
                this.footerActiveChat = false;
            }
        },
        computedChat: {
            deep: true,
            handler: function (newVal, oldVal) {
                var chat = document.getElementById("chat-main")
                if (chat) {
                    // chat.scrollTop = chat.scrollHeight - chat.clientHeight;
                    var current = chat.scrollTop;
                    var max = chat.scrollHeight - chat.clientHeight
                    var min = max - 500;
                    if (current > min) {
                        chat.scrollTop = chat.scrollHeight - chat.clientHeight;
                    }
                }
            }
        },
        showChat(newVal, oldVal) {
            if (newVal == true) {
                this.treeUnselect();
            } else {
                this.shortcu_type = "";
                this.$root.$refs.TicketTicket2.showDetails = true;
                this.$root.$refs.TicketTicket2.showActions = false;
            }
        },
        queue(newVal, oldVal) {
            if (newVal && oldVal) {
                if (newVal.current_page !== oldVal.current_page) {
                    this.chat.show = false;
                    this.rs_mouse = 'leave';
                }
            }
        },
        in_progress(newVal, oldVal) {
            if (newVal && oldVal) {
                if (newVal.current_page !== oldVal.current_page) {
                    this.chat.show = false;
                    this.rs_mouse = 'leave';
                }
            }
        },
        overdue(newVal, oldVal) {
            if (newVal && oldVal) {
                if (newVal.current_page !== oldVal.current_page) {
                    this.chat.show = false;
                    this.rs_mouse = 'leave';
                }
            }
        },
        resolved(newVal, oldVal) {
            if (newVal && oldVal) {
                if (newVal.current_page !== oldVal.current_page) {
                    this.chat.show = false;
                    this.rs_mouse = 'leave';
                }
            }
        },
        canceled(newVal, oldVal) {
            if (newVal && oldVal) {
                if (newVal.current_page !== oldVal.current_page) {
                    this.chat.show = false;
                    this.rs_mouse = 'leave';
                }
            }
        },
        pp_selected() {
            if (this.showTableQueue) {
                this.getChatsOnQueue();
            } else if (this.showTableInProgress) {
                this.getChatsInProgress();
            } else if (this.showTableOverdue) {
                this.getChatsOverdue();
            } else if (this.showTableResolved) {
                this.getChatsFinished();
            } else if (this.showTableCanceled) {
                this.getChatsCanceled();
            }
        },
        searchQuery(newVal, oldVal) {
            // RESETA O CONTADOR TODA VEZ QUE DIGITA
            clearTimeout(this.timeOut);
            //AGUARDA 1 SEGUNDO PARA FAZER A REQUISIÇÃO
            this.timeOut = setTimeout(() => {
                this.getAgentTablesCount();
                this.clearAllChats();

                if (this.showTableQueue) {
                    this.showTableQueue = false;
                    this.showTableComponent('queue');
                } else if (this.showTableInProgress) {
                    this.showTableInProgress = false;
                    this.showTableComponent('inProgress');
                } else if (this.showTableOverdue) {
                    this.showTableOverdue = false;
                    this.showTableComponent('overdue');
                } else if (this.showTableResolved) {
                    this.showTableResolved = false;
                    this.showTableComponent('resolved');
                } else if (this.showTableCanceled) {
                    this.showTableCanceled = false;
                    this.showTableComponent('canceled');
                }

            }, 1000)
        }
    }
};
</script>

<style scoped>
.scroll {
    overflow-y: scroll !important;
    max-height: 20vh !important;
}

.ft-16 {
    font-size: 16px;
}

.ba-hd__title {
    color: #0080fc;
    font-family: Muli;
    font-weight: 800;
    font-size: 1.4rem;
    letter-spacing: 0px;
    color: #0080fc;
    width: 0px;
}

.chat-txt {
    max-height: 445px;
    min-height: 44px;
}

.js-autoresize {
    font: normal normal normal 16px/20px Muli;
    letter-spacing: 0px;
    color: #707070;
    max-height: 159px;
    min-height: 44px;
    width: 100%;
    border: none;
    resize: none;
    background: transparent;
}

.col-btn-back {
    max-width: 80px !important;
    min-width: 80px !important;
}

@media only screen and (min-width: 1279px) {
    .col-btn-back {
        display: none;
    }
}

@media only screen and (max-width: 1279px) {
    .col-btn-back {
        display: unset;
    }
}

.btn-back {
    height: 10px !important;
}

.sidebar-right .list-group-item {
    border-radius: 0px !important;
    background: transparent !important;
    height: 55px !important;
    font: normal normal 600 16px/21px Muli;
    letter-spacing: 0px;
    color: #656565;
    border: none;
    border-bottom: 1px solid #d7dee6;
}

.sidebar-right .list-group-item:last-child {
    border-bottom: none !important;
}

.sidebar-right .list-group-item:hover {
    background: #2273fe !important;
    color: white;
}

@media only screen and (min-width: 1279px) {
    .btn-back {
        display: none;
    }
}

.modal-content {
    background: #f3f7ff 0% 0% no-repeat padding-box;
    box-shadow: 0px 14px 32px #00000040;
    border-radius: 10px;
    border: none;
    padding-top: 40px;
    padding-left: 40px;
    padding-right: 40px;
}

.modal-title {
    font: normal normal bold 20px/26px Muli;
    letter-spacing: 0px;
    color: #434343;
}

.modal {
    background-color: #59607173;
}

.modal .close {
    background: #acb8d8;
    color: white;
    text-shadow: none;
    padding: 2px;
    margin-top: 1px;
    border-radius: 100%;
    font-size: 15px;
    height: 25px;
    width: 25px;
    margin-right: 2px;
}

.modal-body label {
    font: normal normal bold 14px/35px Muli;
    letter-spacing: 0px;
    color: #656565;
}

.modal-body select {
    background: #fafbfc 0% 0% no-repeat padding-box;
    border: 1px solid #dddddd;
    border-radius: 3px;
    height: 50px;
}

#footer {
    overflow-x: auto;
    white-space: nowrap;
    position: fixed;
    bottom: 0px;
    height: 80px;
    width: calc(100% - 100px);
}

@media only screen and (max-width: 576px) {
    #footer {
        width: calc(100% - 37px);
    }
}

.footer-active-chat {
    max-width: 240px !important;
    min-width: 240px !important;
    height: 50px !important;
    margin-left: 10px;
    display: inline-block !important;
    background: #ffffff 0% 0% no-repeat padding-box;
    border: 1px solid #dddddd;
    border-radius: 6px;
    opacity: 1;
    cursor: pointer;
}

.footer-active-chat:hover {
    background: #f7f8fc;
}

.footer-active-chat.answered {
    background: #ff4872;
}

.footer-active-chat.answered .fac-name {
    color: #f4f4f4;
}

.footer-active-chat.answered .btn-fac-close {
    background: #fa4b57;
}

.footer-active-chat.active {
    box-shadow: 0px 0px 9px #25242624;
}

.footer-active-chat.active .fac-name {
    font-weight: bold;
}

.footer-active-chat.active {
    max-width: 242px !important;
    min-width: 242px !important;
    height: 52px !important;
}

.fac-avatar {
    max-width: 26px;
    min-width: 26px;
    height: 100%;
}

.fac-name {
    height: 100%;
    font: normal normal 600 16px/21px Muli;
    letter-spacing: 0px;
    color: #656565;
    text-transform: capitalize;
    max-width: 176px;
}

.fac-close {
    max-width: 18px;
    min-width: 18px;
    height: 100%;
}

.btn-fac-close {
    border: none !important;
    border-radius: 100%;
    height: 18px !important;
    width: 18px !important;
    background: #d2dae5;
}

.btn-fac-close:hover {
    background: #fa4b57;
}

button:focus {
    outline: 0 !important;
}

#dropdown-action .bs-ico.build {
    position: relative;
    font-size: 16px;
    color: #707070;
    top: 3px;
    left: -2px;
}


.span-dropdown-action {
    color: #333333 !important;
    text-transform: capitalize !important;
    font-size: 14.2px !important;
    line-height: 20px !important;
    font-weight: bold !important;
    text-rendering: optimizeLegibility !important;
    font-family: Muli, Lato, Helvetica Neue, Helvetica, Arial, sans-serif !important;
}



.action_icon {
    color: #656565;
    font-size: 18px;
    max-width: 20px;
    min-width: 20px;
    position: relative;
    top: 4px;
    left: -5px;
}

.action-title {
    font-family: Muli;
    font-weight: 600;
    font-size: 0.9rem;
    letter-spacing: 0px;
    color: #656565;
    text-transform: capitalize;
}

.is_mobile .modal {
    padding-left: 0px !important;
}

.is_mobile .modal-dialog {
    max-width: 100vw !important;
    min-height: 100vh !important;
    max-height: 100vh !important;
    margin: 0px !important;
}

.is_mobile .modal-content {
    height: 100vh !important;
    padding-left: 10px !important;
    padding-right: 10px !important;
}

.is_mobile .btn-cancel-modal {
    padding: 0px;
    padding-right: 10px;
}

.ba-hd__left-sidebar .opt-new-ticket {
    border-radius: 0px;
    border: none;
    height: 37px;
    background: #F8F8F8 0% 0% no-repeat padding-box;
    box-shadow: 0px 1px 1px #1E120D1A;
    border-radius: 5px;
    padding: 10px 15px 0px 15px;
    display: flex;
    font: normal normal 800 0.8rem/16px Muli;
    letter-spacing: 0.42px;
    color: #818181;
    text-transform: uppercase;
    cursor: pointer;
}

.caret {
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
</style>
