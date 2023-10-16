<template>
    <v-row no-gutters justify="start">
        <v-col cols="11" xl="6" lg="7" md="9" sm="9" class="text-right">
            <v-row no-gutters class="py-1">
                <v-col class="w-fc mr-5">
                    <v-gravatar
                        v-show="!$store.state.isMobile"
                        :size="$store.state.isMobile ? '30' : '40'"
                        :robot="true"
                    ></v-gravatar>
                </v-col>
                <v-col style="width: 1px" class="mt-1">
                    <div class="bubble-left" style="max-width: 100%;">
                        <v-list two-line class="py-0" rounded color="transparent">
                            <v-list-item class="px-1">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <v-row no-gutters>
                                            <v-col class="text-truncate text-left text-subtitle-2">
                                                {{ $store.state.name_robot }}
                                            </v-col>
                                            <v-col class="text-right w-fc text-caption">
                                                <small>{{ $formatDate(message.created_at) }}</small>
                                            </v-col>
                                        </v-row>
                                    </v-list-item-title>
                                    <v-list-item-subtitle v-if="!isDeptRobot" class="text-body-2 text-left text-wrap"  style="word-break: break-word !important;">
                                        {{$ct(message.content)}}
                                        <div class="pa-2" v-if="message.showDeptSelect">
                                            <v-select
                                                :items="departments"
                                                dense
                                                solo
                                                v-model="$store.state.newChat.department"
                                                :readonly="$store.state.newChat.department !== null"
                                                item-disabled="disabled"
                                                background-color="input"
                                                class="rounded-lg"
                                                hide-details
                                            >
                                                <template v-slot:label>
                                                    <span class="text-body-2">{{ $t('bs-select-a-department') }}</span>
                                                </template>
                                                <template v-slot:selection="{ item }">
                                                    <!-- <span class="text-body-2">{{item.name}}</span> -->
                                                    <!-- <span class="text-body-2">{{item.name}} - [{{item.openHour}} {{ item.closeHour }}]</span> -->
                                                    <span v-if="!item.disabled" class="text-body-2">{{item.name}} - [{{format_L(item.openDateToUTC, item.disabled)}} {{format_L(item.closeDateToUTC, item.disabled)}}]</span>
                                                    <span v-else class="text-body-2">{{item.name}} - [{{$t('bs-closed')}}]</span>
                                                </template>
                                                <template v-slot:item="{ item }">
                                                    <!-- <span class="text-body-2">{{item.name}}</span> -->
                                                    <!-- <span class="text-body-2">{{item.name}} - [{{item.openHour}} {{ item.closeHour }}]</span> -->
                                                    <span  v-if="!item.disabled" class="text-body-2">{{item.name}} - [{{format_L(item.openDateToUTC, item.disabled)}} {{format_L(item.closeDateToUTC, item.disabled)}}]</span>
                                                    <span v-else class="text-body-2">{{item.name}} - [{{$t('bs-closed')}}]</span>
                                                </template>
                                            </v-select>
                                        </div>
                                    </v-list-item-subtitle>
                                    <template v-else>
                                        <v-list-item-subtitle v-if="content.type == 'text'" class="text-body-2 text-left text-wrap"  style="word-break: break-word !important;">
                                            {{ content.text }}
                                            <div class="btn-group-options" v-if="isDad">
                                                <template v-for="(item, idx) in content.children">
                                                    <v-sheet
                                                        :key="idx"
                                                        v-if="item.type !== 'text'"
                                                        class="pa-2 font-weight-medium text-center rounded-lg"
                                                        :class="isLatest ? 'enabled' : 'disabled'"
                                                        color="primary"
                                                        elevation="1"
                                                        @click="btnActions(item, content.inputTime, idx)"
                                                    >
                                                        {{ item.text }}
                                                    </v-sheet>
                                                </template>
                                            </div>
                                        </v-list-item-subtitle>
                                        <v-list-item-subtitle v-else class="text-body-2 text-left text-wrap"  style="word-break: break-word !important;">
                                            <div class="btn-group-options">
                                                <v-sheet
                                                    class="pa-2 font-weight-medium text-center rounded-lg"
                                                    :class="isLatest ? 'enabled' : 'disabled'"
                                                    color="primary"
                                                    elevation="1"
                                                    @click="btnActions(content, content.inputTime, index, false)"
                                                >
                                                    {{ content.text }}
                                                </v-sheet>
                                            </div>
                                        </v-list-item-subtitle>
                                    </template>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </div>
                </v-col>
            </v-row>
        </v-col>
    </v-row>
