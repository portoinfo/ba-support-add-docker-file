<template>
    <v-row
        no-gutters
        :style="{ background: $vuetify.theme.themes[theme].input }"
        class="rounded-lg"
    >
        <v-col cols="12">
            <div id="toolbar" :style="{ background: $vuetify.theme.themes[theme].white }">
                <select class="mr-2 ql-size" v-if="!$store.state.isMobile"></select>
                <button class="mr-2 ql-bold"></button>
                <button class="mr-2 ql-italic"></button>
                <button class="mr-2 ql-underline"></button>
                <button class="mr-2 ql-list" value="bullet" v-if="!$store.state.isMobile"></button>
                <button class="mr-2 ql-link"></button>
                <select class="mr-2 ql-align d-none" value=""></select>
                <button class="mr-2 ql-code-block" v-if="!$store.state.isMobile"></button>
                <button class="mr-2 ql-image"></button>
                <v-btn v-if="attachFile" icon color="transparent" @click="upload"><v-icon size="19px">$attach_file</v-icon></v-btn>
                <input
                    type="file"
                    id="attachments"
                    ref="attachments"
                    multiple
                    v-on:change="handleFilesUpload()"
                    style="display: none"
                />
            </div>
        </v-col>
        <v-col>
            <quill-editor
                v-model="editorVal"
                :options="quillEditorOptions"
                id="quill-editor"
            ></quill-editor>
        </v-col>
        <v-col class="w-fc px-3 d-flex align-center">
            <v-btn class="mx-2" elevation="0" fab color="transparent" small @click="sendMessage()"> 
                <v-icon> $send </v-icon>
            </v-btn>
        </v-col>
        <v-col cols="12" v-if="files.length">
            <v-chip
                v-for="(file, index) in files" :key="index"
                class="ma-1"
                close
                small
                @click:close="removeFile(index)"
            >
                {{formatFilename(file.name)}}
            </v-chip>
        </v-col>
    </v-row>
</template>

<script>
import Quill from "quill";
import { quillEditor } from "vue-quill-editor";
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";
import "quill-emoji/dist/quill-emoji.css";
import * as Emoji from "quill-emoji";
import { container, ImageExtend, QuillWatch } from "quill-image-extend-module";
import BlotFormatter from "quill-blot-formatter";
import ImageCompress from "quill-image-compress";
import AutoLinks from "quill-auto-links";
import Delta, { AttributeMap } from 'quill-delta';
import bold from "../icons/quill/bold.js";
import italic from "../icons/quill/italic.js";
import underline from "../icons/quill/underline.js";
import list from "../icons/quill/list.js";
import link from "../icons/quill/link.js";
import align from "../icons/quill/align.js";
import image from "../icons/quill/image.js";
import code_block from "../icons/quill/code-block.js";
var icons = Quill.import("ui/icons");
icons["italic"] = italic;
icons["bold"] = bold;
icons["underline"] = underline;
icons["list"] = list;
icons["link"] = link;
icons["align"][""] = align;
icons["image"] = image;
icons["code-block"] = code_block;

