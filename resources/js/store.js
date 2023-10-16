import Vue from "vue";
import Vuex from "vuex";
//import Http from "./services/http";
Vue.use(Vuex);

function changeAnswerStatus(state, event) {
    if (event.chat) {
        var e = event.chat;
    } else if (event.item) {
        var e = event.item;
    }
    if (e.action && e.action == 'update') {
        // let i = state.cucd.findIndex(
        //     (item) => item.company_user_company_department_id === e.comp_user_comp_depart_id_current
        // );
        // if (i !== -1) {
        let index2 = state.chatsFooter.findIndex(
            (item) => item.chat_id === e.chat_id
        );
        if (index2 !== -1) {
            state.chatsFooter[index2].comp_user_comp_depart_id_current = e.comp_user_comp_depart_id_current;
            if (e.agent_answered) {
                // se foi o atendente
                state.chatsFooter[index2].answered = 0;
                let old = state.chatsFooter;
                localStorage.setItem("chatsFooter", JSON.stringify(old));
                state.chatsFooter = Array(0);
                state.chatsFooter = old;
            } else {
                // se foi o cliente
                state.chatsFooter[index2].answered = 1;
                let old = state.chatsFooter;
                localStorage.setItem("chatsFooter", JSON.stringify(old));
                state.chatsFooter = Array(0);
                state.chatsFooter = old;
            }
        }
        //}
    }
}

