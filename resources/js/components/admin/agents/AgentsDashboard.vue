<template>
    <b-container fluid>
        <!-- title -->
        <b-row class="px-2" cols='1' cols-lg='3' align-h="between" align-v="center" no-gutters>
            <b-col class="py-2">
                <h3 class="bs-title" v-html="title"></h3>
                <span class="bs-subtitle" v-html="subtitle"></span>
            </b-col>
            <b-col class="py-2 px-lg-2 text-right">
                <b-button @click="listAgent" variant="btn bs-btn-back btn-block-md">
                    <i class="fa fa-bars" aria-hidden="true"></i>&nbsp;{{btnAgentList}}
                </b-button>
            </b-col>
            <b-col class="py-2 px-lg-2">
                <b-button @click="createAgent" variant="btn bs-btn-add btn-block">
                    <i class="fa fa-user-plus secondary" aria-hidden="true"></i> {{btnNewAgent}}
                </b-button>
            </b-col>
        </b-row>
        <analyze-body
            :session_user="usuario"
            :timezones="timezones"
        ></analyze-body>
        <br>

        <b-row class="mb-3 px-2" cols='1' cols-lg="5" align-h="start" align-v="center" no-gutters>
            <b-col class='py-2 sm-1'>
                <b-form-select v-model="selectedDepartment" :options="selectDepartmentOptions" class="dashboard_select"></b-form-select>
            </b-col>
            <b-col class='py-2 sm-1 ml-lg-2'>
                <b-form-select v-model="selectedAgent" :options="selectAgentOptions" class="dashboard_select"></b-form-select>
            </b-col>
        </b-row>
        <!-- line one -->
        <b-row no-gutters cols='1' cols-sm="2" align-h="start" align-v="stretch" class="mb-3">
            <b-col v-for="(stat, k) in stats" :key="k" class="mt-2 px-2">
                <summary-card
                    :circularBgIcon="{
                        icon: {
                            name: stat.icon,
                            size: 26,
                            color: 'white'
                        },
                        bgClass: stat.background,
                    }"
                    :title="stat.title"
                    :value="stat.value"
                >
                    <circular-bg-icon class="mr-1" slot="icon" slot-scope="prop" v-bind="prop.circularBgIcon">
                        <i
                            class="material-icons"
                            slot="icon"
                            slot-scope="prop"
                            :style="{
                                color: prop.icon.color,
                                fontSize: `${prop.icon.size}px!important`
                            }"
                        >{{prop.icon.name}}</i>
                    </circular-bg-icon>
                </summary-card>
            </b-col>
        </b-row>

        <!-- line two -->
        <b-row no-gutters cols='1' cols-sm="2" align-h="start" align-v="stretch" class="mb-3">
            <b-col v-for="(stat, k) in stats1" :key="k" class="mt-2 px-2">
                <summary-card
                    :circularBgIcon="{
                        icon: {
                            name: stat.icon,
                            size: 26,
                            color: 'white'
                        },
                        bgClass: stat.background,
                    }"
                    :title="stat.title"
                    :value="stat.value"
                >
                    <circular-bg-icon class="mr-1" slot="icon" slot-scope="prop" v-bind="prop.circularBgIcon">
                        <i
                            class="material-icons"
                            slot="icon"
                            slot-scope="prop"
                            :style="{
                                color: prop.icon.color,
                                fontSize: `${prop.icon.size}px!important`
                            }"
                        >{{prop.icon.name}}</i>
                    </circular-bg-icon>
                </summary-card>
            </b-col>
        </b-row>

        <!-- line three -->
        <b-row no-gutters cols='1' cols-sm="2" cols-lg='4' align-h="between" align-v="stretch" class="mb-4">
            <b-col v-for="(stat, k) in stats2" :key="k" class="mt-2 px-2" >
                <summary-card
                    :circularBgIcon="{
                        icon: {
                            name: stat.icon,
                            size: 26,
                            color: 'white'
                        },
                        bgClass: stat.background,
                    }"
                    :title="stat.title"
                    :value="stat.value"
                >
                    <circular-bg-icon class="mr-1" slot="icon" slot-scope="prop" v-bind="prop.circularBgIcon">
                        <i
                            class="material-icons"
                            slot="icon"
                            slot-scope="prop"
                            :style="{
                                color: prop.icon.color,
                                fontSize: `${prop.icon.size}px!important`
                            }"
                        >{{prop.icon.name}}</i>
                    </circular-bg-icon>
                </summary-card>
            </b-col>
        </b-row>

        <!-- chart -->
        <b-row class="mb-3 px-2" align-v="stretch">
            <b-col sm="12" class="py-3">
                <!-- card charts -->
                <b-card style="height: 100%!important;">
                    <b-card-header class="px-0 bg-transparent">
                        <b-container fluid class="px-0">
                            <b-row cols-sm="1" align-h="between" align-v="stretch" no-gutters>
                                <b-col md='7' class="pt-2 py-md-0">
                                    <h4 v-text="chart1.title"></h4>
                                </b-col>
                                <b-col md='3' class="pl-md-2 pt-2 py-md-0">
                                    <b-form-select v-model="selectedPeriod" :options="selectOptionsPeriod" class="dashboard_select"></b-form-select>
                                </b-col>
                            </b-row>
                        </b-container>
                    </b-card-header>
                    <b-card-text class="px-0 py-2">
                        <bar-chart
                            v-if="chart1.loadedData"
                            :chartData="{
                                labels: chart1.xLabels,
                                datasets: chart1.barData
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
                                            suggestedMax: 100,
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
                        ></bar-chart>
                        <div v-else class="text-center">
                            Loading...
                        </div>
                        </b-card-text>
                </b-card>
            </b-col >
        </b-row>

        <!-- chart2 -->
        <b-row class="mb-5 px-2" align-v="stretch">
            <b-col sm="12" class="py-3">
                <!-- card charts -->
                <b-card style="height: 100%!important;">
                    <b-card-header class="px-0 bg-transparent">
                        <b-container fluid class="px-0">
                            <b-row cols-sm="1" align-h="between" align-v="stretch" no-gutters>
                                <b-col md='7' class="pt-2 py-md-0">
                                    <h4 v-text="chart2.title"></h4>
                                </b-col>
                                <b-col md='3' class="pl-md-2 pt-2 py-md-0">
                                    <b-form-select v-model="selectedPeriod2" :options="selectOptionsPeriod" class="dashboard_select"></b-form-select>
                                </b-col>
                            </b-row>
                        </b-container>
                    </b-card-header>
                    <b-card-text class="px-0 py-2">
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
                                            suggestedMax: 100,
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
                            Loading...
                        </div>
                        </b-card-text>
                </b-card>
            </b-col >
        </b-row>
        <b-row class="mb-5 px-2" cols="1" cols-lg='2' align-v="stretch">
            <b-col class="mb-4 mb-lg-0 row row-cols-1 align-items-between no-gutters">
                <b-row class="mb-3 mb-xs-0 mx-0" align-v="stretch" no-gutters>
                    <b-col class="local-title">
                        {{section1.title}} <b-form-select v-model="ticketPeriodSelected" :options="selectTicketPeriodOptions" class="dashboard_select my-2"></b-form-select>
                    </b-col>
                </b-row>
                <b-row cols='1' cols-lg='2' align-v="stretch" class="mx-0" no-gutters>
                    <b-col :class="['py-1', key > 0 ? 'pl-lg-2' : '']" v-for="(card, key) in section1.cards" :key="key">
                        <time-card v-bind="card"></time-card>
                    </b-col>
                </b-row>
            </b-col>
            <b-col class="row row-cols-1 align-items-stretch no-gutters">
                <b-row class="mb-3 mx-0" align-v="stretch" no-gutters>
                    <b-col class="local-title">
                        {{section2.title}} <b-form-select v-model="chatPeriodSelected" :options="selectChatPeriodOptions" class="dashboard_select my-2"></b-form-select>
                    </b-col>
                </b-row>
                <b-row cols='1' cols-lg='2' align-v="stretch" class="mx-0" no-gutters>
                    <b-col :class="['py-1', key > 0 ? 'pl-lg-2' : '']" v-for="(card, key) in section2.cards" :key="key">
                        <time-card v-bind="card"></time-card>
                    </b-col>
                </b-row>
            </b-col>
        </b-row>
    </b-container>
