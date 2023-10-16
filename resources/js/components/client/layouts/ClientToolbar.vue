<template>
    <v-navigation-drawer 
        app 
        clipped 
        :mini-variant="$store.state.drawer" 
        mini-variant-width="76"
        color="transparent"
        floating
        class="pt-3 pl-3 pb-3 pr-0"
        id="sidebar"
        mobile-breakpoint="600"
    >
        <div class="h-100 px-1" :style="{ background: $vuetify.theme.themes[theme].white, 'border-radius': '15px' }">
            <v-list nav dense color="transparent">
                <v-list-item-group mandatory v-model="selectedItem" class="pt-1">
                    <template v-for="(item, i) in items">
                        <v-list-item
                            :key="i"
                            :to="{ name: item.to }"
                            :active-class="routeName == 'home' ? 'dash-route-active' : 'route-active'"
                            v-if="item.visible"
                        >
                            <v-list-item-icon style="padding: 3px">
                                <v-icon size="1.125rem">${{ item.icon }}</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title class="text-subtitle-2"
                                    v-text="item.text"
                                ></v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </template>
                </v-list-item-group>
            </v-list>
        </div>
	</v-navigation-drawer>
</template>

<script>
export default {
	data: () => ({
		selectedItem: 0,
	}),
	computed: {
        theme() {
			return this.$vuetify.theme.dark ? "dark" : "light";
		},
		items() {
			var array = [
				{
					text: "Dashboard",
					icon: this.routeName == "home" ? "dashboard_active" : "dashboard_inactive",
					to: "home",
                    visible: !this.$store.state.user.is_anonymous
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
		routeName() {
			return this.$route.name;
		},
	},
    watch: {
        '$route': {
            handler: function(n, o){
                if(!o && n.name == 'home') {
                    this.$store.state.drawer = false;
                } else if (o && o.name !== 'home' && !this.$store.state.drawer) {
                    this.$store.state.drawer = false;
                } else {
                    this.$store.state.drawer = true;
                }
            },
            deep: true,
            immediate: true
        }
    },
};
</script>

<style scoped>
.v-navigation-drawer {
	border-radius: 15px;
}
.v-list-item--active:before {
	opacity: 1 !important;
}

.v-list--nav .v-list-item:before {
	border-radius: 7px !important;
}

.dash-route-active {
	color: #3b82f6 !important;
}

.dash-route-active .v-list-item__title {
	color: white !important;
	z-index: 1 !important;
}

</style>