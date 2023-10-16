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
                return this.$store.state.ticketTextFilterContent;
            },
            set(v) {
                this.$store.state.ticketTextFilterContent = v;
            }   
        },
    },
    watch: {
        content(value) {
            clearTimeout(this.timeOut);
            this.timeOut = setTimeout(() => {
                this.clearTickets();
                this.$store.state.homeTicketsPage = 1;
                this.getClientTickets();
            }, 1000)  
        }
    },
    methods: {
        ...mapMutations(["getClientTickets", "clearTickets"]),
    },
}
</script>