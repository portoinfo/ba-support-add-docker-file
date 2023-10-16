<template>
    <div>
        <div :class="{ 'ticket-ticket': !showAnswer, 'mobile': isMobile }" v-if="!showAnswer">

            <div v-if="!isMobile" id="ba-hd__header" class="ticket-showed-header">
                <div class="header_title d-table h-100">
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
                        <b-button class="btn-back-mobile" variant="light" @click="clearAndShowTable()">
                            <span class="bs-ico">&#xe5c4;</span>
                            <gravatar :email="itemselected.email_created" :status="$status.get(itemselected.id_created)"
                                size="46px" :name="$t(itemselected.name_created)" color="primary"
                                :ba_acct_data="itemselected.builderall_account_data" />
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
                        <b-button class="btn-close_info" variant="light"
                            @click="$root.$emit('bv::toggle::collapse', 'sidebar-right-info-2')">
                            <span class="bs-ico">&#xe88e;</span>
                        </b-button>
                    </div>
                </center>
                <div class="item-dept pl-1 text-truncate">
                    <small>{{ `${$t('bs-ticket')} #${itemselected.id} ${messageSelectedNumber}` }}</small>
                </div>
            </div>

            <div class="ticket-showed-left" v-if="!isMobile || showList">
                <div class="ba-hd__card-content">
                    <header>
                        <div class="text-center">
                            <b-form-checkbox v-model="cb_message" @change="showMessage" size="sm">
                                {{ $t("bs-posts") }}
                            </b-form-checkbox>
                        </div>
                        <div class="text-center">
                            <b-form-checkbox v-model="cb_eventos" @change="showMessage" size="sm">
                                {{ $t('bs-events') }}
                            </b-form-checkbox>
                        </div>
                        <div class="text-center">
                            <b-form-checkbox v-model="cb_logs" @change="showMessage" size="sm">
                                {{ $t('bs-logs') }}
                            </b-form-checkbox>
                        </div>
                    </header>
                    <b-list-group class="ticket-list" id="ticket-list">
                        <template v-for="(item, index) in chat_history">
                            <template v-if="item.type == 'EVENT'">
                                <!-- TYPE EVENT -->
                                <b-list-group-item class="default event" :key="index" v-show="cb_eventos">
                                    <div class="list-content text-center">

                                        <div class="date">{{ item.name }}</div>
                                        <div class="name">{{ translate(item.content) }}</div>

                                    </div>
                                </b-list-group-item>
                            </template>
                            <template v-else>
                                <!-- TYPE TEXT -->
                                <!-- clas default ou active pra saber se ta selecionado -->
                                <b-list-group-item :key="index"
                                    @click="clickMessage(index); messageSelectedNumber = item.aux ? `#${item.seq}` : ''"
                                    :class="{ 'default': isMobile || !item.active, 'active': item.active && !isMobile }"
                                    v-show="cb_message">
                                    <div class="list-content">
                                        <div class="avatar">
                                            <gravatar v-if="showGravatar" :email="item.email"
                                                :status="$status.get(item.id_creator)" size="50px" :name="item.name"
                                                :ba_acct_data="item.builderall_account_data" />
                                        </div>
                                        <div class="date">{{ cb_logs ? UTCtoClientTZ2(item.created_at) : '' }}</div>
                                        <div class="name">{{ item.name }}</div>
                                        <div class="counter text-center d-table">
                                            <span class="d-table-cell align-middle">
                                                {{ item.aux ? `#${item.seq}` : '' }}
                                            </span>
                                        </div>
                                    </div>
                                </b-list-group-item>
                            </template>
                        </template>

                        <!-- FIXO DO CHAT/QUESTIONARIO -->
                        <b-list-group-item
                            @click="clickMessage(-1); messageSelectedNumber = isChat ? $t('bs-chat').toUpperCase() : `${$t('bs-initial-form')} + ${$t('bs-description')}`"
                            :class="{ 'default': isMobile || !chat_before_selected, 'active': chat_before_selected && !isMobile }">
                            <div class="list-content">
                                <div class="avatar">
                                    <span class="bs-ico fz-50">&#xe0bf;</span>
                                </div>
                                <div class="date">{{ UTCtoClientTZ2(itemselected.created_at) }}</div>
                                <div class="name">
                                    {{ isChat ? $t('bs-chat').toUpperCase() : `${$t('bs-initial-form')} +
                                                                        ${$t('bs-description')}`
                                    }}
                                </div>
                            </div>
                        </b-list-group-item>

                    </b-list-group>
                </div>
            </div>

            <div class="ticket-showed-main" v-if="!isMobile || showMain">
                <div class="main-content">
                    <div class="ticket-nav">
                        <b-nav tabs>

                            <b-nav-item :active="details_showed" @click="showTab(1)">
                                {{ $t('bs-details') }}
                            </b-nav-item>

                            <b-nav-item :active="actions_showed" @click="showTab(2)" v-if="restriction[0].ticket_returnQueue == 1 ||
                            restriction[0].ticket_moved == 1 ||
                            restriction[0].ticket_moved == 1 ||
                            restriction[0].ticket_blocked == 1 ||
                            restriction[0].ticket_delete == 1 || is_admin">
                                {{ $t('bs-actions') }}
                            </b-nav-item>

                        </b-nav>
                    </div>

                    <!-- DETAILS -->
                    <div v-show="showDetails" class="ticket-header" id="resizable">
                        <div class="ba-hd__card-content">
                            <b-list-group class="pt-3">
                                <b-list-group-item>
                                    <b>{{ $t("bs-name") }}</b>: {{ $t(itemselected.name_created) }}
                                </b-list-group-item>
                                <b-list-group-item>
                                    <b>{{ $t("bs-email") }}</b>: {{ itemselected.email_created }}
                                </b-list-group-item>
                                <b-list-group-item>
                                    <b>{{ $t("bs-attendants") }}</b>: {{ itemselected.email_attendant }}
                                </b-list-group-item>
                                <b-list-group-item>
                                    <div class="output ql-snow">
                                        <div class="ql-editor pt-0 pl-0" v-viewer
                                            v-html="`<b>${$t('bs-description')}:</b> ${itemselected.description}`">
                                        </div>
                                    </div>
                                </b-list-group-item>
                            </b-list-group>
                        </div>
                    </div>
                    <div v-show="showDetails" class="ticket-content">
                        <div class="ba-hd__card-content" id="ticket-ticket-main"
                            :style="!beforeAfter ? 'padding: 0px' : 'padding: 4px 20px;'">
                            <template v-if="beforeAfter">
                                <template
                                    v-if="chat_history[positionSelected] !== undefined && chat_history[positionSelected].type === 'TEXT'">
                                    <div class="output ql-snow">
                                        <div class="ql-editor" v-viewer
                                            v-html="translate(chat_history[positionSelected].content)"></div>
                                    </div>
                                    <!-- <span style="white-space: pre-line;">
                                        {{ translate(chat_history[positionSelected].content) }}
                                    </span> -->
                                </template>
                                <template
                                    v-if="chat_history[positionSelected] !== undefined && chat_history[positionSelected].type === 'EVENT'">
                                </template>
                                <template
                                    v-else-if="chat_history[positionSelected] !== undefined && chat_history[positionSelected].type === 'FILE' && chat_history[positionSelected].content.message !== undefined">
                                    <div class="output ql-snow">
                                        <div class="ql-editor" v-viewer
                                            v-html="translate(chat_history[positionSelected].content.message)"></div>
                                    </div>
                                    <!-- <span style="white-space: pre-line;">
                                        {{ translate(chat_history[positionSelected].content.message) }}
                                    </span> -->
                                    <div>
                                        <br>
                                        <b-button v-for="(item, index) in chat_history[positionSelected].content.files"
                                            :key="index" size="sm" variant="primary"
                                            @click="getFile3(item, chat_history[positionSelected].id)">
                                            {{ item.original_name }}
                                        </b-button>
                                    </div>
                                </template>
                                <br>
                                <br>
                            </template>
                            <template v-else>
                                <div class="grid-container1" v-if="questionary && questionary.length > 0">
                                    <div class="item3 pt-3 pb-3 pl-5">
                                        <center>
                                            {{ `${$t(itemselected.name_created)} :
                                                                                        ${$t("bs-answered-the-questionnaire")}`
                                            }}
                                        </center>
                                    </div>
                                    <div class="item4">{{ formatTime(itemselected.created_at) }}</div>
                                </div>

                                <div v-for="(row, index) in questionary" :key="index">
                                    <div class="grid-container2">
                                        <div class="item1">
                                            {{ itemselected.department }}
                                        </div>
                                        <div class="item2">
                                            <gravatar :email="itemselected.department" :status="'false'" size="32px"
                                                :name="$t(itemselected.department)" />
                                        </div>
                                        <div class="item3 pr-1" v-linkified>
                                            {{ $t(row.question) }}
                                        </div>
                                        <div class="item4">{{ formatTime(itemselected.created_at) }}</div>
                                    </div>

                                    <div class="grid-container2 client">
                                        <div class="item1">
                                            {{ $t(itemselected.name_created) }}
                                        </div>
                                        <div class="item2">
                                            <gravatar :email="itemselected.email_created"
                                                :status="$status.get(itemselected.id_created)" size="32px"
                                                :name="$t(itemselected.name_created)"
                                                :ba_acct_data="itemselected.builderall_account_data" />
                                        </div>
                                        <div class="item3 pr-1">
                                            <div class="output ql-snow">
                                                <div class="ql-editor" v-viewer v-html="row.answer"></div>
                                            </div>
                                        </div>
                                        <div class="item4">{{ formatTime(itemselected.created_at) }}</div>
                                    </div>
                                </div>

                                <template v-if="isChat">
                                    <div>
                                        <component v-for="(message, index) in chat_history_before" :key="index"
                                            :is="setMessageComponent(message.type)"
                                            v-bind="setMessageProps(message, index)" />
                                    </div>
                                </template>
                                
                                <div class="grid-container2 client">
                                    <div class="item1">
                                        {{ $t(itemselected.name_created) }}
                                    </div>
                                    <div class="item2">
                                        <gravatar :email="itemselected.email_created"
                                            :status="$status.get(itemselected.id_created)" size="32px"
                                            :name="$t(itemselected.name_created)"
                                            :ba_acct_data="itemselected.builderall_account_data" />
                                    </div>
                                    <div class="item3 pr-1">
                                        <div class="output ql-snow">
                                            <div class="ql-editor" v-viewer v-html="translate(itemselected.description)"></div>
                                        </div>
                                    </div>
                                    <div class="item4">{{ formatTime(itemselected.created_at) }}</div>
                                </div>

                                

                            </template>
                        </div>
                    </div>

                    <!-- ACTIONS -->
                    <div v-show="showActions" class="actions">
                        <template v-if="!(itemselected.email_attendant == null && itemselected.status == 'CANCELED')">
                            <b-card
                                v-if="restriction[0].ticket_returnQueue == 1 && itemselected.status != 'RESOLVED' && itemselected.status != 'CLOSED' || is_admin">
                                <div class="card-content">
                                    <div>
                                        <span class="ta-l">{{ $t("bs-return-ticket-to-queue") }} -
                                            <b>{{ $t(itemselected.department) }}</b>
                                        </span>
                                    </div>
                                    <div></div>
                                    <div class="actions-col-btn">
                                        <b-button size="sm" variant="success" @click="saveAction(4)">
                                            {{ $t("bs-return-to-queue") }}
                                        </b-button>
                                    </div>
                                </div>
                            </b-card>
                            <b-card
                                v-if="restriction[0].ticket_moved == 1 && itemselected.status != 'RESOLVED' || restriction[0].ticket_moved == 1 && itemselected.status != 'CLOSED' || is_admin">
                                <div class="card-content">
                                    <div>
                                        <span class="ta-l">{{ $t("bs-transfer-department-and-attendant") }} -
                                            <b v-if="itemselected.department_type == 'builderall-mentor'">
                                                {{ $t(itemselected.department) }} <img src="/images/icons/icon_vip.svg"
                                                    height="20" alt="">
                                            </b>
                                            <b v-else>
                                                {{ $t(itemselected.department) }}
                                            </b>
                                        </span>
                                    </div>
                                    <div></div>
                                    <div class="actions-col-btn">
                                        <b-button size="sm" variant="success" @click="openAction(5)">
                                            {{ $t("bs-change") }}
                                        </b-button>
                                    </div>
                                </div>
                            </b-card>
                            <b-card
                                v-if="restriction[0].ticket_moved == 1 && itemselected.status != 'RESOLVED' || restriction[0].ticket_moved == 1 && itemselected.status != 'CLOSED' || is_admin">
                                <div class="card-content">
                                    <div>
                                        <span class="ta-l">{{ $t("bs-transfer-ticket-to-another-department") }} -
                                            <b v-if="itemselected.department_type == 'builderall-mentor'">
                                                {{ $t(itemselected.department) }} <img src="/images/icons/icon_vip.svg"
                                                    height="20" alt="">
                                            </b>
                                            <b v-else>
                                                {{ $t(itemselected.department) }}
                                            </b>
                                        </span>
                                    </div>
                                    <div></div>
                                    <div class="actions-col-btn">
                                        <b-button size="sm" variant="success" @click="openAction(2)">
                                            {{ $t("bs-change") }}
                                        </b-button>
                                    </div>
                                </div>
                            </b-card>
                            <b-card
                                v-if="restriction[0].ticket_moved == 1 && itemselected.status != 'RESOLVED' || restriction[0].ticket_moved == 1 && itemselected.status != 'CLOSED' || is_admin">
                                <div class="card-content">
                                    <div>
                                        <span class="ta-l">{{ $t("bs-transfer-ticket-to-another-attendant") }} -
                                            <b>{{ $t(itemselected.name) }}</b>
                                        </span>
                                    </div>
                                    <div></div>
                                    <div class="actions-col-btn">
                                        <b-button size="sm" variant="success" @click="openAction(1)">
                                            {{ $t("bs-change") }}
                                        </b-button>
                                    </div>
                                </div>
                            </b-card>
                            <template v-if="itemselected.status != 'OPENED' &&
                                restriction[0].ticket_moved == 1 &&
                                itemselected.status != 'RESOLVED' &&
                                itemselected.status != 'CLOSED' ||
                                is_admin ||
                                restriction[0].ticket_admin ||
                                restriction[0].ticket_reopenTicket
                            ">
                                <b-card>
                                    <div class="card-content">
                                        <div class="pr-1"><span class="ta-l">{{ $t("bs-change-ticket-status") }} -
                                                <b>{{ convertTranslate(itemselected.status) }}</b></span></div>
                                        <div class="pr-3">
                                            <b-form-select v-model="changeStatus" :options="listStatus" size="sm">
                                            </b-form-select>
                                        </div>
                                        <div class="actions-col-btn">
                                            <b-button size="sm" variant="success" @click="saveAction(3)">
                                                {{ $t("bs-save") }}
                                            </b-button>
                                        </div>
                                    </div>
                                </b-card>
                            </template>
                        </template>
                        <b-card v-if="restriction[0].ticket_blocked == 1 || is_admin">
                            <div class="card-content">
                                <div>
                                    <span class="ta-l">{{ $t('bs-block') }} {{ $t('bs-client') }} -
                                        <b>{{ $t(itemselected.name_created) }}</b>
                                    </span>
                                </div>
                                <div></div>
                                <div class="actions-col-btn">
                                    <b-button size="sm" :variant="statusblock ? 'success' : 'danger'"
                                        @click="showModalBLock">
                                        {{ statusblock ? $t('bs-unlock') : $t('bs-block') }}
                                    </b-button>
                                </div>
                            </div>
                        </b-card>
                        <b-card v-if="restriction[0].ticket_delete == 1 || is_admin">
                            <div class="card-content">
                                <div class="pr-1"><span class="ta-l">{{ $t("bs-delete") }} {{ $t("bs-ticket") }} -
                                        <b>{{ itemselected.id }}</b></span></div>
                                <div></div>
                                <div class="actions-col-btn">
                                    <b-button size="sm" variant="danger" @click="saveAction(5)">
                                        {{ $t('bs-delete') }}
                                    </b-button>
                                </div>
                            </div>
                        </b-card>
                    </div>
                </div>
            </div>

            <div class="ticket-showed-right" v-if="!isMobile">
                <slot name="comments"></slot>
                <div class="ba-hd__card-content">
                    <header class="pr-2 pl-2">
                        <div style="display: grid; grid-template: auto / auto  auto;" v-if="itemselected.show">
                            <div class="text-left d-table">
                                <b v-if="!$root.$refs.FullTicket2.info_minimized" class="d-table-cell align-middle">{{
                                        $t('bs-ticket-information')
                                }}</b>
                            </div>
                            <div v-if="showChat" class="text-right">
                                <span v-if="!$root.$refs.FullTicket2.info_minimized" class="bs-ico cursor-pointer"
                                    @click="$root.$refs.FullTicket2.info_minimized = true; $root.$refs.FullTicket2.rs_mouse = 'leave'">&#xe931;</span>
                                <span v-else class="bs-ico cursor-pointer pt-1"
                                    @click="$root.$refs.FullTicket2.info_minimized = false; $root.$refs.FullTicket2.rs_mouse = 'over'">&#xe8f4;</span>
                            </div>
                            <div v-else class="text-right pt-1">
                                <span class="bs-ico cursor-pointer"
                                    @click="itemselected.show = false; $root.$refs.FullTicket2.rs_mouse = 'leave'">&#xe5cd;</span>
                            </div>
                        </div>
                    </header>
                    <div v-if="!itemselected.show && !showChat || (itemselected.show && $root.$refs.FullTicket2.info_minimized) && (showChat && $root.$refs.FullTicket2.info_minimized)"
                        class="h-100 w-100 d-table text-center no-data">
                        <span class="align-middle d-table-cell w-100">
                            <img class="m-auto d-block" src="images/icons/olho.svg" width="45px">
                            <br>
                            <span v-show="show_info" class="mr-3 ml-3">{{ $t('bs-no-client-information') }}</span>
                        </span>
                    </div>
                    <template v-else>
                        <slot name="chat-info"></slot>
                    </template>
                </div>
            </div>

            <div class="ticket-showed-footer" v-if="!isMobile">
                <div class="text-left">
                    <b-button
                        v-if="itemselected.status != 'CLOSED' && itemselected.status != 'CANCELED' && itemselected.status != 'RESOLVED'"
                        size="sm" variant="primary" @click="checkAttendant">
                        {{ $t('bs-answer') }}
                    </b-button>
                </div>
                <div class="text-right">
                    <b-button size="sm" variant="primary" @click="clearAndShowTable()">{{ $t('bs-back') }}</b-button>
                </div>
            </div>
        </div>

        <b-button variant="primary" class="float-btn-ticket-create" v-if="isMobile && !showAnswer"
            @click="showAnswer = true">
            <span class="material-icons-outlined"
                style="font-size: 30px; position: relative; top: 4px;  left: -2px;">add_comment</span>
        </b-button>

        <ticket-answer2 v-if="showAnswer && settings && typeof settings === 'object'" :showAnswer="showAnswer"
            :settings="settings" :departments_config="departments_config" :user="user" :is_admin="is_admin" :restriction="restriction" :openCategory="openCategory"
            :itemselected="itemselected" :chat_history="chat_history" :chat_history_before="chat_history_before"
            :isMobile="isMobile" />

        <!-- ========================================================================== MODAIS ========================================================================== -->
        <modal-block v-if="showModalReason" :showmodal="showModalReason" :client_id="itemselected.client.id" :itemselected="itemselected" :type="'TICKET'"></modal-block>

        <!-- <div class="modal fade" id="showModalReason" tabindex="-1" aria-labelledby="showModalReasonLabel"
            aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                        <h5 class="modal-title" id="showModalReasonLabel">{{ $t('bs-reason-for-blocking') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            X
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label v-if="!statusblock" for="exampleFormControlSelect1">{{ $t('bs-type-here') }}:
                                    </label>
                                    <b-form-textarea id="textarea" v-model="textReason"
                                        :placeholder="$t('bs-type-here') + '...'" rows="4" :disabled="statusblock">
                                    </b-form-textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" @click="nextstep(0)" class="text-capitalize btn btn-primary"
                            data-dismiss="modal">
                            {{ $t('bs-cancel') }}
                        </button>
                        <span v-if="!statusblock">
                            <button type="button" @click="blockclient(itemselected, 1)" data-dismiss="modal"
                                id="btn-department" class="btn btn-danger">
                                {{ $t('bs-block') }}
                            </button>
                        </span>
                        <span v-if="statusblock">
                            <button type="button" @click="blockclient(itemselected, 0)" data-dismiss="modal"
                                id="btn-department" class="btn btn-success">
                                {{ $t('bs-unlock') }}
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div> -->
        <div class="modal fade" id="listAttendatsModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                        <h5 class="modal-title" id="exampleModalLabel">{{ $t("bs-change") }} {{ $t('bs-attendant') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="viewCancel">
                            X
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">{{ $t('bs-agents') }}</label>
                                    <multiselect v-model="changeAgents" deselect-label="" selectLabel="" track-by="name"
                                        label="name" :placeholder="phSelectDepart" :options="listAgents"
                                        :searchable="false" :allow-empty="false" id="departments"></multiselect>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" @click="viewCancel" class="text-capitalize btn" data-dismiss="modal">
                            {{ $t('bs-cancel') }}
                        </button>
                        <button type="button" @click="saveAction(1)" :data-dismiss="checkDepartment" id="btn-department"
                            class="btn btn-primary">
                            {{ $t('bs-save') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="listDepartmentModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                        <h5 class="modal-title" id="exampleModalLabel">{{ $t("bs-change") }} {{ $t('bs-department') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="viewCancel">
                            X
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">{{ $t('bs-department') }}</label>
                                    <multiselect v-model="changeDepartment" deselect-label="" selectLabel=""
                                        track-by="name" label="name" :placeholder="phSelectDepart"
                                        :options="listDepartment" :searchable="false" :allow-empty="false"
                                        id="departments">
                                        <template slot="singleLabel" slot-scope="{ option }">
                                            <strong>
                                                {{ option.name }}
                                            </strong>
                                            <img v-if="option.type == 'builderall-mentor'"
                                                src="/images/icons/icon_vip.svg" width="50px" alt="">
                                        </template>
                                        <template slot="option" slot-scope="{ option }">
                                            <strong>
                                                {{ option.name }}
                                            </strong>
                                            <img v-if="option.type == 'builderall-mentor'"
                                                src="/images/icons/icon_vip.svg" width="50px" alt="">
                                        </template>
                                    </multiselect>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" @click="viewCancel" class="text-capitalize btn" data-dismiss="modal">
                            {{ $t('bs-cancel') }}
                        </button>
                        <button type="button" @click="saveAction(2)" :data-dismiss="checkDepartment" id="btn-department"
                            class="btn btn-primary">
                            {{ $t('bs-save') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="listDepartmentAgentsModal" tabindex="-1"
            aria-labelledby="listDepartment2AgentsModal" aria-hidden="true" data-backdrop="static"
            data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                        <h5 class="modal-title" id="exampleModalLabel">{{ $t("bs-change") }} {{ $t('bs-department') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="viewCancel">
                            X
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">{{ $t('bs-department') }}</label>
                                    <multiselect v-model="changeDepartment" deselect-label="" selectLabel=""
                                        track-by="name" label="name" :placeholder="phSelectDepart"
                                        :options="listDepartment" :searchable="false" :allow-empty="false"
                                        id="departments">
                                        <template slot="singleLabel" slot-scope="{ option }">
                                            <strong>
                                                {{ option.name }}
                                            </strong>
                                            <img v-if="option.type == 'builderall-mentor'"
                                                src="/images/icons/icon_vip.svg" width="50px" alt="">
                                        </template>
                                        <template slot="option" slot-scope="{ option }">
                                            <strong>
                                                {{ option.name }}
                                            </strong>
                                            <img v-if="option.type == 'builderall-mentor'"
                                                src="/images/icons/icon_vip.svg" width="50px" alt="">
                                        </template>
                                    </multiselect>
                                </div>
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">{{ $t('bs-agents') }}</label>
                                    <multiselect v-model="changeAgents" deselect-label="" selectLabel="" track-by="name"
                                        label="name" :placeholder="phSelectDepart" :options="listAgents"
                                        :searchable="false" :allow-empty="false" id="departments"></multiselect>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" @click="viewCancel" class="text-capitalize btn" data-dismiss="modal">
                            {{ $t('bs-cancel') }}
                        </button>
                        <button type="button" @click="saveAction(6)" :data-dismiss="checkDepartment" id="btn-department"
                            class="btn btn-primary">
                            {{ $t('bs-save') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ticketAnswer from '../employee/tickets/ticket-answer.vue';

export default {
    components: { ticketAnswer },
    data() {
        return {
            messageOpened: Boolean,
            messageSelectedNumber: "",
            title: "Tickets",
            // showTree: true, // sem uso pratico
            // showListTicket: true, // sem uso pratico
            showTicket: false,
            // showInformation: true, // sem uso pratico
            showDetails: true,
            showAnexo: false,
            showAction: false,
            selected: null,
            options: [{ value: null, text: "Português" }],
            ss: {
                ss1: "tab active",
                ss2: "tab ",
                ss3: "tab ",
            },
            showBody: true,
            viewAswer: false,
            settings: "",
            chat_history: [],
            chat_history_before: [
                {
                    type: "",
                    content: "",
                    name: "",
                    created_at: ""
                },
            ],
            isChat: false,
            beforeAfter: false,
            arquivos: [],
            cb_message: true,
            cb_eventos: false,
            cb_logs: false,
            listAgents: [],
            listDepartment: [],
            listStatus: [],
            changeStatus: "",
            changeDepartment: "",
            changeAgents: "",
            tz: '',
            phSelectDepart: this.$t('bs-select-a-department'),
            checkDepartment: '',
            positionSelected: 0,
            showMenuMobile: false,
            showdata: true,
            statusblock: false,
            textReason: '',
            chat_before_selected: false,
            questionary: "",
            /** message components */
            messageComponent: {
                EVENT: "message-type-event",
                TEXT: "message-type-text",
                OPEN: "message-type-open-agent",
                CLOSE: "message-type-close-agent",
                FILE: "message-type-file-agent",
                IMAGE: "message-type-image-agent",
                ROBOT: "message-type-robot"
            },
            showDetails: true,
            showActions: false,
            showAnswer: false,
            showGravatar: true,
            showModalReason: false,
            index_block: null,
            items: [],
        }
    },
    mounted() {
        this.getCHatHistoryBefore();
        var vm = this;
        vm.settings = JSON.parse(vm.itemselected.settings);
        axios.get('check-block-ticket', {
            params: {
                id: vm.itemselected.client.id,
            }
        }).then(function (r_resposta) {
            // console.log(r_resposta.data.value);
            vm.statusblock = r_resposta.data.value;
        }).catch(function (error) {
            console.log(error);
        });

        axios.get("tickets/get-department", {
            params: {
                is_vip: JSON.parse(vm.itemselected.builderall_account_data)
            }
        }).then(function (r_resposta) {
            //console.log(r_resposta.data.result);
            for (let i = 0; i < r_resposta.data.result.length; i++) {
                r_resposta.data.result[i].name = vm.$t(r_resposta.data.result[i].name);
            }
            vm.listDepartment = r_resposta.data.result;
            let index = vm.listDepartment.findIndex(
                (item) => item.id === vm.itemselected.department_id
            );
            if (index !== -1) {
                vm.listDepartment.splice(index, 1);
            }
        }).catch(function (error) {
            console.log(error);
        });

        axios.get("tickets/get-agents", {
            params: {
                department_id: vm.itemselected.department_id,
            }
        }).then(function (r_resposta) {
            //console.log(r_resposta.data);
            vm.listAgents = r_resposta.data.result;
            // let index = vm.listAgents.findIndex(
            //     (item) => item.email === vm.user.email
            // );
            // if (index !== -1) {
            //     vm.listAgents.splice(index, 1);
            // }
        }).catch(function (error) {
            console.log(error);
        });

        if (this.itemselected.status == 'OPENED') {
            this.itemselected.status = 'IN_PROGRESS'
        }

        this.isMobile ? this.messageOpened = false : this.messageOpened = true;

        //ABRIR ACTIONS AUTOMATICO
        if (this.shortcu_type == 'agent') {
            this.showDetails = false;
            this.showActions = true;
            this.openAction(1);
        } else if (this.shortcu_type == 'depart') {
            this.showDetails = false;
            this.showActions = true;
            this.openAction(2);
        }

        this.setHeaderResize();
    },
    created() {
        this.$root.$refs.TicketTicket2 = this;

        this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
        if (localStorage.getItem("menuticket") == null) {
            localStorage.setItem("menuticket", true + ',' + false + ',' + false);
            this.cb_message = true;
            this.cb_eventos = false;
            this.cb_logs = false;
        } else {
            var array = localStorage.getItem("menuticket").split(',');
            this.cb_message = (array[0] === 'true');
            this.cb_eventos = (array[1] === 'true');
            this.cb_logs = (array[2] === 'true');
        }

        if (this.restriction[0].ticket_close == 1 && this.restriction[0].ticket_resolved == 1 || this.restriction[0].ticket_admin == 1) {
            this.listStatus = [
                { text: this.$t("bs-in-progress"), value: "IN_PROGRESS" },
                { text: this.$t("bs-closed"), value: "CLOSED" },
                { text: this.$t("bs-finalized"), value: "RESOLVED" },
            ];
        } else if (this.restriction[0].ticket_close == 1) {
            this.listStatus = [
                { text: this.$t("bs-in-progress"), value: "IN_PROGRESS" },
                { text: this.$t("bs-closed"), value: "CLOSED" },
            ]
        } else if (this.restriction[0].ticket_resolved == 1) {
            if (this.restriction[0].ticket_close == 0) {
                this.listStatus = [
                    { text: this.$t("bs-in-progress"), value: "IN_PROGRESS" },
                    { text: this.$t("bs-finalized"), value: "RESOLVED" },
                ]
            } else {
                this.listStatus = [
                    { text: this.$t("bs-in-progress"), value: "IN_PROGRESS" },
                    { text: this.$t("bs-finalized"), value: "RESOLVED" },
                ]
            }
        }
    },
    computed: {
        actions_showed() {
            return this.showActions && !this.showDetails;
        },
        details_showed() {
            return this.showDetails && !this.showActions;
        },
        showList() {
            return this.isMobile && !this.messageOpened;
        },
        showMain() {
            return this.isMobile && this.messageOpened;
        },
        resizable() {
            return !this.showAnswer && (!this.isMobile || this.showMain) && this.showDetails;
        }
    },
    props: {
        user: "",
        restriction: "",
        departments_config: "",
        is_admin: "",
        itemselected: "",
        clearActiveChat: "",
        openClientHistory: "",
        openCategory: "",
        info_minimized: "",
        rs_mouse: "",
        show_info: "",
        showChat: "",
        clientChatHistory: "",
        clientTicketHistory: "",
        isMobile: Boolean,
        shortcu_type: "",
    },
    watch: {
        itemselected() {
            console.log(this.itemselected);
        },
        showAnswer() {
            if (this.itemselected.status == 'CLOSED' || this.itemselected.status == 'RESOLVED') {
                this.messageOpened = false;
                this.messageSelectedNumber = "";
                this.clearActiveChat();
                this.$root.$refs.FullTicket2.showTableComponent('inProgress');
            }
        },
        changeDepartment() {
            var vm = this;
            vm.changeAgents = ''
            axios.get("tickets/get-agents", {
                params: {
                    department_id: vm.changeDepartment.id,
                }
            }).then(function (r_resposta) {
                //console.log(r_resposta.data);
                vm.listAgents = r_resposta.data.result;
            }).catch(function (error) {
                console.log(error);
            });
        },
        resizable(val) {
            if (val) {
                var timer = setInterval(() => {
                    if ($('#resizable').length) {
                        this.setHeaderResize();
                        clearInterval(timer);
                        return;
                    }
                }, 100);
            }
        },
        isMobile() {
            var timer = setInterval(() => {
                if ($('#resizable').length) {
                    this.setHeaderResize();
                    clearInterval(timer);
                    return;
                }
            }, 100);
        }
    },
    methods: {
        modalDatabase(){
            this.$root.$refs.FullTicket2.modalDatabase();
        },
        checkAttendant() {
            if (this.itemselected.email_attendant != this.user.email) {
                Swal.fire({
                    title: this.$t('bs-info'),
                    text: this.$t('bs-this-ticket-belongs-to-another-agent-if'),
                    icon: 'info',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: this.$t('bs-ok'),
                });
            }
            this.showAnswer = true;
        },
        setHeaderResize() {
            $("#resizable").resizable({
                handles: 's',
                maxHeight: 1000,
            });
        },
        viewCancel() {
            $("#listDepartmentModal").modal("hide");
        },
        addRealtimeChat_history() {
            Echo.leave(`ticket.${this.itemselected.chat_id_crypt}`);

            Echo.join(`ticket.${this.itemselected.chat_id_crypt}`).listen("MessageSentTicket", (event) => {

                if (!['TEXT', 'EVENT'].includes(event.message.type)) {
                    event.message.content = JSON.parse(event.message.content)
                }

                let seq = Number(1);
                let aux = 0;

                for (let index = 0; index < this.chat_history.length; index++) {
                    if (this.chat_history[index].type != "EVENT") {
                        seq = this.chat_history[index].seq + 1;
                        aux = 1;
                        break;
                    } else {
                        aux = 1;
                        seq = Number(1);
                    }
                }

                event.message.aux = aux;
                event.message.seq = seq;

                this.chat_history.unshift(event.message);

            });
        },
        openAction(value) {
            var vm = this;

            if (value == 1) {
                //TRANFERIR AGENTE
                $("#listAttendatsModal").modal("show");
            }

            if (value == 2) {

                //TRANFERIR DEPARTAMENTO
                $("#listDepartmentModal").modal("show");
            }

            if (value == 3) {
                $("#listDepartAgentModal").modal("show");
            }

            if (value == 5) {
                $("#listDepartmentAgentsModal").modal("show");
            }


        },
        saveAction(value) {
            var vm = this;
            //Agents
            if (value == 1) {
                axios
                    .post("update-ticket", {
                        type: 1,
                        agent: vm.changeAgents,
                        department: vm.itemselected.department_id,
                        ticket: vm.itemselected.id,
                        chat_id: vm.itemselected.chat_id_crypt,
                        email: vm.itemselected.email_created
                    })
                    .then(function (response) {
                        if (response.data.success) {
                            document.location.reload(true);
                            vm.$snotify.success(
                                vm.$t("bs-update-saved-successfully"),
                                vm.$t("bs-success"), {
                                position: "rightTop",
                            });
                        } else {
                            if (response.data.value == "not_agent") {
                                vm.$snotify.info(
                                    vm.$t("bs-he-doesnt-work-in-that-department"),
                                    vm.$t("bs-info"), {
                                    position: "rightTop",
                                });
                            } else {
                                vm.$snotify.error(
                                    vm.$t("bs-error-updating"),
                                    vm.$t("bs-error"), {
                                    position: "rightTop",
                                });
                            }
                        }
                    })
                    .catch(function () {
                        console.log("FAILURE!!");
                    });
            }

            //Department
            if (value == 2) {
                axios
                    .post("update-ticket", {
                        type: 2,
                        department: vm.changeDepartment,
                        ticket: vm.itemselected.id,
                        chat_id: vm.itemselected.chat_id_crypt,
                        email: vm.itemselected.email_created
                    })
                    .then(function (response) {
                        //console.log(response.data.created);
                        if (response.data.success) {
                            document.location.reload(true);
                            vm.$snotify.success(
                                vm.$t("bs-update-saved-successfully"),
                                vm.$t("bs-success"), {
                                position: "rightTop",
                            });
                        } else {
                            vm.$snotify.error(vm.$t("bs-error-updating"), vm.$t("bs-error"), {
                                position: "rightTop",
                            });
                        }
                    })
                    .catch(function () {
                        console.log("FAILURE!!");
                    });
            }

            //Status
            if (value == 3) {
                if (vm.changeStatus == "") {
                    return vm.$snotify.info(vm.$t("bs-empty-field"), vm.$t("bs-info"), {
                        position: "rightTop",
                    });
                }

                axios.post("update-ticket", {
                    type: 3,
                    status: vm.changeStatus,
                    ticket: vm.itemselected.id,
                    department: vm.itemselected.department_id,
                    chat_id: vm.itemselected.chat_id_crypt,
                    current_status: vm.itemselected.status,
                    cucdiu: vm.itemselected.comp_user_comp_depart_id_current,
                    email: vm.itemselected.email_created,
                })
                    .then(function (response) {
                        //console.log(response.data.created);
                        if (response.data.success) {
                            vm.itemselected.status = vm.changeStatus;
                            vm.itemselected.answered = false;
                            vm.clearAndShowTableSet('IN_PROGRESS');
                            vm.$snotify.success(
                                vm.$t("bs-update-saved-successfully"),
                                vm.$t("bs-success"), {
                                position: "rightTop",
                            });
                        } else {
                            if (response.data.info == 'attendant_removed_department') {
                                vm.$snotify.info(vm.$t("bs-attendant-removed-from-department-change"), vm.$t("bs-info"), {
                                    position: "rightTop",
                                });
                            } else {
                                vm.$snotify.error(vm.$t("bs-error-updating"), vm.$t("bs-error"), {
                                    position: "rightTop",
                                });
                            }

                        }
                    })
                    .catch(function (e) {
                        console.log(e);
                        console.log("FAILURE!!");
                    });
            }

            // RETURN TICKET TO QUEUE
            if (value == 4) {
                axios
                    .post("return-ticket", {
                        ticket: vm.itemselected.id,
                        department: vm.itemselected.department_id,
                        chat_id: vm.itemselected.chat_id_crypt,
                        email: vm.itemselected.email_created
                    })
                    .then(function (response) {
                        if (response.data.success) {
                            vm.$snotify.success(
                                vm.$t("bs-ticket-returned-to-queue") + ': ' + vm.itemselected.id,
                                vm.$t("bs-success"), {
                                position: "rightTop",
                            });
                            vm.clearAndShowTable();
                        } else {
                            vm.$snotify.error(vm.$t("bs-error-updating"), vm.$t("bs-error"), {
                                position: "rightTop",
                            });
                        }
                    })
                    .catch(function () {
                        console.log("FAILURE!!");
                    });
            }

            //Delete
            if (value == 5) {
                var vm = this;
                Swal.fire({
                    title: this.$t('bs-are-you-sure'),
                    text: this.$t('bs-you-wont-be-able-to-revert-this'),
                    icon: 'warning',
                    showCancelButton: true,
                    cancelButtonText: this.$t('bs-cancel'),
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: this.$t('bs-yes-delete-it'),
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post('ticket-delete', {
                            ticket_id: vm.itemselected.id,
                        })
                            .then(({ data }) => {
                                if (data.success) {
                                    vm.clearActiveChat();
                                }
                            })
                            .catch(err => {
                                console.error(err);
                            })
                    }
                });
            }

            //Alterar Departamento e Atendente 
            if (value == 6) {
                axios.post("update-ticket", {
                    type: 6,
                    department: vm.changeDepartment,
                    ticket: vm.itemselected.id,
                    chat_id: vm.itemselected.chat_id_crypt,
                    agent: vm.changeAgents,
                })
                    .then(function (response) {
                        //console.log(response.data.created);
                        if (response.data.success) {
                            document.location.reload(true);
                            vm.$snotify.success(
                                vm.$t("bs-update-saved-successfully"),
                                vm.$t("bs-success"), {
                                position: "rightTop",
                            });
                        } else {
                            vm.$snotify.error(vm.$t("bs-error-updating"), vm.$t("bs-error"), {
                                position: "rightTop",
                            });
                        }
                    })
                    .catch(function () {
                        console.log("FAILURE!!");
                    });
            }

        },
        nextstep(value) {
            var vm = this;
            if (value == 1) {
                $("#listDepartmentModal").modal("hide");
            }
            if (value == 2) {
                $("#selectAgentModal").modal("hide");
                $("#selectClientModal").modal("show");
            }
            if (value == 3) {
            }
            if (value == 4) {


            }
        },
        blockclient(item, status) {
            var vm = this;
            axios.post('block-client-ticket', {
                id: item.id_created,
                textReason: vm.textReason,
                status: status,
            }).then(function (response) {

                if (status) {
                    vm.$snotify.success(vm.$t('bs-client-blocked-successfully'), vm.$t('bs-success'), {
                        timeout: 2000,
                        showProgressBar: false,
                        pauseOnHover: true,
                        position: "rightTop",
                    });
                } else {
                    vm.$snotify.success(vm.$t('bs-customer-successfully-released'), vm.$t('bs-success'), {
                        timeout: 2000,
                        showProgressBar: false,
                        pauseOnHover: true,
                        position: "rightTop",
                    });
                }

                vm.statusblock = !vm.statusblock;
            })
                .catch(function (erro) {
                    console.log(erro);
                    console.log('FAILURE!!');
                });
        },
        showModalBLock() {
            // $("#showModalReason").modal("show");
            this.showModalReason = true;
        },
        showTab(tab) {
            switch (tab) {
                case 1:
                    this.showDetails = true;
                    this.showActions = false;
                    break;

                case 2:
                    this.showDetails = false;
                    this.showActions = true;
                    break;
            }
        },
        setMessageComponent(type) {
            return this.messageComponent[type];
        },
        setMessageProps(message, index) {
            return {
                comp_user_comp_depart_id_current: null,
                message: this.chat_history_before[index],
                formatTime: this.formatTime,
                index: index,
                chat_history: this.chat_history_before
            };
        },
        getFile3(message, id) {
            window.open(
                `${window.origin}/ticket/files/${id}/${message.unique_name}`,
                '_blank'
            );
        },
        getFile2(message, id) {
            //console.log(message)
            //console.log(id)
            return `ticket/files/${id}/${message.unique_name}`;
        },
        clickMessage(index) {
            this.chat_before_selected = false;
            this.chat_history.forEach(element => {
                element.active = false;
            });

            this.messageOpened = true;

            if (index == -1 || this.chat_history[index].type == 'EVENT') {
                this.beforeAfter = false;
                this.chat_before_selected = true;
                return;
            } else {
                this.beforeAfter = true;
                this.positionSelected = index;
                this.chat_history[index].active = true;
            }
        },
        showMessage() {
            localStorage.setItem("menuticket", this.cb_message + ',' + this.cb_eventos + ',' + this.cb_logs);
        },
        translate: function (value) {
            if (!value) return ''
            else if (/^bs-/.test(value)) return this.$t(value)
            return value
        },
        getCHatHistoryBefore() {
            var vm = this;
            var url = "ticket-chat-answer/agent/get-ticket-chat-answers";

            axios
                .get(url, {
                    params: {
                        id: vm.itemselected.ticket_id,
                        reference: "ticket_id"
                    }
                })
                .then(response => {
                    vm.questionary = response.data.result;
                    if (!(response.data.result && response.data.result.length > 0)) {
                        axios
                            .get(url, {
                                params: {
                                    id: vm.itemselected.chat_id_crypt,
                                    reference: "chat_id"
                                }
                            })
                            .then(response => {
                                vm.questionary = response.data.result;
                            });
                    }
                });

            axios.get("tickets/get-tickets-chat/", {
                params: {
                    id: vm.itemselected.id,
                    chat_id: vm.itemselected.chat_id_crypt,
                    chat_type: vm.itemselected.chat_type,
                    created_at: vm.itemselected.created_at
                }
            }).then(function (response) {

                //depois
                vm.chat_history = response.data.result2;
                // vm.chat_history = vm.chat_history.reverse()

                if (vm.chat_history.length != 0) {
                    vm.beforeAfter = true;
                }

                //antes
                vm.chat_history_before = response.data.result;

                vm.isChat = response.data.isChat

                vm.addRealtimeChat_history();

                //CARREGAR O ULTIMO TEXT NO TICKET
                var aux = null;
                for (var i = 0; i < vm.chat_history.length; i++) {
                    if (vm.chat_history[i].type == 'TEXT') {
                        //console.log('ENTROU');
                        aux = i;
                        break;
                    }
                }
                if (!vm.isMobile) {
                    if (aux == null) {
                        vm.clickMessage(0);
                    } else {
                        vm.clickMessage(aux);
                    }
                }

                //CARREGAR O ULTIMO TEXT NO TICKET

            })
                .catch(function (error) {
                    console.log(error);
                });
        },
        convertTranslate(value) {
            if (value == 'IN_PROGRESS') {
                return this.$t("bs-in-progress")
            } else if (value == 'RESOLVED') {
                return this.$t("bs-resolved")
            } else if (value == 'CLOSED') {
                return this.$t("bs-closed")
            }
        },
        clearAndShowTable() {
            if (this.showMain) {
                this.messageOpened = false;
                this.messageSelectedNumber = "";
            } else {
                var status = this.itemselected.status;
                this.clearActiveChat();

                switch (status) {
                    case 'IN_PROGRESS':
                        this.$root.$refs.FullTicket2.showTableComponent('inProgress')
                        break;

                    case 'CLOSED':
                    case 'RESOLVED':
                        this.$root.$refs.FullTicket2.showTableComponent('resolved')
                        break;

                    case 'CANCELED':
                        this.$root.$refs.FullTicket2.showTableComponent('canceled')
                        break;

                    default:
                        this.$root.$refs.FullTicket2.showTableComponent('inProgress')
                        break;
                }
            }

            $("#listDepartmentModal").modal("hide");
            $("#listDepartAgentModal").modal("hide");
            $("#listAttendatsModal").modal("hide");
            $("#selectAgentModal").modal("hide");
            $("#listDepartmentAgentsModal").modal("hide");
        },
        clearAndShowTableSet(status) {
            if (this.showMain) {
                this.messageOpened = false;
                this.messageSelectedNumber = "";
            } else {
                this.clearActiveChat();
                switch (status) {
                    case 'IN_PROGRESS':
                        this.$root.$refs.FullTicket2.showTableComponent('inProgress')
                        break;

                    case 'CLOSED':
                    case 'RESOLVED':
                        this.$root.$refs.FullTicket2.showTableComponent('resolved')
                        break;

                    case 'CANCELED':
                        this.$root.$refs.FullTicket2.showTableComponent('canceled')
                        break;

                    default:
                        this.$root.$refs.FullTicket2.showTableComponent('inProgress')
                        break;
                }
            }

            $("#listDepartmentModal").modal("hide");
            $("#listDepartAgentModal").modal("hide");
            $("#listAttendatsModal").modal("hide");
            $("#selectAgentModal").modal("hide");
        },
        UTCtoClientTZ2(value = null) {
            try {
                if (value === null) {
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
                        .tz(Intl.DateTimeFormat().resolvedOptions().timeZone)
                        .locale(this.user.language)
                        .format('LT L');
                }
            } catch (error) {
                return '';
            }
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
                .locale(this.user.language)
                .format("LT");
        },
    },
}
</script>

<style scoped>
#ticket-ticket-main .ql-editor {
    word-break: break-word !important;
}

.ql-editor {
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

.grid-container1 .item3 {
    grid-area: content;
    color: #707070;
    font-size: 15px;
    font-stretch: 100%;
    font-weight: 700;
    text-rendering: optimizeLegibility;
    -webkit-font-feature-settings: "kern" 1;
    line-height: 19px;
    padding-bottom: 5px;
    padding-right: 5px;
    padding-left: 5px;
}

.grid-container1 .item4 {
    grid-area: time;
    text-align: right;
    color: #6e6e6e;
    opacity: 1;
    font-size: 11px;
    line-height: 20px;
    text-rendering: optimizeLegibility;
    font-weight: 700;
    padding-right: 5px;
}

.grid-container1 {
    display: grid;
    grid-template-areas: "content time";
    grid-template-columns: auto 60px;
    border-top: 1px solid rgba(215, 222, 230, 0.2);
}



.grid-container2 .item1 {
    grid-area: name;
    color: #0080fc;
    font-size: 15px;
    font-stretch: 100%;
    font-weight: 800;
    text-rendering: optimizeLegibility;
    line-height: 22px;
    padding-left: 5px;
}

.grid-container2 .item2 {
    grid-area: gravatar;
    display: flex;
    align-items: initial;
    justify-content: center;
    padding-top: 8px;
}

.grid-container2 .item3 {
    grid-area: content;
    color: #707070;
    font-size: 0.9rem;
    font-stretch: 100%;
    font-weight: 600;
    text-rendering: optimizeLegibility;
    -webkit-font-feature-settings: "kern" 1;
    line-height: 19px;
    padding-bottom: 5px;
    padding-right: 5px;
    padding-left: 5px;
    word-break: break-word;
}

.grid-container2 .item4 {
    grid-area: time;
    text-align: right;
    color: #6e6e6e;
    opacity: 1;
    font-size: 11px;
    line-height: 20px;
    text-rendering: optimizeLegibility;
    font-weight: 700;
    padding-right: 5px;
}

.grid-container2 {
    display: grid;
    grid-template-areas:
        "gravatar name time"
        "gravatar content content";
    grid-template-columns: 45px auto 60px;
    border-top: 1px solid rgba(215, 222, 230, 0.2);
}

.grid-container2.client .item1 {
    color: #333333;
}

@media screen and (max-width: 992px) {
    .grid-container2 .item3 {
        font-size: 16px;
    }
}


.mobile .ticket-showed-header {
    position: relative;
    top: 0;
    width: 100vw;
    z-index: 4;
    min-height: 55px !important;
    max-height: 55px !important;
    grid-template-columns: 80px auto 50px;
    line-height: unset !important;
    background: red;
    background-color: white;
    border-radius: 5px 5px 0px 0px;
    box-shadow: 0px 0px 9px #26242424;
    z-index: 4;
    display: grid;
    grid-template-areas:
        'gravatar name btn'
        'gravatar dept btn';
    grid-template-rows: 50%;
    border-radius: 0px !important;
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

.item-dept {
    grid-area: dept;
}


.mobile .ticket-showed-header .btn-back-mobile {
    background: white;
    padding-left: 0;
    padding-top: 0;
    padding-bottom: 3px;
    padding-right: 4px;
    border: none !important;
    border-radius: 14px;
}

.mobile .ticket-showed-header .btn-back-mobile .bs-ico {
    color: #333333;
    position: relative;
    bottom: -9px;
}

.mobile .ticket-showed-header .btn-close_info {
    background: white;
    padding: 0;
    border: none !important;
    border-radius: 14px;
    height: 30px;
    width: 30px;
}

.mobile .ticket-showed-header .btn-close_info .bs-ico {
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

span.info {
    max-width: 100px;
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
    /* font: normal normal bold 14px/35px Muli; */
    letter-spacing: 0px;
    color: #656565;
}

.modal-body select {
    background: #fafbfc 0% 0% no-repeat padding-box;
    border: 1px solid #dddddd;
    border-radius: 3px;
    height: 50px;
}
</style>
