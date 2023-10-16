<template>
    <div class="h-100 zoom-80">
        <page-title>
            <b-button
                v-if="showChat"
                class="p-0 pb-3 bg-transparent border-0 text-primary btn-back mt-n4"
                @click="backToMenu"
            >
                <vue-material-icon name="keyboard_arrow_left" :size="40" />
            </b-button>
            {{ $t("bs-chat") }}
            <template v-if="showChat"> #{{ chat.number }} </template>
        </page-title>
        <b-row class="h-100 pt-2 ml-custom">
            <!-- - - - - -  C O L U N A  D A  E S Q U E R D A  - - - - - -->
            <left-card
                :chatstatus="chat.status"
                :showTableComponent="showTableComponent"
                :showTableQueue="showTableQueue"
                :showTableInProgress="showTableInProgress"
                :showTableTransferred="showTableTransferred"
                :showTableClosed="showTableClosed"
                :showTableResolved="showTableResolved"
                :showTableCanceled="showTableCanceled"
                :hidden="hidden"
                :hideOnSmall="hideOnSmall"
                :countOnQueue="countOnQueue"
                :countTransferred="countTransferred"
                :countClosed="countClosed"
                :countResolved="countResolved"
                :countCanceled="countCanceled"
                :countInProgress="countInProgress"
                :notify="notify"
                :footerActiveChat="footerActiveChat"
            />

            <!-- - - - - -  C O L U N A  D O  M E I O  [ TABLE IN PROGRESS ] - - - - - -->
            <table-in-progress
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
            >
                <b-row>
                    <b-col col class="col-btn-back">
                        <b-button
                            class="p-0 bg-transparent border-0 text-primary"
                            @click="backToMenu"
                        >
                            <vue-material-icon
                                name="keyboard_arrow_left"
                                :size="40"
                            />
                        </b-button>
                    </b-col>
                    <b-col col>
                        <multiselect
                            id="departments"
                            v-model="company_department"
                            track-by="name"
                            label="name"
                            deselect-label=""
                            selectLabel=""
                            :selectedLabel="$t('bs-selected')"
                            deselectGroupLabel=""
                            selectGroupLabel=""
                            group-values="options"
                            group-label="title"
                            :group-select="true"
                            :multiple="true"
                            :placeholder="$t('bs-filter-by-department')"
                            :options="departments"
                            :searchable="false"
                            :allow-empty="false"
                            :limit="2"
                            :limitText="count => `+ ${count}`"
                        >
                        </multiselect>
                    </b-col>
                    <b-col v-if="full_list" col cols="auto">
                        <b-form-checkbox
                            id="checkbox-1"
                            v-model="filter_my_chats"
                            name="checkbox-1"
                            class="mt-2 fz-18"
                        >
                            {{ $t("bs-my-chats") }}
                        </b-form-checkbox>
                    </b-col>
                </b-row>
            </table-in-progress>

            <!-- - - - - -  C O L U N A  D O  M E I O  [ TABLE QUEUE ] - - - - - -->
            <table-queue
                v-if="showTableQueue"
                :chat_admin="admin"
                :chat_queue_full_control="chat_queue_full_control"
                :setInfo="setInfo"
                :catchChat="catchChat"
                :queue="chats_on_queue"
                :getChatsOnQueue="getChatsOnQueue"
                :company_department="company_department"
                :showTableComponent="showTableComponent"
                :hideOnSmall="hideOnSmall"
                :resetTable="resetTable"
                :user="session_user"
                :countOnQueue="countOnQueue"
                :footerActiveChat="footerActiveChat"
                :key="key_queue"
            >
                <b-row>
                    <b-col col class="col-btn-back">
                        <b-button
                            class="p-0 bg-transparent border-0 text-primary"
                            @click="backToMenu"
                        >
                            <vue-material-icon
                                name="keyboard_arrow_left"
                                :size="40"
                            />
                        </b-button>
                    </b-col>
                    <b-col col>
                        <multiselect
                            id="departments"
                            v-model="company_department"
                            track-by="name"
                            label="name"
                            deselect-label=""
                            selectLabel=""
                            :selectedLabel="$t('bs-selected')"
                            deselectGroupLabel=""
                            selectGroupLabel=""
                            group-values="options"
                            group-label="title"
                            :group-select="true"
                            :multiple="true"
                            :placeholder="$t('bs-filter-by-department')"
                            :options="departments"
                            :searchable="false"
                            :allow-empty="false"
                            :limit="2"
                            :limitText="count => `+ ${count}`"
                        >
                        </multiselect>
                    </b-col>
                </b-row>
            </table-queue>

            <!-- - - - - -  C O L U N A  D O  M E I O  [ TABLE TRANSFERRED ] - - - - - -->
            <table-transferred
                v-if="showTableTransferred"
                :hideOnSmall="hideOnSmall"
                :footerActiveChat="footerActiveChat"
                :key="key_transferred"
            >
                <b-row>
                    <b-col col class="col-btn-back">
                        <b-button
                            class="p-0 bg-transparent border-0 text-primary"
                            @click="backToMenu"
                        >
                            <vue-material-icon
                                name="keyboard_arrow_left"
                                :size="40"
                            />
                        </b-button>
                    </b-col>
                    <b-col col>
                        <multiselect
                            id="departments"
                            v-model="company_department"
                            track-by="name"
                            label="name"
                            deselect-label=""
                            selectLabel=""
                            :selectedLabel="$t('bs-selected')"
                            deselectGroupLabel=""
                            selectGroupLabel=""
                            group-values="options"
                            group-label="title"
                            :group-select="true"
                            :multiple="true"
                            :placeholder="$t('bs-filter-by-department')"
                            :options="departments"
                            :searchable="false"
                            :allow-empty="false"
                            :limit="2"
                            :limitText="count => `+ ${count}`"
                        >
                        </multiselect>
                    </b-col>
                    <b-col v-if="full_list" col cols="auto">
                        <b-form-checkbox
                            id="checkbox-1"
                            v-model="filter_my_chats"
                            name="checkbox-1"
                            class="mt-2 fz-18"
                        >
                            {{ $t("bs-my-chats") }}
                        </b-form-checkbox>
                    </b-col>
                </b-row>
            </table-transferred>

            <!-- - - - - -  C O L U N A  D O  M E I O  [ TABLE CLOSED ] - - - - - -->
            <table-closed
                v-if="showTableClosed"
                :setInfo="setInfo"
                :user="session_user"
                :chat="chat"
                :chats="chats_closed"
                :getChatsClosed="getChatsClosed"
                :company_department="company_department"
                :hideOnSmall="hideOnSmall"
                :openChat="openChat"
                :resetTable="resetTable"
                :countClosed="countClosed"
                :footerActiveChat="footerActiveChat"
                :key="key_closed"
            >
                <b-row>
                    <b-col col class="col-btn-back">
                        <b-button
                            class="p-0 bg-transparent border-0 text-primary"
                            @click="backToMenu"
                        >
                            <vue-material-icon
                                name="keyboard_arrow_left"
                                :size="40"
                            />
                        </b-button>
                    </b-col>
                    <b-col col>
                        <multiselect
                            id="departments"
                            v-model="company_department"
                            track-by="name"
                            label="name"
                            deselect-label=""
                            selectLabel=""
                            :selectedLabel="$t('bs-selected')"
                            deselectGroupLabel=""
                            selectGroupLabel=""
                            group-values="options"
                            group-label="title"
                            :group-select="true"
                            :multiple="true"
                            :placeholder="$t('bs-filter-by-department')"
                            :options="departments"
                            :searchable="false"
                            :allow-empty="false"
                            :limit="2"
                            :limitText="count => `+ ${count}`"
                        >
                        </multiselect>
                    </b-col>
                    <b-col v-if="full_list" col cols="auto">
                        <b-form-checkbox
                            id="checkbox-1"
                            v-model="filter_my_chats"
                            name="checkbox-1"
                            class="mt-2 fz-18"
                        >
                            {{ $t("bs-my-chats") }}
                        </b-form-checkbox>
                    </b-col>
                </b-row>
            </table-closed>

            <!-- - - - - -  C O L U N A  D O  M E I O  [ TABLE RESOLVED ] - - - - - -->
            <table-resolved
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
                :key="key_resolved"
            >
                <b-row>
                    <b-col col class="col-btn-back">
                        <b-button
                            class="p-0 bg-transparent border-0 text-primary"
                            @click="backToMenu"
                        >
                            <vue-material-icon
                                name="keyboard_arrow_left"
                                :size="40"
                            />
                        </b-button>
                    </b-col>
                    <b-col col>
                        <multiselect
                            id="departments"
                            v-model="company_department"
                            track-by="name"
                            label="name"
                            deselect-label=""
                            selectLabel=""
                            :selectedLabel="$t('bs-selected')"
                            deselectGroupLabel=""
                            selectGroupLabel=""
                            group-values="options"
                            group-label="title"
                            :group-select="true"
                            :multiple="true"
                            :placeholder="$t('bs-filter-by-department')"
                            :options="departments"
                            :searchable="false"
                            :allow-empty="false"
                            :limit="2"
                            :limitText="count => `+ ${count}`"
                        >
                        </multiselect>
                    </b-col>
                    <b-col v-if="full_list" col cols="auto">
                        <b-form-checkbox
                            id="checkbox-1"
                            v-model="filter_my_chats"
                            name="checkbox-1"
                            class="mt-2 fz-18"
                        >
                            {{ $t("bs-my-chats") }}
                        </b-form-checkbox>
                    </b-col>
                </b-row>
            </table-resolved>

            <!-- - - - - -  C O L U N A  D O  M E I O  [ TABLE CANCELED ] - - - - - -->
            <table-canceled
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
                :key="key_canceled"
            >
                <b-row>
                    <b-col col class="col-btn-back">
                        <b-button
                            class="p-0 bg-transparent border-0 text-primary"
                            @click="backToMenu"
                        >
                            <vue-material-icon
                                name="keyboard_arrow_left"
                                :size="40"
                            />
                        </b-button>
                    </b-col>
                    <b-col col>
                        <multiselect
                            id="departments"
                            v-model="company_department"
                            track-by="name"
                            label="name"
                            deselect-label=""
                            deselectGroupLabel=""
                            selectGroupLabel=""
                            selectLabel=""
                            :selectedLabel="$t('bs-selected')"
                            group-values="options"
                            group-label="title"
                            :group-select="true"
                            :multiple="true"
                            :placeholder="$t('bs-filter-by-department')"
                            :options="departments"
                            :searchable="false"
                            :allow-empty="false"
                            :limit="2"
                            :limitText="count => `+ ${count}`"
                        >
                        </multiselect>
                    </b-col>
                    <b-col v-if="full_list" col cols="auto">
                        <b-form-checkbox
                            id="checkbox-1"
                            v-model="filter_my_chats"
                            name="checkbox-1"
                            class="mt-2 fz-18"
                        >
                            {{ $t("bs-my-chats") }}
                        </b-form-checkbox>
                    </b-col>
                </b-row>
            </table-canceled>

            <!-- - - - - -  C O L U N A  D O  M E I O  [ CHAT - MENSAGENS ] - - - - - -->
            <col-chat
                :chat="chat"
                :sendMessage="sendMessage"
                :hideOnSmall="hideOnSmall"
                :incognito_mode="incognito_mode"
                :incognito_id="incognito_id"
                v-show="showChat"
                :footerActiveChat="footerActiveChat"
                :departmentCommands="departmentCommands"
                :getContentOnLocalStorage="getContentOnLocalStorage"
            >
                <template slot="translator">
                    <translator />
                </template>
                <template slot="messages">
                    <message-type-questionary
                        v-if="questionary.length"
                        :chat="chat"
                        :questionary="questionary"
                    />
                    <component
                        v-for="(message, index) in chat_history"
                        :key="index"
                        :is="setMessageComponent(message.type)"
                        v-bind="setMessageProps(message, index)"
                    />
                </template>

                <!-- - - - - -  C O L U N A  D O  M E I O  [ CHAT - INPUT ] - - - - - -->
                <template v-if="this.chat.status === 'IN_PROGRESS'">
                    <template slot="chat-buttons">
                        <span class="ml-3 mr-3 cursor-pointer" @click="upload">
                            <img src="images/icons/chat/attach_file.svg" />
                            {{ $t("bs-attach") }}
                        </span>
                        <input
                            type="file"
                            id="attachments"
                            ref="attachments"
                            multiple
                            v-on:change="handleFilesUpload()"
                            style="display: none"
                        />
                        <button-action v-if="Object.keys(actions).length" />
                        <button-quick-reply
                            v-if="!isMobile && departmentCommands.length > 0"
                        />
                        <button-emoji />
                        <button-incognito
                            :chat="chat"
                            :activeIncognito="activeIncognito"
                            :incognito_mode="incognito_mode"
                            :incognito_id="incognito_id"
                        />
                        <b-tooltip
                            target="incognito"
                            triggers="hover"
                            placement="top"
                            variant="secondary"
                        >
                            {{ $t("bs-incognito-mode-messages-not-visible") }}
                        </b-tooltip>
                        <b-row>
                            <b-col>
                                <b-popover
                                    :target="`popover-1`"
                                    :placement="'topcenter'"
                                    title=""
                                    triggers="hover focus"
                                    :id="'pop1'"
                                >
                                    <div class="list-group sidebar-right">
                                        <a
                                            v-for="(action, index) in actions"
                                            :key="index"
                                            href="#"
                                            @click.prevent="
                                                executeAction(action)
                                            "
                                            class="list-group-item list-group-item-action"
                                            >{{ action.title }}</a
                                        >
                                    </div>
                                </b-popover>
                            </b-col>
                        </b-row>
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
                                        <div class="card-body p-0">
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
                        <b-row>
                            <b-col>
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
                            </b-col>
                        </b-row>
                    </template>
                    <template slot="select" v-if="files != ''">
                        <multiselect
                            :hide-selected="true"
                            :placeholder="
                                $t('bs-placeholder-cancel-sending-the-file')
                            "
                            v-model="files"
                            :options="files"
                            :openDirection="'bottom'"
                            :multiple="true"
                            :close-on-select="false"
                            label="name"
                            track-by="name"
                            class="ml-4 mt-2"
                        >
                        </multiselect>
                    </template>
                    <template slot="sent">
                        <button-send-message
                            :chat="chat"
                            :incognito_id="incognito_id"
                            :incognito_mode="incognito_mode"
                            :sendMessage="sendMessage"
                        />
                    </template>
                </template>
            </col-chat>
            <!-- - - - - -  C O L U N A  D A  D I R E I T A  - - - - - - -->
            <right-card
                :chat="chat"
                :showChat="showChat"
                :user="session_user"
                :footerActiveChat="footerActiveChat"
                :openClientHistory="openClientHistory"
            />
            <modal-client-history
                :chat="chat"
                :clientChatHistory="clientChatHistory"
                :clientTicketHistory="clientTicketHistory"
                :user="session_user"
            />
        </b-row>

        <div
            class="modal fade"
            id="modalDepartmentTicket"
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
                                        selectLabel=""
                                        track-by="name"
                                        label="name"
                                        :placeholder="
                                            $t('bs-select-a-department')
                                        "
                                        :options="departments_to_transfer"
                                        :searchable="false"
                                        :allow-empty="false"
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
                                        </template>
                                    </multiselect>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">
                                        {{ $t("bs-description") }}
                                    </label>
                                    <textarea
                                        v-model="ticket_description"
                                        class="form-control"
                                    ></textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">
                                        {{ $t("bs-comments") }}
                                    </label>
                                    <textarea
                                        v-model="ticket_comments"
                                        class="form-control"
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button
                            type="button"
                            class="text-capitalize btn"
                            data-dismiss="modal"
                        >
                            {{ $t("bs-cancel") }}
                        </button>
                        <button
                            type="button"
                            id="btn-new-chat"
                            class="btn btn-primary"
                            @click="turnIntoTicket()"
                        >
                            {{ $t("bs-turn-into-ticket").toUpperCase() }}
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
                            class="text-capitalize btn"
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

        <!-- MODAL BLOQUEAR CLIENTE  -->

        <div
            class="modal fade"
            id="showModalReason"
            tabindex="-1"
            aria-labelledby="showModalReasonLabel"
            aria-hidden="true"
            data-backdrop="static"
            data-keyboard="false"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                        <h5 class="modal-title" id="showModalReasonLabel">
                            {{ $t("bs-reason-for-blocking") }}
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
                                    <label for="exampleFormControlSelect1"
                                        >{{ $t("bs-type-here") }}:
                                    </label>
                                    <b-form-textarea
                                        id="textarea"
                                        v-model="textReason"
                                        :placeholder="
                                            $t('bs-type-here') + '...'
                                        "
                                        rows="4"
                                    ></b-form-textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button
                            type="button"
                            @click="nextstep(0)"
                            class="text-capitalize btn btn-primary"
                            data-dismiss="modal"
                        >
                            {{ $t("bs-cancel") }}
                        </button>
                        <!-- <span v-if="statusblock">
                            <button
                                type="button"
                                @click="blockclient(itemselected, 1)"
                                data-dismiss="modal"
                                id="btn-department"
                                class="btn btn-danger"
                            >
                                {{ $t("bs-block") }}
                            </button>
                        </span>
                        -->
                        <!-- <span v-if="!statusblock"> -->
                        <span>
                            <button
                                type="button"
                                @click="blockclient(chat.client.id, 1)"
                                data-dismiss="modal"
                                class="btn btn-danger"
                            >
                                {{ $t("bs-block") }}
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
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
                            class="text-capitalize btn"
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

        <!-- modalDepartmentNot -->
        <alert-not-department title="chat"></alert-not-department>
    </div>
