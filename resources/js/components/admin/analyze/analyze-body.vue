<template>
	<div>
	    <b-container fluid>
            <!-- title -->
            <!-- <b-row v-if="!openDetails" class="px-2" cols='1' cols-lg='3' align-h="between" align-v="center" no-gutters>
                <b-col class="py-2">
                    <h3 class="bs-title" v-html="title"></h3>
                    <span class="bs-subtitle" v-html="subtitle"></span>
                </b-col>
            </b-row> -->

            <b-row style="background-color:white;" class="px-0 pb-2 pt-2">
                <b-col cols="auto" class="mt-2">
                    <span class="material-icons-two-tone" style="vertical-align: text-bottom;">info</span> 
                </b-col>
                <b-col cols="auto" class="mt-2">
                    <b-form-checkbox
                    id="checkbox-1"
                    v-model="checkedDelete"
                    @change="changeSeletecDeleted"
                    name="checkbox-1"
                    >
                    <b id="tooltip-attendants-deleted">{{$t('bs-agents')}} {{$t('bs-deleted-s')}}</b>
                    </b-form-checkbox>
                    <b-tooltip target="tooltip-attendants-deleted" triggers="hover" placement="right" variant="secondary">
                        {{$t('bs-tooltip-attendants-deleted')}}
                    </b-tooltip>
                </b-col>
                <b-col>
                    <!-- <div class="text-right mtcustom">
                        <b-form-checkbox
                        id="checkbox-1"
                        v-model="selectedAll"
                        @change="changeSeletecAll"
                        name="checkbox-1"
                        >
                        <b>Todos</b>
                        </b-form-checkbox>
                    </div> -->
                </b-col>
                <b-col cols="auto">
                    <div class="text-right">
                        <b-form-select size="sm" @change="changeChatTicket" v-model="selectedChatTicket" :options="options"></b-form-select>
                    </div>
                </b-col>
                <b-col cols="auto">
                    <div class="text-right">
                        <b-form-select size="sm" @change="changeDepartment" v-model="selectedDepartment" :options="optionsDepartment">
                        </b-form-select>
                    </div>
                </b-col>
                <b-col cols="auto">
                    <div class="text-right">
                        <b-form-select size="sm" @change="changeTime" v-model="selectedTime1" :options="optionsTime1"></b-form-select>
                    </div>
                </b-col>
                <b-col v-if="showtimes" cols="auto">
                    <div class="text-right">
                        <b-form-datepicker id="example-datepicker" @input="getAgents" v-model="pInitialDate" class="mb-2"></b-form-datepicker>
                    </div>
                    <div class="text-right">
                        <b-form-datepicker id="example-datepicker-2" @input="getAgents" v-model="pFinalDate" class="mb-2"></b-form-datepicker>
                    </div>
                </b-col>
            </b-row>
            <b-row style="background-color:white;border-top: 1px solid #E6E6E6;">
                <b-col class="mx-1">
                    <b-table
                        :items="agentsOptions"
                        :fields="agentsfields"
                        :sticky-header="tablesize"
                        select-mode="single"
                        responsive="sm"
                        ref="selectableTableAgents"
                        :selectable="selectable"
                        table-variant="light"
                        borderless hover
                        class="customfont"
                        :sort-by.sync="sortByagents"
                        :sort-desc.sync="sortDescagents"
                        @row-selected="onRowSelected"
                    >
                    <template #cell(attendant)="row" >
                        <span style="white-space:nowrap !important;" :class="row.item.user_removed ? 'deleted' : ''">

                            <gravatar
                                :email="row.item.email"
                                :status="$status.get(row.item.id)"
                                size="30px"
                                :name="row.item.attendant"
                                color="light"
                            />
                            <span>{{$t(row.item.attendant)}}</span>  
                            <!-- <i v-if="$status.get(row.item.id) == 'online'" class="fa fa-circle bs-green" aria-hidden="true"></i>
                            <i v-if="$status.get(row.item.id) == 'offline'" class="fa fa-circle bs-offline" aria-hidden="true"></i>
                            <i v-if="$status.get(row.item.id) == 'busy'" class="fa fa-circle bs-red" aria-hidden="true"></i>
                            <i v-if="$status.get(row.item.id) == 'appear_away'" class="fa fa-circle bs-yellow" aria-hidden="true"></i> -->
                        </span>
                    </template>
                    <template #cell(opened)="row">
                        <span :class="row.item.user_removed ? 'deleted' : ''">
                            <span v-if="row.item.opened != null && row.item.opened != ''">
                                {{$t(row.item.opened)}}
                            </span>
                            <span v-else>
                                0
                            </span>
                        </span>  
                    </template>
                    <template #cell(finished)="row">
                        <span :class="row.item.user_removed ? 'deleted' : ''">
                            <span v-if="row.item.finished != null && row.item.finished != ''">
                                {{$t(row.item.finished)}}
                            </span>
                            <span v-else>
                                0
                            </span>
                        </span>
                    </template>
                    <template #cell(canceled)="row">
                        <span :class="row.item.user_removed ? 'deleted' : ''">
                            <span v-if="row.item.canceled != null && row.item.canceled != ''">
                                {{$t(row.item.canceled)}}
                            </span>
                            <span v-else>
                                0
                            </span>
                        </span>
                    </template>
                    <template #cell(moved_to_ticket)="row">
                        <span :class="row.item.user_removed ? 'deleted' : ''">
                            <span v-if="row.item.moved_to_ticket != null && row.item.moved_to_ticket != ''">
                                {{$t(row.item.moved_to_ticket)}}
                            </span>
                            <span v-else>
                                0
                            </span>
                        </span>
                    </template>
                    <template #cell(average)="row">
                        <span :class="row.item.user_removed ? 'deleted' : ''">
                            <span v-if="row.item.average != null && row.item.average != ''">
                                {{$t(row.item.average)}}
                            </span>
                            <span v-else>
                                0
                            </span>
                        </span>
                    </template>
                    <template #cell(avg_queue_time)="row">
                        <span :class="row.item.user_removed ? 'deleted' : ''">
                           {{row.item.avg_queue_time}}
                        </span>
                    </template>
                    <template #cell(media_stars_atendent)="row">
                        <span :class="row.item.user_removed ? 'deleted' : ''">
                            {{row.item.media_stars_atendent }}
                        </span>
                    </template>
                    <template #cell(media_stars_service)="row">
                        <span :class="row.item.user_removed ? 'deleted' : ''">
                            {{row.item.media_stars_service}}
                        </span>
                    </template>
                    <template #empty="scope">
                        <div class="text-center">{{ $t('bs-no-command-registered') }}</div>
                    </template>
                    </b-table>
                </b-col>
                <b-col v-show="openDetails" style="background-color:white;" class="mx-1"> 
                    <b-row class="mt-2 ">
                       
                        <b-col cols="auto" class="bs-m-spacing mt-3">
                            <span id="service-and-time" class="material-icons-two-tone" style="vertical-align: text-bottom;">info</span>
                            <b-tooltip target="service-and-time" triggers="hover" placement="right" variant="secondary">
                                {{$t('bs-service-and-time')}}
                            </b-tooltip>
                        </b-col>
                        <b-col cols="auto" class="bs-m-spacing mt-3">
                            <a v-on:click.stop="showIB(1, $event)" href="#" :class="ss.ss1">{{$t('bs-calls')}}</a> 
                        </b-col>
                        <!-- <b-col cols="auto" class="bs-m-spacing mt-3">
                            <a v-on:click.stop="showIB(2, $event)" href="#" :class="ss.ss2">{{$t('bs-percentage-of-responses')}}</a>
                        </b-col> -->
                        <b-col cols="" class="bs-m-spacing mt-3">
                            <a v-on:click.stop="showIB(3, $event)" href="#" :class="ss.ss3">{{$t('bs-time')}}</a>
                        </b-col>
                    </b-row>
                    <div v-if="show.show1">
                        <b-row>
                            <b-col class="father">
                                <span id="t-customer-response" class="son"><b>{{$t('bs-awaiting-customer-response')}}<br><center class="mt-1">{{waiting_customer ? waiting_customer : 0 }}</center></b></span>
                                <b-tooltip target="t-customer-response" triggers="hover" placement="right" variant="secondary">
                                    {{$t('bs-t-customer-response')}}
                                </b-tooltip>
                            </b-col>
                            <b-col  class="father">
                                <span id="t-attendant-response" class="son"><b>{{$t('bs-waiting-for-a-response-from-the-attendant')}}<br><center class="mt-1">{{ waiting_attendant ? waiting_attendant : 0 }}</center></b></span>
                                <b-tooltip target="t-attendant-response" triggers="hover" placement="right" variant="secondary">
                                    {{$t('bs-t-attendant-response')}}
                                </b-tooltip>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col  class="father">
                                <span id="t-no-customer-interaction" class="son"><b>{{$t('bs-no-customer-interaction')}}<br><center class="mt-1">{{interaction_customer}}</center></b></span>
                                <b-tooltip target="t-no-customer-interaction" triggers="hover" placement="right" variant="secondary">
                                    {{$t('bs-t-no-customer-interaction')}}
                                </b-tooltip>
                            </b-col>
                            <b-col  class="father">
                                <span id="t-no-attendant-interaction" class="son"><b>{{$t('bs-no-attendant-interaction')}}<br><center class="mt-1">{{interaction_attendant}}</center></b></span>
                                <b-tooltip target="t-no-attendant-interaction" triggers="hover" placement="right" variant="secondary">
                                    {{$t('bs-t-no-attendant-interaction')}}
                                </b-tooltip>
                            </b-col>
                        </b-row>
                    </div>
                    <!-- <div v-if="show.show2">
                        <b-row>
                            <b-col  class="father">
                                <span class="son"><b>{{$t('bs-average-of-responses-received')}}<br><center class="mt-1">0</center></b></span>
                            </b-col>
                            <b-col  class="father">
                                <span class="son"><b>{{$t('bs-percentage-of-responses')}}<br><center class="mt-1">0</center></b></span>
                            </b-col>
                        </b-row>
                    </div> -->
                    <div v-if="show.show3">
                        <b-row>
                            <b-col  class="father">
                                <span id="t-total-time-in-service" class="son"><b>{{$t('bs-total-time-in-service')}}<br><center class="mt-1">{{total_time_service}}</center></b></span>
                                <b-tooltip target="t-total-time-in-service" triggers="hover" placement="right" variant="secondary">
                                    {{$t('bs-t-total-time-in-service')}}
                                </b-tooltip>
                            </b-col>
                            <b-col  class="father">
                                <span id="t-total-amount" class="son"><b>{{$t('bs-total-amount')}}<br><center class="mt-1">{{total_chat_ticket}}</center></b></span>
                                <b-tooltip target="t-total-amount" triggers="hover" placement="right" variant="secondary">
                                    {{$t('bs-t-total-amount')}}
                                </b-tooltip>
                            </b-col>
                        </b-row>
                    </div>
                    <div v-if="show.show4">
                        d
                    </div>
                </b-col>
            </b-row>
            <b-row v-show="openDetails">
                <b-col style="background-color:white;" cols="12" class="mt-3 pt-2 pb-2">
                    <b-row>
                        <b-col class="mt-4">
                            <span class="material-icons-two-tone" style="vertical-align: text-bottom;">info</span> 
                            <span v-if="!subshow.show3" style="text-align: center;"><b>{{$t('bs-satisfaction')}}</b></span>
                            <span v-if="subshow.show3" style="text-align: center;"><b>{{$t('bs-status')}}</b></span>
                        </b-col>
                        <b-col cols="auto" style="background-color:white;" class="text-right mx-1">
                            <b-row class="mt-2">
                                <b-col cols="auto" class="bs-m-spacing mt-3">
                                    <a v-on:click.stop="subshowIB(1, $event)" href="#" :class="subss.ss1">{{$t('bs-listing')}}</a>
                                </b-col>
                                <b-col cols="auto" class="bs-m-spacing mt-3">
                                    <a v-on:click.stop="subshowIB(2, $event)" href="#" :class="subss.ss2">{{$t('bs-percentage')}}</a>
                                </b-col>
                                <b-col cols="auto" class="bs-m-spacing mt-3">
                                    <a v-on:click.stop="subshowIB(3, $event)" href="#" :class="subss.ss3">{{$t('bs-status')}}</a>
                                </b-col>
                                <b-col cols="auto" class="bs-m-spacing mt-1 text-right">
                                    <b-form-select size="sm" @change="updateChartSelectedTime2" v-model="selectedTime2" :options="optionsTime2"></b-form-select>
                                </b-col>
                            </b-row>
                        </b-col>
                    </b-row>
                </b-col>
                <b-col  :cols="subshow.show1 && avaliationselected.length ? '8' : '12'" v-if="subshow.show1" style="background-color:white;">
                    <b-table
                        :items="avaliationOptions"
                        :fields="avaliationFields"
                        sticky-header="200px"
                        select-mode="single"
                        responsive="sm"
                        ref="selectableTableAvaliation"
                        selectable
                        table-variant="light"
                        borderless hover
                        class="customfont"
                        @row-selected="onRowSelectedAvaliation"
                    >
                    <template #cell(id)="row">
                        {{row.item.id}}
                    </template>
                    <template #cell(name)="row">
                        {{$t(row.item.name)}}
                    </template>
                    <template #cell(hour)="row">
                        {{UTCtoClientTZ2(row.item.hour, tz)}}
                    </template>
                    <template #cell(stars_atendent)="row">
                         <b-form-rating v-model="row.item.stars_atendent" readonly no-border variant="warning" class="mb-2"></b-form-rating>
                    </template>
                    <template #cell(stars_service)="row">
                        <b-form-rating v-model="row.item.stars_service" readonly no-border variant="warning" class="mb-2"></b-form-rating>
                    </template>
                    <template #cell(message)="row">
                       {{filterText(row.item.message)}}
                    </template>
                    <template #empty="scope">
                        <div class="text-center">{{ $t('bs-no-command-registered') }}</div>
                    </template>
                    </b-table>
                </b-col>
                <b-col cols="4" class="backgroudcustom" v-if="subshow.show1 && avaliationselected.length">
                   <p class="mt-2 ml-1"><b>{{$t('bs-message')}}: </b></p>
                   <p class="mt-2 ml-1"><b>{{avaliationselected[0].message}}</b></p>
                </b-col>
                <b-col v-if="subshow.show2" style="background-color:white;">
                    <b-row class="mb-5 px-2" align-v="stretch">
                        <b-col>
                            <line-chart
                                v-if="chart2.loadedData"
                                :chartData="{
                                    labels: chart2.xLabels,
                                    datasets: chart2.lineData
                                }"
                                :options="{
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    legend: {
                                        align: 'start',
                                        labels: {
                                            boxWidth: 10,
                                            fontFamily: 'Muli, Lato, Havelica',
                                            fontSize: 14,
                                            fullWidth: false
                                        }
                                    },
                                    scales: {
                                        yAxes: [{
                                            gridLines: {
                                                borderDash: [1,2],
                                                drawBorder: false,
                                            },
                                            ticks: {
                                                beginAtZero: true, // inicia de 0
                                                suggestedMin: 0,
                                                suggestedMax: 50,
                                                min: 0, // sem valores negativos
                                            },
                                        }],
                                        xAxes: [{
                                            gridLines: {
                                                display: false,
                                            },
                                        }],
                                    }
                                }"
                                :styles="{ // parent node
                                    position: 'relative',
                                    width: '100%',
                                    height: 'auto',
                                }"
                                :width="534"
                                :height="300"
                                :plugins="[
                                    {
                                        id: 'my-plugin',
                                        beforeInit: function (chart) {
                                            chart.legend.afterFit = function() {
                                                this.height = this.height + 30;
                                            };
                                        }
                                    }
                                ]"
                            ></line-chart>
                            <div v-else class="text-center">
                                {{bs-loading}}...
                            </div>
                        </b-col>
                        <b-col cols="auto">
                            <doughnut-chart
                                v-if="chart3.loadedData"
                                :chartData="{
                                    labels: chart3.xLabels,
                                    datasets: chart3.lineData
                                }"
                                :options="{
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    legend: {
                                        align: 'start',
                                        labels: {
                                            boxWidth: 10,
                                            fontFamily: 'Muli, Lato, Havelica',
                                            fontSize: 14,
                                            fullWidth: false
                                        }
                                    },
                                
                                }"
                                :styles="{ // parent node
                                    position: 'relative',
                                    width: '100%',
                                    height: 'auto',
                                }"
                                :width="200"
                                :height="300"
                                :plugins="[
                                    {
                                        id: 'my-plugin',
                                        beforeInit: function (chart) {
                                            chart.legend.afterFit = function() {
                                                this.height = this.height + 30;
                                            };
                                        }
                                    }
                                ]"
                            ></doughnut-chart>
                        </b-col>
                    </b-row>
                </b-col>
                
                <b-col v-if="subshow.show3" style="background-color:white;">
                    <b-table 
                        table-variant="light"
                        hover
                        :items="statusListUser"
                        :fields="statusListfields"
                    >
                        <!-- <template #cell(name)="row">
                            {{ row.item.name }}
                        </template> -->
                        <template #cell(status)="row">
                                <b-badge v-if="row.item.status === 'online'" variant="success">{{ $t('bs-on') }}</b-badge>
                                <b-badge v-if="row.item.status === 'busy'" variant="danger">{{ $t('bs-busy') }}</b-badge>
                                <b-badge v-if="row.item.status === 'appear_away'" variant="warning">{{ $t('appear-away') }}</b-badge>
                                <b-badge v-if="row.item.status === 1" variant="success">{{ $t('bs-active')}}</b-badge>
                                <b-badge v-if="row.item.status === 0" variant="danger">{{ $t('bs-disabled') }}</b-badge>
                        </template>
                        <template #cell(created_at)="row">
                            {{UTCtoClientTZ2(row.item.created_at, tz)}}
                        </template>
                    </b-table>
                    
                    
                    <!-- <b-row class="mb-5 px-2" align-v="stretch">
                        <b-col>
                            ggg
                        </b-col>
                        <b-col>
                            ggg
                        </b-col>
                        <b-col>
                            ggg
                        </b-col>
                    </b-row> -->
                </b-col>
            </b-row>

            <!-- type	string	mime type [xls, csv], default: xls -->
            <!-- <export-excel
                v-if="showDownload && !openDetails"
                class   = "caret fl ml-3"
                :data   = "json_data"
                :fields = "json_fields"
                worksheet = "My Worksheet"
                :name    = "name_file">
                <i class="fa fa-download" aria-hidden="true"></i>
                {{$t('bs-download-excel')}}
            </export-excel>
            <export-excel
                v-if="showDownload && !openDetails"
                class   = "caret fl"
                :data   = "json_data"
                :fields = "json_fields"
                worksheet = "My Worksheet"
                :name    = "name_file"
                type    = "csv" >
                <i class="fa fa-download" aria-hidden="true"></i>
                 {{$t('bs-download')}} CSV
            </export-excel> -->
                <span class="caret fl" @click="generateFile">
                    <i class="fa fa-download" aria-hidden="true"></i>
                    {{$t('bs-download')}} Excel - Todos os atendentes
                </span>
        </b-container>
	</div>
