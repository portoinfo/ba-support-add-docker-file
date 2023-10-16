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
                    <div class="bubble-left">
                        <v-list two-line class="py-0" rounded color="transparent">
                            <v-list-item class="px-1">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <v-row no-gutters>
                                            <v-col class="text-truncate text-left text-subtitle-2">
                                                {{ $store.state.name_robot }}
                                            </v-col>
                                            <v-col class="text-right w-fc text-body-2">
                                                <small>{{ $formatDate(message.created_at, 'lll') }}</small>
                                            </v-col>
                                        </v-row>
                                    </v-list-item-title>
                                    <v-list-item-subtitle class="text-body-2 text-left text-wrap"  style="word-break: break-word !important;">
                                        {{$ct(message.content)}}
                                        <div class="pa-2" v-if="message.showDeptSelect">
                                            <v-select
                                                :items="departments"
                                                :label="$t('bs-select-a-department')"
                                                dense
                                                solo
                                                v-model="$store.state.newTicket.department"
                                                :readonly="$store.state.newTicket.department !== null"
                                                item-disabled="disabled"
                                                background-color="input"
                                                class="rounded-lg"
                                                hide-details
                                            >
                                                <template v-slot:selection="{ item }">
                                                    <span class="text-body-2">{{ item.name }}</span>
                                                </template>
                                                <template v-slot:item="{ item }">
                                                    <span class="text-body-2">{{ item.name }}</span>
                                                </template>
                                            </v-select>
                                        </div>
                                    </v-list-item-subtitle>
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
    props: {
        message: {
            type: Object,
            default: {}
        },
        index: ''
    },
    created(){
    },
    computed: {
        departments() {
            return this.$store.state.departments; 
        },
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