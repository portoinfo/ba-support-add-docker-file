<template>
  <b-col
    cols="2"
    class="ml-n3 card-info"
    :class="{ smaller: footerActiveChat, 'h-100': !footerActiveChat }"
  >
    <b-card
      id="info"
      show
      class="h-100 shadow chat-information"
      :title="$t('bs-chat-information')"
      v-if="chat.show"
    >
      <b-list-group>
        <b-list-group-item class="break-word">
          #{{ chat.number }}&nbsp;
          <b-button
            v-show="chat.number !== undefined"
            class="local-button local-btn-style1"
            size="sm"
            pill
            variant="outline-secondary"
            @click="copyToClipboard(chat.number, 'idChat')"
            :id="`id-chat-${_uid}`"
          >
            <i class="material-icons">content_copy</i>
          </b-button>
          <b-tooltip
            :target="`id-chat-${_uid}`"
            :show.sync="tooltips.idChat"
            triggers="manual"
            placement="right"
            :variant="tooltipVariant2"
            :container="''._uid"
          >
            {{ $t("bs-copied") }}
          </b-tooltip>
        </b-list-group-item>
      </b-list-group>

      <hr />
      <b-row id="answer-form" cols="1" class="mx-0">
        <b-col class="title-answer-form" id="answer-form-title"
          >{{ form.titulo }} - <i class="fa fa-question-circle" aria-hidden="true"></i
        ></b-col>
        <b-tooltip
          target="answer-form-title"
          triggers="hover"
          placement="bottom"
          variant="secondary"
          container="info"
        >
          {{ $t("bs-tooltip-answer") }}
        </b-tooltip>
        <b-col class="card-answer-form py-3">
          <b-row cols="1" no-gutters>
            <b-col v-for="(answer, index) in form.answers" :key="index">
              <b-row no-gutters>
                <b-col cols="12" class="answer-title"
                  ><b>{{ $t(answer.question) }}</b></b-col
                >
                <!-- <b-col cols="3" class="text-right answer-time">{{answer.hora}}</b-col> -->
                <b-col cols="12" class="answer-text">
                  {{ answer.answer }}&nbsp;
                  <b-button
                    v-show="answer.answer !== undefined"
                    class="local-button local-btn-style2"
                    size="sm"
                    pill
                    variant="outline-secondary"
                    @click="copyToClipboard(answer.answer, 'answers', index)"
                    :id="`answer-${_uid}-${index}`"
                  >
                    <i class="material-icons">content_copy</i>
                  </b-button>
                  <b-tooltip
                    :target="`answer-${_uid}-${index}`"
                    :show.sync="tooltips.answers[index]"
                    triggers="manual"
                    placement="right"
                    :variant="tooltipVariant2"
                    :container="''._uid"
                  >
                    {{ $t("bs-copied") }}
                  </b-tooltip>
                </b-col>
              </b-row>
            </b-col>
          </b-row>
        </b-col>
      </b-row>

      <hr />
      <div class="px-3 mb-3 break-word title-client">{{ $t("bs-chat-created-by") }}:</div>
      <b-list-group>
        <b-list-group-item class="break-word">
          <!-- <img src="/images/icons/chat/user.svg" /> -->
          <gravatar
            v-if="showData"
            :email="chat.client.email"
            :status="$status.get(chat.client.id)"
            size="25px"
            :name="$t(chat.client.name)"
            color="light"
            :ba_acct_data="chat.client.builderall_account_data"
          />
          &nbsp;{{ $t(chat.client.name) }}&nbsp;
          <b-button
            v-show="chat.client.name !== undefined"
            class="local-button local-btn-style1"
            size="sm"
            pill
            variant="outline-secondary"
            @click="copyToClipboard($t(chat.client.name), 'creatorName')"
            :id="`creator-name-${_uid}`"
          >
            <i class="material-icons">content_copy</i>
          </b-button>
          <b-tooltip
            :target="`creator-name-${_uid}`"
            :show.sync="tooltips.creatorName"
            triggers="manual"
            placement="right"
            :variant="tooltipVariant2"
            :container="''._uid"
          >
            {{ $t("bs-copied") }}
          </b-tooltip>
        </b-list-group-item>

        <b-list-group-item class="break-word">
          <img src="/images/icons/chat/email.svg" />
          &nbsp;{{ chat.client.email }}&nbsp;
          <b-button
            v-show="chat.client.email !== undefined"
            class="local-button local-btn-style1"
            size="sm"
            pill
            variant="outline-secondary"
            @click="copyToClipboard(chat.client.email, 'creatorEmail')"
            :id="`creator-email-${_uid}`"
          >
            <i class="material-icons">content_copy</i>
          </b-button>
          <b-tooltip
            :target="`creator-email-${_uid}`"
            :show.sync="tooltips.creatorEmail"
            triggers="manual"
            placement="right"
            :variant="tooltipVariant2"
            :container="''._uid"
          >
            {{ $t("bs-copied") }}
          </b-tooltip>
        </b-list-group-item>
        <b-list-group-item class="break-word">
          <img src="/images/icons/chat/users.svg" />
          &nbsp;{{ $t(chat.department) }}&nbsp;
          <b-button
            v-show="chat.department !== undefined"
            class="local-button local-btn-style1"
            size="sm"
            pill
            variant="outline-secondary"
            @click="copyToClipboard($t(chat.department), 'department')"
            :id="`department-${_uid}`"
          >
            <i class="material-icons">content_copy</i>
          </b-button>
          <b-tooltip
            :target="`department-${_uid}`"
            :show.sync="tooltips.department"
            triggers="manual"
            placement="right"
            :variant="tooltipVariant2"
            :container="''._uid"
          >
            {{ $t("bs-copied") }}
          </b-tooltip>
        </b-list-group-item>
      </b-list-group>

      <template v-if="chat.client.builderall_account_data && showData">
        <hr />
        <div class="px-3 mb-3 break-word title-client">{{ $t('bs-builderall-office-data') }}</div>
        <b-list-group>
          <b-list-group-item class="break-word" v-if="JSON.parse(chat.client.builderall_account_data)['is_vip']">
            <img src="images/icons/vip.svg" alt="vip" class="img_vip">
          </b-list-group-item>
          <b-list-group-item class="break-word">
            <span class="noselect ml-1 mr-3" style="color: #a5b9d5">#</span>
            &nbsp;{{ JSON.parse(chat.client.builderall_account_data)["id"] }}&nbsp;
            <b-button
              v-show="chat.client.email !== undefined"
              class="local-button local-btn-style1"
              size="sm"
              pill
              variant="outline-secondary"
              @click="
                copyToClipboard(
                  JSON.parse(chat.client.builderall_account_data)['id'],
                  'creatorAccountData'
                )
              "
              :id="`creator-builderall_account_data-${_uid}`"
            >
              <i class="material-icons">content_copy</i>
            </b-button>
            <b-tooltip
              :target="`creator-builderall_account_data-${_uid}`"
              :show.sync="tooltips.creatorAccountData"
              triggers="manual"
              placement="right"
              :variant="tooltipVariant2"
              :container="''._uid"
            >
              {{ $t("bs-copied") }}
            </b-tooltip>
          </b-list-group-item>

          <b-list-group-item class="break-word">
            <span class="noselect uuid" style="color: #a5b9d5">UUID&nbsp;</span>
            &nbsp;{{ JSON.parse(chat.client.builderall_account_data)["uuid"] }}&nbsp;
            <b-button
              v-show="chat.client.email !== undefined"
              class="local-button local-btn-style1"
              size="sm"
              pill
              variant="outline-secondary"
              @click="
                copyToClipboard(
                  JSON.parse(chat.client.builderall_account_data)['uuid'],
                  'creatorAccountData'
                )
              "
              :id="`creator-builderall_account_data-${_uid}`"
            >
              <i class="material-icons">content_copy</i>
            </b-button>
            <b-tooltip
              :target="`creator-builderall_account_data-${_uid}`"
              :show.sync="tooltips.creatorAccountData"
              triggers="manual"
              placement="right"
              :variant="tooltipVariant2"
              :container="''._uid"
            >
              {{ $t("bs-copied") }}
            </b-tooltip>
          </b-list-group-item>
        </b-list-group>
      </template>
      <hr />

      <b-list-group>
        <b-list-group-item class="break-word">
          <img src="/images/icons/chat/calendar.svg" />
          &nbsp;{{ format_L(chat.created_at) }}&nbsp;
          <b-button
            v-show="chat.created_at !== undefined"
            class="local-button local-btn-style1"
            size="sm"
            pill
            variant="outline-secondary"
            @click="copyToClipboard(format_L(chat.created_at), 'creationDate')"
            :id="`creation-date-${_uid}`"
          >
            <i class="material-icons">content_copy</i>
          </b-button>
          <b-tooltip
            :target="`creation-date-${_uid}`"
            :show.sync="tooltips.creationDate"
            triggers="manual"
            placement="right"
            :variant="tooltipVariant2"
            :container="''._uid"
          >
            {{ $t("bs-copied") }}
          </b-tooltip>
        </b-list-group-item>
        <b-list-group-item class="break-word">
          <img src="/images/icons/chat/clock-nine.svg" />
          &nbsp;{{ format_LT(chat.created_at) }}&nbsp;
          <b-button
            v-show="chat.created_at !== undefined"
            class="local-button local-btn-style1"
            size="sm"
            pill
            variant="outline-secondary"
            @click="copyToClipboard(format_LT(chat.created_at), 'creationHour')"
            :id="`creation-hour-${_uid}`"
          >
            <i class="material-icons">content_copy</i>
          </b-button>
          <b-tooltip
            :target="`creation-hour-${_uid}`"
            :show.sync="tooltips.creationHour"
            triggers="manual"
            placement="right"
            :variant="tooltipVariant2"
            :container="''._uid"
          >
            {{ $t("bs-copied") }}
          </b-tooltip>
        </b-list-group-item>
      </b-list-group>
      <!--
      <hr v-if="!showChat"/>

      <b-list-group v-if="!showChat">
        <b-list-group-item>
          <img src="/images/icons/chat/description.svg" />
          {{ chat.sideContent }}
        </b-list-group-item>
      </b-list-group>

      <hr />

      <b-list-group>
        <b-list-group-item class="break-word">
          <img src="/images/icons/chat/locale.svg" /> {{ chat.client.location }}
        </b-list-group-item>
        <b-list-group-item class="break-word">
          <img src="/images/icons/chat/zone.svg" /> {{ chat.client.browser }}
        </b-list-group-item>
        <b-list-group-item class="break-word">
          <img src="/images/icons/chat/settings.svg" /> {{ chat.client.so }}
        </b-list-group-item>
        <b-list-group-item class="break-word">
          <img src="/images/icons/chat/locale.svg" /> {{ chat.client.ip }}
        </b-list-group-item>
      </b-list-group>
      -->

      <hr class="mb-0 pb-0" />

      <b-list-group-item
        class="break-word cursor-pointer history m-0 pl-4 pt-2"
        @click="openClientHistory(chat.client.id ? chat.client.id : chat.client_id)"
      >
        <img src="/images/icons/chat/history.svg" />
        &nbsp;{{ $t("bs-view-customer-history") }}
        <!-- View customer history -->
      </b-list-group-item>

      <hr class="mt-0 pt-0" />

      <b-list-group class="mb-2">
        <b-list-group-item class="break-word">
          <i class="bbi bbi-zone bbi-18 mr-1"></i>
          &nbsp;{{ chat.client.browser }}&nbsp;
          <b-button
            v-show="
              chat.client.browser !== undefined &&
              chat.client.browser !== null &&
              chat.client.browser.trim() != ''
            "
            class="local-button local-btn-style1"
            size="sm"
            pill
            variant="outline-secondary"
            @click="copyToClipboard(chat.client.browser, 'userAgent')"
            :id="`user-agent-${_uid}`"
          >
            <i class="material-icons">content_copy</i>
          </b-button>
          <b-tooltip
            :target="`user-agent-${_uid}`"
            :show.sync="tooltips.userAgent"
            triggers="manual"
            placement="right"
            :variant="tooltipVariant2"
            :container="''._uid"
          >
            {{ $t("bs-copied") }}
          </b-tooltip>
        </b-list-group-item>
      </b-list-group>
      <!-- copy -->
      <div class="hidden-input">
        <textarea :ref="`input-${_uid}`"></textarea>
      </div>
    </b-card>
    <b-card
      id="info"
      show
      class="h-100 z80"
      :title="$t('bs-click-on-a-chat-to-view-the-information')"
      v-else
    />
  </b-col>
