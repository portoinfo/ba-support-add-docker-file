<template>
<div class="w-100 h-100 p-0 m-0 non-selectable" v-if="!loading">
    <div class="table-responsive" :class="{'paginated': !non_paginated}">
        <table class="table table-sm table-bordered table-striped">
            <thead>
                <tr>
                    <th scope="col" v-if="cb_take">
                        <b-button
                            size="sm"
                            variant="light"
                            block
                            @click="ticketsCheckeds"
                        >
                            {{ namebttake }}
                        </b-button>
                    </th>
                    <th scope="col"></th>
                    <th scope="col">
                        <b-form-checkbox
                            id="checkbox"
                            v-model="cb_take"
                            name="checkbox"
                        >
                        </b-form-checkbox>
                    </th>
                    <th scope="col" v-if="!cb_take"></th>
                    <th scope="col" class="caret" :style="{ 'color': dc.id }" @click="orderTickets('id')">
                        #
                        <i v-if="storageActive == undefined || storageOrder != 'id'" class="fa fa-fw fa-sort"></i>
                        <i v-else-if="storageActive" class="fa fa-fw fa-sort-asc"></i>
                        <i v-else-if="!storageActive" class="fa fa-fw fa-sort-desc"></i>
                    </th>
                    <th scope="col" class="caret" :style="{ 'color': dc.created_at }" @click="orderTickets('created_at')">
                        {{ $t("bs-waiting-time") }}
                        <i v-if="storageActive == undefined || storageOrder != 'created_at'" class="fa fa-fw fa-sort"></i>
                        <i v-else-if="storageActive" class="fa fa-fw fa-sort-asc"></i>
                        <i v-else-if="!storageActive" class="fa fa-fw fa-sort-desc"></i>
                    </th>
                    <th scope="col" class="caret text-left" :style="{ 'color': dc.name_created } " @click="orderTickets('name_created')">
                        {{ $t("bs-client") }}
                        <i v-if="storageActive == undefined || storageOrder != 'name_created'" class="fa fa-fw fa-sort"></i>
                        <i v-else-if="storageActive" class="fa fa-fw fa-sort-asc"></i>
                        <i v-else-if="!storageActive" class="fa fa-fw fa-sort-desc"></i>
                        </th>
                    <th scope="col" class="caret text-left" :style="{ 'color': dc.department }" @click="orderTickets('department')">
                        {{ $t("bs-department") }}
                        <i v-if="storageActive == undefined || storageOrder != 'department'" class="fa fa-fw fa-sort"></i>
                        <i v-else-if="storageActive" class="fa fa-fw fa-sort-asc"></i>
                        <i v-else-if="!storageActive" class="fa fa-fw fa-sort-desc"></i>
                        </th>
                    <th scope="col">
                        {{ $t("bs-opening") }}
                    </th>
                    <th scope="col" class="caret" :style="{ 'color': dc.email_created }" @click="orderTickets('email_created')">
                        {{ $t("bs-email") }}
                        <i v-if="storageActive == undefined || storageOrder != 'email_created'" class="fa fa-fw fa-sort"></i>
                        <i v-else-if="storageActive" class="fa fa-fw fa-sort-asc"></i>
                        <i v-else-if="!storageActive" class="fa fa-fw fa-sort-desc"></i>
                    </th>
                </tr>
            </thead>
            <tbody v-if="chats_on_queue.data.length">
                 <template v-for="(q, i) in chats_on_queue.data">
                    <tr
                        :key="i"
                        v-if="!company_department.id || (company_department.id && q.company_department_id === company_department.id)"
                        :class="{'selected': chat_show_info && chat_number_info == q.number}"
                    >
                        <td>
                            <b-form-checkbox 
                                v-if="cb_take"
                                :id="'checkbox-'+i"
                                :name="'checkbox-'+i"
                                :value="q"
                                v-model="takeTicket[i]"
                            >
                            </b-form-checkbox>
                        </td>
                        <td>
                            <b-button
                                v-if="!cb_take"
                                :id="q.number"
                                size="sm"
                                variant="light"
                                block
                                class="text-dark"
                                @click="callSetInfo(q); callOpenChat(q)"
                            >
                                {{ $t("bs-take") }}
                            </b-button>
                        </td>
                        <td @click="callSetInfo(q)">
                            <b-button
                                size="sm"
                                variant="light"
                                block
                                class="text-dark"
                                @click="preview(q)"
                            >
                                {{ $t("bs-view") }}
                            </b-button>
                        </td>
                        <th scope="row" @click="callSetInfo(q)" v-b-toggle.sidebar-right-info-2>#{{ q.number }}</th>
                        <td :id="'time-elapsed-queue-' + q.ticket_id" @click="callSetInfo(q)" v-b-toggle.sidebar-right-info-2>
                            {{
                                calculateWaitingTime(
                                    UTCtoClientTZ(q.created_at, tz),
                                    "time-elapsed-queue-" + q.ticket_id
                                )
                            }}
                        </td>
                        <td class="text-left td-name" @click="callSetInfo(q)" v-b-toggle.sidebar-right-info-2>
                            <gravatar
                                :email="q.email_created"
                                :status="$status.get(q.client_id)"
                                :size="gravatarSize"
                                :name="$t(q.name_created)"
                                color="primary"
                                class="mr-2 ml-1"
                                :ba_acct_data="q.builderall_account_data"
                            />
                            {{ $t(q.name_created) }}
                        </td>
                        <td class="text-left" @click="callSetInfo(q)" v-b-toggle.sidebar-right-info-2>
                            <!-- <img v-if="q.department_type == 'builderall-mentor'" src="images/icons/vip.svg" alt="vip" height="20"> -->
                            <gravatar
                                :email="q.department"
                                status="false"
                                :size="gravatarSize"
                                :name="$t(q.department)"
                                class="mr-2 ml-1"
                                :ba_acct_data='`{"is_vip": ${q.department_type == "builderall-mentor"}}`'
                            />
                            {{ $t(q.department) }}
                        </td>
                        <td @click="callSetInfo(q)" v-b-toggle.sidebar-right-info-2>{{ UTCtoClientTZ2(q.created_at, tz) }}</td>
                        <td @click="callSetInfo(q)" v-b-toggle.sidebar-right-info-2>{{ q.name_created == 'bs-user' ? '--' : q.email_created }}</td>
                    </tr>
                 </template>
            </tbody>
            <tbody v-else>
                <tr>
                    <td colspan="100%" class="bg-white border-0 pt-5 pb-5">
                        <center>
                            <br>
                            <img class="m-auto d-block" src="images/icons/olho.svg" width="45px">
                            <br>
                            <span>{{ $t('bs-no-tickets') }}</span>
                            <br>
                        </center>
                    </td>
                </tr>
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
                    :data="chats_on_queue"
                    :show-disabled="true"
                    @pagination-change-page="getChatsOnQueue"
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
            ref: "tableQueue",
            tz: "",
            loading: Boolean,
            page: 1,
            //GETMUTIPLETICKETS
            cb_take: false,
            takeTicket: [],
            namebttake: this.$t("bs-all"),
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
        chat_admin: Number,
        chat_queue_full_control: Number,
        setInfo: "",
        openChat: "",
        openMultipleTickets: "",
        getChatsOnQueue: "",
        chats_on_queue: Object,
        company_department: "",
        resetTable: "",
        hideOnSmall: Boolean,
        user: "",
        chat_number_info: "",
        chat_show_info: Boolean,
        loading2catchChat: Boolean,
        filter_departments: '',
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
            if (this.chats_on_queue.data) {
                return true;
            } else {
                return false;
            }
        },
        non_paginated() {
            var minOption = this.$store.state.pp_options[0].value;
            return this.chats_on_queue.current_page == 1 && this.chats_on_queue.data.length < this.$root.$refs.FullTicket2.pp_selected && this.chats_on_queue.data.length < minOption;
        }
    },
    watch: {
        loaded(newValue, oldValue) {
            if (newValue) {
                this.loading = false;
            }
        },
        loading2catchChat(newValue, oldValue) {
            if (newValue == true) {
                this.loading = true;
            } else {
                this.loading = false
            }
        },
        cb_take(){
            if(!this.cb_take){
                this.takeTicket = [];
            }
        },
        takeTicket(){
            // console.log(this.takeTicket);
            this.takeTicket.forEach(item => {
                this.namebttake = this.$t("bs-all");
                if(item){
                    this.namebttake = this.$t("bs-take");
                }
            });
        }
    },
    created() {
        this.$root.$refs.TableQueue = this;
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
            this.$emit('orderTickets', 'QUEUE');
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
        ticketsCheckeds(){
            // console.log(this.takeTicket);
            var count = 0;
            this.takeTicket.forEach(item => {
                if(item == false){
                    count++;
                }
            });

            if(this.takeTicket.length == 0 || count == this.takeTicket.length){
                this.takeTicket = [];
                this.chats_on_queue.data.forEach(item => {
                    this.takeTicket.push(item);
                });
               this.namebttake = this.$t("bs-take");
            }else{
                this.takeTicket.forEach(item => {
                    if(item){
                        this.callMultipleTickets(item);
                    }
                });
                this.cb_take = false;
            }
        },
        preview(chat) {
            this.$root.$refs.FullTicket2.isPreview = true;
            this.$root.$refs.FullTicket2.chatPreview = chat;
            this.$root.$refs.FullTicket2.openClientHistory(chat.client_id);
        },
        onlyTheFirst(i, item) {
            if (item.dep_type == "builderall-mentor") {
                return false;
            } else if (this.chat_admin) {
                return false;
            } else if (this.chat_queue_full_control) {
                return false;
            } else if (i !== 0 || this.chats_on_queue.current_page > 1) {
                return true;
            }
        },
        callResetTable() {
            this.resetTable(this.ref);
        },
        callSetInfo(q) {
            this.setInfo(q, true);
        },
        callOpenChat(chat) {
            document.getElementById(chat.number).disabled = true;
            this.openChat(chat);    

        },
        callMultipleTickets(chat) {
            this.openMultipleTickets(chat);
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
                timeZone: tz
            });
            return moment(converted_time, "DD/MM/YYYY HH:mm:ss").format(
                "YYYY-MM-DD HH:mm:ss"
            );
        },
        UTCtoClientTZ2(h, tz) {
            try {
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
                    timeZone: tz
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
                    document.getElementById(c).innerHTML =
                        h + ":" + m + ":" + s;
                }
            }

            setInterval(updateClock, 1000);
        }
    }
};
</script>

<style scoped>
.btn.disabled {
    cursor: not-allowed	;
}

.caret {
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
</style>
