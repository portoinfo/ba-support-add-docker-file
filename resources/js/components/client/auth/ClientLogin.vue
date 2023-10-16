<template>
	<auth-template>
        <v-container v-show="step === 0" class="px-xl-13 px-lg-13 px-md-13 px-sm-13 px-0">
            <h1>{{ titleLogin }}</h1>
            <span class="sub">{{ subtitleLogin }}</span>
            <v-form
                id="loginForm"
                class="mt-4"
                ref="loginForm"
                v-model="form.valid"
                lazy-validation
                @submit.prevent="login()"
            >
                <v-container>
                    <v-row v-if="!form.loginUnknown">
                        <v-col cols="12">
                            <v-text-field
                                v-model="form.email"
                                :rules="rules.email"
                                :label="$t('bs-email')"
                                validate-on-blur
                                :disabled="form.loginUnknown"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                :append-icon="form.passwordShow ? 'mdi-eye' : 'mdi-eye-off'"
                                :rules="[rules.password.required]"
                                :type="form.passwordShow ? 'text' : 'password'"
                                name="password"
                                :label="$t('bs-password')"
                                v-model="form.password"
                                class="input-group--focused"
                                @click:append="form.passwordShow = !form.passwordShow"
                                autocomplete="new-password"
                                :validate-on-blur="!isPopup"
                                :disabled="form.loginUnknown"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row no-gutters :class="!form.loginUnknown ? 'mt-7' : false">
                        <v-col  class="text-left w-fc" v-if="!form.loginUnknown">
                            <a
                                :href="`/client/${company.hash_code}/register-new`"
                                class="c404040 fz-15 text-decoration-none"
                            >
                                {{ $t("bs-register") }}
                            </a>
                        </v-col>
                        <v-col class="text-right text-decoration-underline" v-if="!form.loginUnknown">
                            <a class="fz-15" @click="goToStep(1)">
                                {{$t("bs-forgot-your-password")}}</a>
                        </v-col>
                        <template v-if="incognito_chckbx">
                            <v-col cols="12">
                                <v-checkbox
                                    v-model="form.loginUnknown"
                                    class="fz-15"
                                    :label="$t('bs-i-dont-want-to-identify-myself')"
                                    @click="resetForm()"
                                    :hint="$t('bs-by-selecting-this-option-you-will-not-be-a')"
                                    :persistent-hint="form.loginUnknown"
                                ></v-checkbox>
                                <v-checkbox
                                    v-if="form.loginUnknown"
                                    v-model="form.terms_user"
                                    :rules="rules.terms"
                                    class="fz-15"
                                >
                                    <template v-slot:label>
                                        <div>
                                            {{ $t("bs-i-agree-with-the") }}
                                            <v-tooltip bottom>
                                                <template v-slot:activator="{ on }">
                                                    <a @click.stop="goToPrivacyPolicy()" v-on="on">
                                                        {{ $t("bs-terms-of-use") }} &
                                                        {{ $t("bs-privacy-policy") }}
                                                    </a>
                                                </template>
                                                {{ $t("bs-view") }}
                                            </v-tooltip>
                                        </div>
                                    </template>
                                </v-checkbox>
                            </v-col>
                        </template>
                    </v-row>
                    <v-row justify="center">
                        <v-col xl="6" lg="6" md="6" sm="6" cols="9">
                            <v-btn
                                large
                                elevation="0"
                                width="100%"
                                class="text-capitalize fz-18 white--text"
                                color="#0080FC"
                                style="border-radius: 50px"
                                type="submit"
                            >
                                {{ $t("bs-enter") }}
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-container>
            </v-form>
        </v-container>
        <v-container v-show="step === 1" class="px-xl-13 px-lg-13 px-md-13 px-sm-13 px-0">
            <h1>{{$t('bs-forgot-your-password')}}</h1>
            <v-form
                id="recoveryForm"
                class="mt-4"
                ref="recoveryForm"
                v-model="recoveryForm.valid"
                @submit.prevent="sendRecoveryEmail()"
            >
                <v-container>
                    <v-row justify="center">
                        <v-col cols="12">
                            <v-text-field
                                v-model="recoveryForm.email"
                                :rules="recoveryRules.email"
                                :label="$t('bs-email')"
                                validate-on-blur
                                autofocus
                            ></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col class="text-center">
                            <span class="fz-15 c404040">
                                <a @click="goToStep(0)" class="text-decoration-underline">
                                    {{$t('bs-back-to')}}
                                    {{$t("bs-login-page")}}
                                </a>
                            </span>
                        </v-col>
                    </v-row>
                    <v-row justify="center">
                        <v-col xl="6" lg="6" md="6" sm="6" cols="9">
                            <v-btn
                                large
                                elevation="0"
                                width="100%"
                                class="text-capitalize fz-18 white--text"
                                color="#0080FC"
                                style="border-radius: 50px"
                                type="submit"
                            >
                                {{ $t("bs-send") }}
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-container>
            </v-form>
        </v-container>
        <v-container v-show="step === 2" class="px-xl-13 px-lg-13 px-md-13 px-sm-13 px-0">
            <h1>{{$t('bs-successfully-forwarded-email')}}!</h1>
            <span class="sub">{{$t('bs-an-email-with-a-password-recovery-request')}}</span>
            <v-row justify="center" class="mt-5">
                <v-col cols="9">
                    <v-btn
                        large
                        elevation="0"
                        width="100%"
                        class="text-lowercase fz-18 white--text"
                        color="#0080FC"
                        style="border-radius: 50px"
                        @click="goToStep(0)"
                    >
                        {{$t('bs-back-to-login')}}
                    </v-btn>
                </v-col>
            </v-row>
        </v-container>
    </auth-template>
