<template>
	<div>
		<b-container fluid>
			<!-- title -->
			<b-row
				class="mb-5 px-2"
				cols="1"
				cols-md="2"
				align-h="between"
				align-v="center"
				no-gutters
			>
				<b-col class="py-2">
				<h3 class="bs-title" v-html="title"></h3>
				<span class="bs-subtitle" v-html="`${subtitle}${csname}`"></span>
				</b-col>
				<b-col class="text-md-right py-2">
					
					
				<span v-if="usuario.builderall_status == 'ACTIVE'">
					<div v-if="usuario.can_create_company == 1">
						<b-button @click="createCompany" variant="btn bs-btn-add btn-block-sm">
							<i class="fa fa-building-o" aria-hidden="true"></i> {{ btnNewCompany }}
						</b-button>
					</div>
				</span>
				
				</b-col>
			</b-row>

			<!-- line one -->
			<b-row
				no-gutters
				cols="1"
				cols-sm="2"
				cols-lg="4"
				cols-xl="5"
				align-h="start"
				align-v="stretch"
				class="mb-5"
			>
				<b-col v-for="(stat, k) in stats" :key="k" class="mt-2 px-2">
				<summary-card
					:circularBgIcon="{
					icon: {
						name: stat.icon,
						size: 26,
						color: 'white',
					},
					bgClass: stat.background,
					}"
					:title="stat.title"
					:value="stat.value"
				>
					<circular-bg-icon
					class="mr-1"
					slot="icon"
					slot-scope="prop"
					v-bind="prop.circularBgIcon"
					>
					<i
						class="material-icons"
						slot="icon"
						slot-scope="prop"
						:style="{
						color: prop.icon.color,
						fontSize: `${prop.icon.size}px!important`,
						}"
						>{{ prop.icon.name }}</i
					>
					</circular-bg-icon>
				</summary-card>
				</b-col>
			</b-row>

			<b-row class="mb-5 px-2" align-v="stretch">
				<b-col sm="12" lg="9" class="py-3">
				<!-- card charts -->
				<b-card style="height: 100% !important">
					<b-card-header class="px-0 bg-transparent">
					<b-container fluid class="px-0">
						<b-row cols-sm="1" align-h="between" align-v="stretch" no-gutters>
						<b-col md="9">
							<h4 v-text="chart1.title"></h4>
						</b-col>
						<b-col md="3">
							<b-form-select
							v-model="selected"
							:options="selectOptions"
							class="dashboard_select"
							></b-form-select>
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
							},
						],
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
							fullWidth: false,
							},
						},
						scales: {
							yAxes: [
							{
								gridLines: {
								borderDash: [1, 2],
								drawBorder: false,
								},
								ticks: {
								beginAtZero: true, // inicia de 0
								suggestedMin: 0,
								suggestedMax: 100,
								min: 0, // sem valores negativos
								},
							},
							],
							xAxes: [
							{
								gridLines: {
								display: false,
								},
							},
							],
						},
						}"
						:styles="{
						// parent node
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
							chart.legend.afterFit = function () {
								this.height = this.height + 30;
							};
							},
						},
						]"
					></bar-chart>
					<div v-else class="text-center">Loading...</div>
					</b-card-text>
				</b-card>
				</b-col>
				<b-col sm="12" lg="3" class="py-3">
				<progress-card
					:title="chart2.title"
					:doneToday="chart2.today"
					:doneYesterday="chart2.yesterday"
					:totalToday="chart2.totalToday"
					:totalYesterday="chart2.totalYesterday"
				></progress-card>
				<progress-card
					:title="chart3.title"
					:doneToday="chart3.today"
					:doneYesterday="chart3.yesterday"
					:totalToday="chart3.totalToday"
					:totalYesterday="chart3.totalYesterday"
					:todayBarVariant="'info'"
				></progress-card>
				</b-col>
			</b-row>
		</b-container>
  	</div>
</template>

