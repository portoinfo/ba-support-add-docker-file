<template>
	<div>
		<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<div class="modal-content">
					<div class="modal-header border-0 p-0">
						<h5 class="modal-title" id="exampleModalLabel">{{$t('bs-choose-the')}} {{$t('bs-department')}}</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="viewCancel">
							X
						</button>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label for="exampleFormControlSelect1">{{$t('bs-department')}}</label>
									<multiselect
									v-model="departmentselected"
									deselect-label=""
									selectLabel=""
									track-by="name"
									label="name"
									:placeholder="phSelectDepart"
									:options="departmentOptions"
									:searchable="false"
									:allow-empty="false"
									id="departments">
								</multiselect>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer border-0">
					<button type="button" @click="viewCancel" class="text-capitalize btn" data-dismiss="modal">
						{{$t('bs-cancel')}}
					</button>
					<button type="button" @click="nextstep(1)" :data-dismiss="checkDepartment" id="btn-department" class="btn btn-primary">
						{{$t('bs-next')}}
					</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="selectAgentModal" tabindex="-1" aria-labelledby="selectAgentModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-dialog-centered modal-lg">
			<div class="modal-content">
				<div class="modal-header border-0 p-0">
					<h5 class="modal-title" id="selectAgentModalLabel">{{$t('bs-choose-the')}} {{$t('bs-attendants')}}</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="viewCancel">
						X
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-12">
							<div class="form-group">
								<label for="exampleFormControlSelect1">{{$t('bs-attendants')}}</label>
								<multiselect
								v-model="agentselected"
								deselect-label=""
								selectLabel=""
								track-by="name"
								label="name"
								:placeholder="phSelectDepart"
								:options="agentsOptions"
								:searchable="false"
								:allow-empty="false"
								id="departments"></multiselect>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer border-0">
					<button type="button" @click="nextstep(0)" class="text-capitalize btn" data-dismiss="modal">
						{{$t('bs-back')}}
					</button>
					<button type="button" @click="nextstep(2)" :data-dismiss="checkDepartment" id="btn-department" class="btn btn-primary">
						{{$t('bs-next')}}
					</button>
				</div>
			</div>
		</div>
	</div>

		<div class="modal fade" id="selectClientModal" tabindex="-1" aria-labelledby="selectAgentModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-dialog-centered modal-lg">
			<div class="modal-content">
				<div class="modal-header border-0 p-0">
					<h5 class="modal-title" id="selectAgentModalLabel">{{$t('bs-choose-the')}} {{$t('bs-client')}}</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="viewCancel">
						X
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-12">
							<div class="form-group">
								<label for="exampleFormControlSelect1">{{$t('bs-enter-email')}} {{$t('bs-of')}} {{$t('bs-client')}}</label>
								 <b-form-input
						          id="input-1"
						          v-model="clientdescription"
						          type="email"
						          placeholder="Enter email"
						          class="bs-input"
						          required
						        ></b-form-input>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer border-0">
					<button type="button" @click="nextstep(1)" class="text-capitalize btn" data-dismiss="modal">
						{{$t('bs-back')}}
					</button>
					<button type="button" @click="nextstep(3)" :data-dismiss="checkDepartment" id="btn-department" class="btn btn-primary">
						{{$t('bs-next')}}
					</button>
				</div>
			</div>
		</div>
	</div>



	<div class="modal fade" style="overflow: scroll;" id="createTicketModal" tabindex="-1" aria-labelledby="createTicketModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg">
			<div class="modal-content">
				<div class="modal-header border-0 p-0">
					<h5 class="modal-title" id="createTicketModalLabel">{{$t('bs-describe-the-ticket-under-the')}}</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="viewCancel">
						X
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-12">
							<div class="form-group">
								<label for="exampleFormControlSelect1">{{$t('bs-attendants')}}</label>
								<b-form-input
						          id="input-22"
						          v-model="agentselected.name"
						          placeholder="Enter name"
						          required
						          disabled
						        ></b-form-input>
					   		</div>

							<div class="form-group">
								<label for="exampleFormControlSelect1">{{$t('bs-client')}}</label>
								<span v-if="clientselected == null">
									<b-form-input
							          id="input-2"
							          :value="notBoundClient"
							          placeholder="Enter name"
							          required
							          disabled
							        ></b-form-input>
								</span>
								<span v-else>
									<b-form-input
							          	id="input-2"
						          		v-model="clientdescription"
							          	placeholder="Enter name"
							          	required
							          	disabled
							        ></b-form-input>
								</span>
					   		</div>

							<div class="form-group">
								<label for="exampleFormControlSelect1">{{$t('bs-status-ticket')}}</label>
								<multiselect
								v-model="statusselected"
								deselect-label=""
								selectLabel=""
								track-by="name"
								label="name"
								:placeholder="phSelectDepart"
								:options="statusOptions"
								:searchable="false"
								:allow-empty="false"
								id="departments"></multiselect>
							</div>
							<div class="form-group">
								<label for="exampleFormControlSelect1">{{$t('bs-description')}} {{$t('bs-of')}} Ticket</label>
								<multiselect
								v-if="files != ''"
								:hide-selected="true"
								:placeholder="phFile"
								v-model="files"
								:options="files"
								:openDirection="'bottom'"
								:multiple="true"
								:close-on-select="false"
								label="name"
								track-by="name"
								>
							</multiselect>
							<div class="content-textarea">
								<!-- <b-form-textarea
								id="textarea"
								v-model="descriptionTicket"
								placeholder="Enter something..."
								autofocus
								rows="3"
								max-rows="6"></b-form-textarea> -->
								<quill-editor
									ref="TicketDescriptionQuillEditor"
									v-model="descriptionTicket"
									:options="quillEditorOptions"
									id="q-editor-description"
									class="bg-white border"
								/>
							</div>
							<!-- <div class="form-group mt-2">
								<label for="exampleFormControlSelect1">{{$t('bs-comments')}}</label>
								<b-form-textarea
								id="textarea"
								v-model="comment"
								autofocus
								rows="3"
								max-rows="6"></b-form-textarea>
							</div> -->
						</div>
					</div>
					<span @click="upload" class="ml-4 mr-4 caret">
						<i class="bbi bbi-save-as bbi-16 mx-1"></i>
						<span class="text2">{{$t('bs-attachment')}}</span>
					</span>
					<input
					type="file"
					id="attachments"
					ref="attachments"
					multiple
					v-on:change="handleFilesUpload()"
					style="display: none"
					/>
				</div>
			</div>
			<div class="modal-footer border-0">
				<button type="button" @click="nextstep(2)" class="text-capitalize btn" data-dismiss="modal">
					{{$t('bs-back')}}
				</button>
				<button type="button" @click="nextstep(4)" :data-dismiss="checkDepartment" id="btn-department" class="btn btn-primary">
					{{$t('bs-save')}}
				</button>
			</div>
		</div>
	</div>
