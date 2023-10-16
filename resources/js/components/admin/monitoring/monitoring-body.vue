<template>
	<div>
		<b-row>
			<b-col cols="12" class="mr-auto p-3 bs-title"> {{$t('bs-monitoring')}}
				<b-card-text class="bs-subtitle">
					{{$t('bs-service-monitoring')}}
				</b-card-text>
			</b-col>
			<b-col cols="auto">
				<b-row class="mt-4">
					<!-- <b-col cols="auto">
						<b-form-select @change="updateType" v-model="selectedType" :options="typeselect">
						</b-form-select>
					</b-col> -->
					<b-col cols="auto">
						<b-form-select v-model="selectedDepart" :options="departselect" @change="filterAll">
							<template #first>
								<b-form-select-option :value="null">{{$t('bs-departments')}}</b-form-select-option>
							</template>
						</b-form-select>
					</b-col>
					<b-col cols="auto">
						<b-button variant="info" @click="toggleFullScreen">
							<i v-show="iconBolean" class="fa fa-expand" aria-hidden="true"></i>
							<i v-show="!iconBolean" class="fa fa-compress" aria-hidden="true"></i>
							{{$t('bs-full-screen')}}
						</b-button>
					</b-col>
				</b-row>
			</b-col>
		</b-row>
		<br>
		<span class="update-date mt-2">{{ now }}</span>


		<b-row>

			<b-col class="col-custom">
				<b-card class="custom-center">
					<b-card-text>
						<span class="title-card">{{$t('bs-in-queue')}}</span>
						<span class="sub-card mt-1">
							<span class="font-custom">{{ form.countTicketChat }}</span><br>
							<span v-show="chat_ticket">{{$t('bs-chats')}}</span>
							<span v-show="!chat_ticket">{{$t('bs-tickets')}}</span>
						</span>
					</b-card-text>
				</b-card>
			</b-col>

			<b-col class="col-custom">
				<b-card class="custom-center">
					<b-card-text>
						<span class="title-card">{{$t('bs-waiting-time')}}</span>
						<span class="sub-card mt-1">
							<span class="font-custom">{{ formatTime(form.avgWaitingTime) }}</span><br>
							<span>{{$t('bs-average')}}</span>
						</span>
					</b-card-text>
				</b-card>
			</b-col>

			<b-col class="col-custom">
				<b-card class="custom-center">
					<b-card-text>
						<span class="title-card">{{$t('bs-lost')}}</span>
						<span class="sub-card mt-1">
							<span class="font-custom">{{ form.TicketsChatsCancelled }}</span><br>
							<span v-show="chat_ticket">{{$t('bs-chats')}}</span>
							<span v-show="!chat_ticket">{{$t('bs-tickets')}}</span>
						</span>
					</b-card-text>
				</b-card>
			</b-col>

		</b-row>


		<b-row>
			<b-col class="col-custom">
				<b-card class="custom-center">
					<b-card-text>
						<span class="title-card">{{$t('bs-service-at-the-moment')}}</span>
						<span class="sub-card mt-1">
							<span class="font-custom">{{ form.chatsInProgress  }}</span><br>
							<span v-show="chat_ticket">{{$t('bs-chats')}}</span>
							<span v-show="!chat_ticket">{{$t('bs-tickets')}}</span>
						</span>
					</b-card-text>
				</b-card>
			</b-col>
			<b-col class="col-custom">
				<b-card class="custom-center">
					<b-card-text>
						<span class="title-card">{{$t('bs-chats-by-attendant')}}</span>
                        <br><br>
						<span class="sub-card-2 mt-1">
                            <b-row>
                                <b-col class="col-custom">
                                    <span class="font-custom">{{ form.TicketsChatsPerAgentConnected }}</span><br>
                                    <span>{{$t('bs-connected')}}</span>
                                </b-col>
                                <b-col class="col-custom">
                                        <span class="font-custom">{{ form.TicketsChatsPerAgentOnline }}</span><br>
                                        <span>{{$t('bs-on')}}</span>
                                </b-col>
                            </b-row>
                        </span>
					</b-card-text>
				</b-card>
			</b-col>
			<b-col class="col-custom">
				<b-card class="custom-center">
					<b-card-text>
						<span class="title-card">{{$t('bs-duration')}}</span>
                            <br><br>
                            <span class="sub-card-2 mt-1">
                                <b-row>
                                    <b-col class="col-custom">
                                        <span class="font-custom">{{ formatTime(form.TicketsChatsLongerServiceTime) }}</span><br>
                                        <span>{{$t('bs-longe')}}</span>
                                    </b-col>
                                    <b-col class="col-custom">
                                            <span class="font-custom">{{ formatTime(form.TicketsChatsAVGServiceTime) }}</span><br>
                                            <span>{{$t('bs-average')}}</span>
                                    </b-col>
                                </b-row>
                            </span>
					</b-card-text>
				</b-card>
			</b-col>
		</b-row>
		<b-row>
			<b-col class="col-custom">
				<b-card class="custom-center">
					<b-card-text>
						<span class="title-card">{{$t('bs-on')}}</span>
                        <br><br>
						<span class="sub-card-2 mt-1">
							<b-row>
								<b-col class="col-custom">
									<span class="font-custom">{{ form.onlineAgents }}</span><br>
									<span>{{$t('bs-agents')}}</span>
								</b-col>
								<b-col class="col-custom">
										<span class="font-custom">{{ form.onlineClients }}</span><br>
										<span>{{$t('bs-clients')}}</span>
								</b-col>
							</b-row>
						</span>
					</b-card-text>
				</b-card>
			</b-col>
			<b-col class="col-custom">
				<b-card class="custom-center">
					<b-card-text>
						<span class="title-card mb-1">{{`${$t('bs-satisfaction')} (${$t('bs-service')})`}}</span>
						<br><br>
						<span class="sub-card-2 mt-1">
							<b-row>
								<b-col class="col-custom">
                                    <span class="font-custom">{{ form.service_satisfaction }}</span>
                                    <b-form-rating v-model="form.service_satisfaction" readonly precision="2" no-border variant="warning"></b-form-rating>
								</b-col>
							</b-row>
						</span>
					</b-card-text>
				</b-card>
			</b-col>
            <b-col class="col-custom">
				<b-card class="custom-center">
					<b-card-text>
						<span class="title-card mb-1">{{`${$t('bs-satisfaction')} (${$t('bs-attendant')})`}}</span>
						<br><br>
						<span class="sub-card-2 mt-1">
							<b-row>
								<b-col class="col-custom">
                                    <span class="font-custom">{{ form.atendent_satisfaction }}</span>
                                    <b-form-rating v-model="form.atendent_satisfaction" readonly precision="2" no-border variant="warning"></b-form-rating>
								</b-col>
							</b-row>
						</span>
					</b-card-text>
				</b-card>
			</b-col>
		</b-row>


	</div>
