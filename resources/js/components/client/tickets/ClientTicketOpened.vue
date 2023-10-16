<template>
    <v-container v-if="!$store.state.isMobile" fluid class="h-100 d-flex flex-column flex-nowrap">
        <v-row no-gutters class="grow overflow-hidden" justify="center" style="position: relative;">
            <v-col style="position: absolute;" class="h-100">
                <v-row no-gutters class="h-100">
                    <v-col class="h-100 pr-1 col-chat-opened-list" v-if="!$store.state.isTablet">
                        <client-ticket-list></client-ticket-list>
                    </v-col>
                    <v-col class="h-100" style="width: 1px;" :class="{'px-1': showDetails, 'pl-1': !showDetails}">
                        <v-sheet color="containerBackground" class="py-2" height="100%" rounded="lg">
                            <v-container fluid pa-0	class="d-flex flex-column flex-grow-1 h-100">
                                <v-row no-gutters class="top-row flex-grow-1 flex-shrink-1 pb-12">
                                    <v-col cols="12">
                                        <client-ticket-opened-header
                                            :app="false"
                                            :clippedRight="false"
                                            :flat="false"
                                        />
                                    </v-col>
                                    <v-col cols="12" class="ov-y h-100" v-chat-scroll="{always: false, smooth: true}">
                                        <v-row justify="center" no-gutters>
                                            <v-col xl="10" lg="11">
                                                <v-container v-if="ticketInfoLoaded">
                                                    <infinite-loading direction="top" @infinite="getChatHistory">
                                                        <div slot="spinner">
                                                            <v-row  no-gutters>
                                                                <v-col>
                                                                    <v-progress-circular indeterminate color="primary"></v-progress-circular>
                                                                </v-col>
                                                            </v-row>
                                                        </div>
                                                        <div slot="no-more"></div>
                                                        <div slot="no-results"></div>
                                                    </infinite-loading>
                                                    <component
                                                        v-for="(ch, index) in messages"
                                                        :key="index"
                                                        :is="setMessageComponent(ch)"
                                                        :message="ch"
                                                        :index="index"
                                                    />
                                                </v-container>
                                                <v-row v-else no-gutters>
                                                    <v-col class="text-center py-3">
                                                        <v-progress-circular indeterminate color="primary"></v-progress-circular>
                                                    </v-col>
                                                </v-row>
                                            </v-col>
                                        </v-row>
                                    </v-col>
                                </v-row>
                                <v-row justify="center" no-gutters class="flex-grow-0 flex-shrink-0">
                                    <v-col cols="12" style="height: fit-content" class="px-5 pt-7 pb-0 justify-center">
                                        <template v-if="showEditor">
                                            <v-sheet v-if="!isCreate" @click="answerDialogShown = true" color="input" class="text-body-2 font-weight-medium w-fc mx-auto py-2 px-4 cursor-pointer" style="margin-bottom: 72px;" rounded elevation="4">
                                                {{ buttonSendMessageText }}
                                                <v-icon color="#0080FC">mdi-plus</v-icon>
                                            </v-sheet>
                                            <template v-else>
                                                <client-chat-ticket-editor module="ticket" v-if="$store.state.newTicket.questions.length && !storingTicket"/>
                                                <v-skeleton-loader
                                                    v-else
                                                    type="card"
                                                    height="90px"
                                                    class="rounded-lg"
                                                ></v-skeleton-loader>
                                            </template>
                                        </template>
                                    </v-col>
                                </v-row>
                            </v-container>
                        </v-sheet>
                    </v-col>
                    <v-col v-if="showDetails && !$store.state.isMedium" class="h-100 pl-1 col-chat-opened-details" style="width: 1px;">
                        <v-system-bar window color="containerBackground" class="details-bar">
                            <v-icon class="cursor-pointer" @click="showDetails = false">mdi-close</v-icon>
                            <span>{{ $t('bs-details') }}</span>
                            <v-spacer></v-spacer>
                        </v-system-bar>
                       <client-ticket-opened-details></client-ticket-opened-details>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
    </v-container>
    <client-ticket-opened-mobile v-else>
        <template v-slot:header>
            <client-ticket-opened-header
                :app="true"
                :clippedRight="true"
                :flat="true"
            />
        </template>
        <template v-slot:main>
            <v-container v-if="ticketInfoLoaded">
                <infinite-loading direction="top" @infinite="getChatHistory">
                    <div slot="spinner">
                        <v-row  no-gutters>
                            <v-col>
                                <v-progress-circular indeterminate color="primary"></v-progress-circular>
                            </v-col>
                        </v-row>
                    </div>
                    <div slot="no-more"></div>
                    <div slot="no-results"></div>
                </infinite-loading>
                <component
                    v-for="(ch, index) in messages"
                    :key="index"
                    :is="setMessageComponent(ch)"
                    :message="ch"
                    :index="index"
                />
            </v-container>
            <v-row v-else no-gutters>
                <v-col class="text-center py-3">
                    <v-progress-circular indeterminate color="primary"></v-progress-circular>
                </v-col>
            </v-row>
        </template>
        <template v-slot:footer>
            <template v-if="showEditor">
                <v-sheet v-if="!isCreate" @click="answerDialogShown = true" color="input" class="text-body-2 font-weight-medium w-fc mx-auto py-2 px-4 cursor-pointer" rounded elevation="4">
                    {{ buttonSendMessageText }}
                    <v-icon color="#0080FC">mdi-plus</v-icon>
                </v-sheet>
                <template v-else>
                    <client-chat-ticket-editor module="ticket" v-if="$store.state.newTicket.questions.length && !storingTicket"/>
                    <v-skeleton-loader
                        v-else
                        type="card"
                        height="90px"
                        class="rounded-lg"
                    ></v-skeleton-loader>
                </template>
            </template>
        </template>
    </client-ticket-opened-mobile>
