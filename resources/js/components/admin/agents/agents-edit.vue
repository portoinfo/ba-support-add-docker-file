<template>
	<div>
		<b-row>
			<b-col cols="auto" class="mr-auto p-3 bs-title">{{$t('bs-edit-attendant')}}
				<b-card-text class="bs-subtitle">
					{{$t('bs-edit-attendant')}} {{itemselected.attendants}}
				</b-card-text></b-col>
				<b-col 
				cols="auto mt-4">
				<span v-if="typeEditorRegister == 'edit'">
					<b-button @click="btnBack" variant="light bs-btn-back">{{$t('bs-back')}}</b-button>
				</span>
				
				<b-button @click="updateAgent" class="btn btn-success" ><i class="fa fa-floppy-o" aria-hidden="true"></i> {{$t('bs-save')}}</b-button>
			</b-col>
		</b-row><br>
		<span v-if="typeEditorRegister == 'register'">

			<b-alert
				fade
				show variant="warning"
				class="bui-alert"
				>
				<slot>
				<h5>
					{{$t('bs-attendant')}}
				</h5>
					<p>
						{{$t('bs-after-registration-the-attendant-must-be')}}<br>
					</p>
				</slot>
			</b-alert>

		</span>
		<div class="body">
			<div>
				<b-form v-if="show" class="label">
					<b-row>
						<b-col>
							<b-form-group class="bs-label" id="input-group-1" :label="lbName" label-for="input-1">
								<b-form-input
								id="input-1"
								v-model="name"
								required
								:disabled="is_admin == '1' ? false : true "
								:placeholder="phName"
								class="bs-input"
								:state="vName"
								></b-form-input>
							</b-form-group>
						</b-col>
					</b-row>
					<b-row>
						<b-col>
							<b-form-group class="bs-label" id="input-group-2" :label="lbEmail" label-for="input-2">
								<b-form-input
								id="input-2"
								v-model="email"
								required
								:placeholder="phEmail"
								class="bs-input"
								:state="vEmail"
								disabled
								></b-form-input>
							</b-form-group>
						</b-col>
					</b-row>