</template>

<script>
export default {
	data() {
		return {
            step: 0,
			form: {
				email: "",
				password: "",
				userLang: "",
				loginUnknown: false,
				terms_user: false,
				passwordShow: false,
				valid: true,
			},
            recoveryForm: {
                email: "",
                valid: true
            },
			rules: {
				email: [
					(v) => this.form.loginUnknown || !!v || this.$t("bs-mandatory"),
					(v) => this.form.loginUnknown || /.+@.+\..+/.test(v) || this.$t("bs-invalid-field"),
				],
				password: {
					required: (v) => this.form.loginUnknown || !!v || this.$t("bs-mandatory"),
				},
				terms: [
                    (v) => !!v || this.$t("bs-mandatory")
                ],
			},
            recoveryRules: {
                email: [
					(v) => !!v || this.$t("bs-mandatory"),
					(v) => /.+@.+\..+/.test(v) || this.$t("bs-invalid-field"),
				],
            },
			titleLogin: "",
			subtitleLogin: "",
			disableChat: Boolean,
            isPopup: false,
            isOfficePopup: false,
            officePopupClientHash: null,
		};
	},
	props: {
		company: Object,
		acesso_anonymous: Boolean,
		settings: Array,
	},
	computed: {
		incognito_chckbx() {
			return this.acesso_anonymous && !this.disableChat;
		},
	},
    created() {
		this.autoLogin();
    },
	mounted() {
        this.checkShowChatSetting();
		this.getUserLang();
		this.setTitleBySettings();
		this.openDirectlyInAModule();
	},
	methods: {
        autoLogin(){
            try{
                const url = new URL(location.href);
                var urltype = url.searchParams.get("system");
                if(urltype === "popup" || window.top !== window.self) {
                    this.isPopup = true;
                    const builderall_office = url.searchParams.get("builderall-office");
                    if (builderall_office === '1') {
                        this.isOfficePopup = true;
                        var client_hash = url.searchParams.get("client-hash");
                        this.officePopupClientHash = client_hash.replace(/-/g,'+').replace(/_/g,'/').replace(/,/g,'=');
                        this.form.email = "buiderall@builderall.com";
                        this.form.password = "buiderall";
                        this.autoValidateFormLogin();
                    } else {
                        var popupAutoLoginData = localStorage.getItem("popupAutoLoginData");
                        if (popupAutoLoginData !== null){
                            this.form = JSON.parse(popupAutoLoginData);
                            this.autoValidateFormLogin();
                        }
                    }
                }else {
                    this.isPopup = false;
                }
            } catch (e) {
                localStorage.removeItem("popupAutoLoginData");
            }
        },
        autoValidateFormLogin() {
            var validateInterval = setInterval(() => {
                if (this.$refs.loginForm.validate()) {
                    this.login();
                    clearInterval(validateInterval);
                }
            }, 150);
        },
        checkShowChatSetting() {
            var showChat = JSON.parse(this.settings[0]["general"]).showChat; // obs: esta invertido o nome da variavel nas configurações
		    this.disableChat = showChat;
        },
		getUserLang() {
			var userLang = navigator.language || navigator.userLanguage;
			this.$i18n.locale = userLang.replace("-", "_");
			this.form.userLang = "en_US";
			this.$store.state.languages.forEach((language) => {
				if (language.key == this.$i18n.locale) {
					this.form.userLang = this.$i18n.locale;
				}
			});
		},
		goToPrivacyPolicy() {
			axios
				.post("/user/privacy-policy", {
					locale: this.form.userLang,
				})
				.then((res) => {
					window.open(res.data.terms_of_use_url, "_blank").focus();
				});
		},
        goToStep(step) {
            this.step = step
            this.resetForm();
        },
		login() {
			if (this.$refs.loginForm.validate()) {
				var vm = this;
				var url = `/client/${vm.company.hash_code}/login-new`;
                vm.$loading.true();
				axios
					.post(url, {
						email: vm.form.email,
						password: vm.form.password,
						force_login: 1,
						loginUnknown: vm.form.loginUnknown,
						terms_user: vm.form.terms_user,
						language: vm.form.userLang,
						country: vm.form.userLang.split("_")[1] ? vm.form.userLang.split("_")[1] : "US",
                        isPopup: vm.isPopup,
                        isOfficePopup: vm.isOfficePopup,
                        officePopupClientHash: vm.officePopupClientHash
					})
					.then(({ data }) => {
						if (data.success) {
                            vm.form.email = data.email;
                            vm.form.password = data.password;
                            vm.form.loginUnknown = false;
                            if (vm.isPopup && !vm.isOfficePopup) {
                                localStorage.setItem("popupAutoLoginData", JSON.stringify(vm.form));
                            }
                            vm.isOfficePopup ? data.redir = `${data.redir}?builderall-office=1` : '';
							window.location.href = data.redir;
						} else {
                            vm.$loading.false();
							var title = data.value == "not_email" ? vm.$t("bs-email-or-user-invalid") : vm.$t("bs-error");
							var icon = data.value == "not_email" ? "warning" : "error";
							vm.$notify({
								title: `${title}!`,
								icon: icon,
							});
						}
					})
					.catch((err) => {
                        vm.$loading.false();
						console.error(err);
					});
			}
		},
		openDirectlyInAModule() {
			// verifico se existe "module" na URL
			var url_dtype = new URL(location.href).searchParams.get("module");
			if (url_dtype === "ticket") {
				localStorage.setItem("ticket_module", 1);
				this.removeParamFromURL("module");
			} else if (url_dtype === "chat") {
				localStorage.setItem("chat_module", 1);
				this.removeParamFromURL("module");
			}
		},
		removeParamFromURL(param) {
			const url = new URL(window.location.href);
			const params = new URLSearchParams(url.search.slice(1));
			params.delete(param);
			window.history.replaceState({}, "", `${window.location.pathname}`);
		},
		resetForm() {
            this.form.email = "";
			this.form.password = "";
            this.recoveryForm.email = "";
			if (!this.form.loginUnknown) {
				this.form.terms_user = false;
			}
		},
        sendRecoveryEmail() {
            var vm = this;
            if (vm.$refs.recoveryForm.validate()) {
                vm.$loading.true();
                var url = 'forgot-password';
                axios.post(url,{
                    email: vm.recoveryForm.email,
                    company: vm.company.id,
                    language: vm.$i18n.locale
                })
                .then(({data}) => {
                    vm.$loading.false();
                    if (data.success) {
                        vm.goToStep(2);
                    } else if(data.error == 'not_found') {
                        vm.$notify({
                            title: `${vm.$t('bs-email-not-found')}!`,
                            icon: 'warning',
                        });
                    }
                })
                .catch(err => {
                    vm.$loading.false();
                    vm.$notify({
                        title: `${vm.$t('bs-error')}!`,
                        icon: 'error',
                    });
                })

            }
        },
		setTitleBySettings() {
			if (this.settings[0] != undefined) {
				if (
					JSON.parse(this.settings[0].general).titleLogin == "" ||
					JSON.parse(this.settings[0].general).titleLogin == undefined
				) {
					this.titleLogin = this.$t("bs-welcome-to-the") + " " + this.company.name;
					this.subtitleLogin = this.$t("bs-before-starting-to-login-to-the-page");
				} else {
					this.titleLogin = JSON.parse(this.settings[0].general).titleLogin.replace("{company_name}", this.company.name);
					this.subtitleLogin = JSON.parse(this.settings[0].general).subtitleLogin.replace("{company_name}", this.company.name);
				}
			} else {
				this.titleLogin = this.$t("bs-welcome-to-the") + " " + this.company.name;
				this.subtitleLogin = this.$t("bs-before-starting-to-login-to-the-page");
			}
		},
	},
};
</script>
