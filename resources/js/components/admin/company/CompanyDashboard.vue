<template>
	<div>
		<b-container fluid>
            <!-- title -->
			<b-row  cols="auto" align-h="between" align-v="center" no-gutters>
				<b-col class="py-2">
                    <h3 class="bs-title" v-html="title"></h3>
					<span class="bs-subtitle" v-html="`${subtitle}: ${csname}`"></span>
				</b-col>
                <b-col cols="auto" class="mr-3">
					<b-button @click="editCompany" variant="btn bs-btn-cancel btn-block-md">{{btnEditCompany}}</b-button>
				</b-col>
                <b-col cols="auto">
					<b-button @click="viewIntegration" variant="primary"><i class="fa fa-cog fa-1x" style="font-size:18px;" aria-hidden="true"></i>&nbsp&nbsp {{btnIntegration}}</b-button>
				</b-col>
			</b-row>
			<!-- line one -->
            <b-row no-gutters cols='1' cols-sm="2" cols-lg="4" align-h="start" align-v="stretch" class="mb-5">
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

            <b-row v-if="location != 'hs.builderall.com'" class="mb-5 px-2" align-v="stretch">
                <b-col class="py-3">
                    <!-- card charts -->
                    <b-card style="height: 100%!important;">
                        <b-card-header class="px-0 bg-transparent">
                            <b-container fluid class="px-0">
                                <b-row cols-sm="1" align-h="between" align-v="stretch" no-gutters>
                                    <b-col md='2'>
                                        <h4 v-text="chart2.title"></h4>
                                    </b-col>
                                    <b-col md='2' class="pt-2 py-md-0">
                                        <!-- <b-form-select @change="getInfoDashboard" v-model="dpSelected" :options="selectDepartmentOptions" class="dashboard_select"></b-form-select> -->
                                        <multiselect
                                            v-model="dpSelected"
                                            deselect-label=""
                                            selectLabel=""
                                            track-by="name"
                                            label="name"
                                            :multiple="true"
                                            :placeholder="'phSelectDepart'"
                                            :options="selectDepartmentOptions" 
                                            @input="updateOptions"
                                            :searchable="false"
                                            :allow-empty="false"
                                            id="departments-1">
                                        </multiselect>
                                        <b-form-checkbox
                                        id="checkbox-1"
                                        name="checkbox-1"
                                        @change="selectVip"
                                        v-if="hostname == 'ba-support.builderall.com' || hostname == 'localhost'"
                                        >
                                        {{ $t('bs-vip') }}
                                        </b-form-checkbox>
                                    </b-col>
                                    <b-col md='3' class="pl-md-2 pt-2 py-md-0">
                                        <b-form-datepicker id="datepicker" @input="getInfoDashboard" v-model="date_initial" class="mb-2 b-calendar"></b-form-datepicker>
                                    </b-col>
                                    <b-col md='3' class="pl-md-2 pt-2 py-md-0">
                                        <b-form-datepicker id="datepicker2" @input="getInfoDashboard" v-model="date_final" class="mb-2 b-calendar"></b-form-datepicker>
                                    </b-col>
                                    <b-col md='2' class="pl-md-2 pt-2 py-md-0">
                                        <label for="text">{{$t('bs-daily-weekly')}}</label>
                                        <label class="switch">
											<input type="checkbox" @change="getInfoDashboard" v-model="changeWeeklyMonthly">
											<span class="slider round"></span>
										</label>
                                    </b-col>
                                </b-row>
                            </b-container>
                        </b-card-header>
                        <b-card-text class="px-0 py-2">
                            <bar-chart
                                v-if="chart2.loadedData"
                                :chartData="{
                                    labels: chart2.xLabels,
                                    datasets: [
                                        {
                                            label: chart2.barData[0].label,
                                            backgroundColor: '#2ECF44',
                                            data: chart2.barData[0].data,
                                            barPercentage: 0.666666667,
                                            categoryPercentage: 0.32,
                                        },
                                        {
                                            label: chart2.barData[1].label,
                                            backgroundColor: '#00dbdb',
                                            data: chart2.barData[1].data,
                                            barPercentage: 0.666666667,
                                            categoryPercentage: 0.32,
                                        },
                                        {
                                            label: chart2.barData[2].label,
                                            backgroundColor: '#3F42DC',
                                            data: chart2.barData[2].data,
                                            barPercentage: 0.666666667,
                                            categoryPercentage: 0.32,
                                        },
                                        {
                                            label: chart2.barData[3].label,
                                            backgroundColor: '#D29C2B',
                                            data: chart2.barData[3].data,
                                            barPercentage: 0.666666667,
                                            categoryPercentage: 0.32,
                                        }
                                    ]
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
                                                suggestedMax: 20,
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

            <b-row v-if="location != 'hs.builderall.com'" class="mb-5 px-2" align-v="stretch">
                <b-col class="py-3">
                    <!-- card charts -->
                    <b-card style="height: 100%!important;">
                        <b-card-header class="px-0 bg-transparent">
                            <b-container fluid class="px-0">
                                <b-row cols-sm="1" align-h="between" align-v="stretch" no-gutters>
                                    <b-col md='3'>
                                        <h4 v-text="chart3.title"></h4>
                                    </b-col>
                                    <b-col md='3' class="pt-2 py-md-0">
                                        <!-- <b-form-select @change="getInfoTopThree" v-model="dpSelected3" :options="selectDepartmentOptions" class="dashboard_select"></b-form-select> -->
                                        <multiselect
                                            v-model="dpSelected3"
                                            deselect-label=""
                                            selectLabel=""
                                            track-by="name"
                                            label="name"
                                            :multiple="true"
                                            :placeholder="'phSelectDepart'"
                                            :options="selectDepartmentOptions" 
                                            @input="updateOptions3"
                                            :searchable="false"
                                            :allow-empty="false"
                                            id="departments-3">
                                        </multiselect>
                                        <b-form-checkbox
                                        id="checkbox-3"
                                        name="checkbox-3"
                                        @change="selectVip3"
                                        v-if="hostname == 'ba-support.builderall.com' || hostname == 'localhost'"
                                        >
                                        {{ $t('bs-vip') }}
                                        </b-form-checkbox>
                                    </b-col>
                                    <b-col md='3' class="pl-md-2 pt-2 py-md-0">
                                        <b-form-datepicker id="datepicker5" @input="getInfoTopThree" v-model="date_initial3" :date-disabled-fn="dateDisabled" class="mb-2 b-calendar"></b-form-datepicker>
                                    </b-col>
                                    <b-col md='3' class="pl-md-2 pt-2 py-md-0">
                                        <b-form-datepicker id="datepicker6" @input="getInfoTopThree" v-model="date_final3" :date-disabled-fn="dateDisabled" class="mb-2 b-calendar"></b-form-datepicker>
                                    </b-col>
                                </b-row>
                            </b-container>
                        </b-card-header>
                        <b-card-text class="px-0 py-2">
                            <bar-chart
                                v-if="chart3.loadedData"
                                :chartData="{
                                    labels: chart3.xLabels,
                                    datasets: datasets
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
                                                suggestedMax: 10,
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

            <b-row v-if="location != 'hs.builderall.com'" class="mb-5 px-2" align-v="stretch">
                <b-col class="py-3">
                    <!-- card charts -->
                    <b-card style="height: 100%!important;">
                        <b-card-header class="px-0 bg-transparent">
                            <b-container fluid class="px-0">
                                <b-row cols-sm="1" align-h="between" align-v="stretch" no-gutters>
                                    <b-col md='2'>
                                        <h4 v-text="$t('bs-bugs')+'/'+$t('bs-time')"></h4>
                                    </b-col>
                                    <b-col md='2' class="pt-2 py-md-0">
                                        <!-- <b-form-select @change="getInfoBugs" v-model="dpSelected2" :options="selectDepartmentOptions" class="dashboard_select"></b-form-select> -->
                                        <multiselect
                                            v-model="dpSelected2"
                                            deselect-label=""
                                            selectLabel=""
                                            track-by="name"
                                            label="name"
                                            :multiple="true"
                                            :placeholder="'phSelectDepart'"
                                            :options="selectDepartmentOptions" 
                                            @input="updateOptions2"
                                            :searchable="false"
                                            :allow-empty="false"
                                            id="departments-2">
                                        </multiselect>
                                        <b-form-checkbox
                                        id="checkbox-2"
                                        name="checkbox-2"
                                        @change="selectVip2"
                                        v-if="hostname == 'ba-support.builderall.com' || hostname == 'localhost'"
                                        >
                                        {{ $t('bs-vip') }}
                                        </b-form-checkbox>
                                    </b-col>
                                    <b-col md='3' class="pl-md-2 pt-2 py-md-0">
                                        <b-form-datepicker id="datepicker3" @input="getInfoBugs" v-model="date_initial2" class="mb-2 b-calendar"></b-form-datepicker>
                                    </b-col>
                                    <b-col md='3' class="pl-md-2 pt-2 py-md-0">
                                        <b-form-datepicker id="datepicker4" @input="getInfoBugs" v-model="date_final2" class="mb-2 b-calendar"></b-form-datepicker>
                                    </b-col>
                                    <b-col md='2' class="pl-md-2 pt-2 py-md-0">
                                        <b-form-input v-on:keyup.enter="getInfoBugs" v-model="valueHours" placeholder="Hours"></b-form-input>
                                    </b-col>
                                </b-row>
                            </b-container>
                        </b-card-header>
                        <b-card-text class="px-0 py-2">
                            <b-row v-if="showInfoBug" style="justify-content: center;">
                                <b-col>
                                    <b-card :sub-title="$t('bs-total-bugs')">
                                        <b-card-text class="text-card-cust">
                                            {{total_bugs}}
                                        </b-card-text>
                                    </b-card>
                                </b-col>
                                <b-col>
                                    <b-card :sub-title="$t('bs-fixed-bugs')">
                                        <b-card-text class="text-card-cust">
                                            {{total_not_resolved}}
                                        </b-card-text>
                                    </b-card>
                                </b-col>
                            </b-row>
                            <b-row v-if="showInfoBug" style="justify-content: center;">
                                <b-col>
                                    <b-card :sub-title="$t('bs-chats')+': '+$t('bs-queue-time')">
                                        <b-card-text class="text-card-cust">
                                            {{updateClock(chats.avg_queue_time)}}
                                        </b-card-text>
                                    </b-card>
                                </b-col>
                                <b-col>
                                    <b-card :sub-title="$t('bs-chats')+': '+$t('bs-service-time')">
                                        <b-card-text class="text-card-cust">
                                            {{updateClock(chats.avg_service_time)}}
                                        </b-card-text>
                                    </b-card>
                                </b-col>
                            </b-row>
                            <b-row v-if="showInfoBug" style="justify-content: center;">
                                <b-col>
                                    <b-card :sub-title="$t('bs-tickets')+': '+$t('bs-queue-time')">
                                        <b-card-text class="text-card-cust">
                                            {{updateClock(tickets.avg_queue_time)}}
                                        </b-card-text>
                                    </b-card>
                                </b-col>
                                <b-col>
                                    <b-card :sub-title="$t('bs-tickets')+': '+$t('bs-service-time')">
                                        <b-card-text class="text-card-cust">
                                            {{updateClock(tickets.avg_service_time)}}
                                        </b-card-text>
                                    </b-card>
                                </b-col>
                            </b-row>

                            <!-- <b-row v-if="showInfoBug" class="mt-2">
                                <b-col>
                                    <label for="text" class="teste">Quantidade em atraso. (Horas)</label>
                                    <b-form-input v-model="valueHours" placeholder="Hours"></b-form-input>
                                </b-col>
                            </b-row> -->
                            <b-row v-if="showInfoBug" style="justify-content: center;">
                                <b-col>
                                    <b-card :sub-title="valueHours == 1 ? 
                                    $t('bs-tickets')+': '+valueHours+' '+$t('bs-hour-or-more')+' '+$t('bs-the-queue') : 
                                    $t('bs-tickets')+': '+valueHours+' '+$t('bs-hours-or-more')+' '+$t('bs-the-queue')">
                                        <!-- <span class="teste">{{$t('bs-quantity-of')+' '+$t('bs-tickets')+': '+$t('bs-queue-time')}}
                                            <b-form-input style="width: 20% !important;display: inherit;" placeholder="Horas"></b-form-input>
                                        </span> -->
                                        <b-card-text class="text-card-cust">
                                            {{tickets.quant_avg_queue_time}}
                                        </b-card-text>
                                    </b-card>
                                </b-col>
                                <b-col>
                                    <b-card :sub-title="valueHours == 1 ? 
                                    $t('bs-tickets')+': '+valueHours+' '+$t('bs-hour-or-more')+' '+$t('bs-in-progress') : 
                                    $t('bs-tickets')+': '+valueHours+' '+$t('bs-hours-or-more')+' '+$t('bs-in-progress')">
                                        <b-card-text class="text-card-cust">
                                            {{tickets.quant_avg_service_time}}
                                        </b-card-text>
                                    </b-card>
                                </b-col>
                            </b-row>
                            <div v-else class="text-center">
                                Loading...
                            </div>
                        </b-card-text>
                    </b-card>
                </b-col >
			</b-row>   

			<!-- <b-row class="mb-5 px-2" align-v="stretch">
                <b-col class="py-3">
                    <b-card style="height: 100%!important;">
                        <b-card-header class="px-0 bg-transparent">
                            <b-container fluid class="px-0">
                                <b-row cols-sm="1" align-h="between" align-v="stretch" no-gutters>
                                    <b-col md='6'>
                                        <h4 v-text="chart1.title"></h4>
                                    </b-col>
                                    <b-col md='3' class="pt-2 py-md-0">
                                        <b-form-select v-model="departmentSelected" :options="selectDepartmentOptions" class="dashboard_select"></b-form-select>
                                    </b-col>
                                    <b-col md='3' class="pl-md-2 pt-2 py-md-0">
                                        <b-form-select v-model="selected" :options="selectOptions" class="dashboard_select"></b-form-select>
                                    </b-col>
                                </b-row>
                            </b-container>
                        </b-card-header>
                        <b-card-text class="px-0 py-2">
                            <bar-chart
                                v-if="chart1.loadedData"
                                :chartData="{
                                    labels: chart1.xLabels,
                                    datasets: [
                                        {
                                            label: chart1.barData[0].label,
                                            backgroundColor: '#1665d8',
                                            data: chart1.barData[0].data,
                                            barPercentage: 0.666666667,
                                            categoryPercentage: 0.32,
                                        },
                                        {
                                            label: chart1.barData[1].label,
                                            backgroundColor: '#00dbdb',
                                            data: chart1.barData[1].data,
                                            barPercentage: 0.666666667,
                                            categoryPercentage: 0.32,
                                        }
                                    ]
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
			</b-row> -->

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
	</div>
