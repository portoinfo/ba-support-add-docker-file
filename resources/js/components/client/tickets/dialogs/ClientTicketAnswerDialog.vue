<template>
    <v-dialog v-model="answerDialogShown" scrollable max-width="800" content-class="custom-dialog ticket-answer-dialog">
        <v-card class="card-dialog" color="whiteCard" elevation="15">
            <v-badge color="#0080FC" avatar overlap offset-y="15" offset-x="15">  
                <template v-slot:badge>
                    <v-avatar size="30" @click="answerDialogShown = false">
                        <v-icon size="20">mdi-close</v-icon>
                    </v-avatar>
                </template>
                <v-card-title class="text-h6 font-weight-bold justify-left">
                    {{ $t("bs-send-message") }}
                </v-card-title>
                <v-card-text>
                    <client-chat-ticket-editor module="ticket"/>
                </v-card-text>
            </v-badge>
        </v-card>
	</v-dialog>
</template>

<script>
export default {
    computed: {
        answerDialogShown: {
			get() {
				return this.$root.$refs.ClientTicketOpened.answerDialogShown;
			},
			set(value) {
				this.$root.$refs.ClientTicketOpened.answerDialogShown = value;
			},
		},
        ticket() {
            return this.$store.state.ticket;
        }
    },
}
</script>