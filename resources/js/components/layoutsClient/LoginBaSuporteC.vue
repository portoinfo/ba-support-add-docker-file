<template>
	<div id="tela" data-id="tela">
		<b-container v-if="steep == 0" fluid class="">
			<div class="title"><center>{{titleLogin}}</center></div>
				<div class="subtitle"><center>{{subtitleLogin}}</center></div>
				<br><br>
				<b-form @submit="onSubmit" v-if="show" class="label" autocomplete="new-password">
					<center>
						<div class="body col-sm-5 relative-wrapper">
							<!-- <img class="img-left" src="/images/man.svg"/> -->
							<!-- <img class="img-right" src="/images/woman.svg"/> -->
							<div>
								<b-form-group v-show="!loginUnknown == true" id="input-group-1" label="E-mail:" label-for="input-1">
									<b-form-input
									id="input-1"
									v-model="form.email"
									type="email"
									:required='!loginUnknown'
									:placeholder="$t('bs-enter-email')"
									class="bs-input"
									:state="vEmail"
									></b-form-input>
								</b-form-group>
								<b-form-group v-show="!loginUnknown == true" id="input-group-2" :label="$t('bs-password')+':'" label-for="input-2">
									<b-form-input
									id="input-2"
									v-model="form.password"
									type="password"
									autocomplete="on"
									:required='!loginUnknown'
									:placeholder="$t('bs-password')"
									class="bs-input"
									:state="vPassword"
									></b-form-input>
								</b-form-group>
								<div class="mx-auto">
										<b-form-group v-show="!loginUnknown == true" id="input-3">
											<b-row>
												<b-col cols="auto">
													<b-link :href="`/client/${this.company.hash_code}/register`" class="link">
														<b-form-group
														id="fieldset-horizontal"
														:description="$t('bs-register')"
														class="bs-text">
														</b-form-group>
													</b-link>
												</b-col>

												<b-col>
													<!-- <b-form-checkbox v-model="saveDateLogin">Salvar data do login</b-form-checkbox> -->
												</b-col>

												<b-col>

												</b-col>

												<b-col cols="auto">
													<!-- <b-form-checkbox v-model="loginAuto">{{$t('bs-auto-login')}}</b-form-checkbox> -->
													<!-- <a href="#" @click="changepass">{{$t('bs-forgot-your-password')}}</a> -->
													<b-link href="#">
														<a @click="changepass">{{$t('bs-forgot-your-password')}}</a>
													</b-link>
												</b-col>
											</b-row>
										</b-form-group>
										<center id="tooltip-chat-admin" class="mb-1" v-if="acesso_anonymous && !disableChat">
											<b-form-checkbox v-model="loginUnknown" @change="clearInput">{{$t('bs-i-dont-want-to-identify-myself')}} </b-form-checkbox>
										</center>
										<!-- <b-tooltip target="tooltip-chat-admin" triggers="hover" placement="right" variant="secondary">
											Ao selecionar isso essa opção não será possivel criar tickets, se ouver nessecidade peço para clicar em registrar
										</b-tooltip> -->
										<span v-show="loginUnknown == true">
											<b-row>
												<b-col>
													<span class="termtext">
														<b-form-checkbox
														id="checkbox-1"
														v-model="terms_user"
														name="checkbox-1"
														:required='loginUnknown'
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
							</div>
						</div><br>
					<center>
						<b-button type="submit" variant="primary" class="btn-next">
							{{$t('bs-enter')}} <i class="fa fa-arrow-right" aria-hidden="true"></i>
						</b-button>
					</center>
				</center>
			</b-form>
		</b-container>
		<b-container v-if="steep == 1" fluid class="">
			<div class="title"><center>{{$t('bs-forgot-your-password')}}</center></div>
			<!-- <div class="subtitle"><center> Digite seu email</center></div> -->
			<b-form @submit="onSubmitSend" v-if="show" class="label" autocomplete="new-password">
				<center>
					<div class="body col-sm-5">
						<b-form-group id="input-group-1" label="Digite seu e-mail:" label-for="input-1">
							<b-form-input
								id="input-1"
								v-model="form.email"
								type="text"
								:placeholder="$t('bs-enter-email')"
								class="bs-input"
								:state="vEmail"
								required
							></b-form-input>
						</b-form-group>
						<center><a href="#" @click="back"> {{$t('bs-back-to-login')}} </a></center>
					</div>
					<br>
					<center>
						<b-button type="submit" variant="primary" class="btn-next" > {{$t('bs-send')}} <i class="fa fa-arrow-right" aria-hidden="true"></i></b-button>
					</center>
				</center>
			</b-form>
		</b-container>
		<b-container v-if="steep == 2" fluid class="">
			<div class="title"><center>{{$t('bs-successfully-forwarded-email')}}</center></div>
				<center>
					<br>
					<div class="col-sm-5 relative-wrapper">
						<b-form-group id="input-group-1" label-for="input-1">
							<center>{{$t('bs-an-email-with-a-password-recovery-request')}}</center>
						</b-form-group>
					</div>
					<br>
					<center>
						<b-button type="button" @click="back" variant="primary" class="btn-next" >{{$t('bs-back-to-login')}}<i class="fa fa-undo" aria-hidden="true"></i></b-button>
					</center>
				</center>
		</b-container>
	<vue-snotify></vue-snotify>
