<template>
    <v-row no-gutters class="h-100 d-flex flex-column flex-nowrap">
        <v-col class="shrink" v-show="!$store.state.isTablet">
            <v-container fluid>
                <v-btn :to="{ name: 'ticket' }" exact class="text-capitalize text-h6" text elevation="0">
                    <v-icon left> $back </v-icon>
                    {{ $t('bs-back') }}
                </v-btn>
            </v-container>
        </v-col>
        <v-col class="grow">
            <v-row no-gutters class="h-100 d-flex flex-column flex-nowrap">
                <v-col cols="12" class="grow">
                    <v-sheet height="100%" class="rounded-lg pb-2" color="containerBackground2">
                        <v-row no-gutters class="h-100 d-flex flex-column flex-nowrap">
                            <v-col class="shrink">
                                <v-list>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <ticket-text-filter></ticket-text-filter>
                                        </v-list-item-content>
                                        <v-list-item-action>
                                            <v-btn 
                                                v-if="!$store.state.loadingTicket"
                                                class="btn-create-fab" 
                                                color="btnLight"
                                                fab 
                                                :to="{ name: 'ticket-opened', params: { id: 'create' } }"
                                            >
                                                <v-icon>$edit</v-icon>
                                            </v-btn>
                                            <v-btn 
                                                v-else
                                                class="btn-create-fab" 
                                                color="btnLight"
                                                fab 
                                            >
                                                <v-icon>$edit</v-icon>
                                            </v-btn>
                                        </v-list-item-action>
                                    </v-list-item>
                                </v-list>
                            </v-col>
                            <v-col class="grow overflow-auto">
                                <v-list two-line class="pa-0" color="input">
                                    <v-list-item-group>
                                        <template v-for="(item, n) in tickets">
                                            <template v-if="!empty">
                                                <v-list-item 
                                                    v-if="item.visible" 
                                                    :key="n" 
                                                    :class="activeTicket == item.hash_id ? 'ticket-selected' : ''"
                                                    @click.prevent="goToTicket(item.hash_id ? item.hash_id : 'create')"
                                                >
                                                    <v-list-item-avatar tile>
                                                        <v-gravatar 
                                                            v-if="item.cucd_id !== null"
                                                            :email="item.attendant_email"
                                                            :name="item.attendant_name"
                                                            :status="$status.get(item.attendant_id)"
                                                            size="40"
                                                        ></v-gravatar>
                                                        <v-gravatar v-else :robot="true"/> 
                                                    </v-list-item-avatar>
                                                    <v-list-item-content>
                                                        <v-list-item-title
                                                            class="font-weight-bold"
                                                            v-text="item.attendant_name !== null ? item.attendant_name : $store.state.name_robot"    
                                                        >                      
                                                        </v-list-item-title>
                                                        <v-list-item-subtitle
                                                            class="text-caption"
                                                            :class="{'font-weight-bold': item.agent_answered && item.status == 'IN_PROGRESS'}"
                                                            v-text="$ct(item.department)"
                                                        ></v-list-item-subtitle>
                                                        <v-list-item-subtitle
                                                            class="text-caption"
                                                            :class="{'font-weight-bold': item.agent_answered && item.status == 'IN_PROGRESS'}"
                                                            v-text="$ct($formatDescription(item.description))"
                                                        ></v-list-item-subtitle>
                                                    </v-list-item-content>
                                                    <v-list-item-action>
                                                        <v-list-item-action-text
                                                            class="text-caption"
                                                            :class="{'font-weight-bold': item.agent_answered && item.status == 'IN_PROGRESS'}"
                                                            v-text="$formatDate(item.id !== 'create' ? item.date : item.date, 'L')"
                                                        >
                                                        </v-list-item-action-text>
                                                        <v-list-item-action-text
                                                            class="font-weight-bold text-body-2"
                                                            v-text="item.id !== 'create' ? `${$t('bs-ticket')} #${item.id}` : '--'"
                                                        >
                                                        </v-list-item-action-text>
                                                        <v-list-item-action-text>
                                                            <v-chip
                                                                :color="$formatStatus(item.status, 'background', 'ticket', item.date)"
                                                                x-small
                                                                :text-color="$formatStatus(item.status, 'color', 'ticket', item.date)"
                                                            >
                                                                {{ $formatStatus(item.status, 'text', 'ticket', item.date) }}
                                                                <v-icon right x-small> {{ $formatStatus(item.status, 'icon', 'ticket', item.date) }} </v-icon>
                                                            </v-chip>
                                                        </v-list-item-action-text>
                                                    </v-list-item-action>
                                                </v-list-item>
                                            </template>
                                            <template v-else>
                                                <v-sheet color="containerBackground2" :key="n">
                                                    <v-row no-gutters class="text-center">
                                                        <v-col cols="12"><span class="text-h6 font-weight-bold">{{ $t('bs-no-history-record') }}</span></v-col>
                                                        <v-col cols="12">
                                                            <v-btn large dark color="btnPrimary" class="text-capitalize mt-3" :to="{ name: 'ticket-opened', params: { id: 'create' } }">
                                                                {{ $t('bs-create-ticket') }}
                                                            </v-btn>
                                                        </v-col>
                                                    </v-row>
                                                </v-sheet>
                                            </template>
                                        </template>
                                    </v-list-item-group>
                                    <infinite-loading  v-if="!$store.state.emptyTickets" @infinite="getClientTickets">
                                        <div slot="spinner"></div>
                                        <div slot="no-more"></div>
                                        <div slot="no-results"></div>
                                    </infinite-loading>
                                    <template v-if="!loaded">
                                        <v-skeleton-loader
                                            type="list-item-avatar-three-line@10"
                                        >
                                        </v-skeleton-loader>
                                    </template>
                                </v-list>
                            </v-col>
                        </v-row>
                    </v-sheet>
                </v-col>
            </v-row>
        </v-col>
    </v-row>
</template>

<script>
import { mapState, mapMutations } from "vuex";
export default {
    computed: {
        tickets: {
            get () {
                return this.$store.state.tickets;
            }
        },
        loaded: {
            get () {
                return !this.$store.state.overlayTickets;
            }
        },
        activeTicket() {
            return this.$route.params.id
        },
        empty() {
            if (this.tickets.length === 1 && !this.tickets[0].visible && this.loaded) {
                return true
            } else {
                return false;
            }
        },
    },
    methods: {
        ...mapMutations(["getClientTickets"]),
        goToTicket(hash_id) {
            if (!this.$store.state.loadingTicket) {
                if (this.$route.params.id !== hash_id) {
                    this.$router.push({ name: 'ticket-opened', params: {'id': hash_id} })
                }
            }
        },
    },
}
</script>

<style scoped>
.v-list {
    background: transparent !important;
}

.v-list-item--active:before {
    opacity: 0 !important;
}
</style>