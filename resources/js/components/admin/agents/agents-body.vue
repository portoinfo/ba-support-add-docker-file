<template>
	<div>
		<b-container v-if="showHome" fluid>
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
				<b-col cols="auto" class="mr-auto p-3 title">
					<b-card-text class="subtitle">
					</b-card-text>
				</b-col>
				<b-col v-if="showHome" cols="auto mt-4">
					<b-row>
						<b-col sm="auto">
							<b-form-input  variant="light" class="bs-input icon" v-model="searchQuery" :placeholder="phBuscar"></b-form-input>
							<i class="fa fa-search iconButton" aria-hidden="true"></i>
						</b-col>
						<b-col>
							<b-button @click="createdAgent" variant="btn bs-btn-add"><i class="fa fa-user-plus" aria-hidden="true"></i> {{$t('bs-add-new-attendant')}}</b-button>
						</b-col>
					</b-row>
				</b-col>
			</b-row>
			<br>

			<b-table responsive bordered borderless striped hover
				class="local-striped-table"
				head-variant="light"
				table-variant="light"
				:items="resultQuery"
				:fields="fields"
				show-empty
			>
            <template #cell(attendants)="row">
                <gravatar
                    :email="row.item.email"
                    :status="$status.get(row.item.id)"
                    size="40px"
                    :name="row.item.attendants"
                />
                {{ row.item.attendants }}
			</template>
			<template #cell(last_login)="row">
				{{row.item.last_login | formatData}}
				<!-- <b-link href="#">
					<i :class="lastlogin" aria-hidden="true"></i>
				</b-link> -->
			</template>
			<template #cell(Open_tickets)="row">
				{{row.item.Open_tickets}}
			</template>
			<template #cell(Open_chats)="row">
				{{row.item.Open_chats}}
			</template>
			<template #cell(actions)="row">
				<div v-if="row.item.is_active == 1">
					<b-link href="#" @click="itemEdit(row.item)">
						<i class="fa fa-pencil fa-2x bs-pencil" aria-hidden="true"></i>
					</b-link>
					<b-link href="#" @click="itemEye(row.item)">
						<i class="fa fa-eye fa-2x bs-eye" aria-hidden="true"></i>
					</b-link>
					<span v-if="row.item.is_admin == 0">
						<b-link href="#" @click="itemDelete(row.item, row.index)">
							<i class="fa fa-trash-o fa-2x bs-trash" aria-hidden="true"></i>
						</b-link>
					</span>
				</div>
			</template>
			<template #cell(is_active)="row">
				<span v-if="row.item.is_admin == 0">
					<div v-if="row.item.is_active == 1">
						<label class="switch">
							<input type="checkbox" v-model="row.item.is_active" @click="itemDisable(row.item, row.index)">
							<span class="slider round"></span>
						</label>
					</div>
					<div v-if="row.item.is_active == 0">
						<label class="switch">
							<input type="checkbox" v-model="row.item.is_active" @click="itemRestore(row.item, row.index)">
							<span class="slider round"></span>
						</label>
					</div>
				</span>
			</template>
			<template #empty="scope">
				<div class="text-center">{{$t('bs-no-attendant-registered')}}.</div>
			</template>
		</b-table>
	</b-container>

	<div v-if="showCreate">
		<agents-create v-on:back="showBack" v-on:save="saveRowAgent" :csid="csid" :base_url="base_url"></agents-create>
	</div>

	<div v-if="showEdit">
		<agents-edit  v-on:back="showBack" :itemselected="itemselected" :typeEditorRegister="typeEditorRegister" :base_url="base_url" :is_admin="is_admin"></agents-edit>
	</div>

	<vue-snotify></vue-snotify>
</div>
</template>

<script>

