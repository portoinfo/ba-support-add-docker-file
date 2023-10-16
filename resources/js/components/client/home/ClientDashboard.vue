<template>
    <v-row no-gutters class="d-flex flex-column flex-nowrap" :style="{height: $store.state.innerHeight}">
        <v-col class="grow overflow-auto  py-0">
            <v-container class="pt-xl-8 pt-lg-8 pt-md-8 pt-sm-8 pt-3 h-100" id="dashboard">
                <v-row no-gutters class="mb-xl-6 mb-lg-6 mb-md-6 mb-sm-6 mb-3">
                    <v-list-item two-line class="pb-0">
                        <v-list-item-content>
                            <v-list-item-title
                                class="text-h5 font-weight-bold"
                                v-text="$t('bs-home')"
                            >
                            </v-list-item-title>
                            <v-list-item-subtitle
                                class="text-subtitle-2 text-wrap"
                                v-text="`${$t('bs-welcome')}, ${$ct($store.state.user.name)}, ${$t('bs-choose-the-call-you-want-to-open')}.`"
                            >
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                </v-row>
                <v-row no-gutters justify="center">
                    <v-col xl="9" lg="11" md="12" cols="12">
                        <template v-if="$store.state.isMedium">
                            <v-row no-gutters justify="center">
                                <v-col v-if="showTicketModule">
                                    <v-container fluid class="h-100 py-2">
                                        <ticket-card></ticket-card>
                                    </v-container>
                                </v-col>
                                <v-col v-if="showChatModule">
                                    <v-container fluid class="h-100 py-2">
                                        <chat-card></chat-card>
                                    </v-container>
                                </v-col>
                            </v-row>
                            <v-row no-gutters justify="center" class="pb-2">
                                <v-col v-if="showTicketModule">
                                    <v-container fluid class="h-100 py-2">
                                        <ticket-info-card :tickets="tickets"></ticket-info-card>
                                    </v-container>
                                </v-col>
                                <v-col v-if="showChatModule">
                                    <v-container fluid class="h-100 py-2">
                                        <chat-info-card :chats="chats"></chat-info-card>
                                    </v-container>
                                </v-col>
                            </v-row>
                        </template>
                        <template v-else>
                            <v-row no-gutters justify="center" v-if="showTicketModule">
                                <v-col xl="3" lg="3" md="12" sm="12" xs="12">
                                    <v-container fluid class="h-100 py-2">
                                        <ticket-card></ticket-card>
                                    </v-container>
                                </v-col>
                                <v-col>
                                    <v-container fluid class="h-100 py-2">
                                        <ticket-info-card :tickets="tickets"></ticket-info-card>
                                    </v-container>
                                </v-col>
                            </v-row>
                            <v-row no-gutters justify="center" class="pb-2" v-if="showChatModule">
                                <v-col xl="3" lg="3" md="12" sm="12" xs="12">
                                    <v-container fluid class="h-100 py-2">
                                        <chat-card></chat-card>
                                    </v-container>
                                </v-col>
                                <v-col>
                                    <v-container fluid class="h-100 py-2">
                                        <chat-info-card :chats="chats"></chat-info-card>
                                    </v-container>
                                </v-col>
                            </v-row>
                        </template>
                    </v-col>
                </v-row>
            </v-container>
        </v-col>
    </v-row>
</template>

<script>
export default {
    data() {
        return {
            chats: {
                in_progress: null,
                finished: null,
                in_queue: null,
            },
            tickets: {
                in_progress: null,
                finished: null,
                in_queue: null,
                latest: null,
            },
        }
    },
    mounted () {
        this.getChatsTicketsData();
        this.getLast2Tickets();
    },
    computed: {
        showChatModule() {
            return this.$store.state.showChatModule
        },
        showTicketModule() {
            return this.$store.state.showTicketModule
        },
    },
    methods: {
        getChatsTicketsData() {
            var vm = this;
            var url = 'client/get-chat-ticket-data';
            axios.get(url)
            .then(({data}) => {
                if (data.success) {

                    vm.chats.in_progress   = data.chats.IN_PROGRESS;
                    vm.chats.finished      = data.chats.RESOLVED + data.chats.CLOSED;
                    vm.chats.in_queue      = data.chats.OPENED;

                    vm.tickets.in_progress = data.tickets.IN_PROGRESS;
                    vm.tickets.finished    = data.tickets.RESOLVED + data.tickets.CLOSED;
                    vm.tickets.in_queue    = data.tickets.OPENED;

                }
            })
        },
        getLast2Tickets() {
            var vm = this;
            var url = 'client/get-last-2-tickets'
            axios.get(url)
            .then(({data}) => {
                if (data.success) {
                    vm.tickets.latest = [];
                    if (data.tickets.length) {
                        data.tickets.forEach(element => {
                            var hasImg = element.description ? element.description.search("<img") : -1;
                            hasImg !== -1 ? element.hasImg = true : element.hasImg = false;
                            vm.tickets.latest.push(element);
                        });
                    }
                }
            })
        },
    },
}
</script>