<!-- 					<b-row>
						<b-col>
							<b-form-group class="bs-label" id="input-group-3" :label="lbPassword" label-for="input-3">
								<b-form-input
								id="input-3"
								v-model="password"
								type="password"
								required
								autocomplete="on"
								:placeholder="phPassword"
								:state="vPassword"
								class="bs-input"
								></b-form-input>
							</b-form-group>
							<b-form-group class="bs-label" id="input-group-4" :label="lbConfimPassword" label-for="input-4">
								<b-form-input
								id="input-4"
								v-model="password2"
								type="password"
								autocomplete="on"
								required
								:placeholder="phConfimPassword"
								:state="vPassword2"
								class="bs-input"
								></b-form-input>
							</b-form-group>
						</b-col>
					</b-row> -->
					<br>
					<b-row>
						<b-col>
							<b-form-group class="bs-label mb-2" id="input-group-5" label-for="input-5">
								<span class="mb-2">
									{{lbGroups}} 
									- <b-badge class="caret" @click="reditect('group')" variant="success">{{$t('bs-register-a-new-group')}}</b-badge>
									- <b-link @click="BuscarDados" href="#">{{$t('bs-update')}}</b-link>
								</span>
							    <b-form-select class="mt-2" v-model="selectedGroup" :options="group" multiple :select-size="6">
									<template #first>
										<b-form-select-option :value="null" disabled>-- {{$t('bs-please-select-an-option')}} --</b-form-select-option>
									</template>
								</b-form-select>
							</b-form-group>
						</b-col>
					</b-row>
					<br>
					<b-row>
						<b-col>
							<b-form-group class="bs-label" id="input-group-6" label-for="input-6">
								<span class="mb-2">
									{{lbDepartments}} 
									- <b-badge class="caret" @click="reditect('department')" variant="success">{{$t('bs-register-a-new-department')}}</b-badge>
									- <b-link @click="BuscarDados" href="#">{{$t('bs-update')}}</b-link>
								</span>
							    <b-form-select class="mt-2" v-model="selectedDepartment" :options="department" multiple :select-size="6">
									<template #first>
										<b-form-select-option :value="null" disabled>-- {{$t('bs-please-select-an-option')}} --</b-form-select-option>
									</template>
								</b-form-select>
							</b-form-group>
						</b-col>
					</b-row>
					<b-row class="mt-2">
						<b-col cols="4" style="padding-top:30px;">
							<span class="bs-label">{{ $t('bs-working') }}</span>
						</b-col>
						<b-col>
							<b-form-select 
								v-model="workingStatus" 
								:options="optionsWS" 
								size="sm" class="mt-3"
							>
							</b-form-select>
						</b-col>
					</b-row>
					<b-row class="mt-2">
						<b-col cols="4" style="padding-top:14px;">
							<span class="bs-label">{{ $t('bs-region') }}: {{ convertTimezoneToName(timezoneSelected) }}</span>
						</b-col>
						<b-col>
								<base-timezone-selector
								:timezones="timezone"
								v-model="timezoneSelected"
								:searchDisable="true"
								></base-timezone-selector>
						</b-col>
					</b-row>
					<b-row v-for="(day, index) in daysOfWeek" :key="index" class="mt-3">
						<b-col style="margin-top:33px;width: 102px;">
							<b>{{ $t(day) }}:</b>
						</b-col>
						<b-col cols="auto">
							<b-form class="bs-label">
								<b-form-group
									:id="'input-group1-'+index"
									:label="$t('bs-entry')+' '+1"
									:label-for="'input1-'+index"
								>
								<!-- :class="day.valid ? 'bs-input is-valid' : 'bs-input is-invalid'" -->
								<b-form-input
									:id="'input-group1-'+index"
									class='bs-input is-valid'
									v-model=" hoursPerDay[index].entry1"
									type="time"
									></b-form-input>
									</b-form-group>
								</b-form>
						</b-col>
						<b-col cols="auto">
							<b-form class="bs-label">
								<b-form-group
									:id="'input-group2-'+index"
									:label="$t('bs-output')+' '+1"
									:label-for="'input2-'+index"
								>
								<b-form-input
									:id="'input-group2-'+index"
									class='bs-input is-valid'
									v-model="hoursPerDay[index].exit1"
									type="time"
									></b-form-input>
								</b-form-group>
							</b-form>
						</b-col>
						<b-col cols="auto">
							<b-form class="bs-label">
								<b-form-group
									:id="'input-group3-'+index"
									:label="$t('bs-entry')+' '+2"
									:label-for="'input3-'+index"
								>
								<b-form-input
									:id="'input-group3-'+index"
									class='bs-input is-valid'
									v-model="hoursPerDay[index].entry2"
									type="time"
									></b-form-input>
								</b-form-group>
							</b-form>
						</b-col>
						<b-col cols="auto">
							<b-form class="bs-label">
								<b-form-group
									:id="'input-group4-'+index"
									:label="$t('bs-output')+' '+2"
									:label-for="'input4-'+index"
								>
								<b-form-input
									:id="'input-group4-'+index"
									class='bs-input is-valid'
									v-model="hoursPerDay[index].exit2"
									type="time"
									></b-form-input>
								</b-form-group>
							</b-form>
						</b-col>
					</b-row>
				</b-form>
			</div>
		</div>
		<vue-snotify></vue-snotify><br>
	</div>
</template>

<script>

