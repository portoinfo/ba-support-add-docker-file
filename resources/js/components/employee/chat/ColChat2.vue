<template>
    <div class="h-100 w-100" :class="{ 'chat-mobile': isMobile, 'ticket_alert': chat.turn_into_ticket_at_closing }">
        <div class="h-100 w-100" id="chat-content" :style=" isMobile ? {'min-height': heigthMobile, 'max-height': heigthMobile, position: 'fixed', bottom: '0px'} : '' ">
            <div class="chat-header">
                <center class="item-gravatar">
                    <div class="vertical-center">
                        <b-button class="btn-back-mobile" variant="light" @click="clearAndShowTable()">
                            <span v-show="isMobile" class="bs-ico">&#xe5c4;</span>
                            <gravatar
                                :email="chat.client.email"
                                :status="$status.get(chat.client.id)"
                                :size="isMobile ? '46px' : '31px'"
                                :name="$t(chat.client.name)"
                                color="primary"
                                :ba_acct_data="chat.client.builderall_account_data"
                            />
                        </b-button>
                    </div>
                </center>
                <div class="item-name pl-1">
                    <div class="vertical-bottom mw-100 pr-1">
                        <span class="text-truncate d-block">
                            <b>{{ $t(chat.client.name) }}</b>
                        </span>
                    </div>
                </div>
                <center class="item-btn">
                    <div class="vertical-center">
                        <b-button
                            v-show="!isMobile"
                            class="btn-close_info"
                            variant="light"
                            @click="clearAndShowTable()"
                        >
                            <span class="bs-ico">&#xe5cd;</span>
                        </b-button>
                        <b-button v-show="isMobile" class="btn-close_info" variant="light" v-b-toggle.sidebar-right-info @click="iOSBodyScroll('enable')">
                            <span class="bs-ico">&#xe88e;</span>
                        </b-button>
                        <b-sidebar
                            v-if="isMobile"
                            id="sidebar-right-info"
                            :title="$t('bs-chat-information')"
                            right
                            shadow
                            z-index="3"
                            bg-variant="white"
                            @hidden="iOSBodyScroll('disable')"
                        >
                            <chat-info
                                :chat="$root.$refs.FullChat2.chat"
                                :showChat="$root.$refs.FullChat2.showChat"
                                :user="$root.$refs.FullChat2.session_user"
                                :footerActiveChat="$root.$refs.FullChat2.footerActiveChat"
                                :openClientHistory="$root.$refs.FullChat2.openClientHistory"
                                :chat_admin="$root.$refs.FullChat2.admin"
                                :chat_queue_full_control="$root.$refs.FullChat2.chat_queue_full_control"
                                :chats_on_queue="$root.$refs.FullChat2.chats_on_queue"
                                :chats_resolved="$root.$refs.FullChat2.chats_resolved"
                                :chats_canceled="$root.$refs.FullChat2.chats_canceled"
                                :openChat="$root.$refs.FullChat2.openChat"
                                :restriction="restriction"
                            >
                            </chat-info>
                        </b-sidebar>
                    </div>
                </center>
                <div class="item-dept pl-1">
                    <small>#{{ chat.number }}</small>
                </div>
            </div>
            <div v-if="chat.turn_into_ticket_at_closing" class="alert alert-primary mb-0 border-0" role="alert">
                <span>{{ $t('bs-chat-programmed-to-turn-into-a-ticket') }}</span>
                <span v-if="!$root.$refs.FullChat2.take_on_chat" @click="$root.$refs.FullChat2.showModalEditTurnoIntoTicket" class="alert-link cursor-pointer">{{ $t('bs-edit') }}</span>
            </div>
            <div class="chat-main" id="chat-main" v-on:scroll="handleScroll">
                <slot name="messages"></slot>
                <div v-if="btnScroll" class="footer-btn text-right">
                    <button class="btn-scroll" @click="scrollToBottom()">
                        <span class="bs-ico" style="font-size: 36px">keyboard_arrow_down</span>
                    </button>
                </div>
            </div>
            <footer v-if="chat.status == 'IN_PROGRESS'" class="pb-2" :class="{'incognito': incognito_mode}">
                <slot name="select"></slot>
                <div id="toolbar">
                    <button
                        @click="setEditMode()"
                        id="button-edit-mode"
                    >
                        <span
                            style="font-size: 18px"
                            class="material-icons-outlined"
                            :class="{'ql-active': editMode}"
                        >
                            mode_edit
                        </span>
                    </button>

                    <div v-show="editMode">
                        <select class="ql-size"></select>
                        <button class="ql-bold"></button>
                        <button class="ql-italic"></button>
                        <button class="ql-underline"></button>
                        <button class="ql-list" value="bullet"></button>
                        <button class="ql-link"></button>
                        <button class="ql-code-block"></button>
                        <select class="ql-color"></select>
                        <select class="ql-align" value=""></select>
                    </div>

                    <button class="ql-image"></button>
                    <slot name="chat-buttons"></slot>
                </div>
                <div id="q-content">
                    <quill-editor
                        :class="{'edit-mode': editMode}"
                        class="editor"
                        ref="myQuillEditor"
                        v-model="chat.content"
                        :options="editorOption"
                        @blur="onEditorBlur($event)"
                        @focus="onEditorFocus($event)"
                        @ready="onEditorReady($event)"
                        id="quill-editor"
                    />

                    <div class="d-table">
                        <div class="d-table-cell align-top">
                            <span  @click="callSendMessage" id="send-btn" class="material-icons-outlined" :class="{'incognito': incognito_mode}">send</span>
                        </div>
                    </div>
                </div>
                <div class="output ql-snow" v-if="contentTranslated != ''">
                <!-- <div class="output ql-snow"> -->
                    <div class="ql-editor" v-html="contentTranslated"></div>
                    <!-- <div class="ql-editor">aaaaaaaaaaaaaaa</div> -->
                </div>
            </footer>
        </div>
    </div>