export default {
    props: {
        module:""
    },
    data() {
        return {
            editorVal: "",
            quillEditorOptions: {
                placeholder: this.$t('bs-how-may-we-assist-you-today'),
                modules: {
                    toolbar: {
                        container: "#toolbar",
                    },
                    "emoji-toolbar": true,
                    "emoji-textarea": false,
                    "emoji-shortname": true,
                    autoLinks: {
                        paste: true,
                        type: true,
                    },
                    ImageExtend: {
                        loading: true,
                        name: "img",
                    },
                    imageCompress: {
                        quality: 0.9,
                        maxWidth: 2000,
                        maxHeight: 2000,
                        imageType: "image/jpeg",
                        debug: false,
                        suppressErrorLogging: false,
                    },
                    actions: {
                        sendMessage: this.module == 'chat' ? this.$root.$refs.ClientChatOpened.sendMessage : this.$root.$refs.ClientTicketOpened.sendMessage,
                        module: this.module,
                        isTicketCreate: this.$route.name == 'ticket-opened'&& this.$route.params.id == 'create',
                    },
                    keyboard: {
                        bindings: {
                            enter: {
                                key: 13,
                                handler: function(range, context) {
                                    var el_list = document.querySelector(".ql-list");
                                    var el_code = document.querySelector(".ql-code-block");
                                    if(el_list) var el_list_active = el_list.classList.contains('ql-active');
                                    if(el_code) var el_code_active = el_code.classList.contains('ql-active');
                                    if (el_list_active || el_code_active || this.quill.options.modules.actions.module == 'ticket' && !this.quill.options.modules.actions.isTicketCreate) {
                                        const [line, offset] = this.quill.getLine(range.index);
                                        const delta = new Delta()
                                        .retain(range.index)
                                        .insert('\n', context.format)
                                        .retain(line.length() - offset - 1)
                                        .retain(1, { header: null });
                                        this.quill.updateContents(delta, Quill.sources.USER);
                                        this.quill.setSelection(range.index + 1, Quill.sources.SILENT);
                                        this.quill.scrollIntoView()
                                    } else {
                                        this.quill.options.modules.actions.sendMessage() 
                                    }
                                }
                            },
                            ctrl_enter: {
                                key: 13,
                                ctrlKey: true,
                                handler: function(range, context) {
                                    const [line, offset] = this.quill.getLine(range.index);
                                    const delta = new Delta()
                                    .retain(range.index)
                                    .insert('\n', context.format)
                                    .retain(line.length() - offset - 1)
                                    .retain(1, { header: null });
                                    this.quill.updateContents(delta, Quill.sources.USER);
                                    this.quill.setSelection(range.index + 1, Quill.sources.SILENT);
                                    this.quill.scrollIntoView()
                                }
                            },
                        }
                    }
                },
            },
            //uploads
            extImages: ["jpg", "jpeg", "png", "bmp", "gif"],
            extDocuments: ["docx", "doc", "pdf", "xps", "txt", "odt", "svg"],
            extSpreadsheets: ["xlsx", "xls", "xlt", "csv", "ods"],
            extPresentation: ["pptx", "ppt", "pot", "ppsx", "pps", "odp"],
            extensions: [],
            file_exists: false,
        }
    },
    components: {
		quillEditor,
	},
    created() {
        this.$root.$refs.QuillEditor = this;
		Quill.register("modules/blotFormatter", BlotFormatter);
		Quill.register("modules/ImageExtend", ImageExtend);
		Quill.register("modules/emoji", Emoji);
		Quill.register("modules/imageCompress", ImageCompress);
		Quill.register("modules/autoLinks", AutoLinks);
        Quill.register('modules/actions', function() {});
        Quill.debug('error');
        this.setExtensions();
        this.getContentStoraged();
	},
    mounted () {
        this.focus();
    },
    computed: {
        theme() {
			return this.$vuetify.theme.dark ? "dark" : "light";
		},
        attachFile() {
            if (this.module == 'chat') {
                if (this.$store.state.newChat.questions.length || (this.$store.state.newChatInRobot[this.$root.$refs.ClientChatOpened.ncir_idx] &&  this.$store.state.newChatInRobot[this.$root.$refs.ClientChatOpened.ncir_idx].questions.length)) {
                    return false;
                } else {
                    return true;
                }
            } else {
                if (this.$store.state.newTicket.questions.length) {
                    return false;
                } else {
                    return true;
                }
            }
        },
        content: {
			get() {
				return this.$store.state.currentEditor.content;
			},
			set(value) {
				this.$store.state.currentEditor.content = value;
			},
		},
        files: {
			get() {
				return this.$store.state.currentEditor.files;
			},
			set(value) {
				this.$store.state.currentEditor.files = value;
			},
		},
    },
    watch: {
        editorVal(content) {
            if (content) {
                this.content = content;
            }

            var contentStoraged = localStorage.getItem('content');
            var chat_id = this.module == 'chat' ? (this.$store.state.chat.status != 'ROBOT' ? this.$store.state.chat.id : false) : this.$store.state.ticket.chat_id 

            if (contentStoraged && chat_id) {
                var contentArray = JSON.parse(contentStoraged)

                var index = contentArray.findIndex(
                    (item) => item.chat_id === chat_id
                );

                if (index !== -1) {
                    contentArray[index].content = content;
                    var item = JSON.stringify(contentArray);
                    localStorage.setItem("content", item)
                } else {
                    contentArray.push({chat_id, content});
                    var item = JSON.stringify(contentArray);
                    localStorage.setItem("content", item)
                }

            } else if (chat_id){
                var item = JSON.stringify([{chat_id, content}]);
                localStorage.setItem("content", item)
            }

        },
        content(v) {
            if (this.$store.state.iOS) {
                var objDiv = document.getElementById("chat-scroll");
                objDiv.scrollTop = objDiv.scrollHeight;
            }
            if (!v) {
                this.editorVal = "";
            }
        }
    },
    methods: {
        handleFilesUpload() {
            var attachments = this.$refs.attachments.files;
            var errors = [];
            if (attachments.length <= 5) {
                Array.from(attachments).forEach((attachment) => {
                    let reverse_ext = attachment["name"]
                    .split("")
                    .reverse()
                    .join("")
                    .split(".", 1)
                    .toString();
                    let ext = reverse_ext.split("").reverse().join("");
                    if (this.extensions.includes(ext) || this.extensions.includes(ext.toLowerCase())) {
                        if (this.files.length) {
                            this.files.forEach((file) => {
                                file["name"] === attachment["name"] ? (this.file_exists = true) : "";
                            });
                        }
                        if (this.file_exists == false && attachment.size <= 5242880) {
                            var files = this.files;
                            files.push(attachment);
                            this.files = files;
                        } else {
                            this.file_exists = false;
                        }
                    } else {
                        errors.push(attachment["name"]);
                    }
                    if (errors.length) {
                        this.$swal({
                            icon: 'warning',
                            title: `${this.$t("bs-oops")}...`,
                            html: 
                                `${this.$t("bs-invalid-file-format")}</br>
                                <b>${errors.join(", ")}</b></br></br>
                                ${this.$t("bs-the-allowed-formats-are")}:</br>
                                <b>${this.extensions.join(", ")}.</b>`
                        });
                    }
                });
                this.focus();
            } else {
                this.$swal({
                    icon: 'warning',
                    title: `${this.$t("bs-oops")}...`,
                    text: this.$t('bs-you-can-select-only-5-files')
                });
            }
        },
        focus() {
            var interval = setInterval(function() {
                var quill = document.getElementById('quill-editor');
                if (quill) {
                    clearInterval(interval);
                    var container = quill.children[0];
                    container.firstChild.focus();
                }
            })
        },
        formatFilename(file) {
            var name = file.split('.')[0];
            var ext = file.split('.')[1];
            return `${name.substr(0, 10)}${name.length > 10 ? '...' : '.'}${ext}`
        },
        getContentStoraged() {
            var contentStoraged = localStorage.getItem('content');
            if (contentStoraged) {
                var chat_id = this.module == 'chat' ? this.$store.state.chat.id : this.$store.state.ticket.chat_id 
                var contentArray = JSON.parse(contentStoraged);
                var index = contentArray.findIndex(
                    (item) => item.chat_id === chat_id
                );
                if (index !== -1) {
                    this.editorVal = contentArray[index].content;
                }
            }
        },
        sendMessage() {
            
            if (this.module == 'chat') {
                this.$root.$refs.ClientChatOpened.sendMessage();
            } else {
                this.$root.$refs.ClientTicketOpened.sendMessage();
            }

            if (this.$store.state.isMobile) {
                var quill = document.getElementById('quill-editor');
                var container = quill.children[0];
                container.firstChild.focus();
            }
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
        removeFile(index) {
            var files = this.files;
            files.splice(index, 1);
            this.files = files;
        }
    },
}
</script>