<template>
	<div>
		<b-container fluid="lg">
			<b-row class="bodyNew">
				<b-col lg>
					<b-row class='ml-2'>
						<b-col class="px-0 mx-0" cols="auto" id="chat-wait-time">
							{{$t('bs-chat-wait-time')}} ({{$t('bs-minutes')}}):
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</b-col>
						<b-tooltip target="chat-wait-time" triggers="hover" placement="right" :variant="tooltipVariant">
							{{$t('bs-tooltip-chat-wait-time')}}
						</b-tooltip>
						<b-col>
						</b-col>
						<b-col cols="auto">
							<input type="number" @change="emitvalues" v-model="localSettings.timewait" min="0" style="border: none;width: 60px;">
							<b-link href="#foo" @click="addNumber(1)"><i class="fa fa-plus fa-1x add" aria-hidden="true"></i></b-link>
							<b-link href="#foo" @click="removeNumber(1)"><i class="fa fa-minus fa-1x remove" aria-hidden="true"></i></b-link>
						</b-col>
					</b-row>
					<div class="ml-2 spantext">
						<span>{{$t('bs-if-it-is-set-to-0')}}</span>
					</div>
				</b-col>
			</b-row>
			<b-row class="bodyNew">
				<b-col lg>
					<b-row class='ml-2'>
						<b-col class="px-0 mx-0" cols="auto" id="chat-by-attendant">
							{{$t('bs-chat')}} {{$t('bs-by-attendant')}}:
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</b-col>
						<b-tooltip target="chat-by-attendant" triggers="hover" placement="right" :variant="tooltipVariant">
							{{$t('bs-tooltip-chat-by-attendant')}}
						</b-tooltip>
						<b-col>
						</b-col>
						<b-col cols="auto">
							<input type="number" @change="emitvalues" v-model="localSettings.quantidadechat" min="0" style="border: none;width: 60px;">
							<b-link href="#foo" @click="addNumber(2)"><i class="fa fa-plus fa-1x add" aria-hidden="true"></i></b-link>
							<b-link href="#foo" @click="removeNumber(2)"><i class="fa fa-minus fa-1x remove" aria-hidden="true"></i></b-link>
						</b-col>
					</b-row>
					<div class="ml-2 spantext">
						<span>{{$t('bs-if-it-is-set-to-0')}}</span>
					</div>
				</b-col>
			</b-row>
			<b-row class="bodyNew">
				<b-col lg>
					<b-row class='ml-2'>
						<b-col class="px-0 mx-0" cols="auto" id="ticket-by-attendant">
							Ticket {{$t('bs-by-attendant')}}:
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</b-col>
						<b-tooltip target="ticket-by-attendant" triggers="hover" placement="right" :variant="tooltipVariant">
							{{$t('bs-tooltip-ticket-by-attendant')}}
						</b-tooltip>
						<b-col>
						</b-col>
						<b-col cols="auto">
							<input type="number" @change="emitvalues" v-model="localSettings.quantidadeticket" min="0" style="border: none;width: 60px;">
							<b-link href="#foo" @click="addNumber(3)"><i class="fa fa-plus fa-1x add" aria-hidden="true"></i></b-link>
							<b-link href="#foo" @click="removeNumber(3)"><i class="fa fa-minus fa-1x remove" aria-hidden="true"></i></b-link>
						</b-col>
					</b-row>
					<div class="ml-2 spantext">
						<span>{{$t('bs-if-it-is-set-to-0')}}</span>
					</div>
				</b-col>
			</b-row>
