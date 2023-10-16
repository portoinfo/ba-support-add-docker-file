<template>
  <b-col class="ml-n3 card-progress" :class="{ 'hide-small': hideOnSmall, 'smaller': footerActiveChat, 'h-100': !footerActiveChat }">
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
            <th scope="col">{{ $t("bs-client") }}</th>
            <th scope="col">{{ $t("bs-department") }}</th>
            <th scope="col">{{ $t("bs-operator") }}</th>
            <th scope="col">{{ $t("bs-start") }}</th>
            <th scope="col">{{ $t("bs-end") }}</th>
            <th scope="col" class="brrt">{{ $t("bs-duration") }}</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="bg-white td-title" colspan="8">{{ $t("bs-lost-s") }}</td>
          </tr>
          <template v-for="(chat, index) in chats">
            <tr
              @click="callSetInfo(chat)"
              v-if="
                !company_department.id ||
                (company_department.id &&
                  chat.company_department_id === company_department.id)
              "
              :key="index"
            >
              <td v-bind:class="{ answered: chat.answered }">
                <b-button size="sm" @click="callOpenChat(chat)">{{
                  $t("bs-to-see").toUpperCase()
                }}</b-button>&nbsp;#{{chat.number}}
              </td>
              <td v-bind:class="{ answered: chat.answered }">
                      {{ $t(chat.name) }}
              </td>
              <td v-bind:class="{ answered: chat.answered }">
                  <img v-if="chat.dep_type == 'builderall-mentor'" src="images/icons/vip.svg" alt="vip" height="20">
                  {{ $t(chat.department) }}</td>
              <td v-bind:class="{ answered: chat.answered }">
                      {{ chat.operator }}
              </td>
              <td v-bind:class="{ answered: chat.answered }">
                {{ format_L_LT(chat.created_at) }}
              </td>
              <td v-bind:class="{ answered: chat.answered }">
                {{ format_L_LT(chat.end) }}
              </td>
              <td v-bind:class="{ answered: chat.answered }">
                {{ diffTime(chat.created_at, chat.end) }}
              </td>
            </tr>
          </template>
          <infinite-loading
            @infinite="getChatsCanceled"
            spinner="spiral"
            ref="tableCanceled"
          >
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
      ref: "tableCanceled",
      tz: "",
    };
  },
  props: {
    setInfo: "",
    getChatsCanceled: "",
    company_department: "",
    chats: "",
    hideOnSmall: Boolean,
    resetTable: "",
    openChat: "",
    user: "",
    countCanceled: "",
    footerActiveChat: ""
  },
  created() {
    this.callResetTable();
    this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
  },
  methods: {
    callResetTable() {
      this.resetTable(this.ref);
    },
    callSetInfo(chat) {
      this.setInfo(chat);
    },
    callOpenChat(chat) {
      this.openChat(chat);
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
    format_L_LT(h) {
      let converted_time = this.toUTC(h);
      var mt = require("moment-timezone");
      return (
        mt(converted_time, "DD/MM/YYYY HH:mm:ss")
          .tz(this.tz)
          .locale(this.user.language)
          .format("L") +
        " - " +
        mt(converted_time, "DD/MM/YYYY HH:mm:ss")
          .tz(this.tz)
          .locale(this.user.language)
          .format("LT")
      );
    },
    diffTime(t1, t2) {
      var ms = moment(t1, "YYYY-MM-DD HH:mm:ss").diff(moment(t2, "YYYY-MM-DD HH:mm:ss"));
      var d = moment.duration(ms);
      var days = d.days();
      var h = d.hours();
      var m = d.minutes();
      var s = d.seconds();
      days = days * -1;
      h = h * -1;
      m = m * -1;
      s = s * -1;

      if (h < 10) {
        h = "0" + h;
      }
      if (m < 10) {
        m = "0" + m;
      }
      if (s < 10) {
        s = "0" + s;
      }
      if (days > 0) {
        return days + ":" + h + ":" + m + ":" + s;
      } else {
        return h + ":" + m + ":" + s;
      }
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
