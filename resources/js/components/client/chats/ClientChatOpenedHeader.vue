<template>
	<v-app-bar
        id="chat-header"
        :app="app"
        :clipped-right="clippedRight"
        :flat="flat"
        elevation="0"
        color="white"
        height="72"
    >
        <v-list width="100%" color="transparent">
            <v-list-item two-line v-if="$root.$refs.ClientChatOpened.chatInfoLoaded">
                <v-list-item-action v-if="$store.state.isTablet" class="mx-0">
                    <v-btn icon
                        :to="{ name: 'chat' }"
                        exact
                        elevation="0"
                    >
                        <v-icon size="1rem"> $back </v-icon>
                    </v-btn>
                </v-list-item-action>
                <v-list-item-avatar tile size="48" class="mr-2 ml-1">
                    <v-gravatar v-if="$root.$refs.ClientChatOpened.isRobot" size="48" :robot="true" />
                    <v-gravatar
                        v-else
                        :email="$store.state.chat.attendant_email"
                        :name="$store.state.chat.attendant_name"
                        :status="$status.get($store.state.chat.attendant_id)"
                        size="48"
                    ></v-gravatar>
                </v-list-item-avatar>
                <v-list-item-content class="py-0">
                    <v-list-item-title
                        class="font-weight-bold"
                        v-text="$root.$refs.ClientChatOpened.isRobot ? $store.state.name_robot : $store.state.chat.attendant_name"
                    >
                    </v-list-item-title>
                    <v-list-item-subtitle class="text-body-2" v-text="$store.state.chat.department_name">
                    </v-list-item-subtitle>
                </v-list-item-content>
                <v-list-item-action v-if="showOptions" class="mx-0">
                    <v-menu offset-y left min-width="250px">
                        <template v-slot:activator="{ on, attrs }">
                        <v-badge
                            :value="!evaluated && toEvaluate"
                            color="orange"
                            overlap
                            :bottom="$store.state.isPopup"
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
                            <v-subheader class="text-uppercase">{{
                                $t("bs-actions")
                            }}</v-subheader>
                            <v-list-item-group color="primary">
                                <template v-for="item in options">
                                    <v-list-item :key="item.status" v-if="item.visible" @click="executeAction(item.status)">
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
        <rate-chat-dialog></rate-chat-dialog>
	</v-app-bar>
</template>

<script>
import { mapMutations } from "vuex";
export default {
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
    created () {
        this.$root.$refs.ClientChatOpenedHeader = this;
    },
    computed: {
        chat: {
            get() {
                return this.$store.state.chat;
            }
        },
        departmentSettings() {
            return this.$root.$refs.ClientChatOpened.departmentSettings;
        },
        isCreate() {
            return this.$root.$refs.ClientChatOpened.isCreate;
        },
        evaluated() {
            return this.$root.$refs.ClientChatOpened.evaluated;
        },
        toEvaluate() {
            if ('evaluation' in this.departmentSettings) {
                return this.departmentSettings.evaluation.text_atten_active || this.departmentSettings.evaluation.text_comment_active || this.departmentSettings.evaluation.text_serv_active;
            }
        },
        options() {
            return [
                {
                    'title': this.$t('bs-cancel-chat'),
                    'visible': this.isCreate && !this.$store.state.isPopup || (this.chat.status == 'OPENED' || this.chat.status == 'ROBOT'),
                    'status': this.isCreate ? 'RESET' : 'CANCELED',
                    'icon': 'mdi-cancel'
                },
                {
                    'title': this.toEvaluate ? this.$t('bs-rate-chat-and-end') : this.$t('bs-close-chat'),
                    'visible': this.chat.status == 'IN_PROGRESS',
                    'status': this.toEvaluate ?  'RATE-AND-RESOLVE' : 'RESOLVED',
                    'icon': 'mdi-chat-remove'
                },
                {
                    'title': this.$t('bs-rate-the-chat'),
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
    },
    methods: {
        ...mapMutations(["resetNewChatData"]),
        executeAction(status) {
            switch (status) {
                case 'RESET':
                    this.resetNewChatData();
                    break;

                case 'CANCELED':
                    this.$root.$refs.ClientChatOpened.cancelChat();
                    break;

                case 'RATE-AND-RESOLVE':
                case 'RATE':
                    this.$root.$refs.ClientChatOpened.openRateDialog(status);
                    break;

                case 'RESOLVED':
                    this.$root.$refs.ClientChatOpened.resolveChat();
                    break;
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
</style>
