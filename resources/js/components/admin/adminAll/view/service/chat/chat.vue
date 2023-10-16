<template>
	<div>
        <div class='ba-flex ba-gp-1'>
            <div class="ba-w-100">
                <div class='ba-card-breadcrumb ba-card'>
                    <nav class='ba-breadcrumb'>
                        <ol class='ba-breadcrumb-ol'>
                            <li class='ba-breadcrumb-item ba-main-item'>
                                <a href='#'>
                                    <span class='ba-icon'><icons-custom name="logo-head-icon" width="22"></icons-custom></span>
                                </a>
                            </li>
                            <li class='ba-breadcrumb-item'>
                                <a href='#' @click="closechat()">{{ $t('bs-chat') }} {{ itemselected.length == 0 ? '' :'#'+itemselected.number }}</a>
                            </li>
                        </ol>
                    </nav>
                </div>
                <br>
                <div class='ba-flex ba-gp-1 ba-h-100'>
                    <div v-if="!chatOpen">
                        <div class="left-out-wall ba-h-100">
                            <div class="ba-montserrat-700 ba-mg-l-1 caret fz-14 color-r">
                                All Chats (384) 
                                <!-- <icons-custom style="padding-left: 20%;" :active="false" name="down-icon" width="12"></icons-custom> -->
                            </div>
                            <div class='ba-sidebar box-shadow-remove' style="width: 182px;">
                                <ul class='ba-nav'>
                                    <li class='ba-nav-item '>
                                        <input type='checkbox' checked class='ba-nav-item-input-status ' id='ba-toggle-expandable-content-1'>
                                        <label class='ba-nav-item-expandable' style="width: 182px;" for='ba-toggle-expandable-content-1'>
                                            <div class='ba-nav-item-expandable-content grid-block'>
                                                <span class='ba-montserrat-500 fz-14'>Active (1)</span>
                                                <icons-custom style="padding-left: 39%;" :active="false" name="down-icon" width="12"></icons-custom>
                                            </div>
                                            <ul class='ba-nav-subitems'>
                                                <hr class='ba-hr-row'>
                                                <li class='ba-nav-subitem'>
                                                    <a href='#' @click="getChatsOpeneds" :class='"ba-nav-subitem-link "+activeAllchats[0]'>
                                                        <span class='ba-mg-l-1 ba-montserrat-500 fz-14'>{{$t("bs-in-queue")}} ({{countOnQueue}})</span>
                                                    </a>
                                                </li>
                                                <li class='ba-nav-subitem'>
                                                    <a href='#' @click="getChatsInProgress" :class='"ba-nav-subitem-link "+activeAllchats[1]'>
                                                        <span class='ba-mg-l-1 ba-montserrat-500 fz-14'>{{$t("bs-in-progress")}} ({{countInProgress}})</span>
                                                    </a>
                                                </li>
                                                <li class='ba-nav-subitem'>
                                                    <a href='#' @click="getChatsFinish" :class='"ba-nav-subitem-link "+activeAllchats[2]'>
                                                        <span class='ba-mg-l-1 ba-montserrat-500 fz-14'>{{$t("bs-finish")}} ({{countFinish}})</span>
                                                    </a>
                                                </li>
                                                <li class='ba-nav-subitem'>
                                                    <a href='#' @click="getChatsLost" :class='"ba-nav-subitem-link "+activeAllchats[3]'>
                                                        <span class='ba-mg-l-1 ba-montserrat-500 fz-14'>{{$t("bs-lost")}} ({{countcanceled}})</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div v-if="!chatOpen" class="ba-w-100" style="max-height: 744px;">
                        <div class='ba-card ba-h-100'>
                            <div class="ba-top">
                                <span class="ba-title" style="font-size:12px">Chats</span>
                                <div class="ba-box-items ba-flex ba-gp-1 flex-column flex-md-row">
                                
                                    <select @change="chatQuantPerPage($event)" class='ba-select ba-md' name='' id=''>
                                        <option value='10'>10 chats per page</option>
                                        <option value='20'>20 chats per page</option>
                                        <option value='30'>30 chats per page</option>
                                    </select>
                                    <button @click="filterConfigs" class='ba-btn ba-sm ba-light'>
                                        <icons-custom :active="false" width="15" name="filter-icon"></icons-custom>
                                        Chat Filter
                                    </button>
                                </div>
                            </div>
                            <div class="ba-w-100 ba-h-100" style="overflow: auto;">
                                <builderall-loader :loading='loadingTable'>
                                    <table-queue v-if="activeAllchats[0] != ''" :chats_in_queue="chats_in_queue" @open-chat="openchat" @open-info="openInfo"></table-queue>
                                    <table-in-progress v-if="activeAllchats[1] != ''" :chats_in_progress="chats_in_progress" @open-chat="openchat" @open-info="openInfo"></table-in-progress>
                                    <table-finish v-if="activeAllchats[2] != ''" :chats_finish="chats_finish" @open-chat="openchat" @open-info="openInfo"></table-finish>
                                    <table-lost v-if="activeAllchats[3] != ''" :chats_lost="chats_lost" @open-chat="openchat" @open-info="openInfo"></table-lost>
                                </builderall-loader>
                            </div>
                            <div class="ba-w-100" style="border: 1px solid red;">
                                <span style="width: 46px;
                                    height: 33px;
                                    top: 1279px;
                                    left: 143px;
                                    border-radius: 4px;
                                ">
                                    <div class='ba-flex ba-gp-1'>
                                        <div>
                                            <icons-custom :active="false" width="25" name="edit-icon"></icons-custom>
                                        </div>
                                        <div>
                                            <div class="tab-css ba-montserrat-700 fz-12 caret">
                                                CHAT #2408
                                            </div>
                                        </div>
                                        <div>
                                            <div class="tab-css ba-montserrat-700 fz-12 caret">
                                                CHAT #12364
                                            </div>
                                        </div>
                                        <div>
                                            <div class="tab-css ba-montserrat-700 fz-12 caret">
                                                CHAT #21408
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <icons-custom :active="false" width="15" name="ellipsis-icon"></icons-custom>    -->
                                </span>
                            </div>
                            <div class="ba-w-100">
                                    <nav class='ba-pagination ba-md'>
                                        <ul class='ba-list' style="justify-content: center;">
                                            <li class='ba-nav-item'>
                                                <a class='ba-back' href='#'>
                                                    <span class='ba-icon'>
                                                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-arrow-left' viewBox='0 0 16 16'>
                                                            <path fill-rule='evenodd' d='M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z'/>
                                                        </svg>
                                                    </span>
                                                </a>
                                            </li>
                                            <li class='ba-nav-item'>
                                                <a class='ba-link ba-is-active' href='#'>1</a>
                                            </li>
                                            <li class='ba-nav-item'>
                                                <a class='ba-link' href='#'>2</a>
                                            </li>
                                            ...
                                            <li class='ba-nav-item'>
                                                <a class='ba-link' href='#'>9</a>
                                            </li>
                                            <li class='ba-nav-item'>
                                                <a class='ba-link' href='#'>10</a>
                                            </li>
                                            <li class='ba-nav-item'>
                                                <a class='ba-next' href='#'>
                                                    <span class='ba-icon'>
                                                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-arrow-right' viewBox='0 0 16 16'>
                                                            <path fill-rule='evenodd' d='M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z'/>
                                                        </svg>
                                                    </span>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                        </div>
                    </div>
                    <div class='ba-card' v-if="chatOpen">
                        <div class="ba-montserrat-700 ba-mg-l-1 caret fz-14 color-r">
                            <div class='ba-flex ba-gp-1'>
                                <div>
                                    Chats
                                </div>
                                <div class='ba-box-search-input'>
                                    <input style="width:118px;" type='search' class='ba-input' placeholder='Search...' />
                                    <button class='ba-button'>
                                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-search' viewBox='0 0 16 16'>
                                            <path d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <br>
                            <div class='ba-card shaddow ba-mg-t-1'>
                                <div class='ba-flex ba-gp-1'>
                                    <div>
                                        <gravatar-new
                                        size="48px" 
                                        cEmail="marlos_gpi@live.com" 
                                        :id="19"
                                        name="online"
                                        :ba_acct_data="null"
                                        ></gravatar-new> 
                                    </div>
                                    <div class='ba-mg-l-1'>
                                        Shelly Turner<br>
                                        Hello! I would like t...
                                    </div>
                                </div>
                            </div>
                            <div class='ba-card borderc ba-mg-t-1'>
                                <div class='ba-flex ba-gp-1'>
                                    <div>
                                        <gravatar-new
                                        size="48px" 
                                        cEmail="marlos_gpi@live.com" 
                                        :id="19"
                                        name="online"
                                        :ba_acct_data="null"
                                        ></gravatar-new>
                                    </div>
                                    <div class='ba-mg-l-1'>
                                        aaaaaaaaaa<br>
                                        vvvvvvvvvvvvvv.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="chatOpen" class="ba-w-100">
                        <div class='ba-card ba-h-100'>
                            <div class='ba-flex ba-gp-1'>
                                <div class="ba-mg-r-1">
                                    <gravatar-new
                                    size="48px" 
                                    :cEmail="'jgfontana98@gmail.com'" 
                                    :id="$store.state.session_user.id"
                                    :name="'João'"
                                    :ba_acct_data="null"
                                    ></gravatar-new>
                                </div>
                                <div class='ba-mg-l-1'>
                                    <span class="ba-title">{{$store.state.session_user.name}} - {{ $store.state.session_user.email }}</span>
                                    <br>
                                    Active now - {{ $status.get($store.state.session_user.id) }} - {{ $store.state.session_user.id }}
                                </div>
                            </div>
                            <hr class='ba-hr-row' style="margin-top: -8px;">

                            <div class='ba-w-100 ba-h-100' style="border: 1px solid red;max-height: 615px;overflow-y: scroll;">
                                <!-- <span class="card-body" v-for="(item, index) in chat_history_before" :key="index+'axa'">
                                    <span v-if="item.type == 'TEXT'">
                                        <message-type-text 
                                        :comp_user_comp_depart_id_current="item.company_user_company_department_id"
                                        :message="$t(item)" 
                                        :formatTime="formatTime"></message-type-text>
                                    </span>
                                    <span v-if="item.type == 'EVENT'">
                                        <message-type-event
                                        :message="$t(item)" 
                                        :formatTime="formatTime"></message-type-event>
                                    </span>
                                    <span v-if="item.type == 'OPEN'">
                                        <message-type-open-agent
                                        :message="$t(item)" 
                                        :formatTime="formatTime"></message-type-open-agent>
                                    </span>
                                    <span v-if="item.type == 'IMAGE'">
                                        <message-type-image-agent
                                        :message="$t(item)" 
                                        :formatTime="formatTime"></message-type-image-agent>
                                    </span>
                                    <span v-if="item.type == 'FILE'">
                                        <message-type-file-agent
                                        :message="$t(item)" 
                                        :formatTime="formatTime"></message-type-file-agent>
                                    </span>
                                </span> -->


                                <!-- {
                                    'chat_id': 'b0s4NUl6TjVyYXpXdGQzTjlRVlZOQT09',
                                    'number': 92158,
                                    'company_id': 'UTJINmpiNG94U3dLYkhhaWJkWWNWUT09',
                                    'company_department_id': 'NkE2VEhXeHdvelRrbVNaZ1FzUmYwQT09',
                                    'comp_user_comp_depart_id_current': 'emt5bzNyUStuSU8rcENnTWNBKzkyZz09',
                                    'status': 'IN_PROGRESS',
                                    'description': '<p>13</p>',
                                    'type': 'chat',
                                    'user_agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36',
                                    'turn_into_ticket_at_closing': 0,
                                    'date': '21/08/2023',
                                    'time': '18:36:09',
                                    'created_at': '2023-08-21T18:36:09.000000Z',
                                    'department': 'Bingo',
                                    'dep_type': null,
                                    'client_id': 'aXlxamwyUlVXVm5QZkswN3l2RFRUZz09',
                                    'builderall_account_data': '{\'is_vip\':false,\'id\':\'1634790\',\'uuid\':\'1f25f051-7bd1-4fb3-af57-39e72aa798c0\'}',
                                    'name': 'Marlos',
                                    'email': 'marlos_gpi@live.com',
                                    'operator': 'adminMaster',
                                    'operator_id': 'NTNPWUEwVnNsbXd0c0o0ZHJ6cHRXdz09',
                                    'operator_email': 'adminMaster@live.com',
                                    'answered': 0,
                                    'category_chat_id': null,
                                    'action': 'add',
                                    'user_id': 19
                                } -->
                                <span v-for="(item, index) in chat_history" :key="index+'axa'">
                                    <span v-if="item.type == 'TEXT'">
                                        <message-type-text-new
                                            :comp_user_comp_depart_id_current="item.company_user_company_department_id"
                                            :message="$t(item)" 
                                            :formatTime="formatTime"></message-type-text-new>
                                    </span>
                                    <span v-if="item.type == 'EVENT'">
                                        <message-type-event-new
                                            :message="$t(item)" 
                                            :formatTime="formatTime"></message-type-event-new>
                                    </span>
                                    <span v-if="item.type == 'ROBOT'">      
                                        <message-type-robot-new
                                        :comp_user_comp_depart_id_current="item.company_user_company_department_id"
                                        :message="$t(item)"></message-type-robot-new>
                                    </span>
                                    <span v-if="item.type == 'OPEN'">
                                        <message-type-open-new
                                            :message="$t(item)" 
                                            :formatTime="formatTime"></message-type-open-new>
                                    </span>
                                    <span v-if="item.type == 'IMAGE'">
                                        I
                                    </span>
                                    <span v-if="item.type == 'FILE'">
                                        F
                                    </span>
                                </span>

                            </div>
                            <div class='ba-w-100' style="border: 1px solid red;">
                                <div class="ba-box-search-input ba-white">
                                    <input type='search' class='ba-input ba-w-100' style="height: 100px;" placeholder='Search...' />
                                    <button class='ba-button'>
                                        <icons-custom width="16" name="send-icon"></icons-custom>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="showInfoChat" class="right-out-wall">
                <card-info type="chat" :questionary="questionary" :itemselected="itemselected"></card-info>
            </div>
            <div v-show="showfilterDepartments" class="right-out-wall">
                <card-filter-depart type="chat"></card-filter-depart>
            </div>
        </div>
        <br>
        <!-- <div class="ba-w-100" style="border: 1px solid red;">
            TAB DOS CHATS
        </div> -->
   </div>
