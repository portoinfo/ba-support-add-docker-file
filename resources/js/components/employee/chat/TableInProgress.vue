<template>
  <b-col
    class="ml-n3 card-progress"
    :class="{
      'hide-small': hideOnSmall,
      smaller: footerActiveChat,
      'h-100': !footerActiveChat,
    }"
  >
    <div class="row">
      <div class="col-12">
        <slot></slot>
      </div>
    </div>
    <b-card show id="list" class="h-100 mt-2 z80">
      <table class="table table-borderless table-striped">
        <thead>
          <tr>
            <th scope="col" class="brlt">#</th>
            <th scope="col">{{ $t("bs-opening") }}</th>
            <th scope="col">{{ $t("bs-client") }}</th>
            <th scope="col">{{ $t("bs-duration") }}</th>
            <th scope="col">{{ $t("bs-department") }}</th>
            <th scope="col" class="brrt">{{ $t("bs-operator") }}</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="bg-white td-title" colspan="8">
              {{ $t("bs-in-progress") }}
            </td>
          </tr>
          <template v-for="(chat, i) in chats_in_progress">
            <tr
              @click="callSetInfo(chat)"
              v-if="
                !company_department.id ||
                (company_department.id &&
                  chat.company_department_id === company_department.id)
              "
              :key="i"
            >
              <td v-bind:class="{ answered: chat.answered }">
                <b-button size="sm" @click="callOpenChat(chat)">{{
                  $t("bs-chat").toUpperCase()
                }}</b-button>
                #{{ chat.number }}
              </td>
              <td v-bind:class="{ answered: chat.answered }">
                {{ UTCtoClientTZ2(chat.created_at, tz) }}
              </td>
              <td v-bind:class="{ answered: chat.answered }">
                {{ $t(chat.name) }}
              </td>
              <td
                :id="'time-elapsed-progress-' + chat.chat_id"
                v-bind:class="{ answered: chat.answered }"
              >
                {{
                  calculateWaitingTime(
                    UTCtoClientTZ(chat.created_at, tz),
                    "time-elapsed-progress-" + chat.chat_id
                  )
                }}
              </td>
              <td v-bind:class="{ answered: chat.answered }">
                <img
                  v-if="chat.dep_type == 'builderall-mentor'"
                  src="images/icons/vip.svg"
                  alt="vip"
                  height="20"
                />
                {{ $t(chat.department) }}
              </td>
              <td v-bind:class="{ answered: chat.answered }">
                {{ chat.operator }}
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </b-card>
  </b-col>
</template>

<script>
export default {
  data() {
    return {
      ref: "tableInProgress",
      tz: "",
    };
  },
  props: {
    setInfo: "",
    openChat: "",
    company_department: "",
    resetTable: "",
    hideOnSmall: Boolean,
    user: "",
    //countInProgress: "",
    footerActiveChat: "",
    url_prefix: "",
  },
  created() {
    this.callResetTable();
    this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
  },
  computed: {
    chats_in_progress: {
      get() {
        return this.$store.state.chats_in_progress;
      },
    },
  },
  methods: {
    LI(value) {
      if (value == null) {
        return "LI";
      }
      return value.substr(0, 2);
    },
    callResetTable() {
      this.resetTable(this.ref);
    },
    callSetInfo(chat) {
      this.setInfo(chat);
    },
    callOpenChat(chat) {
      this.openChat(chat);
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
        .calendar();
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
};
</script>

<style scoped>
.card {
  /*tirar*/
  box-shadow: 0px 0px 9px #26242424;
  border-radius: 5px;
  opacity: 1;
  border: none;
}

.answered {
  background: #ff4872;
  color: #f4f4f4;
}

#list .card-body {
  padding: 0px;
}

#list {
  overflow: auto;
}

tr {
  width: 100%;
  display: inline-table;
  table-layout: fixed;
}

table {
  height: 100%;
  display: -moz-groupbox;
  text-align: center;
  max-width: 100% !important;
  zoom: 90%;
}

tbody {
  height: 100%;
  width: 100%;
}

thead {
  background: #f7f8fc;
  border: none;
  font: normal normal bold 14px/26px Muli;
  letter-spacing: 0px;
  color: #333333;
  opacity: 1;
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

.infinite-message {
  color: "#6E6E6E";
  margin-top: 1px;
  margin-bottom: 1px;
}

td {
  border: 1px solid #dee3ea;
  border-top: none;
  height: 63px;
  vertical-align: middle;
  font: normal normal 600 16px/19px Lato;
  letter-spacing: 0px;
  color: #6e6e6e;
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
  width: 10px;
  height: 10px;
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

tbody tr:hover {
  background-color: #f7f8fc !important;
  cursor: pointer;
}

.card-progress {
  position: absolute;
  padding-bottom: 163px;
  /* left: 380px; */
  margin-left: 300px !important;
  margin-right: 330px !important;
  width: auto !important;
}

@media only screen and (max-width: 1367px) {
  .card-progress {
    padding-bottom: 165px;
  }
  .smaller {
    height: calc(100% - 67px) !important;
  }
}

@media only screen and (max-width: 1279px) {
  .card-progress {
    margin-right: 0px !important;
    left: auto;
    margin-left: 2px !important;
  }

  .hide-small {
    display: none;
  }

  table {
    min-width: 991px !important;
  }
}

@media only screen and (max-width: 576px) {
  .card-progress {
    width: calc(100% - 5px) !important;
  }

  table {
    min-width: 991px !important;
  }
}
.smaller {
  height: calc(100% - 90px);
}
</style>
