<template>
    <v-row no-gutters justify="start">
        <v-col cols="12" class="my-3" v-show="!$root.$refs.ClientTicketOpened.isCreate"><v-divider></v-divider></v-col>
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
                                            <v-col class="text-right w-fc text-body-2">
                                                <small>{{ $formatDate(message.created_at, 'lll') }}</small>
                                            </v-col>
                                        </v-row>
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        <div class="output ql-snow">
                                            <div class="ql-editor px-0 py-3" v-viewer v-html="message.content"></div>
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
    },
    created () {
        if (this.message.content && this.message.content.search('src="chat/files/')) {
            this.message.content = this.message.content.replaceAll('src="chat/files/', `src="${this.$store.state.baseURL}/chat/files/`);
        }
    },
}
</script>