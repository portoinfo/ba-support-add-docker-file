<template>
    <v-container v-if="!$store.state.isMobile" class="h-100 d-flex flex-column flex-nowrap">
        <v-row no-gutters class="grow overflow-hidden" justify="center" style="position: relative;">
            <v-col style="position: absolute;" class="h-100">
                <v-row no-gutters class="h-100">
                    <v-col class="h-100 pr-1 col-chat-opened-list" v-if="!$store.state.isTablet">
                       <client-chat-list></client-chat-list>
                    </v-col>
                    <v-col class="h-100 pl-1" style="width: 1px;">
                        <v-sheet color="containerBackground" class="py-2" height="100%" rounded="lg">
                            <v-container fluid pa-0	class="d-flex flex-column flex-grow-1 h-100">
                                <v-row no-gutters class="top-row flex-grow-1 flex-shrink-1 pb-12">
                                    <v-col cols="12">
                                        <client-chat-opened-header
                                            :app="false"
                                            :clippedRight="false"
                                            :flat="false"
                                        />
                                    </v-col>
                                    <v-col id="scrollable" cols="12" class="ov-y h-100" v-chat-scroll="{always: false, smooth: true}">
                                        <v-row justify="center" no-gutters>
                                            <v-col xl="10" lg="11">
                                                <v-container v-if="chatInfoLoaded">
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
                                                        :settings="departmentSettings"
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
                                <v-row no-gutters class="flex-grow-0 flex-shrink-0">
                                    <v-col cols="12" style="height: fit-content" class="px-5 pt-6 pb-0">
                                    <span class="ml-2 mt-1" style="opacity: 0.5;" id="texto-digitando" v-if="storingChat && is_faqRobot">{{$t('bs-typing')}}</span>
                                        <queue-chat-position v-if="chat.status == 'OPENED'"></queue-chat-position>
                                        <client-chat-ticket-editor module="chat" v-if="showEditor && !storingChat"/>
                                        <v-skeleton-loader
                                            v-else-if="isCreate"
                                            type="card"
                                            height="90px"
                                            class="rounded-lg"
                                        ></v-skeleton-loader>
                                    </v-col>
                                </v-row>
                            </v-container>
                        </v-sheet>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
    </v-container>
    <client-chat-opened-mobile v-else>
        <template v-slot:header>
            <client-chat-opened-header
                :app="true"
                :clippedRight="true"
                :flat="true"
            />
        </template>
        <template v-slot:main>
            <div v-if="chatInfoLoaded">
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
                    :settings="departmentSettings"
                    :message="ch"
                    :index="index"
                />
            </div>
            <v-row v-else no-gutters>
                <v-col class="text-center py-3">
                    <v-progress-circular indeterminate color="primary"></v-progress-circular>
                </v-col>
            </v-row>
        </template>
        <template v-slot:footer>
            <queue-chat-position v-if="chat.status == 'OPENED'"></queue-chat-position>
            <client-chat-ticket-editor module="chat" v-if="showEditor && !storingChat"/>
            <v-skeleton-loader
                v-else-if="isCreate"
                type="card"
                height="83px"
                class="rounded-lg"
            ></v-skeleton-loader>
        </template>
    </client-chat-opened-mobile>
</template>

<script>
import { title } from "process";
import Q from "q";
import { mapState, mapMutations } from "vuex";
function initialState(){
  return {
    component: {
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
        FAQ_ROBOT: "message-type-faq-robot",
    },
    messages: [],
    messages_page: 1,
    chatInfoLoaded: false,
    isCreate: false,
    departmentSettings: {},
    rateDialogShown: false,
    rateFormAction: 'RATE',
    evaluated: true,
    storingChat: false,
    showchatCustom: false,
    allTitles: [],
    pasteTools: 'Alltools',
    lastShowTool: '',
    is_faqRobot: false,
    saveAllDada: false,
    arrayMsgBot: [],
    sendMessageToCreate: false,
    infos: [],
    hostname: '',
    understandCount: 0,
    showmutipleresults: {
        status: false,
        id: [],
        title: [],
        description: [],
    },
    searchAutomatic: false,
    firstMessage: '',
  }
}

