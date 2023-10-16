<template>
    <div class="answer-showed view" :class="{'mobile': isMobile}">
        <div class="ticket-answer-header" v-if="!isMobile">
            <div class="header_title d-table h-100 w-100">
                <span class="ba-hd__title d-table-cell align-middle">
                    {{$t('bs-view')}} - {{$t('bs-ticket')}} #{{ itemselected.id }}
					<div style="float: right;">
                        <b-button size="sm" variant="light" @click="modalDatabase">
                            <i class="fa fa-database" aria-hidden="true"></i>
                        </b-button>
                    </div>
                </span>
            </div>
        </div>
        <div v-else class="ticket-showed-header">
                <center class="item-gravatar">
                    <div class="vertical-center">
                        <b-button
                            class="btn-back-mobile"
                            variant="light"
                            @click="$root.$refs.TicketTicket2.showAnswer = false"
                        >
                            <span class="bs-ico">&#xe5c4;</span>
                            <gravatar
                                :email="itemselected.email_created"
                                :status="$status.get(itemselected.id_created)"
                                size="46px"
                                :name="$t(itemselected.name_created)"
                                color="primary"
                                :ba_acct_data="itemselected.builderall_account_data"
                            />
                        </b-button>
                    </div>
                </center>
                <div class="item-name pl-1">
                    <div class="vertical-bottom mw-100 pr-1">
                        <span class="text-truncate d-block">
                            <b>{{ $t(itemselected.name_created) }}</b>
                        </span>
                    </div>
                </div>
                <center class="item-btn">
                    <div class="vertical-center">
                        <b-button class="btn-close_info" variant="light" @click="$root.$emit('bv::toggle::collapse', 'sidebar-right-info-2')">
                            <span class="bs-ico">&#xe88e;</span>
                        </b-button>
                    </div>
                </center>
                <div class="item-dept pl-1 text-truncate">
                    <small>{{ `${$t('bs-ticket')} #${itemselected.id} - ${$t('bs-view')}`}}</small>
                </div>
        </div>


        <div class="ticket-answer">
             <div class="ba-hd__card-content p-3">
                <div class="ticket-view-header">
                    <b-form-group :label-cols="isMobile ? '12' : '1'" :label="`${$t('bs-receiver')}:`" label-for="input1">
                        <b-form-input disabled v-model="itemselected.name_created" id="input1"></b-form-input>
                    </b-form-group>
                    <b-form-group :label-cols="isMobile ? '12' : '1'" label="CC:"  label-for="input2">
                        <b-form-input disabled v-model="itemselected.email_created" id="input2"></b-form-input>
                    </b-form-group>
                    <b-form-group :label-cols="isMobile ? '12' : '1'" :label="`${$t('bs-attendants')}:`"  label-for="input3">
                        <b-form-input disabled v-model="subject" id="input3"></b-form-input>
                    </b-form-group>
                </div>
                <div class="border">
					<div class="output ql-snow">
                        <div class="ql-editor" v-viewer v-html="text"></div>
                    </div>
                </div>
                <br>
                <span class="v-title">{{$t('bs-change-ticket-status')}}</span>
                <b-form-group class="mb-2 mt-1"
                    id="input-group-1"
                    label-for="input-1">
                        <b-form-select
                            id="input-3"
                            v-model="status"
                            :options="statusOptions"
                            required
                            class="pt-0"
                        ></b-form-select>
                </b-form-group>
				<!-- <br>
				<span class="v-title">{{$t('bs-internal-comment')}}</span>
				<b-form class="mb-2 mt-1">
					<textarea
                        id="comments"
                        v-model="comments"
                        :placeholder="phMsg"
                        v-autogrow
                        class="border"
					></textarea>
				</b-form> -->
            </div>
        </div>

        <b-button variant="primary" class="float-btn-ticket-create" @click="sendMessage" v-if="isMobile">
            <span class="material-icons-outlined" style="font-size: 30px; position: relative; top: 2px;  left: 2px;">send</span>
        </b-button>

        <div class="ticket-answer-footer" v-if="!isMobile">
            <div class="text-left">
				<b-button size="sm" variant="primary" @click="categoryTicket">{{ $t('bs-categorize') }}</b-button>
            </div>
            <div class="text-right">
                <b-button size="sm" v-if="showbuttonSave" variant="success" @click="checkCategory">{{ $t('bs-save') }}</b-button> <!-- sendMessage -->
                <b-button size="sm" variant="secondary" @click="back()">{{ $t('bs-back') }}</b-button>
            </div>
        </div>
    </div>