</template>

<script>
export default {
    data(){
		return {
			tz: "",
		}
	},
    props: {
        message: {
            type: Object,
            default: {}
        },
        index: '',
    },
    computed: {
        isDeptRobot() {
            var isJson = false;
            try {
                var content = JSON.parse(this.message.content);
                isJson = true;
            } catch (e) {
                // console.error(e);
            }
            if (isJson && 'text' in content) {
                return true;
            } else {
                return false;
            }
        },
        content() {
            if (this.isDeptRobot) {
                return JSON.parse(this.message.content)
            }
        },
        departments() {
            return this.$store.state.departments;
        },
        messages() {
            return this.$root.$refs.ClientChatOpened.messages;
        },
        chat() {
            return this.$store.state.chat;
        },
        isDad() {
            if (this.isDeptRobot) {
                return 'children' in this.content && this.content.children.length > 0;
            }
        },
        isLatest() {
            return !(this.messages[this.index + 1]);
        }
    },
    mounted () {
        if (this.isDad && this.chat.status == 'ROBOT') {
            this.content.children.forEach(item => {
                if (item.type == 'text') {
                    let i = this.messages.findIndex(el => {
                        if (typeof el.content === 'object') {
                        return false;
                        } else {
                        try {
                            const contentObj = JSON.parse(el.content);
                            return contentObj.id == item.id;
                        } catch (e) {
                            return false;
                        }
                        }
                    });

                    if (i == -1) {
                        this.$root.$refs.ClientChatOpened.executeRobotAction(this.content, this.content.inputTime)
                    }
                }
            });
        }
    },
    created() {
        this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
    },
    methods: {
        format_L(h, type) {
            const dataIso = new Date(h);
            var converted_time2 = dataIso.toUTCString();

            var date = new Date(converted_time2);

            var seconds = date.getSeconds();
            var minutes = date.getMinutes();
            var hour = date.getHours();
            if(hour == 0){
                hour = '00';
            }

            if(minutes == 0){
                minutes = '00';
            }

            return hour+':'+minutes;
        },
        toUTC(h) {
            let h_format = moment(h, "YYYY-MM-DD HH:mm:ss").format(
                "YYYY-MM-DD HH:mm:ss"
            );
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
            let dateUTC = new Date(
                Date.UTC(year, month_integer, day, hour, minute, second)
            );
            let converted_time = dateUTC.toLocaleString("pt-BR", {
                timeZone: this.tz
            });

            return converted_time;
        },
        updateMessageSelected(i) {
            var vm = this;
            var content = JSON.parse(vm.messages[vm.index].content);
            content.children[i].selected = true;
            axios.post(`${vm.$store.state.baseURL}/chat-history/client/update`,{
                ch_id: vm.messages[vm.index]['ch_id'],
                content
            })
            .then(res => {})
        },
        btnActions(item, inputTime, idx, update = true) {
            if (this.isLatest) {
                if (update) {
                    this.updateMessageSelected(idx);
                }
                this.$root.$refs.ClientChatOpened.executeRobotAction(item, inputTime)
            }
        }
    },
}
</script>

<style scoped>
    .theme--dark .v-list {
        background: #292935;
    }

    .btn-group-options {
        padding: 10px 2px 2px 2px;
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        row-gap: 10px;
        column-gap: 10px;
    }

    .btn-group-options .v-sheet {
        flex-grow: 1;
        color: white;
    }

    .btn-group-options .enabled {
        cursor: pointer;
    }

    .btn-group-options .disabled {
        cursor: not-allowed;
    }
</style>
