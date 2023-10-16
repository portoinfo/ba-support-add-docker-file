<template>
	<div>
        <div class='ba-flex ba-gp-1'>
            <div class="ba-w-100">
                <div class='ba-card-breadcrumb ba-card'>
                    <nav class='ba-breadcrumb'>
                        <ol class='ba-breadcrumb-ol'>
                            <li class='ba-breadcrumb-item ba-main-item'>
                                <a href='#'>
                                    <span class='ba-icon'><icons-custom name="logo-head-icon" width="22"></icons-custom></span>
                                </a>
                            </li>
                            <li class='ba-breadcrumb-item'>
                                <a href='#' @click="openchatNEW()">{{ $t('bs-chat') }}</a>
                            </li>
                        </ol>
                    </nav>
                </div>
                <br>
                <div class='ba-flex ba-gp-1 ba-h-100'>
                    <div v-if="!chatOpenNew">
                        <div class="left-out-wall ba-h-50">
                            <div class="ba-montserrat-700 ba-mg-l-1 caret fz-14 color-r">
                                All Chats (384) 
                                <!-- <icons-custom style="padding-left: 20%;" :active="false" name="down-icon" width="12"></icons-custom> -->
                            </div>
                            <div class='ba-sidebar box-shadow-remove' style="width: 182px;">
                                <ul class='ba-nav'>
                                    <li class='ba-nav-item '>
                                        <input type='checkbox' checked class='ba-nav-item-input-status ' id='ba-toggle-expandable-content-1'>
                                        <label class='ba-nav-item-expandable' style="width: 182px;" for='ba-toggle-expandable-content-1'>
                                            <div class='ba-nav-item-expandable-content grid-block'>
                                                <span class='ba-montserrat-500 fz-14'>Active (1)</span>
                                                <icons-custom style="padding-left: 39%;" :active="false" name="down-icon" width="12"></icons-custom>
                                            </div>
                                            <ul class='ba-nav-subitems'>
                                                <hr class='ba-hr-row'>
                                                <li class='ba-nav-subitem'>
                                                    <a href='#' class='ba-nav-subitem-link'>
                                                        <span class='ba-mg-l-1 ba-montserrat-500 fz-14'>In Queue (0)</span>
                                                    </a>
                                                </li>
                                                <li class='ba-nav-subitem'>
                                                    <a href='#' class='ba-nav-subitem-link ba-is-active'>
                                                        <span class='ba-mg-l-1 ba-montserrat-500 fz-14'>In Progress (1)</span>
                                                    </a>
                                                </li>
                                                <li class='ba-nav-subitem'>
                                                    <a href='#' class='ba-nav-subitem-link'>
                                                        <span class='ba-mg-l-1 ba-montserrat-500 fz-14'>Finished (231)</span>
                                                    </a>
                                                </li>
                                                <li class='ba-nav-subitem'>
                                                    <a href='#' class='ba-nav-subitem-link'>
                                                        <span class='ba-mg-l-1 ba-montserrat-500 fz-14'>Lost (152)</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div v-if="!chatOpenNew" class="ba-w-100">
                        <div class='ba-card'>
                            <div class="ba-top">
                                <span class="ba-title" style="font-size:12px">Chats</span>
                                <div class="ba-box-items ba-flex ba-gp-1 flex-column flex-md-row">
                                
                                    <button class='ba-btn ba-sm ba-light'>
                                        <icons-custom :active="false" width="15" name="filter-icon"></icons-custom>
                                        View per page
                                    </button>
                                    <button class='ba-btn ba-sm ba-light'>
                                        <icons-custom :active="false" width="15" name="filter-icon"></icons-custom>
                                        Chat Filter
                                    </button>
                                </div>
                            </div>
                            <table class='ba-table ba-sm ba-lines ba-w-100 center-t'>
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>#</th>
                                        <th>Client</th>
                                        <th>Operator/Department</th>
                                        <th>Duration</th>
                                        <th>Start</th>
                                        <th>End</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="index in 10" :key="index" @click="openchatNEW(index)">
                                        <td>
                                            <svg width="33" height="25" viewBox="0 0 33 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12.9999 19.5336C15.0566 19.5336 16.7239 17.8663 16.7239 15.8096C16.7239 13.7529 15.0566 12.0856 12.9999 12.0856C10.9432 12.0856 9.27588 13.7529 9.27588 15.8096C9.27588 17.8663 10.9432 19.5336 12.9999 19.5336Z" fill="#0072E1"/>
                                                <path d="M33 7.81116C33 11.6772 29.866 14.8112 26 14.8112C22.134 14.8112 19 11.6772 19 7.81116C19 3.94516 22.134 0.811157 26 0.811157C29.866 0.811157 33 3.94516 33 7.81116Z" fill="#FB6C76"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M17.0017 7.98985C15.7055 7.62213 14.3591 7.43323 13 7.43323C10.4101 7.43323 7.8665 8.11916 5.62777 9.42125C3.38905 10.7233 1.53512 12.5952 0.25457 14.8463C0.0877075 15.1408 0 15.4736 0 15.8122C0 16.1507 0.0877075 16.4835 0.25457 16.7781C1.53468 19.0294 3.38856 20.9013 5.62743 22.2031C7.8663 23.5049 10.4102 24.1901 13 24.1889C15.5898 24.1901 18.1337 23.5049 20.3726 22.2031C22.6018 20.9069 24.4493 19.0455 25.7289 16.8071C20.943 16.6655 17.0952 12.7877 17.0017 7.98985ZM16.1034 11.1653C15.1848 10.5515 14.1048 10.2238 13 10.2238C11.5185 10.2238 10.0977 10.8124 9.05009 11.8599C8.00251 12.9075 7.41398 14.3284 7.41398 15.8098C7.41398 16.9147 7.7416 17.9947 8.3554 18.9133C8.9692 19.8319 9.84161 20.5479 10.8623 20.9707C11.883 21.3934 13.0062 21.5041 14.0898 21.2885C15.1734 21.073 16.1687 20.541 16.9499 19.7598C17.7311 18.9785 18.2631 17.9832 18.4787 16.8996C18.6942 15.816 18.5836 14.6929 18.1608 13.6722C17.738 12.6515 17.022 11.7791 16.1034 11.1653Z" fill="#0072E1"/>
                                            </svg>
                                            <svg width="21" height="23" viewBox="0 0 21 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M20.9966 15.157C20.9943 14.9663 20.9924 9.84994 20.9924 9.84994C20.9924 8.31969 19.1466 7.80924 17.9947 8.40279C17.7322 7.05915 15.7523 6.68187 14.5823 7.33181C14.1964 6.1551 12.4134 5.82531 11.2899 6.39511V1.71276C11.2894 0.768274 10.3265 0 9.14248 0C7.95753 0 6.99366 0.768274 6.99366 1.71276V11.681L4.53161 9.9245C3.61288 9.269 2.7742 9.13137 2.23199 9.13137C1.2872 9.13137 0.494597 9.54463 0.16229 10.2098C-0.269615 11.0752 0.172529 12.1195 1.34584 13.0032C1.38773 13.0343 1.46638 13.0863 1.57343 13.1553C2.20221 13.5604 3.87212 14.6365 4.08993 15.9353C4.56745 18.3054 6.38164 19.1349 7.70621 19.741C7.7602 19.7655 7.81279 19.7896 7.86538 19.8138V22.629C7.86538 22.8338 8.07342 23 8.33079 23H18.6863C18.9437 23 19.1517 22.8338 19.1517 22.629V19.813C21.0269 18.1986 21.0148 16.9803 20.9966 15.157Z" fill="#0072E1"/>
                                            </svg>
                                        </td>
                                        <td>900</td>
                                        <td class='ba-double'>
                                            <div class='ba-title'>Marlos</div>
                                            <div class='ba-subtitle'>Fernandes</div>
                                        </td>
                                        <td class='ba-double'>
                                            <div class='ba-title'>Administrador</div>
                                            <div class='ba-subtitle'>Suport BR</div>
                                        </td>
                                        <td>1:18:23</td>
                                        <td>1/4/2023 - 1:18 PM</td>
                                        <td>1/4/2023 - 1:18 PM</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="ba-w-100">
                                <nav class='ba-pagination ba-md'>
                                    <ul class='ba-list' style="justify-content: center;">
                                        <li class='ba-nav-item'>
                                            <a class='ba-back' href='#'>
                                                <span class='ba-icon'>
                                                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-arrow-left' viewBox='0 0 16 16'>
                                                        <path fill-rule='evenodd' d='M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z'/>
                                                    </svg>
                                                </span>
                                            </a>
                                        </li>
                                        <li class='ba-nav-item'>
                                            <a class='ba-link ba-is-active' href='#'>1</a>
                                        </li>
                                        <li class='ba-nav-item'>
                                            <a class='ba-link' href='#'>2</a>
                                        </li>
                                        ...
                                        <li class='ba-nav-item'>
                                            <a class='ba-link' href='#'>9</a>
                                        </li>
                                        <li class='ba-nav-item'>
                                            <a class='ba-link' href='#'>10</a>
                                        </li>
                                        <li class='ba-nav-item'>
                                            <a class='ba-next' href='#'>
                                                <span class='ba-icon'>
                                                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-arrow-right' viewBox='0 0 16 16'>
                                                        <path fill-rule='evenodd' d='M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z'/>
                                                    </svg>
                                                </span>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class='ba-card' v-if="chatOpenNew">
                        <div class="ba-montserrat-700 ba-mg-l-1 caret fz-14 color-r">
                            <div class='ba-flex ba-gp-1'>
                                <div>
                                    Chats
                                </div>
                                <div class='ba-box-search-input'>
                                    <input style="width:118px;" type='search' class='ba-input' placeholder='Search...' />
                                    <button class='ba-button'>
                                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-search' viewBox='0 0 16 16'>
                                            <path d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <br>
                            <div class='ba-card shaddow ba-mg-t-1'>
                                <div class='ba-flex ba-gp-1'>
                                    <div>
                                        <gravatar
                                            :email="'marlos_gpi@live.com'"
                                            :status="'online'"
                                            size="20px"
                                            :name="'marlos'"
                                            color="light"
                                            
                                        /> 
                                    </div>
                                    <div class='ba-mg-l-1'>
                                        Shelly Turner<br>
                                        Hello! I would like t...
                                    </div>
                                </div>
                            </div>
                            <div class='ba-card borderc ba-mg-t-1'>
                                <div class='ba-flex ba-gp-1'>
                                    <div>
                                        <gravatar
                                            :email="'marlos_gpi@live.com'"
                                            :status="'online'"
                                            size="20px"
                                            :name="'marlos'"
                                            color="light"
                                            
                                        /> 
                                    </div>
                                    <div class='ba-mg-l-1'>
                                        Shelly Turner<br>
                                        Hello! I would like t...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="chatOpenNew" class="ba-w-100">
                        <div class='ba-card ba-h-100'>
                            <div class='ba-flex ba-gp-1'>
                                <div class="ba-mg-r-1">
                                    <gravatar
                                        :email="'marlos_gpi@live.com'"
                                        :status="'online'"
                                        size="40px"
                                        :name="'marlos'"
                                        color="light"
                                        
                                    /> 
                                </div>
                                <div class='ba-mg-l-1'>
                                    <span class="ba-title">Giulia Neitzke</span>
                                    <br>
                                    Active now
                                </div>
                            </div>
                            <hr class='ba-hr-row' style="margin-top: -8px;">

                            <div class='ba-w-100 ba-h-100' style="border: 1px solid red;">
                                <!-- <span class="card-body" v-for="(item, index) in chat_history_before" :key="index+'axa'">
                                    <span v-if="item.type == 'TEXT'">
                                        <message-type-text 
                                        :comp_user_comp_depart_id_current="item.company_user_company_department_id"
                                        :message="$t(item)" 
                                        :formatTime="formatTime"></message-type-text>
                                    </span>
                                    <span v-if="item.type == 'EVENT'">
                                        <message-type-event
                                        :message="$t(item)" 
                                        :formatTime="formatTime"></message-type-event>
                                    </span>
                                    <span v-if="item.type == 'OPEN'">
                                        <message-type-open-agent
                                        :message="$t(item)" 
                                        :formatTime="formatTime"></message-type-open-agent>
                                    </span>
                                    <span v-if="item.type == 'IMAGE'">
                                        <message-type-image-agent
                                        :message="$t(item)" 
                                        :formatTime="formatTime"></message-type-image-agent>
                                    </span>
                                    <span v-if="item.type == 'FILE'">
                                        <message-type-file-agent
                                        :message="$t(item)" 
                                        :formatTime="formatTime"></message-type-file-agent>
                                    </span>
                                </span> -->


                                <!-- {
                                    'chat_id': 'b0s4NUl6TjVyYXpXdGQzTjlRVlZOQT09',
                                    'number': 92158,
                                    'company_id': 'UTJINmpiNG94U3dLYkhhaWJkWWNWUT09',
                                    'company_department_id': 'NkE2VEhXeHdvelRrbVNaZ1FzUmYwQT09',
                                    'comp_user_comp_depart_id_current': 'emt5bzNyUStuSU8rcENnTWNBKzkyZz09',
                                    'status': 'IN_PROGRESS',
                                    'description': '<p>13</p>',
                                    'type': 'chat',
                                    'user_agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36',
                                    'turn_into_ticket_at_closing': 0,
                                    'date': '21/08/2023',
                                    'time': '18:36:09',
                                    'created_at': '2023-08-21T18:36:09.000000Z',
                                    'department': 'Bingo',
                                    'dep_type': null,
                                    'client_id': 'aXlxamwyUlVXVm5QZkswN3l2RFRUZz09',
                                    'builderall_account_data': '{\'is_vip\':false,\'id\':\'1634790\',\'uuid\':\'1f25f051-7bd1-4fb3-af57-39e72aa798c0\'}',
                                    'name': 'Marlos',
                                    'email': 'marlos_gpi@live.com',
                                    'operator': 'adminMaster',
                                    'operator_id': 'NTNPWUEwVnNsbXd0c0o0ZHJ6cHRXdz09',
                                    'operator_email': 'adminMaster@live.com',
                                    'answered': 0,
                                    'category_chat_id': null,
                                    'action': 'add',
                                    'user_id': 19
                                } -->


                                <message-type-text
                                        :comp_user_comp_depart_id_current="'emt5bzNyUStuSU8rcENnTWNBKzkyZz09'"
                                        :message="{
                                            'name': 'Marlos',
                                            'content': 'Olá meu nome é marlosMarlos',
                                            'hidden_for_client': 1,
                                            'company_user_company_department_id': 'emt5bzNyUStuSU8rcENnTWNBKzkyZz09',
                                            'client_email': 'marlos_gpi@live.com',
                                            'user_email': 'adminMaster@live.com',
                                            'client_id': '1',
                                            'client_name': 'Marlos',
                                            'user_name': 'Admin',
                                            'builderall_account_data': '',
                                        }"
                                :formatTime="formatTime('2021-03-16 18:59:36')">
                            </message-type-text>

                            </div>
                            <div class='ba-w-100' style="border: 1px solid red;">
                                <div class="ba-box-search-input ba-white">
                                    <input type='search' class='ba-input ba-w-100' style="height: 100px;" placeholder='Search...' />
                                    <button class='ba-button'>
                                        <icons-custom width="16" name="send-icon"></icons-custom>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="showInfoChat" class="right-out-wall">
                <card-info type="chat"></card-info>
            </div>
        </div>
        <br>
   </div>