</template>

<script>
import iconsCustom from '../../../util/icons/iconsCustom.vue';
import CardInfo from '../../service/cards/card-info.vue';
import CardFilterDepart from '../../service/cards/card-filter-depart.vue';
import GravatarNew from "../../../util/gravatar/GravatarNew.vue";
import TableQueue from "../chat/tables/TableQueue.vue";
import TableInProgress from "../chat/tables/TableInProgress.vue";
import TableFinish from "../chat/tables/TableFinish.vue";
import TableLost from "../chat/tables/TableLost.vue";
import MessageTypeTextNew from '../../service/chat/messages/MessageTypeTextNew.vue';
import MessageTypeEventNew from '../../service/chat/messages/MesageTypeEventNew.vue';
import MessageTypeRobotNew from '../../service/chat/messages/MessageTypeRobotNew.vue';
import MessageTypeOpenNew from '../../service/chat/messages/MessageTypeOpenNew.vue';
// import Gravatar from 'vue-gravatar';
export default {
    components:{
        iconsCustom,
        CardInfo,
        CardFilterDepart,
        GravatarNew,
        TableQueue,
        TableInProgress,
        TableFinish,
        TableLost,
        MessageTypeTextNew,
        MessageTypeEventNew,
        MessageTypeRobotNew,
        MessageTypeOpenNew,
    },
	data(){
		return {
			showInfoChat: false,
            showfilterDepartments: false,
            chatOpen: false,
            activeAllchats: ['ba-is-active','','',''],
            chats_in_queue: [],
            chats_in_progress: [],
            chats_finish: [],
            chats_lost: [],
            departments: [],
            take: 10,
            skip: 0,
            chat_history: [],
            questionary: [],
            my_chats: 0,
            filter_my_chats: 0,
            countOnQueue: 0,
            countInProgress: 0,
            countFinish: 0,
            countcanceled: 0,
            loadingTable: true,
            itemselected: [],
		}
	},
	created() {
        this.$root.$refs.ChatNew = this;
        this.getDepartmentByCompany();
    },
	methods: {
        setMessageComponentRobot(message) {
            switch (message.content.type) {
                case 'text':
                    return this.componentRobot['TEXT'];
                    break;

                case 'to_jump':
                case 'link':
                case 'create_chat':
                case 'create_ticket':
                case 'transfer_department':
                case 'solved':
                case 'unsolved':
                case 'default_button':
                    return this.componentRobot['DEFAULT_BUTTON'];
                    break;
            }
        },
        openInfo(item){
            this.itemselected = item;
            axios.get("/ticket-chat-answer/agent/get-ticket-chat-answers", {
                params: {
                    id: item.chat_id,
                    reference: "chat_id"
                }
            }).then(response => {
                this.questionary = response.data.result;
                this.showInfoChat = true;
                this.showfilterDepartments = false;
            });
        },
        getAgentTablesCount() {
            const api = this.$store.state.is_admin == 1 ? '/full-chat-admin/get-agent-tables-count' : '/full-chat/get-agent-tables-count';
            // console.log(this.$store.state.filter_departments.map(item => ({ id: item })));
            axios.get(api, {
                params: {
                    departments: this.$store.state.filter_departments.map(item => ({ id: item })),
                    my_chats: this.filter_my_chats ? 1 : 0,
                    filter_not_category: this.filter_not_category ? 1 : 0,
                }
            })
            .then(({ data }) => {
                this.countOnQueue = data.queue == undefined ? 0 : data.queue;
                this.countInProgress = data.in_progress == undefined ? 0 : data.in_progress;
                let finished = 0;
                data.resolved >= 1
                    ? (finished = finished + data.resolved)
                    : (finished = finished);
                data.closed >= 1
                    ? (finished = finished + data.closed)
                    : (finished = finished);
                this.countFinish= finished;
                this.countcanceled = data.canceled == undefined ? 0 : data.canceled;
            });
        },
        getDepartmentByCompany(){
            const route = this.$store.state.is_admin == 1 ? '/get-departments-by-company' : '/company-user-company-department/get-department-by-agent';
            axios.get(route).then(({ data }) => {
                this.$store.state.departments = data;
                this.getChatsOpeneds();
                this.getAgentTablesCount();
            });
        },
        getChatsOpeneds(){
            this.loadingTable = true;
            this.activeAllchats = ['ba-is-active','','',''];
            const route = this.$store.state.is_admin == 1 ? '/full-chat-admin/get-chats-on-queue' : '/full-chat/get-chats-on-queue';
            this.skip = 0;
            this.chats_in_queue = [];
            // console.log(this.$store.state.filter_departments);
            axios.get(route, {
                params: {
                    take: this.take,
                    skip: this.skip,
                    departments: this.$store.state.filter_departments.map(item => ({ id: item })),
                }
            })
            .then(({ data }) => {
                if (data.chats.length) {
                    this.chats_in_queue.push(...data.chats);
                    this.skip = data.skip;
                }
                this.loadingTable = false;
            });
        },
        getChatsInProgress(){
            this.activeAllchats = ['','ba-is-active','',''];
            const route = this.$store.state.is_admin == 1 ? '/full-chat-admin/get-chats-in-progress' : '/full-chat/get-chats-in-progress';
            this.skip = 0;
            this.chats_in_progress = [];
            // console.log(this.$store.state.filter_departments);
            axios.get(route, {
                params: {
                    my_chats: this.filter_my_chats ? 1 : 0,
                    take: this.take,
                    skip: this.skip,
                    departments: this.$store.state.filter_departments.map(item => ({ id: item })),
                }
            })
            .then(({ data }) => {
                if (data.chats.length) {
                    this.chats_in_progress.push(...data.chats);
                    this.skip = data.skip;
                }
            });
        },
        getChatsFinish(){
            this.activeAllchats = ['','','ba-is-active',''];
            const route = this.$store.state.is_admin == 1 ? '/full-chat-admin/get-chats-finished' : '/full-chat/get-chats-finished';
            this.skip = 0;
            this.chats_finish = [];
            // console.log(this.$store.state.filter_departments);
            axios.get(route, {
                params: {
                    take: this.take,
                    skip: this.skip,
                    my_chats: this.filter_my_chats ? 1 : 0,
                    per_page: this.take,
                    departments: this.$store.state.filter_departments.map(item => ({ id: item })),
                }
            })
            .then(({ data }) => {
                // console.log(data.data)
                if (data.data.length) {
                    this.chats_finish.push(...data.data);
                    this.skip = data.skip;
                }
            });
        },
        getChatsLost(){
            this.activeAllchats = ['','','','ba-is-active'];
            const route = this.$store.state.is_admin == 1 ? '/full-chat-admin/get-chats-canceled' : '/full-chat/get-chats-canceled';
            this.skip = 0;
            this.chats_lost = [];
            // console.log(this.$store.state.filter_departments);
            axios.get(route, {
                params: {
                    take: this.take,
                    skip: this.skip,
                    my_chats: this.filter_my_chats ? 1 : 0,
                    departments: this.$store.state.filter_departments.map(item => ({ id: item })),
                }
            })
            .then(({ data }) => {
                if (data.chats.length) {
                    this.chats_lost.push(...data.chats);
                    this.skip = data.skip;
                }
            });
        },
        filterConfigs(){
            this.showfilterDepartments = !this.showfilterDepartments;
            this.closeInfos();
        },
        chatQuantPerPage(event){
            const selectedValue = event.target.value;
            this.take = selectedValue;
            this.getChatsOpeneds();
        },
        openchat(chat){
            this.itemselected = chat;
            this.chatOpen = !this.chatOpen;
            this.showInfoChat = true;
            // console.log(chat)
            this.getChatHistory(chat.chat_id).then((data) => {
                this.chat_history = data;
            });
        },
        closechat(){
            this.chatOpen = false;
        },
        getChatHistory(id) {
            return new Promise((resolve, reject) => {
                this.getQuestionary(id).then(() => {
                    axios
                    .get("/chat-history/agent/get-chat-history", {
                        params: {
                            id: id
                        }
                    })
                    .then(({data}) => {
                        resolve(data);
                    });
                })
            });
        },
        getQuestionary(id) {
            return new Promise((resolve, reject) => {
                axios.get("/ticket-chat-answer/agent/get-ticket-chat-answers", {
                    params: {
                        id: id,
                        reference: "chat_id"
                    }
                })
                .then(response => {
                    if (response.data.status) {
                        this.translateQuestionary(response.data.result, id).then((questionary) => {
                            this.questionary = questionary;
                            resolve();
                        })
                    }
                });
                resolve();
            })
        },
        translateQuestionary(questionary, chat_id) {
            return new Promise((resolve, reject) => {

                if (this.$root.$refs.ModalTranslate.status_visitors_messages) {

                    if (localStorage.getItem(`${chat_id}_translated_questionary`) === null) {

                        var itemsProcessed = 0;

                        questionary.forEach(element => {
                            this.$google.translate(element.answer, this.$root.$refs.ModalTranslate.languageMyMessages).then((result) => {
                                itemsProcessed++;
                                element.answer = result.data.translations[0].translatedText;

                                if(itemsProcessed === questionary.length) {
                                    var translated_questions = [{
                                        language: this.$root.$refs.ModalTranslate.languageMyMessages,
                                        content: questionary
                                    }];

                                    localStorage.setItem(`${chat_id}_translated_questionary`, JSON.stringify(translated_questions));
                                    resolve(questionary);
                                }
                            });
                        });

                    } else {
                        var localstorage = JSON.parse(localStorage.getItem(`${chat_id}_translated_questionary`));
                        var itemsProcessed = 0;
                        var found = false;
                        var content = [];

                        localstorage.forEach(element => {
                            itemsProcessed++;

                            if (element.language == this.$root.$refs.ModalTranslate.languageMyMessages) {
                                found = true;
                                content = element.content;
                            }

                            if(itemsProcessed === localstorage.length) {
                                if (found) {
                                    resolve(content);
                                } else {
                                    var QuestionaryItemsProcessed = 0;
                                    questionary.forEach(element => {
                                        this.$google.translate(element.answer, this.$root.$refs.ModalTranslate.languageMyMessages).then((result) => {
                                            QuestionaryItemsProcessed++;
                                            element.answer = result.data.translations[0].translatedText;

                                            if(QuestionaryItemsProcessed === questionary.length) {

                                                localstorage.push({
                                                    language: this.$root.$refs.ModalTranslate.languageMyMessages,
                                                    content: questionary
                                                })

                                                localStorage.setItem(`${chat_id}_translated_questionary`, JSON.stringify(localstorage));

                                                resolve(questionary);
                                            }
                                        });
                                    });
                                }
                            }
                        });
                    }
                } else {
                    resolve(questionary)
                }
            })
        },
        chatScrollTop() {
            var chat = document.getElementById("chat-main")
            if (chat) {
                chat.scrollTop = chat.scrollHeight - chat.clientHeight;
            }
        },
        closeInfos(){
            this.showInfoChat = false;
        },
        formatTime(h) {
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
            let dateUTC = new Date(Date.UTC(year, month, day, hour, minute, second));
            //pega o fuso do cliente. Ex: "America/Sao_Paulo"
            let client_tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
            let converted_time = dateUTC.toLocaleString("pt-BR", {
                timeZone: client_tz,
            });
            return moment(converted_time, "YYYY-MM-DD HH:mm:ss").format(
                "HH:mm YYYY-MM-DD"
            );
        },
	},
};
</script>

<style scoped>
    /* .m-r-25{
        margin-right: -25px !important;
    } */
    .borderc{
        border: 1px solid #C4D1E0
    }
    .shaddow{
        box-shadow: 1px 1px 4px 0px #00000029;
    }
    .color-r-t{
        color:#4D5D71CC
    }
    .color-r{
        color:#4D5D71
    }
    .grid-block{
        display: block !important;
    }
    .fz-12{
        font-size: 12px !important;
    }

    .fz-14{
        font-size: 14px !important;
    }

    .center-t{
        text-align: center;
    }
</style>