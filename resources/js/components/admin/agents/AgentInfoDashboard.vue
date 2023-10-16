<template>
    <b-container fluid>
        <!-- title -->
        <b-row class="px-2 mb-4" cols='1' cols-lg='2' align-h="between" align-v="center" no-gutters>
            <b-col class="py-2">
                <h3 class="bs-title" v-html="`${title}: ${agent.attendants}`"></h3>
                <span class="bs-subtitle" v-html="subtitle"></span>
            </b-col>
            <b-col class="py-2 px-lg-2 text-right">
                <b-button @click="goBack" variant="btn bs-btn-back btn-block-md">
                    {{btnGoBack}}
                </b-button>
            </b-col>
        </b-row>

        <!-- filters -->
        <b-row class="mb-5 px-2" align-h="between" align-v="center" no-gutters>
            <b-col class='py-2' cols='12' lg="5">
                <span class="bs-info">{{currentDate}}</span>
            </b-col>
            <b-col class='' cols='12' lg="7">
                <b-row cols='1' cols-lg='3' align-h="between" align-v="center" no-gutters>
                    <b-col class="py-2 px-lg-1">
                        <b-form-select v-model="selectedDepartment" :options="selectDepartmentOptions" class="dashboard_select"></b-form-select>
                    </b-col>
                    <b-col class="py-2 px-lg-1">
                        <b-form-datepicker
                            class="p-0"
                            v-model="dateStartFilterValue"
                            :locale="usuario ? usuario.language.replace('_','-') : 'en-US'"
                            v-bind="labels"
                            :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }"
                        ></b-form-datepicker>
                    </b-col>
                    <b-col class="py-2 px-lg-1">
                        <b-form-datepicker
                            class="p-0"
                            v-model="dateEndFilterValue"
                            :locale="usuario ? usuario.language.replace('_','-') : 'en-US'"
                            v-bind="labels"
                            :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }"
                        ></b-form-datepicker>
                    </b-col>
                </b-row>
            </b-col>
        </b-row>

        <!-- chat summary -->
        <b-row cols="1" class="px-2 mb-3">
            <b-col class="mb-3">
                <h3 class="bs-title" v-html="chatTitle"></h3>
            </b-col>
            <b-col class="px-2">
                <b-row cols='1' cols-md="2" cols-lg="4" no-gutters>
                    <b-col v-for="(card, key) in chatCards" :key="key" class="px-md-2 py-2">
                        <quantitative-card
                            v-if="card.type == 'QuantitativeCard'"
                            v-bind="card.props"
                        ></quantitative-card>
                        <qualitative-card
                            v-else-if="card.type == 'QualitativeCard'"
                            v-bind="card.props"
                        ></qualitative-card>
                        <time-card
                            v-else
                            v-bind="card.props"
                        ></time-card>
                    </b-col>
                </b-row>
            </b-col>
            <!-- <b-col class="text-right px-4">
                <a :href="chatLink.link" target="_self" class="local-link" v-html="chatLink.label" ></a>
            </b-col> -->
        </b-row>

        <!-- tickets summary -->
        <b-row cols="1" class="px-2 mb-3">
            <b-col class="mb-3">
                <h3 class="bs-title" v-html="ticketTitle"></h3>
            </b-col>
            <b-col class="px-2">
                <b-row cols='1' cols-md="2" cols-lg="4" no-gutters>
                    <b-col v-for="(card, key) in ticketCards" :key="key" class="px-md-2 py-2">
                        <quantitative-card
                            v-if="card.type == 'QuantitativeCard'"
                            v-bind="card.props"
                        ></quantitative-card>
                        <qualitative-card
                            v-else-if="card.type == 'QualitativeCard'"
                            v-bind="card.props"
                        ></qualitative-card>
                        <time-card
                            v-else
                            v-bind="card.props"
                        ></time-card>
                    </b-col>
                </b-row>
            </b-col>
            <!-- <b-col class="text-right px-4">
                <a :href="ticketLink.link" target="_self" class="local-link" v-html="ticketLink.label" ></a>
            </b-col> -->
        </b-row>
    </b-container>
