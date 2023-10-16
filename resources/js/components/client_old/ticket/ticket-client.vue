<template>
    <div class="h-100 ticket-client" :class="{ content: !showChat, 'margin-0': showChat }">
        <template v-if="!showChat">
            <!-- <beta-alert :showChat="showChat"></beta-alert> -->
            <b-row class="mt-5">
                <b-col sm="12" lg="">
                    <h1>{{ $t("bs-ticket") }}</h1>
                    <span class="personTextArea" v-if="showDescription">
                        {{ $t('bs-description') }}: {{ description | str_limit(120) }}
                    </span>
                    <span v-else>
                        <h2 class="mt-1 mb-3">{{ $t("bs-actives") }}</h2>
                    </span>
                </b-col>
            </b-row>
            <div class="table-responsive table-borderless table-striped table-hover responsivetable">
                <!-- class - tableFixHead -->
                <table class="table text-nowrap text-center responsivetable">
                    <thead>
                        <tr>
                            <!-- <th scope="col">#</th> -->
                            <th scope="col">#</th>
                            <th scope="col">{{ $t("bs-waiting-time") }}</th>
                            <!-- <th scope="col">Ultima interação</th> -->
                            <th scope="col">{{ $t("bs-status") }}</th>
                            <!-- <th scope="col">{{$t('bs-doubt')}}</th> -->
                            <th scope="col">{{ $t("bs-attendants") }}</th>
                            <th scope="col">{{ $t("bs-created-on") }}</th>
                            <th scope="col">{{ $t("bs-department") }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, index) in resultQuery" :key="index" @click="openTicket(row, index)"
                            @mouseover="mouseOver(row.description, true)"
                            @mouseleave="mouseOver(row.description, false)">
                            <td v-bind:class="{
                                answered: row.answered && row.status === 'IN_PROGRESS',
                            }">
                                #{{ row.id }}
                            </td>
                            <td v-if="row.status === 'OPENED'" v-bind:class="{
                                answered: row.answered && row.status === 'IN_PROGRESS',
                            }" :id="'time-elapsed-' + index">
                                {{
                                        calculateWaitingTime(
                                            UTCtoClientTZ(row.created_at, tz),
                                            "time-elapsed-" + index
                                        )
                                }}
                                {{ w_time }}
                            </td>
                            <td v-else v-bind:class="{
                                answered: row.answered && row.status === 'IN_PROGRESS',
                            }">
                                ---
                            </td>
                            <!-- <td>{{ row.last_update_status | lastInteraction }}</td> -->
                            <td v-bind:class="{
                                answered: row.answered && row.status === 'IN_PROGRESS',
                            }">
                                <span :class="colorStatus(row.status, row.answered)">
                                    <span v-if="row.status === 'IN_PROGRESS' && row.answered">
                                        {{ statusTicket(row.status) }} / {{ $t('bs-answered') }}
                                    </span>
                                    <span v-else-if="row.status === 'CLOSED'">
                                        {{ $t('bs-answered') }} / {{ $t('bs-solution-sent') }}
                                    </span>
                                    <span v-else>
                                        {{ statusTicket(row.status) }}
                                    </span>
                                </span>
                            </td>
                            <!-- <td>{{ row.description }}</td> -->
                            <td v-bind:class="{
                                answered: row.answered && row.status === 'IN_PROGRESS',
                            }" v-if="row.agent == null">
                                <span class="op-10"> -- </span>
                            </td>
                            <td v-bind:class="{
                                answered: row.answered && row.status === 'IN_PROGRESS',
                            }" v-else>
                                <span class="op-10">{{ row.agent }}</span>
                            </td>
                            <td v-bind:class="{
                                answered: row.answered && row.status === 'IN_PROGRESS',
                            }">
                                {{ UTCtoClientTZ2(row.created_at, tz) }}
                            </td>
                            <td v-bind:class="{
                                answered: row.answered && row.status === 'IN_PROGRESS',
                            }">
                                <span v-if="row.department_type == 'builderall-mentor'">
                                    <img src="/images/icons/icon_vip.svg" height="20" alt=""> {{ $t(row.department) }}
                                </span>
                                <span v-else>
                                    {{ $t(row.department) }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!--   @click="loadDepartmentsByTimezone" -->
            <!-- <a
        
        data-toggle="modal"
        data-target="#exampleModal"
        class="float"
      >
        <vue-material-icon name="add" :size="60" />ba
      </a> -->

            <span data-toggle="modal" data-target="#exampleModal" class="float">
                <span class="floatIcon">
                    <vue-material-icon name="add" :size="25" />
                </span>
                <span class="">{{ $t('bs-create-ticket') }}</span>
            </span>

        </template>
        <div v-show="showChat" class="card h-100">
            <!-- <beta-alert :showChat="showChat"></beta-alert> -->
            <div class="card-header pr-4">
                <b-row>
                    <b-col class="mw-50">
                        <!-- <img src="images/li.png" height="40" width="40" /> -->
                        <!-- <b-avatar
              variant="light mw-50 opacity-0 c-white"
              :text="LI(itemselected.agent)"
              size="40px"
            ></b-avatar> -->
                    </b-col>
                    <b-col class="col-name">
                        <div class="list-group p-0 m-0">
                            <a
                                class="list-group-item list-group-item-action flex-column align-items-start border-0 p-0 m-0">
                                <div class="d-flex justify-content-between">
                                    <template v-if="itemselected.agent == null">
                                        <h5 class="mb-1 ellipsis">{{ $t("bs-awaiting") }}...</h5>
                                    </template>
                                    <template v-else>
                                        <h5 class="mb-1 ellipsis">{{ itemselected.agent }}</h5>
                                    </template>
                                </div>
                                <p class="mb-1 mt-n1 ellipsis">
                                    <span v-if="ticket_info.department_type == 'builderall-mentor'">
                                        {{ $t(ticket_info.department_name) }} <img src="/images/icons/icon_vip.svg"
                                            width="40px" alt="">
                                    </span>
                                    <span v-else>
                                        {{ $t(ticket_info.department_name) }}
                                    </span>
                                </p>
                            </a>
                        </div>
                    </b-col>
                    <b-col class="mw-80 mt-2 buttons">
                        <span @click="back" class="level-3 care cursor-pointer">
                            <i class="fa fa-arrow-left mt-1" aria-hidden="true"></i>
                            {{ $t("bs-back") }}
                        </span>
                    </b-col>
                </b-row>
            </div>
            <div @click="actions(false)" class="card-body content-chat" v-chat-scroll>
                <component v-for="(message, index) in chat_history" :key="index" :is="setMessageComponent(message)"
                    v-bind="setMessageProps(message, index)" />
            </div>

            <!-- CAMPO PARA DESCREVER O TICKET -->
            <div class="card-footer footer-1">
                <span class="op-10" v-if="
                itemselected.status == 'IN_PROGRESS' ||
                itemselected.status == 'OPENED' ||
                itemselected.status == 'CLOSED' && configReopen">

                    <!--           <div class="content-input">
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
            <div class="row row-textarea">
              <div class="col">
                <textarea
                  id="input"
                  v-model="chat.clear"
                  @keyup.enter="sendMessage"
                  autofocus
                  class="textarea js-autoresize"
                ></textarea>
              </div>
              <div class="col col-input-btn">
                <a @click="sendMessage">
                  <img src="images/icons/send.svg" height="15" width="15" />
                </a>
              </div>
            </div>
          </div> -->
                    <!-- //PAREIAQUI -->
                    <div class="row row-textarea">
                        <button type="button" class="btn-block btncreate" @click="showAnwerTicket = true">
                            + {{ $t('bs-click-here-to-add-a-message') }}
                            <template v-if="itemselected.status == 'CLOSED'">{{ $t('bs-and') }} {{ $t('bs-reopen') }}
                                {{ $t('bs-ticket') }}</template>
                        </button>
                    </div>
                </span>
            </div>
            <div class="card-footer footer-2 pr-3 pl-3">
                <span class="care op-8" @click="back">
                    <b>#{{ ticket_info.id_ticket }}</b>
                </span>
                <span v-show="showActions">
                    <b-list-group flush>
                        <span class="op-10" v-show="itemselected.agent != null">
                            <b-list-group-item>
                                <b-button class="w-25" @click="setStatusTicket(0)" variant="danger">{{ $t("bs-close") }}
                                    {{ $t('bs-ticket') }}</b-button>
                            </b-list-group-item>
                            <b-list-group-item>
                                <b-button class="w-25" @click="setStatusTicket(1)" variant="success">{{
                                        $t("bs-resolved")
                                }}
                                    {{ $t('bs-ticket') }}</b-button>
                            </b-list-group-item>
                        </span>
                        <b-list-group-item>
                            <span class="op-10" v-show="itemselected.agent == null">
                                <b-button class="w-25" @click="setStatusTicket(2)" variant="warning">{{ $t("bs-cancel")
                                }}
                                    {{ $t('bs-ticket') }}</b-button>
                            </span>
                        </b-list-group-item>
                    </b-list-group>
                </span>
                <div class="float-right">
                    <span class="op-10" v-show="typeSendMessage != 'createticket'">
                        <span class="op-10">
                            <!--               <small @click="upload" class="mr-3"
                ><img
                  src="images/icons/upload.svg"
                  v-if="
                    itemselected.status != 'CLOSED' &&
                    itemselected.status != 'RESOLVED' &&
                    itemselected.status != 'CANCELED'
                  "
              /></small> -->
                            <!--               <input
                type="file"
                id="attachments"
                ref="attachments"
                multiple
                v-on:change="handleFilesUpload()"
                style="display: none"
                v-if="
                  itemselected.status != 'CLOSED' &&
                  itemselected.status != 'RESOLVED' &&
                  itemselected.status != 'CANCELED'
                "
              /> -->

                            <!-- <small v-for="(option, index) in sidebarOptions" :key="index" class="mr-3" @click.prevent="actionSidebarOptions(option)" v-b-toggle.sidebar-right>
                  <template v-if="option.status == 'EVALUATE-CLOSE'">
                    <i class="fa fa-times" style="font-size: 20px;"  aria-hidden="true"></i>
                    <b class="dspNone">{{ $t("bs-close") }} {{$t("bs-chat")}}</b>
                  </template>
                  <template v-if="option.status == 'EVALUATE'">
                    <i class="fa fa-thumbs-o-up" style="font-size: 20px;" aria-hidden="true"></i>
                    <b class="dspNone">{{ $t("bs-evaluate") }}</b>
                  </template>
                  <template v-if="option.status == 'CANCELED'">
                    <i class="fa fa-times-circle-o bs-trash" style="font-size: 20px;" aria-hidden="true"></i>
                    <b class="dspNone">{{ $t("bs-cancel") }}</b>
                  </template>
              </small> -->

                            <small class="mr-3" @click="showSidebarRight = true" v-b-toggle.sidebar-right v-if="
                                itemselected.status != 'CANCELED'
                            ">
                                <template v-if="itemselected.status == 'IN_PROGRESS'">
                                    <img src="images/icons/more_horiz.svg" />
                                    <b class="dspNone">{{ $t('bs-options') }}</b>
                                </template>
                                <template v-if="itemselected.status == 'CLOSED'">
                                    <!-- <img src="images/icons/more_horiz.svg" />
                  <b class="dspNone">{{$t('bs-options')}}</b> -->

                                    <!-- <small href="#" class="fz-14" @click="setStatusTicket(3)"><i class="fa fa-repeat bs-green" aria-hidden="true"></i> {{$t('bs-reopen')}}</small> -->
                                    <small href="#" class="fz-14" @click="setStatusTicket(1)"><i
                                            class="fa fa-check bs-blue" aria-hidden="true"></i> {{ $t('bs-evaluation')
                                            }}</small>
                                </template>
                                <!-- <img src="images/icons/more_horiz.svg" />
                <b class="dspNone">{{$t('bs-options')}}</b> -->
                            </small>

                            <small class="mr-3" @click="setStatusTicket(2)" v-b-toggle.sidebar-right v-if="
                                itemselected.status != 'CANCELED'
                            ">
                                <template v-if="itemselected.status == 'OPENED'">
                                    <i class="fa fa-times-circle-o bs-trash" style="font-size: 20px;"
                                        aria-hidden="true"></i>
                                    <b class="dspNone">{{ $t("bs-cancel") }}</b>
                                </template>
                            </small>

                        </span>
                    </span>
                </div>
                <div>
                    <b-sidebar v-if="showSidebarRight" class="p-0 bg-transparent" id="sidebar-right"
                        :title="$t('bs-options')" right shadow ref="sidebarright">
                        <b-list-group flush class="sidebar-right">
                            <!-- TICKET COM ATENDENTE -->
                            <span class="op-10" v-show="itemselected.agent != null">
                                <!-- TICKET EM PROGRESSSO PODE FAZER ISSO -->
                                <span class="op-10" v-show="itemselected.status == 'IN_PROGRESS'">
                                    <a href="#" @click="setStatusTicket(1)" v-show="itemselected.status != 'RESOLVED'"
                                        class="list-group-item list-group-item-action">
                                        {{ $t("bs-mark-as-resolved") }}
                                    </a>

                                    <a href="#" @click="setStatusTicket(2)"
                                        class="list-group-item list-group-item-action">
                                        {{ $t("bs-cancel-ticket") }}
                                    </a>
                                </span>

                                <a href="#" @click="openEvaluation()" v-show="
                                    itemselected.status == 'RESOLVED' &&
                                    !(itemselected.stars_atendent || itemselected.stars_service)
                                " class="list-group-item list-group-item-action">
                                    {{ $t("bs-rate-the-ticket") }}
                                </a>
                            </span>
                            <span class="op-10" v-show="itemselected.agent == null">
                                <a href="#" @click="setStatusTicket(2)" class="list-group-item list-group-item-action">
                                    {{ $t("bs-cancel-ticket") }}
                                </a>
                            </span>
                            <span class="op-10" v-show="itemselected.status == 'CLOSED'">
                                <a href="#" @click="setStatusTicket(1)" v-show="itemselected.status != 'RESOLVED'"
                                    class="list-group-item list-group-item-action">
                                    {{ $t("bs-mark-as-resolved") }}
                                </a>

                                <!-- <a
                  href="#"
                  @click="setStatusTicket(3)"
                  class="list-group-item list-group-item-action"
                >
                  {{ $t("bs-reopen-ticket") }}
                </a> -->
                            </span>
                        </b-list-group>
                    </b-sidebar>
                </div>
            </div>
        </div>

        <!-- MODAL DE CRIAR TICKET -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
            data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                        <h5 class="modal-title" id="exampleModalLabel">
                            {{ $t("bs-choose-the") }} {{ $t("bs-department") }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="back">
                            X
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">{{
                                            $t("bs-department")
                                    }}</label>
                                    <!-- <select class="form-control" id="exampleFormControlSelect1">
                            <option>Departamento</option>
                        </select> -->
                                    <multiselect v-model="value" deselect-label="" selectLabel="" track-by="name"
                                        label="name" :placeholder="phSelectDepart" :options="options"
                                        :searchable="false" :allow-empty="false" id="departments">
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
                        <button type="button" @click="back" class="text-capitalize btn" data-dismiss="modal">
                            {{ $t("bs-cancel") }}
                        </button>
                        <button @click="BtCreateTicket" type="button" :data-dismiss="checkDepartment"
                            id="btn-department" class="btn btn-primary">
                            {{ $t("bs-next") }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ---------------------------------------------------------------- -->
        <!-- Modal -->
        <div class="modal fade" id="modalQuestionary" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" data-backdrop="static"
            data-keyboard="false">
            <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalScrollableTitle">
                            {{ $t("bs-department") }}
                            {{ ticket_info ? $t(ticket_info.department_name) : "" }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="back">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <form @submit.prevent="openEmptyChat" id="my-form">
                                        <span v-if="ticket_info.translateMsgOpen == null">
                                            {{ $t(ticket_info.msgOpen) }}
                                        </span>
                                        <span v-else>
                                            <span class="mt-4" v-for="(item, index) in ticket_info.translateMsgOpen"
                                                :key="index">
                                                <label class="bmd-label-floating mt-2"
                                                    style="line-height: 1.5em; word-break: break-all"
                                                    v-if="item.code == user.language.split('_')[1]">
                                                    <span v-if="item.text == ''">
                                                        {{ $t(ticket_info.msgOpen) }}
                                                    </span>
                                                    <span v-else>
                                                        {{ $t(item.text) }}
                                                    </span>
                                                    <span v-show="false">{{ teste = true }}</span>
                                                </label>

                                                <label class="bmd-label-floating mt-2"
                                                    style="line-height: 1.5em; word-break: break-all">
                                                    <span v-if="!teste">
                                                        <span v-if="ticket_info.msgOpen == null">
                                                            {{ $t('bs-welcome-wait-we-will-soon-assist-you') }}
                                                        </span>
                                                        <span v-else>
                                                            {{ ($t(ticket_info.msgOpen)) }}
                                                        </span>
                                                    </span>
                                                </label>
                                            </span>
                                        </span>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group bmd-form-group">
                                                    <label class="bmd-label-floating mt-4" style="line-height: 1.5em">
                                                        <span
                                                            v-if="ticket_info.translateDescription == null || ticket_info.translateDescription == undefined || ticket_info.translateDescription == ''">
                                                            {{ $t('bs-ticket-description') }}
                                                        </span>
                                                        <span v-else>
                                                            <span class="mt-4"
                                                                v-for="(item, index) in ticket_info.translateDescription"
                                                                :key="index">
                                                                <span v-if="item.code == user.language.split('_')[1]">
                                                                    <span v-if="item.text == null || item.text == ''">
                                                                        {{ $t('bs-ticket-description') }}
                                                                    </span>
                                                                    <span v-else>
                                                                        {{ $t(item.text) }}
                                                                    </span>
                                                                    <span v-show="false">{{ teste2 = true }}</span>
                                                                </span>

                                                                <span v-if="!teste2">
                                                                    {{ $t('bs-ticket-description') }}
                                                                </span>

                                                            </span>
                                                        </span>
                                                        <!--  Descreva a funcionalidade que você tem problema / dúvida? -->
                                                        <span class="op-10" style="color: red">*</span></label>

                                                    <quill-editor ref="TicketDescriptionQuillEditor"
                                                        v-model="chat.clear" :options="quillEditorOptions"
                                                        id="q-editor-description" class="bg-white border" />



                                                </div>
                                                <div class="form-group bmd-form-group"
                                                    v-for="(row, index) in questOptions" :key="index">
                                                    <div v-if="row.language == null">
                                                        <label class="bmd-label-floating" style="line-height: 1.5em">
                                                            {{ row.quest }}
                                                            <template v-if="row.mandatory">
                                                                <span class="op-10" style="color: red">*</span>
                                                            </template>
                                                        </label>
                                                        <div class="content-textarea">
                                                            <template v-if="row.mandatory">
                                                                <textarea v-model="answers[index]"
                                                                    :placeholder="phTypeHere" autofocus required
                                                                    class="textarea" rows="3"></textarea>
                                                            </template>
                                                            <template v-else>
                                                                <textarea v-model="answers[index]"
                                                                    :placeholder="phTypeHere" autofocus class="textarea"
                                                                    rows="3"></textarea>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div v-if="row.language == user.language.split('_')[1]">
                                                        <label class="bmd-label-floating" style="line-height: 1.5em">
                                                            {{ row.quest }}
                                                            <template v-if="row.mandatory">
                                                                <span class="op-10" style="color: red">*</span>
                                                            </template>
                                                        </label>
                                                        <div class="content-textarea">
                                                            <template v-if="row.mandatory">
                                                                <textarea v-model="answers[index]"
                                                                    :placeholder="phTypeHere" autofocus required
                                                                    class="textarea" rows="3"></textarea>
                                                            </template>
                                                            <template v-else>
                                                                <textarea v-model="answers[index]"
                                                                    :placeholder="phTypeHere" autofocus class="textarea"
                                                                    rows="3"></textarea>
                                                            </template>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="text-capitalize btn" data-dismiss="modal"
                            @click="backToDepartments()">
                            {{ $t("bs-back") }}
                        </button>
                        <button type="submit" id="btn-quests" class="btn btn-primary" form="my-form">
                            {{ $t("bs-create-new-ticket") }}
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <!-- MODAL DE AVALIAÇÃO -->
        <div class="modal fade" id="avaliationModal" tabindex="-1" aria-labelledby="avaliationModalLabel"
            aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                        <h5 class="modal-title" id="avaliationModalLabel">
                            {{ `${$t("bs-ticket-rating")} ${$t("bs-number")}: ${ticket_info ? '#' +
                                    ticket_info.id_ticket : ""}`
                            }}
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <form @submit.prevent="avaliation()" id="my-form-avaliation">
                                        <div class="row">
                                            <div class="col-md-12">

                                                <template v-if="show_attendant_evaluation">
                                                    <span class="mt-3 mr-2">{{
                                                            $t("bs-please-rate-the-attendant")
                                                    }}</span>
                                                    <chat-ticket-evaluation module="ticket" :type="type_evaluation"
                                                        toEvaluate="attendant" />
                                                </template>

                                                <template v-if="show_service_evaluation">
                                                    <span class="mt-3 mr-2">{{
                                                            $t("bs-please-rate-the-service")
                                                    }}</span>
                                                    <chat-ticket-evaluation module="ticket" :type="type_evaluation"
                                                        toEvaluate="service" />
                                                </template>

                                                <template v-if="show_comment_evaluation">
                                                    <b-row>
                                                        <b-col class="mt-3">


                                                            <span class="mt-5 mr-2">
                                                                {{
                                                                        $t("bs-please-leave-calculation-informing-your")
                                                                }}
                                                            </span>
                                                            <b-form-textarea id="textarea-formatter"
                                                                v-model="evaluationComment" class="mt-3 lighttextarea"
                                                                placeholder="Comment"></b-form-textarea>
                                                            <!-- </span> -->
                                                        </b-col>
                                                    </b-row>
                                                </template>

                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" id="btn-avaliation" class="btn btn-primary" form="my-form-avaliation"
                            :disabled="!evaluateFormValidated">
                            {{ $t("bs-send-evaluation") }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <template v-if="showAnwerTicket">
            <answer-ticket v-on:setfalse="showAnwerTicket = false" v-on:descriptionTicketSend="descriptionTicketSend">
            </answer-ticket>
        </template>

    </div>
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
    data() {
        return {
            msgDescriptionticket: '',
            phSelectDepart: this.$t("bs-select-a-department"),
            //phFile: this.$t("bs-enter-your-text") + "...",
            lbDescrTicket: this.$t("bs-describe-the-ticket-under-the") + ": ",
            phMsg: this.$t("bs-enter-your-text") + ": ",
            showChat: true,
            showActions: false,
            showDescription: false,
            showSidebarRight: false,
            showAnwerTicket: false,
            sidebarOptions: null,
            component: {
                FILE: "message-type-file", // "message-type-text-file"
                IMAGE: "message-type-image",
                EVENT: "message-type-event-client",
                TEXT_SENT: "message-type-text-sent",
                TEXT_RECEIVED: "message-type-text-received",
                TEXT_SENT_RECEIVED_NEW: "message-type-tk-message"
            },
            tickets: [],
            searchQuery: null,
            chat: {
                id: "",
                type: "TEXT",
                content: "",
                clear: "",
            },
            chat_history: [],
            value: null,
            options: [],
            ticket_info: {
                id_department: "",
                department_type: "",
                id_ticket: "",
                company_user_id: "",
                settings: {
                    evaluation: {
                        text_atten_active: "0",
                        text_serv_active: "0",
                        text_comment_active: "0",
                    },
                },
                priority: "",
                description: "",
                msgOpen: "",
            },
            teste: '',
            teste2: '',
            checkDepartment: "",
            typeSendMessage: "",
            styleObject: "",
            itemselected: [],
            questOptions: [],
            questionsConfirm: [],
            company_department: null,
            answers: [],
            questionary: [],
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
            w_time: "",
            tz: "",
            phTypeHere: this.$t('bs-type-here') + '...',
            showDescription: false,
            description: '',
            checkIsdepart: true,
            stars_atendent: 0,
            stars_service: 0,
            evaluationComment: "",
            type_evaluation: 'stars',
            show_attendant_evaluation: Boolean,
            show_service_evaluation: Boolean,
            show_comment_evaluation: Boolean,
            quillEditorOptions: {
                placeholder: this.$t('bs-type-here') + '...',
                modules: {
                    toolbar: [
                        [
                            'bold',
                            'italic',
                            'underline',
                            'strike',
                            'code-block',
                            'link',
                            'image',
                            { 'list': 'bullet' },
                            { 'size': ['small', false, 'large', 'huge'] },
                            { 'color': [] },
                            { 'background': [] },
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
            configReopen: false,
        };
    },
    computed: {
        resultQuery() {
            if (this.searchQuery) {
                return this.tickets.filter((item) => {
                    if (this.searchQuery == item.id) {
                        return this.searchQuery;
                    }
                    return this.searchQuery
                        .toLowerCase()
                        .split(" ")
                        .every((v) => item.description.toLowerCase().includes(v));
                });
            } else {
                return this.tickets;
            }
        },
        showTicketOptions() {
            return (
                this.itemselected.agent == null ||
                this.itemselected.status != "RESOLVED" ||
                ((this.itemselected.status == "CLOSED" ||
                    this.itemselected.status == "RESOLVED") &&
                    !(
                        this.itemselected.stars_atendent || this.itemselected.stars_service
                    ))
            );
        },
        commentFormValidated() {
            if (this.show_comment_evaluation) {
                return this.evaluationComment !== '';
            } else {
                return true;
            }
        },
        attendantFormValidated() {
            if (this.show_attendant_evaluation) {
                return this.stars_atendent > 0
            } else {
                return true;
            }
        },
        serviceFormValidated() {
            if (this.show_service_evaluation) {
                return this.stars_service > 0
            } else {
                return true;
            }
        },
        evaluateFormValidated() {
            return this.commentFormValidated && this.attendantFormValidated && this.serviceFormValidated;
        }
    },
    mounted() {
        const urlParams = new URLSearchParams(window.location.search);
        const myParam = urlParams.get('open-departments');
        if (localStorage.getItem("open_modal") == 1 || myParam) {
            this.loadDepartmentsByTimezone();
            $('#exampleModal').modal('show');
            localStorage.removeItem("open_modal");
        } else {
            this.loadDepartmentsByTimezone();
        }
    },
    created() {
        this.$root.$refs.TicketClient = this;
        Quill.register('modules/blotFormatter', BlotFormatter);
        Quill.register('modules/ImageExtend', ImageExtend)
        Quill.register('modules/imageCompress', ImageCompress);
        Quill.register('modules/autoLinks', AutoLinks);
        this.showChat = false;
        //setResizeListeners(this.$el, ".js-autoresize");
        //$("#avaliationModal").modal("show");
        Echo.join(
            `client-tickets-list.${this.company_id}.${this.user_client_id}`
        ).listen("ClientTicketsList", (event) => {
            switch (event.ticket.action) {
                case "updateStatus":
                    let index = this.tickets.findIndex(
                        (ticket) => ticket.id === event.ticket.id
                    );
                    //console.log("event.ticket.company_user_id:" +event.ticket.company_user_id);
                    this.ticket_info.company_user_id = event.ticket.company_user_id;
                    // console.log(event);
                    // console.log(index);
                    switch (event.ticket.status) {
                        case "IN_PROGRESS":
                            if (index !== -1) {
                                this.tickets[index].status = event.ticket.status;
                                this.tickets[index].agent = event.ticket.agent;
                            }
                            break;
                        case "CANCELED":
                            if (index !== -1) {
                                this.tickets[index].status = event.ticket.status;
                                this.showChat = false;
                            }
                            break;
                        case "RESOLVED":
                            if (index !== -1) {
                                $("#avaliationModal").modal("show");
                                this.ticket_info.id_ticket = this.tickets[index].id;
                                this.tickets[index].status = event.ticket.status;
                                this.showChat = false;
                            }
                            break;
                        case "CLOSED":
                            if (index !== -1) {
                                this.tickets[index].status = event.ticket.status;
                            }
                            break;
                        default:
                            if (index !== -1) {
                                this.tickets[index].status = event.ticket.status;
                            }
                            break;
                    }
                    break;
            }
        });
        Echo.join(`client-tickets-answer.${this.company_id}`).listen(
            "ClientTicketAnswer",
            (event) => {
                switch (event.ticket.action) {
                    case "alertClient":
                        let index = this.tickets.findIndex(
                            (ticket) => ticket.id === event.ticket.id
                        );
                        switch (event.ticket.status) {
                            case "IN_PROGRESS":
                                if (index !== -1) {
                                    //SETAR COR PARA A COLUNA
                                    this.tickets[index].answered = 1;
                                    //alert('ATENDENTE RESPONDEU O TICKET '+ this.tickets[index].id);
                                }
                                break;
                        }
                        break;
                }
            }
        );
        this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
        this.setExtensions();
        this.getClientTickets();
        $("#exampleModal")
            .on("show.bs.modal", function (e) {
                $("body").addClass("example-open");
            })
            .on("hide.bs.modal", function (e) {
                $("body").removeClass("example-open");
            });
        this.joinDeletedChannel();
    },
    methods: {
        joinDeletedChannel() {
            Echo.leave(`chat-ticket-delete.${this.company_id}`);
            Echo.join(`chat-ticket-delete.${this.company_id}`).listen("ChatTicketDelete", (event) => {
                if (event.item.type == 'TICKET') {
                    if (this.showChat && this.itemselected.id == event.item.id) {
                        Swal.fire({
                            title: this.$t('bs-this-ticket-was-deleted'),
                            timer: 3000,
                            timerProgressBar: true,
                            icon: 'info'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.back();
                            }
                            if (result.dismiss === Swal.DismissReason.timer) {
                                this.back();
                            }
                        })
                    }

                    let index = this.tickets.findIndex(
                        (item) => item.id === event.item.id
                    );
                    if (index !== -1) {
                        this.tickets.splice(index, 1);
                    }
                }
            });
        },
        mouseOver(description, boole) {
            this.showDescription = boole;
            this.description = description;
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
        openEvaluation() {
            $("#avaliationModal").modal("show");
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
                            this.$snotify.info(
                                this.$t("bs-archive") +
                                " " +
                                attachment["name"] +
                                ": " +
                                this.$t("bs-bigger") +
                                " " +
                                this.$t("bs-than") +
                                " 5 MB (5242880 B). " +
                                this.$t("bs-cannot-be-sent") +
                                ".",
                                this.$t("bs-info")
                            );
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
                            this.$snotify.info(
                                this.$t("bs-archive") +
                                " " +
                                attachment["name"] +
                                ": " +
                                this.$t("bs-bigger") +
                                " " +
                                this.$t("bs-cannot-be-sent") +
                                ", " +
                                this.$t("bs-as-the-limit-of-5-uploads-has-already-been") +
                                ".",
                                this.$t("bs-info")
                            );
                        }
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
                        icon: "error",
                        title: this.$t("bs-oops"),
                        text:
                            this.$t("bs-invalid-file-s-format") +
                            ": '" +
                            this.errors.join(", ") +
                            this.$t("bs-the-allowed-formats-are") +
                            ": " +
                            this.extensions.join(", ") +
                            ".",
                    });
                }
            });
            this.errors = [];
        },
        openEmptyChat() {
            $("#modalQuestionary").modal("hide");
            this.questionsConfirm = [];
            for (var i = 0; i < this.questOptions.length; i++) {
                if (this.answers[i] != undefined) {
                    var my_object = {
                        id: this.questOptions[i].id,
                        answer: this.answers[i],
                    };
                    this.questionsConfirm.push(my_object);
                    var my_object2 = {
                        type: "TEXT",
                        content: this.questOptions[i].quest,
                        name: this.$t(this.ticket_info.department_name),
                    };
                    this.chat_history.push(my_object2);
                    var my_object3 = {
                        type: "TEXT",
                        content: this.answers[i],
                        name: this.user.name,
                    };
                    this.chat_history.push(my_object3);
                }
            }
            if (this.typeSendMessage == 'addmessage') {
                this.sendMessage();
            } else if (this.typeSendMessage == 'createticket') {
                this.sendMessage2();
            }
            this.ticket_info.description = "";
            this.showDescription = true;
        },
        backToDepartments() {
            this.value = null;
            this.chat_history = [];
            this.chat.clear = "";
            this.$loading(true);
            $("#modalQuestionary").modal("hide");
            $("#exampleModal").modal("show");
            this.$loading(false);
        },
        loadDepartmentsByTimezone() {
            this.options = [];
            axios
                .get("/client-ticket-department?language=" + navigator.language, {
                    params: {
                        country: this.user.language.split("_")[1],
                    },
                }).then((response) => {
                    if (response.data.result.length == 0 && this.checkIsdepart) {
                        this.loadDepartmentsByTimezone();
                        this.checkIsdepart = false;
                    }
                    this.data = response.data.result;
                    this.data.forEach((item) => {
                        //se settings for null, nao aparece o dep no select... eh pq o dep foi criado recentemente e ainda nao foi configurado..
                        if (item.settings !== null) {
                            this.options.push({
                                name: this.$t(item.name),
                                id: item.id,
                                type: item.type,
                                settings: item.settings,
                            });
                            // popula a option com o departamento e status
                        }
                    });
                    if (response.data.dtype) {
                        this.options.forEach((item, index) => {
                            if (item.type == 'builderall-mentor') {
                                this.value = this.options[index];
                            }
                        });
                    }
                });
        },
        openTicket(row, index) {
            this.showSidebarRight = false;
            this.chat = {
                id: "",
                type: "TEXT",
                content: "",
                clear: "",
            };
            this.chat_history = [];
            this.showChat = true;
            this.showDescription = true;
            this.ticket_info.id_department = row.department_id;
            this.ticket_info.department_type = row.department_type;
            this.ticket_info.id_ticket = row.id;
            this.ticket_info.status = row.status;
            this.ticket_info.priority = row.priority;
            this.ticket_info.description = row.description;
            this.ticket_info.department_name = this.$t(row.department);
            this.ticket_info.company_user_id = row.company_user_id;
            this.ticket_info.settings = JSON.parse(row.settings);
            this.ticket_info.msgOpen = this.ticket_info.settings.ticket.msgOpen;
            this.type_evaluation = this.ticket_info.settings.evaluation.typeevaluation == null ? 'stars' : this.ticket_info.settings.evaluation.typeevaluation;
            this.show_attendant_evaluation = this.ticket_info.settings.evaluation.text_atten_active;
            this.show_service_evaluation = this.ticket_info.settings.evaluation.text_serv_active;
            this.show_comment_evaluation = this.ticket_info.settings.evaluation.text_comment_active;
            if (index !== undefined) {
                this.tickets[index].answered = 0;
            }
            this.getChatHistory(row.id, row.chat_id);
            this.itemselected = row;
            this.checkReopenticket();

            this.typeSendMessage = "addmessage";
            Echo.leave(`ticket.${row.chat_id}`);
            Echo.join(`ticket.${row.chat_id}`).listen("MessageSentTicket", (event) => {
                if (!['TEXT', 'EVENT'].includes(event.message.type)) {
                    event.message.content = JSON.parse(event.message.content)
                }
                this.chat_history.push(event.message);
            });
        },
        checkReopenticket(){
            if(this.ticket_info.settings.quant_limity.maxdaysreopenticket == undefined){
                this.ticket_info.settings.quant_limity.maxdaysreopenticket = 0;
            }
            const date1 = new Date(Date.now());
            const date2 = new Date(this.itemselected.update_status_closed_resolved);
            const diffTime = Math.abs(date2 - date1);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
            // console.log(diffTime + " milliseconds");
            // console.log(diffDays + " days");

            if(this.ticket_info.settings.quant_limity.maxdaysreopenticket == 0 || this.ticket_info.settings.quant_limity.maxdaysreopenticket >= diffDays){
                this.configReopen = true;
            }else{
                this.configReopen = false;
            }
        },
        BtCreateTicket() {
            this.showSidebarRight = false;
            this.$loading(true);
            //obriga a selecionar um departamento
            if (this.value == null) {
                this.checkDepartment = "modal";
                this.$snotify.info(
                    this.$t("bs-select-a-department"),
                    this.$t("bs-info")
                );
                this.$loading(false);
                this.back();
                return;
            }
            if (this.tickets.length == 0) { //VERIFICAR SE NÃO TEM TICKET - LIBERA PARA CRIAR MAIS
                this.ticket_info.description = "";
                this.typeSendMessage = "createticket";
            } else { //SE NÃO FAZ UM CONTADOR PARA LIMITAR BASEADO NO LIMITE DE TICKETS POR CLIENTE
                var aux = 0;
                for (var i = 0; i < this.tickets.length; i++) {
                    if (this.tickets[i].status == "OPENED") {
                        aux = aux + 1;
                    }
                    if (this.tickets[i].status == "IN_PROGRESS") {
                        aux = aux + 1;
                    }
                }
                //VERIFICA NA CONFIGURAÇÃO
                if (this.tickets[0].settings_ticket > aux || this.tickets[0].settings_ticket == 0) {
                    this.ticket_info.description = "";
                    this.typeSendMessage = "createticket";
                } else {
                    this.checkDepartment = "modal";
                    $("#exampleModal").modal("hide");
                    //this.$snotify.info(this.$t('bs-maximum-number-of-tickets-created-simultan')+ ', '+this.$t('bs-wait-for-these-to-finish-until-you-can-ope')+'.', this.$t('bs-info'));
                    Swal.fire(
                        this.$t("bs-info"),
                        this.$t("bs-maximum-number-of-tickets-created-simultan") +
                        ", " +
                        this.$t("bs-wait-for-these-to-finish-until-you-can-ope"),
                        "info"
                    );
                    this.$loading(false);
                    return;
                }
            }
            //DEPOIS DE ESTÁ LIBERADO PARA CRIAR TICKET
            axios
                .get("client-get-quests-department/" + this.value.id, {})
                .then((response) => {
                    this.questOptions = response.data.result
                        ? response.data.result
                        : [];
                    // se nao tiver perguntas cadastradas nao traz o modal
                    // if(this.questOptions.length == 0){
                    //     $("#modalQuestionary").modal("hide");
                    //     this.showDescription = true;
                    //     this.$loading(false);
                    // }else{
                    //console.log(this.questOptions);
                    this.$loading(false);
                    $("#modalQuestionary").modal("show");
                    if (this.questOptions.length == 0) {
                        this.showDescription = true;
                    }
                    this.msgDescriptionticket = response.data.questionTicket;
                    // }
                });
            this.$loading(true);
            this.chat_history = [];
            $("#exampleModal").modal("hide");
            this.ticket_info.id_department = this.value.id;
            this.ticket_info.department_name = this.$t(this.value.name);
            this.ticket_info.settings = JSON.parse(this.value.settings);
            this.ticket_info.msgOpen = this.ticket_info.settings.ticket.msgOpen;
            // console.log(JSON.parse(this.value.settings))
            // console.log(this.ticket_info.translateMsgOpen)
            this.ticket_info.translateMsgOpen = this.ticket_info.settings.ticket.arrayTranslate.msgOpen;
            this.ticket_info.translateDescription = this.ticket_info.settings.ticket.arrayTranslate.desriptionTicket;
            this.showDescription = false;
        },
        getClientTickets() {
            let vm = this;
            axios.get("client-get-ticket", {}).then((response) => {
                vm.tickets = response.data.result;
                //console.log(vm.tickets);
            });
        },
        getChatHistory(id_ticket, id_chat) {
            axios.get("client-chat-ticket/" + id_ticket, {
                params: {
                    department_id: this.ticket_info.id_department,
                },
            }).then((response) => {
                this.chat.id = id_chat;
                let _this = this
                this.chat_history = response.data.result.map(function (obj) {
                    obj.department = _this.$t(_this.ticket_info.department_name)
                    return obj
                });

                this.chat_history.unshift({
                    type: "TEXT",
                    content: response.data.ticket.description,
                    client_id: response.data.client_id,
                    name: response.data.client_name,
                    client_email: response.data.client_email,
                    department: "",
                    created_at: response.data.ticket.created_at
                });
                // Questions separadas
                // for (var i = response.data.quests.length - 1; i >= 0; i--) {
                //   //console.log(response.data.quests[i].question);
                //   //console.log(response.data.quests[i].answer);
                //   this.chat_history.unshift({
                //     type: "TEXT",
                //     content: response.data.quests[i].answer,
                //     name: this.user.name,
                //     department: '',
                //     created_at: response.data.quests[i].created_at,
                //   });
                //   this.chat_history.unshift({
                //     type: "TEXT",
                //     content: response.data.quests[i].question,
                //     name: this.ticket_info.department_name,
                //     department: this.ticket_info.department_name,
                //     company_user_company_department_id: -1,
                //     created_at: response.data.quests[i].created_at
                //   });
                // }
                // Questions juntas
                var QuestionAndAnswers = '';
                for (var i = response.data.quests.length - 1; i >= 0; i--) {
                    QuestionAndAnswers += response.data.quests[i].question + "\n" + response.data.quests[i].answer + "\n";
                }
                if (QuestionAndAnswers.trim() !== "") {
                    this.chat_history.unshift({
                        type: "TEXT",
                        content: QuestionAndAnswers,
                        name: this.ticket_info.department_name,
                        department: this.$t(this.ticket_info.department_name),
                        created_at: response.data.ticket.created_at,
                    });
                }
                // Questions juntas
            });
        },
        setMessageComponent(message) {
            if (['TEXT', 'IMAGE', 'FILE', 'ROBOT'].includes(message.type)) {
                /* original
                if (message.company_user_company_department_id) {
                  return this.component["TEXT_RECEIVED"];
                } else {
                  return this.component["TEXT_SENT"];
                } */
                return this.component["TEXT_SENT_RECEIVED_NEW"];
            } else {
                return this.component[message.type];
            }
        },
        setMessageProps(message, index) {
            return {
                message: this.chat_history[index],
                language: this.user.language,
                user: this.user,
                department_name: this.ticket_info.department_name,
                settings: this.ticket_info.settings,
                type: 'TICKET',
            };
        },
        descriptionTicketSend(files, text) {
            //console.log(files);
            //console.log(text);
            this.files = files;
            this.chat.clear = text;
            this.chat.content = text;
            //console.log(this.chat);
            this.showAnwerTicket = false;
            this.sendMessage();
        },
        sendMessage() {
            var vm = this;
            let uploadedFiles = vm.files;
            vm.chat.content = vm.chat.clear;
            // console.log('vm.chat.content: '+ vm.chat.content);
            if (vm.chat.content.trim() !== "") {
                vm.chat.clear = "";
                vm.files = [];
                vm.showActions = false;
                let formData = new FormData();
                if (uploadedFiles.length > 0) {
                    for (var i = 0; i < uploadedFiles.length; i++) {
                        let file = uploadedFiles[i];
                        formData.append("files[" + i + "]", file);
                    }
                    formData.append("type", "FILE");
                } else {
                    formData.append("type", "TEXT");
                }
                formData.append("id", vm.chat.id);
                formData.append("status", vm.ticket_info.status);
                formData.append("content", vm.chat.content);
                formData.append("id_department", vm.ticket_info.id_department);
                formData.append("id_ticket", vm.ticket_info.id_ticket);
                formData.append("company_user_id", vm.ticket_info.company_user_id);
                formData.append("priority", vm.ticket_info.priority);
                formData.append("typeSendMessage", vm.typeSendMessage);
                formData.append("questionsConfirm", vm.questionsConfirm);
                formData.append("extImages", vm.extImages);
                formData.append("is_client", true);
                formData.append("is_ticket", true);
                axios.post(`chat-history-ticket/client/store`, formData, {
                    headers: { "Content-Type": "multipart/form-data" },
                }).then(function (response) {
                    uploadedFiles = [];
                    vm.chat.content = [];
                    console.log(response.data);
                    if (response.data.status == 'OPENED') {
                        vm.itemselected.status = 'OPENED';
                        vm.itemselected.agent = null;
                    } else if (vm.ticket_info.status == 'CLOSED' || vm.itemselected.status == 'CLOSED') {
                        vm.itemselected.status = 'IN_PROGRESS';
                    }
                })
                    .catch(function (erro) {
                        console.log(erro);
                        console.log("FAILURE!!");
                    });
            } else {
                uploadedFiles = [];
                vm.chat.content = "";
                vm.chat.clear = "";
                vm.attachments = [];
                vm.$snotify.info(
                    vm.$t("bs-empty-fields") + "!",
                    vm.$t("bs-info")
                );
            }
        },
        sendMessage2() {
            var vm = this;
            vm.chat.content = vm.chat.clear;
            vm.chat.clear = "";
            if (vm.chat.content.trim() !== "") {
                // verificar se tem IMAGEM dentro do html do content
                var quotes = vm.chat.content.split('"');
                var images = [];
                quotes.forEach(element => {
                    if (element.substring(0, 10) == 'data:image') {
                        images.push(element);
                    }
                });
                // mesma altura para todas as imagens
                vm.chat.content = vm.chat.content.replace('><img', '><img  style="height: 150px;"');

                vm.showActions = false;
                axios
                    .post("client-create-chat-history", {
                        id: vm.chat.id,
                        type: "TEXT",
                        content: vm.chat.content,
                        id_department: vm.ticket_info.id_department,
                        id_ticket: vm.ticket_info.id_ticket,
                        company_user_id: vm.ticket_info.company_user_id,
                        priority: vm.ticket_info.priority,
                        typeSendMessage: vm.typeSendMessage,
                        questionsConfirm: vm.questionsConfirm,
                        onlineUsers: vm.$store.state.online_users,
                        images: images.length ? images : null
                    })
                    .then(function (response) {
                        // console.log(response);
                        if (response.data.success) {
                            if (response.data.value == "ticket_create") {
                                vm.ticket_info.description = vm.chat.content;
                                vm.$snotify.success(
                                    vm.$t("bs-ticket-created-successfully"),
                                    vm.$t("bs-success")
                                );
                                vm.typeSendMessage = "addmessage";
                                vm.openTicket(response.data.result);
                                vm.tickets.push(response.data.result);
                            } else {
                                // if (vm.files.length > 0) {
                                //     let formData = new FormData();
                                //     for (var i = 0; i < vm.files.length; i++) {
                                //       let file = vm.files[i];
                                //       formData.append("files[" + i + "]", file);
                                //     }
                                //     formData.append("chat_id", vm.chat.id);
                                //     formData.append("extImages", vm.extImages);
                                //     formData.append("is_client", true);
                                //     formData.append("is_ticket", true);
                                //     formData.append("id_ticket", vm.ticket_info.id_ticket);
                                //     axios
                                //       .post(`chat/client/upload`, formData, {
                                //         headers: {
                                //           "Content-Type": "multipart/form-data",
                                //         },
                                //       })
                                //       .then(function () {
                                // receive response via realtime "MessageSent" event
                                //         console.log("SUCCESS!!");
                                //       })
                                //       .catch(function () {
                                //         console.log("FAILURE!!");
                                //       });
                                //   }
                                // vm.files = [];
                                // vm.uploadComponent = [];
                            }
                        } else {
                            if (response.data.value == "not_message") {
                                vm.$snotify.info(
                                    vm.$t("bs-you-cannot-send-messages-until-the-ticket") + "!",
                                    vm.$t("bs-info")
                                );
                            } else {
                                vm.$snotify.error(
                                    vm.$t("bs-error-trying-to-save"),
                                    vm.$t("bs-error")
                                );
                            }
                        }
                    })
                    .catch(function (erro) {
                        console.log(erro);
                        console.log("FAILURE!!");
                    });
            }
        },
        avaliation() {
            var vm = this;
            if (vm.evaluateFormValidated) {
                vm.showActions = false;
                axios.post("client-avaliation-ticket", {
                    id: vm.itemselected.id,
                    chat_id: vm.itemselected.chat_id,
                    atendant: vm.stars_atendent,
                    service: vm.stars_service,
                    comment: vm.evaluationComment,
                })
                    .then(function (response) {
                        // console.log(response);
                        if (response.data.result == 'checked') {
                            vm.showSidebarRight = false;
                            $("#avaliationModal").modal("hide");
                            vm.$snotify.info(
                                vm.$t("bs-this-ticket-has-already-been-rated"),
                                vm.$t("bs-info")
                            );
                        } else {
                            vm.$snotify.success(
                                vm.$t("bs-thank-you-for-your-review"),
                                vm.$t("bs-success")
                            );
                            $("#avaliationModal").modal("hide");
                            vm.showChat = false;
                        }
                        vm.stars_atendent = 0;
                        vm.stars_service = 0;
                        vm.evaluationComment = "";
                        vm.$root.$refs.ChatTicketEvaluation.stars = 0;
                    }).catch(function (erro) {
                        vm.$snotify.error(
                            vm.$t("bs-error-trying-to-save"),
                            vm.$t("bs-error")
                        );
                        // console.log(erro);
                        console.log("FAILURE!!");
                    });
            }
        },
        actions(value) {
            if (value == false) {
                this.showSidebarRight = value;
            } else {
                this.showSidebarRight = !this.showActions;
            }
        },
        back() {
            this.showChat = false;
            this.value = null;
            this.chat_history = [];
            this.questOptions = [];
            this.answers = [];
            this.loadDepartmentsByTimezone();
        },
        setStatusTicket(value) {
            var vm = this;
            Swal.fire({
                title:
                    value == 0
                        ? vm.$t("bs-close")
                        : value == 1
                            ? vm.$t("bs-solved")
                            : value == 2
                                ? vm.$t("bs-cancel")
                                : value == 3
                                    ? vm.$t("bs-reopen")
                                    : "",
                text: //vm.$t("bs-are-you-sure-you-want-to-do-this"),
                    value == 0
                        ? vm.$t("bs-are-you-sure-you-want-to-do-this")
                        : value == 1
                            ? vm.$t("bs-would-you-like-to-mark-the-ticket-as-resol") + " " + vm.$t("bs-after-this-action-it-will-no-longer-be-pos")
                            : value == 2
                                ? vm.$t("bs-are-you-sure-you-want-to-cancel-the-ticket") + " " + vm.$t("bs-after-this-action-it-will-no-longer-be-pos")
                                : value == 3
                                    ? vm.$t("bs-would-you-like-to-reopen-this-ticket")
                                    : "",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: vm.$t("bs-yes"),
                cancelButtonText: vm.$t("bs-not"),
                confirmButtonColor: "#3085d6",
                // reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    vm.showActions = false;
                    axios
                        .post("client-status-ticket", {
                            id: vm.itemselected.id,
                            original_status_ticket: vm.itemselected.status,
                            chat_id: vm.itemselected.chat_id,
                            status: value,
                            company_user_id: vm.ticket_info.company_user_id,
                            company_department_id: vm.itemselected.department_id
                        })
                        .then(function (response) {
                            vm.getClientTickets();
                            vm.itemselected.status = response.data.value;
                            if (response.data.value == 'CLOSED') {
                                vm.$snotify.success(
                                    vm.$t("bs-dont-forget-to-rate-the-ticket"),
                                    vm.$t("bs-success")
                                );
                            }
                            //vm.showChat = false;
                            if (value == 2) {
                                vm.showChat = false;
                            }
                            if (value != 3) {
                                if (value != 2) {
                                    $("#avaliationModal").modal("show");
                                }
                            }
                        })
                        .catch(function (erro) {
                            console.log(erro);
                            console.log("FAILURE!!");
                        });
                } else {
                    vm.actions(false);
                }
            });
        },
        colorStatus(value, color = 0) {
            if (value == "OPENED") {
                return "in-queue";
            } else if (value == "IN_PROGRESS") {
                if (color) {
                    return "in-progress2";
                } else {
                    return "in-progress";
                }
            } else if (value == "CLOSED") {
                return "in-closed";
            } else if (value == "RESOLVED") {
                return "in-resolved";
            } else if (value == "CANCELED") {
                return "in-canceled";
            }
        },
        setSidebarOptions(status) {
            switch (status) {
                case "OPENED":
                    this.sidebarOptions = {
                        canceled: {
                            title: this.$t("bs-cancel") + " ticket",
                            status: "CANCELED",
                            message: "Você tem certeza que deseja cancelar o ticket?",
                        },
                    };
                    break;
                case "IN_PROGRESS":
                    this.sidebarOptions = {
                        resolved: {
                            title: "Marcar como resolvido",
                            status: "RESOLVED",
                            message: "Este ticket solucionou o seu problema?",
                        },
                        canceled: {
                            title: "Cancelar ticket",
                            status: "CANCELED",
                            message: "Você tem certeza que deseja cancelar o ticket?",
                        },
                    };
                    break;
                case "CLOSED":
                    this.sidebarOptions = {
                        resolved: {
                            title: "Marcar como resolvido",
                            status: "RESOLVED",
                            message: "Este ticket solucionou o seu problema?",
                        },
                        reopen: {
                            title: "Reabrir o ticket",
                            status: "REOPENED",
                            message: "Você tem certeza que deseja reabrir o ticket?",
                        },
                    };
                    break;
                case "RESOLVED":
                    this.sidebarOptions = {
                        evaluate: {
                            title: "Avaliar o ticket",
                            status: "EVALUATE",
                            message: "Deseja avaliar este ticket?",
                        },
                    };
                    break;
            }
        },
        statusTicket(value) {
            if (value == "OPENED") {
                return this.$t("bs-opened");
            } else if (value == "IN_PROGRESS") {
                return this.$t("bs-in-progress");
            } else if (value == "CLOSED") {
                return this.$t("bs-closed");
                // return this.$t("bs-Finalize o Ticket");
            } else if (value == "RESOLVED") {
                return this.$t("bs-resolved");
            } else if (value == "CANCELED") {
                return this.$t("bs-canceled");
            }
        },
        LI(value) {
            if (value == null) {
                return "LI";
            }
            return value.substr(0, 2);
        },
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
                timeZone: tz,
            });
            return moment(converted_time, "DD/MM/YYYY HH:mm:ss").format(
                "YYYY-MM-DD HH:mm:ss"
            );
        },
        UTCtoClientTZ2(h, tz) {
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
                timeZone: tz,
            });
            var mt = require("moment-timezone");
            return mt(converted_time, "DD/MM/YYYY HH:mm:ss")
                .tz(tz)
                .locale(this.user.language)
                .format('L LT');
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
    props: {
        user: Object,
        csname: String,
        user_client_id: String,
        user_auth_id: String,
        setting_chat: String,
        company_id: String,
    },
    filters: {
        str_limit(value, size) {
            if (!value) return '';
            value = value.toString();
            if (value.length <= size) {
                return value;
            }
            return value.substr(0, size) + '...';
        },
        formatData(value) {
            return moment(value).format("HH:mm DD/MM/YYYY");
        },
        lastInteraction(value) {
            if (value == null) {
                return "--";
            }
            var d = new Date();
            var date1 = moment(value);
            var date2 = moment(d);
            var diff = date2.diff(date1);
            var diffInMinutes = date2.diff(date1, "minutes");
            var num = diffInMinutes;
            var hours = num / 60;
            var rhours = Math.floor(hours);
            var minutes = (hours - rhours) * 60;
            var rminutes = Math.round(minutes);
            if (rhours >= 24) {
                return "Há mais de " + (rhours / 24).toFixed(0) + " dias pendentes";
            } else {
                return rhours + " hour(s) and " + rminutes + " minute(s).";
                //VERSÃO INGLES
                return rhours + " hora(s) e " + rminutes + " minuto(s).";
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
    },
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css">
</style>
<style scoped>
.fz-14 {
    font: normal normal 800 14px/14px Muli;
}

.bs-green {
    color: green;
    font-size: 14px;
}

.bs-blue {
    color: blue;
    font-size: 14px;
}

.personTextArea {
    color: #707070;
    opacity: 0.8;
}

.scroll {
    overflow: scroll !important;
}

.btncreate {
    text-align: center;
    background-color: #A9D0F5;
    padding: 14px;
    margin: 50px;
    color: #03529c;
    font-size: 16px;
    border: none;
    font-weight: bold;
}

.btncreate:hover {
    background-color: #58ACFA;
    color: #045FB4;
    border: none;
}

.cl-b {
    color: white;
}

.op-8 {
    opacity: 0.8;
}

.op-10 {
    opacity: 1;
}

.care {
    cursor: pointer;
}

h1 {
    text-align: left;
    font: normal normal 800 25px/19px Muli;
    letter-spacing: 0px;
    color: #0080fc;
    opacity: 1;
}

h2 {
    font: normal normal 800 15px/31px Muli;
    letter-spacing: 0px;
    color: #333333;
    opacity: 1;
}

h5 {
    font: normal normal bold 16px/22px Muli;
    letter-spacing: 0px;
    color: #434343;
}

span {
    font: normal normal 800 15px/16px Muli;
    letter-spacing: 0.45px;
}

thead tr {
    background: #f7f8fc 0% 0% no-repeat padding-box;
    border-radius: 5px 5px 0px 0px;
    opacity: 1;
}

thead tr th {
    border: none;
}

thead tr th:first-child {
    border-radius: 5px 0px 0px 0px;
}

thead tr th:last-child {
    border-radius: 0px 5px 0px 0px;
}

tbody td {
    height: 63px;
    vertical-align: middle;
    border: 1px solid #dee3ea;
}

tbody td:first-child {
    border-left: none !important;
}

tbody td:last-child {
    border-right: none !important;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: white;
}

tbody tr {
    background-color: #fdfdfd;
}

tbody tr:hover {
    background-color: #f7f8fc !important;
    cursor: pointer;
}

.in-queue {
    opacity: 1;
    color: #ffb244;
}

.in-progress {
    opacity: 1;
    color: #00c38e;
}

.in-progress2 {
    opacity: 1;
    color: #f4f4f4;
}

.in-closed {
    opacity: 1;
    color: #c0c3c6;
}

.in-resolved {
    opacity: 1;
    color: #0294ff;
}

.in-canceled {
    opacity: 1;
    color: #ff4872;
}

.answered {
    background: #ff4872;
    color: #f4f4f4;
}

.answered:hover {
    background: #f5829d;
    color: #f4f4f4;
}

tbody tr {
    font: normal normal 600 16px/19px Lato;
    letter-spacing: 0px;
    color: #6e6e6e;
}

.content {
    margin-right: 40px;
    margin-left: 40px;
}

.margin-0 {
    margin: -15px;
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

#search {
    max-width: 250px;
    float: right;
    background: #ffffff 0% 0% no-repeat padding-box;
    box-shadow: 0px 0px 2px #26242440;
    border-radius: 5px;
    opacity: 1;
    height: 50px;
    padding-top: 8px;
    padding-left: 8px;
}

#search input {
    border: none !important;
}

#search input::placeholder {
    font: normal normal medium 14px/17px Lato;
    letter-spacing: 0px;
    color: #434343;
    opacity: 1;
}