</template>

<script>
var convert = require('convert-seconds');
var moment = require("moment-timezone");
export default {
	data(){
		return {
            now: "",
			selectedType: 'CHAT',
			typeselect: [
				{ value: 'CHAT', text: this.$t('bs-chats'), disabled: false },
				// { value: 'TICKET', text: this.$t('bs-tickets'), disabled: true },
			],
			selectedDepart: null,
			departselect: [],
			iconBolean: true,
			chat_ticket: true,
            form: {
                countTicketChat: Number(0),
                onlineAgents: Number(0),
                onlineClients: Number(0),
                chatsInProgress: Number(0),
                avgWaitingTime: Number(0),
                avgServiceTime: Number(0),
                countTicketChatsToday: Number(0),
                countTicketChatsAnsweredToday: Number(0),
                sumQueueTime: Number(0),
                TicketsChatsCancelled: Number(0),
                TicketsChatsPerAgentConnected: Number(0),
                TicketsChatsPerAgentOnline: Number(0),
                TicketsChatsAVGServiceTime: Number(0),
                TicketsChatsLongerServiceTime: Number(0),
                sumTotalServiceTime: Number(0),
                service_satisfaction: Number(0),
                atendent_satisfaction: Number(0),
            },
            sumAVGWaitingTime: Number(0),
            sum_diff_time_value: Function,
		}
	},
    props: {
        session_user_company: Object,
        session_user: Object,
    },
    created () {
        setInterval(() => {
            this.now = moment().lang(this.session_user.language.split('_')[0]).format('llll');
        }, 1000);
    },
	mounted(){
		this.getDepartments();
        this.filterAll();
        this.joinEvaluationsChannel();
	},
    computed: {
        countTicketChatsToday() {
            return this.form.countTicketChatsToday;
        },
        TicketsChatsCancelled() {
            return this.form.TicketsChatsCancelled;
        },
        sumTotalServiceTime() {
            return this.form.sumTotalServiceTime;
        },
    },
    watch: {
        "$store.state.online_users": function () {
            this.setOnlineUsers();
        },
        "$store.state.chats_in_progress": function (newVal) {
            this.setChatsInProgress().then(() => {
               this.SumTicketsChatsPerAgentOnline();
                clearInterval(this.sum_diff_time_value);
                this.form.countTicketChatsAnsweredToday = this.form.countTicketChatsToday - this.form.TicketsChatsCancelled;
                this.SumDurationTicketsChats().then((result) => {
                    this.form.sumTotalServiceTime = result.sum_total_service_time;
                    this.form.TicketsChatsAVGServiceTime = result.avg;
                    this.form.TicketsChatsLongerServiceTime = result.longer;
                    this.calcTicketChatDurationRealTime(result.diff);
                })
                this.countAgentsByCompany().then((count) => {
                    this.SumTicketsChatsPerAgentConnected(count);
                });
            })
        },
        sumAVGWaitingTime() {
            this.form.sumQueueTime +=1;
            this.form.avgWaitingTime = (this.form.sumQueueTime) / this.form.countTicketChatsToday;
        },
        countTicketChatsToday(value) {
            this.form.countTicketChatsAnsweredToday = value - this.form.TicketsChatsCancelled;
        },
        TicketsChatsCancelled(value) {
            this.form.countTicketChatsAnsweredToday = this.form.countTicketChatsToday - value;
        },
        sumTotalServiceTime(value) {
            this.form.TicketsChatsAVGServiceTime = value/this.form.countTicketChatsAnsweredToday;
        }

    },
	methods: {
		updateType(){
			this.chat_ticket = !this.chat_ticket;
            this.filterAll();
		},
		toggleFullScreen(){
			this.iconBolean = !this.iconBolean;
			if ((document.fullScreenElement && document.fullScreenElement !== null) || (!document.mozFullScreen && !document.webkitIsFullScreen)) {
				if (document.documentElement.requestFullScreen) {
				document.documentElement.requestFullScreen();
				} else if (document.documentElement.mozRequestFullScreen) {
				document.documentElement.mozRequestFullScreen();
				} else if (document.documentElement.webkitRequestFullScreen) {
				document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
				}
			} else {
				if (document.cancelFullScreen) {
				document.cancelFullScreen();
				} else if (document.mozCancelFullScreen) {
				document.mozCancelFullScreen();
				} else if (document.webkitCancelFullScreen) {
				document.webkitCancelFullScreen();
				}
			}
		},
		getDepartments(){
			axios.get('/get-departments-by-company')
			.then(res => {
				this.departselect = res.data;
			})
			.catch(err => {
				console.error(err);
			})
		},
        filterAll() {
            this.countTicketsChatsOnQueue().then((count) => {
                this.form.countTicketChat = count;
                this.calcAVGWaitingTime().then((value) => {
                    this.form.avgWaitingTime = value.avg_queue_time;
                    this.form.avgServiceTime = value.avg_service_time;
                    this.form.countTicketChatsToday = value.count;
                    this.form.sumQueueTime = parseInt(value.sum_queue_time);
                    this.calcAVGWaitingTimeRealtime();
                    this.joinInQueueChatChannel();
                });
            });

            this.getChatsCancelled().then((count) => {
                this.form.TicketsChatsCancelled = count;
                this.joinCanceledChatChannel();
            });

            this.getEvaluationsToday().then((evaluations) => {
                this.form.service_satisfaction = evaluations.service_satisfaction;
                this.form.atendent_satisfaction = evaluations.atendent_satisfaction;
            });

            this.setChatsInProgress().then(() => {
               this.SumTicketsChatsPerAgentOnline();
                clearInterval(this.sum_diff_time_value);
                this.form.countTicketChatsAnsweredToday = this.form.countTicketChatsToday - this.form.TicketsChatsCancelled;
                this.SumDurationTicketsChats().then((result) => {
                    this.form.sumTotalServiceTime = result.sum_total_service_time;
                    this.form.TicketsChatsAVGServiceTime = result.avg;
                    this.form.TicketsChatsLongerServiceTime = result.longer;
                    this.calcTicketChatDurationRealTime(result.diff);
                })
                this.countAgentsByCompany().then((count) => {
                    this.SumTicketsChatsPerAgentConnected(count);
                });
            })

            this.setOnlineUsers();
        },
        countTicketsChatsOnQueue() {
            return new Promise((resolve, reject) => {
                var vm = this;
                axios.get('monitoring/count-tickets-chats-on-queue', {
                    params: {
                        type: vm.selectedType,
                        departments: vm.selectedDepart
                    }
                })
                .then(({data}) => {
                    if(data.success) {
                        resolve(data.count)
                    }
                })
            })
        },
        calcAVGWaitingTime() {
            return new Promise((resolve, reject) => {
                var vm = this;
                axios.get('monitoring/calc-avg-waiting-time', {
                    params: {
                        type: vm.selectedType,
                        departments: vm.selectedDepart,
                        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                    }
                })
                .then(({data}) => {
                    if(data.success) {
                        resolve(data.value)
                    }
                })
            })
        },
        calcAVGWaitingTimeRealtime() {
            const timeValue = setInterval(() => {
                if (this.form.countTicketChat == 0) {
                    clearInterval(timeValue);
                } else {
                    this.sumAVGWaitingTime++;
                }
            }, 1000);
        },
        getChatsCancelled() {
            return new Promise((resolve, reject) => {
                var vm = this;
                axios.get('monitoring/get-chats-tickets-canceled', {
                    params: {
                        type: vm.selectedType,
                        departments: vm.selectedDepart,
                        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                    }
                })
                .then(({data}) => {
                    if (data.success) {
                        resolve(data.count);
                    }
                })
            })
        },
        countAgentsByCompany() {
            return new Promise((resolve, reject) => {
                var vm = this;
                axios.get('monitoring/count-agents-by-company', {
                    params: {
                        department: vm.selectedDepart
                    }
                })
                .then(({data}) => {
                    if (data.success) {
                        resolve(data.count)
                    }
                })
            })
        },
        SumTicketsChatsPerAgentConnected(count) {
            if (this.$store.state.chats_in_progress.length > 0) {
                this.form.TicketsChatsPerAgentConnected = this.$store.state.chats_in_progress.length/count;
            } else {
                this.form.TicketsChatsPerAgentConnected = Number(0);
            }
        },
        SumTicketsChatsPerAgentOnline() {
            if (this.$store.state.chats_in_progress.length > 0 && this.form.onlineAgents > 0) {
                this.form.TicketsChatsPerAgentOnline = this.form.chatsInProgress/this.form.onlineAgents;
            } else {
                this.form.TicketsChatsPerAgentOnline = Number(0);
            }
        },
		SumDurationTicketsChats() {
			return new Promise((resolve, reject) => {
				var vm = this;
				axios.get('monitoring/sum-duration-tickets-chats', {
					params: {
						type: vm.selectedType,
                        departments: vm.selectedDepart,
                        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
					}
				})
				.then(({data}) => {
                    if (data.success) {
                        resolve(data.counters)
                    }
				})
			})
		},
        calcTicketChatDurationRealTime(chats) {
            chats.forEach(element => {
                if (element.diff != null) {
                    this.sumTicketChatDiff(element.diff);
                }
            });
        },
        sumTicketChatDiff(diff) {
            this.sum_diff_time_value = setInterval(() => {
                diff++;
                this.form.sumTotalServiceTime++;
                var diff_now = diff;
                if (diff_now > this.form.TicketsChatsLongerServiceTime) {
                    this.form.TicketsChatsLongerServiceTime = diff_now;
                }
            }, 1000);
        },
        getEvaluationsToday() {
            return new Promise((resolve, reject) => {
                var vm = this;
                axios.get('monitoring/get-evaluations-today', {
                    params: {
                        type: vm.selectedType,
                        departments: vm.selectedDepart,
                        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                    }
                })
                .then(({data}) => {
                    if (data.success) {
                        resolve(data.evaluations)
                    }
                })
            });
        },
        setChatsInProgress() {
            return new Promise((resolve, reject) => {
                if (this.selectedDepart == null) {
                    this.form.chatsInProgress = this.$store.state.chats_in_progress.length;
                    resolve();
                } else {
                    var itemsProcessed = 0;
                    var count = Number(0);
                    if (this.$store.state.chats_in_progress.length) {
                        this.$store.state.chats_in_progress.forEach(element => {
                            itemsProcessed++;
                            if (element.company_department_id == this.selectedDepart) {
                                count++;
                            }
                            if(itemsProcessed === this.$store.state.chats_in_progress.length) {
                                this.form.chatsInProgress = count;
                                resolve();
                            }
                        });
                    } else {
                        this.form.chatsInProgress = count;
                        resolve();
                    }
                }
            })
        },
        getDepartmentByChatId(chat_id) {
            return new Promise((resolve, reject) => {
                var vm = this;
                axios.get('monitoring/get-department-by-chat-id', {
                    params: {
                        chat_id: chat_id
                    }
                })
                .then(({data}) => {
                    if (data.success) {
                        resolve(data.department_id)
                    }
                })
            })
        },
        countOnlineAgentsByDeparment(online_agents) {
            return new Promise((resolve, reject) => {
                var vm = this;
                axios.get('monitoring/count-online-agents-by-department', {
                    params: {
                        online_agents: online_agents,
                        department_id: vm.selectedDepart
                    }
                })
                .then(({data}) => {
                    if (data.success) {
                        resolve(data.count);
                    }
                })
            })
        },
        setOnlineUsers() {
            // AGENTS
            var online_agents = this.$store.state.online_users;
            online_agents = online_agents.filter((u) => u.is_client !== 1);
            online_agents = online_agents.filter((u) => u.status == 'online');
            if (this.selectedDepart == null) {
                this.form.onlineAgents = online_agents.length;
                this.SumTicketsChatsPerAgentOnline();
            } else {
                var online_agents_ids = [];
                var itemsProcessed = 0;
                online_agents.forEach(element => {
                    itemsProcessed++;
                    online_agents_ids.push(element.hash_id)
                    if(itemsProcessed === online_agents.length) {
                        this.countOnlineAgentsByDeparment(online_agents_ids).then((count) => {
                            this.form.onlineAgents = count;
                            this.SumTicketsChatsPerAgentOnline();
                        });
                    }
                });
            }

            // CLIENTS
            var online_clients = this.$store.state.online_users;
            online_clients = online_clients.filter((u) => u.is_client == 1);
            online_clients = online_clients.filter((u) => u.status == 'online');
            this.form.onlineClients = online_clients.length;
        },

        /** @UTIL */

        formatTime(seconds) {
            if (isFinite(seconds) && seconds > 0) {
                var converted = convert(seconds);
                var hours = converted.hours;
                var min = converted.minutes;
                var sec = converted.seconds;

                if (hours > 0) {
                    return `${hours}h ${min}min ${sec}s`
                } else if (min > 0) {
                    return `${min}min ${sec}s`
                } else {
                    return `${sec}s`
                }
            } else {
                return "0s"
            }

        },

        /** @WEBSOCKETS_CHANNELS */

        joinEvaluationsChannel() {
            const channel = `evaluation`;
            const event = `EvaluationUpdated`;
            Echo.leave(`${channel}.${this.session_user_company.id}`)
            Echo.join(`${channel}.${this.session_user_company.id}`).listen(
                event,
                e => {
                    if (this.selectedDepart == null) {
                        this.getEvaluationsToday().then((evaluations) => {
                            this.form.service_satisfaction = evaluations.service_satisfaction;
                            this.form.atendent_satisfaction = evaluations.atendent_satisfaction;
                        })
                    } else {
                        this.getDepartmentByChatId(e.item.chat_id).then((department_id) => {
                            if (department_id == this.selectedDepart) {
                                this.getEvaluationsToday().then((evaluations) => {
                                    this.form.service_satisfaction = evaluations.service_satisfaction;
                                    this.form.atendent_satisfaction = evaluations.atendent_satisfaction;
                                })
                            }
                        })
                    }
                }
            );
        },
        joinInQueueChatChannel() {
            const channel = `queue`;
            const event = `QueueUpdated`;
            Echo.leave(`${channel}.${this.session_user_company.id}`)
            Echo.join(`${channel}.${this.session_user_company.id}`).listen(
                event,
                e => {
                    switch (e.item.action) {
                        case 'splice':
                            if (this.selectedDepart == null) {
                                this.form.countTicketChat--;
                            } else {
                                this.getDepartmentByChatId(e.item.chat_id).then((department_id) => {
                                    if (department_id == this.selectedDepart) {
                                        this.form.countTicketChat--;
                                    }
                                })
                            }
                        break;

                        case 'push':
                            if (this.selectedDepart == null) {
                                var check_department = true;
                            } else if (e.item.company_department_id == this.selectedDepart) {
                                var check_department = true;
                            } else {
                                var check_department = false;
                            }

                            if (check_department) {
                                this.form.countTicketChat++;
                                if (this.form.countTicketChat < 2) {
                                    this.calcAVGWaitingTime().then((value) => {
                                        this.form.avgWaitingTime = value.avg_queue_time;
                                        this.form.avgServiceTime = value.avg_service_time;
                                        this.form.countTicketChatsToday = value.count;
                                        this.form.sumQueueTime = parseInt(value.sum_queue_time);
                                        this.calcAVGWaitingTimeRealtime();
                                    });
                                } else {
                                    this.form.countTicketChatsToday++;
                                }
                            }
                        break;
                    }
                }
            );
        },
        joinCanceledChatChannel() {
            const channel = `full-chat.canceled`;
            const event = `FullChatCanceled`;
            Echo.leave(`${channel}.${this.session_user_company.id}`)
            Echo.join(`${channel}.${this.session_user_company.id}`).listen(
                event,
                e => {
                    switch (e.item.action) {
                        case 'push':
                            if (this.selectedDepart == null) {
                                var check_department = true;
                            } else if (e.item.company_department_id == this.selectedDepart) {
                                var check_department = true;
                            } else {
                                var check_department = false;
                            }

                            if (check_department) {
                                this.form.TicketsChatsCancelled++;
                            }

                        break;
                    }
                }
            );
        }
	},
};
</script>

