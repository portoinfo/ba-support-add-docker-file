<template>
    <!-- componente removido 15/02/2021-->
    <b-container fluid>
        <!-- title -->
        <b-row class="mb-3 px-2" cols='1' cols-lg='3' align-h="between" align-v="center" no-gutters>
            <b-col class="py-2">
                <h3 class="bs-title" v-html="title"></h3>
                <span class="bs-subtitle" v-html="subtitle"></span>
            </b-col>
            <b-col class="py-2 px-lg-2 text-right">
                <b-button @click="listGroup" variant="btn bs-btn-back btn-block-md">
                    <i class="fa fa-bars" aria-hidden="true"></i>&nbsp;{{btnGroupList}}
                </b-button>
            </b-col>
            <b-col class="py-2 px-lg-2">
                <b-button @click="createGroup" variant="btn bs-btn-add btn-block">
                    <i class="fa fa-plus secondary" aria-hidden="true"></i> {{btnNewGroup}}
                </b-button>
            </b-col>
        </b-row>
        <b-row class="mb-3 px-2" cols='1' cols-lg="5" align-h="start" align-v="center" no-gutters>
            <b-col class='sm-1'>
                <b-form-select v-model="selectedGroup" :options="selectGroupOptions" class="dashboard_select"></b-form-select>
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
        <b-row no-gutters cols='1' cols-sm="2" cols-lg='3' align-h="between" align-v="stretch" class="mb-4">
            <b-col v-for="(stat, k) in stats1" :key="k" class="mt-2 px-2" >
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
                                <b-col md='6' class="pt-2 py-md-0">
                                    <h4 v-text="chart1.title"></h4>
                                </b-col>
                                <b-col md='4' class="pt-2 py-md-0">
                                    <multiselect
                                        v-model="selectedAttendantOccurrences"
                                        :options="selectAttendantOptions" multiple
                                        label="text"
                                        track-by="text"
                                        :limit="2"
                                        :max="5"
                                        :placeholder="placeholderSelectAttendant"
                                    ></multiselect>
                                </b-col>
                                <b-col md='2' class="pl-md-2 pt-2 py-md-0">
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
                                <b-col md='6' class="pt-2 py-md-0">
                                    <h4 v-text="chart2.title"></h4>
                                </b-col>
                                <b-col md='4' class="pt-2 py-md-0">
                                    <multiselect
                                        v-model="selectedAttendantVariations"
                                        :options="selectAttendantOptions"
                                        multiple
                                        label="text"
                                        track-by="text"
                                        :limit="2"
                                        :max="5"
                                        :placeholder="placeholderSelectAttendant"
                                    ></multiselect>
                                </b-col>
                                <b-col md='2' class="pl-md-2 pt-2 py-md-0">
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
    <!-- componente removido 15/02/2021-->
</template>

<script>
    export default { 
        name: 'group-dashboard',
        props: {
            list_groups_url: {
                type: String,
                default: ''
            },
            create_group_url: {
                type: String,
                default: ''
            },
            base_url: {
                type: String,
                default: ''
            },
        },
        data: function() {
            return {
                title: this.$t('bs-group-dashboard'),
                subtitle: this.$t('bs-group-dashboard'),
                btnGroupList: this.$t('bs-groups-table'),
                btnNewGroup: this.$t('bs-register-new-group'),
                selectedGroup: 'all',
                selectGroupOptions: [
                    {
                        text: this.$t('bs-all'),
                        value: 'all',
                    },
                    {
                        text: 'Grupo 1',
                        value: 'group1',
                    },
                    {
                        text: 'Grupo 2',
                        value: 'group2',
                    },
                    {
                        text: 'Grupo 3',
                        value: 'group3',
                    },
                    {
                        text: 'Grupo 4',
                        value: 'group4',
                    },
                    {
                        text: 'Grupo 5',
                        value: 'group5',
                    },
                    {
                        text: 'Grupo 6',
                        value: 'group6',
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
                ],

                placeholderSelectAttendant: this.$t('bs-select-attendantd'),
                selectedAttendantOccurrences: [],
                selectedAttendantVariations: [],
                selectAttendantOptions: [ 
                    {
                        text: 'Atendente 1',
                        value: 'attendant1',
                    },
                    {
                        text: 'Atendente 2',
                        value: 'attendant2',
                    },
                    {
                        text: 'Atendente 3',
                        value: 'attendant3',
                    },
                    {
                        text: 'Atendente 4',
                        value: 'attendant4',
                    },
                    {
                        text: 'Atendente 5',
                        value: 'attendant5',
                    },
                    {
                        text: 'Atendente 6',
                        value: 'attendant6',
                    },
                ],


                // Bar chart
                chart1:{
                    title: this.$t('bs-occurrences-by-attendants'),
                    loadedData: false,
                    xLabels: [this.$t('bs-in-open-tickets'), this.$t('bs-closed-tickets'), this.$t('bs-in-open-chats'), this.$t('bs-closed-chats')],
                    barData: []
                },

                // line chart
                chart2:{
                    title: this.$t('bs-variations-by-attendants'),
                    loadedData: false,
                    xLabels: [this.$t('bs-in-open-tickets'), this.$t('bs-closed-tickets'), this.$t('bs-in-open-chats'), this.$t('bs-closed-chats')],
                    lineData: []
                },
            }
        },
        methods: {
            listGroup: function() {
                window.open(this.list_groups_url, '_self')
            },
            createGroup: function () {
                window.open(this.create_group_url, '_self')
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
            loadChartOcurrences(attendants) {
                this.chart1.loadedData = false

                this.$set(this.chart1, 'xLabels', [this.$t('bs-in-open-tickets'), this.$t('bs-closed-tickets'), this.$t('bs-in-open-chats'), this.$t('bs-closed-chats')])

                let datasets = []
                for(let key in attendants) {
                    datasets.push({
                        label: attendants[key].text,
                        backgroundColor: this.getBgColor(key),
                        data: [Math.random()*100, Math.random()*100, Math.random()*100, Math.random()*100],
                        barPercentage: 0.666666667,
                        categoryPercentage: 0.32,
                    })
                }
                this.$set(this.chart1, 'barData', datasets)
                this.chart1.loadedData = true
            },
            loadChartVariations(attendants) {
                this.chart2.loadedData = false

                this.$set(this.chart2, 'xLabels', [this.$t('bs-in-open-tickets'), this.$t('bs-closed-tickets'), this.$t('bs-in-open-chats'), this.$t('bs-closed-chats')])

                let datasets = []
                for(let key in attendants) {
                    datasets.push({
                        label: attendants[key].text,
                        borderColor: this.getBgColor(key),
                        fill: false,
                        sttepedLine: true,
                        data: [Math.random()*100, Math.random()*100, Math.random()*100, Math.random()*100],
                        lineTension: 0,
                    })
                }
                this.$set(this.chart2, 'lineData', datasets)
                this.chart2.loadedData = true
            },
        },
        watch: {
            selectedAttendantOccurrences: function (newVal, oldVal) {
                this.loadChartOcurrences(newVal)
            },
            selectedAttendantVariations: function (newVal, oldVal) {
                this.loadChartVariations(newVal)
            },
        },
        mounted(){
            this.$set(this.$data, 'selectedAttendantOccurrences', this.selectAttendantOptions.slice(0, 5))
            this.$set(this.$data, 'selectedAttendantVariations', this.selectAttendantOptions.slice(0, 5))
        }
    }
</script>

<style lang="scss" scoped>

</style>