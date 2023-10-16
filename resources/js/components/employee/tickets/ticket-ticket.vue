<template>
    <div class="h-100">
        <!-- TITLE -->
        <b-row v-if="showBody" class="ml-custom mobile">
            <b-container fluid>
                <h1>{{$t('bs-ticket')}} #{{ itemselected.id }}</h1>
            </b-container>
        </b-row>

        <!-- BODY -->
        <div v-if="showBody" class="h-100 mt-2">
            <b-row
            v-show="isMobile && !showMenuMobile && showBody"
            class="mobile pt-2"
            cols="1"
            >
                <b-col>
                    <div
                    @click="goBackToMenu()"
                    class="p-0 bg-transparent border-0 text-primary btn-back"
                    >
                    <i class="material-icons" style="font-size: 40px"
                        >keyboard_arrow_left</i
                    >{{ $t("bs-see-list").toUpperCase() }}
                    </div>
                </b-col>
            </b-row>
            <b-row class="h-90 mobile">
                <!-- LEFT SIDEBAR-->
                <b-col v-if="!isMobile || (isMobile && showMenuMobile)" class="pr-0" style="min-width: 280px; width: 100%; height: 100%;">
                    <b-card id="card-tree" class="h-100" style="overflow: scroll;">
                        <ul class="list-group myUL">
                            <div class="row ml-2 m-3">
                                <b-form-checkbox
                                    v-model="cb_message"
                                    @change="showMessage"
                                    class="ml-1 text"
                                >
                                    {{ $t("bs-posts") }}
                                </b-form-checkbox>
                                <b-form-checkbox class="ml-1 text" v-model="cb_eventos" @change="showMessage">
                                    {{$t('bs-events')}}
                                </b-form-checkbox>
                                <b-form-checkbox class="ml-1 text" v-model="cb_logs" @change="showMessage">{{$t('bs-logs')}}</b-form-checkbox>
                            </div>
                        </ul>
                        <span v-for="(item, index) in chat_history" :key="index">
                            <span v-if="item.type == 'EVENT'">
                                <span v-show="cb_eventos">
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            <span style="text-transform: capitalize"> {{item.name}} </span>: {{ translate(item.content) }}
                                        </li>
                                        <!-- <b-list-group-item variant="success">
                                            <span style="text-transform: capitalize"> {{item.name}} </span>: {{ translate(item.content) }}
                                        </b-list-group-item>   -->
                                    </ul>
                                </span>
                            </span>
                            <span v-else>
                                <span v-show="cb_message">
                                    <ul class="list-group myUL caret">
                                        <li class="list-group-item" @click="clickMessage(index); goToContent();" :style="{ backgroundColor: settingcolor[index], color: color[index] }">
                                            <b-row align-v="center" align-h="around" no-gutters>
                                                <b-col class="text-left col-auto">
                                                    <span v-if="item.name == itemselected.name">
                                                        <gravatar
                                                            v-if="showdata"
                                                            :email="itemselected.email"
                                                            :status="$status.get(user.id)"
                                                            size="50px"
                                                            :name="itemselected.name"
                                                            color="light"
                                                            :ba_acct_data="user.builderall_account_data"
                                                        />
                                                    </span>
                                                    <span v-else>
                                                        <gravatar
                                                            v-if="showdata"
                                                            :email="itemselected.email_created"
                                                            :status="$status.get(itemselected.id_created)"
                                                            size="50px"
                                                            :name="itemselected.name_created"
                                                            color="light"
                                                            :ba_acct_data="itemselected.builderall_account_data"
                                                        />
                                                    </span>
                                                </b-col>
                                                <b-col cols="8" lg="7" class="px-2">
                                                    <span v-show="cb_logs">
                                                        <span style="color: black; font-size: 14px">
                                                            {{ UTCtoClientTZ2(item.created_at) }}
                                                        </span>
                                                        <br />
                                                    </span>
                                                    <span style="color: black">
                                                        {{ item.name }}
                                                    </span>
                                                </b-col>
                                                <b-col class="text-right">
                                                    <span style="color: black;font-size: 24px;">
                                                        <!-- <span v-show="false">{{contador = index + 1 }}</span> -->
                                                        <!-- #{{chat_history.length - contador}}aa -->
                                                    </span>
                                                </b-col>
                                            </b-row>
                                        </li>
                                    </ul>
                                </span>
                            </span>
                        </span>

                        <!-- //ANTES DE VIRAR TICKET - CARD UNICO  -->
                        <ul class="list-group myUL caret">
                            <li class="list-group-item" @click="clickMessage(-1); goToContent();" :style="{ backgroundColor: settingcolor[-1]}">
                                <b-row align-v="center" align-h="around" no-gutters>
                                    <b-col class="text-left col-auto">
                                        <!-- :text="LI(itemselected.name)" -->
                                        <b-avatar
                                            class="mr-1"
                                            variant="info"
                                            text="CHAT"
                                            size="3rem"
                                        ></b-avatar>
                                    </b-col>
                                    <b-col cols="8" lg="7" class="px-2">
                                        <span style="color: black; font-size: 14px">
                                            {{ UTCtoClientTZ2(itemselected.created_at) }}
                                        </span><br />
                                        <span style="color: black">
                                            <!-- {{ item.name }} -->
                                            {{isChat ? $t('bs-chat').toUpperCase() : `${$t('bs-initial-form')} + ${$t('bs-description')}`}}
                                        </span>
                                    </b-col>
                                    <b-col class="text-right">
                                        <span style="color: black;font-size: 24px;">
                                            <!-- #{{chat_history.length - index }}  #0-->
                                        </span>
                                    </b-col>
                                </b-row>
                            </li>
                        </ul>



                    </b-card>
                </b-col>

                <!-- CONTENT -->
                <b-col v-if="!isMobile || (isMobile && !showMenuMobile)" cols="12" xl="7" class="h-100 wrapper">
                    <!-- TAB TITLES -->
                    <template>
                        <div class="ml-custom px-lg-3 wrapper-tabs">
                            <div class="bs-m-spacing">
                                <a v-on:click.stop="showIB(1)" href="#" :class="ss.ss1">
                                    {{ $t("bs-details") }}
                                </a>
                            </div>
                            <!-- <div class="bs-m-spacing">
                                <a v-on:click.stop="showIB(2)" href="#" :class="ss.ss2">
                                    {{ $t("bs-attachments") }}
                                </a>
                            </div> -->
                            <div
                                v-if="restriction[0].ticket_moved == 1 || is_admin == 1 || restriction[0].ticket_admin == 1"
                                class="bs-m-spacing"
                            >
                                <a v-on:click.stop="showIB(3)" href="#" :class="ss.ss3">
                                    {{ $t("bs-actions") }}
                                </a>
                            </div>
                        </div>
                    </template>

                    <!-- TAB DETAILS -->
                    <div class="wrapper-tab-details" v-if="showDetails">
                        <div class="pt-2">
                            <b-card
                                show
                                id="list"
                                class="h-100"
                                style="
                                    font: normal normal bold 16px/25px Muli;
                                    letter-spacing: 0px;
                                    color: #707070;
                                "
                            >
                                <span><b>{{ $t("bs-name") }}:</b> {{ itemselected.name_created }} </span>
                                <br />
                                <span><b>{{ $t("bs-email") }}:</b> {{ itemselected.email_created }} </span>
                                <br />
                                <span><b>{{ $t("bs-attendants") }}:</b> {{ itemselected.email }} </span>
                                <br />
                                <span><b>{{ $t("bs-description") }}:</b> {{ itemselected.description }} </span>
                                <br />
                            </b-card>
                        </div>
                        <div class="">
                            <b-card
                                show
                                id="list"
                                class="h-100"
                                style="
                                    font: normal normal bold 16px/31px Muli;
                                    letter-spacing: 0px;
                                    color: #707070;
                                " v-chat-scroll
                            >
                                <!-- DEPOIS DO CHAT -->
                                <span v-if="beforeAfter">
                                    <!-- TIPO TEXT -->
                                    <template v-if="chat_history[positionSelected] !== undefined && chat_history[positionSelected].type === 'TEXT'">
                                        <span style="white-space: pre-line;">
                                            {{ translate(chat_history[positionSelected].content) }}
                                        </span>
                                    </template>
                                    <!--  TIPO EVENT (FUTURO) -->
                                    <template v-if="chat_history[positionSelected] !== undefined && chat_history[positionSelected].type === 'EVENT'">
                                        <!-- <span style="white-space: pre-line;">
                                            {{ translate(chat_history[positionSelected].content) }}
                                        </span> -->

                                        <b style="text-transform: capitalize;">
                                            <span style="color:#31B404;" :data-tooltip="UTCtoClientTZ2( chat_history[positionSelected].created_at )"><hr>{{$t('bs-event')}}:</span>
                                        </b>
                                        <span>
                                            <span style="white-space: pre-line;">{{ chat_history[positionSelected].name }} {{ translate(chat_history[positionSelected].content) }}<hr></span>
                                        </span>
                                    </template>
                                    <!-- FILE MODELO NOVO -->
                                    <template v-else-if="chat_history[positionSelected]!== undefined && chat_history[positionSelected].type === 'FILE' && chat_history[positionSelected].content.message !== undefined">
                                        <span style="white-space: pre-line;">
                                            {{ translate(chat_history[positionSelected].content.message) }}
                                        </span>
                                        <br>
                                        <br>
                                        <span v-for="(item, index) in chat_history[positionSelected].content.files" :key="index">
                                           <a
                                            :href="getFile2(item, chat_history[positionSelected].id)"
                                            target="_blank"
                                            :class="['btn ml-2', chat_history[positionSelected].company_user_company_department_id ? 'btn-secondary' : 'btn-primary' ]"
                                        >{{item.original_name}}</a>
                                        </span>
                                    </template>
                                    <!-- TIPO IMAGE/FILE MODELO ANTIGO -->
                                    <template v-else-if="chat_history[positionSelected] !== undefined && ['FILE', 'IMAGE'].includes(chat_history[positionSelected].type)">
                                        <br>
                                        <br>
                                        <span>
                                           <a
                                            :href="getFile2(chat_history[positionSelected].content, chat_history[positionSelected].id)"
                                            target="_blank"
                                            :class="['btn ml-2', chat_history[positionSelected].company_user_company_department_id ? 'btn-secondary' : 'btn-primary' ]"
                                        >{{chat_history[positionSelected].content.original_name}}</a>
                                        </span>
                                    </template>
                                </span>
                                <!-- HISTORICO CHAT -->
                                <span v-else>
                                    <div class="mt-1" v-for="(item, key) in chat_history_before" :key="key">
                                        <!-- FALA FUNCIONARIO -->
                                        <span v-if="user.id == item.created_by">
                                            <!-- TIPO TEXTO -->
                                            <template v-if="item.type === 'TEXT'">
                                                <b style="text-transform: capitalize;">
                                                    <span style="color:#B40404;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ item.name }}:</span>
                                                </b>
                                                <span v-linkified>
                                                    <span style="white-space: pre-line;">{{ translate(item.content) }}</span>
                                                </span>
                                            </template>
                                            <!-- TIPO EVENT -->
                                            <template v-if="item.type === 'EVENT'">
                                                <b style="text-transform: capitalize;">
                                                    <span style="color:#31B404;" :data-tooltip="UTCtoClientTZ2( item.created_at)"><hr>{{$t('bs-event')}}:</span>
                                                </b>
                                                <span v-linkified>
                                                    <span style="white-space: pre-line;">{{ item.name }} {{ translate(item.content) }}<hr></span>
                                                </span>
                                            </template>
                                            <!-- TIPO FILE (PADRAO ANTIGO) -->
                                            <template v-if="item.type === 'FILE'">
                                                <b style="text-transform: capitalize;">
                                                    <span style="color:#B40404;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ item.name }}:</span>
                                                </b>
                                                <a target="_blank" :href="getFile(item)">
                                                    {{ item.content.original_name }}
                                                </a>
                                            </template>
                                            <!-- TIPO IMAGE (PADRAO ANTIGO) -->
                                            <template v-if="item.type === 'IMAGE'">
                                                <b style="text-transform: capitalize;">
                                                    <span style="color:#B40404;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ item.name }}:</span>
                                                </b>
                                                <clazy-load :src="getFile(item)">
                                                    <a target="_blank" :href="getFile(item)">
                                                        <img style="max-width: 40%" :src="getFile(item)"/>
                                                    </a>
                                                    <div class="preloader" slot="placeholder">
                                                        <clip-loader :color="'#A9A9A9'" :size="'40px'" />
                                                    </div>
                                                </clazy-load>
                                            </template>
                                        </span>
                                        <!-- FALA CLIENTE -->
                                        <span v-else>
                                            <!-- TIPO TEXT -->
                                            <template v-if="item.type === 'TEXT'">
                                                <b style="text-transform: capitalize;">
                                                    <span style="color:#0B0B61;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ item.name }}:</span>
                                                </b>
                                                <span v-linkified>
                                                    {{ translate(item.content) }}
                                                </span>
                                            </template>
                                            <!-- TIPO EVENT -->
                                            <template v-if="item.type === 'EVENT'">
                                                <!-- <b style="text-transform: capitalize;">
                                                    <span style="color:#0B0B61;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ item.name }}:</span>
                                                </b>
                                                <span v-linkified>
                                                    {{ translate(item.content) }}
                                                </span> -->

                                                <b style="text-transform: capitalize;">
                                                    <span style="color:#31B404;" :data-tooltip="UTCtoClientTZ2( item.created_at)"><hr>{{$t('bs-event')}}:</span>
                                                </b>
                                                <span v-linkified>
                                                    <span style="white-space: pre-line;">{{ item.name }} {{ translate(item.content) }}<hr></span>
                                                </span>
                                            </template>
                                            <template v-if="item.type === 'FILE'">
                                                <b style="text-transform: capitalize;">
                                                    <span style="color:#0B0B61;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ item.name }}:</span>
                                                </b>
                                                <a target="_blank" :href="getFile(item)">
                                                    {{ item.content.original_name }}
                                                </a>
                                            </template>
                                            <template v-if="item.type === 'IMAGE'">
                                                <b style="text-transform: capitalize;">
                                                    <span style="color:#0B0B61;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ item.name }}:</span>
                                                </b>
                                                <clazy-load :src="getFile(item)">
                                                    <a target="_blank" :href="getFile(item)">
                                                        <img style="max-width: 40%" :src="getFile(item)"/>
                                                    </a>
                                                    <div class="preloader" slot="placeholder">
                                                        <clip-loader :color="'#A9A9A9'" :size="'40px'" />
                                                    </div>
                                                </clazy-load>
                                            </template>
                                        </span>
                                    </div>
                                </span>

                                <br>
                            </b-card>
                        </div>
                    </div>

                    <!-- TAB attachments -->
                    <div class="h-100 wrapper-tab-attachments" v-if="showAnexo">
                        <div class="h-100 pt-2">
                            <b-card
                                show
                                id="list"
                                style="
                                    padding: 8px;
                                    margin-bottom: 8px;
                                    font: normal normal bold 14px/31px Muli;
                                    letter-spacing: 0px;
                                    color: #707070;
                                "
                            >
                                <img :src="arquivos.route" class="css-class" width="60px;" />
                                <b-link target="_blank" :href="arquivos.route">{{
                                    arquivos.name
                                }}</b-link>
                            </b-card>
                        </div>
                    </div>

                    <!-- TAB ACTIONS -->
                    <div class="h-100 wrapper-tab-actions" v-if="showAction">
                        <div class="h-100 pt-2">
                            <!-- ALTERAR DEPARTAMENTO DO TICKET -->
                            <b-card show id="list" class="card-custom">
                                 <b-row cols="1" cols-lg="2" align-h="between" no-gutters>
                                    <b-col class="py-2 py-md-1 px-2">
                                        <span class="ta-l">{{ $t("bs-transfer-ticket-to-another-department") }} -
                                            <b v-if="itemselected.department_type == 'builderall-mentor'">
                                                {{ $t(itemselected.department) }} <img src="/images/icons/icon_vip.svg" height="20" alt="">
                                                </b>
                                            <b v-else>
                                                {{ $t(itemselected.department) }}
                                            </b>
                                        </span>
                                    </b-col>
                                    <b-col class="ta-r">
                                        <b-row cols="1" cols-lg="2" align-h="end" no-gutters>
                                            <!-- <b-col class="py-2 py-md-1 px-2">
                                                <select
                                                    v-model="changeDepartment"
                                                    class="custom-select"
                                                >
                                                    <option></option>
                                                    <option
                                                        v-for="(option,key) in listDepartment"
                                                        v-bind:value="option.id"
                                                        :key="key"
                                                    >
                                                        {{ option.name }}
                                                    </option>
                                                </select>
                                            </b-col> -->
                                            <b-col lg="auto" class="py-2 py-md-1 px-2">
                                                <button
                                                    type="button"
                                                    @click="openAction(2)"
                                                    class="btn btn-success btn-block"
                                                >
                                                    {{ $t("bs-change") }}
                                                </button>
                                            </b-col>
                                        </b-row>
                                    </b-col>
                                </b-row>
                            </b-card>
                            <!-- ALTERAR ATENDENTE DO TICKET -->
                            <b-card show id="list" class="card-custom">
                                <b-row cols="1" cols-lg="2" align-h="between" no-gutters>
                                    <b-col class="py-2 py-md-1 px-2">
                                        <span class="ta-l">{{ $t("bs-transfer-ticket-to-another-attendant") }} -
                                            <b>{{ itemselected.name }}</b>
                                        </span>
                                    </b-col>
                                    <b-col class="ta-r">
                                        <b-row cols="1" cols-lg="2" align-h="end" no-gutters>
                                            <b-col lg="auto" class="py-2 py-md-1 px-2">
                                                <button
                                                    type="button"
                                                    @click="openAction(1)"
                                                    class="btn btn-success btn-block"
                                                >
                                                    {{ $t("bs-change") }}
                                                </button>
                                            </b-col>
                                        </b-row>
                                    </b-col>
                                </b-row>
                            </b-card>
                            <!-- <b-card show id="list" class="card-custom">
                                <b-row cols="1" cols-lg="2" align-h="between" no-gutters>
                                    <b-col class="py-2 py-md-1 px-2">
                                        <span class="ta-l">Alterar departamento e atendente -
                                            <b>{{ itemselected.department }} - {{ itemselected.name }}</b>
                                        </span>
                                    </b-col>
                                    <b-col class="ta-r">
                                        <b-row cols="1" cols-lg="2" align-h="end" no-gutters>
                                            <b-col lg="auto" class="py-2 py-md-1 px-2">
                                                <button
                                                    type="button"
                                                    @click="openAction(3)"
                                                    class="btn btn-success btn-block"
                                                >
                                                    {{ $t("bs-change") }}
                                                </button>
                                            </b-col>
                                        </b-row>
                                    </b-col>
                                </b-row>
                            </b-card> -->
                            <!-- ALTERAR STAUTS DO TICKET -->
                            <template v-if="itemselected.status != 'OPENED'">
                                <b-card show id="list" class="card-custom">
                                    <b-row  cols="1" cols-lg="2" align-h="between" no-gutters>
                                        <b-col class="py-2 py-md-1 px-2">
                                            <span class="ta-l">{{ $t("bs-change-ticket-status") }}</span>
                                        </b-col>
                                        <b-col class="ta-r">
                                            <b-row  cols="1" cols-lg="2" align-h="end" no-gutters>
                                                <b-col class="py-2 py-md-1 px-2">
                                                    <b-form-select
                                                        class="custom-select"
                                                        v-model="changeStatus"
                                                        :options="listStatus"
                                                    ></b-form-select>
                                                </b-col>
                                                <b-col lg="auto" class="py-2 py-md-1 px-2">
                                                    <button
                                                        type="button"
                                                        @click="saveAction(3)"
                                                        class="btn btn-success btn-block"
                                                    >
                                                        {{ $t("bs-save") }}
                                                    </button>
                                                </b-col>
                                            </b-row>
                                        </b-col>
                                    </b-row>
                                </b-card>
                            </template>
                            <b-card v-if="restriction[0].ticket_blocked" id="list" class="card-custom">
                                <b-row cols="1" cols-lg="2" align-h="between" no-gutters>
                                    <b-col class="py-2 py-md-1 px-2">
                                        <span class="ta-l">{{$t('bs-block')}} {{$t('bs-client')}} -
                                            <b>{{ itemselected.name_created }}</b>
                                        </span>
                                    </b-col>
                                    <b-col class="ta-r">
                                        <b-row cols="1" cols-lg="2" align-h="end" no-gutters>
                                            <b-col lg="auto" class="py-2 py-md-1 px-2">
                                                <button
                                                    v-if="statusblock"
                                                    type="button"
                                                    @click="showModalBLock"
                                                    class="btn btn-danger btn-block"
                                                >
                                                    {{$t('bs-block')}}
                                                </button>
                                                <button
                                                    v-if="!statusblock"
                                                    type="button"
                                                    @click="showModalBLock"
                                                    class="btn btn-info btn-block"
                                                >
                                                    {{$t('bs-release')}}
                                                </button>
                                            </b-col>
                                        </b-row>
                                    </b-col>
                                </b-row>
                            </b-card>
                        </div>
                    </div>
                </b-col>

                <!-- RIGHT SIDEBAR-->
                <b-col v-if="!isMobile" class="h-100 pl-0">
                    <ticket-information
                        :itemselected="itemselected"
                        :user="user"
                        :openClientHistory="openClientHistory"
                    ></ticket-information>
                </b-col>
            </b-row>

            <!-- BUTTONS -->
            <b-row v-if="showBody" class="mobile py-2 buttons-wrapper">
                <b-col sm="12" xl="3" class="mt-1">
                    <b-button
                        v-if="itemselected.status != 'CLOSED' && itemselected.status != 'CANCELED' && itemselected.status != 'RESOLVED'"
                        type="submit" variant="primary"
                        @click="OpenAswer"
                    >{{
                        $t("bs-answer")
                    }}</b-button>
                    <b-button
                        type="submit"
                        variant="primary"
                        @click="back"
                        :style="{
                            gridColumn: itemselected.status == 'CLOSED' ? '1 / span 2' : '2 / span 1'
                        }"
                    >{{
                        $t("bs-back")
                    }}</b-button>
                </b-col>
            </b-row>
        </div>

        <div class="modal fade" id="showModalReason" tabindex="-1" aria-labelledby="showModalReasonLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                        <h5 class="modal-title" id="showModalReasonLabel">{{$t('bs-reason-for-blocking')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            X
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">{{$t('bs-type-here')}}: </label>
                                    <b-form-textarea
                                    id="textarea"
                                    v-model="textReason"
                                    :placeholder="$t('bs-type-here')+'...'"
                                    rows="4"
                                    ></b-form-textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" @click="nextstep(0)" class="text-capitalize btn btn-primary" data-dismiss="modal">
                            {{$t('bs-cancel')}}
                        </button>
                        <span v-if="statusblock">
                            <button type="button" @click="blockclient(itemselected, 1)" data-dismiss="modal" id="btn-department" class="btn btn-danger">
                                {{$t('bs-block')}}
                            </button>
                        </span>
                        <span v-if="!statusblock">
                            <button type="button" @click="blockclient(itemselected, 0)" data-dismiss="modal" id="btn-department" class="btn btn-success">
                                {{$t('bs-unlock')}}
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="listDepartmentModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                        <h5 class="modal-title" id="exampleModalLabel">{{$t("bs-change")}} {{$t('bs-department')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="viewCancel">
                            X
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">{{$t('bs-department')}}</label>
                                    <multiselect
                                    v-model="changeDepartment"
                                    deselect-label=""
                                    selectLabel=""
                                    track-by="name"
                                    label="name"
                                    :placeholder="phSelectDepart"
                                    :options="listDepartment"
                                    :searchable="false"
                                    :allow-empty="false"
                                    id="departments">
                                    <template slot="singleLabel" slot-scope="{ option }">
                                        <strong>
                                        {{ option.name }}
                                        </strong>
                                        <img v-if="option.type == 'builderall-mentor'" src="/images/icons/icon_vip.svg" width="50px" alt="">
                                    </template>
                                    <template slot="option" slot-scope="{ option }">
                                        <strong>
                                        {{ option.name }}
                                        </strong>
                                        <img v-if="option.type == 'builderall-mentor'" src="/images/icons/icon_vip.svg" width="50px" alt="">
                                    </template>
                                    </multiselect>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" @click="viewCancel" class="text-capitalize btn" data-dismiss="modal">
                            {{$t('bs-cancel')}}
                        </button>
                        <button type="button" @click="saveAction(2)" :data-dismiss="checkDepartment" id="btn-department" class="btn btn-primary">
                            {{$t('bs-save')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="listAttendatsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                        <h5 class="modal-title" id="exampleModalLabel">{{$t("bs-change")}} {{$t('bs-attendant')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="viewCancel">
                            X
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">{{$t('bs-agents')}}</label>
                                    <multiselect
                                    v-model="changeAgents"
                                    deselect-label=""
                                    selectLabel=""
                                    track-by="name"
                                    label="name"
                                    :placeholder="phSelectDepart"
                                    :options="listAgents"
                                    :searchable="false"
                                    :allow-empty="false"
                                    id="departments"></multiselect>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" @click="viewCancel" class="text-capitalize btn" data-dismiss="modal">
                            {{$t('bs-cancel')}}
                        </button>
                        <button type="button" @click="saveAction(1)" :data-dismiss="checkDepartment" id="btn-department" class="btn btn-primary">
                            {{$t('bs-save')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="listDepartAgentModal" tabindex="-1" aria-labelledby="listDepartAgentModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                        <h5 class="modal-title" id="listDepartAgentModalLabel">{{$t('bs-choose-the')}} {{$t('bs-department')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="viewCancel">
                            X
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">{{$t('bs-department')}}</label>
                                    <multiselect
                                    v-model="changeDepartment2"
                                    deselect-label=""
                                    selectLabel=""
                                    track-by="name"
                                    label="name"
                                    :placeholder="phSelectDepart"
                                    :options="listDepartment"
                                    :searchable="false"
                                    :allow-empty="false"
                                    id="departments"></multiselect>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" @click="viewCancel" class="text-capitalize btn" data-dismiss="modal">
                            {{$t('bs-cancel')}}
                        </button>
                        <button type="button" @click="openAction(4)" :data-dismiss="checkDepartment" id="btn-department" class="btn btn-primary">
                            {{$t('bs-next')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="listDepartAgentModalPart2" tabindex="-1" aria-labelledby="exampleModalPart2Label" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                        <h5 class="modal-title" id="exampleModalLabel">{{$t("bs-change")}} {{$t('bs-department')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="viewCancel">
                            X
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">{{$t('bs-agents')}}</label>
                                    <multiselect
                                    v-model="changeAgents"
                                    deselect-label=""
                                    selectLabel=""
                                    track-by="name"
                                    label="name"
                                    :placeholder="phSelectDepart"
                                    :options="listAgents"
                                    :searchable="false"
                                    :allow-empty="false"
                                    id="departments"></multiselect>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" @click="viewCancel" class="text-capitalize btn" data-dismiss="modal">
                            {{$t('bs-cancel')}}
                        </button>
                        <button type="button" @click="openAction(5)" :data-dismiss="checkDepartment" id="btn-department" class="btn btn-primary">
                            {{$t('bs-save')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <!-- CALL TICKET ANSWER -->
        <template v-if="viewAswer" class="mobile">
            <ticket-answer
                :settings="settings"
                :user="user"
                :is_admin="is_admin"
                :restriction="restriction"
                :itemselected="itemselected"
                v-on:back="OpenAswer"
                v-on:ticket_ticket="ticket_ticket"
                :openClientHistory="openClientHistory"
                :chat_history="chat_history"
                :chat_history_before="chat_history_before"
            ></ticket-answer>
        </template>
    </div>
</template>

<script>
export default {
    data() {
        return {
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
                    type: "" ,
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
            changeDepartment2: "",
            changeAgents: "",
            tz: '',
            phSelectDepart: this.$t('bs-select-a-department'),
            checkDepartment: '',
            settingcolor: [],
            color: [],
            positionSelected: 0,
            showMenuMobile: false,
            showdata: true,
            statusblock: true,
            textReason: '',
        };
    },
    props: {
        user: Object,
        itemselected: Object,
        restriction: Array,
        is_admin: Number,
        isMobile: {
            type: Boolean,
            default: false
        },
        openClientHistory: Function
    },
    created(){
        this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
        //console.log(this.restriction[0]);
        if(this.restriction[0].ticket_close == 1 && this.restriction[0].ticket_resolved == 1 || this.restriction[0].ticket_admin == 1){
            this.listStatus = [
                { text: this.$t("bs-in-progress"), value: "IN_PROGRESS" },
                { text: this.$t("bs-closed"), value: "CLOSED" },
                { text: this.$t("bs-finalized"), value: "RESOLVED" },
            ];
        }else if(this.restriction[0].ticket_close == 1){
            this.listStatus = [
                { text: this.$t("bs-in-progress"), value: "IN_PROGRESS" },
                { text: this.$t("bs-closed"), value: "CLOSED" },
            ]
        }else if(this.restriction[0].ticket_resolved == 1){
            if(this.restriction[0].ticket_close == 0){
                this.listStatus = [
                    { text: this.$t("bs-in-progress"), value: "IN_PROGRESS" },
                    { text: this.$t("bs-finalized"), value: "RESOLVED" },
                ]
            }else{
                this.listStatus = [
                    { text: this.$t("bs-in-progress"), value: "IN_PROGRESS" },
                    { text: this.$t("bs-finalized"), value: "RESOLVED" },
                ]
            }
        }

        if(localStorage.getItem("menuticket") == null){
            localStorage.setItem("menuticket", true+','+false+','+false);
            this.cb_message = true;
            this.cb_eventos = false;
            this.cb_logs = false;
        }else{
            var array = localStorage.getItem("menuticket").split(',');
            this.cb_message = (array[0] === 'true');
            this.cb_eventos = (array[1] === 'true');
            this.cb_logs = (array[2] === 'true');
        }

    },
    mounted() {
        //this.OpenAswer();
        var vm = this;
        vm.showDetails = true;
        vm.showAnexo = false;
        vm.showAction = false;
        vm.settings = JSON.parse(vm.itemselected.settings);

        axios.get("tickets/get-tickets-chat/", {
            params:{
                id: vm.itemselected.id,
                chat_id: vm.itemselected.chat_id,
                chat_type: vm.itemselected.chat_type,
            }
        }).then(function (response) {

            //depois
            vm.chat_history = response.data.result2;
            vm.chat_history = vm.chat_history.reverse()


            //console.log(vm.chat_history);
            if(vm.chat_history.length != 0){
               vm.beforeAfter = true;
           }
            //antes
            vm.chat_history_before = response.data.result;

            vm.chat_history_before.unshift({
                type: 'TEXT',
                content: response.data.description.description,
                name: vm.$t("bs-description"),
                created_at: response.data.description.created_at,
                created_by: -1
            });

            for (var i = response.data.quests.length - 1; i >= 0; i--) {

                //console.log(response.data.quests[i].question);
                //console.log(response.data.quests[i].answer);
                vm.chat_history_before.unshift({
                    type: 'TEXT',
                    content: response.data.quests[i].answer,
                    name: vm.itemselected.name_created,
                    created_at: response.data.quests[i].created_at,
                    created_by: -1
                });

                vm.chat_history_before.unshift({
                    type: 'TEXT',
                    content: response.data.quests[i].question,
                    name: vm.$t(vm.itemselected.department),
                    created_at: response.data.quests[i].created_at,
                    created_by: vm.user.id
                });
            }

            vm.isChat = response.data.isChat

            vm.addRealtimeChat_history();

            //CARREGAR O ULTIMO TEXT NO TICKET
            var aux = null;
            for (var i = 0; i < vm.chat_history.length; i++) {
                if(vm.chat_history[i].type == 'TEXT'){
                    //console.log('ENTROU');
                    aux = i;
                    break;
                }
            }
            if(aux == null){
                vm.clickMessage(0);
            }else{
                vm.clickMessage(aux);
            }
            //CARREGAR O ULTIMO TEXT NO TICKET

        })
        .catch(function (error) {
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
        })
        .catch(function (error) {
            console.log(error);
        });
        axios.get('check-block-ticket', {
            params: {
                id: vm.itemselected.id_created,
            }
        }).then(function(r_resposta){
            vm.statusblock = r_resposta.data.result;
            vm.textReason = r_resposta.data.reason;
        }).catch(function (error) {
            console.log(error);
        });
    },
    methods: {
        parseJson(item){
            item = JSON.parse(item)
            return item;
        },
        addRealtimeChat_history(){
            //console.log(this.itemselected)
            Echo.leave(`ticket.${this.itemselected.chat_id}`);

            Echo.join(`ticket.${this.itemselected.chat_id}`).listen("MessageSentTicket", (event) => {
                //console.log(event);
                if( !['TEXT', 'EVENT'].includes(event.message.type) ){
                    event.message.content = JSON.parse(event.message.content)
                }
                // console.log(this.chat_history);
                this.showdata = false
                this.chat_history.unshift(event.message);
                setTimeout(() => {
                    this.showdata = true;
                }, 4);
            });
        },
        getFile(message) {
            if(message.chat_id){
                return `ticket/files/${message.chat_id}/${message.content.unique_name}`;
            }else{
                return `ticket/files/${message.id}/${message.content.unique_name}`;
            }
        },
        getFile2(message, id) {
            //console.log(message)
            //console.log(id)
            return `ticket/files/${id}/${message.unique_name}`;
        },
        showIB(value) {
            var vm = this;
            vm.ss.ss1 = "tab";
            vm.ss.ss2 = "tab";
            vm.ss.ss3 = "tab";
            vm.showDetails = false;
            vm.showAnexo = false;
            vm.showAction = false;

            if (value == 1) {
                vm.showDetails = true;
                vm.ss.ss1 = "tab active";
            } else if (value == 2) {
                axios
                .get("tickets/get-files-ticket/" + vm.itemselected.id)
                .then(function (r_resposta) {
                    // console.log(r_resposta.data.value);
                    if (r_resposta.data.success) {
                        vm.arquivos = r_resposta.data.value;
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
                vm.showAnexo = true;
                vm.ss.ss2 = "tab active";
            } else if (value == 3) {
                vm.showAction = true;
                vm.ss.ss3 = "tab active";
            }
        },
        back() {
            var vm = this;
            vm.$emit("back");
        },
        OpenAswer() {
            var vm = this;
            vm.showBody = !vm.showBody;
            vm.viewAswer = !vm.viewAswer;
        },
        ticket_ticket(status){
            var vm = this;

            if(vm.itemselected.status == 'RESOLVED' || vm.itemselected.status == 'CLOSED'){
                vm.$emit('ticket_ticket');
            }else{
                vm.OpenAswer();
            }
        },
        LI(value) {
            if (value == null) {
                return "LI";
            }
            return value.substr(0, 2);
        },
        showMessage() {
            localStorage.setItem("menuticket", this.cb_message+','+this.cb_eventos+','+this.cb_logs);
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
                    chat_id: vm.itemselected.chat_id,
                })
                .then(function (response) {
                    //ALTERAR DEPARTAMENTO
                    if (response.data.success) {
                        document.location.reload(true);
                        vm.$snotify.success(
                            vm.$t("bs-update-saved-successfully"),
                            vm.$t("bs-success")
                        );
                    } else {
                        if (response.data.value == "not_agent") {
                            vm.$snotify.info(
                                vm.$t("bs-he-doesnt-work-in-that-department"),
                                vm.$t("bs-info")
                            );
                        } else {
                            vm.$snotify.error(
                                vm.$t("bs-error-updating"),
                                vm.$t("bs-error")
                            );
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
                    chat_id: vm.itemselected.chat_id,
                })
                .then(function (response) {
                    //console.log(response.data.created);
                    if (response.data.success) {
                        document.location.reload(true);
                        vm.$snotify.success(
                            vm.$t("bs-update-saved-successfully"),
                            vm.$t("bs-success")
                        );
                    } else {
                        vm.$snotify.error(vm.$t("bs-error-updating"), vm.$t("bs-error"));
                    }
                })
                .catch(function () {
                    console.log("FAILURE!!");
                });
            }

            //Status
            if (value == 3) {
                if(vm.changeStatus == ""){
                    return vm.$snotify.info(vm.$t("bs-empty-field"), vm.$t("bs-info"));
                }

                axios
                .post("update-ticket", {
                    type: 3,
                    status: vm.changeStatus,
                    ticket: vm.itemselected.id,
                    department: vm.itemselected.department_id,
                    chat_id: vm.itemselected.chat_id,
                }).then(function (response) {
                    //console.log(response.data.created);
                    if (response.data.success) {
                        document.location.reload(true);
                        vm.$snotify.success(
                            vm.$t("bs-update-saved-successfully"),
                            vm.$t("bs-success")
                        );
                    } else {
                        vm.$snotify.error(vm.$t("bs-error-updating"), vm.$t("bs-error"));
                    }
                })
                .catch(function () {
                    console.log("FAILURE!!");
                });
            }
        },
        openAction(value){
            var vm = this;

            if (value == 1) {
                //SELECIONA AGENTE DO MESMO DEPARTAMENTO
                axios
                .get("tickets/get-agents", {
                    params:{
                        department_id: vm.itemselected.department_id,
                    }
                })
                .then(function (r_resposta) {
                    //console.log(r_resposta.data);
                    vm.listAgents = r_resposta.data.result;
                    $("#listAttendatsModal").modal("show");
                })
                .catch(function (error) {
                    console.log(error);
                });
            }

            if (value == 2) {

                //REMOVER A OPÇÃO DE TRANSFERIR PARA O MESMO DEPARTAMENTO
                let index = vm.listDepartment.findIndex(
                  (item) => item.id === vm.itemselected.department_id
                );
                if (index !== -1) {
                  vm.listDepartment.splice(index, 1);
                }

                //TRANFERIR DEPARTAMENTO
                $("#listDepartmentModal").modal("show");
            }

            if (value == 3) {
                $("#listDepartAgentModal").modal("show");
            }

            if (value == 4) {
                axios
                .get("tickets/get-agents", {
                    params:{
                        department_id: vm.changeDepartment2.id,
                    }
                })
                .then(function (r_resposta) {
                    //console.log(r_resposta.data);
                    vm.listAgents = r_resposta.data.result;
                    $("#listDepartAgentModalPart2").modal("show");
                })
                .catch(function (error) {
                    console.log(error);
                });
                $("#listDepartAgentModal").modal("hide");
                $("#listDepartAgentModalPart2").modal("show");
            }

            if (value == 5) {
                alert('salvou');
            }


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
        hourMessage(hora){
            return moment(hora).format("HH:mm:ss");
        },
        viewCancel(){
            $("#listDepartmentModal").modal("hide");
        },
        nextstep(value){
            var vm = this;
            if(value == 1){
                $("#listDepartmentModal").modal("hide");
            }
            if(value == 2){
                $("#selectAgentModal").modal("hide");
                $("#selectClientModal").modal("show");
            }
            if(value == 3){
            }
            if(value == 4){

                axios.post('create-ticket', {
                }).then(function(response){
                })
                .catch(function(erro){
                    console.log(erro);
                    console.log('FAILURE!!');
                });
            }
        },
        clickMessage(index){

            if(index == -1 || this.chat_history[index].type == 'EVENT'){
                this.settingcolor = [];
                this.settingcolor[-1] = "#2273FE";
                this.color = [];
                this.color[-1] = "#000000";
                this.beforeAfter = false;
                return;
            }else{
                this.beforeAfter = true;
                this.positionSelected = index;
                this.settingcolor = [];
                this.settingcolor[index] = "#2273FE";
                this.color = [];
                this.color[index] = "#000000";
            }


            //this.$set(this.settingcolor, index, "#2273FE");

        },
        goBackToMenu() {
            if (this.isMobile) {
                this.showMenuMobile = true;
            }
        },
        goToContent() {
            if (this.isMobile) {
                this.showMenuMobile = false;
            }
        },
        translate: function (value) {
            if (!value) return ''
            else if(/^bs-/.test(value)) return  this.$t(value)
            return value
        },
        showModalBLock(){
            $("#showModalReason").modal("show");
        },
        blockclient(item, status){
            console.log(item)
            var vm = this;
            axios.post('block-client-ticket', {
                id: item.id_created,
                textReason: vm.textReason,
                status: status,
            }).then(function(response){

                if(status){
                    vm.$snotify.success(vm.$t('bs-client-blocked-successfully'), vm.$t('bs-success'), {
                        timeout: 2000,
                        showProgressBar: false,
                        pauseOnHover: true
                    });
                }else{
                    vm.$snotify.success(vm.$t('bs-customer-successfully-released'), vm.$t('bs-success'), {
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
    },
    watch: {
        isMobile: function(newVal, oldVal){
            if(newVal) {
                this.goToContent();
            }
        }
    }
};
</script>

<style scoped lang="scss">
h1 {
    font: normal normal 800 25px/19px Muli;
    letter-spacing: 0px;
    color: #0080fc;
    opacity: 1;
}

.text {
    font: normal normal bold 14px/24px Muli;
    color: #333333;
}

.ta-l {
    text-align: left;
}

.ta-r {
    text-align: right;
}

.h-80 {
    height: 80%;
}
.h-90 {
    height: 90%;
}

.card-custom {
    padding: 10px;
    margin-bottom: 8px;
    font: normal normal bold 14px/31px Muli;
    letter-spacing: 0px;
    color: #707070;
}

.card {
    box-shadow: 0px 0px 9px #26242424;
    border-radius: 5px;
    opacity: 1;
    border: none;
}

.br5 {
    border-radius: 5px;
}

#card-tree .card-body {
    padding: 0px;
}

ul,

.myUL {
    list-style-type: none;
    font: normal normal 600 14px/18px Muli;
    letter-spacing: 0px;
    color: #7c94b4;
    opacity: 1;
}

.caret {
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.caret::before {
    color: #a5b9d5;
}

.nested {
    display: none;
}

.active {
    display: block;
}

ul,
li {
    border-radius: 0px;
    border: none;
}

.level-1 {
    margin-left: 19px;
}

.level-2 {
    margin-left: 44px;
}

.level-3 {
    margin-left: 69px;
}

#list .card-body {
    padding: 0px;
}

#list {
    overflow: hidden;
}

table {
    height: 100%;
    display: -moz-groupbox;
    text-align: center;
}

tbody {
    overflow-y: scroll;
    height: 100%;
    position: absolute;
}

thead {
    background: #f7f8fc;
    border: none;
    font: normal normal bold 16px/30px Muli;
    letter-spacing: 0px;
    color: #333333;
    opacity: 1;
}

tr {
    width: 100%;
    display: inline-table;
    table-layout: fixed;
    color: #6e6e6e;
}
tr:hover {
    background-color: #0294ff;
    color: white;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: #fdfdfd;
}

.brlt {
    border-radius: 5px 0px 0px 0px;
}

.brrt {
    border-radius: 0px 5px 0px 0px;
}

.td-title {
    font: normal normal 800 15px/16px Muli;
    letter-spacing: 0.45px;
    color: #333333;
    opacity: 1;
    text-align: left;
}

td {
    border: 1px solid #dee3ea;
    border-top: none;
    height: 63px;
    vertical-align: middle;
    font: normal normal 600 16px/19px Lato;
    letter-spacing: 0px;
    opacity: 1;
}

table button {
    background: #f4f4f4 0% 0% no-repeat padding-box;
    border-radius: 5px;
    opacity: 1;
    border: none;
    font: normal normal 800 12px/16px Muli;
    letter-spacing: 0.42px;
    color: #434343;
    text-transform: uppercase;
    opacity: 1;
    width: 80px;
    padding: 10px;
}

/* SCROLL */

::-webkit-scrollbar {
    width: 5px;
}

::-webkit-scrollbar-track {
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #0294ff33;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #0294ff33;
}

#info .card-body {
    padding-left: 0px;
    padding-right: 0px;
}

#info .list-group-item {
    border: none;
    font: normal normal normal 15px/24px Muli;
    letter-spacing: 0px;
    color: #656872;
    opacity: 1;
    padding: 0px;
    margin-left: 20px;
    margin-right: 20px;
}

#info img {
    width: 15px;
    margin-right: 5px;
}

.card-title {
    font: normal normal 600 16px/24px Muli;
    letter-spacing: 0px;
    color: #434343;
    opacity: 1;
    margin-left: 20px;
    margin-right: 20px;
}

.h-60 {
    height: 60% !important;
    min-height: 60% !important;
    max-height: 60% !important;
}

.h-40 {
    height: calc(40% - 20px) !important;
    max-height: calc(40% - 20px) !important;
    min-height: calc(40% - 20px) !important;
}

.translator {
    background: #f7f8fc 0% 0% no-repeat padding-box;
    opacity: 1;
    border: none;
    font: normal normal bold 16px/30px Muli;
    letter-spacing: 0px;
    color: #333333;
    text-transform: capitalize;
    opacity: 1;
}

#language {
    background-color: #f2f2f2;
    opacity: 1;
    font: normal normal normal 15px/18px Lato;
    letter-spacing: 0px;
    color: #434343;
    opacity: 1;
    border: none;
}

#btn-discard {
    font: normal normal 800 13px/16px Muli;
    letter-spacing: 0.39px;
    color: #5a5959;
    opacity: 1;
    background: transparent;
    border: none;
    text-transform: capitalize;
}

#btn-translate {
    background: #0080fc 0% 0% no-repeat padding-box;
    box-shadow: 0.62px 0.79px 2px #1e120d1a;
    border-radius: 5px;
    opacity: 1;
    font: normal normal 800 14px/16px Muli;
    letter-spacing: 0.42px;
    color: #f8fafd;
    text-transform: uppercase;
    border: none;
}

.msg {
    border: none;
    border-radius: 0px;
    border-bottom: 1px solid #d7dee6;
    padding-right: 0px;
}

.msg-hour {
    text-align: right;
    max-width: 80px;
    min-width: 80px;
    font: normal normal bold 16px/35px Muli;
    letter-spacing: 0px;
    color: #6e6e6e;
    opacity: 1;
}

.content {
    text-align: justify;
    font: normal normal bold 14px/18px Muli;
    letter-spacing: 0px;
    color: #707070;
    opacity: 1;
}

.msg-event h2 {
    text-align: center;
    font: normal normal 800 15px/31px Muli;
    letter-spacing: 0px;
    color: #707070;
    opacity: 1;
}

textarea {
    font: normal normal bold 14px/18px Muli;
    letter-spacing: 0px;
    color: #707070;
    opacity: 1;
    resize: none;
}

#btn-send {
    position: absolute;
    right: 0;
    bottom: 0;
    padding: 0px;
    margin: 0px;
    border: none;
}

.bs-m-spacing {
    padding-left: 1px;
    padding-top: 6px;
    padding-right: 15px;
    text-transform: uppercase;
    margin-left: 1px;
    font-size: 16px;
}

.tab {
    color: #a4a4a4;
    border-bottom: 1px solid #bdbdbd;
    font-weight: bold;
    text-decoration: none;
    margin-bottom: 2px;
}

.active,
.tab:hover {
    color: #0080fc;
    padding-left: 4px;
    border-bottom: 3px solid #0080fc;
}

@media screen and (max-width: 576px) {
    .bs-m-spacing {
        justify-content: center;
        text-align: center;
        padding: 8px;
        margin-left: 1px;
    }

    .active,
    .tab:hover {
        color: #0080fc;
        padding-left: 10px;
        /* border: 3px solid #0080fc; */
    }
}
.custom-select{
    min-width: 150px!important;
}

.wrapper{
    min-height: 0;  /* NEW */
    min-width: 0;   /* NEW; needed for Firefox */

    display: grid;
    grid-template-rows: 37px auto;
    grid-template-columns: 1fr;
    justify-content: stretch;
    justify-items: stretch;
    align-content: stretch;
    align-items: stretch;

    .wrapper-tabs{
        div{
            display: inline-block;
        }
    }
    .wrapper-tab-details{
        min-height: 0;  /* NEW */
        min-width: 0;   /* NEW; needed for Firefox */

        display: grid;
        grid-template-rows: 110px auto;
        grid-template-columns: 1fr;
        justify-content: stretch;
        justify-items: stretch;
        align-content: stretch;
        align-items: stretch;

        row-gap: 15px;
        &>div{
            overflow: hidden; // necessario para word-wrap
            .card{
                overflow-y: auto!important;
                word-wrap: break-word;
                padding: 4px 20px;
            }
        }
    }
    .wrapper-tab-attachments,
    .wrapper-tab-actions{
        overflow-y: auto!important;
    }


}

@media screen and (max-width: 1024px) {
    .bs-m-spacing {
        justify-content: center;
        text-align: center;
        padding: 8px;
        margin-left: 1px;
    }

    .active,
    .tab:hover {
        color: #0080fc;
        padding-left: 10px;
        border-bottom: 3px solid #0080fc;
    }
}

/* @media screen and (max-width: 1366px) and (min-width: 576px) {
    .ml-custom {
        margin-left: 8px;
    }
} */

@media screen and (max-width: 1199px) {
    .ml-custom {
        margin-left: 8px;
    }
    .mobile{
        margin-left: 0!important;
        margin-right: 0!important;
        padding-left: 0!important;
        padding-right: 0!important;
        box-sizing: border-box;
        .col,
        .container-fluid,
        .wrapper{
            margin-left: 0!important;
            margin-right: 0!important;
            padding-left: 0!important;
            padding-right: 0!important;
        }
    }
    .buttons-wrapper{
        &>div{
            display: grid;
            grid-template-rows: 1fr;
            grid-template-columns: 1fr 1fr;
            justify-content: stretch;
            justify-items: stretch;
            align-content: stretch;
            align-items: stretch;
            column-gap: 15px;

            margin-left: 0!important;
            margin-right: 0!important;
            padding-left: 0!important;
            padding-right: 0!important;
        }
    }
}



.caret {
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

h5 {
    font: normal normal bold 16px/22px Muli;
    letter-spacing: 0px;
    color: #434343;
}

.content {
    margin-right: 40px;
    margin-left: 40px;
}

/* SCROLL */

::-webkit-scrollbar {
    width: 5px;
    height: 5px;
}

::-webkit-scrollbar-track {
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #0080fc;
    border-radius: 10px;
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

.example-open .modal-backdrop {
    background-color: red;
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

.btn-back {
  font: normal normal 800 15px/31px Muli;
  letter-spacing: 0px;
  color: #0080fc;
  margin-left: 12px;
  i {
    font-size: 20px !important;
  }
  &:hover {
    cursor: pointer;
  }
}
</style>