<script>
export default {
  name: "home-admin-dashboard",
  props: {
    usuario: Object,
    csname: String,
    csid: String,
    cslogo: String,
    gravatar: String,
    base_url: {
      type: String,
      default: "",
    },
  },
  data() {
    return {
      // header stats
      btnNewCompany: this.$t("bs-change-company"),
      title: this.$t("bs-menu-home"),
      subtitle: `${this.$t("bs-welcome-to")} `,
      stats: [
        {
          icon: "person",
          background: "blue-gradiente-bg",
          value: "0",
          title: this.$t("bs-open-chats"),
        },
        {
          icon: "chat_bubble",
          background: "red-gradiente-bg",
          value: "0",
          title: this.$t("bs-chats-in-progress"),
        },
        {
          icon: "group",
          background: "yellow-gradiente-bg",
          value: "0",
          title: this.$t("bs-open-tickets"),
        },
        {
          icon: "perm_phone_msg",
          background: "blue-gradiente-bg",
          value: "0",
          title: this.$t("bs-tickets-in-progress"),
        },
        {
          icon: "person",
          background: "green-gradiente-bg",
          value: "0",
          title: this.$t("bs-on-attendants"),
        },
      ],

      selected: "week",
      selectOptions: [
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

      // Bar chart
      chart1: {
        title: this.$t("bs-tk-chat-open"),
        loadedData: false,
        xLabels: [],
        barData: [
          {
            label: this.$t("bs-chats"),
            data: [30, 35, 29, 37, 40, 45, 50],
          },
          {
            label: this.$t("bs-tickets"),
            data: [16, 20, 18, 21, 25, 26, 27],
          },
        ],
      },
      // Progress Chats
      chart2: {
        title: this.$t("bs-closed-chats-previous-day"),
        today: 290,
        yesterday: 300,
        totalToday: 500,
        totalYesterday: 400,
      },
      // Progress Tickets
      chart3: {
        title: this.$t("bs-closed-tickets-previous-day"),
        today: 310,
        totalToday: 600,
        totalYesterday: 700,
      },
      isFirstLoad: true,
    };
  },
  methods: {
    createCompany: function () {
      // redir to route create company
      window.open("select-company", "_self");
    },
    getSummaryCards: async function () {
      let url = `${this.base_url}/home/get-summary-cards-home-dashboard`;
      if (!this.isFirstLoad) {
        this.$spinner.show();
      }
      try {
        let response = await axios.post(url, {
          company_id: this.csid,
        });
        if (response.data.success) {
          //chats
          this.stats[0].value = response.data.chat.qtd_opened;
          this.stats[1].value = response.data.chat.qtd_in_progress;

          //ticket
          this.stats[2].value = response.data.ticket.qtd_opened;
          this.stats[3].value = response.data.ticket.qtd_in_progress;
        } else {
          vm.$snotify.error(vm.$t("bs-error-fetching-summary-card"), vm.$t("bs-error"));
        }
      } catch (e) {
        console.log("FAILURE!!");
      } finally {
        if (!this.isFirstLoad) {
          this.$spinner.hide();
        }
      }
    },
    getProgressCards: async function () {
      let url = `${this.base_url}/home/get-progress-cards-home-dashboard`;
      if (!this.isFirstLoad) {
        this.$spinner.show();
      }
      try {
        let response = await axios.post(url, {
          company_id: this.csid,
        });
        if (response.data.success) {
          //chats
          this.chart2.today = response.data.chat.fechado_hoje;
          this.chart2.totalToday = response.data.chat.total_hoje;

          this.chart2.yesterday = response.data.chat.fechado_ontem;
          this.chart2.totalYesterday = response.data.chat.total_ontem;

          //ticket
          this.chart3.today = response.data.ticket.fechado_hoje;
          this.chart3.totalToday = response.data.ticket.total_hoje;

          this.chart3.yesterday = response.data.ticket.fechado_ontem;
          this.chart3.totalYesterday = response.data.ticket.total_ontem;
        } else {
          vm.$snotify.error(vm.$t("bs-error-fetching-progress-card"), vm.$t("bs-error"));
        }
      } catch (e) {
        console.log("FAILURE!!");
      } finally {
        if (!this.isFirstLoad) {
          this.$spinner.hide();
        }
      }
    },
    loadWeekChart: async function () {
      let url = `${this.base_url}/home/get-bar-chart-home-dashboard`;
      if (!this.isFirstLoad) {
        this.$spinner.show();
      }
      try {
        let response = await axios.post(url, {
          company_id: this.csid,
          period: this.selected,
        });
        if (response.data.success) {
          let _this = this;
          this.$set(
            this.chart1,
            "xLabels",
            response.data.labels.map(function (value) {
              return _this.$i18n.locale == "pt_BR"
                ? `${value.dia}/${value.mes}`
                : `${value.mes}/${value.dia}`;
            })
          );
          this.$set(this.chart1, "barData", [
            {
              label: this.$t("bs-chats"),
              data: response.data.chats,
            },
            {
              label: this.$t("bs-tickets"),
              data: response.data.tickets,
            },
          ]);
        } else {
          vm.$snotify.error(vm.$t("bs-error-fetching-bar-chart"), vm.$t("bs-error"));
        }
      } catch (e) {
        console.log("FAILURE!!");
      } finally {
        this.chart1.loadedData = true;
        if (!this.isFirstLoad) {
          this.$spinner.hide();
        }
      }
    },
    loadMonthChart: async function () {
      let url = `${this.base_url}/home/get-bar-chart-home-dashboard`;
      if (!this.isFirstLoad) {
        this.$spinner.show();
      }
      try {
        let response = await axios.post(url, {
          company_id: this.csid,
          period: this.selected,
        });
        if (response.data.success) {
          let _this = this;
          this.$set(
            this.chart1,
            "xLabels",
            response.data.labels.map(function (value) {
              switch (value) {
                case 1:
                  return _this.$t("bs-first-week");
                case 2:
                  return _this.$t("bs-second-week");
                case 3:
                  return _this.$t("bs-third-week");
                case 4:
                  return _this.$t("bs-fourth-week");
              }
            })
          );
          this.$set(this.chart1, "barData", [
            {
              label: this.$t("bs-chats"),
              data: response.data.chats,
            },
            {
              label: this.$t("bs-tickets"),
              data: response.data.tickets,
            },
          ]);
        } else {
          vm.$snotify.error(vm.$t("bs-error-fetching-bar-chart"), vm.$t("bs-error"));
        }
      } catch (e) {
        console.log("FAILURE!!");
      } finally {
        this.chart1.loadedData = true;
        if (!this.isFirstLoad) {
          this.$spinner.hide();
        }
      }
    },
    loadYearChart: async function () {
      let url = `${this.base_url}/home/get-bar-chart-home-dashboard`;
      if (!this.isFirstLoad) {
        this.$spinner.show();
      }
      try {
        let response = await axios.post(url, {
          company_id: this.csid,
          period: this.selected,
        });
        if (response.data.success) {
          let _this = this;
          this.$set(
            this.chart1,
            "xLabels",
            response.data.labels.map(function (value) {
              return _this.$i18n.locale == "pt_BR"
                ? `${value.mes}/${value.ano}`
                : `${value.mes}/${value.ano}`;
            })
          );
          this.$set(this.chart1, "barData", [
            {
              label: this.$t("bs-chats"),
              data: response.data.chats,
            },
            {
              label: this.$t("bs-tickets"),
              data: response.data.tickets,
            },
          ]);
        } else {
          vm.$snotify.error(vm.$t("bs-error-fetching-bar-chart"), vm.$t("bs-error"));
        }
      } catch (e) {
        console.log("FAILURE!!");
      } finally {
        this.chart1.loadedData = true;
        if (!this.isFirstLoad) {
          this.$spinner.hide();
        }
      }
    },
    loadChart: async function (type) {
      this.chart1.loadedData = false;
      switch (type.toLowerCase()) {
        case "week":
        default:
          await this.loadWeekChart();
          break;
        case "month":
          await this.loadMonthChart();
          break;
        case "year":
          await this.loadYearChart();
          break;
      }
    },
  },
  watch: {
    selected: function (newVal, oldVal) {
      this.loadChart(newVal);
    },
    "$store.state.online_users": function () {
        var online_users = this.$store.state.online_users;
        online_users = online_users.filter((u) => u.is_client !== 1);
        online_users = online_users.filter((u) => u.status == 'online');
        this.stats[4].value = online_users.length;
    },
  },
  mounted: async function () {
    await this.getSummaryCards();
    await this.getProgressCards();
    await this.loadChart(this.selected);
    if (this.isFirstLoad) {
      this.isFirstLoad = false;
    }
  },
};
</script>

<style scoped lang="scss"></style>
