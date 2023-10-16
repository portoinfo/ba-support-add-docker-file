import Vue from "vue";
import goTo from "vuetify/lib/services/goto";
import Vuex from "vuex";
import { router } from "../../../client-app.js";
import { i18n } from "../../../client-app.js"; // i18n.t == this.$t
import { notify } from '../util/notify'
import { stripHtml } from '../util/strip-html'
import ClientTicketOpened from '../tickets/ClientTicketOpened.vue'; // Substitua o caminho pelo caminho correto para o seu componente

Vue.use(Vuex);

export default new Vuex.Store({
    components: {
        ClientTicketOpened,
    },
    state: {
        /** @GERAL */
        drawer: false,
        font_size: '',
        languages: [
            {
                "key": "pt_BR",
                "desc": "Português (Brasil)"
            },
            {
                "key": "en_US",
                "desc": "English"
            },
            {
                "key": "fr_FR",
                "desc": "Français"
            },
            {
                "key": "de_DE",
                "desc": "Deutsch"
            },
            {
                "key": "es_ES",
                "desc": "Español"
            },
            {
                "key": "it_IT",
                "desc": "Italiano"
            },
            {
                "key": "pt_PT",
                "desc": "Português"
            },
            {
                "key": "he_IL",
                "desc": "עברית"
            },
            {
                "key": "hu_HU",
                "desc": "Magyarország"
            },
            {
                "key": "pl_PL",
                "desc": "Polskie"
            },
            {
                "key": "cz_CZ",
                "desc": "Český"
            },
            {
                "key": "es_CO",
                "desc": "Spanish - COLOMBIA"
            }
        ],
        showChatModule: true,
        showTicketModule: true,
        iOS: false,
        baseURL: "",
        isPopup: "",
        isOpened: true,
        company: "",
        user_client_id: "",
        csname: "",
        csid: "",
        online_users: Array(0),
        overlay: false,
        user: null,
        newChatTicketDepartmentMessage: {
            id: 1,
            cucd_id: null,
            type: 'ROBOT_CREATE',
            content: 'bs-select-a-department',
            created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
            showDeptSelect: true
        },
        isMobile: false,
        isTablet: false,
        isMedium: false,
        innerHeight: '100%',
        visibilityState: true,
        allDepartments: [], //todos
        currentEditor: {
            content: "",
            files: []
        },
        ticket_limit: 0,
        chat_limit: 0,
        email_prefix: "",
        checkout: false,
        builderallMentor: false,
        isOfficePopup: false,

        /** @CHATS */
        departments: [], //somente os abertos
        chats: [
            {
                cucd_id: null,
                attendant_name: null,
                latest_ch: "",
                id: 'create',
                description: '',
                status: 'ROBOT',
                visible: false,
                date: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss')
            }
        ],
        newChat: {
            department: null,
            questions: [],
            q_idx: 0,
            answers: [],
            chat_history: [],
        },
        newChatInRobot: Array(0),
        chat: {
            attendant_email: "",
            attendant_id: "",
            attendant_name: "",
            created_at: "",
            cucd_id: "",
            department_id: "",
            department_name: "",
            id: "",
            is_robot: "",
            hash_id: "",
            status: "",
        },
        chatTextFilterContent: "",
        chatFilterDepartment: [],
        chatFilterStatus: [],
        chatQueuePosition: 0,
        homeChatsPage: 1,
        overlayCHats: false,
        loadingChat: false,
        emptyChats: false,
        setting_chat: '',

        /** @TICKETS */
        overlayTickets: false,
        homeTicketsPage: 1,
        ticketTextFilterContent: "",
        ticketFilterDepartment: [],
        ticketFilterStatus: [],
        tickets: [
            {
                cucd_id: null,
                attendant_name: null,
                latest_ch: "",
                id: 'create',
                status: 'ROBOT',
                visible: false,
                date: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss')
            }
        ],
        emptyTickets: false,
        loadingTicket: false,
        ticket: {
            attendant_email: "",
            attendant_id: "",
            attendant_name: "",
            created_at: "",
            cucd_id: "",
            department_id: "",
            department_name: "",
            description: "",
            id: "",
            hash_id: "",
            hash_chat_id: "",
            chat_id: "",
            chat_type: "",
            status: "",
        },
        newTicket: {
            department: null,
            questions: [],
            q_idx: 0,
            answers: [],
            chat_history: [],
        },
        name_robot: '',
        faqTools: [],
        skipGetFaqRobot: 1,
        skipGetFaqRobotID: 1,
    },
    mutations: {
        /** @GERAL */
        loading(state, payload) {
            state.overlay = payload
        },
        online(state) {
            var users = Array(0);
            Echo.leave(`status-online.${state.company}`);
            Echo.join(`status-online.${state.company}`)
            .here(user => {
                users = user;
                state.online_users = users;
            })
            .joining(user => {
                users.push(user);
                state.online_users = users;
            })
            .leaving(user => {
                users = users.filter(u => u.id != user.id);
                state.online_users = users;
            });
        },
        setFontSize(state, size) {
            state.font_size = size;
            document.getElementById('root').style.fontSize = size;
            if (!state.user.config || 'fontSize' in JSON.parse(state.user.config) && size != JSON.parse(state.user.config).fontSize) {
                var url = `${state.baseURL}/set-user-font-size`;
                axios
                .post(url,{size})
                .then(({data}) => {
                    if (data.success) {
                        state.user.config = data.config;
                    }
                })
            }
        },

        /** @CHATS */
        clearChats(state) {
            state.chats = [
                {
                    cucd_id: null,
                    attendant_name: null,
                    latest_ch: "",
                    id: 'create',
                    status: 'ROBOT',
                    visible: false,
                    date: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss')
                }
            ]
        },
        getClientChats(state, $state) {
            var url = `${state.baseURL}/client/get-chats`;
            state.overlayCHats = true;
            axios.get(url, {
                params: {
                    page: state.homeChatsPage,
                    content: state.chatTextFilterContent,
                    departments: state.chatFilterDepartment,
                    status: state.chatFilterStatus,
                }
            })
            .then(({data}) => {
                if (data.success) {
                    if (data.chats.length) {
                        state.homeChatsPage += 1;
                        state.chats.push(...data.chats);
                        if ($state) {
                            $state.loaded();
                        }
                        state.emptyChats = false;
                    } else {
                        state.emptyChats = true;
                        if ($state) {
                            $state.complete();
                        }
                    }
                }
            })
            .finally(() => {
                state.overlayCHats = false;
            })
        },
        joinClientQueueStatus(state) {
            Echo.join(`client-queue.${state.company}.${state.user_client_id}`).listen("ClientQueueStatus", (event) => {
                let index = state.chats.findIndex(
                    (item) => item.hash_id === event.item.chat_id
                );
                if (index !== -1) {
                    var chat_opened = router.currentRoute.name == 'chat-opened' && router.currentRoute.params.id == event.item.chat_id;
                    var chat = state.chats[index];
                    if ('action' in event.item) {
                        if (event.item.action == 'splice') {
                            state.chats.splice(index, 1);
                            if (chat_opened) {
                                if (!'turned_into_ticket' in event.item) {
                                    if (state.chats.length > 1 && !state.isTablet) {
                                        router.push({ name: 'chat-opened', params: {'id': state.chats[1].hash_id} })
                                    }  else {
                                        router.push({ name: 'chat' })
                                    }
                                }
                            }
                        }
                    }
                    if ('agent' in event.item) {
                        state.chats[index].attendant_name = event.item.agent;
                        if (chat_opened) {
                            state.chat.attendant_name = event.item.agent;
                        }
                    }
                    if ('agent_answered' in event.item) {
                        state.chats[index].agent_answered = event.item.agent_answered;
                    }
                    if ('agent_email' in event.item) {
                        state.chats[index].attendant_email = event.item.agent_email;
                        if (chat_opened) {
                            state.chat.attendant_email = event.item.agent_email;
                        }
                    }
                    if ('agent_id' in event.item) {
                        state.chats[index].attendant_id = event.item.agent_id;
                        if (chat_opened) {
                            state.chat.attendant_id = event.item.agent_id;
                        }
                    }
                    if ('comp_user_comp_depart_id_current' in event.item) {
                        state.chats[index].cucd_id = event.item.comp_user_comp_depart_id_current;
                        if (chat_opened) {
                            state.chat.cucd_id = event.item.comp_user_comp_depart_id_current;
                        }
                    }
                    if ('department_name' in event.item) {
                        state.chats[index].department = event.item.department_name;
                        if (chat_opened) {
                            state.chat.department_name = event.item.department_name;
                        }
                    }
                    if ('department_id' in event.item) {
                        if (chat_opened) {
                            state.chat.department_id = event.item.department_id;
                        }
                    }
                    if ('status' in event.item) {
                        state.chats[index].status = event.item.status;
                        if (state.chatFilterStatus.length) {
                            var found = state.chatFilterStatus.find(element => element == event.item.status);
                            if (!found) {
                                if (chat_opened) {
                                    router.push({ name: 'chat' })
                                }
                                state.chats.splice(index, 1);
                            }
                        }
                        switch (event.item.status) {
                            case 'OPENED':
                                state.chats[index].attendant_name = null;
                                state.chats[index].attendant_email = null;
                                state.chats[index].attendant_id = null;
                                state.chats[index].cucd_id = null;
                                break;

                            case 'CANCELED':
                                state.chats.splice(index, 1);
                                if (chat_opened) {
                                    if (state.chats.length > 1 && !state.isTablet) {
                                        router.push({ name: 'chat-opened', params: {'id': state.chats[1].hash_id} })
                                    }  else {
                                        router.push({ name: 'chat' })
                                    }
                                }
                                break;
                        }

                    }
                    if ('turned_into_ticket' in event.item) {
                        notify({
                            icon: 'chat',
                            titleText: `#${chat.id}`,
                            text: i18n.t('bs-the-chat-was-turned-into-ticket')
                        });
                    }
                    if ('content' in event.item) {
                        if(state.chats[index]) state.chats[index].latest_ch = event.item.content;
                        if (!state.visibilityState || !chat_opened && event.item.agent_answered) {
                            var hasImg = event.item.content ? event.item.content.search("<img") : -1;
                            if(hasImg !== -1) {
                                var content = i18n.t('bs-image');
                            }  else {
                                var content = i18n.t(stripHtml(event.item.content));
                            }
                            var attendant = 'sent_by' in event.item && event.item.sent_by !== null ? event.item.sent_by : i18n.t('bs-robot');
                            notify({
                                icon: 'chat',
                                titleText: `${i18n.t('bs-chat')} #${chat.id}`,
                                html: `<b>${attendant}:</b></br>${content.substr(0, 100)}${content.length > 100 ? '...' : ''}`,
                                sound: true,
                                soundName: chat.status == 'IN_PROGRESS' ? 'add' : 'remove',
                                goToChat: true,
                                chatId: chat.hash_id,
                                timer: '8000'
                            });
                        }
                    }
                } else if ('action' in event.item && event.item.action == 'push') {
                    this.commit("resetNewChatData");
                    // if (router.currentRoute.name == 'chat-opened') {
                        router.push({ name: 'chat-opened', params: {'id': event.item.chat_id} })
                    // }
                    state.chats.splice(1, 0, {
                        agent_answered: 0,
                        attendant_email: null,
                        attendant_id: null,
                        attendant_name: null,
                        cucd_id: null,
                        date: event.item.created_at,
                        date_latest_ch: event.item.created_at,
                        department: event.item.department,
                        hash_id: event.item.chat_id,
                        id: event.item.number,
                        latest_ch: "",
                        queue_time: 0,
                        status: event.item.status,
                        visible: true,
                    })
                }
            });
        },
        pushNewCHat(state) {
            state.chats[0].visible = true;
        },
        resetNewChatData(state) {
            state.chats[0].visible = false;
            state.newChat = {
                department: null,
                questions: [],
                q_idx: 0,
                answers: [],
                chat_history: [],
            };
            if (router.currentRoute.name == 'chat-opened') {
                if (state.chats.length > 1) {
                    router.push({ name: 'chat-opened', params: {'id': state.chats[1].hash_id} })
                }  else {
                    router.push({ name: 'chat' })
                }
            }
        },
        resetChatData(state) {
            state.chat = {
                attendant_email: "",
                attendant_id: "",
                attendant_name: "",
                created_at: "",
                cucd_id: "",
                department_id: "",
                department_name: "",
                id: "",
                is_robot: "",
                hash_id: "",
                status: "",
            }
            state.currentEditor = {
                content: "",
                files: [],
            }
        },

        /** @TICKETS */
        clearTickets(state) {
            state.tickets = [
                {
                    cucd_id: null,
                    attendant_name: null,
                    latest_ch: "",
                    id: 'create',
                    status: 'ROBOT',
                    visible: false,
                    date: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss')
                }
            ]
        },
        getClientTickets(state, $state) {
            var url = `${state.baseURL}/client/get-tickets`;
            state.overlayTickets = true;
            axios.get(url, {
                params: {
                    page: state.homeTicketsPage,
                    content: state.ticketTextFilterContent,
                    departments: state.ticketFilterDepartment,
                    status: state.ticketFilterStatus,
                }
            })
            .then(({data}) => {
                if (data.success) {
                    if (data.tickets.length) {
                        state.homeTicketsPage += 1;
                        state.tickets.push(...data.tickets);
                        if ($state) {
                            $state.loaded();
                        }
                        state.emptyTickets = false;
                    } else {
                        state.emptyTickets = true;
                        if ($state) {
                            $state.complete();
                        }
                    }
                }
            })
            .finally(() => {
                state.overlayTickets = false;
            })
        },
        joinClientTicketsList(state) {
            Echo.join(`client-tickets-list.${state.company}.${state.user_client_id}`).listen("ClientTicketsList", (event) => {
                let index = state.tickets.findIndex(
                    (item) => item.id === event.ticket.id
                );
                if (index !== -1) {
                    var ticket_opened = router.currentRoute.name == 'ticket-opened' && router.currentRoute.params.id == event.ticket.hash_id;
                    if ('action' in event.ticket) {
                        if (event.ticket.action == 'splice') {
                            state.tickets.splice(index, 1);
                            router.push({ name: 'ticket' })
                        }
                    }
                    if ('cucd_id' in event.ticket) {
                        state.tickets[index].cucd_id = event.ticket.cucd_id;
                        if (ticket_opened) {
                            state.ticket.cucd_id = event.ticket.cucd_id;
                        }
                    }
                    if ('agent' in event.ticket) {
                        state.tickets[index].attendant_name = event.ticket.agent;
                        if (ticket_opened) {
                            state.ticket.attendant_name = event.ticket.agent;
                        }
                    }
                    if ('agent_answered' in event.ticket) {
                        state.tickets[index].agent_answered = event.ticket.agent_answered;
                    }
                    if ('agent_email' in event.ticket) {
                        state.tickets[index].attendant_email = event.ticket.agent_email;
                        if (ticket_opened) {
                            state.ticket.attendant_email = event.ticket.agent_email;
                        }
                    }
                    if ('agent_id' in event.ticket) {
                        state.tickets[index].attendant_id = event.ticket.agent_id;
                        if (ticket_opened) {
                            state.ticket.attendant_id = event.ticket.agent_id;
                        }
                    }
                    if ('department_name' in event.ticket) {
                        state.tickets[index].department = event.ticket.department_name;
                        if (ticket_opened) {
                            state.ticket.department_name = event.ticket.department_name;
                        }
                    }
                    if ('department_id' in event.ticket) {
                        if (ticket_opened) {
                            state.ticket.department_id = event.ticket.department_id;
                        }
                    }
                    if ('status' in event.ticket) {
                        state.tickets[index].status = event.ticket.status;
                        this.$root.$refs.ClientTicketOpened.buttonSendMessageText();
                        if (state.ticketFilterStatus.length) {
                            var found = state.ticketFilterStatus.find(element => element == event.ticket.status);
                            if (!found) {
                                if (ticket_opened) {
                                    router.push({ name: 'ticket' })
                                }
                                state.ticket.splice(index, 1);
                            }
                        }

                        if (ticket_opened) {
                            state.ticket.status = event.ticket.status
                        }

                        switch (event.ticket.status) {
                            case 'OPENED':
                                state.tickets[index].attendant_name = null;
                                state.tickets[index].attendant_email = null;
                                state.tickets[index].attendant_id = null;
                                state.tickets[index].cucd_id = null;
                                break;

                            case 'CANCELED':
                                state.tickets.splice(index, 1);
                                if (ticket_opened) {
                                    if (state.tickets.length > 1 && !state.isTablet) {
                                        router.push({ name: 'ticket-opened', params: {'id': state.tickets[1].hash_id} })
                                    }  else {
                                        router.push({ name: 'ticket' })
                                    }
                                }
                                break;
                        }

                    }
                } else if ('action' in event.ticket && event.ticket.action == 'push') {
                    this.commit("resetNewTicketData");
                    if (router.currentRoute.name == 'ticket-opened') {
                        router.push({ name: 'ticket-opened', params: {'id': event.ticket.hash_id} });
                    }
                    state.tickets.splice(1, 0, {
                        agent_answered: 'cucd_id' in event.ticket ? 1 : 0,
                        attendant_email: 'attendant_email' in event.ticket ? event.ticket.attendant_email : null,
                        attendant_id: 'attendant_id' in event.ticket ? event.ticket.attendant_id : null,
                        attendant_name: 'attendant_name' in event.ticket ? event.ticket.attendant_name : null,
                        chat_id: event.ticket.chat_id,
                        cucd_id: 'cucd_id' in event.ticket ? event.ticket.cucd_id : null,
                        date: event.ticket.created_at,
                        date_latest_ch: event.ticket.created_at,
                        department: event.ticket.department,
                        description: event.ticket.description,
                        hash_id: event.ticket.hash_id,
                        id: event.ticket.ticket_id,
                        status: event.ticket.status,
                        visible: true,
                    })
                }
            });
        },
        pushNewTicket(state) {
            state.tickets[0].visible = true;
        },
        resetTicketData(state) {
            state.ticket = {
                attendant_email: "",
                attendant_id: "",
                attendant_name: "",
                created_at: "",
                cucd_id: "",
                department_id: "",
                department_name: "",
                id: "",
                is_robot: "",
                hash_id: "",
                status: "",
            }
            state.currentEditor = {
                content: "",
                files: [],
            }
        },
        resetNewTicketData(state) {
            state.tickets[0].visible = false;
            state.newTicket = {
                department: null,
                questions: [],
                q_idx: 0,
                answers: [],
                chat_history: [],
            };
            if (router.currentRoute.name == 'ticket-opened') {
                if (state.tickets.length > 1) {
                    router.push({ name: 'ticket-opened', params: {'id': state.tickets[1].hash_id} })
                }  else {
                    router.push({ name: 'ticket' })
                }
            }
        },
    }
})
