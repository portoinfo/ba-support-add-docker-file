<template>
	<div class="c-scroll">
		<div v-if="showBody">
			<b-row class="ml-custom">
				<b-container fluid>
					<h1>{{$t('bs-ticket')}} #{{ itemselected.id }}</h1>
				</b-container>
			</b-row>
			<br>
			<b-col class="ml-n3 mt-3">
				<div class="cards">
					<h1>{{$t('bs-ticket-header')}}</h1>
					<b-row class="mt-5">
						<b-col cols="auto">

							<!-- 
							<v-select
								:dir="$dir"
								style="background-color: #F2F2F2"
								class="mt-1 mx-3 mb-3"
								:clearable="false"
								:options="languages"
								label="desc"
								@input="saveLanguage()"
								v-model="userLang"
								:reduce="value => value.key"
							>
								<template #selected-option="{key, desc}">
									<img height="24" class="mx-3" :src="`/images/flags/${key}.svg`" alt="">
									{{desc}}
								</template>
								<template #option="{key, desc}">
									<img height="24" class="mx-2" :src="`/images/flags/${key}.svg`" alt="">
									{{desc}}
								</template>
							</v-select>	 -->


							<b-form-select
							id="input-3"
							v-model="form.openPhrase"
							class="bs-label custom-mb w-400 ml-3"
							required
							>
							<b-form-select-option value=""></b-form-select-option>
							<template v-for="(item, index) in settings.commands">
								<template v-if="item.status == 'START'">
									<b-form-select-option :value="item.description"  :key="'typetop'+index">{{$t(item.description)}}</b-form-select-option>
								</template>
							</template>
						</b-form-select>




					</b-col>
				</b-row>
			</div>
		</b-col>
		<b-col class="ml-n3 mt-3">
			<div class="cards">
				<h1>{{$t('bs-ticket-body')}}</h1>
				<div id="chat" class="card h-40">
					<div class="card-header translator">
							<!-- <span class="ml-4 mr-4">
								<i class="bbi bbi-my-group bbi-16 mx-1"></i>
								<span class="text2"> Base de conhecimento</span>
							</span> -->
							<span @click="upload" class="ml-4 mr-4">
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
							<span class="ml-4 mr-4" @click="clear">
								<i class="fa fa-times bs-trash xm-1" aria-hidden="true"></i>
								<span class="text2">{{$t('bs-to-clean')}}</span>
							</span>
							<span class="ml-4 mr-4">
								<i class="fa fa-eye fa-1x bs-eye" aria-hidden="true"></i>
								<span @click="showQuestion=!showQuestion" class="text2"> {{$t('bs-show-conversation')}}</span> 
							</span>
							<!-- <b-list-group-item
									:class="['break-word m-0 pl-4 py-2', Object.keys(itemselected).length > 0 ? 'history cursor-pointer' : ''] "
									@click="openClientHistory(itemselected.created_by_encrypted)"
								>
									<img src="/images/icons/chat/history.svg" />
									&nbsp;<span v-if="Object.keys(itemselected).length > 0">{{ $t('bs-view-customer-history') }}</span>
							</b-list-group-item> -->
						</div>
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
					<div class="card-body">
						<b-row>
							<b-col>
								<b-form-textarea
								id="testee"
								v-model="form.textTicket"
								@input="changeanswer"
								:placeholder="phMsg"
								class="input-ticket"
								rows="10"
								></b-form-textarea>
							</b-col>
							<b-col cols="6" v-if="showQuestion" style="max-height:400px; overflow: auto; boder:2px solid black">
								<span v-for="(item, index) in chat_history_before" :key="index+'aa'">
										<hr>
                                        <!-- FALA FUNCIONARIO -->
                                        <span v-if="user.id == item.created_by">
                                            <!-- TIPO TEXTO -->
                                            <template v-if="item.type === 'TEXT'">
                                                <b style="text-transform: capitalize;">
                                                    <span style="color:#B40404;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ $t(item.name) }}:</span>
                                                </b>
                                                <span v-linkified>
                                                    <span style="white-space: pre-line;">{{ translate(item.content) }}</span>
                                                </span>
                                            </template>
                                            <!-- TIPO EVENT -->
                                            <template v-if="item.type === 'EVENT'">
                                               <center>
													<span v-linkified>
														{{ translate(item.content) }}
													</span>
												</center>
                                            </template>
                                            <!-- TIPO FILE (PADRAO ANTIGO) -->
                                            <template v-if="item.type === 'FILE'">
                                                <!-- <b style="text-transform: capitalize;">
                                                    <span style="color:#B40404;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ item.name }}:</span>
                                                </b>
                                                <span v-linkified>
													<span style="white-space: pre-line;">{{ translate(item.content.message) }}</span><br>
													<span v-for="(image, index) in item.content.files" :key="index+'bb'">
														<b-link variant="secondary" :href="getFile(item.id, image.unique_name)" target="_blank">
															{{image.original_name}}
														</b-link>
													</span>
												</span> -->
                                            </template>
                                            <!-- TIPO IMAGE (PADRAO ANTIGO) -->
                                            <template v-if="item.type === 'IMAGE'">
                                                <b style="text-transform: capitalize;">
                                                    <span style="color:#B40404;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ $t(item.name) }}:</span>
                                                </b>
												<img style="max-width: 40%" :src="getFile(item.id, item.content.unique_name)"/>
                                            </template>
                                        </span>
                                        <!-- FALA CLIENTE -->
                                        <span v-else>
                                            <!-- TIPO TEXT -->
                                            <template v-if="item.type === 'TEXT'">
                                                <b style="text-transform: capitalize;">
                                                    <span style="color:#0B0B61;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ $t(item.name) }}:</span>
                                                </b>
                                                <span v-linkified>
                                                    {{ translate(item.content) }}
                                                </span>
                                            </template>
                                            <!-- TIPO EVENT -->
                                            <template v-if="item.type === 'EVENT'">
												<center>
													<span v-linkified>
														{{ translate(item.content) }}
													</span>
												</center>
                                            </template>
                                            <template v-if="item.type === 'FILE'">
                                                <!-- <b style="text-transform: capitalize;">
                                                    <span style="color:#0B0B61;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ item.name }}:</span>
                                                </b>
                                                <span v-linkified>
													<span style="white-space: pre-line;">{{ translate(item.content.message) }}</span><br>
													<span v-for="(image, index) in item.content.files" :key="index+'bb'">
														<b-link variant="secondary" :href="getFile(item.id, image.unique_name)" target="_blank">
															{{image.original_name}}
														</b-link>
													</span>
												</span> -->
                                            </template>
                                            <template v-if="item.type === 'IMAGE'">
                                                <b style="text-transform: capitalize;">
                                                    <span style="color:#0B0B61;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ $t(item.name) }}:</span>
                                                </b>
												<img style="max-width: 40%" :src="getFile(item.id, item.content.unique_name)"/>
                                            </template>
                                        </span> 
								</span>
								<span v-for="(item, index) in chat_history.slice().reverse()" :key="index">
									<hr>
									<!-- FALA FUNCIONARIO -->
									<span v-if="user.id == item.created_by">
										<template v-if="item.type === 'TEXT'">
	                                        <b style="text-transform: capitalize;">
	                                            <span style="color:#B40404;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ $t(item.name) }}:</span>
	                                        </b>
	                                        <span v-linkified>
	                                            <span style="white-space: pre-line;">{{ translate(item.content) }}</span>
	                                        </span>
	                                    </template>
										<template v-if="item.type === 'FILE'">
	                                        <b style="text-transform: capitalize;">
	                                            <span style="color:#B40404;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ $t(item.name) }}:</span>
	                                        </b>
	                                        <span v-linkified>
												<span style="white-space: pre-line;">{{ translate(item.content.message) }}</span>
												<br>
												<span v-for="(image, index) in item.content.files" :key="index+'bb'">
													<b-link variant="secondary" :href="getFile(item.id, image.unique_name)" target="_blank">
														{{image.original_name}}
													</b-link>
												</span><br>
	                                        </span>
	                                    </template>
										<template v-if="item.type === 'EVENT'">
											<center>
												<span v-linkified>
													{{ translate(item.content) }}
												</span>
											</center>
										</template>
									</span>
									<!-- FALA CLIENTE -->
									<span v-else>
										<template v-if="item.type === 'TEXT'">
	                                        <b style="text-transform: capitalize;">
	                                            <span style="color:#0B0B61;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ $t(item.name) }}:</span>
	                                        </b>
	                                        <span v-linkified>
	                                            <span style="white-space: pre-line;">{{ translate(item.content) }}</span>
	                                        </span>
	                                    </template>
										<template v-if="item.type === 'FILE'">
	                                        <b style="text-transform: capitalize;">
	                                            <span style="color:#0B0B61;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ $t(item.name) }}:</span>
	                                        </b>
											<span v-linkified>
												<span style="white-space: pre-line;">{{ translate(item.content.message) }}</span>
												<br>
												<span v-for="(image, index) in item.content.files" :key="index+'cc'">
													<b-link variant="secondary" :href="getFile(item.id, image.unique_name)" target="_blank">
														{{image.original_name}}
													</b-link>
												</span>
	                                        </span>
	                                    </template>
										<template v-if="item.type === 'EVENT'">
											<center>
												<span v-linkified>
													{{ translate(item.content) }}
												</span>
											</center>
										</template>
									</span>
								</span>
							</b-col>
						</b-row>
					</div>
				</div>
			</div>
		</b-col>
		<b-col class="ml-n3 mt-3">
			<div class="cards">
				<h1 class="mb-5">{{$t('bs-ticket-footer')}}</h1>
				<b-row>
					<b-col cols="auto">
						<b-form-select
						id="input-3"
						v-model="form.closePhrase"
						class="bs-label custom-mb w-400 ml-3"
						required
						>
						<b-form-select-option value=""></b-form-select-option>
						<template v-for="(item, index) in settings.commands">
							<template v-if="item.status == 'END'">
								<b-form-select-option :value="item.description" :key="'typefooter'+index">{{$t(item.description)}}</b-form-select-option>
							</template>
						</template>
					</b-form-select>
				</b-col>
			</b-row>
		</div>
	</b-col>
	<b-col class="ml-n3 mt-3">
		<div class="cards">
			<h1 class="mb-4">{{$t('bs-signature')}}</h1>
			<b-form-textarea
			id="textarea"
			v-model="form.textAssin"
			:placeholder="phMsg"
			rows="10"
			></b-form-textarea>
		</div>
		<b-button type="submit" @click="back" variant="primary fr mt-2 ml-2">{{$t('bs-back')}}</b-button>
		<b-button type="submit" @click="openView" variant="primary fr mt-2 mb-5">{{$t('bs-preview')}}</b-button>
		<!-- <b-button type="submit" @click="save" variant="success fr mt-2 mr-2">{{$t('bs-save')}}</b-button> -->
	</b-col>
	<div>
		<b-table responsive bordered borderless striped hover
		class="local-striped-table"
		head-variant="light"
		table-variant="light"
		:fields="fields"
		:items="settings.commands">
			<template #cell(description)="row">
				{{$t(row.item.description)}}
			</template>
			<template #cell(status)="row">
				<span v-if="row.item.status == 'START'">
					{{$t('bs-start')}}
				</span>
				<span v-else-if="row.item.status == 'MIDDLE'">
					{{$t('bs-middle')}}
				</span>
				<span v-else>
					{{$t('bs-end')}}
				</span>
			</template>
		</b-table>
	</div>

	<br><br><br>
	</div>
	<template v-if="showView">
		<ticket-view 
			v-on:openView="openView" 
			v-on:back="openView"
			v-on:ticket_ticket="ticket_ticket"
			:openPhrase="form.openPhrase" 
			:closePhrase="form.closePhrase" 
			:textTicket="form.textTicket" 
			:textAssin="form.textAssin" 
			:user="user"
			:files="files"
			:extImages="extImages"
			:itemselected="itemselected" 
			:restriction="restriction"
			:is_admin="is_admin"
			:saveDirect="saveDirect"
		></ticket-view>
	</template>

	</div>
