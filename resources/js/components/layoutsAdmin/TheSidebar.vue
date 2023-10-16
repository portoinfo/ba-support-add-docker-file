<template>
    <div>
        <!-- SIDEBAR DESKTOP -->
        <div :class="['bb-sidebar d-none flex-column justify-content-between', !sidebar_is_mini ? 'bb-mini' : '', !customer_service ? 'd-sm-flex' : 'd-lg-flex']">

            <template v-if="company != 'null'">
                <b-nav class="overflowsidebar" vertical>
                    <!-- MENU HOME -->
                    <template v-for="(menu, k) in menuHome">
                        <b-nav-item
                            :key="k+100"
                            @click="goToPage(menu.href)"
                            :active="checkActiveLink('home')"
                            :id="`sidebar-menu-home${k+101}`"
                        >
                            <span class="material-icons-two-tone">{{menu.icon}}</span>
                            <span class="title">{{ menu.title }}</span>
                        </b-nav-item>
                    </template>


                    <b-nav-item id="sidebar-menu-service" v-b-toggle.collapse-a :class="{'active': (collapseAOpened || checkActiveLink(managementRoutes)) && !collapseBOpened}">
                        <span class="material-icons-two-tone">headset_mic</span>
                        <span class="title">{{$t('bs-service')}}</span>
                        <span class="w-100 text-right">
                            <i v-if="collapseAOpened" class="fa fa-chevron-up mr-1" :class="{'arrow-mini': !sidebar_is_mini}" aria-hidden="true"></i>
                            <i v-if="!collapseAOpened" class="fa fa-chevron-down mr-1" :class="{'arrow-mini': !sidebar_is_mini}"  aria-hidden="true"></i>
                        </span>
                    </b-nav-item>

                    <b-collapse id="collapse-a">
                        <!-- MENU DE ATENDIMENTO -->
                        <template v-for="(menu, key2) in menuManagement">
                            <b-nav-item
                                :key="key2+202"
                                @click="goToPage(menu.href)"
                                :active="checkActiveLink(menu.routes)"
                                :id="`sidebar-menu-management${key2+202}`"
                            >
                                <span class="material-icons-two-tone">{{menu.icon}}</span>
                                <span class="title">{{ menu.title }}</span>
                            </b-nav-item>
                        </template>
                    </b-collapse>

                    <b-nav-item v-if="showGeneralsSettings" id="sidebar-menu-general"  v-b-toggle.collapse-b :class="{'active': (collapseBOpened || checkActiveLink(adminRoutes)) && !collapseAOpened}">
                        <span class="material-icons-two-tone">settings</span>
                        <span class="title">{{$t('bs-general')}}</span>
                        <span class="w-100 text-right">
                            <i v-if="collapseBOpened" class="fa fa-chevron-up mr-1" :class="{'arrow-mini': !sidebar_is_mini}"  aria-hidden="true"></i>
                            <i v-if="!collapseBOpened" class="fa fa-chevron-down mr-1" :class="{'arrow-mini': !sidebar_is_mini}"  aria-hidden="true"></i>
                        </span>
                    </b-nav-item>

                    <b-collapse id="collapse-b">
                         <!-- MENU DE ADMIN -->
                        <template v-for="(menu, key3) in menuAdmin">
                            <b-nav-item
                                :key="key3+303"
                                @click="goToPage(menu.href)"
                                :active="checkActiveLink(menu.routes)"
                                :id="`sidebar-menu-general${key3+303}`"
                            >
                                <span class="material-icons-two-tone">{{menu.icon}}</span>
                                <span class="title">{{ menu.title }}</span>
                            </b-nav-item>
                        </template>
                    </b-collapse>
                </b-nav>
            </template>

            <!-- FOOTER OPTIONS -->
            <b-nav vertical class="pb-4 pt-3 mt-3 bb-sidebar-bottom">
                <b-nav-item
                    v-for="(menu, k) in bottom_menus"
                    :key="k+700"
                    @click="goToPage(menu.href)"
                    :id="`sidebar-menu-bottom-${k+700}`"
                    :active="checkActiveLink(menu.routes)"
                    :target="menu.target"
                >
                    <i :class="['bbi', menu.icon]"></i>
                    <span class="title">{{ menu.title }}</span>
                    <span
                        v-if="menu.badge"
                        class="sidebar-badge"
                        :style="`background-color: ${menu.badge}`"
                    ></span>
                </b-nav-item>
            </b-nav>
        </div>

        <!-- SIDEBAR MOBILE -->
        <b-sidebar id="sidebar-mobile" class="bb-sidebar-mobile" shadow backdrop no-header>
            <div class="bb-sidebar-mobile-header">
                <div class="d-flex justify-content-end">
                    <b-button-close id="closeSidebarMobile" text-variant="white" v-b-toggle.sidebar-mobile></b-button-close>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <span class="name">{{ user.name }}</span>
                        <small>{{ user.email }}</small>
                    </div>
                </div>
            </div>
            <div class="pt-2">
                <b-nav vertical sticky v-if="is_admin == 1">
                    <b-nav-item
                        v-for="(menu, k) in menuHome"
                        :key="k+10"
                        @click="goToPage(menu.href)"
                        :active="checkActiveLink(menu.routes)"
                    >
                        <i :class="['bbi', menu.icon]"></i>
                        <span>{{ menu.title }}</span>
                    </b-nav-item>

                    <b-nav-item
                        v-for="(menu, k) in menuManagement"
                        :key="k+20"
                        @click="goToPage(menu.href)"
                        :active="checkActiveLink(menu.routes)"
                    >
                        <i :class="['bbi', menu.icon]"></i>
                        <span>{{ menu.title }}</span>
                    </b-nav-item>

                    <b-nav-item
                        v-for="(menu, k) in menuAdmin"
                        :key="k+30"
                        @click="goToPage(menu.href)"
                        :active="checkActiveLink(menu.routes)"
                    >
                        <i :class="['bbi', menu.icon]"></i>
                        <span >{{ menu.title }}</span>
                    </b-nav-item>
                </b-nav>

                <b-nav vertical sticky v-if="is_admin == 0">
                    <b-nav-item
                        v-for="(menu, k) in menuHome"
                        :key="k+10"
                        @click="goToPage(menu.href)"
                        :active="checkActiveLink(menu.routes)"
                    >
                        <i :class="['bbi', menu.icon]"></i>
                        <span>{{ menu.title }}</span>
                    </b-nav-item>

                    <b-nav-item
                        v-for="(menu, k) in menuManagement"
                        :key="k+20"
                        @click="goToPage(menu.href)"
                        :active="checkActiveLink(menu.routes)"
                    >
                        <i :class="['bbi', menu.icon]"></i>
                        <span>{{ menu.title }}</span>
                    </b-nav-item>

                    <b-nav-item
                        v-for="(menu, k) in menuAdmin"
                        :key="k+30"
                        @click="goToPage(menu.href)"
                        :active="checkActiveLink(menu.routes)"
                    >
                        <i :class="['bbi', menu.icon]"></i>
                        <span >{{ menu.title }}</span>
                    </b-nav-item>
                </b-nav>

                <b-nav vertical sticky>
                    <b-nav-item
                        v-for="(menu, k) in bottom_menus"
                        :key="k+10"
                        @click="goToPage(menu.href)"
                        :active="checkActiveLink(menu.routes)"
                    >
                        <i :class="['bbi', menu.icon]"></i>
                        <span>{{ menu.title }}</span>
                    </b-nav-item>
                </b-nav>
            </div>
        </b-sidebar>
    </div>