</template>
<script>
export default {
  data() {
    return {
      tz: "",
      form: {
        titulo: this.$t("bs-initial-form"),
        answers: [],
      },
      tooltipVariant2: "info",
      tooltips: {
        idChat: false,
        answers: [],
        creatorName: false,
        creatorEmail: false,
        creatorAccountData: false,
        department: false,
        creationDate: false,
        creationHour: false,
        userAgent: false,
      },
      showData: true,
    };
  },
  props: {
    chat: Object,
    showChat: Boolean,
    user: "",
    footerActiveChat: "",
    openClientHistory: "",
  },
  created() {
    this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
  },
  methods: {
    getAnswers(id) {
      let vm = this;
      axios
        .get("ticket-chat-answer/agent/get-ticket-chat-answers", {
          params: {
            id: id,
            reference: "chat_id",
          },
        })
        .then((response) => {
          if (response.data.status) {
            vm.$set(vm.form, "answers", response.data.result);
            vm.fillTooltipFlags();
          }
        });
    },
    toUTC(h) {
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

      return converted_time;
    },
    format_L(h) {
      let converted_time = this.toUTC(h);
      var mt = require("moment-timezone");
      return mt(converted_time, "DD/MM/YYYY HH:mm:ss")
        .tz(this.tz)
        .locale(this.user.language)
        .format("L");
    },
    format_LT(h) {
      let converted_time = this.toUTC(h);
      var mt = require("moment-timezone");
      return mt(converted_time, "DD/MM/YYYY HH:mm:ss")
        .tz(this.tz)
        .locale(this.user.language)
        .format("LT");
    },
    copyToClipboard: function (text, modalFlag, index = null) {
      const elem = this.$refs[`input-${this._uid}`];
      elem.value = text;
      elem.select();
      document.execCommand("copy");
      elem.value = "";
      this.openTooltip(modalFlag, index);
    },
    openTooltip(modalFlag, index = null) {
      if (index != null) {
        this.$set(this.tooltips[modalFlag], index, true);
      } else {
        this.tooltips[modalFlag] = true;
      }
      setTimeout(this.closeTootip, 2000, this, modalFlag, index);
    },
    closeTootip(vm, modalFlag, index = null) {
      if (index != null) {
        this.$set(vm.tooltips[modalFlag], index, false);
      } else {
        vm.tooltips[modalFlag] = false;
      }
    },
    fillTooltipFlags() {
      let len = this.form.answers.length;
      for (let i = 0; i < len; i++) {
        this.tooltips.answers.push(false);
      }
      //console.log(this.tooltips.answers);
    },
  },
  computed: {
    // watch the entire as a new object
    // por fazer referencia ao mesmo objeto
    // no watch deep newVal.chat_id era igual a oldVal.chat_id
    computedChat: function () {
      return JSON.parse(JSON.stringify(this.chat)); // copy object and remove reactivity
    },
    computedNumber: function () {
      return JSON.parse(JSON.stringify(this.chat.number)); // copy object and remove reactivity
    },
  },
  watch: {
    computedChat: {
      deep: true,
      handler: function (newVal, oldVal) {
        if (newVal.chat_id != oldVal.chat_id) {
          if (newVal.show) {
            this.getAnswers(newVal.chat_id);
          } else {
            this.$set(this.form, "answers", []);
            this.$set(this.tooltips, "answers", []);
          }
        }
      },
    },
    computedNumber: {
      deep: true,
      handler: function (newVal, oldVal) {
        this.showData = false;
        setTimeout(() => {
          this.showData = true;
        }, 4);
      },
    },
  },
};
</script>

