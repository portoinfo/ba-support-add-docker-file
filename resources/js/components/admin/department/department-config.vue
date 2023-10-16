<template>
	<div>
		<b-container fluid="lg">
			<b-row>
				<b-col cols="auto" class="mr-auto p-3 title">{{$t('bs-department-configuration')}}
					<b-card-text class="subtitle">
						{{$t('bs-department')}} {{$t(itemselected.name)}}
					</b-card-text>
				</b-col>
				<b-col cols="auto mt-4">
					<b-button @click="btnBack" variant="light btn-back">{{$t('bs-back')}}</b-button>
					<b-button @click="saveAllConfigs" variant="btn btn-save" class="btn btn-success" :disabled="!allowedToSave"><i class="fa fa-floppy-o" aria-hidden="true"></i> {{$t('bs-save')}}</b-button>
				</b-col>
			</b-row>
			<br>

			<b-alert fade show variant="primary" class="bui-alert">
				<slot v-if="show.show1">
					<h5>
						{{$t('bs-general')}}
					</h5>
					<p>
						{{$t('bs-on-this-tab-you-can-make-some-general-pur')}}
					</p>
				</slot>
				<slot v-if="show.show2">
					<h5>
						{{$t('bs-manage-assistants')}}
					</h5>
					<p>
						{{$t('bs-on-this-tab-you-can-add-or-remove-clerks')}}
					</p>
				</slot>
				<slot v-if="show.show3">
					<h5>
						{{$t('bs-text-shortcut')}}
					</h5>
					<p>
						{{$t('bs-In-this-tab-you-can-configure-text-shortc')}}
					</p>
				</slot>
				<slot v-if="show.show4">
					<h5>
						{{$t('bs-limitations')}}/{{$t('bs-quantities')}}
					</h5>
					<p>
						{{$t('bs-on-this-tab-you-can-configure-service-lim')}}
					</p>
				</slot>
				<slot v-if="show.show5">
					<h5>
						{{$t('bs-robot')}}
					</h5>
					<p>
						{{$t('bs-in-this-tab-you-can-configure-the-bot-to')}}
					</p>
				</slot>
				<slot v-if="show.show6">
					<h5>
						{{$t('bs-evaluation')}}
					</h5>
					<p>
						{{$t('bs-in-this-tab-you-can-configure-how-your-se')}}
					</p>
				</slot>
				<slot v-if="show.show7">
					<h5>
						{{$t('bs-chat')}}
					</h5>
					<p>
						{{$t('bs-in-this-tab-you-can-configure-the-Chats-b')}}
					</p>
				</slot>
				<slot v-if="show.show8">
					<h5>
						{{$t('bs-ticket')}}
					</h5>
					<p>
						{{$t('bs-on-this-tab-you-can-configure-the-behavio')}}
					</p>
				</slot>
			</b-alert>


			<div v-if="show.show" class="row">
				<div class="col-auto bs-m-spacing">
					<a v-on:click.stop="showIB(1)" href="#" :class="ss.ss1">{{$t('bs-general')}}</a>
				</div>
				<div class="col-auto bs-m-spacing">
					<a v-on:click.stop="showIB(2)" href="#" :class="ss.ss2">{{$t('bs-manage-assistants')}}</a>
				</div>
				<div class="col-auto bs-m-spacing">
					<a v-on:click.stop="showIB(3)" href="#" :class="ss.ss3">{{$t('bs-text-shortcut')}}</a>
				</div>
				<div class="col-auto bs-m-spacing">
					<a v-on:click.stop="showIB(4)" href="#" :class="ss.ss4">{{$t('bs-limitations')}}/{{$t('bs-quantities')}}</a>
				</div>
				<div class="col-auto bs-m-spacing">
					<a v-on:click.stop="showIB(5)" href="#" :class="ss.ss5">{{$t('bs-robot')}}</a>
				</div>
				<div class="col-auto bs-m-spacing">
					<a v-on:click.stop="showIB(6)" href="#" :class="ss.ss6">{{$t('bs-evaluation')}}</a>
				</div>
				<div class="col-auto bs-m-spacing">
					<a v-on:click.stop="showIB(7)" href="#" :class="ss.ss7">CHAT</a>
				</div>
				<div class="col-auto bs-m-spacing">
					<a v-on:click.stop="showIB(8)" href="#" :class="ss.ss8">TICKET</a>
				</div>
			</div><br>

		</b-container>
		<div v-if="show.show1">
			<config-general v-on:addGeneral="addGeneral" :idSettings="idSettings_cds" :settings="settings.general" :itemselected="itemselected" :timezones="timezones" :base_url="base_url"></config-general>
		</div>
		<div v-if="show.show2">
			<config-management v-on:addAgent="addAgent" :title='title' :subtitle='subtitle' :itemselected="itemselected" :base_url="base_url"></config-management>
		</div>
		<div v-if="show.show3">
			<config-auto-answer v-on:addCommand="addCommand" v-on:deleteCommand="deleteCommand" v-on:updateCommand="updateCommand"  :settings="settings.commands" :idSettings="idSettings_cds" :itemselected="itemselected" :base_url="base_url"></config-auto-answer>
		</div>
		<div v-if="show.show4">
			<config-quantity-limitations :idSettings="idSettings_cds" :settings="settings.quant_limity" :itemselected="itemselected" v-on:addQuantLimity="addQuantLimity" :base_url="base_url"></config-quantity-limitations>
		</div>
		<div v-if="show.show5">
			<config-robot :itemselected="itemselected" :settings="settings.robot"  v-on:addRobot="addRobot" :base_url="base_url"></config-robot>
		</div>
		<div v-if="show.show6">
			<config-evaluation :idSettings="idSettings_cds" :itemselected="itemselected" :settings="settings.evaluation" v-on:addEvaluation="addEvaluation" :base_url="base_url"></config-evaluation>
		</div>
		<div v-if="show.show7">
			<config-chat :itemselected="itemselected" v-on:addChat="addChat" :settings="settings.chat" :general="settings.general" :base_url="base_url"></config-chat>
		</div>
		<div v-if="show.show8">
			<config-ticket :itemselected="itemselected" v-on:addTicket="addTicket" :settings="settings.ticket" :general="settings.general" :base_url="base_url" :company_id="company_id"></config-ticket>
		</div>
		<vue-snotify></vue-snotify>
	</div>
