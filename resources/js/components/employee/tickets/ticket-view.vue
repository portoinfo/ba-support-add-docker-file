<template>
	<div>
		<b-row class="ml-custom">
			<b-container fluid>
				<h1>{{$t('bs-view')}} - {{$t('bs-ticket')}} #{{ itemselected.id }}</h1>
			</b-container>
		</b-row>
		<b-col class="ml-n3 mt-3">
			<div class="cards">
				<b-form inline class="mb-3">
					<b-col cols="md-1">
						<label class="f-l" for="inline-form-input-name">{{$t('bs-receiver')}}: </label>
					</b-col>
					<b-col cols="md-11">
						<b-form-input
						class="bs-input"
						v-model="itemselected.name_created"
						disabled
						style="width:100%;"
						placeholder="Jane Doe"
						></b-form-input>
					</b-col>
				</b-form>
				<b-form inline class="mb-3">
					<b-col cols="md-1">
						<label class="f-l" for="inline-form-input-name">CC: </label>
					</b-col>
					<b-col cols="md-11">
						<b-form-input
						class="bs-input"
						v-model="itemselected.email_created"
						disabled
						style="width:100%;"
						placeholder="Jane Doe"
						></b-form-input>
					</b-col>
				</b-form>
				<b-form inline class="mb-3">
					<b-col cols="md-1">
						<label class="f-l" for="inline-form-input-name">{{$t('bs-attendants')}}: </label>
					</b-col>
					<b-col cols="md-11">
						<b-form-input
						class="bs-input"
						v-model="subject"
						disabled
						style="width:100%;"
						placeholder="Jane Doe"
						></b-form-input>
					</b-col>
				</b-form>
				<br>
				<b-form class="mb-3">
					<b-form-textarea
					id="textarea"
					v-model="text"
					class="mt-3 personTextArea"
					:placeholder="phMsg"
					disabled
					rows="10"
					></b-form-textarea>
				</b-form>

				<span class="v-title">{{$t('bs-change-ticket-status')}}</span>
				<b-form-group class="mb-2 mt-3"
				id="input-group-1"
				label-for="input-1">
					<b-form-select
			          id="input-3"
			          v-model="status"
			          :options="statusOptions"
			          required
			        ></b-form-select>
				</b-form-group>
				<br>
				<span class="v-title">{{$t('bs-internal-comment')}}</span>
				<b-form class="mb-2 mt-3">
					<b-form-textarea
					id="comments"
					v-model="comments"
					:placeholder="phMsg"
					rows="10"
					></b-form-textarea>
				</b-form>
			</div>
			<b-button type="submit" @click="back" variant="primary fr mt-1 mb-5 ml-2">{{$t('bs-back')}}</b-button>
			<b-button type="submit" @click="sendMessage" variant="success fr mt-1 mb-5"><i class="fa fa-floppy-o" aria-hidden="true"></i> {{$t('bs-save')}}</b-button>
			<br><br><br><br><br>
		</b-col>
	</div>
</template>

<script>

