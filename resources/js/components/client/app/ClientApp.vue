
<template>
    <v-app>
        <client-toolbar v-if="!$store.state.isPopup"></client-toolbar>
        <client-header v-if="!$store.state.isPopup"></client-header>
        <v-main :style="{ background: $vuetify.theme.themes[theme].page_backgroud }">
            <router-view></router-view>
        </v-main>
        <client-footer-toolbar v-if="$store.state.isMobile && !$store.state.isPopup"></client-footer-toolbar>
        <client-popup-footer v-if="$store.state.isPopup" color="page_backgroud"/>
        <client-preferences-dialog></client-preferences-dialog>
    </v-app>
</template>

<script>
import { mapState, mapMutations } from "vuex";
export default {
	props: {
		base_url: "",
		user: Object,
		hc: String,
		csname: String,
		csid: String,
        user_client_id: String,
        setting_chat: "",
        restriction_client: "",
        session_dtype: "",
        session_department_id: "",
        is_popup: "",
	},
	created() {
        this.$store.state.isPopup = this.is_popup && this.isIframe;
        this.$store.state.company = this.hc;
		this.$store.state.showChatModule = !this.restriction_client[0].chat_hide;
		this.$store.state.showTicketModule = !this.restriction_client[0].ticket_hide;
        this.$store.state.user_client_id = this.user_client_id;
        this.$store.state.setting_chat = this.setting_chat;
		this.online();
		this.$store.state.baseURL = this.base_url;

        // CAUSA BUGS SE EXECUTAR checkoutIntegration SEMPRE.
        var url = window.location.href;
        var partesDaUrl = url.split('/');
        var rota = partesDaUrl[partesDaUrl.length - 1];
        // console.log(rota);
        if(rota == 'customer-home' || rota == 'customer-chat?open-departments=1'){
            axios.get(`/chat/get-info-chat-checkout-opened`).then(res => {
                if(res.data == 'create_checkout'){
                    this.checkoutIntegration();
                }
            })
            .catch(err => {
                console.error(err); 
            });
        }

        this.$store.state.email_prefix = this.user.email.split("_")[0]+"_"+this.user.email.split("_")[1]+"_";
		this.user.email = this.clearEmail(this.user.email);
		this.$store.state.user = this.user;
        this.$vuetify.theme.dark = this.user.dark_mode;
		this.$store.state.csname = this.csname;
		this.$store.state.csid = this.csid;
        this.joinClientQueueStatus();
        this.joinClientTicketsList();
        this.setRootClassTheme();
        this.nameRobot();
	},
	mounted() {
        var innerHeight = require('ios-inner-height');
        if (this.$iOS()){
            this.$store.state.iOS = true;
            this.$store.state.innerHeight = `calc(100% - ${innerHeight() - window.innerHeight}px)`
            $('html, body').css("height", "100%");
        } else {
           $('html, body').css("height", "100vh");
        }
        this.onResize()
        window.addEventListener('resize', this.onResize, { passive: true })
		if (!this.isChatRoute || this.$store.state.isMobile && this.isChatRoute) {
			this.getClientChats();
		}
        this.focusEventListener();

        if (localStorage.getItem("profile-updated") == 1) {
            setTimeout(() => {
                this.$notify({
                    title: this.$t("bs-profile-updated"),
                    icon: "success"
                });
                localStorage.removeItem("profile-updated");
            }, 1000);
        }
	},
	computed: {
		theme() {
			return this.$vuetify.theme.dark ? "dark" : "light";
		},
		route: {
			get() {
				return this.$route.name;
			},
		},
		isChatRoute() {
			return this.route == "chat" || this.route == "chat-opened";
		},
        isIframe() {
            return window.top !== window.self;
        }
	},
    watch: {
        '$vuetify.theme.dark'() {
            this.setRootClassTheme();
        },
    },
	methods: {
		...mapMutations(["online", "getClientChats", "joinClientQueueStatus", "joinClientTicketsList"]),
        nameRobot() {
            var vm = this;
            axios.get(`${vm.$store.state.baseURL}/chat/get-name-robot`)
            .then(({data}) => {
                // console.log(data);
                if(data.value !== null && data.value !== ''){
                    vm.$store.state.name_robot = data.value;
                }else{
                    var hostname = window.location.hostname;
                    if(hostname == 'ba-support.builderall.com' || hostname == 'localhost'){
                        vm.$store.state.name_robot = 'Fred';
                    }else{
                        vm.$store.state.name_robot = vm.$t('bs-robot');
                    }
                }
                
                // var hostname = window.location.hostname;
                // if(hostname == 'ba-support.builderall.com' || hostname == 'localhost'){
                //     vm.$store.state.name_robot = vm.$t('bs-fred-the-friendly-robot');
                // }
            })
        },
        checkoutIntegration() {
            if (this.session_dtype !== "null") {
                this.checkCheckout().then((result) => {
                    if(result){
                        var vm = this;
                        var url = `${vm.$store.state.baseURL}/chat/get-active-chats-from-department`;
                        axios.get(url, {
                            params: {
                                company_department_id: vm.session_department_id
                            }
                        }).then(({data}) => {
                            if (!data.active_chats_from_department) {
                                // SOLUÇÃO TEMPORARIRA @JOÃO - deixar a outra opção ai cria dois chats de uma vez.
                                this.$router.push({ name: 'chat-opened', params: {'id': 'create'} });
                                
                                // vm.$router.push({name: 'chat-opened', params: {id: 'create'}}).then(() => {
                                //     vm.$store.state.newChat.department = {
                                //         id: vm.session_department_id,
                                //         name: "Checkout",
                                //         type: "checkout",
                                //     }
                                // });
                            }
                        })
                    }
                })
            }
        },
        checkCheckout(){
            return new Promise((resolve, reject) => {
                var session_dtype = JSON.parse(this.session_dtype);
                session_dtype.forEach((element) => {
                    if (element == "checkout") {
                        this.$store.state.checkout = true;
                    }

                    if (element == "builderall-mentor") {
                        this.$store.state.builderallMentor = true;
                    }
                });
                resolve(this.$store.state.checkout);
            })
        },
		clearEmail(srt) {
			var aux = srt.split("_");
			aux = aux.splice(2);
			var concat = "";

			for (var i = 0; i < aux.length; i++) {
				concat += aux[i] + "_";
			}

			return concat.slice(0, -1);
		},
        onResize () {
            this.$store.state.isMobile = window.innerWidth < 600
            this.$store.state.isTablet = window.innerWidth < 960
            this.$store.state.isMedium = window.innerWidth < 1264
            if (window.innerWidth < 1264) {
                this.$store.state.drawer = true;
            }
        },
        focusEventListener() {
            var vm = this;
            window.addEventListener('blur', function(){
                vm.$store.state.visibilityState = false;
            }, false);

            window.addEventListener('focus', function(){
                vm.$store.state.visibilityState = true;
            }, false);
        },
        setRootClassTheme() {
            if (this.$vuetify.theme.dark) {
                document.getElementById('root').classList.add('theme--dark');
                document.getElementById('root').classList.remove('theme--light');
            } else {
                document.getElementById('root').classList.add('theme--light');
                document.getElementById('root').classList.remove('theme--dark');
            }
        },
	},
    beforeDestroy () {
      if (typeof window === 'undefined') return
      window.removeEventListener('resize', this.onResize, { passive: true })
    },
};
</script>
