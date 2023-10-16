<template>
	<v-app-bar
        id="ticket-header"
        :app="app"
        :clipped-right="clippedRight"
        :flat="flat"
        elevation="0"
        color="white"
        height="72"
    >
        <v-list width="100%" color="transparent">
            <v-list-item two-line v-if="$root.$refs.ClientTicketOpened.ticketInfoLoaded">
                <v-list-item-action v-if="$store.state.isTablet" class="mx-0">
                    <v-btn icon
                        :to="{ name: 'ticket' }"
                        exact
                        elevation="0"
                    >
                        <v-icon size="1rem"> $back </v-icon>
                    </v-btn>
                </v-list-item-action>
                <v-list-item-avatar tile size="48" class="mr-2 ml-1">
                    <v-gravatar v-if="$root.$refs.ClientTicketOpened.isRobot" size="48" :robot="true" />
                    <v-gravatar
                        v-else
                        :email="$store.state.ticket.attendant_email"
                        :name="$store.state.ticket.attendant_name"
                        :status="$status.get($store.state.ticket.attendant_id)"
                        size="48"
                    ></v-gravatar>
                </v-list-item-avatar>
                <v-list-item-content class="py-0">
                    <v-list-item-title
                        class="font-weight-bold"
                        v-text="$root.$refs.ClientTicketOpened.isRobot ? $store.state.name_robot : $store.state.ticket.attendant_name"
                    ></v-list-item-title>
                    <div class="header-description">
                        <v-list-item-subtitle v-if="$root.$refs.ClientTicketOpened.isCreate" class="text-body-2" v-text="$store.state.ticket.department_name"></v-list-item-subtitle>
                        <template v-else>
                            <v-list-item-subtitle class="text-body-2 shrink" style="flex: auto" v-text="$ct($formatDescription($store.state.ticket.description))"></v-list-item-subtitle>
                            <a @click="$root.$refs.ClientTicketOpened.showDetails = true" style="font-size: 0.875rem;" class="grow text-decoration-none primary--text font-weight-medium">{{ $t('bs-see-more') }}</a>
                        </template>
                    </div>
                </v-list-item-content>
                <v-list-item-action>
                    <v-menu offset-y left min-width="250px">
                        <template v-slot:activator="{ on, attrs }">
                        <v-badge
                            :value="!evaluated && toEvaluate"
                            color="orange"
                            overlap
                            icon="mdi-star"
                            offset-x="16"
                            offset-y="16"
                        >
                            <v-btn icon v-bind="attrs" v-on="on">
                                <v-icon>mdi-dots-vertical</v-icon>
                            </v-btn>
                        </v-badge>
                        </template>
                        <v-list dense color="white">
                            <v-list-item @click="showDetailsMediumScreen = true">
                                <v-list-item-icon>
                                    <v-icon>mdi-information</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title v-text="$t('bs-details')"></v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <template v-if="showOptions">
                                <v-subheader class="text-uppercase">{{ $t("bs-actions") }}</v-subheader>
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
                            </template>
                        </v-list>
                    </v-menu>
                </v-list-item-action>
            </v-list-item>
            <v-skeleton-loader
                v-else
                class="skeleton-header-chat"
                type="list-item-avatar-two-line"
                width="300px"
                height="100%"
            ></v-skeleton-loader>
        </v-list>
        <rate-ticket-dialog></rate-ticket-dialog>
        <client-ticket-answer-dialog></client-ticket-answer-dialog>
        <v-dialog
            fullscreen
            hide-overlay
            scrollable
            v-model="showDetailsMediumScreen"
        >
            <v-card color="containerBackground">
                <v-app-bar color="containerBackground" tile @click="showDetailsMediumScreen = false">
                    <v-icon class="cursor-pointer">mdi-close</v-icon>
                    <span>{{ $t('bs-details') }}</span>
                    <v-spacer></v-spacer>
                </v-app-bar>
                <v-card-text class="px-0">
                    <client-ticket-opened-details></client-ticket-opened-details>
                </v-card-text>
            </v-card>
        </v-dialog>
	</v-app-bar>
</template>

<script>
import { mapMutations } from "vuex";
export default {
    created () {
        this.$root.$refs.ClientTicketOpenedHeader = this;
    },
    props: {
        app: {
            type: Boolean,
            default: false,
        },
        clippedRight: {
            type: Boolean,
            default: false,
        },
        flat: {
            type: Boolean,
            default: false,
        },
    },
    computed: {
        ticket: {
            get() {
                return this.$store.state.ticket;
            }
        },
        departmentSettings() {
            return this.$root.$refs.ClientTicketOpened.departmentSettings;
        },
        isCreate() {
            return this.$root.$refs.ClientTicketOpened.isCreate;
        },
        evaluated() {
            return this.$root.$refs.ClientTicketOpened.evaluated;
        },
        toEvaluate() {
            if ('evaluation' in this.departmentSettings) {
                return this.departmentSettings.evaluation.text_atten_active || this.departmentSettings.evaluation.text_comment_active || this.departmentSettings.evaluation.text_serv_active;
            }
        },
        options() {
            return [
                {
                    'title': this.$t('bs-cancel-ticket'),
                    'visible': this.isCreate || (this.ticket.status == 'OPENED' || this.ticket.status == 'IN_PROGRESS' ||this.ticket.status == 'ROBOT'),
                    'status': this.isCreate ? 'RESET' : 'CANCELED',
                    'icon': 'mdi-cancel'
                },
                {
                    'title': this.$t('bs-mark-as-resolved'),
                    'visible': this.ticket.status == 'IN_PROGRESS',
                    'status': this.toEvaluate ?  'RATE-AND-RESOLVE' : 'RESOLVED',
                    'icon': 'mdi-check-bold'
                },
                {
                    'title': this.$t('bs-rate-the-ticket'),
                    'visible': !this.evaluated && this.toEvaluate,
                    'status': 'RATE',
                    'icon': 'mdi-star-outline'
                },
            ]
        },
        showOptions() {
            let index = this.options.findIndex(item => item.visible == true);
            if (index !== -1) {
                return true;
            } else {
                return false
            }
        },
        showDetailsMediumScreen: {
            get() {
                return this.$root.$refs.ClientTicketOpened.showDetails && this.$store.state.isMedium
            },
            set(v) {
                this.$root.$refs.ClientTicketOpened.showDetails = v;
            }
        }
    },
}
</script>

<style scoped>
    .v-list,
    .v-list-item__content {
        padding: 0px !important;
    }

    .header-description {
        max-width: 100%;
        display: flex;
        column-gap: 5px;
    }
</style>
