<template>
  <b-col
    class="ml-n3 card-queue"
    :class="{
      'hide-small': hideOnSmall,
      smaller: footerActiveChat,
      'h-100': !footerActiveChat,
    }"
  >
    <div class="q">
      <div class="col-12">
        <slot></slot>
      </div>
    </div>
    <b-card show id="list" class="h-100 mt-2 z80">
      <table class="table table-borderless table-striped">
        <thead>
          <tr>
            <th scope="col" class="brlt">#</th>
            <th scope="col">{{ $t("bs-client") }}</th>
            <th scope="col">{{ $t("bs-waiting-time") }}</th>
            <th scope="col">{{ $t("bs-opening") }}</th>
            <th scope="col" class="brrt">{{ $t("bs-department") }}</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="bg-white td-title" colspan="8">
              {{ $t("bs-in-queue") }} {{ countOnQueue ? "(" + countOnQueue + ")" : "" }}
            </td>
          </tr>
          <template v-for="(q, i) in queue">
            <tr
              @click="callSetInfo(q)"
              v-if="
                !company_department.id ||
                (company_department.id &&
                  q.company_department_id === company_department.id)
              "
              :key="i"
            >
              <td>
                <b-button
                  :id="q.number"
                  :disabled="onlyTheFirst(i, q)"
                  size="sm"
                  @click="callCatchChat(q)"
                  >{{ $t("bs-take").toUpperCase() }}</b-button
                >&nbsp;#{{ q.number }}
              </td>
              <td>{{ $t(q.name) }}</td>
              <td>
                <small :id="'time-elapsed-queue-' + q.chat_id" class="fc">
                  {{
                    calculateWaitingTime(
                      UTCtoClientTZ(q.created_at, tz),
                      "time-elapsed-queue-" + q.chat_id
                    )
                  }}
                </small>
              </td>
              <td>{{ UTCtoClientTZ2(q.created_at, tz) }}</td>
              <td>
                  <img v-if="q.dep_type == 'builderall-mentor'" src="images/icons/vip.svg" alt="vip" height="20">
                  {{ $t(q.department) }}
              </td>
            </tr>
          </template>
          <infinite-loading @infinite="getChatsOnQueue" spinner="spiral" ref="tableQueue">
            <!-- criar chave de tradução -->
            <div slot="no-more" class="mt-2 mb-2">
              <span :style="{ color: '#6E6E6E' }">{{ $t("bs-no-more-results") }}</span>
            </div>
            <!-- criar chave de tradução -->
            <div slot="no-results" class="mt-2 mb-2">
              <!--<span :style="{ color: '#6E6E6E' }">{{ $t("bs-no-results-found") }}</span>-->
            </div>
          </infinite-loading>
        </tbody>
      </table>
    </b-card>
  </b-col>
</template>
<script>
export default {
  data() {
    return {
      ref: "tableQueue",
      tz: "",
    };
  },
  props: {
    chat_admin: Number,
    chat_queue_full_control: Number,
    setInfo: "",
    catchChat: "",
    getChatsOnQueue: "",
    queue: "",
    company_department: "",
    resetTable: "",
    showTableComponent: "",
    hideOnSmall: Boolean,
    user: "",
    countOnQueue: "",
    footerActiveChat: "",
  },
  created() {
    this.callResetTable();
    this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
  },
  methods: {
    onlyTheFirst(i, item) {
        if (item.dep_type == 'builderall-mentor') {
            return false;
        } else if (this.chat_admin) {
            return false;
        } else if (this.chat_queue_full_control) {
            return false;
        } else if (i !== 0) {
            return true;
        }
    },
    callResetTable() {
      this.resetTable(this.ref);
    },
    callSetInfo(q) {
      this.setInfo(q, true);
    },
    callCatchChat(q) {
      document.getElementById(q.number).disabled = true;
      if (window.location.pathname == "/chat") {
        this.$root.$refs.ChatBody.catchChat(q);
      } else if (window.location.pathname == "/full-chat") {
        this.$root.$refs.FullChat.catchChat(q);
      }
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
  border: none;
  font: normal normal 800 12px/16px Muli;
  letter-spacing: 0.42px;
  color: #434343 !important;
  text-transform: uppercase;
  opacity: 1;
  width: 80px;
  padding: 10px;
}

table button:disabled {
  background: #e2e2e2 0% 0% no-repeat padding-box !important;
  opacity: 0.4;
  cursor: not-allowed;
}

table button:hover {
  background-color: #6e6e6e !important;
  color: #e2e2e2 !important;
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

.card-queue {
  position: absolute;
  padding-bottom: 163px;
  /* left: 380px; */
  margin-left: 300px !important;
  margin-right: 330px !important;
  width: auto !important;
}

@media only screen and (max-width: 1367px) {
  .card-queue {
    padding-bottom: 165px;
  }

  .smaller {
    height: calc(100% - 67px) !important;
  }
}

@media only screen and (max-width: 1279px) {
  .card-queue {
    margin-right: 0px !important;
    left: auto;
    margin-left: 2px !important;
  }

  .hide-small {
    display: none;
  }

  table {
    min-width: 636px !important;
  }
}
tbody tr:hover {
  background-color: #f7f8fc !important;
  cursor: pointer;
}

@media only screen and (max-width: 576px) {
  .card-queue {
    width: calc(100% - 5px) !important;
  }

  table {
    min-width: 636px !important;
  }
}

.fc {
  font: normal normal 600 16px/19px Lato;
  letter-spacing: 0px;
  color: #6e6e6e;
}

.smaller {
  height: calc(100% - 90px);
}
</style>
