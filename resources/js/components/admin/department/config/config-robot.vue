<template>
	<div>
		<b-form @submit.prevent="atualizarLista">
			<b-row>
				<b-col>
					<li class="list-group-item isactive mb-2">
						<div class="form-row align-items-center"> 
							<div class="col-auto" id="active-robot">
								{{check_active}} &nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
								<b-tooltip target="active-robot" triggers="hover" placement="right" :variant="tooltipVariant">
									{{check_active_tooltip}}
								</b-tooltip>
							</div>
							<!-- {{ settings.is_active }} -->
							<div class="col" style="text-align: right;margin-top:8px;">
								<label class="switch">
									<input type="checkbox" v-model="settings.is_active" @click="active(settings.is_active)">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
					</li>
				</b-col>
			</b-row>
			<b-row>
				<b-col>
					<li class="list-group-item isactive">
						<div class="form-row align-items-center"> 
							<div class="col-auto" id="robot-finale">
								{{$t('bs-suggest-creating-a-chat-at-the-end-of-the')}}: &nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
								<b-tooltip target="robot-finale" triggers="hover" placement="right" :variant="tooltipVariant">
									{{$t('bs-if-this-option-is-checked-if-the-robot-doe')}}
								</b-tooltip>
							</div>
							<div class="col" style="text-align: right;margin-top:8px;">
								<label class="switch">
									<input type="checkbox" v-model="settings.robot_finale" @click="active(settings.robot_finale)">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
					</li>
				</b-col>
			</b-row>
			<br>
			<b-row class="justify-custom" v-if='idSelected != ""'>
				<b-col>
					<b-form-group class="mt-2" :label="$t('bs-options')+': '+ textTypeSelected" v-slot="{ ariaDescribedby }"> 
						<b-form-select :aria-describedby="ariaDescribedby" v-model="typeSelected" :options="options"></b-form-select>
					</b-form-group>
				</b-col>
				<b-col cols="auto" class="mt-4">
					<b-form-checkbox class="mt-4 mb-1" @change="settime" v-model="checkTime">
						<span class="material-icons-outlined blue">schedule</span> <span>{{$t('bs-time')}}</span>
					</b-form-checkbox>
				</b-col>
			</b-row>
			<b-row class="justify-custom" v-if='idSelected != ""'>
				<b-col>
					<label for="text-1"> {{descriptionLabel}}:</label>
					<b-form-input
						id="input-1"
						type="text"
						class="bs-input"
						v-model="questionSelected"
					></b-form-input>
				</b-col>
				<b-col v-if="showinputLink">
					<label for="text-2"> {{$t('bs-link')}}:</label>
					<b-form-input
						id="input-2"
						type="text"
						class="bs-input"
						v-model="inputLink"
					></b-form-input>
				</b-col>
				<b-col v-show="showInputTransferDepartment">
					<label for="text-4"> {{$t('bs-departments')}}:</label>
					<b-form-select v-model="inputTransferDepartment" :options="departments"></b-form-select>
				</b-col>
				<b-col v-if="showInputTime">
					<label for="text-3">{{$t('bs-waiting-time-minutes')}}:</label>
					<b-form-input
						id="input-3"
						type="number"
						class="bs-input"
						v-model="inputTime"
					></b-form-input>
				</b-col>
				<b-col cols="auto" class="mt-2">
					<b-button type="submit" class="bs-btn-save mt-3" variant="success" >{{$t('bs-save')}}</b-button>
				</b-col>
			</b-row>
		</b-form>
		<br>
		<organization-chart :zoom="false" class='nodeCustom' :datasource="ds"  :pan="true">
			<template slot-scope="{ nodeData }">
					<center v-if="nodeData.root == 'start'">
						<div class="circle" @click="CreateStart">{{$t('bs-start')}}</div>
					</center>
					<div @click="EditarCard(nodeData)" v-else>
						<div class="titleCustom text-dark" :class="{ 'selected': nodeData.id == idSelected,  'notselected': nodeData.id != idSelected, }"> 
							<!-- <i class="fa fa-users symbol"></i> -->
							{{ nodeData.text }}
						</div>
						<div class="contentCustom">
							<span class="material-icons-outlined blue">{{nodeData.type | IconConvert}}</span>
						</div>
					</div>
					<b-badge v-if='idSelected == nodeData.id && !checkisChildrenText' @click="atualizarLista(nodeData, 'create')" variant="primary">{{$t('bs-add')}}</b-badge>
					<b-badge v-if='idSelected == nodeData.id' @click="atualizarLista(nodeData, 'delete')" variant="danger">{{$t('bs-delete')}}</b-badge>
			</template>
		</organization-chart>
		<br>
		<span></span>
		<b-row class="justify-custom">
			<b-col class="mt-1">
				<label for="text-4"> {{$t('bs-load-lrobot-lfrom-another-department')}}: </label>
				<b-form-select v-model="loadRobotDepartment" :options="departments"></b-form-select>
			</b-col>
			<b-col cols="auto" class="mt-3">
				<b-button type="button" class="bs-btn-save mt-3" variant="success" @click="loadingRobot">{{$t('bs-load')}}</b-button>
			</b-col>
		</b-row>
		<br><br><br><br><br><br>
	</div>