</template>

<script>
import { mapMutations } from "vuex";
function initialState(){
  return {
    chatMessageComponents: {
        OPEN: "received-message-type-robot",
        CLOSE: "received-message-type-robot",
        FILE_SENT: "sent-message-type-file",
        FILE_RECEIVED: "received-message-type-file",
        IMAGE_SENT: "sent-message-type-text",
        IMAGE_RECEIVED: "received-message-type-text",
        EVENT: "message-type-event",
        TEXT_SENT: "sent-message-type-text",
        TEXT_RECEIVED: "received-message-type-text",
        ROBOT: "received-message-type-robot",
    },
    ticketMessageComponents: {
        OPEN: "received-message-type-robot-ticket",
        CLOSE: "received-message-type-robot-ticket",
        FILE_SENT: "sent-message-type-file-ticket",
        FILE_RECEIVED: "received-message-type-file-ticket",
        IMAGE_SENT: "sent-message-type-image-ticket",
        IMAGE_RECEIVED: "received-message-type-image-ticket",
        EVENT: "message-type-event-ticket",
        TEXT_SENT: "sent-message-type-text-ticket",
        TEXT_RECEIVED: "received-message-type-text-ticket",
        ROBOT: "received-message-type-robot-ticket",
    },
    messages: [],
    messages_page: 1,
    ticketInfoLoaded: false,
    isCreate: false,
    departmentSettings: {},
    rateDialogShown: false,
    // rateFormAction: 'RATE',
    evaluated: true,
    storingTicket: false,
    answerDialogShown: false,
    showDetails: false,
    maxDaysToReopenExceded: false,
  }
}
export default {
    data() {
        return initialState();
    },
    created () {
        this.$root.$refs.ClientTicketOpened = this;
        this.$store.state.drawer = true; // minimizar a navbar
    },
    computed: {
        country() {
            return this.$store.state.user.language.split("_")[1];
        },
        isRobot() {
            return  this.isCreate || this.ticket.cucd_id == null;
        },
        ticket: {
            get() {
                return this.$store.state.ticket;
            }
        },
        tickets: {
            get() {
                return this.$store.state.tickets;
            }
        },
        newTicket: {
            get() {
                return this.$store.state.newTicket;
            }
        },
        showEditor() {
            return (
                ["IN_PROGRESS", "OPENED"].some(status => status === this.ticket.status)
                || this.$store.state.newTicket.questions.length
                || this.isCreate
                || (this.ticket.status == 'CLOSED' && !this.maxDaysToReopenExceded)
            )
                && this.ticket.status !== 'RESOLVED'
        },
        q_idx: {
            get() {
                return this.$store.state.newTicket.q_idx
            }
        },
        content: {
			get() {
				return this.$store.state.currentEditor.content;
			},
			set(value) {
				this.$store.state.currentEditor.content = value;
			},
		},
        onlineUsers: {
            get() {
                return this.$store.state.online_users;
            }
        },
        buttonSendMessageText() {
            if (this.ticket.status == 'CLOSED') {
                return `${this.$t('bs-click-here-to-add-a-message')} ${this.$t('bs-and')} ${this.$t('bs-reopen')} ${this.$t('bs-ticket')}`;
            } else {
                return this.$t('bs-click-here-to-add-a-message');
            }
        }
    },
    watch: {
        '$route.params.id': {
            handler: function(newTicketId, oldTicketId) {
                if (oldTicketId) {
                    Object.assign(this.$data, initialState());
                    if (oldTicketId !== 'create') {
                        this.resetTicketData();
                        this.departmentSettings = {};
                    }
                }
                if (newTicketId == 'create') {
                    this.pushNewTicket();
                    this.isCreate = true;
                    this.ticketInfoLoaded = true;
                    if (this.newTicket.chat_history.length) {
                        this.messages = this.newTicket.chat_history;
                    } else {
                        this.openEmptyTicket();
                    }
                } else {
                    this.openTicket(newTicketId);
                }
            },
            deep: true,
            immediate: true
        },
        '$store.state.newTicket.department': function (v) {
            if (this.$route.params.id == 'create') {
                if (typeof v === 'object') {
                    var vm = this;
                    vm.getActiveTicketsFromCompany().then(() => {
                        vm.ticketInfoLoaded = false;
                        vm.$store.state.tickets[0].department = v.name
                        vm.$store.state.ticket.department_name = v.name;
                        vm.$store.state.ticket.department_id = v.id;
                        vm.getDepartmentQuests(v.id).then((questions) => {
                            var i = 0;
                            questions.forEach(quest => {
                                i++;
                                if (quest.language == null || vm.country == quest.language) {
                                    vm.$store.state.newTicket.questions.push(quest)
                                }
                                if (i == questions.length) {
                                    vm.ticketInfoLoaded = true;
                                    vm.sendQuestion();
                                }
                            });
                        })
                    })
                }
            }
        },
        ticket(newVal, oldVal) {
            if ('chat_hash_id' in oldVal) {
                this.leaveMessageSentTicketChannel(oldVal.chat_hash_id);
            }
        },
        'ticket.department_id': {
            handler: function(id, oldId) {
                var vm = this;
                if (id && oldId && id !== oldId) {
                    vm.getTicketInfo(vm.ticket.id).then((info) => {
                        vm.departmentSettings = info.settings;
                    })
                }
            },
            deep: true,
            immediate: true
        }
    },
    methods: {
        ...mapMutations(["resetTicketData", "pushNewTicket", "resetNewTicketData"]),
        cancelTicket() {
            var vm = this;
            vm.$swal.fire({
                icon: 'warning',
                title: vm.$t('bs-cancel'),
                text: `${vm.$t("bs-are-you-sure-you-want-to-cancel-the-ticket")} ${vm.$t("bs-after-this-action-it-will-no-longer-be-pos")}`,
                showCancelButton: true,
                confirmButtonText: vm.$t("bs-yes"),
                cancelButtonText: vm.$t("bs-not")
            })
            .then((result) => {
                if (result.isConfirmed) {
                    var url = `${vm.$store.state.baseURL}/client-status-ticket`;
                    axios.post(url, {
                        id: vm.ticket.id,
                        original_status_ticket: vm.ticket.status,
                        chat_id: vm.ticket.chat_hash_id,
                        status: 2 // CANCELED
                    })
                    .then(({data}) => {
                        if (data.success){
                            vm.$notify({
                                title: vm.$t('bs-the-ticket-has-been-cancelled	'),
                                icon: 'success',
                            })
                        } else {
                            vm.$notify({
                                title: vm.$t('bs-error-cancelling'),
                                icon: 'error',
                            })
                        }
                    })
                    .catch(err => {
                        vm.$notify({
                            title: vm.$t('bs-error-cancelling'),
                            icon: 'error',
                        })
                    })
                }
            })
        },
        checkDaysToReopen() {
            var quant_limity = this.departmentSettings.quant_limity;
            if ('maxdaysreopenticket' in quant_limity) {
                var maxdaysreopenticket = quant_limity.maxdaysreopenticket;
                const date1 = new Date(Date.now());
                const date2 = new Date(this.ticket.update_status_closed_resolved);
                const diffTime = Math.abs(date2 - date1);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                if (maxdaysreopenticket == 0 || maxdaysreopenticket >= diffDays) {
                    this.maxDaysToReopenExceded = false;
                } else {
                    this.maxDaysToReopenExceded = true;
                }
            } else {
                this.maxDaysToReopenExceded = false;
            }
        },
        checkEvaluation(openRateDialog = false) {
            var vm = this;
            var url = `${vm.$store.state.baseURL}/ticket/check-evaluation`;
            axios.get(url, {
                params: {
                    ticket_id: vm.ticket.hash_id,
                }
            })
            .then(({data}) => {
                data.status ? vm.evaluated = true : vm.evaluated = false
                if (!data.status && openRateDialog) {
                    vm.rateDialogShown = true;
                }
            })
        },
        createTicket(status = 'OPENED') {
            var vm = this;
            vm.storingTicket = true;
            var url = `${vm.$store.state.baseURL}/ticket/store`;
            var answers = vm.$store.state.newTicket.answers
            var questions = vm.$store.state.newTicket.questions

            var answers_images = [];
            var index = 0;

            answers.forEach(element => {
                var images = this.$extractImages(element);
                if (images.length) {
                    answers[index] = this.$replaceImageSize(element)
                }
                answers_images.push(images);
                index++
            });

            var company_department = vm.$store.state.newTicket.department.id;

            axios.post(url, {
                answers,
                answers_images,
                company_department,
                questions,
                onlineUsers: vm.onlineUsers,
            })
            .catch(err => {
                console.error(err);
            })
            .finally(() => {
                vm.storingTicket = false;
                if(vm.country == 'BR' || vm.country == 'US'){
                    vm.$router.push({ name: 'ticket'});
                }
            });
        },
        executeAction(status) {
            switch (status) {
                case 'RESET':
                    this.resetNewTicketData();
                    break;

                case 'CANCELED':
                    this.cancelTicket();
                    break;

                case 'RATE-AND-RESOLVE':
                case 'RATE':
                    this.openRateDialog(status);
                    break;

                case 'RESOLVED':
                    var vm = this;
                    vm.$swal.fire({
                        icon: 'warning',
                        title: vm.$t('bs-are-you-sure'),
                        text: `${vm.$t("bs-would-you-like-to-mark-the-ticket-as-resol")} ${vm.$t("bs-after-this-action-it-will-no-longer-be-pos")}`,
                        showCancelButton: true,
                        confirmButtonText: vm.$t("bs-yes"),
                        cancelButtonText: vm.$t("bs-not")
                    })
                    .then((result) => {
                        if (result.isConfirmed) {
                            this.resolveTicket();
                        }
                    })
                    break;
            }
        },
        getActiveTicketsFromCompany() {
            var vm = this;
            return new Promise((resolve) => {
                var url = `${vm.$store.state.baseURL}/ticket/get-active-tickets-from-company`;
                axios.get(url)
                .then(({data}) => {
                    if(data.exceeded) {
                        vm.resetNewTicketData();
                        vm.$swal({
                            icon: 'info',
                            title: vm.$t("bs-caution"),
                            text: vm.$t("bs-active-ticket-limit-exceeded"),
                        })
                    } else {
                        resolve();
                    }
                })
            })
        },
        getChatHistory($state) {
            if (this.ticket.id !== 'create' && !this.$store.state.loadingTicket) {
                var vm = this;
                vm.$store.state.loadingTicket = true;
                var url = `${vm.$store.state.baseURL}/client/get-chat-history`;
                axios.get(url, {
                    params: {
                        id: vm.ticket.chat_id,
                        ticket_id: vm.ticket.id,
                        status: vm.ticket.status,
                        page: vm.messages_page,
                        first: vm.messages[0] ? vm.messages[0].ch_id : null
                    }
                }).then(({data}) => {
                    if (data.success) {
                        if (data.messages.length) {
                            vm.messages_page += 1;
                            vm.messages.unshift(...data.messages.reverse());
                            $state.loaded();
                        } else {
                            $state.complete();
                        }
                    } else {
                        $state.complete();
                    }
                })
                .finally(() => {
                    vm.$store.state.loadingTicket = false;
                })
            } else {
                $state.complete();
            }
        },
        getDepartmentQuests(department_id) {
            return new Promise((resolve, reject) => {
                var vm = this;
                var url = `${vm.$store.state.baseURL}/client-get-quests-department/${department_id}`
                axios.get(url)
                .then(({data}) => {
                    if (data.success) {
                        var questions = data.result;
                        if (data.questionTicket) {
                            questions.unshift({
                                quest: data.questionTicket,
                                type: "TEXT",
                                language: null
                            })
                        } else {
                            questions.unshift({
                                quest: vm.$t('bs-please-enter-a-description-for-the-ticket'),
                                type: "TEXT"
                            })
                        }
                        resolve(questions);
                    }
                });
            })
        },
        getTicketInfo(id) {
            return new Promise((resolve, reject) => {
                var vm = this;
                vm.ticketInfoLoaded = false;
                var url = `${vm.$store.state.baseURL}/client/get-ticket-info`;
                axios.get(url, {
                    params: {
                        id
                    }
                })
                .then(({data}) => {
                    if (data.success) {
                        resolve({ticket: data.ticket, settings: data.settings});
                    } else {
                        reject();
                    }
                })
                .finally(() => {
                    vm.ticketInfoLoaded = true;
                })
            })
        },
        getDepartments(language=navigator.language) {
            var vm = this;
            return new Promise((resolve, reject) => {
                var url = `${vm.$store.state.baseURL}/client-ticket-department?language=${language}`;
                axios.get(url, {
                    params: {
                        country: vm.country
                    }
                })
                .then(({data}) => {
                    if (data.success) {
                        resolve(data.result)
                    } else {
                        reject();
                    }
                })
            });
        },
        joinMessageSentTicketChannel(chat_hash_id) {
            Echo.join(`ticket.${chat_hash_id}`).listen("MessageSentTicket", (event) => {
                var message = {
                    id: event.message.ch_id,
                    company_user_company_department_id: event.message.company_user_company_department_id,
                    type: event.message.type,
                    content: event.message.content,
                    created_at: event.message.created_at,
                    user_id: event.message.id_creator,
                    user_email: event.message.email,
                    user_name: event.message.name,
                    is_ticket_msg: true,
                };
                this.messages.push(message);
            });
        },
        leaveMessageSentTicketChannel(chat_hash_id) {
            Echo.leave(`ticket.${chat_hash_id}`);
        },
        openEmptyTicket() {
            var vm = this;
            vm.ticketInfoLoaded = false;
            vm.getDepartments().then((departments) => {
                if (departments.length) {
                    vm.ticketInfoLoaded = true;
                    vm.$store.state.departments = departments
                    vm.sendDepartmentMessage(); // envio a primeira mensagem
                } else if (navigator.language !== 'en-US'){
                    vm.getDepartments('en-US').then((departments) => {
                        vm.ticketInfoLoaded = true;
                        vm.$store.state.departments = departments
                        vm.sendDepartmentMessage(); // envio a primeira mensagem
                    });
                }
            });
        },
        openTicket(id) {
            var vm = this;
            vm.$store.state.loadingTicket = true;
            vm.messages = [];
            vm.getTicketInfo(id).then((info) => {
                vm.$store.state.ticket = info.ticket;
                vm.departmentSettings = info.settings;
                vm.checkDaysToReopen();
                vm.joinMessageSentTicketChannel(info.ticket.chat_hash_id);
                if (["CLOSED", "RESOLVED"].some(status => status === info.ticket.status)) {
                    vm.checkEvaluation();
                }
                vm.$store.state.loadingTicket = false;
            }).catch((err) => {
                vm.$store.state.loadingTicket = false;
                vm.resetWindow();
                vm.$router.push({ name: 'ticket-opened', params: {'id': 'create'} })
            })
        },
        openRateDialog(action = 'RATE') {
            this.rateFormAction = action;
            this.rateDialogShown = true; // chats/dialogs/rateChatDialog.vue
        },
        rateTicket(data) {
            return new Promise((resolve, reject) => {
                var vm = this;
                var url = `${vm.$store.state.baseURL}/client-avaliation-ticket`;
                axios.post(url, {
                    id: vm.ticket.id,
                    chat_id: vm.ticket.chat_hash_id,
                    atendant: data.stars_atendent,
                    service: data.stars_service,
                    comment: data.evaluationComment,
                })
                .then(({data}) => {
                    if (data.result == 'checked') {
                        vm.$notify({
                            title: vm.$t('bs-this-ticket-has-already-been-rated'),
                            icon: 'info',
                        })
                    } else {
                        vm.$notify({
                            title: vm.$t('bs-thank-you-for-your-review'),
                            icon: 'success',
                        })
                    }
                    resolve();
                })
                .catch(err => {
                    vm.$notify({
                        title: vm.$t('bs-error-submitting-review'),
                        icon: 'error',
                    })
                    reject();
                })
            });
        },
        resetWindow() {
            Object.assign(this.$data, initialState());
        },
        resolveTicket() {
            var vm = this;
            var url = `${vm.$store.state.baseURL}/client-status-ticket`;
            axios.post(url, {
                id: vm.ticket.id,
                original_status_ticket: vm.ticket.status,
                chat_id: vm.ticket.chat_hash_id,
                status: 1 // RESOLVED
            })
            .then(({data}) => {
                if (data.success){
                    vm.$notify({
                        title: vm.$t('bs-ticket-has-been-marked-as-resolved'),
                        icon: 'success',
                    })
                } else {
                    vm.$notify({
                        title: vm.$t('bs-error-finishing'),
                        icon: 'error',
                    })
                }
            })
            .catch(err => {
                vm.$notify({
                    title: vm.$t('bs-error-finishing'),
                    icon: 'error',
                })
            })
        },
        sendAnswer() {
            const content = this.content;
            this.content = "";
            var message = {
                id: 1,
                company_user_company_department_id: null,
                type: 'TEXT',
                content: content,
                created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                user_id: this.$store.state.user.id,
                user_email: this.$store.state.user.email,
                user_name: this.$store.state.user.name,
            };

            this.messages.push(message);
            this.tickets[0].description = message.content;
            this.$store.state.newTicket.answers.push(content);
            this.$store.state.newTicket.chat_history = this.messages;

            if (this.$store.state.newTicket.questions[this.q_idx + 1]) {
                this.$store.state.newTicket.q_idx++;
                this.sendQuestion();
            } else {
                this.createTicket();
            }
        },
        sendDepartmentMessage() {
            this.messages.push(this.$store.state.newChatTicketDepartmentMessage);
            this.tickets[0].description = this.$ct(this.$store.state.newChatTicketDepartmentMessage.content);
            this.$store.state.newTicket.chat_history = this.messages;
        },
        sendMessage() {
            var content = this.content;
            if (this.isCreate) {
                if (content !== '') {
                    this.sendAnswer();
                }
            } else {
                if (content !== '') {
                    var vm = this;
                    vm.content = "";
                    var url = `${vm.$store.state.baseURL}/ticket-history/client/store`

                    var images = vm.$extractImages(content);
                    var files = vm.$store.state.currentEditor.files;
                    var formData = new FormData();

                    if (images.length) {
                        content = vm.$replaceImageSize(content)
                        for (var i = 0; i < images.length; i++) {
                            var image = images[i];
                            formData.append("images[" + i + "]", image);
                        }
                    }

                    for (var i = 0; i < files.length; i++) {
                        var file = files[i];
                        formData.append("files[" + i + "]", file);
                    }

                    formData.append("chat_id", vm.ticket.chat_hash_id);
                    formData.append("content", content);
                    formData.append("cucd_id", vm.ticket.cucd_id);
                    formData.append("department_id", vm.ticket.department_id);
                    formData.append("status", vm.ticket.status);
                    formData.append("ticket_id", vm.ticket.hash_id);

                    axios.post(url, formData, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    })
                    .finally(() => {
                        vm.$store.state.currentEditor.files = [];
                        vm.answerDialogShown = false;
                    })
                } else {
                    this.$notify({
                        title: this.$t('bs-empty-field'),
                        icon: 'warning',
                    })
                }
            }
        },
        sendQuestion() {
            var id = 1;
            var company_user_company_department_id = null;
            var type = 'ROBOT_QUESTION';
            var created_at = moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss');
            if (this.q_idx === 0) {
                this.messages.splice(0, 1);
            }
            var content = this.$store.state.newTicket.questions[this.q_idx].quest;
            this.messages.push({id, company_user_company_department_id, type, content, created_at});
            this.tickets[0].description = content;
            this.$store.state.newTicket.chat_history = this.messages;

        },
        setMessageComponent(message) {
            var component = "";
            // mensagens do chat
            if (this.ticket.chat_type == 'CHANGED_TO_TICKET' && !message.is_ticket_msg) {
                if (message.content == 'bs-the-chat-ended-due-to-inactivity') {
                    message.type = 'ROBOT';
                }
                switch (message.type) {
                    case "TEXT":
                        if (message.company_user_company_department_id !== null) {
                            component = this.chatMessageComponents["TEXT_RECEIVED"];
                        } else {
                            component = this.chatMessageComponents["TEXT_SENT"];
                        }
                        break;

                    case "FILE":
                    case "IMAGE":
                        if (message.company_user_company_department_id !== null) {
                            component = this.chatMessageComponents["FILE_RECEIVED"];
                        } else {
                            component = this.chatMessageComponents["FILE_SENT"];
                        }
                        break;

                    case "ROBOT":
                        if (this.ticket.status == 'ROBOT') {
                            component = this.chatMessageComponents["ROBOT"];
                        }
                        break;

                    case "ROBOT_CREATE":
                    case "ROBOT_QUESTION":
                        component = this.chatMessageComponents["ROBOT"];
                        break;

                    default:
                        component = this.chatMessageComponents[message.type];
                        break;
                }
            }
            // mensagens do ticket;
            else {
                switch (message.type) {
                    case "TEXT":
                        if (this.isCreate) {
                            component = this.chatMessageComponents["TEXT_SENT"];
                        }
                        else if (message.company_user_company_department_id !== null) {
                            component = this.ticketMessageComponents["TEXT_RECEIVED"];
                        } else {
                            if (!message.is_ticket_msg) {
                                component = this.chatMessageComponents["TEXT_SENT"];
                            } else {
                                component = this.ticketMessageComponents["TEXT_SENT"];
                            }
                        }
                        break;

                    case "FILE":
                        if (message.company_user_company_department_id !== null) {
                            component = this.ticketMessageComponents["FILE_RECEIVED"];
                        } else {
                            component = this.ticketMessageComponents["FILE_SENT"];
                        }
                        break;

                    case "IMAGE":
                        if (message.company_user_company_department_id !== null) {
                            component = this.ticketMessageComponents["IMAGE_RECEIVED"];
                        } else {
                            component = this.ticketMessageComponents["IMAGE_SENT"];
                        }
                        break;

                    case "ROBOT":
                        if (this.ticket.status == 'ROBOT') {
                            component = this.ticketMessageComponents["ROBOT"];
                        }
                        break;

                    case "ROBOT_CREATE":
                    case "ROBOT_QUESTION":
                        component = this.ticketMessageComponents["ROBOT"];
                        break;

                    default:
                        component = this.ticketMessageComponents[message.type];
                        break;
                }
            }

            return component;
        },
    },
    beforeDestroy () {
        this.resetTicketData();
        this.departmentSettings = {};
        this.messages = [];
        this.leaveMessageSentTicketChannel(this.ticket.chat_hash_id);
    },
}
</script>
