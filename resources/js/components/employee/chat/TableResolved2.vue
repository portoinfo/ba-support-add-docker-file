<template>
<div class="w-100 h-100 p-0 m-0 non-selectable" v-if="!loading">
    <div class="table-responsive" :class="{'paginated': !non_paginated}">
        <table class="table table-sm table-bordered table-striped">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col">#</th>
                    <th scope="col" class="text-left">{{ $t("bs-client") }}</th>
                    <th scope="col" class="text-left">{{ $t("bs-operator") }}</th>
                    <th scope="col" class="text-left">{{ $t("bs-department") }}</th>
                    <th scope="col">{{ $t("bs-start") }}</th>
                    <th scope="col">{{ $t("bs-end") }}</th>
                    <th scope="col">{{ $t("bs-duration") }}</th>
                    <th scope="col">{{ $t("bs-email") }}</th>
                </tr>
            </thead>
            <tbody>
                 <template v-for="(q, i2) in chats.data">
                    <tr
                        :key="i2"
                        @click="callSetInfo(q)"
                        v-if="!company_department.id || (company_department.id && q.company_department_id === company_department.id)"
                        :class="{'selected': chat_show_info && chat_number_info == q.number}"
                        v-b-toggle.sidebar-right-info-2
                    >
                        <td>
                            <b-button
                                size="sm"
                                variant="light"
                                block
                                class="text-dark"
                                @click="callOpenChat(q)"
                            >
                                {{ $t("bs-view") }}
                            </b-button>
                        </td>
                        <th scope="row">#{{ q.number }}</th>
                        <td class="text-left td-name">
                            <gravatar
                                :email="q.email"
                                :status="$status.get(q.client_id)"
                                :size="gravatarSize"
                                :name="$t(q.name)"
                                color="primary"
                                class="mr-2 ml-1"
                                :ba_acct_data="q.builderall_account_data"
                            />
                            {{ $t(q.name) }}
                        </td>
                        <td class="text-left td-name">
                          <gravatar
                              :email="q.is_robot && q.comp_user_comp_depart_id_current == null ? 'grey' : q.operator_email"
                              :status="q.is_robot && q.comp_user_comp_depart_id_current == null ? 'false' : $status.get(q.operator_id)"
                              :size="gravatarSize"
                              :name="q.is_robot && q.comp_user_comp_depart_id_current == null ? $t('bs-robot') : $t(q.operator)"
                              class="mr-2 ml-1"
                          />
                          {{ q.is_robot && q.comp_user_comp_depart_id_current == null ? $t('bs-robot') :q.operator }}
                        </td>
                        <td class="text-left">
                            <!-- <img v-if="q.dep_type == 'builderall-mentor'" src="images/icons/vip.svg" alt="vip" height="20"> -->
                            <gravatar
                                :email="q.department"
                                status="false"
                                :size="gravatarSize"
                                :name="$t(q.department)"
                                class="mr-2 ml-1"
                                :ba_acct_data='`{"is_vip": ${q.dep_type == "builderall-mentor"}}`'
                            />
                            {{ $t(q.department) }}
                        </td>
                        <td>
                            {{ format_L_LT(q.created_at) }}
                        </td>
                        <td>
                            {{ format_L_LT(q.end) }}
                        </td>
                        <td>
                            {{ diffTime(q.created_at, q.end) }}
                        </td>
                        <td>{{ q.name == 'bs-user' ? '--' : q.email }}</td>
                    </tr>
                 </template>
            </tbody>
        </table>
    </div>
    <div class="table-pagination pl-3 pr-3" v-if="!non_paginated">
        <div class="row">
            <div class="col pr-0 pt-1 align-middle">
                <div>
                    <small class="results-pp" for="sb-inline">{{ $t('bs-per-page') }}</small>
                    <select v-model="$root.$refs.FullChat2.pp_selected">
                        <option
                            v-for="option in $store.state.pp_options"
                            :key="option.value"
                        >
                            {{ option.text }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="col pl-0">
                <pagination
                    :limit="2"
                    align="right"
                    size="small"
                    :data="chats"
                    :show-disabled="true"
                    @pagination-change-page="getChatsResolved"
                >
                    <span slot="prev-nav"><vue-material-icon name="arrow_back" :size="14" /></span>
                    <span slot="next-nav"><vue-material-icon name="arrow_forward" :size="14" /></span>
                </pagination>
            </div>
        </div>
    </div>
</div>
<table-loading v-else/>
</template>

<script>
export default {
  data() {
    return {
      ref: "tableResolved",
      tz: "",
      loading: Boolean,
    };
  },
  props: {
    setInfo: "",
    getChatsResolved: "",
    company_department: "",
    chats: "",
    hideOnSmall: Boolean,
    resetTable: "",
    openChat: "",
    user: "",
    countResolved: "",
    footerActiveChat: "",
    chat_number_info: "",
    chat_show_info: Boolean,
  },
  computed: {
        gravatarSize() {
            if (this.$root.$refs.FullChat2.isMobile) {
                return "40px";
            } else {
                return "30px";
            }
        },
        loaded() {
            if (this.chats.data) {
                return true;
            } else {
                return false;
            }
        },
        non_paginated() {
            var minOption = this.$store.state.pp_options[0].value;
            return this.chats.current_page == 1 && this.chats.data.length < this.$root.$refs.FullChat2.pp_selected && this.chats.data.length < minOption;
        }
    },
    watch: {
        loaded(newValue, oldValue) {
            if (newValue) {
                this.loading = false;
            }
        }
    },
  created() {
    this.$root.$refs.TableResolved = this;
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
