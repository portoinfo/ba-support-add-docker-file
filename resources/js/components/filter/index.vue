<template>
    <div id="ba-hd__chat" class="chat">
        <div id="ba-hd__header" class="filter">
            <div class="header_title d-table h-100 w-100">
                <span class="ba-hd__title d-table-cell align-middle">
                    {{ title }}
                </span>
            </div>
            <div class="header_title d-table h-100 w-100" v-if="isMobile">
                <span class="ba-hd__title d-table-cell align-middle">
                    <b-button v-b-toggle.sidebar-filter-mobile size="sm" variant="light">
                        <span class="material-icons-outlined">filter_list</span>
                    </b-button>
                </span>
            </div>
        </div>
        <div id="ba-hd__main">
            <div class="ba-hd__card-content">
                <div class="w-100 h-100 p-0 m-0 non-selectable" v-if="!loading">
                    <div class="table-responsive paginated">
                        <table class="table table-sm table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <b-button
                                            size="sm"
                                            variant="light"
                                            block
                                            @click="selectAll"
                                            v-model="allSelected"
                                        >
                                            {{ namebttake }}
                                        </b-button>
                                    </th>
                                    <th scope="col"></th>
                                    <th scope="col">#</th>
                                    <th scope="col">{{ $t("bs-type") }}</th>
                                    <th scope="col" class="text-left">{{ $t("bs-client") }}</th>
                                    <th scope="col" class="text-left">{{ $t("bs-email") }}</th>
                                    <th scope="col">{{ $t("bs-date") }}</th>
                                    <th scope="col">{{ $t("bs-status") }}</th>
                                    <th scope="col" class="text-left">{{ $t("bs-department") }}</th>
                                    <th scope="col" class="text-left">{{ $t("bs-operator") }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(q, i2) in list.data">
                                    <tr
                                        :key="i2"
                                    >
                                        <td>
                                            <b-form-checkbox 
                                                @change="selectOne"
                                                :value="q.chat_id"
                                                v-model="selectedChatid"
                                            >
                                            </b-form-checkbox>
                                        </td>
                                        <td>
                                            <b-button
                                                size="sm"
                                                variant="light"
                                                block
                                                class="text-dark"
                                                @click="q.ticket_id ?  openModal(q, 'ticket_id') : openModal(q, 'chat_id')"
                                            >
                                                {{ $t("bs-view") }}
                                            </b-button>
                                        </td>
                                        <th scope="row" :class="q.is_merged ? 'mergedcss' : ''">#{{ q.ticket_id ? q.ticket_id : q.chat_id }}</th>
                                        <td class="text-left">
                                            <span class="material-icons-outlined mr-2" style="position: relative; top: 5px;">{{ q.ticket_id ? 'email' : 'question_answer'}}</span>
                                            {{ q.ticket_id ? $t('bs-ticket') : $t('bs-chat')}}
                                        </td>
                                        <td class="text-left td-name">
                                            <gravatar
                                                :email="q.email_client"
                                                :status="$status.get(q.id_client)"
                                                :size="gravatarSize"
                                                :name="$t(q.name_client)"
                                                class="mr-2 ml-1"
                                                :ba_acct_data="q.builderall_account_data"
                                            />
                                            {{ $t(q.name_client) }}
                                        </td>
                                        <td>{{ q.name_client == 'bs-user' ? '--' : q.email_client }}</td>
                                        <td>
                                            {{ q.ticket_id ? UTCtoClientTZ(q.ticket_created_at, tz) : UTCtoClientTZ(q.chat_created_at, tz) }}
                                        </td>
                                        <td>
                                            {{ q.ticket_id ? statusFormat(q.ticket_status) : statusFormat(q.status) }}
                                        </td>
                                        <td class="text-left">
                                            <gravatar
                                                :email="q.department"
                                                status="false"
                                                :size="gravatarSize"
                                                :name="$t(q.department)"
                                                class="mr-2 ml-1"
                                                :ba_acct_data='`{"is_vip": ${q.department_type == "builderall-mentor"}}`'
                                            />
                                            {{ $t(q.department) }}
                                        </td>
                                        <td class="text-left td-name">
                                            <gravatar
                                                :email="q.is_robot ? 'grey' : q.email"
                                                :status="q.is_robot ? 'false' : $status.get(q.operator_id)"
                                                :size="gravatarSize"
                                                :name="q.is_robot ? $t('bs-robot') : $t(q.name)"
                                                class="mr-2 ml-1"
                                            />
                                            {{ q.is_robot ? $t('bs-robot') :q.name }}
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-pagination pl-3 pr-3">
                        <div class="row">
                            <div class="col pr-0 pt-1 align-middle">
                                <div>
                                    <small class="results-pp" for="sb-inline">{{ $t('bs-per-page') }}</small>
                                    <select v-model="pp_selected">
                                        <option
                                            v-for="option in $store.state.pp_options"
                                            :key="option.value"
                                        >
                                            {{ option.text }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col pl-0">
                                <pagination
                                    :limit="2"
                                    align="right"
                                    size="small"
                                    :data="list"
                                    :show-disabled="true"
                                    @pagination-change-page="getAllHistory"
                                >
                                    <span slot="prev-nav"><vue-material-icon name="arrow_back" :size="14" /></span>
                                    <span slot="next-nav"><vue-material-icon name="arrow_forward" :size="14" /></span>
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
                <table-loading v-else/>
            </div>
        </div>
        <div class="ba-hd__right-sidebar" v-if="!isMobile">
            <div class="ba-hd__card-content p-2 overflow-auto">
                <label>
                    <b>{{ $t("bs-type") }}</b>
                </label>
                <b-form-select
                    class="mb-3"
                    v-model="typeSelected"
                    :options="typeOptions"
                ></b-form-select>

                <label>
                    <b>{{ $t("bs-status") }}</b>
                </label>
                <b-form-select
                    class="mb-3"
                    v-model="statusSelected"
                    :options="statusOptions"
                ></b-form-select>

                <label>
                    <b>{{ $t("bs-department") }}</b>
                </label>
                <b-form-select
                    class="mb-3"
                    v-model="departmentSelected"
                    :options="departments"
                ></b-form-select>

                <div class="form-group">
                    <div class="form-outline">
                    <input
                        type="search"
                        v-model="searchQuery"
                        class="form-control"
                        :placeholder="$t('bs-search')"
                    />
                    </div>
                </div>

                <b-form-group class="mt-4" v-slot="{ ariaDescribedby }">
                    <b-form-radio
                    @change="setTypeSelected"
                    v-model="selected"
                    :aria-describedby="ariaDescribedby"
                    name="some-radios"
                    value="filter-nameComplete"
                    >{{ $t("bs-full-name") }}</b-form-radio
                    >
                    <b-form-radio
                    @change="setTypeSelected"
                    v-model="selected"
                    :aria-describedby="ariaDescribedby"
                    name="some-radios"
                    value="filter-id"
                    >ID</b-form-radio
                    >
                    <b-form-radio
                    @change="setTypeSelected"
                    v-model="selected"
                    :aria-describedby="ariaDescribedby"
                    name="some-radios"
                    value="filter-description"
                    >{{ $t("bs-description") }}</b-form-radio
                    >
                    <b-form-radio
                    @change="setTypeSelected"
                    v-model="selected"
                    :aria-describedby="ariaDescribedby"
                    name="some-radios"
                    value="filter-email"
                    >E-mail</b-form-radio
                    >

                    <b-form-radio
                    @change="setTypeSelected"
                    v-model="selected"
                    :aria-describedby="ariaDescribedby"
                    name="some-radios"
                    value="filter-operator"
                    >{{ $t("bs-operator") }}</b-form-radio
                    >
                    <b-form-radio
                    v-if="typeSelected === 'TICKET'"
                    @change="setTypeSelected"
                    v-model="selected"
                    :aria-describedby="ariaDescribedby"
                    name="some-radios"
                    value="filter-comment"
                    >{{ $t("bs-comment") }}</b-form-radio
                    >
                </b-form-group>
                <hr />
                <b-form-group>
                    <b-form-checkbox
                    @change="(date1 = ''), (date2 = '')"
                    v-model="filter_date"
                    name="some-radios"
                    value="true"
                    unchecked-value="false"
                    >{{ $t("bs-date") }}</b-form-checkbox
                    >
                </b-form-group>
                <div class="form-group">
                    <label for="date1" class="col-12 col-form-label"><b>De</b></label>
                    <div class="col-12">
                    <input
                        :disabled="filter_date === 'false'"
                        class="form-control"
                        type="date"
                        v-model="date1"
                        id="date1"
                    />
                    </div>
                    <label for="date2" class="col-12 col-form-label"><b>Até</b></label>
                    <div class="col-12">
                    <input
                        :disabled="filter_date === 'false'"
                        class="form-control"
                        type="date"
                        v-model="date2"
                        id="date2"
                    />
                    </div>
                </div>
                <export-excel
                    v-if="showDownload"
                    class   = "caret w-100 btn btn-success"
                    :data   = "json_data"
                    :fields = "json_fields"
                    worksheet = "My Worksheet"
                    :name    = "name_file">
                    <i class="fa fa-download" aria-hidden="true"></i>
                    {{$t('bs-download-excel')}}
                </export-excel>
                <!-- <b-button class="w-100" variant="danger"  @click="optionsPDF()">PDF</b-button> -->
                <b-button class="w-100 mb-5" variant="primary" @click="getAllHistory">
                    {{$t("bs-filter")}}
                </b-button>
            </div>
        </div>

        <b-sidebar id="sidebar-filter-mobile" :title="$t('bs-filter')" right shadow backdrop>
            <div class="p-2">
                <label>
                    <b>{{ $t("bs-type") }}</b>
                </label>
                <b-form-select
                    class="mb-3"
                    v-model="typeSelected"
                    :options="typeOptions"
                ></b-form-select>

                <label>
                    <b>{{ $t("bs-status") }}</b>
                </label>
                <b-form-select
                    class="mb-3"
                    v-model="statusSelected"
                    :options="statusOptions"
                ></b-form-select>

                <label>
                    <b>{{ $t("bs-department") }}</b>
                </label>
                <b-form-select
                    class="mb-3"
                    v-model="departmentSelected"
                    :options="departments"
                ></b-form-select>

                <div class="form-group">
                    <div class="form-outline">
                    <input
                        type="search"
                        v-model="searchQuery"
                        class="form-control"
                        :placeholder="$t('bs-search')"
                    />
                    </div>
                </div>

                <b-form-group class="mt-4" v-slot="{ ariaDescribedby }">
                    <b-form-radio
                    @change="setTypeSelected"
                    v-model="selected"
                    :aria-describedby="ariaDescribedby"
                    name="some-radios"
                    value="filter-nameComplete"
                    >{{ $t("bs-full-name") }}</b-form-radio
                    >
                    <b-form-radio
                    @change="setTypeSelected"
                    v-model="selected"
                    :aria-describedby="ariaDescribedby"
                    name="some-radios"
                    value="filter-id"
                    >ID</b-form-radio
                    >
                    <b-form-radio
                    @change="setTypeSelected"
                    v-model="selected"
                    :aria-describedby="ariaDescribedby"
                    name="some-radios"
                    value="filter-description"
                    >{{ $t("bs-description") }}</b-form-radio
                    >
                    <b-form-radio
                    @change="setTypeSelected"
                    v-model="selected"
                    :aria-describedby="ariaDescribedby"
                    name="some-radios"
                    value="filter-email"
                    >E-mail</b-form-radio
                    >

                    <b-form-radio
                    @change="setTypeSelected"
                    v-model="selected"
                    :aria-describedby="ariaDescribedby"
                    name="some-radios"
                    value="filter-operator"
                    >{{ $t("bs-operator") }}</b-form-radio
                    >
                    <b-form-radio
                    v-if="typeSelected === 'TICKET'"
                    @change="setTypeSelected"
                    v-model="selected"
                    :aria-describedby="ariaDescribedby"
                    name="some-radios"
                    value="filter-comment"
                    >{{ $t("bs-comment") }}</b-form-radio
                    >
                </b-form-group>
                <hr />
                <b-form-group>
                    <b-form-checkbox
                    @change="(date1 = ''), (date2 = '')"
                    v-model="filter_date"
                    name="some-radios"
                    value="true"
                    unchecked-value="false"
                    >{{ $t("bs-date") }}</b-form-checkbox
                    >
                </b-form-group>
                <div class="form-group">
                    <label for="date1"><b>De</b></label>
                    <input
                        :disabled="filter_date === 'false'"
                        class="form-control"
                        type="date"
                        v-model="date1"
                        id="date1"
                    />
                    <label for="date2"><b>Até</b></label>
                    <input
                        :disabled="filter_date === 'false'"
                        class="form-control"
                        type="date"
                        v-model="date2"
                        id="date2"
                    />
                </div>
                <b-button class="w-100 mb-5" variant="primary" @click="getAllHistory">{{
                    $t("bs-filter")
                }}</b-button>
            </div>
        </b-sidebar>

        <div
            class="modal fade"
            id="modalClientHistory"
            tabindex="-1"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
            data-keyboard="false"
        >
            <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header border-0">
                <h5 class="modal-title" id="exampleModalLabel">
                    {{ $t("bs-client") }}: <span>{{ chat.client.name }}</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="pr-3 pl-3 mt-n3">
                <div class="card">
                    <div class="card-header bg-white">
                    {{ $t(chat.type) }} <b>#{{ chat.number }}</b> - {{ chat.date }}
                    </div>
                    <div
                    class="card-body p-0 content-msg"
                    >
                    <message-type-questionary
                        v-if="questionary.length"
                        :chat="chat"
                        :questionary="questionary"
                        :formatTime="UTCtoClientTZ2"
                    />
                    <template v-if="chat_history.length">
                        <component
                        v-for="(message, index) in chat_history"
                        :key="index"
                        :is="setMessageComponent(message.type)"
                        v-bind="setMessageProps(message, index)"
                        />
                    </template>
                    <template v-if="ticket_history.length">
                        <component
                        v-for="(message, index) in ticket_history"
                        :key="index"
                        :is="setTicketMessageComponent(message)"
                        v-bind="setTicketMessageProps(message, index)"
                        />
                    </template>
                    </div>
                    <div class="card-footer border-0 bg-white"></div>
                </div>
                </div>

                <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    {{ $t("bs-close") }}
                </button>
                </div>
            </div>
            </div>
        </div>

    <div class="modal fade" id="optionsModalPDF" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"  data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content pt-2 mt-2">
                <div class="modal-body">
                    <div class="row">
                        <div class="modal-header border-0 p-0 m-0 mb-3">
                            <center><h5 class="modal-title" id="optionsModalPDFLabel">Selecione as configurações que desejar:</h5></center>
                        </div>
                        <div class="col-12">
                            <b-form-checkbox
                            id="checkbox-1"
                            name="checkbox-1"
                            v-model="cbpdf.title"
                            >
                            <span class="textcheck"> - {{$t('bs-title')}}</span>
                            </b-form-checkbox>
                        </div>
                        <div class="col-12">
                            <b-form-checkbox
                            id="checkbox-2"
                            name="checkbox-2"
                            v-model="cbpdf.title"
                            >
                            <span class="textcheck"> - {{$t('bs-signature')}}</span>
                            </b-form-checkbox>
                        </div>
                    </div>
                    <br>
                    <div>
                        <b-row cols="auto">
                            <b-col>
                                <center class="mb-1">
                                    <b-button block  class="caret" variant="outline-danger">Gerar PDF</b-button>
                                </center>
                            </b-col>
                        </b-row>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
</template>

<script>

export default {
  name: "filter-tickets-chats",
  props: {
    session_user: Object,
    session_user_company: Object,
    session_user_cucd: Array,
    session_user_departments: Array,
    session_user_permissions: Array,
  },
  data: function () {
    return {
        showDownload: false,
        name_file: '',
        json_fields: {
            'Complete name': 'name',
            'City': 'city',
            'Telephone': 'phone.mobile',
            'Telephone 2' : {
                field: 'phone.landline',
                callback: (value) => {
                    return `Landline Phone - ${value}`;
                }
            },
        },
        json_data: [
            {
                'name': 'Tony Peña',
                'city': 'New York',
                'country': 'United States',
                'birthdate': '1978-03-15',
                'phone': {
                    'mobile': '1-541-754-3010',
                    'landline': '(541) 754-3010'
                }
            },
        ],
        json_meta: [
            [
                {
                    'key': 'charset',
                    'value': 'utf-8'
                }
            ]
        ],
        pp_selected: "25",
        chat: {
            number: "",
            type: "",
            date: "",
            department: "",
            client: {
            id: "",
            name: "",
            email: "",
            },
        },
        ref: "filterTicketsChats",
        id: "",
        tz: "",
        admin: this.session_user_permissions[0]["chat_admin"],
        company_department: {},
        departments: [],
        title: this.$t("bs-archive"),
        isMobile: false,
        showMenuMobile: false,
        selected: "filter-id",
        filter_date: "false",
        date1: "",
        date2: "",
        searchQuery: "",
        list: [],
        searchInterval: null,
        searchIntervalTimeout: 2000, // 2s
        typeOptions: [
            { value: "ALL", text: this.$t("bs-all") },
            { value: "CHAT", text: this.$t("bs-chat") },
            { value: "TICKET", text: this.$t("bs-ticket") },
        ],
        typeSelected: "ALL",
        statusOptions: [
            { value: "ALL", text: this.$t("bs-all") },
            { value: "CLOSED", text: this.$t("bs-closed-s") },
            { value: "IN_PROGRESS", text: this.$t("bs-in-progress") },
            { value: "RESOLVED", text: this.$t("bs-resolved-s") },
            { value: "CANCELED", text: this.$t("bs-canceled-s") },
        ],
        statusSelected: "ALL",
        departmentSelected: "ALL",
        questionary: [],
        chat_history: [],
        ticket_history: [],
        messageComponent: {
            EVENT: "message-type-event",
            TEXT: "message-type-text",
            OPEN: "message-type-open-agent",
            CLOSE: "message-type-close-agent",
            FILE: "message-type-file-agent",
            IMAGE: "message-type-image-agent",
        },
        ticketMessageComponent: {
            FILE: "message-type-file", // "message-type-text-file"
            IMAGE: "message-type-image",
            EVENT: "message-type-event-client",
            TEXT_SENT: "message-type-text-sent",
            TEXT_RECEIVED: "message-type-text-received",
            TEXT_SENT_RECEIVED_NEW: "message-type-tk-message",
        },
        ticket_info: {
            id_department: "",
            id_ticket: "",
            company_user_id: "",
            settings: {
            evaluation: {
                text_atten_active: "0",
                text_serv_active: "0",
                text_comment_active: "0",
            },
            },
            priority: "",
            description: "",
            msgOpen: "",
        },
        chat2: {
            id: "",
            type: "TEXT",
            content: "",
            clear: "",
        },
        cb_take: false,
        takeTicket2: [],
        namebttake: this.$t("bs-all"),
        //loading
        page: 1,
        infiniteId: +new Date(),
        numberOfTabs: 0,
        loading: true,
        allSelected: false,
        selectedChatid: [],
        cbpdf: {
            title: true,
            signature: true,
        },
        indexCh: 0,
        chatHistoryFields: [
            { key: 'index', label: '#'},
            { key: 'type', label: this.$t('bs-type')},
            { key: 'content', label: this.$t('bs-message')},
            { key: 'content_translated', label: this.$t('bs-translation')},
            { key: 'created_at', label: this.$t('bs-date')},
        ]
    };
  },
  mounted(){
  },
  methods: {
    selectAll() {
        this.allSelected = !this.allSelected
        this.selectedChatid = [];
        if (this.allSelected) {
            for (var i = 0; i < this.list.data.length;i++){
                this.selectedChatid.push(this.list.data[i].chat_id);
            }
        }

    },
    selectOne(item) {
        console.log(item);
        this.selectedChatid = item
    },
    getChatHistoryExcel(){
        alert('eae');
    },
    optionsPDF(){
        if(this.selectedChatid.length == 0){
            this.$snotify.info( this.$t('bs-please-select-an-option') , this.$t('bs-info'), {
                position: "rightTop",
            });
        }else{
            $("#optionsModalPDF").modal("show");
            axios.get("get-ticket-chat-generate-pdf", {
                params: {
                    ct_ids: this.selectedChatid
                },
            }).then((response) => {
                console.log(response);
            });
        }
    },
    openModal(chat, ref) {
      this.clearInfo();
      if (ref === "chat_id") {
        this.chat.id = chat.chat_number;
        this.chat.number = chat.chat_id;
        this.chat.type = "bs-chat";
        this.chat.date = this.UTCtoClientTZ(chat.chat_created_at, this.tz);
        this.id = chat.chat_number;

        this.chat.department = chat.department;
        this.chat.client.id = chat.id_client;
        this.chat.client.name = chat.name_client;
        this.getChatHistory();
        $("#modalClientHistory").modal("show");
      } else if (ref === "ticket_id") {
        this.chat2 = {
          id: "",
          type: "TEXT",
          content: "",
          clear: "",
        };
        this.chat.number = chat.ticket_id;
        this.chat.date = this.UTCtoClientTZ(chat.ticket_created_at, this.tz);
        this.chat.client.name = chat.name_client;
        this.id = chat.ticket_number;
        this.chat.type = "bs-ticket";
        this.ticket_info.id_department = chat.department_id;
        this.ticket_info.id_ticket = chat.ticket_id;
        this.ticket_info.priority = chat.priority;
        this.ticket_info.description = chat.description;
        this.ticket_info.department_name = chat.department;
        this.getTicketHistory(chat.ticket_id, chat.chat_id);
        $("#modalClientHistory").modal("show");
      }
    },
    clearInfo() {
      this.chat_history = [];
      this.ticket_history = [];
      this.chat.id = "";
      this.chat.number = "";
      this.chat.type = "";
      this.chat.date = "";
      this.id = "";
      this.chat.department = "";
      this.chat.client.name = "";
      this.ticket_info.id_department = "";
      this.ticket_info.id_ticket = "";
      this.ticket_info.priority = "";
      this.ticket_info.description = "";
      this.ticket_info.department_name = "";
    },
    getChatHistory() {
      axios.get("ticket-chat-answer/agent/get-ticket-chat-answers", {
          params: {
            id: this.id,
            reference: "chat_id",
          },
        }).then((response) => {
          if (response.data.status) {
            this.questionary = response.data.result;
          }
        });
      setTimeout(() => {
        const api = `chat-history/agent/get-chat-history`;
        axios
          .get(api, {
            params: {
              id: this.id,
            },
          })
          .then(({ data }) => {
            data.forEach(message => {
                if (message.content_translated !== null) {
                    if (message.company_user_company_department_id !== null) {
                      var content_translated = JSON.parse(message.content_translated);
                      content_translated = content_translated[0].content;

                      var content = message.content;

                      message.content = content_translated;
                      message.content_translated = content;
                    } else {
                        var content_translated = JSON.parse(message.content_translated);
                        content_translated = content_translated[0].content;
                        message.content_translated = content_translated;
                    }
                    
                    this.chat_history.push(message);
                } else {
                    this.chat_history.push(message);
                }
            });
          });
      }, 1000);
    },
    setMessageComponent(type) {
      return this.messageComponent[type];
    },
    setMessageProps(message, index) {
      return {
        message: this.chat_history[index],
        formatTime: this.UTCtoClientTZ2,
      };
    },
    getTicketHistory(id_ticket, id_chat) {
      axios.get("client-chat-ticket/" + id_ticket).then((response) => {
        // console.log(response)
        this.chat2.id = id_chat;
        let _this = this;
        this.ticket_history = response.data.result.map(function (obj) {
          obj.department = _this.ticket_info.department_name;
          return obj;
        });
        this.ticket_history.unshift({
          type: "TEXT",
          content: response.data.ticket.description,
          client_id: response.data.client_id,
          name: response.data.client_name,
          email: response.data.client_email,
          department: "",
          created_at: response.data.ticket.created_at,
        });
        //for (var i = 0 ; i < response.data.quests.length; i++) {
        for (var i = response.data.quests.length - 1; i >= 0; i--) {
          //console.log(response.data.quests[i].question);
          //console.log(response.data.quests[i].answer);
          this.ticket_history.unshift({
            type: "TEXT",
            content: response.data.quests[i].answer,
            client_id: response.data.client_id,
            name: response.data.client_name,
            email: response.data.client_email,
            department: "",
            created_at: response.data.quests[i].created_at,
          });
          this.ticket_history.unshift({
            type: "TEXT",
            content: response.data.quests[i].question,
            name: this.$t(this.ticket_info.department_name),
            email: this.ticket_info.department_name,
            department: this.ticket_info.department_name,
            company_user_company_department_id: -1,
            created_at: response.data.quests[i].created_at,
          });
        }
      });
    },
    setTicketMessageComponent(message) {
      if (["TEXT", "IMAGE", "FILE"].includes(message.type)) {
        /* original
        if (message.company_user_company_department_id) {
          return this.component["TEXT_RECEIVED"];
        } else {
          return this.component["TEXT_SENT"];
        } */
        return this.ticketMessageComponent["TEXT_SENT_RECEIVED_NEW"];
      } else {
        return this.ticketMessageComponent[message.type];
      }
    },
    setTicketMessageProps(message, index) {
      return {
        message: this.ticket_history[index],
        language: this.session_user.language,
        user: this.session_user
      };
    },

    toUTC(h) {
      let h_format = moment(h, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD HH:mm:ss");
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
      let dateUTC = new Date(Date.UTC(year, month_integer, day, hour, minute, second));
      let converted_time = dateUTC.toLocaleString("pt-BR", {
        timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone,
      });

      return converted_time;
    },
    format_L_LT(h) {
      let converted_time = this.toUTC(h);
      var mt = require("moment-timezone");
      return (
        mt(converted_time, "DD/MM/YYYY HH:mm:ss")
          .tz(Intl.DateTimeFormat().resolvedOptions().timeZone)
          .locale(this.session_user.language)
          .format("L") +
        " - " +
        mt(converted_time, "DD/MM/YYYY HH:mm:ss")
          .tz(Intl.DateTimeFormat().resolvedOptions().timeZone)
          .locale(this.session_user.language)
          .format("LT")
      );
    },
    onResize(e) {
            if ($(window).width() <= 992) {
                this.isMobile = true;
            } else {
                this.isMobile = false;
            }
        },
    goBackToMenu() {
      if (this.isMobile) {
        this.showMenuMobile = true;
      }
    },
    goToTable() {
      if (this.isMobile) {
        this.showMenuMobile = false;
      }
    },
    statusFormat(status) {
      switch (status) {
        default:
        case "CLOSED":
          return this.$t("bs-closed");
          break;
        case "RESOLVED":
          return this.$t("bs-resolved");
          break;
        case "CANCELED":
          return this.$t("bs-canceled");
          break;
        case "IN_PROGRESS":
          return this.$t("bs-in-progress");
          break;
      }
    },
    setTypeSelected() {
      localStorage.setItem("fullfilterselected", this.selected);
    },
    searchIntervalFunc: function (vm = this) {
      //   console.log("interval");
      //   vm.search();

      clearInterval(vm.searchInterval);
      vm.searchInterval = null;
      //   console.log("clear interval INSIDE");
    },
    getAllHistory(page = 1) {
      var vm = this;
      vm.loading = true;
      if (
        (vm.filter_date === "true" && vm.date1 === "") ||
        (vm.filter_date === "true" && vm.date2 === "")
      ) {
        Swal.fire({
          icon: "warning",
          heightAuto: false,
          title: vm.$t("bs-caution"),
          text: vm.$t("bs-please-fill-in-the-date-fields-correctly"),
        });
      } else if (vm.filter_date === "true" && vm.date1 > vm.date2) {
        Swal.fire({
          icon: "warning",
          heightAuto: false,
          title: vm.$t("bs-caution"),
          text: vm.$t("bs-the-first-date-cannot-be-later"),
        });
      } else {
        if (vm.filter_date === "true") {
          var mt = require("moment-timezone");
          var dt1 = mt.tz(vm.date1, vm.tz).utc().format("YYYY-MM-DD");
          var dt2 = mt.tz(vm.date2, vm.tz).utc().format("YYYY-MM-DD");
        } else {
          var dt1 = "";
          var dt2 = "";
        }
        axios
          .get("/get-all-history", {
            params: {
              type: vm.typeSelected,
              status: vm.statusSelected,
              department: vm.departmentSelected,
              option: vm.selected,
              search_query: vm.searchQuery,
              filter_date: vm.filter_date,
              date1: dt1,
              date2: dt2,
              tz: this.tz,
              page: page,
              per_page: Number(vm.pp_selected)
            },
          })
          .then(({ data }) => {
            // console.log(data.list);
            vm.list = data.list;
            vm.loading = false;
          });
      }
    },
    getDepartmentsByAgent() {
      const prefix = "company-user-company-department";
      const api = `${
        this.admin ? "get-departments-by-company" : prefix + "/get-department-by-agent"
      }`;

      axios.get(api).then(({ data }) => {
            this.departments.push({ value: "ALL", text: this.$t("bs-all") });
        data.forEach((element) => {
            this.departments.push({ value: element.id, text: element.name });
        });
      });
    },
    UTCtoClientTZ(h, tz) {
      let h_format = moment(h, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD HH:mm:ss");
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
      let dateUTC = new Date(Date.UTC(year, month_integer, day, hour, minute, second));
      let converted_time = dateUTC.toLocaleString("pt-BR", {
        timeZone: tz,
      });

      var mt = require("moment-timezone");
      return mt(converted_time, "DD/MM/YYYY HH:mm:ss")
        .tz(this.tz)
        .locale(this.session_user.language)
        .calendar();
    },
    UTCtoClientTZ2(h, tz) {
      let h_format = moment(h, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD HH:mm:ss");
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
      let dateUTC = new Date(Date.UTC(year, month_integer, day, hour, minute, second));
      let converted_time = dateUTC.toLocaleString("pt-BR", {
        timeZone: tz,
      });

      var mt = require("moment-timezone");
      return mt(converted_time, "DD/MM/YYYY HH:mm:ss")
        .tz(this.tz)
        .locale(this.session_user.language)
        .format("LT");
    },
  },
  computed: {
    gravatarSize() {
        if (this.isMobile) {
            return "40px";
        } else {
            return "30px";
        }
    },
  },
  watch: {
    selectedChatid(){

        this.json_fields = {
            'Chat ID' : 'chat_id',
            'Nome': 'name',
            'Messagem' : 'content',
            'Tipo' : 'type',
            'Data' : 'created_at',
        };
  
        if(this.selectedChatid.length > 0){
        // if(this.selectedChatid.length == 1){
            axios.get("get-ticket-chat-generate-excel", {
                params: {
                    ct_ids: this.selectedChatid,
                    tz: this.tz,
                },
            }).then((response) => {
                this.showDownload = true;
                this.name_file = 'chat_history';
                console.log(response);
                this.json_data = response.data.chat_history;
            });
        }else{
            this.showDownload = false;
        }

       
    },
    pp_selected(newValue, oldValue) {
        if (newValue !== oldValue) {
            this.getAllHistory();
        }
    },
    date1: function () {
      if (this.date2 === "") {
        this.date2 = this.date1;
      }
    },
    searchQuery: function (newVal, oldVal) {
      //   console.log("watch = ", newVal);
      if (this.searchInterval !== null) {
        // console.log("clear interval");
        clearInterval(this.searchInterval);
        this.searchInterval = null;
      }
      if (newVal != "") {
        this.searchInterval = setInterval(
          this.searchIntervalFunc,
          this.searchIntervalTimeout,
          this
        );
      }
    },
    selected: function (newVal, oldVal) {
      //   console.log("watch selected = ", oldVal, " | ", newVal);
      if (newVal != "") {
        // this.search();
      }
    },
    typeSelected: function (newVal, oldVal) {
      //   console.log("watch status selected = ", oldVal, " | ", newVal);
      if (newVal != "") {
        // this.search();
      }
    },
    statusSelected: function (newVal, oldVal) {
      //   console.log("watch type selected = ", oldVal, " | ", newVal);
      if (newVal != "") {
        // this.search();
      }
    },
    "$store.state.chatsFooter": function () {
        this.numberOfTabs = this.$store.state.chatsFooter.length;
    },
  },
  created() {
    this.numberOfTabs = this.$store.state.chatsFooter.length;
    this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
    this.getDepartmentsByAgent();
    window.addEventListener("resize", this.onResize);
    this.onResize();

    if (localStorage.getItem("fullfilterselected") == null) {
      localStorage.setItem("fullfilterselected", "filter-id");
    } else {
      this.selected = localStorage.getItem("fullfilterselected");
    }

    this.getAllHistory();
  },
  mounted() {},
};
</script>

<style lang="scss" scoped>

.ba-hd__title {
    color: #0080fc;
    font-family: Muli;
    font-weight: 800;
    font-size: 1.4rem;
    letter-spacing: 0px;
    color: #0080fc;
    width: 0px;
}

.mergedcss{
  background-color: #e79a9a;
  color: black;
}

table, tr, td, th, tbody, thead, tfoot {
    page-break-inside: avoid !important;
}

.ql-editor {
    padding: 0 !important;
}

.textcheck{
    color:black;
}

#modalClientHistory .modal-dialog {
  height: calc(100% - 100px) !important;
}

#modalClientHistory .modal-content {
  height: auto;
  min-height: 100%;
  background-color: #e9edf2 !important;
}

#modalClientHistory .modal-title span {
  color: #0080fc !important;
}

#modalClientHistory .modal-title {
  color: #96989a !important;
  font-weight: bold;
}

#modalClientHistory .nav-pills .nav-link {
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

#modalClientHistory .nav-pills .nav-link:hover {
  background-color: transparent !important;
  color: #0080fc !important;
  border-bottom: 3px solid #0080fc !important;
}

#modalClientHistory .nav-pills .nav-link.active {
  background-color: transparent !important;
  color: #0080fc !important;
  border-bottom: 3px solid #0080fc !important;
}

#modalClientHistory .card-body {
  min-height: calc(100vh - 252px);
  max-height: calc(100vh - 252px);
  overflow-y: auto;
  overflow-x: hidden;
}

#modalClientHistory .card {
  border-radius: 0px;
}

.ba-hd__right-sidebar{
    max-width: 250px;
    margin-left: 6px;
}

</style>
