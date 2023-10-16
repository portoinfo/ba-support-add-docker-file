<template>
    <footer id="footer" v-if="chatsFooter.length && show && !isMobile">
        <div
            v-for="(row, index) in chatsFooter"
            :key="index"
            class="footer-active-chat"
            :class="{ answered: row.answered }"
        >
            <div v-if="showPopup(row.number)" class="popup">
                <div class="card m-0 p-0 border-0 h-100">
                    <div class="card-header pp-header">
                        <div class="row m-0 h-100">
                            <div class="col">
                                <span class="text-truncate">{{
                                    $t(row.name)
                                }}</span>
                            </div>
                            <div class="col pt-1" @click="redirectToChat(row)">
                                <span
                                    ><vue-material-icon name="call_made"
                                /></span>
                            </div>
                            <div class="col pt-1" @click="openPopup(row)">
                                <span><vue-material-icon name="close"/></span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pp-body h-100 p-0">
                        <div class="card h-100 p-0 border-0">
                            <div
                                class="card-body p-0 pp-content w-100"
                                v-chat-scroll
                            >

                                <component
                                    v-for="(message, index) in chat_history_robot"
                                    :key="index"
                                    :is="setMessageComponentRobot(message)"
                                    v-bind="setMessagePropsRobot(message, index)"
                                    :formatTime="UTCtoClientTZ2"
                                />

                                <message-type-questionary
                                    v-if="questionary.length"
                                    :chat="chat"
                                    :questionary="questionary"
                                    :formatTime="UTCtoClientTZ2"
                                />

                                <component
                                    v-for="(message, index) in chat_history"
                                    :key="index"
                                    :is="setMessageComponent(message.type)"
                                    v-bind="setMessageProps(message, index)"
                                />
                            </div>
                            <div
                                class="card-footer border-0 p-0 pr-2 pl-1 pb-1 pp-message"
                            >
                                <div v-if="files != ''" class="row p-0 m-0">
                                    <div class="col-12 p-0 m-0">
                                        <multiselect
                                            :hide-selected="true"
                                            :placeholder="
                                                $t(
                                                    'bs-placeholder-cancel-sending-the-file'
                                                )
                                            "
                                            v-model="files"
                                            :options="files"
                                            :openDirection="'bottom'"
                                            :multiple="true"
                                            :close-on-select="false"
                                            label="name"
                                            track-by="name"
                                        >
                                        </multiselect>
                                    </div>
                                </div>

                                <input
                                    type="file"
                                    id="attachments"
                                    ref="attachments"
                                    multiple
                                    @change="handleFilesUpload()"
                                    style="display: none"
                                />


                                <div id="toolbar">
                                    <button
                                        @click="setEditMode()"
                                        id="button-edit-mode"
                                    >
                                        <!-- :class="{'ql-active': editMode}" -->
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
                                    <button @click="upload">
                                        <input
                                            type="file"
                                            id="attachments"
                                            ref="attachments"
                                            multiple
                                            v-on:change="handleFilesUpload()"
                                            style="display: none"
                                        />
                                        <span
                                            style="font-size: 18px"
                                            class="material-icons-outlined"
                                        >
                                            upload_file
                                        </span>
                                    </button>
                                </div>
                                <div id="q-content">
                                    <quill-editor
                                        :class="{'edit-mode': editMode}"
                                        class="editor"
                                        ref="myQuillEditor"
                                        v-model="chat.content"
                                        :options="editorOption"
                                        id="quill-editor"
                                    />
                                    <div class="d-table">
                                        <div class="d-table-cell align-top">
                                            <span @click="sendMessage" id="send-btn" class="material-icons-outlined">send</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="card-footer pp-footer">
                        <div class="row m-0 h-100">
                            <div class="col">
                                <span class="text-truncate">{{
                                    row.department
                                }}</span>
                            </div>
                            <div class="col pb-1">
                                <span @click="upload"
                                    ><img src="images/icons/upload.svg"
                                /></span>
                            </div>
                            <div class="col pt-1">
                                <span :id="`popover-3`"
                                    ><vue-material-icon
                                        name="sentiment_very_satisfied"
                                /></span>
                            </div>
                            <b-popover
                                :target="`popover-3`"
                                :placement="'topcenter'"
                                title=""
                                triggers="hover focus"
                                :id="'pop3'"
                            >
                                <VEmojiPicker
                                    :showSearch="false"
                                    @select="concatEmoji"
                                />
                            </b-popover>
                        </div>
                    </div> -->
                </div>
            </div>

            <div class="row h-100 pr-4 pl-4 noselect">
                <div
                    @click="openPopup(row)"
                    class="p-0 col fac-avatar align-items-center mr-1"
                >
                    <span v-if="row.type == 'chat'" class="material-icons-two-tone">question_answer</span>
                    <span v-else-if="row.type == 'ticket'" class="material-icons-two-tone">email</span>
                </div>
                <div
                    @click="openPopup(row)"
                    class="p-0 col fac-avatar align-items-center mr-1"
                >
                    <gravatar
                        :email="row.email"
                        :status="$status.get(row.client_id)"
                        size="26px"
                        :name="$t(row.name)"
                        color="ligth"
                        :ba_acct_data="row.builderall_account_data"
                    />
                </div>
                <div
                    @click="openPopup(row)"
                    class="p-0 col fac-name d-flex align-items-center"
                >
                    <span class="text-left w-100 text-truncate pl-1 pr-1"
                        ><b>#{{ row.number }} </b>{{ $t(row.name) }}</span
                    >
                </div>
                <div class="p-0 col fac-close d-flex align-items-center">
                    <button
                        class="btn-fac-close p-0 text-white"
                        @click="closeFooterActiveChat(row)"
                    >
                        <vue-material-icon name="close" :size="18" />
                    </button>
                </div>
            </div>
        </div>
    </footer>
</template>

<script>
import { VEmojiPicker } from "v-emoji-picker";
import { mapState, mapMutations } from "vuex";