</template>

<script>
export default {
	data(){
		return {
            showDownload: false,
            name_file: '',
            json_fields: {
                'Complete name': 'name',
                'City': 'city',
                'Telephone': 'phone.mobile',
                'Telephone 2' : {
                    field: 'phone.landline',
                    callback: (value) => {
                        return `Landline Phone - ${value}`;
                    }
                },
            },
            json_data: [
                {
                    'name': 'Tony Peña',
                    'city': 'New York',
                    'country': 'United States',
                    'birthdate': '1978-03-15',
                    'phone': {
                        'mobile': '1-541-754-3010',
                        'landline': '(541) 754-3010'
                    }
                },
            ],
            json_meta: [
                [
                    {
                        'key': 'charset',
                        'value': 'utf-8'
                    }
                ]
            ],
            title: this.$t('bs-analyze') +' '+ this.$t('bs-agents'),
            subtitle: this.$t('bs-attendants-information'),
            selectedAll: false,
            selectedChatTicket: 'CHAT',
            options: [
                { value: 'ALL', text: this.$t('bs-all'), },
                { value: 'CHAT', text:  this.$t('bs-chat'),},
                { value: 'TICKET', text:  this.$t('bs-ticket'), },
            ],
            selectedDepartment: 'ALL',
            optionsDepartment: [],
            itemselected: [],
            openDetails: false,
            tablesize: '600px',
            show: {// SELECT ATENDIMENTOS/PERCENTUAL/TEMPO
				'show1': true,
				'show2': false,
				'show3': false,
				'show4': false,
				'show5': false,
				'show6': false,
				'show7': false,
				'show8': false,
			},
            ss: {
				'ss1': 'tab active', 
				'ss2': 'tab',
				'ss3': 'tab',
				'ss4': 'tab',
				'ss5': 'tab',
				'ss6': 'tab',
			},
            subshow: { // SELECT LISTAGEM/PORCENTAGEM
                'show1': true,
				'show2': false,
				'show3': false,
				'show4': false,
			},
            subss: {
				'ss1': 'tab active', 
				'ss2': 'tab',
				'ss3': 'tab',
				'ss4': 'tab',
			},
            checkedDelete: false,
            showtimes: false,
            pInitialDate: new Date(),
            pFinalDate: new Date(),
            selectedTime1: 'LAST_7_DAYS',
            optionsTime1: [
                { value: 'CUSTOM', text: this.$t('bs-custom') },
                { value: 'LAST_24_HOURS', text: this.$t('bs-day') },
                { value: 'LAST_7_DAYS', text: this.$t('bs-week') },
                { value: 'LAST_30_DAYS', text: this.$t('bs-month') },
                { value: 'LAST_365_DAYS', text: this.$t('bs-year') },
            ],
            selectedTime2: 'WEEK',
            optionsTime2: [
                // { value: 'LAST_24_HOURS', text: 'Dia' },
                { value: 'WEEK', text: this.$t('bs-week') },
                { value: 'MONTH', text: this.$t('bs-month') },
                { value: 'YEAR', text: this.$t('bs-year') },
            ],
            avaliationselected: '',
            avaliationOptions: null,
            avaliationFields: [
                { key: 'chat_id', sortable: true, label: '#'},
                { key: 'name', sortable: true, label: this.$t('bs-name')},
                { key: 'hour', sortable: true, label: this.$t('bs-date') },
                { key: 'stars_atendent', sortable: true, label: this.$t('bs-attendant') },
                { key: 'stars_service', sortable: true, label: this.$t('bs-service') },
                { key: 'message', sortable: true, label: this.$t('bs-message') },
            ],
            // line chart
            chart2:{},
            chart3:{},
            selectable: true,
            agentsOptions: null,
            agentsfields: [
                { key: 'attendant', sortable: true, label: this.$t('bs-agents'), headerTitle: this.$t('bs-company-attendants')},
                { key: 'interation', sortable: true, label: this.$t('bs-interactions'), headerTitle: this.$t('bs-analyze-d-interactions')},
                { key: 'opened', sortable: true, label: this.$t('bs-opened'), headerTitle: this.$t('bs-analyze-d-opened')},
                { key: 'finished', sortable: true, label: this.$t('bs-closed'), headerTitle: this.$t('bs-analyze-d-finished') },
                { key: 'moved_to_ticket', sortable: true, label: this.$t('bs-moved-to-ticket'), headerTitle: this.$t('bs-analyze-d-moved-ticket') },
                { key: 'average', sortable: true, label: this.$t('bs-average-response-time'), headerTitle: this.$t('bs-analyze-d-average') },
                { key: 'avg_queue_time', sortable: true, label: this.$t('bs-queue-time'), headerTitle: this.$t('bs-analyze-d-avg-queue-time') },
                { key: 'media_stars_atendent', sortable: true, label: this.$t('bs-stars-atendent'), headerTitle: this.$t('bs-analyze-d-media-stars-atendent') },
                { key: 'media_stars_service', sortable: true, label: this.$t('bs-stars-service'), headerTitle: this.$t('bs-analyze-d-media-stars-service') },
            ],
            sortByagents: null,
            sortDescagents: null,
            waiting_attendant: 0,
            waiting_customer: 0,
            interaction_customer: 0,
            interaction_attendant: 0,
            total_time_service: 0,
            total_chat_ticket: 0,
            tz: "",
            statusListUser: [], 
            statusListfields: [
                { key: 'status', sortable: true, label: this.$t('bs-status'), headerTitle: this.$t('bs-company-attendants')},
                { key: 'created_at', sortable: true, label: this.$t('bs-date-of-change'), headerTitle: this.$t('bs-analyze-d-interactions')},
                // { key: 'name', sortable: true, label: this.$t('bs-name'), headerTitle: this.$t('Alterado por')},
            ],
		}
	},
    props: {
        session_user: Object,
        timezones: Object,
    },
    created(){
        this.updateGraph();
        this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
    },
    mounted(){
        this.getAgents();
        this.getDepartments();
        
        if(localStorage.getItem('sortByagents') == null){
            localStorage.setItem('sortByagents', ['attendant;',false]);
            this.sortByagents = 'attendant';
            this.sortDescagents = false;
        }else{
            var string = localStorage.getItem('sortByagents');
            var retorno = string.split(",");
            this.sortByagents = retorno[0];
            this.sortDescagents = retorno[1] == 'true' ? true:false;
        }
        
    },
    watch: {
        sortByagents(){
            localStorage.setItem('sortByagents', [this.sortByagents, this.sortDescagents]);
        },
        sortDescagents(){
            localStorage.setItem('sortByagents', [this.sortByagents, this.sortDescagents]);
        }
    },
    methods: {   
        generateFile(unique = false){
            var vm = this;
               axios.get('analyse/generate-file-excel', {
                params:{
                    selectedAllagents: vm.selectedAll,
                    ChatorTicket:  vm.selectedChatTicket,
                    selectedDepartment: vm.selectedDepartment,
                    selectedTime1: vm.selectedTime1,
                    selectedTime2: vm.selectedTime2,
                    pInitialDate: vm.pInitialDate,
                    pFinalDate: vm.pFinalDate,
                    checkedDelete: vm.checkedDelete,
                    tz: this.tz,
                    // company_user_id: vm.itemselected[0].company_user_id,
                }
            }).then(function(r_resposta){
                window.open(r_resposta.data, '_blank');
            }).catch(function (error) {

                console.log(error);
            });
        },
        mountedExcel(){
            var vm = this;
            vm.showDownload = true;
            // json_data
            // json_meta
            var data = new Date();
  
            vm.name_file = vm.selectedChatTicket+'-'+data.toLocaleDateString()+'.xls';

            if(vm.selectedChatTicket == 'CHAT'){
                vm.json_fields = {
                    [this.$t('bs-agents')] : 'attendant',
                    [this.$t('bs-opened')] : 'opened',
                    [this.$t('bs-closed')] : 'finished',
                    [this.$t('bs-moved-to-ticket')] : 'moved_to_ticket',
                    [this.$t('bs-average-response-time')] : 'average',
                    [this.$t('bs-queue-time')] : 'avg_queue_time',
                    [this.$t('bs-stars-atendent')] : 'media_stars_atendent',
                    [this.$t('bs-stars-service')] : 'media_stars_service',
                };
            }else if(vm.selectedChatTicket == 'TICKET'){
                vm.json_fields = {
                    [this.$t('bs-agents')] : 'attendant',
                    [this.$t('bs-opened')] : 'opened',
                    [this.$t('bs-closed')] : 'finished',
                    [this.$t('bs-canceled')] : 'canceled',
                    [this.$t('bs-average-response-time')] : 'average',
                    [this.$t('bs-queue-time')] : 'avg_queue_time',
                    [this.$t('bs-stars-atendent')] : 'media_stars_atendent',
                    [this.$t('bs-stars-service')] : 'media_stars_service',
                };
            }else{
                vm.json_fields = {
                    [this.$t('bs-agents')] : 'attendant',
                    [this.$t('bs-opened')] : 'opened',
                    [this.$t('bs-closed')] : 'finished',
                    [this.$t('bs-canceled')] : 'canceled',
                    [this.$t('bs-moved-to-ticket')] : 'moved_to_ticket',
                    [this.$t('bs-average-response-time')] : 'average',
                    [this.$t('bs-queue-time')] : 'avg_queue_time',
                    [this.$t('bs-stars-atendent')] : 'media_stars_atendent',
                    [this.$t('bs-stars-service')] : 'media_stars_service',
                };
            }

            vm.json_data = vm.agentsOptions;
        },
        updateGraph(){
            this.chart2 = {
                title: this.$t('bs-tk-chat-resolved-by-attendant'),
                loadedData: true,
                xLabels: [
                    this.$t('bs-monday'), 
                    this.$t('bs-tuesday'), 
                    this.$t('bs-wednesday'), 
                    this.$t('bs-thursday'), 
                    this.$t('bs-friday'), 
                    this.$t('bs-saturday'), 
                    this.$t('bs-sunday'), 
                ],
                lineData: [
                    {
                        label: this.selectedChatTicket == 'CHAT' ? this.$t('bs-open-chats') : this.$t('bs-open-tickets'),
                        backgroundColor: "#00C38E",
                        borderColor: "#00C38E",
                        fill: false,
                        data: [0]
                    },
                    {
                        label: this.selectedChatTicket == 'CHAT' ? this.$t('bs-chats')+' '+this.$t('bs-finalized-s') : this.$t('bs-tickets')+' '+this.$t('bs-finalized-s'),
                        backgroundColor: "#0080FC",
                        borderColor: "#0080FC",
                        fill: false,
                        data: [0]
                    },
                    {
                        label: this.selectedChatTicket == 'CHAT' ? this.$t('bs-chats')+' '+this.$t('bs-lost-s') : this.$t('bs-tickets')+' '+this.$t('bs-canceled-s'),
                        backgroundColor: "#FF4872",
                        borderColor: "#FF4872",
                        fill: false,
                        data: [0]
                    }
                ]
            };
            this.chart3 = { // doughnut-chart
                title: this.$t('bs-tk-chat-resolved-by-attendant'),
                loadedData: true,
                xLabels: ['Bom', 'Ruim'],
                lineData: [
                    {
                        label: this.selectedChatTicket == 'CHAT' ? this.$t('bs-open-chats') : this.$t('bs-open-tickets'),
                        backgroundColor: ["#00C38E", '#FF4872'] ,
                        data: []
                    },
                ]
            };
        },
        updateChartSelectedTime2() {
            var vm = this;
            // console.log(vm.selectedTime2);selectedTime2
            if(vm.selectedTime2 == 'YEAR'){
                vm.chart2.xLabels = [
                    this.$t('bs-january'),
                    this.$t('bs-february'),
                    this.$t('bs-march'),
                    this.$t('bs-april'),
                    this.$t('bs-may'),
                    this.$t('bs-june'),
                    this.$t('bs-july'),
                    this.$t('bs-august'),
                    this.$t('bs-september'),
                    this.$t('bs-october'),
                    this.$t('bs-november'),
                    this.$t('bs-december'),
                ];
            }else if(vm.selectedTime2 == 'WEEK'){
                vm.chart2.xLabels = [
                    this.$t('bs-monday'), 
                    this.$t('bs-tuesday'), 
                    this.$t('bs-wednesday'), 
                    this.$t('bs-thursday'), 
                    this.$t('bs-friday'), 
                    this.$t('bs-saturday'), 
                    this.$t('bs-sunday'), 
                ];
            }else if(vm.selectedTime2 == 'MONTH'){
                // var objData = new Date(),
                //     numAno = objData.getFullYear(),
                //     numMes = objData.getMonth()+1,
                //     numDias = new Date(numAno, numMes, 0).getDate();
                //     vm.chart2.xLabels = [];
                // for(let i = 1; i<= numDias; i++) {
                //     vm.chart2.xLabels.push(i);
                // }
                vm.chart2.xLabels = [];
                vm.chart2.xLabels.push(vm.$t('bs-week')+ ' 1');
                vm.chart2.xLabels.push(vm.$t('bs-week')+ ' 2');
                vm.chart2.xLabels.push(vm.$t('bs-week')+ ' 3');
                vm.chart2.xLabels.push(vm.$t('bs-week')+ ' 4');
            }
            vm.getGraphics();
        },
        onRowSelected(item){
            var vm = this;

            if(item.length > 0){
                if(item[0].id == undefined && vm.itemselected[0] != 'selectedAll'){
                    vm.selectedAll = false;
                }
            }
            
            if(item.length){ //CLICOU

                vm.openDetails = true;
                vm.tablesize = '300px';
                vm.itemselected = item;
                console.log(vm.itemselected);
                vm.avaliationselected = '';
                if(vm.selectedChatTicket == 'CHAT'){
                    vm.agentsfields =  [
                        { key: 'attendant', sortable: true, label: this.$t('bs-agents'), headerTitle: this.$t('bs-company-attendants')},
                        { key: 'interation', sortable: true, label: this.$t('bs-interactions'), headerTitle: this.$t('bs-analyze-d-interactions')},
                        { key: 'opened', sortable: true, label: this.$t('bs-opened'), headerTitle: this.$t('bs-analyze-d-opened')},
                        { key: 'finished', sortable: true, label: this.$t('bs-closed'), headerTitle: this.$t('bs-analyze-d-finished')},
                    ];

                    vm.avaliationFields = [
                        { key: 'chat_id', sortable: true, label: '#'},
                        { key: 'name', sortable: true, label: this.$t('bs-name')},
                        { key: 'hour', sortable: true, label: this.$t('bs-date') },
                        { key: 'stars_atendent', sortable: true, label: this.$t('bs-attendant') },
                        { key: 'stars_service', sortable: true, label: this.$t('bs-service') },
                        { key: 'message', sortable: true, label: this.$t('bs-message') },
                    ];
                    
                }else{
                    vm.agentsfields =  [
                        { key: 'attendant', sortable: true, label: this.$t('bs-agents'), headerTitle: this.$t('bs-company-attendants')},
                        { key: 'interation', sortable: true, label: this.$t('bs-interactions'), headerTitle: this.$t('bs-analyze-d-interactions')},
                        { key: 'opened', sortable: true, label: this.$t('bs-opened'), headerTitle: this.$t('bs-analyze-d-opened')},
                        { key: 'finished', sortable: true, label: this.$t('bs-closed'), headerTitle: this.$t('bs-analyze-d-finished')},
                    ];

                    vm.avaliationFields = [
                        { key: 'ticket_id', sortable: true, label: '#'},
                        { key: 'name', sortable: true, label: this.$t('bs-name')},
                        { key: 'hour', sortable: true, label: this.$t('bs-date') },
                        { key: 'stars_atendent', sortable: true, label: this.$t('bs-attendant') },
                        { key: 'stars_service', sortable: true, label: this.$t('bs-service') },
                        { key: 'message', sortable: true, label: this.$t('bs-message') },
                    ];
                }
                
                vm.getAvaliations();
                vm.getInfos();
            }else{  //VAZIO

                vm.openDetails = false;
                vm.tablesize = '600px';
                vm.itemselected = [];
                vm.avaliationselected = '';

                if(this.selectedChatTicket == 'CHAT'){
                    
                    vm.agentsfields =[
                        { key: 'attendant', sortable: true, label: this.$t('bs-agents'), headerTitle: this.$t('bs-company-attendants')},
                        { key: 'interation', sortable: true, label: this.$t('bs-interactions'), headerTitle: this.$t('bs-analyze-d-interactions')},
                        { key: 'opened', sortable: true, label: this.$t('bs-opened'), headerTitle: this.$t('bs-analyze-d-opened')},
                        { key: 'finished', sortable: true, label: this.$t('bs-closed'), headerTitle: this.$t('bs-analyze-d-finished') },
                        { key: 'moved_to_ticket', sortable: true, label: this.$t('bs-moved-to-ticket'), headerTitle: this.$t('bs-analyze-d-moved-ticket') },
                        { key: 'average', sortable: true, label: this.$t('bs-average-response-time'), headerTitle: this.$t('bs-analyze-d-average') },
                        { key: 'avg_queue_time', sortable: true, label: this.$t('bs-queue-time'), headerTitle: this.$t('bs-analyze-d-avg-queue-time') },
                        { key: 'media_stars_atendent', sortable: true, label: this.$t('bs-stars-atendent'), headerTitle: this.$t('bs-analyze-d-media-stars-atendent') },
                        { key: 'media_stars_service', sortable: true, label: this.$t('bs-stars-service'), headerTitle: this.$t('bs-analyze-d-media-stars-service') },
                    ];
                }else{
                    vm.agentsfields =[
                        { key: 'attendant', sortable: true, label: this.$t('bs-agents'), headerTitle: this.$t('bs-company-attendants')},
                        { key: 'interation', sortable: true, label: this.$t('bs-interactions'), headerTitle: this.$t('bs-analyze-d-interactions')},
                        { key: 'opened', sortable: true, label: this.$t('bs-opened'), headerTitle: this.$t('bs-analyze-d-opened')},
                        { key: 'finished', sortable: true, label: this.$t('bs-closed'), headerTitle: this.$t('bs-analyze-d-finished') },
                        { key: 'canceled', sortable: true, label: this.$t('bs-canceled'), headerTitle: this.$t('bs-analyze-d-canceled') }, //CANCELADO
                        { key: 'average', sortable: true, label: this.$t('bs-average-response-time'), headerTitle: this.$t('bs-analyze-d-average') },
                        { key: 'avg_queue_time', sortable: true, label: this.$t('bs-queue-time'), headerTitle: this.$t('bs-analyze-d-avg-queue-time') },
                        { key: 'media_stars_atendent', sortable: true, label: this.$t('bs-stars-atendent'), headerTitle: this.$t('bs-analyze-d-media-stars-atendent') },
                        { key: 'media_stars_service', sortable: true, label: this.$t('bs-stars-service'), headerTitle: this.$t('bs-analyze-d-media-stars-service') },
                    ];
                }
            }

            if(vm.subshow.show3){
                vm.getupdateStatus(item[0].id);
            }
        },
        changeSeletecAll(){
            if(this.selectedAll){
                this.itemselected = ['selectedAll'];
            }else{
                this.itemselected = [];
            }
            this.selectable = !this.selectedAll;
            this.onRowSelected(this.itemselected);
        },
        changeChatTicket(){

            // if(this.selectedChatTicket == 'ALL'){
            //     this.selectable = false;
            // }else{
            //     this.selectable = true;
            // }
            this.selectedTime2 = 'WEEK';
            this.onRowSelected(this.itemselected);
            this.updateGraph();
            this.getAgents();
        },
        changeDepartment(){
            this.getAgents();
        },
        changeTime(){
            if(this.selectedTime1 == 'CUSTOM'){
                this.showtimes = true;
                this.getAgents();
            }else{
                this.showtimes = false;
                this.getAgents();
            }
        },
        onRowSelectedAvaliation(avaliation){
            var vm = this;
            vm.avaliationselected = avaliation;
            if(avaliation.length){ //CLICOU
            }else{//VAZIO
            }
        },
        getInfos(){
            var vm = this;
            var url = `analyse/get-infos`;
            axios.get(url, {
                params:{
                    selectedAllagents: vm.selectedAll,
                    ChatorTicket:  vm.selectedChatTicket,
                    selectedDepartment: vm.selectedDepartment,
                    selectedTime1: vm.selectedTime1,
                    selectedTime2: vm.selectedTime2,
                    pInitialDate: vm.pInitialDate,
                    pFinalDate: vm.pFinalDate,
                    tz: this.tz,
                    company_user_id: vm.itemselected[0].company_user_id,
                }
            }).then(function(r_resposta){
                // console.log(r_resposta.data);
                vm.waiting_attendant = 0;
                vm.waiting_customer = 0;
                r_resposta.data.result_answer.forEach(item => {
                    if(item.company_user_company_department_id == null){
                        vm.waiting_attendant++;
                    }else{
                        vm.waiting_customer++;
                    }
                });
                vm.interaction_attendant = 0;
                vm.interaction_customer = 0;
                // interaction_customer
                r_resposta.data.result_interaction.forEach(item => {
                    if(item.hist_atend == 0){
                        vm.interaction_attendant += 1;
                    }
                    
                    if(item.hist_client == 0){
                        vm.interaction_customer += 1;
                    }
                });

                vm.total_time_service = vm.fancyTimeFormat(r_resposta.data.result_time);
                vm.total_chat_ticket = r_resposta.data.result_count;

            }).catch(function (error) {
                console.log(error);
            });
        },
        getAgents(){
            var vm = this;
            var url = `analyse/get-agents`;
            axios.get(url, {
                params:{
                    selectedAllagents: vm.selectedAll,
                    ChatorTicket:  vm.selectedChatTicket,
                    selectedDepartment: vm.selectedDepartment,
                    selectedTime1: vm.selectedTime1,
                    selectedTime2: vm.selectedTime2,
                    pInitialDate: vm.pInitialDate,
                    pFinalDate: vm.pFinalDate,
                    checkedDelete: vm.checkedDelete,
                    tz: this.tz,
                }
            }).then(function(r_resposta){
                // console.log(vm.agentsOptions);
                vm.agentsOptions = r_resposta.data.result;
                
                vm.agentsOptions.forEach(item => {
                    if(item.average == ''){
                        item.average = 0
                    }

                    if(item.avg_queue_time == null){
                        item.avg_queue_time = 0
                    }else{
                        item.avg_queue_time = vm.fancyTimeFormat(parseInt(item.avg_queue_time));
                    }

                    if(item.media_stars_atendent == null){
                        item.media_stars_atendent = '0%'
                    }else{
                        item.media_stars_atendent = parseFloat(item.media_stars_atendent) * 100 / 5 +'%';
                    }

                    if(item.media_stars_service == null){
                        item.media_stars_service = '0%'
                    }else{
                        item.media_stars_service = parseFloat(item.media_stars_service) * 100 / 5 +'%';
                    }
                });
                vm.getAgentsTime();
            }).catch(function (error) {

                console.log(error);
            });
        },
        getAgentsTime(){
            var vm = this;
            var url = `analyse/get-agents-time`;
            axios.get(url, {
                params:{
                    selectedAllagents: vm.selectedAll,
                    ChatorTicket:  vm.selectedChatTicket,
                    selectedDepartment: vm.selectedDepartment,
                    selectedTime1: vm.selectedTime1,
                    selectedTime2: vm.selectedTime2,
                    pInitialDate: vm.pInitialDate,
                    pFinalDate: vm.pFinalDate,
                    tz: this.tz,
                }
            }).then(function(r_resposta){
                var aux = 0;
                // console.log(r_resposta);
                vm.agentsOptions.forEach(item => {
                    r_resposta.data.result.forEach(item2 => {
                        if(item.id == item2.user_auth_id){
                            vm.agentsOptions[aux].average = vm.fancyTimeFormat(item2.average);
                        }
                    });
                    aux++;
                });
                vm.mountedExcel();
            }).catch(function (error) {
                console.log(error);
            });
        },
        getDepartments(){
            var vm = this;
            var url = `analyse/get-departments`;
            axios.get(url).then(function(r_resposta){
                // console.log(r_resposta.data);
                vm.optionsDepartment = r_resposta.data.result;
                vm.optionsDepartment.forEach(element => {
                    element.text = vm.$t(element.text);
                });

                vm.optionsDepartment.unshift({value: 'ALL', text: vm.$t('bs-all')});
            }).catch(function (error) {

                console.log(error);
            });
        },
        getAvaliations(){
            var vm = this;
            // console.log(vm.itemselected[0].id)
            axios.get('analyse/get-avaliations', {
                params:{
                    selectedAllagents: vm.selectedAll,
                    ChatorTicket:  vm.selectedChatTicket,
                    selectedDepartment: vm.selectedDepartment,
                    selectedTime1: vm.selectedTime1,
                    selectedTime2: vm.selectedTime2,
                    pInitialDate: vm.pInitialDate,
                    pFinalDate: vm.pFinalDate,
                    tz: this.tz,
                    company_user_id: vm.itemselected[0].company_user_id,
                }
            }).then(function(r_resposta){
                vm.avaliationOptions = r_resposta.data.result;

            }).catch(function (error) {

                console.log(error);
            });
        },
        getGraphics(){
            var vm = this;
            axios.get(`analyse/get-graphic`, {
                params:{
                    selectedAllagents: vm.selectedAll,
                    ChatorTicket:  vm.selectedChatTicket,
                    selectedDepartment: vm.selectedDepartment,
                    selectedTime1: vm.selectedTime1,
                    selectedTime2: vm.selectedTime2,
                    id: vm.itemselected[0].id,
                }
            }).then(function(r_resposta){

                //     chart2:{
                //     title: this.$t('bs-tk-chat-resolved-by-attendant'),
                //     loadedData: true,
                //     xLabels: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                //     lineData: [
                //         {
                //             label: "Chats Abertos",
                //             backgroundColor: "#00C38E",
                //             borderColor: "#00C38E",
                //             fill: false,
                //             data: [10, 15, 42, 55, 78, 47, 12, 65]
                //         },
                //         {
                //             label: "Chats Solucionados",
                //             backgroundColor: "#0080FC",
                //             borderColor: "#0080FC",
                //             fill: false,
                //             data: [40, 37, 67]
                //         },
                //         {
                //             label: "Chats Perdidos",
                //             backgroundColor: "#FF4872",
                //             borderColor: "#FF4872",
                //             fill: false,
                //             data: [6, 1, 10]
                //         }
                //     ]
                // },
                // chart3:{ // doughnut-chart
                //     title: this.$t('bs-tk-chat-resolved-by-attendant'),
                //     loadedData: true,
                //     xLabels: ['Bom', 'Ruim'],
                //     lineData: [
                //         {
                //             label: "Chats Abertos",
                //             backgroundColor: ["#00C38E", '#FF4872'] ,
                //             data: [100, 20]
                //         },
                //     ]
                // },
                // console.log(r_resposta.data);
                // console.log(vm.chart2.lineData);

                vm.chart2.lineData[0].data = [];
                vm.chart2.lineData[1].data = [];
                vm.chart2.lineData[2].data = [];
            
                // console.log(r_resposta.data.result);
                if(vm.selectedTime2 == 'YEAR'){
                    vm.chart2.lineData[0].data = [];
                    
                    // console.log(vm.chart2.lineData[0].data);
                    if(vm.selectedChatTicket == 'CHAT'){
                        r_resposta.data.result.forEach(item => {
                            // fazer verificação de filtro 
                            if(item.type == "Chats_Opened"){
                                vm.chart2.lineData[0].data[item.month] = item.count;
                            }
                            
                            if(item.type == "Chats_Closed"){
                                vm.chart2.lineData[1].data[item.month] = item.count;
                            }

                            if(item.type == "Chats_Closed"){
                                vm.chart2.lineData[2].data[item.month] = item.count;
                            }
                        });
                    }

                    if(vm.selectedChatTicket == 'TICKET'){
                        r_resposta.data.result.forEach(item => {
                            if(item.type == 'Tickets_Opened'){
                                vm.chart2.lineData[0].data[item.month] = item.count;
                            }

                            if(item.type == 'Chats_Canceled'){
                                vm.chart2.lineData[1].data[item.month] = item.count;
                            }

                            if(item.type == 'Tickets_Canceled'){
                                vm.chart2.lineData[2].data[item.month] = item.count;
                            }
                        });
                    }


                }else{
                    if(vm.selectedChatTicket == 'CHAT'){
                        r_resposta.data.result.forEach(item => {
                            // fazer verificação de filtro 
                            if(item.type == "Chats_Opened"){
                                vm.chart2.lineData[0].data.push(item.count);
                            }
                            
                            if(item.type == "Chats_Closed"){
                                vm.chart2.lineData[1].data.push(item.count);
                            }

                            if(item.type == "Chats_Closed"){
                                vm.chart2.lineData[2].data.push(item.count);
                            }
                        });
                    }
                    
                    if(vm.selectedChatTicket == 'TICKET'){
                        r_resposta.data.result.forEach(item => {
                            if(item.type == 'Tickets_Opened'){
                                vm.chart2.lineData[0].data.push(item.count);
                            }

                            if(item.type == 'Chats_Canceled'){
                                vm.chart2.lineData[1].data.push(item.count);
                            }

                            if(item.type == 'Tickets_Canceled'){
                                vm.chart2.lineData[2].data.push(item.count);
                            }
                        });
                    }
                }


                // console.log(vm.avaliationOptions);
                vm.chart3.loadedData = false;
                vm.chart3.xLabels = ['5', '4', '3', '2', '1'];
                vm.chart3.lineData[0].backgroundColor = ["#00C38E","#5ec300","#c0c300","#f78e05","#FF4872"] ,
                vm.chart3.lineData[0].data[0] = 0;
                vm.chart3.lineData[0].data[1] = 0;
                vm.chart3.lineData[0].data[2] = 0;
                vm.chart3.lineData[0].data[3] = 0;
                vm.chart3.lineData[0].data[4] = 0;
                vm.avaliationOptions.forEach(item => {

                    if(item.stars_atendent == 5 || item.stars_service == 5){
                        vm.chart3.lineData[0].data[0] = vm.chart3.lineData[0].data[0] + 1;
                    }
                    if(item.stars_atendent == 4 || item.stars_service == 4){
                        vm.chart3.lineData[0].data[1] = vm.chart3.lineData[0].data[1] + 1;
                    }
                    if(item.stars_atendent == 3 || item.stars_service == 3){
                        vm.chart3.lineData[0].data[2] = vm.chart3.lineData[0].data[2] + 1;
                    }
                    if(item.stars_atendent == 2 || item.stars_service == 2){
                        vm.chart3.lineData[0].data[3] = vm.chart3.lineData[0].data[3] + 1;
                    }
                    if(item.stars_atendent == 1 || item.stars_service == 1){
                        vm.chart3.lineData[0].data[4] = vm.chart3.lineData[0].data[4] + 1;
                    }

                });
                vm.chart3.loadedData = true;



            }).catch(function (error) {
                console.log(error);
            });
        },
        changeSeletecDeleted(){
            this.getAgents();
        },
        filterText(value){
            if(this.isMobile){
                return value.substr(0, 20) + '...';
            }else{
                if(value == null){
                    return '--';
                }
                if(value.length < 120){
                    return value;
                }else{
                    return value.substr(0, 120) + '...';
                }
            }
        },
        showIB(item, event){
            event.preventDefault();
			var vm = this;
			vm.show.show1 = false;
			vm.show.show2 = false;
			vm.show.show3 = false;
			vm.show.show4 = false;
			vm.show.show5 = false;
			vm.show.show6 = false;
			vm.show.show7 = false;
			vm.show.show8 = false;
			vm.ss.ss1 = 'tab';
			vm.ss.ss2 = 'tab';
			vm.ss.ss3 = 'tab';
			vm.ss.ss4 = 'tab';
			vm.ss.ss5 = 'tab';
			vm.ss.ss6 = 'tab';
			if(item == 1){
				vm.show.show1 = true;
				vm.ss.ss1 = 'tab active';
			}else if(item == 2){
				vm.show.show2 = true;
				vm.ss.ss2 = 'tab active';
			}else if(item == 3){
				vm.show.show3 = true;
				vm.ss.ss3 = 'tab active';
			}else if(item == 4){
				vm.show.show4 = true;
				vm.ss.ss4 = 'tab active';
			}
		},
        subshowIB(item, event){
            event.preventDefault();
            var vm = this;
			vm.subshow.show1 = false;
			vm.subshow.show2 = false;
			vm.subshow.show3 = false;
			vm.subshow.show4 = false;
			vm.subshow.show5 = false;
			vm.subshow.show6 = false;
			vm.subshow.show7 = false;
			vm.subshow.show8 = false;
			vm.subss.ss1 = 'tab';
			vm.subss.ss2 = 'tab';
			vm.subss.ss3 = 'tab';
			vm.subss.ss4 = 'tab';
			vm.subss.ss5 = 'tab';
			vm.subss.ss6 = 'tab';
			if(item == 1){
				vm.subshow.show1 = true;
				vm.subss.ss1 = 'tab active';
			}else if(item == 2){
				vm.subshow.show2 = true;
				vm.subss.ss2 = 'tab active';
                vm.updateChartSelectedTime2();
                vm.updateGraph();
			}else if(item == 3){
				vm.subshow.show3 = true;
				vm.subss.ss3 = 'tab active';
                vm.getupdateStatus(vm.itemselected[0].id);
			}else if(item == 4){
				vm.subshow.show4 = true;
				vm.subss.ss4 = 'tab active';
			}
        },
        getupdateStatus(id){
            var vm = this;
            axios.get('analyse/get-status-user', {
                params:{
                    id,
                    tz: this.tz,
                    company_user_id: vm.itemselected[0].company_user_id,
                }
            }).then(function(r_resposta){
                vm.statusListUser = r_resposta.data;
            }).catch(function (error) {

                console.log(error);
            });
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
                .locale(this.session_user.language)
                .calendar();
        },
        fancyTimeFormat(duration){   
            // Hours, minutes and seconds
            var hrs = ~~(duration / 3600);
            var mins = ~~((duration % 3600) / 60);
            var secs = ~~duration % 60;

            // Output like "1:01" or "4:03:59" or "123:03:59"
            var ret = "";

            if (hrs > 0) {
                ret += "" + hrs + ":" + (mins < 10 ? "0" : "");
            }

            ret += "" + mins + ":" + (secs < 10 ? "0" : "");
            ret += "" + secs + "s";
            return ret;
        },
    },
};
</script>