</template>

<script>
export default {
    name: 'agent-info-dashboard',
    props: {
        base_url: {
            type: String,
            default: ''
        },
        go_back_url: {
            type: String,
            default: ''
        },
        agent: {
            type: Object,
            default: {
                attendants: 'Usuário Fictício da Silva'
            }
        },
        csid: String,
        usuario: Object
    },
    data: function () {
        return {
             // title section
            title: this.$t('bs-view-the-data-of'),
            subtitle: this.$t('bs-agent-info-dashboard'),
            btnGoBack: this.$t('bs-back'),
            currentDate: 'Sex, 16 de Out de 2020 as 10:16',

            // filter section
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
            dateStartFilterValue: null,
            dateEndFilterValue: null,

            // chat summary
            chatTitle: this.$t('bs-chat'),
            chatLink: {
                label: this.$t('bs-see-all'),
                link: '#'
            },
            chatCards: [
                {
                    type: 'QuantitativeCard',
                    props: {
                        title: this.$t('bs-in-progress'),
                        value: 25
                    }
                },
                {
                    type: 'QuantitativeCard',
                    props: {
                        title: this.$t('bs-finished'),
                        value: 30
                    }
                },
                // {
                //     type: 'QuantitativeCard',
                //     props: {
                //         title: this.$t('bs-resolved'),
                //         value: 25
                //     }
                // },
                {
                    type: 'QuantitativeCard',
                    props: {
                        title: this.$t('bs-moved-to-ticket'),
                        value: 4
                    }
                },
                {
                    type: 'TimeCard',
                    props: {
                        title: this.$t('bs-average-queue-time'),
                        durationInSeconds: 4000
                    }
                },
                {
                    type: 'TimeCard',
                    props: {
                        title: this.$t('bs-average-service-time'),
                        durationInSeconds: 5000
                    }
                },
                {
                    type: 'QualitativeCard',
                    props: {
                        title: this.$t('bs-service-satisfaction-level'),
                        count_1: 0,
                        count_2: 0,
                        count_3: 0,
                        count_4: 0,
                        count_5: 0,
                        count_geral: 0,
                        media_stars: 0,
                    }
                },
                {
                    type: 'QualitativeCard',
                    props: {
                        title: this.$t('bs-level-of-satisfaction-with-the-attendant'),
                        count_1: 0,
                        count_2: 0,
                        count_3: 0,
                        count_4: 0,
                        count_5: 0,
                        count_geral: 0,
                        media_stars: 0,
                    }
                },
            ],
            // ticket summary
            ticketTitle: this.$t('bs-ticket'),
            ticketLink: {
                label: this.$t('bs-see-all'),
                link: '#'
            },
            ticketCards: [
                {
                    type: 'QuantitativeCard',
                    props: {
                        title: this.$t('bs-in-progress'),
                        value: 25
                    }
                },
                {
                    type: 'QuantitativeCard',
                    props: {
                        title: this.$t('bs-closed'),
                        value: 30
                    }
                },
                {
                    type: 'QuantitativeCard',
                    props: {
                        title: this.$t('bs-resolved'),
                        value: 25
                    }
                },
                {
                    type: 'TimeCard',
                    props: {
                        title: this.$t('bs-average-queue-time'),
                        durationInSeconds: 4000
                    }
                },
                {
                    type: 'TimeCard',
                    props: {
                        title: this.$t('bs-average-service-time'),
                        durationInSeconds: 5000
                    }
                },
                {
                    type: 'QualitativeCard',
                    props: {
                        title: this.$t('bs-service-satisfaction-level'),
                        count_1: 0,
                        count_2: 0,
                        count_3: 0,
                        count_4: 0,
                        count_5: 0,
                        count_geral: 0,
                        media_stars: 1.0,
                    }
                },
                {
                    type: 'QualitativeCard',
                    props: {
                        title: this.$t('bs-level-of-satisfaction-with-the-attendant'),
                        count_1: 0,
                        count_2: 0,
                        count_3: 0,
                        count_4: 0,
                        count_5: 0,
                        count_geral: 0,
                        media_stars: 1.0,
                    }
                },
            ],

            labels: {
                labelCalendar: this.$t('bs-calendar'),
                labelCloseButton: this.$t('bs-close'),
                labelCurrentMonth: this.$t('bs-current-month'),
                labelHelp: this.$t('bs-use-cursor-keys-to-navigate-calendar-dates'),
                labelNav: this.$t('bs-calendar-navigation'),
                labelNextDecade: this.$t('bs-next-decade'),
                labelNextMonth: this.$t('bs-next-month'),
                labelNextYear: this.$t('bs-next-year'),
                labelNoDateSelected: this.$t('bs-no-date-selected'),
                labelPrevDecade: this.$t('bs-previous-decade'),
                labelPrevMonth: this.$t('bs-previous-month'),
                labelPrevYear: this.$t('bs-previous-year'),
                labelResetButton: this.$t('bs-reset'),
                labelSelected: this.$t('bs-selected'),
                labelToday: this.$t('bs-today'),
                labelTodayButton: this.$t('bs-select-today')
            },

            isFirstLoad: true

        }
    },
    methods: {
        goBack: function() {
            window.open(this.go_back_url, '_self')
        },
        setCurrentDate(vm){
            let date = new Date();
            vm.currentDate = new Intl.DateTimeFormat(
                vm.usuario ? vm.usuario.language.replace('_','-') : 'en-US',
                { dateStyle: 'full', timeStyle: 'short' }
            ).format(date)
        },
        getDepartments: async function(){
            let url = `${this.base_url}/agents/get-departments-agent-info-dashboard`
            if(!this.isFirstLoad){
                this.$spinner.show()
            }
            try{
                let response = await axios.post(url, {
                    company_id: this.csid,
                    attendant_id: this.agent.id
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
                    this.$snotify.error(vm.$t('bs-error-fetching-departments'), this.$t('bs-error'));
                }
            } catch(e) {
                console.log('FAILURE!!');
            } finally {
                if(!this.isFirstLoad){
                    this.$spinner.hide()
                }
            }
        },
        getQuantitativeAndTimeCards: async function(department_id, attendant_id, initial_date, final_date) {
            let url = `${this.base_url}/agents/get-quantitative-and-time-cards-agent-info-dashboard`
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
                    this.chatCards[0].props.value = response.data[0].in_progress
                    this.chatCards[1].props.value = response.data[0].closed + response.data[0].resolved
                    this.chatCards[2].props.value = response.data[0].changed_to_ticket
                    this.chatCards[3].props.durationInSeconds = response.data[0].avg_queue_time
                    this.chatCards[4].props.durationInSeconds = response.data[0].avg_service_time


                    // tickets
                    this.ticketCards[0].props.value = response.data[1].in_progress
                    this.ticketCards[1].props.value = response.data[1].closed
                    this.ticketCards[2].props.value = response.data[1].resolved
                    this.ticketCards[3].props.durationInSeconds = response.data[1].avg_queue_time
                    this.ticketCards[4].props.durationInSeconds = response.data[1].avg_service_time


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
            let url = `${this.base_url}/agents/get-qualitative-cards-agent-info-dashboard`
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
                    
                // console.log(response.data);

                if(response.data.success){
                    // chats    
                    this.chatCards[5].props.count_1 = response.data[0].count_1_atendent;
                    this.chatCards[5].props.count_2 = response.data[0].count_2_atendent;
                    this.chatCards[5].props.count_3 = response.data[0].count_3_atendent;
                    this.chatCards[5].props.count_4 = response.data[0].count_4_atendent;
                    this.chatCards[5].props.count_5 = response.data[0].count_5_atendent;

                    this.chatCards[5].props.count_geral = response.data[0].count_geral
                    this.chatCards[5].props.media_stars = parseInt(response.data[0].media_stars_atendent)

                    this.chatCards[6].props.count_1 = response.data[0].count_1_service;
                    this.chatCards[6].props.count_2 = response.data[0].count_2_service;
                    this.chatCards[6].props.count_3 = response.data[0].count_3_service;
                    this.chatCards[6].props.count_4 = response.data[0].count_4_service;
                    this.chatCards[6].props.count_5 = response.data[0].count_5_service;

                    this.chatCards[6].props.count_geral = response.data[0].count_geral;
                    this.chatCards[6].props.media_stars = parseInt(response.data[0].media_stars_service);
                  

                    //tickets
                    this.ticketCards[5].props.count_1 = response.data[1].count_1_atendent;
                    this.ticketCards[5].props.count_2 = response.data[1].count_2_atendent;
                    this.ticketCards[5].props.count_3 = response.data[1].count_3_atendent;
                    this.ticketCards[5].props.count_4 = response.data[1].count_4_atendent;
                    this.ticketCards[5].props.count_5 = response.data[1].count_5_atendent;

                    this.ticketCards[5].props.count_geral = response.data[1].count_geral
                    this.ticketCards[5].props.media_stars = parseInt(response.data[1].media_stars_atendent);

                    this.ticketCards[6].props.count_1 = response.data[1].count_1_service;
                    this.ticketCards[6].props.count_2 = response.data[1].count_2_service;
                    this.ticketCards[6].props.count_3 = response.data[1].count_3_service;
                    this.ticketCards[6].props.count_4 = response.data[1].count_4_service;
                    this.ticketCards[6].props.count_5 = response.data[1].count_5_service;

                    this.ticketCards[6].props.count_geral = response.data[1].count_geral;
                    this.ticketCards[6].props.media_stars = parseInt(response.data[1].media_stars_service);
                } else {
                    this.$snotify.error(this.$t('bs-error-fetching-departments'), this.$t('bs-error'));
                }
              
            } catch(e) {
                console.log(e);
                console.log('FAILURE!!');
            } finally {
                if(!this.isFirstLoad){
                    this.$spinner.hide()
                }
            }
        },
        refreshPageData: async function (department_id, attendant_id, initial_date, final_date) {
            // asdf
            await this.getQuantitativeAndTimeCards(department_id, attendant_id, initial_date, final_date)
            await this.getQualitativeCards(department_id, attendant_id, initial_date, final_date)
        }
    },
    watch: {
        selectedDepartment: async function(newVal, oldVal) {
            await this.refreshPageData(newVal, this.agent.id, this.dateStartFilterValue, this.dateEndFilterValue)
            if(this.isFirstLoad){
                this.isFirstLoad = false
            }
        },
        dateStartFilterValue: function(newVal, oldVal) {
            console.log(this.dateStartFilterValue);
            if(this.dateStartFilterValue == null || this.dateEndFilterValue == null){

            }else{
                this.refreshPageData(this.selectedDepartment, this.agent.id, newVal, this.dateEndFilterValue)
            }
        },
        dateEndFilterValue: function(newVal, oldVal) {
            this.refreshPageData(this.selectedDepartment, this.agent.id, this.dateStartFilterValue, newVal)
        },
    },
    created() {
        this.setCurrentDate(this);
        setInterval(this.setCurrentDate, 60000, this)
        this.getDepartments()
    }
}
</script>

<style lang="scss" scoped>
    .bs-info{
        font: normal normal bold 16px/24px Muli;
        color: #434343;
    }
    .local-link{
        text-decoration: underline;
        font: normal normal normal 10px/12px Muli;
        letter-spacing: 0px;
        color: #0F7BFF;
        opacity: 0.5;
        &:hover{
            cursor: pointer;
        }
    }
</style>