</div>
</template>

<script>

export default {
	data(){
		return {
			saveDateLogin: false,
			loginAuto: false,
			form: {
				email: '',
				password: '',
				userLang: '',
			},
			formCompany: {
				email: false,
				password: false,
			},
			show: true,
			terms_user: false,
			loginUnknown: false,
			steep: 0,
			languages: this.$store.state.languages,
			subtitleLogin: '',
			titleLogin: '',
            disableChat: Boolean,
		}
	},
	props:{
		company: Object,
		acesso_anonymous: Boolean,
		settings: Array,
	},
	mounted(){
        var showChat = JSON.parse(this.settings[0]['general']).showChat; // obs: esta invertido o nome da variavel nas configurações
        this.disableChat = showChat;


		var userLang = navigator.language || navigator.userLanguage;
		this.$i18n.locale = userLang.replace('-', '_');
		this.form.userLang = 'en_US';
		for (var i = 0; i < this.languages.length; i++) {
			if(this.languages[i].key == this.$i18n.locale){
				this.form.userLang = this.$i18n.locale;
			}
		}

		if(this.settings[0] != undefined){
			if(JSON.parse(this.settings[0].general).titleLogin == '' || JSON.parse(this.settings[0].general).titleLogin == undefined){
				this.titleLogin = this.$t('bs-welcome-to-the') +' '+ this.company.name;
				this.subtitleLogin = this.$t('bs-before-starting-to-login-to-the-page');
			}else{
				this.titleLogin = JSON.parse(this.settings[0].general).titleLogin.replace('{company_name}', this.company.name);
				this.subtitleLogin = JSON.parse(this.settings[0].general).subtitleLogin.replace('{company_name}', this.company.name);
			}
		}else{
			this.titleLogin = this.$t('bs-welcome-to-the') +' '+ this.company.name;
			this.subtitleLogin = this.$t('bs-before-starting-to-login-to-the-page');
		}

		this.loginAuto = localStorage.getItem("loginAuto");
		this.loginAutomatico();
        this.openDirectlyInAModule();
	},
	watch: {
		loginAuto: function (val) {
			localStorage.setItem("loginAuto", this.loginAuto);
		},
		'form.password': function(val) {
			var event = document.createEvent('Event');
			if(this.loginAuto == 'true'){
				if(this.form.password != ''){
					this.onSubmit(event);
				}
			}
		},
	},
	methods:{
		onSubmitSend(evt){
			evt.preventDefault();
			var vm = this;
			axios.post('forgot-password', {
				email: vm.form.email,
				company: vm.company.id,
				language: vm.$i18n.locale
			}).then(function(response){
				if(response.data.success){
					vm.form.email = '';
					vm.form.password = '';
					vm.changepass();
				}else{
					if(response.data.error == 'not_found'){
						vm.$snotify.error(vm.$t('bs-email-not-found'), vm.$t('bs-error'));
					}
				}

			}).catch(function(err){
				console.log(err);
			});
		},
		changepass(){
			this.steep = this.steep+1;
			this.form.email = '';
			this.form.password = '';
		},
		back(){
			this.form.email = '';
			this.form.password = '';
			this.steep = 0;
		},
		clearInput(){
			this.form.email = '';
			this.form.password = '';
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
		loginAutomatico(){
			if(this.loginAuto == 'true'){

				const encrypto_decrypto = require('encrypto-decrypto')
				const encryptoDecrypto = new encrypto_decrypto({
					key: 'someReallyLongStringToUseAsAKey!',
					iv: 'nonceString4Key!'
				})
				const loggin = encryptoDecrypto.encrypt('login');
				var two = localStorage.getItem(loggin);
				if(localStorage.getItem(loggin) == null){
					//naofaznd
				}else{
					two = two.split('_');
					const decrStre = encryptoDecrypto.decrypt(two[0]);
					const decrStrp = encryptoDecrypto.decrypt(two[1]);
					this.formCompany.email = true;
					this.formCompany.password = true;
					this.form.email = decrStre;
					this.form.password = decrStrp;
					//VAI PRO WATCH
				}
			}
		},
		onSubmit(evt) {
			var vm = this;
			evt.preventDefault();
			var url = `/client/${this.company.hash_code}/login`;

			axios.post(url, {
				email: this.form.email,
				password: this.form.password,
				force_login: 1,
				loginUnknown: vm.loginUnknown,
				terms_user: this.terms_user,
				language: this.form.userLang,
				country: this.form.userLang.split('_')[1] ? this.form.userLang.split('_')[1] :'US',
			}).then(function(response){

				const encrypto_decrypto = require('encrypto-decrypto');
				const encryptoDecrypto = new encrypto_decrypto({
					key: 'someReallyLongStringToUseAsAKey!',
					iv: 'nonceString4Key!'
				});
				const encStre = encryptoDecrypto.encrypt(vm.form.email);
				const encStrm = encryptoDecrypto.encrypt(vm.form.password);
				const gg = encryptoDecrypto.encrypt('login');
				localStorage.setItem(gg, encStre+"_"+encStrm);

				if(response.data.success){
					window.location.href = response.data.redir;
				}else{
					vm.$snotify.error(vm.$t('bs-email-or-user-invalid')+'!',  vm.$t('bs-error'));
					vm.form.password = '';
				}
			})
			.catch(function(err){
				console.log(err)
			});

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
	computed: {
		vPassword() {
			this.formCompany.password = this.form.password.length > 0 && this.form.password.length < 100;
			return this.formCompany.password;
		},
		vEmail(){
			var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			this.formCompany.email = re.test(this.form.email);
			return this.formCompany.email;
		},
		invalidForm(){
			return !this.formCompany.email || !this.formCompany.password;
		}
	},
};
</script>

<style scoped lang="scss">

.pass{
	margin-top: 2px;
	text-align: left;
	text-decoration: underline;
	font: normal normal normal 13px/16px Muli;
	letter-spacing: 0px;
	color: #0F7BFF;
	opacity: 0.7;
}

.termtext{
	text-align: center;
	font: normal normal bold 12px/18px Muli;
	letter-spacing: 0px;
	opacity: 1;
}

.bs-text{
	font: normal normal bold 18px/8px Muli;
}

.relative-wrapper{
	position: relative;
	.img-left{
		position: absolute;
		top: 30px;
		left: -35.9442857%;
		height: 115.2263374%;
		min-height: 97,94238679%;
		width: auto;
		z-index: -1;
	}

	.img-right{
		position: absolute;
		bottom: 47px;
		right: -35.9442857%;
		height: 115.2263374%;
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
	top: 25%;
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
	font: normal normal 800 50px/83px Muli;
	letter-spacing: 0px;
	color: #0294FF;
	opacity: 1;
	margin-top: 86px;
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

.bs-input{
	border: 1px solid #a4a4a4;
	padding-top: 20px;
	padding-bottom: 20px;
	padding-left: 20px;
	text-align: left;
	font: normal normal bold 14px/18px Muli;
	letter-spacing: 0px;
	opacity: 0.75;
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
