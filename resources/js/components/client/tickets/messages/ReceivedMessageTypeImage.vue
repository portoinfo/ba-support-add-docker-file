<template>
    <v-row no-gutters justify="start">
        <v-col cols="12" class="my-3"><v-divider></v-divider></v-col>
        <v-col cols="12" class="text-right">
            <v-row no-gutters class="py-1">
                <v-col class="w-fc mr-5">
                    <v-gravatar 
                        v-show="!$store.state.isMobile"
                        :size="$store.state.isMobile ? '30' : '40'" 
                        :name="message.user_name"
                        :email="message.user_email"
                        :status="$status.get(message.user_id)"
                    ></v-gravatar>
                </v-col>
                <v-col style="width: 1px" class="mt-1">
                    <div class="bubble-left ticket-msg">
                        <v-list two-line class="py-0" rounded color="transparent">
                            <v-list-item class="px-1">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <v-row no-gutters>
                                            <v-col class="text-truncate text-left text-subtitle-2">
                                                {{ message.user_name }}
                                            </v-col>
                                            <v-col class="text-right w-fc text-caption">
                                                <small>{{ $formatDate(message.created_at) }}</small>
                                            </v-col>
                                        </v-row>
                                    </v-list-item-title>
                                    <v-list-item-subtitle class="text-left py-2 px-1">
                                        <v-btn class="text-left text-capitalize px-0" height="60" color="input" target="_blank" :href="`${$store.state.baseURL}/chat/files/${ticket.chat_hash_id}/${unique_name}`">
                                            <v-list width="250" color="transparent">
                                                <v-list-item two-line style="cursor:pointer">
                                                    <v-list-item-avatar>
                                                        {{ extension }}
                                                    </v-list-item-avatar>
                                                    <v-list-item-content>
                                                        <v-list-item-title class="text-body-2" v-text="$t('bs-attachment')"></v-list-item-title>
                                                        <v-list-item-subtitle class="text-caption font-italic text-lowercase" v-text="original_name"></v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                            </v-list>
                                        </v-btn>
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
    },
    computed: {
        original_name() {
            return JSON.parse(this.message.content).original_name;
        },
        unique_name() {
            return JSON.parse(this.message.content).unique_name;
        },
        extension() {
            var splitted = this.original_name.split('.');
            return splitted[splitted.length - 1].toUpperCase();
        },
        ticket: {
            get() {
                return this.$store.state.ticket;
            }
        },
    },
}
</script>

<style>

</style>