</template>

<script>

export default {
	data(){
		return {
			phMsg: this.$t('bs-type-your-message-here'),
			text: "",
			status: 'IN_PROGRESS',
			showbuttonSave: false,
			statusOptions: [{ text: this.$t('bs-in-progress'), value: 'IN_PROGRESS' }],
			subject: this.user.email,
			comments: this.itemselected.comments,
			rejectCheck: false
		}
	},
	props:{
		openPhrase: String,
		closePhrase: String,
		textTicket: String,
		textAssin: String,
		user: Object,
		itemselected: Object,
		restriction: Array,
		is_admin: Number,
		files: Array,
		extImages: Array,
		saveDirect: Boolean,
        isMobile: Boolean,
		prestatus: String,
		settings: Object,
		openCategory: "",
        sendEmailSelected: String,
	},
	created () {
        this.$root.$refs.TicketView2 = this;
    },
	mounted(){
		var vm = this;

     	if(vm.openPhrase.trim() !== "") {
			vm.text += '<p>' + vm.$t(vm.openPhrase) + '</p>\n';
		}

		if(vm.textTicket != ''){
			vm.text += vm.textTicket + '\n';
		}

     	if(vm.closePhrase.trim() !== "") {
			vm.text += '<p>' + vm.$t(vm.closePhrase) + '</p>\n';
		}

     	if(vm.textAssin.trim() !== "") {
			vm.text += '<p>' + vm.textAssin + '</p>';
		}

		if(vm.restriction[0].ticket_close == 1 || vm.is_admin == 1 || vm.restriction[0].ticket_admin == 1){
			vm.statusOptions.push({text: this.$t('bs-closed'), value: 'CLOSED'})
		}

		if(vm.restriction[0].ticket_resolved == 1 || vm.is_admin == 1 || vm.restriction[0].ticket_admin == 1){
			if(vm.restriction[0].ticket_close == 1){
				vm.statusOptions.push({text: this.$t('bs-resolved'), value: 'RESOLVED'});
			}else{
				vm.statusOptions.push({text: this.$t('bs-finalized-s'), value: 'RESOLVED'});
			}
		}
		
		if(vm.saveDirect){
			vm.status = vm.prestatus;
			vm.showbuttonSave = true;
			vm.sendMessage();
		}else{
			vm.getConfigCompany();
		}
	},
	methods:{
		modalDatabase(){
            this.$root.$refs.FullTicket2.modalDatabase();
        },
		categoryTicket(){
            this.$root.$refs.FullTicket2.showCategory = true;
			this.$store.state.showAlertCategory = false;
        },
		checkCategory(){
			try {
                if(JSON.parse(this.itemselected.settings).ticket.showCategory){
                    if(this.status == 'CLOSED' || this.status == 'RESOLVED'){
                        if(this.rejectCheck == true){
                            this.sendMessage();
                            this.rejectCheck = false;
                        }else{
                            axios.get('check-category', {
                                params:{
                                    chat_id: this.itemselected.chat_id,
                                    cttype: 'TICKET'
                                }
                            }).then(res => {
                                if(res.data.result){
                                    this.sendMessage();
                                }else{
                                    this.categoryTicket();
                                    this.$store.state.showAlertCategory = true;
                                    // this.rejectCheck = true;
                                }
                            }).catch(function(e){
                                console.log(e);
                            });
                        }
                    }else{
                        this.sendMessage();
                    }
                }else{
                    this.sendMessage();
                }

            } catch (e) {
                this.sendMessage();
            }
        },
		getConfigCompany(){
            // axios.get('get-config-company')
            // .then(res => {
            //     this.status = res.data.value;
            //     this.showbuttonSave = res.data.success;
            // }).catch(function(){
			// 	this.showbuttonSave = true;
			// 	this.status = 'IN_PROGRESS';
			// });
			
			if(this.settings.ticket.selectedStatus == undefined){
                this.status = 'IN_PROGRESS';
            }else{
                this.status = this.settings.ticket.selectedStatus;
            }
            this.showbuttonSave = true;
        },
		sendMessage() {
			var vm = this;
			let uploadedFiles = vm.files;
			vm.$loading(true);
			let formData = new FormData();

			if (uploadedFiles.length > 0) {
				for (var i = 0; i < uploadedFiles.length; i++) {
					let file = uploadedFiles[i];
					formData.append("files[" + i + "]", file);
				}
				formData.append("type", "FILE");
			} else {
				formData.append("type", "TEXT");
			}

			// verificar se tem IMAGEM dentro do html do content
			let quotes = vm.text.split('"');
            let images = [];

            quotes.forEach(element => {
                if (element.substring(0, 10) == 'data:image') {
                    images.push(element);
                }
            });

			for (var i = 0; i < images.length; i++) {
				formData.append('images[]', images[i]);
			}

			// mesma altura para todas as imagens
            vm.text = vm.text.replace('><img', '><img  style="height: 150px;"');
            console.log(vm.itemselected);
			formData.append("id_ticket", vm.itemselected.id);
			formData.append("email", vm.itemselected.email_created);
			formData.append("department_id", vm.itemselected.department_id);
			formData.append("chat_id", vm.itemselected.chat_id);
			formData.append("original_status", vm.itemselected.original_status);
			formData.append("created_by", vm.itemselected.created_by);
			formData.append("text", vm.text);
			formData.append("status", vm.status);
			formData.append("comments", vm.comments);
			formData.append("user_agent", vm.itemselected.user_agent);
			formData.append("comp_user_comp_dep_current_id", vm.itemselected.comp_user_comp_depart_id_current);
			formData.append("is_client", false);
			formData.append("is_ticket", true);
			formData.append("sendEmailSelected", vm.sendEmailSelected);

			axios.post(`ticket-result-chat`, formData, {
				headers: { "Content-Type": "multipart/form-data" },
			}).then(function (response) {
				//console.log(response);
				if(response.data.success){
					vm.$loading(false);

					if(response.data.event == 'change-attendant'){
						vm.$snotify.info( vm.$t(response.data.value) +' '+vm.$t(response.data.attendant) , vm.$t('bs-info'), {
							position: "rightTop",
							timeout: 7000,
						});
					}else{
						vm.$snotify.success(vm.$t('bs-response-saved-successfully'), vm.$t('bs-success'), {
							position: "rightTop",
						});
					}

				 	// var my_object = {
					// 	id: response.data.id,
					// 	created_at: response.data.created,
					// 	status: vm.status,
					// 	department: vm.department.name,
					// };
					vm.itemselected.status = vm.status;
					vm.itemselected.comments = vm.comments;
					vm.itemselected.name = vm.user.name;
					vm.itemselected.updated_at = response.data.updated_at;
					vm.itemselected.answered = false;
					vm.itemselected.update_status_in_progress = response.data.created;
					//console.log(vm.itemselected);
					//console.log(response.data.created_by);
					localStorage.removeItem("ticket-"+vm.itemselected.id);
					vm.$root.$refs.TicketTicket2.showAnswer = false;

				}else{
					vm.$snotify.info( vm.$t(response.data.value) , vm.$t('bs-info'), {
						position: "rightTop",
					});
				}
			})
			.catch(function (erro) {
				console.log(erro);
				console.log("FAILURE!!");
			});

		},
		back(){
            this.$root.$refs.TicketAnswer2.showBody = true;
            this.$root.$refs.TicketAnswer2.showView = false;
		},
	},
};
</script>

<style scoped>
.v-title{
	text-align: left;
	font: normal normal 800 15px/31px Muli;
	letter-spacing: 0px;
}

.ba-hd__title {
    color: #0080fc;
    font-family: Muli;
    font-weight: 800;
    font-size: 1.4rem;
    letter-spacing: 0px;
    color: #0080fc;
    width: 0px;
}
</style>
