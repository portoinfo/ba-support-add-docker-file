<template>
	<div>
		<b-form @submit="addCommand" class="label">
			<b-row>
				<b-col sm="3">
					<b-form-group class="bs-label" id="input-group-1" label-for="input-1">
						<template v-slot:label>
							<span id="tooltip-auto-answer-command">
								{{lbCommand}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
							</span>
						</template>
						<b-form-input
						id="input-1"
						v-model="command"
						required
						:placeholder="phName"
						class="bs-input"
						></b-form-input>
					</b-form-group>
				</b-col>
				<b-tooltip target="tooltip-auto-answer-command" triggers="hover" placement="right" variant="secondary">
					{{$t('bs-tooltip-auto-answer-command')}}
				</b-tooltip>
				<b-col sm="3">
					<b-form-group class="bs-label" id="input-group-2" label-for="input-1">
						<template v-slot:label>
							<span id="tooltip-auto-answer-status">
								{{lbStatus}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
							</span>
						</template>
						<b-form-select
						id="input-2"
						v-model="status"
						:options="optionstatus"
						required
						></b-form-select>
					</b-form-group>
				</b-col>
				<b-tooltip target="tooltip-auto-answer-status" triggers="hover" placement="right" variant="secondary">
					{{$t('bs-tooltip-auto-answer-status')}}
				</b-tooltip>
				<b-col>
					<b-form-group class="bs-label" id="input-group-9" label-for="input-9">
						<template v-slot:label>
							<span id="tooltip-auto-answer-description">
								{{lbDescription}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
							</span>
						</template>
						<b-form-input
						id="input-9"
						v-model="description"
						required
						:placeholder="phName"
						class="bs-input"
						></b-form-input>
					</b-form-group>
				</b-col>
				<b-tooltip target="tooltip-auto-answer-description" triggers="hover" placement="right" variant="secondary">
					{{$t('bs-tooltip-auto-answer-description')}}
				</b-tooltip>
				<b-col sm="auto">
					<b-button @click="addCommand" variant="primary" style="margin-top:25px;">{{$t('bs-add').toUpperCase()}}</b-button>
				</b-col>
			</b-row>
		</b-form>

		<div>
			<span class="title">{{$t('bs-custom')}}</span>
			<b-table responsive bordered borderless striped hover
			class="local-striped-table mt-2"
			head-variant="light"
			table-variant="light"
			:fields="fields"
			:items="localSettings"
			show-empty
			>
				<template #cell(command)="row" v-if="showeditdescription">
					<template v-if="indexDescription == row.index">
						<b-form-textarea 
							rows="3"
							v-model="newCommand"
						></b-form-textarea>
					</template>
				</template>
				<template #cell(status)="row" v-if="showeditdescription">
					<template v-if="indexDescription == row.index">
						<b-form-select
						id="input-2"
						v-model="newStatus"
						:options="optionstatus"
						required
						></b-form-select>
					</template>
				</template>
				<template #cell(description)="row" v-if="showeditdescription">
					<template v-if="indexDescription == row.index">
						<b-form-textarea 
							rows="3"
							v-model="newDescription"
						></b-form-textarea>
					</template>
				</template>
				<template #cell(description)="row" v-else>
					{{$t(row.item.description)}}
				</template>
				<template #cell(actions)="row">
					<template v-if="!showeditdescription">
						<b-link size="lg" @click="itemEdit(row.item, row.index)">
							<i class="fa fa-pencil fa-2x bs-pencil" aria-hidden="true"></i>
						</b-link>	
						<b-link size="lg" @click="itemDelete(row.item, row.index)">
							<i class="fa fa-trash-o trash fa-2x" aria-hidden="true"></i>
						</b-link>
					</template>
					<template v-else>
						<template v-if="indexDescription == row.index">
							<b-link size="lg" @click="itemSave(row.item, row.index)">
								<i class="fa fa-check fa-2x bs-check" aria-hidden="true"></i>
							</b-link>
						</template>
					</template>
				</template>
				<template #empty="scope">
					<div class="text-center">{{ $t('bs-no-command-registered') }}</div>
				</template>
			</b-table>
		</div>

		<div>
			<span class="title">{{$t('bs-basics')}}</span>
			<b-table responsive bordered borderless striped hover
			class="local-striped-table mt-2"
			head-variant="light"
			table-variant="light"
			:fields="fieldsFIXED"
			:items="localSettingsFixed">

				<template #cell(description)="row" v-if="showeditdescription">
					{{$t('row.item.description')}}
				</template>
			</b-table>
		</div>
	<vue-snotify></vue-snotify>
</div>
</template>

<script>

export default {
	data(){
		return {
			showeditdescription: false,
			lbCommand: this.$t('bs-command'),
			lbStatus: 'Status',
			lbDescription: this.$t('bs-description'),
			phName: this.$t('bs-enter-name'),
			command: "",
			description: "",
			newCommand: "",
			newStatus: "",
			newDescription: "",
			indexDescription: null,
			status: "START",
			fields: [
			{ key: 'command', sortable: true, label: this.$t('bs-command') },
			{ key: 'status', sortable: true, label: this.$t('bs-status') },
			{ key: 'description', sortable: true, label: this.$t('bs-description') },
			{ key: 'actions', sortable: true, label: this.$t('bs-actions') }
			],
			fieldsFIXED: [
			{ key: 'command', sortable: true, label: this.$t('bs-command') },
			{ key: 'status', sortable: true, label: this.$t('bs-status') },
			{ key: 'description', sortable: true, label: this.$t('bs-description') },
			],
			optionstatus: [
			{ text: this.$t('bs-start').toUpperCase(), value: 'START' },
			{ text: this.$t('bs-middle').toUpperCase(), value: 'MIDDLE' }, 
			{ text: this.$t('bs-end').toUpperCase(), value: 'END' }
			],
			localSettings: [],
			localSettingsFixed: [],
		}
	},
	props:{
		itemselected: Object,
		settings: Array,
		idSettings: String,
		base_url: {
			type: String,
			default: ''
		},
	},
	created() {
		let vm = this
		this.localSettings = JSON.parse(JSON.stringify(this.settings)).map(function(item){
			item.status = vm.translateStatus(item.status);
			return item;
		});
		vm.fixedCommands();
	},
	methods:{
		addCommand(){
			var vm = this;

			//VERIFICAR CAMPOS VAZIOS
			if (vm.command.trim() === "" || vm.description.trim() === "") {
				return vm.$snotify.info(vm.$t('bs-empty-fields'), vm.$t('bs-info'));;
			}

			//VERIFICAR VALORES REPETIDOS
			for (var i = vm.localSettings.length - 1; i >= 0; i--) {
				if(vm.localSettings[i].command == vm.command){
					vm.command = "";
					vm.description = "";
					return vm.$snotify.info(vm.$t('bs-command-already-exists'), vm.$t('bs-info'));
				}
			}

			//VERIFICAR VALORES REPETIDOS
			for (var i = vm.localSettingsFixed.length - 1; i >= 0; i--) {
			if(vm.localSettingsFixed[i].command == vm.command){
					vm.command = "";
					vm.description = "";
					return vm.$snotify.info(vm.$t('bs-command-already-exists'), vm.$t('bs-info'));
				}
			}

			var create_item = {
				command: vm.command.replace(/\s/g, ''),
				status: vm.status,
				description: vm.description,
			};
			vm.$emit('addCommand', create_item);
			vm.command = "";
			vm.description = "";
		},
		itemSave(item, index){
			var vm = this;
			item.command = vm.newCommand;
			item.description = vm.newDescription;
			item.status = vm.newStatus;
			//console.log(item.status)
			vm.showeditdescription = false;
			vm.$emit('updateCommand', item, index);	
		},
		itemEdit(item, index){
			var vm = this;
			vm.newCommand = item.command;
			vm.newDescription = vm.$t(item.description);
			if(item.status == undefined){
				vm.newStatus = "";
			}else{
				vm.newStatus = vm.reversseStatus(item.status);
			}
			vm.indexDescription = index;
			vm.showeditdescription = true;
		},
		itemDelete(item, index){
			var vm = this;
			vm.$emit('deleteCommand', index);
		},
		translateStatus(status) {
			switch(status) {
				case 'START':
				return this.$t('bs-start').toUpperCase();
				case 'MIDDLE':
				return this.$t('bs-middle').toUpperCase();
				case 'END':
				return this.$t('bs-end').toUpperCase();
			}
		},
		reversseStatus(status) {
			switch(status) {
				case this.$t('bs-start').toUpperCase():
				return 'START';
				case this.$t('bs-middle').toUpperCase():
				return 'MIDDLE';
				case this.$t('bs-end').toUpperCase():
				return 'END';
			}
		},
		fixedCommands(){
			this.localSettingsFixed.push({
				command: 'cl_name',
				status: 'FIXED',
				description: '('+this.$t('bs-client-name')+')',
				fixed: true,
			});

			this.localSettingsFixed.push({
				command: 'cl_email',
				status: 'FIXED',
				description: '('+this.$t('bs-customer-email')+')',
				fixed: true,
			});

			this.localSettingsFixed.push({
				command: 'company',
				status: 'FIXED',
				description: '('+this.$t('bs-company-name')+')',
				fixed: true,
			});

			this.localSettingsFixed.push({
				command: 'dept',
				status: 'FIXED',
				description: '('+this.$t('bs-department-name')+')',
				fixed: true,
			});
		}
	},
	watch: {
		settings: function(newVal, oldVal) {
			let vm = this
			this.localSettings = JSON.parse(JSON.stringify(newVal)).map(function(item){
				//console.log('map ', vm)
				item.status = vm.translateStatus(item.status);
				return item;
			})
		}
	},
};

</script>

<style scoped>

.title{
	font: normal normal bold 18px/22px Muli;
}

.bs-check{
	color: rgb(25, 155, 25);
}

.btn-save{
	background-color: #4cf0fc;
	box-shadow: 0px 1px 1px #1E120D1A;
	color: white;
	font: normal normal 800 14px/16px Muli;
}
.bs-input{
	border: 1px solid #a4a4a4;
	padding-top: 18px;
	padding-bottom: 18px;
	padding-left: 20px;
	text-align: left;
	font: normal normal bold 14px/18px Muli;
	letter-spacing: 0px;
	opacity: 0.75;
}
.trash{
	color: red;
	margin-left: 4px;
}
</style>