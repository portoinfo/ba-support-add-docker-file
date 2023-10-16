<template>
	<div>
		<b-container fluid>
            <!-- title -->
			<b-row class="mb-5 px-2" cols='1' cols-md='2' align-h="between" align-v="center" no-gutters>
				<b-col class="py-2">
                    <h3 class="bs-title" v-html="title"></h3>
					<span class="bs-subtitle" v-html="`${subtitle}${csname}`"></span>
				</b-col>
			</b-row>
      
			<!-- line one -->
            <b-row no-gutters cols='1' cols-sm="2" cols-lg="4" align-h="start" align-v="stretch" class="mb-3">
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
            <b-row no-gutters cols='1' cols-sm="2" cols-lg="4" align-h="start" align-v="stretch" class="mb-3">
                <b-col v-for="(card, k) in qualitativeCards" :key="k" class="mt-2 px-2">
                    <qualitative-card
                        v-bind="card"
                    ></qualitative-card>
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
		</b-container>
	</div>
</template>

<script>

export default {
    name: 'home-employee-dashboard',
	props:{
        usuario: Object,
        csname: String,
        csid: String,
        cslogo: String,
        gravatar: String,
        attendant_id: String,
        base_url: {
            type: String,
            default: ''
        }
	},
	data(){
		return {
            // header stats
		    title: this.$t('bs-menu-home'),
            subtitle: `${this.$t('bs-welcome-to')} `,

			stats: [
                {
                    icon: 'person',
                    background: 'blue-gradiente-bg',
					value: `15`,
					title: this.$t('bs-chats-in-progress'),
				},
				{
                    icon: 'chat_bubble',
                    background: 'red-gradiente-bg',
					value: `15`,
					title: this.$t('bs-closed-resolved-chats'),
				},
				{
                    icon: 'group',
                    background: 'yellow-gradiente-bg',
					value: `20`,
					title: this.$t('bs-tickets-in-progress'),
				},
				{
                    icon: 'perm_phone_msg',
                    background: 'blue-gradiente-bg',
					value: `23`,
					title: this.$t('bs-closed-resolved-tickets'),
				},
            ],

            qualitativeCards: [
                {
                    title: `${this.$t('bs-chat')} - ${this.$t('bs-service-satisfaction-level')}`,
                    positiveLabel: this.$t('bs-good'),
                    positiveValue: 8,
                    negativeLabel: this.$t('bs-bad'),
                    negativeValue: 2
                },
                {
                    title: `${this.$t('bs-chat')} - ${this.$t('bs-level-of-satisfaction-with-the-attendant')}`,
                    positiveLabel: this.$t('bs-good'),
                    positiveValue: 5,
                    negativeLabel: this.$t('bs-bad'),
                    negativeValue: 0
                },

                {
                    title: `${this.$t('bs-ticket')} - ${this.$t('bs-service-satisfaction-level')}`,
                    positiveLabel: this.$t('bs-good'),
                    positiveValue: 8,
                    negativeLabel: this.$t('bs-bad'),
                    negativeValue: 2
                },
                {
                    title: `${this.$t('bs-ticket')} - ${this.$t('bs-level-of-satisfaction-with-the-attendant')}`,
                    positiveLabel: this.$t('bs-good'),
                    positiveValue: 5,
                    negativeLabel: this.$t('bs-bad'),
                    negativeValue: 0
                }
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
            isFirstLoad: true
		}
    },
    methods: {
        getSummaryCards: async function(department_id, attendant_id, initial_date, final_date) {
            let url = `${this.base_url}/home/get-summary-cards-agent-home-dashboard`
            if(!this.isFirstLoad){
                this.$spinner.show()
            }
            try{
                let response = await axios.post(url, {
                    company_id: this.csid,
                    attendant_id: attendant_id,
                    department_id: department_id,
                    initial_date: initial_date,
                    final_date: final_date

                })
                if(response.data.success){
                    // chats
                    this.stats[0].value = response.data[0].in_progress
                    this.stats[1].value = response.data[0].closed + response.data[0].resolved

                    // tickets
                    this.stats[2].value = response.data[1].in_progress
                    this.stats[3].value = response.data[1].closed + response.data[1].resolved
                } else {
                    this.$snotify.error(this.$t('bs-error-fetching-departments'), this.$t('bs-error'));
                }
            } catch(e) {
                console.log('FAILURE!!');
            } finally {
                if(!this.isFirstLoad){
                    this.$spinner.hide()
                }
            }
        },

        getQualitativeCards: async function(department_id, attendant_id, initial_date, final_date) {
            let url = `${this.base_url}/home/get-qualitative-cards-agent-home-dashboard`
            if(!this.isFirstLoad){
                this.$spinner.show()
            }
            try{
                let response = await axios.post(url, {
                    company_id: this.csid,
                    attendant_id: attendant_id,
                    department_id: department_id,
                    initial_date: initial_date,
                    final_date: final_date

                })
                if(response.data.success){

                    this.qualitativeCards[0].count_geral = response.data[0].count_geral
                    this.qualitativeCards[1].count_geral = response.data[0].count_geral
                    this.qualitativeCards[2].count_geral = response.data[1].count_geral
                    this.qualitativeCards[3].count_geral = response.data[1].count_geral

                    // chats
                    this.qualitativeCards[0].media_stars = response.data[0].media_stars_service
                    this.qualitativeCards[1].media_stars = response.data[0].media_stars_atendent
                    
                    // Tickets
                    this.qualitativeCards[2].media_stars = response.data[1].media_stars_service
                    this.qualitativeCards[3].media_stars = response.data[1].media_stars_atendent


                    // chats
                    this.qualitativeCards[0].count_1 = response.data[0].count_1_service
                    this.qualitativeCards[0].count_2 = response.data[0].count_2_service
                    this.qualitativeCards[0].count_3 = response.data[0].count_3_service
                    this.qualitativeCards[0].count_4 = response.data[0].count_4_service
                    this.qualitativeCards[0].count_5 = response.data[0].count_5_service
                    
                    this.qualitativeCards[1].count_1 = response.data[0].count_1_atendent
                    this.qualitativeCards[1].count_2 = response.data[0].count_2_atendent
                    this.qualitativeCards[1].count_3 = response.data[0].count_3_atendent
                    this.qualitativeCards[1].count_4 = response.data[0].count_4_atendent
                    this.qualitativeCards[1].count_5 = response.data[0].count_5_atendent

                    // tickets
                    this.qualitativeCards[2].count_1 = response.data[1].count_1_service
                    this.qualitativeCards[2].count_2 = response.data[1].count_2_service
                    this.qualitativeCards[2].count_3 = response.data[1].count_3_service
                    this.qualitativeCards[2].count_4 = response.data[1].count_4_service
                    this.qualitativeCards[2].count_5 = response.data[1].count_5_service
                    
                    this.qualitativeCards[3].count_1 = response.data[1].count_1_atendent
                    this.qualitativeCards[3].count_2 = response.data[1].count_2_atendent
                    this.qualitativeCards[3].count_3 = response.data[1].count_3_atendent
                    this.qualitativeCards[3].count_4 = response.data[1].count_4_atendent
                    this.qualitativeCards[3].count_5 = response.data[1].count_5_atendent

                    // MODELO ANTIGO
                    // // chats
                    // this.qualitativeCards[0].positiveValue = response.data[0].count_positive_service
                    // this.qualitativeCards[0].negativeValue = response.data[0].count_negative_service
                    // this.qualitativeCards[1].positiveValue = response.data[0].count_positive_atendent
                    // this.qualitativeCards[1].negativeValue = response.data[0].count_negative_atendent

                    // // tickets
                    // this.qualitativeCards[2].positiveValue = response.data[1].count_positive_service
                    // this.qualitativeCards[2].negativeValue = response.data[1].count_negative_service
                    // this.qualitativeCards[3].positiveValue = response.data[1].count_positive_atendent
                    // this.qualitativeCards[3].negativeValue = response.data[1].count_negative_atendent

                } else {
                    this.$snotify.error(this.$t('bs-error-fetching-departments'), this.$t('bs-error'));
                }
            } catch(e) {
                console.log('FAILURE!!');
            } finally {
                if(!this.isFirstLoad){
                    this.$spinner.hide()
                }
            }
        },


        loadWeekBarChart: async function(department_id, attendant_id) {

            let url = `${this.base_url}/home/get-bar-chart-agent-home-dashboard`
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
                    this.$snotify.error(this.$t('bs-error-fetching-bar-chart'), this.$t('bs-error'));
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
            let url = `${this.base_url}/home/get-bar-chart-agent-home-dashboard`
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
                    this.$snotify.error(this.$t('bs-error-fetching-bar-chart'), this.$t('bs-error'));
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
            let url = `${this.base_url}/home/get-bar-chart-agent-home-dashboard`
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
                    this.$snotify.error(this.$t('bs-error-fetching-bar-chart'), this.$t('bs-error'));
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
        loadBarChart: async function (type, department_id, attendant_id) {
            this.chart1.loadedData = false
            switch(type.toLowerCase()) {
                case 'week':
                default:
                    await this.loadWeekBarChart(department_id, attendant_id)
                    break;
                case 'month':
                    await this.loadMonthBarChart(department_id, attendant_id)
                    break;
                case 'year':
                    await this.loadYearBarChart(department_id, attendant_id)
                    break;
            }
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
        },
        loadWeekLineChart: async function(department_id, attendant_id) {
            let url = `${this.base_url}/home/get-line-chart-agent-home-dashboard`
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
                            label: this.usuario.name,
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
                    this.$snotify.error(this.$t('bs-error-fetching-line-chart'), this.$t('bs-error'));
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
            let url = `${this.base_url}/home/get-line-chart-agent-home-dashboard`
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
                            label: this.usuario.name,
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
                    this.$snotify.error(this.$t('bs-error-fetching-line-chart'), this.$t('bs-error'));
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
            let url = `${this.base_url}/home/get-line-chart-agent-home-dashboard`
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
                            label: this.usuario.name,
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
                    this.$snotify.error(this.$t('bs-error-fetching-line-chart'), this.$t('bs-error'));
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
    },
    watch: {
        selectedPeriod: function(newVal, oldVal) {
            this.loadBarChart(newVal, 0, this.attendant_id)
        },
        selectedPeriod2: function(newVal, oldVal) {
            this.loadLineChart(newVal, 0, this.attendant_id)
        }
    },
	created: async function(){
        await this.getSummaryCards(0, this.attendant_id, null, null)
        await this.getQualitativeCards(0, this.attendant_id, null, null)
        await this.loadBarChart(this.selectedPeriod, 0, this.attendant_id)
        await this.loadLineChart(this.selectedPeriod2, 0, this.attendant_id)
        if(this.isFirstLoad){
            this.isFirstLoad = false
        }
        // this.loadBarChart(this.selectedPeriod)
    }
};
</script>

<style scoped lang="scss">
</style>
