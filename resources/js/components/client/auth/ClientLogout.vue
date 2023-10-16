<template>
    <v-overlay>
        <v-progress-circular
            indeterminate
            size="64"
        ></v-progress-circular>
    </v-overlay>
</template>
<script>
export default {
    created () {
        if(this.$store.state.isPopup){
            localStorage.removeItem("popupAutoLoginData");
        }
        window.location.href = `${this.$store.state.baseURL}/logout`
    },
}
</script>
