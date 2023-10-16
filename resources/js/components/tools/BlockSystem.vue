<template>
    <div>
        <div class="modal fade" id="BlockSystem" tabindex="-1" aria-labelledby="BlockSystemLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="border-0 p-0">
						<center><h5 class="modal-title" id="BlockSystemLabel">{{$t('bs-blocked-company')}}</h5></center>
                        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="viewCancel">
                            X
                        </button> -->
                    </div>
                    <br><br>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group textsubb">
                                    <label for="exampleFormControlSelect1">
                                        {{$t('bs-company-access-is-blocked-due-to-account-s')}} {{user.email}} . <br>
										{{$t('bs-to-restore-access-contact-support')}}.
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
					
					<b-button  @click="viewSuport" class="text-capitalize btn ml-5 mr-5" data-dismiss="modal" variant="primary">{{$t('bs-support')}}</b-button>
					<br>
					<b-button  @click="viewCancel" class="text-capitalize btn ml-5 mr-5" data-dismiss="modal" variant="danger">{{$t('bs-cancel')}}</b-button>
					<br>
		
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
	data(){
		return {
			politicaPrivacidade: "#terms-privacy-policy/"+this.userLang,
			termoUso: "#terms-of-use/"+this.userLang,
		}
	},
	props:{
		user:Object,
	},
    mounted(){
		// $("#BlockSystem").modal("show");
        // var vm = this;
		// if(vm.user.terms_user == 0){
		// 	$("#PrivacyTerms").modal("hide");
		// 	axios.get("config-terms", {
		// 	}).then(function (r_resposta) {
		// 		if(r_resposta.data.success){
		// 			if(r_resposta.data.result.terms_user == 0){
		// 				$("#PrivacyTerms").modal("show");
		// 			}
		// 		}else{
		// 			vm.$snotify.error(vm.$t('bs-error'), vm.$t('bs-error'));
		// 		}
		// 	}).catch(function (error) {
		// 	console.log(error);
		// 	});
		// }
    },
    methods:{
        // accept(){
        //     axios.post(`config-terms`, {
        //     }).then(function (r_resposta) {
        //         if(r_resposta.data.success){
        //             $("#PrivacyTerms").modal("hide");
        //         }else{
        //             vm.$snotify.error(vm.$t('bs-error'), vm.$t('bs-error'));
        //         }
        //     }).catch(function (error) {
        //     console.log(error);
        //     });
        // },
        viewCancel(){
            $("#BlockSystem").modal("hide");
        },
        viewSuport(){
            window.open('https://office.builderall.com/us/office/tickets', '_self');
        }
    }
};
</script>

<style scoped>

.f-26{
	font-weight: bold;
	color: blue;
}

.textsubb {
    text-align: center;
    font: normal normal bold 14px/20px Muli;
    letter-spacing: 0px;
	color: black;
    opacity: 1;
}



.caret {
	cursor: pointer;
	-webkit-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
}

h5 {
	font: normal normal bold 16px/22px Muli;
	letter-spacing: 0px;
	color: #434343;
}

.content {
	margin-right: 40px;
	margin-left: 40px;
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
	font: normal normal bold 28px/32px Muli;
	letter-spacing: 0px;
	color: #201f1f;
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