export default {
	data(){
		return {
			agents: [],
			showHome: true,
			showUsers: true,
			showCreate: false,
			showEdit: false,
			itemselected: {},
			searchQuery: null,
			phBuscar: this.$t('bs-search'),
			title: this.$t('bs-list-of-attendants'),
			subtitle: '',
			fields: [
				{
					key: 'attendants',
					label: this.$t('bs-attendants')
				},
				{
					key: 'last_login',
					label: this.$t('bs-last-login')
				},
				{
					key: 'Open_tickets',
					label: this.$t('bs-open-tickets')
				},
				{
					key: 'Open_chats',
					label: this.$t('bs-open-chats')
				},
				{
					key: 'actions',
					label: this.$t('bs-actions')
				},
				{
					key: 'is_active',
					label: this.$t('bs-is-active')
				}
			],
			// fields: ['attendants', 'last_login', 'Open_tickets', 'Open_chats', 'actions', 'is_active'],
			lastlogin: 'fa fa-circle fa-1x bs-trash',
			typeEditorRegister: '',
		}
	},
	props:{
		usuario: Object,
		csid: String,
        base_url: {
            type: String,
            default: ''
		},
		go_back_url: {
            type: String,
            default: ''
        },
		is_admin: "",
	},
	computed: {
		resultQuery(){
			if(this.searchQuery){
				return this.agents.filter((item)=>{
					return this.searchQuery.toLowerCase().split(' ').every(v => item.attendants.toLowerCase().includes(v))
				})
			}else{
				return this.agents;
			}
		}
	},
	mounted(){
		var vm = this;
		var url = `${this.base_url}/agents/get-agents`;
		axios.get(url).then(function(r_resposta){

			//console.log(r_resposta.data);
			vm.agents = r_resposta.data.result;

		}).catch(function (error) {

			console.log(error);
		});

		const urlParams = new URLSearchParams(window.location.search);
		const myParam = urlParams.get('new');
		if(myParam == 'agent'){
			vm.createdAgent();
		}

	},
	methods:{
		saveRowAgent(item){
			var vm = this;
			vm.itemselected = item;
			vm.agents.push(item);
			vm.showUsers = false;
			vm.showCreate = false;
			vm.showHome = false;
			vm.showEdit = true;
			vm.title = this.$t('bs-list-of-attendants');
			vm.subtitle = '';
			vm.typeEditorRegister = 'register';


			// var vm = this;
			// vm.showUsers = true;
			// vm.showCreate = false;
			// vm.showEdit = false;
			// vm.showHome = true;
			// vm.title = this.$t('bs-list-of-attendants');
			// vm.subtitle = '';
			// vm.itemselected = item;
			// vm.agents.push(item);
		},
		itemEdit(item){
			var vm = this;
			vm.itemselected = item;
			vm.showUsers = false;
			vm.showCreate = false;
			vm.showHome = false;
			vm.showEdit = true;
			vm.typeEditorRegister = 'edit';
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

					axios.post(`${this.base_url}/agents/agents-delete`, {
						type: 'delete',
						csid: vm.csid,
						company_user_id: item.company_user_id,
					}).then(function(response){


						if(response.data.success){
							vm.agents.splice(index, 1);

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
			});
		},
		itemDisable(item, index){
			var vm = this;
			axios.post(`${this.base_url}/agents/agents-delete`, {
				type: 'disable',
				csid: vm.csid,
				company_user_id: item.company_user_id,
			}).then(function(response){


				if(response.data.success){

					vm.agents[index].is_active = 0;

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
			axios.post(`${this.base_url}/agents/agents-delete`, {
				type: 'restore',
				csid: vm.csid,
				company_user_id: item.company_user_id,
			}).then(function(response){


				if(response.data.success){
					vm.agents[index].is_active = 1;
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
			if(this.showHome) {
				window.open(this.go_back_url, '_self')
			} else {
				vm.showUsers = true;
				vm.showCreate = false;
				vm.showEdit = false;
				vm.showHome = true;
				vm.title = this.$t('bs-list-of-attendants')
				vm.subtitle = '';
			}
		},
		createdAgent(){
			var vm = this;
			vm.showUsers = false;
			vm.showHome = false;
			vm.showEdit = false;
			vm.showCreate = true;
		},

		itemEye(item) {
			window.open(`${this.base_url}/agents/agent-info-dashboard/${item.id}`, '_self')
		}
	},
	filters:{
		formatData(value){
			return moment(value).format('HH:mm DD/MM/YYYY')
		},
	},
};
</script>

<style scoped>
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

.bs-eye{
	color: #A4BEDE;
	margin-right: 4px;
}

</style>
