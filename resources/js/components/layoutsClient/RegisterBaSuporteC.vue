<template>
	<div>
		<b-container fluid class="">
			<div class="title"><center>{{$t('bs-welcome-to-the')}} {{company.name}}.</center></div>
			<div class="subtitle"><center>{{$t('bs-before-starting-register-on-the-page')}}.</center></div>
			<br>
			<b-form @submit="onSubmit" v-if="show" class="label" name="foo" autocomplete="new-password">
				<center>
					<div class="body col-sm-5 relative-wrapper">
						<!-- <img class="img-left" src="/images/man.svg"/> -->
						<!-- <img class="img-right" src="/images/woman.svg"/> -->
						<div v-show="!loginUnknown">
							<!-- <b-form-group id="input-group-1" :label="$t('bs-name')" label-for="input-1">
								<b-form-input
								id="input-1"
								v-model="form.name"
								autocomplete="new-password"
								:required="!loginUnknown"
								:placeholder="phName"
								class="bs-input bodersimple"
								:state="vName"
								></b-form-input>
							</b-form-group> -->
							<b-form-group id="input-group-2" :label="$t('bs-email')" label-for="input-1">
								<b-form-input
								id="input-2"
								v-model="form.email"
								autocomplete="new-password"
								:required="!loginUnknown"
								:placeholder="phEmail"
								class="bs-input bodersimple"
								:state="vEmail"
								></b-form-input>
							</b-form-group>
							<b-form-group id="input-group-3" :label="$t('bs-password')" label-for="input-1">
								<b-form-input
								id="input-123123123"
								v-model="form.password"
								type="password"
								autocomplete="new-password"
								:required="!loginUnknown"
								:placeholder="phPass"
								class="bs-input bodersimple"
								:state="vPassword"
								></b-form-input>
							</b-form-group>
							<!-- <b-form-group id="input-group-4" :label="$t('bs-confirm-password')" label-for="input-1">
								<b-form-input
                                    id="input-1231234"
                                    v-model="form.password2"
                                    type="password"
                                    autocomplete="new-password"
                                    :required="!loginUnknown"
                                    :placeholder="phConfiPass"
                                    class="bs-input bodersimple"
                                    :state="vPassword2"
                                    >
                                </b-form-input>
					        </b-form-group> -->

					<b-form-group id="input-group-5" :label="$t('bs-language')" label-for="input-1">
					<v-select
                        style="background-color: #F2F2F2"
                        :clearable="false"
                        :options="languages"
                        label="desc"
                        v-model="form.userLang"
                        :reduce="value => value.key"
                    >
                        <template #selected-option="{key, desc}">
                            <img height="24" class="mx-3" :src="`/images/flags/${key}.svg`" alt="">
                            {{desc}}
                        </template>
                        <template #option="{key, desc}">
                            <img height="24" class="mx-2" :src="`/images/flags/${key}.svg`" alt="">
                            {{desc}}
                        </template>
                    </v-select>
                    </b-form-group>
					<!-- <b-form-group id="input-group-6" label="Telefone" label-for="input-1">
						<vue-tel-input class="input" label="Telefone" v-model="form.telefone"></vue-tel-input>
					</b-form-group> -->
				</div>
				<b-row v-show="loginUnknown == false">
					<b-col>
						<span class="termtext">
							<b-form-checkbox
							id="checkbox-1"
							v-model="terms_user"
							name="checkbox-1"
							required
							:state="vterms"
							>
							<label for="exampleFormControlSelect1">
								{{$t('bs-i-agree-with-the') }}
								<b-link @click.prevent="goToPrivacyPolicy()">{{ $t("bs-terms-of-use") }} & {{$t("bs-privacy-policy")}}</b-link>
							</label>
							</b-form-checkbox>
						</span>
					</b-col>
				</b-row>
				<b-row>
					<b-col>
						<center>
							<label for="exampleFormControlSelect1">
								{{$t('bs-back-to')}}
								<b-link class="links" :href="`/client/${this.company.hash_code}/login`">{{$t('bs-login-page')}}</b-link>
							</label>
						</center>
					</b-col>
				</b-row>
				<!-- <center class="mb-1 mt-2" v-if="acesso_anonymous">
					<b-form-checkbox v-model="loginUnknown">{{$t('bs-i-dont-want-to-identify-myself	')}}</b-form-checkbox>
				</center> -->
				<span v-show="loginUnknown == true">
					<b-row>
						<b-col>
							<span class="termtext">
								<b-form-checkbox
								id="checkbox-2"
								v-model="terms_user"
								name="checkbox-2"
								:required='loginUnknown'
								:state="vterms"
								>
								<label for="exampleFormControlSelect1">
									{{$t('bs-i-agree-with-the') }}
									<b-link @click.prevent="goToPrivacyPolicy()">{{ $t("bs-terms-of-use") }} & {{$t("bs-privacy-policy")}}</b-link>
								</label>
								</b-form-checkbox>
							</span>
						</b-col>
					</b-row>
					<b-alert show variant="danger"><center>{{$t('bs-by-selecting-this-option-you-will-not-be-a')}}.</center></b-alert>
				</span>
			</div>
			<br>
			<center>
				<b-button type="submit" variant="primary" class="btn-next" :disabled="invalidForm">
					<span v-if="loginUnknown">
						{{$t('bs-enter')}}
					</span>
					<span v-else>
						{{$t('bs-register')}}
					</span>
					<i class="fa fa-arrow-right" aria-hidden="true"></i>
					</b-button>
			</center>
		</center>
	</b-form>