</template>

<script>
import { VEmojiPicker } from "v-emoji-picker";
import { mapState, mapMutations } from "vuex";

export default {
    components: {
        VEmojiPicker
    },
    props: {
        session_user: Object,
        session_user_company: Object,
        session_user_cucd: Array,
        session_user_departments: Array,
        session_user_permissions: Array,
        restriction: Array,
    },
    data() {
        return {
            /** props */
            admin: this.session_user_permissions[0]["chat_admin"],
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
            chat: {
                show: false,
                id: "",
                number: "",
                companyDepartmentId: "",
                //comp_user_comp_depart_id_current: "",
                department: "",
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
                }
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
            chats_on_queue: [],
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
                IMAGE: "message-type-image-agent"
            },
            // modals
            transfer_to_agent: false,
            departments_to_transfer: [],
            agents_to_transfer: [],
            selected_department: null,
            selected_agent: null,
            ticket_description: null,
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
            textReason: ""
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
        this.$root.$refs.FullChat = this;
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
        this.onResize();
    },
    mounted() {
        this.countInProgress = this.$store.state.chats_in_progress.length;
    },
    methods: {
        ...mapMutations(["tabs"]),
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
                        `${window.location.pathname}`
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
            const api = `chat-history/agent/store`;
            axios
                .post(api, {
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
                    company_id: this.session_user_company.id
                })
                .then(({ data }) => {});
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
                document.getElementById("input").focus();
            }, 0);
        },
        upload() {
            $("#attachments").click();
        },
        /** @GET */
        getChatHistory(id) {
            this.$loading(true);
            axios
                .get("ticket-chat-answer/agent/get-ticket-chat-answers", {
                    params: {
                        id: id,
                        reference: "chat_id"
                    }
                })
                .then(response => {
                    if (response.data.status) {
                        this.questionary = response.data.result;
                    }
                });
            axios
                .get("chat-history/agent/get-chat-history", {
                    params: {
                        id: id
                    }
                })
                .then(response => {
                    this.chat_history = response.data;
                    this.$loading(false);
                });
        },
        getChatsOnQueue($state) {
            this.$loading(true);
            this.chat.show = false;

            const api = `${
                this.admin ? this.url_prefix : "chat"
            }/get-chats-on-queue`;
            axios
                .get(api, {
                    params: {
                        take: this.take,
                        skip: this.skip,
                        departments: this.filter_departments
                    }
                })
                .then(({ data }) => {
                    if (data.chats.length) {
                        this.chats_on_queue.push(...data.chats);
                        this.skip = data.skip;

                        if (this.take === data.chats.length) {
                            $state.loaded();
                        } else {
                            $state.complete();
                        }
                        this.$loading(false);
                    } else {
                        $state.complete();
                        this.$loading(false);
                    }
                });
        },
        getChatsClosed($state) {
            this.$loading(true);
            this.chat.show = false;

            const api = `${this.url_prefix}/get-chats-closed`;
            axios
                .get(api, {
                    params: {
                        take: this.take,
                        skip: this.skip,
                        my_chats: this.filter_my_chats ? 1 : 0,
                        departments: this.filter_departments
                    }
                })
                .then(({ data }) => {
                    if (data.chats.length) {
                        this.chats_closed.push(...data.chats);
                        this.skip = data.skip;

                        if (this.take === data.chats.length) {
                            $state.loaded();
                        } else {
                            $state.complete();
                        }
                        this.$loading(false);
                    } else {
                        $state.complete();
                        this.$loading(false);
                    }
                });
        },
        getChatsResolved($state) {
            this.$loading(true);
            this.chat.show = false;

            //const api = `${this.url_prefix}/get-chats-resolved`;
            const api = `${this.url_prefix}/get-chats-finished`;
            axios
                .get(api, {
                    params: {
                        take: this.take,
                        skip: this.skip,
                        my_chats: this.filter_my_chats ? 1 : 0,
                        departments: this.filter_departments
                    }
                })
                .then(({ data }) => {
                    if (data.chats.length) {
                        this.chats_resolved.push(...data.chats);
                        this.skip = data.skip;

                        if (this.take === data.chats.length) {
                            $state.loaded();
                        } else {
                            $state.complete();
                        }
                        this.$loading(false);
                    } else {
                        $state.complete();
                        this.$loading(false);
                    }
                });
        },
        getChatsCanceled($state) {
            this.$loading(true);
            this.chat.show = false;

            const api = `${this.url_prefix}/get-chats-canceled`;
            axios
                .get(api, {
                    params: {
                        take: this.take,
                        skip: this.skip,
                        my_chats: this.filter_my_chats ? 1 : 0,
                        departments: this.filter_departments
                    }
                })
                .then(({ data }) => {
                    if (data.chats.length) {
                        this.chats_canceled.push(...data.chats);
                        this.skip = data.skip;

                        if (this.take === data.chats.length) {
                            $state.loaded();
                        } else {
                            $state.complete();
                        }
                        this.$loading(false);
                    } else {
                        $state.complete();
                        this.$loading(false);
                    }
                });
        },
        getAgentTablesCount() {
            const api = `${this.url_prefix}/get-agent-tables-count`;
            axios
                .get(api, {
                    params: {
                        departments: this.company_department,
                        my_chats: this.filter_my_chats ? 1 : 0
                    }
                })
                .then(({ data }) => {
                    data.queue >= 1
                        ? (this.countOnQueue = data.queue)
                        : (this.countOnQueue = "");
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
                });
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

                vm.departments = [
                    {
                        title: vm.$t("bs-all"),
                        options: data
                    }
                ];
                if (data == "") {
                    $("#modalDepartmentNot").modal("show");
                }
                vm.showTableComponent("queue");
                // this.handleDepartmentsFilter();
                // this.getChatsInProgress();
                // this.getAgentTablesCount();
            });
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
                    commands.forEach(element => {
                        element.description = this.$t(element.description);
                    });
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
                this.chat.type = info.type;
                this.chat.created_at = info.created_at;
                this.chat.number = info.number;
                this.chat.dep_type = info.dep_type;
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
                resolve();
            });
        },
        /** @ACTIONS */
        getActions() {
            this.actions = {};

            if (this.take_on_chat) {
                this.actions.take = {
                    title: this.$t("bs-take-on-chat"),
                    status: "TAKE_ON",
                    message: this.$t("bs-taking-over-the-chat")
                };
            } else {
                if (this.permissions.chat_transform === 1 || this.admin === 1) {
                    this.actions.ticket = {
                        title: this.$t("bs-turn-into-ticket"),
                        status: "TICKET",
                        message: this.$t("bs-you-want-to-turn-the-chat-into-tk")
                    };
                }
                if (this.permissions.chat_moved === 1 || this.admin === 1) {
                    this.actions.transfer_to_agent = {
                        title: this.$t("bs-transfer-to-another-attendant"),
                        status: "TRANSFERRED_TO_AGENT",
                        message: `${this.$t(
                            "bs-do-you-want-to-transfer-the-chat-to"
                        )} `
                    };
                    this.actions.transfer_to_department = {
                        title: this.$t("bs-transfer-to-another-department"),
                        status: "TRANSFERRED_TO_DEPARTMENT",
                        message: this.$t(
                            "bs-transfer-the-chat-to-the-department"
                        )
                    };
                }
                if (this.permissions.chat_resolved === 1 || this.admin == 1) {
                    this.actions.resolved = {
                        title: this.$t("bs-mark-as-resolved"),
                        status: "RESOLVED",
                        message: this.$t("bs-mark-the-chat-as-resolved")
                    };
                }
                if (this.permissions.chat_close === 1 || this.admin == 1) {
                    this.actions.close = {
                        title: this.$t("bs-close-chat"),
                        status: "CLOSED",
                        message: this.$t("bs-you-want-to-end-the-chat")
                    };
                }
                if (this.permissions.chat_blocked === 1 || this.admin == 1) {
                    this.actions.chat_blocked = {
                        title: this.$t("bs-block") + " " + this.$t("bs-client"),
                        status: "BLOCK_CLIENT",
                        message: ""
                    };
                }
            }
        },
        executeAction(action) {
            this.$loading(true);
            switch (action.status) {
                case "TICKET":
                    this.$loading(true);
                    this.selected_department = null;
                    this.departments_to_transfer = [];
                    axios
                        .get("/get-departments-of-agent", {})
                        .then(response => {
                            if (response.data.length) {
                                response.data.forEach(element => {
                                    if (
                                        element.type == "" ||
                                        (element.type.toUpperCase() ==
                                            "BUILDERALL-MENTOR" &&
                                            this.chat.is_vip)
                                    ) {
                                        this.departments_to_transfer.push(
                                            element
                                        );
                                    }
                                });
                                $("#modalDepartmentTicket").modal("show");
                                this.$loading(false);
                            } else {
                                Swal.fire({
                                    heightAuto: false,
                                    icon: "error",
                                    title: this.$t("bs-error"),
                                    text: this.$t(
                                        "bs-no-active-departments-found"
                                    )
                                });
                                this.$loading(false);
                            }
                        });
                    break;
                case "TRANSFERRED_TO_AGENT":
                    this.transfer_to_agent = true;
                    this.departments_to_transfer = [];
                    axios
                        .get("/get-open-departments", {
                            params: {
                                country: this.country_sys,
                                except_this: 0 // caso nao quiser listar um dep, passar o id dele aqui (hasheado);
                            }
                        })
                        .then(({ data }) => {
                            if (data.length) {
                                data.forEach(element => {
                                    if (
                                        element.type == "" ||
                                        element.type.toUpperCase() ==
                                            "CHECKOUT" ||
                                        (element.type.toUpperCase() ==
                                            "BUILDERALL-MENTOR" &&
                                            this.chat.is_vip)
                                    ) {
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
                                            $isDisabled: !element.online
                                        });
                                    }
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
                                country: this.country_sys,
                                except_this: this.chat.companyDepartmentId // caso nao quiser listar um dep, passar o id dele aqui (hasheado);
                            }
                        })
                        .then(({ data }) => {
                            if (data.length) {
                                data.forEach(element => {
                                    if (
                                        element.type == "" ||
                                        element.type.toUpperCase() ==
                                            "CHECKOUT" ||
                                        (element.type.toUpperCase() ==
                                            "BUILDERALL-MENTOR" &&
                                            this.chat.is_vip)
                                    ) {
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
                                            $isDisabled: !element.online
                                        });
                                    }
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
                                        vm.addFooterActiveChat(vm.chat);
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
                    $("#showModalReason").modal("show");
                    this.$loading(false);
                    break;
                default:
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
                                    }
                                });
                        } else {
                            this.$loading(false);
                        }
                    });
                    break;
            }
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
                console.log(erro);
                console.log('FAILURE!!');
            });
        },
        turnIntoTicket() {
            this.closeFooterActiveChat(this.chat, false);
            this.$loading(true);
            axios
                .post(`chat/agent/update-status`, {
                    id: this.chat.chat_id,
                    action: "TICKET",
                    company_department: this.selected_department.id,
                    description: this.ticket_description,
                    comments: this.ticket_comments
                })
                .then(response => {
                    if (response.data.status) {
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
                        this.clearActiveChat();
                        $("#modalDepartmentTicket").modal("hide");
                        this.$loading(false);
                    } else {
                        Swal.fire({
                            heightAuto: false,
                            icon: "error",
                            title: this.$t("bs-error"),
                            text: this.$t(
                                "bs-error-when-turning-chat-into-ticket"
                            )
                        });
                        this.$loading(false);
                    }
                });
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
            this.clearActiveChat().then(() => {
                this.setInfo(chat, true).then(() => {
                    if (this.admin) {
                        this.setEmployee(true);
                    } else if (this.permissions.chat_open) {
                        var vm = this;
                        axios
                            .get("chat/get-agent-active-chats", {
                                params: {
                                    company_department_id:
                                        vm.chat.companyDepartmentId
                                }
                            })
                            .then(({ data }) => {
                                if (data.status) {
                                    vm.setEmployee(false);
                                } else {
                                    Swal.fire({
                                        heightAuto: false,
                                        icon: "info",
                                        text: vm.$t(
                                            "bs-maximum-number-of-active-chats"
                                        )
                                    });
                                    vm.$loading(false);
                                }
                            });
                    } else {
                        Swal.fire({
                            heightAuto: false,
                            icon: "info",
                            text: this.$t(
                                "bs-you-are-not-allowed-to-catch-chats"
                            )
                        });
                        this.$loading(false);
                    }
                });
            });
        },
        setEmployee(is_admin) {
            var vm = this;
            this.$loading(true);
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
                            vm.openChat(vm.chat, true);
                        });
                    } else {
                        Swal.fire({
                            heightAuto: false,
                            icon: "info",
                            text: vm.$t(data.status)
                        });
                        vm.$loading(false);
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
        // checkUserStatus(hash_id) {
        //   let online_index = this.$store.state.online_users.findIndex((item) => item.hash_id === hash_id);
        //   let busy_index = this.$store.state.busy_users.findIndex((item) => item.hash_id === hash_id);
        //   if (online_index !== -1) {
        //     return "online";
        //   } else if (busy_index !== -1) {
        //     return "busy";
        //   } else {
        //     return "offline";
        //   }
        // },
        connectToChannels() {
            this.joinInQueueChannel();
            //this.joinInProgressChannel();
            this.joinInClosedChannel();
            this.joinInResolvedChannel();
            this.joinInCanceledChannel();
        },
        joinInQueueChannel() {
            /** begin */
            const channel = `queue`;
            const event = `QueueUpdated`;
            /** join to the channel and listen events */
            Echo.join(`${channel}.${this.session_user_company.id}`).listen(
                event,
                e => {
                    /** if the user is a admin, we get the actions for him, else we get the actions of employee */
                    if (this.admin) {
                        this.adminInQueueChannelActions(e.item);
                    } else {
                        this.employeeInQueueChannelActions(e.item);
                    }
                }
            );
            /** end */
        },
        // joinInProgressChannel() {
        //     const channel = `full-chat.progress`;
        //     const event = `FullChatProgress`;
        //     Echo.leave(`${channel}.${this.session_user_company.id}`);
        //     Echo.join(`${channel}.${this.session_user_company.id}`).listen(
        //         event,
        //         e => {
        //             if (this.admin) {
        //                 this.adminInProgressChannelActions(e.item);
        //             } else {
        //                 this.employeeInProgressChannelActions(e.item);
        //             }
        //         }
        //     );
        // },
        joinInClosedChannel() {
            /** begin */
            const channel = `full-chat.closed`;
            const event = `FullChatClosed`;
            /** join to the channel and listen events */
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
        adminInQueueChannelActions(item) {
            switch (item.action) {
                /** add item to the queue */
                case "push":
                    /** in this case, we don't have to verify if the user belongs to the department of event item. He is a ADMIN and can see all chats of the company */
                    this.notifyQueue();

                    let i = this.filter_departments.findIndex(
                        f => f.id === item.company_department_id
                    );

                    if (i !== -1) {
                        this.chats_on_queue.push(item);
                        /** increment count of items on queue and add notification badge  */
                        if (this.countOnQueue) {
                            this.countOnQueue++;
                        } else {
                            this.countOnQueue = 1;
                        }
                    }
                    /** close the chat tab on footer if this chat was transferred  */
                    if (item.transferred) {
                        this.closeFooterActiveChat(item, true);
                    }
                    break;
                /** remove item from queue */
                case "splice":
                    /** find the position of event item on array */
                    let index = this.chats_on_queue.findIndex(
                        element => element.chat_id === item.chat_id
                    );
                    /** only try to remove if find the position */
                    if (index !== -1) {
                        this.chats_on_queue.splice(index, 1);
                        /** decrement count of items on queue*/
                        if (this.countOnQueue) {
                            this.countOnQueue--;
                        }
                    }
                    break;
            }
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
                            this.chats_resolved.push(item);
                            if (this.countResolved) {
                                this.countResolved++;
                                this.notifyClosed();
                            } else {
                                this.countResolved = 1;
                                this.notifyClosed();
                            }
                        }
                        // remover do footer
                        this.closeFooterActiveChat(item, true);
                        break;
                    case "splice":
                        //recupero o chat do evento e encontro a index dele no objeto
                        let index = this.chats_resolved.findIndex(
                            element => element.chat_id === item.chat_id
                        );
                        if (index !== -1) {
                            // removo o chat da posição atual no objeto
                            this.chats_resolved.splice(index, 1);
                            // faço o decremento na contagem de chats em progresso
                            if (this.countResolved) {
                                this.countResolved--;
                            }
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
                            this.chats_resolved.push(item);
                            if (this.countResolved) {
                                this.countResolved++;
                                this.notifyResolved();
                            } else {
                                this.countResolved = 1;
                                this.notifyResolved();
                            }
                        }
                        // remover do footer
                        this.closeFooterActiveChat(item, true);
                        break;
                    case "splice":
                        //recupero o chat do evento e encontro a index dele no objeto
                        let index = this.chats_resolved.findIndex(
                            element => element.chat_id === item.chat_id
                        );
                        if (index !== -1) {
                            // removo o chat da posição atual no objeto
                            this.chats_resolved.splice(index, 1);
                            // faço o decremento na contagem de chats em progresso
                            if (this.countResolved) {
                                this.countResolved--;
                            }
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
                    this.chats_canceled.push(item);
                    if (this.countCanceled) {
                        this.countCanceled++;
                        this.notifyCanceled();
                    } else {
                        this.countCanceled = 1;
                        this.notifyCanceled();
                    }
                }
            }
            // remover do footer
            this.closeFooterActiveChat(item, true);
        },
        employeeInQueueChannelActions(item) {
            switch (item.action) {
                /** add item to the queue */
                case "push":
                    /** only push if user belongs to the department of event item  */
                    //if (this.session_user_departments.includes(item.company_department_id)) {

                    if (item.transferred) {
                        this.closeFooterActiveChat(item, true);
                    }
                    //}
                    /** push item to the queue  */
                    let i = this.filter_departments.findIndex(
                        f => f.id === item.company_department_id
                    );

                    if (i !== -1) {
                        this.notifyQueue();
                        this.chats_on_queue.push(item);
                        /** increment count of items on queue and add notification badge  */
                        if (this.countOnQueue) {
                            this.countOnQueue++;
                        } else {
                            this.countOnQueue = 1;
                        }
                    }
                    /** close the chat tab on footer if this chat was transferred  */

                    break;
                /** remove item from queue */
                case "splice":
                    /** find the position of event item on array */
                    let index = this.chats_on_queue.findIndex(
                        element => element.chat_id === item.chat_id
                    );
                    /** only try to remove if find the position */
                    if (index !== -1) {
                        this.chats_on_queue.splice(index, 1);
                        /** decrement count of items on queue*/
                        if (this.countOnQueue) {
                            this.countOnQueue--;
                        }
                    }
                    break;
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
                            this.chats_resolved.push(item);
                            if (this.countResolved) {
                                this.countResolved++;
                                this.notifyClosed();
                            } else {
                                this.countResolved = 1;
                                this.notifyClosed();
                            }
                        }
                        // remover do footer
                        this.closeFooterActiveChat(item, true);
                        break;
                    case "splice":
                        //recupero o chat do evento e encontro a index dele no objeto
                        let index = this.chats_resolved.findIndex(
                            element => element.chat_id === item.chat_id
                        );
                        if (index !== -1) {
                            // removo o chat da posição atual no objeto
                            this.chats_resolved.splice(index, 1);

                            // faço o decremento na contagem de chats em progresso
                            if (this.countResolved) {
                                this.countResolved--;
                            }
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
                            this.chats_resolved.push(item);
                            if (this.countResolved) {
                                this.countResolved++;
                                this.notifyResolved();
                            } else {
                                this.countResolved = 1;
                                this.notifyResolved();
                            }
                        }
                        // remover do footer
                        this.closeFooterActiveChat(item, true);
                        break;
                    case "splice":
                        //recupero o chat do evento e encontro a index dele no objeto
                        let index = this.chats_resolved.findIndex(
                            element => element.chat_id === item.chat_id
                        );
                        if (index !== -1) {
                            // removo o chat da posição atual no objeto
                            this.chats_resolved.splice(index, 1);

                            // faço o decremento na contagem de chats em progresso
                            if (this.countResolved) {
                                this.countResolved--;
                            }
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
                    this.chats_canceled.push(item);
                    if (this.countCanceled) {
                        this.countCanceled++;
                        this.notifyCanceled();
                    } else {
                        this.countCanceled = 1;
                        this.notifyCanceled();
                    }
                    // remover do footer
                    this.closeFooterActiveChat(item, true);
                }
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
        addFooterActiveChat(chat) {
            chat.action = "add";
            axios
                .post("tabs", {
                    chat: chat
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
                        this.addFooterActiveChat(event);
                    }
            }
        },
        closeFooterActiveChat(chat, clear) {
            return new Promise((resolve, reject) => {
                if (this.chat.chat_id == chat.chat_id && clear) {
                    this.clearActiveChat();
                    this.showTableInProgress = true;
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
        },
        openChatActions(chat) {
            this.$loading(true);
            this.getContentOnLocalStorage(chat.number);
            this.getChatHistory(chat.chat_id);
            if (chat.status === "IN_PROGRESS") {
                this.addFooterActiveChat(chat);
            }
            this.setIncognitoMode();
            this.getActions();
            this.showChatComponent();
            this.connectToMessageSentChannel(chat);
            this.connectToChatStatusChangerChannel(chat);
            if (chat.company_department_id) {
                this.getDepartmentComands(chat.company_department_id);
            } else {
                this.getDepartmentComands(this.chat.companyDepartmentId);
            }
            this.$loading(false);
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
                this.chat_history.push(event.message);
            });
        },
        connectToChatStatusChangerChannel(chat) {
            Echo.join(`chat-status-changer.${chat.chat_id}`).listen(
                "ChatStatusChanger",
                event => {
                    this.chat.status = event.item.status;
                    switch (event.item.status) {
                        case "CANCELED":
                            if (this.showChat) {
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
                    }
                }
            );
        },
        setIncognitoMode() {
            let index = this.cucd.findIndex(
                item =>
                    item.company_department_id === this.chat.companyDepartmentId
            );

            if (index !== -1) {
                if (
                    this.cucd[index].company_user_company_department_id !==
                    this.chat.comp_user_comp_depart_id_current
                ) {
                    this.take_on_chat = true;
                    this.incognito_mode = true;
                } else {
                    this.take_on_chat = false;
                    this.incognito_mode = false;
                }
                this.incognito_id = this.cucd[
                    index
                ].company_user_company_department_id;
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
                this.company_department = this.departments[0].options;
            }
            /** set filter_departments variable */
            this.filter_departments = this.company_department;
            /** delete settings prop for each department in filter variable, this way will not send settings to the route when the filters changes */
            this.filter_departments.forEach(element => {
                delete element.settings;
            });

            localStorage.setItem(
                "filter_departments",
                JSON.stringify(this.company_department)
            );
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
            this.clientChatHistory = [];
            this.clientTicketHistory = [];

            const api = `client/get-client-history`;
            axios
                .get(api, {
                    params: {
                        client_id: id
                    }
                })
                .then(({ data }) => {
                    data.forEach(element => {
                        if (
                            element.type === "TICKET" ||
                            element.type === "CHANGED_TO_TICKET"
                        ) {
                            this.clientTicketHistory.push(element);
                        } else {
                            this.clientChatHistory.push(element);
                        }
                    });
                    $("#modalClientHistory").modal("show");
                });
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
            document.getElementById("input").focus();
        },
        LI(value) {
            if (value == null) {
                return "LI";
            }
            return value.substr(0, 2);
        },
        onResize(e) {
            if (window.outerWidth < 500) {
                if (!this.isMobile) {
                    this.isMobile = true;
                }
            } else {
                if (this.isMobile) {
                    this.isMobile = false;
                }
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
        /** @SHOW */
        showModalAtendent() {
            if (this.selected_department !== null) {
                var vm = this;
                vm.$loading(true);
                axios
                    .get("user-auth/get-agents-by-department", {
                        params: {
                            company_department_id: vm.selected_department.id
                        }
                    })
                    .then(response => {
                        if (response.data.agents.length) {
                            vm.agents_to_transfer = response.data.agents;

                            $("#modalDepartment").modal("hide");
                            $("#modalAtendent").modal("show");
                            vm.$loading(false);
                        } else {
                            Swal.fire({
                                heightAuto: false,
                                icon: "error",
                                title: vm.$t("bs-error"),
                                text: vm.$t(
                                    "bs-no-active-attendants-in-the-department"
                                )
                            });
                            vm.$loading(false);
                        }
                    });
            }
        },
        showTableComponent(table) {
            if (this.hideOnSmall) {
                this.hideOnSmall = !this.hideOnSmall;
            }
            this.hidden = true;
            switch (table) {
                case "inProgress":
                    if (!this.showTableInProgress) {
                        //this.chats_in_progress = [];
                        //this.getChatsInProgress();
                        this.showTableInProgress = true;
                        this.showTableQueue = false;
                        this.showTableTransferred = false;
                        this.showTableClosed = false;
                        this.showTableResolved = false;
                        this.showTableCanceled = false;
                        this.notify.inProgress = false;
                    }
                    break;
                case "queue":
                    if (!this.showTableQueue) {
                        this.chats_on_queue = [];
                        this.showTableQueue = true;
                        this.showTableInProgress = false;
                        this.showTableTransferred = false;
                        this.showTableClosed = false;
                        this.showTableResolved = false;
                        this.showTableCanceled = false;
                        this.notify.onQueue = false;
                    }
                    break;
                case "transferred":
                    if (!this.showTableTransferred) {
                        this.chats_transferred = [];
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
                        this.chats_closed = [];
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
                        this.chats_resolved = [];
                        this.showTableTransferred = false;
                        this.showTableInProgress = false;
                        this.showTableQueue = false;
                        this.showTableClosed = false;
                        this.showTableResolved = true;
                        this.showTableCanceled = false;
                        this.notify.resolved = false;
                    }
                    break;
                case "canceled":
                    if (!this.showTableCanceled) {
                        this.chats_canceled = [];
                        this.showTableTransferred = false;
                        this.showTableInProgress = false;
                        this.showTableQueue = false;
                        this.showTableClosed = false;
                        this.showTableResolved = false;
                        this.showTableCanceled = true;
                        this.notify.canceled = false;
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
                //incognito
                this.incognito_mode = false;
                this.incognito_id = null;
                resolve();
            });
        },
        clearAllChats() {
            /** cleat actual chat */
            this.clearActiveChat();
            /** reset skip */
            this.skip = 0;
            /** clear chats */
            this.chats_on_queue = [];
            //this.chats_in_progress = [];
            this.chats_transferred = [];
            this.chats_closed = [];
            this.chats_resolved = [];
            this.chats_canceled = [];
        },
        resetTable(ref) {
            this.skip = 0;
            switch (ref) {
                case "tableQueue":
                    //limpar array
                    this.chats_on_queue = [];
                    break;
                case "tableInProgress":
                    //limpar array
                    //this.chats_in_progress = [];
                    break;
                case "tableClosed":
                    this.chats_closed = [];
                    break;
                case "tableResolved":
                    this.chats_resolved = [];
                    break;
                case "tableTransferred":
                    //limpar o array
                    this.chats_transferred = [];
                    break;
                case "tableCanceled":
                    this.chats_canceled = [];
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
        }
    },
    computed: {
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
        }
    },
    watch: {
        company_department() {
            /** handle departments filter*/
            this.handleDepartmentsFilter();
            this.$store.commit("getChatsInProgress", this.url_prefix);
            this.getAgentTablesCount();
            /** clear all chats */
            this.clearAllChats();
            /** change table keys */
            this.changeTableKeys();
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
        computedChat: {
            deep: true,
            handler: function(newVal, oldVal) {
                //console.log("mudou");
            }
        }
    }
};
</script>

<style scoped>
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

/*
.footer-active-chat.active .btn-fac-close {
  background: #90d1ff;
} */

::-webkit-scrollbar {
    width: 8px !important;
    height: 8px !important;
}

::-webkit-scrollbar-track {
    background: #dadfed !important;
    border-radius: 0px !important;
}

::-webkit-scrollbar-thumb {
    background: #82c8fa !important;
    border-radius: 2px !important;
}

::-webkit-scrollbar-thumb:hover {
    background: #82c8fa !important;
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
</style>