</template>

<script>
import { VEmojiPicker } from "v-emoji-picker";
import { mapState, mapMutations } from "vuex";
import Treeselect from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'
import { disableBodyScroll, enableBodyScroll, clearAllBodyScrollLocks } from 'body-scroll-lock';

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

import iconsCustom from '../../../util/icons/iconsCustom.vue';
import Gravatar from '../../../../../../components/tools/Gravatar.vue';
import CardInfo from '../../service/cards/card-info.vue';
import MessageTypeText from '../chat/messages/MessageTypeText2.vue';
export default {
    components: {
        VEmojiPicker,
        Treeselect,
        quillEditor,
        iconsCustom,
        Gravatar,
        CardInfo,
        MessageTypeText,
    },
    props: {
    },
    data() {
        return {
            translateIsActive: false,
            showTranslate: false,
            modalTranslateShowed: false,
            aux_dept: [],
            tree_items: [
                {
                    text: this.$t("bs-all-chats"),
                    state: { expanded: true },
                    data: { id: 'all' },
                    children: [
                        {
                            text: this.$t("bs-active-s"),
                            state: { expanded: true},
                            data: {id: 'active'},
                            children: [
                                {
                                    text: this.$t("bs-in-queue"),
                                    state: { selected: false },
                                    data: { icon: "query_builder", ml: 1, id: 'queue' }
                                },
                                {
                                    text: this.$t("bs-in-progress"),
                                    state: { selected: false },
                                    data: { icon: "question_answer", ml: 1, id: 'in_progress' }
                                },
                            ]
                        }
                    ]
                },
                {
                    text: this.$t("bs-finished-s"),
                    state: { selected: false },
                    data: { icon: "done", id: 'resolved' }
                },
                {
                    text: this.$t("bs-lost-s"),
                    state: { selected: false },
                    data: { icon: "report_problem", id: 'canceled'}
                }
            ],
             /** props */
            chat_queue_full_control: '',
            permissions: '',
            cucd: this.session_user_cucd,
            url_prefix: '',
            /** chat */
            questionary: [],
            chat_history: [],
            chat_history_robot: [],
            chat: {
                show: false,
                id: "",
                number: "",
                companyDepartmentId: "",
                //comp_user_comp_depart_id_current: "",
                department: "",
                description: "",
                date: "",
                time: "",
                type: "",
                content: "",
                sideContent: "",
                client: {
                    id: "",
                    name: "",
                    email: "",
                    location: "",
                    browser: "",
                    so: "",
                    ip: ""
                },
                turn_into_ticket_at_closing: false
            },
            // chat tabs of footer
            chatsFooter: Array(0),
            chatContent: Array(0),
            clientChatHistory: [],
            clientTicketHistory: [],
            footerActiveChat: false,
            /** file upload */
            file_exists: false,
            attachments: [],
            files: [],
            errors: [],
            extensions: [],
            extImages: ["jpg", "jpeg", "png", "bmp"],
            extDocuments: ["docx", "doc", "pdf", "xps", "txt", "odt", "svg"],
            extSpreadsheets: ["xlsx", "xls", "xlt", "csv", "ods"],
            extPresentation: ["pptx", "ppt", "pot", "ppsx", "pps", "odp"],
            /** show */
            showChat: false,
            showTableQueue: false,
            showTableInProgress: false,
            showTableTransferred: false,
            showTableClosed: false,
            showTableResolved: false,
            showTableCanceled: false,
            isMobile: false,
            hidden: false,
            hideOnSmall: false,
            //tables badge notifications
            notify: {
                onQueue: false,
                inProgress: false,
                transferred: false,
                closed: false,
                resolved: false,
                canceled: false
            },
            /** tables chats */
            chats_transferred: [],
            chats_closed: [],
            chats_resolved: [],
            chats_canceled: [],
            /** keys */
            key_queue: 0,
            key_progress: 0,
            key_transferred: 0,
            key_closed: 0,
            key_resolved: 0,
            key_canceled: 0,
            //tables count
            countOnQueue: 0,
            countTransferred: 0,
            countClosed: 0,
            countResolved: 0,
            countCanceled: 0,
            countInProgress: 0,
            /** message components */
            messageComponent: {
                EVENT: "message-type-event",
                TEXT: "message-type-text",
                OPEN: "message-type-open-agent",
                CLOSE: "message-type-close-agent",
                FILE: "message-type-file-agent",
                IMAGE: "message-type-image-agent",
                FAQ_ROBOT: "message-type-faq-robot"
            },
            componentRobot: {
                TEXT: "robot-message-text",
                DEFAULT_BUTTON: "robot-message-button",
            },
            // modals
            transfer_to_agent: false,
            departments_to_transfer: [],
            agents_to_transfer: [],
            selected_department: null,
            selected_agent: null,
            ticket_description: "",
            ticket_description_edit: "",
            ticket_comments: null,
            // departments
            departments: [],
            company_department: [],
            // actions
            actions: {},
            take_on_chat: false,
            //incognito
            incognito_mode: false,
            incognito_id: null,
            //loading
            skip: 0,
            take: 30,
            // commands
            departmentCommands: Array(0),
            time_inactivityMessage: "",
            // timezone
            tz: "",
            country_bw: "",
            country_sys: '',
            //
            info_chat_id: "",
            // status
            //online_users: Array(0),
            full_list: Boolean,
            textReason: "",
            rs_mouse: "",
            show_filter: false,
            // define the default value
            filter_dep_value: null,
            // define options
            filter_dep_options: [ {
            id: 'a',
            label: 'a',
            children: [ {
                id: 'aa',
                label: 'aa',
            }, {
                id: 'ab',
                label: 'ab',
            } ],
            }, {
            id: 'b',
            label: 'b',
            }, {
            id: 'c',
            label: 'c',
            } ],
            loading_messages: false,
            info_minimized: false,
            loading2catchChat: false,
            page: 1,
            chat_catched_id: "",
            dropdownActionWithKeyboardOpened: false,
            isPreview: false,
            chatPreview: {},
            forceReloadQuestionary: true,
            loading_counters: true,
            showActions: false,
            showAgentForm: false,
            quillEditorTicketOptions: {
                placeholder: this.$t('bs-type-your-message-here'),
                modules: {
                    toolbar: [
                        [
                        'bold', 'italic', 'underline', 'strike',        
                        'blockquote', 'code-block',
                        'link', 'image',                                
                        { 'list': 'ordered'}, { 'list': 'bullet' },
                        { 'indent': '-1'}, { 'indent': '+1' },          
                        { 'direction': 'rtl' },                         
                        { 'size': ['small', false, 'large', 'huge'] },  
                        { 'color': [] }, { 'background': [] },          
                        { 'align': [] },
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
            showCategory: false,
            rejectCheck: false,
            itemselected_settings: [],
            showDatabase: false,
            showModalReason: false,

            showInfoChat: true,
            chatOpenNew: true,
            is_admin: '',
            session_user: '',
            session_user_company: '',
            session_user_cucd: '',
            session_user_departments: '',
            session_user_permissions: '',
            restriction: '',
        };
    },
    created() {
        if (this.restriction && (this.restriction[0].chat_admin || this.restriction[0].chat_alllist)) {
            this.full_list = 1;
        } else {
            this.full_list = 0;
        }
        this.$store.state.cucd = this.session_user_cucd;
        this.openRedirectedChat();
        this.$root.$refs.FullChat2 = this;
        // window.onbeforeunload = function () {
        //   return "Você tem certeza que deseja sair?";
        // };
        this.getCountry();
        // localStorage.clear();
        localStorage.setItem("chatContent", JSON.stringify(this.chatContent));
        this.getDepartmentsByAgent();
        this.connectToChannels();
        if (
            localStorage.getItem("chatsFooter") &&
            localStorage.getItem("chatsFooter") !== "[]"
        ) {
            this.footerActiveChat = true;
        } else if (!localStorage.getItem("chatsFooter")) {
            this.footerActiveChat = false;
        }
        this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
        //this.getAgentTablesCount();
        //this.getActions();
        this.getExtensions();

        //this.verifyActiveFooterChats();

        window.addEventListener("resize", this.onResize);
        Quill.register('modules/blotFormatter', BlotFormatter);
        Quill.register('modules/ImageExtend', ImageExtend)
        Quill.register('modules/imageCompress', ImageCompress);
        Quill.register('modules/autoLinks', AutoLinks);


        this.is_admin = this.$store.state.is_admin;
        this.session_user = this.$store.state.session_user;
        this.session_user_company = this.$store.state.session_user_company;
        this.session_user_cucd = this.$store.state.session_user_cucd;
        this.session_user_departments = this.$store.state.session_user_departments;
        this.session_user_permissions = this.$store.state.session_user_permissions;
        this.restriction = this.$store.state.restriction;
        this.chat_queue_full_control = this.session_user_permissions[0]["chat_queue_full_control"];
        this.permissions = this.session_user_permissions[0];
        this.url_prefix = `${
                this.session_user_permissions[0]["chat_admin"]
                    ? "full-chat-admin"
                    : "full-chat"
        }`
        this.country_sys = this.session_user.language.split("_")[1],

        console.log(this.session_user_permissions);
        console.log(this.$store.state.session_user_permissions);
    },
    mounted() {
        this.$root.$on('bv::dropdown::show', bvEvent => {
            if (bvEvent.componentId == "dropdown-action") {
                var el = document.getElementById('input');
                var input_focused = el === document.activeElement;
                if (input_focused) {
                    this.dropdownActionWithKeyboardOpened = true;
                    el.focus();
                } else {
                    this.dropdownActionWithKeyboardOpened = false;
                }
            }
        })

        this.$root.$on('bv::dropdown::hide', bvEvent => {
            if (bvEvent.componentId == "dropdown-action") {
                if(this.dropdownActionWithKeyboardOpened) {
                    bvEvent.preventDefault();
                    this.dropdownActionWithKeyboardOpened = false;
                }
            }
        })


        this.countInProgress = this.$store.state.chats_in_progress.length;
        this.countOnQueue = this.$store.state.chats_in_queue.length;
        // var objs = [
        //     { id: '5', last_nom: 'Jamf'     },
        //     { id: '9',    last_nom: 'Bodine'   },
        //     { id: '2', last_nom: 'Prentice' }
        // ];

        // objs.sort((a,b) => (a.id > b.id) ? 1 : ((b.id > a.id) ? -1 : 0))

        // console.log(objs);
        this.onResize();

        this.showTableComponent('queue');

        clearAllBodyScrollLocks();
    },
    methods: {
        openchatNEW(index){
            var vm = this;
            vm.chatOpenNew = !vm.chatOpenNew;
        },
        closeInfos(){
            this.showInfoChat = false;
        },
        formatTime(h) {
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
            let dateUTC = new Date(Date.UTC(year, month, day, hour, minute, second));
            //pega o fuso do cliente. Ex: "America/Sao_Paulo"
            let client_tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
            let converted_time = dateUTC.toLocaleString("pt-BR", {
                timeZone: client_tz,
            });
            return moment(converted_time, "YYYY-MM-DD HH:mm:ss").format(
                "HH:mm YYYY-MM-DD"
            );
        },
        modalDatabase(){
            this.showDatabase = !this.showDatabase;
        },
        ...mapMutations(["tabs"]),
        updateMessage(index, content) {
            this.chat_history[index].content_translated = content;
        },
        setDepts() {
            var aux = [];
            var n = this.aux_dept;
            this.departments.forEach(e1 => {
                n.forEach(e2 => {
                    if (e1.id == e2) aux.push(e1);
                });
            });
            this.company_department = aux;
        },
        shownActions() {
            if(this.dropdownActionWithKeyboardOpened) {
                var el = document.getElementById('input');
                el.focus();
            }
        },
        onNodeSelected(node) {
            switch (node.data.id) {
                case 'queue':
                    this.showTableComponent('queue');
                    break;
                case 'in_progress':
                    this.showTableComponent('inProgress');
                    break;
                case 'resolved':
                    this.showTableComponent('resolved');
                    break;
                case 'canceled':
                    this.showTableComponent('canceled');
                    break;
            }
            this.$refs.popover_menu.$emit('close')
        },
        returnCountByStatus(id) {
            switch (id) {
                case 'all':
                    var count = Number(this.countOnQueue) + Number(this.countInProgress) + Number(this.countCanceled) + Number(this.countResolved);
                    break;
                case 'active':
                    var count = Number(this.countOnQueue) + Number(this.countInProgress);
                    break;
                case 'queue':
                    var count = Number(this.countOnQueue);
                    break;
                case 'in_progress':
                    var count = Number(this.countInProgress);
                    break;
                case 'resolved':
                    var count = Number(this.countResolved);
                    break;
                case 'canceled':
                    var count = Number(this.countCanceled);
                    break;
            }

            if (count > 0) {
                return count;
            } else {
                return "0";
            }
        },
        openRedirectedChat() {
            let number = new URL(location.href).searchParams.get("id");
            if (number !== null) {
                let chatsFooter = JSON.parse(
                    localStorage.getItem("chatsFooter")
                );
                let i = chatsFooter.findIndex(item => item.number == number);
                if (i !== -1) {
                    let chat = chatsFooter[i];
                    setTimeout(() => {
                        this.openChat(chat);
                    }, 2000);
                    const url = new URL(window.location.href);
                    const params = new URLSearchParams(url.search.slice(1));
                    params.delete("id");
                    window.history.replaceState(
                        {},
                        "",
                        `${window.location.pathname}?module=chat`
                    );
                }
            }
        },
        getCountry() {
            fetch("https://extreme-ip-lookup.com/json/")
                .then(res => res.json())
                .then(response => {
                    if (response.countryCode) {
                        this.country_bw = response.countryCode;
                    } else {
                        this.country_bw = Intl.DateTimeFormat()
                            .resolvedOptions()
                            .locale.split("-")[1]; //BR
                    }
                })
                .catch((data, status) => {
                    this.country_bw = Intl.DateTimeFormat()
                        .resolvedOptions()
                        .locale.split("-")[1]; //BR
                });
        },
        /** @SEND */
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
            /** if incognito mode is not set, send the message with the actual agent of the chat */
            if (!this.incognito_mode) {
                this.storeMessage(
                    this.chat.comp_user_comp_depart_id_current,
                    message
                );
                /** else if incognito id is set, send the message with the incognito agent of the chat */
            } else if (this.incognito_id) {
                this.storeMessage(this.incognito_id, message);
                /** else attach incognito agent to the chat department and send the message*/
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
            formData.append("company_id", this.session_user_company.id);

            if (this.incognito_mode) {
                formData.append("is_incognito", this.incognito_mode);
            }

            axios
                .post(api, formData, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                })
                .then(({ data }) => {});
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
            var language = this.session_user.language.split('_')[0];
            var content_translated = [{'language': language, 'content': $message}];
            axios
                .post(api, {
                    id: this.chat.chat_id,
                    type: "TEXT",
                    content: this.$root.$refs.ColChat2.contentTranslated !== "" &&
                        this.$root.$refs.ColChat2.contentTranslated !== null &&
                        this.$root.$refs.ColChat2.contentTranslated !== undefined  ?
                        this.$root.$refs.ColChat2.contentTranslated :
                        $message,
                    content_translated: this.$root.$refs.ModalTranslate.status_my_messages ? content_translated : null,
                    is_agent: true,
                    is_incognito: this.incognito_mode,
                    company_department_id: this.chat.companyDepartmentId,
                    company_user_company_department_id: $cucdic,
                    time_for_inactivity_message: this.time_inactivityMessage,
                    chat: this.chat,
                    company_id: this.session_user_company.id,
                    images: images
                })
                .then(({ data }) => {});
            // document.getElementById("input").focus();
        },
        handleFilesUpload() {
            // o atributo 'attachments' recebe os arquivos enviados pelo onchange do input de uploads
            this.attachments = this.$refs.attachments.files;
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
                // document.getElementById("input").focus();
            }, 0);
        },
        upload() {
            $("#attachments").click();
        },
        /** @GET */
        getChatHistory(id) {
            return new Promise((resolve, reject) => {
                this.getQuestionary(id).then(() => {
                    axios
                    .get("chat-history/agent/get-chat-history", {
                        params: {
                            id: id
                        }
                    })
                    .then(({data}) => {
                        resolve(data);
                    });
                })
            });
        },
        getQuestionary(id) {
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
                        vm.translateQuestionary(response.data.result, id).then((questionary) => {
                            vm.questionary = questionary;
                            resolve();
                        })
                    }
                });
                resolve();
            })
        },
        translateQuestionary(questionary, chat_id) {
            return new Promise((resolve, reject) => {

                if (this.$root.$refs.ModalTranslate.status_visitors_messages) {

                    if (localStorage.getItem(`${chat_id}_translated_questionary`) === null) {

                        var itemsProcessed = 0;

                        questionary.forEach(element => {
                            this.$google.translate(element.answer, this.$root.$refs.ModalTranslate.languageMyMessages).then((result) => {
                                itemsProcessed++;
                                element.answer = result.data.translations[0].translatedText;

                                if(itemsProcessed === questionary.length) {
                                    var translated_questions = [{
                                        language: this.$root.$refs.ModalTranslate.languageMyMessages,
                                        content: questionary
                                    }];

                                    localStorage.setItem(`${chat_id}_translated_questionary`, JSON.stringify(translated_questions));
                                    resolve(questionary);
                                }
                            });
                        });

                    } else {
                        var localstorage = JSON.parse(localStorage.getItem(`${chat_id}_translated_questionary`));
                        var itemsProcessed = 0;
                        var found = false;
                        var content = [];

                        localstorage.forEach(element => {
                            itemsProcessed++;

                            if (element.language == this.$root.$refs.ModalTranslate.languageMyMessages) {
                                found = true;
                                content = element.content;
                            }

                            if(itemsProcessed === localstorage.length) {
                                if (found) {
                                    resolve(content);
                                } else {
                                    var QuestionaryItemsProcessed = 0;
                                    questionary.forEach(element => {
                                        this.$google.translate(element.answer, this.$root.$refs.ModalTranslate.languageMyMessages).then((result) => {
                                            QuestionaryItemsProcessed++;
                                            element.answer = result.data.translations[0].translatedText;

                                            if(QuestionaryItemsProcessed === questionary.length) {

                                                localstorage.push({
                                                    language: this.$root.$refs.ModalTranslate.languageMyMessages,
                                                    content: questionary
                                                })

                                                localStorage.setItem(`${chat_id}_translated_questionary`, JSON.stringify(localstorage));

                                                resolve(questionary);
                                            }
                                        });
                                    });
                                }
                            }
                        });
                    }
                } else {
                    resolve(questionary)
                }

            })
        },
        setChatHistories(data) {
            return new Promise((resolve, reject) => {

                this.chat_history_robot = [];
                this.chat_history = [];

                var itemsProcessed = 0;

                data.forEach(element => {
                    this.setChatHistory(element, false).then(() => {
                        itemsProcessed++;
                        if(itemsProcessed === data.length) {
                            this.loading_messages = false;
                            resolve();
                        }
                    });
                });
            })
        },
        setChatHistory(message, realtime) {
            return new Promise((resolve, reject) => {
                if (message.type == 'ROBOT') {
                    this.chat_history_robot.push(message);
                    resolve();
                } else if (message.type !== 'ROBOT'){
                    if (message.company_user_company_department_id == null && realtime) {
                        message.content_translated = null;
                        if (this.$root.$refs.ModalTranslate.status_visitors_messages) {
                            this.$google.translate(message.content,  this.$root.$refs.ModalTranslate.init_language_my_messages()).then((result) => {
                                this.$root.$refs.ModalTranslate.setTranslatedMessages(result.data.translations[0].translatedText, this.$root.$refs.ModalTranslate.init_language_my_messages(), message.ch_id).then((content_translated) => {
                                    content_translated.forEach(ct => {
                                        if (ct.language == this.$root.$refs.ModalTranslate.init_language_my_messages()) {
                                            message.content_translated = result.data.translations[0].translatedText;
                                            this.chat_history.push(message);
                                            resolve();
                                        }
                                    });
                                })
                            })
                        } else {
                            this.chat_history.push(message);
                            resolve();
                        }
                    } else {
                        if (message.content_translated !== null) {
                            if (message.company_user_company_department_id !== null) {
                                var content_translated = JSON.parse(message.content_translated);
                                content_translated = content_translated[0].content;

                                var content = message.content;

                                message.content = content_translated;
                                message.content_translated = content;

                                this.chat_history.push(message);
                                resolve();
                            }  else {
                                var content_translated = JSON.parse(message.content_translated);
                                content_translated = content_translated[0].content;
                                message.content_translated = content_translated;
                                this.chat_history.push(message);
                                resolve();
                            }
                        } else {
                            this.chat_history.push(message);
                            resolve();
                        }
                    }

                } else {
                    reject();
                }
            });
        },
        getChatsResolved(page = 1) {
            var vm = this;
            const api = `${vm.url_prefix}/get-chats-finished?page=${page}`;
            axios
                .get(api, {
                    params: {
                        my_chats: vm.filter_my_chats ? 1 : 0,
                        filter_not_category: vm.filter_not_category ? 1 : 0,
                        departments: vm.filter_departments,
                        per_page: vm.pp_selected
                    }
                })
                .then(({ data }) => {
                        data.last_page = Math.ceil(vm.countResolved / vm.pp_selected);
                        data.last_page_url = `${data.path}?page=${data.last_page}`;
                        data.total = vm.countResolved;
                        vm.chats_resolved = data;
                });
        },
        getChatsCanceled(page = 1) {

            const api = `${this.url_prefix}/get-chats-canceled?page=${page}`;
            axios
                .get(api, {
                    params: {
                        my_chats: this.filter_my_chats ? 1 : 0,
                        filter_not_category: this.filter_not_category ? 1 : 0,
                        departments: this.filter_departments,
                        per_page: this.pp_selected
                    }
                })
                .then(({ data }) => {
                    this.chats_canceled = data;
                });
        },
        getAgentTablesCount() {
            this.loading_counters = true;
            const api = `${this.url_prefix}/get-agent-tables-count`;
            axios
                .get(api, {
                    params: {
                        departments: this.company_department,
                        my_chats: this.filter_my_chats ? 1 : 0,
                        filter_not_category: this.filter_not_category ? 1 : 0,
                    }
                })
                .then(({ data }) => {
                    this.countOnQueue = this.$store.state.chats_in_queue.length;
                    this.countInProgress = this.$store.state.chats_in_progress.length;
                    data.transferred >= 1
                        ? (this.countTransferred = data.transferred)
                        : (this.countTransferred = "");
                    data.canceled >= 1
                        ? (this.countCanceled = data.canceled)
                        : (this.countCanceled = "");

                    let finished = 0;
                    data.resolved >= 1
                        ? (finished = finished + data.resolved)
                        : (finished = finished);
                    data.closed >= 1
                        ? (finished = finished + data.closed)
                        : (finished = finished);
                    this.countResolved = finished;
                })
                .finally(() => {
                    this.loading_counters = false;
                })
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
                data.forEach(element => {
                    element.name = vm.$t(element.name);
                });
                let local_filter = JSON.parse(
                    localStorage.getItem("filter_departments")
                );
                if (local_filter) {
                    local_filter.forEach(e1 => {
                        let index = data.findIndex(e2 => e2.id === e1.id);

                        if (index !== -1) {
                            vm.company_department.push(e1);
                        }
                    });
                } else {
                    vm.company_department = data;
                }

                vm.departments = data;
                if (data == "") {
                    $("#modalDepartmentNot").modal("show");
                }
                //vm.showTableComponent("queue");
                // this.handleDepartmentsFilter();
                // this.getChatsInProgress();
                // this.getAgentTablesCount();
            });
        },
        getDepartmentComands(company_department_id) {
            return new Promise((resolve, reject) => {
                var vm = this;
                axios
                    .get("get-department-settings", {
                        params: {
                            id: company_department_id
                        }
                    })
                    .then(({ data }) => {
                        let settings = JSON.parse(data[0]["settings"]);
                        this.itemselected_settings = JSON.parse(data[0]["settings"]);
                        let commands = settings.commands;
                        commands.forEach(element => {
                            element.description = vm.$t(element.description);
                        });
                        let inactivityMessage =
                            settings.quant_limity.inactivityMessage;
                        let default_commands = vm.setDefaultCommands();
                        default_commands.forEach(element => {
                            commands.push(element);
                        });
                        vm.departmentCommands = commands;
                        vm.time_inactivityMessage = inactivityMessage;
                        resolve();
                    });
            });
        },
        /** @SET */
        setDefaultCommands() {
            let commands = Array;
            let company = this.session_user_company;
            commands = [
                {
                    command: "cl_name",
                    description: this.$t(this.chat.client.name),
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
                    description: this.$t(this.chat.department),
                    status: null
                }
            ];
            return commands;
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
        setInfo(info, clear) {
            return new Promise((resolve, reject) => {
                if (clear) {
                    let client = this.chat.client;
                    Object.keys(this.chat).forEach(k => (this.chat[k] = ""));
                    this.chat.client = client;
                    Object.keys(this.chat.client).forEach(
                        k => (this.chat.client[k] = "")
                    );
                }
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
                this.chat.description = info.description;
                this.chat.type = info.type;
                this.chat.created_at = info.created_at;
                this.chat.number = info.number;
                this.chat.dep_type = info.dep_type;
                this.chat.turn_into_ticket_at_closing = info.turn_into_ticket_at_closing;
                // info do client
                this.chat.client.id = info.client_id;
                this.chat.client.name = info.name;
                this.chat.client.email = info.email;
                this.chat.client.browser = info.user_agent;
                this.chat.user_agent = info.user_agent;
                this.chat.show = true;
                this.chat.client_id = info.client_id;
                this.chat.name = info.name;
                this.chat.email = info.email;

                if (info.builderall_account_data != null) {
                    this.chat.client.builderall_account_data =
                        info.builderall_account_data;
                    this.chat.builderall_account_data =
                        info.builderall_account_data;
                    var ba_acct = JSON.parse(info.builderall_account_data);
                    this.chat.is_vip = ba_acct["is_vip"];
                } else {
                    this.chat.client.builderall_account_data = null;
                    this.chat.builderall_account_data = null;
                }
                this.rs_mouse = 'over';
                resolve();
            });
        },
        /** @ACTIONS */
        getActions() {
            this.actions = {};
            if (this.take_on_chat) {
                this.actions.take = {
                    title: this.$t("bs-take-on-chat"),
                    icon: "chat",
                    status: "TAKE_ON",
                    message: this.$t("bs-taking-over-the-chat")
                };
            } else {
                if ((this.permissions.chat_transform === 1 || this.admin === 1) && !this.chat.turn_into_ticket_at_closing) {
                    this.actions.ticket = {
                        title: this.$t("bs-turn-into-ticket"),
                        icon: "email",
                        status: "TICKET",
                        message: this.$t("bs-you-want-to-turn-the-chat-into-tk")
                    };
                }
                if (this.permissions.chat_moved === 1 || this.admin === 1) {
                    this.actions.transfer_to_agent = {
                        title: this.$t("bs-transfer-to-another-attendant"),
                        icon: "person",
                        status: "TRANSFERRED_TO_AGENT",
                        message: `${this.$t(
                            "bs-do-you-want-to-transfer-the-chat-to"
                        )} `
                    };
                    this.actions.transfer_to_department = {
                        title: this.$t("bs-transfer-to-another-department"),
                        icon: "groups",
                        status: "TRANSFERRED_TO_DEPARTMENT",
                        message: this.$t(
                            "bs-transfer-the-chat-to-the-department"
                        )
                    };
                }
                if (this.permissions.chat_resolved === 1 || this.admin == 1) {
                    this.actions.resolved = {
                        title: this.$t("bs-mark-as-resolved"),
                        icon: "done",
                        status: "RESOLVED",
                        message: this.$t("bs-mark-the-chat-as-resolved")
                    };
                }
                if (this.permissions.chat_close === 1 || this.admin == 1) {
                    this.actions.close = {
                        title: this.$t("bs-close-chat"),
                        icon: "close",
                        status: "CLOSED",
                        message: this.$t("bs-you-want-to-end-the-chat")
                    };
                }
                if (this.permissions.chat_blocked === 1 || this.admin == 1) {
                    this.actions.chat_blocked = {
                        title: this.$t("bs-block"),
                        icon: "block",
                        status: "BLOCK_CLIENT",
                        message: ""
                    };
                }
                 if (this.permissions.chat_delete === 1 || this.admin == 1) {
                    this.actions.chat_delete = {
                        title: this.$t("bs-delete") + " " + this.$t("bs-chat"),
                        icon: "delete_forever",
                        status: "DELETE",
                        message: ""
                    };
                }
            }
        },
        getDepartmentsByCompany() {
            return new Promise((resolve) => {
                var vm = this;
                var url = '/get-departments-by-company';
                axios.get(url)
                .then(({data}) => {
                    resolve(data);
                })
            })
        },
        executeAction(action) {
            this.$loading(true);
            this.$refs.pop4.$emit('close')
            switch (action.status) {
                case "TICKET":
                    var vm = this;
                    vm.selected_department = null;
                    vm.selected_agent = null;
                    vm.ticket_description = "";
                    vm.showAgentForm = false;
                    vm.departments_to_transfer = [];
                    vm.getDepartmentsByCompany().then((departments) => {
                        if (departments.length) {
                            var i = 0;
                            departments.forEach(element => {
                                i++
                                vm.departments_to_transfer.push(element);
                                if (i == departments.length) {
                                    $("#modalDepartmentTicket").modal("show");
                                }
                            });
                            vm.$loading(false);
                        } else {
                            Swal.fire({
                                heightAuto: false,
                                icon: "error",
                                title: vm.$t("bs-error"),
                                text: vm.$t(
                                    "bs-no-active-departments-found"
                                )
                            });

                            vm.$loading(false);
                        }
                    })
                    break;
                case "TRANSFERRED_TO_AGENT":
                    this.transfer_to_agent = true;
                    this.departments_to_transfer = [];
                    axios
                        .get("/get-open-departments", {
                            params: {
                                country: 'ALL',
                                except_this: 0 // caso nao quiser listar um dep, passar o id dele aqui (hasheado);
                            }
                        })
                        .then(({ data }) => {
                            if (data.length) {
                                data.forEach(element => {

                                    let client_tz = Intl.DateTimeFormat().resolvedOptions()
                                        .timeZone;
                                    let opening_time = this.UTCtoClientTZ(
                                        element.openDateToUTC,
                                        client_tz
                                    );
                                    let closing_time = this.UTCtoClientTZ(
                                        element.closeDateToUTC,
                                        client_tz
                                    );

                                    this.departments_to_transfer.push({
                                        id: element.id,
                                        name: this.$t(element.name),
                                        status:
                                            opening_time
                                                .split(" ")[1]
                                                .split(":")[0] +
                                            ":" +
                                            opening_time
                                                .split(" ")[1]
                                                .split(":")[1] +
                                            " - " +
                                            closing_time
                                                .split(" ")[1]
                                                .split(":")[0] +
                                            ":" +
                                            closing_time
                                                .split(" ")[1]
                                                .split(":")[1],
                                        type: element.type,
                                        // $isDisabled: !element.online // cancelado por hora
                                        $isDisabled: false
                                    });

                                });
                                $("#modalDepartment").modal("show");
                                this.$loading(false);
                            } else {
                                this.$loading(false);
                                Swal.fire({
                                    heightAuto: false,
                                    icon: "error",
                                    title: this.$t("bs-error"),
                                    text: this.$t(
                                        "bs-no-active-departments-found"
                                    )
                                });
                            }
                        });
                    break;
                case "TRANSFERRED_TO_DEPARTMENT":
                    this.transfer_to_agent = false;
                    this.departments_to_transfer = [];
                    axios
                        .get("/get-open-departments", {
                            params: {
                                country: 'ALL',
                                except_this: this.chat.companyDepartmentId // caso nao quiser listar um dep, passar o id dele aqui (hasheado);
                            }
                        })
                        .then(({ data }) => {
                            if (data.length) {
                                data.forEach(element => {

                                    let client_tz = Intl.DateTimeFormat().resolvedOptions()
                                        .timeZone;
                                    let opening_time = this.UTCtoClientTZ(
                                        element.openDateToUTC,
                                        client_tz
                                    );
                                    let closing_time = this.UTCtoClientTZ(
                                        element.closeDateToUTC,
                                        client_tz
                                    );

                                    this.departments_to_transfer.push({
                                        id: element.id,
                                        name: this.$t(element.name),
                                        status:
                                            opening_time
                                                .split(" ")[1]
                                                .split(":")[0] +
                                            ":" +
                                            opening_time
                                                .split(" ")[1]
                                                .split(":")[1] +
                                            " - " +
                                            closing_time
                                                .split(" ")[1]
                                                .split(":")[0] +
                                            ":" +
                                            closing_time
                                                .split(" ")[1]
                                                .split(":")[1],
                                        type: element.type,
                                        // $isDisabled: !element.online // cancelado por hora
                                        $isDisabled: false
                                    });
                                });
                                $("#modalDepartment").modal("show");
                                this.$loading(false);
                            } else {
                                this.$loading(false);
                                Swal.fire({
                                    heightAuto: false,
                                    icon: "error",
                                    title: this.$t("bs-error"),
                                    text: this.$t(
                                        "bs-no-active-departments-found"
                                    )
                                });
                            }
                        });
                    break;
                case "TAKE_ON":
                    var vm = this;
                    this.$loading(true);
                    const api = `chat/take-the-chat`;
                    axios
                        .post(api, {
                            chat: vm.chat,
                            company_department_id: vm.chat.companyDepartmentId,
                            comp_user_comp_depart_id_current:
                                vm.chat.comp_user_comp_depart_id_current,
                            is_admin: vm.admin ? true : false
                        })
                        .then(({ data }) => {
                            if (data.status) {
                                if (data.is_admin) {
                                    vm.cucd = data.session_user_cucd;
                                }
                                vm.closeFooterActiveChat(vm.chat, false).then(
                                    () => {
                                        vm.chat.client_id = vm.chat.client.id;
                                        vm.chat.name = vm.chat.client.name;
                                        vm.chat.email = vm.chat.client.email;
                                        vm.addFooterActiveChat(vm.chat, 'chat');
                                    }
                                );
                                vm.$loading(false);
                            } else {
                                Swal.fire({
                                    heightAuto: false,
                                    icon: "info",
                                    text: vm.$t(data.status)
                                });
                                vm.$loading(false);
                            }
                        });
                    break;
                case "BLOCK_CLIENT":
                    // $("#showModalReason").modal("show");
                    this.showModalReason = true;
                    this.$loading(false);
                    break;
                case "DELETE":
                    var vm = this;
                    Swal.fire({
                        title: vm.$t('bs-are-you-sure'),
                        text: vm.$t('bs-you-wont-be-able-to-revert-this'),
                        icon: 'warning',
                        showCancelButton: true,
                        cancelButtonText: vm.$t('bs-cancel'),
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: vm.$t('bs-yes-delete-it'),
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.post('chat/delete',{
                                chat_id: vm.chat.chat_id
                            })
                            .then(({data}) => {
                                if(data.success) {
                                    vm.closeFooterActiveChat(vm.chat, false).then(() => {
                                        vm.clearActiveChat();
                                    })
                                    this.$loading(false);
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                this.$loading(false);
                            })
                        } else {
                            this.$loading(false);
                        }
                    })
                break;
                default:
                     try {
                        if(this.itemselected_settings.chat.showCategory){
                            if(action.status == 'CLOSED' || action.status == 'RESOLVED'){
                                if(this.rejectCheck == true){
                                    this.save(action);
                                    this.rejectCheck = false;
                                }else{
                                    axios.get('check-category', {
                                        params:{
                                            chat_id: this.chat.chat_id,
                                            cttype: 'CHAT'
                                        }
                                    }).then(res => {
                                        if(res.data.result){
                                            this.save(action);
                                        }else{
                                            this.openCategory();
                                            this.$store.state.showAlertCategory = true;
                                            // this.rejectCheck = true;
                                            this.$loading(false);
                                        }
                                    }).catch(function(e){
                                        console.log(e);
                                    });
                                }
                            }else{
                                this.save(action);
                            }
                        }else{
                            this.save(action);
                        }
                    } catch (e) {
                        this.save(action);
                    }
                break;
            }
        },
        save(action){
             Swal.fire({
                title: action.message,
                heightAuto: false,
                showCancelButton: true,
                confirmButtonText: this.$t("bs-yes"),
                cancelButtonText: this.$t("bs-no")
            }).then(result => {
                if (result.isConfirmed) {
                    axios
                        .post(`chat/agent/update-status`, {
                            id: this.chat.chat_id,
                            chat: this.chat,
                            action: action.status,
                            company_department: this.chat
                                .companyDepartmentId
                        })
                        .then(response => {
                            if (response.data.status) {
                                //this.chat.status = response.data.status;
                                //this.getChatsInProgress();
                                this.clearActiveChat();
                                this.departments_to_transfer = [];
                                this.agents_to_transfer = [];
                                this.transfer_to_agent = false;
                                this.selected_department = null;
                                this.selected_agent = null;
                                this.showTableInProgress = true;
                                this.showTableQueue = false;
                                this.showTableClosed = false;
                                this.showTableResolved = false;
                                this.showTableCanceled = false;
                                this.$loading(false);
                            } else {
                                this.$loading(false);
                            }
                        });
                } else {
                    this.$loading(false);
                }
            });
        },
        blockclient(item, status){
            var vm = this;
            axios.post('block-client-ticket', {
                id: item,
                textReason: vm.textReason,
                status: status,
            }).then(function(response){

                if(status){
                    vm.$snotify.success('Cliente Bloqueado com successo!', vm.$t('bs-success'), {
                        timeout: 2000,
                        showProgressBar: false,
                        pauseOnHover: true
                    });
                }else{
                    vm.$snotify.success('Cliente Liberado com successo!', vm.$t('bs-success'), {
                        timeout: 2000,
                        showProgressBar: false,
                        pauseOnHover: true
                    });
                }

                vm.statusblock = !vm.statusblock;
            })
            .catch(function(erro){
                console.log('FAILURE!!');
            });
        },
        showModalEditTurnoIntoTicket () {
            var vm = this;
            vm.$loading(true);
            vm.getInfoToTurnIntoTicket().then((info) => {
                vm.ticket_description = info.description;
                vm.ticket_description_edit = info.description;
                vm.departments_to_transfer = [];
                vm.getDepartmentsByCompany().then((departments) => {
                    if (departments.length) {
                        var i = 0;
                        departments.forEach(element => {
                            i++
                            vm.departments_to_transfer.push(element);
                            if (i == departments.length) {
                                let idx = vm.departments_to_transfer.findIndex(
                                    element => element.id === info.company_department
                                );
                                if (idx !== -1) {
                                    vm.selected_department = vm.departments_to_transfer[idx];
                                    vm.showAgentsFormByDepartment(info.cucd_id);
                                    $("#modalEditTurnoIntoTicket").modal("show");
                                    vm.$loading(false);
                                }
                            }
                        });
                    } else {
                        vm.$loading(false);
                    }
                })
            }).catch(() => {
                console.log('FAILURE!!');
            });
        },
        getInfoToTurnIntoTicket() {
            var vm = this;
            return new Promise((resolve, reject) => {
                var url = 'chat/get-information-to-turn-into-ticket'
                axios.get(url, {
                    params: {
                        chat_id: vm.chat.chat_id,
                    }
                })
                .then(({data}) => {
                    if (data.success) {
                        resolve(data.info)
                    } else {
                        reject();
                    }
                })
            })
        },
        turnIntoTicket() {
            var vm = this;
            var chat_number = `#${vm.chat.number}`
            $("#modalDepartmentTicket").modal("hide");
            Swal.fire({
                title: vm.$t('bs-stay-in-this-chat'),
                icon: 'question',
                html:
                    `${vm.$t('bs-if-you-want-to-continue-select-yes')} ${vm.$t('bs-the-chat-will-be-turned-into-a-ticket-auto')}` +
                    '<br/>' +
                    '<br/>' +
                    `${vm.$t('bs-if-you-want-to-end-the-chat-now-and-turn')}`,
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: vm.$t('bs-yes'),
                denyButtonText: vm.$t('bs-no'),
                cancelButtonText: vm.$t('bs-back'),
                confirmButtonColor: '#01D4B9',
                denyButtonColor: '#0080FC',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    vm.setInfoToTurnIntoticket();
                } else if (result.isDenied) {
                    vm.closeFooterActiveChat(vm.chat, false);
                    vm.updateStatusToTicket().then((ticket) => {
                        vm.departments_to_transfer = [];
                        vm.agents_to_transfer = [];
                        vm.ticket_description = "";
                        vm.transfer_to_agent = false;
                        vm.selected_department = null;
                        vm.selected_agent = null;
                        vm.showTableInProgress = true;
                        vm.showTableQueue = false;
                        vm.showTableClosed = false;
                        vm.showTableResolved = false;
                        vm.showTableCanceled = false;
                        vm.clearActiveChat();
                        Swal.fire({
                            icon: 'success',
                            html: `${vm.$t('bs-chat')} <bold>#${chat_number}</bold> ${vm.$t('bs-has-been-transformed-into')} ${vm.$t('bs-ticket')} <bold>#${ticket.id}</bold>`,
                            showConfirmButton: true,
                        })
                    }).catch(() => {
                        Swal.fire({
                            heightAuto: false,
                            icon: "error",
                            title: vm.$t("bs-error"),
                            text: vm.$t("bs-error-when-turning-chat-into-ticket")
                        });
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    $("#modalDepartmentTicket").modal("show");
                }
            })
        },
        setInfoToTurnIntoticket(action = 'create') {
            var vm = this
            var url = 'chat/set-information-to-turn-into-ticket';


            if (action == 'update') {
                vm.ticket_description = vm.ticket_description_edit
            }


            // verificar se tem IMAGEM dentro do html do content
			let quotes = vm.ticket_description.split('"');
            let images = [];

            quotes.forEach(element => {
                if (element.substring(0, 10) == 'data:image') {
                    images.push(element);
                }
            });

			// mesma altura para todas as imagens
            vm.ticket_description = vm.ticket_description.replace('><img', '><img  style="height: 150px;"');


            axios.post(url, {
                chat_id: vm.chat.chat_id,
                company_department: vm.selected_department.id,
                description: vm.ticket_description,
                cucd_id: vm.selected_agent.company_user_company_department_id,
                images: images.length ? images : null
            })
            .then(({data}) => {
                if (data.success) {
                    if (action == 'update') {
                        $("#modalEditTurnoIntoTicket").modal("hide");
                        //Swal.fire(vm.$t('bs-saved-successfully'), '', 'success')
                    }
                }
            })
            .catch(err => {
                //
            })
        },
        deleteInfoToTurnIntoticket() {
            var vm = this;

            Swal.fire({
                title: `${vm.$t('bs-cancel-transformation')}?`,
                showCancelButton: true,
                confirmButtonText: vm.$t('bs-yes'),
                cancelButtonText: vm.$t('bs-no'),
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = 'chat/delete-information-to-turn-into-ticket';
                    axios.post(url, {
                        chat_id: vm.chat.chat_id,
                    })
                    .then(({data}) => {
                        if (data.success) {
                            $("#modalEditTurnoIntoTicket").modal("hide");
                            vm.chat.turn_into_ticket_at_closing = false;
                            Swal.fire(`${vm.$t('bs-canceled')}!`, '', 'success')
                        }
                    })
                    .catch(err => {
                        console.error(err);
                    })
                }
            })
        },
        updateStatusToTicket() {
            var vm = this;
            vm.$loading(true);
            let quotes = vm.ticket_description.split('"');
            let images = [];
            quotes.forEach(element => {
                if (element.substring(0, 10) == 'data:image') {
                    images.push(element);
                }
            });
            vm.ticket_description = vm.ticket_description.replace('><img', '><img  style="height: 150px;"');
            var url = `chat/agent/update-status`;
            return new Promise(function(resolve, reject) {
                axios.post(url,{
                    id: vm.chat.chat_id,
                    chat: vm.chat,
                    action: "TICKET",
                    company_department: vm.selected_department.id,
                    description: vm.ticket_description,
                    comments: vm.ticket_comments,
                    cucd_id: vm.selected_agent.company_user_company_department_id,
                    images: images.length ? images : null
                })
                .then(res => {
                    if(res.data.status) {
                        vm.$loading(false);
                        resolve(res.data.ticket)
                    } else {
                        vm.$loading(false);
                        reject()
                    }
                })
                .catch(err => {
                    vm.$loading(false);
                    reject();
                })
            })
        },
        transferChat() {
            if (this.selected_department !== null) {
                if (this.transfer_to_agent) {
                    if (this.selected_agent !== null) {
                        this.$loading(true);
                        this.closeFooterActiveChat(this.chat, false);
                        axios
                            .post(`chat/agent/update-status`, {
                                id: this.chat.chat_id,
                                action: "TRANSFERRED_TO_AGENT",
                                company_department: this.selected_department,
                                department_name: this.chat.department,
                                agent: this.selected_agent,
                                chat: this.chat
                            })
                            .then(response => {
                                if (response.data.status) {
                                    this.clearActiveChat();
                                    //this.getChatsInProgress();
                                    this.departments_to_transfer = [];
                                    this.agents_to_transfer = [];
                                    this.transfer_to_agent = false;
                                    this.selected_department = null;
                                    this.selected_agent = null;
                                    this.showTableInProgress = true;
                                    this.showTableQueue = false;
                                    this.showTableClosed = false;
                                    this.showTableResolved = false;
                                    this.showTableCanceled = false;
                                    $("#modalAtendent").modal("hide");
                                    this.$loading(false);
                                } else {
                                    Swal.fire({
                                        heightAuto: false,
                                        icon: "error",
                                        title: this.$t("bs-error"),
                                        text: `${this.$t(
                                            "bs-error-transferring-chat-to"
                                        )} ${this.selected_agent.name}`
                                    });
                                    this.$loading(false);
                                }
                            });
                    }
                } else {
                    this.$loading(true);
                    this.closeFooterActiveChat(this.chat, false);
                    axios
                        .post(`chat/agent/update-status`, {
                            id: this.chat.chat_id,
                            action: "TRANSFERRED_TO_DEPARTMENT",
                            company_department: this.selected_department.id,
                            department_name: this.selected_department.name,
                            department_type: this.selected_department.type,
                            chat: this.chat
                        })
                        .then(response => {
                            if (response.data.status) {
                                this.clearActiveChat();
                                this.departments_to_transfer = [];
                                this.agents_to_transfer = [];
                                this.transfer_to_agent = false;
                                this.selected_department = null;
                                this.selected_agent = null;
                                this.showTableInProgress = true;
                                this.showTableQueue = false;
                                this.showTableClosed = false;
                                this.showTableResolved = false;
                                this.showTableCanceled = false;
                                $("#modalDepartment").modal("hide");
                                this.$loading(false);
                            } else {
                                Swal.fire({
                                    heightAuto: false,
                                    icon: "error",
                                    title: this.$t("bs-error"),
                                    text: `${this.$t(
                                        "bs-error-transferring-to-department"
                                    )} ${this.selected_department.name}`
                                });
                                this.$loading(false);
                            }
                        });
                }
            }
        },
        catchChat(chat) {
            var vm = this;
            vm.chat_catched_id = chat.chat_id;
            vm.loading2catchChat = true;
            vm.clearActiveChat().then(() => {
                vm.setInfo(chat, true).then(() => {
                    if (vm.permissions.chat_open || vm.admin) {
                        axios
                            .get("chat/get-agent-active-chats", {
                                params: {
                                    company_department_id:
                                        vm.chat.companyDepartmentId
                                }
                            })
                            .then(({ data }) => {
                                if (data.status || vm.admin) {
                                    vm.loading2catchChat = false;
                                    vm.setEmployee(vm.admin ? true : false);
                                } else {
                                    vm.chat_catched_id = ""
                                    vm.loading2catchChat = false;
                                    Swal.fire({
                                        heightAuto: false,
                                        icon: "info",
                                        text: vm.$t(
                                            "bs-maximum-number-of-active-chats"
                                        )
                                    });
                                }
                            });
                    } else {
                        vm.chat_catched_id = "";
                        Swal.fire({
                            heightAuto: false,
                            icon: "info",
                            text: vm.$t(
                                "bs-you-are-not-allowed-to-catch-chats"
                            )
                        });
                    }
                });
            });
        },
        setEmployee(is_admin) {
            var vm = this;
            vm.loading2catchChat = true;
            const api = `chat/set-employee`;
            axios
                .post(api, {
                    chat: vm.chat,
                    company_department_id: vm.chat.companyDepartmentId,
                    is_admin: is_admin,
                    dep_type: vm.chat["dep_type"]
                })
                .then(({ data }) => {
                    if (data.status) {
                        if (data.is_admin) {
                            vm.cucd = data.session_user_cucd;
                        }
                        vm.setCucdIdCurrent(data).then(() => {
                            vm.loading2catchChat = false;
                            vm.openChat(vm.chat, true);
                        });
                    } else {
                        vm.loading2catchChat = false;
                        Swal.fire({
                            heightAuto: false,
                            icon: "info",
                            text: vm.$t(data.message)
                        });
                    }
                });
        },
        setCucdIdCurrent(data) {
            return new Promise((resolve, reject) => {
                if (data.comp_user_comp_depart_id_current !== null) {
                    this.chat.comp_user_comp_depart_id_current =
                        data.comp_user_comp_depart_id_current;
                    this.chat.status = "IN_PROGRESS";
                    this.chat.answered = 0;
                    this.chat.operator = this.session_user.name;
                    resolve();
                } else {
                    reject();
                }
            });
        },
        /** @WEBSOCKETS */
        connectToChannels() {
            //this.joinInQueueChannel();
            //this.joinInProgressChannel();
            this.joinInClosedChannel();
            this.joinInResolvedChannel();
            this.joinInCanceledChannel();
            this.joinInDeletedChannel();
        },
        joinInClosedChannel() {
            /** begin */
            const channel = `full-chat.closed`;
            const event = `FullChatClosed`;
            /** join to the channel and listen events */
            Echo.leave(`${channel}.${this.session_user_company.id}`)
            Echo.join(`${channel}.${this.session_user_company.id}`).listen(
                event,
                e => {
                    /** if the user is a admin, we get the actions for him, else we get the actions of employee */

                    if (this.admin) {
                        this.adminClosedChannelActions(e.item);
                    } else {
                        this.employeeClosedChannelActions(e.item);
                    }
                }
            );
            /** end */
        },
        joinInResolvedChannel() {
            /** begin */
            const channel = `full-chat.resolved`;
            const event = `FullChatResolved`;
            /** join to the channel and listen events */
            Echo.leave(`${channel}.${this.session_user_company.id}`)
            Echo.join(`${channel}.${this.session_user_company.id}`).listen(
                event,
                e => {
                    /** if the user is a admin, we get the actions for him, else we get the actions of employee */
                    if (this.admin) {
                        this.adminResolvedChannelActions(e.item);
                    } else {
                        this.employeeResolvedChannelActions(e.item);
                    }
                }
            );
            /** end */
        },
        joinInCanceledChannel() {
            /** begin */
            const channel = `full-chat.canceled`;
            const event = `FullChatCanceled`;
            /** join to the channel and listen events */
            Echo.leave(`${channel}.${this.session_user_company.id}`)
            Echo.join(`${channel}.${this.session_user_company.id}`).listen(
                event,
                e => {
                    /** if the user is a admin, we get the actions for him, else we get the actions of employee */
                    if (this.admin) {
                        this.adminCanceledChannelActions(e.item);
                    } else {
                        this.employeeCanceledChannelActions(e.item);
                    }
                }
            );
            /** end */
        },
        joinInDeletedChannel() {
            /** begin */
            const channel = `chat-ticket-delete`;
            const event = `ChatTicketDelete`;
            /** join to the channel and listen events */
            Echo.leave(`${channel}.${this.session_user_company.id}`)
            Echo.join(`${channel}.${this.session_user_company.id}`).listen(
                event,
                e => {
                    if (e.item.type == 'CHAT') {
                        switch (e.item.status) {
                            case 'IN_PROGRESS':

                                let idx = this.chats_in_progress.findIndex(
                                    element => element.chat_id === e.item.id
                                );
                                if (idx !== -1) {
                                    this.$store.commit("spliceChatsInProgress", idx);
                                }
                                break;

                            case 'RESOLVED':
                            case 'CLOSED':
                                this.adminClosedChannelActions({
                                    action: 'splice',
                                    chat_id: e.item.id,
                                });
                                break;

                            case 'OPENED':
                                this.$store.commit("getChatsOnQueue", this.url_prefix);
                                break;

                            case 'CANCELED':

                                if(this.chats_canceled.data) {
                                    let index = this.chats_canceled.data.findIndex(
                                        element => element.chat_id === e.item.id
                                    );

                                    let current_page = this.chats_canceled.current_page;
                                    let last_page = this.chats_canceled.last_page;
                                    let per_page = this.chats_canceled.per_page;
                                    let data = this.chats_canceled.data;

                                    if (index !== -1) {
                                        if(data.length == 1 && current_page != 1) {
                                            this.getChatsCanceled(current_page - 1);
                                        } else if (data.length == per_page) {
                                            this.getChatsCanceled(current_page);
                                        } else if (data.length < per_page && current_page == 1) {
                                            this.chats_canceled.data.splice(index, 1);
                                        }
                                    } else {
                                        if(data.length == 1 && current_page != 1) {
                                            this.getChatsCanceled(current_page - 1);
                                        } else {
                                            this.getChatsCanceled(current_page);
                                        }
                                    }
                                }

                                if (this.countCanceled) {
                                    this.countCanceled--;
                                }

                                break;

                            default:
                                break;
                        }
                    }
                }
            );
            /** end */
        },
        adminClosedChannelActions(item) {
            let my_chat = this.cucd.findIndex(
                c =>
                    c.company_user_company_department_id ===
                        item.comp_user_comp_depart_id_current ||
                    this.session_user.id === item.user_agent_id
            );

            // se o "meus chats" estiver marcado só passa oq é meu, se nao passa tudo normal
            if (
                !this.filter_my_chats ||
                (my_chat !== -1 && this.filter_my_chats)
            ) {
                switch (item.action) {
                    case "push":
                        let i = this.filter_departments.findIndex(
                            f => f.id === item.company_department_id
                        );

                        if (i !== -1) {

                            if (this.countResolved) {
                                this.countResolved++;
                            } else {
                                this.countResolved = 1;
                            }

                            if(this.chats_resolved.data) {
                                let current_page = this.chats_resolved.current_page;
                                let last_page = this.chats_resolved.last_page;
                                let per_page = this.chats_resolved.per_page;
                                let data = this.chats_resolved.data;

                                if (data.length < per_page && current_page == 1) {
                                    this.chats_resolved.data.unshift(item);
                                } else {
                                    this.getChatsResolved(current_page);
                                }
                            }
                        }
                        // remover do footer
                        this.closeFooterActiveChat(item, true);
                    break;
                    case "splice":
                        //recupero o chat do evento e encontro a index dele no objeto
                        if(this.chats_resolved.data) {
                            let index = this.chats_resolved.data.findIndex(
                                element => element.chat_id === item.chat_id
                            );

                            let current_page = this.chats_resolved.current_page;
                            let last_page = this.chats_resolved.last_page;
                            let per_page = this.chats_resolved.per_page;
                            let data = this.chats_resolved.data;

                            if (index !== -1) {
                                if(data.length == 1 && current_page != 1) {
                                    this.getChatsResolved(current_page - 1);
                                } else if (data.length == per_page) {
                                    this.getChatsResolved(current_page);
                                } else if (data.length < per_page && current_page == 1) {
                                    this.chats_resolved.data.splice(index, 1);
                                }

                                if (!this.showChat && this.show_info && item.chat_id == this.info_chat_id) {
                                    this.rs_mouse = 'leave'
                                    this.chat.show = false;
                                }

                            } else {
                                if(data.length == 1 && current_page != 1) {
                                    this.getChatsResolved(current_page - 1);
                                } else {
                                    this.getChatsResolved(current_page);
                                }
                            }
                        }

                        if (this.countResolved) {
                            this.countResolved--;
                        }

                    break;
                }
            }
        },
        adminResolvedChannelActions(item) {
            let my_chat = this.cucd.findIndex(
                c =>
                    c.company_user_company_department_id ===
                        item.comp_user_comp_depart_id_current ||
                    this.session_user.id === item.user_agent_id
            );

            // se o "meus chats" estiver marcado só passa oq é meu, se nao passa tudo normal
            if (
                !this.filter_my_chats ||
                (my_chat !== -1 && this.filter_my_chats)
            ) {
                switch (item.action) {
                   case "push":
                        let i = this.filter_departments.findIndex(
                            f => f.id === item.company_department_id
                        );

                        if (i !== -1) {
                            if (this.countResolved) {
                                this.countResolved++;
                            } else {
                                this.countResolved = 1;
                            }

                            if(this.chats_resolved.data) {

                                let current_page = this.chats_resolved.current_page;
                                let last_page = this.chats_resolved.last_page;
                                let per_page = this.chats_resolved.per_page;
                                let data = this.chats_resolved.data;

                                if (data.length < per_page && current_page == 1) {
                                    this.chats_resolved.data.unshift(item);
                                } else {
                                    this.getChatsResolved(current_page);
                                }
                            }
                        }

                        // remover do footer
                        this.closeFooterActiveChat(item, true);
                    break;
                    case "splice":
                        //recupero o chat do evento e encontro a index dele no objeto
                        if(this.chats_resolved.data) {
                            let index = this.chats_resolved.data.findIndex(
                                element => element.chat_id === item.chat_id
                            );

                            let current_page = this.chats_resolved.current_page;
                            let last_page = this.chats_resolved.last_page;
                            let per_page = this.chats_resolved.per_page;
                            let data = this.chats_resolved.data;

                            if (index !== -1) {
                                if(data.length == 1 && current_page != 1) {
                                    this.getChatsResolved(current_page - 1);
                                } else if (data.length == per_page) {
                                    this.getChatsResolved(current_page);
                                } else if (data.length < per_page && current_page == 1) {
                                    this.chats_resolved.data.splice(index, 1);
                                }
                                if (!this.showChat && this.show_info && item.chat_id == this.info_chat_id) {
                                    this.rs_mouse = 'leave'
                                    this.chat.show = false;
                                }
                            } else {
                                if(data.length == 1 && current_page != 1) {
                                    this.getChatsResolved(current_page - 1);
                                } else {
                                    this.getChatsResolved(current_page);
                                }
                            }
                        }

                        if (this.countResolved) {
                            this.countResolved--;
                        }

                    break;
                }
            }
        },
        adminCanceledChannelActions(item) {
            let my_chat = this.cucd.findIndex(
                c =>
                    c.company_user_company_department_id ===
                        item.comp_user_comp_depart_id_current ||
                    this.session_user.id === item.user_agent_id
            );

            // se o "meus chats" estiver marcado só passa oq é meu, se nao passa tudo normal
            if (
                !this.filter_my_chats ||
                (my_chat !== -1 && this.filter_my_chats)
            ) {
                let i = this.filter_departments.findIndex(
                    f => f.id === item.company_department_id
                );

                if (i !== -1) {
                    if (this.countCanceled) {
                        this.countCanceled++;
                    } else {
                        this.countCanceled = 1;
                    }
                }

                if(this.chats_canceled.data) {

                    let current_page = this.chats_canceled.current_page;
                    let last_page = this.chats_canceled.last_page;
                    let per_page = this.chats_canceled.per_page;
                    let data = this.chats_canceled.data;

                    if (data.length < per_page && current_page == 1) {
                        this.chats_canceled.data.unshift(item);
                    } else {
                        this.getChatsCanceled(current_page);
                    }
                }

                // remover do footer
                this.closeFooterActiveChat(item, true);
            }
        },
        employeeClosedChannelActions(item) {
            let my_chat = this.cucd.findIndex(
                c =>
                    c.company_user_company_department_id ===
                        item.comp_user_comp_depart_id_current ||
                    this.session_user.id === item.user_agent_id
            );

            // se o "meus chats" estiver marcado só passa oq é meu, se nao passa tudo normal
            if (
                !this.filter_my_chats ||
                (my_chat !== -1 && this.filter_my_chats)
            ) {
                switch (item.action) {
                    case "push":
                        let i = this.filter_departments.findIndex(
                            f => f.id === item.company_department_id
                        );

                        if (i !== -1) {
                            if (this.countResolved) {
                                this.countResolved++;
                            } else {
                                this.countResolved = 1;
                            }

                            if(this.chats_resolved.data) {
                                let current_page = this.chats_resolved.current_page;
                                let last_page = this.chats_resolved.last_page;
                                let per_page = this.chats_resolved.per_page;
                                let data = this.chats_resolved.data;

                                if (data.length < per_page && current_page == 1) {
                                    this.chats_resolved.data.push(item);
                                } else {
                                    this.getChatsResolved(current_page);
                                }
                            }
                        }

                        // remover do footer
                        this.closeFooterActiveChat(item, true);
                    break;
                    case "splice":
                        //recupero o chat do evento e encontro a index dele no objeto
                        if(this.chats_resolved.data) {

                            let index = this.chats_resolved.data.findIndex(
                                element => element.chat_id === item.chat_id
                            );

                            let current_page = this.chats_resolved.current_page;
                            let last_page = this.chats_resolved.last_page;
                            let per_page = this.chats_resolved.per_page;
                            let data = this.chats_resolved.data;

                            if (index !== -1) {
                                if(data.length == 1 && current_page != 1) {
                                    this.getChatsResolved(current_page - 1);
                                } else if (data.length == per_page) {
                                    this.getChatsResolved(current_page);
                                } else if (data.length < per_page && current_page == 1) {
                                    this.chats_resolved.data.splice(index, 1);
                                }
                                if (!this.showChat && this.show_info && item.chat_id == this.info_chat_id) {
                                    this.rs_mouse = 'leave'
                                    this.chat.show = false;
                                }
                            } else {
                                if(data.length == 1 && current_page != 1) {
                                    this.getChatsResolved(current_page - 1);
                                } else {
                                    this.getChatsResolved(current_page);
                                }
                            }
                        }

                        if (this.countResolved) {
                            this.countResolved--;
                        }

                    break;
                }
            }
        },
        employeeResolvedChannelActions(item) {
            let my_chat = this.cucd.findIndex(
                c =>
                    c.company_user_company_department_id ===
                        item.comp_user_comp_depart_id_current ||
                    this.session_user.id === item.user_agent_id
            );

            // se o "meus chats" estiver marcado só passa oq é meu, se nao passa tudo normal
            if (
                !this.filter_my_chats ||
                (my_chat !== -1 && this.filter_my_chats)
            ) {
                switch (item.action) {
                    case "push":
                        let i = this.filter_departments.findIndex(
                            f => f.id === item.company_department_id
                        );

                        if (i !== -1) {
                            if (this.countResolved) {
                                this.countResolved++;
                            } else {
                                this.countResolved = 1;
                            }

                            if(this.chats_resolved.data) {
                                let current_page = this.chats_resolved.current_page;
                                let last_page = this.chats_resolved.last_page;
                                let per_page = this.chats_resolved.per_page;
                                let data = this.chats_resolved.data;

                                if (data.length < per_page && current_page == 1) {
                                    this.chats_resolved.data.unshift(item);
                                } else {
                                    this.getChatsResolved(current_page);
                                }
                            }
                        }

                        // remover do footer
                        this.closeFooterActiveChat(item, true);
                    break;
                    case "splice":
                        //recupero o chat do evento e encontro a index dele no objeto
                        if(this.chats_resolved.data) {
                            let index = this.chats_resolved.data.findIndex(
                                element => element.chat_id === item.chat_id
                            );

                            let current_page = this.chats_resolved.current_page;
                            let last_page = this.chats_resolved.last_page;
                            let per_page = this.chats_resolved.per_page;
                            let data = this.chats_resolved.data;

                            if (index !== -1) {
                                if(data.length == 1 && current_page != 1) {
                                    this.getChatsResolved(current_page - 1);
                                } else if (data.length == per_page) {
                                    this.getChatsResolved(current_page);
                                } else if (data.length < per_page && current_page == 1) {
                                    this.chats_resolved.data.splice(index, 1);
                                }
                                if (!this.showChat && this.show_info && item.chat_id == this.info_chat_id) {
                                    this.rs_mouse = 'leave'
                                    this.chat.show = false;
                                }
                            } else {
                                if(data.length == 1 && current_page != 1) {
                                    this.getChatsResolved(current_page - 1);
                                } else {
                                    this.getChatsResolved(current_page);
                                }
                            }
                        }

                        if (this.countResolved) {
                            this.countResolved--;
                        }

                    break;
                }
            }
        },
        employeeCanceledChannelActions(item) {

            let my_chat = this.cucd.findIndex(
                c =>
                    c.company_user_company_department_id ===
                        item.comp_user_comp_depart_id_current ||
                    this.session_user.id === item.user_agent_id
            );

            // se o "meus chats" estiver marcado só passa oq é meu, se nao passa tudo normal
            if (
                !this.filter_my_chats ||
                (my_chat !== -1 && this.filter_my_chats)
            ) {
                let i = this.filter_departments.findIndex(
                    f => f.id === item.company_department_id
                );

                if (i !== -1) {
                    if (this.countCanceled) {
                        this.countCanceled++;
                    } else {
                        this.countCanceled = 1;
                    }
                }


                if(this.chats_canceled.data) {
                    let current_page = this.chats_canceled.current_page;
                    let last_page = this.chats_canceled.last_page;
                    let per_page = this.chats_canceled.per_page;
                    let data = this.chats_canceled.data;

                    if (data.length < per_page && current_page == 1) {
                        this.chats_canceled.data.unshift(item);
                    } else {
                        this.getChatsCanceled(current_page);
                    }
                }

                // remover do footer
                this.closeFooterActiveChat(item, true);
            }
        },
        /** @STORAGE */
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
        addFooterActiveChat(chat, type) {
            chat.action = "add";
            axios
                .post("tabs", {
                    chat: chat,
                    type: type
                })
                .then(response => {});
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
        closeFooterActiveChat(chat, clear) {
            return new Promise((resolve, reject) => {
                if (this.chat.chat_id == chat.chat_id && clear) {
                    this.clearActiveChat();
                    this.showTableComponent('inProgress')
                }
                chat.action = "remove";
                axios
                    .post("tabs", {
                        chat: chat
                    })
                    .then(response => {
                        resolve();
                    });
            });
        },
        /** @OTHERS */
        openChat(chat, queue) {
            if(!this.loading_messages) {
                if (queue) {
                    this.openChatActions(chat);
                } else if (
                    (this.showChat == false && this.info_chat_id == chat.chat_id) ||
                    chat.chat_id !== this.chat.chat_id ||
                    (this.showChat === false && chat.chat_id !== this.chat.chat_id)
                ) {
                    this.clearActiveChat();
                    this.setInfo(chat, true);
                    this.openChatActions(chat);
                }
            }
        },
        openChatActions(chat) {
            this.loading_messages = true;
            this.getContentOnLocalStorage(chat.number);
            this.getChatHistory(chat.chat_id).then((data) => {
                this.setChatHistories(data).then(() => {
                    this.chatScrollTop();
                })
            });
            if (chat.status === "IN_PROGRESS") {
                this.addFooterActiveChat(chat, 'chat');
            }
            this.setIncognitoMode();
            this.getActions();
            this.showChatComponent();
            this.connectToMessageSentChannel(chat);
            this.connectToChatStatusChangerChannel(chat);
            if (chat.company_department_id) {
                this.getDepartmentComands(chat.company_department_id).then(() => {
                    this.chatScrollTop();
                });
            } else {
                this.getDepartmentComands(this.chat.companyDepartmentId).then(() => {
                    this.chatScrollTop();
                });
            }
            this.show_filter = false;
        },
        chatScrollTop() {
            var chat = document.getElementById("chat-main")
            if (chat) {
                chat.scrollTop = chat.scrollHeight - chat.clientHeight;
            }
        },
        connectToMessageSentChannel(chat) {
            Echo.join(`chat.${chat.chat_id}`).listen("MessageSent", event => {
                if (event.message.took_over) {
                    let index = this.session_user_cucd.findIndex(
                        element =>
                            element.company_user_company_department_id ===
                            event.message.comp_user_comp_depart_id_current
                    );
                    this.chat.comp_user_comp_depart_id_current =
                        event.message.comp_user_comp_depart_id_current;
                    this.chat.answered = 0;
                    this.chat.operator = event.message.operator;
                    if (index !== -1) {
                        this.take_on_chat = false;
                        this.incognito_mode = false;
                        this.incognito_id =
                            event.message.comp_user_comp_depart_id_current;
                        this.getActions();
                    } else {
                        this.closeFooterActiveChat(event.message, true);
                    }
                }
                this.setChatHistory(event.message, true).then(() => {
                    var chat = document.getElementById("chat-main")
                    if (chat) {
                        // chat.scrollTop = chat.scrollHeight - chat.clientHeight;
                        var current = chat.scrollTop;
                        var max = chat.scrollHeight - chat.clientHeight
                        var min = max - 500;
                        if (current > min) {
                            chat.scrollTop = chat.scrollHeight - chat.clientHeight;
                        }
                    }
                })
            });
        },
        connectToChatStatusChangerChannel(chat) {
            Echo.join(`chat-status-changer.${chat.chat_id}`).listen(
                "ChatStatusChanger",
                event => {
                    switch (event.item.status) {

                        case "CANCELED":
                            if (this.showChat) {
                                this.chat.status = 'CANCELED';
                                this.showTableComponent("inProgress");
                                Swal.fire({
                                    icon: "info",
                                    text: "O chat foi cancelado pelo cliente.",
                                    heightAuto: false,
                                    showCancelButton: false,
                                    confirmButtonText: `Ok`
                                });
                            }
                            break;
                        case "RESOLVED":
                             this.chat.status = 'RESOLVED';
                            if (this.showChat) {
                                this.showTableComponent("inProgress");
                                Swal.fire({
                                    icon: "info",
                                    text: "O chat foi marcado como resolvido.",
                                    heightAuto: false,
                                    showCancelButton: false,
                                    confirmButtonText: `Ok`
                                });
                            }
                            break;
                        case "TURN_INTO_TICKET_AT_CLOSING":
                            Swal.fire(this.$t('bs-chat-programmed-to-turn-into-a-ticket'), '', 'success')
                            this.chat.turn_into_ticket_at_closing = 1;
                            break;

                        case "TICKET":
                            if (this.showChat) {
                                this.showTableComponent("inProgress");
                                Swal.fire({
                                    icon: 'success',
                                    html: `${this.$t('bs-chat')} <bold>#${event.item.id}</bold> ${this.$t('bs-has-been-transformed-into')} ${this.$t('bs-ticket')} <bold>#${event.item.ticket_id}</bold>`,
                                    showConfirmButton: true,
                                });
                            }
                            break;

                        default:
                            this.chat.status = event.item.status;
                            break;
                    }
                }
            );
        },
        setIncognitoMode() {
            let index = this.cucd.findIndex(item => item.company_department_id === this.chat.companyDepartmentId);

            if (index !== -1) {
                if (this.cucd[index].company_user_company_department_id !== this.chat.comp_user_comp_depart_id_current) {
                    this.take_on_chat = true;
                    this.incognito_mode = true;
                } else {
                    this.take_on_chat = false;
                    this.incognito_mode = false;
                }
                this.incognito_id = this.cucd[index].company_user_company_department_id;
            } else {
                this.take_on_chat = true;
                this.incognito_mode = true;
            }
        },
        getExtensions() {
            this.extensions = this.extImages.concat(
                this.extDocuments,
                this.extSpreadsheets,
                this.extPresentation
            );
        },
        handleDepartmentsFilter() {
            /** if the variable is empty, I put all departments in it */
            if (!this.company_department.length) {
                this.company_department = this.departments;
            }
            /** set filter_departments variable */
            this.filter_departments = this.company_department;
            //this.getChatsOnQueue();
            this.showTableQueue = false;
            this.showTableComponent('queue');
            /** delete settings prop for each department in filter variable, this way will not send settings to the route when the filters changes */
            this.filter_departments.forEach(element => {
                delete element.settings;
            });


        },
        changeTableKeys() {
            /** update keys */
            this.key_queue = this.key_queue += 1;
            this.key_progress = this.key_progress += 1;
            this.key_transferred = this.key_transferred += 1;
            this.key_closed = this.key_closed += 1;
            this.key_resolved = this.key_resolved += 1;
            this.key_canceled = this.key_canceled += 1;
        },
        openClientHistory(id) {
            var vm = this;

            const api = `client/get-client-history`;
            axios
                .get(api, {
                    params: {
                        client_id: id
                    }
                })
                .then(({ data }) => {
                    var itemsProcessed = 0;

                    var clientChatHistory = [];
                    var clientTicketHistory = [];

                    data.forEach(element => {
                        itemsProcessed++;

                        if (
                            element.type === "TICKET" ||
                            element.type === "CHANGED_TO_TICKET"
                        ) {
                            clientTicketHistory.push(element);
                        } else {
                            clientChatHistory.push(element);
                        }

                        if(itemsProcessed === data.length) {

                            vm.clientTicketHistory = clientTicketHistory;
                            vm.clientChatHistory = clientChatHistory;

                        }

                    });

                    $("#modalClientHistory").modal("show");
                });
        },
        openCategory() {
            this.showCategory = true;
            this.$store.state.showAlertCategory = false;
        },
        activeIncognito() {
            if (!this.take_on_chat) {
                this.incognito_mode = !this.incognito_mode;
            } else {
                this.incognito_mode = true;
            }
        },
        concatEmoji(emoji) {
            this.chat.content = this.chat.content.concat(emoji.data);
            // document.getElementById("input").focus();
        },
        LI(value) {
            if (value == null) {
                return "LI";
            }
            return value.substr(0, 2);
        },
        onResize(e) {
            if ($(window).width() <= 1120) {
                var sidebar_is_mini = this.$store.state.sidebar_is_mini;
                if (this.show_info && this.show_filter && sidebar_is_mini) {
                    this.$store.commit("toggleSidebarMini");
                }
            }
            if ($(window).width() <= 992) {
                this.isMobile = true;
            } else {
                this.isMobile = false;
                this.$root.$emit('bv::hide::popover')
            }
        },
        nameWithLang({ name, status }) {
            return `${name} — [${status}]`;
        },
        /** @NOTIFICATIONS */
        notifyQueue() {
            if (!this.showTableQueue) {
                this.notify.onQueue = true;
            }
        },
        notifyInProgress() {
            if (!this.showTableInProgress) {
                this.notify.inProgress = true;
            }
            //this.notify.audio.play();
        },
        notifyTransferred() {
            if (!this.showTableTransferred) {
                this.notify.transferred = true;
            }
        },
        notifyClosed() {
            if (!this.showTableClosed) {
                this.notify.closed = true;
            }
        },
        notifyResolved() {
            if (!this.showTableResolved) {
                this.notify.resolved = true;
            }
        },
        notifyCanceled() {
            if (!this.showTableCanceled) {
                this.notify.canceled = true;
            }
        },
        getAgentsByDepartment(id) {
            return new Promise((resolve, reject) => {
                var url = 'user-auth/get-agents-by-department';
                axios.get(url, {
                    params: {
                        company_department_id: id
                    }
                })
                .then(({data}) => {
                    resolve(data.agents);
                })
            })
        },
        getAllAgentsByDepartment(id) {
            return new Promise((resolve, reject) => {
                var url = 'user-auth/get-all-agents-by-department';
                axios.get(url, {
                    params: {
                        company_department_id: id
                    }
                })
                .then(({data}) => {
                    resolve(data.agents);
                })
            })
        },
        /** @SHOW */
        showAgentsFormByDepartment(selected_agent = false) {
            if (this.selected_department !== null) {
                var vm = this;
                vm.showAgentForm = false;
                vm.agents_to_transfer = [];
                vm.getAllAgentsByDepartment(vm.selected_department.id).then((agents) => {
                    vm.agents_to_transfer.push({
                        'company_user_company_department_id': null,
                        'id': null,
                        'name': vm.$t('bs-without-attendant'),
                    })
                    if (agents.length) {
                        var i = 0;
                        agents.forEach(element => {
                            i++
                            vm.agents_to_transfer.push(element)
                            if (i == agents.length) {
                                if (selected_agent == false) {
                                    vm.selected_agent = null;
                                } else {
                                    let idx = vm.agents_to_transfer.findIndex(
                                        element => element.company_user_company_department_id === selected_agent
                                    );

                                    if (idx !== -1) {
                                        vm.selected_agent = vm.agents_to_transfer[idx];
                                    }

                                }
                                vm.showAgentForm = true;
                            }
                        });
                    } else {
                        vm.showAgentForm = true;
                    }
                })
            }
        },
        showModalAtendent() {
            if (this.selected_department !== null) {
                var vm = this;
                vm.$loading(true);
                vm.getAgentsByDepartment(vm.selected_department.id).then((agents) => {
                    if (agents.length) {
                        vm.agents_to_transfer = agents;
                        $("#modalDepartment").modal("hide");
                        vm.$loading(false);
                        $("#modalAtendent").modal("show");
                    } else {
                        vm.$loading(false);
                        Swal.fire({
                            heightAuto: false,
                            icon: "error",
                            title: vm.$t("bs-error"),
                            text: vm.$t(
                                "bs-no-active-attendants-in-the-department"
                            )
                        });
                    }
                })
            }
        },
        treeUnselect() {
            let item = this.$refs.tree.findAll({ state: { selected: true } })
            item.unselect();

        },
        showTableComponent(table, forceGet = false) {
            if (this.hideOnSmall) {
                this.hideOnSmall = !this.hideOnSmall;
            }
            this.hidden = true;
            switch (table) {
                case "inProgress":
                    if (!this.showTableInProgress) {
                        this.treeUnselect();
                        //this.chats_in_progress = [];
                        //this.getChatsInProgress();
                        this.showTableInProgress = true;
                        this.showTableQueue = false;
                        this.showTableTransferred = false;
                        this.showTableClosed = false;
                        this.showTableResolved = false;
                        this.showTableCanceled = false;
                        this.notify.inProgress = false;
                        let selection = this.$refs.tree.findAll({ text: this.$t("bs-in-progress") })
                        selection.select(true)
                    }
                    break;
                case "queue":
                    if (!this.showTableQueue || forceGet)  {
                        this.$store.commit("getChatsOnQueue", this.url_prefix);
                        this.treeUnselect();
                        //this.getChatsOnQueue();
                        //this.chats_on_queue = {};
                        this.showTableQueue = true;
                        this.showTableInProgress = false;
                        this.showTableTransferred = false;
                        this.showTableClosed = false;
                        this.showTableResolved = false;
                        this.showTableCanceled = false;
                        this.notify.onQueue = false;
                        let selection = this.$refs.tree.findAll({ text: this.$t("bs-in-queue") })
                        selection.select(true)
                    }
                    break;
                case "transferred":
                    if (!this.showTableTransferred) {
                        this.chats_transferred = {};
                        this.showTableTransferred = true;
                        this.showTableInProgress = false;
                        this.showTableQueue = false;
                        this.showTableClosed = false;
                        this.showTableResolved = false;
                        this.showTableCanceled = false;
                        this.notify.transferred = false;
                    }
                    break;
                case "closed":
                    if (!this.showTableClosed) {
                        this.chats_closed = {};
                        this.showTableTransferred = false;
                        this.showTableInProgress = false;
                        this.showTableQueue = false;
                        this.showTableClosed = true;
                        this.showTableResolved = false;
                        this.showTableCanceled = false;
                        this.notify.closed = false;
                    }
                    break;
                case "resolved":
                    if (!this.showTableResolved) {
                        this.treeUnselect();
                        this.getChatsResolved();
                        this.chats_resolved = {};
                        this.showTableTransferred = false;
                        this.showTableInProgress = false;
                        this.showTableQueue = false;
                        this.showTableClosed = false;
                        this.showTableResolved = true;
                        this.showTableCanceled = false;
                        this.notify.resolved = false;
                        let selection = this.$refs.tree.findAll({ text: this.$t("bs-finished-s") })
                        selection.select(true)
                    }
                    break;
                case "canceled":
                    if (!this.showTableCanceled) {
                        this.treeUnselect();
                        this.getChatsCanceled();
                        this.chats_canceled = {};
                        this.showTableTransferred = false;
                        this.showTableInProgress = false;
                        this.showTableQueue = false;
                        this.showTableClosed = false;
                        this.showTableResolved = false;
                        this.showTableCanceled = true;
                        this.notify.canceled = false;
                        let selection = this.$refs.tree.findAll({ text: this.$t("bs-lost-s") })
                        selection.select(true)
                    }
                    break;
            }
            this.clearActiveChat();
        },
        showChatComponent() {
            this.chat_history = [];
            this.showTableQueue = false;
            this.showTableInProgress = false;
            this.showTableTransferred = false;
            this.showTableClosed = false;
            this.showTableResolved = false;
            this.showTableCanceled = false;
            this.showChat = true;
            this.hideOnSmall = false;
        },
        backToMenu() {
            this.clearActiveChat();
            this.hideOnSmall = !this.hideOnSmall;
            this.hidden = true;
            this.showTableQueue = false;
            this.showTableInProgress = false;
            this.showTableTransferred = true;
            this.showTableClosed = false;
            this.showTableResolved = false;
            this.showTableCanceled = false;
            //this.showTableQueue = true;
            //this.showTableComponent('queue');
        },
        /** @CLEAR */
        clearActiveChat() {
            return new Promise((resolve, reject) => {
                if (this.chat.chat_id != "" && this.chat.chat_id != undefined) {
                    this.putContentOnLocalStorage({
                        number: this.chat.number,
                        content: this.chat.content
                    });
                }
                Echo.leave(`chat.${this.chat.chat_id}`);
                Echo.leave(`chat-status-changer.${this.chat.chat_id}`);
                this.showChat = false;
                let client = this.chat.client;
                Object.keys(this.chat).forEach(k => (this.chat[k] = ""));
                this.chat.client = client;
                Object.keys(this.chat.client).forEach(
                    k => (this.chat.client[k] = "")
                );
                this.incognito_mode = false;
                this.incognito_id = null;
                this.chat.show = false;
                this.info_minimized = false;
                this.rs_mouse = 'leave'
                this.chat_catched_id = ""
                resolve();
            });
        },
        clearAllChats() {
            /** cleat actual chat */
            this.clearActiveChat();
            /** reset skip */
            this.skip = 0;
            /** clear chats */
            //this.chats_on_queue = {};
            //this.chats_in_progress = [];
            this.chats_transferred = {};
            this.chats_closed = {};
            this.chats_resolved = {};
            this.chats_canceled = {};
        },
        resetTable(ref) {
            this.skip = 0;
            switch (ref) {
                case "tableQueue":
                    //limpar array
                    //this.chats_on_queue = {};
                    break;
                case "tableInProgress":
                    //limpar array
                    //this.chats_in_progress = [];
                    break;
                case "tableClosed":
                    this.chats_closed = {};
                    break;
                case "tableResolved":
                    this.chats_resolved = {};
                    break;
                case "tableTransferred":
                    //limpar o array
                    this.chats_transferred = {};
                    break;
                case "tableCanceled":
                    this.chats_canceled = {};
                    break;
            }
        },
        /** @TIMEZONE */
        UTCtoClientTZ(h, tz) {
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
                timeZone: tz
            });
            return moment(converted_time, "DD/MM/YYYY HH:mm:ss").format(
                "YYYY-MM-DD HH:mm:ss"
            );
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
                timeZone: this.tz
            });

            var mt = require("moment-timezone");
            return mt(converted_time, "DD/MM/YYYY HH:mm:ss")
                .tz(this.tz)
                .locale(this.session_user.language)
                .format("LT");
        },
        calculateWaitingTime(d, c) {
      var moment = require("moment-timezone");
      var startDateTime = moment
        .tz(d, Intl.DateTimeFormat().resolvedOptions().timeZone)
        .toDate();
      var startStamp = startDateTime.valueOf();

      var newDate = new Date();
      var newStamp = newDate.getTime();

      var timer;

      var diff_0 = false;

      function updateClock() {
        newDate = new Date();
        newStamp = newDate.getTime();
        var diff = Math.round((newStamp - startStamp) / 1000);

        // var d = Math.floor(diff / (24 * 60 * 60));
        // diff = diff - d * 24 * 60 * 60;
        var h = Math.floor(diff / (60 * 60));
        diff = diff - h * 60 * 60;
        var m = Math.floor(diff / 60);
        diff = diff - m * 60;
        var s = diff;

        if (h < 10) {
          h = "0" + h;
        }

        if (m < 10) {
          m = "0" + m;
        }

        if (s < 10) {
          s = "0" + s;
        }

        if (document.getElementById(c) !== null) {
          document.getElementById(c).innerHTML = h + ":" + m + ":" + s;
        }
      }

      setInterval(updateClock, 1000);
    },
    },
    computed: {
        admin(){
            return this.$store.state.session_user_permissions[0]["chat_admin"] || this.is_admin;
        },
        current_table() {
            if (this.showTableQueue) {
                return this.$t("bs-in-queue");
            } else if (this.showTableInProgress) {
                return this.$t("bs-in-progress")
            } else if (this.showTableResolved) {
                return this.$t("bs-finished-s")
            } else if (this.showTableCanceled) {
                return this.$t("bs-lost-s")
            }
        },
        current_table_count() {
            if (this.showTableQueue) {
                return this.countOnQueue;
            } else if (this.showTableInProgress) {
                return this.countInProgress;
            } else if (this.showTableResolved) {
                return this.countResolved;
            } else if (this.showTableCanceled) {
                return this.countCanceled;
            }
        },
        pp_selected: {
            get() {
                return this.$store.getters.getPerPage;
            },
            set(value) {
                this.$store.commit("setPerPage", value);
            }
        },
        tabs_length() {
            return this.$store.state.chatsFooter.length;
        },
        computedChat: function() {
            return JSON.parse(JSON.stringify(this.chat)); // copy object and remove reactivity
        },
        filter_my_chats: {
            get() {
                return this.$store.state.filter_my_chats;
            },
            set(value) {
                this.$store.commit("updateMyChatsFilter", value);
            }
        },
        filter_not_category: {
            get() {
                return this.$store.state.filter_not_category;
            },
            set(value) {
                this.$store.commit("updateFilterNotCategory", value);
            }
        },
        filter_departments: {
            get() {
                return this.$store.state.filter_departments;
            },
            set(value) {
                this.$store.commit("updateChatFilterDepartments", value);
                localStorage.setItem(
                    "filter_departments",
                    JSON.stringify(this.filter_departments)
                );
            }
        },
        chats_in_progress: {
            get() {
                return this.$store.state.chats_in_progress;
            }
        },
        chats_on_queue: {
            get() {
                return this.$store.state.chats_in_queue;
            }
        },
        resolved() {
            return this.chats_resolved;
        },
        canceled() {
            return this.chats_canceled;
        },
        show_info() {

                if(this.rs_mouse == 'over') {
                    return true;
                } else if (this.rs_mouse == 'leave') {
                    return false;
                } else if(this.chat.show) {
                    return true;
                }

        },
        validateFormTurnIntoTicket() {
            return this.ticket_description !== '' && this.selected_agent != null && this.selected_department != null;
        }
    },
    watch: {
        company_department() {
            this.aux_dept = [];
            this.company_department.forEach(element => {
                this.aux_dept.push(element.id);
            });
            /** handle departments filter*/
            this.handleDepartmentsFilter();
            this.$store.commit("getChatsInProgress", this.url_prefix);
            this.$store.commit("getChatsOnQueue", this.url_prefix);
            this.getAgentTablesCount();
            /** clear all chats */
            this.clearAllChats();
            /** change table keys */
            this.changeTableKeys();
        },
        filter_not_category() {
            /** cleat actual chat */
            this.clearActiveChat();
            /** reset skip */
            this.skip = 0;
            /** clear chats */
            this.$store.commit("getChatsInProgress", this.url_prefix);
            this.chats_transferred = [];
            this.chats_closed = [];
            this.chats_resolved = [];
            this.chats_canceled = [];
            /** change table keys */
            this.changeTableKeys();
            this.getAgentTablesCount();
            this.showTableComponent('queue', true);
        },
        filter_my_chats() {
            /** cleat actual chat */
            this.clearActiveChat();
            /** reset skip */
            this.skip = 0;
            /** clear chats */
            this.$store.commit("getChatsInProgress", this.url_prefix);
            this.chats_transferred = [];
            this.chats_closed = [];
            this.chats_resolved = [];
            this.chats_canceled = [];
            /** change table keys */
            this.changeTableKeys();
            this.getAgentTablesCount();
            this.showTableComponent('queue')
        },
        "$store.state.chatsFooter": function() {
            if (
                localStorage.getItem("chatsFooter") &&
                localStorage.getItem("chatsFooter") !== "[]"
            ) {
                this.footerActiveChat = true;
            } else {
                this.footerActiveChat = false;
            }
        },
        "$store.state.chats_in_progress": function() {
            this.countInProgress = this.$store.state.chats_in_progress.length;
        },
        "$store.state.chats_in_queue": function() {
            this.countOnQueue = this.$store.state.chats_in_queue.length;
        },
        computedChat: {
            deep: true,
            handler: function(newVal, oldVal) {
                var chat = document.getElementById("chat-main")
                if (chat) {
                    // chat.scrollTop = chat.scrollHeight - chat.clientHeight;
                    var current = chat.scrollTop;
                    var max = chat.scrollHeight - chat.clientHeight
                    var min = max - 500;
                    if (current > min) {
                        chat.scrollTop = chat.scrollHeight - chat.clientHeight;
                    }
                }
                // transição ao mudar de chat
                // if (oldVal.number && newVal.number !== oldVal.number) {
                //     this.chat.show = false;
                //     setTimeout(() => {
                //         this.chat.show = true;
                //     }, 100);
                // }
                if (newVal !== oldVal) {
                    this.showTranslate = false;
                    setTimeout(() => {
                        this.showTranslate = true;
                    }, 200);
                }
            }
        },
        showChat(newVal, oldVal) {
            if (newVal == true) {
                this.treeUnselect();
            }
        },
        queue(newVal, oldVal) {
            if (newVal.current_page !== oldVal.current_page) {
                this.chat.show = false;
                this.rs_mouse = 'leave';
            }
        },
        resolved(newVal, oldVal) {
            if (newVal.current_page !== oldVal.current_page) {
                this.chat.show = false;
                this.rs_mouse = 'leave';
            }
        },
        canceled(newVal, oldVal) {
            if (newVal.current_page !== oldVal.current_page) {
                this.chat.show = false;
                this.rs_mouse = 'leave';
            }
        },
        pp_selected() {
            if (this.showTableResolved) {
                this.getChatsResolved();
            } else if (this.showTableCanceled) {
                this.getChatsCanceled();
            }
        },
        questionary(newVal, oldVal) {
            this.forceReloadQuestionary = false;
            setTimeout(() => {
                this.forceReloadQuestionary = true;
            }, 4);
        }
    }
};
</script>

<style scoped>
    .borderc{
        border: 1px solid #C4D1E0
    }
    .shaddow{
        box-shadow: 1px 1px 4px 0px #00000029;
    }
    .color-r-t{
        color:#4D5D71CC
    }
    .color-r{
        color:#4D5D71
    }
    .grid-block{
        display: block !important;
    }
    .fz-12{
        font-size: 12px !important;
        color: #2A3C51;
    }
    .fz-14{
        font-size: 14px !important;
    }

    .center-t{
        text-align: center;
    }
</style>