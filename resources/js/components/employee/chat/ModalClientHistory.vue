<template>
    <div
        class="modal fade"
        id="modalClientHistory"
        tabindex="-1"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
        data-backdrop="static"
        data-keyboard="false"
    >
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header border-0" v-if="!isPreview">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{ dataInfo ? $t("bs-category")+': ' : $t("bs-customer-history") }}:
                        <span v-if="dataInfo">{{ category_name}}</span>
                        <span v-else>{{
                            chatLocale.client !== undefined
                                ? $t(chatLocale.client.name)
                                : $t(chatLocale.name_created)
                        }}</span>
                    </h5>
                </div>
                <div class="modal-body" :class="{'pt-0': !isPreview, 'pt-3': isPreview}">
                    <template v-if="!isPreview">
                        <ul
                            v-if="!showChat && !showTicket"
                            class="nav nav-pills mb-2"
                        >
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{ active: showTableChats }"
                                    @click="setTable('chats')"
                                    href="#"
                                    >{{ $t("bs-chats") }}</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{ active: showTableTickets }"
                                    @click="setTable('tickets')"
                                    href="#"
                                    >{{ $t("bs-tickets") }}</a
                                >
                            </li>
                        </ul>
                        <ul v-else class="nav nav-pills mb-2">
                            <li class="nav-item">
                                <a class="nav-link" @click="back()" href="#">
                                    <i
                                        class="material-icons"
                                        style="font-size: 20px !important"
                                        >keyboard_arrow_left</i
                                    >
                                    {{ $t("bs-back") }}
                                </a>
                            </li>
                        </ul>
                    </template>
                    <!-- TABELA DO CHAT -->
                    <div
                        v-if="showTableChats && !showChat && !showTicket"
                        class="table-responsive"
                    >
                        <table
                            class="table table-hover table-bordered bg-white"
                        >
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ $t("bs-department") }}</th>
                                    <th>{{ $t("bs-operator") }}</th>
                                    <th>{{ $t("bs-status") }}</th>
                                    <th>{{ $t("bs-start") }}</th>
                                    <th>{{ $t("bs-end") }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    class="cursor-pointer"
                                    v-for="(chat, index) in clientChatHistory"
                                    :key="index"
                                    @click="openChat(chat, 'chat_id')"
                                >
                                    <td class="filterable-cell">
                                        #{{ chat.chat_number }}
                                    </td>
                                    <td class="filterable-cell">
                                        {{ $t(chat.department) }}
                                    </td>
                                    <td
                                        v-if="
                                            chat.comp_user_comp_depart_id_current !==
                                                null
                                        "
                                        class="filterable-cell"
                                    >
                                        {{ chat.name }}
                                    </td>
                                    <td v-else class="filterable-cell">---</td>
                                    <td
                                        class="filterable-cell"
                                        :class="{
                                            'text-primary':
                                                chat.status == 'RESOLVED',
                                            'text-danger':
                                                chat.status == 'CANCELED',
                                            'text-success':
                                                chat.status == 'IN_PROGRESS'
                                        }"
                                    >
                                        {{ getChatStatus(chat) }}
                                    </td>
                                    <td class="filterable-cell">
                                        {{ format_L_LT(chat.chat_created_at) }}
                                    </td>
                                    <td
                                        v-if="
                                            chat.status !== 'IN_PROGRESS' &&
                                                chat.status !== 'OPENED'
                                        "
                                        class="filterable-cell"
                                    >
                                        {{ format_L_LT(chat.chat_updated_at) }}
                                    </td>
                                    <td v-else class="filterable-cell">---</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- FIM TABELA DO CHAT -->

                    <!-- TABELA DO TICKET -->
                    <div
                        v-if="showTableTickets && !showChat && !showTicket"
                        class="table-responsive"
                    >
                        <table
                            class="table table-hover table-bordered bg-white"
                        >
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ $t("bs-department") }}</th>
                                    <th>{{ $t("bs-operator") }}</th>
                                    <th>{{ $t("bs-status") }}</th>
                                    <th>{{ $t("bs-start") }}</th>
                                    <th>{{ $t("bs-end") }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    class="cursor-pointer"
                                    v-for="(chat, index) in clientTicketHistory"
                                    :key="index"
                                    @click="openTicket(chat, 'ticket_id')"
                                >
                                    <td class="filterable-cell">
                                        #{{ chat.ticket_number }}
                                    </td>
                                    <td class="filterable-cell">
                                        {{ $t(chat.department) }}
                                    </td>
                                    <td
                                        v-if="
                                            chat.comp_user_comp_depart_id_current !==
                                                null
                                        "
                                        class="filterable-cell"
                                    >
                                        {{ chat.name }}
                                    </td>
                                    <td v-else class="filterable-cell">---</td>
                                    <td
                                        class="filterable-cell"
                                        :class="{
                                            'text-primary':
                                                chat.ticket_status ==
                                                'RESOLVED',
                                            'text-danger':
                                                chat.ticket_status ==
                                                'CANCELED',
                                            'text-success':
                                                chat.ticket_status ==
                                                'IN_PROGRESS'
                                        }"
                                    >
                                        {{ getTicketStatus(chat) }}
                                    </td>
                                    <td class="filterable-cell">
                                        {{
                                            format_L_LT(chat.ticket_created_at)
                                        }}
                                    </td>
                                    <td
                                        v-if="
                                            chat.ticket_status !==
                                                'IN_PROGRESS' &&
                                                chat.ticket_status !== 'OPENED'
                                        "
                                        class="filterable-cell"
                                    >
                                        {{
                                            format_L_LT(chat.ticket_updated_at)
                                        }}
                                    </td>
                                    <td v-else class="filterable-cell">---</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- FIM TABELA DO TICKET -->
                </div>

                <!-- LISTAGEM DO CHAT -->
                <div v-if="showChat" class="pr-3 pl-3 mt-n3">
                    <div class="card">
                        <div class="card-header bg-white">
                            <b>#{{ number }}</b>
                        </div>
                        <div class="card-body p-0">
                            <message-type-questionary
                                v-if="!loading_questionary"
                                :chat="dataInfo ? chatLocale : chat"
                                :questionary="questionary"
                                :formatTime="UTCtoClientTZ2"
                            />
                            <component
                                v-for="(message, index) in chat_history"
                                :key="index"
                                :is="setMessageComponent(message.type)"
                                v-bind="setMessageProps(message, index)"
                            />
                        </div>
                        <div class="card-footer border-0 bg-white"></div>
                    </div>
                </div>
                <!-- FIM LISTAGEM DO CHAT -->

                <!-- LISTAGEM DO TICKET -->
                <div v-if="showTicket" class="pr-3 pl-3 mt-n3">
                    <div class="card">
                        <div class="card-header bg-white">
                            <b>#{{ number }}</b>
                        </div>
                        <div class="card-body" v-chat-scroll>
                            <component
                                v-for="(message, index) in ticket_history"
                                :key="index"
                                :is="setTicketMessageComponent(message)"
                                v-bind="setTicketMessageProps(message, index)"
                            />
                        </div>
                        <div class="card-footer border-0 bg-white"></div>
                    </div>
                </div>
                <!-- FIM LISTAGEM DO TICKET -->

                <div class="modal-footer border-0">
                    <button
                        @click="clearModal"
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal"
                    >
                        {{ $t("bs-close") }}
                    </button>
                    <!-- <button type="sbutton" class="btn btn-primary">Save changes</button> -->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
