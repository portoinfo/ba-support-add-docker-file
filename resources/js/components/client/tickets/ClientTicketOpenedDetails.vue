<template>
    <v-card :tile="$store.state.isMedium" color="containerBackground" height="calc(100% - 32px)" elevation="0" class="pb-5" :class="{'card-details-desktop': !$store.state.isMedium}">
        <v-card-title class="justify-center text-center">
            <v-list-item class="justify-center">
                <v-list-item-avatar size="150" class="mx-0">
                    <v-gravatar v-if="$root.$refs.ClientTicketOpened.isRobot" :robot="true" size="150"/>
                    <v-gravatar
                        v-else
                        :email="ticket.attendant_email"
                        :name="ticket.attendant_name"
                        :status="$status.get(ticket.attendant_id)"
                        size="150"
                    ></v-gravatar>
                </v-list-item-avatar>
            </v-list-item>
            <span v-if="$root.$refs.ClientTicketOpened.isRobot">{{ $store.state.name_robot}}</span>
            <span v-else>{{ ticket.attendant_name }}</span>
        </v-card-title>
        <v-card-subtitle class="justify-center text-center">
            {{ ticket.department_name }}
        </v-card-subtitle>
         <v-sheet tile color="white" class="mb-2">
            <v-container>
                <v-row no-gutters>
                    <v-col cols="12">
                        <span class="text-body-2 font-weight-bold">{{ $t('bs-description') }}</span>
                    </v-col>
                    <v-col cols="12">
                        <div class="output ql-snow mt-2">
                            <div class="ql-editor pa-0" style="max-height: 100% !important;" v-html="$ct(description)"></div>
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </v-sheet>
        <v-sheet tile color="white" class="mb-2">
            <v-container>
                <v-row no-gutters>
                    <v-col cols="12">
                        <span class="text-body-2 font-weight-bold">{{ $t('bs-ticket') }}</span>
                    </v-col>
                    <v-col>
                        <v-list-item>
                            <v-list-item-icon><v-icon small>mdi-pound</v-icon></v-list-item-icon>
                            <v-list-item-title class="text-body-2" v-text="ticket.id"></v-list-item-title>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-icon><v-icon small>mdi-calendar</v-icon></v-list-item-icon>
                            <v-list-item-title class="text-body-2" v-text="$formatDate(ticket.created_at, 'LLL')"></v-list-item-title>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-icon><v-icon small>{{ $formatStatus(ticket.status, 'icon') }}</v-icon></v-list-item-icon>
                            <v-list-item-title class="text-body-2" v-text="$formatStatus(ticket.status, 'text')"></v-list-item-title>
                        </v-list-item>
                    </v-col>
                </v-row>
            </v-container>
        </v-sheet>
        <v-sheet tile color="white" v-if="showOptions">
            <v-list dense color="white">
                <v-list-item-group color="primary">
                    <template v-for="item in options">
                        <v-list-item :key="item.status" v-if="item.visible" @click="$root.$refs.ClientTicketOpened.executeAction(item.status)">
                            <v-list-item-icon>
                                <v-icon v-text="item.icon"></v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title v-text="item.title"></v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </template>
                </v-list-item-group>
            </v-list>
        </v-sheet>
    </v-card>
</template>

<script>
export default {
    computed: {
        showDetails: {
            get() {
                return this.$root.$refs.ClientTicketOpened.showDetails;
            },
            set(v) {
                this.$root.$refs.ClientTicketOpened.showDetails = v;
            }
        },
        ticket() {
            return this.$store.state.ticket;
        },
        description() {
            if (this.ticket.description && this.ticket.description.search('src="chat/files/')) {
                var description = this.ticket.description.replaceAll('src="chat/files/', `src="${this.$store.state.baseURL}/chat/files/`);
                return description;
            } else {
                return this.ticket.description;
            }
        },
        options() {
            return this.$root.$refs.ClientTicketOpenedHeader.options;
        },
        showOptions() {
            return this.$root.$refs.ClientTicketOpenedHeader.showOptions;
        }
    },
}
</script>

<style scoped>

</style>