</template>

<script>
const  bodyScrollLock  =  require ( 'body-scroll-lock' ) ;

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
    created () {
        Quill.register('modules/blotFormatter', BlotFormatter);
        Quill.register('modules/ImageExtend', ImageExtend)
        Quill.register("modules/emoji", Emoji);
        Quill.register('modules/imageCompress', ImageCompress);
        Quill.register('modules/autoLinks', AutoLinks);
        Quill.register('modules/actions', function() {});
        this.$root.$refs.ColChat2 = this;

    },
    data() {
        return {
            hash:
                "98E549D9B6DA26962E7D1B2BEE8064918353F3CB979F9550C39F97B098DFD0DB",
            showInput: true,
            btnScroll: false,
            heigthMobile: "",
            iOS: false,
            contentTranslated: "",
            translating: false,
            editorOption: {
                placeholder: this.$t("bs-type-here") + "...",
                modules: {
                    autoLinks: {
                        paste: true,
                        type: true
                    },
                    ImageExtend: {
                        loading: true,
                        name: 'img',
                    },
                    toolbar: {
                        container: '#toolbar',
                    },
                    imageCompress: {
                        quality: 0.9,
                        maxWidth: 2000,
                        maxHeight: 2000,
                        imageType: 'image/jpeg',
                        debug: false,
                        suppressErrorLogging: false,
                    },
                    actions: {
                        sendMessage: this.callSendMessage,
                        replace: this.replace
                    },
                    "emoji-toolbar": true,
                    "emoji-textarea": true,
                    "emoji-shortname": true,
                    keyboard: {
                        bindings: {
                            enter: {
                                key: 13,
                                handler: function(range, context) {
                                    var toolbar = this.quill.getModule('toolbar');
                                    var btn_edit = toolbar.container.firstChild;
                                    var span_edit = btn_edit.firstChild;
                                    var active = span_edit.classList.contains('ql-active');
                                    if (active) {
                                        const [line, offset] = this.quill.getLine(range.index);
                                        const delta = new Delta()
                                        .retain(range.index)
                                        .insert('\n', context.format)
                                        .retain(line.length() - offset - 1)
                                        .retain(1, { header: null });
                                        this.quill.updateContents(delta, Quill.sources.USER);
                                        this.quill.setSelection(range.index + 1, Quill.sources.SILENT);
                                        this.quill.scrollIntoView();
                                    } else {
                                        this.quill.options.modules.actions.sendMessage()
                                    }
                                }
                            },
                            tab: {
                                key: 9,
                                handler: function(range, context) {
                                    this.quill.options.modules.actions.replace().then((result) => {
                                        //
                                    }).catch((err) => {
                                        if (context.format.table) return true;
                                        this.quill.history.cutoff();
                                        const delta = new Delta()
                                        .retain(range.index)
                                        .delete(range.length)
                                        .insert('\t');
                                        this.quill.updateContents(delta, Quill.sources.USER);
                                        this.quill.history.cutoff();
                                        this.quill.setSelection(range.index + 1, Quill.sources.SILENT);
                                        return false;
                                    });
                                }
                            }
                        }
                    }
                }
            },
            editMode: false,
        };
    },
    props: {
        hideOnSmall: Boolean,
        incognito_mode: Boolean,
        incognito_id: String,
        chat: Object,
        sendMessage: "",
        footerActiveChat: "",
        departmentCommands: "",
        clearActiveChat: "",
        showTableComponent: Function,
        isMobile: Boolean,
        restriction: Object,
    },
    mounted() {
        window.addEventListener("resize", this.setChatHeightMobile());
        var userAgent = window.navigator.userAgent;
        if (userAgent.match(/iPad/i) || userAgent.match(/iPhone/i)) {
            const  disableBodyScroll  =  bodyScrollLock.disableBodyScroll;
            const targetElement  =  document.querySelector('#chat-main');
            disableBodyScroll(targetElement);
        }
    },
    computed: {
        computedChat: function() {
            return JSON.parse(JSON.stringify(this.chat));
        },
        editor() {
            return this.$refs.myQuillEditor.quill
        },
    },
    watch: {
        computedChat: {
            deep: true,
            handler: function(newVal, oldVal) {
                if (newVal.show) {
                    setTimeout(() => {
                        var quill = document.getElementById('quill-editor');
                        var container = quill.children[0];
                        container.firstChild.focus();
                    }, 1000);
                }

                // if (newVal.content !== oldVal.content) {
                //     this.translate();
                // }

                if (newVal.number !== oldVal.number) {
                    this.showInput = false;
                    setTimeout(() => {
                        this.showInput = true;
                    }, 200);
                }
            }
        }
    },
    // beforeDestroy(){
    //     window.removeEventListener('keydown', this.ChatShortcutActions);
    // },
    methods: {
        setEditMode() {
            this.editMode = !this.editMode
            var quill = document.getElementById('quill-editor');
            var container = quill.children[0];
            container.firstChild.focus();

        },
        // ChatShortcutActions(e) {
        //     console.log(e);

        // },
        onEditorBlur(quill) {
            //console.log('editor blur!', quill)
        },
        onEditorFocus(quill) {
            //console.log('editor focus!', quill)
        },
        onEditorReady(quill) {

        },
        onEditorChange({ quill, html, text }) {
            console.log('editor change!', quill, html, text)
            this.content = html
        },
        translate() {
            return new Promise((resolve, reject)=>{
                if(this.$root.$refs.ModalTranslate.status_my_messages) {
                    this.translating = true;
                    this.$google.translate(this.chat.content, this.$root.$refs.ModalTranslate.languageVisitorMessages).then((result) => {
                        this.contentTranslated = result.data.translations[0].translatedText
                        this.translating = false;
                        resolve();
                    })
                }else{
                    resolve();
                }
            }) 
        },
        clearAndShowTable() {

            var status = this.chat.status;
            // this.clearActiveChat();

            switch (status) {
                case 'IN_PROGRESS':
                    this.$root.$refs.FullChat2.showTableComponent('inProgress')
                    break;

                case 'CLOSED':
                case 'RESOLVED':
                    this.$root.$refs.FullChat2.showTableComponent('resolved')
                    break;

                case 'CANCELED':
                    this.$root.$refs.FullChat2.showTableComponent('canceled')
                    break;

                default:
                    this.$root.$refs.FullChat2.showTableComponent('inProgress')
                    break;
            }
        },
        iOSBodyScroll(action) {
            var userAgent = window.navigator.userAgent;
            if (userAgent.match(/iPad/i) || userAgent.match(/iPhone/i)) {
                if (action == 'enable') {
                    const  enableBodyScroll  =  bodyScrollLock.enableBodyScroll;
                    const targetElement  =  document.querySelector('#chat-main');
                    enableBodyScroll(targetElement);
                } else if (action == 'disable') {
                    const  disableBodyScroll  =  bodyScrollLock.disableBodyScroll;
                    const targetElement  =  document.querySelector('#chat-main');
                    disableBodyScroll(targetElement);
                }
            }
        },
        setChatHeightMobile() {
            var userAgent = window.navigator.userAgent;
            if (userAgent.match(/iPad/i) || userAgent.match(/iPhone/i)) {
                // iPhone or iPad
                this.iOS = true;
                setTimeout(() => {
                    if (window.visualViewport) {
                        // iOS >= 13
                        var height = window.visualViewport.height;
                    } else {
                        // iOS < 13
                        var height = window.innerHeight;
                    }
                    this.heigthMobile = `${height}px`;
                }, 200);
            } else {
                // Android
                setTimeout(() => {
                    this.heisgthMobile = "100vh";
                }, 200);
            }
        },
        scrollToBottomMobile() {
            if (this.isMobile) {
                setTimeout(() => {
                    if (!this.btnScroll) {
                        this.scrollToBottom();
                    }
                }, 200);
            }
        },
        callShowTableComponent(table) {
            var userAgent = window.navigator.userAgent;
            if (userAgent.match(/iPad/i) || userAgent.match(/iPhone/i)) {
                const  enableBodyScroll  =  bodyScrollLock.enableBodyScroll;
                const targetElement  =  document.querySelector('#chat-main');
                enableBodyScroll(targetElement);
                this.showTableComponent(table);
            } else {
                this.showTableComponent(table);
            }

        },
        handleScroll() {
            var chat = document.getElementById("chat-main");
            if (chat) {
                var current = chat.scrollTop;
                var max = chat.scrollHeight - chat.clientHeight;
                var min = max - 500;
                if (current < min) {
                    this.btnScroll = true;
                } else {
                    this.btnScroll = false;
                }
            }
        },
        scrollToBottom() {
            var chat = document.getElementById("chat-main");
            chat.scrollTop = chat.scrollHeight - chat.clientHeight;
        },
        callSendMessage() {
            this.translate().then(()=>{
                console.log("!")
                if (!this.translating) {
                    this.sendMessage();
                    this.contentTranslated = "";
                }
            });
        },
        replace() {
            return new Promise((resolve, reject) => {
                var itemsProcessed = 0;
                var found = 0;

                this.departmentCommands.forEach(element => {
                    itemsProcessed++;
                    var search = this.chat.content.search(element.command);
                    if (search !== -1) {
                        found = 1;
                        this.chat.content = this.chat.content.replace(element.command, element.description);
                    }

                    if(itemsProcessed === this.departmentCommands.length) {
                        if (found) {
                            resolve();
                        } else {
                            reject();
                        }
                    }
                });
            })
        }
    }
};
</script>

