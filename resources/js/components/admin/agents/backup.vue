<template>
	<div class="container">
		<div class="row title">
			<div class="col-sm-6">
				<h4>Tabela de Agents</h4>
			</div>
			<div class="col-sm-6 buttonvoltar">
				<button type="button" class="btn btn-light"  style="font-size:14px;font-weight:bold;color: #848484" v-on:click.stop.prevent="showBack">VOLTAR</button>
			</div>
		</div>
		<div v-show="showHome" class="row title">
			<div class="col-sm-1">
			</div>
			<div class="col buttonvoltar">
				<div class="row">
					<div class="col justificar">
						<button type="button" class="btn btn-primary" v-on:click.stop.prevent="showCreated"><i class="fa fa-plus" aria-hidden="true"></i> Add new agent</button>
					</div>
					<div class="col justificar">
						<input type="" class="form-control" name="" v-model="searchQuery" placeholder="Buscar">
					</div>
				</div>
			</div>
		</div>
		<br>
		<div v-if="showUsers" v-for="(item, index) in resultQuery">
			<div v-if="item.is_active">
				<div class="body row">
					<div class="col namecss">{{item.name}} - {{item.email}}</div>
					<div class="col buttonvoltar">
						<a href="#" v-on:click.stop.prevent="itemConfi(item)">
							<i class="fa fa-pencil fa-2x pencil" aria-hidden="true"></i>
						</a>

						<a href="#" v-on:click.stop.prevent="itemDelete(item, index)">
							<i class="fa fa-trash-o fa-2x trash" aria-hidden="true"></i>
						</a>
					</div>
				</div>
			</div>
		</div>
		<div v-if="showUsers" v-for="(item, index) in resultQuery">
			<div v-if="!item.is_active">
				<div class="bodyDisable row">
					<div class="col namecss">{{item.name}} - {{item.email}}</div>
					<div class="col buttonvoltar">
						<a href="#" v-on:click.stop.prevent="itemRestore(item, index)">
							<i class="fa fa-window-restore fa-2x pencil" aria-hidden="true"></i>
						</a>

						<a href="#" v-on:click.stop.prevent="itemDelete(item, index)">
							<i class="fa fa-trash-o fa-2x trash" aria-hidden="true"></i>
						</a>
					</div>
				</div>
			</div>
		</div>

		<div v-if="showCreate" class="body">
			<agents-create v-on:save="saveRow"></agents-create>
		</div>

		<div v-if="showConfig" class="body">
			<agents-config  v-on:back="showBack" :itemselected="itemselected"></agents-config>
		</div>
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
			agents: [],
			showHome: true,
			showUsers: true,
			showCreate: false,
			showConfig: false,
			itemselected: {},
			searchQuery: null,
		}
	},
	computed: {
		resultQuery(){
			if(this.searchQuery){
				return this.agents.filter((item)=>{
					return this.searchQuery.toLowerCase().split(' ').every(v => item.name.toLowerCase().includes(v))
				})
			}else{
				return this.agents;
			}
		}
	},
	mounted(){
		var vm = this;
		var url = `${this.base_url}/agents/getAgents`;
		axios.get(url).then(function(r_resposta){

			//console.log(r_resposta.data);
			vm.agents = r_resposta.data.result;

		}).catch(function (error) {

			console.log(error);
		});
	},
	methods:{
		saveRow(item){
			var vm = this;
			//console.log(item);
			vm.showUsers = true;
			vm.showCreate = false;
			vm.showConfig = false;
			vm.agents.push(item);
		},
		itemConfi(item){
			console.log(item);
			var vm = this;
			vm.itemselected = item;
			vm.showUsers = false;
			vm.showCreate = false;
			vm.showHome = false;
			vm.showConfig = true;

		},
		itemDelete(item, index){
			var vm = this;
			Swal.fire({
				title: 'Are you sure you want to delete?',
				showDenyButton: true,
				showCancelButton: true,
				confirmButtonText: `Delete`,
				denyButtonText: `Disable?`,
			}).then((result) => {
				/* Read more about isConfirmed, isDenied below */
				if (result.isConfirmed) {

					axios.post(`${this.base_url}/agents/agentsDelete`, {
						type: 'delete',
						id: item.id,
					}).then(function(response){


						if(response.data.success){

							vm.agents.splice(index, 1);

							Toast.fire({
								icon: 'success',
								title: 'Company deleted success'
							});

						}else{
							Toast.fire({
								icon: 'error',
								title: 'Company deleted create'
							});
						}

					})
					.catch(function(){
						console.log('FAILURE!!');
					});

					//Swal.fire('Saved!', '', 'success')
				} else if (result.isDenied) {

					Swal.fire({
					  title: 'Do you want to disable?',
					  showDenyButton: true,
					  showCancelButton: true,
					  confirmButtonText: `Disable`,
					  denyButtonText: `Don't save`,
					  width: 600,
					}).then((result) => {
					  /* Read more about isConfirmed, isDenied below */
					  if (result.isConfirmed) {

					  	axios.post(`${this.base_url}/agents/agentsDelete`, {
					  	type: 'disable',
						id: item.id,
						}).then(function(response){


							if(response.data.success){

								vm.agents[index].is_active = 0;

								Toast.fire({
									icon: 'success',
									title: 'Agent disabled success'
								});

							}else{
								Toast.fire({
									icon: 'error',
									title: 'Agent not disabled'
								});
							}
						})
						.catch(function(){
							console.log('FAILURE!!');
						});
					  } else if (result.isDenied) {
					    Swal.fire('Changes are not saved', '', 'info')
					  }
					})
				}
			});
		},
		itemRestore(item, index){
			var vm = this;
			Swal.fire({
				title: 'Restore?',
				showCancelButton: true,
				confirmButtonText: `Yes`,
			}).then((result) => {
				/* Read more about isConfirmed, isDenied below */
				if (result.isConfirmed) {

					axios.post(`${this.base_url}/agents/agentsDelete`, {
						type: 'restore',
						id: item.id,
					}).then(function(response){


						if(response.data.success){

							vm.agents[index].is_active = 1;

							Toast.fire({
								icon: 'success',
								title: 'Agent restored success'
							});

						}else{
							Toast.fire({
								icon: 'error',
								title: 'Agent restored error'
							});
						}

					})
					.catch(function(){
						console.log('FAILURE!!');
					});

					//Swal.fire('Saved!', '', 'success')
				}
			});
		},
		showBack(){
			var vm = this;
			vm.showUsers = true;
			vm.showCreate = false;
			vm.showConfig = false;
			vm.showHome = true;
		},
		showCreated(){

			var vm = this;
			if(vm.showCreate){
				vm.showUsers = true;
				vm.showCreate = false;
				vm.showConfig = false;
			}else{
				vm.showUsers = false;
				vm.showCreate = true;
				vm.showConfig = false;
			}

		},
	},
};
</script>

<style scoped>
.title{
	color: #4465df;
	padding: 8px;
	margin-top:20px;
	border-radius: 3px;
	font-weight: bold;
}

.body{
	background-color: white;
	margin-bottom:10px;
	padding: 15px;
}

.bodyDisable{
	background-color: #D8D8D8;
	color: #6E6E6E;
	margin-bottom:10px;
	padding: 15px;
}

.buttonvoltar{
	text-align: right;
}

.justificar{
	padding-left: 5px;
	padding-right: 5px;
}

.namecss{
	font-size: 18px;
	margin-left:10px;
}
.pencil{
	color: blue;
	margin-right: 4px;
}
.trash{
	color: red;
	margin-left: 4px;
}

.barra{
	border: 1px solid black;
	width: 10px;
	height: 10px;
}
</style>
