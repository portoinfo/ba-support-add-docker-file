<template>
	<div>
		<b-container fluid="lg">
			<b-row>
				<b-col cols="auto" class="mr-auto p-3 title">{{$t('bs-permission-group-configuration')}}
					<b-card-text class="subtitle">
						{{$t('bs-manage-group-users')}} {{itemselected.name}}
					</b-card-text>
				</b-col>
				<b-col cols="auto mt-4">
					<b-button @click="btnBack" variant="light btn-back">{{$t('bs-back')}}</b-button>
					<b-button  v-show="!show.show1" @click="saveAllConfigs" variant="btn btn-save" class="btn btn-success"><i class="fa fa-floppy-o" aria-hidden="true"></i> {{$t('bs-save')}}</b-button>
				</b-col>
			</b-row>
			<br>
			<b-alert fade show variant="primary" class="bui-alert">
				<slot  v-if="show.show1">
					<h5>
						{{$t('bs-manage-group-users')}}
					</h5>
					<p>
						{{$t('bs-on-this-tab-you-can-add-or-remove-attendan')}}<br>
					</p>
				</slot>
				<slot  v-if="show.show2">
					<h5>
						{{$t('bs-manage-group-access')}}
					</h5>
					<p>
						{{$t('bs-on-this-tab-you-can-configure-group-acces')}}<br>
						{{$t('bs-to-activate-a-permission-just-click-on-the')}}<br>
						{{$t('bs-after-that-ask-the-attendants-who-are-par')}}
					</p>
				</slot>
			</b-alert>

		</b-container>

		<!-- <div class="row">
			<div class="col-auto bs-m-spacing">
				<a v-on:click.stop="showIB(1)" href="#" :class="ss.ss1">{{$t('bs-manage-group-users')}}</a>
				<a v-on:click.stop="showIB(2)" href="#" :class="ss.ss2">{{$t('bs-manage-group-access')}}</a>
			</div>
		</div> -->

		<b-row class="ml-1">
			<b-col cols="auto" class="bs-m-spacing">
					<a v-on:click.stop="showIB(1)" href="#" :class="ss.ss1">{{$t('bs-manage-group-users')}}</a>
			</b-col>
			<b-col cols="auto" class="bs-m-spacing">
					<a v-on:click.stop="showIB(2)" href="#" :class="ss.ss2">{{$t('bs-manage-group-access')}}</a>
			</b-col>
		</b-row>

		<br><br>
		<div v-if="show.show1">
			<config-user-group :title='title' :subtitle='subtitle' :itemselected="itemselected" :base_url="base_url"></config-user-group>
		</div>
		<div v-if="show.show2">
			<config-accesses-group :itemselected="itemselected" v-on:permission="permissions" :base_url="base_url"></config-accesses-group>
		</div>
		<div v-show="show.show3">
			c
		</div>
		<div v-show="show.show4">
			d
		</div>
		<vue-snotify></vue-snotify>
	</div>
</template>

<script>

export default {
	data(){
		return {
			show: {
				'show1': false,
				'show2': true,
				'show3': false,
				'show4': false,
				'show5': false,
				'show6': false,
				'show7': false,
				'show8': false,
			},
			ss: {
				'ss1': 'tab ', 
				'ss2': 'tab active',
				'ss3': 'tab',
				'ss4': 'tab',
				'ss5': 'tab',
				'ss6': 'tab',
			},
			permission : {},
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
	methods:{
		back: function() {
			var vm = this;
			vm.$emit('back');
		},
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
			}else if(item == 6){
				vm.show.show6 = true;
			}else if(item == 7){
				vm.show.show7 = true;
			}else if(item == 8){
				vm.show.show8 = true;
			}
		},
		btnBack(){
			var vm = this;
			vm.$emit('back');
		},
		permissions(permission){
			//console.log(permission);
			var vm = this;
			vm.permission = permission;
		},
		saveAllConfigs(){
			var url = `${this.base_url}/group/save-permission-group`;
			var vm = this;

			if(Object.entries(vm.permission)){
				vm.permission = vm.itemselected.settings.permissions;
			}

			axios.post(url, {
				id_group: vm.itemselected.id,
				permission: vm.permission,
			}).then(function(response){
				//console.log(response.data);

				if(response.data.success){
					
					vm.$snotify.success(vm.$t('bs-group-settings-saved-successfully'), vm.$t('bs-success'));
				}else{
					
					vm.$snotify.error(vm.$t('bs-error-saving-group-settings'), vm.$t('bs-error'));
				}

			})
			.catch(function(error){
				//console.log(error);
				console.log('FAILURE!!');
			});
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