<style scoped>

button:focus {outline:0;}

#send-btn {
    background: #0080FC;
    padding: 7px;
    color: white;
    border-radius: 100%;
}

#send-btn.incognito {
    background: #ff6600;
}

#q-content {
    display: grid;
    grid-template-columns: minmax(calc(100% - 50px), calc(100% - 50px)) 50px;
    padding-left: 11px;
}

#chat-content {
    display: grid;
    grid-template-columns: 100%;
    grid-template-rows: 35px minmax(100px, 100vh) auto;
}

.ticket_alert #chat-content {
    grid-template-rows: 35px auto minmax(100px, 100vh) auto;
}

.chat-mobile #chat-content {
    z-index: 4;
    overflow: hidden !important;
    width: 100vw;
    grid-template-rows: 55px minmax(100px, 100vh) auto;
}

.chat-mobile.ticket_alert #chat-content {
    z-index: 4;
    overflow: hidden !important;
    width: 100vw;
    grid-template-rows: 55px auto minmax(100px, 100vh) auto;
}


.chat-mobile #chat-content .chat-header {
    border-radius: 0px !important;
}

.chat-header {
    background-color: white;
    border-radius: 5px 5px 0px 0px;
    box-shadow: 0px 0px 9px #26242424;
    z-index: 2;
    display: grid;
    grid-template-areas:
        'gravatar name btn'
        'gravatar dept btn';
    grid-template-columns: 50px auto 50px;
    height: 35px;
    grid-template-rows: 50%;
    line-height: 1 !important;
}

