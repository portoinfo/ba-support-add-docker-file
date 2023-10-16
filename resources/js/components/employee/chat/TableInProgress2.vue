<template>
<div class="w-100 h-100 p-0 m-0 non-selectable" v-if="loading">
    <div class="table-responsive">
        <table class="table table-sm table-bordered table-striped">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col">#</th>
                    <th scope="col" class="text-left">{{ $t("bs-client") }}</th>
                    <th scope="col" class="text-left">{{ $t("bs-operator") }}</th>
                    <th scope="col" class="text-left">{{ $t("bs-department") }}</th>
                    <th scope="col">{{ $t("bs-duration") }}</th>
                    <th scope="col">{{ $t("bs-email") }}</th>
                    <th scope="col">{{ $t("bs-opening") }}</th>
                </tr>
            </thead>
            <tbody>
                <tr class="tr-title">
                    <td colspan="100%">
                        <span class="bs-ico" @click="hideShowMyChats()">
                            <template v-if="show_myChats">
                                &#xe5ce;
                            </template>
                            <template v-else>
                                &#xe5cf;
                            </template>
                        </span>
                        <span @click="hideShowMyChats()" class="title">{{ `${$t('bs-my-chats')} ${qtd_myChats}` }}</span>
                    </td>
                </tr>
                <template v-if="my_chats">
                    <template v-for="(chat, index1) in my_chats">
                        <tr
                            :key="index1"
                            @click="callSetInfo(chat)"
                            :class="{'selected': chat_show_info && chat_number_info == chat.number}"
                            v-if="!company_department.id || (company_department.id && chat.company_department_id === company_department.id)"
                            v-show="show_myChats"
                            v-b-toggle.sidebar-right-info-2
                        >
                            <td>
                                <b-button
                                    :id="chat.number"
                                    size="sm"
                                    variant="light"
                                    block
                                    class="text-dark"
                                    @click="callOpenChat(chat)"
                                >
                                    {{ $t("bs-chat") }}
                                </b-button>
                            </td>
                            <td>
                                <b-button
                                    size="sm"
                                    variant="light"
                                    block
                                    class="text-dark"
                                    @click="preview(chat)"
                                >
                                    {{ $t("bs-view") }}
                                </b-button>
                            </td>
                            <th scope="row">
                                <span
                                    class="bs-ico"
                                    :class="{
                                        'md-answered' : chat.answered,
                                        'md-inactive' : !chat.answered,
                                    }"
                                >
                                    feedback
                                </span>
                            </th>
                            <th scope="row">#{{ chat.number }}</th>
                            <td class="text-left td-name">
                            <gravatar
                                :email="chat.email"
                                :status="$status.get(chat.client_id)"
                                :size="gravatarSize"
                                :name="$t(chat.name)"
                                color="primary"
                                class="mr-2 ml-1"
                                :ba_acct_data="chat.builderall_account_data"
                            />
                            {{ $t(chat.name) }}
                            </td>
                            <td class="text-left td-name">
                                <gravatar
                                    :email="chat.operator_email"
                                    :status="$status.get(chat.operator_id)"
                                    :size="gravatarSize"
                                    :name="$t(chat.operator)"
                                    color="primary"
                                    class="mr-2 ml-1"
                                />
                                {{ chat.operator }}
                            </td>
                            <td class="text-left">
                                <!-- <img v-if="chat.dep_type == 'builderall-mentor'" src="images/icons/vip.svg" alt="vip" height="20"> -->
                                <gravatar
                                    :email="chat.department"
                                    status="false"
                                    :size="gravatarSize"
                                    :name="$t(chat.department)"
                                    class="mr-2 ml-1"
                                    :ba_acct_data='`{"is_vip": ${chat.dep_type == "builderall-mentor"}}`'
                                />
                                {{ $t(chat.department) }}
                            </td>
                            <td :id="'time-elapsed-progress-' + chat.chat_id">
                                {{
                                    calculateWaitingTime(
                                        UTCtoClientTZ(chat.created_at, tz),
                                        "time-elapsed-progress-" + chat.chat_id
                                    )
                                }}
                            </td>
                            <td>
                                {{ chat.name == 'bs-user' ? '--' : chat.email }}
                            </td>
                            <th scope="row">{{ UTCtoClientTZ2(chat.created_at, tz) }}</th>
                        </tr>
                    </template>
                </template>
                <tr v-if="!(my_chats && my_chats.length > 0)" v-show="show_myChats">
                    <td colspan="100%">{{ $t('bs-no-chats') }}</td>
                </tr>
            </tbody>
            <tbody v-if="!$store.state.filter_my_chats">
                <tr class="tr-title">
                    <td colspan="100%">
                        <span class="bs-ico" @click="hideShowOtherChats()">
                            <template v-if="show_otherChats">
                                &#xe5ce;
                            </template>
                            <template v-else>
                                &#xe5cf;
                            </template>
                        </span>
                        <span @click="hideShowOtherChats()" class="title">{{ `${$t('bs-other-chats')} ${qtd_OtherChats}` }}</span>
                    </td>
                </tr>
                <template v-if="other_chats">
                    <template v-for="(chat, index2) in other_chats">
                        <tr
                            :key="index2"
                            @click="callSetInfo(chat)"
                            :class="{'selected': chat_show_info && chat_number_info == chat.number}"
                            v-if="!company_department.id || (company_department.id && chat.company_department_id === company_department.id)"
                            v-show="show_otherChats"
                            v-b-toggle.sidebar-right-info-2
                        >
                            <td>
                                <b-button
                                    :id="chat.number"
                                    size="sm"
                                    variant="light"
                                    block
                                    class="text-dark"
                                    @click="callOpenChat(chat)"
                                >
                                    {{ $t("bs-chat") }}
                                </b-button>
                            </td>
                            <td>
                                <b-button
                                    size="sm"
                                    variant="light"
                                    block
                                    class="text-dark"
                                    @click="preview(chat)"
                                >
                                    {{ $t("bs-view") }}
                                </b-button>
                            </td>
                            <th scope="row">
                                <span
                                    class="bs-ico"
                                    :class="{
                                        'md-answered' : chat.answered,
                                        'md-inactive' : !chat.answered,
                                    }"
                                >
                                    feedback
                                </span>
                            </th>
                            <th scope="row">#{{ chat.number }}</th>
                            <td class="text-left td-name">
                            <gravatar
                                :email="chat.email"
                                :status="$status.get(chat.client_id)"
                                :size="gravatarSize"
                                :name="$t(chat.name)"
                                color="primary"
                                class="mr-2 ml-1"
                                :ba_acct_data="chat.builderall_account_data"
                            />
                            {{ $t(chat.name) }}
                            </td>
                            <td class="text-left td-name">
                            <gravatar
                                :email="chat.operator_email"
                                :status="$status.get(chat.operator_id)"
                                :size="gravatarSize"
                                :name="$t(chat.operator)"
                                color="primary"
                                class="mr-2 ml-1"
                            />
                            {{ chat.operator }}
                            </td>
                            <td class="text-left">
                                <!-- <img v-if="chat.dep_type == 'builderall-mentor'" src="images/icons/vip.svg" alt="vip" height="20"> -->
                                <gravatar
                                    :email="chat.department"
                                    status="false"
                                    :size="gravatarSize"
                                    :name="$t(chat.department)"
                                    class="mr-2 ml-1"
                                    :ba_acct_data='`{"is_vip": ${chat.dep_type == "builderall-mentor"}}`'
                                />
                                {{ $t(chat.department) }}
                            </td>
                            <td :id="'time-elapsed-progress-' + chat.chat_id">
                                {{
                                    calculateWaitingTime(
                                        UTCtoClientTZ(chat.created_at, tz),
                                        "time-elapsed-progress-" + chat.chat_id
                                    )
                                }}
                            </td>
                            <td>
                                {{ chat.name == 'bs-user' ? '--' : chat.email }}
                            </td>
                            <th scope="row">{{ UTCtoClientTZ2(chat.created_at, tz) }}</th>
                        </tr>
                    </template>
                 </template>
                 <tr v-if="!(other_chats && other_chats.length > 0)" v-show="show_otherChats">
                    <td colspan="100%">{{ $t('bs-no-chats') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<table-loading v-else/>
</template>

<script>
export default {
  data() {
    return {
      ref: "tableInProgress",
      tz: "",
      loading: Boolean,
      show_myChats: Boolean,
      show_otherChats: Boolean,
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
    chat_number_info: "",
    chat_show_info: Boolean,
    cucd: Array,
  },
  created() {
    if(localStorage.getItem("myChats") !== null) {
        this.show_myChats = JSON.parse(localStorage.getItem("myChats"));
    } else {
        this.show_myChats = true;
    }

    if(localStorage.getItem("otherChats") !== null) {
        this.show_otherChats = JSON.parse(localStorage.getItem("otherChats"));
    } else {
        this.show_otherChats = true;
    }

    this.callResetTable();
    this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
  },
  computed: {
    chats_in_progress: {
      get() {
        return this.$store.state.chats_in_progress;
      },
    },
    my_chats() {
        if (this.chats_in_progress.length) {
            var my_chats = [];
            this.chats_in_progress.forEach(chat => {
                let index = this.cucd.findIndex(item => item.company_department_id === chat.companyDepartmentId || item.company_department_id === chat.company_department_id);
                if (index !== -1) {
                    if (this.cucd[index].company_user_company_department_id == chat.comp_user_comp_depart_id_current) {
                        my_chats.push(chat);
                    }
                }
            });
            my_chats = my_chats.sort(function(a, b) {
                return parseFloat(a.number) - parseFloat(b.number);
            });
            return my_chats;
        }
    },
    other_chats() {
        if (this.chats_in_progress.length) {
            var other_chats = [];
            this.chats_in_progress.forEach(chat => {
                let index = this.cucd.findIndex(item => item.company_department_id === chat.companyDepartmentId || item.company_department_id === chat.company_department_id);
                if (index !== -1) {
                    if (this.cucd[index].company_user_company_department_id !== chat.comp_user_comp_depart_id_current) {
                        other_chats.push(chat);
                    }
                } else {
                    other_chats.push(chat);
                }
            });
            other_chats = other_chats.sort(function(a, b) {
                return parseFloat(a.number) - parseFloat(b.number);
            });
            return other_chats;
        }
    },
    qtd_myChats() {
        if (this.my_chats && this.my_chats.length > 0) {
            return `(${Number(this.my_chats.length)})`;
        } else {
            return `(${Number(0)})`
        }
    },
    qtd_OtherChats() {
        if (this.other_chats && this.other_chats.length > 0) {
            return `(${Number(this.other_chats.length)})`;
        } else {
            return `(${Number(0)})`
        }
    },
    gravatarSize() {
        if (this.$root.$refs.FullChat2.isMobile) {
            return "40px";
        } else {
            return "30px";
        }
    },
    loaded() {
        if (this.chats_in_progress.length) {
            return true;
        } else {
            return false;
        }
    }
  },
  watch: {
        loaded(newValue, oldValue) {
            if (newValue) {
                this.loading = false;
            }
        },
        show_myChats(newValue, oldValue) {
            localStorage.setItem("myChats", JSON.stringify(newValue));
        },
        show_otherChats(newValue, oldValue) {
            localStorage.setItem("otherChats", JSON.stringify(newValue));
        }
    },
  methods: {
    preview(chat) {
        this.$root.$refs.FullChat2.isPreview = true;
        this.$root.$refs.FullChat2.chatPreview = chat;
        this.$root.$refs.FullChat2.openClientHistory(chat.client_id);
    },
    hideShowMyChats() {
        if (this.show_myChats) {
            this.show_myChats = false;
        } else {
            this.show_myChats = true;
        }
    },
    hideShowOtherChats() {
        if (this.show_otherChats) {
            this.show_otherChats = false;
        } else {
            this.show_otherChats = true;
        }
    },
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
        try {
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
        } catch (error) {
            return h
        }
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
