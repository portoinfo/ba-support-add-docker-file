<template>
	<div>
		<b-container v-if="showGroup" fluid>
			<b-row>
				<b-col cols="auto" class="mr-auto p-3 bs-title">{{title}}
					<b-card-text class="bs-subtitle">
						{{subtitle}}
					</b-card-text>
				</b-col>
				<b-col cols="auto mt-4">
					<b-button @click="showBack" variant="light bs-btn-back">{{$t('bs-back')}}</b-button>
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
							<b-button @click="showCreated" variant="btn bs-btn-add"><i class="fa fa-plus" aria-hidden="true"></i> {{$t('bs-register-new-group')}}</b-button>
						</b-col>
					</b-row>
				</b-col>
			</b-row>
			<br>

			<div v-if="showZeroResister" class='local-striped-table'>
				<b-row>
					<b-col class="bs-body">
						<b-card-text class="bodytext">
							<center>{{$t('bs-no-groups-registered-at-the-moment')}}</center>
						</b-card-text>
					</b-col>
				</b-row>
			</div>

			<div v-for="(item, index) in resultQuery" :key="index"  class='local-striped-table' :even="index % 2 == 0 ? 'true' : 'false'">
				<b-row v-if="showGroup">
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
									<div v-if="item.is_active == 0" class="px-3" >
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
			<group-create v-on:save="saveRow" v-on:back="showBack" :base_url="base_url"></group-create>
		</div>

		<div v-if="showEdit">
			<group-edit :itemselected="itemselected" v-on:back="showBack" v-on:update="saveRowUpdate" :base_url="base_url"></group-edit>
		</div>

		<div v-if="showConfig">
			<group-config  v-on:back="showBack" :title='title' :subtitle='subtitle' :itemselected="itemselected" :base_url="base_url"></group-config>
		</div>
		<vue-snotify></vue-snotify>
	</div>
</template>

<script>