.chat-mobile .chat-header {
    position: relative;
    top: 0;
    width: 100vw;
    z-index: 4;
    height: 55px;
    grid-template-columns: 80px auto 50px;
    line-height: unset !important;
}

.item-gravatar {
    grid-area: gravatar;
    position: relative;
}
.item-name {
    grid-area: name;
    position: relative;
    font-family: Muli;
    font-size: 16px;
    color: #333333;
}
.item-btn {
    grid-area: btn;
    position: relative;
}
.item-dept { grid-area: dept;}


.chat-mobile #chat-content .chat-header {
    border-radius: 0px !important;
}

.chat-header .btn-back-mobile {
    background: white;
    padding-left: 0;
    padding-top: 0;
    padding-bottom: 3px;
    padding-right: 4px;
    border: none !important;
    border-radius: 14px;
}

.chat-header .btn-back-mobile .bs-ico {
    color: #333333;
    position: relative;
    bottom: -9px;
}

.chat-header .btn-close_info {
    background: white;
    padding: 0;
    border: none !important;
    border-radius: 14px;
    height: 30px;
    width: 30px;
}

.chat-header .btn-close_info .bs-ico {
    color: #333333;
    position: relative;
    bottom: -2px;
}

.vertical-center {
  margin: 0;
  position: absolute;
  top: 50%;
  -ms-transform: translateY(-50%);
  transform: translateY(-50%);
  width: 100%;
}