</template>

<script>
	import OrganizationChart from 'vue-organization-chart'
  	import 'vue-organization-chart/dist/orgchart.css'

export default {
	data(){
		return {
			tooltipVariant: 'secondary',
			check_active: this.$t('bs-activate') + ': ',
			check_active_tooltip: this.$t('bs-if-this-option-is-checked-the-robot-will-n'),
			quests: [],
			quest: "",
			id: 0,
			caminho: [],
			lbWay: this.$t('bs-question-and-answer-path') + ': ',
			phQuestion: this.$t('bs-question'),
			questionAnswer: this.$t('bs-question-or-answer'),
			showCaminho: false,
			nulle: null,
			options: [
				{ value: 'default_button', text: this.$t('bs-button') },
				{ value: 'text', text: this.$t('bs-text') },
				{ value: 'link', text: this.$t('bs-link') },
				{ value: 'create_chat', text: this.$t('bs-create-chat') },
				{ value: 'create_ticket', text: this.$t('bs-create-ticket') },
				{ value: 'transfer_department', text: this.$t('bs-transfer-department') },
				{ value: 'to_jump', text: this.$t('bs-jump') },
			],
			ds:{ 
				'id': '0',
				'text': 'START',
				'type': [],
				'root': 'start',
				// 'children': [{ 
				// 	'id': '1', 'text': 'Olá senhor cliente, o suporte está em recesso nosso guia irá lhe auxiliar. por favor seleciona uma das opções', 
				// 	'type': ["text"],
				// 	'children': [
				// 		{ 'id': '8', 'text': 'Dominio', 'type': [] },
				// 		{ 'id': '3', 'text': 'Site Offiline', 'type': [] },
				// 		{ 'id': '4', 'text': 'Mailingboss', 'type': [] },
				// 	]
				// 	},
				// ]
			},
			questionSelected: '',
			typeSelected: '',
			textTypeSelected: '',
			idSelected: '',
			showinputLink: false,
			inputLink: '',
			checkTime: '',
			showInputTime: false,
			showInputTransferDepartment: false,
			inputTime: '',
			inputTransferDepartment: '',
			departments: [],
			checkboxShow: {
				to_jump: true,
				text: true,
				link: true,
				time: true,
				create_chat: true,
				create_ticket: true,
				transfer_department: true,
				default_button: true,
			},
			contadorGlobal: 1,
			checkisChildrenText: false,
			descriptionLabel: this.$t('bs-inform-the-text'),
			loadRobotDepartment: '',
		}
	},
	props:{
		itemselected: Object,
		settings: Object,
		base_url: {
			type: String,
			default: ''
		},
	},
	components:{
		OrganizationChart,
	},
	mounted(){
		var vm = this;
		vm.getDepartments();
		var url = `${this.base_url}/department/get-robot/${vm.itemselected.id}`;
		axios.get(url).then(function(r_resposta){
			vm.ds = JSON.parse(r_resposta.data.result.quest);
			// console.log(vm.ds);
			vm.setCountCreateCard();
		}).catch(function (error) {
			console.log(error);
		});
		vm.loadRobotDepartment = vm.itemselected.id;
		vm.active(!vm.settings.is_active);
	},
	watch:{
		typeSelected(){
			
			// SETAR TEXTO EXPLICATIVO.
			if(this.typeSelected == 'to_jump'){
				this.textTypeSelected = this.$t('bs-t-o-w-give-the-possibility-to-finish-the-s');
			}
			if(this.typeSelected == 'text'){
				this.descriptionLabel = this.$t('bs-inform-the-text');
				this.textTypeSelected = this.$t('bs-t-o-w-be-presented-in-text-format');
			}
			if(this.typeSelected == 'link'){
				this.descriptionLabel = this.$t('Nome do botão: ');
				this.textTypeSelected = this.$t('bs-t-o-w-be-presented-in-link-format');
			}
			if(this.typeSelected == 'create_chat'){
				this.descriptionLabel = this.$t('bs-inform-the-text');
				this.textTypeSelected = this.$t('bs-t-o-w-give-the-possibility-to-open-a-chat-');
			}
			if(this.typeSelected == 'create_ticket'){
				this.descriptionLabel = this.$t('bs-inform-the-text');
				this.textTypeSelected = this.$t('bs-t-o-w-give-the-possibility-of-opening-a-ti');
			}
			if(this.typeSelected == 'transfer_department'){
				this.descriptionLabel = this.$t('bs-inform-the-text');
				this.textTypeSelected = this.$t('bs-t-o-w-give-you-the-possibility-to-transfer');
			}
			if(this.typeSelected == 'default_button'){
				this.descriptionLabel = this.$t('bs-inform-the-text');
				this.textTypeSelected = this.$t('bs-t-o-w-be-displayed-in-a-button-format');
			}
			if(this.checkTime == 'time'){
				this.descriptionLabel = this.$t('bs-inform-the-text');
				this.textTypeSelected = this.$t('Ao inserir uma tempo o cliente deve esperar esse tempo para prosseguir no robô.');
			}


			// DESATIVAR OU ATIVAR INPUTS
			if(this.typeSelected == 'transfer_department'){
				this.showInputTransferDepartment = true;
			}else{
				this.showInputTransferDepartment = false;
			}
			if(this.typeSelected == 'link'){
				this.showinputLink = true;
			}else{
				this.showinputLink = false;
			}
		}
	},
	methods:{
		loadingRobot(){
			var vm = this;

			if(vm.itemselected.id == vm.loadRobotDepartment){
				return vm.$snotify.info(vm.$t('Selecione outro departmaento'), vm.$t('bs-info'));
			}


			Swal.fire({
				title: vm.$t('bs-are-you-sure'),
				text: vm.$t('bs-you-wont-be-able-to-revert-this'),
				icon: 'warning',
				showCancelButton: true,
				cancelButtonText: vm.$t('bs-cancel'),
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: vm.$t('bs-yes'),
			}).then((result) => {
				if (result.isConfirmed) {
					var url = `${this.base_url}/department/get-robot/${vm.loadRobotDepartment}`;
					axios.get(url).then(function(r_resposta){
						vm.ds = JSON.parse(r_resposta.data.result.quest);
						vm.setCountCreateCard();
						vm.SaveDB();
					}).catch(function (error) {
						console.log(error);
					});
				}
			})

		},
		settime(){
			console.log(this.checkboxShow.time);
			this.showInputTime = this.checkTime;
		},
		getDepartments(){
			axios.get('/get-departments-by-company')
			.then(res => {
				// console.log(res.data)
				this.departments = res.data;
			})
			.catch(err => {
				console.error(err); 
			})
		},
		atualizarLista(item, type = 'default'){
			var vm = this;
			
			let checkisValidade = true;
			var bro = vm.getBrother(vm.ds.children, vm.idSelected);
			if(bro.length > 1){
				bro.forEach(element => {
					if(element.type == 'text'){
						checkisValidade = false;
					}else if(vm.typeSelected == 'text'){
						checkisValidade = false;
					}
				});
					vm.$snotify.info(vm.$t('bs-text-type-items-must-be-unique'), vm.$t('bs-info'));
			}

			if(checkisValidade){
				if(type == 'create'){
					//CHECAR PRA VER SE NÃO TEM UM FILHO
					var object = vm.getParent(vm.ds.children, item.id);
					if(object.children == undefined){
						object.children = [{
							'id': vm.contadorGlobal, 
							'text': 'Card' + vm.contadorGlobal, 
							'type': ''
						}];
					}else{
						object.children.push({
							'id': vm.contadorGlobal, 
							'text': 'Card' + vm.contadorGlobal, 
							'type': ''
						});
					}
					vm.SaveDB();
					vm.setCountCreateCard();
				}else if(type == 'delete'){
					Swal.fire({
						title: vm.$t('bs-are-you-sure'),
						text: vm.$t('bs-you-wont-be-able-to-revert-this'),
						icon: 'warning',
						showCancelButton: true,
						cancelButtonText: vm.$t('bs-cancel'),
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: vm.$t('bs-yes-delete-it'),
					}).then((result) => {
						if (result.isConfirmed) {
							vm.deleteParent(vm.ds.children, item.id);
							vm.SaveDB();
						}
					})

				}else{ // EDIÇÃO DO CARD
					// console.log(vm.ds.children);
					var object = vm.getParent(vm.ds.children, vm.idSelected);
					object.text = vm.questionSelected;
					object.type = vm.typeSelected;
					object.inputTime = vm.inputTime;
					object.inputLink = vm.inputLink; 
					object.inputTransferDepartment = vm.inputTransferDepartment;
					// vm.$snotify.success(vm.$t('editado com sucesso'), vm.$t('bs-success'));
					vm.SaveDB();
				}

				vm.typeSelected = '';
				vm.questionSelected = '';
				vm.idSelected = '';
			}
			

		},
		EditarCard(item){
			var vm = this;
			vm.checkisChildrenText = false;
			if(item.children && item.children.length > 0){
				item.children.forEach(element => {
					if(element.type == 'text'){
						vm.checkisChildrenText = true;
					}
				});
			}

			vm.idSelected = item.id;
			vm.questionSelected = item.text;
			vm.typeSelected = item.type;
			
			if(item.type == null || item.type == ''){
				vm.typeSelected = 'default_button';
			}

			vm.inputTransferDepartment = item.inputTransferDepartment;
			if(!vm.inputTransferDepartment){
				vm.showInputTransferDepartment = false;
				vm.inputTransferDepartment = null;
			}

			vm.inputTime = item.inputTime;
			if(vm.inputTime){
				vm.showInputTime = true;
				vm.checkTime = true;
			}else{
				vm.showInputTime = false;
				vm.checkTime = false;
				vm.inputTime = '';
			}

			vm.inputLink = item.inputLink;
			if(!vm.inputLink){
				vm.showinputLink = false;
				vm.inputLink = '';
			}

			vm.textTypeSelected = '';
			
		},
		ValidadeInputes(){
			var vm = this;
			if(vm.typeSelected.length == 0){
				vm.checkBoxValues(true);
			}else{
				vm.typeSelected.forEach(function(element){
					if(element == 'text'){
						vm.checkBoxValues(true);
						vm.checkboxShow.link = false;
						vm.checkboxShow.default_button = false;

						var index = vm.typeSelected.indexOf('default_button');
						var index2 = vm.typeSelected.indexOf('link');
						if (index > -1) {
							vm.typeSelected.splice(index, 1);
						}
						if (index2 > -1) {
							vm.typeSelected.splice(index, 1);
						}
					}
					if(element == 'link'){
						vm.checkBoxValues(true);
						vm.checkboxShow.text = false;
						vm.checkboxShow.default_button = false;
						var index3 = vm.typeSelected.indexOf('text');
						if (index3 > -1) {
							vm.typeSelected.splice(index, 1);
						}
						var index4 = vm.typeSelected.indexOf('default_button');
						if (index4 > -1) {
							vm.typeSelected.splice(index, 1);
						}
					}
					if(element == 'time'){
						vm.checkBoxValues(true);
						vm.showInputTime = true;

						var index3 = vm.typeSelected.indexOf('transfer_department');
						if (index3 > -1) {
							vm.typeSelected.splice(index, 1);
						}
					}
					if(element == 'transfer_department'){
						vm.checkBoxValues(true);
						vm.showInputTransferDepartment = true;
						var index3 = vm.typeSelected.indexOf('time');
						if (index3 > -1) {
							vm.typeSelected.splice(index, 1);
						}
					}
					if(element == 'default_button'){
						vm.checkBoxValues(true);
					}
				});
			}
		},
		setCountCreateCard(){
			var vm = this;
			var count = 1;
			if(vm.ds.children != undefined){
				while(vm.getParent(vm.ds.children, count) != null){
					count++
				}
				vm.contadorGlobal = count;
			}
		},
		CreateStart(){
			if(this.ds.children == undefined || this.ds.children.length == 0){
				this.ds = {
					'id': '0',
					'text': 'START',
					'type': '',
					'root': 'start',
					'children': [{ 
						'id': '1',
						'text': 'Card1',
						'type': '',
					}]
				};
				this.setCountCreateCard();
			}
		},
		checkBoxValues(booleano){
			var vm = this;
			vm.checkboxShow.to_jump = booleano;
			vm.checkboxShow.text = booleano;
			vm.checkboxShow.link = booleano;
			vm.checkboxShow.time = booleano;
			vm.checkboxShow.create_chat = booleano;
			vm.checkboxShow.create_ticket = booleano;
			vm.checkboxShow.transfer_department = booleano;
			vm.checkboxShow.default_button = booleano;
			vm.showinputLink = false;
			vm.showInputTime = false;
			vm.showInputTransferDepartment = false;
		},
		getBrother(root, id) {
			var vm = this;
			var node;
			root.some(function (n) {
				if (n.id == id) {
					return node = root;
				}
				if (n.children) {
					return node = vm.getBrother(n.children, id);
				}
			});
			return node || null;
		},
		getParent(root, id) {
			var vm = this;
			var node;
			root.some(function (n) {
				if (n.id == id) {
					return node = n;
				}
				if (n.children) {
					return node = vm.getParent(n.children, id);
				}
			});
			return node || null;
		},
		deleteParent(root, id) {
			// console.log(root);
			// console.log(id);
			var vm = this;
			var del = [];
			var count = 0;
			root.forEach(element => {
				if(element.id == id) {
					return root.splice(count, 1);
				}
				if(element.children) {
					count++;
					return vm.deleteParent(element.children, id);
				}
			});
		},
		active(item){
			var vm = this;
			if(item){
				vm.check_active = this.$t('bs-active');
				vm.check_active_tooltip = this.$t('bs-if-this-option-is-checked-the-robot-will-n');
			}else{
				vm.check_active = this.$t('bs-disabled');
				vm.check_active_tooltip = this.$t('bs-if-this-option-is-checked-the-robot-will-b');
			}

			var my_object = {
				is_active: vm.settings.is_active,
				robot_finale: vm.settings.robot_finale,
			};

			console.log(my_object);
			vm.$emit('addRobot', my_object);

		},
		SaveDB(){
			var vm = this;
			axios.post(`${vm.base_url}/department/set-robot`, {
				ds: vm.ds,
				id: vm.itemselected.id,
			}).then(function(response){
				if(response.data.success){
					// vm.$snotify.success(vm.$t('bs-question-saved-successfully'), vm.$t('bs-success'));
					vm.textTypeSelected = '';
				}else{
					// vm.$snotify.error(vm.$t('bs-error-saving-question'), vm.$t('bs-error'));
				}
			})
			.catch(function(e){
				console.log(e);
				console.log('FAILURE!!');
			});
		},
	},
	filters: {
		convert: function (text) {
			if(text == 'to_jump'){
				return 'Pular, ';
			}
			if(text == 'text'){
				return 'Texto, ';
			}
			if(text == 'link'){
				return 'Link, ';
			}
			if(text == 'time'){
				return 'Time, ';
			}
			if(text == 'create_chat'){
				return 'Criar chat, ';
			}
			if(text == 'create_ticket'){
				return 'Criar ticket, ';
			}
			if(text == 'transfer_department'){
				return 'Transferir para outro departamento, ';
			}
			if(text == 'default_button'){
				return 'Botão, ';
			}
		},
		IconConvert: function (text) {
			if(text == 'to_jump'){
				return 'arrow_upward';
			}
			if(text == 'text'){
				return 'text_format';
			}
			if(text == 'link'){
				return 'link';
			}
			if(text == 'time'){
				return 'schedule';
			}
			if(text == 'create_chat'){
				return 'chat';
			}
			if(text == 'create_ticket'){
				return 'confirmation_number';
			}
			if(text == 'transfer_department'){
				return 'change_circle';
			}
			if(text == 'default_button'){
				return 'touch_app';
			}
		},
	}
};
// https://pt.stackoverflow.com/questions/471828/buscar-id-em-array-de-objetos-com-javascript
</script>

