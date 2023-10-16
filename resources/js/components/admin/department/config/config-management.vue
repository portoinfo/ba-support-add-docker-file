<template>
	<div>
		<div>
			<span class="title">{{$t('bs-department-attendants')}}</span>
			<table class="table table-borderless table-hover local-striped-table mt-2">
				<thead>
					<tr class="table-light styleba">
						<th scope="col">
							<span id="tooltip-department-management-attendant">
								{{$t('bs-department-clerk')}} {{$t(itemselected.name)}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
							</span>
						</th>
						<b-tooltip target="tooltip-department-management-attendant" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-tooltip-department-management-attendant')}}
						</b-tooltip>
						<th scope="col">{{$t('bs-departments')}}</th>
						<th width="1px;"></th>
					</tr>
				</thead>
				<tbody>
					<tr class="table-light styleba2"
						v-for="(item, index) in agents" 
						:key="index"
						v-if="item.is_active == 1">
						<td>{{item.name}}</td>
						<td>{{$t(item.departamentos)}}</td>
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
			<span class="title">{{$t('bs-all-attendants')}}</span>
			<table class="table table-borderless table-hover local-striped-table mt-2">
				<thead >
					<tr class="table-light styleba">
						<th scope="col">{{$t('bs-attendants')}}</th>
						<th scope="col">{{$t('bs-departments')}}</th>
						<th width="1px;"></th>
					</tr>
				</thead>
				<tbody>
					<tr class="table-light styleba2" 
						v-for="(item, index) in agents"  
						:key="index"
						v-if="item.is_active != 1"> 
						<td>{{item.name}}</td>
						<td>{{$t(item.departamentos)}}</td>
						<td><a href="#" v-on:click.stop.prevent="itemInsert(item, index)"><i class="fa fa-plus pencil" aria-hidden="true"></i></a></td>
					</tr>
					<tr v-if="semAtendentes <= 0" class="blank_row">
						<td colspan="3"><center>{{$t('bs-no-attendant-registered')}}.</center></td>
					</tr>
				</tbody>
			</table>
		</div>
	<vue-snotify></vue-snotify>
	</div>
</template>

<script>

export default {
	data(){
		return {
			agents: [],
			show: true,
			show1: false,
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
	created(){
		var vm = this;
		var url2 = `${this.base_url}/department/get-agents/${vm.itemselected.id}`;
		axios.get(url2).then(function(r_resposta){
			vm.agents = r_resposta.data.result;
			vm.AjusteTecnico();
			vm.comAtendentes = r_resposta.data.contador;
			vm.semAtendentes = vm.agents.length - r_resposta.data.contador;
		}).catch(function (error) {
			console.log(error);
		});
	},
	methods:{
		AjusteTecnico(){
			var vm = this;
			for (let i = 0; i < vm.agents.length; i++) {
				const element = "";
				const array = vm.agents[i].departamentos.split(', ');
				for (let f = 0; f < array.length; f++) {
					if(f == 0){
						element = vm.$t(array[f])
					}else{
						element = element+ ', '+ vm.$t(array[f])
					}
				}
				vm.agents[i].departamentos = element.toString();
				console.log(vm.agents[i].departamentos);
			}
		},
		itemInsert(item, index){
			var vm = this;
			axios.post(`${this.base_url}/department/add-user-department`, {
				id_user: item.company_user_id,
				id_department: vm.itemselected.id,
			}).then(function(response){
				//console.log(response.data);

				if(response.data.success){

					if(vm.agents[index].departamentos == null){
						vm.agents[index].departamentos = vm.itemselected.name;
					}else{
						var teste = vm.agents[index].departamentos.split(', ');
						teste.push(vm.$t(vm.itemselected.name));
						if(teste[0] == ""){
							teste.splice(0, 1);
						}
						vm.agents[index].departamentos = teste.toString().replace(',', ', ');
					}

					vm.agents[index].is_active = 1;

					vm.comAtendentes += 1;
					vm.semAtendentes -= 1;
					vm.$emit('addAgent', vm.$t('bs-department-configuration'), true);
					vm.$snotify.success(vm.$t('bs-user-successfully-inserted'), vm.$t('bs-success'));

				}else{
					vm.$snotify.error(vm.$t('bs-error-inserting-user'), vm.$t('bs-error'));
				}

			})
			.catch(function(){
				console.log('FAILURE!!');
			});
		},
		itemDelete(item, index){
			var vm = this;
			axios.post(`${this.base_url}/department/remove-user-department`, {
				id_user: item.company_user_id,
				id_department: vm.itemselected.id,
			}).then(function(response){
				//console.log(response.data);
				if(response.data.success){
					var teste = vm.agents[index].departamentos.split(', ');
					for (var i = teste.length - 1; i >= 0; i--) {
						if(vm.$t(teste[i]) == vm.$t(vm.itemselected.name)){
							teste.splice(i, 1);
						}
					}
					//console.log(teste);

					vm.agents[index].departamentos = teste.toString().replace(',', ', ');

					vm.comAtendentes -= 1;
					vm.semAtendentes += 1;
					vm.agents[index].is_active = 0;
					vm.$snotify.success(vm.$t('bs-successfully-removed'), vm.$t('bs-success'));
				}else{
					vm.$snotify.error(vm.$t(response.data.error), vm.$t('bs-error'));
				}

			})
			.catch(function(){
				console.log('FAILURE!!');
			});
		},
	}
};
</script>

<style scoped>

.title{
	font: normal normal bold 18px/22px Muli;
}

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
	color: green;
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
	background-color: white;
	margin-bottom:10px;
	padding: 15px;
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
	pointer-events: none!important;
}
#tooltip-department-management-attendant{
	pointer-events:  auto!important;
}
</style>