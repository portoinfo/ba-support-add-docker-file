<template>
	<b-card :id="_uid" show class="h-100 ticket-information" :title="title">
		<b-list-group>
			<b-list-group-item class="break-word">
				<span v-if="itemselected.chat_type == 'CHANGED_TO_TICKET'">
					&nbsp;Ticket: #{{ itemselected.id }}&nbsp;| Chat: #{{ itemselected.chat_id_decry }}&nbsp;
				</span>
				<span v-else>
					&nbsp;Ticket: #{{ itemselected.id }}&nbsp;
				</span>
				<b-button
				 	v-show="itemselected.id !== undefined"
					class="local-button local-btn-style1"
					size="sm"
					pill
					variant="outline-secondary"
					@click="copyToClipboard(itemselected.id, 'idTicket')"
					:id="`id-tk-${_uid}`"
				>
					<i class="material-icons">content_copy</i>
				</b-button>
				<b-tooltip :target="`id-tk-${_uid}`" :show.sync="tooltips.idTicket"  triggers="manual" placement="right" :variant="tooltipVariant2" :container="''._uid">
					{{$t('bs-copied')}}
				</b-tooltip>
			</b-list-group-item>
		</b-list-group>

		<hr />
		<b-row
			id="answer-form"
			cols="1"
			class="mx-0"
		>
			<b-col class="title-answer-form" :id="`answer-form-title-${_uid}`" >{{form.titulo}} - <i class="fa fa-question-circle" aria-hidden="true"></i></b-col>
			<b-tooltip :target="`answer-form-title-${_uid}`" triggers="hover" placement="bottom" :variant="tooltipVariant" :container="''._uid">
				{{$t('bs-tooltip-answer')}}
			</b-tooltip>
			<b-col class="card-answer-form py-3">
				<b-row cols="1" no-gutters>
					<b-col
					v-for="(answer, index) in form.answers"
					:key="index"
					>
						<b-row no-gutters>
							<b-col cols="12" class="answer-title"><b>{{answer.question}}</b></b-col>
							<!-- <b-col cols="3" class="text-right answer-time">{{answer.hora}}</b-col> -->
							<b-col cols="12" class="answer-text limit">
								{{answer.answer}}&nbsp;
								<b-button
									class="local-button local-btn-style2"
									size="sm"
									pill
									variant="outline-secondary"
									@click="copyToClipboard(answer.answer, 'answers', index)"
									:id="`answer-${_uid}-${index}`"
								>
									<i class="material-icons">content_copy</i>
								</b-button>
								<b-tooltip :target="`answer-${_uid}-${index}`" :show.sync="tooltips.answers[index]"  triggers="manual" placement="right" :variant="tooltipVariant2" :container="''._uid">
									{{$t('bs-copied')}}
								</b-tooltip>
							</b-col>
						</b-row>
					</b-col>
				</b-row>
			</b-col>
		</b-row>
		<hr />
		<b-card-footer class="bs-label">
			<b-col class="px-0 mx-0 break-word" cols="auto" :id="`commentt-${_uid}`">
				{{$t('bs-comment')}} - <i class="fa fa-question-circle" aria-hidden="true"></i>
			</b-col>
			<b-tooltip :target="`commentt-${_uid}`" triggers="hover" placement="bottom" :variant="tooltipVariant" :container="''._uid">
				{{$t('bs-tooltip-comment')}}
			</b-tooltip>
			<span :style="update.style" class="break-word">{{update.info}}</span>
		</b-card-footer>
		<div class="p-3">
			<b-button
				v-show="itemselected.comments !== undefined && itemselected.comments !== null && itemselected.comments.trim() != ''"
				class="local-button mb-2 local-btn-style1"
				size="sm"
				pill
				variant="outline-secondary"
				@click="copyToClipboard(itemselected.comments, 'comment')"
				:id="`comment-${_uid}`"
			>
				<i class="material-icons">content_copy</i>
			</b-button>
			<b-tooltip :target="`comment-${_uid}`" :show.sync="tooltips.comment"  triggers="manual" placement="right" :variant="tooltipVariant2" :container="''._uid">
				{{$t('bs-copied')}}
			</b-tooltip>
			<span v-if="itemselected.email_created != null">
				<b-form-textarea
			      id="textarea"
			      v-model="itemselected.comments"
			      @change="updatecomment"
			      placeholder="Enter something..."
			      rows="4"
			    ></b-form-textarea>
			</span>
		</div>
		<hr />
		<div class="px-3 mb-3 break-word title-client">{{title2}}:</div>
		<b-list-group>
			<b-list-group-item class="break-word">
				<!-- <i class="bbi bbi-user bbi-16 mr-1"></i> -->
				<gravatar
					v-if="showData"
					:email="itemselected.email_created"
					:status="$status.get(itemselected.id_created)"
					size="25px"
					:name="$t(itemselected.name_created)"
					color="light"
					:ba_acct_data="itemselected.builderall_account_data"
				/>
				&nbsp;{{$t(itemselected.name_created)}}&nbsp;
				<b-button
				 	v-show="itemselected.name_created !== undefined"
					class="local-button local-btn-style1"
					size="sm"
					pill
					variant="outline-secondary"
					@click="copyToClipboard(itemselected.name_created, 'creatorName')"
					:id="`creator-name-${_uid}`"
				>
					<i class="material-icons">content_copy</i>
				</b-button>
				<b-tooltip :target="`creator-name-${_uid}`" :show.sync="tooltips.creatorName"  triggers="manual" placement="right" :variant="tooltipVariant2" :container="''._uid">
					{{$t('bs-copied')}}
				</b-tooltip>
			</b-list-group-item>
			<b-list-group-item class="break-word">
				<i class="bbi bbi-email bbi-16 mr-1"></i>
				&nbsp;{{itemselected.email_created}}&nbsp;
				<b-button
				 	v-show="itemselected.email_created !== undefined"
					class="local-button local-btn-style1"
					size="sm"
					pill
					variant="outline-secondary"
					@click="copyToClipboard(itemselected.email_created, 'creatorEmail')"
					:id="`creator-email-${_uid}`"
				>
					<i class="material-icons">content_copy</i>
				</b-button>
				<b-tooltip :target="`creator-email-${_uid}`" :show.sync="tooltips.creatorEmail"  triggers="manual" placement="right" :variant="tooltipVariant2" :container="''._uid">
					{{$t('bs-copied')}}
				</b-tooltip>
			</b-list-group-item>
			<!-- <b-list-group-item class="break-word">
				<i class="bbi bbi-phone bbi-16 mr-1"></i> (--) --
			</b-list-group-item> -->
			<b-list-group-item class="break-word">
				<i class="bbi bbi-users bbi-16 mr-1"></i>
				&nbsp;{{$t(itemselected.department)}}&nbsp;
				<b-button
				 	v-show="itemselected.department !== undefined"
					class="local-button local-btn-style1"
					size="sm"
					pill
					variant="outline-secondary"
					@click="copyToClipboard($t(itemselected.department), 'department')"
					:id="`department-${_uid}`"
				>
					<i class="material-icons">content_copy</i>
				</b-button>
				<b-tooltip :target="`department-${_uid}`" :show.sync="tooltips.department"  triggers="manual" placement="right" :variant="tooltipVariant2" :container="''._uid">
					{{$t('bs-copied')}}
				</b-tooltip>
			</b-list-group-item>
		</b-list-group>

		<hr />
			<template v-if="itemselected.builderall_account_data && itemselected.id !== undefined">
				<div class="px-3 mb-3 break-word title-client">{{ $t('bs-builderall-office-data') }}</div>
				<b-list-group>
				<b-list-group-item class="break-word" v-if="JSON.parse(itemselected.builderall_account_data)['is_vip']">
					<img src="images/icons/vip.svg" alt="vip" class="img_vip">
				</b-list-group-item>
				<b-list-group-item class="break-word">
					<span class="noselect" style="color: #a5b9d5">#&nbsp;</span>
					&nbsp;{{ JSON.parse(itemselected.builderall_account_data)["id"] }}&nbsp;
					<b-button
					v-show="itemselected.email_created !== undefined"
					class="local-button local-btn-style1"
					size="sm"
					pill
					variant="outline-secondary"
					@click="
						copyToClipboard(
						JSON.parse(itemselected.builderall_account_data)['id'],
						'creatorAccountData'
						)
					"
					:id="`creator-builderall_account_data-${_uid}`"
					>
					<i class="material-icons">content_copy</i>
					</b-button>
					<b-tooltip
					:target="`creator-builderall_account_data-${_uid}`"
					:show.sync="tooltips.creatorAccountData"
					triggers="manual"
					placement="right"
					:variant="tooltipVariant2"
					:container="''._uid"
					>
					{{ $t("bs-copied") }}
					</b-tooltip>
				</b-list-group-item>
				<b-list-group-item class="break-word">
					<span class="noselect uuid" style="color: #a5b9d5">UUID&nbsp;</span>
					&nbsp;{{ JSON.parse(itemselected.builderall_account_data)["uuid"] }}&nbsp;
					<b-button
					v-show="itemselected.email !== undefined"
					class="local-button local-btn-style1"
					size="sm"
					pill
					variant="outline-secondary"
					@click="
						copyToClipboard(
						JSON.parse(itemselected.builderall_account_data)['uuid'],
						'creatorAccountData'
						)
					"
					:id="`creator-builderall_account_data-${_uid}`"
					>
					<i class="material-icons">content_copy</i>
					</b-button>
					<b-tooltip
					:target="`creator-builderall_account_data-${_uid}`"
					:show.sync="tooltips.creatorAccountData"
					triggers="manual"
					placement="right"
					:variant="tooltipVariant2"
					:container="''._uid"
					>
					{{ $t("bs-copied") }}
					</b-tooltip>
				</b-list-group-item>
				</b-list-group>
			</template>
		<hr />

		<b-list-group>
			<b-list-group-item class="break-word">
				<i class="bbi bbi-calendar bbi-16 mr-1"></i>
				&nbsp;{{ formatDataDays(itemselected.created_at) }}&nbsp;
				<b-button
				 	v-show="itemselected.created_at !== undefined"
					class="local-button local-btn-style1"
					size="sm"
					pill
					variant="outline-secondary"
					@click="copyToClipboard(formatDataDays(itemselected.created_at), 'creationDate')"
					:id="`creation-date-${_uid}`"
				>
					<i class="material-icons">content_copy</i>
				</b-button>
				<b-tooltip :target="`creation-date-${_uid}`" :show.sync="tooltips.creationDate"  triggers="manual" placement="right" :variant="tooltipVariant2" :container="''._uid">
					{{$t('bs-copied')}}
				</b-tooltip>
			</b-list-group-item>
			<b-list-group-item class="break-word">
				<i class="bbi bbi-clock-nine bbi-16 mr-1"></i>
				&nbsp;{{ formatDataHour(itemselected.created_at) }}&nbsp;
				<b-button
				 	v-show="itemselected.created_at !== undefined"
					class="local-button local-btn-style1"
					size="sm"
					pill
					variant="outline-secondary"
					@click="copyToClipboard(formatDataHour(itemselected.created_at), 'creationHour' )"
					:id="`creation-hour-${_uid}`"
				>
					<i class="material-icons">content_copy</i>
				</b-button>
				<b-tooltip :target="`creation-hour-${_uid}`" :show.sync="tooltips.creationHour"  triggers="manual" placement="right" :variant="tooltipVariant2" :container="''._uid">
					{{$t('bs-copied')}}
				</b-tooltip>
			</b-list-group-item>
		</b-list-group>

		<hr v-if="showList" />

		<b-list-group v-if="showList">
			<b-list-group-item class="break-word">
				<i class="bbi bbi-description bbi-18 mr-1"></i>
				&nbsp;{{itemselected.description | truncate(80)}}&nbsp;
				<b-button
				 	v-show="itemselected.description !== undefined"
					class="local-button local-btn-style1"
					size="sm"
					pill
					variant="outline-secondary"
					@click="copyToClipboard(itemselected.description, 'description')"
					:id="`description-${_uid}`"
				>
					<i class="material-icons">content_copy</i>
				</b-button>
				<b-tooltip :target="`description-${_uid}`" :show.sync="tooltips.description"  triggers="manual" placement="right" :variant="tooltipVariant2" :container="''._uid">
					{{$t('bs-copied')}}
				</b-tooltip>
			</b-list-group-item>
		</b-list-group>

		<hr class="mb-0 pb-0"/>

		<b-list-group-item
			:class="['break-word m-0 pl-4 py-2', Object.keys(itemselected).length > 0 ? 'history cursor-pointer' : ''] "
			@click="openClientHistory(itemselected.chat_created_by_encrypted)"
		>
			<img class="ticket-information-img" src="/images/icons/chat/history.svg" />
			&nbsp;<span v-if="Object.keys(itemselected).length > 0">{{ $t('bs-view-customer-history') }}</span>
			<!-- View customer history -->
		</b-list-group-item>

		<hr class="pb-0 mt-0" />

		<b-list-group class="mb-2">
			<!--<b-list-group-item class="break-word">
				<i class="bbi bbi-locale bbi-18 mr-1"></i>
			</b-list-group-item> -->
			<b-list-group-item class="break-word">
				<i class="bbi bbi-zone bbi-18 mr-1"></i>
				&nbsp;{{itemselected.user_agent}}&nbsp;
				<b-button
				 	v-show="itemselected.user_agent !== undefined"
					class="local-button local-btn-style1"
					size="sm"
					pill
					variant="outline-secondary"
					@click="copyToClipboard(itemselected.user_agent, 'userAgent')"
					:id="`user-agent-${_uid}`"
				>
					<i class="material-icons">content_copy</i>
				</b-button>
				<b-tooltip :target="`user-agent-${_uid}`" :show.sync="tooltips.userAgent"  triggers="manual" placement="right" :variant="tooltipVariant2" :container="''._uid">
					{{$t('bs-copied')}}
				</b-tooltip>
			</b-list-group-item>
			<!--<b-list-group-item class="break-word">
				<i class="bbi bbi-settings bbi-18 mr-1"></i> Ubuntu
			</b-list-group-item>
			<b-list-group-item class="break-word">
				<i class="bbi bbi-locale bbi-18 mr-1"></i> 143.255.93.154
			</b-list-group-item> -->
		</b-list-group>


		<!-- copy -->
		<div class="hidden-input">
			<textarea :ref="`input-${_uid}`"></textarea>
		</div>
	</b-card>