<style scoped>

.blue{
	color: #0080FC;
	font-size: 16px;
}

.orgchart .nodeCustom {
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    display: inline-block;
    position: relative;
    margin: 0;
    padding: 3px;
    border: 2px dashed transparent;
    text-align: center;
    width: 130px;
}

.titleCustom{
    text-align: center;
    font-size: 12px;
    font-weight: 700;
    /* height: 20px; */
    line-height: 20px;
    /* overflow: hidden;
    white-space: nowrap; */
    color: #fff;
    border-radius: 4px 4px 0 0;
	border: 1px solid #BDBDBD;
}

.contentCustom{
	-webkit-box-sizing: border-box;
    box-sizing: border-box;
    width: 100%;
    height: 20px;
    font-size: 11px;
    line-height: 18px;
    border: 1px solid #0080FC;
    border-radius: 0 0 4px 4px;
    text-align: center;
    background-color: #fff;
    color: #333;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}



.circle{
	background-color:#0080FC;
	padding-top: 3px;
	padding-bottom: 3px;
	color: white;
	border-radius: 100% !important ;
	justify-content: center !important;
}

.selected{
	background-color:#0080FC !important ;
	color: white !important ;
}

.notselected{
	background-color:#F7F8FC!important ;
}

.justify-custom{
	font: normal normal bold 14px/18px Muli;
	color: #707070;
	max-width: 99%;
}

.isactive{
	margin:0;
	padding:10px;
	padding-top:0px;
	padding-bottom:0px;
	font: normal normal bold 14px/18px Muli;
	color: #707070;
	max-width: 97%;
}
.bs-input{
	border: 1px solid #a4a4a4;
	padding-top: 18px;
	padding-bottom: 18px;
	padding-left: 20px;
	text-align: left;
	font: normal normal normal 14px/18px Muli;
	letter-spacing: 0px;
	opacity: 0.75;
}
.caminho{
	text-align: left;
	margin-left: 10px;
	font: normal normal bold 14px/18px Muli;
	letter-spacing: 0px;
	color: #707070;
	opacity: 1;
}




.switchLabel{
	margin-top: 7px;
	margin-right: 7px;
}
.switch {
	position: relative;
	display: inline-block;
	width: 50px;
	height: 28px;
}
.switch input {
	opacity: 0;
	width: 0;
	height: 0;
}
.slider {
	position: absolute;
	cursor: pointer;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background-color: #ccc;
	-webkit-transition: .4s;
	transition: .4s;
}
.slider:before {
	position: absolute;
	content: "";
	height: 20px;
	width: 20px;
	left: 4px;
	bottom: 4px;
	background-color: white;
	-webkit-transition: .4s;
	transition: .4s;
}
input:checked + .slider {
	background-color: #00C38E;
}
input:focus + .slider {
	box-shadow: 0 0 1px #2196F3;
}
input:checked + .slider:before {
	-webkit-transform: translateX(22px);
	-ms-transform: translateX(22px);
	transform: translateX(22px);
}
/* Rounded sliders */
.slider.round {
	border-radius: 34px;
}
.slider.round:before {
	border-radius: 50%;
}
</style>