export default new Vuex.Store({
    //data
    state: {
        sidebar_is_mini: false,
        companyselect: "teste",
        languages: [{
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
        online_users: Array(0),
        busy_users: Array(0),
        user_id: "",
        company: "",
        csid: "",
        cucd: Array,
        // TABS
        numberOfTabs: Number(0),
        chatsFooter: Array(0),
        // POPUP CHAT API
        hash_company: "",
        baseURL: "",
        isLogged: Boolean,
        user: Object,
        module: "",
        chat_type: "",
        api_showChat: 0,
        builderallOffice: false,
        client_hash: "",
        icon_hidden: false,
        icon_size: "",
        icon_url: "",
        icon_margin_bottom: "",
        icon_margin_right: "",
        popup_margin_bottom: "",
        popup_margin_right: "",
        popup_height: "",
        popup_width: "",
        // CHAT
        chat_admin: Boolean,
        filter_my_chats: Boolean,
        filter_my_tickets: Boolean,
        filter_not_category: Boolean,
        filter_departments: Array(0),
        filter_departments_ticket: Array(0),
        chats_in_progress: Array(0),
        chats_in_queue: Array(0),
        // ALERT another-agent-took-over-the-chat
        alertAnotherAgenttookOverTheChat: Object,
        // CUSTOMER-SERVICE
        showChat: new URL(location.href).searchParams.get("module") == 'chat',
        showTicket: new URL(location.href).searchParams.get("module") == 'ticket',
        showFilter: new URL(location.href).searchParams.get("module") == 'filter',
        showCategory: new URL(location.href).searchParams.get("module") == 'category',
        pp_options: [
            { text: '15', value: 15 },
            { text: '25', value: 25 },
            { text: '50', value: 50 },
            { text: '100', value: 100 },
            { text: '500', value: 500 }
        ],
        per_page: localStorage.getItem("pp_selected"),
        showAlertCategory: false,
    },

    mutations: {
        // SIDEBAR MINI
        toggleSidebarMini: state => {
            state.sidebar_is_mini = !state.sidebar_is_mini;
            localStorage.setItem("sidebar_is_mini", state.sidebar_is_mini);
            let mainElement = document.getElementById("main");
            if (mainElement) mainElement.classList.toggle("mini");
        },
        // COMPANY NAME
        changenamecompany: (state, payload) => {
            state.companyselect = payload;
        },
        // STATUS (ONLINE, OFFLINE ...)
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
        offline(state) {
            Echo.leave(`status-online.${state.company}`);
            //Echo.leave(`status-busy.${state.company}`);
        },
        // TABS
        tabs(state) {
            Echo.leave(`tabs.${state.company}.${state.user_id}`);
            Echo.join(`tabs.${state.company}.${state.user_id}`).listen(
                "Tabs",
                event => {
                    let chat = event.item;
                    // console.log(chat);
                    if (chat.action == "add") {
                        //adicionar uma tab
                        const storagedChats = JSON.parse(localStorage.getItem("chatsFooter"));
                        let i = 0;
                        let data = [];
                        let exists = false;

                        if (localStorage.getItem("chatsFooter")) {
                            for (let index = 1; index <= Object.keys(storagedChats).length; index++) {
                                data[i] = storagedChats[i];
                                if (storagedChats[i]["chat_id"] === chat.chat_id) {
                                    exists = true;
                                }
                                i++;
                            }
                            if (!exists) {
                                data[i] = chat;
                                localStorage.setItem("chatsFooter", JSON.stringify(data));
                                state.chatsFooter = data;
                            }
                        } else {
                            data[0] = chat;
                            localStorage.setItem("chatsFooter", JSON.stringify(data));
                            state.chatsFooter = data;
                        }

                    } else if (chat.action == "remove") {
                        //remover uma tab
                        let index = state.chatsFooter.findIndex((item) => item.chat_id === chat.chat_id);
                        if (index !== -1) {
                            state.chatsFooter.splice(index, 1);
                            //let old = state.chatsFooter;
                            localStorage.setItem("chatsFooter", JSON.stringify(state.chatsFooter));
                            //state.chatsFooter = Array(0);
                        }
                        //state.chatsFooter = JSON.parse(localStorage.getItem("chatsFooter"));
                    }
                }
            );
            if (window.location.pathname != '/chat' && window.location.pathname != '/full-chat' && !window.location.search == "?module=chat") {
                Echo.leave(`in-progress.${state.company}.${state.user_id}`);
                Echo.join(`in-progress.${state.company}.${state.user_id}`).listen("InProgressUpdated", (event) => {
                    changeAnswerStatus(state, event);
                });

                Echo.leave(`full-chat.progress.${state.company}`);
                Echo.join(`full-chat.progress.${state.company}`).listen("FullChatProgress", (event) => {
                    changeAnswerStatus(state, event);
                });
            }
        },
        // MY CHATS FILTER
        updateMyChatsFilter(state, value) {
            state.filter_my_chats = value;
        },
        // MY TICKETS FILTER
        updateMyTicketsFilter(state, value) {
            state.filter_my_tickets = value;
            localStorage.setItem('filter_my_tickets', JSON.stringify(value));
        },
        // MY TICKETS NOT CATEGORY
        updateFilterNotCategory(state, value) {
            state.filter_not_category = value;
            localStorage.setItem('filter_not_category', JSON.stringify(value));
        },
        // DEPARTMENT CHATS FILTER
        updateChatFilterDepartments(state, value) {
            state.filter_departments = value;
        },
        updateChatFilterDepartmentsticket(state, value) {
            state.filter_departments_ticket = value;
        },
        // CHATS IN_PROGRESS
        getChatsInProgress(state, url_prefix) {
            if (state.filter_departments && state.filter_departments.length > 0) {
                let departments = [];
                state.filter_departments.forEach(element => {
                    departments.push({ id: element.id });
                });
                const api = `${url_prefix}/get-chats-in-progress`;
                axios
                    .get(api, {
                        params: {
                            my_chats: state.filter_my_chats ? 1 : 0,
                            departments: departments,
                            filter_not_category: state.filter_not_category,
                        },
                    })
                    .then(({ data }) => {
                        if (data && data.chats) state.chats_in_progress = data.chats;
                    });
            }
        },
        spliceChatsInProgress(state, index) {
            state.chats_in_progress.splice(index, 1);
        },
        pushChatsInProgress(state, item) {
            state.chats_in_progress.push(item);
        },
        setAnwserChatsInProgress(state, request) {
            state.chats_in_progress[request['index']].answered = request['value'];
        },
        // CHATS IN_QUEUE
        getChatsOnQueue(state, url_prefix) {
            if (state.filter_departments && state.filter_departments.length > 0) {
                let departments = [];
                state.filter_departments.forEach(element => {
                    departments.push({ id: element.id });
                });
                const api = `${url_prefix}/get-chats-on-queue`;
                axios
                    .get(api, {
                        params: {
                            departments: departments,
                            filter_not_category: state.filter_not_category,
                        }
                    })
                    .then(({ data }) => {
                        if (data && data.chats) state.chats_in_queue = data.chats;
                    });
            }
        },
        //CUSTOMER-SERVIVE
        openChats(state) {
            state.showChat = true;
            state.showTicket = false;
            state.showFilter = false;
            state.showCategory = false;
        },
        openTickets(state) {
            state.showChat = false;
            state.showTicket = true;
            state.showFilter = false;
            state.showCategory = false;
        },
        openFilters(state) {
            state.showChat = false;
            state.showTicket = false;
            state.showFilter = true;
            state.showCategory = false;
        },
        openCategory(state) {
            state.showChat = false;
            state.showTicket = false;
            state.showFilter = false;
            state.showCategory = true;
        },
        apiShowChat(state, request) {
            state.api_showChat = request;
        },
        setPerPage(state, value) {
            localStorage.setItem('pp_selected', value);
            state.per_page = value;
        }
    },
    //computed
    getters: {
        getPerPage(state) {
            if (state.per_page == null) {
                var valDefault = 25;
                localStorage.setItem('pp_selected', valDefault);
                return valDefault;
            } else {
                return state.per_page;
            }
        }
    },

    //methods
    actions: {
        initStates(context) {
            context.dispatch("initSidebarState");
        },
        initSidebarState({ commit }) {
            if (localStorage.getItem("sidebar_is_mini") == "true")
                commit("toggleSidebarMini");
        }
    }
});