<template>
    <div v-if="authorized">
        <button
            v-if="subButtons && !opened"
            class="ba-helpdesk__btn-open-ticket"
            :style="styleSubButtonTicket"
            @click="goToTicketPage()"
        >
            <span>
                <img :src="baseURL + '/images/bs/email_18dp.svg'" :height="subBtn_IconSize" :width="subBtn_IconSize"/>
            </span>
            <br>
            <span>
                Ticket
            </span>
        </button>

        <button
            v-if="subButtons && !opened"
            @click.prevent="openClosePopup()"
            :style="styleSubButtonChat"
            class="ba-helpdesk__btn-open-chat"
        >
            <span>
                <img :src="baseURL + '/images/bs/forum_18dp.svg'" :height="subBtn_IconSize" :width="subBtn_IconSize"/>
            </span>
            <br>
            <span>
                Chat
            </span>
        </button>

        <button
            class="ba-helpdesk__btn-open"
            :style="styleIcon"
            v-show="!opened && !icon_hidden"
            @click="openCloseSubButtons()"
        >
            <img :src="icon_url" :width="icon_size" :height="icon_size" style="border-radius: 100%"/>
        </button>

        <div class="ba-helpdesk__popup" :style="stylePopup" v-show="opened">
            <header>
                <div />
                <img
                    :src="`${baseURL}/images/icons/close_modal.svg`"
                    alt="Close Popup"
                    height="20"
                    width="20"
                    @click="openClosePopup()"
                />
            </header>
            <iframe
                v-if="showIframe"
                sandbox="
                    allow-storage-access-by-user-activation
                    allow-scripts
                    allow-same-origin
                    allow-forms
                    allow-modals
                    allow-pointer-lock
                    allow-popups
                    allow-popups-to-escape-sandbox
                    allow-top-navigation
                    allow-top-navigation-by-user-activation
                "
                width="100%"
                height="100%"
                :src="`${$store.state.baseURL}/client/${$store.state.hash_company}/login-new${iframe_params}`"
                title="BA HelpDesk"
            >
            </iframe>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            showIframe: false,
            authorized: true,
            opened: false,
            subButtons: false,
            company_name: "",
            company: this.$store.state.hash_company,
            baseURL: this.$store.state.baseURL,
            isLogged: this.$store.state.isLogged,
            showLogin: true,
            showRegister: false,
            showRecoverPassword: false,
            user: [],
            window: {
                width: 0,
                height: 0,
            },
            btn_width: 0,
            sub_btn_width: 0,
            chat: "",
            styleIcon: {
                bottom: `${this.$store.state.icon_margin_bottom}px`,
                right: `${this.$store.state.icon_margin_right}px`,
            },
            styleSubButtonTicket: {
                bottom: `${parseInt(this.$store.state.icon_size) + 49}px`,
                height: `${this.$store.state.icon_size}px`,
                width: `${this.$store.state.icon_size}px`,
                right: `${parseInt(this.$store.state.icon_margin_right) - (parseInt(this.$store.state.icon_size) / 1.5)}px`,
                fontSize: `${parseInt(this.$store.state.icon_size) - (parseInt(this.$store.state.icon_size) * 0.773)}px`,
            },
            styleSubButtonChat: {
                bottom: `${parseInt(this.$store.state.icon_size) + 49}px`,
                height: `${this.$store.state.icon_size}px`,
                width: `${this.$store.state.icon_size}px`,
                right: `${parseInt(this.$store.state.icon_margin_right) + (parseInt(this.$store.state.icon_size) / 1.5)}px`,
                fontSize: `${parseInt(this.$store.state.icon_size) - (parseInt(this.$store.state.icon_size) * 0.773)}px`,
            },
            stylePopup: {
                bottom: `${this.$store.state.popup_margin_bottom}px`,
                right: `${this.$store.state.popup_margin_right}px`,
                width: `${this.$store.state.popup_width}px`,
                height: `${this.$store.state.popup_height}px`,
            },
        };
    },
    computed: {
        icon_size() {
            return this.$store.state.icon_size;
        },
        icon_url() {
            return this.$store.state.icon_url;
        },
        subBtn_IconSize() {
            return `${parseInt(this.$store.state.icon_size) - (parseInt(this.$store.state.icon_size) * 0.666)}px`;
        },
        icon_hidden() {
            return this.$store.state.icon_hidden;
        },
        iframe_params() {
            if (this.$store.state.builderallOffice) {
                var str = JSON.stringify(this.$store.state.client_hash);
                str = str.replace(/\+/g,'-').replace(/\//g,'_').replace(/=/g,',');
                return `?system=popup&builderall-office=1&client-hash=${str}`
            } else {
                return "?system=popup";
            }
        },
        is_chrome() {
            return navigator.userAgent.indexOf('Chrome') > -1;
        },
        is_safari() {
            if (navigator.userAgent.indexOf("Safari") > -1){
                if (this.is_chrome)  {
                    // Chrome seems to have both Chrome and Safari userAgents
                    return false;
                }
                else {
                    return true;
                }
            }
        },
        is_ios() {
            return [
                'iPad Simulator',
                'iPhone Simulator',
                'iPod Simulator',
                'iPad',
                'iPhone',
                'iPod'
            ].includes(navigator.platform)
            // iPad on iOS 13 detection
            || (navigator.userAgent.includes("Mac") && "ontouchend" in document)
        }
    },
    watch: {
        opened(val) {
            var el = document.getElementById("loopedinSelector");
            if (val) {
                this.showIframe = true;
                if (el) el.style.display = "none";
            } else {
                if (el) el.style.display = "flex";
            }
        }
    },
    created() {
        this.$root.$refs.Popup = this;
        window.addEventListener("resize", this.handleResize);
        this.handleResize();
    },
    mounted () {
        this.findLoopedinEl(1);
        this.checkCompanyPermission();
    },
    destroyed() {
        window.removeEventListener("resize", this.handleResize);
    },
    methods: {
        checkCompanyPermission() {
            var vm = this;
            var url = `${this.baseURL}/api/company`;
            axios
            .get(url, {
                params: {
                    hash_code: vm.company,
                    origin: window.location.hostname,
                },
            })
            .catch((err) => {
                vm.authorized = false;
            });
        },
        findLoopedinEl(retries) {
            var vm = this;
            if (vm.$store.state.builderallOffice) {
                var loopedin_iframe = document.getElementById("loopedin-sidebar-iframe")
                var maxRetry = 300;
                if(!loopedin_iframe && retries <= maxRetry) {
                    retries++;
                    return setTimeout(function() {
                        vm.findLoopedinEl(retries);
                    }, 1);
                } else if(loopedin_iframe) {
                    var loopedin_document = loopedin_iframe.contentWindow.document;
                    setTimeout(() => {
                        loopedin_document.querySelector("#loopedin-feed > div.sidebar-body > div > div > div.ui.bottom.attached.tab.segment.home.active > div:nth-child(4) > ul > li:nth-child(1) > a").addEventListener('click', function(e){
                            console.log('entrou');          
                            e.preventDefault();
                            document.getElementById("loopedin-sidebar").style.display = "none";
                            document.getElementById("loopedinSelector").style.transform = "scale(1)";
                            vm.openClosePopup();
                        }); 
                    }, 600);
                }
            }
        },
        goToChatPage() {
            var url = `${this.baseURL}/client/${this.company}/login-new?module=chat`;
            window.open(url, "_blank");
        },
        goToTicketPage() {
            var url = `${this.baseURL}/client/${this.company}/login-new?module=ticket`;
            window.open(url, "_blank");
        },
        handleResize() {
            this.window.width = window.innerWidth;
            this.window.height = window.innerHeight;
            var btn_width = window.innerWidth * 0.036;

            if (btn_width <= 55) {
                this.btn_width = 47;
            } else {
                this.btn_width = window.innerWidth * 0.03;
                this.sub_btn_width = window.innerWidth * 0.023;
            }
        },
        openClosePopup() {
            if (this.$store.state.chat_type == "system" || this.is_safari || this.is_ios) {
                this.goToChatPage();
            } else if (this.$store.state.chat_type == "pop-up") {
                if (this.opened) {
                    this.opened = false;
                    this.subButtons = false;
                } else {
                    this.opened = true;
                }
            }
        },
        openCloseSubButtons() {
            if (this.$store.state.module == "all") {
                this.subButtons = !this.subButtons;
            } else if (this.$store.state.module == "chat") {
                this.openClosePopup();
            } else if (this.$store.state.module == "ticket") {
                this.goToTicketPage();
            }
        },
  },
};
</script>

<style scoped lang="scss">
    @import "sass/style.scss";
</style>
