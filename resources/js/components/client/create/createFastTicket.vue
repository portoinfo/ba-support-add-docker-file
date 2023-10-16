<template>
    <v-app class="login ov-y" style="height: 100%">
		<v-main class="d-flex justify-center align-center">
			<v-col cols="11" xl="4" lg="5" md="6" sm="10" class="mx-auto">
				<v-card
					class="dt-login-card text-center mx-auto"
                    :class="{'py-12': !isIframe}"
					elevation="0"
					max-width="616"
				>
                <v-container class="px-xl-13 px-lg-13 px-md-13 px-sm-13 px-0">
                        <h1>{{ titleLogin }}</h1>
                        <span class="sub">{{ subtitleLogin }}</span>
                        <v-form
                            id="loginForm"
                            class="mt-4"
                            ref="loginForm"
                            lazy-validation
                            @submit.prevent="createCT()"
                        >
                            <v-container>
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field
                                            v-model="form.subjectbox"
                                            :label="$t('bs-subject')"
                                            validate-on-blur
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-text-field
                                            v-model="form.bodymessage"
                                            :label="$t('bs-message')"
                                            validate-on-blur
                                        ></v-text-field>
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
                </v-card>
			</v-col>
		</v-main>
	</v-app>
</template>

<script>
export default {
	data() {
		return {
			form: {
				subjectbox: "",
				bodymessage: "",
				userLang: "",
			},
			titleLogin: "",
			subtitleLogin: "",
            uniqueCreate: true,
		};
	},
    props: {
        depart_id: String,
        depart_name: String,
    },
	computed: {
        isIframe() {
            return window.top !== window.self;
        },
        onlineUsers: {
            get() {
                return this.$store.state.online_users;
            }
        },
	},
    created() {
    },
	mounted() {
		this.getUserLang();
		this.setTitleBySettings();
	},
	methods: {
        createCT(){
            var vm = this;
            if(vm.uniqueCreate){
                vm.uniqueCreate = false;
                axios.post('create-fast-ticket', {
                    item: vm.form,
                    onlineUsers: vm.onlineUsers,
                    depart: vm.depart_id,
                })
                .then(res => {
                    // console.log(res.data);
                    if(res.data.success){
                        window.location.href = '/customer-ticket/'+res.data.hash_id;
                    }
                }).catch(err => {
                    console.error(err); 
                });
            }
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
		setTitleBySettings() {
            this.titleLogin = this.depart_name;
            this.subtitleLogin = this.$t("bs-exclusive-form-to-contact-our-success-coac");
		},
	},
};
</script>