export default {
	data(){
		return {
			phBuscar: this.$t('bs-search'),
			title: this.$t('bs-groups'),
			subtitle: this.$t('bs-attendance-permission-group'),
			group: [],
			showHome: true,
			showGroup: true,
			showCreate: false,
			showConfig: false,
			showEdit: false,
			itemselected: {},
			searchQuery: null,
			showZeroResister: false,
		}
	},
	props:{
		usuario: Object,
		base_url: {
			type: String,
			default: ''
		},
		go_back_url: {
			type: String,
			default: ''
		}
	},
	computed: {
		resultQuery(){
			if(this.searchQuery){
				return this.group.filter((item)=>{
					return this.searchQuery.toLowerCase().split(' ').every(v => item.name.toLowerCase().includes(v))
				})
			}else{
				return this.group;
			}
		}
	},
	mounted(){
		var vm = this;
		var url = `${this.base_url}/group/get-group`;
		axios.get(url).then(function(r_resposta){
			//console.log(r_resposta.data);
			// grupos recem criados vem com settings null isso buga a configuracao de permissao
			r_resposta.data.result = r_resposta.data.result.map(function(item) {
				if(item.settings == undefined || item.settings == null) {
					item.settings = {
						"permissions":{"company":{"view":false,"insert":false,"edit":false,"delete":false},
						"department":{
							"view":false,"insert":false,"edit":false,"delete":false,
							"config":{
								"general":{"view":false,"insert":false,"edit":false,"delete":false},
								"management":{"view":false,"insert":false,"edit":false,"delete":false},
								"autoAnswer":{"view":false,"insert":false,"edit":false,"delete":false},
								"quantLimitation":{"view":false,"insert":false,"edit":false,"delete":false},
								"robot":{"view":false,"insert":false,"edit":false,"delete":false},
								"evalution":{"view":false,"insert":false,"edit":false,"delete":false},
								"chat":{"view":false,"insert":false,"edit":false,"delete":false},
								"ticket":{"view":false,"insert":false,"edit":false,"delete":false}
								}
							},
						"group":{"view":false,"insert":false,"edit":false,"delete":false},
						"agents":{"view":false,"insert":false,"edit":false,"delete":false},
						"integration":{"view":false,"insert":false,"edit":false,"delete":false},
						"chat":{"open":false,"solution":false,"close":false,"moved":false,
						"admin":false,"resolved":false,"transform":false,"queue_full_control":false},
						"ticket":{"open":false,"solution":false,"close":false,"moved":false,"alllist":false,"create":false,"admin":false,"resolved":false}}}
				}
				return item
			})
			
			vm.group = r_resposta.data.result;

			if(vm.group.length == 0){
				vm.showZeroResister = true;
			}

		}).catch(function (error) {
			console.log(error);
		});

		if(new URL(location.href).searchParams.get('new') == 'group'){
			vm.showCreated();
		}
	},
	methods:{
		saveRow(item){
			var vm = this;
			//console.log(item);
			vm.showGroup = true;
			vm.showCreate = false;
			vm.showConfig = false;
			item.settings = JSON.parse(item.settings);
			vm.showZeroResister = false;
			vm.group.push(item);
			vm.itemConfig(item);
		},
		saveRowUpdate(item){
			var vm = this;
			//console.log(item);
			vm.showGroup = true;
			vm.showEdit = false;
			vm.showConfig = false;
			let index = vm.group.findIndex(function(el) {
				return el.id == vm.id
			})
			this.$set(this.group, index, item)
		},
		itemConfig(item){
			//console.log(item);
			var vm = this;
			vm.itemselected = item;
			vm.showGroup = false;
			vm.showCreate = false;
			vm.showHome = false;
			vm.showConfig = true;
			vm.showEdit = false;
			vm.title = this.$t('bs-group-configuration');
			vm.subtitle = this.$t('bs-group-configuration')+ ' '+item.name;
		},
		itemEdit(item){
			var vm = this;
			vm.itemselected = item;
			vm.showGroup = false;
			vm.showCreate = false;
			vm.showHome = false;
			vm.showConfig = false;
			vm.showEdit = true;
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

					axios.post(`${this.base_url}/group/group-delete`, {
						type: 'delete',
						id: item.id,
					}).then(function(response){

						if(response.data.success){

							vm.group.splice(index, 1);

							if(vm.group.length == 0){
								vm.showZeroResister = true;
							}

							vm.$snotify.success(vm.$t('bs-successfully-deleted'), vm.$t('bs-success'));
							Swal.fire(
								vm.$t('bs-deleted'),
								vm.$t('bs-your-file-has-been-deleted'),
								'success'
							);
						}else{

							vm.$snotify.error(vm.$t('bs-error-while-deleting'), vm.$t('bs-error'));
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

			axios.post(`${this.base_url}/group/group-delete`, {
				type: 'disable',
				id: item.id,
			}).then(function(response){
				//console.log(response.data);

				if(response.data.success){

					vm.group[index].is_active = 0;
					vm.$snotify.success(vm.$t('bs-successfully-deactivated'), vm.$t('bs-success'));
				}else{
					vm.$snotify.error(vm.$t('bs-error-deactivating'), vm.$t('bs-error'));
				}
			})
			.catch(function(){
				console.log('FAILURE!!');
			});
		},
		itemRestore(item, index){
			var vm = this;
			axios.post(`${this.base_url}/group/group-delete`, {
				type: 'restore',
				id: item.id,
			}).then(function(response){
				//console.log(response.data);

				if(response.data.success){
					vm.group[index].is_active = 1;
					vm.$snotify.success(vm.$t('bs-successfully-restored'), vm.$t('bs-success'));

				}else{
					vm.$snotify.error(vm.$t('bs-error-when-restoring'), vm.$t('bs-error'));
				}
			})
			.catch(function(){
				console.log('FAILURE!!');
			});
		},
		showBack(){
			var vm = this;
			if(this.showGroup) {
				window.open(this.go_back_url, '_self')
			} else {
				vm.showGroup = true;
				vm.showCreate = false;
				vm.showConfig = false;
				vm.showHome = true;
				vm.showEdit = false;
				vm.title = this.$t('bs-groups');
				vm.subtitle = this.$t('bs-attendance-permission-group');
			}
		},
		showCreated(){
			var vm = this;
			if(vm.showCreate){
				vm.showGroup = true;
				vm.showCreate = false;
				vm.showConfig = false;
			}else{
				vm.showGroup = false;
				vm.showCreate = true;
				vm.showConfig = false;
			}
		},
	},
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