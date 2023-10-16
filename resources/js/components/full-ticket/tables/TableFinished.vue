<template>
<div class="w-100 h-100 p-0 m-0 non-selectable" v-if="!loading">
    <div class="table-responsive" :class="{'paginated': !non_paginated}">
        <table class="table table-sm table-bordered table-striped">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col" class="caret" :style="{ 'color': dc.id }" @click="orderTickets('id')">
                        #
                        <i v-if="storageActive == undefined || storageOrder != 'id'" class="fa fa-fw fa-sort"></i>
                        <i v-else-if="storageActive" class="fa fa-fw fa-sort-asc"></i>
                        <i v-else-if="!storageActive" class="fa fa-fw fa-sort-desc"></i>
                        </th>
                    <th scope="col">{{ $t("bs-status") }}
                    </th>
                    <th scope="col" class="caret text-left" :style="{ 'color': dc.name_created }" @click="orderTickets('name_created')">
                        {{ $t("bs-client") }}
                        <i v-if="storageActive == undefined || storageOrder != 'name_created'" class="fa fa-fw fa-sort"></i>
                        <i v-else-if="storageActive" class="fa fa-fw fa-sort-asc"></i>
                        <i v-else-if="!storageActive" class="fa fa-fw fa-sort-desc"></i>
                    </th>
                    <th scope="col" class="caret text-left" :style="{ 'color': dc.name }" @click="orderTickets('name')">
                        {{ $t("bs-operator") }}
                        <i v-if="storageActive == undefined || storageOrder != 'name'" class="fa fa-fw fa-sort"></i>
                        <i v-else-if="storageActive" class="fa fa-fw fa-sort-asc"></i>
                        <i v-else-if="!storageActive" class="fa fa-fw fa-sort-desc"></i>
                    </th>
                    <th scope="col" class="caret text-left" :style="{ 'color': dc.department }" @click="orderTickets('department')">
                        {{ $t("bs-department") }}
                        <i v-if="storageActive == undefined || storageOrder != 'department'" class="fa fa-fw fa-sort"></i>
                        <i v-else-if="storageActive" class="fa fa-fw fa-sort-asc"></i>
                        <i v-else-if="!storageActive" class="fa fa-fw fa-sort-desc"></i>
                    </th>
                    <th scope="col" class="caret" :style="{ 'color': dc.created_at }" @click="orderTickets('created_at')">
                        {{ $t("bs-start") }} 
                        <i v-if="storageActive == undefined || storageOrder != 'created_at'" class="fa fa-fw fa-sort"></i>
                        <i v-else-if="storageActive" class="fa fa-fw fa-sort-asc"></i>
                        <i v-else-if="!storageActive" class="fa fa-fw fa-sort-desc"></i>
                    </th>
                    <th scope="col" class="caret" :style="{ 'color': dc.end }" @click="orderTickets('end')">
                        {{ $t("bs-end")}} 
                        <i v-if="storageActive == undefined || storageOrder != 'end'" class="fa fa-fw fa-sort"></i>
                        <i v-else-if="storageActive" class="fa fa-fw fa-sort-asc"></i>
                        <i v-else-if="!storageActive" class="fa fa-fw fa-sort-desc"></i>
                    </th>
                    <th scope="col" class="caret">
                        {{ $t("bs-duration")}}
                    </th>
                    <th scope="col" class="caret" :style="{ 'color': dc.email_created }" @click="orderTickets('email_created')">
                        {{ $t("bs-email") }}
                        <i v-if="storageActive == undefined || storageOrder != 'email_created'" class="fa fa-fw fa-sort"></i>
                        <i v-else-if="storageActive" class="fa fa-fw fa-sort-asc"></i>
                        <i v-else-if="!storageActive" class="fa fa-fw fa-sort-desc"></i>
                    </th>
                </tr>
            </thead>
            <tbody>
                 <template v-for="(row, i2) in chats.data">
                    <tr
                        :key="i2"
                        v-if="!company_department.id || (company_department.id && row.company_department_id === company_department.id)"
                        :class="{'selected': chat_show_info && chat_number_info == row.number}"
                    >
                        <td>
                            <b-button
                                size="sm"
                                variant="light"
                                block
                                class="text-dark"
                                @click="callSetInfo(row); callOpenChat(row)"
                            >
                                {{ $t("bs-view") }}
                            </b-button>
                        </td>
                        <th scope="row" @click="callSetInfo(row)" v-b-toggle.sidebar-right-info-2>#{{ row.number }}</th>
                        <td @click="callSetInfo(row)" v-b-toggle.sidebar-right-info-2>{{ filterStatus(row.status) }}</td>
                        <td class="text-left td-name" @click="callSetInfo(row)" v-b-toggle.sidebar-right-info-2>
                            <gravatar
                                :email="row.email_created"
                                :status="$status.get(row.id_created)"
                                :size="gravatarSize"
                                :name="$t(row.name_created)"
                                color="primary"
                                class="mr-2 ml-1"
                                :ba_acct_data="row.builderall_account_data"
                            />
                            {{ $t(row.name_created) }}
                        </td>
                        <td class="text-left td-name" @click="callSetInfo(row)" v-b-toggle.sidebar-right-info-2>
                          <gravatar
                              :email="row.email"
                              :status="$status.get(row.operator_id)"
                              :size="gravatarSize"
                              :name="$t(row.name)"
                              color="primary"
                              class="mr-2 ml-1"
                          />
                          {{ row.name }}
                        </td>
                        <td class="text-left" @click="callSetInfo(row)" v-b-toggle.sidebar-right-info-2>
                            <!-- <img v-if="row.department_type == 'builderall-mentor'" src="images/icons/vip.svg" alt="vip" height="20"> -->
                            <gravatar
                                :email="row.department"
                                status="false"
                                :size="gravatarSize"
                                :name="$t(row.department)"
                                class="mr-2 ml-1"
                                :ba_acct_data='`{"is_vip": ${row.department_type == "builderall-mentor"}}`'
                            />
                            {{ $t(row.department) }}
                        </td>
                        <td @click="callSetInfo(row)" v-b-toggle.sidebar-right-info-2>
                            {{ format_L_LT(row.created_at) }}
                        </td>
                        <td @click="callSetInfo(row)" v-b-toggle.sidebar-right-info-2>
                            {{ format_L_LT(row.end) }}
                        </td>
                        <td @click="callSetInfo(row)" v-b-toggle.sidebar-right-info-2>
                            {{ diffTime(row.created_at, row.end) }}
                        </td>
                        <td @click="callSetInfo(row)" v-b-toggle.sidebar-right-info-2>{{ row.name_created == 'bs-user' ? '--' : row.email_created }}</td>
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
                    <select v-model="$root.$refs.FullTicket2.pp_selected">
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
                    @pagination-change-page="getChatsFinished"
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
            ref: "tableFinished",
            tz: "",
            loading: Boolean,
            storageActive: localStorage.getItem("orderTable") ? JSON.parse(localStorage.getItem("orderTable")).isActive: true,
            storageOrder: localStorage.getItem("orderTable") ? JSON.parse(localStorage.getItem("orderTable")).order: null,
            dc: {
                id: '',
                name: '',
                name_created: '',
                department: '',
                created_at: '',
                end: '',
                email_created: '',
            },
        };
    },
  props: {
    setInfo: "",
    getChatsFinished: "",
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
            if (this.$root.$refs.FullTicket2.isMobile) {
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
            return this.chats.current_page == 1 && this.chats.data.length < this.$root.$refs.FullTicket2.pp_selected && this.chats.data.length < minOption;
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
    this.aplicColor(null);
  },
  methods: {
    orderTickets(order){
        this.aplicColor(order);
        if(localStorage.getItem("orderTable") != null){
            if(JSON.parse(localStorage.getItem("orderTable")).order == order){
                this.storageActive = !this.storageActive;
            }else{
                this.storageActive = false;
            }
        }else{
            this.storageActive = false;
        }

        var my_object = {
            isActive: this.storageActive,
            order: order,
        };

        this.storageOrder = order;
        localStorage.setItem("orderTable", JSON.stringify(my_object));
        this.$emit('orderTickets', 'FINISHED');
    },
    aplicColor(order){
        if(order == null){
            if(localStorage.getItem("orderTable")){
                order = this.storageOrder;
            }
        }

        this.dc.id = '#444';
        this.dc.name = '#444';
        this.dc.name_created = '#444';
        this.dc.department = '#444';
        this.dc.created_at = '#444';
        this.dc.end = '#444';
        this.dc.email_created = '#444';

        if(order == 'id'){this.dc.id = '#0080fc';}
        if(order == 'name'){this.dc.name = '#0080fc';}
        if(order == 'name_created'){this.dc.name_created = '#0080fc';}
        if(order == 'department'){this.dc.department = '#0080fc';}
        if(order == 'created_at'){this.dc.created_at = '#0080fc';}
        if(order == 'end'){this.dc.end = '#0080fc';}
        if(order == 'email_created'){this.dc.email_created = '#0080fc';}
    },
    filterStatus(str) {
        if (str == "OPENED") {
            return this.$t("bs-opened");
        } else if (str == "IN_PROGRESS") {
            return this.$t("bs-in-progress");
        } else if (str == "CLOSED") {
            return this.$t("bs-closed");
        } else if (str == "RESOLVED") {
            return this.$t("bs-resolved");
        } else if (str == "CANCELED") {
            return this.$t("bs-canceled");
        } else if (str == "OVERDUE") {
            return this.$t("bs-in-progress") + " - " + this.$t("bs-overdue");
        }
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
    toUTC(h) {
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
                timeZone: this.tz,
            });

            return converted_time;
        } catch (error) {
            return h
        }
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
