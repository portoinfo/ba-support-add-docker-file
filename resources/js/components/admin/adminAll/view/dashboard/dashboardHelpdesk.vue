<template>
    <div class='ba-container ba-flex ba-gp-1 ba-ai-baseline ba-jc-center ba-f-wrap'>
        <div class='ba-card ba-card-grid ba-w-100'>
            <div class='ba-top'>
                <span class='ba-title'>Activity</span>
            </div>
            <div class='ba-content'>
                <div class='ba-card ba-statistics ba-300'>
                    <div class='ba-top'>
                        <icons-custom name="activity-1-icon"></icons-custom>
                        <span class='ba-title'>{{activity[0].label}}</span>
                    </div>
                    <div class='ba-content ba-jc-fs'>
                        <span class='ba-data'>{{activity[0].value}}</span>
                    </div>
                </div>
                <hr class='ba-hr-column'>
                <div class='ba-card ba-statistics ba-300'>
                    <div class='ba-top'>
                        <icons-custom name="activity-2-icon"></icons-custom>
                        <span class='ba-title'>{{activity[0].label}}</span>
                    </div>
                    <div class='ba-content ba-jc-fs'>
                        <span class='ba-data'>{{activity[0].value}}</span>
                    </div>
                </div>
                <hr class='ba-hr-column'>
                <div class='ba-card ba-statistics ba-300'>
                    <div class='ba-top'>
                        <icons-custom name="activity-3-icon"></icons-custom>
                        <span class='ba-title'>{{activity[2].label}}</span>
                    </div>
                    <div class='ba-content ba-jc-fs'>
                        <span class='ba-data'>{{activity[2].value}}</span>
                    </div>
                </div>
                <hr class='ba-hr-column'>
                <div class='ba-card ba-statistics ba-300'>
                    <div class='ba-top'>
                        <icons-custom name="activity-4-icon"></icons-custom>
                        <span class='ba-title'>{{activity[3].label}}</span>
                    </div>
                    <div class='ba-content ba-jc-fs'>
                        <span class='ba-data'>{{activity[3].value}}</span>
                    </div>
                </div>
                <hr class='ba-hr-column'>
                <div class='ba-card ba-statistics ba-300'>
                    <div class='ba-top'>
                        <icons-custom name="activity-5-icon"></icons-custom>
                        <span class='ba-title'>{{activity[4].label}}</span>
                    </div>
                    <div class='ba-content ba-jc-fs'>
                        <span class='ba-data'>{{activity[4].value}}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class='ba-flex ba-gp-1 ba-w-100'>
            <div class="ba-w-100">
                <div class='ba-card'>
                    <div class='ba-top'>
                        <span class='ba-title ba-pd-b-1'>{{$t('bs-tickets')}}</span>
                    </div>
                    <div class='ba-content ba-w-100 select-helpdesk'>
                        <select class="ba-input ba-w-100" v-model="ticketPeriodSelected">
                            <option v-for="(item, index) in selectTicketPeriodOptions" :key="'tickets'+index" :value="item.value">{{item.text}}</option>
                        </select>
                    </div>
                    <div class='ba-flex ba-gp-1 ba-w-100'>
                        <div class="box1 ba-mg-t-1 ba-w-100">
                            <div class="ba-mg-1">
                                <div class='box1-t'>{{$t('bs-queue-time')}}</div>
                                <div class='box1-s'>{{convertSecondstoDate(ticketTimeAverageQueueTime)}}</div>
                            </div>
                        </div>
                        <div class="box1 ba-mg-t-1 ba-w-100">
                            <div class="ba-mg-1">
                                <div class='box1-t'>{{$t('bs-service-time')}}</div>
                                <div class='box1-s'>{{convertSecondstoDate(ticketTimeAverageServiceTime)}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ba-w-100">
                <div class='ba-card'>
                    <div class='ba-top'>
                        <span class='ba-title ba-pd-b-1'>{{$t('bs-chats')}}</span>
                    </div>
                    <div class='ba-content ba-w-100 select-helpdesk'>
                        <select class="ba-input ba-w-100" v-model="chatPeriodSelected">
                            <option v-for="(item, index) in selectChatPeriodOptions" :key="'chats'+index" :value="item.value">{{item.text}}</option>
                        </select>
                    </div>
                    <div class='ba-flex ba-gp-1 ba-w-100'>
                        <div class="box1 ba-mg-t-1 ba-w-100">
                            <div class="ba-mg-1">
                                <div class='box1-t'>{{$t('bs-queue-time')}}</div>
                                <div class='box1-s'>{{convertSecondstoDate(chatTimeAverageQueueTime)}}</div>
                            </div>
                        </div>
                        <div class="box1 ba-mg-t-1 ba-w-100">
                            <div class="ba-mg-1">
                                <div class='box1-t'>{{$t('bs-service-time')}}</div>
                                <div class='box1-s'>{{convertSecondstoDate(chatTimeAverageServiceTime)}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class='ba-card ba-card-grid ba-w-100'>
            <div class='ba-top'>
                <span class='ba-title'>Memberbytes</span>
            </div>
            <div class='ba-content'>
                <div class='ba-card ba-statistics ba-300'>
                    <div class='ba-top'>
                        <icons-custom name="memberbytes-1-icon"></icons-custom>
                        <span class='ba-title'>{{memberbytes[0].label}}</span>
                    </div>
                    <div class='ba-content ba-jc-fs'>
                        <span class='ba-data'>{{memberbytes[0].value}}</span>
                    </div>
                </div>
                <hr class='ba-hr-column'>
                <div class='ba-card ba-statistics ba-300'>
                    <div class='ba-top'>
                        <icons-custom name="memberbytes-2-icon"></icons-custom>
                        <span class='ba-title'>{{memberbytes[0].label}}</span>
                    </div>
                    <div class='ba-content ba-jc-fs'>
                        <span class='ba-data'>{{memberbytes[0].value}}</span>
                    </div>
                </div>
                <hr class='ba-hr-column'>
                <div class='ba-card ba-statistics ba-300'>
                    <div class='ba-top'>
                        <icons-custom name="memberbytes-3-icon"></icons-custom>
                        <span class='ba-title'>{{memberbytes[2].label}}</span>
                    </div>
                    <div class='ba-content ba-jc-fs'>
                        <span class='ba-data'>{{memberbytes[2].value}}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class='ba-flex ba-gp-1 ba-w-100'>
            <div class="ba-w-100">
                <div class='ba-card'>
                    <div class='ba-top'>
                        <span class='ba-title'>{{$t('bs-tk-chat-open')}}</span>
                        <div class='ba-box-items ba-flex ba-gp-1'>
                            <select class='ba-select ba-md' name='selectct' id='selectct' v-model="selectedTC">
                                <option v-for="(item, index) in selectOptionsTC" :key="'chats'+index" :value="item.value">{{item.text}}</option>
                            </select>
                        </div>
                    </div>
                    <canvas class='ba-content' id="myChart"></canvas>
                </div>
            </div>
            <div>
                <div>
                    <div cols="11">
                        <div class='ba-card m-content'>
                            <div class='ba-top'>
                                <span class='ba-title-c'>{{$t("bs-closed-chats-previous-day")}}</span>
                            </div>
                            
                            <div class='ba-content center-card'>
                                <div class="progress-info">
                                    <span class="left-text">{{chartChat.today}}</span>
                                    <span class="right-text">{{percent(chartChat.today,chartChat.totalToday)}}%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" v-bind:style="{ width: progressBarWidth }"></div>
                                </div>
                                <div class="progress-info">
                                    <span class="left-text"></span>
                                    <span class="right-text">{{ $t('bs-today') }}: {{chartChat.today}}/{{chartChat.totalToday}}</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" v-bind:style="{ width: progressBarWidth }"></div>
                                </div>
                                <div class="progress-info">
                                    <span class="left-text"></span>
                                    <span class="right-text c-t-y">{{ $t('bs-yesterday') }}: {{chartChat.yesterday}}/{{chartChat.totalYesterday}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ba-mg-t-1">
                        <div class='ba-card m-content'>
                            <div class='ba-top'>
                                <span class='ba-title-c'>{{$t("bs-closed-tickets-previous-day")}}</span>
                            </div>
                            <div class='ba-content'>
                                <div class='ba-content center-card'>
                                    <div class="progress-info">
                                        <span class="left-text">{{chartTicket.today}}</span>
                                        <span class="right-text">{{percent(chartTicket.today,chartTicket.totalToday)}}%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress" v-bind:style="{ width: progressBarWidth }"></div>
                                    </div>
                                    <div class="progress-info">
                                        <span class="left-text"></span>
                                        <span class="right-text">{{ $t('bs-today') }}:  {{chartTicket.today}}/{{chartTicket.totalToday}}</span>
                                    </div>

                                    <div class="progress-bar">
                                        <div class="progress" v-bind:style="{ width: progressBarWidth }"></div>
                                    </div>
                                    <div class="progress-info">
                                        <span class="left-text"></span>
                                        <span class="right-text c-t-y">{{ $t('bs-yesterday') }}: {{chartTicket.yesterday}}/{{chartTicket.totalYesterday}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import iconsCustom from '../../util/icons/iconsCustom.vue';
export default {
    components:{
        iconsCustom,
    },
	data(){
		return {
            activity: [
                { key: 'open_chats', label: this.$t('bs-in-open-chats'), value:10 },
                { key: 'in_progress_chats',  label: this.$t('bs-chats-in-progress'), value:0 },
                { key: 'open_tickets',  label: this.$t('bs-in-open-tickets'), value:2 },
                { key: 'in_progress_tickets',  label: this.$t('bs-tickets-in-progress'), value:1 },
                { key: 'attendants_on',  label: this.$t('bs-attendant') +' '+this.$t('bs-online'), value:1 },
            ],
            memberbytes: [
                { key: 'departments', label: this.$t('bs-departments'), value:15 },
                { key: 'separate',  label:'', value:0 },
                { key: 'groups',  label: this.$t('bs-groups'), value:8 },
                { key: 'separate',  label:'', value:0 },
                { key: 'agents',  label: this.$t('bs-agents'), value:25 },
            ],
            progress: 0,
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
            ticketTimeAverageQueueTime: this.$t('bs-no-data-period'),
            ticketTimeAverageServiceTime: this.$t('bs-no-data-period'),

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
            chatTimeAverageQueueTime: this.$t('bs-no-data-period'),
            chatTimeAverageServiceTime: this.$t('bs-no-data-period'),
            selectedTC: "month",
            selectOptionsTC: [
                {
                text: this.$t("bs-week"),
                value: "week",
                },
                {
                text: this.$t("bs-month"),
                value: "month",
                },
                {
                text: this.$t("bs-year"),
                value: "year",
                },
            ],
            chartInstance: null,
            // Progress Chats
            chartChat: {
                title: this.$t("bs-closed-chats-previous-day"),
                today: 0,
                yesterday: 0,
                totalToday: 0,
                totalYesterday: 0,
            },
            // Progress Tickets
            chartTicket: {
                title: this.$t("bs-closed-tickets-previous-day"),
                today: 0,
                yesterday: 0,
                totalToday: 0,
                totalYesterday: 0,
            },
		}
	},
    mounted: async function () {
        // this.generateGraphic();
        await this.getSummaryCards();
        await this.getTicketTimeCharts(this.ticketPeriodSelected);
        await this.getChatTimeCharts(this.chatPeriodSelected);
        await this.getMemberByte(this.chatPeriodSelected);
        await this.getloadChart(this.selectedTC);
        await this.getProgressCards();
        // await this.loadChart(this.selected);
        // if (this.isFirstLoad) {
        //     this.isFirstLoad = false;
        // }
    },
    computed: {
        progressBarWidth() {
            return this.progress + '%';
        }
    },
    watch: {
        "$store.state.online_users": function () {
            var online_users = this.$store.state.online_users;
            online_users = online_users.filter((u) => u.is_client !== 1);
            online_users = online_users.filter((u) => u.status == 'online');
            this.activity[4].value = online_users.length;
        },
        ticketPeriodSelected: function(newVal, oldVal) {
            this.getTicketTimeCharts(newVal)
        },
        chatPeriodSelected: function(newVal, oldVal) {
            this.getChatTimeCharts(newVal)
        },
        selectedTC: function(newVal, oldVal) {
            this.getloadChart(newVal)
        },
    },
	methods: {
        percent(value, total){
            if(value == 0 && total == 0){
                return 0;
            }
            return (100*parseInt(value))/parseInt(total);
        },
        getProgressCards: async function () {
            let url = `/home/get-progress-cards-home-dashboard`;
            // if (!this.isFirstLoad) {
            //     this.$spinner.show();
            // }
            try {
                let response = await axios.post(url, {
                    company_id: this.$store.state.csid,
                });
                if (response.data.success) {
                    //chats
                    this.chartChat.today = response.data.chat.fechado_hoje;
                    this.chartChat.totalToday = response.data.chat.total_hoje;

                    this.chartChat.yesterday = response.data.chat.fechado_ontem;
                    this.chartChat.totalYesterday = response.data.chat.total_ontem;

                    //ticket
                    this.chartTicket.today = response.data.ticket.fechado_hoje;
                    this.chartTicket.totalToday = response.data.ticket.total_hoje;

                    this.chartTicket.yesterday = response.data.ticket.fechado_ontem;
                    this.chartTicket.totalYesterday = response.data.ticket.total_ontem;
                } else {
                    // vm.$snotify.error(vm.$t("bs-error-fetching-progress-card"), vm.$t("bs-error"));
                }
            } catch (e) {
                console.log("FAILURE!!");
            } finally {
                // if (!this.isFirstLoad) {
                // this.$spinner.hide();
                // }
            }
        },
        getloadChart: async function (selectedTC) {
            let url = `/home/get-bar-chart-home-dashboard`;
            // if (!this.isFirstLoad) {
            //     this.$spinner.show();
            // }
            try {
                let response = await axios.post(url, {
                company_id: this.$store.state.csid,
                period: selectedTC,
                });
                if (response.data.success) {
                    this.generateGraphic(selectedTC, response.data);
                } else {
                
                }
            } catch (e) {
                console.log(e);
            } finally {
                // this.chart1.loadedData = true;
                // if (!this.isFirstLoad) {
                // this.$spinner.hide();
                // }
            }
        },
        getMemberByte: async function(period) {  
            axios.get('/company/get-count-member', {
                params: {
                    company_id: this.$store.state.csid,
                }
            })
            .then(res => {
                this.memberbytes[0].value = res.data.result1[0].department;
                this.memberbytes[2].value = res.data.result2[0].groupss;
                this.memberbytes[4].value = res.data.result3[0].agents;
            })
        },
        convertSecondstoDate(value){
            if( value > 0) {
                let d = moment.duration(value, 'seconds')

                let seconds = Math.round(d.asSeconds()) > 0 ? `${d.seconds()}${this.$t('bs-second-abbreviation')}` : ''
                let minutes = Math.round(d.asMinutes()) > 0 ? `${d.minutes()}${this.$t('bs-minute-abbreviation')}` : ''                
                let hours = Math.round(d.asHours()) > 0 ? `${d.hours()}${this.$t('bs-hour-abbreviation')}` : ''

                let days = Math.round(d.asDays()) > 0 ? `${d.days()}${this.$t('bs-day-abbreviation')}` : ''
                let months = Math.round(d.asMonths()) > 0 ? `${d.months()}${this.$t('bs-month-abbreviation')}` : ''
                let years = Math.round(d.asYears()) > 0 ? `${d.years()}${this.$t('bs-year-abbreviation')}` : ''

                let p2 = `${years} ${months} ${days}`
                p2 = p2.trim() == '' ? '' : `${p2}`
                
                
                return `${p2}${hours} ${minutes} ${seconds}`
            } else {
                return this.$t('bs-no-data-period')
            }
        },
        getTicketTimeCharts: async function(period) {
            let url = `/company/get-ticket-time-cards-company-dashboard`
            // if(!this.isFirstLoad) {
            //     this.$spinner.show();
            // }
            try{
                let response = await axios.post(url, { 
                    company_id: this.$store.state.csid,
                    period: period
                })
                if(response.data.success){
                    this.ticketTimeAverageQueueTime = response.data[0].avg_queue_time == null ? 0 : response.data[0].avg_queue_time
                    this.ticketTimeAverageServiceTime = response.data[0].avg_service_time == null ? 0 : response.data[0].avg_service_time
                } else {
                    // vm.$snotify.error(vm.$t('bs-error-fetching-time-card'), vm.$t('bs-error'));
                }
            } catch(e) {
                console.log('FAILURE!!');
            } finally {
                // this.chart1.loadedData = true
                // if(!this.isFirstLoad) {
                //     this.$spinner.hide();
                // }
            }
        },
        getChatTimeCharts: async function(period) {
            let url = `/company/get-chat-time-cards-company-dashboard`
            // if(!this.isFirstLoad) {
            //     this.$spinner.show();
            // }
            try{
                let response = await axios.post(url, { 
                    company_id: this.$store.state.csid,
                    period: period
                })
                if(response.data.success){
                    this.chatTimeAverageQueueTime = response.data[0].avg_queue_time == null ? 0 : response.data[0].avg_queue_time
                    this.chatTimeAverageServiceTime = response.data[0].avg_service_time == null ? 0 : response.data[0].avg_service_time
                } else {
                    // vm.$snotify.error(vm.$t('bs-error-fetching-time-card'), vm.$t('bs-error'));
                }
            } catch(e) {
                console.log('FAILURE!!');
            } finally {
                // this.chart1.loadedData = true
                // if(!this.isFirstLoad) {
                //     this.$spinner.hide();
                // }
            }
        },
        getSummaryCards: async function () {
            let url = `/home/get-summary-cards-home-dashboard`;
            // if (!this.isFirstLoad) {
            //     this.$spinner.show();
            // }
            try {
                let response = await axios.post(url, {
                    company_id: this.$store.state.csid,
                });
                if (response.data.success) {
                //chats
                this.activity[0].value = response.data.chat.qtd_opened;
                this.activity[1].value = response.data.chat.qtd_in_progress;

                //ticket
                this.activity[2].value = response.data.ticket.qtd_opened;
                this.activity[3].value = response.data.ticket.qtd_in_progress;
                } else {
                    // vm.$snotify.error(vm.$t("bs-error-fetching-summary-card"), vm.$t("bs-error"));
                }
            } catch (e) {
                console.log("FAILURE!!");
            } finally {
                // if (!this.isFirstLoad) {
                //     this.$spinner.hide();
                // }
            }
        },
        generateGraphic(type, item) {
            const ctx = document.getElementById('myChart');

            // Destrói o gráfico anterior, se existir
            if (this.chartInstance) {
                this.chartInstance.destroy();
            }

            if (type === 'week') {
                this.chartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: this.$i18n.locale == "pt_BR" ? item.labels.map(item => `${item.dia}/${item.mes}`) : item.labels.map(item => `${value.mes}/${value.dia}`),
                        datasets: [{
                        label: 'Chats',
                        data: item.chats,
                        backgroundColor: ['#205ECD', '#205ECD'],
                        borderWidth: 1,
                        maxBarThickness: 50,
                        },
                        {
                        label: 'Tickets',
                        data: item.tickets,
                        backgroundColor: ['#36D7D5', '#36D7D5'],
                        borderWidth: 1,
                        maxBarThickness: 50,
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            } else if (type === 'month') {
                this.chartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: [this.$t("bs-first-week"), this.$t("bs-second-week"), this.$t("bs-third-week"), this.$t("bs-fourth-week")],
                        datasets: [{
                        label: 'Chats',
                        data: item.chats,
                        backgroundColor: ['#205ECD', '#205ECD'],
                        borderWidth: 1,
                        maxBarThickness: 50,
                        },
                        {
                        label: 'Tickets',
                        data: item.tickets,
                        backgroundColor: ['#36D7D5', '#36D7D5'],
                        borderWidth: 1,
                        maxBarThickness: 50,
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            } else if (type === 'year') {
                this.chartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: this.$i18n.locale == "pt_BR" ? item.labels.map(item => `${item.mes}/${item.ano}`) : item.labels.map(item => `${value.mes}/${value.ano}`),
                        datasets: [{
                        label: 'Chats',
                        data: item.chats,
                        backgroundColor: ['#205ECD', '#205ECD'],
                        borderWidth: 1,
                        maxBarThickness: 50,
                        },
                        {
                        label: 'Tickets',
                        data: item.tickets,
                        backgroundColor: ['#36D7D5', '#36D7D5'],
                        borderWidth: 1,
                        maxBarThickness: 50,
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }
    },  
};
</script>

<style scoped>
    .ba-statistics{
        min-width: 186px !important;
    }

    .ba-title{
        font-family: Montserrat !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        line-height: 15px !important;
        letter-spacing: 0.055em !important;
        text-align: left !important;
    }
    .progress-info{
        display: flex;
        justify-content: space-between;
        width: 100%; /* Ajuste conforme necessário */
    }
    .left-text, .right-text {
        font-size: 11px;
        font-weight: 500;
        line-height: 12px;
        letter-spacing: 0px;
        margin-top: 10px;
    }
    .center-card{
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin-left: 1rem;
    }
    .progress-bar{
        background-color: #D9D9D9;
        border-radius: 10px;
        overflow: hidden;
        width: 235px;
        height: 4px;
        margin-top: 20px;
    }

    .progress{
        width: 0%;
        height: 100%;
        background-color: #4caf50;
        transition: width 0.3s ease;
    }

        /* Example styles for the progress bar */


    .m-content{
        width: 298px;
        height: 201px;
        top: 955px;
        left: 886px;
        border: 1px;
        width: 298px;
        border-radius: 14px;
        padding-top: 20px;
    }
    .box1{
        background: #FFFFFF;
        border: 0.5px solid #C4D1E0;
        border-radius: 4px;
    }
    .box1-t{
        font-style: normal;
        font-weight: 600;
        line-height: 15px;
        color: #4D5D71;
        padding: 6px;
    }
    .box1-s{
        font-style: normal;
        font-weight: 500;
        line-height: 15px;
        padding: 6px;
    }
    .select-helpdesk{
        gap: 8px;
        height: 34px;
        border-radius: 6px;
    }
    .ba-title-c{
        font-family: Montserrat;
        font-size: 11px;
        font-weight: 600;
        line-height: 13px;
        letter-spacing: 0px;
        text-align: left;
        color: #8D9EB5;
    }
    
</style>