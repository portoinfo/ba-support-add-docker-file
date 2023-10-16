<template>
	<v-footer app color="page_backgroud" class="pt-0 px-3">
        <v-tabs
            background-color="white"
            hide-slider
            class="rounded-lg"
            height="56"
        >
            <v-row no-gutters class="px-1">
                <template v-for="(item, i) in items">
                    <v-col :key="i" v-if="item.visible" class="d-flex align-center" style="width: 1px;">
                        <v-btn
                            :active-class="routeName == 'home' ? 'dash-active' : $vuetify.theme.dark ? 'dark-active' : 'active'"
                            color="transparent"
                            depressed
                            :to="{ name: item.to }"
                            height="46"
                            width="100%"
                            class="pa-0 ma-0"
                        >
                            <v-icon>${{ item.icon }}</v-icon>
                        </v-btn>
                    </v-col>
                </template>
            </v-row>
        </v-tabs>
	</v-footer>
</template>

<script>
export default {
    computed: {
        routeName() {
			return this.$route.name;
		},
        items() {
			var array = [
				{
					text: "Dashboard",
					icon: this.routeName == "home" ? "dashboard_active" : "dashboard_inactive",
					to: "home",
                    visible: !this.$store.state.user.is_anonymous,
				},
				{
					text: this.$t('bs-chat'),
					icon: this.routeName == "chat" || this.routeName == "chat-opened" ? "chat_active" : "chat_inactive",
					to: "chat",
                    visible: this.$store.state.showChatModule,
				},
				{
					text: this.$t('bs-ticket'),
					icon: this.routeName == "ticket" || this.routeName == "ticket-opened" ? "ticket_active" : "ticket_inactive",
					to: "ticket",
                    visible: this.$store.state.showTicketModule,
				},
				{
					text: this.$t('bs-exit'),
					icon: "logout_1",
					to: "logout",
                    visible: true
				},
			];
			return array;
		},
    },
};
</script>

<style scoped>
.dash-active {
    background: #3B82F6 !important;
}
.active {
    background: #F1F5F9 !important;
}

.dark-active {
    background: #464648 !important;
}
</style>
