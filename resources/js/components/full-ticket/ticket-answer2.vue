<template>
    <div v-if="showBody" :class="{'answer-showed': showAnswer, 'mobile': isMobile}">
        <div class="ticket-answer-header" v-if="!isMobile">
            <div class="header_title d-table h-100 w-100">
                <span class="ba-hd__title d-table-cell align-middle">
                    {{ `${$t('bs-ticket')}: #${itemselected.number}` }}
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
                    <small>{{ `${$t('bs-ticket')} #${itemselected.id} - ${$t('bs-answer')}`}}</small>
                </div>
        </div>

        <div class="ticket-answer">
            <div class="ba-hd__card-content p-2">
                <div class="header_title d-table w-100">
                    <span class="ba-hd__title d-table-cell align-middle">
                        {{$t('bs-ticket-header')}}
                    </span>
                </div>
               <div class="p-3">
                    <b-form-select
                        size="sm"
                        v-model="form.openPhrase"
                        required
                    >
                    <b-form-select-option value=""></b-form-select-option>
                    <template v-for="(item, index) in settings.commands">
                        <template v-if="item.status == 'START'">
                            <b-form-select-option :value="item.description"  :key="'typetop'+index">{{$t(filterText(item.description))}}</b-form-select-option>
                        </template>
                    </template>
                </b-form-select>
                
                <span v-if="form.openPhrase != ''" class="line-break: auto;" disabled style="font-style:italic;">
                    ({{form.openPhrase}})
                </span>
               </div>
            </div>
            <div class="ba-hd__card-content p-2">
                <div class="header_title d-table w-100">
                    <span class="ba-hd__title d-table-cell align-middle">
                        {{$t('bs-ticket-body')}}
                    </span>
                </div>
                <div class="p-3">
                    <div class="card">
                        <div class="card-header">
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
                            <span v-if="!isMobile"  class="ml-4 mr-4">
                                <i class="fa fa-eye fa-1x bs-eye" aria-hidden="true"></i>
                                <span @click="showQuestion=!showQuestion" class="text2"> {{$t('bs-show-conversation')}}</span>
                            </span>
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
                        <div class="card-body p-0">
                            <b-row>
                                <b-col style="min-height:400px">
                                    <quill-editor
                                        ref="TicketAnswerQuillEditor"
                                        v-model="form.textTicket"
                                        :options="quillEditorOptions"
                                        id="quill-editor"
                                    />
                                </b-col>
                                <b-col cols="6" v-if="showQuestion" style="max-height:400px; overflow: auto; border:1px solid #DEE3EA">
                                    <hr>
                                    <template>
                                        <div class="output ql-snow">
                                            <div class="ql-editor pt-0 pl-0" v-viewer v-html="`<b>${$t('bs-description')}:</b> ${itemselected.description}`"></div>
                                        </div>
                                    </template>
                                    <span v-for="(item, index) in chat_history_before" :key="index+'aa'">
                                            <!-- FALA FUNCIONARIO -->
                                            <span v-if="item.company_user_company_department_id != null">
                                                <!-- TIPO TEXTO -->
                                                <template v-if="item.type === 'TEXT'">
                                                    <b style="text-transform: capitalize;">
                                                        <span style="color:#B40404;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ $t(item.name) }}:</span>
                                                    </b>
                                                     <div class="output ql-snow">
                                                        <div class="ql-editor" v-viewer v-html="item.content"></div>
                                                    </div>
                                                </template>
                                                <!-- TIPO EVENT -->
                                                <template v-if="item.type === 'EVENT'">
                                                <center>
                                                    <span>
                                                        {{ translate(item.content) }}
                                                    </span>
                                                </center>
                                                </template>
                                                <!-- TIPO IMAGE (PADRAO ANTIGO) -->
                                                <template v-if="item.type === 'IMAGE'">
                                                    <b style="text-transform: capitalize;">
                                                        <span style="color:#B40404;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ $t(item.name) }}:</span>
                                                    </b>
                                                    <img style="max-width: 40%" :src="getFile(item.id, JSON.parse(item.content).unique_name)"/>
                                                </template>
                                            </span>
                                            <!-- FALA CLIENTE -->
                                            <span v-else>
                                                <!-- TIPO TEXT -->
                                                <template v-if="item.type === 'TEXT'">
                                                    <b style="text-transform: capitalize;">
                                                        <span style="color:#0B0B61;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ $t(item.name) }}:</span>
                                                    </b>
                                                    <div class="output ql-snow">
                                                        <div class="ql-editor" v-viewer v-html="item.content"></div>
                                                    </div>
                                                </template>
                                                <!-- TIPO EVENT -->
                                                 <template v-if="item.type === 'EVENT'">
                                                    <center>
                                                        <span v-linkified>
                                                            {{ translate(item.content) }}
                                                        </span>
                                                    </center>
                                                </template>
                                                <template v-if="item.type === 'IMAGE'">
                                                    <b style="text-transform: capitalize;">
                                                        <span style="color:#0B0B61;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ $t(item.name) }}:</span>
                                                    </b>
                                                    <img style="max-width: 40%" :src="getFile(item.id, JSON.parse(item.content).unique_name)"/>
                                                </template>
                                            </span>
                                    </span>
                                    <span v-for="(item, index) in chat_history.slice().reverse()" :key="index">
                                        <hr>
                                        <!-- FALA FUNCIONARIO -->
                                        <span v-if="item.company_user_company_department_id != null">
                                            <template v-if="item.type === 'TEXT'">
                                                <b style="text-transform: capitalize;">
                                                    <span style="color:#B40404;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ $t(item.name) }}:</span>
                                                </b>
                                                <div class="output ql-snow">
                                                    <div class="ql-editor" v-viewer v-html="item.content"></div>
                                                </div>
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
                                                            <b-button variant="primary"> {{image.original_name}}</b-button>
                                                        </b-link>
                                                    </span><br>
                                                </span>
                                            </template>
                                            <template v-if="item.type === 'EVENT'">
                                                <center>
                                                    <span>
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
                                                <div class="output ql-snow">
                                                    <div class="ql-editor" v-viewer v-html="item.content"></div>
                                                </div>
                                            </template>
                                            <template v-if="item.type === 'FILE'">
                                                <b style="text-transform: capitalize;">
                                                    <span style="color:#0B0B61;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ $t(item.name) }}:</span>
                                                </b>
                                                <span v-linkified>
                                                    <span style="white-space: pre-line;">
                                                        <div class="output ql-snow">
                                                            <div class="ql-editor" v-viewer v-html="translate(item.content.message)"></div>
                                                        </div>
                                                    </span>
                                                    <br>
                                                    <span v-for="(image, index) in item.content.files" :key="index+'cc'">
                                                        <b-link variant="secondary" :href="getFile(item.id, image.unique_name)" target="_blank">
                                                           <b-button variant="primary"> {{image.original_name}}</b-button>
                                                        </b-link>
                                                    </span>
                                                </span>
                                            </template>
                                            <template v-if="item.type === 'EVENT'">
                                                <center>
                                                    <span>
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
            </div>
            <div class="ba-hd__card-content p-2">
                <div class="header_title d-table w-100">
                    <span class="ba-hd__title d-table-cell align-middle">
                        {{$t('bs-ticket-footer')}}
                    </span>
                </div>
                <div class="p-3">
                    <b-form-select
                        size="sm"
                        v-model="form.closePhrase"
                        required
                        >
                        <b-form-select-option value=""></b-form-select-option>
                        <template v-for="(item, index) in settings.commands">
                            <template v-if="item.status == 'END'">
                                <b-form-select-option :value="item.description"  :key="'typefooter'+index">{{$t(filterText(item.description))}}</b-form-select-option>
                            </template>
                        </template>
                    </b-form-select>

                    <span v-if="form.closePhrase != ''" class="line-break: auto;" disabled style="font-style:italic;">
                        ({{form.closePhrase}})
                    </span>
                </div>
            </div>
            <div class="ba-hd__card-content p-2">
                <div class="header_title d-table w-100">
                    <span class="ba-hd__title d-table-cell align-middle">
                        {{$t('bs-signature')}}
                    </span>
                </div>
                <div class="p-3">
                    <textarea
                        v-model="form.textAssin"
                        :placeholder="phMsg"
                        class="border pl-2"
                        v-autogrow
                    ></textarea>
                </div>
            </div>
            <div class="ba-hd__card-content p-2">
                <div class="row">
                    <div class="col-sm">
                        <div class="header_title d-table w-100">
                            <span class="ba-hd__title d-table-cell align-middle">
                                {{$t('bs-status')}}
                            </span>
                        </div>
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
                    </div>
                    <div class="col-sm">
                        <div class="header_title d-table w-100">
                            <span class="ba-hd__title d-table-cell align-middle">
                                {{$t('bs-send-notification-email')}}
                            </span>
                        </div>
                        <b-form-group class="mb-2 mt-1"
                            id="input-group-1"
                            label-for="input-1">
                                <b-form-select
                                    id="input-3"
                                    v-model="sendEmailSelected"
                                    :options="emailOptions"
                                    required
                                    class="pt-0"
                                ></b-form-select>
                        </b-form-group>
                    </div>
                </div>
            </div>
        </div>

        <div class="ticket-answer-footer" v-if="!isMobile">
            <div class="text-left">
                <b-button size="sm" variant="primary" @click="categoryTicket">{{ $t('bs-categorize') }}</b-button>
            </div>
            <div class="text-right">
                <b-button v-if="showbuttonSave" size="sm" variant="success" @click="checkCategory">{{ $t('bs-save') }}</b-button> <!-- saveTicket -->
                <b-button size="sm" variant="info" @click="openView">{{ $t('bs-preview') }}</b-button>
                <b-button size="sm" variant="secondary" @click="$root.$refs.TicketTicket2.showAnswer = false">{{ $t('bs-cancel') }}</b-button>
            </div>
        </div>

        <b-button variant="primary" class="float-btn-ticket-create" v-if="isMobile && !showView" @click="openView">
            <span class="material-icons-outlined" style="font-size: 30px; position: relative; top: 4px;  left: 0px;">visibility</span>
        </b-button>
    </div>

    <ticket-view2
        v-else-if="showView"
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
        :isMobile="isMobile"
        :prestatus="status"
        :settings="settings"
        :openCategory="openCategory"
        :sendEmailSelected="sendEmailSelected"
    ></ticket-view2>
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
			phMsg: this.$t('bs-type-your-message-here'),
			form: {
				text1: '',
				text2: '',
				text3: '',
				openPhrase: '',
				closePhrase: '',
				textTicket: '',
				textAssin: '',
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
            showbuttonSave: false,
            status: 'IN_PROGRESS',
			statusOptions: [{ text: this.$t('bs-in-progress'), value: 'IN_PROGRESS' }],
            sendEmailSelected: 'SEND',
            emailOptions: [{ text: this.$t('bs-send'), value: 'SEND' },{ text: this.$t('bs-not-send'), value: 'NOT_SEND' }],
            quillEditorOptions: {
                placeholder: this.$t('bs-type-your-message-here'),
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                        ['blockquote', 'code-block'],
                        ['link', 'image'],                                // links/images
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
                        [{ 'direction': 'rtl' }],                         // text direction
                        [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
                        [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
                        [{ 'align': [] }],
                        ['emoji'],                                        // emojis
                        ['clean']                                         // remove formatting button
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
            rejectCheck: false,
		}
	},
    created() {
        Quill.register('modules/blotFormatter', BlotFormatter);
        Quill.register('modules/ImageExtend', ImageExtend)
        Quill.register('modules/imageCompress', ImageCompress);
        Quill.register('modules/autoLinks', AutoLinks);
        this.$root.$refs.TicketAnswer2 = this;
    },
    computed: {
        editor() {
            return this.$refs.TicketAnswerQuillEditor.quill
        },
        content() {
            return this.form.textTicket
        }
    },
    watch: {
        content() {
            this.changeanswer();
        }
    },
	mounted(){
		//this.openView();
		this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
		this.setExtensions();
		this.form.textTicket = localStorage.getItem("ticket-"+this.itemselected.id);

        if(this.settings.ticket.sendEmail){
            this.sendEmailSelected = 'SEND';
        }else{
            this.sendEmailSelected = 'NOT_SEND';
        }

		if(this.settings.ticket.arrayTranslate == undefined){
			this.form.textAssin = '';
		}else{
            if(this.settings.ticket.arrayTranslate.signatureTicket == undefined){
                this.form.textAssin = '';
            }else{
                console.log( this.settings.ticket.arrayTranslate.signatureTicket);
                this.settings.ticket.arrayTranslate.signatureTicket.forEach((item) => {
                    if(item.code == this.user.language.split('_')[1]){
                        this.form.textAssin = item.text;
                    }

                    if(this.form.textAssin == ''){
                        this.form.textAssin = item.text;
                    }
                });
            }
			
		}

        if(this.restriction[0].ticket_close == 1 || this.is_admin == 1 || this.restriction[0].ticket_admin == 1){
			this.statusOptions.push({text: this.$t('bs-closed'), value: 'CLOSED'})
		}

		if(this.restriction[0].ticket_resolved == 1 || this.is_admin == 1 || this.restriction[0].ticket_admin == 1){
			if(this.restriction[0].ticket_close == 1){
				this.statusOptions.push({text: this.$t('bs-resolved'), value: 'RESOLVED'});
			}else{
				this.statusOptions.push({text: this.$t('bs-finalized-s'), value: 'RESOLVED'});
			}
		}
        this.getConfigCompany();
	},
	props:{
		settings: Object,
		user: Object,
		itemselected: Object,
		restriction: Array,
		is_admin: Number,
		chat_history: Array,
		chat_history_before: Array,
        showAnswer: Boolean,
        isMobile: Boolean,
        openCategory: "",
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
                            this.saveTicket();
                            this.rejectCheck = false;
                        }else{
                            axios.get('check-category', {
                                params:{
                                    chat_id: this.itemselected.chat_id,
                                    cttype: 'TICKET'
                                }
                            }).then(res => {
                                if(res.data.result){
                                    this.saveTicket();
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
                        this.saveTicket();
                    }
                }else{
                    this.saveTicket();
                }

            } catch (e) {
                this.saveTicket();
            }
        },
        saveTicket(){
            if(this.form.textTicket == '' || this.form.textTicket == null){
				this.$snotify.info( this.$t('bs-empty-fields')+", "+this.$t('bs-ticket-body'), this.$t('bs-info'), {
                    position: "rightTop",
                });
				return;
			}

            this.saveDirect = true;
			this.openView();
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
        filterText(value){
            if(this.isMobile){
                return value.substr(0, 20) + '...';
            }else{
                if(value.length < 120){
                    return value;
                }else{
                    return value.substr(0, 120) + '...';
                }
            }
        },
		getFile(chat_id, unique_name) {
            return `chat/files/${chat_id}/${unique_name}`;
        },
		translate: function (value) {
            if (!value) return ''
            else if(/^bs-/.test(value)) return  this.$t(value)
            return value
        },
        UTCtoClientTZ2(value = null){
            try {
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
            } catch (error) {
                return '';
            }
		},
		openView(){
			if(this.form.textTicket == '' || this.form.textTicket == null){
				this.$snotify.info( this.$t('bs-empty-fields')+", "+this.$t('bs-ticket-body'), this.$t('bs-info'), {
                    position: "rightTop",
                });
				return;
			}

			//LUGAR PARA SUBTITUIR COMANDO POR TEXTO
			for (var i = this.settings.commands.length - 1; i >= 0; i--) {
				this.form.textTicket = this.form.textTicket.replace('{{'+this.settings.commands[i].command+'}}', this.$t(this.settings.commands[i].description));
			}

			//LUGAR PARA SUBTITUIR COMANDO POR TEXTO
            if(this.form.textAssin == '' || this.form.textAssin == null) {
                this.form.textAssin = '';
            }else{
                this.form.textAssin = this.form.textAssin.replace('{name}', this.user.name);
			    this.form.textAssin = this.form.textAssin.replace('{department}', this.itemselected.department);
            }

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
}
</script>

<style scoped>
#quill-editor {
    word-break: break-word !important;
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
