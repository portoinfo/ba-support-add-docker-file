<template>
	<div class="card h-100">
		<div class="card-header pr-4">
			<b-row>
				<b-col class="mw-50 op-10">
					<b-avatar class="op-10" variant="success"  style="color:white" :text="LI(itemselected.name)" size="3rem"></b-avatar>
				</b-col>
				<b-col>
					<div class="list-group p-0 m-0">
						<a
						class="list-group-item list-group-item-action flex-column align-items-start border-0 p-0 m-0"
						>
						<div class="d-flex w-100 justify-content-between">
							<div v-if="itemselected.agent == null">
								<h5 class="mb-1">Aguardando... - {{itemselected.status}}</h5>
							</div>
							<div v-else>
								<h5 class="mb-1">{{itemselected.name}} - {{itemselected.status}}</h5>
							</div>
						</div>
						<p class="mb-1 mt-n1">Suporte ao cliente</p>
					</a>
				</div>
			</b-col>
			<b-col class="mw-80 mt-2 buttons">
				<span class="op-10" v-if="itemselected.name != null">
					<small @click="clickLike" class="mr-2">
						<vue-material-icon name="thumb_up" :size="25"/>
					</small>
					<small @click="clickDeslike" class="mr-2">
						<vue-material-icon name="thumb_down" :size="25" />
					</small>
				</span>
			</b-col>
		</b-row>
	</div>
	<div class="card-body content-chat" v-chat-scroll>
		
		<div>
			<span class="container op-10 ml-4" style="color:blue">Ticket</span>
			<div class="container br-10 bg-chat mb-2">
				{{itemselected.description}}
			</div>
		</div>

		<div v-for="item in messages">
			<span class="container op-10 ml-4" style="color:green">{{item.name}}</span>
			<div class="container br-10 bg-chat">
				{{item.content}}
			</div>
			<div style="margin-top: -30px;">
				<b-avatar class="op-10" variant="success"  style="color:white" :text="LI(item.name)" size="2rem"></b-avatar>
			</div>
		</div>
	</div>

	<div class="card-footer footer-1">
		<div class="content-input">
			<div id="chat" class="">
				<div class="card-top translator">
						<!-- <span class="op-8 mr-4">
							<img src="images/icons/chat/format_color.svg" />
						</span>
						<span class="op-8">
							<img src="images/icons/chat/format_bold.svg" />
						</span>
						<span class="op-8">
							<img src="images/icons/chat/format_italic.svg" />
						</span>
						<span class="op-8">
							<img src="images/icons/chat/format_underlined.svg" />
						</span> -->

						<span @click="uploadFile" class="op-8 ml-4 mr-4">
							<img src="images/icons/chat/attach_file.svg" />
							Anexar					
						</span>
						<span @click="OpenAnexo" class="op-8 ml-4 mr-4">
							<img src="images/icons/chat/save_as.svg" />
							Anexos
						</span>
						<span @click="OpenAcoes" class="op-8 ml-4 mr-4">
							<img src="images/icons/chat/build.svg" />
							Ação
						</span>
						<!-- <span class="op-8 ml-4 mr-4">
							<img src="images/icons/chat/edit.svg" />
							Inserir Assinatura
						</span> -->
					</div>
				</div>
				<div class="card-body" style="background-color:#FAFAFA;height:200px;">
					<!-- <b-row class="h-100 w-100"> -->
						<b-row>
							<b-col>
								<b-form-textarea
								id="textarea"
								v-model="textTicket"
								placeholder="Enter something..."
								class="w-100 h-100 border-0"
								:state="vText"
								rows="12"
								></b-form-textarea>
							</b-col>
							<b-col cols="auto" class="col-btn-send">
								<b-button @click="createChatHistory" variant="outline-light" id="btn-send">
									<img src="images/icons/chat/send.svg" height="25px" width="25px" />
								</b-button>
							</b-col>
							<b-col v-show="showAnexo" cols="auto" class="extend">
								<center><b>Anexos</b></center>
								<span class="op-10" v-for="item in files">
									<b-link class="container mb-1" target="_blank" :href="item.route">{{item.name}}</b-link><br>
								</span>
							</b-col>
							<b-col v-show="showAcoes" cols="auto" class="extend">
								<center><b>Acões</b></center>
								<h5><b-button variant="outline-success w-100 op-8" style="color:black">Solucionar ticket</b-button></h5>
								<h5><b-button variant="outline-danger w-100 op-8" style="color:black">Cancelar ticket</b-button></h5>
							</b-col>
						</b-row>
					</div>
					<div class="ml-3">Selected file: <a @click="viewImage" href="#">{{ file1 ? file1.name : '' }}</a></div>
				</div>
			</div>
		</div>
	</template>

	<script>

	export default {
		data(){
			return {
				showAnexo: false,
				showAcoes: false,
				textTicket: '',
				formCompany: {
					textTicket: false,
				},
				file1: null,
				messages: [],
				files: [],
			}
		},
		props:{
			itemselected: Object,
		},
		mounted(){
			var vm = this;
			axios.get('client-chat-ticket/'+vm.itemselected.id).then(function(r_resposta){
				vm.messages =r_resposta.data.result;
				vm.files =r_resposta.data.value;
			}).catch(function (error) {
				console.log(error);
			});
		},
		methods: {
			createChatHistory(){
				var vm = this;

				if(this.formCompany.textTicket){

					let formData = new FormData();
					formData.append('id_department', vm.itemselected.department_id);
					formData.append('id_ticket', vm.itemselected.id);
					formData.append('chat_id', vm.itemselected.chat_id);
					formData.append('text', vm.textTicket);
					formData.append('file', vm.file1);

					axios.post('client-create-chat-history', formData,
					{
						headers: {
							'Content-Type': 'multipart/form-data'
						}
					}).then(function(response){
						if(response.data.success){
						// var my_object = {
						// 	id: response.data.id,
						// 	status: response.data.status,
						// 	description: vm.textTicket,
						// 	created_at: response.data.created_at,
						// 	department: vm.department,
						// };

						//vm.messages
						//vm.files


						var messagesAdd = {
							name: 'EU',
							content: vm.textTicket
						};
						vm.messages.push(messagesAdd);
						

						if(vm.file1 != null){
							var files = {
								route: response.data.route+'/'+vm.file1.name,
								name:  vm.file1.name
							};

							vm.files.push(files);
						}

						vm.textTicket = '';
						vm.file1 = null;

						vm.$snotify.success(vm.$t('bs-saved-successfully'), vm.$t('bs-success'));

					}else{
						vm.$snotify.error(vm.$t('bs-error-trying-to-save'), vm.$t('bs-error'));
					}
				})
					.catch(function(erro){
						console.log(erro);
						console.log('FAILURE!!');
					});

				}else{
					vm.$snotify.info( vm.$t('bs-please-enter-the-information'), vm.$t('bs-invalid-input'));
				}
			},
			uploadFile(){
				var vm = this;
				Swal.fire({
					title: 'Select image',
					input: 'file',
					inputAttributes: {
						'accept': 'image/*',
						'aria-label': 'Upload your profile picture'
					},
					preConfirm: (file) => {
						//console.log(file)
						if(file == null){
							alert('nenhum arquivo selecionado!');
							return;
						}else{
							vm.file1 = file;
						}
					},
				})
			},
			viewImage(){
				var vm = this;
				if (vm.file1) {
					const reader = new FileReader()
					reader.onload = (e) => {
						Swal.fire({
							title: 'Your uploaded picture',
							imageUrl: e.target.result,
							imageAlt: 'The uploaded picture'
						})
					}
					reader.readAsDataURL(vm.file1);
				}
			},
			LI(value){
				if(value == null){
					return 'LI';
				}
				return value.substr(0, 2);
			},
			OpenAnexo(){
				this.showAnexo = !this.showAnexo;
			},
			OpenAcoes(){
				this.showAcoes = !this.showAcoes;
			},
		},
		computed: {
			vText() {
				this.formCompany.textTicket = this.textTicket.length > 1 && this.textTicket.length < 65500;
				return this.formCompany.textTicket;
			},
		}
	};
	</script>

	<style scoped>

	h1 {
		text-align: left;
		font: normal normal 800 25px/19px Muli;
		letter-spacing: 0px;
		color: #0080fc;
		opacity: 1;
	}

	h2 {
		font: normal normal 800 15px/31px Muli;
		letter-spacing: 0px;
		color: #333333;
		opacity: 1;
	}

	h5 {
		font: normal normal bold 16px/22px Muli;
		letter-spacing: 0px;
		color: #434343;
	}

	span {
		text-align: left;
		font: normal normal 800 15px/16px Muli;
		letter-spacing: 0.45px;
		color: #434343;
		opacity: 0.5;
	}

	thead tr {
		background: #f7f8fc 0% 0% no-repeat padding-box;
		border-radius: 5px 5px 0px 0px;
		opacity: 1;
	}

	thead tr th {
		border: none;
	}

	thead tr th:first-child {
		border-radius: 5px 0px 0px 0px;
	}

	thead tr th:last-child {
		border-radius: 0px 5px 0px 0px;
	}

	tbody td {
		height: 63px;
		vertical-align: middle;
		border: 1px solid #dee3ea;
	}

	tbody td:first-child {
		border-left: none !important;
	}

	tbody td:last-child {
		border-right: none !important;
	}

	.table-striped tbody tr:nth-of-type(odd) {
		background-color: white;
	}

	tbody tr {
		background-color: #fdfdfd;
	}

	tbody tr:hover {
		background-color: #f7f8fc !important;
		cursor: pointer;
	}

	.extend{
		margin-top: -25px;
		padding: 4px;
	}

	.in-queue {
		color: #ffb244;
	}

	.in-progress {
		color: #00c38e;
	}

	.answered {
		background: #ff4872;
		color: #f4f4f4;
	}

	tbody tr {
		font: normal normal 600 16px/19px Lato;
		letter-spacing: 0px;
		color: #6e6e6e;
	}

	.content {
		margin-right: 40px;
		margin-left: 40px;
	}

	.op-8{
		opacity: 0.8;
	}

	.op-10{
		opacity: 0.8;
	}

	.br-10{
		border-radius: 10px;
	}

	.bg-chat{
		background-color: #F2F2F2;
		padding: 18px;
	}

	.margin-0 {
		margin: -15px;
	}

	/* SCROLL */
	.scroll{
		overflow: auto;
	}

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

	#search {
		max-width: 250px;
		float: right;
		background: #ffffff 0% 0% no-repeat padding-box;
		box-shadow: 0px 0px 2px #26242440;
		border-radius: 5px;
		opacity: 1;
	}

	#search input {
		border: none !important;
	}

	#search input::placeholder {
		font: normal normal medium 14px/17px Lato;
		letter-spacing: 0px;
		color: #434343;
		opacity: 1;
	}

	.input-group-prepend {
		color: #434343;
	}

	textarea:focus,
	input[type="text"]:focus,
	input[type="password"]:focus,
	input[type="datetime"]:focus,
	input[type="datetime-local"]:focus,
	input[type="date"]:focus,
	input[type="month"]:focus,
	input[type="time"]:focus,
	input[type="week"]:focus,
	input[type="number"]:focus,
	input[type="email"]:focus,
	input[type="url"]:focus,
	input[type="search"]:focus,
	input[type="tel"]:focus,
	input[type="color"]:focus,
	.uneditable-input:focus {
		border-color: none;
		box-shadow: none;
		outline: 0 none;
	}

	.card {
		border: none;
		border-radius: 0px;
		padding-bottom: 50px;
	}

	.card-top{
		padding: 5px;
		background-color: #F2F2F2;
	}

	.card-header {
		height: 60px;
		background: #ffffff 0% 0% no-repeat padding-box;
		box-shadow: 0px 0px 9px #26242424;
		opacity: 1;
		border: none;
	}

	.card-header .buttons small {
		cursor: pointer;
	}

	.card-header .buttons small:hover {
		-webkit-text-fill-color: #0294ff;
		-webkit-text-stroke: 1px #0294ff;
	}

	.content-chat {
		overflow: auto;
		background-color: #e6e7e7;
	}

	.float {
		position: fixed;
		bottom: 40px;
		right: 40px;
		color: white;
		border-radius: 100%;
		text-align: center;
		box-shadow: 2px 2px 3px #999;
		height: 60px;
		width: 60px;
		background-color: #0294ff;
		cursor: pointer;
	}

	.mw-50 {
		padding: 0;
		max-width: 50px;
	}

	.mw-80 {
		padding: 0px;
		max-width: 80px !important;
		-webkit-text-stroke: 1px #d7dee6;
		-webkit-text-fill-color: white;
	}

	.list-group-item p {
		font: normal normal normal 13px/16px Muli;
		letter-spacing: 0px;
		color: #434343;
	}

	.footer-2 {
		font: normal normal 600 16px/20px Muli;
		letter-spacing: 0px;
		color: #6c7f98;
		vertical-align: middle;
	}

	.footer-2 small {
		cursor: pointer;
	}
	.footer-1 {
		background-color: #e6e7e7;
		border: none;
		padding: 0px;
	}

	.content-input {
		background: white;
		margin-left: 50px;
		margin-right: 50px;
		margin-bottom: 15px;
		border-radius: 3px;
		border: 1px solid #dddddd;
	}

	.row-textarea {
		padding-right: 30px;
		background-color: transparent;
	}

	textarea {
		font: normal normal normal 16px/20px Muli;
		letter-spacing: 0px;
		color: #707070;
		max-height: 159px;
		width: 100%;
		border: none;
		resize: none;
		background: transparent;
	}

	.col-input-btn {
		min-width: 15px;
		max-width: 15px;
		background: transparent;
		margin-bottom: 5px;
		display: flex;
		align-items: flex-end;
		padding-left: 5px;
	}

	.col-input-btn a {
		float: left !important;
		left: 0;
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

		.content-input {
			margin-left: 0px;
			margin-right: 0px;
			margin-bottom: 0px;
			border: none;
			border-radius: 0px;
		}

		.content-chat {
			zoom: 85%;
		}
	}
	</style>