<!-- 			<b-row class="bodyNew">
				<b-col lg>
					<b-row class='ml-2'>
						{{$t('bs-days-to-close-the-ticket')}}:
						<b-col>
						</b-col>
						<b-col cols="auto">
							<input type="number" v-model="localSettings.diasfecharchat" min="0" style="border: none;width: 60px;">
							<b-link href="#foo" @click="addNumber(3)"><i class="fa fa-plus fa-1x add" aria-hidden="true"></i></b-link>
							<b-link href="#foo" @click="removeNumber(3)"><i class="fa fa-minus fa-1x remove" aria-hidden="true"></i></b-link>
						</b-col>
					</b-row>
					<div class="ml-2 spantext">
						<span>{{$t('bs-if-it-is-set-to-0')}}</span>
					</div>
				</b-col>
			</b-row> -->
			<b-row class="bodyNew">
				<b-col lg>
					<b-row class='ml-2'>
						<b-col class="px-0 mx-0" cols="auto" id="inactivity-message-after-minutes">
							{{$t('bs-inactivity-message-after')}} ({{$t('bs-minutes')}}):
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</b-col>
						<b-tooltip target="inactivity-message-after-minutes" triggers="hover" placement="right" :variant="tooltipVariant">
							{{$t('bs-tooltip-inactivity-message-after-minutes')}}
						</b-tooltip>
						<b-col>
						</b-col>
						<b-col cols="auto">
							<input type="number" @change="emitvalues" v-model="localSettings.inactivityMessage" min="0" style="border: none;width: 60px;">
							<b-link href="#foo" @click="addNumber(5)"><i class="fa fa-plus fa-1x add" aria-hidden="true"></i></b-link>
							<b-link href="#foo" @click="removeNumber(5)"><i class="fa fa-minus fa-1x remove" aria-hidden="true"></i></b-link>
						</b-col>
					</b-row>
					<div class="ml-2 spantext">
						<span>{{$t('bs-if-it-is-set-to-0')}} </span>
					</div>
				</b-col>
			</b-row>
			<b-row class="bodyNew">
				<b-col lg>
					<b-row class='ml-2'>
						<b-col class="px-0 mx-0" cols="auto" id="time-ticket-for-becomes-late-minutes">
							{{$t('bs-time-ticket-for-becomes-late')}}  ({{$t('bs-minutes')}}):
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</b-col>
						<b-tooltip target="time-ticket-for-becomes-late-minutes" triggers="hover" placement="right" :variant="tooltipVariant">
							{{$t('bs-tooltip-time-tk-for-becomes-late-minutes')}}
						</b-tooltip>
						<b-col>
						</b-col>
						<b-col cols="auto">
							<input type="number" @change="emitvalues" v-model="localSettings.lateTime" min="0" style="border: none;width: 60px;">
							<b-link href="#foo" @click="addNumber(6)"><i class="fa fa-plus fa-1x add" aria-hidden="true"></i></b-link>
							<b-link href="#foo" @click="removeNumber(6)"><i class="fa fa-minus fa-1x remove" aria-hidden="true"></i></b-link>
						</b-col>
					</b-row>
					<div class="ml-2 spantext">
						<span>{{$t('bs-if-it-is-set-to-0')}}</span>
					</div>
				</b-col>
			</b-row>
<!-- 			<b-row class="bodyNew">
				<b-col lg>
					<b-row class='ml-2'>
						{{$t('bs-number-of-open-tickets-per-customer')}}:
						<b-col>
						</b-col>
						<b-col cols="auto">
							<input type="number" @change="emitvalues"  v-model="localSettings.maxopentickclient" min="0" style="border: none;width: 60px;">
							<b-link href="#foo" @click="addNumber(7)"><i class="fa fa-plus fa-1x add" aria-hidden="true"></i></b-link>
							<b-link href="#foo" @click="removeNumber(7)"><i class="fa fa-minus fa-1x remove" aria-hidden="true"></i></b-link>
						</b-col>
					</b-row>
					<div class="ml-2 spantext">
						<span>{{$t('bs-if-it-is-set-to-0')}}</span>
					</div>
				</b-col>
			</b-row> -->
			<b-row class="bodyNew">
				<b-col lg>
					<b-row class='ml-2'>
						<b-col class="px-0 mx-0" cols="auto" id="time-reopen-ticket">
							{{$t('bs-time-to-reopen-ticket')}}  ({{$t('bs-days')}}):
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</b-col>
						<b-tooltip target="time-reopen-ticket" triggers="hover" placement="right" :variant="tooltipVariant">
							{{$t('bs-tooltip-time-tk-reopen')}}
						</b-tooltip>
						<b-col>
						</b-col>
						<b-col cols="auto">
							<input type="number" @change="emitvalues" v-model="localSettings.maxdaysreopenticket" min="0" style="border: none;width: 60px;">
							<b-link href="#foo" @click="addNumber(8)"><i class="fa fa-plus fa-1x add" aria-hidden="true"></i></b-link>
							<b-link href="#foo" @click="removeNumber(8)"><i class="fa fa-minus fa-1x remove" aria-hidden="true"></i></b-link>
						</b-col>
					</b-row>
					<div class="ml-2 spantext">
						<span>{{$t('bs-if-it-is-set-to-0')}}</span>
					</div>
				</b-col>
			</b-row>
		</b-container>
	</div>
</template>

<script>