</template>

<script>
import TimeCard from '../../tools/dashboardTools/TimeCard.vue';

export default {
	name: 'company-dashboard',
	props:{
		usuario: Object,
        csid: String,
        csname: String,
        base_url: {
            type: String,
            default: ''
        },
        createHref: {
            type: String,
            default: 'register-new-company'
        },
        editHref: {
            type: String,
            default: '#'
        },
        integrationHref: {
            type: String,
            default: 'company-integration'
        },
        is_admin: String,
	},
	data: function() {
		return {
			title: this.$t('bs-dashboard-company'),
			subtitle: this.$t('bs-company'),
			btnNewCompany: this.$t('bs-register-new-company'),
			btnEditCompany: this.$t('bs-edit-company'),
			btnIntegration: this.$t('bs-integration'),
			stats: [
				{
                    icon: 'chat_bubble',
					background: 'red-gradiente-bg',
					title: this.$t('bs-departments'),
					value: '0',
				},
				{
                    icon: 'group',
					background: 'yellow-gradiente-bg',
					title: this.$t('bs-groups'),
					value: '0',
				},
				{
                    icon: 'perm_phone_msg',
					background: 'blue-gradiente-bg',
					title: this.$t('bs-agents'),
					value: '0',
				},
				{
                    icon: 'person',
					background: 'green-gradiente-bg',
					title: this.$t('bs-customers'),
					value: '0',
					
				},
			],
			selected: 'week',
            selectOptions: [
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

            departmentSelected: 'dep1',
            selectDepartmentOptions: [
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
                {
                    text: 'Departamento 5',
                    value: 'dep5',
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
                title: this.$t('bs-occurrences-by-department'),
                loadedData: false,
                xLabels: [],
                barData: [
                    {
                        label: this.$t('bs-chats'),
                        data: [30, 35, 29, 37, 40, 45, 50],
                    },
                    {
                        label: this.$t('bs-tickets'),
                        data: [16, 20, 18, 21, 25, 26, 27],
                    },
                ]
			},

            // Bar chart
            chart2:{
                title: this.$t('bs-chats')+'/'+this.$t('bs-tickets'),
                loadedData: false,
                xLabels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                barData: [
                    {
                        label: this.$t('bs-open-chats'),
                        data: [],
                    },
                    {
                        label: this.$t('bs-closed-resolved-chats'),
                        data: [],
                    },
                    {
                        label: this.$t('bs-open-tickets'),
                        data: [],
                    },
                    {
                        label: this.$t('bs-closed-resolved-tickets'),
                        data: [],
                    },
                ]
			},

            // Bar chart
            chart3:{
                title: this.$t('bs-top-3-tools-reports'),
                loadedData: false,
                xLabels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                barData: [
                    {
                        label: '',
                        data: [],
                    }
                ]
			},

            // Bar chart
            chart4:{
                title: this.$t('bs-top-3-tools-reports'),
                loadedData: false,
                xLabels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                barData: [
                    {
                        label: '',
                        data: [],
                    },
                    {
                        label: '',
                        data: [],
                    },
                    {
                        label: '',
                        data: [],
                    }
                ]
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
            
            isFirstLoad: true,
            dpSelected: '',
            date_initial: '',
            date_final: '',
            total_bugs: 0,
            total_not_resolved: 0,
            dpSelected2: '',
            date_initial2: '',
            date_final2: '',
            dpSelected3: 'all',
            date_initial3: '', // 2021-01-01
            date_final3: '', // 2023-01-31
            chats:{
                avg_queue_time: 0,
                avg_service_time: 0,
            },
            tickets:{
                avg_queue_time: 0,
                avg_service_time: 0,
                quant_avg_queue_time: 0,
                quant_avg_service_time: 0,
            },
            showInfoBug: false,
            is_hs: false,
            location: window.location.hostname,
            valueHours: 48,
            datasets: [],
            hostname: '',
            changeWeeklyMonthly: false,
		}
	},
    created(){
        this.hostname = window.location.hostname;
    },
	methods: {
        getInfoTopThree(){
            this.chart3.loadedData = false;
            this.datasets = [];
            this.chart3.barData = [];
            if(this.date_initial3 == '' || this.date_final3 == '' || this.dpSelected3 == ''){
                console.log('CAMPOS VAZIOS');
            }else{
                var date1 = new Date(this.date_initial3);
                var date2 = new Date(this.date_final3 );
                var timeDiff = Math.abs(date2.getTime() - date1.getTime());
                var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
                if(diffDays >= 26 && diffDays <= 31){
                    axios.get(`${this.base_url}/company/get-info-topthree`, {
                        params: {
                            department_id: this.dpSelected3,
                            all_depart: this.selectDepartmentOptions,
                            date_initial: this.date_initial3,
                            date_final: this.date_final3,
                        }
                    }).then(res => {
                        // console.log(res.data.result5);

                        // backgroundColor
                        var count = 0
                        var colors =[
                            '#2ECF44', '#00dbdb', '#3F42DC', '#8A0808','#DBA901','#084B8A',
                            '#4B088A','#8A0886','#8A2908','#74DF00','#848484','#B404AE'
                        ];
                        res.data.result5.forEach(item => {
                            if(item.categoria_pai == ''){
                            }else{
                                if(this.datasets.find((el) => el.label == item.categoria_pai)){
                                    
                                }else{
                                    this.chart3.barData.push(
                                        {
                                            label: '',
                                            data: [],
                                        }
                                    );
                                    this.datasets.push(
                                        {
                                            label: item.categoria_pai,
                                            backgroundColor: colors[count],
                                            data: this.chart3.barData[count].data,
                                            barPercentage: 0.666666667,
                                            categoryPercentage: 0.32,
                                        }
                                    );
                                    count++;
                                }
                                    
                            }
                        });
                        var count = 0
                        res.data.result5.forEach(item => {
                            // console.log(item);
                            this.datasets.forEach(element => {
                                
                                if(item.categoria_pai == element.label){
                                    element.data[item.week.substring(5) - 1] = item.count;
                                }
                            });
                            
                            
                            count++;
                            if(count == 3){
                                count = 0;
                            }
                        });

                        setTimeout(() => {
                            this.chart3.loadedData = true;
                        }, 200);
                    });
                }else{
                    this.date_initial3 = '';
                    this.date_final3 = '';
                    this.chart3.loadedData = false;
                }  
            }
        },
        getInfoBugs(){
            this.showInfoBug = false;
            if(this.date_initial2 == '' || this.date_final2 == '' || this.dpSelected2 == ''){
                //CAMPOS VAZIOS
            }else{
                axios.get(`${this.base_url}/company/get-info-bugs-dashboard`, {
                    params: {
                        department_id: this.dpSelected2,
                        all_depart: this.selectDepartmentOptions,
                        date_initial: this.date_initial2,
                        date_final: this.date_final2,
                        valueHours: this.valueHours == '' ? 48 : this.valueHours,
                    }
                })
                .then(res => {
                    // console.log(res.data.result3);
                    // console.log(res.data.result4);
                    // console.log(res.data.result6);

                    this.total_bugs = res.data.result3[0].Count;
                    this.total_not_resolved = res.data.result3[0].closed_resolved == null ? 0 : res.data.result3[0].closed_resolved;
                    this.chats.avg_queue_time = res.data.result4[0].avg_queue_time == null ? 0 : res.data.result4[0].avg_queue_time;
                    this.chats.avg_service_time = res.data.result4[0].avg_service_time == null ? 0 : res.data.result4[0].avg_service_time;
                    this.tickets.avg_queue_time = res.data.result4[1].avg_queue_time == null ? 0 : res.data.result4[1].avg_queue_time;
                    this.tickets.avg_service_time = res.data.result4[1].avg_service_time == null ? 0 : res.data.result4[1].avg_service_time;
                    this.tickets.quant_avg_queue_time = res.data.result6[0].avg_queue_time == null ? 0 : res.data.result6[0].avg_queue_time;
                    this.tickets.quant_avg_service_time = res.data.result6[0].avg_service_time == null ? 0 : res.data.result6[0].avg_service_time;
                    this.showInfoBug = true;
                })
            }
        },
        getInfoDashboard(){
            this.chart2.loadedData = false;
            this.chart2.barData = [
                {
                    label: this.$t('bs-open-chats'),
                    data: [],
                },
                {
                    label: this.$t('bs-closed-resolved-chats'),
                    data: [],
                },
                {
                    label: this.$t('bs-open-tickets'),
                    data: [],
                },
                {
                    label: this.$t('bs-closed-resolved-tickets'),
                    data: [],
                },
            ];
            if(this.date_initial == '' || this.date_final == '' || this.dpSelected == ''){
                //CAMPOS VAZIOS
            }else{
                // var date1 = new Date(this.date_initial);
                // var date2 = new Date(this.date_final);
                // var timeDiff = Math.abs(date2.getTime() - date1.getTime());
                // var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 

                // if(diffDays >= 26 && diffDays <= 31){
                    axios.get(`${this.base_url}/company/get-info-dashboard`, {
                        params: {
                            department_id: this.dpSelected,
                            all_depart: this.selectDepartmentOptions,
                            date_initial: this.date_initial,
                            date_final: this.date_final,
                            changeWeeklyMonthly: this.changeWeeklyMonthly,
                        }
                    })
                    .then(res => {
                        
                        if(this.changeWeeklyMonthly){
                            var count = 0;
                            var count2 = 0;
                            this.chart2.xLabels = [];
                            res.data.result3.forEach(item => {
                                if (!this.chart2.xLabels.includes(item._date)) {
                                    this.chart2.xLabels.push(item._date);
                                }
                            });

                            this.chart2.xLabels.forEach((data, index) => {
                                res.data.result3.forEach((item, index2) => {
                                    console.log(data);
                                    console.log(item._date);
                                    if(data == item._date){
                                        if(item.type == "Chats_Opened"){
                                            this.chart2.barData[0].data[index] = item.count;
                                        }
                                        if(item.type == 'Tickets_Opened'){
                                            this.chart2.barData[2].data[index] = item.count;
                                        }
                                        if(item.type == "Chats_Closed"){
                                            this.chart2.barData[1].data[index] = item.count;
                                        }
                                        if(item.type == "Tickets_Closed"){
                                            this.chart2.barData[3].data[index] = item.count;
                                        }
                                    }
                                });
                            });

                            setTimeout(() => {
                                this.chart2.loadedData = true;
                            }, 150);

                        }else{
                            this.chart2.xLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
                            res.data.result.forEach(item => {
                                if(item.type == "Chats_Opened"){
                                    this.chart2.barData[0].data[item.week.substring(5) - 1] = item.count;
                                }
                
                                if(item.type == 'Tickets_Opened'){
                                    this.chart2.barData[2].data[item.week.substring(5) - 1] = item.count;
                                }
                            });

                            res.data.result2.forEach(item => {
                                if(item.type == "Chats_Closed"){
                                    this.chart2.barData[1].data[item.week.substring(5) - 1] = item.count;
                                }
                
                                if(item.type == "Tickets_Closed"){
                                    this.chart2.barData[3].data[item.week.substring(5) - 1] = item.count;
                                }
                            });
                            
                            setTimeout(() => {
                                this.chart2.loadedData = true;
                            }, 150);
                        }
                    })
                // }else{
                //     this.date_initial = '';
                //     this.date_final = '';
                //     this.chart2.loadedData = false;
                // }  
            }
        },
        updateClock(diff) {
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
            return h + ":" + m + ":" + s;
        },
		createCompany: function() {
            window.open(this.createHref, '_self')
		},
		editCompany: function () {
            window.open(this.editHref, '_self')
        },
		viewIntegration: function () {
            window.open(this.integrationHref, '_self')
        },
        getSummaryCards: async function(){
            let url = `${this.base_url}/company/get-summary-cards-company-dashboard`
            if(!this.isFirstLoad) {
                this.$spinner.show();
            }
            try{
                let response = await axios.post(url, { 
                    company_id: this.csid
                })
                if(response.data.success){
                    //chats
                    this.stats[0].value = response.data[0].Count
                    this.stats[1].value = response.data[1].Count

                    //ticket
                    this.stats[2].value = response.data[2].Count
                    this.stats[3].value = response.data[3].Count
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
        getDepartments: async function(){
            let url = `${this.base_url}/company/get-departments-company-dashboard`
            if(!this.isFirstLoad) {
                this.$spinner.show();
            }
            try{
                let response = await axios.post(url, { 
                    company_id: this.csid
                })
                if(response.data.success){
                    this.selectDepartmentOptions = response.data.result;
                    
                    this.selectDepartmentOptions.unshift({
                        deleted: 0,
                        name: this.$t('bs-all'),
                        id: 'all',
                    });

                    this.departmentSelected = {
                        deleted: 0,
                        name: this.$t('bs-all'),
                        id: 'all',
                    }

                    this.dpSelected = {
                        deleted: 0,
                        name: this.$t('bs-all'),
                        id: 'all',
                    };
                    this.dpSelected2 = this.dpSelected;
                    this.dpSelected3 = this.dpSelected;

                } else {
                    this.$snotify.error(this.$t('bs-error-fetching-summary-card'), this.$t('bs-error'));
                }
            } catch(e) {
                console.log(e);
                console.log('FAILURE!!');
            } finally {
                if(!this.isFirstLoad) {
                    this.$spinner.hide();
                }
            }
        },
        updateOptions(value) {
            this.dpSelected.forEach((element) => {
                if (element.id == "all" && this.dpSelected[0].id != 'all') {
                    this.dpSelected = {
                        deleted: 0,
                        name: this.$t('bs-all'),
                        id: 'all',
                    };
                }
            });

            if(value.length >= 2 && value[0].id == 'all'){
                this.dpSelected.shift();
            }
            this.getInfoDashboard();
        },
        updateOptions2(value) {
            this.dpSelected2.forEach((element) => {
                if (element.id == "all" && this.dpSelected2[0].id != 'all') {
                    this.dpSelected2 = {
                        deleted: 0,
                        name: this.$t('bs-all'),
                        id: 'all',
                    };
                }
            });

            if(value.length >= 2 && value[0].id == 'all'){
                this.dpSelected2.shift();
            }
            this.getInfoBugs();
        },
        updateOptions3(value) {
            this.dpSelected3.forEach((element) => {
                if (element.id == "all" && this.dpSelected3[0].id != 'all') {
                    this.dpSelected3 = {
                        deleted: 0,
                        name: this.$t('bs-all'),
                        id: 'all',
                    };
                }
            });

            if(value.length >= 2 && value[0].id == 'all'){
                this.dpSelected3.shift();
            }
            this.getInfoTopThree();
        },
		loadWeekChart: async function() {
            let url = `${this.base_url}/company/get-bar-chart-company-dashboard`
            if(!this.isFirstLoad) {
                this.$spinner.show();
            }
            try{
                let response = await axios.post(url, { 
                    company_id: this.csid,
                    department_id: this.departmentSelected,
                    period: this.selected
                })
                if(response.data.success){
                    let _this = this
                    this.$set(this.chart1, 'xLabels', response.data.labels.map(function(value) {
                        return (_this.$i18n.locale == "pt_BR") ? `${value.dia}/${value.mes}` : `${value.mes}/${value.dia}`
                    }) )
                    this.$set(this.chart1, 'barData',  [
                        {
                            label: this.$t('bs-chats'),
                            data: response.data.chats,
                        },
                        {
                            label: this.$t('bs-tickets'),
                            data: response.data.tickets,
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
        },
        loadMonthChart: async function () {
            let url = `${this.base_url}/company/get-bar-chart-company-dashboard`
            if(!this.isFirstLoad) {
                this.$spinner.show();
            }
            try{
                let response = await axios.post(url, { 
                    company_id: this.csid,
                    department_id: this.departmentSelected,
                    period: this.selected
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
                            label: this.$t('bs-chats'),
                            data: response.data.chats,
                        },
                        {
                            label: this.$t('bs-tickets'),
                            data: response.data.tickets,
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
        },
        loadYearChart: async function () {
            let url = `${this.base_url}/company/get-bar-chart-company-dashboard`
            if(!this.isFirstLoad) {
                this.$spinner.show();
            }
            try{
                let response = await axios.post(url, { 
                    company_id: this.csid,
                    department_id: this.departmentSelected,
                    period: this.selected
                })
                if(response.data.success){
                    let _this = this
                    this.$set(this.chart1, 'xLabels', response.data.labels.map(function(value) {
                        return (_this.$i18n.locale == "pt_BR") ? `${value.mes}/${value.ano}` : `${value.mes}/${value.ano}`
                    }) )
                    this.$set(this.chart1, 'barData',  [
                        {
                            label: this.$t('bs-chats'),
                            data: response.data.chats,
                        },
                        {
                            label: this.$t('bs-tickets'),
                            data: response.data.tickets,
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
        },
        loadChart: async function(type) {
            this.chart1.loadedData = false
            switch(type.toLowerCase()) {
                case 'week':
                default:
                    await this.loadWeekChart()
                    break;
                case 'month':
                    await this.loadMonthChart()
                    break;
                case 'year':
                    await this.loadYearChart()
                    break;
            }
        },
        getTicketTimeCharts: async function(period) {
            let url = `${this.base_url}/company/get-ticket-time-cards-company-dashboard`
            if(!this.isFirstLoad) {
                this.$spinner.show();
            }
            try{
                let response = await axios.post(url, { 
                    company_id: this.csid,
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
        getChatTimeCharts: async function(period) {
            let url = `${this.base_url}/company/get-chat-time-cards-company-dashboard`
            if(!this.isFirstLoad) {
                this.$spinner.show();
            }
            try{
                let response = await axios.post(url, { 
                    company_id: this.csid,
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
        selectVip(value) {
            if(value){
                var aux = [];
                this.selectDepartmentOptions.forEach((element, index) => {
                    if (element.type == 'builderall-mentor') {
                        aux.push(element);
                    }
                });
                this.dpSelected = aux;
            }else{
                this.dpSelected = {
                    deleted: 0,
                    name: this.$t('bs-all'),
                    id: 'all',
                };
            }
        },
        selectVip2(value) {
            if(value){
                var aux = [];
                this.selectDepartmentOptions.forEach((element, index) => {
                    if (element.type == 'builderall-mentor') {
                        aux.push(element);
                    }
                });
                this.dpSelected2 = aux;
            }else{
                this.dpSelected2 = {
                    deleted: 0,
                    name: this.$t('bs-all'),
                    id: 'all',
                };
            }
        },
        selectVip3(value) {
            console.log(value);
            if(value){
                var aux = [];
                this.selectDepartmentOptions.forEach((element, index) => {
                    if (element.type == 'builderall-mentor') {
                        aux.push(element);
                    }
                });
                this.dpSelected3 = aux;
            }else{
                this.dpSelected3 = {
                    deleted: 0,
                    name: this.$t('bs-all'),
                    id: 'all',
                };
            }
        },
        dateDisabled(ymd, date) {
            const day = date.getDate()
            return day === 2 || day === 3 || day === 4 || day === 5 || day === 6 || day === 7
            || day === 8 || day === 9 || day === 10 || day === 11 || day === 12  || day === 13
            || day === 14 || day === 15 || day === 16 || day === 17 || day === 18 || day === 19
            || day === 20 || day === 21 || day === 22 || day === 23 || day === 24 || day === 25
            || day === 26 || day === 27;
        },
        dateDisabled2(ymd, date) {
            const day = date.getDate()
            return day === 2 || day === 3 || day === 4 || day === 5 || day === 6 || day === 7
            || day === 8 || day === 9 || day === 10 || day === 11 || day === 12  || day === 13
            || day === 14 || day === 15 || day === 16 || day === 17 || day === 18 || day === 19
            || day === 20 || day === 21 || day === 22 || day === 23 || day === 24 || day === 25
            || day === 26 || day === 27;
        },
	},
    watch: {
        selected: function(newVal, oldVal) {
            this.loadChart(newVal)
        },
        departmentSelected: function(newVal, oldVal) {
            this.loadChart(this.selected)
        },
        ticketPeriodSelected: function(newVal, oldVal) {
            this.getTicketTimeCharts(newVal)
        },
        chatPeriodSelected: function(newVal, oldVal) {
            this.getChatTimeCharts(newVal)
        }
    },
	mounted: async function(){
        await this.getDepartments()
        await this.getSummaryCards()
        await this.loadChart(this.selected)
        await this.getTicketTimeCharts(this.ticketPeriodSelected)
        await this.getChatTimeCharts(this.chatPeriodSelected)
        if(this.isFirstLoad){
            this.isFirstLoad = false
        }
    }
};
</script>

<style scoped>

    .teste{
        font-weight: bold !important;
        color: #6c757d !important;
        margin-bottom: 0.5rem !important;
        margin-top: -0.375rem;
    }
    .text-muted{
        font-weight: bold !important;
    }

    .text-card-cust{
        text-align: center;
		font: normal normal 700 3rem Muli;
    }

	.local-title{
		font: normal normal 800 25px/31px Muli;
		letter-spacing: 0px;
		color: #333333;
	}

    .b-calendar .b-calendar-grid-caption {
        background-color: yellow !important;
        color: black !important;
    }
    .b-calendar-grid-weekdays small {
        color: black;
    }
</style>