<style scoped>

	.rowcustom{
		/* border: 1px solid red; */
		height: 20px;
		/* width: 100px; */
		display: inline-block;
		margin-left: 20px;
		margin-top: 20px;
		text-align: center;
	}

	.green{
		color: green;
	}
	.red{
		color: red;
	}

	.font-custom{
		font: normal normal bold 23px/20px Muli !important;
	}

	.sub-card{
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		text-align: center;
	}

	.sub-card-2{
		position: inherit;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		text-align: center;
	}

	.custom-center{
		position: relative;
		min-height: 160px;
		box-shadow: 0px 3px 6px #00000029;
	}

	.title-card{
		top: 369px;
		left: 380px;
		width: 42px;
		height: 30px;
		text-align: left;
		font: normal normal 800 17px/17px Muli;
		letter-spacing: 0px;
		color: #333333;
		opacity: 1;
	}

	.update-date{
		text-align: left;
		font: normal normal bold 16px/24px Muli;
		letter-spacing: 0px;
		color: #434343;
		opacity: 1;
	}

	.col-custom{
		padding: 8px !important;
	}

	.row{
		display: flex !important;
	}

	@media only screen and (max-width: 576px) {

	}

	@media only screen and (max-width: 960px) {
		.row{
			display: block !important;
		}

		.custom-center{
			position: relative;
			min-height: 180px;
			box-shadow: 0px 3px 6px #00000029;
		}
	}

	@media only screen and (max-width: 1200px) {
		.rowcustom{
			/* border: 1px solid red; */
			height: 20px;
			display: inline-block;
			margin-left: 0;
			margin-top: 0;
		}
	}
</style>