export default {
	data(){
		return {
			tooltipVariant: 'secondary',
			localSettings: {
				timewait: 0,
				quantidadechat: 0,
				quantidadeticket: 0,
				diasfecharchat: 0,
				inactivityMessage: 0,
				lateTime: 0,
				maxopentickclient: 0,
				maxdaysreopenticket: 0
			}
		}
	},
	props:{
		itemselected: Object,
		settings: Object,
		idSettings: String,
		base_url: {
			type: String,
			default: ''
		},
	},
	methods:{
		emitvalues(){
			var vm = this;
			var create_item = {
				timewait: vm.localSettings.timewait,
				quantidadechat: vm.localSettings.quantidadechat,
				quantidadeticket: vm.localSettings.quantidadeticket,
				diasfecharchat: vm.localSettings.diasfecharchat,
				inactivityMessage: vm.localSettings.inactivityMessage,
				lateTime: vm.localSettings.lateTime,
				maxopentickclient: vm.localSettings.maxopentickclient,
				maxdaysreopenticket: vm.localSettings.maxdaysreopenticket,
			};
			vm.$emit('addQuantLimity', create_item);
		},
		addNumber(value){
			var vm = this;
			if(value == 1){
				vm.localSettings.timewait = this.limit( parseInt(vm.localSettings.timewait) + 1 );
			}else if(value == 2){
				// console.log('antes = ', vm.settings)
				vm.localSettings.quantidadechat = this.limit( parseInt(vm.localSettings.quantidadechat) + 1 );
			}else if(value == 3){
				// console.log('antes = ', vm.settings)
				vm.localSettings.quantidadeticket = this.limit( parseInt(vm.localSettings.quantidadeticket) + 1 );
			}else if(value == 4){
				vm.localSettings.diasfecharchat = this.limit( parseInt(vm.localSettings.diasfecharchat) + 1 );
			}else if(value == 5){
				vm.localSettings.inactivityMessage = this.limit( parseInt(vm.localSettings.inactivityMessage) + 1 );
			}else if(value == 6){
				vm.localSettings.lateTime = this.limit( parseInt(vm.localSettings.lateTime) + 1 );
			}else if(value == 7){
				vm.localSettings.maxopentickclient = this.limit( parseInt(vm.localSettings.maxopentickclient) + 1 );
			}else if(value == 8){
				vm.localSettings.maxdaysreopenticket = this.limit( parseInt(vm.localSettings.maxdaysreopenticket) + 1 );
			}
			vm.emitvalues();
		},
		removeNumber(value){
			var vm = this;
			if(value == 1){
				vm.localSettings.timewait = this.limit( parseInt(vm.localSettings.timewait) - 1 );
			}else if(value == 2){
				vm.localSettings.quantidadechat = this.limit( parseInt(vm.localSettings.quantidadechat) - 1 );
			}else if(value == 3){
				vm.localSettings.quantidadeticket = this.limit( parseInt(vm.localSettings.quantidadeticket) - 1 );
			}else if(value == 4){
				vm.localSettings.diasfecharchat = this.limit( parseInt(vm.localSettings.diasfecharchat) - 1 );
			}else if(value == 5){
				vm.localSettings.inactivityMessage = this.limit( parseInt(vm.localSettings.inactivityMessage) - 1 );
			}else if(value == 6){
				vm.localSettings.lateTime = this.limit( parseInt(vm.localSettings.lateTime) - 1 );
			}else if(value == 7){
				vm.localSettings.maxopentickclient = this.limit( parseInt(vm.localSettings.maxopentickclient) - 1 );
			}else if(value == 8){
				vm.localSettings.maxdaysreopenticket = this.limit( parseInt(vm.localSettings.maxdaysreopenticket) - 1 );
			}
			vm.emitvalues();
		},
		limit(val, limit = 0) {
			return val < limit ? 0 : val
		}
	},
	watch: {
		settings:  {
			handler(newVal, oldVal) {
				/* 
					nao e recomendado alterar diretamente as props no vuejs
					caso precise de manipulacao defina variavel local e altere quando
					a prop mudar.
				 */
				let local = JSON.parse(JSON.stringify(newVal))
				Object.keys(local).map(function(key, index) {
					local[key]  = parseInt(local[key]);
				})
				this.$set(this.$data, 'localSettings', Object.assign( {}, JSON.parse(JSON.stringify(this.localSettings)), local )   )
			},
			deep: true,
			immediate: true
		}
	},
	created(){
	},
	mounted(){
	}
};
</script>

<style scoped>
.bodyNew{
	background-color: white;
	margin-bottom:10px;
	padding: 15px;
	padding-left: 1px;
	font-weight: bold;
	color: #0080FC;
}
.spantext{
	background: #FFFFFF 0% 0% no-repeat padding-box;
	border-radius: 5px;
	color: #a4a4a4;
	margin-top: 6px;
	font-size: 12px;
}

.add{
	color: #0080FC;
	padding-right: 4px;
}
.remove{
	color: red;
}
</style>