</b-container>
	<vue-snotify></vue-snotify>

</div>
</template>

<script>

export default {
	data(){
		return {
			phName: '',
			phEmail: '',
			phPass: '',
			phConfiPass: '',
			name: '',
			backto: '',
			pageto: '',
			password: '',
			confirPass: '',
			language: '',
			languages: this.$store.state.languages,
			form: {
				name: '',
				email: '',
				password: '',
				password2: '',
				telefone: '',
				userLang: '',
			},
			formCompany: {
				name: false,
				email: false,
				password: false,
				password2: false,
				terms: false,
			},
			show: true,
			politicaPrivacidade: "",
			termoUso: "",
			terms_user: false,
			loginUnknown: false,
		}
	},
	props:{
		company: Object,
		acesso_anonymous: Boolean,
	},
	mounted(){
		var userLang = navigator.language || navigator.userLanguage;
		this.$i18n.locale = userLang.replace('-', '_');
		this.form.userLang = 'en_US';

		for (var i = 0; i < this.languages.length; i++) {
			if(this.languages[i].key == this.$i18n.locale){
				this.form.userLang = this.$i18n.locale;
			}
		}

		this.phName = this.$t('bs-enter-name');
		this.phEmail = this.$t('bs-enter-email');
		this.phPass = this.$t('bs-password');
		this.phConfiPass = this.$t('bs-confirm-password');

		this.politicaPrivacidade = "#terms-privacy-policy/"+this.$i18n.locale;
		this.termoUso = "#terms-of-use/"+this.$i18n.locale;
        this.openDirectlyInAModule();
	},
	methods:{
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
        loginAutomatico(){
            var loginAuto = localStorage.getItem("loginAuto");
			if(loginAuto == 'true'){
                var url = `/client/${this.company.hash_code}/login`;
                window.location.href = url;
			}
		},
		onSubmit(evt) {
			var vm = this;
			evt.preventDefault();
			var url = `/client/${this.company.hash_code}/register`;

			if(!vm.invalidForm){

			 	axios.post(url, {
					// name: this.form.name,
					name: this.form.email.split('@')[0],
					email: this.form.email,
					password: this.form.password,
					telefone: this.form.telefone,
					language: this.form.userLang,
					terms_user: this.terms_user,
					country: this.form.userLang.split('_')[1] ? this.form.userLang.split('_')[1] :'US',
					loginUnknown: vm.loginUnknown,
				}).then(function(response){

					if(response.data.success){
						vm.$snotify.success(vm.$t('bs-registration-successfully-complete'), vm.$t('bs-success'));
						location.href = response.data.redir;
					}else{
						vm.$snotify.error(response.data.value, vm.$t('bs-error'));
						vm.form.password = '';
						vm.form.password2 = '';

					}

				})
				.catch(function(){
					vm.$snotify.error(vm.$t('bs-email-not-registered')+'!', vm.$t('bs-error'));
					console.log('FAILURE!!');
				});

			}else{
				alert('CAMPO INVALIDO!');
			}
		},
		goToPrivacyPolicy() {
			axios.post("/user/privacy-policy", {
				locale: this.form.userLang,
			})
				.then(res => {

				window.open(res.data.terms_of_use_url, '_blank').focus();
			})
		},
	},
	watch:{
		'form.userLang': function () {
			this.$i18n.locale = this.form.userLang;
			// this.form.userLang = this.$i18n.locale;
		},
	},
	computed: {
		vName() {
			this.formCompany.name = this.form.name.length > 1 && this.form.name.length < 100;
			return this.formCompany.name;
		},
		vEmail(){
			var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			this.formCompany.email = re.test(this.form.email);
			return this.formCompany.email;
		},
		vPassword(){
			this.formCompany.password = this.form.password.length > 5 && this.form.password.length < 255;
			return this.formCompany.password;
		},
		vPassword2(){
			// this.formCompany.password2 = this.form.password == this.form.password2;
			// return this.formCompany.password2;
            return true;
		},
		vterms(){
			this.formCompany.terms = this.terms_user == true;
			return this.formCompany.terms;
		},
		invalidForm(){
			// console.log(!(this.formCompany.name && this.formCompany.email && this.formCompany.password && this.formCompany.password2 && this.form.userLang != '') && !this.loginUnknown);
			// return !(this.formCompany.name && this.formCompany.email && this.formCompany.password && this.formCompany.password2 && this.form.userLang != '') && !this.loginUnknown; //  && this.formCompany.terms
			return !(this.formCompany.email && this.formCompany.password  && this.form.userLang != '' && this.formCompany.terms) && !this.loginUnknown; //  && this.formCompany.terms
		}
	},
};
</script>

