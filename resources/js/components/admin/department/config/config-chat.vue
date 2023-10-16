<template>
	<div>
		<b-container fluid="lg">
			<div>
				<li class="list-group-item isactive">
					<div class="form-row align-items-center">
						<div class="col-auto bs-label" id="tooltip-chat-show-queue">
							{{$t('bs-show-queue')}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</div>
						<div class="col" style="text-align: right;margin-top:8px;">
							<label class="switch">
								<input type="checkbox" v-model="settings.showQueue" @click="queueActive">
								<span class="slider round"></span>
							</label>
						</div>
					</div>
					<b-tooltip target="tooltip-chat-show-queue" triggers="hover" placement="right" variant="secondary">
						{{$t('bs-tooltip-chat-show-queue')}}
					</b-tooltip>
				</li>
				<br>
				<li class="list-group-item isactive">
					<div class="form-row align-items-center">
						<div class="col-auto bs-label" id="tooltip-chat-open-chat">
							{{$t('bs-open')}} {{$t('bs-chat')}} &nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</div>
						<div class="col" style="text-align: right;margin-top:8px;">
							<label class="switch">
								<input type="checkbox" v-model="settings.openChat" @click="queueActive">
								<span class="slider round"></span>
							</label>
						</div>
					</div>
					<b-tooltip target="tooltip-chat-open-chat" triggers="hover" placement="right" variant="secondary">
						{{$t('bs-tooltip-chat-open-chat')}}
					</b-tooltip>
				</li>
				<br>
				<li class="list-group-item isactive">
					<div class="form-row align-items-center">
						<div class="col-auto bs-label" id="tooltip-ticket-evets">
							{{$t('bs-events')}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</div>
						<div class="col" style="text-align: right;margin-top:8px;">
							<b-button class="mt-1" @click="showOptionEvents" variant="secondary" size="sm">{{$t('bs-exhibit')}}</b-button>
							<label class="switch">
								<input type="checkbox" v-model="settings.events" @click="queueActive">
								<span class="slider round"></span>
							</label>
						</div>
					</div>
					<b-tooltip target="tooltip-ticket-evets" triggers="hover" placement="right" variant="secondary">
						{{$t('bs-tooltip-ticket-evets')}}
					</b-tooltip>
				</li>
				<br>
				<li class="list-group-item isactive">
					<div class="form-row align-items-center">
						<div class="col-auto bs-label" id="tooltip-chat-category">
							{{$t('bs-show-category-modal-when-closing-chat')}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</div>
						<div class="col" style="text-align: right;margin-top:8px;">
							<label class="switch">
								<input type="checkbox" v-model="settings.showCategory" @click="queueActive">
								<span class="slider round"></span>
							</label>
						</div>
					</div>
					<b-tooltip target="tooltip-chat-category" triggers="hover" placement="right" variant="secondary">
						{{$t('bs-tooltip-chat-category')}}
					</b-tooltip>
				</li>
				<br>
			</div>
			<div class="body">
				<div>
					<b-form class="label">
						<b-form-group id="input-group-1" class="bs-label" label-for="input-1">
							<template v-slot:label>
								<span id="tooltip-chat-msg-open">
									{{lbmsgopen}}: &nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
								</span>
							</template>
							<v-select
								style="background-color: #F2F2F2"
								:clearable="false"
								:options="options"
								@input="updateOptions"
								label="desc"
								v-model="languageSelectedMsgOpen"
							>
								<template #selected-option="{ label, code_lang }">
									<img height="20px"
									v-if="code_lang != 'ALL'"
									:src="`${base_url}/images/flags/${code_lang}.svg`"
									class="mr-2"
									/>
									{{ label }}
								</template>
								<template v-slot:option="{ label, code_lang }">
									<img height="20px"
									v-if="code_lang != 'ALL'"
									:src="`${base_url}/images/flags/${code_lang}.svg`"
									class="mr-2"
									/>
									{{ label }}
								</template>
							</v-select>
							<b-form-textarea
								id="input-1"
								v-model="settings.msgOpen"
								:placeholder="lbmsgopen"
								@change="queueActive"
								class="bs-input"
								rows="3"
							></b-form-textarea>
							<b-tooltip target="tooltip-chat-msg-open" triggers="hover" placement="right" variant="secondary">
								{{$t('bs-tooltip-chat-msg-open')}}
							</b-tooltip>
						</b-form-group>
						<b-form-group id="input-group-7" class="bs-label" :label="lbmsgclose" label-for="input-7">
							<template v-slot:label>
								<span id="tooltip-chat-msg-close">
									{{lbmsgclose}}: &nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
								</span>
							</template>
							<v-select
								style="background-color: #F2F2F2"
								:clearable="false"
								:options="options"
								@input="updateOptions2"
								label="desc"
								v-model="languageSelectedMsgClose"
							>
								<template #selected-option="{ label, code_lang }">
									<img height="20px"
									v-if="code_lang != 'ALL'"
									:src="`${base_url}/images/flags/${code_lang}.svg`"
									class="mr-2"
									/>
									{{ label }}
								</template>
								<template v-slot:option="{ label, code_lang }">
									<img height="20px"
									v-if="code_lang != 'ALL'"
									:src="`${base_url}/images/flags/${code_lang}.svg`"
									class="mr-2"
									/>
									{{ label }}
								</template>
							</v-select>
							<b-form-textarea
								id="input-7"
								v-model="settings.msgClose"
								:placeholder="lbmsgclose"
								@change="queueActive"
								class="bs-input"
								rows="3"
							></b-form-textarea>
							<b-tooltip target="tooltip-chat-msg-close" triggers="hover" placement="right" variant="secondary">
								{{$t('bs-tooltip-chat-msg-close')}}
							</b-tooltip>
						</b-form-group>
					</b-form>
				</div>
			</div>
			<div>
				<div class="d-block pb-2">
					<span id="tooltip-chat-initial-form" class="local-label">
						{{lbInitialForm}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
					</span>
				</div>
				<b-tooltip target="tooltip-chat-initial-form" triggers="hover" placement="right" variant="secondary">
					{{$t('bs-tooltip-chat-initial-form')}}
				</b-tooltip>


				<b-card v-if="showResisterQuestion">
					<b-form @submit="registerQuest">
						<b-row>
							<b-col>
								<b-form-group id="input-group-2" :label="$t('bs-question')+':'" label-for="input-2">
									<b-form-input
									id="input-2"
									class="bs-input-2"
									v-model="form.question"
									:placeholder="$t('bs-question')"
									required
									></b-form-input>
								</b-form-group>
							</b-col>

							<b-col cols="auto">
								<b-form-group id="input-group-3" :label="$t('bs-select-the-answer-type')+':'"  label-for="input-3">
									<b-form-select
									id="input-3"
									v-model="form.select"
									:options="optionsType"
									required
									></b-form-select>
								</b-form-group>
							</b-col>

							<b-col cols="auto">
								<b-form-group id="input-group-4" v-slot="{ ariaDescribedby }">
									<b-form-group id="input-group-3" :label="$t('bs-is-it-mandatory')" label-for="input-3">
										<b-form-checkbox-group
										v-model="form.checked"
										class="mt-2"
										id="checkboxes-4"
										:aria-describedby="ariaDescribedby"
										>
											<b-form-checkbox value="1" unchecked-value="0">{{$t('bs-yes')}}</b-form-checkbox>
										</b-form-checkbox-group>
									</b-form-group>
								</b-form-group>
							</b-col>

							<b-col>
								<b-form-group id="input-group-2" :label="$t('bs-language')+':'" label-for="input-2">
									<v-select
										style="background-color: #F2F2F2"
										:clearable="false"
										:options="options"
										@input="updateOptions"
										label="desc"
										v-model="languageSelectedQuestion"
									>
										<template #selected-option="{ label, code_lang }">
											<img
											v-if="code_lang != 'ALL'"
											:src="`${base_url}/images/flags/${code_lang}.svg`"
											class="mr-2"
											/>
											{{ label }}
										</template>
										<template v-slot:option="{ label, code_lang }">
											<img
											v-if="code_lang != 'ALL'"
											:src="`${base_url}/images/flags/${code_lang}.svg`"
											class="mr-2"
											/>
											{{ label }}
										</template>
									</v-select>
								</b-form-group>
							</b-col>

							<b-col cols="auto" class="mt-1">
								<b-button class="mt-4" type="submit" variant="primary">{{$t('bs-add')}}</b-button>
							</b-col>
						</b-row>
					</b-form>
				</b-card>

				<div v-if="!showResisterQuestion" class="text-right">
					<b-button variant="primary" @click="showResisterQuestion = !showResisterQuestion"><i class="fa fa-plus" aria-hidden="true"></i> {{$t('bs-register-question')}}</b-button>
				</div>

				<br>

				<b-table responsive bordered borderless striped hover
					class="local-striped-table"
					head-variant="light"
					table-variant="light"
					:fields="fields"
					:items="quests">
					<template #cell(quest)="row">
						{{$t(row.item.quest)}}
					</template>
					<template #cell(mandatory)="row">
						<div v-if="row.item.mandatory == 1">
							<b-link size="lg" @click="changeStatus(row.item)">
								<i class="fa fa-check-circle-o bs-ok fa-2x" aria-hidden="true"></i>
							</b-link>
						</div>
						<div v-else>
							<b-link size="lg" @click="changeStatus(row.item)">
								<i class="fa fa-times-circle-o bs-times fa-2x" aria-hidden="true"></i>
							</b-link>
						</div>
					</template>
					<template #cell(language)="row">
						<span v-for="(bdItem, index) in options" :key="index">
							<span v-if="row.item.language == bdItem.code">
								{{bdItem.label}}
							</span>
						</span>
					</template>
					<template #cell(delete)="row">
						<b-link size="lg" @click="itemDelete(row.item, row.index)">
							<i class="fa fa-trash bs-trash fa-2x" aria-hidden="true"></i>
						</b-link>
					</template>
					<template #cell(active)="row">
						<label class="switch">
							<input type="checkbox" v-model="row.item.active" @click="itemRestore(row.item, row.index)">
							<span class="slider round"></span>
						</label>
					</template>
				</b-table>
			</div>
			<div>
				<div class="title-blue mb-5">
					<span id="tooltip-opening-hours-of-chat">
						{{lbOpeningHoursOfChat}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
					</span>
				</div>
				<b-tooltip target="tooltip-opening-hours-of-chat" triggers="hover" placement="right" variant="secondary">
					{{$t('bs-tooltip-opening-hours-of-chat')}}
				</b-tooltip>

				<!-- ATENDENTE ONLINE -->
				<li class="list-group-item isactive mt-2 mb-4">
					<div class="form-row align-items-center">
						<div class="col-auto bs-label" id="tooltip-chat-open-online-chat">
							{{$t('bs-close-the-chat-when-there-is-no-online-att')}} &nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</div>
						<div class="col" style="text-align: right;margin-top:8px;">
							<label class="switch">
								<input type="checkbox" v-model="settings.openChatOnline" @click="queueActive">
								<span class="slider round"></span>
							</label>
						</div>
					</div>
					<b-tooltip target="tooltip-chat-open-online-chat" triggers="hover" placement="right" variant="secondary">
						{{$t('bs-tooltip-chat-open-online-chat')}}
					</b-tooltip>
				</li>

				<div v-for="(day, i) in weekDays" :key="i">
					<b-row align-v="center">
						<b-col cols="sm-2" style="display: flex; align-items: center;">
							<i class="fa fa-check-circle pencil fa-2x pdleft" aria-hidden="true"></i>{{day.title}}
						</b-col>
						<b-col cols="auto">
							<b-form-input
							id="input-group-2"
							:class="day.valid ? 'bs-input mb-2 mr-sm-2 ml-sm-2 mb-sm-0 is-valid' : 'bs-input mb-2 mr-sm-2 ml-sm-2 mb-sm-0 is-invalid'"
							v-model="settings.openDepartment[i].am"
							type="time"
							@change="queueActive"
							></b-form-input>
						</b-col>
						<div class="mt-0"> {{$t('bs-up-until').toLowerCase()}} </div>
						<b-col cols="auto">
							<b-form-input
							id="input-group-3"
							:class="day.valid ? 'bs-input mb-2 mr-sm-2 ml-sm-2 mb-sm-0 is-valid' : 'bs-input mb-2 mr-sm-2 ml-sm-2 mb-sm-0 is-invalid'"
							v-model="settings.openDepartment[i].ap"
							type="time"
							@change="queueActive"
							></b-form-input>
						</b-col>
						<b-col cols="sm-12"  v-if="!day.valid">
							<label class="text-danger text-sm pt-2 pl-3" v-text="$t('bs-invalid-chat-time')"></label>
						</b-col>
						<br>
					</b-row>
					<br/>
				</div>
			</div>

			<div class="modal fade" id="modalEvents" tabindex="-1" aria-labelledby="modalEvents" aria-hidden="true" data-backdrop="static" data-keyboard="false">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header border-0 p-0">
							<h5 class="modal-title" id="modalEvents">{{$t('bs-events')}}</h5>
						</div>
						<br>
						<div class="modal-body-custom">
						  	<b-form-group>
								<b-form-checkbox-group
									id="checkbox-group-1"
									v-model="selectedEvents"
									:options="optionsEvents"
									name="flavour-1"
									stacked
								></b-form-checkbox-group>
							</b-form-group>
						</div>
						<div class="modal-footer border-0">
							<button type="button" class="text-capitalize btn btn-danger" data-dismiss="modal">
								{{$t('bs-cancel')}}
							</button>
							<button type="button" @click="queueActive" data-dismiss="modal" id="btn-department" class="btn btn-primary">
								{{$t('bs-change')}}
							</button>
						</div>
						<!-- <div>Selected: <strong>{{ selectedEvents }}</strong></div> -->
					</div>
				</div>
			</div>

			<vue-snotify></vue-snotify>
		</b-container>
	</div>
</template>

<script>

export default {
	data(){
		return {
			lbmsgopen: this.$t('bs-opening-message'),
			lbmsgclose: this.$t('bs-closing-message'),
			lbInitialForm: this.$t('bs-initial-form') + ':',
			lbOpeningHoursOfChat: this.$t('bs-opening-hours-of-chat') + ':',
			fields: [
				{ key: 'quest', sortable: true, label: this.$t('bs-question') },
				{ key: 'type', sortable: true, label: this.$t('bs-type') },
				{ key: 'mandatory', sortable: true, label: this.$t('bs-mandatory') },
				{ key: 'language', label: this.$t('bs-language') },
				{ key: 'active', label: this.$t('bs-active') },
				{ key: 'delete', label: this.$t('bs-delete') }
			],
			//quests: this.settings.quests,
			quests: [],
			old: [
			{ am: '08:00', ap: '18:00'},
			{ am: '08:00', ap: '18:00'},
			{ am: '08:00', ap: '18:00'},
			{ am: '08:00', ap: '18:00'},
			{ am: '08:00', ap: '18:00'},
			{ am: '08:00', ap: '18:00'},
			{ am: '08:00', ap: '18:00'},
			],
			weekDays: [
				{
					title: this.$t('bs-sunday'),
					valid:true,
				},
				{
					title: this.$t('bs-monday'),
					valid:true
				},
				{
					title: this.$t('bs-tuesday'),
					valid:true
				},
				{
					title: this.$t('bs-wednesday'),
					valid:true
				},
				{
					title: this.$t('bs-thursday'),
					valid:true
				},
				{
					title: this.$t('bs-friday'),
					valid:true
				},
				{
					title: this.$t('bs-saturday'),
					valid:true
				},
			],
			options: [],
			languages: this.$store.state.languages,
			languageSelectedMsgOpen: '',
			languageSelectedMsgClose: '',
			languageSelectedQuestion: '',
			storeTranslation: {
				msgOpen:[],
				msgClose:[],
			},
			form: {
				question: '',
				select: 'TEXT',
				checked: [],
			},
			showResisterQuestion: false,
			optionsType: [
				{ value: 'TEXT', text: this.$t('bs-text')},
				// { value: 'b', text: 'SELECT' },
				// { value: 'c', text: 'CHECKBOX' },
			],
			check_is_exist: true,
			selectedEvents: [ "bs-took-over-the-chat", "bs-joined-the-chat", "bs-canceled-the-chat", 
			"bs-closed-the-chat", "bs-reopened-the-chat", "bs-marked-as-resolved", "bs-transferred-the-chat-to-another-department",
			"bs-transferred-the-chat-to-another-agent", "bs-turned-chat-into-ticket", "bs-the-chat-ended-due-to-inactivity", "bs-rated-the-chat" ],
			optionsEvents: [
				{ text: this.$t('bs-chat')+' - '+this.$t('bs-joined-the-chat'), value: 'bs-joined-the-chat' },
				{ text: this.$t('bs-chat')+' - '+this.$t('bs-took-over-the-chat'), value: 'bs-took-over-the-chat' },
				{ text: this.$t('bs-chat')+' - '+this.$t('bs-closed-the-chat'), value: 'bs-closed-the-chat' },
				{ text: this.$t('bs-chat')+' - '+this.$t('bs-canceled-the-chat'), value: 'bs-canceled-the-chat' },
				{ text: this.$t('bs-chat')+' - '+this.$t('bs-marked-as-resolved'), value: 'bs-marked-as-resolved' },
				{ text: this.$t('bs-chat')+' - '+this.$t('bs-reopened-the-chat'), value: 'bs-reopened-the-chat' },
				{ text: this.$t('bs-chat')+' - '+this.$t('bs-transferred-the-chat-to-another-agent'), value: 'bs-transferred-the-chat-to-another-agent' },
				{ text: this.$t('bs-chat')+' - '+this.$t('bs-transferred-the-chat-to-another-department'), value: 'bs-transferred-the-chat-to-another-department' },
				{ text: this.$t('bs-chat')+' - '+this.$t('bs-turned-chat-into-ticket'), value: 'bs-turned-chat-into-ticket' },
				{ text: this.$t('bs-chat')+' - '+this.$t('bs-the-chat-ended-due-to-inactivity'), value: 'bs-the-chat-ended-due-to-inactivity' },
				{ text: this.$t('bs-chat')+' - '+this.$t('bs-rated-the-chat'), value: 'bs-rated-the-chat' },
			],
		}
	},
	props:{
		itemselected: Object,
		settings: Object,
		general: Object,
		base_url: {
			type: String,
			default: ''
		},
	},
	mounted(){
		var vm = this;
		var url = `${this.base_url}/department/get-quests/${vm.itemselected.id}`;
		axios.get(url, {
			params:{
				type: 'CHAT',
			}
		}).then(function(r_resposta){
			vm.quests = r_resposta.data.result;
		}).catch(function (error) {
			console.log(error);
		});

		//CARREGAR A LINGUAGEM SELECIONADA E AS TRADUÇÕES CADASTRADAS
			if(vm.general.userLang[0].code == 'ALL'){
				vm.getCountryOptions();
			}else{
				vm.options = vm.general.userLang;
			}

			vm.languageSelectedMsgOpen = vm.options[0];
			vm.languageSelectedMsgClose = vm.options[0];
			vm.languageSelectedQuestion = vm.options[0];
			vm.storeTranslation = vm.settings.arrayTranslate;

			if(vm.settings.arrayTranslate == undefined){
				vm.settings.arrayTranslate = {
					msgOpen:[{ text: vm.settings.msgOpen, code: vm.languageSelectedMsgOpen.code}],
					msgClose:[{ text: vm.settings.msgClose, code: vm.languageSelectedMsgClose.code}],
				};
				vm.storeTranslation = {
					msgOpen:[{ text: vm.settings.msgOpen, code: vm.languageSelectedMsgOpen.code}],
					msgClose:[{ text: vm.settings.msgClose, code: vm.languageSelectedMsgClose.code}],
				};
			}

			vm.settings.arrayTranslate.msgOpen.forEach((item) => {
				if(item.code == vm.options[0].code){
					vm.settings.msgOpen = item.text;
				}
			});

			// console.log(vm.settings.arrayTranslate)
			vm.settings.arrayTranslate.msgClose.forEach((item) => {
				if(item.code == vm.options[0].code){
					vm.settings.msgClose = item.text;
				}
			});
		//CARREGAR A LINGUAGEM SELECIONADA E AS TRADUÇÕES CADASTRADAS


		if(vm.settings.selectedEvents == undefined){
			vm.selectedEvents = [ "bs-took-over-the-chat", "bs-joined-the-chat", "bs-canceled-the-chat", 
			"bs-closed-the-chat", "bs-reopened-the-chat", "bs-marked-as-resolved", "bs-transferred-the-chat-to-another-department",
			"bs-transferred-the-chat-to-another-agent", "bs-turned-chat-into-ticket", "bs-the-chat-ended-due-to-inactivity", "bs-rated-the-chat" ];
			vm.settings.events = true;
			vm.queueActive();
		}else{
			vm.selectedEvents = vm.settings.selectedEvents;
		}

	},
	methods:{
		getCountryOptions() {
			// push no "Todos" na primeira posição com key 0 (default);
			// this.options.push({ key: 0, code: "ALL", label: this.$t("bs-all") });

			// array de languages do store.js
			if (this.languages.length) {
				var key = 1;
				this.languages.forEach((element) => {
                let code_lang = element.key;
				// pego apenas o codigo do país, ex: pt_BR -> BR;
				let code = element.key.split("_")[1];
				this.options.push({ key: key, code: code, code_lang: code_lang, label: element.desc });
				key++;
				});
			}
		},
		updateOptions(value) {
			var vm = this;
			vm.settings.msgOpen = '';
			vm.settings.arrayTranslate.msgOpen.forEach((item) => {
				if(item.code == value.code){
					vm.settings.msgOpen = item.text;
				}
			});
		},
		updateOptions2(value) {
			var vm = this;
			vm.settings.msgClose = '';
			vm.settings.arrayTranslate.msgClose.forEach((item) => {
				if(item.code == value.code){
					vm.settings.msgClose = item.text;
				}
			});
		},
		queueActive(){
			var vm = this;

			if(vm.storeTranslation.msgOpen.length > 0) {
				vm.storeTranslation.msgOpen.forEach((item) => {
					if(item.code == vm.languageSelectedMsgOpen.code){
						item.text = vm.settings.msgOpen;
						vm.check_is_exist = false;
					}
				});

				if(vm.check_is_exist){
					vm.storeTranslation.msgOpen.push({ text: vm.settings.msgOpen, code: vm.languageSelectedMsgOpen.code});
					vm.check_is_exist = true;
				}else{
					vm.check_is_exist = true;
				}

			}else{
				vm.storeTranslation.msgOpen.push({ text: vm.settings.msgOpen, code: vm.languageSelectedMsgOpen.code});
				vm.check_is_exist = true;
			}

			if(vm.storeTranslation.msgClose.length > 0) {
				vm.storeTranslation.msgClose.forEach((item) => {
					if(item.code == vm.languageSelectedMsgClose.code){
						item.text = vm.settings.msgClose;
						vm.check_is_exist = false;
					}
				});

				if(vm.check_is_exist){
					vm.storeTranslation.msgClose.push({ text: vm.settings.msgClose, code: vm.languageSelectedMsgClose.code});
					vm.check_is_exist = true;
				}else{
					vm.check_is_exist = true;
				}

			}else{
				vm.storeTranslation.msgClose.push({ text: vm.settings.msgClose, code: vm.languageSelectedMsgClose.code});
				vm.check_is_exist = true;
			}

			var my_object = {
				showQueue: vm.settings.showQueue,
				openChat: vm.settings.openChat,
				openChatOnline: vm.settings.openChatOnline,
				msgOpen: vm.settings.msgOpen,
				arrayTranslate: vm.storeTranslation,
				msgClose: vm.settings.msgClose,
				openDepartment: vm.settings.openDepartment,
				events: vm.settings.events,
				selectedEvents: vm.selectedEvents,
				showCategory: vm.settings.showCategory,
			};
			vm.$emit('addChat', my_object);
		},
		showOptionEvents(){
			$("#modalEvents").modal("show");
		},
		registerQuest(event){
			var vm = this;
			event.preventDefault();

			axios.post(`${this.base_url}/department/set-setting-questions`, {
				id_department: vm.itemselected.id,
				quest: vm.form.question,
				type: vm.form.select,
				mandatory: vm.form.checked,
				language: vm.languageSelectedQuestion.code,
				type_question: 'CHAT',
			}).then(function(response){
				//console.log(response.data);
				if(response.data.success){

					var my_object = {
						id: response.data.id,
						quest: response.data.question,
						type: response.data.type,
						mandatory: response.data.mandatory,
						language: response.data.language,
						active: 1,
					};
					vm.showResisterQuestion = !vm.showResisterQuestion;
					vm.quests.push(my_object);

					vm.form.question = '';
					vm.form.select = '';
					vm.form.checked = [];

					vm.$snotify.success(vm.$t('bs-saved-successfully'), vm.$t('bs-success'));
				}else{
					vm.$snotify.error(vm.$t('bs-error-trying-to-save'), vm.$t('bs-error'));
				}
			}).catch(function(erro){
				console.log(erro);
				console.log('FAILURE!!');
			});

		},
		viewCancel(){
            $("#modalQuestions").modal("hide");
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
					axios.post(`${this.base_url}/department/delete-setting-questions`, {
						type: 'delete',
						id: item.id,
					}).then(function(response){
						if(response.data.success){
							vm.quests.splice(index, 1);
							vm.$snotify.success(vm.$t('bs-successfully-deleted'), vm.$t('bs-success'));
							Swal.fire(
								vm.$t('bs-deleted'),
								vm.$t('bs-your-file-has-been-deleted'),
								'success'
							);
						}else{
							vm.$snotify.error(vm.$t('bs-error-while-deleting'), vm.$t('bs-error'));
						}
					}).catch(function(){
						console.log('FAILURE!!');
					});
				}
			})
		},
		changeStatus(item){
			if(item.mandatory == 1){
				item.mandatory = 0;
			}else{
				item.mandatory = 1;
			}
			axios.post(`${this.base_url}/department/update-setting-questions`, {
				item: item,
			});
		},
		itemRestore(item, index){
			var vm = this;
			if(item.active == 0){
				axios.post(`${this.base_url}/department/delete-setting-questions`, {
					type: 'restore',
					id: item.id,
				}).then(function(response){
					//console.log(response.data);
					if(response.data.success){
						vm.quests[index].active = 1;
						vm.$snotify.success(vm.$t('bs-department-successfully-restored'), vm.$t('bs-success'));
					}else{
						vm.$snotify.error(vm.$t('bs-error-restoring-department'), vm.$t('bs-error'));
					}
				})
				.catch(function(){
					console.log('FAILURE!!');
				});
			}else{
				axios.post(`${this.base_url}/department/delete-setting-questions`, {
					type: 'disable',
					id: item.id,
				}).then(function(response){
					//console.log(response.data);

					if(response.data.success){

						vm.quests[index].active = 0;
						vm.$snotify.success(vm.$t('bs-department-successfully-deactivated'), vm.$t('bs-success'));

					}else{
						vm.$snotify.error(vm.$t('bs-error-when-deactivating-department'), vm.$t('bs-error'));
					}
				})
				.catch(function(){
					console.log('FAILURE!!');
				});
			}
		},
	},
	watch:{
		settings(val){
			this.$parent.allowedToSave = true;
			for (var i = 0; i < val.openDepartment.length; i++) {
				if(val.openDepartment[i].ap < val.openDepartment[i].am){
					this.weekDays[i].valid = false;
					this.$parent.allowedToSave = false;
				}else{
					this.weekDays[i].valid = true;
				}
			}
		}
	}
};


</script>

<style scoped>

.bs-input-2 {
    padding-top: 20px;
    padding-bottom: 20px;
    padding-left: 20px;
    text-align: left;
    font: normal normal bold 14px/18px Muli;
    letter-spacing: 0px;
    opacity: 0.75;
}

.isactive{
	margin:0;
	padding:10px;
	padding-top:0px;
	padding-bottom:0px;
	font: normal normal bold 14px/18px Muli;
	color: #707070;
}
.pdleft{
	padding-right: 5px;
	margin-right: 5px;
}

.bs-times{
	color: red;
	margin-left: 4px;
}

.bs-ok{
	color: green;
	margin-left: 4px;
}

.pencil{
	color:#0080FC;
}

.local-label{
	color: #707070;
	font-family: Muli;
	font-size: 14px;
	font-weight: 700;
}

.local-striped-table{
	overflow-y: hidden;
}

.title-blue{
	color: #0080FC;
	font-family: Muli;
	font-weight: 800;
	font-size: 25px;
}

/* SCROLL */

::-webkit-scrollbar {
    width: 5px;
    height: 5px;
}

::-webkit-scrollbar-track {
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #0080fc;
    border-radius: 10px;
}

.modal-content {
    background: #f3f7ff 0% 0% no-repeat padding-box;
    box-shadow: 0px 14px 32px #00000040;
    border-radius: 10px;
    border: none;
    padding-top: 40px;
    padding-left: 40px;
    padding-right: 40px;
}

.modal-title {
    font: normal normal bold 20px/26px Muli;
    letter-spacing: 0px;
    color: #434343;
}

.modal {
    background-color: #59607173;
}

.modal .close {
    background: #acb8d8;
    color: white;
    text-shadow: none;
    padding: 2px;
    margin-top: 1px;
    border-radius: 100%;
    font-size: 15px;
    height: 25px;
    width: 25px;
    margin-right: 2px;
}

.modal-body label {
    font: normal normal bold 14px/35px Muli;
    letter-spacing: 0px;
    color: #656565;
}

.modal-body select {
    background: #fafbfc 0% 0% no-repeat padding-box;
    border: 1px solid #dddddd;
    border-radius: 3px;
}

.example-open .modal-backdrop {
    background-color: red;
}

@media only screen and (max-width: 575px) {
    .content {
        margin-right: 10px;
        margin-left: 10px;
    }

    .card-header {
        padding-top: 20px;
        height: 80px;
        zoom: 120%;
    }

    .modal-dialog {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
    }

    .modal-content {
        height: auto;
        min-height: 100%;
        border-radius: 0;
    }

    .modal-footer {
        padding-right: 0px;
        padding-left: 0px;
    }

    #btn-new-chat {
        max-width: 140px;
        padding-left: 6px;
        padding-right: 6px;
    }
}

</style>