<style scoped lang="scss">
.title-client,
#answer-form {
  margin-top: -1rem;
}
.title-client,
.title-answer-form {
  font-family: Muli;
  font-weight: 700;
  color: #707070;
  background-color: rgba(0, 0, 0, 0.03);
  padding-top: 12px;
  padding-bottom: 12px;
}
.card-answer-form {
  & > .row {
    border: 1px #ced4da solid;
    border-radius: 4px;
    background-color: white;
    min-height: 100px;
    & > .col {
      padding: 6px 4px;
      .answer-title {
        color: #333;
      }
      .answer-time {
        color: #6e6e6e;
      }
      .answer-text {
        color: #707070;
      }
    }
  }
}

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

.shadow {
  background: #ffffff 0% 0% no-repeat padding-box;
  box-shadow: 0px 0px 9px #26242424;
  border-radius: 5px;
  opacity: 1;
  border: none;
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

.card-info {
  min-width: 344px;
  max-width: 344px;
  position: absolute;
  padding-bottom: 115px;
  right: 0;
}

@media only screen and (max-width: 1367px) {
  .card-info {
    padding-bottom: 120px;
  }

  .smaller {
    height: calc(100% - 65px) !important;
  }
}

@media only screen and (max-width: 1279px) {
  .card-info {
    display: none;
  }
}

.smaller {
  height: calc(100% - 90px);
}

.card {
  background: #ffffff 0% 0% no-repeat padding-box;
  box-shadow: 0px 0px 9px #26242424;
  border-radius: 5px;
  opacity: 1;
  border: none;
}

.chat-information {
  zoom: 85%;
}

.chat-information::v-deep {
  overflow-y: auto !important;
  padding: 10px 0;
  width: 100% !important;
}

.chat-information::v-deep .card-body {
  padding-left: 0px;
  padding-right: 0px;
  height: 100% !important;
}
.break-word {
  word-wrap: break-word;
  word-break: break-word;
}
.hidden-input {
  width: 1px;
  height: 1px;
  max-width: 1px;
  max-height: 1px;
  overflow: hidden;
  opacity: 0;
}
.btn:focus {
  box-shadow: none !important;
}
.local-button {
  padding: 0.2rem 0.4rem !important;
  font-size: 0.63rem !important;
}

.local-btn-style1::v-deep {
  color: #a5b9d5 !important;
  border-color: #a5b9d5 !important;
  &:hover {
    color: #fff !important;
    background-color: #a5b9d5 !important;
  }
}

.local-btn-style2::v-deep {
  color: #bdbdbd !important;
  border-color: #bdbdbd !important;
  &:hover {
    color: #fff !important;
    background-color: #bdbdbd !important;
  }
}

.history {
  height: 44px;
  font-weight: bold !important;
}

.history:hover {
  background: #0080fc;
  color: whitesmoke !important;
}

.uuid {
    font-size: 12px;
}

.img_vip {
    width: fit-content !important;
    height: 20px !important;
}
</style>