<style scoped lang="scss">
.links{
	text-align: left;
	font: normal normal bold 12px/18px Muli;
	letter-spacing: 0px;
}

.termtext{
	text-align: center;
	font: normal normal bold 12px/18px Muli;
	letter-spacing: 0px;
	opacity: 1;
}

.relative-wrapper{
	position: relative;
	.img-left{
		position: absolute;
		top: 110px;
		left: -35.9442857%;
		height: 63.7813212%;
		min-height: 97,94238679%;
		width: auto;
		z-index: -1;
	}

	.img-right{
		position: absolute;
		bottom: 190px;
		right: -35.9442857%;
		height: 63.7813212%;
		min-height: 97,94238679%;
		width: auto;
		z-index: -1;
	}
}

@media screen and (max-width: 1199px) {
	.relative-wrapper{
		.img-left{
			left: -45.9442857%;
		}
		.img-right{
			right: -45.9442857%;
		}
	}
}
@media screen and (max-width: 967px) {
	.relative-wrapper{
		.img-left,
		.img-right{
			display: none;
		}
	}
}

.imageback{
	top: 15%;
	bottom: 0;
	left: 0;
	right: 0;
	position: absolute;
	z-index: -11;
}

.subtitle{
	text-align: left;
	font: normal normal bold 24px/35px Muli;
	letter-spacing: 0px;
	color: #656565;
	opacity: 1;
}

.title{
	text-align: left;
	font: normal normal 800 50px/73px Muli;
	letter-spacing: 0px;
	color: #0294FF;
	opacity: 1;
	margin-top: 26px;
}

.btn-save{
	background-color: #4cf0fc;
	box-shadow: 0px 1px 1px #1E120D1A;
	color: white;
	font: normal normal 800 14px/16px Muli;
}

.btn-next{
	background: #0F7BFF 0% 0% no-repeat padding-box;
	border-radius: 8px;
	padding: 27px;
	padding-top: 20px;
	padding-bottom: 20px;
}

.body{
	background: #FFFFFF 0% 0% no-repeat padding-box;
	box-shadow: 0px 2px 4px #0000001A;
	border-radius: 5px;
	padding: 35px;
	padding-top: 25px;
	padding-bottom: 15px;
	text-align: left;
}

.label{
	text-align: left;
	font: normal normal bold 14px/18px Muli;
	letter-spacing: 0px;
	color: #707070;
	opacity: 1;
}

.input{
	border: 1px solid #a4a4a4;
	padding-top: 5px;
	padding-bottom: 5px;
	padding-left: 5px;
	text-align: left;
	font: normal normal bold 14px/18px Muli;
	letter-spacing: 0px;
	opacity: 0.75;
}

.bodersimple{
	border: 1px solid #a4a4a4;
}

.link{
	text-align: left;
	font: normal normal normal 15px/15px Muli;
	letter-spacing: 0px;
	color: #707070;
	opacity: 1;
}

.linkrigth{
	text-align: right;
	text-decoration: underline;
	font: normal normal normal 15px/15px Muli;
	letter-spacing: 0px;
	color: blue;
}


@media screen and (max-width: 880px) {

	.title{
		text-align: left;
		font: normal normal 800 40px/53px Muli;
		letter-spacing: 0px;
		color: #0294FF;
		opacity: 1;
		margin-top: 66px;
	}

	.subtitle{
		text-align: left;
		font: normal normal bold 20px/30px Muli;
		letter-spacing: 0px;
		color: #656565;
		opacity: 1;
	}

	.body{
		background: #FFFFFF 0% 0% no-repeat padding-box;
		box-shadow: 0px 2px 4px #0000001A;
		border-radius: 5px;
		padding: 35px;
		padding-top: 25px;
		padding-bottom: 15px;
		text-align: left;
		max-width: 500px;
	}

}

@media screen and (max-width: 576px) {

	.title{
		text-align: left;
		font: normal normal 800 30px/43px Muli;
		letter-spacing: 0px;
		color: #0294FF;
		opacity: 1;
		margin-top: 46px;
	}

}


</style>