</div>


</div>
</template>

<script>
import Quill from 'quill';
import { quillEditor } from 'vue-quill-editor';
import 'quill/dist/quill.core.css';
import 'quill/dist/quill.snow.css';
import 'quill/dist/quill.bubble.css';
import "quill-emoji/dist/quill-emoji.css";
import * as Emoji from "quill-emoji";
import { container, ImageExtend, QuillWatch } from 'quill-image-extend-module';
import BlotFormatter from 'quill-blot-formatter';
import ImageCompress from 'quill-image-compress';
import AutoLinks from 'quill-auto-links';
import Delta, { AttributeMap } from 'quill-delta';

export default {
	components: {
		quillEditor,
	},
	data(){
		return {
			comment: '',
			notBoundClient: this.$t('bs-not-bound'),
			departmentselected: null,
			agentselected: { name: this.$t('bs-without-attendant')+' - '+this.$t('bs-open'), value: 'OPENED' },
			statusselected: null,
			clientdescription: '',
			clientselected: '',
			statusOptions:  [],
			departmentOptions: [],
			agentsOptions: [],
			checkDepartment: '',
			phSelectDepart: this.$t('bs-select-a-department'),
			descriptionTicket: "",
			//uploads
			attachments: [],
			files: [],
			errors: [],
			extImages: ["jpg", "jpeg", "png", "bmp"],
			extDocuments: ["docx", "doc", "pdf", "xps", "txt", "odt", "svg"],
			extSpreadsheets: ["xlsx", "xls", "xlt", "csv", "ods"],
			extPresentation: ["pptx", "ppt", "pot", "ppsx", "pps", "odp"],
			extensions: [],
			file_exists: false,
			uploadComponent: [],
			phFile: this.$t('bs-enter-your-text')+'...',
			quillEditorOptions: {
				placeholder: this.$t('bs-type-here')+'...',
				modules: {
					toolbar: [
						[
						'bold', 
						'italic', 
						'underline', 
						'strike', 
						'code-block', 
						'link', 
						'image', 
						{'list': 'bullet'},
						{'size': ['small', false, 'large', 'huge']},
						{'color': [] }, 
						{'background': []}, 
						{'align': []},
						'emoji',
						'clean'
						]                                         
					],
					"emoji-toolbar": true,
					"emoji-textarea": false,
					"emoji-shortname": true,
					autoLinks: {
						paste: true,
						type: true
					},
					ImageExtend: {
						loading: true,
						name: 'img',
					},
					imageCompress: {
						quality: 0.9,
						maxWidth: 2000,
						maxHeight: 2000,
						imageType: 'image/jpeg',
						debug: false,
						suppressErrorLogging: false,
					},
				}
			},
		}
	},
	created () {
		Quill.register('modules/blotFormatter', BlotFormatter);
		Quill.register('modules/ImageExtend', ImageExtend)
		Quill.register('modules/imageCompress', ImageCompress);
		Quill.register('modules/autoLinks', AutoLinks);
	},
	mounted(){
		this.setExtensions();
		var vm = this;
		$("#exampleModal").modal("show");
		axios.get("tickets/get-department", {
		}).then((response) => {
			this.data = response.data.result;
			this.data.forEach((item) => {
				//se settings for null, nao aparece o dep no select... eh pq o dep foi criado recentemente e ainda nao foi configurado..
				if (item.settings !== null) {
					this.departmentOptions.push({name: vm.$t(item.name), id: item.id, settings: item.settings});
					// popula a departmentOptions com o departamento e status
	            }
	        });
		});
	},
	props:{
		user: Object,
		cs: Object,
	},
	methods:{
		nextstep(value){
			var vm = this;
			if(value == 0){
				$("#selectAgentModal").modal("hide");
				$("#exampleModal").modal("show");
			}
			if(value == 1){
				if(vm.departmentselected == null){
					this.$snotify.info(this.$t('bs-select-a-department'), this.$t('bs-info'));
					return;
				}
				vm.getAgentDepartment();
				$("#exampleModal").modal("hide");
				$("#selectClientModal").modal("hide");
				$("#selectAgentModal").modal("show");
			}
			if(value == 2){
				if(vm.agentselected.name == ''){
					this.$snotify.info(this.$t('bs-select-a-attendants'), this.$t('bs-info'));
					return;
				}
				$("#selectAgentModal").modal("hide");
				$("#selectClientModal").modal("show");
			}
			if(value == 3){

				axios.post('check-client-exists', {
					email: vm.clientdescription
				}).then(function(response){
					vm.clientselected = response.data.value;
					if(vm.clientselected == null){
						vm.$snotify.info(vm.$t('bs-ticket-is-not-linked-to-any-customer'), vm.$t('bs-info'));
					}else{
						$("#selectAgentModal").modal("hide");
						$("#selectClientModal").modal("hide");
						$("#createTicketModal").modal("show");
					}
				})
				.catch(function(erro){
					console.log(erro);
					console.log('FAILURE!!');
				});

				vm.statusselected = null;
				if(vm.agentselected.value == 'OPENED'){
					vm.statusOptions =	[
						{ name: this.$t('bs-without-attendant')+' - '+this.$t('bs-open'), value: 'OPENED' },
					];
					vm.statusselected = { name: this.$t('bs-without-attendant')+' - '+this.$t('bs-open'), value: 'OPENED' };
				}else{
					vm.statusOptions = [
						{ name: this.$t('bs-in-progress'), value: 'IN_PROGRESS' },
						// { name: this.$t('bs-closed'), value: 'CLOSED' },
						// { name: this.$t('bs-resolved'), value: 'RESOLVED' },
						// { name: this.$t('bs-canceled'), value: 'CANCELED' }
					];
					vm.statusselected = { name: this.$t('bs-in-progress'), value: 'IN_PROGRESS' };
				}
				//return vm.$snotify.success(vm.$t('bs-response-saved-successfully'), vm.$t('bs-success'));
			}
			if(value == 4){
				if(vm.descriptionTicket.trim() == ""){
					vm.$snotify.info(this.$t('bs-empty-fields'), vm.$t('bs-info'));
					return;
				}
				$("#createTicketModal").modal("hide");
				vm.$loading(true);
				//SAVE
				// verificar se tem IMAGEM dentro do html do content
				var quotes = vm.descriptionTicket.split('"');
				var images = [];

				quotes.forEach(element => {
				if (element.substring(0, 10) == 'data:image') {
					images.push(element);
				}
				});

				// mesma altura para todas as imagens
				vm.descriptionTicket = vm.descriptionTicket.replace('><img', '><img  style="height: 150px;"');

				axios.post('create-ticket', {
					status: vm.statusselected.value,
					department: vm.departmentselected.id,
					company_user_id_selected: vm.agentselected.company_user_id,
					priority: 'NORMAL',
					textTicket: vm.descriptionTicket,
					clientselected: vm.clientselected,
					comment: vm.comment,
					images: images.length ? images : null
				}).then(function(response){
					if(response.data.success){
						vm.$loading(false);
				        if (vm.files.length > 0) {
				            let formData = new FormData();

				            for (var i = 0; i < vm.files.length; i++) {
				                let file = vm.files[i];
				                formData.append("files[" + i + "]", file);
				            }

				            //console.log(response.data.company_department_id);
				            formData.append("chat_id", response.data.chat_id);
				            formData.append("company_department_id", response.data.company_department_id);
				            formData.append("extImages", vm.extImages);

				            axios
				            .post(`chat/agent/upload`, formData, {
				                headers: {
				                    "Content-Type": "multipart/form-data",
				                },
				            })
				            .then(function () {
				            	axios.post(`ticket-update-date`, {
						          id: response.data.id
						        });
				                console.log("SUCCESS!!");
				            })
				            .catch(function () {
				                console.log("FAILURE!!");
				            });
				        }

						vm.$snotify.success(vm.$t('bs-ticket-created-successfully'), vm.$t('bs-success'));
			            vm.$emit("back");
			            vm.$loading(false);
				        vm.files = [];
				        vm.uploadComponent = [];

					}
				})
				.catch(function(erro){
					console.log(erro);
					console.log('FAILURE!!');
				});
			}
		},
		getAgentDepartment(){
			this.agentsOptions = [];
			//console.log(this.departmentselected);
			axios.get("tickets/get-agents", {
				params: {
					department_id : this.departmentselected.id
				}
			}).then((response) => {
				this.data = response.data.result;
				this.agentsOptions.push({name: this.$t('bs-without-attendant')+' - '+this.$t('bs-open'), value: 'OPENED'});
				this.data.forEach((item) => {
		                //se settings for null, nao aparece o dep no select... eh pq o dep foi criado recentemente e ainda nao foi configurado..
		                if (item.settings !== null) {
		                	this.agentsOptions.push({name: item.name, company_user_id: item.company_user_id});
		                // popula a departmentOptions com o departamento e status
		            }
		        });
			});

		},
		viewCancel(){
			var vm = this;
			vm.$emit('back');
		},
		setExtensions() {
			this.extensions = this.extImages.concat(
				this.extDocuments,
				this.extSpreadsheets,
				this.extPresentation
				);
		},
		upload() {
			$("#attachments").click();
		},
		handleFilesUpload() {
      		// o atributo 'attachments' recebe os arquivos enviados pelo onchange do input de uploads
      		this.attachments = this.$refs.attachments.files;
      		// faço um laço para verificar cada arquivo valido e adiciona-lo ao array que será enviado para API
      		Array.from(this.attachments).forEach((attachment) => {
        	// reverto a string e pego os primeiros caracteres antes do primeiro '.' na string
        	let reverse_ext = attachment["name"]
        	.split("")
        	.reverse()
        	.join("")
        	.split(".", 1)
        	.toString();
       		// pego a string gerada e reverto ela novamente, assim gerando a extensão do arquivo. Ex: jpg, png etc..
       		let ext = reverse_ext.split("").reverse().join("");
        	// verifico se a extensao do arquivo estiver incluso nas extensões permitidas
        	if (
        		this.extensions.includes(ext) ||
        		this.extensions.includes(ext.toLowerCase())
        		) {
          		// caso o array de arquivos validos for diferente de vazio..
          	if (this.files.length) {
            	// é feito um laço para verificar se o arquivo que está sendo enviado já está no array de arquivos válidos
            	this.files.forEach((file) => {
              	// caso esteja, o atributo 'file_exists' é setado como true
              	file["name"] === attachment["name"]
              	? (this.file_exists = true)
              	: "";
              });
            }
        		// verifico se o atributo file_exists é falso, isso indica que o arquivo não existe no array de arquivos válidos
        		if (
        			this.file_exists == false &&
        			this.files.length < 5 &&
        			attachment.size <= 5242880
        			) {
                	// como ele não existe, ele é adicionado
                this.files.push(attachment);
                console.log(
                	"Arquivo: " +
                	attachment["name"] +
                	" < que 5 MB (5242880 B). Pode ser enviado." +
                	" Tamanho: " +
                	attachment.size +
                	" B."
                	);
		            // this.$snotify.info(this.$t('bs-archive')
		            //     + " " + attachment["name"]
		            //     +': ' + this.$t('bs-smaller')
		            //     +' ' + this.$t('bs-than')
		            //     + ' 5 MB (5242880 B). '
		            //     + this.$t('bs-can-be-sent')+".", this.$t('bs-info'));
		        } else {
		        	if (attachment.size > 5242880) {
		        		console.log(
		        			"Arquivo: " +
		        			attachment["name"] +
		        			" > que 5 MB (5242880 B). Não pode ser enviado." +
		        			" Tamanho: " +
		        			attachment.size +
		        			" B."
		        			);
		        		this.$snotify.info(this.$t('bs-archive')
		        			+ " " + attachment["name"]
		        			+': ' + this.$t('bs-bigger')
		        			+' ' + this.$t('bs-than')
		        			+ ' 5 MB (5242880 B). '
		        			+ this.$t('bs-cannot-be-sent')+".", this.$t('bs-info'));
		        	}
		        	if (this.files.length > 5) {
		        		console.log(
		        			"Arquivo: " +
		        			attachment["name"] +
		        			" > Não pode ser enviado, pois o limite de 5 uploads já foi atingido." +
		        			" Tamanho: " +
		        			attachment.size +
		        			" B."
		        			);
		        		this.$snotify.info(this.$t('bs-archive')
		        			+ " " + attachment["name"]
		        			+': ' + this.$t('bs-bigger')
		        			+ ' ' + this.$t('bs-cannot-be-sent')+", "
		        			+ this.$t('bs-as-the-limit-of-5-uploads-has-already-been')+"." , this.$t('bs-info'));
		        	}
            			// o atributo 'file_exists' é setado como false para poder ser usado na verificação do arquivo que está na próxima posição do laço
            			this.file_exists = false;
            		}
            	}else{
          			// caso a extensão do arquivo não seja valida, adiciono o nome desse arquivo ao atributo 'errors' que armazena os arquivos que não puderam ser adicionados
          			this.errors.push(attachment["name"]);
          		}
        			// caso algum arquivo tenha sido enviado com a extensão inválido, é disparado um alert para informar o ocorrido ao usuário.
        			if (this.errors.length) {
        				Swal.fire({
        					heightAuto: false,
        					icon: "error",
        					title: this.$t('bs-oops'),
        					text:
        					this.$t('bs-invalid-file-s-format')+": '" +
        					this.errors.join(", ") +
        					this.$t('bs-the-allowed-formats-are')+": " +
        					this.extensions.join(", ") +
        					".",
        				});
        			}
        		});
this.errors = [];
},
}
};
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style scoped>

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
