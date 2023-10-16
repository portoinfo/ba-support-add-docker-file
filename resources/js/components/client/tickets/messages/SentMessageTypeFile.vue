<template>
    <v-row no-gutters justify="end">
        <v-col cols="12" class="my-3"><v-divider></v-divider></v-col>
        <v-col cols="12" class="text-left">
            <v-row no-gutters>
                <v-col class="w-fc mr-5">
                    <v-gravatar 
                        v-show="!$store.state.isMobile"
                        size="40" 
                        :name="$ct(message.user_name)"
                        :email="message.user_email"
                        :status="$status.get(message.user_id)"
                    ></v-gravatar>
                </v-col>
                <v-col style="width: 1px" class="mt-1">
                    <div class="bubble-right ticket-msg">
                        <v-list two-line class="py-0" rounded color="transparent">
                            <v-list-item class="px-1">
                                <v-list-item-content>
                                    <v-list-item-title class="theme--dark">
                                        <v-row no-gutte rs>
                                            <v-col class="text-truncate text-left text-subtitle-2">
                                                {{ $ct(message.user_name) }}
                                            </v-col>
                                            <v-col class="text-right w-fc text-body-2">
                                                <small>{{ $formatDate(message.created_at, 'lll') }}</small>
                                            </v-col>
                                        </v-row>
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        <div class="output ql-snow">
                                            <div class="ql-editor px-0 py-3" v-viewer v-html="text"></div>
                                        </div>
                                    </v-list-item-subtitle>
                                    <v-list-item-subtitle>
                                        <div class="btn-group-options">
                                            <v-btn 
                                                v-for="item in files" 
                                                :key="item.id" 
                                                class="text-left text-capitalize px-0" 
                                                height="60" 
                                                color="input" 
                                                target="_blank"
                                                :href="`${$store.state.baseURL}/chat/files/${$store.state.ticket.chat_hash_id}/${item.unique_name}`"
                                            >
                                                <v-list width="100%" color="transparent">
                                                    <v-list-item two-line style="cursor:pointer">
                                                        <v-list-item-avatar>
                                                            {{ getExtension(item) }}
                                                        </v-list-item-avatar>
                                                        <div style="max-width: 180px;">
                                                            <v-list-item-content>
                                                                <v-list-item-title class="text-body-2" v-text="$t('bs-attachment')"></v-list-item-title>
                                                                <v-list-item-subtitle class="text-caption font-italic text-lowercase" v-text="item.original_name"></v-list-item-subtitle>
                                                            </v-list-item-content>
                                                        </div>
                                                    </v-list-item>
                                                </v-list>
                                            </v-btn>
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
    computed: {
        content() {
            if ('content' in this.message) {
                return JSON.parse(this.message.content);
            };
        },
        text() {
            if ('message' in this.content) {
                var message = this.content.message;
                if (message && message.search('src="chat/files/')) {
                    message = message.replaceAll('src="chat/files/', `src="${this.$store.state.baseURL}/chat/files/`);
                }
                return message;
            }
        },
        files() {
            if ('files' in this.content) {
                return this.content.files
            }
        },
    },
    methods: {
        getExtension(item) {
            var splitted = item.original_name.split('.');
            return splitted[splitted.length - 1].toUpperCase();
        }
    },
}
</script>

<style scoped>
.btn-group-options {
    padding: 10px 2px 2px 2px; 
    display: flex;
    flex-direction: row;
    justify-content: left;
    align-items: left;
    flex-wrap: wrap;
    row-gap: 10px;
    column-gap: 10px;
}

.btn-group-options .v-btn {
    color: white;
    max-width: 250px;
}
</style>