.vertical-bottom {
  margin: 0;
  position: absolute;
  bottom: 0;
}

.chat-mobile span.info {
    max-width: 100px;
}

.chat-main {
    overflow-x: hidden;
    overflow-y: auto;
    margin-bottom: 6px;
    /* box-shadow: 0px 0px 9px #26242424; */
    border-radius: 0px 0px 5px 5px;
    word-break: break-word;
    background: white;
}

.chat-mobile .chat-main {
    margin-bottom: 0px !important;
    border-radius: 0px !important;
}

footer.incognito {
    background:  rgba(255, 102, 0, 0.5);
}

footer {
    background: #f7f8fc;
    padding: 0;
    text-align: center;
    box-shadow: 0px 0px 9px #26242424;
    border-radius: 5px;
    height: fit-content;
}

.chat-mobile footer {
    border-radius: 0px !important;
    width: 100vw;
}

textarea {
    max-height: 30vh;
    min-height: 40px;
    resize: none;
    border-radius: 5px;
    padding-top: 2px;
    padding-bottom: 2px;
    color: #707070;
    font-size: 0.9rem;
    font-stretch: 100%;
    font-weight: 600;
    text-rendering: optimizeLegibility;
    -webkit-font-feature-settings: "kern" 1;
    line-height: 19px;
}