function initialState() {
    return {
        showTableChats: true,
        showTableTickets: false,
        showChat: false,
        showTicket: false,
        chat_history: [],
        ticket_history: [],
        questionary: [],
        number: "",
        messageComponent: {
            EVENT: "message-type-event",
            TEXT: "message-type-text",
            OPEN: "message-type-open-agent",
            CLOSE: "message-type-close-agent",
            FILE: "message-type-file-agent",
            IMAGE: "message-type-image-agent"
        },
        ticketMessageComponent: {
            FILE: "message-type-file", // "message-type-text-file"
            IMAGE: "message-type-image",
            EVENT: "message-type-event-client",
            TEXT_SENT: "message-type-text-sent",
            TEXT_RECEIVED: "message-type-text-received",
            TEXT_SENT_RECEIVED_NEW: "message-type-tk-message"
        },
        id: "",
        ref: "",
        ticket_info: {
            id_department: "",
            id_ticket: "",
            company_user_id: "",
            settings: {
                evaluation: {
                    text_atten_active: "0",
                    text_serv_active: "0",
                    text_comment_active: "0"
                }
            },
            priority: "",
            description: "",
            msgOpen: ""
        },
        chat2: {
            id: "",
            type: "TEXT",
            content: "",
            clear: ""
        },
        loading_questionary: true,
    };
}
export default {
    computed: {
        isPreview() {
            let module = new URL(location.href).searchParams.get("module");
            if (module == "chat") {
                return this.$root.$refs.FullChat2.isPreview;
            } else if (module == "ticket") {
                return this.$root.$refs.FullTicket2.isPreview;
            } else if (module == "category") {
                return this.$root.$refs.categoryGraphic.isPreview;
            }else{
                return this.$root.$refs.UserClientBody.isPreview;
            }
        },
        chatPreview() {
            let module = new URL(location.href).searchParams.get("module");
            if (module == "chat") {
                return this.$root.$refs.FullChat2.chatPreview;
            } else if (module == "ticket") {
                return this.$root.$refs.FullTicket2.chatPreview;
            } else if (module == "category") {
                return this.$root.$refs.categoryGraphic.chatPreview;
            }else{
                return this.$root.$refs.UserClientBody.chatPreview;
            }
        },
        clientChatHistory() {
            let module = new URL(location.href).searchParams.get("module");
            if (module == "chat") {
                return this.$root.$refs.FullChat2.clientChatHistory;
            } else if (module == "ticket") {
                return this.$root.$refs.FullTicket2.clientChatHistory;
            } else if (module == "category") {
                return this.$root.$refs.categoryGraphic.clientChatHistory;
            }else{
                return this.$root.$refs.UserClientBody.clientChatHistory;
            }
        },
        clientTicketHistory() {
            let module = new URL(location.href).searchParams.get("module");
            if (module == "ticket") {
                return this.$root.$refs.FullTicket2.clientTicketHistory;
            } else if (module == "chat") {
                return this.$root.$refs.FullChat2.clientTicketHistory;
            } else if (module == "category") {
                return this.$root.$refs.categoryGraphic.clientTicketHistory;
            }else{
                return this.$root.$refs.UserClientBody.clientTicketHistory;
            }
        }
    },
    watch: {
        clientChatHistory(newValue, oldValue) {
            if (this.isPreview && Array.isArray(newValue)) {
                let i = newValue.findIndex(item => item.chat_id == this.chat.chat_id);
                if (i !== -1) {
                    this.openChat(newValue[i], "chat_id");
                }
            }
        },
        clientTicketHistory(newValue, oldValue) {
            if (this.isPreview && Array.isArray(newValue)) {
                let i = newValue.findIndex(item => item.ticket_id == this.chat.id);
                if (i !== -1) {
                    this.openTicket(newValue[i], "ticket_id");
                }
            }
        },
        questionary(){
            this.loading_questionary= true
            setTimeout(() => {
                this.loading_questionary= false
            }, 1000);
        },
        showChat(value) {
            if (value) {
                // this.getChatHistory();
                this.infoDepartmentAgent();
            }
        }
    },
    data() {
        return {
            ...initialState(),
            chatLocale: {},
            userLocale: {},
        };
    },
    props: {
        chat: Object,
        user: Object,
        dataInfo: Boolean,
        category_name: String,
    },
    created(){
        this.$root.$refs.ModalClientHistory= this;
        var vm = this;
        vm.chatLocale = {
            client: {
                browser: "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36",
                builderall_account_data: "{\"is_vip\":false,\"id\":\"1634790\",\"uuid\":\"1f25f051-7bd1-4fb3-af57-39e72aa798c0\"}",
                email: "departamento@departamento.departamento",
                id: "aXlxamwyUlVXVm5QZkswN3l2RFRUZz09",
                ip: "",
                location: "",
                name: "Cliente",
                so: "",
            },
            builderall_account_data: "{\"is_vip\":false,\"id\":\"1634790\",\"uuid\":\"1f25f051-7bd1-4fb3-af57-39e72aa798c0\"}",
            chat_id: "Tis5Y3FMbUpJWXd3WGk3UXdXaFhidz09",
            client_id: "aXlxamwyUlVXVm5QZkswN3l2RFRUZz09",
            comp_user_comp_depart_id_current: "emt5bzNyUStuSU8rcENnTWNBKzkyZz09",
            companyDepartmentId: "NkE2VEhXeHdvelRrbVNaZ1FzUmYwQT09",
            content: "",
            created_at: "2023-05-15T17:07:31.000000Z",
            date: "15/05/2023",
            dep_type: "",
            department: "Departamento",
            email: "departamento@departamento.departamento",
            id: "",
            is_vip: false,
            name: "Departamento",
            number: vm.number,
            show: true,
            sideContent: null,
            status: "RESOLVED",
            time: "17:07:31",
            turn_into_ticket_at_closing: 0,
            type: "DEFAULT",
        };

        vm.userLocale = {
                builderall_account_data: null,
                builderall_status: "ACTIVE",
                can_create_company: 1,
                config: "{\"fontSize\":\"16px\",\"signature\":\"\",\"notification\":{\"email\":1,\"system\":1,\"telegram\":0},\"chat_id_favorite\":[91349]}",
                cookies_accepted: 1,
                created_at: "2021-01-03T13:05:37.000000Z",
                created_by: null,
                dark_mode: 0,
                deleted_at: null,
                deleted_by: null,
                email: "agent@teste.teste",
                email_verified_at: null,
                hash_code: "YjM3RnBDSVJkYysvVFUvdjRIOUVNdz09",
                id: 19,
                is_anonymous: 0,
                language: 'pt_BR',
                name: "agent",
                permanent_delete: 0,
                phone: null,
                subsidiary_id: 2,
                terms_user: 1,
                updated_at: "2023-06-13T14:55:07.000000Z",
                updated_by: 19,
                user_uuid: "123123",
            };
    },
    methods: {
        infoDepartmentAgent(){
            var vm = this;
            axios.get("get-info-category-selected/" + vm.chat.chat_id).then(response => {
                console.log(response.data.infoUser.length);
                if(response.data.infoUser.length > 0){
                    vm.chatLocale.client.builderall_account_data = response.data.infoUser[0].builderall_account_data;
                    vm.chatLocale.client.name = response.data.infoUser[0].name;
                    vm.chatLocale.client.email = response.data.infoUser[0].email;
                    vm.chatLocale.client.id = response.data.infoUser[0].user_auth_id;

                    vm.chatLocale.builderall_account_data = response.data.infoUser[0].builderall_account_data;
                    vm.chatLocale.client_id = response.data.infoUser[0].user_auth_id;
                    vm.chatLocale.comp_user_comp_depart_id_current = response.data.infoUser[0].comp_user_comp_depart_id_current;
                    vm.chatLocale.companyDepartmentId = response.data.infoUser[0].company_department_id;
                    vm.chatLocale.chat_id = response.data.infoUser[0].chat_id;
                    vm.chatLocale.is_vip = response.data.infoUser[0].is_vip;
                    vm.chatLocale.status = response.data.infoUser[0].status;
                    vm.chatLocale.created_at = response.data.infoUser[0].created_at;
                    vm.chatLocale.name = response.data.infoUser[0].name;
                    vm.chatLocale.email = response.data.infoUser[0].email;
                    vm.chatLocale.id = response.data.infoUser[0].d_user_auth_id;
                    vm.chatLocale.department = response.data.infoUser[0].department_name;
                }

                if(response.data.infoAgent.length > 0){
                    vm.userLocale.builderall_account_data = response.data.infoAgent[0].builderall_account_data;
                    vm.userLocale.builderall_status = response.data.infoAgent[0].builderall_status;
                    vm.userLocale.can_create_company = response.data.infoAgent[0].can_create_company;
                    vm.userLocale.config = response.data.infoAgent[0].config;
                    vm.userLocale.cookies_accepted = response.data.infoAgent[0].cookies_accepted;
                    vm.userLocale.created_at = response.data.infoAgent[0].created_at;
                    vm.userLocale.deleted_at = response.data.infoAgent[0].deleted_at;
                    vm.userLocale.deleted_by = response.data.infoAgent[0].deleted_by;
                    vm.userLocale.email = response.data.infoAgent[0].email;
                    vm.userLocale.hash_code = response.data.infoAgent[0].hash_code;
                    vm.userLocale.id = response.data.infoAgent[0].id;
                    vm.userLocale.is_anonymous = response.data.infoAgent[0].is_anonymous;
                    vm.userLocale.language = response.data.infoAgent[0].language;
                    vm.userLocale.name = response.data.infoAgent[0].name;
                    vm.userLocale.permanent_delete = response.data.infoAgent[0].permanent_delete;
                    vm.userLocale.subsidiary_id = response.data.infoAgent[0].subsidiary_id;
                    vm.userLocale.terms_user = response.data.infoAgent[0].terms_user;
                    vm.userLocale.updated_at = response.data.infoAgent[0].updated_at;
                    vm.userLocale.updated_by = response.data.infoAgent[0].updated_by;
                    vm.userLocale.user_uuid = response.data.infoAgent[0].user_uuid;
                }
                // if(response.data.infoAgent.length > 0 && response.data.infoUser.length > 0){
                    this.getChatHistory();
                // }
            });
        },
        openTicket(row, ref) {
            this.ref = ref;
            this.chat2 = {
                id: "",
                type: "TEXT",
                content: "",
                clear: ""
            };
            this.ticket_history = [];
            this.showTicket = true;
            this.ticket_info.id_department = row.department_id;
            this.ticket_info.id_ticket = row.ticket_id;
            this.ticket_info.priority = row.priority;
            this.ticket_info.description = row.description;
            this.ticket_info.department_name = row.department;
            this.getTicketHistory(row.ticket_id, row.chat_id);
            this.setInfo(row, "ticket_id");
        },
        getTicketHistory(id_ticket, id_chat) {
            axios.get("client-chat-ticket/" + id_ticket).then(response => {
                this.chat2.id = id_chat;
                let _this = this;
                this.ticket_history = response.data.result.map(function(obj) {
                    obj.department = _this.ticket_info.department_name;
                    return obj;
                });

                this.ticket_history.unshift({
                    type: "TEXT",
                    content: response.data.ticket.description,
                    name: response.data.client_name,
                    client_id: response.data.client_id,
                    client_email: response.data.client_email,
                    department: "",
                    created_at: response.data.ticket.created_at
                });
                for (var i = response.data.quests.length - 1; i >= 0; i--) {
                    this.ticket_history.unshift({
                        type: "TEXT",
                        content: response.data.quests[i].answer,
                        name: response.data.client_name,
                        client_id: response.data.client_id,
                        client_email: response.data.client_email,
                        department: "",
                        created_at: response.data.quests[i].created_at
                    });
                    this.ticket_history.unshift({
                        type: "TEXT",
                        content: response.data.quests[i].question,
                        name: this.ticket_info.department_name,
                        department: this.ticket_info.department_name,
                        company_user_company_department_id: -1,
                        created_at: response.data.quests[i].created_at
                    });
                }
            });
        },
        setTicketMessageComponent(message) {
            if (["TEXT", "IMAGE", "FILE"].includes(message.type)) {
                return this.ticketMessageComponent["TEXT_SENT_RECEIVED_NEW"];
            } else {
                return this.ticketMessageComponent[message.type];
            }
        },
        setTicketMessageProps(message, index) {
            return {
                message: this.ticket_history[index],
                language: this.dataInfo ? this.userLocale.language  : this.user.language,
                user: this.dataInfo ? this.userLocale  : this.user
            };
        },

        openChat(chat, ref) {
            this.id = chat.chat_id;
            this.ref = ref;
            this.setInfo(chat, ref);
            this.showChat = true;

            setTimeout(() => {
                if (this.$refs.infinite) {
                    this.$refs.infinite.stateChanger.reset();
                }
            }, 200);
            this.joinChatChannel();
        },
        joinChatChannel() {
            Echo.join(`chat.${this.id}`).listen("MessageSent", event => {
                if(event.message.chat_id == this.id) {
                    this.chat_history.push(event.message);
                }
            });
        },
        getChatHistory() {
            axios
                .get("ticket-chat-answer/agent/get-ticket-chat-answers", {
                    params: {
                        id: this.id,
                        reference: "chat_id"
                    }
                })
                .then(response => {
                    if (response.data.status) {
                        this.questionary = response.data.result;
                    }
                }),

                this.chat_history = [];

                const api = `chat-history/agent/get-chat-history`;
                axios
                    .get(api, {
                        params: {
                            id: this.id
                        }
                    })
                    .then(({ data }) => {
                        data.forEach(message => {
                            if (message.content_translated !== null) {
                                var content_translated = JSON.parse(message.content_translated);
                                content_translated = content_translated[0].content;

                                var content = message.content;

                                message.content = content_translated;
                                message.content_translated = content;

                                this.chat_history.push(message);
                            } else {
                                this.chat_history.push(message);
                            }
                        });
                    });
        },
        setInfo(chat, ref) {
            if (ref === "chat_id") {
                this.number = chat.chat_number;
            } else if (ref === "ticket_id") {
                this.number = chat.ticket_number;
            }
        },
        setTable(table) {
            if (table === "chats") {
                this.showTableTickets = false;
                this.showTableChats = true;
            } else if (table === "tickets") {
                this.showTableChats = false;
                this.showTableTickets = true;
            }
        },
        clearModal() {
            let module = new URL(location.href).searchParams.get("module");
            if (module == "chat") {
                Echo.leave(`chat.${this.id}`);
                this.$root.$refs.FullChat2.isPreview = false;
                this.$root.$refs.FullChat2.chatPreview = {};
                Object.assign(this.$data, initialState());
            } else if (module == "ticket") {
                this.$root.$refs.FullTicket2.isPreview = false;
                this.$root.$refs.FullTicket2.chatPreview = {};
                Object.assign(this.$data, initialState());
                this.$root.$refs.FullTicket2.openingChat = false;
            } else if (module == "category") {
                this.$root.$refs.categoryGraphic.isPreview = false;
                this.$root.$refs.categoryGraphic.chatPreview = {};
                Object.assign(this.$data, initialState());
                this.$root.$refs.categoryGraphic.openingChat = false;
            }else{
                this.$root.$refs.UserClientBody.isPreview = false;
                this.$root.$refs.UserClientBody.chatPreview = {};
                Object.assign(this.$data, initialState());
                this.$root.$refs.UserClientBody.openingChat = false;
            }
        },
        back() {
            if (this.showTableChats) {
                this.clearModal();
            } else if (this.showTableTickets) {
                this.clearModal();
                this.showTableChats = false;
                this.showTableTickets = true;
            }
        },
        setMessageComponent(type) {
            return this.messageComponent[type];
        },
        setMessageProps(message, index) {
            return {
                message: this.chat_history[index],
                formatTime: this.UTCtoClientTZ2
            };
        },
        toUTC(h) {
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
                timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone
            });

            return converted_time;
        },
        UTCtoClientTZ2(h) {
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
                timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone
            });

            var mt = require("moment-timezone");
            return mt(converted_time, "DD/MM/YYYY HH:mm:ss")
                .tz(Intl.DateTimeFormat().resolvedOptions().timeZone)
                .locale(this.dataInfo ? this.userLocale.language  : this.user.language)
                .format("LT");
        },
        format_L_LT(h) {
            let converted_time = this.toUTC(h);
            var mt = require("moment-timezone");
            return (
                mt(converted_time, "DD/MM/YYYY HH:mm:ss")
                    .tz(Intl.DateTimeFormat().resolvedOptions().timeZone)
                    .locale(this.dataInfo ? this.userLocale.language  : this.user.language)
                    .format("L") +
                " - " +
                mt(converted_time, "DD/MM/YYYY HH:mm:ss")
                    .tz(Intl.DateTimeFormat().resolvedOptions().timeZone)
                    .locale(this.dataInfo ? this.userLocale.language  : this.user.language)
                    .format("LT")
            );
        },
        getChatStatus(chat) {
            let status = "";
            switch (chat.status) {
                case "OPENED":
                    status = this.$t("bs-in-queue");
                    break;
                case "IN_PROGRESS":
                    status = this.$t("bs-in-progress");
                    break;
                case "CLOSED":
                    status = this.$t("bs-closed");
                    break;
                case "RESOLVED":
                    status = this.$t("bs-resolved");
                    break;
                case "CANCELED":
                    status = this.$t("bs-canceled");
                    break;
                case "MERGED":
                    status = this.$t("bs-merged");
                    break;
            }
            return status;
        },
        getTicketStatus(chat) {
            let status = "";
            switch (chat.ticket_status) {
                case "OPENED":
                    status = this.$t("bs-in-queue");
                    break;
                case "IN_PROGRESS":
                    status = this.$t("bs-in-progress");
                    break;
                case "CLOSED":
                    status = this.$t("bs-closed");
                    break;
                case "RESOLVED":
                    status = this.$t("bs-resolved");
                    break;
                case "CANCELED":
                    status = this.$t("bs-canceled");
                    break;
                case "MERGED":
                    status = this.$t("bs-merged");
                    break;
            }
            return status;
        }
    }
};
</script>

