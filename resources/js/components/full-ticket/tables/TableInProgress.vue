<template>
<div class="w-100 h-100 p-0 m-0 non-selectable" v-if="!loading && showTable && chats.data">
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
                    <th scope="col" class="caret" :style="{ 'color': dc.answered }" @click="orderTickets('answered')">
                        <i v-if="storageActive == undefined || storageOrder != 'answered'" class="fa fa-fw fa-sort"></i>
                        <i v-else-if="storageActive" class="fa fa-fw fa-sort-asc"></i>
                        <i v-else-if="!storageActive" class="fa fa-fw fa-sort-desc"></i>
                    </th>
                    <th scope="col" class="caret" :style="{ 'color': dc.id }" @click="orderTickets('id')">
                        # 
                        <i v-if="storageActive == undefined || storageOrder != 'id'" class="fa fa-fw fa-sort"></i>
                        <i v-else-if="storageActive" class="fa fa-fw fa-sort-asc"></i>
                        <i v-else-if="!storageActive" class="fa fa-fw fa-sort-desc"></i>
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
                        {{ $t("bs-duration") }} 
                        <i v-if="storageActive == undefined || storageOrder != 'created_at'" class="fa fa-fw fa-sort"></i>
                        <i v-else-if="storageActive" class="fa fa-fw fa-sort-asc"></i>
                        <i v-else-if="!storageActive" class="fa fa-fw fa-sort-desc"></i>
                    </th>
                    <th scope="col" class="caret" :style="{ 'color': dc.email_created }" @click="orderTickets('email_created')">
                        {{ $t("bs-email") }} 
                        <i v-if="storageActive == undefined || storageOrder != 'email_created'" class="fa fa-fw fa-sort"></i>
                        <i v-else-if="storageActive" class="fa fa-fw fa-sort-asc"></i>
                        <i v-else-if="!storageActive" class="fa fa-fw fa-sort-desc"></i>
                    </th>
                    <th scope="col">
                        {{ $t("bs-opening") }}
                    </th>
                </tr>
            </thead>
            <tbody :class="{'overdue': overdue}">
                 <template v-for="(row, i2) in chats.data">
                    <tr
                        :key="i2"
                        v-if="!company_department.id || (company_department.id && row.company_department_id === company_department.id)"
                        :class="{'selected': chat_show_info && chat_number_info == row.number}"
                    >
                        <td>
                            <b-form-checkbox 
                                v-if="cb_take"
                                :id="'checkboxt-'+i2"
                                :name="'checkboxt-'+i2"
                                :value="row"
                                v-model="takeTicket[i2]"
                            >
                            </b-form-checkbox>
                        </td>
                        <td>
                            <b-button
                                v-if="!cb_take"
                                :id="row.number"
                                size="sm"
                                variant="light"
                                block
                                class="text-dark"
                                @click="callOpenChat(row)"
                            >
                                {{ $t("bs-open") }}
                            </b-button>
                        </td>
                        <td scope="row"></td>
                        <td scope="row" @click="callSetInfo(row)" v-b-toggle.sidebar-right-info-2>
                            <span
                                class="bs-ico"
                                :class="{
                                    'md-answered' : row.answered,
                                    'md-inactive' : !row.answered,
                                }"
                            >
                                feedback
                            </span>
                        </td>
                        <td scope="row" @click="callSetInfo(row)" v-b-toggle.sidebar-right-info-2>#{{ row.number }}</td>
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
                        <td :id="'time-elapsed-progress-' + row.ticket_id" @click="callSetInfo(row)" v-b-toggle.sidebar-right-info-2>
                            {{
                                calculateWaitingTime(
                                    UTCtoClientTZ(row.created_at, tz),
                                    "time-elapsed-progress-" + row.ticket_id
                                )
                            }}
                        </td>
                        <td @click="callSetInfo(row)" v-b-toggle.sidebar-right-info-2>
                            {{ row.name_created == 'bs-user' ? '--' : row.email_created }}
                        </td>
                        <th scope="row" @click="callSetInfo(row)" v-b-toggle.sidebar-right-info-2>{{ UTCtoClientTZ2(row.created_at, tz) }}</th>
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
                    @pagination-change-page="getChatsInProgress"
                >
                    <span slot="prev-nav"><vue-material-icon name="arrow_back" :size="14" /></span>
                    <span slot="next-nav"><vue-material-icon name="arrow_forward" :size="14" /></span>
                </pagination>
            </div>
        </div>
    </div>

        <div class="modal fade" id="actiontableSelected" tabindex="-1" aria-labelledby="actiontableSelectedLabel"
            aria-hidden="true" data-keyboard="false">
            <div :class="!shortcu_type ? 'modal-dialog modal-dialog-centered' : 'modal-dialog modal-dialog-centered modal-lg'">
                <div :class="!shortcu_type ? 'modal-content pt-2 mt-2' : 'modal-content'">
                    <div v-if="shortcu_type" class="modal-header border-0 p-0">
                        <h5 class="modal-title" id="exampleModalLabel">{{ $t("bs-change") }} {{ $t('bs-department') }} {{ $t('bs-and') }}   {{ $t('bs-attendant') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="viewCancel">
                            X
                        </button>
                    </div>
 
                    <div v-if="!shortcu_type" class="modal-body">
                        <div class="row">
                            <div class="modal-header border-0 p-0 m-0 mb-3">
                                <center>
                                    <h5 class="modal-title" id="actiontableSelectedLabel">
                                    {{ $t('bs-select-the-option-you-want-to-change') }}:</h5>
                                </center>
                            </div>
                            <div class="col-12">
                                [1] - {{ $t('bs-department') }}
                            </div>
                            <div class="col-12">
                                [2] - {{ $t('bs-agents') }}
                            </div>
                            <div class="col-12 mb-3">
                                [3] - {{ $t('bs-cancel') }}
                            </div>
                        </div>
                    </div>
                    <div v-if="shortcu_type == 'depart'" class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">{{ $t('bs-department') }}</label>
                                    <multiselect @input="getAgents" v-model="changeDepartment" deselect-label="" selectLabel=""
                                        track-by="name" label="name" :placeholder="phSelectDepart"
                                        :options="listDepartment" :searchable="false" :allow-empty="false"
                                        id="departments">
                                        <template slot="singleLabel" slot-scope="{ option }">
                                            <strong>
                                                {{ option.name }}
                                            </strong>
                                            <img v-if="option.type == 'builderall-mentor'"
                                                src="/images/icons/icon_vip.svg" width="50px" alt="">
                                        </template>
                                        <template slot="option" slot-scope="{ option }">
                                            <strong>
                                                {{ option.name }}
                                            </strong>
                                            <img v-if="option.type == 'builderall-mentor'"
                                                src="/images/icons/icon_vip.svg" width="50px" alt="">
                                        </template>
                                    </multiselect>
                                </div>
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">{{ $t('bs-agents') }}</label>
                                    <multiselect v-model="changeAgents" deselect-label="" selectLabel="" track-by="name"
                                        label="name" :placeholder="phSelectAgent" :options="listAgents"
                                        :searchable="false" :allow-empty="false" id="departments"></multiselect>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="shortcu_type == 'depart'" class="modal-footer border-0">
                        <button type="button" @click="viewCancel" class="text-capitalize btn" data-dismiss="modal">
                            {{ $t('bs-cancel') }}
                        </button>
                        <button type="button" @click="changeDepartTickets" :data-dismiss="checkDepartment" id="btn-department"
                            class="btn btn-primary">
                            {{ $t('bs-change') }}
                        </button>
                    </div>
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
            ref: "tableInProgress",
            tz: "",
            loading: Boolean,
            showTable: true,
            storageActive: localStorage.getItem("orderTable") ? JSON.parse(localStorage.getItem("orderTable")).isActive: true,
            storageOrder: localStorage.getItem("orderTable") ? JSON.parse(localStorage.getItem("orderTable")).order: null,
            dc: {
                answered: '',
                id: '',
                name: '',
                name_created: '',
                department: '',
                created_at: '',
                end: '',
                email_created: '',
            },
            cb_take: false,
            namebttake: this.$t("bs-all"),
            takeTicket: [],
            shortcu_type: 'depart',
            phSelectDepart: this.$t('bs-select-a-department'),
            phSelectAgent: this.$t('bs-select-an-attendant'),
            changeDepartment: "",
            changeAgents: "",
            listAgents: [],
            listDepartment: [],
            checkDepartment: '',
        };
    },
    props: {
        setInfo: "",
        getChatsInProgress: "",
        company_department: "",
        chats: "",
        hideOnSmall: Boolean,
        resetTable: "",
        openChat: "",
        user: "",
        countInProgress: "",
        footerActiveChat: "",
        chat_number_info: "",
        chat_show_info: Boolean,
        overdue: Boolean
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
        },
        overdue(n, o) {
            this.showTable = false;
            setTimeout(() => {
                this.showTable = true;
            }, 4);
        },
        takeTicket(){
            // console.log(this.takeTicket);
            this.takeTicket.forEach(item => {
                this.namebttake = this.$t("bs-all");
                if(item){
                    this.namebttake = this.$t("bs-action");
                }
            });
        }
    },
    created() {
        this.$root.$refs.TableInProgress = this;
        this.callResetTable();
        this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
        this.aplicColor(null);
    },
    methods: {
        changeDepartTickets(){
            var vm = this;
            var ids = [];
            vm.takeTicket.forEach(element => {
                ids.push({
                    ticket_id: element.id,
                    chat_id: element.chat_id
                });
            });

            axios.post("update-ticket-group", {
                type: 1,
                tickets_id: ids,
                department: vm.changeDepartment,
                agent: vm.changeAgents,
            }).then(function (response) {
                console.log(response.data.success);
                if (response.data.success) {
                    vm.viewCancel();
                    vm.$snotify.success(
                        vm.$t("bs-update-saved-successfully"),
                        vm.$t("bs-success"), {
                        position: "rightTop",
                    });
                } else {
                    vm.$snotify.error(vm.$t("bs-error-updating"), vm.$t("bs-error"), {
                        position: "rightTop",
                    });
                }
            })
            .catch(function (e) {
                console.log(e);
                console.log("FAILURE!!");
            });
        },
        viewCancel() {
            // this.takeTicket = [];
            $("#actiontableSelected").modal("hide");
        },
        ticketsCheckeds(){
            var vm = this;
            // console.log(this.takeTicket);
            var count = 0;
            vm.takeTicket.forEach(item => {
                if(item == false){
                    count++;
                }
            });

            if(vm.takeTicket.length == 0 || count == vm.takeTicket.length){
                vm.takeTicket = [];
                vm.chats.data.forEach(item => {
                    vm.takeTicket.push(item);
                });
               vm.namebttake = vm.$t("bs-action");
            }else{
                vm.getDepart();
                vm.getAgents();
                $("#actiontableSelected").modal("show");
            }
        },
        getDepart(){
            var vm = this;
            vm.changeDepartment = '';
            axios.get("tickets/get-department")
            .then(function (r_resposta) {
                for (let i = 0; i < r_resposta.data.result.length; i++) {
                    r_resposta.data.result[i].name = vm.$t(r_resposta.data.result[i].name);
                }
                vm.listDepartment = r_resposta.data.result;
                // let index = vm.listDepartment.findIndex(
                //     (item) => item.id === vm.itemselected.department_id
                // );
                // if (index !== -1) {
                //     vm.listDepartment.splice(index, 1);
                // }
            }).catch(function (error) {
                console.log(error);
            });
        },
        getAgents(){
            var vm = this;
            vm.changeAgents = '';
            // vm.shortcu_type = 'agent';
            axios.get("tickets/get-agents", {
                params: {
                    department_id: vm.changeDepartment.id,
                }
            }).then(function (r_resposta) {
                //console.log(r_resposta.data);
                vm.listAgents = r_resposta.data.result;
                // let index = vm.listAgents.findIndex(
                //     (item) => item.email === vm.user.email
                // );
                // if (index !== -1) {
                //     vm.listAgents.splice(index, 1);
                // }
            }).catch(function (error) {
                console.log(error);
            });
        },
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
            if(this.overdue){
                this.$emit('orderTickets', 'OVERDUE');
            }else{
                this.$emit('orderTickets', 'IN_PROGRESS');
            }
        },
        aplicColor(order){
            if(order == null){
                if(localStorage.getItem("orderTable")){
                    order = this.storageOrder;
                }
            }

            this.dc.answered = '#444';
            this.dc.id = '#444';
            this.dc.name = '#444';
            this.dc.name_created = '#444';
            this.dc.department = '#444';
            this.dc.created_at = '#444';
            this.dc.end = '#444';
            this.dc.email_created = '#444';

            if(order == 'answered'){this.dc.answered = '#0080fc';}
            if(order == 'id'){this.dc.id = '#0080fc';}
            if(order == 'name'){this.dc.name = '#0080fc';}
            if(order == 'name_created'){this.dc.name_created = '#0080fc';}
            if(order == 'department'){this.dc.department = '#0080fc';}
            if(order == 'created_at'){this.dc.created_at = '#0080fc';}
            if(order == 'end'){this.dc.end = '#0080fc';}
            if(order == 'email_created'){this.dc.email_created = '#0080fc';}
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

<style scoped>

    .modal-content {
        background: #f3f7ff 0% 0% no-repeat padding-box;
        box-shadow: 0px 14px 32px #00000040;
        border-radius: 10px;
        border: none;
        padding-top: 40px;
        padding-left: 40px;
        padding-right: 40px;
    }

    .modal-title {
        font: normal normal bold 20px/26px Muli;
        letter-spacing: 0px;
        color: #434343;
    }

    .modal {
        background-color: #59607173;
    }

    .modal .close {
        background: #acb8d8;
        color: white;
        text-shadow: none;
        padding: 2px;
        margin-top: 1px;
        border-radius: 100%;
        font-size: 15px;
        height: 25px;
        width: 25px;
        margin-right: 2px;
    }

    .modal-body label {
        font: normal normal bold 14px/35px Muli;
        letter-spacing: 0px;
        color: #656565;
    }

    .modal-body select {
        background: #fafbfc 0% 0% no-repeat padding-box;
        border: 1px solid #dddddd;
        border-radius: 3px;
        height: 50px;
    }
</style>