.input-group-prepend {
    color: #434343;
}

textarea:focus,
input[type="text"]:focus,
input[type="password"]:focus,
input[type="datetime"]:focus,
input[type="datetime-local"]:focus,
input[type="date"]:focus,
input[type="month"]:focus,
input[type="time"]:focus,
input[type="week"]:focus,
input[type="number"]:focus,
input[type="email"]:focus,
input[type="url"]:focus,
input[type="search"]:focus,
input[type="tel"]:focus,
input[type="color"]:focus,
.uneditable-input:focus {
    border-color: none;
    box-shadow: none;
    outline: 0 none;
}

.card {
    border: none;
    border-radius: 0px;
    padding-bottom: 50px;
}

.card-header {
    height: 60px;
    background: #ffffff 0% 0% no-repeat padding-box;
    box-shadow: 0px 0px 9px #26242424;
    opacity: 1;
    border: none;
}

.card-header .buttons small {
    cursor: pointer;
}

.card-header .buttons small:hover {
    -webkit-text-fill-color: #0294ff;
    -webkit-text-stroke: 1px #0294ff;
}

.clickavaliation:hover {
    -webkit-text-fill-color: #0294ff;
    -webkit-text-stroke: 1px #0294ff;
}

.content-chat {
    overflow: auto;
    background-color: #e6e7e7;
}

