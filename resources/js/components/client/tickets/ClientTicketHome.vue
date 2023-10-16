<template>
    <v-container :fluid="$store.state.isMedium" class="d-flex flex-column flex-nowrap client-ticket-home" :style="{height: $store.state.innerHeight}">
        <v-row no-gutters class="shrink">
            <v-list-item two-line>
                <v-list-item-content>
                    <v-list-item-title
                        class="text-h5 font-weight-bold"
                        v-text="$t('bs-ticket')"
                    >
                    </v-list-item-title>
                    <v-list-item-subtitle 
                        v-show="!$store.state.isMobile"
                        class="text-subtitle-2 text-wrap d-none d-md-flex d-lg-flex d-xl-flex"
                        v-text="$t('bs-inform-your-problem-doubt')"
                    >
                    </v-list-item-subtitle>
                    <v-list-item-action-text class="mt-2">
                        <v-btn
                            rounded-sm
                            elevation="0"
                            class="text-capitalize"
                            color="input"
                            :to="{ name: 'ticket-opened', params: { id: 'create' } }"
                        >
                            {{ $t('bs-create-new-ticket') }}
                            <v-icon right> $edit </v-icon>
                        </v-btn>
                    </v-list-item-action-text>
                </v-list-item-content>
                <v-list-item-action  style="margin-top:-36px">
                    <v-list-item-action-text>
                        <ticket-btn-filter></ticket-btn-filter>
                    </v-list-item-action-text>
                    <!-- <v-list-item-action-text v-if="!$store.state.isMobile">
                    </v-list-item-action-text> -->
                </v-list-item-action>
                <v-list-item-action  style="margin-top:-36px" v-if="!$store.state.isMobile">
                    <v-list-item-action-text class="mt-2">
                        <ticket-text-filter></ticket-text-filter>
                    </v-list-item-action-text>
                    <!-- <v-list-item-action-text>
                        <v-btn
                            rounded-sm
                            elevation="0"
                            class="d-none d-md-flex d-lg-flex d-xl-flex text-capitalize"
                            color="input"
                            :to="{ name: 'ticket-opened', params: { id: 'create' } }"
                        >
                            {{ $t('bs-create-new-ticket') }}
                            <v-icon right> $edit </v-icon>
                        </v-btn>
                    </v-list-item-action-text> -->
                </v-list-item-action>
            </v-list-item>
        </v-row>
        <v-row class="grow overflow-hidden" justify="center" style="position: relative;" :no-gutters="$store.state.isTablet">
            <v-col style="position: absolute;" class="h-100" xl="11">
                <v-sheet class="card-table py-5" height="100%" width="100%" color="white" v-if="!$store.state.isTablet">
                    <v-simple-table fixed-header height="100%" :class="{'no-data': empty }" style="background-color: transparent">
                        <template v-slot:default>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="text-center">#</th>
                                    <th class="text-center">{{ $t('bs-attendant') }}</th>
                                    <th class="text-center">{{ $t('bs-description') }}</th>
                                    <th class="text-center">{{ $t('bs-status') }}</th>
                                    <th class="text-center">{{ $t('bs-department') }}</th>
                                    <th class="text-center">{{ $t('bs-date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, i) in tickets" :key="i" :class="{'h-100': empty}">
                                    <template v-if="!empty">
                                        <template v-if="item.visible">
                                            <td class="py-2">
                                                <template v-if="item.agent_answered && item.status == 'IN_PROGRESS'">
                                                    <v-badge avatar bordered overlap>
                                                        <template v-slot:badge>
                                                            <v-avatar color="#fa4b57">
                                                                <v-icon>$bell</v-icon>
                                                            </v-avatar>
                                                        </template>
                                                         <v-btn dark color="#0080FC" class="text-capitalize" 
                                                            :to="{ name: 'ticket-opened', params: { id: item.hash_id ? item.hash_id : 'create' } }"
                                                        >
                                                            <span>{{ $t('bs-view') }}</span>
                                                            <v-icon right> mdi-eye </v-icon>
                                                        </v-btn>
                                                    </v-badge>
                                                </template>
                                                <template v-else>
                                                    <v-btn dark color="#0080FC" class="text-capitalize" 
                                                        :to="{ name: 'ticket-opened', params: { id: item.hash_id ? item.hash_id : 'create' } }"
                                                    >
                                                        <span>{{ $t('bs-view') }}</span>
                                                        <v-icon right> mdi-eye </v-icon>
                                                    </v-btn>
                                                </template>
                                            </td>
                                            <td class="font-weight-medium">{{ item.id == 'create' ? '--' : `#${item.id}` }}</td>
                                            <td class="font-weight-medium">{{ item.attendant_name !== null ? item.attendant_name : $store.state.name_robot }}</td>
                                            <td class="font-weight-medium" style="width: 250px;">
                                                <v-expansion-panels accordion flat tile>
                                                    <v-expansion-panel>
                                                        <v-expansion-panel-header>
                                                            <div style="width: 250px; height: 1rem;" class="text-truncate text-left pa-0 ma-0">
                                                                {{ $ct($formatDescription(item.description)) }}
                                                            </div>
                                                        </v-expansion-panel-header>
                                                        <v-expansion-panel-content class="text-wrap text-left text-caption break-word">
                                                            <div class="output ql-snow mt-2">
                                                                <div class="ql-editor pa-0" style="max-height: 100% !important;" v-html="$ct(description(item.description))"></div>
                                                            </div>
                                                        </v-expansion-panel-content>
                                                    </v-expansion-panel>
                                                </v-expansion-panels>
                                            </td>
                                            <td class="font-weight-medium">
                                                <v-chip
                                                    :color="$formatStatus(item.status, 'background', 'ticket', item.date)"
                                                    small
                                                    :text-color="$formatStatus(item.status, 'color', 'ticket', item.date)"
                                                >
                                                   {{ $formatStatus(item.status, 'text', 'ticket', item.date) }}
                                                    <v-icon right small> {{ $formatStatus(item.status, 'icon', 'ticket', item.date) }} </v-icon>
                                                </v-chip>
                                            </td>
                                            <td class="font-weight-medium">{{ item.department }}</td>
                                            <td class="font-weight-medium">{{ $formatDate(item.date, 'LLL')}}</td>
                                        </template>
                                    </template>
                                    <template v-else>
                                        <td colspan="100%">
                                            <v-row>
                                                <v-col><span class="text-h5 font-weight-bold">{{ $t('bs-no-history-record') }}</span></v-col>
                                            </v-row>
                                            <v-row>
                                                <v-col>
                                                    <v-btn large dark color="btnPrimary" class="text-capitalize" :to="{ name: 'ticket-opened', params: { id: 'create' } }">
                                                        {{ $t('bs-create-ticket') }}
                                                    </v-btn>
                                                </v-col>
                                            </v-row>
                                        </td>
                                    </template>
                                </tr>
                                <infinite-loading v-if="!$store.state.emptyTickets" @infinite="getClientTickets">
                                    <div slot="spinner"></div>
                                    <div slot="no-more"></div>
                                    <div slot="no-results"></div>
                                </infinite-loading>
                                <template v-if="!loaded">
                                    <tr v-for="y in 15" :key="-y">
                                        <td><v-skeleton-loader type="list-item"></v-skeleton-loader></td>
                                        <td><v-skeleton-loader type="list-item"></v-skeleton-loader></td>
                                        <td><v-skeleton-loader type="list-item"></v-skeleton-loader></td>
                                        <td><v-skeleton-loader type="list-item"></v-skeleton-loader></td>
                                        <td><v-skeleton-loader type="list-item"></v-skeleton-loader></td>
                                        <td><v-skeleton-loader type="list-item"></v-skeleton-loader></td>
                                        <td><v-skeleton-loader type="list-item"></v-skeleton-loader></td>
                                    </tr>
                                </template>
                            </tbody>
                        </template>
                    </v-simple-table>
                </v-sheet>
                <client-ticket-list v-else></client-ticket-list>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import { mapMutations } from "vuex";
export default {
    computed: {
        tickets: {
            get () {
                return this.$store.state.tickets;
            }
        },
        loaded: {
            get() {
                return !this.$store.state.overlayTickets;
            }
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
        description(text) {
            if (text) {
                if (text.search('src="chat/files/')) {
                    var description = text.replaceAll('src="chat/files/', `src="${this.$store.state.baseURL}/chat/files/`);
                    return description;
                } else {
                    return text;
                }
            }
        }
    },    
};
</script>

<style scoped>
    .v-expansion-panel{
        background-color: transparent !important;
    }
</style>