<template>
	<div>
		<b-container fluid="lg">
			<b-row>
				<b-col cols="auto" class="mr-auto p-3 bs-title">{{$t('bs-attendant-registration')}}
					<b-card-text class="bs-subtitle">
						{{$t('bs-register-a-new-attendant')}}
					</b-card-text>
				</b-col>
				<b-col cols="auto mt-4">
					<b-button @click="btnBack" variant="light bs-btn-back">{{$t('bs-back')}}</b-button>
					<b-button @click="saveUser" variant="btn bs-btn-save"><i class="fa fa-floppy-o" aria-hidden="true"></i> {{$t('bs-save')}}</b-button>
				</b-col>
			</b-row>
			<br><br>
			<div class="body col-lg-8">
				<b-form-group
				id="input-group-31"
				:label="lbEmail"
				label-for="input-31"
				:description="messageemail"
				>
				<b-form-input
					id="input-31"
					v-model="emailFun"
					@change="emailFunEvent"
					type="email"
					placeholder="Enter email"
					required
					class="bs-input"
					:state="vEmail"
				></b-form-input>
				</b-form-group>
				<div class="text-right">
					<b-button v-show="showBtEmail" @click="checkEmail" variant="primary">{{$t('bs-check')}}</b-button>
				</div>
				<div class="mt-2" v-if="showRegister">
					<b-form v-if="show" class="bs-label">
						<b-form-group class="bs-label" id="input-group-1" :label="lbName" label-for="input-1">
							<b-form-input
							id="input-1"
							v-model="nameFun"
							required
							:placeholder="phName"
							class="bs-input"
							:state="vName"
							></b-form-input>
						</b-form-group>
					</b-form>
					<b-form-group class="bs-label" id="input-group-2" :label="lbPassword" label-for="input-2">
						<b-form-input
						id="input-2"
						v-model="passwordFun"
						type="password"
						required
						:placeholder="phPassword"
						class="bs-input"
						:state="vPassword"
						></b-form-input>
					</b-form-group>
				</div>
				<div class="text-right">
					<b-button v-show="!showBtEmail" @click="saveUser" variant="btn bs-btn-save">{{$t('bs-register')}}</b-button>
				</div>
			</div>
			<vue-snotify></vue-snotify>
		</b-container>
	</div>
</template>

<script>

	export default {
		data(){
			return {
				messageemail: '',
				showRegister: false,
				showBtEmail: true,
				phName: this.$t('bs-enter-name'),
				phPassword: this.$t('bs-enter-password'),
				lbName: this.$t('bs-name')+':',
				lbEmail: 'Email:',
				lbPassword: this.$t('bs-password')+':',
				show: true,
				nameFun: "",
				emailFun: "",
				passwordFun: "",
				formCompany: {
					nameFun: false,
					emailFun: false,
					passwordFun: false,
				},
			}
		},
		props:{
			csid: String,
			base_url: {
				type: String,
				default: ''
			},
		},
		methods:{
			saveUser(evt){
				var url = `${this.base_url}/agents`;
				var vm = this;
				evt.preventDefault();
				if(this.formCompany.nameFun && this.formCompany.emailFun && this.formCompany.passwordFun){

					axios.post(url, {
						csid: vm.csid,
						name: vm.nameFun,
						email: vm.emailFun,
						password: vm.passwordFun,
					}).then(function(response){
					//console.log(response.data.created);
					if(response.data.success){
						var my_object = {
							id: response.data.id,
							attendants: vm.nameFun,
							email: vm.emailFun,
							password: vm.password,
							is_active: 1,
							company_user_id: response.data.company_user_id,
							created_at: response.data.created,
						};

						vm.$emit('save', my_object);
						vm.$snotify.success(vm.$t('bs-attendant-saved-successfully'), vm.$t('bs-success'));
					}else{
						if(response.data.type == 'email'){
							vm.$snotify.info(vm.$t('bs-email-already-registered	'), vm.$t('bs-info'));
						}else{
							if(response.data.value == 'is_exist'){
								vm.$snotify.info(vm.$t('bs-email-already-registered	'), vm.$t('bs-info'));
							}else{
								vm.$snotify.error(vm.$t('bs-error-trying-to-save-attendant'), vm.$t('bs-error'));
							}
						}
					}
				}).catch(function(){
					console.log('FAILURE!!');
				});
			}else{
				vm.$snotify.info( vm.$t('bs-please-enter-the-information'), vm.$t('bs-invalid-input'));
			}
		},
		btnBack(){
			var vm = this;
			vm.$emit('back');
		},
		checkEmail(){
			var vm = this;


			if(vm.emailFun == '' || this.formCompany.emailFun == false){
				vm.$snotify.info( vm.$t('bs-please-enter-the-information'), vm.$t('bs-invalid-input'));
				return;
			}

			axios.post(`${this.base_url}/department/isValidEmail`, {
				email: vm.emailFun,
			}).then(function(response){
				if(response.data.success){
					vm.showRegister = !response.data.success;
					vm.showBtEmail = false;
					vm.$snotify.info(vm.$t('bs-email-already-registered'), vm.$t('bs-info'));
					vm.messageemail = vm.$t('bs-email-already-registered')+' ('+vm.$t('bs-you-could-just-link-the-account')+')';
					vm.formCompany.nameFun = true;
					vm.formCompany.passwordFun = true;
					vm.nameFun = response.data.value;
				}else{
					vm.messageemail = vm.$t('bs-unregistered-email');
					vm.showRegister = !response.data.success;
					vm.showBtEmail = true;
					vm.showBtEmail = false;
					vm.formCompany.nameFun = false;
					vm.formCompany.passwordFun = false;
					vm.nameFun = '';
					vm.passwordFun = '';
				}

			}).catch(function(){
				console.log('FAILURE!!');
			});
		},
		emailFunEvent(){
			this.showRegister = false;
			this.showBtEmail = true;
			this.formCompany.nameFun = false;
			this.formCompany.passwordFun = false;
		}
	},
	computed: {
		vName() {
			this.formCompany.nameFun = this.nameFun.length > 1 && this.nameFun.length < 100;
			return this.formCompany.nameFun;
		},
		vEmail(){
			var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			this.formCompany.emailFun = re.test(this.emailFun);
			return this.formCompany.emailFun;
		},
		vPassword() {
			this.formCompany.passwordFun = this.passwordFun.length > 8 && this.passwordFun.length < 100;
			return this.formCompany.passwordFun;
		},
	}
};
</script>

<style scoped="scss">

.body{
	background: #FFFFFF 0% 0% no-repeat padding-box;
	border: 1px solid #DEE3EA;
	border-radius: 5px 5px 0px 0px;
	opacity: 1;
	padding: 25px;
}

</style>
