<template>
	<div>
		<b-container fluid class="bs-label">
			<b-form-group id="input-group-3" :label="lbTipo" label-for="input-3">

				<multiselect 
					v-model="value" 
					:placeholder="$t('bs-please-select-an-option')" 
					label="title" 
					track-by="title" 
					:options="options" 
					:option-height="104" 
					:show-labels="false"
					:allow-empty="false"
				>
					<template slot="singleLabel" slot-scope="props">
						<evaluation-options :props="props"/>
					</template>

					<template slot="option" slot-scope="props">
						<evaluation-options :props="props"/>
					</template>
				</multiselect>

			</b-form-group>

			<b-form-group class="bodyNew" id="input-group-1" label-for="input-1">
				<template v-slot:label>
					<span id="tooltip-evaluation-attendant">
						{{lbEvaluationAten}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
					</span>
				</template>
				<div class="justify_switch">
					<label class="switch" style="margin-top:5px;">
						<input type="checkbox" v-model="settings.text_atten_active" @change="someHandler">
						<span class="slider round"></span>
					</label>
				</div>
			</b-form-group>
			<b-tooltip target="tooltip-evaluation-attendant" triggers="hover" placement="right" variant="secondary">
				{{$t('bs-tooltip-evaluation-attendant')}}
			</b-tooltip>


			



			<b-form-group class="bodyNew" id="input-group-1" label-for="input-1">
				<template v-slot:label>
					<span id="tooltip-evaluation-service">
						{{lbEvaluationService}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
					</span>
				</template>
				<div class="justify_switch">
					<label class="switch" style="margin-top:5px;">
						<input type="checkbox" v-model="settings.text_serv_active" @change="someHandler">
						<span class="slider round"></span>
					</label>
				</div>
			</b-form-group>
			<b-tooltip target="tooltip-evaluation-service" triggers="hover" placement="right" variant="secondary">
				{{$t('bs-tooltip-evaluation-service')}}
			</b-tooltip>

			<b-form-group class="bodyNew" id="input-group-2" label-for="input-2">
				<template v-slot:label>
					<span id="tooltip-evaluation-comment">
						{{lbComment}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
					</span>
				</template>
				<div class="justify_switch">
					<label class="switch" style="margin-top:5px;">
						<input type="checkbox" v-model="settings.text_comment_active" @change="someHandler">
						<span class="slider round"></span>
					</label>
				</div>
			</b-form-group>
			<b-tooltip target="tooltip-evaluation-comment" triggers="hover" placement="right" variant="secondary">
				{{$t('bs-tooltip-evaluation-comment')}}
			</b-tooltip>

		</b-container>
		<vue-snotify></vue-snotify>
	</div>
</template>

<script>
import evaluationOptions from './evaluation-options.vue';

export default {
  components: { evaluationOptions },
	data(){
		return {
			lbTipo: this.$t('bs-type'),
			lbEvaluationAten: this.$t('bs-enable-attendant-evaluation'),
			lbEvaluationService: this.$t('bs-enable-service-evaluation'),
			lbComment: this.$t('bs-enable-comment-rating'),
			phName: this.$t('bs-enter-text'),
			evaluations: [{ text: this.$t('bs-good')+' '+this.$t('bs-or')+' '+this.$t('bs-bad')+ '(2)'}],
			// evalaluations select
			value: Object,
			options: [
				{ title: this.getAvaliationTitleByType('good_bad'), desc: '1 - 5', type: 'good_bad' },
				{ title: this.getAvaliationTitleByType('stars'), desc: '1 - 2 - 3 - 4 - 5', type: 'stars' },
			]
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
		someHandler(){
			var vm = this;

			var my_object = {
				text_atten: vm.$t('bs-please-rate-the-attendant')+':',
				text_atten_active: vm.settings.text_atten_active,
				text_serv: vm.$t('bs-please-leave-a-comment-informing-your-exp')+':',
				text_serv_active: vm.settings.text_serv_active,
				text_comment: vm.$t('bs-please-leave-a-comment-informing-your-exp')+':',
				text_comment_active: vm.settings.text_comment_active,
				typeevaluation: vm.settings.typeevaluation,
			};
			vm.$emit('addEvaluation', my_object);
		},
		getAvaliationTitleByType(type) {
			switch (type) {
				case 'good_bad':
					return this.$t('bs-thumbs')
					break;

				case 'stars':
					return this.$t('bs-stars')
					break;
				
			}
		},
		getAvaliationDescByType(type) {
			switch (type) {
				case 'good_bad':
					return "1 - 5";
					break;

				case 'stars':
					return "1 - 2 - 3 - 4 - 5";
					break;
				
			}
		}
	},
	created () {
		if (this.settings.typeevaluation == null) {
			this.value = { title: this.getAvaliationTitleByType('stars'), desc: this.getAvaliationDescByType('stars'), type: 'stars' }
		} else {
			var type = this.settings.typeevaluation;
			this.value = { title: this.getAvaliationTitleByType(type), desc: this.getAvaliationDescByType(type), type: type }
		}
	},
	watch: {
		value(newValue) {
			this.settings.typeevaluation = newValue.type;
		}
	},
};
</script>

<style scoped>

.bodyNew{
	background-color: white;
	padding: 14px;
	padding-bottom: 0px;
	padding-left: 12px;
	font-weight: bold;
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
.justify_switch{
	text-align: right;
	margin-top: -32px;
	margin-right: 4px;
}



.inputInside{
	margin-top: 18px;
	margin-left: -70px;
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