</template>

<script>

export default {
	data(){
		return {
			ss: {
				'ss1': 'tab active',
				'ss2': 'tab',
				'ss3': 'tab',
				'ss4': 'tab',
				'ss5': 'tab',
				'ss6': 'tab',
				'ss7': 'tab ',
				'ss8': 'tab ',
			},
			show: {
				'show': true,
				'show1': false,
				'show2': false,
				'show3': false,
				'show4': false,
				'show5': false,
				'show6': false,
				'show7': false,
				'show8': false,
			},
			idSettings_cds: "",
			idSettings_cd: "",
			settings: [],
			allowedToSave: true,
		}
	},
	props:{
		itemselected: Object,
		title: String,
		subtitle: String,
		timezones: Object,
		base_url: {
			type: String,
			default: ''
		},
		company_id: String,
	},
	mounted(){
	 	var vm = this;
		var url = `${this.base_url}/department/get-setting`;

		axios.get(url, {
            params: {
            id: vm.itemselected.id,
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
            }
        }).then(function(r_resposta){
			//console.log(r_resposta.data.result[0].settings);
			vm.idSettings_cds = r_resposta.data.company_department_settings_id;
			vm.idSettings_cd = r_resposta.data.company_department_id;
			vm.settings = r_resposta.data.value;
			vm.show.show1 = true;
		}).catch(function (error) {

			console.log(error);
		});
	},
	methods:{
		showIB(item){
			var vm = this;
			vm.show.show1 = false;
			vm.show.show2 = false;
			vm.show.show3 = false;
			vm.show.show4 = false;
			vm.show.show5 = false;
			vm.show.show6 = false;
			vm.show.show7 = false;
			vm.show.show8 = false;
			vm.ss.ss1 = 'tab';
			vm.ss.ss2 = 'tab';
			vm.ss.ss3 = 'tab';
			vm.ss.ss4 = 'tab';
			vm.ss.ss5 = 'tab';
			vm.ss.ss6 = 'tab';
			vm.ss.ss7 = 'tab';
			vm.ss.ss8 = 'tab';
			if(item == 1){
				vm.show.show1 = true;
				vm.ss.ss1 = 'tab active';
			}else if(item == 2){
				vm.show.show2 = true;
				vm.ss.ss2 = 'tab active';
			}else if(item == 3){
				vm.show.show3 = true;
				vm.ss.ss3 = 'tab active';
			}else if(item == 4){
				vm.show.show4 = true;
				vm.ss.ss4 = 'tab active';
			}else if(item == 5){
				vm.show.show5 = true;
				vm.ss.ss5 = 'tab active';
			}else if(item == 6){
				vm.show.show6 = true;
				vm.ss.ss6 = 'tab active';
			}else if(item == 7){
				vm.show.show7 = true;
				vm.ss.ss7 = 'tab active';
			}else if(item == 8){
				vm.show.show8 = true;
				vm.ss.ss8 = 'tab active';
			}
		},
		addAgent(item, value){
			var vm = this;
			vm.$emit('addAgent', item);
			vm.show.show = value;
		},
		addGeneral(item){
			var vm = this;
			vm.settings.general = item;
		},
		addCommand(item){
			//console.log(item);
			var vm = this;
			vm.settings.commands.push(item);
			vm.saveAllConfigs();
		},
		updateCommand(item, index){
			//console.log(item);
			var vm = this;
			vm.settings.commands[index] = item;
			vm.saveAllConfigs();
		},
		deleteCommand(index){
			var vm = this;
			vm.settings.commands.splice(index, 1);
			vm.saveAllConfigs();
		},
		addQuantLimity(item){
			var vm = this;
			vm.settings.quant_limity = item;
		},
		addEvaluation(item){
			var vm = this;
			vm.settings.evaluation = item;
		},
		addRobot(item){
			var vm = this;
			vm.settings.robot = item;
		},
		addTicket(item){
			var vm = this;
			vm.settings.ticket = item;
			// vm.saveAllConfigs();
		},
		addChat(item){
			var vm = this;
			vm.settings.chat = item;
			// vm.saveAllConfigs();
		},
		saveAllConfigs(){
			var vm = this;

			if(vm.settings.general.userLang == ''){
				return vm.$snotify.info(vm.$t('bs-select-a-country'), vm.$t('bs-info'));
			}

			if(vm.settings.general.language == ''){
				return vm.$snotify.info(vm.$t('bs-select-a-region'), vm.$t('bs-info'));
			}

			axios.post(`${this.base_url}/department/set-setting`, {
				id: vm.idSettings_cds,
				id_cd: vm.idSettings_cd,
				settings: vm.settings,
			}).then(function(response){
				//console.log(response.data);
				if(response.data.success){

					//console.log(vm.settings);

					vm.$snotify.success(vm.$t('bs-saved-successfully'), vm.$t('bs-success'));
				}else{
					vm.$snotify.error(vm.$t('bs-error-trying-to-save'), vm.$t('bs-error'));
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
.tab{
	padding: 8px;
	color:#A4A4A4;
	border-bottom: 1px solid #BDBDBD;
	font-weight: bold;
	text-decoration: none;
}
.buttonvoltar{
	text-align: right;
}
.active, .tab:hover{
	color: #0080fc;
	padding-left: 10px;
	border-bottom: 3px solid #0080fc;
}
.line{
	padding:5px;
	margin:5px;
	border-bottom: 1px solid #BDBDBD;
}
.marginbutton{
	margin-top:35px;
	margin-bottom:15px;
}

.bs-m-spacing{
	padding: 6px;
	text-transform: uppercase;
	margin-left: 1px;
}

@media screen and (max-width: 576px) {

	.bs-m-spacing{
		justify-content: center;
		text-align: center;
		padding: 8px;
		margin-left: 1px;
	}

	.active, .tab:hover{
		color: #0080fc;
		padding-left: 10px;
		border-bottom: 3px solid #0080fc;
	}
}

@media screen and (max-width: 1024px) {

	.bs-m-spacing{
		justify-content: center;
		text-align: center;
		padding: 8px;
		margin-left: 1px;
	}

	.active, .tab:hover{
		color: #0080fc;
		padding-left: 10px;
		border-bottom: 3px solid #0080fc;
	}
}


</style>
