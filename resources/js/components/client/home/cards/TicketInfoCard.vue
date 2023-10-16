<template>
    <v-card class="card-dash" height="100%" color="whiteCard">
        <v-card-title class="justify-center text-h5 font-weight-black">
            {{  $t('bs-ticket')  }}
        </v-card-title>
        <v-card-text v-if="!$store.state.isPopup" class="text-center">
            <v-row justify="center" id="row-chat-info" class="row-ticket-chat-info" v-resize="onResize">
                <v-col xl="3" class="px-0 py-xl-0 py-lg-0">
                    <v-list-item two-line class="px-0">
                        <v-list-item-content>
                            <v-list-item-title>
                                <h1 v-if="tickets.in_progress != null" class="text-h4 font-weight-bold">{{ tickets.in_progress }}</h1>
                                <v-progress-circular v-else size="38" indeterminate color="#606060"></v-progress-circular>
                            </v-list-item-title>
                            <v-list-item-subtitle>
                                <h3 class="text-subtitle-1">{{ $t('bs-in-progress') }}</h3>
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                </v-col>
                <v-col xl="3" class="px-0 py-xl-0 py-lg-0">
                    
                    <v-list-item two-line class="px-0">
                        <v-divider vertical v-show="rowChatDivider.left" />
                        <v-list-item-content>
                            <v-list-item-title>
                                <h1 v-if="tickets.finished != null" class="text-h4 font-weight-bold">{{ tickets.finished }}</h1>
                                <v-progress-circular v-else size="38" indeterminate color="#606060"></v-progress-circular>
                            </v-list-item-title>
                            <v-list-item-subtitle>
                                <h3 class="text-subtitle-1">{{ $t('bs-finished-s') }}</h3>
                            </v-list-item-subtitle>
                        </v-list-item-content>
                            <v-divider vertical v-show="rowChatDivider.right" />
                    </v-list-item>
                    
                </v-col>
                <v-col xl="3" class="px-0 py-xl-0 py-lg-0">
                    <v-list-item two-line class="px-0">
                        <v-list-item-content>
                            <v-list-item-title>
                                <h1 v-if="tickets.in_queue != null" class="text-h4 font-weight-bold">{{ tickets.in_queue }}</h1>
                                <v-progress-circular v-else size="38" indeterminate color="#606060"></v-progress-circular>
                            </v-list-item-title>
                            <v-list-item-subtitle>
                                <h3 class="text-subtitle-1">{{ $t('bs-in-queue') }}</h3>
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                </v-col>
            </v-row>
            <v-row class="px-5" justify="center" v-show="!$store.state.isMedium">
                <template v-if="tickets.latest != null">
                    <template v-if="tickets.latest.length">
                        <v-col 
                            v-for="item in tickets.latest" 
                            :key="item.id" 
                            xl="6" 
                            lg="6" 
                            md="12" 
                            sm="12" 
                            xs="12"
                        >
                            <v-container class="last-chat-ticket-container">
                                <v-list-item class="px-0">
                                    <v-card color="whiteCard" class="card-calendar" min-width="70">
                                        <v-list-item two-line class="py-0 px-0">
                                            <v-list-item-content class="py-0 px-0">
                                                <v-list-item-title class="text-h5 font-weight-bold">
                                                    {{ $formatDate(item.date, 'D') }}
                                                </v-list-item-title>
                                                <v-list-item-subtitle class="text-capitalize text-subtitle2 font-weight-regular">
                                                    {{ $formatDate(item.date, 'MMM') }}
                                                </v-list-item-subtitle>
                                            </v-list-item-content>
                                        </v-list-item>
                                    </v-card>

                                    <v-list-item-content class="text-left pl-2">
                                        <v-list-item-title class="text-subtitle-1 font-weight-medium" style="width: 1px;"> 
                                            <template v-if="item.description !== ''">
                                                {{ $ct($formatDescription(item.description)) }}
                                            </template>
                                            <template v-else-if="item.hasImg">
                                                <v-icon>mdi-image</v-icon>
                                                {{ $t('bs-image') }}
                                            </template>
                                        </v-list-item-title>
                                        <v-list-item-subtitle class="text-body-2">
                                            {{ $ct(item.department) }}
                                        </v-list-item-subtitle>
                                    </v-list-item-content>

                                    <v-list-item-action>
                                        <v-btn color="iconPrimary" icon large :to="{ name: 'ticket-opened', params: { id: item.hash_id } }">
                                            <v-icon x-large>mdi-chevron-right</v-icon>
                                        </v-btn>
                                    </v-list-item-action>
                                </v-list-item>
                            </v-container>
                        </v-col>
                    </template>
                    <template v-else>
                        <v-col cols="12" class="my-6">
                            <span class="text-h5 font-weight-bold">{{ $t('bs-no-history-record') }}</span>
                        </v-col>
                    </template>
                </template>
                <template v-else>
                    <v-col cols="6" v-for="n in 2" :key="n">
                        <v-container class="last-chat-ticket-container py-5" style="height: 90px;">
                            <v-progress-circular size="45" indeterminate color="rgba(0,0,0,0.1)"></v-progress-circular>
                        </v-container>
                    </v-col>
                </template>
            </v-row>
        </v-card-text>
        <v-card-actions class="justify-center">
            <v-btn v-if="tickets.latest !== null && tickets.latest.length" dark  elevation="0" large color="btnPrimary" class="text-capitalize" :to="{ name: 'ticket' }">
                {{  $t('bs-access-my-tickets') }}
                <v-icon right>
                    mdi-arrow-right
                </v-icon>
            </v-btn>
            <v-btn v-else dark  elevation="0" large color="btnPrimary" class="text-capitalize" :to="{ name: 'ticket-opened', params: { id: 'create' } }">
                {{  $t('bs-create-ticket') }}
                <v-icon right>
                    mdi-arrow-right
                </v-icon>
            </v-btn>
        </v-card-actions>  
    </v-card>
</template>

<script>
    export default {
        data() {
            return {
                rowChatSize: {
                    x: 0,
                    y: 0,
                },
                rowChatDivider: {
                    left: true,
                    right: true
                }
            }
        },
        props: {
            tickets: {
                type: Object,
                default: {
                    in_progress: null,
                    finished: null,
                    in_queue: null,
                    latest: null
                }
            },
        },
        mounted () {
            this.onResize()
        },
        methods: {
            onResize () {
                this.rowChatSize = { x: $('#row-chat-info').innerWidth(), y: $('#row-chat-info').innerHeight() }
                if (this.rowChatSize.x < 555 && this.rowChatSize.x > 369) {
                    this.rowChatDivider.left = true;
                    this.rowChatDivider.right = false;
                } else if (this.rowChatSize.x < 369) {
                    this.rowChatDivider.left = false;
                    this.rowChatDivider.right = false;
                } else {
                    this.rowChatDivider.left = true;
                    this.rowChatDivider.right = true;
                }
            },
        },
    }
</script>