</template>

<script>
import { mapState, mapMutations } from "vuex";

export default {
    data() {
        return {
            collapseState: {
                'collapseA': '',
                'collapseB': '',
            },
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            menus: [
                {
                    title: this.$t("bs-menu-home"),
                    href: `${this.base_url}/home`,
                    routes: ["home", "/"],
                    icon: "dashboard",
                },
                {
                    title: this.$t("bs-menu-company"),
                    href: `${this.base_url}/company`,
                    routes: ["company"],
                    icon: "business",
                },
                {
                    title: this.$t("bs-menu-department"),
                    href: `${this.base_url}/department`,
                    routes: ["department", "listDepartments"],
                    icon: "account_tree",
                },
                {
                    title: this.$t("bs-menu-group"),
                    href: `${this.base_url}/group`,
                    routes: ["group"],
                    icon: "groups",
                },
                {
                    title: this.$t("bs-menu-agent"),
                    href: `${this.base_url}/agents`,
                    routes: ["agents"],
                    icon: "person",
                },
                {
                    title: "Chat",
                    href: `${this.base_url}/full-chat`,
                    routes: ["full-chat"],
                    icon: "forum",
                },
                {
                    title: "Ticket",
                    href: `${this.base_url}/ticket`,
                    routes: ["ticket"],
                    icon: "email",
                },
                {
                    title: this.$t("bs-filters"),
                    href: `${this.base_url}/filter`,
                    routes: ["filter"],
                    icon: "inbox",
                },
            ],
	        menuHome:[
                {
                    title: this.$t("bs-menu-home"),
                    href: `${this.base_url}/`,
                    routes: ["home", "/"],
                    icon: "dashboard",
                },
	        ],
            menuAdmin: [],
	        menuManagement:[
                {
                    title: "Chat",
                    href: `${this.base_url}/full-chat`,
                    routes: ["full-chat"],
                    icon: "forum",
                },
                {
                    title: "Ticket",
                    href: `${this.base_url}/full-ticket`,
                    routes: (this.restriction[0].ticket_admin || this.restriction[0].ticket_alllist)
                        ? ["full-ticket"]
                        : ["ticket"],
                    icon: "email",
                },
                {
                    title: this.$t("bs-filters"),
                    href: `${this.base_url}/filter`,
                    routes: ["filter"],
                    icon: "inbox",
                },
                {
                    title: this.$t("bs-category"),
                    href: `${this.base_url}/category`,
                    routes: ["category"],
                    icon: "category",
                },
	        ],
            bottom_menus: [
                {
                    title: this.$t("bs-menu-change-company"),
                    href: `${this.base_url}/select-company`,
                    routes: ["selectcompany"],
                    icon: "fa fa-exchange fa-1x",
                },
                {
                    title: "Backoffice",
                    href: "https://office.builderall.com/",
                    routes: [],
                    icon: "bbi-menu-backoffice bbi-22",
                    target: "_blank",
                },
            ],
            customer_service: false,
            managementRoutes: [
                "full-chat",
                "full-ticket",
                "filter",
                "category"
            ],
            adminRoutes: [
                "company",
                "department",
                "group",
                "agents",
                "UsersClient",
                "company-integration",
                "listDepartments"
            ]
        };
    },
    created() {
        if (window.location.search == "?module=chat" || window.location.search == "?module=ticket" || window.location.search == "?module=filter" || window.location.search == "?module=category") {
            this.customer_service = true;
            localStorage.setItem("lastPageLocation", window.location.search);
        }else{
            localStorage.setItem("lastPageLocation", window.location);
        }
        
        if (this.restriction && (this.restriction[0].chat_admin || this.restriction[0].chat_alllist)) {
            this.filter_my_chats = 0;
        } else {
            this.filter_my_chats = 1;
        }

        // if (this.restriction && (this.restriction[0].ticket_admin || this.restriction[0].ticket_alllist)) {
        //     this.filter_my_tickets = 0;
        // } else {
        //     this.filter_my_tickets = 1;
        // }

        if(this.restriction[0].ticket_alllist || this.restriction[0].ticket_admin){
            if(localStorage.getItem("filter_my_tickets") == 'true'){
                this.filter_my_tickets = true;
            }else{
                this.filter_my_tickets = false;
            }
        }else{
            this.filter_my_tickets = false;
        }

        if(this.restriction[0].ticket_alllist || this.restriction[0].ticket_admin){
            if(localStorage.getItem("filter_not_category") == 'true'){
                this.filter_not_category = true;
            }else{
                this.filter_not_category = false;
            }
        }else{
            this.filter_not_category = false;
        }
        

        if (this.company !== "null") {
            this.$store.state.user_id = this.user.id;
            this.$store.state.company = JSON.parse(this.company).id;
            this.$store.state.cucd = this.session_user_cucd;
            this.online();
            this.tabs();
            // add user departments ids to logged user object
            this.user.departments_id = JSON.parse(this.user_departments_id);
            // connect to notification channel of the selected company
            this.notificationConnect(JSON.parse(this.company).id, this.user);
        }

        var refreshIntervalId = setInterval(() => {
            if (!navigator.onLine) {
                document.location.reload();
                clearInterval(refreshIntervalId);
            }
        }, 250);
    },
    props: {
        company: String,
        user_departments_id: String,
        current: {
            type: String,
            required: false,
            default: "",
        },
        is_admin: String,
        user: Object,
        base_url: {
            type: String,
            default: "",
        },
        restriction: Array,
        session_user_cucd: Array
    },
    mounted() {
        // console.log(this.restriction);
        this.setAdminOptionsByRestriction();

        if (JSON.parse(localStorage.getItem("collapseA"))) {
            this.$root.$emit('bv::toggle::collapse', 'collapse-a')
        } else if (JSON.parse(localStorage.getItem("collapseB"))) {
            this.$root.$emit('bv::toggle::collapse', 'collapse-b')
        }

        this.$root.$on('bv::collapse::state', (collapseId, isJustShown) => {
            switch (collapseId) {
                case 'collapse-a':
                    this.collapseState.collapseA = isJustShown;
                    localStorage.setItem("collapseA", isJustShown);
                    if (isJustShown && this.collapseState.collapseB) {
                        this.$root.$emit('bv::toggle::collapse', 'collapse-b')
                    }
                    break;

                case 'collapse-b':
                    this.collapseState.collapseB = isJustShown;
                    localStorage.setItem("collapseB", isJustShown);
                    if (isJustShown && this.collapseState.collapseA) {
                        this.$root.$emit('bv::toggle::collapse', 'collapse-a')
                    }
                    break;
            }
        })
    },
    methods: {
        ...mapMutations(["online"]),
        ...mapMutations(["offline"]),
        ...mapMutations(["tabs"]),
        setAdminOptionsByRestriction() {
            if (this.restriction == undefined) {
                return;
            }
        
            // if (this.restriction[0].analyze == 1 || this.is_admin == 1) {
            //     this.menuAdmin.unshift({
            //         title: this.$t("bs-analyze"),
            //         href: `${this.base_url}/analyze`,
            //         routes: ["Analyze"],
            //         icon: "query_stats",
            //     });
            // }

            if (this.restriction[0].monitoring == 1 || this.is_admin == 1) {
                this.menuAdmin.unshift({
                    title: this.$t("bs-monitoring"),
                    href: `${this.base_url}/monitoring`,
                    routes: ["Monitoring"],
                    icon: "monitor",
                });
            }

            if (this.restriction[0].client == 1 || this.is_admin == 1) {
                this.menuAdmin.unshift({
                    title: this.$t("bs-client"),
                    href: `${this.base_url}/user-client`,
                    routes: ["UsersClient"],
                    icon: "face",
                });
            }

            if (this.restriction[0].integration == 1 || this.is_admin == 1) {
                this.menuAdmin.unshift({
                    title: this.$t("bs-integration"),
                    href: `${this.base_url}/company-integration`,
                    routes: ["company-integration"],
                    icon: "electrical_services",
                });
            }

            if (this.restriction[0].agents == 1 || this.is_admin == 1) {
                this.menuAdmin.unshift({
                    title: this.$t("bs-menu-agent"),
                    href: `${this.base_url}/agents`,
                    routes: ["agents"],
                    icon: "person",
                });
            }

            if (this.restriction[0].group == 1 || this.is_admin == 1) {
                this.menuAdmin.unshift({
                    title: this.$t("bs-menu-group"),
                    href: `${this.base_url}/group`,
                    routes: ["group"],
                    icon: "groups",
                });
            }

            if (this.restriction[0].department == 1 || this.is_admin == 1) {
                this.menuAdmin.unshift({
                    title: this.$t("bs-menu-department"),
                    href: `${this.base_url}/department`,
                    routes: ["department", "listDepartments"],
                    icon: "account_tree",
                });
            }

            if (this.restriction[0].company == 1 || this.is_admin == 1) {
                this.menuAdmin.unshift({
                    title: this.$t("bs-menu-company"),
                    href: `${this.base_url}/company`,
                    routes: ["company"],
                    icon: "business",
                });
            }
        },
        notificationConnect($companyId, $user) {
            Echo.join(`notification.${$companyId}`).listen("GlobalNotification", (event) => {
                if (event.notification.type === "chat") {
                    if (this.restriction[0].chat_admin) {
                        this.notificationChat(event);
                    } else {
                        var chat_dep_filter = JSON.parse(localStorage.getItem("filter_departments"));
                        if (chat_dep_filter !== null) {
                            var departments = [];
                            chat_dep_filter.forEach(element => {
                                departments.push(element.id)
                            });
                        } else {
                            var departments = $user.departments_id;
                        }
                        if (departments.includes(event.notification.company_department_id)) {
                            this.notificationChat(event);
                        }
                    }
                } else if (event.notification.type === "ticket") {
                    if (this.restriction[0].ticket_admin) {
                        this.notificationTicket(event);
                    } else {
                        var ticket_dep_filter = JSON.parse(localStorage.getItem("filter_departments_ticket"));
                        if (ticket_dep_filter !== null) {
                            var departments = [];
                            ticket_dep_filter.forEach(element => {
                                departments.push(element.id)
                            });
                        } else {
                            var departments = $user.departments_id;
                        }
                        if (departments.includes(event.notification.company_department_id)) {
                            this.notificationTicket(event);
                        }
                    }
                    // if (this.restriction[0].ticket_admin) {
                    //     this.notificationTicket(event);
                    // } else if ($user.departments_id.includes(event.notification.company_department_id)) {
                    //     this.notificationTicket(event);
                    // }
                }
            });
        },
        notificationChat(event) {
            if (event.notification.status === "OPENED") {
                this.notificationSend(event);
            } else if (event.notification.company_user_id && this.company && JSON.parse(this.company).company_user_id === event.notification.company_user_id) {
                this.notificationSend(event);
            }
        },
        notificationTicket(event) {
            if (event.notification.status === "OPENED") {
                this.notificationSendTicket(event);
            } else if (event.notification.company_user_id && this.company && JSON.parse(this.company).company_user_id === event.notification.company_user_id) {
                this.notificationSendTicket(event);
            }
        },
        notificationSend(event) {
            let audio = "";
            if (event.notification.body === "bs-new-message-received" || event.notification.body === "bs-the-chat-will-end-due-to-inactivity") {
                audio = new Audio("/media/new-message.mp3");
            } else {
                audio = new Audio("/media/new-item.mp3");
            }
            // O título da notificação
            let title = `BA-Support | ${this.$t(event.notification.title)} #${event.notification.number}`;
            let small_title = `${this.$t(event.notification.title)} #${event.notification.number}`;
            // As opções da notificação
            let options = {
                // O corpo(mensagem) da notificação.
                body: this.$t(event.notification.body),
                // A URL da imagem usada como um ícone da notificação.
                icon: event.notification.icon,
            };
            // A url para onde a notificação irá redirecionar
            let url = event.notification.url !== "" ? event.notification.url : false;
            // Verifica se o browser suporta notificações
            if (!("Notification" in window)) {
                console.log("Este browser não suporta notificações de Desktop");
            }
            // Let's check whether notification permissions have already been granted
            else if (Notification.permission === "granted") {
                // If it's okay let's create a notification
                let n = new Notification(title, options);
                audio.play();
                if (event.notification.body === "bs-the-chat-will-end-due-to-inactivity") {
                    this.$snotify.warning(options.body, small_title, {
                        timeout: 10000,
                        showProgressBar: true,
                        closeOnClick: true,
                    });
                } else {
                    if(event.notification.timermessager){
                        this.$snotify.info(options.body, small_title, {
                            timeout: 6000,
                            closeOnClick: true,
                        });
                    }else{
                        this.$snotify.info(options.body, small_title, {
                            timeout: 6000,
                            closeOnClick: true,
                        });
                    }
                }
                if (url) {
                    n.onclick = function (event) {
                        event.preventDefault(); // prevent the browser from focusing the Notification's tab
                        window.open(url);
                    };
                }
            }
            // Otherwise, we need to ask the user for permission
            else if (Notification.permission !== "denied") {
                Notification.requestPermission(function (permission) {
                    // If the user accepts, let's create a notification
                    if (permission === "granted") {
                        let n = new Notification(title, options);
                        audio.play();
                        if (event.notification.body === "bs-the-chat-will-end-due-to-inactivity") {
                            this.$snotify.warning(options.body, small_title, {
                                timeout: 10000,
                                showProgressBar: true,
                                closeOnClick: true,
                            });
                        } else {
                            this.$snotify.info(options.body, small_title, {
                                timeout: 6000,
                                closeOnClick: true,
                            });
                        }
                        if (url) {
                            n.onclick = function (event) {
                                event.preventDefault(); // prevent the browser from focusing the Notification's tab
                                window.open(url);
                            };
                        }
                    }
                });
            }
        },
        notificationSendTicket(event) {
            let audio = "";
            if (event.notification.body === "bs-new-message-received" || event.notification.body === "bs-the-chat-will-end-due-to-inactivity") {
                audio = new Audio("/media/ticket-new-message.mp3");
            } else {
                audio = new Audio("/media/ticket-new-item.mp3");
            }
            // O título da notificação
            let title = `BA-Support | ${this.$t(event.notification.title)} #${event.notification.number}`;
            let small_title = `${this.$t(event.notification.title)} #${event.notification.number}`;
            // As opções da notificação
            let options = {
                // O corpo(mensagem) da notificação.
                body: this.$t(event.notification.body).replace(/<[^>]*>?/gm, '\n'),
                // A URL da imagem usada como um ícone da notificação.
                icon: event.notification.icon,
            };
            // A url para onde a notificação irá redirecionar
            let url = event.notification.url !== "" ? event.notification.url : false;
            // Verifica se o browser suporta notificações
            if (!("Notification" in window)) {
                console.log("Este browser não suporta notificações de Desktop");
            }
            // Let's check whether notification permissions have already been granted
            else if (Notification.permission === "granted") {
                // If it's okay let's create a notification
                let n = new Notification(title, options);
                audio.play();

                const urlParams = new URLSearchParams(window.location.search);
                const myParam = urlParams.get('module');

                const openAction = () => {
                    this.goToTicket(event.notification.number);
                }

                if(myParam == 'ticket'){
                    this.$snotify.info(options.body, small_title, {
                        position: "rightTop",
                        timeout: 15000,
                        buttons: [
                            {text: this.$t('bs-open'), action: openAction, bold: true },
                        ],
                    });
                }else{
                    this.$snotify.info(options.body, small_title, {
                        timeout: 15000,
                        buttons: [
                            {text: this.$t('bs-open'), action: openAction, bold: true },
                        ],
                    });
                }


                if (url) {
                    n.onclick = function (event) {
                        event.preventDefault(); // prevent the browser from focusing the Notification's tab
                        window.open(url);
                    };
                }
            }
            // Otherwise, we need to ask the user for permission
            else if (Notification.permission !== "denied") {
                Notification.requestPermission(function (permission) {
                    // If the user accepts, let's create a notification
                    if (permission === "granted") {
                        let n = new Notification(title, options);
                        audio.play();

                        this.$snotify.info(options.body, small_title, {
                            timeout: 15000,
                            buttons: [
                                {text: this.$t('bs-open'), action: openAction, bold: true },
                            ],
                        });

                        if (url) {
                            n.onclick = function (event) {
                                event.preventDefault(); // prevent the browser from focusing the Notification's tab
                                window.open(url);
                            };
                        }
                    }
                });
            }
        },
        goToTicket(number) {
            window.location.href = `${this.base_url}/customer-service?module=ticket&id=${number}`;
        },
        goToPage(href) {
            var chat = `${this.base_url}/full-chat`;
            var ticket = `${this.base_url}/full-ticket` || `${this.base_url}/ticket`;
            var filter = `${this.base_url}/filter`;
            var category = `${this.base_url}/category`;
            var module = "";

            if (href == chat || href == ticket || href == filter || href == category) {

                if (href == chat) {
                    module = "chat"
                    this.$store.commit("openChats");
                } else if (href == ticket) {
                    module = "ticket"
                    this.$store.commit("openTickets");
                } else if (href == filter) {
                    module = "filter"
                    this.$store.commit("openFilters");
                } else if (href == category) {
                    module = "category"
                    this.$store.commit("openCategory");
                }

                if (this.current !== 'customer-service') {
                    window.location.href = `${this.base_url}/customer-service?module=${module}`;
                } else {
                    window.history.replaceState({}, "", `${this.base_url}/customer-service?module=${module}`);
                    localStorage.setItem("lastPageLocation", window.location.search);
                }
            } else {
                window.location.href = href;
            }
        },
        checkActiveLink(routes) {
            if(routes == 'home') {
                if (window.location.pathname == "/" || window.location.pathname == "/home") return true;
            } else if (this.current !== 'customer-service') {
                return routes.includes(this.current);
            } else {
                if (this.showChat && routes.includes('full-chat')) return true;
                if (this.showTicket && routes.includes('full-ticket')) return true;
                if (this.showFilter && routes.includes('filter')) return true;
                if (this.showCategory && routes.includes('category')) return true;
            }
        }
    },
    computed: {
        ...mapState(["sidebar_is_mini"]),
        filter_my_chats: {
            get() {
                return this.$store.state.filter_my_chats;
            },
            set(value) {
                this.$store.commit("updateMyChatsFilter", value);
            }
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
        showChat() {
            return this.$store.state.showChat;
        },
        showTicket() {
            return this.$store.state.showTicket;
        },
        showFilter() {
            return this.$store.state.showFilter;
        },
        showCategory() {
            return this.$store.state.showCategory;
        },
        collapseAOpened() {
            return this.collapseState.collapseA;
        },
        collapseBOpened() {
            return this.collapseState.collapseB;
        },
        showGeneralsSettings() {
            return this.is_admin == 1 ||
                   this.restriction[0].client == 1 ||
                   this.restriction[0].integration == 1 ||
                   this.restriction[0].agents == 1 ||
                   this.restriction[0].group == 1 ||
                   this.restriction[0].department == 1 ||
                   this.restriction[0].company == 1
        },
    },
    destroyed() {
        this.offline();
    },
};
</script>

<style lang="scss" scoped>
@import "./resources/sass/variables";
.bb-sidebar {
  position: fixed;
  width: $sidebar-width;
  min-height: calc(100vh - #{$header-height});
  border-right: 1px solid #dedede;
  box-shadow: 0 0 3px rgba(38, 36, 36, 0.14);
  background: #fff 0 0 no-repeat padding-box;
  transition: $sidebar-transition;
  &.bb-mini {
    width: $sidebar-width-mini;
    .nav-item .nav-link {
      justify-content: center;
      .title:not(.sidebar-badge) {
        display: none;
      }
    }
  }
}
.bb-sidebar,
.bb-sidebar-mobile {
  .nav .nav-item .nav-link {
    padding: 18px 23px 18px 20px;
    color: #68768c;
    justify-content: flex-start;
    display: flex;
    align-items: center;
    font-size: 15px;
    height: 56px !important;
    font-weight: var(--semibold);
    font-size: 14px;
    border-left: 3px solid transparent;
    transition: 0.4s;
    position: relative;
    .bbi {
      filter: opacity(0.7) grayscale(0.6);
      transition: inherit;
    }
    span.title {
      margin-left: 20px;
    }
    &:hover,
    &.active {
      background-color: #f4f7fc;
      color: var(--primary);
      .bbi {
        filter: unset;
      }
      .sidebar-badge {
        border-color: #f4f7fc;
      }
    }
    &.active {
      border-left: 3px solid #1d5ef5;
    }
    .sidebar-badge {
      background: #ffb244;
      position: absolute;
      width: 14px;
      height: 14px;
      border-radius: 100%;
      border: 3px solid #fff;
      left: -7px;
      top: 14px;
    }
  }
}
.bb-sidebar-mobile {
  .bb-sidebar-mobile-header {
    background: url(/images/images/alert/corner.png) left top no-repeat,
      url(/images/images/alert/wave.png) right bottom/100% no-repeat,
      transparent linear-gradient(180deg, #5e81f4 0%, #1665d8 100%);
    display: flex;
    flex-direction: column;
    padding: 20px;
    color: #fff;
    span.name {
      font-size: 19px;
      margin-top: 1rem;
    }
  }
  .nav {
    .nav-item {
      .nav-link {
        color: #585858;
        i {
          width: 20px;
          height: 20px;
        }
      }
    }
  }
}
.bb-sidebar-bottom {
  border-top: 1px solid #bed1ea;
  overflow: hidden;
  .bbi-menu-backoffice {
    height: 19px !important;
  }
}
::-webkit-scrollbar {
  width: 8px !important;
  height: 8px !important;
}
::-webkit-scrollbar-track {
  background: #dadfed !important;
  border-radius: 0px !important;
}
::-webkit-scrollbar-thumb {
  background: #82c8fa !important;
  border-radius: 2px !important;
}
::-webkit-scrollbar-thumb:hover {
  background: #82c8fa !important;
}
.overflowsidebar{
	overflow-y:auto;
	overflow-x:hidden;
	height:calc(100vh - 220px);
}
.nav {
    display: unset !important;
}

.material-icons-two-tone {
    font-size: 24px !important;
    filter: invert(62%) sepia(38%) saturate(317%) hue-rotate(173deg) brightness(92%) contrast(89%);
}
.active .material-icons-two-tone {
    filter: invert(34%) sepia(98%) saturate(987%) hue-rotate(191deg) brightness(100%) contrast(120%);
}
.fade-enter-active,
.fade-leave-active { transition: opacity .5s; }
.fade-enter,
.fade-leave-to /* .fade-leave-active below version 2.1.8 */ { opacity: 0; }
.h-5{
	height: 5%;
}
.arrow-mini {
    position: relative;
    font-size: 8px;
    left: 2px;
    bottom: -5px;
}
</style>
