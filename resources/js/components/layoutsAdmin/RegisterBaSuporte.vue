<template>
	<div>
		<b-container fluid class="">
			<div class="title"><center>{{$t('bs-welcome-to-the-support-system')}}</center></div>
			<div class="subtitle"><center>{{$t('bs-please')}}, {{$t('bs-make-your-registration')}}.</center></div>
			<br>
			<b-form @submit="onSubmit" v-if="show" class="label">
				<center>
					<div class="body col-sm-5 relative-wrapper">
						<img class="img-left" src="/images/man.svg"/>
						<img class="img-right" src="/images/woman.svg"/>
						<div>
							<b-form-group id="input-group-1" :label="name" label-for="input-1">
								<b-form-input
								id="input-1"
								v-model="form.name"
								required
								:placeholder="phName"
								class="bs-input bodersimple"
								:state="vName"
								></b-form-input>
							</b-form-group>
							<b-form-group id="input-group-2" label="E-mail" label-for="input-1">
								<b-form-input
								id="input-2"
								v-model="form.email"
								required
								:placeholder="phEmail"
								class="bs-input bodersimple"
								:state="vEmail"
								></b-form-input>
							</b-form-group>
							<b-form-group id="input-group-3" :label="password" label-for="input-1">
								<b-form-input
								id="input-3"
								v-model="form.password"
								type="password"
								autocomplete="on"
								required
								:placeholder="phPass"
								class="bs-input bodersimple"
								:state="vPassword"
								></b-form-input>
							</b-form-group>
							<b-form-group id="input-group-4" :label="confirPass" label-for="input-1">
								<b-form-input
								id="input-4"
								v-model="form.password2"
								type="password"
								autocomplete="on"
								required
								:placeholder="phConfiPass"
								class="bs-input bodersimple"
								:state="vPassword2"
								>
							</b-form-input>
					</b-form-group>

					<b-form-group id="input-group-5" :label="language" label-for="input-1">
					<v-select
                        style="background-color: #F2F2F2"
                        :clearable="false"
                        :options="languages"
                        label="desc"
                        v-model="form.userLang"
                        :reduce="value => value.key"
                    >
                        <template #selected-option="{key, desc}">
                            <img height="24" class="mx-3" :src="`images/flags/${key}.svg`" alt="">
                            {{desc}}
                        </template>
                        <template #option="{key, desc}">
                            <img height="24" class="mx-2" :src="`images/flags/${key}.svg`" alt="">
                            {{desc}}
                        </template>
                    </v-select>
                    </b-form-group>
					<!-- <b-form-group id="input-group-6" label="Telefone" label-for="input-1">
						<vue-tel-input class="input" label="Telefone" v-model="form.telefone"></vue-tel-input>
					</b-form-group> -->
				</div>
				<b-row>
						<b-col>
							<b-link href="/login" class="link">
								<b-form-group
								id="fieldset-horizontal"
								:description="registered"
								label-for="input-horizontal">
							</b-form-group>
						</b-link>
					</b-col>
				</b-row>
			</div>
			<br><br>
			<center>
				<b-button type="submit" variant="primary" class="btn-next" :disabled="invalidForm">{{$t('bs-register')}} <i class="fa fa-arrow-right" aria-hidden="true"></i></b-button>
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
			phName: this.$t('bs-enter-name'),
			phEmail: this.$t('bs-enter-email'),
			phPass: this.$t('bs-enter-password'),
			phConfiPass: this.$t('bs-confirm-password'),
			name: this.$t('bs-name'),
			registered: this.$t('bs-i-am-already-registered')+'.',
			password: this.$t('bs-password'),
			confirPass: this.$t('bs-confirm-password'),
			language: this.$t('bs-language'),
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
			},
			show: true,
		}
	},
	mounted(){
		//window.navigator.userLanguage || window.navigator.language, pt_BR
		if(window.navigator.language == 'pt-BR'){
			this.form.userLang = 'pt_BR';
		}else if(window.navigator.language == 'en-US'){
			this.form.userLang = 'en_US';
		}
	},
	methods:{
		onSubmit(evt) {
			var vm = this;
			var url = 'register';
			evt.preventDefault();

			if(!vm.invalidForm){

			 	axios.post(url, {
					name: this.form.name,
					email: this.form.email,
					password: this.form.password,
					telefone: this.form.telefone,
					userLang: this.form.userLang,
				}).then(function(response){
					//console.log(response.data);

					if(response.data.success){

						vm.$snotify.success(vm.$t('bs-registration-successfully-complete'), vm.$t('bs-success'));
						window.location.replace("select-company");
					}else{
						if(response.data.value == 'not_email'){
							vm.$snotify.info(vm.$t('bs-email-already-registered')+'!', vm.$t('bs-info'));
							vm.form.password = '';
							vm.form.password2 = '';
						}else{
							vm.$snotify.error(vm.$t('bs-email-not-registered')+'!', vm.$t('bs-error'));
							vm.form.password = '';
							vm.form.password2 = '';
						}
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
	},
	computed: {
		vName() {
			this.formCompany.name = this.form.name.length > 2 && this.form.name.length < 100;
			return this.formCompany.name;
		},
		vEmail(){
			var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			this.formCompany.email = re.test(this.form.email);
			return this.formCompany.email;
		},
		vPassword(){
			this.formCompany.password = this.form.password.length > 0 && this.form.password.length < 20;
			return this.formCompany.password;
		},
		vPassword2(){
			this.formCompany.password2 = this.form.password == this.form.password2;
			return this.formCompany.password2;
		},
		invalidForm(){
			return !(this.formCompany.name && this.formCompany.email && this.formCompany.password && this.formCompany.password2 && this.form.userLang != '');
		}
	},
};
</script>

<style scoped lang="scss">
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
