<template>
	<div>
		<b-container v-if="showDepartment" fluid>
			<b-row>
				<b-col cols="auto" class="mr-auto p-3 bs-title">{{title}}
					<b-card-text class="bs-subtitle">
						{{subtitle}}
					</b-card-text>
				</b-col>
				<b-col cols="auto mt-4">
					<b-button @click="goBack" variant="light bs-btn-back"> {{$t('bs-back')}}</b-button>
					<!-- <b-button @click="onSubmit" variant="btn btn-save"><i class="fa fa-floppy-o" aria-hidden="true"></i> SALVAR</b-button> -->
				</b-col>
			</b-row>
			<b-row>
				<b-col cols="auto" class="mr-auto p-3 bs-title">
					<b-card-text class="bs-subtitle">
					</b-card-text>
				</b-col>
				<b-col v-if="showHome" cols="auto mt-4">
					<b-row>
						<b-col sm="auto">
							<b-form-input  variant="light" class="bs-input icon" v-model="searchQuery" :placeholder="phBuscar"></b-form-input>
							<i class="fa fa-search iconButton" aria-hidden="true"></i>
						</b-col>
						<b-col>
							<b-button @click="createDepartment" variant="btn bs-btn-add"><i class="fa fa-plus" aria-hidden="true"></i> {{$t('bs-register-new-department')}}</b-button>
						</b-col>
					</b-row>
				</b-col>
			</b-row>
			<br>

			<div v-if="showZeroResister" class='local-striped-table'>
				<b-row>
					<b-col class="bs-body">
						<b-card-text class="bodytext">
							<center>{{$t('bs-no-department-registered-at-the-moment')}}</center>
						</b-card-text>
					</b-col>
				</b-row>
			</div>

			<div v-for="(item, index) in resultQuery" :key="index" class='local-striped-table' :even="index % 2 == 0 ? 'true' : 'false'">
				<b-row v-if="showDepartment">
					<b-col class="bs-body">
						<b-card-text class="bodytext">
							<b-row no-gutters>
								<b-col cols="sm-9" style="margin-top:4px">
									{{$t(item.name)}}
								</b-col>
								<b-col class="bs-textrigth">
									<div v-if="item.is_active == 1" class="px-3">
										<b-row no-gutters>
											<b-col cols="sm-1">
												<b-link href="#" v-model="item.is_active" @click="itemConfig(item)">
													<i class="fa fa-cog fa-2x bs-cog" aria-hidden="true"></i>
												</b-link>
											</b-col>
											<b-col cols="sm-1">
												<b-link href="#" v-model="item.is_active" @click="itemEdit(item)">
													<i class="fa fa-pencil fa-2x bs-pencil" aria-hidden="true"></i>
												</b-link>
											</b-col>
											<b-col cols="sm-1">
												<b-link href="#" v-model="item.is_active" @click="itemDelete(item, index)">
													<i class="fa fa-trash-o fa-2x bs-trash" aria-hidden="true"></i>
												</b-link>
											</b-col>
											<b-col class="ml-1">
												<label class="switch">
													<input type="checkbox" v-model="item.is_active" @click="itemDisable(item, index)">
													<span class="slider round"></span>
												</label>
											</b-col>
										</b-row>
									</div>
									<div v-if="item.is_active == 0" class="px-3">
										<label class="switch">
											<input type="checkbox" v-model="item.is_active" @click="itemRestore(item, index)">
											<span class="slider round"></span>
										</label>
									</div>
								</b-col>
							</b-row>
						</b-card-text>
					</b-col>
				</b-row>
			</div>
		</b-container>

		<div v-if="showCreate">
			<department-create v-on:save="saveRow" v-on:back="showBack" :base_url="base_url"></department-create>
		</div>

		<div v-if="showEdit">
			<department-edit :itemselected="itemselected" v-on:back="showBack" v-on:update-department="updateDepartmentList" :base_url="base_url"></department-edit>
		</div>

		<div v-if="showConfig">
			<department-config
			v-on:addAgent="addAgent"
			:title='title'
			:subtitle='subtitle'
			:itemselected="itemselected"
			:timezones="timezones"
			v-on:back="showBack"
			:base_url="base_url"
			:company_id="company_id"
			>
			</department-config>
		</div>
		<vue-snotify></vue-snotify>
	</div>
