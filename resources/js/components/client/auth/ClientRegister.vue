<template>
    <auth-template>
        <v-container class="px-xl-13 px-lg-13 px-md-13 px-sm-13 px-0">
            <h1>{{$t('bs-welcome-to-the')}} {{company.name}}.</h1>
            <span class="sub">{{$t('bs-before-starting-register-on-the-page')}}.</span>
            <v-form
                class="mt-4"
                ref="form"
                v-model="form.valid"
                lazy-validation
                @submit.prevent="register()"
            >
                <v-container>
                    <v-row :no-gutters="isIframe">
                        <v-col cols="12">
                            <v-text-field
                                v-model="form.email"
                                :rules="rules.email"
                                :label="$t('bs-email')"
                                validate-on-blur
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                :append-icon="form.passwordShow ? 'mdi-eye' : 'mdi-eye-off'"
                                :rules="rules.password"
                                :type="form.passwordShow ? 'text' : 'password'"
                                name="password"
                                :label="$t('bs-password')"
                                v-model="form.password"
                                @click:append="form.passwordShow = !form.passwordShow"
                                autocomplete="new-password"
                                validate-on-blur
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12">
                             <v-select
                                v-model="form.userLang"
                                :items="languages"
                                :rules="rules.language"
                                item-value="key"
                                :label="$t('bs-language')"
                                required
                                solo
                                class="lang-select"
                                height="40"
                                hide-details
                                @change="setLanguage"
                            >
                                <template v-slot:selection="{ item }">
                                    <v-img max-width="35" height="24" :src="`/images/flags/${item.key}.svg`"></v-img>
                                    <span class="ml-2">{{ item.desc }}</span>
                                </template>
                                <template v-slot:item="{ item }">
                                    <v-img max-width="35" height="24" :src="`/images/flags/${item.key}.svg`"></v-img>
                                    <span class="ml-2">{{ item.desc }}</span>
                                </template>
                            </v-select>
                        </v-col>
                        <v-col cols="12">
                            <v-checkbox
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
                        <v-col cols="12" class="text-center">
                            <span class="text--">
                                <a :href="`/client/${company.hash_code}/login-new`" >
                                    {{$t('bs-back-to')}} {{$t("bs-login-page")}}
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
                                {{ $t("bs-register") }}
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-container>
            </v-form>
        </v-container>
    </auth-template>
</template>

<script>
export default {
    data() {
        return {
            form: {
				email: "",
				password: "",
                passwordShow: false,
				userLang: "",
				terms_user: false,
				valid: true,
			},
            rules: {
				email: [
					(v) => !!v || this.$t("bs-mandatory"),
					(v) => /.+@.+\..+/.test(v) || this.$t("bs-invalid-field"),
				],
				password: [
					(v) => !!v || this.$t("bs-mandatory"),
                    (v) => v.length >= 6 || this.$t("bs-invalid-field"),
				],
                language: [
                    (v) => !!v || this.$t("bs-mandatory")
                ],
				terms: [
                    (v) => !!v || this.$t("bs-mandatory")
                ],
			},
            languages: this.$store.state.languages,
        }
    },
    props:{
		company: Object,
		acesso_anonymous: Boolean,
	},
    computed: {
        isIframe() {
            return window.top !== window.self;
        }
    },
    mounted () {
        this.getUserLang();
        this.openDirectlyInAModule();
    },
    methods: {
        setLanguage(){
            this.$i18n.locale = this.form.userLang;
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
            this.loginAutomatico();
        },
        register() {
           if (this.$refs.form.validate()) {
                var vm = this;
                var url = `/client/${vm.company.hash_code}/register-new`;
                vm.$loading.true();
                axios.post(url, {
                    name: vm.form.email.split('@')[0],
					email: vm.form.email,
					password: vm.form.password,
					telefone: '',
					language: vm.form.userLang,
					terms_user: vm.form.terms_user,
					country: vm.form.userLang.split('_')[1] ? vm.form.userLang.split('_')[1] :'US',
					loginUnknown: false,
                })
                .then(({data}) => {
                    vm.$loading.false();
                    if (data.success) {
                        location.href = data.redir;
                    } else {
                        vm.form.email = "";
                        vm.form.password = "";
                        vm.form.terms_user = false;
                        vm.$notify({
                            title: `${vm.$t('bs-error')}!`,
                            icon: 'error',
                        });
                    }
                })
                .catch(err => {
                    vm.$loading.false();
                    console.error(err);
                })
           }
        }
    },
}
</script>

<style>

</style>