<style lang="scss" scoped>

    .start{
        color:#00C38E;
        color:#5ec300;
        color:#c0c300;
        color:#c37500;
        color:#FF4872;
    }

    .fl{
        float: right;
        color: green;
    }
    .deleted{
        color: rgb(253, 67, 67);
    }
    .backgroudcustom{
        background-color:white;
        overflow: auto;
        max-height:220px;
        overflow-x: none;
        word-break: all;
    }

    .textalign{
        text-align: right;
        float: right;
    } 

    .bs-green{
        color:#01d4b9;
    }

    .bs-red{
        color:#fa4b57;
    }
    .bs-yellow{
        color:#ffb244;
    }

    .bs-offline{
        color:#666666;
    }

    .father{
        height: 116px;
        margin: 10px;
        position: relative;
        background: #F5F5F5 0% 0% no-repeat padding-box;
        box-shadow: 0px 3px 6px #00000029;
        border-radius: 4px;
        opacity: 1;
    }

    .son{
        margin: 0;
        position: absolute;
        top: 50%;
        left: 50%;
        margin-right: -50%;
        transform: translate(-50%, -50%);
        font: normal normal normal 12px/12px Muli;
        letter-spacing: 0px;
        color: #333333;
    }

    .cardsPrivate{
        background: #F5F5F5 0% 0% no-repeat padding-box;
        box-shadow: 0px 3px 6px #00000029;
        border-radius: 4px;
        opacity: 1;
        height: 116px;
    }

    .caret {
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .mtcustom{
        margin-top: 0.6rem !important;
    }
    .customfont{
        font: 12px;
    }
    .material-icons-two-tone {
        font-size: 20px !important;
        filter: invert(62%) sepia(38%) saturate(317%) hue-rotate(173deg) brightness(92%) contrast(89%);
    }
    .active .material-icons-two-tone {
        filter: invert(34%) sepia(98%) saturate(987%) hue-rotate(191deg) brightness(100%) contrast(120%);
    }

    .bs-m-spacing{
        padding: 3px;
        text-transform: uppercase;
        margin-left: 1px;
        font-size: 10px;
    }

    @media screen and (max-width: 576px) {

        .bs-m-spacing{
            justify-content: center;
            text-align: center;
            margin-left: 1px;
            font-size: 10px;
        }

        .active, .tab:hover{
            color: #0080fc;
            border-bottom: 3px solid #0080fc;
            font-size: 10px;
        }
    }

    @media screen and (max-width: 1024px) {

        .bs-m-spacing{
            justify-content: center;
            text-align: center;
            margin-left: 1px;
            font-size: 10px;
        }

        .active, .tab:hover{
            color: #0080fc;
            border-bottom: 3px solid #0080fc;
        }
    }

    .tab{
        padding: 8px;
        color:#A4A4A4;
        border-bottom: 1px solid #BDBDBD;
        font-weight: bold;
        text-decoration: none;
    }

    .active, .tab:hover{
        color: #0080fc;
        padding-left: 5px;
        border-bottom: 3px solid #0080fc;
    }

    .bui-alert {
        background: url(/images/meta/corner.png) left top no-repeat,
            url(/images/meta/wave.png) right bottom/100% no-repeat,
            transparent linear-gradient(180deg, #5e81f4 0%, #1665d8 100%);
        border-radius: 16px;
        border: none;
        padding-top: 1.2rem;
        padding-left: 2rem;
        color: #fff;
        p {
            font-size: 0.85rem;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.8);
        }
        .btn {
            background: #ffffff38;
            border: unset;
            font-weight: normal !important;
            text-transform: capitalize;
            border-radius: 8px;
        }
    }



</style>