export default {
	data(){
		return {
			lbName: this.$t('bs-name'),
			lbEmail: 'Email',
			lbPassword: this.$t('bs-password'),
			lbConfimPassword: this.$t('bs-confirm-password'),
			lbGroups: this.$t('bs-groups'),
			lbDepartments: this.$t('bs-departments'),
			phName: this.$t('bs-enter-name'),
			phEmail: this.$t('bs-enter-email'),
			phPassword: this.$t('bs-enter-password'),
			phConfimPassword: this.$t('bs-enter-confirmation-of-new-password'),
			show: true,
			name: this.itemselected.attendants,
			email: this.itemselected.email,
			password: "",
			password2: "",
			groupType: "",
			formCompany: {
				name: false,
				email: false,
				password: false,
				password2: false,
			},
			group: [],
			department: [],
			selectedGroup: [],
			selectedDepartment: [],
			originsGroup: [],
			originsDepartment: [],
			daysOfWeek: [this.$t('bs-sunday'), this.$t('bs-monday'), this.$t('bs-tuesday'), this.$t('bs-wednesday'), this.$t('bs-thursday'), this.$t('bs-friday'), this.$t('bs-saturday')],
            hoursPerDay: [
				{entry1: '09:00',exit1: '12:00',entry2: '13:00',exit2: '18:00'},
				{entry1: '09:00',exit1: '12:00',entry2: '13:00',exit2: '18:00'},
				{entry1: '09:00',exit1: '12:00',entry2: '13:00',exit2: '18:00'},
				{entry1: '09:00',exit1: '12:00',entry2: '13:00',exit2: '18:00'},
				{entry1: '09:00',exit1: '12:00',entry2: '13:00',exit2: '18:00'},
				{entry1: '09:00',exit1: '12:00',entry2: '13:00',exit2: '18:00'},
				{entry1: '09:00',exit1: '12:00',entry2: '13:00',exit2: '18:00'},
			],
            workingStatus: true,
            timezone: {},
            timezoneSelected: '',
			optionsWS: [
				{ value: true , text: this.$t('bs-active') },
				{ value: false , text: this.$t('bs-disabled') },
			],
		}
	},
	props:{
		itemselected: Object,
		typeEditorRegister: String,
        base_url: {
            type: String,
            default: ''
        },
		is_admin: "",
	},
	created(){
		this.BuscarDados();
		this.getSchedule();
	},
	methods:{
		convertTimezoneToName(keyselected) {

			if(keyselected == '' || keyselected == null || keyselected == undefined){
				return keyselected;
			}
			console.log(keyselected);
			const value = [
				...Object.values(this.timezone['Africa']),
				...Object.values(this.timezone['America']),
				...Object.values(this.timezone['Asia']),
				...Object.values(this.timezone['Atlantic']),
				...Object.values(this.timezone['Australia']),
				...Object.values(this.timezone['Europe']),
				...Object.values(this.timezone['Pacific']),
				...Object.values(this.timezone['US/Canada']),
				...Object.values(this.timezone['UTC']),
			];
			const keys = [
				...Object.keys(this.timezone['Africa']),
				...Object.keys(this.timezone['America']),
				...Object.keys(this.timezone['Asia']),
				...Object.keys(this.timezone['Atlantic']),
				...Object.keys(this.timezone['Australia']),
				...Object.keys(this.timezone['Europe']),
				...Object.keys(this.timezone['Pacific']),
				...Object.keys(this.timezone['US/Canada']),
				...Object.keys(this.timezone['UTC']),
			];
			const posicaoEncontrada = keys.indexOf(keyselected);
			return value[posicaoEncontrada];
		},
		getSchedule(){
            axios.get("/department/get-schedule", {
				params:{
					company_user_id: this.itemselected.company_user_id,
				}
            }).then(({ data }) => {
                this.timezone = JSON.parse(data[0]);
				//SCHEDULE
				if(data[1].opening_hours == null){
					this.hoursPerDay = [
						{entry1: '09:00',exit1: '12:00',entry2: '13:00',exit2: '18:00'},
						{entry1: '09:00',exit1: '12:00',entry2: '13:00',exit2: '18:00'},
						{entry1: '09:00',exit1: '12:00',entry2: '13:00',exit2: '18:00'},
						{entry1: '09:00',exit1: '12:00',entry2: '13:00',exit2: '18:00'},
						{entry1: '09:00',exit1: '12:00',entry2: '13:00',exit2: '18:00'},
						{entry1: '09:00',exit1: '12:00',entry2: '13:00',exit2: '18:00'},
						{entry1: '09:00',exit1: '12:00',entry2: '13:00',exit2: '18:00'},
					];
				}else{
					this.hoursPerDay = data[1].opening_hours;
				}

                //IS ACTIVE
                this.workingStatus = data[1].is_working ? true : false;
				
				//TIMEZONE
				if(data[1].time_zone == null){
					this.timezoneSelected = Intl.DateTimeFormat().resolvedOptions().timeZone;
				}else{
					this.timezoneSelected = data[1].time_zone;	
				}
            });
        },
		reditect(name){
			if(name == 'group'){
				window.open('/'+name+'?new='+name, '_blank');
			}else{
				window.open('/department/register-new-department?new='+name, '_blank');
			}	
		},
		BuscarDados(){
			var vm = this;
			vm.group = [];
			vm.department = [];
			var url = `${this.base_url}/agents/get-group-agents/`;
			axios.get(url, {
				params: {
					item: vm.itemselected,
				}
			}).then(function(r_resposta){

				for(var i=0; i < r_resposta.data.result.length; i++){
					r_resposta.data.result[i].text = vm.$t(r_resposta.data.result[i].text); 
				}
				
				vm.group = r_resposta.data.result;
				vm.selectedGroup = r_resposta.data.select;
				vm.originsGroup = r_resposta.data.select;

			}).catch(function (error) {
				console.log(error);
				vm.$snotify.error(vm.$t('bs-error-loading-groups'), 'Error!');
			});
			//console.log(vm.itemselected)
			var url2 = `${this.base_url}/agents/get-department-agents/${vm.itemselected.id}`;
			axios.get(url2, {
				params: {
					company_user_id: vm.itemselected.company_user_id,
				}
			}).then(function(r_resposta){

				// console.log(r_resposta.data);
				for(var i=0; i < r_resposta.data.result.length; i++){
					r_resposta.data.result[i].text = vm.$t(r_resposta.data.result[i].text); 
				}

				vm.department = r_resposta.data.result;
				vm.selectedDepartment = r_resposta.data.select;
				vm.originsDepartment = r_resposta.data.select;

			}).catch(function (error) {
				vm.$snotify.error(vm.$t('bs-error-loading-departments'), 'Error!');
				console.log(error);
			});
		},
		updateAgent(){
			var vm = this;
			var url = `${this.base_url}/agents/update`;

			if(this.selectedGroup == ""){
				return vm.$snotify.info(vm.$t('bs-select-at-least-one')+ ' '+ vm.$t('bs-group'), vm.$t('bs-info'));
			}

			if(this.selectedDepartment == ""){
				return vm.$snotify.info(vm.$t('bs-select-at-least-one')+ ' '+ vm.$t('bs-department'), vm.$t('bs-info'));
			}

			if(this.password != ''){
				if(this.formCompany.password && this.formCompany.password2 ){
					//ATUALIZAÇÃO CASO TENHA ALGO NO PASSWORD
					axios.post(url, {
						type: "password",
						id: vm.itemselected.id,
						company_user_id: vm.itemselected.company_user_id,
						name: vm.name,
						password: vm.password,
						group: vm.group,
						department: vm.department,
						selectedGroup: vm.selectedGroup,
						selectedDepartment: vm.selectedDepartment,
						originsGroup: vm.originsGroup,
						originsDepartment: vm.originsDepartment,
						hoursPerDay: vm.hoursPerDay,
						workingStatus: vm.workingStatus,
						timezoneSelected: vm.timezoneSelected,
					}).then(function(response){
						//console.log(response.data.created);
						if(response.data.success){
							vm.itemselected.name = vm.name;
							vm.$snotify.success(vm.$t('bs-attendant-updated-successfully'), 'Success!');

							// setTimeout(function () {
							// 	window.open(`${vm.base_url}/agents/list-agents`, '_self')
							// }, 2000)
						}else{
							vm.$snotify.error(vm.$t('bs-error-when-updating-the-attendant'), 'Error!');
						}
					})
					.catch(function(){
						console.log('FAILURE!!');
					});
				}else{
					vm.$snotify.info(vm.$t('bs-check-fields-invalid'), 'Info!');
				}
			}else{
				//ATUALIZAÇÃO SÓ DO NOME
				axios.post(url, {
					type: "name",
					id: vm.itemselected.id,
					company_user_id: vm.itemselected.company_user_id,
					name: vm.name,
					group: vm.group,
					department: vm.department,
					selectedGroup: vm.selectedGroup,
					selectedDepartment: vm.selectedDepartment,
					originsGroup: vm.originsGroup,
					originsDepartment: vm.originsDepartment,
					hoursPerDay: vm.hoursPerDay,
					workingStatus: vm.workingStatus,
					timezoneSelected: vm.timezoneSelected,
				}).then(function(response){
					//console.log(response.data.created);
					if(response.data.success){
						vm.itemselected.name = vm.name;
						vm.$snotify.success(vm.$t('bs-attendant-updated-successfully'), 'Success!');

						// setTimeout(function () {
						// 	window.open(`${vm.base_url}/agents/list-agents`, '_self')
						// }, 2000)
					}else{
						vm.$snotify.error(vm.$t('bs-error-when-updating-the-attendant'), 'Error!');
					}
				})
				.catch(function(){
					console.log('FAILURE!!');
				});
			}
		},		
		btnBack(){
			var vm = this;
			vm.$emit('back');
		},
		resetGroup(){
			var vm = this;
			vm.selectedGroup = vm.originsGroup;
		},
		resetDepartment(){
			var vm = this;
			vm.selectedDepartment = vm.originsDepartment;
		},
		updateGroup(){

		},
		updateDepartment(){

		},
	},
	computed: {
		vName() {
			this.formCompany.name = this.name.length > 2 && this.name.length < 100;
			return this.formCompany.name;
		},
		vEmail(){
			var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			this.formCompany.email = re.test(this.email);
			return this.formCompany.email;
		},
		vPassword(){
			this.formCompany.password = this.password.length > 7 && this.password.length < 100;
			return this.formCompany.password;
		},
		vPassword2(){
			this.formCompany.password2 = this.password == this.password2;
			return this.formCompany.password2;
		},
	}
};
</script>

<style lang="scss" scoped>

.bui-alert {
  background: url(/images/meta/corner.png) left top no-repeat,
    url(/images/meta/wave.png) right bottom/100% no-repeat,
    transparent linear-gradient(180deg, #5e81f4 0%, #1665d8 100%);
  border-radius: 16px;
  border: none;
  padding-top: 1.2rem;
  padding-left: 2rem;
  color: #fff;
  p {
    font-size: 0.85rem;
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.8);
  }
  .btn {
    background: #ffffff38;
    border: unset;
    font-weight: normal !important;
    text-transform: capitalize;
    border-radius: 8px;
  }
}

.caret {
  cursor: pointer;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

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

.btn-back{
	background: #F8F8F8 0% 0% no-repeat padding-box;
	box-shadow: 0px 1px 1px #1E120D1A;
	border-radius: 5px;
	opacity: 1;
	font: normal normal 800 14px/16px Muli;
	color: #434343;
	text-transform: uppercase;
}

.body{
	background: #FFFFFF 0% 0% no-repeat padding-box;
	border: 1px solid #DEE3EA;
	border-radius: 5px 5px 0px 0px;
	opacity: 1;
	padding: 15px;
}


</style>