export default {
	data() {
        return initialState();
    },
    computed: {
        isRobot() {
            return this.isCreate || this.chat.cucd_id == null || this.chat.is_robot && this.chat.cucd_id == null
        },
        showEditor() {
            return this.chat.status == 'IN_PROGRESS'
                || this.chat.status == 'OPENED'
                || this.$store.state.newChat.questions.length
                || (this.$store.state.newChatInRobot[this.ncir_idx] && this.$store.state.newChatInRobot[this.ncir_idx].questions.length)
                || this.showchatCustom;
        },
        chats: {
            get() {
                return this.$store.state.chats;
            }
        },
        newChat: {
            get() {
                return this.$store.state.newChat;
            }
        },
        chat: {
            get() {
                return this.$store.state.chat;
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
        country() {
            return this.$store.state.user.language.split("_")[1];
        },
        q_idx: {
            get() {
                return this.$store.state.newChat.q_idx
            }
        },
        q_robot_idx: {
            get() {
                return this.$store.state.newChatInRobot[this.ncir_idx].q_idx
            }
        },
        ncir_idx() {
            let i = this.$store.state.newChatInRobot.findIndex(item => item.chat_id == this.chat.id);
            return i;
        },
        onlineUsers: {
            get() {
                return this.$store.state.online_users;
            }
        },
        onlineAgents() {
            var online_agents = this.onlineUsers;
            online_agents = online_agents.filter((u) => u.is_client !== 1);
            online_agents = online_agents.filter((u) => u.status == 'online');
            return online_agents;
        },
    },
    watch: {
        '$route.params.id': {
            handler: function(newChatId, oldChatId) {
                if (oldChatId) {
                    Object.assign(this.$data, initialState());
                    if (oldChatId !== 'create') {
                        this.leaveChatChannel(oldChatId);
                        this.leaveChatStatusChangerChannel(oldChatId);
                        this.resetChatData();
                        this.departmentSettings = {};
                    }
                }
                if (newChatId == 'create') {
                    this.pushNewCHat();
                    this.isCreate = true;
                    this.chatInfoLoaded = true;
                    if (this.newChat.chat_history.length) {
                        this.messages = this.newChat.chat_history;
                    } else {
                        //VERIFICAR SE FOR CHECKOUT NÃO MOSTAR O FAQ-ROBOT
                        if(!this.$store.state.checkout && !this.$store.state.builderallMentor){
                            this.faqRobot();
                        }else{
                            this.openEmptyChat();
                        }
                    }
                } else if (newChatId == 'popup') {
                    this.openTheLastChatInProgressOrANewChat();
                } else {
                    this.openChat(newChatId);
                }
            },
            deep: true,
            immediate: true
        },
        '$store.state.newChat.department': function (v) {
            if (this.$route.params.id == 'create') {
                if (typeof v === 'object') {
                    var vm = this;
                    vm.chatInfoLoaded = false;
                    vm.$store.state.chats[0].department = v.name
                    vm.$store.state.chat.department_name = v.name;
                    vm.$store.state.chat.department_id = v.id;
                    vm.getActiveChatsFromCompany().then(() => {
                        vm.getActiveChatsFromDepartment(v.id).then(() => {
                            vm.checkIfIsRobot(v.id).then((is_robot) => {
                                if (is_robot) {
                                    if(vm.is_faqRobot){
                                        vm.sendMessageToCreate = true;
                                    }
                                    vm.getRobotMessages(v.id).then((first_message) => {
                                        vm.createChat('ROBOT', first_message ? first_message : null)
                                    });
                                } else {
                                    vm.getDepartmentQuests(v.id).then((questions) => {
                                        let index = questions.findIndex(item => item.active == 1);
                                        if (questions.length && index !== -1) {
                                            var i = 0;
                                            questions.forEach(quest => {
                                                i++;
                                                if (quest.active) {
                                                    if (quest.language == null || vm.country == quest.language) {
                                                        vm.$store.state.newChat.questions.push(quest)
                                                    }
                                                }
                                                if (i == questions.length) {
                                                    vm.chatInfoLoaded = true;
                                                    if (vm.$store.state.newChat.questions.length) {
                                                        vm.sendQuestion();
                                                    } else {
                                                        vm.createChat();
                                                    }
                                                }
                                            });
                                        } else {
                                            vm.createChat();
                                        }
                                    })
                                }
                            });
                        })
                    })
                }
            }
        },
        messages(v) {
            if (v.length) {
                var latest = v[v.length-1].content;
                let index = this.chats.findIndex(item => item.hash_id == this.$route.params.id);
                if (index !== -1) {
                    this.$store.state.chats[index].latest_ch = latest;
                }
            }
        },
        'chat.status': {
            handler: function(status, oldStatus) {
                if (status && status == 'OPENED') {
                    this.getQueuePosition();
                    this.joinQueueUpdatedChannel();
                    if (oldStatus == 'ROBOT') {
                        this.messages = [];
                        this.chatInfoLoaded = false;
                        setTimeout(() => {
                            this.chatInfoLoaded = true;
                        }, 4);
                    }
                } else {
                    this.leaveQueueUpdatedChannel();
                }
            },
            deep: true,
            immediate: true
        },
        'chat.department_id': {
            handler: function(id, oldId) {
                var vm = this;
                if (id && oldId && id !== oldId && vm.chat.status == 'ROBOT') {
                    vm.checkIfIsRobot(id).then((is_robot) => {
                        if (is_robot) {
                            vm.getRobotMessages(id).then((first_message) => {
                                vm.sendRobotsFirstMessage(first_message);
                            });
                        } else {
                            vm.getDepartmentQuests(id).then((questions) => {
                                let index = questions.findIndex(item => item.active == 1);
                                if (questions.length && index !== -1) {
                                    if (vm.ncir_idx === -1) {
                                        vm.$store.state.newChatInRobot.push({
                                            chat_id: vm.chat.id,
                                            questions: [],
                                            q_idx: 0,
                                            answers: [],
                                            chat_history: [],
                                        });
                                        var i = 0;
                                        questions.forEach(quest => {
                                            i++;
                                            if (quest.active) {
                                                if (quest.language == null || vm.country == quest.language) {
                                                    vm.$store.state.newChatInRobot[vm.ncir_idx].questions.push(quest)
                                                }
                                            }
                                            if (i == questions.length) {
                                                vm.$store.state.newChatInRobot[vm.ncir_idx].q_idx = 0;
                                                if(vm.$store.state.newChatInRobot[vm.ncir_idx].questions.length){
                                                    vm.sendQuestion();
                                                }else{
                                                    vm.createChat('ROBOT_TO_OPENED');
                                                }
                                            }
                                        });
                                    } else {
                                        vm.messages = vm.$store.state.newChatInRobot[vm.ncir_idx].chat_history;
                                    }
                                } else {
                                    vm.createChat('ROBOT_TO_OPENED');
                                }
                            })
                        }
                    })
                }
                if (id && oldId && id !== oldId) {
                    vm.getDepartmentSettings(id).then((settings) => {
                        vm.departmentSettings = settings;
                    })
                }
            },
            deep: true,
            immediate: true
        }
    },
    created () {
        this.$root.$refs.ClientChatOpened = this;
        this.hostname = window.location.hostname;
    },
    beforeDestroy () {
        this.resetChatData();
        this.departmentSettings = {};
        this.messages = [];
        this.leaveChatChannel(this.chat.hash_id);
        this.leaveChatStatusChangerChannel(this.chat.hash_id);
        this.leaveQueueUpdatedChannel();
    },
    mounted(){
        const intervalo = setInterval(this.atualizarPontos, 500);
    },
    methods: {
        atualizarPontos() {
            try {
                const textoDigitando = document.getElementById('texto-digitando');
                if (textoDigitando.textContent === this.$t('bs-typing')+'...' || textoDigitando.textContent === this.$t('bs-typing')+'...') {
                    textoDigitando.textContent = this.$t('bs-typing');
                }else {
                    textoDigitando.textContent += '.';
                }
            } catch (error) {
                
            }
        },
        ...mapMutations(["pushNewCHat", "resetChatData", "resetNewChatData"]),
        cancelChat() {
            var vm = this
            var url = `${vm.$store.state.baseURL}/chat/client/cancel`;
            axios.post(url, {
                id: vm.chat.hash_id,
            })
            .then((response) => {
                if (response.data.success) {
                    vm.$notify({
                        title: vm.$t('bs-the-chat-has-been-canceled'),
                        icon: 'success',
                    })
                } else {
                    vm.$notify({
                        title: vm.$t('bs-error-cancelling'),
                        icon: 'error',
                    })
                }
            })
            .catch(() => {
                vm.$notify({
                    title: vm.$t('bs-error-cancelling'),
                    icon: 'error',
                })
            })
        },
        changeChatRobotToTicket() {
            return new Promise((resolve, reject) => {
                var vm = this;
                var url = `${vm.$store.state.baseURL}/chat/client/change-to-ticket`;
                axios.post(url, {
                    company_department: vm.chat.department_id,
                    id: vm.chat.hash_id,
                })
                .then(({data}) => {
                    if (data.success) {
                        resolve(data.ticket_id);
                    } else {
                        reject(data);
                    }
                })
                .catch(err => {
                    reject(err);
                })
            });
        },
        checkEvaluation(openRateDialog = false) {
            var vm = this;
            var url = `${vm.$store.state.baseURL}/chat/check-evaluation`;
            axios.get(url, {
                params: {
                    chat_id: vm.chat.hash_id,
                }
            })
            .then(({data}) => {
                data.status ? vm.evaluated = true : vm.evaluated = false
                if (!data.status && openRateDialog) {
                    if (vm.$root.$refs.ClientChatOpenedHeader.toEvaluate) {
                        vm.rateDialogShown = true;
                    }
                }
            })
        },
        checkIfIsRobot(department_id) {
            return new Promise((resolve, reject) => {
                var url = `${this.$store.state.baseURL}/department/is-robot`;
                axios.get(url, {
                    params: {
                        company_department_id: department_id,
                    }
                })
                .then(({data}) => {
                    resolve(data);
                })
            })
        },
        checkIfTheChatLimitPerClientIsExceeded() {
            return new Promise((resolve, reject) => {
                var vm = this;
                var url = `${vm.$store.state.baseURL}/chat/get-client-active-chats`;
                axios.get(url, {})
                .then(response => {
                    var setting = '';
                    if(JSON.parse(vm.$store.state.setting_chat)){
                        try {
                            setting = JSON.parse(vm.$store.state.setting_chat)[0].chatSimCli;
                        } catch (error) {
                            setting = vm.$store.state.setting_chat;
                        }
                    }else{
                        setting = vm.$store.state.setting_chat;
                    }
                    if (setting == 0 || response.data.client_active_chats <= setting) {
                        resolve();
                    } else {
                        reject(vm.$t("bs-active-chat-limit-exceeded"));
                    }
                })
            })
        },
        countOnlineAgentsByDepartment(online_agents, department_id) {
            return new Promise((resolve, reject) => {
                var vm = this;
                var url = `${vm.$store.state.baseURL}/monitoring/count-online-agents-by-department`;
                axios.get(url, {
                    params: {
                        online_agents,
                        department_id
                    }
                })
                .then(({data}) => {
                    if (data.success) {
                        resolve(data.count)
                    }
                })
            })
        },
        faqRobot(bool = false){
            if(bool){
                //VERSÃO ONDE SÓ MOSTRA O TITLE
                this.getFaqTools().then((tools) => {
                    // if(tools.infos == null || tools.result.length == 0 || !(tools.is_beta_tester || tools.is_master_user)){
                    if(tools.infos == null || tools.result.length == 0){
                        //NÃO CONFIGURADO. - PARA ESSA LINGUAGEM.
                        this.storingChat = false;   
                        this.showchatCustom = false;
                        this.is_faqRobot = false;
                        this.saveAllDada = true;
                        this.openEmptyChat();
                    }else{
                        this.is_faqRobot = true;
                        this.saveAllDada = true;
                        this.showchatCustom = true;
                        
                        setTimeout(() => {
                            tools.result.forEach(element => {
                                this.messages.push({
                                    id: 1,
                                    cucd_id: null,
                                    type: 'FAQ_ROBOT',
                                    content: {
                                        id: element.id,
                                        title: element.title,
                                        description: element.description,
                                        tool_status: element.tool_status,
                                        offline_tool_message: tools.infos.offline_tool_message,
                                        click_title: true,
                                        showDescription: true,
                                    },
                                    created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                                });
                            });   
                        }, 200);

                        setTimeout(() => {
                            if(tools.result3.topNumberTools > 0){
                                this.messages.push({
                                    id: 1,
                                    cucd_id: null,
                                    type: 'FAQ_ROBOT',
                                    content: {
                                        id: 1,
                                        title: this.$t('bs-if-the-above-options-didnt-help-you-can'),
                                        description: null,
                                        tool_status: 1,
                                        click_title: false,
                                        showDescription: false,
                                        not_options: true,
                                        numberTools: tools.result3.topNumberTools,
                                    },
                                    created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                                });
                            }
                        }, 250);
                        setTimeout(() => {
                            const chatElement = document.getElementById('scrollable');
                            chatElement.scrollTop = chatElement.scrollHeight;
                        }, 500);
                    }
                });
            }else{
                //MOSTRA TODO CONTEÚDO INICIAL 
                this.getFaqTools().then((tools) => {
                    // if(tools.infos == null || tools.result.length == 0 || !(tools.is_beta_tester || tools.is_master_user)){
                    if(tools.infos == null || tools.result.length == 0){
                        //NÃO CONFIGURADO. - PARA ESSA LINGUAGEM.
                        this.storingChat = false;
                        this.showchatCustom = false;
                        this.is_faqRobot = false;
                        this.saveAllDada = true;
                        this.openEmptyChat();
                    }else{
                        this.is_faqRobot = true;
                        this.saveAllDada = true;
                        this.showchatCustom = true;

                        setTimeout(() => {
                            this.messages.push({
                                id: 1,
                                cucd_id: null,
                                type: 'FAQ_ROBOT',
                                content: {
                                    title: tools.infos.initial_message,
                                    description: 'off',
                                    click_title: false,
                                    showDescription: true,
                                },
                                created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                            });
                        }, 200);
                        
                        setTimeout(() => {
                            tools.result.forEach(element => {
                                this.messages.push({
                                    id: 1,
                                    cucd_id: null,
                                    type: 'FAQ_ROBOT',
                                    content: {
                                        id: element.id,
                                        title: element.title,
                                        description: element.description,
                                        tool_status: element.tool_status,
                                        offline_tool_message: tools.infos.offline_tool_message,
                                        click_title: true,
                                        showDescription: true,
                                    },
                                    created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                                });
                            });   
                        }, 200);
                    }
                });
            }
        },
        getFaqTools(){
            const url = `${this.$store.state.baseURL}/get-faq-robot-tools`;
            return new Promise((resolve, reject) => {
                axios.get(url, {
                    params: {
                        numberTools: 5,
                        skip: this.$store.state.skipGetFaqRobot,
                    }
                }).then(res => { 
                    if(res.data.finishOptions){
                        setTimeout(() => {
                            this.messages.push({
                                id: 1,
                                cucd_id: null,
                                type: 'FAQ_ROBOT',
                                content: {
                                    id: 1,
                                    title: this.$t('bs-pre-you-can-create-a-chat'),
                                    description: null,
                                    tool_status: 1,
                                    click_title: false,
                                    showDescription: false,
                                    create_chat: true,
                                    // numberTools: tools.result3.topNumberTools,
                                },
                                created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                            }); 
                            vm.showmutipleresults.status = false;
                        }, 150);
                    }else{
                        this.$store.state.skipGetFaqRobot = this.$store.state.skipGetFaqRobot+1;
                        this.allTitles = res.data.allTitles;
                        this.infos = res.data.infos;
                        resolve(res.data);
                    }
                })
                .catch(err => {
                    console.error(err); 
                });
            })
        },
        faqRobotAction(){
            var vm = this;
            const newStr = vm.chats[0].latest_ch.replace(/<\/?[^>]+>/g, "").replace("&nbsp;", "").toLowerCase();
            
            if(newStr == 'mostrar' || newStr == 'show' || newStr == 'ver' || newStr == 'see'){
                return this.messages.push({
                    id: 1,
                    cucd_id: null,
                    type: 'FAQ_ROBOT',
                    content: {
                        id: 1,
                        title: this.$t('bs-if-the-above-options-didnt-help-you-can'),
                        description: null,
                        tool_status: 1,
                        click_title: false,
                        showDescription: false,
                        not_options: true,
                        numberTools: 3,
                    },
                    created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                });
            }


            // // LOGICÁ PARA TROCAR DE FERRAMENTA - DESATIVADA NO MOMENTO
            // if(vm.pasteTools != ''){
            //     var changeaux = false;
            //     if(vm.infos.change_tools_keywords != null){
            //         JSON.parse(vm.infos.change_tools_keywords).forEach((element, index) => {
            //             if(newStr == element){
            //                 changeaux = true;
            //             }
            //         });
            //     }

            //     if(changeaux){
            //         setTimeout(() => {
            //             vm.messages.push({
            //                 id: 1,
            //                 cucd_id: null,
            //                 type: 'FAQ_ROBOT',
            //                 // title: vm.$t('bs-hello-could-you-describe-which-tool-you'),
            //                 content: {
            //                     title: vm.$t('bs-right') +', '+ vm.$t('bs-please-tell-me-which-tool-or-area-you-are'),
            //                     description: 'off',
            //                     click_title: false,
            //                     showDescription: true,
            //                 },
            //                 created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
            //             });
            //             vm.pasteTools = '';
            //             vm.lastShowTool = '';
            //             vm.storingChat = false;       
            //         }, 400);
            //         return;
            //     }
            // }

            //VERIFICAR SE FOI MOSTRADO OPÇÕES PARA O CLIENTE
            if(vm.showmutipleresults.status == true){
                if (isNaN(parseInt(newStr))) {
                    vm.messages.push({
                        id: 1,
                        cucd_id: null,
                        type: 'FAQ_ROBOT',
                        content: {
                            title: vm.$t('bs-enter-only-the-number-of-options'),
                            description: 'off',
                            click_title: false,
                            showDescription: true,
                        },
                        created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                    });
                } else {
                    //VERIFICAR SE O NUMERO DIGITADO EXISTE
                    if(parseInt(newStr) == 0 ){
                        // vm.messages.push({
                        //     id: 1,
                        //     cucd_id: null,
                        //     type: 'FAQ_ROBOT',
                        //     content: {
                        //         title: vm.$t('bs-aright-now-can-you-tell-me-your-problem'),
                        //         description: 'off',
                        //         click_title: false,
                        //         showDescription: true,
                        //     },
                        //     created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                        // });
                        setTimeout(() => {
                            this.messages.push({
                                id: 1,
                                cucd_id: null,
                                type: 'FAQ_ROBOT',
                                content: {
                                    id: 1,
                                    title: this.$t('bs-pre-you-can-create-a-chat'),
                                    description: null,
                                    tool_status: 1,
                                    click_title: false,
                                    showDescription: false,
                                    create_chat: true,
                                    // numberTools: tools.result3.topNumberTools,
                                },
                                created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                            }); 
                            vm.showmutipleresults.status = false;
                        }, 150);
                    }else if(parseInt(newStr) > 0 && vm.showmutipleresults.title.length >= parseInt(newStr)){
                        setTimeout(() => {
                            vm.messages.push({
                                id: 1,
                                cucd_id: null,
                                type: 'FAQ_ROBOT',
                                content: {
                                    title: vm.showmutipleresults.title[parseInt(newStr)-1],
                                    description: vm.showmutipleresults.description[parseInt(newStr)-1],
                                    click_title: false,
                                    showDescription: true,
                                },
                                created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                            });
                        }, 100);
                        setTimeout(() => {
                        vm.messages.push({
                            id: 1,
                            cucd_id: null,
                            type: 'FAQ_ROBOT',
                            content: {
                                id: vm.showmutipleresults.id[parseInt(newStr)-1],
                                title: vm.$t('bs-was-the-above-answer-helpful-to-you-in-any'),
                                click_title: false,
                                showDescription: false,
                                question_final: true,
                            },
                            created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                        });
                        vm.storingChat = false;
                        vm.showmutipleresults.status = false;
                        }, 150);
                    }
                }
                setTimeout(() => {
                    const chatElement = document.getElementById('scrollable');
                    chatElement.scrollTop = chatElement.scrollHeight;
                }, 500);
                return;
            }

            let ToolEncontrada = false;
            let ToolsCombinando = [];

            // ANALISE DE FERRAMENTA - INDENTIFICADOR DE FERRAMENTA
            const minhaPromise = new Promise((resolve, reject) => {
                vm.allTitles.forEach((item, index) => {
                    if (item.title.toLowerCase() === newStr) { 
                        ToolEncontrada = true;
                        ToolsCombinando.push(item.title);
                    } else if (item.title.toLowerCase().includes(vm.removerAcentos(newStr))) {
                        ToolsCombinando.push(item.title);
                    }else if (newStr.includes(item.title.toLowerCase())) { 
                        vm.searchAutomatic = true;
                        vm.firstMessage = newStr;
                        ToolsCombinando.push(item.title);
                    }

                    if (index === vm.allTitles.length - 1) {
                        resolve();
                    }
                });

                if(vm.firstMessage == ''){
                    vm.firstMessage = newStr;
                    vm.searchAutomatic = true;
                }
            });
           
            
           
            minhaPromise.then(() => {
                // const newStr = vm.chats[0].latest_ch.replace(/<\/?[^>]+>/g, "");
                if(vm.pasteTools == ''){
                    if(vm.lastShowTool != ''){
                        if(vm.infos.confirm_keywords != null){
                            JSON.parse(vm.infos.confirm_keywords).forEach((element, index) => {
                                if(newStr == element){
                                    vm.pasteTools = vm.lastShowTool;
                                    ToolEncontrada = true;
                                }
                            });
                        }
                    }

                    if (ToolEncontrada) {
                        vm.showCustomMessageRobot(1, ToolsCombinando);
                    } else if (ToolsCombinando.length > 0) {
                        // console.log("Não encontramos a Tool que você digitou, mas encontramos estas Tools que combinam:");
                        // console.log(ToolsCombinando);
                        vm.showCustomMessageRobot(2, ToolsCombinando);
                    } else {
                        // console.log("Não encontramos nenhuma Tool que combine com o que você digitou.");
                        var aux = false;
                        const checkPromise = new Promise((resolve, reject) => {
                            vm.allTitles.forEach((item, index) => {
                                if(item.keywords != null){
                                    JSON.parse(item.keywords).forEach((value, index2) => {
                                        if (value.toLowerCase() === newStr) {
                                            ToolsCombinando.push(item.title);
                                            aux = true;
                                        }else if (newStr.includes(value.toLowerCase())) { 
                                            vm.searchAutomatic = true;
                                            vm.firstMessage = newStr;
                                            aux = true;
                                            ToolsCombinando.push(item.title);
                                        }
                                        
                                        if (index2 === JSON.parse(item.keywords).length - 1) {
                                            resolve();
                                        }
                                    });
                                }
                                if (index === vm.allTitles.length - 1) {
                                    resolve();
                                }
                            });
                        });
                        
                        checkPromise.then(() => {
                            if(aux){
                                vm.showCustomMessageRobot(2, ToolsCombinando);
                            }else{
                                vm.showCustomMessageRobot(3, ToolsCombinando);
                            }
                        });

                        

                    }

                    
                }else{
                    vm.getFaqRobotTools();
                }
                // --
            });
        }, 
        getFaqRobotTools(){
            var vm = this;
            console.log(vm.pasteTools);
            vm.storingChat = true;
            const newStr = vm.chats[0].latest_ch.replace(/<\/?[^>]+>/g, "").replace("&nbsp;", "").toLowerCase();
            
            var url = `${vm.$store.state.baseURL}/chat/storeRobot`;
            axios.post(url, {
                paste: vm.pasteTools,
                text: vm.searchAutomatic ? vm.firstMessage : newStr,
                hostname: vm.hostname,
            }).then(({data}) => {
                vm.searchAutomatic = false;
                const stepIndex = data.indexOf("step");
                const textAfterStep = data.slice(stepIndex + 5);
                // const textAfterStep = 'CHECKRESULTEXIST11';
                // CHECKRESULTEXIST11
                // CHECKRESULTUNDERSTAND - "Desculpe não entendi o que você quis dizer, vou repassar para um atendente"
                // console.log(textAfterStep);
                if(textAfterStep.includes('CHECKRESULTUNDERSTAND')){
                    vm.understandCount += 1;
                    
                    if(vm.understandCount >= 3){
                        vm.messages.push({
                            id: 1,
                            cucd_id: null,
                            type: 'FAQ_ROBOT',
                            content: {
                                title: vm.$t('bs-sorry-didnt-understand-what-you-mean'),
                                description: 'off',
                                click_title: false,
                                showDescription: true,
                            },
                            created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                        });
                        setTimeout(() => {
                            vm.storingChat = false;   
                            vm.showchatCustom = false;
                            vm.is_faqRobot = true;
                            vm.saveAllDada = true;
                            vm.sendMessageToCreate = true;
                            vm.openEmptyChat();
                        }, 2000);
                    }else{
                        setTimeout(() => {
                            vm.messages.push({
                                id: 1,
                                cucd_id: null,
                                type: 'FAQ_ROBOT',
                                content: {
                                    title: vm.$t('bs-sorry-i-dont-understand-can-you-rephrase'),
                                    description: 'off',
                                    click_title: false,
                                    showDescription: true,
                                },
                                created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                            });
                            vm.storingChat = false;
                        }, 1000);
                    }
                    
                    return; 
                }
                if (textAfterStep.indexOf('CHECKRESULTEXISTAGENT') !== -1) {
                    // console.log('chama atendente');
                    setTimeout(() => {
                            this.messages.push({
                                id: 1,
                                cucd_id: null,
                                type: 'FAQ_ROBOT',
                                content: {
                                    id: 1,
                                    title: null,
                                    description: null,
                                    tool_status: 1,
                                    click_title: false,
                                    showDescription: false,
                                    not_options: true,
                                },
                                created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                            });
                    }, 200);
                    setTimeout(() => {
                        const chatElement = document.getElementById('scrollable');
                        chatElement.scrollTop = chatElement.scrollHeight;
                    }, 500);
                    vm.storingChat = false;
                    return;
                }else if (textAfterStep.indexOf('CHECKRESULTEXIST') !== -1) {
                    // let idTool = textAfterStep.replace('CHECKRESULTEXIST', "");
                    const stringArray = '"'+textAfterStep+'"';
                    const jsonSemQuebraDeLinha = stringArray.replace(/\n/g, ' ');
                    const array = JSON.parse(jsonSemQuebraDeLinha);

                    axios.get(`${vm.$store.state.baseURL}/chat/getRobot`, {
                        params: {
                            array: array
                        }
                    }).then(res => {
                        // console.log(res.data);
                        if(res.data.length == 1){
                            var aux = res.data[0];
                            setTimeout(() => {
                                vm.messages.push({
                                    id: 1,
                                    cucd_id: null,
                                    type: 'FAQ_ROBOT',
                                    content: {
                                        title: aux.title,
                                        description: aux.description,
                                        click_title: false,
                                        showDescription: true,
                                    },
                                    created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                                });
                            }, 100);
                            setTimeout(() => {
                                vm.messages.push({
                                    id: 1,
                                    cucd_id: null,
                                    type: 'FAQ_ROBOT',
                                    content: {
                                        id: aux.id,
                                        title: vm.$t('bs-was-the-above-answer-helpful-to-you-in-any'),
                                        click_title: false,
                                        showDescription: false,
                                        question_final: true,
                                    },
                                    created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                                });
                            }, 200);
                            vm.storingChat = false;
                        }else{
                            var aux = res.data;
                            var titleaux = '';
                            vm.showmutipleresults.status = true;
                            aux.forEach((item, index) => {
                                titleaux = titleaux + '{{'+ (index+1)+' - '+item.title+'}}'+'\n'
                                vm.showmutipleresults.id.push(item.id);
                                vm.showmutipleresults.title.push(item.title);
                                vm.showmutipleresults.description.push(item.description);
                            });
                            titleaux = titleaux + '{{'+ '0 - '+vm.$t('bs-none-of-the-options')+'}}'+'\n';
                            setTimeout(() => {
                                vm.messages.push({
                                    id: 1,
                                    cucd_id: null,
                                    type: 'FAQ_ROBOT',
                                    content: {
                                        title: vm.$t('bs-we-found-some-possible-suggestions-for-you')+':',
                                        description: titleaux,
                                        click_title: false,
                                        showDescription: true,
                                        showMultipleAnswer: true,
                                    },
                                    created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                                });
                            }, 100);
                            vm.storingChat = false;  
                        }
                    });
                } else {
                    // NÃO FAZ NADAD
                    // setTimeout(() => {
                    //     vm.messages.push({
                    //         id: 1,
                    //         cucd_id: null,
                    //         type: 'FAQ_ROBOT',
                    //         content: {
                    //             title: textAfterStep,
                    //             description: 'off',
                    //             click_title: false,
                    //             showDescription: true,
                    //         },
                    //         created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                    //     });
                    // }, 300);
                    vm.storingChat = false;
                }
            }).catch(err => {
                console.error(err);
            }); 
        },
        showCustomMessageRobot(value, ToolsCombinando){
            var vm = this;

            if(value == 1){
                if(vm.searchAutomatic){
                    // console.log(ToolsCombinando);
                    if(vm.lastShowTool == ''){
                        vm.pasteTools = ToolsCombinando[0];
                    }else{
                        vm.pasteTools = vm.lastShowTool;
                    }
                    setTimeout(() => {
                        vm.getFaqRobotTools();
                    }, 200);
                }else{
                    setTimeout(() => {
                        vm.messages.push({
                            id: 1,
                            cucd_id: null,
                            type: 'FAQ_ROBOT',
                            content: {
                                title: vm.$t('bs-aright-now-can-you-tell-me-your-problem'),
                                description: 'off',
                                click_title: false,
                                showDescription: true,
                            },
                            created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                        });
                        vm.storingChat = false;
                        if(vm.lastShowTool == ''){
                            vm.pasteTools = ToolsCombinando[0];
                        }
                    }, 500);
                }
            }else if(value == 2){
                setTimeout(() => {
                    vm.messages.push({
                        id: 1,
                        cucd_id: null,
                        type: 'FAQ_ROBOT',
                        content: {
                            title: vm.$t('bs-did-you-mean')+' '+ ToolsCombinando.join(" "+vm.$t('bs-did-you-mean')+" ") + '?',
                            description: 'off',
                            click_title: false,
                            showDescription: true,
                        },
                        created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                    });
                    if(ToolsCombinando.length == 1){
                        vm.lastShowTool = ToolsCombinando[0];
                    }
                    vm.storingChat = false;
                }, 500);
            }else if(value == 3){
                var vm = this;
                if(!vm.checkRepeat()){
                    if(vm.arrayMsgBot.length >= 3){
                        vm.messages.push({
                            id: 1,
                            cucd_id: null,
                            type: 'FAQ_ROBOT',
                            // title: vm.$t('bs-hello-could-you-describe-which-tool-you'),
                            content: {
                                title: vm.$t('bs-please-tell-me-which-tool-or-area-you-are'),
                                description: 'off',
                                click_title: false,
                                showDescription: true,
                            },
                            created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                        });
                        vm.storingChat = false;       
                    }
                }
            }
        },
        checkRepeat(){
            var vm = this;
            if(vm.arrayMsgBot.length >= 3){
                const array = vm.arrayMsgBot;
                const lastThree = array.slice(-2); // pega os três últimos elementos do array
                const areEqual = lastThree.every((element, index, array) => element === array[0]); // verifica se todos são iguais

                if(areEqual){
                    // console.log("Não encontramos nenhuma Tool que combine com o que você digitou.");
                    setTimeout(() => {
                        vm.messages.push({
                            id: 1,
                            cucd_id: null,
                            type: 'FAQ_ROBOT',
                            content: {
                                title: vm.$t('bs-sorry-cant-help-you'),
                                description: 'off',
                                click_title: false,
                                showDescription: true,
                            },
                            created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                        });
                        vm.storingChat = false;   
                        vm.showchatCustom = false;
                        vm.is_faqRobot = false;
                        vm.saveAllDada = true;
                        vm.sendMessageToCreate = true;
                        vm.openEmptyChat();
                    }, 1500);
                    return true; 
                }
            }
            return false;
        },
        createChat(status = 'OPENED', first_message = null) {
            console.log(status);
            var vm = this;
            vm.storingChat = true;
            if(vm.showchatCustom){
                
            }else{
                vm.checkIfTheChatLimitPerClientIsExceeded().then(() => {
                var url = `${vm.$store.state.baseURL}/chat/store`;
                var is_robot = status == 'ROBOT_TO_OPENED'
                

                    if (is_robot) {
                        if (vm.$store.state.newChatInRobot.length) {
                            var answers = vm.$store.state.newChatInRobot[vm.ncir_idx].answers;
                            var questions = vm.$store.state.newChatInRobot[vm.ncir_idx].questions;
                        } else {
                            var answers = []
                            var questions = []
                        }
                    } else {
                        var answers = vm.$store.state.newChat.answers
                        var questions = vm.$store.state.newChat.questions
                    }
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
                   
                    var company_department = status == 'ROBOT_TO_OPENED' ? { 'id': vm.chat.department_id, 'name': vm.chat.department_name, 'type': '' } : vm.$store.state.newChat.department;
                    var id = status == 'ROBOT_TO_OPENED' ? vm.chat.hash_id : null;

                    
                    for (let i = 0; i <= vm.$store.state.newChat.answers.length*2; i++) {
                        vm.messages.pop();
                    }
  
                    axios.post(url, {
                        answers,
                        answers_images,
                        checkout: false,
                        company_department,
                        first_message,
                        id,
                        onlineUsers: vm.onlineUsers,
                        questions,
                        status,
                        is_faqRobot: vm.saveAllDada || vm.sendMessageToCreate,
                        messages: vm.messages,
                        tz: Intl.DateTimeFormat().resolvedOptions().timeZone,
                    }).then(({data}) => {
                        if(data.close_department){
                            vm.storingChat = false;
                            vm.is_faqRobot = false;
                            vm.saveAllDada = false;
                            vm.resetNewChatData();
                            vm.$swal({
                                icon: 'warning',
                                title: `${vm.$t('bs-oops')}...`,
                                text: vm.$t(data.message),
                            })
                        }
                        // ADICIONA O CAMPO DESCRIÇÃO QUE ESTÁ VAZIO ATÉ O MOMENTO
                        vm.$store.state.chats[1].description = answers[0];
                        
                        // console.log(vm.$store.state.chats);
                        // console.log(answers);
                        // if(this.$store.state.checkout){
                            // location.reload();
                        // }

                    }).catch(err => {
                        console.error(err);
                    })
                    .finally(() => {
                        vm.storingChat = false;
                        if (status == 'ROBOT_TO_OPENED') {
                            vm.$store.state.newChatInRobot.splice(vm.ncir_idx, 1);
                        }
                    });
                }).catch(err => {
                    vm.storingChat = false;
                    vm.resetNewChatData();
                    vm.$swal({
                        icon: 'warning',
                        title: `${vm.$t('bs-oops')}...`,
                        text: err,
                    })
                })
            }
        },
        executeRobotAction(element, inputTime) {
            if (this.chat.status == 'ROBOT') {
                var finished = false;
                // verifico o tipo da opção e executo a ação de acordo com o mesmo.
                switch (element.type) {
                    case 'link':
                        window.open(element.inputLink, "_blank"); // abre o link em uma nova guia
                        break;

                    case 'create_chat':
                        // aqui "crio o chat"
                        // na vdd, o chat já esta criado, apenas altero o status de 'ROBOT' para 'OPENED'
                        // vai para a fila normalmente
                        var vm = this;
                        vm.getActiveChatsFromDepartment(vm.chat.department_id).then(() => {
                            vm.getDepartmentQuests(vm.chat.department_id).then((questions) => {
                                let index = questions.findIndex(item => item.active == 1);
                                if (questions.length && index !== -1) {
                                    if (vm.ncir_idx === -1) {
                                        vm.$store.state.newChatInRobot.push({
                                            chat_id: vm.chat.id,
                                            questions: [],
                                            q_idx: 0,
                                            answers: [],
                                            chat_history: [],
                                        });
                                        var i = 0;
                                        questions.forEach(quest => {
                                            i++;
                                            if (quest.active) {
                                                if (quest.language == null || vm.country == quest.language) {
                                                    vm.$store.state.newChatInRobot[vm.ncir_idx].questions.push(quest)
                                                }
                                            }
                                            if (i == questions.length) {
                                                vm.$store.state.newChatInRobot[vm.ncir_idx].q_idx = 0;
                                                if(vm.$store.state.newChatInRobot[vm.ncir_idx].questions.length){
                                                    vm.sendQuestion();
                                                }else{
                                                    vm.createChat('ROBOT_TO_OPENED');
                                                }
                                            }
                                        });
                                    } else {
                                        vm.messages = vm.$store.state.newChatInRobot[vm.ncir_idx].chat_history;
                                    }
                                } else {
                                    vm.createChat('ROBOT_TO_OPENED');
                                }
                            })
                        })
                        finished = true; // final do fluxo
                        break;

                    case 'create_ticket':
                        // transforma o chat em ticket
                        var vm = this;
                        vm.$swal({
                            title: vm.$t('bs-are-you-sure'),
                            showCancelButton: true,
                            confirmButtonText: vm.$t('bs-create-ticket'),
                            cancelButtonText: vm.$t('bs-cancel'),
                        }).then((result) => {
                            if (result.isConfirmed) {
                                vm.changeChatRobotToTicket().then((ticket_id) => {
                                    vm.$router.push({ name: 'ticket-opened', params: {'id': ticket_id} })
                                }).catch((err) => {
                                    vm.$notify({
                                        title: vm.$t('bs-error'),
                                        icon: 'error',
                                    })
                                });
                            }
                        });

                        finished = true; // final do fluxo
                        break;

                    case 'transfer_department':
                        var vm = this;
                        vm.$swal({
                            title: vm.$t('bs-are-you-sure'),
                            showCancelButton: true,
                            confirmButtonText: vm.$t('bs-transfer'),
                            cancelButtonText: vm.$t('bs-cancel'),
                        }).then((result) => {
                            if (result.isConfirmed) {
                                vm.transferDepartment(element.inputTransferDepartment);
                            }
                        });

                        finished = true; // final do fluxo
                        break;

                    case 'solved':
                        // atualiza o status para 'RESOLVED' (antes abre a modal de avaliação, se caso o departamento tiver)
                        this.actionSidebarOptions({
                            status: 'EVALUATE-CLOSE',
                            message: this.$t("bs-do-you-want-to-rate-this-chat-and-end-it"),
                        });
                        finished = true; // final do fluxo
                        break;

                    case 'unsolved':
                        // verifica se está setada a opção de sugerir criar um chat no final do fluxo
                        // se sim, manda uma mensagem sugerindo abrir um chat
                        // se nao apenas marca como 'CLOSED'
                        this.checkSuggestionCreateChat().then((create) => {
                            if (create) {
                                var vm = this;
                                Swal.fire({
                                    title: this.$t('bs-would-you-like-to-talk-with-one-of-our'),
                                    showCancelButton: true,
                                    confirmButtonText: vm.$t('bs-yes'),
                                    cancelButtonText: vm.$t('bs-cancel'),
                                    heightAuto: false
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        vm.goToQuestionary();
                                    }
                                });
                            } else {
                                this.actionSidebarOptions({
                                    status: 'EVALUATE-CLOSE',
                                    message: this.$t("bs-do-you-want-to-rate-this-chat-and-end-it"),
                                });
                            }
                        })
                        finished = true; // final do fluxo
                        break;

                    default:
                        break;
                }

                // se o fluxo ainda não foi finalizado por uma das opções acima, segue normalmente.
                if (!finished) {

                    var has_time = element.inputTime !== null || inputTime !== null; // verifico se a mensagem tem um time para envio das mensagens filhas;
                    var push = has_time ? 0 : 1; // se tiver, nao da o push na hora
                    var time = has_time ? Number(inputTime !== null ? inputTime : element.inputTime) : 0; // se tiver, seto o time nessa variavel que será enviada para o job PHP, se nao, recebe 0

                    // se a opção possuir opções filhas,  disparo-as
                    if (element.children && element.children.length > 0 && element.type !== 'to_jump') {
                        element.children.forEach(el => {
                            this.sendMessageRobot(el, 0, push, time)
                        });
                    } else {
                        // se não
                        // executa o fim de fluxo padrão nos casos em que não existem mais opções
                        // ou nos casos em que a opção é do tipo pular (to_jump)
                        this.sendMessageRobot(this.robotEndOfFlowMessage, 0, push, time)
                    }
                }
            }
        },
        getActiveChatsFromCompany() {
            var vm = this;
            return new Promise((resolve) => {
                var url = `${vm.$store.state.baseURL}/chat/get-active-chats-from-company`;
                axios.get(url)
                .then(({data}) => {
                    if(data.exceeded) {
                        vm.resetNewChatData();
                        vm.$swal({
                            icon: 'info',
                            title: vm.$t("bs-caution"),
                            text: vm.$t("bs-active-chat-limit-exceeded"),
                        })
                    } else {
                        resolve();
                    }
                })
            })
        },
        getActiveChatsFromDepartment(company_department_id) {
            var vm = this;
            return new Promise((resolve) => {
                var url = `${vm.$store.state.baseURL}/chat/get-active-chats-from-department`;
                axios.get(url, {
                    params: {
                        company_department_id
                    }
                })
                .then(({data}) => {
                    if(data.active_chats_from_department) {
                        vm.resetNewChatData();
                        vm.$swal({
                            icon: 'info',
                            title: vm.$t("bs-caution"),
                            text: vm.$t("bs-existing-chat-department"),
                        })
                    } else {
                        resolve();
                    }
                })
            })
        },
        getChatHistory($state) {
            if (this.chat.id !== 'create' && !this.$store.state.loadingChat) {
                var vm = this;
                vm.$store.state.loadingChat = true;
                var url = `${vm.$store.state.baseURL}/client/get-chat-history`;
                axios.get(url, {
                    params: {
                        id: vm.chat.id,
                        page: vm.messages_page,
                        first: vm.messages[0] ? vm.messages[0].ch_id : null
                    }
                })
                .then(({data}) => {
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
                    vm.$store.state.loadingChat = false;
                })
            } else {
                $state.complete();
            }
        },
        getChatInfo(id) {
            return new Promise((resolve, reject) => {
                var vm = this;
                vm.chatInfoLoaded = false;
                var url = `${vm.$store.state.baseURL}/client/get-chat-info`;
                axios.get(url, {
                    params: {
                        id
                    }
                })
                .then(({data}) => {
                    if (data.success) {
                        resolve(data.chat);
                    } else {
                        reject();
                    }
                })
                .finally(() => {
                    vm.chatInfoLoaded = true;
                })
            })
        },
        getDepartmentQuests(department_id) {
            return new Promise((resolve, reject) => {
                var url = `${this.$store.state.baseURL}/department/get-quests/${department_id}`
                axios.get(url)
                .then(({data}) => {
                    if (data.success) {
                        // console.log(data.result);
                        data.result.unshift({
                            active: 1,
                            id: null,
                            language: null,
                            mandatory: 1,
                            quest: this.$t('bs-please-enter-a-description-for-the-chat'),
                            type: "TEXT",
                        })
                       resolve(data.result);
                    }
                });
            })
        },
        getDepartmentSettings(id) {
            return new Promise((resolve, reject) => {
                var vm = this;
                vm.chatInfoLoaded = false;
                var url = `${vm.$store.state.baseURL}/get-department-settings`
                axios.get(url, {
                    params: {
                        id
                    }
                })
                .then(({data}) => {
                    var settings = JSON.parse(data[0].settings)
                    resolve(settings);
                })
                .finally(() => {
                    vm.chatInfoLoaded = true;
                })
            })
        },
        getOpenDepartments(country) {
            return new Promise((resolve) => {
                var vm = this;
                var url = `${vm.$store.state.baseURL}/client/get-open-departments`
                axios.get(url, {
                    params: {
                        country: country,
                    }
                })
                .then(({data}) => {
                    let index = data.findIndex(item => item.online == 1);
                    if (index !== -1 && data.length) {
                        var i = 0;
                        data.forEach(element => {
                            i++;
                            element.disabled = !element.online;
                            if (i == data.length) {
                                resolve(data);
                            }
                        });
                    } else {
                        // if (country !== 'US') {
                        //     resolve([]);
                        // } else {
                            vm.noOpenDepartmentAlert();
                        // }
                    }
                })
            })
        },
        getQueuePosition() {
            var vm = this;
            var chat_id = this.chat.hash_id;
            var company_department_id = this.chat.department_id;
            var url = `${vm.$store.state.baseURL}/chat/get-queue-position/${chat_id}/${company_department_id}`
            axios.get(url)
            .then(({data}) => {
                vm.$store.state.chatQueuePosition = data.queue_position;
            })
        },
        getRobotMessages(department_id) {
            return new Promise((resolve, reject) => {
                var url = `${this.$store.state.baseURL}/department/get-robot/${department_id}`
                axios.get(url)
                .then(res => {
                    var tree_messages = JSON.parse(res.data.result.quest);
                    var first_message = tree_messages.children[0];
                    resolve(first_message);
                })
            });
        },
        joinChatChannel(chat_hash_id) {
            Echo.join(`chat.${chat_hash_id}`).listen("MessageSent", (event) => {
                if (!event.message.hidden_for_client) {
                    var is_client = event.message.company_user_company_department_id ? false : true;
                    var message = {
                        id: event.message.ch_id,
                        company_user_company_department_id: event.message.company_user_company_department_id,
                        type: event.message.type,
                        content: event.message.content,
                        created_at: event.message.created_at,
                        user_id: is_client ? event.message.client_id : event.message.user_id,
                        user_email: is_client ? event.message.client_email : event.message.user_email,
                        user_name: is_client ? event.message.client_name : event.message.user_name ? event.message.user_name : event.message.name,
                    };
                    this.messages.push(message);
                }
            });
        },
        joinChatStatusChanger(chat_hash_id) {
            Echo.join(`chat-status-changer.${chat_hash_id}`).listen("ChatStatusChanger", (event) => {
                this.$store.state.chat.status = event.item.status;
                let index = this.chats.findIndex(item => item.hash_id == event.item.chat_id);
                if (index !== -1) {
                    this.$store.state.chats[index].status = event.item.status;
                }
                if (["CLOSED", "RESOLVED"].some(status => status === event.item.status)) {
                    this.checkEvaluation(true);
                }
            });
        },
        joinQueueUpdatedChannel() {
            Echo.join(`queue.${this.$store.state.company}`).listen("QueueUpdated", (event) => {
                if (event.item.companyDepartmentId === this.chat.department_id) {
                    if (event.item.action === "splice") {
                        this.getQueuePosition();
                    }
                }
            });
        },
        leaveChatChannel(chat_hash_id) {
            Echo.leave(`chat.${chat_hash_id}`);
        },
        leaveChatStatusChangerChannel(chat_hash_id) {
            Echo.leave(`chat-status-changer.${chat_hash_id}`);
        },
        leaveQueueUpdatedChannel() {
            Echo.leave(`queue.${this.$store.state.company}`);
        },
        newChatActions(departments) {
            var vm = this;
            // setInterval até achar os usuarios online no sistema
            // (no F5, esta função é chamada antes da conexão com o canal online, por isso o setInterval)
            // preciso deles para verificar os agentes online dos departamentos que possuem a config;
            var interval = setInterval(function() {
                if(vm.onlineUsers.length) {
                    clearInterval(interval); // aqui já achou... paro o setInterval e segue o fluxo;
                    var i = 0
                    var depsToCheckAgents = []; // array para armazenar os deps que possuem a config de fechamento automatico.
                    departments.forEach(dep => {
                        i++;
                        if (dep.openChatOnline) {
                            depsToCheckAgents.push(dep.id); // push no array
                        }
                        if (i == departments.length) {
                            if (depsToCheckAgents.length) { // se existir deps com a config
                                if (vm.onlineAgents.length) { //se existem agentes online no momento
                                    var online_agents_ids = [];
                                    var n = 0;
                                    vm.onlineAgents.forEach(agent => {
                                        n++;
                                        online_agents_ids.push(agent.hash_id)
                                        if (n == vm.onlineAgents.length) {
                                            // verifico se o(s) agente(s) é do departamento
                                            var x = 0;
                                            depsToCheckAgents.forEach(dep_id => {
                                                vm.countOnlineAgentsByDepartment(online_agents_ids, dep_id).then((count) => {
                                                    // se nao for, deixo offline
                                                    if (count === 0) {
                                                        let idx = departments.findIndex(item => item.id == dep_id);
                                                        if (idx !== -1) {
                                                            departments[idx].online = false;
                                                            departments[idx].disabled = true;
                                                        }
                                                    }
                                                    x++
                                                    if (x == depsToCheckAgents.length) {
                                                        vm.$store.state.departments = departments
                                                        vm.sendDepartmentMessage(); // envio a primeira mensagem
                                                    }
                                                })
                                            });
                                        }
                                    });
                                } else {
                                    // nenhum agente online
                                    // deixo offline os deps
                                    var n = 0;
                                    depsToCheckAgents.forEach(dep_id => {
                                        n++;
                                        let idx = departments.findIndex(item => item.id == dep_id);
                                        if (idx !== -1) {
                                            departments[idx].online = false;
                                            departments[idx].disabled = true;
                                        }
                                        if (n == depsToCheckAgents.length) {
                                            vm.$store.state.departments = departments
                                            vm.sendDepartmentMessage(); // envio a primeira mensagem
                                        }
                                    });
                                }
                            } else {
                                // se não, os departamentos já estao ok para serem mostrados
                                vm.$store.state.departments = departments
                                vm.sendDepartmentMessage(); // envio a primeira mensagem
                            }
                        }
                    });
                }
            }, 50);
        },
        noOpenDepartmentAlert() {
            this.resetNewChatData();
            this.$swal({
                icon: 'warning',
                title: `${this.$t('bs-oops')}...`,
                text: this.$t('bs-no-departments-open-at-this-time'),
                confirmButtonColor: this.$store.state.user.is_anonymous ? '#3085d6' : '#aaa',
                showDenyButton: this.$store.state.user.is_anonymous ? false : true,
                denyButtonText: this.$t('bs-create-ticket'),
                denyButtonColor: '#3085d6',
            })
            .then((result) => {
                if (result.isDenied) {
                    this.$router.push({ name: 'ticket-opened', params: {'id': 'create'} })
                }
            })
        },
        openEmptyChat() {
            var vm = this;
            vm.chatInfoLoaded = false;
            // primeiro tento pegar os deps do país do cliente;
            // se o país já for 'US' e não tiver resultado, o getOpenDepartments() já mostra o alerta e nao passa no then;

            vm.getOpenDepartments(vm.country).then((departments) => {
                if (departments.length) {
                    // se tiver resultado, segue para a próxima função;
                    vm.chatInfoLoaded = true;
                    vm.newChatActions(departments);
                }
                // se nao achar, tenta pegar os deps dos USA.
                // else if (vm.country !== 'US') {
                //     vm.getOpenDepartments('US').then((departments) => {
                //         vm.chatInfoLoaded = true;
                //         vm.newChatActions(departments)
                //     })
                // }
            })
            
        },
        openChat(id) {
            var vm = this;
            vm.$store.state.loadingChat = true;
            vm.messages = [];
            vm.getChatInfo(id).then((chat) => {
                vm.$store.state.chat = chat;
                vm.getDepartmentSettings(chat.department_id).then((settings) => {
                    vm.departmentSettings = settings;
                })
                vm.joinChatChannel(id);
                vm.joinChatStatusChanger(id);
                if (["CLOSED", "RESOLVED"].some(status => status === chat.status)) {
                    vm.checkEvaluation();
                }
                vm.$store.state.loadingChat = false;
            }).catch(() => {
                vm.$store.state.loadingChat = false;
                vm.resetWindow();
                vm.$router.push({ name: 'chat-opened', params: {'id': 'create'} })
            })
        },
        async openTheLastChatInProgressOrANewChat() {
            const url = `${this.$store.state.baseURL}/client/get-chat-in-progress`;
            const { data } = await axios.get(url);
            if (data.success && data.exists) {
                this.openChat(data.id);
            } else {
                this.$router.push({ name: 'chat-opened', params: {'id': 'create'} })
            }
        },
        openRateDialog(action = 'RATE') {
            this.rateFormAction = action;
            this.rateDialogShown = true; // chats/dialogs/rateChatDialog.vue
        },
        rateChat(data) {
            return new Promise((resolve, reject) => {
                var vm = this;
                var url = `${vm.$store.state.baseURL}/chat/evaluation`;
                axios.post(url, {
                    chat_id: vm.chat.hash_id,
                    stars_atendent: data.stars_atendent,
                    stars_service: data.stars_service,
                    comment: data.evaluationComment,
                })
                .then(({data}) => {
                    if (data.status) {
                        vm.$notify({
                            title: vm.$t('bs-the-chat-has-been-rated'),
                            icon: 'success',
                        })
                        resolve();
                    } else {
                        vm.$notify({
                            title: vm.$t('bs-error-submitting-review'),
                            icon: 'error',
                        })
                        reject();
                    }
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
        resolveChat() {
            var vm = this;
            var url = `${vm.$store.state.baseURL}/chat/client/resolve`;
            axios.post(url, {
                id: vm.chat.hash_id,
            })
            .then((response) => {
                if (response.data.success) {
                    vm.$notify({
                        title: vm.$t('bs-chat-has-been-marked-as-resolved'),
                        icon: 'success',
                    })
                } else {
                    vm.$notify({
                        title: vm.$t('bs-error-when-finishing'),
                        icon: 'error',
                    })
                }
            })
            .catch(() => {
                vm.$notify({
                    title: vm.$t('bs-error-when-finishing'),
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

            var is_robot = this.chat.status == 'ROBOT';

            if (is_robot) {
                let index = this.chats.findIndex(item => item.hash_id == this.chat.hash_id);
                if (index !== -1) this.chats[index].latest_ch = message.content;
                this.$store.state.newChatInRobot[this.ncir_idx].answers.push(content);
                this.$store.state.newChatInRobot[this.ncir_idx].chat_history = this.messages;
            } else {
                this.chats[0].latest_ch = message.content;
                this.$store.state.newChat.answers.push(content);
                this.$store.state.newChat.chat_history = this.messages;
            }


            if (is_robot && this.$store.state.newChatInRobot[this.ncir_idx].questions[this.q_robot_idx + 1]) {
                this.$store.state.newChatInRobot[this.ncir_idx].q_idx++;
                this.sendQuestion();
            } else if (this.$store.state.newChat.questions[this.q_idx + 1]) {
                this.$store.state.newChat.q_idx++;
                this.sendQuestion();
            } else {
                var status = is_robot ? 'ROBOT_TO_OPENED' : 'OPENED'
                this.createChat(status);
            }
        },
        sendDepartmentMessage() {
            this.messages.push(this.$store.state.newChatTicketDepartmentMessage);
            this.chats[0].latest_ch = this.$ct(this.$store.state.newChatTicketDepartmentMessage.content);
            this.$store.state.newChat.chat_history = this.messages;
        },
        sendMessage() {
            var content = this.content;

            if(this.is_faqRobot){
                if(content != ''){
                    setTimeout(() => {
                        const chatElement = document.getElementById('scrollable');
                        chatElement.scrollTop = chatElement.scrollHeight;
                    }, 200);
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
                    this.chats[0].latest_ch = this.content;
                    this.content = "";
                    this.faqRobotAction();
                }
            }else if (this.isCreate || (this.ncir_idx !== -1 && this.$store.state.newChatInRobot[this.ncir_idx].questions.length)) {
                if (content !== '') {
                    this.sendAnswer();
                }
            }else {
                if (content !== '') {
                    var vm = this;
                    vm.content = "";
                    var url = `${vm.$store.state.baseURL}/chat-history/client/store`

                    var images = this.$extractImages(content);
                    if (images.length) {
                        content = this.$replaceImageSize(content)
                    }

                    axios.post(url, {
                        id: vm.chat.hash_id,
                        type: 'TEXT',
                        content,
                        images,
                        company_user_company_department_id: vm.chat.cucd_id,
                        is_client: true,
                        company_department_id: vm.chat.department_id,
                        department: vm.department_name,
                    })
                    .then(({ data }) => {
                        if (data.error) {
                            vm.$store.state.chat.status = data.status;
                        }
                    });
                }
                if (this.$store.state.currentEditor.files.length) {
                    this.sendFiles();
                }
            }
        },
        sendMessageRobot(message, is_bot, push, time) {
            return new Promise((resolve, reject) => {
                var vm = this;
                var url = `${vm.$store.state.baseURL}/chat-history/robot/store`;
                axios.post(url, {
                    id: vm.chat.id,
                    message,
                    is_bot,
                    time
                })
                .then(({data}) => {
                    if(data.success) {
                        if (push) {
                            vm.messages.push(data.message);
                        }
                        resolve();
                    }
                })
                .catch(err => {
                    console.error(err);
                })
            });
        },
        sendFiles() {
            var files = this.$store.state.currentEditor.files;
            var extImages = this.$root.$refs.QuillEditor.extImages;
            var formData = new FormData();

            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                formData.append("files[" + i + "]", file);
            }

            formData.append("chat_id", this.chat.hash_id);
            formData.append("extImages", extImages);
            formData.append("is_client", true);

            var url = `${this.$store.state.baseURL}/chat/client/upload`
            var vm = this;
            axios.post(url, formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            })
            .finally(() => {
                vm.$store.state.currentEditor.files = [];
            })
        },
        sendQuestion() {
            var id = 1;
            var company_user_company_department_id = null;
            var type = 'ROBOT_QUESTION';
            var created_at = moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss');
            if (this.chat.status == 'ROBOT') {
                var content = this.$store.state.newChatInRobot[this.ncir_idx].questions[this.q_robot_idx].quest;
                this.messages.push({id, company_user_company_department_id, type, content, created_at});
                let index = this.chats.findIndex(item => item.hash_id == this.chat.hash_id);
                if (index !== -1) this.chats[index].latest_ch = content;
                this.$store.state.newChatInRobot[this.ncir_idx].chat_history = this.messages;
            } else {
                if (this.q_idx === 0) {
                    this.messages.splice(0, 1);
                }
                var content = this.$store.state.newChat.questions[this.q_idx].quest;
                this.messages.push({id, company_user_company_department_id, type, content, created_at});
                this.chats[0].latest_ch = content;
                this.$store.state.newChat.chat_history = this.messages;
            }
        },
        sendRobotsFirstMessage(first_message) {
            var vm = this;
            var url = `${vm.$store.state.baseURL}/send-robots-first-message`
            axios.post(url, {
                chat_id: vm.chat.hash_id,
                first_message
            })
            .then(({data}) => {
                if (data.success) {
                    vm.messages.push(data.message)
                }
            })
        },
        setMessageComponent(message) {
            if (message.content == 'bs-the-chat-ended-due-to-inactivity') {
                message.type = 'ROBOT';
            }

            var component = "";

            switch (message.type) {
                case "TEXT":
                    if (message.company_user_company_department_id !== null) {
                        component = this.component["TEXT_RECEIVED"];
                    } else {
                        component = this.component["TEXT_SENT"];
                    }
                    break;

                case "FILE":
                case "IMAGE":
                    if (message.company_user_company_department_id !== null) {
                        component = this.component["FILE_RECEIVED"];
                    } else {
                        component = this.component["FILE_SENT"];
                    }
                    break;

                case "ROBOT":
                    if (this.chat.status == 'ROBOT') {
                        component = this.component["ROBOT"];
                    }
                    break;

                case "ROBOT_CREATE":
                case "ROBOT_QUESTION":
                    component = this.component["ROBOT"];
                    break;

                case "FAQ_ROBOT":
                    component = this.component["FAQ_ROBOT"];
                    break;

                default:
                    component = this.component[message.type];
                    break;
            }

            return component;

        },
        transferDepartment(department_id) {
            var vm = this;
            var url = `${this.$store.state.baseURL}/chat/client/transfer`
            axios.post(url, {
                chat_id: vm.chat.hash_id,
                department_id,
            })
        },
        removerAcentos(texto) {
            return texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        }
    },
};
</script>