.float {
    position: fixed;
    bottom: 40px;
    right: 40px;
    color: white;
    border-radius: 10px;
    text-align: center;
    box-shadow: 2px 2px 3px #999;
    height: 40px;
    width: 148px;
    background-color: #0294ff;
    cursor: pointer;
    padding-top: 7px;
}

.floatIcon {
    vertical-align: middle;
}

.mw-50 {
    max-width: 50px;
}

.mw-80 {
    padding: 0px;
    max-width: 100px !important;
}

.list-group-item p {
    font: normal normal normal 13px/16px Muli;
    letter-spacing: 0px;
    color: #434343;
}

.footer-2 {
    font: normal normal 600 16px/20px Muli;
    letter-spacing: 0px;
    color: #6c7f98;
    vertical-align: middle;
}

.footer-2 small {
    cursor: pointer;
}

.footer-1 {
    background-color: #e6e7e7;
    border: none;
    padding: 0px;
}

.content-input {
    background: white;
    padding: 5px;
    margin-left: 50px;
    margin-right: 50px;
    margin-bottom: 15px;
    border-radius: 3px;
    border: 1px solid #dddddd;
}

.content-textarea {
    background: white;
    padding: 5px;
    margin-bottom: 15px;
    border-radius: 3px;
    border: 1px solid #dddddd;
}