</template>

<script>
export default {
	data() {
		return {
			tooltipVariant: 'secondary',
			tooltipVariant2: 'info',
			update: {
				info: '',
				style: ''
			},
			title: this.$t('bs-ticket-information'),
			title2: this.$t('bs-ticket-created-by'),
			showList: true,
			tz: '',
			form: {
				titulo: this.$t('bs-initial-form'),
				answers: []
			},
			tooltips: {
				idTicket: false,
				answers: [],
				creatorName: false,
				creatorEmail: false,
				department: false,
				creationDate: false,
				creationHour: false,
				description: false,
				userAgent: false,
				comment: false
			},
			showData: false,
		};
	},
	props:{
		itemselected: Object,
		user: {
			type: Object,
			default: () => {
				language: "pt_BR"
			}
		},
		openClientHistory: Function
	},
	created(){
		this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
		if(this.form.answers.length == 0 && Object.keys(this.itemselected).length > 0 ) {
			this.getAnswers(this.itemselected.id_encrypted)
		}else {
			this.$set(this.form, 'answers', [] )
		}
		this.showData = true;
	},
	mounted(){
		// alert('eae');
		// var vm = this;
		// axios.get('tickets/get-tickets-creator/'+vm.itemselected.id).then(function(r_resposta){
		// 	console.log(r_resposta.data.result);
		// }).catch(function (error) {
		// 	console.log(error);
		// });
	},
	methods: {
		updatecomment(){
			var vm = this;
			axios.post('ticket-update-comment', {
				id_ticket: vm.itemselected.id,
				comments: vm.itemselected.comments
			}).then(function(response){
				if(response.data.success){

					vm.update = {
						info: vm.$t('bs-saved'),
						style: 'color:green'
					};
					setTimeout(vm.clear, 2000);
				}
			})
			.catch(function(){});
		},
		clear(){
			var vm = this;
			vm.update = {
				info: '',
				style: ''
			}
		},
	  	formatDataHour(value = null){
			if(value === null) {
				return ''
			} else {
				let h_format = moment(value, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD HH:mm:ss");
				let datetime = h_format.split(" ");
				let date = datetime[0];
				let time = datetime[1];
				let date_split = date.split("-");
				let time_split = time.split(":");
				let year = date_split[0];
				let month = date_split[1];
				let day = date_split[2];
				let hour = time_split[0];
				let minute = time_split[1];
				let second = time_split[2];
				var month_integer = parseInt(month, 10);
				if (month_integer >= 1) {
					month_integer--;
				}
				let dateUTC = new Date(Date.UTC(year, month_integer, day, hour, minute, second));
				let converted_time = dateUTC.toLocaleString("pt-BR", {
					timeZone: this.tz,
				});

				var mt = require("moment-timezone");
				return mt(converted_time, "DD/MM/YYYY HH:mm:ss")
					.tz(this.tz)
					.locale(this.user.language)
					.format('LTS');
			}
		},
		formatDataDays(value = null){
			if(value === null) {
				return ''
			} else {
				let h_format = moment(value, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD HH:mm:ss");
				let datetime = h_format.split(" ");
				let date = datetime[0];
				let time = datetime[1];
				let date_split = date.split("-");
				let time_split = time.split(":");
				let year = date_split[0];
				let month = date_split[1];
				let day = date_split[2];
				let hour = time_split[0];
				let minute = time_split[1];
				let second = time_split[2];
				var month_integer = parseInt(month, 10);
				if (month_integer >= 1) {
					month_integer--;
				}
				let dateUTC = new Date(Date.UTC(year, month_integer, day, hour, minute, second));
				let vm = this
				let converted_time = dateUTC.toLocaleString("pt-BR", {
					timeZone: this.tz,
				});

				var mt = require("moment-timezone");
				return mt(converted_time, "DD/MM/YYYY HH:mm:ss")
					.tz(this.tz)
					.locale(this.user.language)
					.format('L');
			}
		},
		getAnswers(id){
			let vm = this
			axios
				.get("ticket-chat-answer/agent/get-ticket-chat-answers", {
				params: {
					id: id,
					reference: "ticket_id",
				},
				})
				.then((response) => {
					if (response.data.status) {
						vm.$set(vm.form, 'answers', response.data.result);
						vm.fillTooltipFlags()
					}
				});
		},
		copyToClipboard: function (text, modalFlag, index = null) {
			const elem = this.$refs[`input-${this._uid}`];
			elem.value = text;
			elem.select();
			document.execCommand('copy');
			elem.value = '';
			this.openTooltip(modalFlag, index)
		},
		openTooltip(modalFlag, index = null) {
			if(index != null) {
				this.$set(this.tooltips[modalFlag], index, true)
			} else {
				this.tooltips[modalFlag] = true
			}
			setTimeout(this.closeTootip, 2000, this, modalFlag, index)
		},
		closeTootip(vm, modalFlag, index = null){

			if(index != null) {
				this.$set(vm.tooltips[modalFlag], index, false)
			} else {
				vm.tooltips[modalFlag] = false
			}
		},
		fillTooltipFlags() {
			let len = this.form.answers.length
			for(let i = 0; i < len; i++  ) {
				this.tooltips.answers.push(false)
			}
			//console.log(this.tooltips.answers)
		}
	},
	filters: {
	  	// formatDataHour(value){
		// 	return moment(value).format('HH:mm:ss');
		// 	var moment = require('moment-timezone');
		// 	return moment(value).tz("UTC").format('HH:mm:ss');
		// },
		// formatDataDays(value){
		// 	return moment(value).format('DD/MM/YYYY');
		// 	var moment = require('moment-timezone');
		// 	return moment(value).tz("UTC").format('HH:mm:ss');
		// },
		truncate(str, length) {
			var output = str;
			if(str == undefined){
				return;
			}

			if (output.length > length){
				output = str.substring(0, length)+'...';
			}
			return output;
		},
	},
	computed: {
		// watch the entire as a new object
		// por fazer referencia ao mesmo objeto
		// no watch deep newVal.id_encrypted era igual a oldVal.id_encrypted
		computedTicket: function() {
			return JSON.parse(JSON.stringify(this.itemselected)); // copy object and remove reactivity
		}
	},
	watch: {
		computedTicket: {
			deep: true,
			handler: function(newVal, oldVal) {
				if(newVal.id_encrypted != oldVal.id_encrypted) {
					if(Object.keys(newVal).length > 0 ) {
						this.getAnswers(newVal.id_encrypted)
					} else {
						this.$set(this.form, 'answers', [] )
						this.$set(this.tooltips, 'answers', [] )
					}
				}
				this.showData = false;
				setTimeout(() => {
				this.showData = true;
				}, 8);
			}
		},
		// itemselected: function(newVal, oldVal) {
		// 	this.itemselected = [];
		// }
	}

};
</script>

<style scoped lang="scss">

	@media only screen and (max-width: 1429px) {
		.limit{
			max-width: 150px;
		}
	}

	.title-client,
	#answer-form{
		margin-top: -1rem;
	}
	.title-client,
	.title-answer-form{
		font-family: Muli;
		font-weight: 700;
		color: #707070;
		background-color: rgba(0, 0, 0, 0.03);
		padding-top: 12px;
		padding-bottom: 12px;
	}
	.card-answer-form{
		&>.row{
			border: 1px #ced4da solid;
			border-radius: 4px;
			background-color: white;
			min-height: 100px;
			&>.col{
				padding: 6px 4px;
				.answer-title{
					color: #333;
				}
				.answer-time{
					color: #6e6e6e;
				}
				.answer-text{
					color: #707070;
				}
			}
		}
	}

	::-webkit-scrollbar {
		width: 8px !important;
		height: 8px !important;
	}

	::-webkit-scrollbar-track {
		background: #dadfed !important;
		border-radius: 0px !important;
	}

	::-webkit-scrollbar-thumb {
		background: #82c8fa !important;
		border-radius: 2px !important;
	}

	::-webkit-scrollbar-thumb:hover {
		background: #82c8fa !important;
	}

	ul,
	.myUL {
		list-style-type: none;
		font: normal normal 600 15px/19px Muli;
		letter-spacing: 0px;
		color: #7c94b4;
		opacity: 1;
	}

	.ticket-information::v-deep {
		overflow-y: auto!important;
		padding: 10px 0;
		width: 100%!important;
	}

	.ticket-information::v-deep .card-body {
		padding-left: 0px;
		padding-right: 0px;
		height: 100%!important;
	}

	.ticket-information::v-deep .list-group-item {
		border: none;
		font: normal normal normal 15px/24px Muli;
		letter-spacing: 0px;
		color: #656872;
		opacity: 1;
		padding: 0px;
		margin-left: 20px;
		margin-right: 20px;
	}

	.ticket-information-img {
		width: 15px;
		margin-right: 5px;
	}

	.card-title {
		font: normal normal 600 16px/24px Muli;
		letter-spacing: 0px;
		color: #434343;
		opacity: 1;
		margin-left: 20px;
		margin-right: 20px;
	}

	.break-word{
		word-wrap: break-word;
		word-break: break-word;
	}
	.hidden-input{
		width: 1px;
		height: 1px;
		max-width: 1px;
		max-height: 1px;
		overflow: hidden;
		opacity: 0;
	}

	.btn:focus{
		box-shadow: none !important;
	}
	.local-button{
		padding: 0.20rem 0.40rem!important;
		font-size: 0.63rem!important;
	}

	.local-btn-style1::v-deep{
		color: #a5b9d5!important;
		border-color: #a5b9d5!important;
		&:hover{
			color: #fff!important;
			background-color: #a5b9d5!important;
		}
	}

	.local-btn-style2::v-deep{
		color: #BDBDBD!important;
		border-color: #BDBDBD!important;
		&:hover{
			color: #fff!important;
			background-color: #BDBDBD!important;
		}
	}

	.span-copy{
		color: green;
	}

	.history {
		height: 44px;
		font-weight: bold !important;
	}

	.history:hover {
		background: #0080fc;
		color: whitesmoke !important;
	}

	.img_vip {
		width: fit-content !important;
		height: 20px !important;
	}
</style>