<style scoped>
.modal-dialog {
    height: calc(100% - 100px) !important;
}

.modal-content {
    height: auto;
    background-color: #e9edf2 !important;
    z-index: 9999999 !important;
}

.modal-title span {
    color: #0080fc !important;
}

.modal-title {
    color: #96989a !important;
    font-weight: bold;
}

.nav-pills .nav-link {
    border-radius: 0px !important;
    border: none !important;
    border-bottom: 2px solid #bdbdbd !important;
    color: #96989a !important;
    font-family: Muli, Lato, Helvetica Neue, Helvetica, Arial, sans-serif;
    font-size: 0.9rem;
    line-height: 1.4;
    text-transform: uppercase;
    font-weight: bold;
    margin-right: 5px;
}

.nav-pills .nav-link:hover {
    background-color: transparent !important;
    color: #0080fc !important;
    border-bottom: 3px solid #0080fc !important;
}

.nav-pills .nav-link.active {
    background-color: transparent !important;
    color: #0080fc !important;
    border-bottom: 3px solid #0080fc !important;
}

table {
    border-color: #96989a;
    text-align: center;
}

tbody {
    font: normal normal 600 16px/19px Lato;
    letter-spacing: 0px;
    color: #6e6e6e;
}

.table-responsive {
    max-height: calc(100vh - 310px);
    overflow-y: auto;
}

.card-body {
    min-height: calc(100vh - 352px);
    max-height: calc(100vh - 352px);
    overflow-y: auto;
}

.card {
    border-radius: 0px;
}

/* SCROLL */

::-webkit-scrollbar {
    width: unset;
}

::-webkit-scrollbar-track {
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #0294ff33;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #0296ff80;
}
</style>
