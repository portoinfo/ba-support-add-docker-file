<template>
	<div id="tela" data-id="tela">
		<b-container v-if="steep == 0" fluid class="">
			<div v-if="company_user == 0">
				<div class="title"><center>Verificação de acesso</center></div>
				<div class="subtitle"><center>Para vincular ao telegram, {{$t('bs-login-to-the-page')}}</center></div>
				<br>
				<b-form @submit="onSubmit" v-if="show" class="label" autocomplete="new-password">
					<center>
						<div class="body col-sm-5 relative-wrapper">
							<img class="img-left" src="/images/man.svg"/>
							<img class="img-right" src="/images/woman.svg"/>
							<div>
								<b-form-group id="input-group-1" label="E-mail:" label-for="input-1">
									<b-form-input
										id="input-1"
										v-model="form.email"
										type="email"
										:placeholder="phEmail"
										disabled="disabled"
										class="bs-input"
										:state="vEmail"
										required
									></b-form-input>
								</b-form-group>
								<b-form-group id="input-group-2" :label="passwordd" label-for="input-2">
									<b-form-input
										id="input-2"
										v-model="form.password"
										type="password"
										autocomplete="on"
										required
										:placeholder="phPass"
										class="bs-input"
										:state="vPassword"
									></b-form-input>

									<b-row>
										<!-- <b-col>
											<b-link href="register" class="link">
												<b-form-group
													id="fieldset-horizontal"
													:description="description"
													label-for="input-horizontal"
												></b-form-group>
											</b-link>
										</b-col> -->
										<b-col>
											<!-- <b-link href="#faa" class="link">
												<b-form-group class="linkrigth"
													description="Esqueceu a sua senha?">
												</b-form-group>
											</b-link> -->
										</b-col>
									</b-row>

									<div class="mx-auto mt-3">
										<b-form-group id="input-3">
											<b-row>
												<b-col  cols="auto">
													<!-- <a href="#" @click="changepass">{{$t('bs-forgot-your-password')}}</a> -->
												</b-col>
													
												<b-col>
													<!-- <b-form-checkbox v-model="saveDateLogin">Salvar data do login</b-form-checkbox> -->
												</b-col>

												<b-col>
													
												</b-col>

												<b-col cols="auto">
											
													<!-- <b-form-checkbox v-model="loginAuto">{{$t('bs-auto-login')}}</b-form-checkbox> -->
												</b-col>
											</b-row>
										</b-form-group>
									</div>
								</b-form-group>
							</div>
						</div>
						<br>
						<center>
							<!-- :disabled="invalidForm" -->
							<b-button type="submit" id="buttom22" data-id="buttom22" variant="primary" class="btn-next" > {{$t('bs-next')}} <i class="fa fa-arrow-right" aria-hidden="true"></i></b-button>
						</center>
					</center>
				</b-form>
			</div>
			<div v-else>
				<div class="title"><center>Email já vinculado</center></div>
			</div>
		</b-container>
        <b-container v-if="steep == 1" fluid class="">
            <center>
                <div class="title"><center>Selecione uma companhia</center></div>
                <div class="body col-sm-5 relative-wrapper">
                    <b-row>
                        <b-col cols="auto"  v-for="(item, index) in resultQuery" :key="index">
                            <div class="mb-2 companycard stylecompany">
                                <span><b-badge variant="secondary">{{$t('bs-attendant')}}</b-badge></span><br>
                                <img
                                    @click="selectCompany(item)"
                                    class="caret mt-2"
                                    :src="companyImg(item.logo)"
                                    :alt="item.name"
                                    width="180"
                                    height="180"
                                    @error="setAltImg"
                                >
                                <hr>
                                <center class="text-center mt-3">{{item.name}}</center>
                            </div>
                        </b-col>
                    </b-row>
                </div>
            </center>
		</b-container>
        <b-container v-if="steep == 2" fluid class="">
			<div class="title"><center>Email vinculado com sucesso!</center></div>
				<center>
					<div>
						<img class="i-left" src="/images/man.svg"/>
						<img class="i-right" src="/images/woman.svg"/>
					</div>
					<div class="col-sm-5 relative-wrapper">
						<b-form-group id="input-group-1" label-for="input-1">
							<center>
                                Seu email foi vinculado com sucesso com o telegram, 
                                agora poderá configurar os alerta do telegram atraves do seu perfil no sistema
                            </center>
						</b-form-group>
					</div>
					<br>
					<center>
						<a href="https://web.telegram.org/"><img src="https://telegram.org/img/t_logo.svg" alt="telegram" width="150px"></a>
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
			phName: "",
			phConfiPass: "",
			phEmail: "",
			phPass: "",
			description: "",
			passwordd: "",
			loginAuto: false,
			form: {
				email: '',
				password: '',
			},
			formCompany: {
				email: false,
				password: false,
			},
			show: true,
			steep: 0,
			languages: this.$store.state.languages,
            resultQuery: [],
		}
	},
    props:{
        getuser: String,
        token: String,
		company_user: String,
    },
	mounted(){
        this.form.email = JSON.parse(this.getuser)[0].email;
		// AGORA SETTAR NO SISTEMA -
		//fazer a logica ainda -

		var userLang = navigator.language || navigator.userLanguage;
		this.$i18n.locale = userLang.replace('-', '_');
		this.form.userLang = 'en_US';
		for (var i = 0; i < this.languages.length; i++) {
			if(this.languages[i].key == this.$i18n.locale){
				this.form.userLang = this.$i18n.locale;
			}
		}

		this.phName = this.$t('bs-enter-name')+':';
		this.phConfiPass = this.$t('bs-bs-confirm-password')+':';
		this.phEmail = this.$t('bs-enter-email');
		this.phPass = this.$t('bs-password');
		this.description = this.$t('bs-register')+':';
		this.passwordd = this.$t('bs-password')+':';

		this.loginAuto = localStorage.getItem("loginAuto");

		this.loginAutomatico();
	},
	watch: {
		loginAuto: function (val) {
			if(this.loginAuto){
				var event = document.createEvent('Event');
				if(this.loginAuto == 'true'|| this.loginAuto == true){

					if(this.form.password != ''){
						this.onSubmit(event);
					}
				}
				localStorage.setItem("loginAuto", this.loginAuto);
			}
		},
	},
	methods:{
        selectCompany(item){
		    var vm = this;
			var url = `/login/telegram`;
			vm.$loading(true);
			axios.post(url, {
				type: 'save',
                token: vm.token,
                item: item,
			}).then(function(response){
				//console.log(response.data.success);
				if(response.data.success){
                    vm.steep = 2;
				}
				vm.$loading(false);
			})
			.catch(function(E){
                // console.log(E);
				console.log('FAILURE!!');
				vm.$loading(false);
			});
        },
        companyImg(link){
			if(link == null){
				link = "";
			}
			return link;
		},
        setAltImg(event){
			event.target.src = 'https://ba-support.builderall.io/images/icons/companhia.svg';
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
		onSubmitSend(evt){
			evt.preventDefault();
			var vm = this;
			axios.post('forgot-password', {
				email: vm.form.email,
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
		onSubmit(evt) {
			var vm = this;
			var url = '/login/telegram';
			evt.preventDefault();
			vm.$loading(true);
			if(vm.formCompany.email && vm.formCompany.password ){
				// alert(JSON.stringify(this.form));

				axios.post(url, {
					email: vm.form.email,
					password: vm.form.password,
                    token: vm.token,
                    type: 'check',
				}).then(function(response){
					// console.log(response.data);
					if(response.data.success){
                        if(response.data.type == 'finish'){
                            vm.steep = 2;
                        }else{
                            vm.steep = 1;
                            vm.resultQuery = response.data.value;
                        }
						vm.$loading(false);
					}else{
						if(response.data.value == 'not_email'){
							vm.$snotify.error(vm.$t('bs-email-or-user-invalid'), vm.$t('bs-error'));
							vm.form.password = '';
						}else{
							vm.$snotify.error(vm.$t('bs-email-or-user-invalid'), vm.$t('bs-error'));
							vm.form.password = '';
						}
						vm.$loading(false);
					}

				})
				.catch(function(err){
					console.log(err);
					vm.$loading(false);
				});

			}else{
				vm.$snotify.info(vm.$t('bs-check-fields-invalid'), vm.$t('bs-info'));
			}
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

.caret {
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.stylecompany{
	max-width: 14rem;
	min-width: 14rem;
	min-height: 18rem;
	background-color: white;
}

.companycard{
	box-shadow: 0px 3px 6px #00000029;
	font: normal normal 600 18px/17px Muli;
	letter-spacing: 0px;
	color: #333333;
	opacity: 1;
	padding: 20px;
	padding-bottom: 0px;
	margin: 11px;
}

.pass{
	margin-top: 2px;
	text-align: left;
	text-decoration: underline;
	font: normal normal normal 13px/16px Muli;
	letter-spacing: 0px;
	color: #0F7BFF;
	opacity: 0.7;
}

.i-left{
	position: absolute;
	left: 10%;
}

.i-right{
	position: absolute;
	right: 10%;
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

	.pass{
		margin-top: 10px;
		margin-bottom: 10px;
		text-align: left;
		text-decoration: underline;
		font: normal normal normal 13px/16px Muli;
		letter-spacing: 0px;
		color: #0F7BFF;
		opacity: 0.7;
	}
	
	.i-left{
		margin-top: 100px;
		position: absolute;
		left: 5%;
		width: 250px;
		z-index: -1;
	}

	.i-right{
		margin-top: 100px;
		position: absolute;
		right: 5%;
		width: 250px;
		z-index: -1;
	}
}
@media screen and (max-width: 907px) {
	.relative-wrapper{
		.img-left,
		.img-right{
			display: none;
		}
	}

	.i-left{
		margin-top: 150px;
		position: absolute;
		left: 5%;
		width: 100px;
		z-index: -1;
	}

	.i-right{
		margin-top: 150px;
		position: absolute;
		right: 5%;
		width: 100px;
		z-index: -1;
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
	max-width: fit-content;
	min-width: 500px;
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
        max-width: fit-content;
        min-width: 500px;
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
