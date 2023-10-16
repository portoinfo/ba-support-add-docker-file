<template>
    <div>
        <div class="modal fade" id="PrivacyTerms" tabindex="-1" aria-labelledby="PrivacyTermsLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                   	<center><h5 class="modal-title" id="PrivacyTermsLabel">{{$t('bs-Terms-of-Use-and-Privacy-Policy')}}</h5></center>
                    <br>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group textsubb">
                                    <label for="exampleFormControlSelect1">
                                        {{$t('bs-i-declare-that-have-read-and-agree-with')}}
                                        <b-link target="_blank" :href="politicaPrivacidade">{{$t('bs-terms-of-use')}}<br>
                                        {{$t('bs-and-symbol')}} {{$t('bs-privacy-policy')}}</b-link>
                                        {{$t('bs-of-the-system')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="viewCancel" class="btn btn-danger text-capitalize" data-dismiss="modal">
                            {{$t('bs-to-deny')}}
                        </button>
                        <button type="button" @click="accept" id="btn-department" class="btn btn-success text-capitalize">
                            {{$t('bs-accept')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
	data(){
		return {
			politicaPrivacidade: "#",
			Ingles: 'https://final-helpdesk-terms.cheetah.builderall.com/',
        	Portugues: 'https://final-helpdesk-terms.cheetah.builderall.com/termos-de-uso',
		}
	},
	props:{
		user:Object,
		userLang:String,
	},
    mounted(){
        var vm = this;
		if(vm.user.terms_user == 0){
			$("#PrivacyTerms").modal("hide");
			axios.get("config-terms", {
			}).then(function (r_resposta) {
				if(r_resposta.data.success){
					if(r_resposta.data.result.terms_user == 0){
						$("#PrivacyTerms").modal("show");
					}
				}else{
					vm.$snotify.error(vm.$t('bs-error'), vm.$t('bs-error'));
				}
			}).catch(function (error) {
			console.log(error);
			});
		}

		if(vm.userLang == "pt_BR"){
			vm.politicaPrivacidade = vm.Portugues;
		}else{
			vm.politicaPrivacidade = vm.Ingles;
		}
		
    },
    methods:{
        accept(){
            axios.post(`config-terms`, {
            }).then(function (r_resposta) {
                if(r_resposta.data.success){
                    $("#PrivacyTerms").modal("hide");
                }else{
                    vm.$snotify.error(vm.$t('bs-error'), vm.$t('bs-error'));
                }
            }).catch(function (error) {
            console.log(error);
            });
        },
        viewCancel(){
            window.open('/logout', '_self');
        }
    }
};
</script>

<style scoped>

.textsubb {
    text-align: center;
    font: normal normal bold 14px/20px Muli;
    letter-spacing: 0px;
    opacity: 0.75;
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
	font: normal normal bold 20px/26px Muli;
	letter-spacing: 0px;
	opacity: 1;
	color: #434343;
}

.modal {
	background-color: #59607173;
	opacity: 1;
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
	opacity: 1;
}

.modal-body label {
	font: normal normal bold 14px/35px Muli;
	letter-spacing: 0px;
	color: black;
	opacity: 1;
}

.modal-body select {
	background: #fafbfc 0% 0% no-repeat padding-box;
	border: 1px solid #dddddd;
	border-radius: 3px;
	height: 50px;
	opacity: 1;
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