<template>
  <div
    class="h-100 chat-client"
    :class="{ content: !showChat, 'margin-0': showChat, mobile: isMobile }"
  >
    <template v-if="!showChat">
      <!-- <beta-alert :showChat="showChat"></beta-alert> -->
      <b-row class="mt-5">
        <b-col sm="12" lg="">
          <h1>{{ $t("bs-chat") }}</h1>
          <span>{{ $t("bs-listing") }}</span>
        </b-col>
      </b-row>
      <!-- <br />
        <h2 class="mt-1 mb-3">{{ $t("bs-actives") }}</h2> -->
      <div class="table-responsive" :class="{ mobile: isMobile }">
        <table class="table text-nowrap text-center">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">{{ $t("bs-waiting-time") }}</th>
              <th scope="col">{{ $t("bs-status") }}</th>
              <th scope="col">{{ $t("bs-attendants") }}</th>
              <th scope="col">{{ $t("bs-chat-start-time") }}</th>
              <th scope="col">{{ $t("bs-department") }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(row, index) in chats" :key="index" @click="openChat(row)">
              <td
                v-bind:class="{
                  answered: row.answered && row.status === 'IN_PROGRESS',
                }"
              >
                #{{ row.number }}
              </td>
              <td
                v-if="row.status === 'OPENED'"
                v-bind:class="{
                  answered: row.answered && row.status === 'IN_PROGRESS',
                }"
                :id="'time-elapsed-' + row.chat_id"
              >
                {{
                  calculateWaitingTime(
                    UTCtoClientTZ(row.created_at, tz),
                    "time-elapsed-" + row.chat_id,
                    row.inactivityMessage,
                    row.timewait
                  )
                }}
              </td>
              <td
                v-else
                v-bind:class="{
                  answered: row.answered && row.status === 'IN_PROGRESS',
                }"
              >
                ---
              </td>

              <td
                v-bind:class="{
                  opened: row.status === 'OPENED',
                  'in-progress': row.status === 'IN_PROGRESS',
                  canceled: row.status === 'CANCELED',
                  closed: row.status === 'CLOSED',
                  resolved: row.status === 'RESOLVED',
                  answered: row.answered && row.status === 'IN_PROGRESS',
                }"
              >
                {{ getChatStatus(row) }}
              </td>

              <td
                v-bind:class="{
                  answered: row.answered && row.status === 'IN_PROGRESS',
                }"
              >
                {{ row.agent ? row.agent : "---" }}
              </td>

              <td
                v-bind:class="{
                  answered: row.answered && row.status === 'IN_PROGRESS',
                }"
              >
                {{ UTCtoClientTZ2(row.created_at, tz) }}
              </td>

              <td
                v-bind:class="{
                  answered: row.answered && row.status === 'IN_PROGRESS',
                }"
              >
                <img
                  v-if="row.dep_type == 'builderall-mentor'"
                  src="http://www.omb100.com/internacional/public/office5/img/general-svg/icon_vip.svg"
                  alt=""
                  height="20"
                />
                {{ $t(row.department) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- <a v-if="!checkout" @click="loadDepartmentsByTimezone" class="float">
        <vue-material-icon name="add" :size="60" />
      </a> -->

      <span v-if="!checkout" @click="loadDepartmentsByTimezone" class="float">
        <span class="floatIcon"><vue-material-icon  name="add" :size="25" /></span>
        <span class="">{{$t('bs-create-chat')}}</span>
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
              :text="LI(agent_name)"
              size="40px"
            ></b-avatar> -->
          </b-col>
          <b-col class="col-name">
            <div class="list-group p-0 m-0">
              <a
                class="list-group-item list-group-item-action flex-column align-items-start border-0 p-0 m-0"
              >
                <div class="d-flex justify-content-between">
                  <h5 class="mb-1 ellipsis">
                    {{
                      agent_name ? agent_name : this.$t("bs-wait-we-will-see-you-soon")
                    }}
                  </h5>
                </div>
                <p class="mb-1 mt-n1 ellipsis">
                  {{ $t(department_name) }}
                  <img
                    v-if="chat.dep_type == 'builderall-mentor'"
                    src="http://www.omb100.com/internacional/public/office5/img/general-svg/icon_vip.svg"
                    alt=""
                    height="15"
                  />
                </p>
              </a>
            </div>
          </b-col>
          <b-col class="mw-80 mt-2 buttons">
            <span v-if="!checkout" @click="back" class="level-3 care cursor-pointer">
              <i class="fa fa-arrow-left mt-1" aria-hidden="true"></i>
              {{ $t("bs-back") }}
            </span>
          </b-col>
        </b-row>
      </div>
      <div class="card-body content-chat" v-chat-scroll @click="showSidebarRight = false">

        <component
            v-for="(message, index) in chat_history_robot"
            :key="index"
            :is="setMessageComponentRobot(message)"
            v-bind="setMessagePropsRobot(message, index)"
            :formatTime="formatMessageTime"
        />

        <message-type-question
          v-if="questionary.length"
          :questionary="questionary"
          :department_name="$t(department_name)"
          :user="user"
        />

        <component
            v-for="(message, index) in chat_history"
            :key="index"
            :is="setMessageComponent(message)"
            v-bind="setMessageProps(message, index)"
            :formatTime="formatMessageTime"
        />

      </div>
      <div
        v-if="chat.status === 'OPENED' || chat.status === 'IN_PROGRESS'"
        class="card-footer footer-1"
      >
        <div class="content-input">
          <multiselect
            v-if="files != ''"
            :hide-selected="true"
            :placeholder="$t('bs-placeholder-cancel-sending-the-file')"
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
                v-model="chat.content"
                @keyup.enter="sendMessage"
                :placeholder="phTypeHere"
                autofocus
                class="textarea js-autoresize"
              ></textarea>
            </div>
            <div class="col col-input-btn mr-3">
              <a :id="`popover-4`">
                <img src="images/icons/chat/emoji.svg" height="25" width="25" />
              </a>
              <b-popover
                :target="`popover-4`"
                :placement="'topright'"
                title=""
                triggers="hover focus"
                :id="'pop4'"
              >
                <VEmojiPicker :showSearch="false" @select="concatEmoji" />
              </b-popover>
            </div>
            <div class="col col-input-btn">
              <a @click="sendMessage">
                <img src="images/icons/send.svg" height="25" width="25" />
              </a>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer footer-2 pr-3 pl-3" v-if="chat.status !== 'CANCELED'">
        <div class="row w-100 p-0">
          <div class="col ellipsis">
            <b>#{{ chat.number }}</b>
            <template
              v-if="
                queue_position >= 0 && queue_position !== null && chat.status === 'OPENED'
              "
            >
              -
              {{ $t("bs-queue-position") }}: <b>{{ queue_position }}</b> -
              {{ $t("bs-waiting-time") }}:
              <b id="time-elapsed-footer">{{
                calculateWaitingTime(
                  UTCtoClientTZ(chat.created_at, tz),
                  "time-elapsed-footer",
                  chat.inactivityMessage,
                  chat.timewait
                )
              }}</b>
            </template>
          </div>
          <div class="col" style="text-align: right">
            <small
              v-if="chat.status !== 'RESOLVED' && chat.status !== 'CLOSED' && chat.status !== 'ROBOT'"
              @click="upload"
              class="mr-3"
            >
              <img src="images/icons/upload.svg" />
              <b class="dspNone" style="color: black">{{ $t("bs-upload") }}</b>
            </small>
            <input
              type="file"
              id="attachments"
              ref="attachments"
              multiple
              v-on:change="handleFilesUpload()"
              style="display: none"
            />
            <small
              v-for="(option, index) in sidebarOptions"
              :key="index"
              class="mr-3"
              @click.prevent="actionSidebarOptions(option)"
              v-b-toggle.sidebar-right
            >
              <template v-if="option.status == 'EVALUATE-CLOSE'">
                <i class="fa fa-times" style="font-size: 20px" aria-hidden="true"></i>
                <b class="dspNone">{{ $t("bs-close") }} {{ $t("bs-chat") }}</b>
              </template>
              <template v-if="option.status == 'EVALUATE'">
                <i
                  class="fa fa-thumbs-o-up"
                  style="font-size: 20px"
                  aria-hidden="true"
                ></i>
                <b class="dspNone">{{ $t("bs-evaluate") }}</b>
              </template>
              <template v-if="option.status == 'CANCELED'">
                <i
                  class="fa fa-times-circle-o bs-trash"
                  style="font-size: 20px"
                  aria-hidden="true"
                ></i>
                <b class="dspNone">{{ $t("bs-cancel") }}</b>
              </template>
            </small>
          </div>
        </div>
      </div>
      <div>
        <b-sidebar
          v-if="showSidebarRight"
          class="p-0 bg-transparent"
          id="sidebar-right"
          :title="$t('bs-options')"
          right
          shadow
          ref="sidebarright"
        >
          <div class="list-group sidebar-right">
            <a
              v-for="(option, index) in sidebarOptions"
              :key="index"
              href="#"
              @click.prevent="actionSidebarOptions(option)"
              class="list-group-item list-group-item-action"
              >{{ option.title }}</a
            >
          </div>
        </b-sidebar>
      </div>
    </div>
    <div
      class="modal fade"
      id="exampleModal"
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
              {{ $t("bs-select-a-department") }}
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              X
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label for="exampleFormControlSelect1">{{ $t("bs-department") }}</label>
                  <!-- <select class="form-control" id="exampleFormControlSelect1">
                        <option>Departamento</option>
                    </select> -->
                  <multiselect
                    v-model="company_department"
                    deselect-label=""
                    selectLabel=""
                    track-by="name"
                    label="name"
                    :custom-label="nameWithLang"
                    :placeholder="$t('bs-select-a-department')"
                    :options="options"
                    :searchable="false"
                    :allow-empty="false"
                    id="departments"
                  >
                    <template slot="singleLabel" slot-scope="{ option }">
                      <strong>
                        {{ option.name }}
                      </strong>
                      <img
                        v-if="option.type == 'builderall-mentor'"
                        src="http://www.omb100.com/internacional/public/office5/img/general-svg/icon_vip.svg"
                        alt=""
                        height="20"
                      />
                    </template>
                    <template slot="option" slot-scope="{ option }">
                      <strong>
                        {{ option.name }}
                      </strong>
                      <img
                        v-if="option.type == 'builderall-mentor'"
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
            <button type="button" class="text-capitalize btn" data-dismiss="modal">
              {{ $t("bs-cancel") }}
            </button>
            <button
              type="button"
              id="btn-new-chat"
              class="btn btn-primary"
              @click="createNewChat()"
            >
              {{ $t("bs-next") }}
            </button>
          </div>
        </div>
      </div>
    </div>
    <div
      class="modal fade"
      id="modalQuestionary"
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
              {{ $t("bs-department") }}
              {{ company_department ? company_department.name : "" }}
            </h5>
            <button
              v-if="!checkout && chat.status !== 'ROBOT'"
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
                  <form @submit.prevent="openEmptyChat(chat.status == 'ROBOT' ? 'ROBOT_TO_OPENED' : 'OPENED')" id="my-form">
                    <div class="row">
                      <div class="col-md-12">
                        <div
                          class="form-group bmd-form-group"
                          v-for="(row, index) in questions"
                          :key="index"
                        >
                          <label
                            v-if="row.mandatory"
                            class="bmd-label-floating"
                            style="line-height: 1.5em"
                          >
                            {{ $t(row.quest) }} <big class="text-danger">*</big>
                          </label>
                          <label
                            v-else
                            class="bmd-label-floating"
                            style="line-height: 1.5em"
                          >
                            {{ $t(row.quest) }}
                          </label>
                          <div class="content-textarea">
                            <template v-if="row.mandatory">
                              <textarea
                                v-model="answers[index]"
                                :placeholder="phTypeHere"
                                autofocus
                                required
                                class="textarea"
                                rows="3"
                              ></textarea>
                            </template>
                            <template v-else>
                              <textarea
                                v-model="answers[index]"
                                :placeholder="phTypeHere"
                                autofocus
                                class="textarea"
                                rows="3"
                              ></textarea>
                            </template>
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
          <div class="modal-footer border-0">
            <button
              v-if="!checkout && chat.status !== 'ROBOT'"
              type="button"
              class="text-capitalize btn"
              data-dismiss="modal"
              @click="backToDepartments()"
            >
              {{ $t("bs-back") }}
            </button>
            <button
              type="submit"
              id="btn-new-chat"
              class="btn btn-primary"
              form="my-form"
            >
              {{ chat.status == 'ROBOT' ?  $t('bs-next') : $t("bs-create-new-chat").toUpperCase() }}
            </button>
          </div>
        </div>
      </div>
    </div>
    <div
      class="modal fade"
      id="evaluationModal"
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
              {{ $t("bs-chat-rating") }}
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              X
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <form @submit.prevent="sendEvaluation" id="my-evaluation">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group bmd-form-group">
                          <template v-if="show_attendant_evaluation">
                            <label class="bmd-label-floating" style="line-height: 1.5em"
                              >{{
                                $t("bs-evaluate-the-performance-of-our-attendant")
                              }}:</label
                            >
                            <chat-ticket-evaluation module="chat" :type="type_evaluation" toEvaluate="attendant" />
                          </template>
                          <template v-if="show_service_evaluation">
                            <label
                              class="bmd-label-floating"
                              style="line-height: 1.5em"
                              >{{ $t("bs-service-provided-solve-your-problem") }}</label
                            >
                            <chat-ticket-evaluation module="chat" :type="type_evaluation" toEvaluate="service"/>
                          </template>
                          <template v-if="show_comment_evaluation">
                            <label
                              class="bmd-label-floating"
                              style="line-height: 1.5em"
                              >{{ $t("bs-please-leave-a-comment") }}</label
                            >
                            <div class="content-textarea">
                              <textarea
                                v-model="evaluationComment"
                                autofocus
                                class="textarea"
                                rows="3"
                              ></textarea>
                            </div>
                          </template>
                        </div>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="submit" class="btn btn-primary" form="my-evaluation" :disabled="!evaluateFormValidated">
              {{ $t("bs-submit-review") }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { VEmojiPicker } from "v-emoji-picker";
export default {
  props: {
    user: "",
    user_auth_id: "",
    user_client_id: "",
    setting_chat: "",
    company_id: "",
    session_dtype: "",
    session_show_only_dtype: "",
    session_cscode: "",
    session_plan: "",
    session_fee: "",
    session_department_id: "",
  },
  components: {
    VEmojiPicker,
  },
  data() {
    return {
      tz: "",
      m: "",
      w_time: "",
      showChat: true,
      newChat: false,
      component: {
        OPEN: "message-type-open",
        CLOSE: "message-type-close",
        FILE: "message-type-file",
        IMAGE: "message-type-image",
        EVENT: "message-type-event-client",
        TEXT_SENT: "message-type-text-sent",
        TEXT_RECEIVED: "message-type-text-received",
      },
      componentRobot: {
          TEXT: "message-text",
          DEFAULT_BUTTON: "message-button",
      },
      chats: [],
      chat: {
        id: "",
        type: "TEXT",
        status: "",
        content: "",
        created_at: "",
      },
      chat_history: [],
      chat_history_robot: [],
      agent_name: "",
      department_name: "",
      company_department_id: "",
      queue_position: null,
      company_department: null,
      options: [],
      sidebarOptions: null,
      showSidebarRight: false,
      questions: [],
      answers: [],
      questionary: [],
      //settings
      clientActiveChats: "",
      //uploads
      attachments: [],
      files: [],
      errors: [],
      extImages: ["jpg", "jpeg", "png", "bmp", "gif"],
      extDocuments: ["docx", "doc", "pdf", "xps", "txt", "odt", "svg"],
      extSpreadsheets: ["xlsx", "xls", "xlt", "csv", "ods"],
      extPresentation: ["pptx", "ppt", "pot", "ppsx", "pps", "odp"],
      extensions: [],
      file_exists: false,
      uploadComponent: [],
      //evaluation
      evaluate_and_close: false,
      show_attendant_evaluation: Boolean,
      show_service_evaluation: Boolean,
      show_comment_evaluation: Boolean,
      type_evaluation: 'stars',
      evaluationComment: "",
      activeClass: {
        "-webkit-text-fill-color": "#0294ff",
        "-webkit-text-stroke": "1px #0294ff",
        cursor: "pointer",
      },
      inactiveClass: {
        cursor: "pointer",
      },
      hideWaitingTime: [],
      isMobile: false,
      checkout: false,
      country_bw: "",
      country_sys: this.user.language.split("_")[1],
      phTypeHere: this.$t("bs-type-here") + "...",
      opening_chat: false,
      stars_atendent: 0,
      stars_service: 0
    };
  },
  computed: {
      robotEndOfFlowMessage() {
        return {
            "type": 'text',
            "text": 'Seu problema foi solucionado?',
            "inputTime":null,
            "inputLink":null,
            "inputTransferDepartment":null,
            "children": [
                {
                    "type": 'unsolved',
                    "text": this.$t('bs-no'),
                    "inputTime":null,
                    "inputLink":null,
                    "inputTransferDepartment":null,
                },
                {
                    "type": 'solved',
                    "text": this.$t('bs-yes'),
                    "inputTime":null,
                    "inputLink":null,
                    "inputTransferDepartment":null,
                },
            ]
        };
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
    this.showChat = false;
    this.joinDeletedChannel();
    this.paramsVerification();
  },
  created() {
    this.$root.$refs.ChatClient = this;
    this.m = require("moment-timezone");
    // this.getCountry();
    this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
    this.setExtensions();
    this.getClientChats();
    window.addEventListener("resize", this.onResize);
    this.onResize();
    $("#exampleModal")
      .on("show.bs.modal", function (e) {
        $("body").addClass("example-open");
      })
      .on("hide.bs.modal", function (e) {
        $("body").removeClass("example-open");
      });
  },
  watch: {
      "$store.state.online_users": function () {
          this.checkOnlineAgentsByDepartment();
      },
  },
  methods: {
      checkOnlineAgentsByDepartment() {
           // AGENTS
           var vm = this;
           if (vm.showChat) {
               vm.checkIfIsOnlineAgent(vm.chat.companyDepartmentId).then((result) => {
                    if (result) {
                        var online_agents = vm.$store.state.online_users;
                        online_agents = online_agents.filter((u) => u.is_client !== 1);
                        online_agents = online_agents.filter((u) => u.status == 'online');
                        var online_agents_ids = [];
                        var itemsProcessed = 0;
                        if (online_agents.length > 0) {
                            online_agents.forEach(element => {
                                itemsProcessed++;
                                online_agents_ids.push(element.hash_id)
                                if(itemsProcessed === online_agents.length) {
                                    axios.get('monitoring/count-online-agents-by-department', {
                                        params: {
                                            online_agents: online_agents_ids,
                                            department_id: vm.chat.companyDepartmentId
                                        }
                                    })
                                    .then(({data}) => {
                                        if (!data.success) {
                                            if (vm.chat.status == 'OPENED') {
                                                vm.showAlertCreateTicket();
                                            }

                                        }
                                    })
                                }
                            });
                        } else {
                            if (vm.chat.status == 'OPENED') {
                                vm.showAlertCreateTicket();
                            }
                        }

                    }
               })
           }
      },
      showAlertCreateTicket() {
        Swal.fire({
            icon: 'warning',
            text: this.$t("bs-all-of-our-attendants-are-busy"),
            heightAuto: false,
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: this.$t('bs-cancel-this-chat-and-create-ticket'),
            denyButtonText: this.$t('bs-cancel-chat'),
            cancelButtonText: this.$t('bs-stay-in-line'),
            confirmButtonColor: '#01D4B9',
            denyButtonColor: '#FA4B57',
            cancelButtonColor: '#0080FC',
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                // CANCELAR CHAT E IR PARA OS TICKETS
                this.cancelChat().then((result) => {
                    window.location.href = '/client-ticket';
                })
            } else if (result.isDenied) {
                // CANCELAR CHAT
                this.actionSidebarOptions(
                    {
                        title: this.$t("bs-cancel-chat"),
                        status: "CANCELED",
                        message: this.$t("bs-are-you-sure-you-want-to-cancel-the-chat"),
                    }
                );
            }
        })
      },
      cancelChat() {
          var vm = this
          return new Promise((resolve, reject) => {
              axios.post(`chat/client/update-status`, {
                        id: vm.chat.id,
                        chat: vm.chat,
                        action: 'CANCELED',
                        company_department: vm.company_department,
                        department: vm.department_name,
                        agent: vm.agent_name,
                        status: vm.chat.status,
                    })
                    .then((response) => {
                        if (response.data.status) {
                            resolve();
                        }
                    });
          })
      },
      countOnlineAgentsByDeparment(online_agents) {
            return new Promise((resolve, reject) => {
                var vm = this;

                axios.get('monitoring/count-online-agents-by-department', {
                    params: {
                        online_agents: online_agents,
                        department_id: vm.chat.companyDepartmentId
                    }
                })
                .then(({data}) => {
                    if (data.success) {
                        resolve(data.count);
                    }
                })
            })
        },
    getCountry() {
      fetch("https://extreme-ip-lookup.com/json/")
        .then((res) => res.json())
        .then((response) => {
          if (response.countryCode) {
            this.country_bw = response.countryCode;
          } else {
            this.country_bw = Intl.DateTimeFormat()
              .resolvedOptions()
              .locale.split("-")[1]; //BR
          }
        })
        .catch((data, status) => {
          this.country_bw = Intl.DateTimeFormat().resolvedOptions().locale.split("-")[1]; //BR
        });
    },
    back() {
      window.location.href = "/client-chat";
    },
    onResize(e) {
      if (window.outerWidth < 576) {
        if (!this.isMobile) {
          this.isMobile = true;
        }
      } else {
        if (this.isMobile) {
          this.isMobile = false;
        }
      }
    },
    concatEmoji(emoji) {
      this.chat.content = this.chat.content.concat(emoji.data);
      document.getElementById("input").focus();
    },
    LI(value) {
      if (value) {
        return value.substr(0, 2);
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
              file["name"] === attachment["name"] ? (this.file_exists = true) : "";
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
            /*
                console.log(
                "Arquivo: " +
                    attachment["name"] +
                    " < que 5 MB (5242880 B). Pode ser enviado." +
                    " Tamanho: " +
                    attachment.size +
                    " B."
                );
                */
          } else {
            /*
                if (attachment.size > 5242880) {
                console.log(
                    "Arquivo: " +
                    attachment["name"] +
                    " > que 5 MB (5242880 B). Não pode ser enviado." +
                    " Tamanho: " +
                    attachment.size +
                    " B."
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
                }
                */
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
              ": " +
              this.extensions.join(", ") +
              ".",
          });
        }
      });
      this.errors = [];
      setTimeout(function () {
        document.getElementById("input").focus();
      }, 0);
    },
    createNewChat() {
        $("#exampleModal").modal("hide");
        var vm = this;
        vm.checkIfIsRobot(vm.company_department.id).then((is_robot) => {
            if (is_robot) {
                vm.openEmptyChat('ROBOT')
            } else {
                vm.goToQuestionary();
            }
        });
    },
    openEmptyChat(status) {
      var vm = this;
      if (!vm.opening_chat) {
        vm.opening_chat = true;
        this.checkOnlineAgentsConfig(this.company_department).then(() => {
            return new Promise((resolve, reject) => {
            vm.opening_chat = true;
            vm.$loading(true);
            $("#modalQuestionary").modal("hide");
            axios
                .get("chat/get-active-chats-from-department", {
                params: {
                    company_department_id: vm.company_department.id,
                },
                })
                .then((response) => {
                let active_chats_from_department =
                    response.data.active_chats_from_department;
                if (!active_chats_from_department) {

                    vm.getRobotMessages(vm.company_department.id, status).then((response) => {
                        if(response) {
                            var first_message = response;
                        } else {
                            var first_message = null;
                        }

                        axios
                        .post(`chat/store`, {
                            client: vm.user,
                            company_department: status == 'ROBOT_TO_OPENED' ? { 'id': vm.company_department, 'name': vm.department_name, 'type': '' } : vm.company_department,
                            content: vm.chat.content,
                            questions: vm.questions,
                            answers: vm.answers,
                            checkout: vm.checkout,
                            onlineUsers: vm.$store.state.online_users,
                            status: status,
                            id: vm.chat.chat_id ? vm.chat.chat_id : null,
                            first_message: first_message
                        })
                        .then((response) => {
                            vm.chats.unshift(response.data);
                            vm.openChat(response.data);
                            vm.showChat = true;
                            vm.department_name = response.data.department;
                            vm.chat.dep_type = response.data.dep_type;
                            vm.company_department_id = response.data.company_department_id;
                            vm.questions = [];
                            vm.answers = [];
                            vm.chat.content = ""; //[];
                            vm.chat.status = response.data.status;

                            vm.$loading(false);
                            vm.opening_chat = false;


                            resolve();
                        });
                    });
                } else {
                    vm.$loading(false);
                    Swal.fire({
                    heightAuto: false,
                    title: vm.$t("bs-caution"),
                    text: vm.$t("bs-existing-chat-department"),
                    });
                    vm.opening_chat = false;
                    resolve();
                }
                });
            });
        })
        .catch((err) => {
            this.opening_chat = false;
            this.$loading(false);
            Swal.fire({
                heightAuto: false,
                title: this.$t("bs-caution"),
                text: this.$t("bs-no-attendant-online-at-the-moment"),
            });
        });
      }
    },
    changeChatRobotToTicket() {
        return new Promise((resolve, reject) => {
            var vm = this;
            axios.post('chat/client/change-to-ticket', {
                company_department: vm.company_department,
                id: vm.chat.chat_id,
                chat: vm.chat
            })
            .then(({data}) => {
                if (data.success) {
                    resolve();
                } else {
                    reject(data);
                }
            })
            .catch(err => {
                reject(err);
            })
        });
    },
    checkSuggestionCreateChat() {
        return new Promise((resolve, reject) => {
            var vm = this;
            axios.get('department/check-robot-chat-suggestion', {
                params: {
                    department_id: vm.company_department
                }
            })
            .then(({data}) => {
                resolve(data);
            })
        });
    },
    checkIfIsRobot(department_id) {
        return new Promise((resolve, reject) => {
            var vm = this;
            axios.get('department/is-robot', {
                params: {
                    company_department_id: department_id,
                }
            })
            .then(({data}) => {
                resolve(data);
            })
        })
    },
    getRobotMessages(department_id, status) {
        return new Promise((resolve, reject) => {
            if (status == 'ROBOT') {
                var vm = this;
                axios.get(`department/get-robot/${department_id}`, {})
                .then(res => {
                    var tree_messages = JSON.parse(res.data.result.quest);
                    var first_message = tree_messages.children[0];
                    resolve(first_message);
                })
            }
            else {
                resolve(false);
            }
        });
    },
    executeRobotAction(element, inputTime) {
        if (this.chat.status == 'ROBOT') {

            var finished = false; // declaro o final do fluxo como falso

            // verifico o tipo da opção e executo a ação de acordo com o mesmo.
            switch (element.type) {
                case 'link':
                    window.open(element.inputLink, "_blank"); // abre o link em uma nova guia
                    break;

                case 'create_chat':
                    // aqui "crio o chat"
                    // na vdd, o chat já esta criado, apenas altero o status de 'ROBOT' para 'OPENED'
                    // vai para a fila normalmente
                    var vm = this;
                    Swal.fire({
                        title: vm.$t('bs-are-you-sure'),
                        showCancelButton: true,
                        confirmButtonText: vm.$t('bs-create-chat'),
                        cancelButtonText: vm.$t('bs-cancel'),
                        heightAuto: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            vm.goToQuestionary();
                        }
                    });

                    finished = true; // final do fluxo
                    break;

                case 'create_ticket':
                    // transforma o chat em ticket
                    var vm = this;
                    Swal.fire({
                        title: vm.$t('bs-are-you-sure'),
                        showCancelButton: true,
                        confirmButtonText: vm.$t('bs-create-ticket'),
                        cancelButtonText: vm.$t('bs-cancel'),
                        heightAuto: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            vm.changeChatRobotToTicket().then(() => {
                                window.location.href = '/client-ticket';
                            }).catch((err) => {
                                Swal.fire({
                                    title: vm.$t('bs-error'),
                                    icon: 'error',
                                    heightAuto: false
                                })
                            });
                        }
                    });

                    finished = true; // final do fluxo
                    break;

                case 'transfer_department':
                    var vm = this;
                    Swal.fire({
                        title: vm.$t('bs-are-you-sure'),
                        showCancelButton: true,
                        confirmButtonText: vm.$t('bs-transfer'),
                        cancelButtonText: vm.$t('bs-cancel'),
                        heightAuto: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            vm.transferDepartment(element.inputTransferDepartment).then((department) => {
                                Swal.fire({
                                    title: vm.$t('bs-transferred'),
                                    icon: 'success',
                                    heightAuto: false
                                })
                                vm.setNewDepartmentInfo(department);
                                vm.checkIfIsRobot(department.id).then((is_robot) => {
                                    if (is_robot) {
                                        vm.getRobotMessages(department.id, 'ROBOT').then((first_message) => {
                                            vm.sendMessageRobot(first_message, 1, 1, 0)
                                        })
                                    } else {
                                        vm.goToQuestionary();
                                    }
                                });
                            }).catch((err) => {
                                Swal.fire({
                                    title: vm.$t('bs-error'),
                                    icon: 'error',
                                    heightAuto: false
                                })
                            });
                        }
                    });

                    finished = true; // final do fluxo
                    break;

                case 'solved':
                    // atualiza o status para 'RESOLVED' (antes abre a modal de avaliação, se caso o departamento tiver)
                    this.actionSidebarOptions({
                        status: 'EVALUATE-CLOSE',
                        message: this.$t("bs-do-you-want-to-rate-this-chat-and-end-it"),
                    });
                    finished = true; // final do fluxo
                    break;

                case 'unsolved':
                    // verifica se está setada a opção de sugerir criar um chat no final do fluxo
                    // se sim, manda uma mensagem sugerindo abrir um chat
                    // se nao apenas marca como 'CLOSED'
                    this.checkSuggestionCreateChat().then((create) => {
                        if (create) {
                            var vm = this;
                            Swal.fire({
                                title: this.$t('bs-would-you-like-to-talk-with-one-of-our'),
                                showCancelButton: true,
                                confirmButtonText: vm.$t('bs-yes'),
                                cancelButtonText: vm.$t('bs-cancel'),
                                heightAuto: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    vm.goToQuestionary();
                                }
                            });
                        } else {
                            this.actionSidebarOptions({
                                status: 'EVALUATE-CLOSE',
                                message: this.$t("bs-do-you-want-to-rate-this-chat-and-end-it"),
                            });
                        }
                    })
                    finished = true; // final do fluxo
                    break;

                default:
                    break;
            }

            // se o fluxo ainda não foi finalizado por uma das opções acima, segue normalmente.
            if (!finished) {

                var has_time = element.inputTime !== null || inputTime !== null; // verifico se a mensagem tem um time para envio das mensagens filhas;
                var push = has_time ? 0 : 1; // se tiver, nao da o push na hora
                var time = has_time ? Number(inputTime !== null ? inputTime : element.inputTime) : 0; // se tiver, seto o time nessa variavel que será enviada para o job PHP, se nao, recebe 0

                // se a opção possuir opções filhas,  disparo-as
                if (element.children && element.children.length > 0 && element.type !== 'to_jump') {
                    element.children.forEach(el => {
                        this.sendMessageRobot(el, 0, push, time)
                    });
                } else {
                    // se não
                    // executa o fim de fluxo padrão nos casos em que não existem mais opções
                    // ou nos casos em que a opção é do tipo pular (to_jump)
                    this.sendMessageRobot(this.robotEndOfFlowMessage, 0, push, time)
                }
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
    sendMessageRobot(message, is_bot, push, time) {
        return new Promise((resolve, reject) => {
            var vm = this;
            var url = 'chat-history/robot/store';
            axios.post(url, {
                id: vm.chat.number,
                message: message,
                is_bot: is_bot,
                time: time,
                old_module: true,
            })
            .then(({data}) => {
                if(data.success) {
                    if (push) {
                        vm.chat_history_robot.push(data.message);
                    }
                    resolve();
                }
            })
            .catch(err => {
                console.error(err);
            })
        });
    },
    setNewDepartmentInfo(department) {
        this.company_department = department.id;
        this.company_department_id = department.id;
        this.chat.companyDepartmentId = department.id;
        this.chat.department = department.name;
        this.department_name = department.name;
        this.chat.dep_type = department.type;
    },
    transferDepartment(company_department) {
        return new Promise((resolve, reject) => {
            var vm = this;
            axios.post('chat/client/transfer',{
                chat_id: vm.chat.chat_id,
                department_id: company_department,
            })
            .then(({data}) => {
                if (data.success) {
                    resolve(data.department);
                } else {
                    reject(data)
                }
            })
            .catch(err => {
                reject(err);
            })
        });
    },
    backToDepartments() {
      this.$loading(true);
      $("#modalQuestionary").modal("hide");
      $("#exampleModal").modal("show");
      this.$loading(false);
    },
    goToQuestionary() {
        this.checkOnlineAgentsConfig(this.company_department).then(() => {
            this.$loading(true);
            var department_id = this.chat.status == 'ROBOT' ? this.company_department : this.company_department.id
            axios
            .get("chat/get-active-chats-from-department", {
                params: {
                    company_department_id: department_id
                },
            })
            .then((response) => {
                let active_chats_from_department = response.data.active_chats_from_department;
                if (!active_chats_from_department) {
                    $("#exampleModal").modal("hide");
                    axios
                        .get(`department/get-quests/${department_id}`, {})
                        .then((response) => {
                            this.questions = [];
                            var result = response.data.result;
                            result.forEach(element => {
                                var dep_lang = element.language;
                                var user_lang = this.user.language.split('_')[1]
                                if (dep_lang == user_lang || !dep_lang) {
                                    this.questions.push(element);
                                }
                            });
                            this.$loading(false);
                            // se existem perguntas inicias
                            if (this.questions.length) {
                                $("#modalQuestionary").modal("show");
                            } else { // abre o chat direto
                                this.openEmptyChat(this.chat.status == 'ROBOT' ? 'ROBOT_TO_OPENED' : 'OPENED');
                            }
                        });
                } else {
                    this.$loading(false);
                    Swal.fire({
                        heightAuto: false,
                        title: this.$t("bs-caution"),
                        text: this.$t("bs-existing-chat-department"),
                    });
                }
            });
        })
        .catch((err) => {
            this.$loading(false);
            Swal.fire({
                heightAuto: false,
                title: this.$t("bs-caution"),
                text: this.$t("bs-no-attendant-online-at-the-moment"),
            });
        });
    },
    sendEvaluation() {
        this.$loading(true);
        if (this.evaluateFormValidated) {

            $("#evaluationModal").modal("hide");

            axios.post('chat/evaluation', {
                chat_id: this.chat.id,
                stars_atendent: this.stars_atendent,
                stars_service: this.stars_service,
                comment: this.evaluationComment,
            })
            .then((response) => {

                if (response.data.status) {

                    this.evaluationComment = "";

                    if (this.evaluate_and_close) {

                        axios.post(`chat/client/update-status`, {
                            id: this.chat.id,
                            chat: this.chat,
                            action: "RESOLVED",
                            company_department: this.company_department,
                            department: this.department_name,
                            agent: this.agent_name,
                            status: this.chat.status,
                        })
                        .then((response) => {
                            this.setSidebarOptions("RESOLVED");
                            this.chat.status =  "RESOLVED";
                            this.showSidebarRight = false;
                            this.$loading(false);
                            // window.location.reload(false);
                        });
                    }

                    this.$loading(false);

                } else {
                    Swal.fire({
                        heightAuto: false,
                        title: this.$t("bs-caution"),
                        text: this.$t("bs-error-submitting-review"),
                    });
                }

            });
        } else {
            Swal.fire({
                heightAuto: false,
                title: this.$t("bs-caution"),
                text: this.$t("bs-enter-at-least-one-field"),
            });
        }
    },
    loadDepartmentsByTimezone() {
        var vm = this;
        vm.company_department = null;
        vm.options = [];
        vm.$loading(true);
        vm.checkIfTheChatLimitPerClientIsExceeded().then(() => {
            vm.getOpenDepartments(vm.country_sys).then((departments) => {
                if (!departments.length) {
                    vm.getOpenDepartments('US').then((departments2) => {
                        if (!departments2.length) {
                            vm.$loading(false);
                            vm.noOpenDepartmentsAlert();
                        } else {
                            vm.setDepartments(departments2);
                        }
                    })
                } else {
                    vm.setDepartments(departments);
                }
            })
        }).catch((err) => {
            vm.$loading(false);
            Swal.fire({
                heightAuto: false,
                title: vm.$t("bs-caution"),
                text: err,
            });
        });
    },
    setDepartments(departments) {
        var vm = this;
        var deptsProcessed = 0;
        departments.forEach((element) => {
            vm.$loading(true);
            deptsProcessed++;
            vm.checkOnlineAgentsConfig(element).then(() => {
                vm.setDepartmentInfo(element).then(() => {
                    if(deptsProcessed === departments.length) {
                        vm.$loading(false);
                        $("#exampleModal").modal("show");
                    }
                })
            })
            .catch(() => {
                element.online = 0;
                vm.setDepartmentInfo(element).then(() => {
                    if(deptsProcessed === departments.length) {
                        vm.$loading(false);
                        $("#exampleModal").modal("show");
                    }
                })
            });
        });
    },
    getOpenDepartments(country) {
        return new Promise((resolve, reject) => {
            var vm = this;
            axios.get("/get-open-departments", {
                params: {
                    country: country,
                }
            })
            .then(({data}) => {
                resolve(data);
            })
        })
    },
    noOpenDepartmentsAlert() {
        var vm = this;
        Swal.fire({
            heightAuto: false,
            title: vm.$t("bs-caution"),
            text: vm.$t('bs-no-departments-open-at-this-time'),
            confirmButtonText: vm.$t('bs-create-ticket'),
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "client-ticket";
            }
        });
    },
    checkIfTheChatLimitPerClientIsExceeded() {
        return new Promise((resolve, reject) => {
            var vm = this;
            axios.get("chat/get-client-active-chats", {})
            .then(response => {
                var setting = '';
                if(JSON.parse(vm.$store.state.setting_chat)){
                  try {
                    setting = JSON.parse(vm.$store.state.setting_chat)[0].chatSimCli;
                  } catch (error) {
                    setting = vm.$store.state.setting_chat;
                  }
                }else{
                  setting = vm.$store.state.setting_chat;
                }

                if (setting == 0 || response.data.client_active_chats < setting) {
                    resolve();
                } else {
                    reject(vm.$t("bs-active-chat-limit-exceeded"));
                }
            })
        })
    },
    checkOnlineAgentsConfig(element) {
        var vm = this;
        return new Promise(function(resolve, reject) {
            if (typeof element === 'string' || element instanceof String) {
                vm.checkIfIsOnlineAgent(element).then((result) => {
                    if (result) {
                        var online_agents = vm.$store.state.online_users;
                        online_agents = online_agents.filter((u) => u.is_client !== 1);
                        online_agents = online_agents.filter((u) => u.status == 'online');

                        if (online_agents.length > 0) {
                            var online_agents_ids = [];
                            var itemsProcessed = 0;
                            online_agents.forEach(el2 => {
                                itemsProcessed++;
                                online_agents_ids.push(el2.hash_id)
                                if(itemsProcessed === online_agents.length) {
                                    vm.countOnlineAgentsByDeparment(online_agents_ids, element).then((count) => {
                                        if(count > 0) {
                                            resolve(element);
                                        } else {
                                            reject();
                                        }
                                    })
                                }
                            });

                        } else {
                            reject();
                        }
                    } else {
                        resolve(element);
                    }
                })
            } else if (element.openChatOnline && !element.hasActiveRobot) {
                var online_agents = vm.$store.state.online_users;
                online_agents = online_agents.filter((u) => u.is_client !== 1);
                online_agents = online_agents.filter((u) => u.status == 'online');

                if (online_agents.length > 0) {
                    var online_agents_ids = [];
                    var itemsProcessed = 0;
                    online_agents.forEach(el2 => {
                        itemsProcessed++;
                        online_agents_ids.push(el2.hash_id)
                        if(itemsProcessed === online_agents.length) {
                            vm.countOnlineAgentsByDeparment(online_agents_ids, element.id).then((count) => {
                                if(count > 0) {
                                    resolve(element);
                                } else {
                                    reject();
                                }
                            })
                        }
                    });

                } else {
                    reject();
                }
            } else {
                resolve(element);
            }
        })
    },
    checkIfIsOnlineAgent(department_id) {
        return new Promise((resolve, reject) => {
            var vm = this;
            axios.get('department/is-online-agent', {
                params: {
                    company_department_id: department_id,
                }
            })
            .then(({data}) => {
                resolve(data);
            })
        })
    },
    setDepartmentInfo(element) {
        return new Promise((resolve, reject) => {

            if (element.openChatOnline && !element.online) {
                var dep_schedule = this.$t('bs-closed');
            } else {
                var client_tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
                var opening_time = this.UTCtoClientTZ(element.openDateToUTC, client_tz);
                var closing_time = this.UTCtoClientTZ(element.closeDateToUTC, client_tz);
                var ot1 = opening_time.split(" ")[1].split(":")[0]; //01
                var ot2 = opening_time.split(" ")[1].split(":")[1]; //02
                var ct1 = closing_time.split(" ")[1].split(":")[0]; //03
                var ct2 = closing_time.split(" ")[1].split(":")[1]; //04
                var dep_schedule = `${ot1}:${ot2} - ${ct1}:${ct2}`; //01:02 - 03:04
            }

            this.options.push({
                id: element.id,
                name: this.$t(element.name),
                openChatOnline: element.openChatOnline,
                hasActiveRobot: element.hasActiveRobot,
                status: dep_schedule,
                type: element.type,
                $isDisabled: !element.online,
            });

            if (element.type == "builderall-mentor") {
                this.company_department = element;
            }

            resolve();
        })
    },
    countOnlineAgentsByDeparment(online_agents, department_id) {
        return new Promise((resolve, reject) => {
            axios.get('client/count-online-agents-by-department', {
                params: {
                    online_agents: online_agents,
                    department_id: department_id
                }
            })
            .then(({data}) => {
                if (data.success) {
                    resolve(data.count);
                }
            })
        })
    },
    formatDate(dia, mes, ano, hora) {
      return (
        ano +
        "-" +
        mes +
        "-" +
        dia +
        " " +
        hora.split(":")[0] +
        ":" +
        hora.split(":")[1] +
        ":" +
        hora.split(":")[2]
      );
    },
    UTCtoClientTZ(h, tz) {
      let h_format = moment(h, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD HH:mm:ss");
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
        timeZone: tz,
      });
      return moment(converted_time, "DD/MM/YYYY HH:mm:ss").format("YYYY-MM-DD HH:mm:ss");
    },
    UTCtoClientTZ2(h, tz) {
      let h_format = moment(h, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD HH:mm:ss");
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
        timeZone: tz,
      });
      var mt = require("moment-timezone");
      return mt(converted_time, "DD/MM/YYYY HH:mm:ss")
        .tz(tz)
        .locale(this.user.language)
        .fromNow();
    },
    formatMessageTime(h) {
      let h_format = moment(h, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD HH:mm:ss");
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
        .format("lll");
    },
    calculateWaitingTime(d, c, inactivityMessage, timewait) {
        if (this.chat.status !== 'ROBOT') {
            var moment = require("moment-timezone");
            var startDateTime = moment
                .tz(d, Intl.DateTimeFormat().resolvedOptions().timeZone)
                .toDate();
            var startStamp = startDateTime.valueOf();
            var newDate = new Date();
            var newStamp = newDate.getTime();
            var timer;
            var diff_0 = false;
            if (parseInt(timewait) > 0) {
                diff_0 = true;
                var hours = Math.floor(timewait / 60);
                var minutes = timewait % 60;
                if (hours < 10) {
                hours = "0" + hours;
                }
                if (minutes < 10) {
                minutes = "0" + minutes;
                }
                var time_settings = hours + ":" + minutes + ":00";
                var swal_title = this.$t("bs-all-of-our-attendants-are-busy");
                var swal_cancel = this.$t("bs-cancel");


            }
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
                if (diff_0 && time_settings == h + ":" + m + ":" + s) {
                    Swal.fire({
                    icon: "info",
                    text: swal_title,
                    allowOutsideClick: false,
                    heightAuto: false,
                    showCancelButton: true,
                    confirmButtonText: "Ok",
                    cancelButtonText: swal_cancel,
                    }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "client-ticket";
                    }
                    });
                }
                document.getElementById(c).innerHTML = h + ":" + m + ":" + s;
                }
            }
            setInterval(updateClock, 1000);
        }
    },
    nameWithLang({ name, status }) {
      return `${name} — [${status}]`;
    },
    openChat(item) {
      this.$loading(true);
      //pegar chat
      this.evaluate_and_close = false;
      if (item.status !== "OPENED") {
        this.agent_name = item.agent;
      }
      this.setSidebarOptions(item.status);
      this.company_department = item.company_department_id;
      this.department_name = item.department;
      this.chat.department = item.department;
      this.company_department_id = item.company_department_id;
      this.chat.companyDepartmentId = item.company_department_id;
      this.chat.id = item.chat_id;
      this.chat.chat_id = item.chat_id;
      this.chat.number = item.number;
      this.chat.status = item.status;
      this.chat.created_at = item.created_at;
      this.chat.settings = item.settings;
      this.chat.settingsJson = JSON.parse(item.settings);
      this.chat.dep_type = item.dep_type;
      this.chat.comp_user_comp_depart_id_current = item.comp_user_comp_depart_id_current;
      let settings = JSON.parse(item.settings);
      this.chat.timewait = settings["quant_limity"]["timewait"];
      this.chat.inactivityMessage = settings["quant_limity"]["inactivityMessage"];
      this.getChatHistory(item.chat_id);
      Echo.leave(`chat.${item.chat_id}`);
      Echo.join(`chat.${item.chat_id}`).listen("MessageSent", (event) => {
        if (!event.message.hidden_for_client && event.message.type !== 'ROBOT') {
            this.chat_history.push(event.message);
        } else if (event.message.type == 'ROBOT' && this.chat.status == 'ROBOT') {
            this.chat_history_robot.push(event.message);
        }
        if (event.message.employee) {
          this.agent_name = event.message.name;
        } else if (event.message.transferred_to_department) {
          this.agent_name = "";
          this.department_name = event.message.department_name;
        } else if (event.message.transferred_to_agent) {
          this.agent_name = event.message.agent_name;
          this.department_name = event.message.department_name;
        }
      });
      Echo.leave(`chat-status-changer.${item.chat_id}`);
      Echo.join(`chat-status-changer.${item.chat_id}`).listen(
        "ChatStatusChanger",
        (event) => {
          switch (event.item.status) {
            case "OPENED":
              this.chat.status = event.item.status;
              this.company_department_id = event.item.company_department_id;
              this.setSidebarOptions(event.item.status);
              this.getQueuePosition({
                chat_id: event.item.chat_id,
                company_department_id: event.item.company_department_id,
              });
              break;
            case "IN_PROGRESS":
              this.chat.status = event.item.status;
              this.setSidebarOptions(event.item.status);
              break;
            case "CLOSED":
              this.chat.status = event.item.status;
              this.setSidebarOptions(event.item.status);
              this.actionSidebarOptions(this.sidebarOptions.resolved);
              break;
            case "RESOLVED":
              this.chat.status = event.item.status;
              this.setSidebarOptions(event.item.status);
              this.actionSidebarOptions(this.sidebarOptions.evaluate);
              break;
            case "TICKET":
              this.showChat = false;
              Swal.fire({
                icon: "info",
                text: this.$t("bs-chat-was-turned-into-a-ticket"),
                allowOutsideClick: false,
                heightAuto: false,
                showCancelButton: false,
                confirmButtonText: this.$t("bs-access-tickets"),
                //cancelButtonText: "Não",
              }).then((result) => {
                this.getClientChats();
                if (result.isConfirmed) {
                  window.location.href = "client-ticket";
                }
              });
              break;
          }
        }
      );
      if (JSON.parse(item.settings).chat.showQueue) {
        this.getQueuePosition(item);
        Echo.leave(`queue.${this.company_id}`);
        Echo.join(`queue.${this.company_id}`).listen("QueueUpdated", (event) => {
          if (event.item.companyDepartmentId === this.company_department_id) {
            if (event.item.action === "splice") {
              if (event.item.chat_id !== item.chat_id) {
                if (
                  this.queue_position >
                  event.item.removed_chat_queue_position.original.queue_position
                ) {
                  this.queue_position--;
                }
              } else {
                this.queue_position = null;
              }
            }
          }
        });
      }
      this.newChat = false;
      this.showChat = true;
      this.checkOnlineAgentsByDepartment();
    },
    setSidebarOptions(status) {
      switch (status) {
        case "OPENED":
        case "ROBOT":
          this.sidebarOptions = {
            canceled: {
              title: this.$t("bs-cancel-chat"),
              status: "CANCELED",
              message: this.$t("bs-are-you-sure-you-want-to-cancel-the-chat"),
            },
          };
          break;
        case "IN_PROGRESS":
          this.sidebarOptions = {
            evaluate: {
              title: this.$t("bs-rate-chat-and-end"),
              status: "EVALUATE-CLOSE",
              message: this.$t("bs-do-you-want-to-rate-this-chat-and-end-it"),
            },
          };
          break;
        case "CLOSED":
          this.sidebarOptions = {
            evaluate: {
              title: this.$t("bs-rate-the-chat"),
              status: "EVALUATE",
              message: this.$t("bs-do-you-want-to-rate-this-chat"),
            },
          };
          break;
        case "RESOLVED":
          this.sidebarOptions = {
            evaluate: {
              title: this.$t("bs-rate-the-chat"),
              status: "EVALUATE",
              message: this.$t("bs-do-you-want-to-rate-this-chat"),
            },
          };
          break;
      }
    },
    actionSidebarOptions(option) {
        var vm = this;
        Swal.fire({
            title: option.message,
            heightAuto: false,
            showCancelButton: true,
            icon: "warning",
            confirmButtonText: vm.$t("bs-yes"),
            cancelButtonText: vm.$t("bs-no"),
            confirmButtonColor: "#3085d6",
        }).then((result) => {
            if (result.isConfirmed) {
                vm.$loading(true);
                if (option.status === "EVALUATE" || option.status === "EVALUATE-CLOSE") {
                    axios.get("chat/check-evaluation", {
                        params: {
                            chat_id: vm.chat.id,
                        },
                    })
                    .then((response) => {
                        if (!response.data.status) {
                            vm.clearEvaluationInputStatus();
                            axios.get("get-department-settings", {
                                params: {
                                    id: vm.company_department,
                                },
                            })
                            .then((res) => {
                                let settings = JSON.parse(res.data[0]["settings"]);
                                vm.show_attendant_evaluation = settings["evaluation"]["text_atten_active"];
                                vm.show_service_evaluation = settings["evaluation"]["text_serv_active"];
                                vm.show_comment_evaluation = settings["evaluation"]["text_comment_active"];
                                vm.type_evaluation = settings["evaluation"]["typeevaluation"] == null ? 'stars' : settings["evaluation"]["typeevaluation"];
                                if (vm.show_attendant_evaluation || vm.show_service_evaluation || vm.show_comment_evaluation) {
                                    $("#evaluationModal").modal("show");
                                    if (option.status === "EVALUATE-CLOSE") {
                                        vm.evaluate_and_close = true;
                                    }
                                    vm.showSidebarRight = false;
                                    vm.$loading(false);
                                } else {
                                    vm.showSidebarRight = false;
                                    vm.$loading(false);

                                    if (option.status === "EVALUATE-CLOSE") {
                                        axios.post(`chat/client/update-status`, {
                                            id: vm.chat.id,
                                            chat: vm.chat,
                                            action: "RESOLVED",
                                            company_department: vm.company_department,
                                            department: vm.department_name,
                                            agent: vm.agent_name,
                                            status: vm.chat.status,
                                        })
                                        .then(() => {
                                            vm.setSidebarOptions("RESOLVED");
                                            vm.chat.status = "RESOLVED";
                                            vm.showSidebarRight = false;
                                            vm.$loading(false);
                                            window.location.reload(false);
                                        });
                                    }
                                }
                            });
                        } else {
                            Swal.fire({
                                heightAuto: false,
                                title: vm.$t("bs-you-have-already-rated-this-chat"),
                            });
                            vm.showSidebarRight = false;
                            vm.$loading(false);
                        }
                    });
                } else {
                    axios.post(`chat/client/update-status`, {
                        id: vm.chat.id,
                        chat: vm.chat,
                        action: option.status,
                        company_department: vm.company_department,
                        department: vm.department_name,
                        agent: vm.agent_name,
                        status: vm.chat.status,
                    })
                    .then((response) => {
                        if (response.data.status) {
                            vm.setSidebarOptions(response.data.status);
                            vm.chat.status = response.data.status;
                            vm.showSidebarRight = false;
                            vm.$loading(false);

                            if (response.data.status === "RESOLVED") {
                                vm.actionSidebarOptions(vm.sidebarOptions.evaluate);
                            }

                            window.location.reload(false);
                        }
                    });
                }
            }
        });
    },
    clearEvaluationInputStatus() {
      this.show_attendant_evaluation = false;
      this.show_service_evaluation = false;
      this.show_comment_evaluation = false;
    },
    getClientChats() {
      this.$loading(true);
      axios.get("chat/get-client-chats", {}).then((response) => {
        this.chats = response.data.queue;
        Echo.leave(`client-queue.${this.company_id}.${this.user_client_id}`);
        Echo.join(`client-queue.${this.company_id}.${this.user_client_id}`).listen(
          "ClientQueueStatus",
          (event) => {
            if (event.item.action) {
              switch (event.item.action) {
                case "push":
                  this.chats.unshift(event.item);
                  break;
                case "splice":
                  let index = this.chats.findIndex(
                    (item) => item.chat_id === event.item.chat_id
                  );
                  if (index !== -1) {
                    this.chats.splice(index, 1);
                  }
                  break;
              }
            } else {
              /* ATUALIZA ORDEM DE CHATS NA TABELA ORDENADO POR ULTIMO RESPONDIDO DESC*/
              //recupero o chat do evento e encontro a index dele no objeto
              let index = this.chats.findIndex(
                (item) => item.chat_id === event.item.chat_id
              );
              if (index !== -1) {
                // atendente (agent_answered = true) cliente (client_answered = true)
                if (event.item.agent_answered) {
                  // se existir o nome do agent no evento recebido
                  if (event.item.agent) {
                    // atualizo o nome do atendente do chat e o comp_user_comp_depart_id_current do mesmo
                    this.chats[index].agent = event.item.agent;
                    this.chats[index].comp_user_comp_depart_id_current =
                      event.item.comp_user_comp_depart_id_current;
                      if (this.chat.chat_id == event.item.chat_id) {
                          this.chat.comp_user_comp_depart_id_current = event.item.comp_user_comp_depart_id_current;
                      }
                  }
                  // se foi o agente que mandou a mensagem, deixo o answered true para aparecer a tarja vermelha
                  this.chats[index].answered = 1;
                } else {
                  // se foi o cliente que mandou a mensagem, deixo o answered false para sumir a tarja vermelha
                  this.chats[index].answered = 0;
                }
                //atualizo o status do chat caso seja necessário
                if (event.item.status) {
                  this.chats[index].status = event.item.status;
                }
                //atualizo algumas informações caso o chat tenha sido transferido para outro departamento
                if (event.item.transferred_to_department) {
                  this.chats[index].agent = null;
                  this.chats[index].company_department_id =
                    event.item.company_department_id;
                  this.chats[index].department = event.item.department_name;
                }
                if (event.item.transferred_to_agent) {
                    this.chats[index].company_department_id = event.item.company_department_id;
                    this.chats[index].department = event.item.department_name;
                }
                // se o chat nao estiver na primeira posição
                if (index > 0) {
                  // crio uma variavel que recebe o chat temporariamente
                  let updatedChat = this.chats[index];
                  // removo o chat da posição atual no objeto
                  this.chats.splice(index, 1);
                  // adiciono o chat novamente na primeira posição do objeto
                  this.chats.unshift(updatedChat);
                }
              }
            }
          }
        );
      });
      this.$loading(false);
    },
    getChatHistory(id) {
      var vm = this;
      vm.$loading(true);
      axios
        .get("ticket-chat-answer/client/get-ticket-chat-answers", {
          params: {
            id: id,
            reference: "chat_id",
          },
        })
        .then((response) => {
          if (response.data.status) {
            vm.questionary = response.data.result;
            //console.log(vm.questionary);
          }
        });
      axios
        .get("chat-history/client/get-chat-history", {
          params: {
            id: id,
          },
        })
        .then((response) => {
          vm.chat.id = id;
          vm.chat_history_robot = [];
          vm.chat_history = [];

          response.data.forEach(element => {
              if (element.type == 'ROBOT') {
                  vm.chat_history_robot.push(element);
              } else {
                 vm.chat_history.push(element);
              }
          });
          vm.$loading(false);
        });
    },
    getQueuePosition(chat) {
      const api = `chat/get-queue-position/${chat.chat_id}/${chat.company_department_id}`;
      axios.get(api, {}).then(({ data }) => {
        this.queue_position = data.queue_position;
      });
    },
    getChatStatus(chat) {
      let status = "";
      switch (chat.status) {
        case "OPENED":
          status = this.$t("bs-in-queue");
          break;
        case "IN_PROGRESS":
          if (chat.answered) {
            status = this.$t("bs-answered");
          } else {
            status = this.$t("bs-in-progress");
          }
          break;
        case "CLOSED":
          status = this.$t("bs-closed");
          break;
        case "RESOLVED":
          status = this.$t("bs-resolved");
          break;
        case "CANCELED":
          status = this.$t("bs-canceled");
          break;
      }
      return status;
    },
    setMessageComponent(message) {
      if (message.type == "TEXT") {
        if (message.company_user_company_department_id) {
          return this.component["TEXT_RECEIVED"];
        } else {
          return this.component["TEXT_SENT"];
        }
      } else {
        return this.component[message.type];
      }
    },
    setMessageProps(message, index) {
      return {
        comp_user_comp_depart_id_current: this.chat.comp_user_comp_depart_id_current,
        message: this.chat_history[index],
        index: index,
        chat_history: this.chat_history,
        settings: this.chat.settingsJson,
        type: 'CHAT',
      };
    },

    setMessagePropsRobot(message, index) {
      return {
        message: message.content,
        index: index,
        chat_history: this.chat_history_robot,
      };
    },
    sendMessage() {
      if (this.chat.content.trim() !== "") {
        // var content = this.chat.content.replace(/(<([^>]+)>)/ig,'');
        axios
          .post(`chat-history/client/store`, {
            id: this.chat.id,
            type: this.chat.type,
            content: this.chat.content,
            company_user_company_department_id: this.chat
              .comp_user_comp_depart_id_current,
            is_client: true,
            company_department_id: this.company_department_id,
            department: this.department_name,
          })
          .then(({ data }) => {
            if (data.error) {
              this.chat.status = data.status;
            }
          });
      }
      if (this.files != "") {
        let formData = new FormData();
        for (var i = 0; i < this.files.length; i++) {
          let file = this.files[i];
          formData.append("files[" + i + "]", file);
        }
        formData.append("chat_id", this.chat.id);
        formData.append("extImages", this.extImages);
        formData.append("is_client", true);
        axios
          .post(`chat/client/upload`, formData, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
          .then(function () {
            console.log("SUCCESS!!");
          })
          .catch(function () {
            console.log("FAILURE!!");
          });
      }
      this.files = [];
      this.uploadComponent = [];
      this.chat.content = "";
      /*
        else {
            axios
            .post(`chat/store`, {
                client: this.user,
                company_department: this.company_department,
                content: this.chat.content,
                type: this.chat.type,
            })
            .then((response) => {
                this.openChat(response.data);
            });
        }
        */
    },
    paramsVerification() {
        if (this.session_dtype !== "null") {
            var session_dtype = JSON.parse(this.session_dtype);
            var checkout = false;
            var vip = true;

            session_dtype.forEach((element) => {
            if (element == "checkout") {
                checkout = true;
            } else if (element == "builderall-mentor") {
                vip = true;
            }
            });
            var open_modal = new URL(location.href).searchParams.get("open-departments");
            if (open_modal) {
            if (checkout && vip) {
                this.loadDepartmentsByTimezone();
            } else if (checkout) {
                this.checkout = true;
                this.checkoutIntegration();
            } else if (vip) {
                this.loadDepartmentsByTimezone();
            }
            }
        }

        if (localStorage.getItem("open_modal") === '1') {
            this.loadDepartmentsByTimezone();
            localStorage.removeItem("open_modal");
        }
    },
    checkoutIntegration() {
      this.company_department = {
        id: this.session_department_id,
        name: "Checkout",
        type: "checkout",
      };
      this.goToQuestionary();
    },
    joinDeletedChannel() {
        Echo.leave(`chat-ticket-delete.${this.company_id}`);
        Echo.join(`chat-ticket-delete.${this.company_id}`).listen("ChatTicketDelete", (event) => {
            if (event.item.type == 'CHAT') {
                if (this.showChat && this.chat.chat_id == event.item.id) {
                    Swal.fire({
                        title: this.$t('bs-this-chat-was-deleted'),
                        timer: 3000,
                        timerProgressBar: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload(false);
                        }
                        if (result.dismiss === Swal.DismissReason.timer) {
                            window.location.reload(false);
                        }
                    })

                } else if (!this.showChat) {
                    let index = this.chats.findIndex(
                        (item) => item.chat_id === event.item.id
                    );
                    if (index !== -1) {
                        this.chats.splice(index, 1);
                    }
                }
            }
        });
    }
  },
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style scoped>
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
.opened {
  color: #ffb244;
}
.in-progress {
  color: #00c38e;
}
.canceled {
  color: #ff4872;
}
.resolved {
  color: #0294ff;
}
.closed {
  color: #c0c3c6;
}
.answered {
  background: #ff4872;
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
.form-group .buttons small:hover {
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
.floatIcon{
  vertical-align: middle;
}
.mw-50 {
  max-width: 50px;
}
.mw-80 {
  padding: 0px;
  max-width: 80px !important;
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
textarea {
  font: normal normal normal 16px/20px Muli;
  letter-spacing: 0px;
  color: #707070;
  max-height: 159px;
  width: 100%;
  border: none;
  resize: none;
  background: transparent;
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
  .modal-footer {
        margin-bottom: 250px;
        text-align: center;
        justify-content: center;
    }
  .content-chat, .modal-content, .w-380 {
    zoom: 85%;
  }
  .w-380 {
    width: 100% !important;
  }
  #search {
    min-width: 84%;
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
    padding-right: 0px;
    padding-left: 0px;
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
.opacity-0 {
  opacity: 1 !important;
}
.ellipsis {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.table-responsive.mobile {
  position: absolute;
  right: 0;
  left: 0;
  min-width: 100%;
  max-width: 100%;
  zoom: 80%;
  top: 225px;
  bottom: 0;
  height: 100% !important;
}
.chat-client.mobile {
  margin-top: -20px !important;
}
.col-name {
  width: calc(75% - 80px);
}
.care {
  font: normal normal 800 15px/16px Muli;
  color: #434343 !important;
}
</style>
