<template>
	<div>
		<div>
			<table class="table table-borderless table-hover local-striped-table">
				<thead>
					<tr class="table-light styleba">
						<th scope="col">{{$t('bs-group-attendants')}} {{$t(itemselected.name)}}</th>
						<th scope="col">{{$t('bs-groups')}}</th>
						<th width="1px;"></th>
					</tr>
				</thead>
				<!-- {{comAtendentes}} -->
				<tbody>
					<tr class="table-light styleba2" 
						v-for="(item, index) in agentsGroup" 
						:key="index" 
						v-if="item.user_group_id != null">
						<td>{{$t(item.name)}}</td>
						<td>{{item.grupos}}</td>
						<td><a href="#" v-on:click.stop.prevent="itemDelete(item, index)"><i class="fa fa-minus trash" aria-hidden="true"></i></a></td>
					</tr>
					<tr v-if="comAtendentes == 0" class="blank_row">
						<td colspan="3"><center>{{$t('bs-no-attendant-registered')}}.</center></td>
					</tr>
				</tbody>
			</table>
		</div>
		<br>
		<div>
			<table class="table table-borderless table-hover local-striped-table">
				<thead >
					<tr class="table-light styleba">
						<th scope="col">{{$t('bs-attendants')}}</th>
						<th scope="col">{{$t('bs-groups')}}</th>
						<th width="1px;"></th>
					</tr>
				</thead>
				<!-- {{semAtendentes}} -->
				<tbody>
					<tr class="table-light styleba2" 
						v-for="(item, index) in agentsGroup"
						:key="index"
						v-if="item.user_group_id == null"> 
						<td>{{$t(item.name)}}</td>
						<td>{{item.grupos}}</td>
						<td><a href="#" v-on:click.stop.prevent="itemInsert(item, index)"><i class="fa fa-plus pencil" aria-hidden="true"></i></a></td>
					</tr>
					<tr v-if="semAtendentes <= 0" class="blank_row">
						<td colspan="3"><center>{{$t('bs-no-attendant-registered')}}.</center></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</template>

<script>

export default {
	data(){
		return {
			agentsGroup: [],
			show: true,
			show1: false,
			fields: [
			{ key: 'name', sortable: true, label: this.$t('bs-name').toUpperCase() },
			{ key: 'grupos', sortable: true, label: this.$t('bs-groups').toUpperCase() },
			{ key: 'actions', sortable: true, label: this.$t('bs-actions').toUpperCase() }
			],
			comAtendentes: null,
			semAtendentes: null,
		}
	},
	props:{
		itemselected: Object,
		title: String,
		subtitle: String,
		base_url: {
			type: String,
			default: ''
		},
	},
	mounted(){
		var vm = this;
		var url2 = `${this.base_url}/group/get-agents/${vm.itemselected.id}`;
		axios.get(url2).then(function(r_resposta){
			//console.log(r_resposta.data);
			vm.agentsGroup = r_resposta.data.result;
			vm.comAtendentes = r_resposta.data.contador;
			vm.semAtendentes = vm.agentsGroup.length - r_resposta.data.contador;
		}).catch(function (error) {
			console.log(error);
		});
	},
	methods:{
		itemInsert(item, index){
			var vm = this;
			axios.post(`${this.base_url}/group/add-user-group`, {
				id_company_user: item.company_user_id,
				id_group: vm.itemselected.id,
			}).then(function(response){
				//console.log(response.data);

				if(response.data.success){

					if(vm.agentsGroup[index].grupos == null){
						vm.agentsGroup[index].grupos = vm.$t(vm.itemselected.name);
					}else{
						var teste = vm.agentsGroup[index].grupos.split(', ');
						teste.push(vm.$t(vm.itemselected.name));
						if(teste[0] == ""){
							teste.splice(0, 1);
						}
						vm.agentsGroup[index].grupos = teste.toString().replace(',', ', ');
					}
					vm.comAtendentes += 1;
					vm.semAtendentes -= 1;
					vm.agentsGroup[index].user_group_id = vm.itemselected.id
					vm.$snotify.success(vm.$t('bs-user-successfully-inserted'), vm.$t('bs-success'));

				}else{
					vm.$snotify.error(vm.$t('bs-error-inserting-user'), vm.$t('bs-error'));
				}

			})
			.catch(function(error){
				console.log(error);
				console.log('FAILURE!!');
			});
		},
		itemDelete(item, index){
			var vm = this;
			axios.post(`${this.base_url}/group/remove-user-group`, {
				id_company_user: item.company_user_id,
				id_group: vm.itemselected.id,
			}).then(function(response){
				//console.log(response.data);

				if(response.data.success){

					var teste = vm.agentsGroup[index].grupos.split(', ');

					for (var i = teste.length - 1; i >= 0; i--) {
						if(teste[i] == vm.itemselected.name){
							teste.splice(i, 1);
						}
					}
					vm.agentsGroup[index].grupos = teste.toString().replace(',', ', ');

					vm.comAtendentes -= 1;
					vm.semAtendentes += 1;
					vm.agentsGroup[index].user_group_id = null;
					vm.$snotify.success(vm.$t('bs-successfully-removed'), vm.$t('bs-success'));

				}else{
					vm.$snotify.error(vm.$t('bs-error-removing'), vm.$t('bs-error'));
				}

			})
			.catch(function(){
				console.log('FAILURE!!');
			});
		},
	},
};
</script>

<style scoped>
.styleba{
	border-radius: 10px;
}
.styleba2{
	border-radius: 10px;}
.addbutton{
	font-size: 12px;
	font-weight: bold;
	padding-left: 12px;
	padding-right: 12px;
}

.top{
	font-size: 18px;
	margin-left:10px;
	background-color: white;
	color: #0080fc;
	font-weight: bold;
	/*box-shadow: 1px 1px 3px #a4a4a4*/
}

.toppd{
	background-color: white;
	padding: 15px;	
	color: #0080fc;
	font-weight: bold;
}	

.line{
	background-color: white;
	padding: 15px;
	padding-left: 30px;
	margin-bottom: 8px;
	/*box-shadow: 1px 1px 3px #a4a4a4*/
}

.pencil{
	color: blue;
	margin-right: 4px;
}
.trash{
	color: red;
	margin-left: 4px;
}
.right{
	text-align: right;
}

.body{
	background: #FFFFFF 0% 0% no-repeat padding-box;
	border: 1px solid #DEE3EA;
	border-radius: 5px 5px 0px 0px;
	opacity: 1;
	padding: 5px;
}

.buttonvoltar{
	text-align: right;
}
.namecss{
	font-size: 18px;
	margin-left:10px;
}
table>thead,
table>thead>tr{
	pointer-events: none;
}
</style>