</template>

<script>

export default {
	data(){
		return {
			department: [],
			configs: [],
			showHome: true,
			showDepartment: true,
			showConfig: false,
			showCreate: false,
			showEdit: false,
			itemselected: {},
			searchQuery: null,
			phBuscar: this.$t('bs-search'),
			title: this.$t('bs-department'),
			subtitle: this.$t('bs-department'),
			showZeroResister: false,
		}
	},
	props:{
		usuario: Object,
		timezones: Object,
		base_url: {
			type: String,
			default: ''
		},
		go_back_url: {
			type: String,
			default: ''
		},
		company_id: String,
	},
	computed: {
		resultQuery(){
			if(this.searchQuery){
				return this.department.filter((item)=>{
					return this.searchQuery.toLowerCase().split(' ').every(v => item.name.toLowerCase().includes(v))
				})
			}else{
				return this.department;
			}
		}
	},
	mounted(){
		var vm = this;
		var url = `${this.base_url}/department/get-department`;
		axios.get(url).then(function(r_resposta){

			//console.log(r_resposta.data);
			vm.department = r_resposta.data.result;

			if(vm.department.length == 0){
				vm.showZeroResister = true;
			}

			const urlParams = new URLSearchParams(window.location.search);
			const myParam = urlParams.get('open');

			let index = vm.department.findIndex(
			(item) => item.name === myParam
			);

			if (index !== -1) {

				vm.itemConfig(vm.department[index]);
			}
			// console.log(vm.department);
			// console.log(myParam);
			// open=awdawd
		}).catch(function (error) {
			console.log(error);
		});

		const urlParams = new URLSearchParams(window.location.search);
		const myParam = urlParams.get('new');
		if(myParam == 'department'){
			vm.createDepartment();
		}
	},
	methods:{
		saveRow(item){
			var vm = this;
			vm.showDepartment = true;
			vm.showCreate = false;
			vm.showConfig = false;
			vm.showEdit = false;
			vm.showZeroResister = false;
			vm.department.push(item);
			vm.itemConfig(item);
			vm.title = this.$t('bs-department');
			vm.subtitle = '';
		},
		itemConfig(item){
			var vm = this;
			vm.itemselected = item;
			vm.showDepartment = false;
			vm.showCreate = false;
			vm.showHome = false;
			vm.showConfig = true;
			vm.showEdit = false;
			this.title = this.$t('bs-department-setting');
			this.subtitle = this.$t('bs-configure-the-department') + " " + item.name;
		},
		itemEdit(item){
			var vm = this;
			vm.itemselected = item;
			vm.showDepartment = false;
			vm.showCreate = false;
			vm.showHome = false;
			vm.showConfig = false;
			vm.showEdit = true;
			this.title = this.$t('bs-edit-department');
			this.subtitle = this.$t('bs-edit-department') + ' ' + item.name;
		},
		itemDelete(item, index){
			var vm = this;

			Swal.fire({
				title: this.$t('bs-are-you-sure'),
				text: this.$t('bs-you-wont-be-able-to-revert-this'),
				icon: 'warning',
				showCancelButton: true,
				cancelButtonText: this.$t('bs-cancel'),
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: this.$t('bs-yes-delete-it'),
			}).then((result) => {
				if (result.isConfirmed) {

					axios.post(`${this.base_url}/department/department-delete`, {
						type: 'delete',
						id: item.id,
					}).then(function(response){
						//console.log(response.data);

						if(response.data.success){

							vm.department.splice(index, 1);

							if(vm.department.length == 0){
								vm.showZeroResister = true;
							}

							vm.$snotify.success(vm.$t('bs-successfully-deleted'), vm.$t('bs-success'));
							Swal.fire(
								vm.$t('bs-deleted'),
								vm.$t('bs-your-file-has-been-deleted'),
								'success'
							);
						}else{
							if(response.data.value == 'not_disable_or_delete'){

							}else{
								vm.$snotify.error(vm.$t('bs-error-while-deleting'), vm.$t('bs-error'));
							}

						}

					})
					.catch(function(){
						console.log('FAILURE!!');
					});
				}
			})
		},
		itemDisable(item, index){
			var vm = this;

			axios.post(`${this.base_url}/department/department-delete`, {
				type: 'disable',
				id: item.id,
			}).then(function(response){
				//console.log(response.data);

				if(response.data.success){

					vm.department[index].is_active = 0;
					vm.$snotify.success(vm.$t('bs-department-successfully-deactivated'), vm.$t('bs-success'));

				}else{
					vm.$snotify.error(vm.$t('bs-error-when-deactivating-department'), vm.$t('bs-error'));
				}
			})
			.catch(function(){
				console.log('FAILURE!!');
			});
		},
		itemRestore(item, index){
			var vm = this;

			axios.post(`${this.base_url}/department/department-delete`, {
				type: 'restore',
				id: item.id,
			}).then(function(response){
				//console.log(response.data);

				if(response.data.success){

					vm.department[index].is_active = 1;
					vm.$snotify.success(vm.$t('bs-department-successfully-restored'), vm.$t('bs-success'));

				}else{
					vm.$snotify.error(vm.$t('bs-error-restoring-department'), vm.$t('bs-error'));
				}

			})
			.catch(function(){
				console.log('FAILURE!!');
			});
		},
		goBack(){
			this.showBack()
			window.open(this.go_back_url, '_self')
		},
		showBack(){
			var vm = this;
			vm.showDepartment = true;
			vm.showCreate = false;
			vm.showConfig = false;
			vm.showHome = true;
			vm.showEdit = false;
			vm.title = this.$t('bs-department');
			vm.subtitle = '';
		},
		createDepartment(){
			var vm = this;
			if(vm.showCreate){
				vm.showDepartment = true;
				vm.showCreate = false;
				vm.showConfig = false;
				vm.showEdit = false;
			}else{
				vm.showDepartment = false;
				vm.showCreate = true;
				vm.showConfig = false;
				vm.showEdit = false;
			}
		},
		addAgent(item){
			var vm = this;
			vm.title = item;
		},
		saveAllConfigs(){

		},
		updateDepartmentList(item) {
			this.itemselected = {}

			var vm = this;
			var url = `${this.base_url}/department/get-department`;
			axios.get(url).then(function(r_resposta){
				vm.$set(vm.$data, 'department',  r_resposta.data.result);
			}).catch(function (error) {
				console.log(error);
			}).finally(function(){
				vm.showDepartment = true;
				vm.showCreate = false;
				vm.showConfig = false;
				vm.showEdit = false;
			});
		}
	}
};
</script>

<style scoped>

.bs-body{
	background: #FFFFFF 0% 0% no-repeat padding-box;
	border: 1px solid #DEE3EA;
	border-radius: 5px 5px 0px 0px;
	opacity: 1;
	padding: 5px;
}

.bodytext{
	font: normal normal 600 16px/19px Lato;
	letter-spacing: 0px;
	color: #6E6E6E;
	opacity: 1;
	padding: 4px;
	margin-left: 20px;
}

.icon{
	padding-left: 30px;
	font: normal normal normal 14px/17px Muli;

}

.iconButton{
	top: 11px;
	left: 26px;
	position: absolute;
	text-align: center;
	justify-content: center;
	color: black;
	opacity: 0.75;
}

.bs-textrigth{
	text-align: right;
}

.col-sm-1
{
	margin-left: 4px;
	padding: 0px;
	max-width: 14%;
}

@media screen and (max-width: 1024px) {

	.col-sm-1
	{
		margin-left: 4px;
		padding: 0px;
		max-width: 20%;
	}
}

</style>