@media screen and (max-width: 992px) {
    textarea {
        font-size: 16px;
    }
}

.col-btn-send {
    max-width: 35px !important;
    min-width: 35px !important;
    height: calc(100% - 60px);
}

.js-autoresize {
    font: normal normal normal 16px/20px Muli;
    letter-spacing: 0px;
    color: #707070;
    max-height: 245px;
    min-height: 64px;
    width: 100%;
    border: none;
    resize: none;
    background: transparent;
}
.card {
    border: none !important;
}

.card-body {
    overflow-y: auto !important;
}

.card-footer {
    border: none;
}

.shadow {
    background: #ffffff 0% 0% no-repeat padding-box;
    box-shadow: 0px 0px 9px #26242424;
    border-radius: 5px;
    opacity: 1;
}

#chat {
    min-height: 0px !important;
    max-height: 500px !important;
}

.card-chat-content {
    overflow: auto;
}

.translator {
    border-radius: 5px 5px 0px 0px;
    opacity: 1;
    height: 28px;
    padding-top: 5px;
    border: none;
    color: #333333;
    text-transform: capitalize;
    opacity: 1;
    text-align: left;
    font-size: 14.2px;
    line-height: 20px;
    font-weight: bold;
    text-rendering: optimizeLegibility;
}

.chat-mobile .translator {
    border-radius: 0px;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
}

.bg_grey {
    background: #f7f8fc 0% 0% no-repeat padding-box;
}

.bg_orange {
    background: #ff9f5f59 0% 0% no-repeat padding-box;
}

.bg_unset {
    background: #f7f8fc 0% 0% no-repeat padding-box;
}

.btn-send {
    height:40px;
    width: 40px;
    background: #0080FC;
    border-radius: 100%;
    padding-top: 9px
}



.btn-send .bs-ico {
    font-size: 21px;
    color: white;
}

::-webkit-scrollbar {
    width: 10px;
    height: 10px;
}
/* Track */

::-webkit-scrollbar-track {
    background: #fdfdfd;
}
/* Handle */

::-webkit-scrollbar-thumb {
    background: #a5b9d5;
    border-radius: 5px;
}
/* Handle on hover */

::-webkit-scrollbar-thumb:hover {
    background: #a5b9d5;
    cursor: pointer;
}

@media only screen and (max-width: 1279px) {
    .hide-small {
        display: none !important;
    }
}

.smaller {
    height: calc(100% - 90px);
}

@media only screen and (max-width: 1367px) {
    .smaller {
        height: calc(100% - 72px);
    }
}

.footer-btn {
    position: sticky;
    z-index: 1;
    bottom: 1px;
    height: 0px;
}

.btn-scroll {
    position: relative;
    top: -50px;
    border-radius: 100%;
    height: 40px;
    width: 40px;
    padding: 0;
    padding-top: 3px;
    border: none;
    box-shadow: 0px 0px 5px #26242459;
    color: white;
    background-color: #0080fc;
}

#textarea-translate {
    width: 100%;
    border: none;
    margin-top: -8px;
    font-size: 14px !important;
    padding: 0px 15px;
    font-style: italic;
}

</style>

<style lang="scss" scoped>
.output {
    width: calc(100% - 62px);
    border: none;
    overflow-y: auto;
    resize: none;
    margin-top: 5px;
    margin-left: 12px;

    &.ql-snow {
    border-top: none;
    }
}
</style>
