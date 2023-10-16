<template>
	<div>
		<b-container fluid="lg">
			<b-row>
				<b-col cols="auto" class="mr-auto p-3 title">{{$t('bs-register-new-group')}}
					<b-card-text class="subtitle">
						{{$t('bs-register-a-new-group')}}
					</b-card-text>
				</b-col>
				<b-col cols="auto mt-4">
					<b-button @click="btnBack" variant="light btn-back">{{$t('bs-back')}}</b-button>
					<b-button @click="saveGroup" variant="btn btn-save" class="btn btn-success"><i class="fa fa-floppy-o" aria-hidden="true"></i> {{$t('bs-save')}}</b-button>
				</b-col>
			</b-row>
			<br>

			<div class="body col-lg-8">
				<div>
					<b-form v-if="show" class="label">

						<b-form-group id="input-group-1" :label="lbName" label-for="input-2">
							<b-form-input
							id="input-1"
							v-model="name"
							required
							:placeholder="phName"
							class="bs-input"
							:state="vName"
							></b-form-input>
						</b-form-group>

						<b-form-group id="input-group-4" :label="lbDescricao" label-for="input-1">
							<b-form-textarea
							id="textarea"
							v-model="description"
							placeholder="..."
							class="bs-input"
							:state="vDescription"
							rows="3"
							></b-form-textarea>
						</b-form-group>
					</b-form>
				</div>
			</div>
			<vue-snotify></vue-snotify>
		</b-container>
	</div>
</template>

<script>

export default {
	props: {
		base_url: {
			type: String,
			default: ''
		},
	},
	data(){
		return {
			name: "",
			description: "",
			lbName: this.$t('bs-name'),
			lbDescricao: this.$t('bs-description'),
			phName: this.$t('bs-enter-name'),
			formCompany: {
				name: false,
				description: false,
			},
			show: true,
		}
	},	
	methods:{
		saveGroup(){
			var url = `${this.base_url}/group`;
			var vm = this;

			if(vm.name == "" || vm.description == ""){
				vm.$snotify.info( vm.$t('bs-empty-fields'), vm.$t('bs-info'));
				return;
			}
			
			axios.post(url, {
				name: vm.name,
				description: vm.description,
			}).then(function(response){
				//console.log(response.data);

				if(response.data.success){
					var my_object = {
						id: response.data.id,
						name: vm.name,
						description: vm.description,
						settings: response.data.settings,
						is_active: 1,
					};

					vm.$emit('save', my_object);

					vm.name = "";
					vm.description = "";

					vm.$snotify.success(vm.$t('bs-group-successfully-created'), vm.$t('bs-success'));
				}else{
					
					vm.$snotify.error(vm.$t('bs-error-trying-to-create-group'), vm.$t('bs-error'));
				}

			})
			.catch(function(){
				console.log('FAILURE!!');
			});
		},
		btnBack(){
			var vm = this;
			vm.$emit('back');
		},
	},
	computed: {
		vName() {
			this.formCompany.name = this.name.length > 1 && this.name.length < 100;
			return this.formCompany.name;
		},
		vDescription(){
			this.formCompany.description = this.description.length > 1 && this.description.length < 250;
			return this.formCompany.description;
		},
	}
};

</script>

<style scoped>
.title{
	text-align: left;
	font: normal normal 800 25px/31px Muli;
	letter-spacing: 0px;
	color: #0080FC;
	opacity: 1;
}

.subtitle{
	text-align: left;
	font: normal normal 800 15px/16px Muli;
	letter-spacing: 0.45px;
	color: #434343;
	opacity: 0.5;
}

.body{
	background: #FFFFFF 0% 0% no-repeat padding-box;
	border: 1px solid #DEE3EA;
	border-radius: 5px 5px 0px 0px;
	opacity: 1;
	padding: 25px;
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
	padding-top: 18px;
	padding-bottom: 18px;
	padding-left: 20px;
	text-align: left;
	font: normal normal bold 14px/18px Muli;
	letter-spacing: 0px;
	opacity: 0.75;
}
</style>