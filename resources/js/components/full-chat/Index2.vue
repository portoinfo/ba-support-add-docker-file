<template>
    <div
        id="ba-hd__chat"
        class="chat"
        :class="{
            'info-hidden': !show_info,
            'info-showed': show_info,
            'filter-hidden': !show_filter,
            'filter-showed': show_filter,
            'chat-showed': showChat,
            'chat-hidden': !showChat,
            'is_mobile': isMobile,
        }"
    >
        <div id="ba-hd__header" v-show="!(showChat && isMobile)">
            <div class="header_menu_mob position-relative h-100">
                <b-badge  v-if="isMobile && !showTableQueue && countOnQueue > 0" class="badge-ntf-queue" pill variant="danger">{{ countOnQueue }}</b-badge>
                <b-button id="popover-button-menu-monile" size="sm" variant="light" class="mr-3" v-show="isMobile">
                    <span class="bs-ico">&#xe5d2;</span>
                </b-button>
                <b-popover ref="popover_menu" target="popover-button-menu-monile" id="popover_menu" placement="bottomright">
                    <tree :data="tree_items" ref="tree_mobile"  @node:selected="onNodeSelected">
                        <span class="tree-text" slot-scope="{ node }" :class="{'tree-text-ml': node.data.ml}">
                            <template v-if="!node.data.icon">
                                {{ node.text }}
                                <template v-if="loading_counters && node.data.id !== 'in_progress' && node.data.id !== 'queue'">
                                    <b-spinner small variant="primary" label="Spinning"></b-spinner>
                                </template>
                                <template v-else>
                                    ({{ returnCountByStatus(node.data.id) }})
                                </template>
                            </template>
                            <template v-else>
                                <vue-material-icon :name="node.data.icon" :size="18" />
                                {{ node.text }}
                                <template v-if="loading_counters && node.data.id !== 'in_progress' && node.data.id !== 'queue'">
                                    <b-spinner small variant="primary" label="Spinning"></b-spinner>
                                </template>
                                <template v-else>
                                    ({{ returnCountByStatus(node.data.id) }})
                                </template>
                            </template>
                        </span>
                    </tree>
                </b-popover>
            </div>
            <div class="header_title d-table h-100">
                <span class="ba-hd__title d-table-cell align-middle">
                    {{ $t("bs-chats") }}
                </span>
                <span class="d-table-cell align-middle" v-if="isMobile && !showChat">
                    <b-badge pill  variant="primary" class="ml-3">{{ current_table }}</b-badge>
                    <b-badge pill  variant="light">{{ current_table_count }}</b-badge>
                </span>
            </div>
            <div class="header_filters position-relative h-100">
                <b-button size="sm" variant="light" @click="show_filter = true" v-if="!showChat && !isMobile">
                    <span class="bs-ico">&#xef4f;</span>
                    <b>{{ $t('bs-chat-filter') }}</b>
                </b-button>
                <b-button v-b-toggle.sidebar-right-filter size="sm" variant="light" v-if="isMobile">
                    <span class="bs-ico">&#xef4f;</span>
                </b-button>
                <b-button size="sm" variant="light" @click="modalDatabase">
                    <i class="fa fa-database" aria-hidden="true"></i>
                </b-button>
                <b-sidebar
                    v-if="isMobile"
                    id="sidebar-right-filter"
                    :title="$t('bs-chat-filter')"
                    right
                    backdrop
                    shadow
                    z-index="3"
                    bg-variant="white"
                    class="text-left"
                    @shown="$root.$emit('bv::hide::popover', 'popover_menu')"
                >
                    <div class="pl-3 pr-3 pt-2">
                        <b-form-group v-if="full_list">
                            <b-form-checkbox
                                id="checkbox-1"
                                v-model="filter_my_chats"
                                name="checkbox-1"
                                class="mt-2 fz-18"
                            >
                                <b>{{ $t("bs-my-chats") }}</b>
                            </b-form-checkbox>
                        </b-form-group>
                    </div>

                    <div class="pl-3">
                        <b-form-group v-if="full_list">
                            <b-form-checkbox id="checkbox-2" v-model="filter_not_category" name="checkbox-2"
                                class=" fz-18">
                                <b>{{$t('bs-not-categorized')}}</b>
                            </b-form-checkbox>
                        </b-form-group>
                    </div>

                    <hr style="width:100%;text-align:left;margin-left:0">

                    <div class="pl-3 pr-3">
                        <label>
                            <b>{{ $t("bs-departments") }}</b>
                        </label>
                        <b-form-group v-slot="{ariaDescribedby}">
                            <b-form-checkbox-group
                                :options="departments"
                                :aria-describedby="ariaDescribedby"
                                stacked
                                v-model="aux_dept"
                                @change="setDepts()"
                            ></b-form-checkbox-group>
                        </b-form-group>
                    </div>
                </b-sidebar>
            </div>
        </div>

        <div v-show="!isMobile" class="ba-hd__left-sidebar">
            <div class="ba-hd__card-content">
                <tree :data="tree_items" ref="tree"  @node:selected="onNodeSelected">
                    <span class="tree-text" slot-scope="{ node }" :class="{'tree-text-ml': node.data.ml}">
                        <template v-if="!node.data.icon">
                            {{ node.text }}
                            <template v-if="loading_counters && node.data.id !== 'in_progress' && node.data.id !== 'queue'">
                                <b-spinner small variant="primary" label="Spinning"></b-spinner>
                            </template>
                            <template v-else>
                                ({{ returnCountByStatus(node.data.id) }})
                            </template>
                        </template>
                        <template v-else>
                            <vue-material-icon :name="node.data.icon" :size="18" />
                            {{ node.text }}
                            <template v-if="loading_counters && node.data.id !== 'in_progress' && node.data.id !== 'queue'">
                                <b-spinner small variant="primary" label="Spinning"></b-spinner>
                            </template>
                            <template v-else>
                                ({{ returnCountByStatus(node.data.id) }})
                            </template>
                        </template>
                    </span>
                </tree>
            </div>
        </div>

        <div id="ba-hd__main">
            <div class="ba-hd__card-content">
                <table-queue2
                    v-if="showTableQueue"
                    :chat_admin="admin"
                    :chat_queue_full_control="chat_queue_full_control"
                    :setInfo="setInfo"
                    :catchChat="catchChat"
                    :company_department="company_department"
                    :resetTable="resetTable"
                    :hideOnSmall="hideOnSmall"
                    :user="session_user"
                    :chat_number_info="chat.number"
                    :chat_show_info="chat.show"
                    :loading2catchChat="loading2catchChat"
                />
                <table-in-progress2
                    v-if="showTableInProgress"
                    :setInfo="setInfo"
                    :openChat="openChat"
                    :user="session_user"
                    :chat="chat"
                    :company_department="company_department"
                    :hideOnSmall="hideOnSmall"
                    :resetTable="resetTable"
                    :footerActiveChat="footerActiveChat"
                    :key="key_progress"
                    :url_prefix="url_prefix"
                    :chat_number_info="chat.number"
                    :chat_show_info="chat.show"
                    :cucd="cucd"
                />

                <table-resolved2
                    v-if="showTableResolved"
                    :setInfo="setInfo"
                    :user="session_user"
                    :chat="chat"
                    :chats="chats_resolved"
                    :getChatsResolved="getChatsResolved"
                    :company_department="company_department"
                    :hideOnSmall="hideOnSmall"
                    :openChat="openChat"
                    :resetTable="resetTable"
                    :countResolved="countResolved"
                    :footerActiveChat="footerActiveChat"
                    :chat_number_info="chat.number"
                    :chat_show_info="chat.show"
                />

                <table-canceled2
                    v-if="showTableCanceled"
                    :setInfo="setInfo"
                    :user="session_user"
                    :chat="chat"
                    :chats="chats_canceled"
                    :getChatsCanceled="getChatsCanceled"
                    :company_department="company_department"
                    :hideOnSmall="hideOnSmall"
                    :openChat="openChat"
                    :resetTable="resetTable"
                    :countCanceled="countCanceled"
                    :footerActiveChat="footerActiveChat"
                    :chat_number_info="chat.number"
                    :chat_show_info="chat.show"
                />

                <!-- =============================================================== chat =============================================================== -->
                <col-chat2
                    v-show="showChat"
                    :chat="chat"
                    :sendMessage="sendMessage"
                    :hideOnSmall="hideOnSmall"
                    :incognito_mode="incognito_mode"
                    :incognito_id="incognito_id"
                    :footerActiveChat="footerActiveChat"
                    :departmentCommands="departmentCommands"
                    :getContentOnLocalStorage="getContentOnLocalStorage"
                    :clearActiveChat="clearActiveChat"
                    :showTableComponent="showTableComponent"
                    :isMobile="isMobile"
                    :restriction="session_user_permissions[0]"

                >
                    <template slot="messages">
                        <template v-if="!loading_messages">

                            <component
                                v-for="(message, index) in chat_history_robot"
                                :key="index"
                                :is="setMessageComponentRobot(message)"
                                v-bind="setMessagePropsRobot(message, index)"
                                :formatTime="UTCtoClientTZ2"
                            />

                            <message-type-questionary
                                v-if="forceReloadQuestionary && questionary.length"
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
                        </template>
                        <div  v-else class="h-100 d-flex justify-content-center align-items-center bg-white">
                            <vue-loading type="bars" color="#0080FC" :size="{ width: '50px', height: '50px' }"></vue-loading>
                        </div>
                    </template>
                    <!-- - - - - -  C O L U N A  D O  M E I O  [ CHAT - INPUT ] - - - - - -->
                    <template v-if="this.chat.status === 'IN_PROGRESS'">
                        <template slot="chat-buttons">
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

                            <button :id="`popover-4`">
                                <span class="d-inline-flex">
                                    <span style="font-size: 18px" class="material-icons-outlined">build</span>
                                    <b class="ml-1">{{ $t('bs-action') }}</b>
                                </span>
                            </button>

                            <b-popover
                                :target="`popover-4`"
                                :placement="'topcenter'"
                                title=""
                                triggers="hover focus"
                                :id="'pop4'"
                                ref="pop4"
                            >
                                <b-list-group>
                                    <b-list-group-item  v-for="(action, index) in actions" :key="index" @click.prevent="executeAction(action)">
                                        <b-row>
                                            <b-col cols="1">
                                                    <span class="bs-ico action_icon">{{action.icon}}</span>
                                            </b-col>
                                            <b-col class="text-left">
                                                <span class="action-title">{{ action.title }}</span>
                                            </b-col>
                                        </b-row>
                                    </b-list-group-item>
                                </b-list-group>
                            </b-popover>

                            <button :id="`popover-2`" v-show="!isMobile">
                                <span style="font-size: 18px" class="material-icons-outlined">quickreply</span>
                            </button>

                            <b-row
                                v-if="!isMobile && departmentCommands.length > 0"
                            >
                                <b-col>
                                    <b-popover
                                        :target="`popover-2`"
                                        :placement="'topcenter'"
                                        title=""
                                        triggers="hover focus"
                                        :id="'pop2'"
                                    >
                                        <div class="card border-0">
                                            <div class="card-header text-center">
                                                {{
                                                    $t(
                                                        "bs-type-the-command-and-then-press-tab"
                                                    )
                                                }}
                                            </div>
                                            <div class="card-body p-0" v-scrollbar="{alwaysShowTracks: true}">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th
                                                                class="w-25 text-center"
                                                            >
                                                                {{
                                                                    $t("bs-command")
                                                                }}
                                                            </th>
                                                            <th class="w-75">
                                                                {{
                                                                    $t(
                                                                        "bs-description"
                                                                    )
                                                                }}
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr
                                                            v-for="(row,
                                                            index) in departmentCommands"
                                                            :key="index"
                                                        >
                                                            <th
                                                                scope="row"
                                                                class="text-center"
                                                            >
                                                                <i>{{
                                                                    row.command
                                                                }}</i>
                                                            </th>
                                                            <td>
                                                                {{
                                                                    row.description
                                                                }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </b-popover>
                                </b-col>
                            </b-row>

                            <button-incognito
                                :chat="chat"
                                :activeIncognito="activeIncognito"
                                :incognito_mode="incognito_mode"
                                :incognito_id="incognito_id"
                            />

                            <button v-b-modal.modal-translate>
                                <span style="font-size: 18px" class="material-icons-outlined">g_translate</span>
                            </button>



                            <b-tooltip
                                target="incognito"
                                triggers="hover"
                                placement="top"
                                variant="secondary"
                            >
                                {{ $t("bs-incognito-mode-messages-not-visible") }}
                            </b-tooltip>

                        </template>
                        <template slot="select" v-if="files != ''">
                            <multiselect
                                :hide-selected="true"
                                v-model="files"
                                :searchable="false"
                                :options="files"
                                :multiple="true"
                                :close-on-select="false"
                                label="name"
                                track-by="name"
                                class="border-0"
                            >
                            </multiselect>
                        </template>
                    </template>
                </col-chat2>
            </div>
        </div>

        <div v-show="!isMobile" class="ba-hd__right-sidebar non-selectable">
            <div class="ba-hd__card-content">
                <header class="pr-2 pl-2">
                    <div style="display: grid; grid-template: auto / auto  auto;" v-if="chat.show">
                        <div class="text-left d-table">
                            <b v-if="!info_minimized" class="d-table-cell align-middle">{{ $t('bs-chat-information') }}</b>
                        </div>
                        <div v-if="showChat" class="text-right">
                            <span  v-if="!info_minimized" class="bs-ico cursor-pointer" @click="info_minimized = true; rs_mouse = 'leave'">&#xe931;</span>
                            <span  v-else class="bs-ico cursor-pointer pt-1" @click="info_minimized = false; rs_mouse = 'over'">&#xe8f4;</span>
                        </div>
                        <div v-else class="text-right pt-1">
                            <span class="bs-ico cursor-pointer" @click="chat.show = false; rs_mouse = 'leave'">&#xe5cd;</span>
                        </div>
                    </div>
                </header>
                <div v-if="!chat.show && !showChat || (chat.show && info_minimized) && (showChat && info_minimized)" class="h-100 w-100 d-table text-center no-data">
                    <span class="align-middle d-table-cell w-100">
                        <img class="m-auto d-block" src="images/icons/olho.svg" width="45px">
                        <br>
                        <span v-show="show_info" class="mr-3 ml-3">{{$t('bs-no-client-information')}}</span>
                    </span>
                </div>
                <chat-info v-else
                    :chat="chat"
                    :showChat="showChat"
                    :user="session_user"
                    :footerActiveChat="footerActiveChat"
                    :openClientHistory="openClientHistory"
                    :openCategory="openCategory" 
                    :chat_admin="admin"
                    :chat_queue_full_control="chat_queue_full_control"
                    :chats_on_queue="chats_on_queue"
                    :chats_resolved="chats_resolved"
                    :chats_canceled="chats_canceled"
                    :openChat="openChat"
                    :restriction="session_user_permissions[0]"
                >
                </chat-info>
            </div>
        </div>
        <div v-show="!isMobile" v-if="show_filter && !showChat" class="ba-hd__right-sidebar2">
            <div class="ba-hd__card-content">
                <header class="pr-2 pl-2">
                    <div style="display: grid; grid-template: auto / auto  auto;">
                        <div class="text-left d-table">
                            <b class="d-table-cell align-middle">{{ $t('bs-filters') }}</b>
                        </div>
                        <div class="text-right pt-1">
                            <span class="bs-ico cursor-pointer" @click="show_filter = false">&#xe5cd;</span>
                        </div>
                    </div>
                </header>
                <div class="pl-3 pr-3 pt-2">
                    <b-form-group v-if="full_list">
                        <b-form-checkbox
                            id="checkbox-1"
                            v-model="filter_my_chats"
                            name="checkbox-1"
                            class="mt-2 fz-18"
                        >
                            <b>{{ $t("bs-my-chats") }}</b>
                        </b-form-checkbox>
                    </b-form-group>

                    <b-form-group v-if="full_list">
                        <b-form-checkbox
                            id="checkbox-3"
                            v-model="filter_not_category"
                            name="checkbox-3"
                            class="mt-2 fz-18"
                        >
                            <b>{{$t('bs-not-categorized')}}</b>
                        </b-form-checkbox>
                    </b-form-group>
                </div>

                <hr style="width:100%;text-align:left;margin-left:0">

                <div class="pl-3 pr-3">


                    <label>
                        <b>{{ $t("bs-departments") }}</b>
                    </label>
                    <b-form-group v-slot="{ariaDescribedby}">
                        <b-form-checkbox-group
                            :options="departments"
                            :aria-describedby="ariaDescribedby"
                            stacked
                            v-model="aux_dept"
                            @change="setDepts()"
                        ></b-form-checkbox-group>
                    </b-form-group>
                </div>
            </div>
        </div>

        <!-- ============================ MODALS ==========================-->

        <modal-client-history
            :chat="chat"
            :clientChatHistory="clientChatHistory"
            :clientTicketHistory="clientTicketHistory"
            :user="session_user"
            :isMobile="isMobile"
        />

        <modal-category v-if="showCategory" :user="session_user" :chat="chat" :filter_not_category="filter_not_category" cttype="CHAT"/>

        <div
            class="modal fade"
            id="modalDepartmentTicket"
            tabindex="-1"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
            data-backdrop="static"
            data-keyboard="false"
        >
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                        <h5 class="modal-title" id="exampleModalLabel">
                            {{ $t("bs-turn-into-ticket") }}
                        </h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            X
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>
                                        {{ $t("bs-department") }} <span class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        v-model="selected_department"
                                        deselect-label=""
                                        selectLabel=""
                                        track-by="name"
                                        label="name"
                                        :placeholder="$t('bs-select-a-department')"
                                        :options="departments_to_transfer"
                                        :searchable="false"
                                        :allow-empty="false"
                                        @input="showAgentsFormByDepartment"
                                    >
                                        <template slot="singleLabel" slot-scope="{ option }">
                                            <strong>{{ option.name }}</strong>
                                            <img
                                                v-if="option.type == 'builderall-mentor'"
                                                src="http://www.omb100.com/internacional/public/office5/img/general-svg/icon_vip.svg"
                                                height="20"
                                            />
                                        </template>
                                        <template slot="option" slot-scope="{ option }">
                                            <strong>{{ option.name }}</strong>
                                            <img
                                                v-if="option.type == 'builderall-mentor'"
                                                src="http://www.omb100.com/internacional/public/office5/img/general-svg/icon_vip.svg"
                                                height="20"
                                            />
                                        </template>
                                    </multiselect>
                                </div>
                            </div>
                            <div class="col-12" v-if="showAgentForm">
                                <div class="form-group">
                                    <label>
                                        {{ $t("bs-attendant") }} <span class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        v-model="selected_agent"
                                        deselect-label=""
                                        selectLabel=""
                                        track-by="name"
                                        label="name"
                                        :placeholder="$t('bs-select-a-attendant')"
                                        :options="agents_to_transfer"
                                        :searchable="false"
                                        :allow-empty="false"
                                    >
                                        <template slot="singleLabel" slot-scope="{ option }">
                                            <strong>{{ option.name }}
                                            </strong>
                                        </template>
                                    </multiselect>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">
                                        {{ $t("bs-description") }} <span class="text-danger">*</span>
                                    </label>
                                    <quill-editor
                                        ref="quillEditorCreateTicketOptions"
                                        v-model="ticket_description"
                                        :options="quillEditorTicketOptions"
                                        class="bg-white border"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button
                            type="button"
                            class="text-capitalize btn btn-cancel-modal"
                            data-dismiss="modal"
                        >
                            {{ $t("bs-cancel") }}
                        </button>
                        <button
                            type="button"
                            id="btn-new-chat"
                            class="btn btn-primary"
                            @click="turnIntoTicket()"
                            :disabled="!validateFormTurnIntoTicket"
                        >
                            {{ $t("bs-next").toUpperCase() }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="modal fade"
            id="modalEditTurnoIntoTicket"
            tabindex="-1"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
            data-backdrop="static"
            data-keyboard="false"
        >
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                        <h5 class="modal-title" id="exampleModalLabel">
                            {{ `${$t("bs-turn-into-ticket")} (${$t('bs-edit')})` }}
                        </h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            X
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>
                                        {{ $t("bs-department") }} <span class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        v-model="selected_department"
                                        deselect-label=""
                                        selectLabel=""
                                        track-by="name"
                                        label="name"
                                        :placeholder="$t('bs-department')"
                                        :options="departments_to_transfer"
                                        :searchable="false"
                                        :allow-empty="false"
                                        @input="showAgentsFormByDepartment"
                                    >
                                        <template slot="singleLabel" slot-scope="{ option }">
                                            <strong>{{ option.name }}</strong>
                                            <img
                                                v-if="option.type == 'builderall-mentor'"
                                                src="http://www.omb100.com/internacional/public/office5/img/general-svg/icon_vip.svg"
                                                height="20"
                                            />
                                        </template>
                                        <template slot="option" slot-scope="{ option }">
                                            <strong>{{ option.name }}</strong>
                                            <img
                                                v-if="option.type == 'builderall-mentor'"
                                                src="http://www.omb100.com/internacional/public/office5/img/general-svg/icon_vip.svg"
                                                height="20"
                                            />
                                        </template>
                                    </multiselect>
                                </div>
                            </div>
                            <div class="col-12" v-if="showAgentForm">
                                <div class="form-group">
                                    <label>
                                        {{ $t("bs-attendant") }} <span class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        v-model="selected_agent"
                                        deselect-label=""
                                        selectLabel=""
                                        track-by="name"
                                        label="name"
                                        :placeholder="$t('bs-attendant')"
                                        :options="agents_to_transfer"
                                        :searchable="false"
                                        :allow-empty="false"
                                    >
                                        <template slot="singleLabel" slot-scope="{ option }">
                                            <strong>{{ option.name }}
                                            </strong>
                                        </template>
                                    </multiselect>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">
                                        {{ $t("bs-description") }} <span class="text-danger">*</span>
                                    </label>
                                    <quill-editor
                                        ref="quillEditorTicketEditOptions"
                                        v-model="ticket_description_edit"
                                        :options="quillEditorTicketOptions"
                                        class="bg-white border"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">

                        <button
                            type="button"
                            class="text-capitalize btn btn-cancel-modal"
                            data-dismiss="modal"
                        >
                            {{ $t("bs-back") }}
                        </button>

                        <button
                            type="button"
                            class="btn btn-secondary"
                            @click="deleteInfoToTurnIntoticket()"
                        >
                            {{ $t("bs-cancel-transformation").toUpperCase() }}
                        </button>

                        <button
                            type="button"
                            id="btn-new-chat"
                            class="btn btn-primary"
                            :disabled="!validateFormTurnIntoTicket"
                            @click="setInfoToTurnIntoticket('update')"
                        >
                            {{ $t("bs-save").toUpperCase() }}
                        </button>

                    </div>
                </div>
            </div>
        </div>

        <div
            class="modal fade"
            id="modalDepartment"
            tabindex="-1"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
            data-backdrop="static"
            data-keyboard="false"
        >
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                        <h5 class="modal-title" id="exampleModalLabel">
                            {{ $t("bs-choose-the-department") }}
                        </h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            X
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">
                                        {{ $t("bs-department") }}
                                    </label>
                                    <multiselect
                                        v-model="selected_department"
                                        deselect-label=""
                                        :custom-label="nameWithLang"
                                        selectLabel=""
                                        track-by="name"
                                        label="name"
                                        :placeholder="
                                            $t('bs-select-a-department')
                                        "
                                        :options="departments_to_transfer"
                                        :searchable="false"
                                        :allow-empty="false"
                                        id="departments"
                                    >
                                        <template
                                            slot="singleLabel"
                                            slot-scope="{ option }"
                                        >
                                            <strong>
                                                {{ option.name }}
                                            </strong>
                                            <img
                                                v-if="
                                                    option.type ==
                                                        'builderall-mentor'
                                                "
                                                src="http://www.omb100.com/internacional/public/office5/img/general-svg/icon_vip.svg"
                                                alt=""
                                                height="20"
                                            />
                                        </template>
                                        <template
                                            slot="option"
                                            slot-scope="{ option }"
                                        >
                                            <strong>
                                                {{ option.name }}
                                            </strong>
                                            <img
                                                v-if="
                                                    option.type ==
                                                        'builderall-mentor'
                                                "
                                                src="http://www.omb100.com/internacional/public/office5/img/general-svg/icon_vip.svg"
                                                alt=""
                                                height="20"
                                            />
                                            — [{{ option.status }}]
                                        </template>
                                    </multiselect>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button
                            type="button"
                            class="text-capitalize btn btn-cancel-modal"
                            data-dismiss="modal"
                        >
                            {{ $t("bs-cancel") }}
                        </button>
                        <button
                            v-if="transfer_to_agent"
                            type="button"
                            id="btn-new-chat"
                            class="btn btn-primary"
                            @click.prevent="showModalAtendent()"
                        >
                            {{ $t("bs-forward").toUpperCase() }}
                        </button>
                        <button
                            v-else
                            type="button"
                            id="btn-new-chat"
                            class="btn btn-primary"
                            @click="transferChat()"
                        >
                            {{ $t("bs-transfer").toUpperCase() }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <modal-block v-if="showModalReason" :showmodal="showModalReason" :client_id="chat.client.id" :itemselected="chat" :type="'CHAT'"></modal-block>

        <div
            class="modal fade"
            id="modalAtendent"
            tabindex="-1"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
            data-backdrop="static"
            data-keyboard="false"
        >
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                        <h5 class="modal-title" id="exampleModalLabel">
                            {{ $t("bs-choose-the-attendant") }}
                        </h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            X
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">
                                        {{ $t("bs-attendant") }}
                                    </label>
                                    <multiselect
                                        v-model="selected_agent"
                                        deselect-label=""
                                        selectLabel=""
                                        track-by="name"
                                        label="name"
                                        :placeholder="
                                            $t('bs-select-a-department')
                                        "
                                        :options="agents_to_transfer"
                                        :searchable="false"
                                        :allow-empty="false"
                                        id="departments"
                                    >
                                        <template
                                            slot="singleLabel"
                                            slot-scope="{ option }"
                                        >
                                            <strong>
                                                {{ option.name }}
                                            </strong>
                                        </template>
                                    </multiselect>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button
                            type="button"
                            class="text-capitalize btn btn-cancel-modal"
                            data-dismiss="modal"
                        >
                            {{ $t("bs-cancel") }}
                        </button>
                        <button
                            type="button"
                            id="btn-new-chat"
                            class="btn btn-primary"
                            @click="transferChat()"
                        >
                            {{ $t("bs-transfer").toUpperCase() }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <modal-translate v-if="showTranslate" :chat="chat" :user="session_user" :messages="chat_history"/>

        <b-sidebar
            v-if="isMobile && show_info"
            id="sidebar-right-info-2"
            :title="$t('bs-chat-information')"
            right
            shadow
            backdrop
            z-index="3"
            bg-variant="white"
        >
            <chat-info
                :chat="chat"
                :showChat="showChat"
                :user="session_user"
                :footerActiveChat="footerActiveChat"
                :openClientHistory="openClientHistory"
                :openCategory="openCategory" 
                :chat_admin="admin"
                :chat_queue_full_control="chat_queue_full_control"
                :chats_on_queue="chats_on_queue"
                :chats_resolved="chats_resolved"
                :chats_canceled="chats_canceled"
                :openChat="openChat"
                :restriction="session_user_permissions[0]"
            >
            </chat-info>
        </b-sidebar>

        <!-- modalDatabase -->
        <modal-database v-if="showDatabase" :type="'CHAT'" :chat="chat" :user="session_user"/>

        <!-- modalDepartmentNot -->
        <alert-not-department title="chat"></alert-not-department>
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

export default {
    components: {
        VEmojiPicker,
        Treeselect,
        quillEditor
    },
    props: {
        is_admin: Number,
        session_user: Object,
        session_user_company: Object,
        session_user_cucd: Array,
        session_user_departments: Array,
        session_user_permissions: Array,
        restriction: Array,
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
            chat_queue_full_control: this.session_user_permissions[0][
                "chat_queue_full_control"
            ],
            permissions: this.session_user_permissions[0],
            cucd: this.session_user_cucd,
            url_prefix: `${
                this.session_user_permissions[0]["chat_admin"]
                    ? "full-chat-admin"
                    : "full-chat"
            }`,
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
            country_sys: this.session_user.language.split("_")[1],
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
                                const content22 = message.content;
                                // Verifica se a string contém uma substring que começa com "data:" e contém "base64,"
                                const hasBase64Image = content22.includes("data:") && content22.includes("base64,");
                                if (hasBase64Image) {
                                    this.chat_history.push(message);
                                    resolve();
                                } else {
                                    // console.log(message.content)
                                    var content_translated = JSON.parse(message.content_translated);
                                    content_translated = content_translated[0].content;
                                    message.content_translated = content_translated;
                                    this.chat_history.push(message);
                                    resolve();
                                }
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
                    axios.post(`chat/agent/update-status`, {
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

                            //PEGAR CHAT DA FILA SE EXISTIR CHAT NA FILA 
                            if(this.$store.state.chats_in_queue.length > 0){
                                axios.post(`chat/agent/check-department`, {
                                    chats_in_queue: this.$store.state.chats_in_queue,
                                }).then(response => {
                                    this.$loading(false);
                                    if(response.data){
                                        this.$store.state.chats_in_queue.forEach((element, index) => {
                                            if(element.number == response.data){
                                                this.$root.$refs.TableQueue.callCatchChat(0, this.$store.state.chats_in_queue[index], true)
                                            }
                                        });
                                    }
                                });
                            }
                           
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
            return this.session_user_permissions[0]["chat_admin"] || this.is_admin;
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
.ba-hd__title {
    color: #0080fc;
    font-family: Muli;
    font-weight: 800;
    font-size: 1.4rem;
    letter-spacing: 0px;
    color: #0080fc;
    width: 0px;
}

.chat-txt {
    max-height: 445px;
    min-height: 44px;
}

.js-autoresize {
    font: normal normal normal 16px/20px Muli;
    letter-spacing: 0px;
    color: #707070;
    max-height: 159px;
    min-height: 44px;
    width: 100%;
    border: none;
    resize: none;
    background: transparent;
}

.col-btn-back {
    max-width: 80px !important;
    min-width: 80px !important;
}

@media only screen and (min-width: 1279px) {
    .col-btn-back {
        display: none;
    }
}

@media only screen and (max-width: 1279px) {
    .col-btn-back {
        display: unset;
    }
}

.btn-back {
    height: 10px !important;
}

.sidebar-right .list-group-item {
    border-radius: 0px !important;
    background: transparent !important;
    height: 55px !important;
    font: normal normal 600 16px/21px Muli;
    letter-spacing: 0px;
    color: #656565;
    border: none;
    border-bottom: 1px solid #d7dee6;
}

.sidebar-right .list-group-item:last-child {
    border-bottom: none !important;
}
.sidebar-right .list-group-item:hover {
    background: #2273fe !important;
    color: white;
}

@media only screen and (min-width: 1279px) {
    .btn-back {
        display: none;
    }
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

#footer {
    overflow-x: auto;
    white-space: nowrap;
    position: fixed;
    bottom: 0px;
    height: 80px;
    width: calc(100% - 100px);
}

@media only screen and (max-width: 576px) {
    #footer {
        width: calc(100% - 37px);
    }
}

.footer-active-chat {
    max-width: 240px !important;
    min-width: 240px !important;
    height: 50px !important;
    margin-left: 10px;
    display: inline-block !important;
    background: #ffffff 0% 0% no-repeat padding-box;
    border: 1px solid #dddddd;
    border-radius: 6px;
    opacity: 1;
    cursor: pointer;
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
    max-width: 242px !important;
    min-width: 242px !important;
    height: 52px !important;
}

.fac-avatar {
    max-width: 26px;
    min-width: 26px;
    height: 100%;
}

.fac-name {
    height: 100%;
    font: normal normal 600 16px/21px Muli;
    letter-spacing: 0px;
    color: #656565;
    text-transform: capitalize;
    max-width: 176px;
}

.fac-close {
    max-width: 18px;
    min-width: 18px;
    height: 100%;
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

button:focus {
    outline: 0 !important;
}

#dropdown-action .bs-ico.build{
    position: relative;
    font-size: 16px;
    color: #707070;
    top: 3px;
    left: -2px;
}


.span-dropdown-action {
    color: #333333 !important;
    text-transform: capitalize !important;
    font-size: 14.2px !important;
    line-height: 20px !important;
    font-weight: bold !important;
    text-rendering: optimizeLegibility !important;
    font-family: Muli, Lato, Helvetica Neue, Helvetica, Arial, sans-serif !important;
}



.action_icon {
    color: #656565;
    font-size: 18px;
    max-width: 20px;
    min-width: 20px;
    position: relative;
    top: 4px;
    left: -5px;
}

.action-title {
    font-family: Muli;
    font-weight: 600;
    font-size: 0.9rem;
    letter-spacing: 0px;
    color: #656565;
    text-transform: capitalize;
}

.is_mobile .modal {
    padding-left: 0px !important;
}

.is_mobile .modal-dialog {
    max-width: 100vw !important;
    min-height: 100vh !important;
    max-height: 100vh !important;
    margin: 0px !important;
}

.is_mobile .modal-content {
    height: 100vh !important;
    padding-left: 10px !important;
    padding-right: 10px !important;
}

.is_mobile .btn-cancel-modal {
    padding: 0px;
    padding-right: 10px;
}

.translate-icon {
    position: relative;
    top: 5px;
    color: #707070;
    font-size: 18px;
    line-height: 0px;
}

.translate-icon.active {
    color: #0080fc !important;
}

.actions-box {
    position: relative;
    z-index: 2;
    bottom: 363px;
    width: 300px;
    left: -98px;
}

#popover-4 {
    margin-right: 39px;
}
</style>