export default {
	data(){
		return {
			phMsg: this.$t('bs-type-your-message-here'),
			text: "",
			status: 'IN_PROGRESS',
			statusOptions: [{ text: this.$t('bs-in-progress'), value: 'IN_PROGRESS' }],
			subject: this.user.email,
			comments: this.itemselected.comments,
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
	},
	mounted(){
		var vm = this;

     	if(vm.openPhrase.trim() !== "") {
			vm.text += vm.$t(vm.openPhrase) + '\n\n';
		}

		if(vm.textTicket != ''){
			vm.text += vm.textTicket + '\n\n';
		}

     	if(vm.closePhrase.trim() !== "") {
			vm.text += vm.$t(vm.closePhrase) + '\n';
		}

     	if(vm.textAssin.trim() !== "") {
			vm.text += vm.textAssin + '';
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

		//saveDirect
	},
	methods:{
		save(){
			var vm = this;
			var url = 'ticket-result-chat';
			//vm.$loading(true);
			axios.post(url, {
				id_ticket: vm.itemselected.id,
				in_progress_created: vm.itemselected.update_status_in_progress,
				text: vm.text,
				openPhrase: vm.openPhrase,
				textTicket: vm.textTicket,
				closePhrase: vm.closePhrase,
				textAssin: vm.textAssin,
				status: vm.status,
				email: vm.Emailreceiver,
				comments: vm.comments,
				priority: vm.itemselected.priority,
				original_status: vm.itemselected.status,
				user_agent: vm.itemselected.user_agent,
			}).then(function(response){
				//console.log(response.data);
				//console.log(response.data.success);
				if(response.data.success){
					vm.$loading(false);
				 	vm.$snotify.success(vm.$t('bs-response-saved-successfully'), vm.$t('bs-success'));

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

					vm.$emit('ticket_ticket');

				}else{
					vm.$snotify.info( vm.$t(response.data.value) , vm.$t('bs-info'));
				}

			})
			.catch(function(){
				vm.$snotify.error( vm.$t('bs-error-trying-to-save') , vm.$t('bs-error'));
				console.log('FAILURE!!');
			});


			if (vm.files.length > 0) {
	            let formData = new FormData();

	            for (var i = 0; i < vm.files.length; i++) {
	                let file = vm.files[i];
	                formData.append("files[" + i + "]", file);
	            }

	            formData.append("chat_id", vm.itemselected.chat_id);
	            formData.append("company_department_id", vm.itemselected.department_id);
	            formData.append("extImages", vm.extImages);

	            axios
	            .post(`chat/agent/upload`, formData, {
	                headers: {
	                    "Content-Type": "multipart/form-data",
	                },
	            })
	            .then(function () {
	                console.log("SUCCESS!!");
	            })
	            .catch(function () {
	                console.log("FAILURE!!");
	            });
	        }
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

			formData.append("id_ticket", vm.itemselected.id);
			formData.append("department_id", vm.itemselected.department_id);
			formData.append("update_status_in_progress", vm.itemselected.update_status_in_progress);
			formData.append("chat_id", vm.itemselected.chat_id);
			formData.append("original_status", vm.itemselected.original_status);
			formData.append("created_by", vm.itemselected.created_by);
			formData.append("text", vm.text);
			formData.append("status", vm.status);
			formData.append("comments", vm.comments);
			formData.append("user_agent", vm.itemselected.user_agent);
			formData.append("is_client", false);
			formData.append("is_ticket", true);

			// formData.append("content", vm.chat.content);
			// formData.append("id_department", vm.ticket_info.id_department);
			// formData.append("company_user_id", vm.ticket_info.company_user_id);
			// formData.append("priority", vm.ticket_info.priority);
			// formData.append("typeSendMessage", vm.typeSendMessage);
			// formData.append("questionsConfirm", vm.questionsConfirm);
			// formData.append("extImages", vm.extImages);


			axios.post(`ticket-result-chat`, formData, {
				headers: { "Content-Type": "multipart/form-data" },
			}).then(function (response) {
				//console.log(response);
				if(response.data.success){
					vm.$loading(false);
				 	vm.$snotify.success(vm.$t('bs-response-saved-successfully'), vm.$t('bs-success'));

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
					vm.$emit('ticket_ticket');

				}else{
					vm.$snotify.info( vm.$t(response.data.value) , vm.$t('bs-info'));
				}
			})
			.catch(function (erro) {
				console.log(erro);
				console.log("FAILURE!!");
			});

		},
		back(){
			this.$emit('back');
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

.personTextArea{
	text-align: left;
	font: normal normal bold 19px/28px Muli;
	letter-spacing: 0px;
	color: #707070;
	opacity: 1;
}

.f-l{
	float: left;
}

h1 {
	font: normal normal 800 25px/19px Muli;
	letter-spacing: 0px;
	color: #0080fc;
	opacity: 1;
}

.cards{
	background: #FFFFFF 0% 0% no-repeat padding-box;
	box-shadow: 0px 0px 9px #26242424;
	border-radius: 5px;
	padding: 27px;
}
</style>
