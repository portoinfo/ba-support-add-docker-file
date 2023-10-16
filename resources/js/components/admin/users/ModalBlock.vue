<template>
    <div class="modal fade" id="showModalReason" tabindex="-1" aria-labelledby="showModalReasonLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header border-0 p-0">
					<h5 class="modal-title" id="showModalReasonLabel">{{$t('bs-reason-for-blocking')}}</h5>
					<button type="button" class="close" @click="Cancel" data-dismiss="modal" aria-label="Close">
						X
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-6">
                            <b-form-group :label="$t('bs-time') +':'" v-slot="{ ariaDescribedby }">
                                <b-form-radio v-model="selectedTime" :aria-describedby="ariaDescribedby" name="some-radios" value="day">{{$t('bs-one-day')}}</b-form-radio>
                                <b-form-radio v-model="selectedTime" :aria-describedby="ariaDescribedby" name="some-radios" value="week">{{$t('bs-one-week')}}</b-form-radio>
                                <b-form-radio v-model="selectedTime" :aria-describedby="ariaDescribedby" name="some-radios" value="mounth">{{$t('bs-one-month')}}</b-form-radio>
                                <b-form-radio v-model="selectedTime" :aria-describedby="ariaDescribedby" name="some-radios" value="year">{{$t('bs-one-year')}}</b-form-radio>
                                <b-form-radio v-model="selectedTime" :aria-describedby="ariaDescribedby" name="some-radios" value="permanent">{{$t('bs-permanent')}}</b-form-radio>
                                <!-- {{selectedTime}} -->
                            </b-form-group>
						</div>
                        <div class="col-6">
                            <b-form-group :label="$t('bs-lock-type')+':'" v-slot="{ ariaDescribedby2 }">
                                <b-form-radio v-model="selectedBan" :aria-describedby="ariaDescribedby2" name="some-radios2" value="chat">{{$t('bs-chat')}}</b-form-radio>
                                <b-form-radio v-model="selectedBan" :aria-describedby="ariaDescribedby2" name="some-radios2" value="ticket">{{$t('bs-ticket')}}</b-form-radio>
                                <b-form-radio v-model="selectedBan" :aria-describedby="ariaDescribedby2" name="some-radios2" value="system">{{$t('bs-of-the-system')}}</b-form-radio>
                                <!-- {{selectedBan}} -->
                            </b-form-group>
						</div>
						<div class="col-12">
							<div class="form-group">
								<label for="exampleFormControlSelect1">{{$t('bs-type-here')}}: </label>
								<b-form-textarea
                                id="textarea"
                                v-model="textReason"
                                :placeholder="$t('bs-type-here')+'...'"
                                rows="4"
                                ></b-form-textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer border-0">
					<button type="button" class="text-capitalize btn btn-primary" @click="Cancel" data-dismiss="modal">
						{{$t('bs-cancel')}}
					</button>
                    <span v-if="!showbuttom">
                        <button type="button" @click="clientStatus(index_block, 0)" data-dismiss="modal" id="btn-department" class="btn btn-danger">
                            {{$t('bs-block')}}
                        </button>
                    </span>
                    <span v-if="showbuttom">
                        <button type="button" @click="clientStatus(index_block, 1)" data-dismiss="modal" id="btn-department" class="btn btn-success">
                            {{$t('bs-unlock')}}
                        </button>
                    </span>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	data(){
		return {
            textReason: null,
            selectedTime: 'permanent',
            selectedBan: 'system',
            showbuttom: true,
		}
	},
    props:{
        index_block: Number,
        client_id: String,
        showmodal: Boolean,
        type: String,
        itemselected: Object,
    },
    created(){
        var vm = this;
        axios.get('check-block-ticket', {
            params: {
                id: vm.client_id,
            }
        }).then(function (r_resposta) {
            // console.log(r_resposta.data);
            if(r_resposta.data.reason == ''){
                vm.showbuttom = false;
            }else{
                vm.textReason = r_resposta.data.reason;
                vm.selectedTime = r_resposta.data.selectedTime;
                vm.selectedBan = r_resposta.data.selectedBan;
                vm.showbuttom = true;
            }
            
            $("#showModalReason").modal("show");
        }).catch(function (error) {
            console.log(error);
        });


        // if(this.type == 'SYSTEM'){

        // }else if(this.type == 'TICKET'){

        // }else{


        // }
        
    },
    methods:{
        clientStatus(index, typee){
            var vm = this;
            axios.post('set-client-status', {
                status: vm.showbuttom,
				client_id: vm.client_id,
                textReason: vm.textReason,
                selectedTime: vm.selectedTime,
                selectedBan: vm.selectedBan,
			})
            .then(function(r_resposta){
                if(r_resposta.data.success){
                    if(typee == 1){
                        vm.$snotify.success(vm.$t('bs-customer-successfully-released'), vm.$t('bs-success'), {
                            timeout: 1000,
                            showProgressBar: false,
                            pauseOnHover: true
                        });
                    }else{
                        vm.$snotify.success(vm.$t('bs-client-blocked-successfully'), vm.$t('bs-success'), {
                            timeout: 1000,
                            showProgressBar: false,
                            pauseOnHover: true
                        });
                        vm.textReason = null;
                        vm.selectedTime = 'permanent';
                        vm.selectedBan = 'system';
                        vm.showbuttom = false;
                    }
                    
                    if(vm.type == 'SYSTEM'){
                        vm.itemselected.status = !vm.itemselected.status;
                        vm.$root.$refs.UserClientBody.showmodal = false;
                    }else if(vm.type == 'TICKET'){
                        vm.$root.$refs.TicketTicket2.showModalReason = false;
                        vm.$root.$refs.TicketTicket2.statusblock = !vm.$root.$refs.TicketTicket2.statusblock;
                    }else{
                        vm.$root.$refs.FullChat2.showModalReason = false;
                    }

                }
            }).catch(function (error) {
                console.log(error);
            });
        },
        Cancel(){
            var vm = this;
            if(vm.type == 'SYSTEM'){
                vm.$root.$refs.UserClientBody.showmodal = false;
            }else if(vm.type == 'TICKET'){
                vm.$root.$refs.TicketTicket2.showModalReason = false;
            }else{
                vm.$root.$refs.FullChat2.showModalReason = false;
            }
        }
    }
};
</script>

<style scoped>

.scroll{
    overflow-y: scroll;
    max-height:380px;
}

.icon{
    padding-left: 30px;
    font: normal normal normal 14px/17px Muli;
}

.iconButton{
    top: 11px;
    left: 26px;
    position: absolute;
    text-align: center;
    justify-content: center;
    color: black;
    opacity: 0.75;
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
	height: 50px;
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