<template>
	<div id="tela" data-id="tela">
		<b-container v-if="steep == 0" fluid class="">
			<div class="title"><center>{{$t('bs-enter-new-password')}}</center></div>
			<b-form @submit="onSubmit" v-if="show" class="label" autocomplete="new-password">
				<center>
					<div class="body col-sm-5 relative-wrapper">
						<img class="img-left" src="/images/man.svg"/>
						<img class="img-right" src="/images/woman.svg"/>
						<div>
							<b-form-group id="input-group-1" :label="newpassword" label-for="input-1">
								<b-form-input
									id="input-3"
									v-model="form.pass"
									type="password"
									:placeholder="phPass"
									class="bs-input"
									:state="vPassword"
									autocomplete="off"
									required
								></b-form-input>
							</b-form-group>
							<b-form-group id="input-group-2" :label="repeatpassword" label-for="input-2">
								<b-form-input
									id="input-2"
									v-model="form.confirm"
									type="password"
									:placeholder="phConfiPass"
									class="bs-input"
									:state="vPassword2"
									autocomplete="off"
									required
								></b-form-input>
							</b-form-group>
						</div>
					</div>
					<br>
					<center>
						<!-- :disabled="invalidForm" -->
						<b-button type="submit" id="buttom22" data-id="buttom22" variant="primary" class="btn-next" > {{$t('bs-redefine-password')}} </b-button>
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
			phName: "",
			phConfiPass: "",
			phEmail: "",
			phPass: "",
			repeatpassword: "",
			newpassword: "",
			description: "",
			passwordd: "",
			loginAuto: false,
			form: {
				pass: '',
				confirm: '',
			},
			formCompany: {
				pass: false,
				confirm: false,
			},
			show: true,
			steep: 0,
		}
	},
	props:{
		result: String,
	},
	created(){
		// console.log(this.result);
	},
	mounted(){
		var userLang = navigator.language || navigator.userLanguage;
		this.$i18n.locale = userLang.replace('-', '_');
		this.phName = this.$t('bs-enter-name')+':';
		this.phConfiPass = this.$t('bs-confirm-password')+':';
		this.phPass = this.$t('bs-password');
		this.repeatpassword = this.$t('bs-repeat-password')+':';
		this.newpassword = this.$t('bs-new-password')+':';
	},
	methods:{
		onSubmit(evt) {
			evt.preventDefault();

			axios.post('/save-change-password', {
				password: this.form.pass,
				token: this.result,
			}).then(function(response){
				if(response.data.success){
					window.location.href = response.data.redirect;
				}
			}).catch(function(){
				console.log('FAILURE!!');
			});

		},
	},
	computed: {
		vPassword() {
			this.formCompany.pass = this.form.pass.length >= 8 && this.form.pass.length < 100;
			return this.formCompany.pass;
		},
		vPassword2(){
			this.formCompany.confirm = this.form.pass == this.form.confirm;
			return this.formCompany.confirm;
		},
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
