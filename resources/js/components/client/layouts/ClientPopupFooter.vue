<template>
  <v-footer padless>
    <v-card class="flex" flat tile :color="color">
      <v-card-title class="pb-2 pt-0 transparent">
        <img @click="goToHelpdesk()" height="24" src="/images/bs/BA_Helpdesk_Logo.svg" alt="Builderall Helpdesk" />
        <v-spacer></v-spacer>
        <v-btn icon @click="logout()" v-if="!$store.state.isOfficePopup">
            <v-icon size="1.125rem">$logout_1</v-icon>
        </v-btn>
      </v-card-title>
    </v-card>
  </v-footer>
</template>

<script>
export default {
    props: {
        color: String
    },
    methods: {
        logout() {
            this.$router.push({name:'logout'})
        },
        goToHelpdesk() {
            const url = `${this.$store.state.baseURL}/customer-home`;
            window.open(url, '_blank').focus();
        }
    },
};
</script>

<style scoped>
    img {
        cursor: pointer;
    }
</style>