</template>

<script>
export default {
    name: 'agents-dashboard',
    props: {
        base_url: {
            type: String,
            default: ''
        },
        list_agent_url: {
            type: String,
            default: ''
        },
        create_agent_url: {
            type: String,
            default: ''
        },
        csid: String,
        timezones: Object,
        usuario: Object
    },
    data: function() {
        return {
            title: this.$t('bs-attendant-dashboard'),
            subtitle: this.$t('bs-attendant-dashboard'),
            btnAgentList: this.$t('bs-list-of-attendants'),
            btnNewAgent: this.$t('bs-add-new-attendant'),

            selectedAgent: null,
            selectAgentOptions: [
                {
                    text: this.$t('bs-all'),
                    value: 0,
                },
                {
                    text: 'Atendente 1',
                    value: 'Agent1',
                },
                {
                    text: 'Atendente 2',
                    value: 'Agent2',
                },
                {
                    text: 'Atendente 3',
                    value: 'Agent3',
                },
                {
                    text: 'Atendente 4',
                    value: 'Agent4',
                },
                {
                    text: 'Atendente 5',
                    value: 'Agent5',
                },
                {
                    text: 'Atendente 6',
                    value: 'Agent6',
                },
            ],

            selectedDepartment: null,
            selectDepartmentOptions: [
                {
                    text: this.$t('bs-all'),
                    value: 0,
                },
                {
                    text: 'Departamento 1',
                    value: 'dep1',
                },
                {
                    text: 'Departamento 2',
                    value: 'dep2',
                },
                {
                    text: 'Departamento 3',
                    value: 'dep3',
                },
                {
                    text: 'Departamento 4',
                    value: 'dep4',
                },
            ],

            selectedPeriod: 'week',
            selectedPeriod2: 'week',
            selectOptionsPeriod: [
                {
                    text: this.$t('bs-week'),
                    value: 'week',
                },
                {
                    text: this.$t('bs-month'),
                    value: 'month',
                },
                {
                    text: this.$t('bs-year'),
                    value: 'year',
                },
            ],

            stats: [
                {
                    icon: 'email',
                    background: 'yellow-gradiente-bg',
                    value: '15%',
                    title: this.$t('bs-percentage-ticket-attended'),
                },
                {
                    icon: 'chat_bubble',
                    background: 'blue-gradiente-bg',
                    value: '15%',
                    title: this.$t('bs-percentage-chat-attended'),
                },
            ],

            stats1: [
                {
                    icon: 'email',
                    background: 'yellow-gradiente-bg',
                    value: '15%',
                    title: this.$t('bs-percentage-of-tickets-in-attendance'),
                },
                {
                    icon: 'chat_bubble',
                    background: 'blue-gradiente-bg',
                    value: '15%',
                    title: this.$t('bs-percentage-of-chat-in-attendance'),
                },
            ],

            stats2: [
                {
                    icon: 'email',
                    background: 'yellow-gradiente-bg',
                    value: '0',
                    title: this.$t('bs-total-tickets'),
                },
                {
                    icon: 'chat_bubble',
                    background: 'blue-gradiente-bg',
                    value: '0',
                    title: this.$t('bs-total-chats'),
                },
                {
                    icon: 'event_note',
                    background: 'green-gradiente-bg',
                    value: '0',
                    title: this.$t('bs-avg-score-for-calls'),
                },
                {
                    icon: 'event_note',
                    background: 'green-gradiente-bg',
                    value: '0',
                    title: this.$t('bs-avg-score-for-attendants'),
                },
            ],

            ticketPeriodSelected: 'LAST_24_HOURS',
            selectTicketPeriodOptions: [
                {
                    text: this.$t('bs-last-24-hours'),
                    value: 'LAST_24_HOURS',
                },
                {
                    text: this.$t('bs-last-7-days'),
                    value: 'LAST_7_DAYS',
                },
                {
                    text: this.$t('bs-last-30-days'),
                    value: 'LAST_30_DAYS',
                },
                {
                    text: this.$t('bs-last-365-days'),
                    value: 'LAST_365_DAYS',
                },
            ],

            chatPeriodSelected: 'LAST_24_HOURS',
            selectChatPeriodOptions: [
                {
                    text: this.$t('bs-last-24-hours'),
                    value: 'LAST_24_HOURS',
                },
                {
                    text: this.$t('bs-last-7-days'),
                    value: 'LAST_7_DAYS',
                },
                {
                    text: this.$t('bs-last-30-days'),
                    value: 'LAST_30_DAYS',
                },
                {
                    text: this.$t('bs-last-365-days'),
                    value: 'LAST_365_DAYS',
                },
            ],

            // Bar chart
            chart1:{
                title: this.$t('bs-tk-chats-opened-closed-by-period'),
                loadedData: false,
                xLabels: ['10/01', '11/01', '12/01', '13/01', '14/01', '15/01', '16/01'],
                barData: []
            },

            // line chart
            chart2:{
                title: this.$t('bs-tk-chat-resolved-by-attendant'),
                loadedData: false,
                xLabels: ['10/01', '11/01', '12/01', '13/01', '14/01', '15/01', '16/01'],
                lineData: []
            },

            section1: {
                title: this.$t('bs-tickets'),
                cards: [
                    {
                        title: this.$t('bs-average-queue-time'),
                        durationInSeconds: 3669
                    },
                    {
                        title: this.$t('bs-average-service-time'),
                        durationInSeconds: 3769
                    }
                ]
            },
            section2: {
                title: this.$t('bs-chats'),
                cards: [
                    {
                        title: this.$t('bs-average-queue-time'),
                        durationInSeconds: 3969
                    },
                    {
                        title: this.$t('bs-average-service-time'),
                        durationInSeconds: 3969
                    }
                ]
            },
            isFirstLoad: true
        }
    },
    methods: {
        listAgent: function() {
           window.open(this.list_agent_url, '_self')
        },
        createAgent: function () {
           window.open(this.create_agent_url, '_self')
        },
        getDepartments: async function(){
            let url = `${this.base_url}/agents/get-departments-agents-dashboard`
            if(!this.isFirstLoad){
                this.$spinner.show()
            }
            try{
                let response = await axios.post(url, { 
                    company_id: this.csid
                })
                if(response.data.success){
                    delete response.data.success
                    response.data = Object.values(response.data)
                    let len = Object.keys(response.data).length
                    for(let i = 0; i< len; i++) {
                        if(response.data[i].deleted == 1 ){
                            response.data[i].text = `${response.data[i].text} (${this.$t('bs-deleted1')})`
                        }
                    }
                    response.data.unshift({
                        text: this.$t('bs-all'),
                        value: 0,
                    })
                    this.$set(this.$data, 'selectDepartmentOptions', response.data)
                    this.selectedDepartment = response.data[0].value

                } else {
                    vm.$snotify.error(vm.$t('bs-error-fetching-departments'), vm.$t('bs-error'));
                }
            } catch(e) {
                console.log('FAILURE!!');
            } finally {
                if(!this.isFirstLoad){
                    this.$spinner.hide()
                }
            }
        },
        getAttendants: async function(department){
            let url = `${this.base_url}/agents/get-attendants-agents-dashboard`
            if(!this.isFirstLoad){
                this.$spinner.show()
            }
            try{
                let response = await axios.post(url, { 
                    company_id: this.csid,
                    department_id: department
                })
                if(response.data.success){
                    delete response.data.success
                    response.data = Object.values(response.data)
                    let len = Object.keys(response.data).length
                    for(let i = 0; i< len; i++) {
                        if(response.data[i].deleted == 1 ){
                            response.data[i].text = `${response.data[i].text} (${this.$t('bs-deleted1')})`
                        }
                    }
                    response.data.unshift({
                        text: this.$t('bs-all'),
                        value: 0,
                    })
                    this.$set(this.$data, 'selectAgentOptions', response.data)
                    if( this.selectedAgent == response.data[0].value ) {
                        // quando o valor vindo da request e igual, o watcher nao aciona
                        // forcando update
                        await this.refreshPageData(this.selectedDepartment, this.selectedAgent)
                    } else {
                        this.selectedAgent = response.data[0].value
                    }

                } else {
                    vm.$snotify.error(vm.$t('bs-error-fetching-attendants'), vm.$t('bs-error'));
                }
            } catch(e) {
                console.log('FAILURE!!');
            } finally {
                if(!this.isFirstLoad){
                    this.$spinner.hide()
                }
            }
        },
        getSummaryCards: async function(department_id, attendant_id){
            let url = `${this.base_url}/agents/get-totals-and-percentages-agents-dashboard`
            if(!this.isFirstLoad){
                this.$spinner.show()
            }
            try{
                let response = await axios.post(url, { 
                    company_id: this.csid,
                    department_id: department_id,
                    attendant_id: attendant_id
                })
                if(response.data.success){

                    //chats
                    this.stats[1].value = response.data[0].perc_fechados+'%'
                    this.stats1[1].value = response.data[0].perc_em_atendimento+'%'
                    this.stats2[1].value = response.data[0].count

                    //tickets
                    this.stats[0].value = response.data[1].perc_fechados+'%'
                    this.stats1[0].value = response.data[1].perc_em_atendimento+'%'
                    this.stats2[0].value = response.data[1].count
                } else {
                    vm.$snotify.error(vm.$t('bs-error-fetching-summary-card'), vm.$t('bs-error'));
                }
            } catch(e) {
                console.log('FAILURE!!');
            } finally {
                if(!this.isFirstLoad){
                    this.$spinner.hide()
                }
            }
        },
        getAvgSummaryCards: async function(department_id, attendant_id){
            let url = `${this.base_url}/agents/get-general-avg-for-attendances-agents-dashboard`
            if(!this.isFirstLoad) {
                this.$spinner.show();
            }
            try{
                let response = await axios.post(url, { 
                    company_id: this.csid,
                    department_id: department_id,
                    attendant_id: attendant_id
                })
                if(response.data.success){
                    if( response.data[0].media_stars_atendent === null) {
                        response.data[0].media_stars_atendent = this.$t('bs-no-data-yet')
                    } else {
                        response.data[0].media_stars_atendent = (this.$i18n.locale == "pt_BR") ? response.data[0].media_stars_atendent.replace(/\./g, ",") : response.data[0].media_stars_atendent
                    }

                    if(response.data[0].media_stars_service === null) {
                        response.data[0].media_stars_service = this.$t('bs-no-data-yet')
                    } else {
                        response.data[0].media_stars_service = (this.$i18n.locale == "pt_BR") ? response.data[0].media_stars_service.replace(/\./g, ",") : response.data[0].media_stars_service
                    }
                    this.stats2[2].value = response.data[0].media_stars_service
                    this.stats2[3].value = response.data[0].media_stars_atendent
                } else {
                    vm.$snotify.error(vm.$t('bs-error-fetching-summary-card'), vm.$t('bs-error'));
                }
            } catch(e) {
                console.log('FAILURE!!');
            } finally {
                if(!this.isFirstLoad) {
                    this.$spinner.hide();
                }
            }
        },
        loadWeekBarChart: async function(department_id, attendant_id) {
            let url = `${this.base_url}/agents/get-bar-chart-agents-dashboard`
            if(!this.isFirstLoad) {
                this.$spinner.show();
            }
            try{
                let response = await axios.post(url, { 
                    company_id: this.csid,
                    department_id: department_id,
                    attendant_id: attendant_id,
                    period: this.selectedPeriod
                })
                if(response.data.success){
                    let _this = this
                    this.$set(this.chart1, 'xLabels', response.data.labels.map(function(value) {
                        return (_this.$i18n.locale == "pt_BR") ? `${value.dia}/${value.mes}` : `${value.mes}/${value.dia}`
                    }) )
                    this.$set(this.chart1, 'barData',  [
                        {
                            label: this.$t('bs-in-open-tickets'),
                            data: response.data.tickets_opened,
                            backgroundColor: this.getBgColor(0),
                            barPercentage: 0.666666667,
                            categoryPercentage: 0.32
                        },
                        {
                            label: this.$t('bs-closed-tickets'),
                            data: response.data.tickets_closed,
                            backgroundColor: this.getBgColor(1),
                            barPercentage: 0.666666667,
                            categoryPercentage: 0.32
                        },
                        {
                            label: this.$t('bs-in-open-chats'),
                            data: response.data.chats_opened,
                            backgroundColor: this.getBgColor(2),
                            barPercentage: 0.666666667,
                            categoryPercentage: 0.32
                        },
                        {
                            label: this.$t('bs-closed-chats'),
                            data: response.data.chats_closed,
                            backgroundColor: this.getBgColor(3),
                            barPercentage: 0.666666667,
                            categoryPercentage: 0.32
                        },
                    ])
                } else {
                    vm.$snotify.error(vm.$t('bs-error-fetching-bar-chart'), vm.$t('bs-error'));
                }
            } catch(e) {
                console.log('FAILURE!!', e);
            } finally {
                this.chart1.loadedData = true
                if(!this.isFirstLoad) {
                    this.$spinner.hide();
                }
            }
            this.chart1.loadedData = true
        },
        loadMonthBarChart: async function(department_id, attendant_id) {
            let url = `${this.base_url}/agents/get-bar-chart-agents-dashboard`
            if(!this.isFirstLoad) {
                this.$spinner.show();
            }
            try{
                let response = await axios.post(url, { 
                    company_id: this.csid,
                    department_id: department_id,
                    attendant_id: attendant_id,
                    period: this.selectedPeriod
                })
                if(response.data.success){
                    let _this = this
                    this.$set(this.chart1, 'xLabels', response.data.labels.map(function(value) {
                        switch(value) {
                            case 1:
                                return _this.$t('bs-first-week')
                            case 2:
                                return _this.$t('bs-second-week')
                            case 3:
                                return _this.$t('bs-third-week')
                            case 4:
                                return _this.$t('bs-fourth-week')
                        }

                    }) )
                    this.$set(this.chart1, 'barData',  [
                        {
                            label: this.$t('bs-in-open-tickets'),
                            data: response.data.tickets_opened,
                            backgroundColor: this.getBgColor(0),
                            barPercentage: 0.666666667,
                            categoryPercentage: 0.32
                        },
                        {
                            label: this.$t('bs-closed-tickets'),
                            data: response.data.tickets_closed,
                            backgroundColor: this.getBgColor(1),
                            barPercentage: 0.666666667,
                            categoryPercentage: 0.32
                        },
                        {
                            label: this.$t('bs-in-open-chats'),
                            data: response.data.chats_opened,
                            backgroundColor: this.getBgColor(2),
                            barPercentage: 0.666666667,
                            categoryPercentage: 0.32
                        },
                        {
                            label: this.$t('bs-closed-chats'),
                            data: response.data.chats_closed,
                            backgroundColor: this.getBgColor(3),
                            barPercentage: 0.666666667,
                            categoryPercentage: 0.32
                        },
                    ])
                } else {
                    vm.$snotify.error(vm.$t('bs-error-fetching-bar-chart'), vm.$t('bs-error'));
                }
            } catch(e) {
                console.log('FAILURE!!', e);
            } finally {
                this.chart1.loadedData = true
                if(!this.isFirstLoad) {
                    this.$spinner.hide();
                }
            }
            this.chart1.loadedData = true
        },
        loadYearBarChart: async function(department_id, attendant_id) {
            let url = `${this.base_url}/agents/get-bar-chart-agents-dashboard`
            if(!this.isFirstLoad) {
                this.$spinner.show();
            }
            try{
                let response = await axios.post(url, { 
                    company_id: this.csid,
                    department_id: department_id,
                    attendant_id: attendant_id,
                    period: this.selectedPeriod
                })
                if(response.data.success){
                    let _this = this
                    this.$set(this.chart1, 'xLabels', response.data.labels.map(function(value) {
                        return (_this.$i18n.locale == "pt_BR") ? `${value.mes}/${value.ano}` : `${value.mes}/${value.ano}`
                    }) )
                    this.$set(this.chart1, 'barData',  [
                        {
                            label: this.$t('bs-in-open-tickets'),
                            data: response.data.tickets_opened,
                            backgroundColor: this.getBgColor(0),
                            barPercentage: 0.666666667,
                            categoryPercentage: 0.32
                        },
                        {
                            label: this.$t('bs-closed-tickets'),
                            data: response.data.tickets_closed,
                            backgroundColor: this.getBgColor(1),
                            barPercentage: 0.666666667,
                            categoryPercentage: 0.32
                        },
                        {
                            label: this.$t('bs-in-open-chats'),
                            data: response.data.chats_opened,
                            backgroundColor: this.getBgColor(2),
                            barPercentage: 0.666666667,
                            categoryPercentage: 0.32
                        },
                        {
                            label: this.$t('bs-closed-chats'),
                            data: response.data.chats_closed,
                            backgroundColor: this.getBgColor(3),
                            barPercentage: 0.666666667,
                            categoryPercentage: 0.32
                        },
                    ])
                } else {
                    vm.$snotify.error(vm.$t('bs-error-fetching-bar-chart'), vm.$t('bs-error'));
                }
            } catch(e) {
                console.log('FAILURE!!');
            } finally {
                this.chart1.loadedData = true
                if(!this.isFirstLoad) {
                    this.$spinner.hide();
                }
            }
            this.chart1.loadedData = true
        },
        loadBarChart: async function(type, department, attendant) {
            this.chart1.loadedData = false
            switch(type.toLowerCase()) {
                case 'week':
                default:
                    await this.loadWeekBarChart(department, attendant)
                    break;
                case 'month':
                    await this.loadMonthBarChart(department, attendant)
                    break;
                case 'year':
                    await this.loadYearBarChart(department, attendant)
                    break;
            }
        },
        getLabel(key, arr) {
            let obj =  arr.find(el => el.value == key)
            return obj != undefined ? obj.text : 'Not found'
        },
        loadWeekLineChart: async function(department_id, attendant_id) {
            let url = `${this.base_url}/agents/get-line-chart-agents-dashboard`
            if(!this.isFirstLoad) {
                this.$spinner.show();
            }
            try{
                let response = await axios.post(url, { 
                    company_id: this.csid,
                    department_id: department_id,
                    attendant_id: attendant_id,
                    period: this.selectedPeriod2
                })
                if(response.data.success){
                    let _this = this
                    this.$set(this.chart2, 'xLabels', response.data.labels.map(function(value) {
                        return (_this.$i18n.locale == "pt_BR") ? `${value.dia}/${value.mes}` : `${value.mes}/${value.dia}`
                    }) )

                    let datasets = []
                    let i = 0;
                    for(let key in response.data.attendants) {
                        datasets.push({
                            label: this.getLabel(key, this.selectAgentOptions),
                            backgroundColor: this.getBgColor(i),
                            borderColor: this.getBgColor(i),
                            fill: false,
                            sttepedLine: true,
                            data: response.data.attendants[key],
                            lineTension: 0,
                        })
                        i++
                    }
                    this.$set(this.chart2, 'lineData', datasets)
                } else {
                    vm.$snotify.error(vm.$t('bs-error-fetching-line-chart'), vm.$t('bs-error'));
                }
            } catch(e) {
                console.log('FAILURE!!');
            } finally {
                this.chart2.loadedData = true
                if(!this.isFirstLoad) {
                    this.$spinner.hide();
                }
            }
        },
        loadMonthLineChart: async function(department_id, attendant_id) {
            let url = `${this.base_url}/agents/get-line-chart-agents-dashboard`
            if(!this.isFirstLoad) {
                this.$spinner.show();
            }
            try{
                let response = await axios.post(url, { 
                    company_id: this.csid,
                    department_id: department_id,
                    attendant_id: attendant_id,
                    period: this.selectedPeriod2
                })
                if(response.data.success){
                    let _this = this
                    this.$set(this.chart2, 'xLabels', response.data.labels.map(function(value) {
                        switch(value) {
                            case 1:
                                return _this.$t('bs-first-week')
                            case 2:
                                return _this.$t('bs-second-week')
                            case 3:
                                return _this.$t('bs-third-week')
                            case 4:
                                return _this.$t('bs-fourth-week')
                        }

                    }) )

                    let datasets = []
                    let i = 0;
                    for(let key in response.data.attendants) {
                        datasets.push({
                            label: this.getLabel(key, this.selectAgentOptions),
                            backgroundColor: this.getBgColor(i),
                            borderColor: this.getBgColor(i),
                            fill: false,
                            sttepedLine: true,
                            data: response.data.attendants[key],
                            lineTension: 0,
                        })
                        i++
                    }
                    this.$set(this.chart2, 'lineData', datasets)
                } else {
                    vm.$snotify.error(vm.$t('bs-error-fetching-line-chart'), vm.$t('bs-error'));
                }
            } catch(e) {
                console.log('FAILURE!!', e);
            } finally {
                this.chart2.loadedData = true
                if(!this.isFirstLoad) {
                    this.$spinner.hide();
                }
            }
        },
        loadYearLineChart: async function (department_id, attendant_id) {
            let url = `${this.base_url}/agents/get-line-chart-agents-dashboard`
            if(!this.isFirstLoad) {
                this.$spinner.show();
            }
            try{
                let response = await axios.post(url, { 
                    company_id: this.csid,
                    department_id: department_id,
                    attendant_id: attendant_id,
                    period: this.selectedPeriod2
                })
                if(response.data.success){
                    let _this = this
                    this.$set(this.chart2, 'xLabels', response.data.labels.map(function(value) {
                        return (_this.$i18n.locale == "pt_BR") ? `${value.mes}/${value.ano}` : `${value.mes}/${value.ano}`
                    }) )

                    let datasets = []
                    let i = 0;
                    for(let key in response.data.attendants) {
                        datasets.push({
                            label: this.getLabel(key, this.selectAgentOptions),
                            backgroundColor: this.getBgColor(i),
                            borderColor: this.getBgColor(i),
                            fill: false,
                            sttepedLine: true,
                            data: response.data.attendants[key],
                            lineTension: 0,
                        })
                        i++
                    }
                    this.$set(this.chart2, 'lineData', datasets)
                } else {
                    vm.$snotify.error(vm.$t('bs-error-fetching-line-chart'), vm.$t('bs-error'));
                }
            } catch(e) {
                console.log('FAILURE!!');
            } finally {
                this.chart2.loadedData = true
                if(!this.isFirstLoad) {
                    this.$spinner.hide();
                }
            }
        },
        loadLineChart: async function(type, department, attendant) {
            this.chart2.loadedData = false
            switch(type.toLowerCase()) {
                case 'week':
                default:
                    await this.loadWeekLineChart(department, attendant)
                    break;
                case 'month':
                    await this.loadMonthLineChart(department, attendant)
                    break;
                case 'year':
                    await this.loadYearLineChart(department, attendant)
                    break;
            }
        },
        getTicketTimeCharts: async function(period, department_id, attendant_id) {
            let url = `${this.base_url}/agents/get-ticket-time-cards-agents-dashboard`
            if(!this.isFirstLoad) {
                this.$spinner.show();
            }
            try{
                let response = await axios.post(url, { 
                    company_id: this.csid,
                    department_id: department_id,
                    attendant_id: attendant_id,
                    period: period
                })
                if(response.data.success){
                    this.section1.cards[0].durationInSeconds = response.data[0].avg_queue_time == null ? 0 : response.data[0].avg_queue_time
                    this.section1.cards[1].durationInSeconds = response.data[0].avg_service_time == null ? 0 : response.data[0].avg_service_time
                } else {
                    vm.$snotify.error(vm.$t('bs-error-fetching-time-card'), vm.$t('bs-error'));
                }
            } catch(e) {
                console.log('FAILURE!!');
            } finally {
                this.chart1.loadedData = true
                if(!this.isFirstLoad) {
                    this.$spinner.hide();
                }
            }
        },
        getChatTimeCharts: async function(period, department_id, attendant_id) {
            let url = `${this.base_url}/agents/get-chat-time-cards-agents-dashboard`
            if(!this.isFirstLoad) {
                this.$spinner.show();
            }
            try{
                let response = await axios.post(url, { 
                    company_id: this.csid,
                    department_id: department_id,
                    attendant_id: attendant_id,
                    period: period
                })
                if(response.data.success){
                    this.section2.cards[0].durationInSeconds = response.data[0].avg_queue_time == null ? 0 : response.data[0].avg_queue_time
                    this.section2.cards[1].durationInSeconds = response.data[0].avg_service_time == null ? 0 : response.data[0].avg_service_time
                } else {
                    vm.$snotify.error(vm.$t('bs-error-fetching-time-card'), vm.$t('bs-error'));
                }
            } catch(e) {
                console.log('FAILURE!!');
            } finally {
                this.chart1.loadedData = true
                if(!this.isFirstLoad) {
                    this.$spinner.hide();
                }
            }
        },
        refreshPageData: async function(department, attendant) {
            await this.getSummaryCards(department, attendant)
            await this.getAvgSummaryCards(department, attendant)
            await this.loadBarChart(this.selectedPeriod, department, attendant)
            await this.loadLineChart(this.selectedPeriod2, department, attendant)
            await this.getTicketTimeCharts(this.ticketPeriodSelected, department, attendant)
            await this.getChatTimeCharts(this.chatPeriodSelected, department, attendant)
        },
        getBgColor(index) {
            switch(parseInt(index)) {
                case 0:
                    return '#1665D8'
                case 1:
                    return '#00DBDB'
                case 2:
                    return '#DBFC01'
                case 3:
                    return '#F67D34'
                case 4:
                default:
                    return '#333333'
            }
        }
    },
    watch: {
        selectedDepartment: async function (newVal, oldVal) {
            this.getAttendants(newVal)

        },
        selectedAgent: async function (newVal, oldVal) {
            await this.refreshPageData(this.selectedDepartment, newVal)
            if(this.isFirstLoad){
                this.isFirstLoad = false
            }
        },
        selectedPeriod: function(newVal, oldVal) {
            this.loadBarChart(newVal, this.selectedDepartment, this.selectedAgent)
        },
        selectedPeriod2: function(newVal, oldVal) {
            this.loadLineChart(newVal, this.selectedDepartment, this.selectedAgent)
        },
        ticketPeriodSelected: function(newVal, oldVal) {
            this.getTicketTimeCharts(newVal, this.selectedDepartment, this.selectedAgent)
        },
        chatPeriodSelected: function(newVal, oldVal) {
            this.getChatTimeCharts(newVal, this.selectedDepartment, this.selectedAgent)
        },
    },
    created: async function (){
        this.getDepartments()
    }
}
</script>

<style lang="scss" scoped>
.local-select .vs__dropdown-toggle .vs__selected-options{
    flex-wrap: flex-wrap!important;
}
.material-icon{
    margin-top: 10px!important;
}
.local-title{
    font: normal normal 800 25px/31px Muli;
    letter-spacing: 0px;
    color: #333333;
}
</style>