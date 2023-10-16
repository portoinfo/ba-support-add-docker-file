<template>
    <div class="text-center mb-2 d-flex justify-center">
        <v-sheet color="input" class="text-caption pa-3" elevation="4">
            <div v-show="!$store.state.isPopup">
                {{ $t('bs-wait-a-moment-for-one-of-our-attendants-to') }}
            </div>
            <div class="font-weight-bold">
                {{ $t("bs-queue-position") }}: {{ chatQueuePosition }}
            </div>
        </v-sheet>
    </div>
</template>
<script>
export default {
    computed: {
        chatQueuePosition() {
            return this.$store.state.chatQueuePosition;
        }
    },
}
</script>
