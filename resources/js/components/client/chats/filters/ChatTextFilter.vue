<template>
    <v-text-field 
        solo 
        flat 
        clearable
        :label="$t('bs-search')" 
        background-color="input" 
        v-model="content"
        hide-details
    >
        <template slot="prepend-inner">
            <v-icon small>$search</v-icon>
        </template>
    </v-text-field>
</template>

<script>
import { mapMutations } from "vuex";
export default {
    data() {
        return {
            timeOut: Function
        }
    },
    computed: {
        content: {
            get() {
                return this.$store.state.chatTextFilterContent;
            },
            set(v) {
                this.$store.state.chatTextFilterContent = v;
            }   
        },
    },
    watch: {
        content(value) {
            clearTimeout(this.timeOut);
            this.timeOut = setTimeout(() => {
                this.clearChats();
                this.$store.state.homeChatsPage = 1;
                this.getClientChats();
            }, 1000)  
        }
    },
    methods: {
        ...mapMutations(["getClientChats", "clearChats"]),
    },
}
</script>