</template>

<script>

export default {
	data(){
		return {
			phMsg: this.$t('bs-type-your-message-here'),
			form: {
				text1: '',
				text2: '',
				text3: '',
				openPhrase: '',
				closePhrase: '',
				textTicket: '',
				textAssin: this.$t('bs-sincerely')+',\n'+this.user.name,
			},
			bodysendMessage: this.itemselected.description,
			show: true,
			showView: false,
			showBody: true,
			showQuestion: false,
			salutation: false,
			fields: [
			{ key: 'command', sortable: true, label: this.$t('bs-command') },
			{ key: 'description', sortable: true, label: this.$t('bs-description') },
			{ key: 'status', sortable: true, label: this.$t('bs-status') }
			],
			command: '',
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
			tz: '',
			saveDirect: false,
		}
	},
	mounted(){
		//this.openView();
		this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
		this.setExtensions();
		this.form.textTicket = localStorage.getItem("ticket-"+this.itemselected.id);

		if(this.settings.ticket.arrayTranslate == undefined){
			this.form.textAssin = this.$t('bs-sincerely') + ',\n' + this.user.name;
		}else{
			this.settings.ticket.arrayTranslate.signatureTicket.forEach((item) => {
				if(item.code == this.user.language.split('_')[1]){
					this.form.textAssin = item.text;
				}
			});
		}
	},
	props:{
		settings: Object,
		user: Object,
		itemselected: Object,
		restriction: Array,
		is_admin: Number,
		openClientHistory: Function,
		chat_history: Array,
		chat_history_before: Array,
	},
	methods:{
		getFile(chat_id, unique_name) {
            return `chat/files/${chat_id}/${unique_name}`;
        },
		translate: function (value) {
            if (!value) return ''
            else if(/^bs-/.test(value)) return  this.$t(value)
            return value
        },
		save(){
			this.saveDirect = true;
			this.openView();
		},
        UTCtoClientTZ2(value = null){
			if(value === null) {
				return ''
			} else {
				let h_format = moment(value, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD HH:mm:ss");
				let datetime = h_format.split(" ");
				let date = datetime[0];
				let time = datetime[1];
				let date_split = date.split("-");
				let time_split = time.split(":");
				let year = date_split[0];
				let month = date_split[1];
				let day = date_split[2];
				let hour = time_split[0];
				let minute = time_split[1];
				let second = time_split[2];
				var month_integer = parseInt(month, 10);
				if (month_integer >= 1) {
					month_integer--;
				}
				let dateUTC = new Date(Date.UTC(year, month_integer, day, hour, minute, second));
				let converted_time = dateUTC.toLocaleString("pt-BR", {
					timeZone: this.tz,
				});

				var mt = require("moment-timezone");
				return mt(converted_time, "DD/MM/YYYY HH:mm:ss")
					.tz(this.tz)
					.locale(this.user.language)
					.format('L LTS');
			}
		},
		openView(){
			if(this.form.textTicket == '' || this.form.textTicket == null){
				this.$snotify.info( this.$t('bs-empty-fields')+", "+this.$t('bs-ticket-body'), this.$t('bs-info'));
				return;
			}

			//LUGAR PARA SUBTITUIR COMANDO POR TEXTO
			for (var i = this.settings.commands.length - 1; i >= 0; i--) {
				this.form.textTicket = this.form.textTicket.replace('{{'+this.settings.commands[i].command+'}}', this.$t(this.settings.commands[i].description));
			}

			//LUGAR PARA SUBTITUIR COMANDO POR TEXTO
			this.form.textAssin = this.form.textAssin.replace('{name}', this.user.name);
			this.form.textAssin = this.form.textAssin.replace('{department}', this.itemselected.department);
			

			this.showView = !this.showView;
			this.showBody = !this.showView;
		},
		back(){
			this.$emit('back');
		},
		ticket_ticket(){
			this.showView = false;
			this.showBody = false;
			this.$emit('ticket_ticket');
		},
		home(){
			this.$emit('home');
		},
		activeSalutation(){
			salutation = !salutation;
		},
		clear(){
			this.form.textTicket = '';
		},
		changeanswer(){
			localStorage.setItem("ticket-"+this.itemselected.id, this.form.textTicket);
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
			},
	};
</script>

<style scoped lang="scss">

[data-tooltip] {
  position: relative;
  font-weight: bold;
}

[data-tooltip]:after {
  display: none;
  position: absolute;
  top: -5px;
  padding: 5px;
  border-radius: 3px;
  left: calc(100% + 2px);
  content: attr(data-tooltip);
  white-space: nowrap;
  background-color: #0095ff;
  color: White;
}

[data-tooltip]:hover:after {
  display: block;
}

.bs-eye{
	color: #A4BEDE;
}
.customDescr{
	padding: 10px;
	height: 100%;
	resize: none;
	text-align: left;
	font: normal normal bold 16px/20px Muli;
	letter-spacing: 0px;
	color: #707070;
	opacity: 1;
}

.input-ticket{
	height: 100%;
	resize: none;
	text-align: left;
	font: normal normal bold 16px/20px Muli;
	letter-spacing: 0px;
	color: #707070;
	background-color: #fff; /* modificado a pedido da giulia */
	opacity: 1;
}

.c-scroll{
	overflow-y: scroll;
	overflow-x: hidden;
	height: 110%;
}

.text2{
	text-align: left;
	font: normal normal bold 16px/30px Muli;
	letter-spacing: 0px;
	color: #333333;
	opacity: 1;
}

.custom-mb{
	margin-top: -10px;
}

.w-220{
	width: 220px;
}

.w-400{
	width: 400px;
}
.cards{
	background: #FFFFFF 0% 0% no-repeat padding-box;
	box-shadow: 0px 0px 9px #26242424;
	border-radius: 5px;
	padding: 27px;
}

h1 {
	font: normal normal 800 25px/19px Muli;
	letter-spacing: 0px;
	color: #0080fc;
	opacity: 1;
}

h2 {
	font: normal normal 800 15px/21px Muli;
	letter-spacing: 0px;
	color: black;
	opacity: 1;
}

@media screen and (max-width: 1199px) {
	.w-400{
		width: 250px!important;
	}
}
</style>