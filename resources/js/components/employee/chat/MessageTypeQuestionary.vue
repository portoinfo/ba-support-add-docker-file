<template>
  <div>
    <!-- <b-list-group-item class="msg d-flex justify-content-between align-items-center">
      <b-row class="w-100">
        <b-col>
          <h2 class="event">
            {{ `${ $t(chat.client) !== undefined ? $t(chat.client.name) : $t(chat.name_created) } : ${$t("bs-answered-the-questionnaire")}` }}
          </h2>
        </b-col>
        <b-col class="msg-hour">
        </b-col>
      </b-row>
    </b-list-group-item>
    <div v-for="(row, index) in questionary" :key="index">
      <b-list-group-item class="msg d-flex justify-content-between align-items-center">
        <b-row class="w-100">
          <b-col>
            <h2 class="message-name">
              {{ $t(chat.department) }}
            </h2>
          </b-col>
          <b-col class="msg-hour">
          </b-col>
          <b-col cols="12" class="content">
            {{ $t(row.question) }}
          </b-col>
        </b-row>
      </b-list-group-item>
      <b-list-group-item class="msg d-flex justify-content-between align-items-center">
        <b-row class="w-100">
          <b-col>
            <h2 class="message-name client">
              {{ $t(chat.client) !== undefined ? $t(chat.client.name) : $t(chat.name_created) }}
            </h2>
          </b-col>
          <b-col class="msg-hour">
          </b-col>
          <b-col cols="12" class="content">
            {{ row.answer }}
          </b-col>
        </b-row>
      </b-list-group-item>
    </div> -->
    <div class="grid-container1">
        <div class="item3 pt-3 pb-3 pl-5">
            <center>
                {{ `${ $t(chat.client) !== undefined ? $t(chat.client.name) : $t(chat.name_created) } : ${$t("bs-answered-the-questionnaire")}` }}
            </center>
        </div>
        <div class="item4">{{ formatTime(chat.created_at) }}</div>
    </div>

    <div v-for="(row, index) in questionary" :key="index">

        <div
            class="grid-container2"
        >
            <div class="item1">
                {{ chat.department }}
            </div>
            <div class="item2">
                <gravatar
                    :email="chat.department"
                    :status="'false'"
                    size="32px"
                    :name="$t(chat.department)"
                />
            </div>
            <div class="item3 pr-1" v-linkified>
                {{ $t(row.question) }}
            </div>
            <div class="item4">{{ formatTime(chat.created_at) }}</div>
        </div>

        <div
            class="grid-container2 client"
        >
            <div class="item1">
                {{ $t(chat.client.name) }}
            </div>
            <div class="item2">
                <gravatar
                    :email="chat.client.email"
                    :status="$status.get(chat.client.id)"
                    size="32px"
                    :name="$t(chat.client.name)"
                    :ba_acct_data="chat.client.builderall_account_data"
                />
            </div>
            <div class="output ql-snow pa-0" v-if="isRichText(row.answer)" v-linkified>
                <div class="ql-editor pa-0" v-html="row.answer"></div>
            </div>
            <div class="item3 pr-1" v-else v-linkified>
                {{ row.answer }}
            </div>
            <div class="item4">{{ formatTime(chat.created_at) }}</div>
        </div>

    </div>

  </div>
</template>

<script>
export default {
  data() {
    return {
      tz: "",
    };
  },
  created() {
    this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
  },
  props: {
    questionary: Array,
    chat: Object,
    formatTime: "",
  },
  methods: {
    isRichText(str) {
        let tag         = str.slice(0, 1) == '<'    && str.slice(-1) == '>';
        let paragraph   = str.slice(0, 2) == '<p'   || str.slice(-4) == '</p>';
        let list        = str.slice(0, 3) == '<ul'  || str.slice(-5) == '</ul>';
        let code        = str.slice(0, 4) == '<pre' || str.slice(-6) == '</pre>';
        
        return tag && (paragraph || list || code);
    },
    UTCtoClientTZ(h) {
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
      return moment(converted_time, "DD/MM/YYYY HH:mm:ss").format("HH:mm");
    },
    format(d) {
      var moment = require("moment-timezone");
      let str = d;
      let date = moment(str);
      return date.format("YYYY-MM-DD HH:mm:ss");
    },
  },
};
</script>

<style scoped>
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

a {
    font-style: italic !important;
}

.grid-container2.client .item1 {
    color: #333333;
}

@media screen and (max-width: 992px) {
    .grid-container2 .item3 {
        font-size: 16px;
    }
}

</style>
