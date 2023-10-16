<template>
	<div>
		<b-container fluid="lg">

			<!-- <div>
				<li class="list-group-item isactive">
					<div class="form-row align-items-center">
						<div class="col-auto bs-label">
							{{$t('bs-show-queue')}}
						</div>
						<div class="col" style="text-align: right;margin-top:8px;">
							<label class="switch">
								<input type="checkbox" v-model="settings.showQueue" @click="queueActive">
								<span class="slider round"></span>
							</label>
						</div>
					</div>
				</li>
			</div> -->
			<br>
			<div>
				<li class="list-group-item isactive">
					<div class="form-row align-items-center">
						<div class="col-auto bs-label" id="tooltip-ticket-send-email">
							{{$t('bs-send-email')}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</div>
						<div class="col" style="text-align: right;margin-top:8px;">
							<label class="switch">
								<input type="checkbox" v-model="settings.sendEmail" @click="queueActive">
								<span class="slider round"></span>
							</label>
						</div>
					</div>
					<b-tooltip target="tooltip-ticket-send-email" triggers="hover" placement="right" variant="secondary">
						{{$t('bs-tooltip-ticket-send-email')}}
					</b-tooltip>
				</li>
				<br>
			</div>
			<div v-if="ConfigsCompanyReleased">
				<li class="list-group-item isactive">
					<div class="form-row align-items-center">
						<div class="col-auto bs-label" id="tooltip-ticket-notifications-office">
							{{$t('bs-notifications-in-office')}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</div>
						<div class="col" style="text-align: right;margin-top:8px;">
							<label class="switch">
								<input type="checkbox" v-model="settings.notificationsOficce" @click="queueActive">
								<span class="slider round"></span>
							</label>
						</div>
					</div>
					<b-tooltip target="tooltip-ticket-notifications-office" triggers="hover" placement="right" variant="secondary">
						{{$t('bs-tooltip-ticket-notifications-office')}}
					</b-tooltip>
				</li>
				<br>
			</div>
			<div>
				<li class="list-group-item isactive">
					<div class="form-row align-items-center">
						<div class="col-8 bs-label" id="tooltip-ticket-notifications-office">
							{{$t('bs-pre-selected-status-when-responding')}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</div>
						<div class="col-4" style="text-align: right;">
							<b-form-select size="sm" v-model="selectedStatus" :options="optionsStatus" @change="queueActive"></b-form-select>
						</div>
					</div>
					<b-tooltip target="tooltip-ticket-notifications-office" triggers="hover" placement="right" variant="secondary">
						{{$t('bs-when-responding-to-ticket-the-status')}}
					</b-tooltip>
				</li>
				<br>
			</div>
			<div>
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
			</div>
			<div>
				<li class="list-group-item isactive">
					<div class="form-row align-items-center">
						<div class="col-auto bs-label" id="tooltip-ticket-category">
							{{$t('bs-show-category-modal-when-closing-ticket')}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</div>
						<div class="col" style="text-align: right;margin-top:8px;">
							<label class="switch">
								<input type="checkbox" v-model="settings.showCategory" @click="queueActive">
								<span class="slider round"></span>
							</label>
						</div>
					</div>
					<b-tooltip target="tooltip-ticket-category" triggers="hover" placement="right" variant="secondary">
						{{$t('bs-tooltip-ticket-category')}}
					</b-tooltip>
				</li>
				<br>
			</div>

			<!-- <b-col lg>
				<b-row class='ml-2'>
					<b-col cols="auto" class="px-0 mx-0" id="tk-opened-simultaneously-customer">
						{{$t('bs-pre-selected-status-when-responding')}}:
						&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						<div class="spantext">
							<span>{{$t('bs-when-responding-to-ticket-the-status')}}</span>
						</div>
					</b-col>
					<b-tooltip target="tk-opened-simultaneously-customer" triggers="hover" placement="right" :variant="tooltipVariant">
						{{$t('bs-tooltip-tk-opened-simultaneously-customer')}}
					</b-tooltip>
					<b-col>
					</b-col>
					<b-col cols="auto">
						<b-form-select v-model="selectedStatus" :options="optionsStatus"></b-form-select>
					</b-col>
				</b-row>
			</b-col> -->
			<div class="body">
				<div>
					<b-form class="label">
						<b-form-group id="input-group-4" class="bs-label" label-for="input-5">
							<template v-slot:label>
								<span id="tooltip-ticket-msg-open">
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
								id="input-5"
								v-model="settings.msgOpen"
								:placeholder="lbmsgopen"
								@change="queueActive"
								class="bs-input"
								rows="3"
							></b-form-textarea>
							<b-tooltip target="tooltip-ticket-msg-open" triggers="hover" placement="right" variant="secondary">
								{{$t('bs-tooltip-ticket-msg-open')}}
							</b-tooltip>
						</b-form-group>
					</b-form>
				</div>
			</div>
			<div class="body">
				<div>
					<b-form class="label">
						<b-form-group id="input-group-4" class="bs-label" label-for="input-6">
							<template v-slot:label>
								<span id="tooltip-desription-ticket">
									{{$t('bs-describe-ticket-title')}}: &nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
								</span>
							</template>
							<v-select
								style="background-color: #F2F2F2"
								:clearable="false"
								:options="options"
								@input="updateOptions2"
								label="desc"
								v-model="languageSelectedDesriptionTicket"
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
								id="input-6"
								v-model="settings.desriptionTicket"
								:placeholder="desriptionTicket"
								@change="queueActive"
								class="bs-input"
								rows="3"
							></b-form-textarea>
							<b-tooltip target="tooltip-desription-ticket" triggers="hover" placement="right" variant="secondary">
								{{$t('bs-tooltip-ticket-description-open')}}
							</b-tooltip>
						</b-form-group>
					</b-form>
				</div>
			</div>
			<div class="body">
				<!-- ASSINATURA DO TICKET -->
				<div>
					<b-form class="label">
						<b-form-group id="input-group-4" class="bs-label" label-for="input-6">
							<template v-slot:label>
								<span id="tooltip-signature-ticket">
									{{$t('bs-signature')}}: &nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
									<b-badge class="caret" @click="setTextComand('name')" variant="info">{{$t('bs-name')}}</b-badge>
									<b-badge class="caret" @click="setTextComand('department')" variant="info">{{$t('bs-department')}}</b-badge>
								</span>
							</template>
							<v-select
								style="background-color: #F2F2F2"
								:clearable="false"
								:options="options"
								@input="updateOptions3"
								label="desc"
								v-model="languageSelectedSignatureTicket"
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
								id="input-6"
								v-model="settings.signatureTicket"
								:placeholder="signaturephTicket"
								@change="queueActive"
								class="bs-input"
								rows="4"
							></b-form-textarea>
							<b-tooltip target="tooltip-signature-ticket" triggers="hover" placement="right" variant="secondary">
								{{$t('bs-tooltip-ticket-signature')}}
							</b-tooltip>
						</b-form-group>
					</b-form>
				</div>
			</div>
			<div>
				<div class="d-block pb-2">
					<span id="tooltip-ticket-initial-form" class="local-label">
						{{lbInitialForm}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
					</span>
				</div>
				<b-tooltip target="tooltip-ticket-initial-form" triggers="hover" placement="right" variant="secondary">
					{{$t('bs-tooltip-ticket-initial-form')}}
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
					<template #cell(active)="row">
						<label class="switch">
							<input type="checkbox" v-model="row.item.active" @click="itemRestore(row.item, row.index)">
							<span class="slider round"></span>
						</label>
					</template>
					<template #cell(delete)="row">
						<b-link size="lg" @click="itemDelete(row.item, row.index)">
							<i class="fa fa-trash bs-trash fa-2x" aria-hidden="true"></i>
						</b-link>
					</template>
				</b-table>
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


		</b-container>
		<vue-snotify></vue-snotify>
	</div>
</template>

<script>

export default {
	data(){
		return {
			desriptionTicket: this.$t('bs-ticket-description'),
			signaturephTicket: this.$t('bs-ticket-signature'),
			lbmsgopen: this.$t('bs-opening-message'),
			lbInitialForm: this.$t('bs-initial-form') + ':',
			fields: [
				{ key: 'quest', sortable: true, label: this.$t('bs-question')  },
				{ key: 'type', sortable: true, label: this.$t('bs-type') },
				{ key: 'mandatory', sortable: true, label: this.$t('bs-mandatory') },
				{ key: 'language', label: this.$t('bs-language') },
				{ key: 'active', label: this.$t('bs-active') },
				{ key: 'delete', label: this.$t('bs-delete')}
			],
			// quests: this.settings.quests,
			quests: [],
			ConfigsCompanyReleased: null,
			options: [],
			languages: this.$store.state.languages,
			languageSelectedMsgOpen: '',
			languageSelectedDesriptionTicket: '',
			languageSelectedSignatureTicket: '',
			languageSelectedQuestion: '',
			storeTranslation: {
				msgOpen:[],
				desriptionTicket:[],
				signatureTicket:[],
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
			selectedStatus: 'IN_PROGRESS',
			optionsStatus: [
				{ value: 'IN_PROGRESS', text: this.$t('bs-in-progress') },
				{ value: 'CLOSED', text: this.$t('bs-closed') },
				{ value: 'RESOLVED', text: this.$t('bs-resolved') },
			],
			selectedEvents: [ "bs-attendant-created-a-ticket", "bs-joined-the-ticket", "bs-closed-the-ticket", "bs-reopened-the-ticket", "bs-canceled-the-ticket", 
				"bs-marked-as-resolved", "bs-ticket-status-changed", "bs-transferred-the-ticket-to-another-agent", 
				"bs-transferred-the-ticket-to-another-departme", "bs-ticket-transferred-department-and-attendan" ],
			optionsEvents: [
				{ text: this.$t('bs-ticket')+' - '+this.$t('bs-attendant-created-a-ticket'), value: 'bs-attendant-created-a-ticket' },
				{ text: this.$t('bs-ticket')+' - '+this.$t('bs-joined-the-ticket'), value: 'bs-joined-the-ticket' },
				{ text: this.$t('bs-ticket')+' - '+this.$t('bs-closed-the-ticket'), value: 'bs-closed-the-ticket' },
				{ text: this.$t('bs-ticket')+' - '+this.$t('bs-reopened-the-ticket'), value: 'bs-reopened-the-ticket' },
				{ text: this.$t('bs-ticket')+' - '+this.$t('bs-canceled-the-ticket'), value: 'bs-canceled-the-ticket' },
				{ text: this.$t('bs-ticket')+' - '+this.$t('bs-marked-as-resolved'), value: 'bs-marked-as-resolved' },
				{ text: this.$t('bs-ticket')+' - '+this.$t('bs-ticket-status-changed'), value: 'bs-ticket-status-changed' },
				{ text: this.$t('bs-ticket')+' - '+this.$t('bs-transferred-the-ticket-to-another-agent'), value: 'bs-transferred-the-ticket-to-another-agent' },
				{ text: this.$t('bs-ticket')+' - '+this.$t('bs-transferred-the-ticket-to-another-departme'), value: 'bs-transferred-the-ticket-to-another-departme' },
				{ text: this.$t('bs-ticket')+' - '+this.$t('bs-ticket-transferred-department-and-attendan'), value: 'bs-ticket-transferred-department-and-attendan' },
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
		company_id: String,
	},
	mounted(){
		var vm = this;
		
		var url = `${this.base_url}/department/get-quests/${vm.itemselected.id}`;
		axios.get(url, {
			params:{
				type: 'TICKET',
			}
		}).then(function(r_resposta){
			//console.log(r_resposta.data.result[0].settings);
			vm.quests = r_resposta.data.result;

			for (let i = 0; i < r_resposta.data.master.length; i++) {
				if(vm.company_id == r_resposta.data.master[i]){
					//console.log('é igual');
					vm.ConfigsCompanyReleased = true;
					break;
				}else{
					//console.log('não é igual');
					vm.ConfigsCompanyReleased = false;
					vm.settings.notificationsOficce = false;
				}
			}

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
			vm.languageSelectedDesriptionTicket = vm.options[0];
			vm.languageSelectedSignatureTicket = vm.options[0];
			vm.languageSelectedQuestion = vm.options[0];
			vm.storeTranslation = vm.settings.arrayTranslate;

			if(vm.settings.arrayTranslate == undefined){
				vm.settings.arrayTranslate = {
					msgOpen:[{ text: vm.settings.msgOpen, code: vm.languageSelectedMsgOpen.code}],
					desriptionTicket:[{ text: vm.settings.desriptionTicket, code: vm.languageSelectedDesriptionTicket.code}],
					signatureTicket:[{ text: vm.settings.signatureTicket, code: vm.languageSelectedSignatureTicket.code}],
				};
				vm.storeTranslation = {
					msgOpen:[{ text: vm.settings.msgOpen, code: vm.languageSelectedMsgOpen.code}],
					desriptionTicket:[{ text: vm.settings.desriptionTicket, code: vm.languageSelectedDesriptionTicket.code}],
					signatureTicket:[{ text: vm.settings.signatureTicket, code: vm.languageSelectedSignatureTicket.code}],
				};
			}

			if(vm.settings.arrayTranslate.signatureTicket == undefined){
				vm.settings.arrayTranslate.signatureTicket = [{ text: vm.settings.signatureTicket, code: vm.languageSelectedSignatureTicket.code}]
			}
			if(vm.storeTranslation.signatureTicket == undefined){
				vm.storeTranslation.signatureTicket = [{ text: vm.settings.signatureTicket, code: vm.languageSelectedSignatureTicket.code}]
			}

			vm.settings.arrayTranslate.msgOpen.forEach((item) => {
				if(item.code == vm.options[0].code){
					vm.settings.msgOpen = item.text;
				}
			});

			vm.settings.arrayTranslate.desriptionTicket.forEach((item) => {
				if(item.code == vm.options[0].code){
					vm.settings.desriptionTicket = item.text;
				}
			});

			vm.settings.arrayTranslate.signatureTicket.forEach((item) => {
				if(item.code == vm.options[0].code){
					vm.settings.signatureTicket = item.text;
				}
			});
		//CARREGAR A LINGUAGEM SELECIONADA E AS TRADUÇÕES CADASTRADAS


		if(vm.settings.selectedStatus != undefined){
			vm.selectedStatus = vm.settings.selectedStatus;
		}

		if(vm.settings.selectedEvents == undefined){
			vm.selectedEvents = [  "bs-attendant-created-a-ticket", "bs-joined-the-ticket", "bs-closed-the-ticket", "bs-reopened-the-ticket", "bs-canceled-the-ticket", 
				"bs-marked-as-resolved", "bs-ticket-status-changed", "bs-transferred-the-ticket-to-another-agent", 
				"bs-transferred-the-ticket-to-another-departme", "bs-ticket-transferred-department-and-attendan" ];
			vm.settings.events = true;
			vm.queueActive();
		}else{
			vm.selectedEvents = vm.settings.selectedEvents;
		}

		// vm.settings.desriptionTicket = vm.$t(vm.settings.desriptionTicket);
	},
	methods:{
		setTextComand(value){
			var aux = ' {'+ value +'}';
			this.settings.signatureTicket += aux;
			this.queueActive();
		},
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
			vm.settings.desriptionTicket = '';
			vm.settings.arrayTranslate.desriptionTicket.forEach((item) => {
				if(item.code == value.code){
					vm.settings.desriptionTicket = item.text;
				}
			});
		},
		updateOptions3(value) {
			var vm = this;
			// console.log(vm.settings.arrayTranslate)
			vm.settings.signatureTicket = '';
			vm.settings.arrayTranslate.signatureTicket.forEach((item) => {
				if(item.code == value.code){
					vm.settings.signatureTicket = item.text;
				}
			});
		},
		queueActive(){
			var vm = this;
			// MENSAGEM OPENED
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

			// DESCRIÇÃO DO TICKET
			if(vm.storeTranslation.desriptionTicket.length > 0) {
				vm.storeTranslation.desriptionTicket.forEach((item) => {
					if(item.code == vm.languageSelectedDesriptionTicket.code){
						item.text = vm.settings.desriptionTicket;
						vm.check_is_exist = false;
					}
				});

				if(vm.check_is_exist){
					vm.storeTranslation.desriptionTicket.push({ text: vm.settings.desriptionTicket, code: vm.languageSelectedDesriptionTicket.code});
					vm.check_is_exist = true;
				}else{
					vm.check_is_exist = true;
				}

			}else{
				vm.storeTranslation.desriptionTicket.push({ text: vm.settings.desriptionTicket, code: vm.languageSelectedDesriptionTicket.code});
				vm.check_is_exist = true;
			}

			// DESCRIÇÃO DA ASSINATURA
			if(vm.storeTranslation.signatureTicket == undefined){
				vm.storeTranslation.signatureTicket.push({ text: vm.settings.signatureTicket, code: vm.languageSelectedSignatureTicket.code});
				vm.check_is_exist = true;
			}
			if(vm.storeTranslation.signatureTicket.length > 0) {
				vm.storeTranslation.signatureTicket.forEach((item) => {
					if(item.code == vm.languageSelectedSignatureTicket.code){
						item.text = vm.settings.signatureTicket;
						vm.check_is_exist = false;
					}
				});

				if(vm.check_is_exist){
					vm.storeTranslation.signatureTicket.push({ text: vm.settings.signatureTicket, code: vm.languageSelectedSignatureTicket.code});
					vm.check_is_exist = true;
				}else{
					vm.check_is_exist = true;
				}

			}else{
				vm.storeTranslation.signatureTicket.push({ text: vm.settings.signatureTicket, code: vm.languageSelectedSignatureTicket.code});
				vm.check_is_exist = true;
			}
			// console.log(vm.selectedEvents);
			var my_object = {
				showQueue: false,
				msgOpen: vm.settings.msgOpen,
				desriptionTicket: vm.settings.desriptionTicket,
				signatureTicket: vm.settings.signatureTicket,
				arrayTranslate: vm.storeTranslation,
				sendEmail: vm.settings.sendEmail,
				events: vm.settings.events,
				selectedEvents: vm.selectedEvents,
				notificationsOficce: vm.settings.notificationsOficce,
				selectedStatus: vm.selectedStatus,
				showCategory: vm.settings.showCategory,
			};

			//console.log(my_object);
			vm.$emit('addTicket', my_object);
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
				type_question: 'TICKET',
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


			// Swal.fire({
			// 	title: this.$t('bs-question'),
			// 	html:
			// 	'<input id="swal-input1" placeholder='+this.$t('bs-question')+' style="margin:0;padding:0;" class="swal2-input">' +
			// 	'<label style="margin-top:10px;">'+this.$t('bs-select-the-answer-type')+'</label>' +
			// 	'<select id="swal-input2" class="form-control swal2-input" id="exampleFormControlSelect1" style="margin-top:0;padding:0;">' +
			// 	'<option value="TEXT" selected>TEXT</option>' +
			// 	'</select>' +
			// 	'<input class="form-check-input" type="checkbox" id="testeee">' +
			// 	'<label class="form-check-label" for="defaultCheck1">' +
			// 	this.$t('bs-is-it-mandatory') +
			// 	'</label>',
			// 	showCancelButton: true,
			// 	preConfirm: (formValues) => {

			// 		if(document.getElementById('swal-input1').value == ''){
			// 			return vm.$snotify.info(vm.$t('bs-field-invalid'), 'Info');
			// 		}
			// 		if(document.getElementById('testeee').checked == null){
			// 			document.getElementById('testeee').checked = 0;
			// 		}
			// 		axios.post(`${this.base_url}/department/set-setting-questions`, {
			// 			id_department: vm.itemselected.id,
			// 			quest: document.getElementById('swal-input1').value,
			// 			type: document.getElementById('swal-input2').value,
			// 			mandatory: document.getElementById('testeee').checked,
			// 			type_question: 'TICKET',
			// 		}).then(function(response){
			// 			//console.log(response.data);
			// 			if(response.data.success){

			// 				var my_object = {
			// 					id: response.data.id,
			// 					quest: response.data.question,
			// 					type: response.data.type,
			// 					mandatory: response.data.mandatory,
			// 					active: 1,
			// 				};

			// 				vm.quests.push(my_object);

			// 				//console.log(vm.quests);

			// 				vm.$snotify.success(vm.$t('bs-saved-successfully'), vm.$t('bs-success'));
			// 			}else{
			// 				vm.$snotify.error(vm.$t('bs-error-trying-to-save'), vm.$t('bs-error'));
			// 			}
			// 		}).catch(function(erro){
			// 			console.log(erro);
			// 			console.log('FAILURE!!');
			// 		});
			// 	},
			// }).then((result) => {
			// });
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
	}
};
</script>

<style scoped>

.caret {
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

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

.bs-times{
	color: red;
	margin-left: 4px;
}

.bs-ok{
	color: green;
	margin-left: 4px;
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

.modal-body-custom label {
    font: normal normal bold 14px/20px Muli !important;
    letter-spacing: 0px;
    color: #656565;
}

.modal-body-custom select {
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