.row-textarea {
    padding-right: 30px;
    background-color: transparent;
}

.lighttextarea {
    padding: 10px;
    background-color: white;
    border-radius: 10px;
    border-color: 1px solid red;
    box-shadow: 0px 4px 8px #00000040;
}

textarea {
    font: normal normal normal 16px/20px Muli;
    letter-spacing: 0px;
    color: #707070;
    max-height: 159px;
    width: 100%;
    border: none;
    resize: none;
}

.col-input-btn {
    min-width: 15px;
    max-width: 15px;
    background: transparent;
    margin-bottom: 5px;
    display: flex;
    align-items: flex-end;
    padding-left: 5px;
}

.col-input-btn a {
    float: left !important;
    left: 0;
}

#btn-filter {
    height: 50px;
    width: 120px;
    padding-top: 8px;
    padding-left: 2px;
    padding-right: 2px;
    background: #fafbfc 0% 0% no-repeat padding-box;
    border: 1px solid #dddddd;
    border-radius: 3px;
    font: normal normal bold 14px/35px Muli;
    letter-spacing: 0px;
    color: #656565;
    float: left;
}

.filter_list {
    max-width: 30px;
    left: -40px;
}

.w-380 {
    max-width: 450px;
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

    .content-input {
        margin-left: 0px;
        margin-right: 0px;
        margin-bottom: 0px;
        border: none;
        border-radius: 0px;
    }

    .content-chat,
    .modal-content,
    .w-380 {
        zoom: 85%;
    }

    .w-380 {
        width: 100% !important;
    }

    #search {
        min-width: 84%;
    }

    table {
        zoom: 80%;
    }

    .filter_list {
        width: 30px;
        left: 8px;
    }

    #btn-filter {
        width: 50px;
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
        margin-bottom: 250px;
        text-align: center;
        justify-content: center;
    }

    #btn-new-chat {
        max-width: 140px;
        padding-left: 6px;
        padding-right: 6px;
    }

    .dspNone {
        display: none;
        margin-left: 10px;
        color: black;
    }
}

