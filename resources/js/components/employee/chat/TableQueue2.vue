<template>
<div class="w-100 h-100 p-0 m-0 non-selectable">
    <div class="table-responsive">
        <table class="table table-sm table-bordered table-striped">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col">#</th>
                    <th scope="col">{{ $t("bs-waiting-time") }}</th>
                    <th scope="col" class="text-left">{{ $t("bs-client") }}</th>
                    <!-- <th scope="col" class="text-left">{{ $t("bs-description") }}</th> -->
                    <th scope="col" class="text-left">{{ $t("bs-department") }}</th>
                    <th scope="col">{{ $t("bs-opening") }}</th>
                    <th scope="col">{{ $t("bs-email") }}</th>
                </tr>
            </thead>
            <tbody v-if="chats_in_queue.length">
                 <template v-for="(q, i) in chats_in_queue">
                    <tr
                        :key="i"
                        v-if="!company_department.id || (company_department.id && q.company_department_id === company_department.id)"
                        :class="{'selected': chat_show_info && chat_number_info == q.number}"
                        v-b-toggle.sidebar-right-info-2
                    >
                        <td>
                            <b-button
                                :id="q.number"
                                :disabled="onlyTheFirst(i, q)"
                                size="sm"
                                variant="light"
                                block
                                class="text-dark"
                                @click="callCatchChat(i, q)"
                            >
                                {{ $t("bs-take") }}
                            </b-button>
                        </td>
                        <td @click="callSetInfo(q)">
                            <b-button
                                :id="q.number"
                                :disabled="onlyTheFirst(i, q)"
                                size="sm"
                                variant="light"
                                block
                                class="text-dark"
                                @click="preview(q)"
                            >
                                {{ $t("bs-view") }}
                            </b-button>
                        </td>
                        <th @click="callSetInfo(q)" scope="row">#{{ q.number }}</th>
                        <td @click="callSetInfo(q)" :id="'time-elapsed-queue-' + q.chat_id">
                            {{
                                calculateWaitingTime(
                                    UTCtoClientTZ(q.created_at, tz),
                                    "time-elapsed-queue-" + q.chat_id
                                )
                            }}
                        </td>
                        <td @click="callSetInfo(q)" class="text-left td-name">
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
                        <!-- <td @click="callSetInfo(q)">{{ q.description.replace(/(<([^>]+)>)/gi, '')}}</td> -->
                        <td @click="callSetInfo(q)" class="text-left">
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
                        <td @click="callSetInfo(q)">{{ UTCtoClientTZ2(q.created_at, tz) }}</td>
                        <td @click="callSetInfo(q)">{{ q.name == 'bs-user' ? '--' : q.email }}</td>
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
                            <span>{{ $t('bs-no-chats') }}</span>
                            <br>
                        </center>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<!-- <table-loading v-else/> -->
</template>
<script>
export default {
    data() {
        return {
            ref: "tableQueue",
            tz: "",
            loading: Boolean,
            page: 1,
        };
    },
    props: {
        chat_admin: Number,
        chat_queue_full_control: Number,
        setInfo: "",
        catchChat: "",
        company_department: "",
        resetTable: "",
        hideOnSmall: Boolean,
        user: "",
        chat_number_info: "",
        chat_show_info: Boolean,
        loading2catchChat: Boolean,
    },
    computed: {
        chats_in_queue: {
            get() {
                return this.$store.state.chats_in_queue;
            },
        },
        gravatarSize() {
            if (this.$root.$refs.FullChat2.isMobile) {
                return "40px";
            } else {
                return "30px";
            }
        },
        loaded() {
            if (this.chats_in_queue.length) {
                return true;
            } else {
                return false;
            }
        },
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
        }
    },
    created() {
        this.$root.$refs.TableQueue = this;
        this.callResetTable();
        this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
    },
    methods: {
        preview(chat) {
            this.$root.$refs.FullChat2.isPreview = true;
            this.$root.$refs.FullChat2.chatPreview = chat;
            this.$root.$refs.FullChat2.openClientHistory(chat.client_id);
        },
        onlyTheFirst(i, item, value = false) {
            if (item.dep_type == "builderall-mentor") {
                return false;
            } else if (this.chat_admin) {
                return false;
            } else if (value == true) {
                return true;
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
        callCatchChat(i, q, value = false) {
            if(!this.onlyTheFirst(i, q, value)) {
                if(!value){
                    document.getElementById(q.number).disabled = true;
                }
                if (window.location.pathname == "/chat") {
                    this.$root.$refs.ChatBody.catchChat(q);
                } else if (window.location.pathname == "/full-chat") {
                    this.$root.$refs.FullChat.catchChat(q);
                } else {
                    this.$root.$refs.FullChat2.catchChat(q);
                }
            }
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
</style>
