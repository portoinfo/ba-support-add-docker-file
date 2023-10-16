<template>
	<div>
		<div
		class="modal fade"
		id="sendMessageModal"
		tabindex="-1"
		aria-labelledby="sendTicket"
		aria-hidden="true"
		data-backdrop="static"
		data-keyboard="false"
		>
		<div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg">
			<div class="modal-content">
				<div class="modal-header border-0 p-0">
					<h5 class="modal-title" id="sendTicket">
						{{$t("bs-describe-the-ticket-issue")}}
					</h5>
					<button
					type="button"
					class="close"
					data-dismiss="modal"
					aria-label="Close"
					@click="closed">
					X</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-12">
						<div class="form-group">
							<form
							@submit.prevent="sendMessageAnwer"
							id="descriptionform"
							>
							<div class="row">
								<div class="col-md-12">
									<b-row>
										<b-col class="mt-3">
											<!-- <span class="mt-5 mr-2">
												{{
													$t("bs-please-leave-calculation-informing-your")
												}}
											</span> -->
											<input
								                type="file"
								                id="attachments"
								                ref="attachments"
								                multiple
								                v-on:change="handleFilesUpload()"
								                style="display: none"
							              	/>
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
											<quill-editor
												v-model="descriptionTicket"
												:options="quillEditorOptions"
												class="bg-white border"
												style="min-height: 300px;"
											/>
										</b-col>
									</b-row>
								</div>
							</div>
							<div class="clearfix"></div>
						</form>
					</div>
				</div>
				<small @click="upload" class="ml-3 care">
		 			<img class="mb-1" src="images/icons/upload.svg"/> {{$t('bs-upload-file')}}
		 		</small>
			</div>
		</div>
		<div class="modal-footer border-0">
			<button
			type="submit"
			id="btn-ticket"
			class="btn btn-primary"
			form="descriptionform"
			>{{$t('bs-send')}}</button>
		</div>
</div>
</div>
</div>
</div>
</template>

<script>
import Quill from 'quill';
import { quillEditor } from 'vue-quill-editor'
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'
import "quill-emoji/dist/quill-emoji.css";
import * as Emoji from "quill-emoji";
import { container, ImageExtend, QuillWatch } from 'quill-image-extend-module'
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
			descriptionTicket: "",
      		phFile: this.$t("bs-enter-your-text") + "...",
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
		$("#sendMessageModal").modal("show");
		this.setExtensions();
	},
	methods:{
		sendMessageAnwer(){
			var vm = this;
            vm.$emit('descriptionTicketSend', vm.files, vm.descriptionTicket);
			$("#sendMessageModal").modal("hide");
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
		closed(){
			var vm = this;
            vm.$emit('setfalse');
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
	              this.$snotify.info(
	                this.$t("bs-archive") +
	                  " " +
	                  attachment["name"] +
	                  ": " +
	                  this.$t("bs-bigger") +
	                  " " +
	                  this.$t("bs-than") +
	                  " 5 MB (5242880 B). " +
	                  this.$t("bs-cannot-be-sent") +
	                  ".",
	                this.$t("bs-info")
	              );
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
	              this.$snotify.info(
	                this.$t("bs-archive") +
	                  " " +
	                  attachment["name"] +
	                  ": " +
	                  this.$t("bs-bigger") +
	                  " " +
	                  this.$t("bs-cannot-be-sent") +
	                  ", " +
	                  this.$t("bs-as-the-limit-of-5-uploads-has-already-been") +
	                  ".",
	                this.$t("bs-info")
	              );
	            }
	            // o atributo 'file_exists' é setado como false para poder ser usado na verificação do arquivo que está na próxima posição do laço
	            this.file_exists = false;
	          }
	        } else {
	          // caso a extensão do arquivo não seja valida, adiciono o nome desse arquivo ao atributo 'errors' que armazena os arquivos que não puderam ser adicionados
	          this.errors.push(attachment["name"]);
	        }
	        // caso algum arquivo tenha sido enviado com a extensão inválido, é disparado um alert para informar o ocorrido ao usuário.
	        if (this.errors.length) {
	          Swal.fire({
	            heightAuto: false,
	            icon: "error",
	            title: this.$t("bs-oops"),
	            text:
	              this.$t("bs-invalid-file-s-format") +
	              ": '" +
	              this.errors.join(", ") +
	              this.$t("bs-the-allowed-formats-are") +
	              ": " +
	              this.extensions.join(", ") +
	              ".",
	          });
	        }
	      });
	      this.errors = [];
	    },
	},
};
</script>

<style scoped>
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

.mw-80 {
	padding: 0px;
	max-width: 100px !important;
}

.ellipsis {
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
}
.col-name {
	width: calc(75% - 80px);
}
.care {
	font: normal normal 800 15px/16px Muli;
	color: #434343 !important;
	cursor: pointer;
}

.clickavaliation:hover {
	-webkit-text-fill-color: #0294ff;
	-webkit-text-stroke: 1px #0294ff;
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
	.content-chat,
	.modal-content,
	.w-380 {
		zoom: 85%;
	}
	.w-380 {
		width: 100% !important;
	}
	#search {
		min-width: 84%;
	}
	table {
		zoom: 80%;
	}
	.filter_list {
		width: 30px;
		left: 8px;
	}
	#btn-filter {
		width: 50px;
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