import Quill from 'quill';
import { quillEditor } from 'vue-quill-editor'
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'
import "quill-emoji/dist/quill-emoji.css";
import * as Emoji from "quill-emoji";
import { container, ImageExtend, QuillWatch } from 'quill-image-extend-module'
import BlotFormatter from 'quill-blot-formatter';
import Delta, { AttributeMap } from 'quill-delta';
export default {
    data() {
        return {
            editMode: false,
            editorOption: {
                placeholder: this.$t("bs-type-here") + "...",
                modules: {
                    ImageExtend: {
                        loading: true,
                        name: 'img',
                    },
                    toolbar: {
                        container: '#toolbar',
                    },
                    blotFormatter: {
                        // see config options below
                    },
                    actions: {
                        sendMessage: this.sendMessage,
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
            chatsFooter: Array(0),
            chatContent: Array(0),
            chat_number_opened: "",
            chat_history: [],
            ticket_history: [],
            chat_history_robot: [],
            questionary: [],
            number: "",
            messageComponent: {
                EVENT: "message-type-event",
                TEXT: "message-type-text",
                OPEN: "message-type-open-agent",
                CLOSE: "message-type-close-agent",
                FILE: "message-type-file-agent",
                IMAGE: "message-type-image-agent"
            },
            componentRobot: {
                TEXT: "robot-message-text",
                DEFAULT_BUTTON: "robot-message-button",
            },
            chat: {
                show: false,
                id: "",
                number: "",
                companyDepartmentId: "",
                comp_user_comp_depart_id_current: "",
                department: "",
                date: "",
                time: "",
                type: "",
                content: "",
                sideContent: "",
                client: {
                    name: "",
                    email: "",
                    location: "",
                    browser: "",
                    so: "",
                    ip: ""
                }
            },
            info_chat_id: "",
            cucd: this.session_user_cucd,
            time_inactivityMessage: "",
            departmentCommands: Array(0),
            hash:
                "98E549D9B6DA26962E7D1B2BEE8064918353F3CB979F9550C39F97B098DFD0DB",
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
            numberOfTabs: 0,
            incognito_mode: false,
            incognito_id: null,
            phTypeHere: this.$t("bs-type-here") + "...",
            showGravatar: true,
            url_prefix: `${
                this.session_user_permissions[0]["chat_admin"]
                    ? "full-chat-admin"
                    : "full-chat"
            }`,
            show: true,
            isMobile: false,
        };
    },
    props: {
        user: Object,
        session_user_cucd: Array,
        company_selected: "",
        restriction: Array,
        session_user_permissions: Array
    },
    components: {
        VEmojiPicker,
        quillEditor
    },
    watch: {
        "$store.state.chatsFooter": function() {
            // this.show = false;
            this.chatsFooter = this.$store.state.chatsFooter;
            // setTimeout(() => {
            //     this.show = true;
            // }, 4);
        },
        chatComponent(value) {
            if (value) {
                this.chat_number_opened = "";
                this.isActive = false;
                this.chat_history = [];
                this.questionary = [];
                this.clearActiveChat();
            }
        },
        alertAnotherAgenttookOverTheChat() {
            if (this.alertAnotherAgenttookOverTheChat.show) {
                Swal.fire({
                    icon: "info",
                    title: this.$t('bs-chat') + " #" + this.alertAnotherAgenttookOverTheChat.number,
                    text: this.$t("bs-another-agent-took-over-the-chat"),
                    heightAuto: false,
                    showCancelButton: false,
                    confirmButtonText: `Ok`
                });
            }
        }
    },
    mounted () {
        if(this.$store.state.company !== "null") {
            this.joinInProgressChannel();
            this.joinInQueueChannel();
        }
        this.onResize();
        var footer = document.getElementById("footer");

    },
    created() {
        Quill.register('modules/blotFormatter', BlotFormatter);
        Quill.register('modules/ImageExtend', ImageExtend)
        Quill.register("modules/emoji", Emoji);
        Quill.register('modules/actions', function() {});

        window.addEventListener("resize", this.onResize);
        if (window.location.pathname == "/select-company") {
            this.show = false;
        }
        this.$store.state.cucd = this.session_user_cucd;
        this.$root.$refs.Tabs = this;
        if (
            localStorage.getItem("chatsFooter") &&
            localStorage.getItem("chatsFooter") !== "[]"
        ) {
            this.chatsFooter = JSON.parse(localStorage.getItem("chatsFooter"));
            this.$store.state.chatsFooter = this.chatsFooter;
        }
        this.verifyActiveFooterChats();
        this.getExtensions();
        if (window.location.search == "?module=chat" || window.location.pathname !== "/full-chat" || window.location.pathname !== "/chat") {
            this.getDepartmentsByAgent();
        }
    },
    computed: {
        filter_my_chats: {
            get() {
                return this.$store.state.filter_my_chats;
            },
            set(value) {
                this.$store.commit("updateMyChatsFilter", value);
            }
        },
        filter_departments: {
            get() {
                return this.$store.state.filter_departments;
            },
            set(value) {
                this.$store.commit("updateChatFilterDepartments", value);
            }
        },
        chats_in_progress: {
            get() {
                return this.$store.state.chats_in_progress;
            }
        },
        alertAnotherAgenttookOverTheChat: {
            get() {
                return this.$store.state.alertAnotherAgenttookOverTheChat;
            },
            set() {

            }
        },
        chatComponent() {
           return this.$store.state.showChat;
        }
    },
    methods: {
        setEditMode() {
            this.editMode = !this.editMode
            var quill = document.getElementById('quill-editor');
            var container = quill.children[0];
            container.firstChild.focus();

        },
        onResize(e) {
            if ($(window).width() <= 992) {
                this.isMobile = true;
            } else {
                this.isMobile = false;
            }
            var footer = document.getElementById("footer");
        },
        getDepartmentsByAgent() {
            var vm = this;
            const prefix = "company-user-company-department";
            const api = `${
                vm.admin
                    ? "get-departments-by-company"
                    : prefix + "/get-department-by-agent"
            }`;

            axios.get(api).then(({ data }) => {
                var departments = [];
                data.forEach(element => {
                    departments.push(element)
                });
                //this.filter_departments = departments;
                //localStorage.setItem("filter_departments", JSON.stringify(departments));

                let local_filter = JSON.parse(
                    localStorage.getItem("filter_departments")
                );
                if (local_filter) {
                    this.filter_departments = local_filter
                } else {
                    this.filter_departments = departments;
                }
                if(departments.length) {
                    this.$store.commit("getChatsInProgress", this.url_prefix);
                    this.$store.commit("getChatsOnQueue", this.url_prefix);
                }
            });
        },
        joinInQueueChannel() {
            var vm = this;
            /** begin */
            const channel = `queue`;
            const event = `QueueUpdated`;
            /** join to the channel and listen events */
            Echo.leave(`${channel}.${this.$store.state.company}`);
            Echo.join(`${channel}.${this.$store.state.company}`).listen(
                event,
                e => {
                    this.inQueueChannelActions(e.item);
                }
            );
            /** end */
        },
        joinInProgressChannel() {
            var vm = this;
            /** begin */
            const channel = `full-chat.progress.`;
            const event = `FullChatProgress`;
            /** join to the channel and listen events */
            Echo.leave(`${channel}${this.$store.state.company}`);
            Echo.join(`${channel}${this.$store.state.company}`).listen(
                event,
                e => {
                        this.inProgressChannelActions(e.item);
                }
            );
            /** end */
        },
        inQueueChannelActions(item) {
            this.$store.commit("getChatsOnQueue", this.url_prefix);

            switch (item.action) {
                case "push":
                    if (item.transferred) {
                        this.closeFooterActiveChat(item);
                    }

                    break;

                case "splice":
                    const urlParams = new URLSearchParams(window.location.search);
                    const moduleParam = urlParams.get('module');

                    if(moduleParam == 'chat'){
                        if (this.$root.$refs.ModalClientHistory.chat.chat_id == item.chat_id){
                            this.$root.$refs.ModalClientHistory.clearModal();
                                $("#modalClientHistory").modal("hide");
                        }
                        if (!this.$root.$refs.FullChat2.showChat && this.$root.$refs.FullChat2.show_info && item.chat_id == this.$root.$refs.FullChat2.info_chat_id && item.chat_id != this.$root.$refs.FullChat2.chat_catched_id && !this.$root.$refs.FullChat2.loading2catchChat) {
                            this.$root.$refs.FullChat2.rs_mouse = 'leave'
                            this.$root.$refs.FullChat2.chat.show = false;
                        }
                    }
                    break;
            }
        },
        inProgressChannelActions(item) {
            let my_chat = this.cucd.findIndex(
                c => c.company_user_company_department_id === item.comp_user_comp_depart_id_current || this.user.id === item.user_agent_id
            );

            if (my_chat !== -1) {
                this.addFooterActiveChatWhenReceivingMessage(item);
            }

            //recupero o chat do evento e encontro a index dele no objeto
            let idx = this.chats_in_progress.findIndex(
                element => element.chat_id === item.chat_id
            );

            // se o "meus chats" estiver marcado só passa oq é meu, se nao passa tudo normal
            if (!this.filter_my_chats || (my_chat !== -1 && this.filter_my_chats)) {
                if (item.action) {
                    switch (item.action) {
                        case "took-over":
                            if (idx !== -1) {
                                let position = this.session_user_cucd.findIndex(
                                    element => element.company_user_company_department_id === item.comp_user_comp_depart_id_current
                                );

                                this.$store.commit("spliceChatsInProgress", idx);
                                this.$store.commit("pushChatsInProgress", item);

                                if (position !== -1) {
                                    if (window.location.search == "?module=chat" || window.location.pathname == "/full-chat") {
                                        this.$root.$refs.FullChat2.closeFooterActiveChat(item, true);
                                        this.$root.$refs.FullChat2.addFooterActiveChat(item, 'chat');
                                        this.$root.$refs.FullChat2.openChat(item);
                                    }
                                } else {
                                    this.closeFooterActiveChat(item);
                                }
                            }
                            break;
                        case "update":
                            if (idx !== -1) {
                                // se o chat for encontrado no array de chats em progresso
                                if (item.agent_answered) {
                                    // se foi o agente que mandou a mensagem, deixo o answered false para sumir a tarja vermelha
                                    this.$store.commit("setAnwserChatsInProgress", {index: idx, value: 0});
                                } else {
                                    // se foi o cliente que mandou a mensagem, deixo o answered true para aparecer a tarja vermelha
                                    this.$store.commit("setAnwserChatsInProgress", {index: idx, value: 1});
                                }
                            }
                            break;
                        case "splice":
                            if (idx !== -1) {
                                this.$store.commit("spliceChatsInProgress", idx);
                                this.closeFooterActiveChat(item);

                                const urlParams = new URLSearchParams(window.location.search);
                                const myParam = urlParams.get('module');

                                if(myParam == 'chat'){
                                    if (!this.$root.$refs.FullChat2.showChat && this.$root.$refs.FullChat2.show_info && item.chat_id == this.$root.$refs.FullChat2.info_chat_id) {
                                        this.$root.$refs.FullChat2.rs_mouse = 'leave'
                                        this.$root.$refs.FullChat2.chat.show = false;
                                    }
                                }

                            }
                            break;
                        case "transferred_to_another_agent":
                            let index = this.session_user_cucd.findIndex(
                                element => element.company_user_company_department_id === item.comp_user_comp_depart_id_current
                            );

                            if (index !== -1) {
                                if (window.location.search == "?module=chat" || window.location.pathname == "/full-chat") {
                                    this.$root.$refs.FullChat2.closeFooterActiveChat(item, true).then(
                                        () => {
                                            this.$root.$refs.FullChat2.addFooterActiveChat(item, 'chat');
                                            this.$root.$refs.FullChat2.openChat(item);
                                        }
                                    );
                                } else {
                                    this.closeFooterActiveChat(item).then(() => {
                                        this.addFooterActiveChat(item, 'chat');
                                    })
                                }

                                // Swal.fire({
                                //     icon: "info",
                                //     title: this.$t('bs-chat') + " #" + item.number,
                                //     text: this.$t("bs-transferred-to-you"),
                                //     heightAuto: false,
                                //     showCancelButton: false,
                                //     confirmButtonText: `Ok`
                                // });
                                this.$snotify.info(this.$t('bs-chat') + " #" + item.number+' '+this.$t("bs-transferred-to-you") + "!", {
                                    timeout: 4000,
                                    showProgressBar: false,
                                    pauseOnHover: true
                                });
                            }

                            if (idx !== -1) {
                                let i = this.filter_departments.findIndex(f => f.id === item.company_department_id);

                                if (i !== -1) {
                                    this.$store.commit("spliceChatsInProgress", idx);
                                    this.$store.commit("pushChatsInProgress", item);
                                } else {
                                    this.closeFooterActiveChat(item);
                                    this.$store.commit("spliceChatsInProgress", idx);
                                }

                            } else if (index !== -1) {
                                this.pushChatsInProgress(item);
                            } else {
                               this.pushChatsInProgress(item);
                            }

                        break;
                        case "splice_and_push":
                            let my_chat = this.session_user_cucd.findIndex(
                                element => element.company_user_company_department_id === item.comp_user_comp_depart_id_current
                            );

                            if (my_chat !== -1) {
                                if (window.location.search == "?module=chat" || window.location.pathname == "/full-chat") {
                                    this.$root.$refs.FullChat2.closeFooterActiveChat(item, true).then(
                                        () => {
                                            this.$root.$refs.FullChat2.addFooterActiveChat(item, 'chat');
                                                this.$root.$refs.FullChat2.openChat(item);
                                        }
                                    );
                                } else {
                                    this.closeFooterActiveChat(item).then(() => {
                                        this.addFooterActiveChat(item, 'chat');
                                    })
                                }
                            } else {
                                this.closeFooterActiveChat(item)
                            }

                            if (idx !== -1) {
                                let i = this.filter_departments.findIndex(f => f.id === item.company_department_id);

                                if (i !== -1) {
                                    this.$store.commit("spliceChatsInProgress", idx);
                                    this.$store.commit("pushChatsInProgress", item);
                                } else {
                                    this.closeFooterActiveChat(item);
                                    this.$store.commit("spliceChatsInProgress", idx);
                                }
                            }

                            break;
                    }
                } else {
                    this.pushChatsInProgress(item);
                }

            } else if (!my_chat !== -1 && this.filter_my_chats) {
                if (item.action) {
                    switch (item.action) {
                        case "took-over" || "transferred_to_another_agent":
                            if (idx !== -1) {
                                this.$store.state.alertAnotherAgenttookOverTheChat = { "show": true, "number": item.number };
                            }
                        break;
                    }
                }

                if (idx !== -1) {
                    this.closeFooterActiveChat(item);
                    this.$store.commit("spliceChatsInProgress", idx);
                }
            }
        },
        pushChatsInProgress(item) {
            let i = this.filter_departments.findIndex(
                f => f.id === item.company_department_id
            );
            if (i !== -1) {
                this.$store.commit("pushChatsInProgress", item);
            }
        },
        pushChatsOnQueue(item) {
            let i = this.filter_departments.findIndex(
                f => f.id === item.company_department_id
            );
            if (i !== -1) {
                this.$store.commit("pushChatsOnQueue", item);
            }
        },
        addFooterActiveChatWhenReceivingMessage(event) {
            switch (event.action) {
                case "update":
                    let index2 = this.$store.state.chatsFooter.findIndex(
                        item => item.chat_id === event.chat_id
                    );

                    if (index2 !== -1) {
                        if (event.agent_answered) {
                            // se foi o atendente
                            this.$store.state.chatsFooter[index2].answered = 0;
                            let old = this.$store.state.chatsFooter;
                            localStorage.setItem(
                                "chatsFooter",
                                JSON.stringify(old)
                            );
                            this.$store.state.chatsFooter = [];
                            this.$store.state.chatsFooter = old;
                        } else {
                            // se foi o cliente
                            this.$store.state.chatsFooter[index2].answered = 1;
                            let old = this.$store.state.chatsFooter;
                            localStorage.setItem(
                                "chatsFooter",
                                JSON.stringify(old)
                            );
                            this.$store.state.chatsFooter = [];
                            this.$store.state.chatsFooter = old;
                        }
                    } else {
                        delete event["company_user_company_department_id"];
                        delete event["content"];
                        delete event["id"];
                        delete event["subsidiary_id"];
                        delete event["email_verified_at"];
                        delete event["phone"];
                        delete event["language"];
                        delete event["hash_code"];
                        delete event["can_create_company"];
                        delete event["updated_at"];
                        delete event["deleted_at"];
                        delete event["created_by"];
                        delete event["updated_by"];
                        delete event["deleted_by"];
                        delete event["action"];
                        delete event["user_agent_id"];
                        delete event["agent_answered"];
                        event.type = "DEFAULT";
                        this.addFooterActiveChat(event, 'chat');
                    }
            }
        },
        setMessageComponentRobot(message) {
            switch (message.content.type) {
                case 'text':
                    return this.componentRobot['TEXT'];
                    break;

                case 'to_jump':
                case 'link':
                case 'create_chat':
                case 'create_ticket':
                case 'transfer_department':
                case 'solved':
                case 'unsolved':
                case 'default_button':
                    return this.componentRobot['DEFAULT_BUTTON'];
                    break;
            }
        },
        setMessagePropsRobot(message, index) {
            return {
                message: message.content,
                created_at: message.created_at,
                index: index,
                chat_history: this.chat_history_robot,
            };
        },
        showPopup(number) {
            if (this.chat_number_opened == number) {
                return true;
            } else {
                return false;
            }
        },
        openPopup(chat) {
            if (chat.type == 'chat') {
                let module = new URL(location.href).searchParams.get("module");
                if (module == "chat") {
                    this.$root.$refs.FullChat2.openChat(chat);
                }
                else {
                    if (this.chat_number_opened == chat.number) {
                        this.chat_number_opened = "";
                        this.isActive = false;
                        this.chat_history = [];
                        this.questionary = [];
                        this.clearActiveChat();
                    } else {
                        this.chat_number_opened = chat.number;
                        this.isActive = true;
                        this.chat_history = [];
                        this.questionary = [];
                        this.clearActiveChat();
                        this.setInfo(chat).then((result) => {
                            this.getContentOnLocalStorage(chat.number);
                            this.getChatHistory(chat.chat_id);
                            if (chat.company_department_id) {
                                this.getDepartmentComands(chat.company_department_id);
                            } else {
                                this.getDepartmentComands(
                                    this.chat.companyDepartmentId
                                );
                            }
                            let index = this.cucd.findIndex(
                                item =>
                                    item.company_department_id ===
                                    this.chat.companyDepartmentId
                            );

                            if (index !== -1) {
                                if (
                                    this.cucd[index]
                                        .company_user_company_department_id !==
                                    this.chat.comp_user_comp_depart_id_current
                                ) {
                                    this.incognito_mode = true;
                                } else {
                                    this.incognito_mode = false;
                                }
                                this.incognito_id = this.cucd[
                                    index
                                ].company_user_company_department_id;
                            } else {
                                this.incognito_mode = true;
                            }

                            Echo.join(`chat.${chat.chat_id}`).listen(
                                "MessageSent",
                                event => {
                                    this.chat_history.push(event.message);
                                }
                            );
                        })
                    }
                }
            } else if (chat.type == 'ticket') {
                if (window.location.search == "?module=ticket") {
                    this.getTicket(chat.id).then((ticket) => {
                        this.$root.$refs.FullTicket2.openChat(ticket);
                    });
                } else {
                    window.location = "/customer-service?module=ticket&id=" + chat.id;
                }
            }

        },
        getTicket(id) {
            return new Promise((resolve, reject) => {
                var vm = this;
                axios.get('tickets/get-ticket', {
                    params: {
                        id: id
                    }
                })
                .then(({data}) => {
                    if (data.success) {
                        resolve(data.ticket);
                    }
                })
            })
        },
        redirectToChat(chat) {
            window.location = "/customer-service?module=chat&id=" + chat.number;
        },
        addFooterActiveChat(chat, type) {
            chat.action = "add";
            axios
                .post("tabs", {
                    chat: chat,
                    type: type
                })
                .then(response => {});
        },
        closeFooterActiveChat(chat) {
            return new Promise((resolve, reject) => {
                if (window.location.search == "?module=chat" || window.location.pathname == "/full-chat") {
                    this.$root.$refs.FullChat2.closeFooterActiveChat(chat, 1);
                    resolve();
                } else {
                    chat.action = "remove";
                    axios.post("tabs", {
                        chat: chat
                    }).then(response => {
                        if (this.$root.$refs.TicketTicket2.itemselected.id == chat.id) {
                            if (typeof this.$root.$refs.TicketAnswer2 !== 'undefined') { //TicketView2
                                if(this.$root.$refs.TicketAnswer2.showView){
                                    this.$root.$refs.TicketAnswer2.showBody = true;
                                    this.$root.$refs.TicketAnswer2.showView = false;
                                    this.$root.$refs.TicketTicket2.showAnswer = !this.$root.$refs.TicketTicket2.showAnswer;
                                    this.$root.$refs.TicketTicket2.clearAndShowTable();
                                    this.$root.$refs.FullTicket2.rs_mouse = 'leave';
                                    return;
                                }
                            }
                            if (typeof this.$root.$refs.TicketAnswer2 !== 'undefined') { //TicketAnswer2
                                if(this.$root.$refs.TicketTicket2.showAnswer){
                                    this.$root.$refs.TicketTicket2.showAnswer = !this.$root.$refs.TicketTicket2.showAnswer;
                                    this.$root.$refs.TicketTicket2.clearAndShowTable();
                                    this.$root.$refs.FullTicket2.rs_mouse = 'leave';
                                    return;
                                }
                            }

                            this.$root.$refs.TicketTicket2.clearAndShowTable();
                            this.$root.$refs.FullTicket2.rs_mouse = 'leave';
                        }
                        resolve();
                    });

                    if (this.chat.chat_id == chat.chat_id) {
                        this.clearActiveChat();
                    }
                }
                // this.chatsFooter = [];
            });
        },
        setInfo(info) {
            return new Promise((resolve, reject) => {
                // info do chat
                this.info_chat_id = info.chat_id;
                this.chat.show = false;
                this.chat.chat_id = info.chat_id;

                if (info.companyDepartmentId) {
                    this.chat.companyDepartmentId = info.companyDepartmentId;
                } else {
                    this.chat.companyDepartmentId = info.company_department_id;
                }
                this.chat.comp_user_comp_depart_id_current =
                    info.comp_user_comp_depart_id_current;
                this.chat.department = info.department;
                this.chat.date = info.date;
                this.chat.time = info.time;
                this.chat.sideContent = info.content;
                this.chat.status = info.status;
                this.chat.type = info.type;
                this.chat.created_at = info.created_at;
                this.chat.number = info.number;
                // info do client
                this.chat.client.id = info.client_id;
                this.chat.client.name = info.name;
                this.chat.client.email = info.email;
                // this.chat.client.location = "Sarandi-RS";
                this.chat.client.browser = info.user_agent;
                // this.chat.client.so = "Ubuntu";
                // this.chat.client.ip = "143.255.93.154";
                this.chat.show = true;
                resolve();
            })

        },
        getContentOnLocalStorage(number) {
            if (
                localStorage.getItem("chatContent") &&
                localStorage.getItem("chatContent") !== "[]"
            ) {
                let index = this.chatContent.findIndex(
                    item => item.number === number
                );
                if (index !== -1) {
                    var content = this.chatContent[index].content;
                    if (this.chat.number == number) {
                        this.chat.content = content;
                    }
                }
            }
        },
        clearActiveChat() {
            if (this.chat.chat_id != "" && this.chat.chat_id != undefined) {
                this.putContentOnLocalStorage({
                    number: this.chat.number,
                    content: this.chat.content
                });
            }
            Echo.leave(`chat.${this.chat.chat_id}`);
            this.showChat = false;
            this.chat.show = false;
            this.chat.id = "";
            this.chat.chat_id = "";
            this.chat.number = "";
            this.chat.status = "";
            this.chat.companyDepartmentId = "";
            this.chat.comp_user_comp_depart_id_current = "";
            this.chat.department = "";
            this.chat.date = "";
            this.chat.time = "";
            this.chat.type = "";
            this.chat.content = "";
            this.chat.sideContent = "";
            this.chat.client.name = "";
            this.chat.client.email = "";
            this.chat.client.id = "";
            this.chat.client.location = "";
            this.chat.client.browser = "";
            this.chat.client.so = "";
            this.chat.client.ip = "";
            //incognito
            this.incognito_mode = false;
            this.incognito_id = null;
        },
        putContentOnLocalStorage(chat) {
            const storagedChats = JSON.parse(
                localStorage.getItem("chatContent")
            );
            let i = 0;
            let data = [];
            let exists = false;

            if (localStorage.getItem("chatContent")) {
                for (
                    let index = 1;
                    index <= Object.keys(storagedChats).length;
                    index++
                ) {
                    data[i] = storagedChats[i];
                    if (storagedChats[i]["number"] === chat.number) {
                        exists = true;
                        storagedChats[i]["content"] = chat.content;
                        localStorage.setItem(
                            "chatContent",
                            JSON.stringify(storagedChats)
                        );
                        this.chatContent = storagedChats;
                    }
                    i++;
                }
                if (!exists) {
                    data[i] = chat;
                    localStorage.setItem("chatContent", JSON.stringify(data));
                    this.chatContent = data;
                }
            } else {
                data[0] = chat;
                localStorage.setItem("chatContent", JSON.stringify(data));
                this.chatContent = data;
            }
        },
        verifyActiveFooterChats() {
            if (
                localStorage.getItem("chatsFooter") &&
                localStorage.getItem("chatsFooter") !== "[]"
            ) {
                axios
                    .post(`chat/verify-active-footer-chats`, {
                        chats: JSON.parse(localStorage.getItem("chatsFooter"))
                    })
                    .then(({ data }) => {
                        if (data.failed_chat_id.length) {
                            data.failed_chat_id.forEach(element => {
                                let index = this.chatsFooter.findIndex(
                                    item => item.chat_id === element
                                );
                                if (index !== -1) {
                                    this.chatsFooter.splice(index, 1);
                                }
                            });
                            localStorage.setItem(
                                "chatsFooter",
                                JSON.stringify(this.chatsFooter)
                            );
                        }
                    });
            }
        },
        LI(value) {
            if (value == null) {
                return "LI";
            }
            return value.substr(0, 2);
        },
        getChatHistory(id) {
            return new Promise((resolve, reject) => {
                var vm = this;
                axios
                    .get("ticket-chat-answer/agent/get-ticket-chat-answers", {
                        params: {
                            id: id,
                            reference: "chat_id"
                        }
                    })
                    .then(response => {
                        if (response.data.status) {
                            vm.questionary = response.data.result;
                        }
                    });
                axios
                    .get("chat-history/agent/get-chat-history", {
                        params: {
                            id: id
                        }
                    })
                    .then(({data}) => {
                        var itemsProcessed = 0;
                        vm.chat_history_robot = [];
                        vm.chat_history = [];
                        data.forEach(element => {
                            if (element.content_translated !== null) {
                                if(element.company_user_company_department_id !== null) {
                                    var content_translated = JSON.parse(element.content_translated);
                                    content_translated = content_translated[0].content;

                                    var content = element.content;

                                    element.content = content_translated;
                                    element.content_translated = content;

                                } else {
                                    var content_translated = JSON.parse(element.content_translated);
                                    content_translated = content_translated[0].content;
                                    element.content_translated = content_translated;
                                }
                            }
                            vm.setChatHistory(element).then(() => {
                                itemsProcessed++;
                                if(itemsProcessed === data.length) {
                                    resolve();
                                }
                            });
                        });
                    });
            });

        },
        setChatHistory(message) {
            return new Promise((resolve, reject) => {
                if (message.type == 'ROBOT') {
                    this.chat_history_robot.push(message);
                    resolve();
                } else if (message.type !== 'ROBOT'){
                    this.chat_history.push(message);
                    resolve();
                } else {
                    reject();
                }
            });
        },
        setMessageComponent(type) {
            return this.messageComponent[type];
        },
        setMessageProps(message, index) {
            return {
                comp_user_comp_depart_id_current: this.chat
                    .comp_user_comp_depart_id_current,
                message: this.chat_history[index],
                formatTime: this.UTCtoClientTZ2,
                index: index,
                chat_history: this.chat_history
            };
        },
        sendMessage() {
            if (this.chat.content.trim() !== "") {
                this.sendContent();
            }
            if (this.files != "") {
                this.sendUpload();
            }
            this.chat.content = "";
            this.files = [];
        },
        sendContent() {
            let message = this.chat.content;
            if (!this.incognito_mode) {
                this.storeMessage(
                    this.chat.comp_user_comp_depart_id_current,
                    message
                );
            } else if (this.incognito_id) {
                this.storeMessage(this.incognito_id, message);
            } else {
                const api = `company-user-company-department/add-agent-to-department`;
                axios
                    .post(api, {
                        company_department_id: this.chat.companyDepartmentId
                    })
                    .then(({ data }) => {
                        this.cucd = data.session_user_cucd;
                        this.incognito_id = data.cucdic;
                        this.storeMessage(data.cucdic, message);
                    });
            }
        },
        storeMessage($cucdic, $message) {

            // verificar se tem IMAGEM dentro do html do content
            var quotes = this.chat.content.split('"');
            var images = [];

            quotes.forEach(element => {
                if (element.substring(0, 10) == 'data:image') {
                    images.push(element);
                }
            });

            $message = $message.replace('><img', '><img  style="height: 150px;"');

            const api = `chat-history/agent/store`;
            axios
                .post(api, {
                    company_id: JSON.parse(this.company_selected).id,
                    id: this.chat.chat_id,
                    type: "TEXT",
                    content: $message,
                    content_translated: null,
                    is_agent: true,
                    is_incognito: this.incognito_mode,
                    company_department_id: this.chat.companyDepartmentId,
                    company_user_company_department_id: $cucdic,
                    time_for_inactivity_message: this.time_inactivityMessage,
                    chat: this.chat,
                    images: images
                })
                .then(({ data }) => {});
        },
        sendUpload() {
            const api = `chat/agent/upload`;
            const formData = new FormData();
            let files = this.files;

            for (var i = 0; i < files.length; i++) {
                let file = files[i];
                formData.append("files[" + i + "]", file);
            }

            formData.append("chat_id", this.chat.chat_id);
            formData.append("extImages", this.extImages);
            formData.append(
                "company_department_id",
                this.chat.companyDepartmentId
            );
            formData.append(
                "time_for_inactivity_message",
                this.time_inactivityMessage
            );
            formData.append("chat", this.chat);
            formData.append("company_id", JSON.parse(this.company_selected).id);

            axios
                .post(api, formData, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                })
                .then(({ data }) => {});
        },
        upload() {
            $("#attachments").click();
        },
        handleFilesUpload() {
            // o atributo 'attachments' recebe os arquivos enviados pelo onchange do input de uploads
            this.attachments = this.$refs.attachments[0].files;
            // faço um laço para verificar cada arquivo valido e adiciona-lo ao array que será enviado para API
            Array.from(this.attachments).forEach(attachment => {
                // reverto a string e pego os primeiros caracteres antes do primeiro '.' na string
                let reverse_ext = attachment["name"]
                    .split("")
                    .reverse()
                    .join("")
                    .split(".", 1)
                    .toString();
                // pego a string gerada e reverto ela novamente, assim gerando a extensão do arquivo. Ex: jpg, png etc..
                let ext = reverse_ext
                    .split("")
                    .reverse()
                    .join("");
                // verifico se a extensao do arquivo estiver incluso nas extensões permitidas
                if (
                    this.extensions.includes(ext) ||
                    this.extensions.includes(ext.toLowerCase())
                ) {
                    // caso o array de arquivos validos for diferente de vazio..
                    if (this.files.length) {
                        // é feito um laço para verificar se o arquivo que está sendo enviado já está no array de arquivos válidos
                        this.files.forEach(file => {
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
                    } else {
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
                        title: `${this.$t("bs-oops")}...`,
                        text:
                            this.$t("bs-invalid-file-format") +
                            " '" +
                            this.errors.join(", ") +
                            "'. " +
                            this.$t("bs-the-allowed-formats-are") +
                            this.extensions.join(", ") +
                            "."
                    });
                }
            });
            this.errors = [];
            setTimeout(function() {
                document.getElementById("input").focus();
            }, 0);
        },
        getExtensions() {
            this.extensions = this.extImages.concat(
                this.extDocuments,
                this.extSpreadsheets,
                this.extPresentation
            );
        },
        concatEmoji(emoji) {
            this.chat.content = this.chat.content.concat(emoji.data);
            document.getElementById("input").focus();
        },
        getDepartmentComands(company_department_id) {
            axios
                .get("get-department-settings", {
                    params: {
                        id: company_department_id
                    }
                })
                .then(({ data }) => {
                    let settings = JSON.parse(data[0]["settings"]);
                    let commands = settings.commands;
                    let inactivityMessage =
                        settings.quant_limity.inactivityMessage;
                    let default_commands = this.setDefaultCommands();
                    default_commands.forEach(element => {
                        commands.push(element);
                    });
                    this.departmentCommands = commands;
                    this.time_inactivityMessage = inactivityMessage;
                });
        },
        setDefaultCommands() {
            let commands = Array;
            let company = JSON.parse(this.company_selected);
            commands = [
                {
                    command: "cl_name",
                    description: this.chat.client.name,
                    status: null
                },
                {
                    command: "cl_email",
                    description: this.chat.client.email,
                    status: null
                },
                {
                    command: "company",
                    description: company.name,
                    status: null
                },
                {
                    command: "dept",
                    description: this.chat.department,
                    status: null
                }
            ];
            return commands;
        },
        replace() {
            var res = "";
            var input = "";
            var last_word = "";
            var l1 = 0; //inicializo com 0, é a variavel q vai receber o length da palavra 1;
            var l2 = 0; //inicializo com 0, é a variavel q vai receber o length da palavra 2;

            input = this.chat.content; //recebe todo o conteudo do input;
            last_word = input.slice(input.lastIndexOf(" ") + 1); // pega a última palavra do input;

            last_word = this.hash + last_word; //coloca uma hash na ultima palavra (pra nao substituir outras palavras);

            l1 = last_word.length; // pega o tamanho da ultima palavra com hash

            /*
      Obs: o tamanho é para saber se a ultima palavra é somente o comando...
      Ex: se o usuário digitar o comando juntamente com outra string, nao pode substiuir a palavra no meio de outra
      */

            input =
                input.substring(0, input.lastIndexOf(" ")) + " " + last_word; //substiui a ultima palavra por ela mesma com a hash;

            // loop em todos os comandos (do settings do department)
            this.departmentCommands.forEach(element => {
                element.command = this.hash + element.command; // o comando do settings é hasheado;
                l2 = element.command.length; // pega o tamanho do comando hasheado;

                //verifica se o comando hasheado está presente no input e se o tamanho é o mesmo;
                if (input.includes(element.command) && l1 === l2) {
                    // recebe todo o input com o comando substituido pela descrição;
                    res = input.replace(element.command, element.description);
                    this.chat.content = res;
                }

                //tira a hash do comando (pra nao dar erro no próximo loop);
                var command_with_hash = element.command;
                var command_without_hash = command_with_hash.replace(
                    this.hash,
                    ""
                );
                element.command = command_without_hash;
            });
        },
        UTCtoClientTZ2(h) {
            let h_format = moment(h, "YYYY-MM-DD HH:mm:ss").format(
                "YYYY-MM-DD HH:mm:ss"
            );
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
            let dateUTC = new Date(
                Date.UTC(year, month_integer, day, hour, minute, second)
            );
            let converted_time = dateUTC.toLocaleString("pt-BR", {
                timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone
            });

            var mt = require("moment-timezone");
            return mt(converted_time, "DD/MM/YYYY HH:mm:ss")
                .tz(Intl.DateTimeFormat().resolvedOptions().timeZone)
                .locale(this.user.language)
                .format("LT");
        }
    }
};
</script>

<style scoped>
.popup {
    position: fixed;
    bottom: 42px;
    height: 60%;
    min-height: 375px;
    width: 327px;
    /* min-width: 240px; */
    background: white;
    box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
    border-radius: 5px;
}

.popup:before {
    content: "";
    position: absolute;
    top: 100%;
    left: 13px;
    width: 0;
    border-top: 10px solid white;
    border-left: 10px solid transparent;
    border-right: 10px solid transparent;
    /* box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px; */
}

.popup .pp-header {
    padding: 0;
    width: 100%;
    height: 10%;
    background: #0294ff;
    border-radius: 5px 5px 0px 0px;
}

.popup .pp-header .row .col {
    text-align: center;
    display: table;
}

.popup .pp-header .row .col:nth-child(1) span {
    max-width: 230px;
}

.popup .pp-header .row .col:nth-child(2) {
    max-width: 10px !important;
    min-width: 10px !important;
    padding: 0;
    padding-right: 5px;
}

.popup .pp-header .row .col:nth-child(3) {
    max-width: 10px !important;
    min-width: 10px !important;
    padding: 0;
    padding-right: 5px;
}

.popup .pp-header .row .col span,
.popup .pp-header .row .col .material-icon {
    display: table-cell;
    vertical-align: middle;
    font-size: 16px;
    font-family: Muli;
    color: #f4f4f4;
}

.popup .pp-content {
    min-height: fit-content !important;
    max-height: fit-content !important;
    overflow: auto;
    overflow-x: hidden;
    white-space: normal !important;
    zoom: 90%;
    margin-bottom: 10px;
}

.popup .pp-footer {
    padding: 0;
    background: white;
    bottom: 0;
    height: 10%;
    width: 100%;
    border-top: 1px solid #d7dee6;
}

.popup .pp-footer .row .col {
    text-align: left;
    display: table;
}

.popup .pp-footer .row .col:nth-child(1) span {
    max-width: 230px;
    color: #acb7c6;
    font-size: 12px;
}

.popup .pp-footer .row .col:nth-child(2) {
    max-width: 10px !important;
    min-width: 10px !important;
    padding: 0;
    padding-right: 5px;
}

.popup .pp-footer .row .col:nth-child(3) {
    max-width: 10px !important;
    min-width: 10px !important;
    padding: 0;
    padding-right: 5px;
}

.popup .pp-footer .row .col:nth-child(3) span {
    color: #acb7c6;
}

.popup .pp-footer .row .col span,
.popup .pp-footer .row .col .material-icon {
    display: table-cell;
    vertical-align: middle;
    font-size: 16px;
    font-family: Muli;
    color: black;
}

.popup .pp-body .pp-message {
    background: white;
}

.popup .pp-body .pp-message .row {
    border: 1px solid #dddddd;
    border-radius: 3px;
}

.popup .pp-body .pp-message .row .col:nth-child(1) textarea {
    width: 100%;
    border: none;
    color: #707070;
    font-size: 14px;
    font-family: Muli;
}

.popup .pp-body .pp-message .row .col:nth-child(1) textarea::-webkit-scrollbar {
    width: 0px !important;
    height: 0px !important;
}

.popup
    .pp-body
    .pp-message
    .row
    .col:nth-child(1)
    textarea
    ::-webkit-scrollbar-track {
    background: transparent !important;
    border-radius: 0px !important;
}

.popup
    .pp-body
    .pp-message
    .row
    .col:nth-child(1)
    textarea
    ::-webkit-scrollbar-thumb {
    background: transparent !important;
    border-radius: 0px !important;
}

.popup
    .pp-body
    .pp-message
    .row
    .col:nth-child(1)
    textarea
    ::-webkit-scrollbar-thumb:hover {
    background: transparent !important;
}

.popup .pp-body .pp-message .row .col:nth-child(2) {
    min-width: 20px;
    max-width: 20px;
    align-items: flex-end !important;
    display: flex !important;
}

#footer {
    overflow-x: auto;
    overflow-y: hidden !important;
    white-space: nowrap;
    height: fit-content;
    width: 100%;
    z-index: 9;
    /* border-top: 1px solid #dddddd; */
    display: inline-block;
    padding-left: 3px;
    padding-top: 3px;
    padding-bottom: 3px;
    scroll-padding: 50px 0 0 50px;
}

/* @media only screen and (max-width: 992px) {
    #footer {
        width: calc(100% - 37px);
    }
} */

.footer-active-chat {
    max-width: 270px !important;
    min-width: 270px !important;
    display: inline-block !important;
    background: #ffffff 0% 0% no-repeat padding-box;
    border: 1px solid #dddddd;
    border-radius: 0px;
    opacity: 1;
    cursor: pointer;
    margin-right: 3px;
    padding-top: 4px;
    padding-bottom: 4px;
}

.footer-active-chat:hover {
    background: #f7f8fc;
}

.footer-active-chat.answered {
    background: #ff4872;
}

.footer-active-chat.answered .fac-name {
    color: #f4f4f4;
}

.footer-active-chat.answered .btn-fac-close {
    background: #fa4b57;
}

.footer-active-chat.active {
    box-shadow: 0px 0px 9px #25242624;
}

.footer-active-chat.active .fac-name {
    font-weight: bold;
}

.footer-active-chat.active {
    max-width: 272px !important;
    min-width: 272px !important;
    height: 52px !important;
}

/*
.footer-active-chat.active .btn-fac-close {
  background: #90d1ff;
} */
::-webkit-scrollbar {
    width: 10px;
    height: 10px;
}
/* Track */

::-webkit-scrollbar-track {
    background: #FDFDFD;
}
/* Handle */

::-webkit-scrollbar-thumb {
    background: #A5B9D5;
    border-radius: 5px
}
/* Handle on hover */

::-webkit-scrollbar-thumb:hover {
    background: #A5B9D5;
    cursor: pointer;
}

@media screen and (max-width: 992px) {
    ::-webkit-scrollbar {
        width: 4px;
        height: 4px;
    }
    /* Track */

    ::-webkit-scrollbar-track {
        background: transparent;
    }
    /* Handle */

    ::-webkit-scrollbar-thumb {
        background: #A5B9D5;
        border-radius: 5px
    }
    /* Handle on hover */

    ::-webkit-scrollbar-thumb:hover {
        background: #A5B9D5;
        cursor: pointer;
    }
}

.fac-avatar {
    max-width: 30px;
    min-width: 30px;
}

.fac-avatar .material-icons-two-tone {
    color: rgba(0, 0, 0, 0.5);
    position: relative;
    bottom: -3px;
}

.fac-name {
    font: normal normal 600 16px/21px Muli;
    letter-spacing: 0px;
    color: #656565;
    text-transform: capitalize;
    min-width: 130px;
    max-width: 1300px;
}

.fac-close {
    max-width: 30px;
    min-width: 30px;
}

.btn-fac-close {
    border: none !important;
    border-radius: 100%;
    height: 18px !important;
    width: 18px !important;
    background: #d2dae5;
}

.btn-fac-close:hover {
    background: #fa4b57;
}

#pop3 {
    z-index: 99999;
    margin-bottom: 110px;
    margin-left: 290px;
}
.multiselect {
    white-space: normal !important;
}

.multiselect__tags-wrap {
    white-space: break-spaces !important;
}

#q-content {
    display: grid;
    grid-template-columns: minmax(calc(100% - 50px), calc(100% - 50px)) 50px;
}

#send-btn {
    background: #0080FC;
    padding: 7px;
    color: white;
    border-radius: 100%;
}
</style>