.sidebar-right .list-group-item {
    border-radius: 0px !important;
    border: none !important;
    background: transparent !important;
}

.sidebar-right .list-group-item:hover {
    background: #0294ff !important;
    color: white;
}

@media only screen and (max-width: 255px) {
    .mt-5 {
        margin-top: 10px !important;
    }

    .mt-1 {
        margin-top: -20px !important;
    }

    .responsivetable {
        top: 155px !important;
        width: 100px !important;
        left: 10px !important;
        right: 0 !important;
    }

    h1 {
        text-align: left;
        font: normal normal 800 20px/14px Muli;
        letter-spacing: 0px;
        color: #0080fc;
        opacity: 1;
    }

    h2 {
        font: normal normal 800 14px/14px Muli;
        letter-spacing: 0px;
        color: #333333;
        opacity: 1;
    }

    h5 {
        font: normal normal bold 16px/22px Muli;
        letter-spacing: 0px;
        color: #434343;
    }

    #search {
        max-width: 250px;
        float: right;
        background: #ffffff 0% 0% no-repeat padding-box;
        box-shadow: 0px 0px 2px #26242440;
        border-radius: 5px;
        opacity: 1;
        height: 50px;
        padding-top: 8px;
        padding-left: 8px;
    }

    #search input {
        border: none !important;
    }

    #search input::placeholder {
        font: normal normal medium 14px/17px Lato;
        letter-spacing: 0px;
        color: #434343;
        opacity: 1;
    }

    .input-group-prepend {
        color: #434343;
    }

    textarea:focus,
    input[type="text"]:focus,
    input[type="password"]:focus,
    input[type="datetime"]:focus,
    input[type="datetime-local"]:focus,
    input[type="date"]:focus,
    input[type="month"]:focus,
    input[type="time"]:focus,
    input[type="week"]:focus,
    input[type="number"]:focus,
    input[type="email"]:focus,
    input[type="url"]:focus,
    input[type="search"]:focus,
    input[type="tel"]:focus,
    input[type="color"]:focus,
    .uneditable-input:focus {
        border-color: none;
        box-shadow: none;
        outline: 0 none;
    }

    .card {
        border: none;
        border-radius: 0px;
        padding-bottom: 50px;
    }

    .card-header {
        height: 60px;
        background: #ffffff 0% 0% no-repeat padding-box;
        box-shadow: 0px 0px 9px #26242424;
        opacity: 1;
        border: none;
    }

    .card-header .buttons small {
        cursor: pointer;
    }

    .card-header .buttons small:hover {
        -webkit-text-fill-color: #0294ff;
        -webkit-text-stroke: 1px #0294ff;
    }

    .clickavaliation:hover {
        -webkit-text-fill-color: #0294ff;
        -webkit-text-stroke: 1px #0294ff;
    }

    .content-chat {
        overflow: auto;
        background-color: #e6e7e7;
    }

    .mw-50 {
        max-width: 50px;
    }

    .mw-80 {
        padding: 0px;
        max-width: 100px !important;
        -webkit-text-stroke: 1px #d7dee6;
        -webkit-text-fill-color: white;
    }

    .list-group-item p {
        font: normal normal normal 13px/16px Muli;
        letter-spacing: 0px;
        color: #434343;
    }

    .footer-2 {
        font: normal normal 600 16px/20px Muli;
        letter-spacing: 0px;
        color: #6c7f98;
        vertical-align: middle;
    }

    .footer-2 small {
        cursor: pointer;
    }

    .footer-1 {
        background-color: #e6e7e7;
        border: none;
        padding: 0px;
    }

    .content-input {
        background: white;
        padding: 5px;
        margin-left: 50px;
        margin-right: 50px;
        margin-bottom: 15px;
        border-radius: 3px;
        border: 1px solid #dddddd;
    }

    .content-textarea {
        background: white;
        padding: 5px;
        margin-bottom: 15px;
        border-radius: 3px;
        border: 1px solid #dddddd;
    }

    .row-textarea {
        padding-right: 30px;
        background-color: transparent;
    }

    .lighttextarea {
        padding: 10px;
        background-color: white;
        border-radius: 10px;
        border-color: 1px solid red;
        box-shadow: 0px 4px 8px #00000040;
    }

    textarea {
        font: normal normal normal 16px/20px Muli;
        letter-spacing: 0px;
        color: #707070;
        max-height: 159px;
        width: 100%;
        border: none;
        resize: none;
    }

    .content {
        margin-right: 0;
        margin-left: 0;
    }

    .card-header {
        padding-top: 0;
        height: 0;
        zoom: 120%;
    }

    .content-input {
        margin-left: 0px;
        margin-right: 0px;
        margin-bottom: 0px;
        border: none;
        border-radius: 0px;
    }

    .content-chat,
    .modal-content,
    .w-380 {
        zoom: 65%;
    }

    .w-380 {
        width: 80% !important;
    }

    #search {
        min-width: 84%;
    }

    table {
        zoom: 80%;
    }

    .filter_list {
        width: 30px;
        left: 8px;
    }

    #btn-filter {
        width: 50px;
    }

    .modal-dialog {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
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

    .modal-footer {
        margin-bottom: 150px;
        text-align: center;
        justify-content: center;
    }

    #btn-new-chat {
        max-width: 140px;
        padding-left: 6px;
        padding-right: 6px;
    }
}

.ellipsis {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.col-name {
    width: calc(75% - 80px);
}

.care {
    font: normal normal 800 15px/16px Muli;
    color: #434343 !important;
}
</style>