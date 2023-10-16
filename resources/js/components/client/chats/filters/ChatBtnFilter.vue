<template>
    <v-menu 
        offset-y 
        left 
        :close-on-content-click="false"
        @input="getDepartmentsByClientChats"
    >
        <template v-slot:activator="{ on, attrs }">
            <v-btn
                class="text-capitalize"
                elevation="0"
                color="input"
                rounded-sm
                v-bind="attrs"
                v-on="on"
            >
                <v-icon left> $filter </v-icon>
                {{ $t('bs-filter') }}
            </v-btn>
        </template>
        <v-card color="white" max-width="320">
            <v-container fluid class="pb-10">
                <v-list-item>
                    <v-list-item-icon>
                        <v-icon x-large>$filter_blue</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title v-text="$t('bs-filter-by')"></v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-row>
                    <v-col cols="12">
                        <v-select
                            v-model="departments"
                            :items="clientChatDepartments"
                            item-text="name"
                            item-value="id"
                            :label="$t('bs-department')"
                            multiple
                            solo
                            class="rounded-lg"
                            background-color="input"
                            hide-details
                            chips
                        >
                        </v-select>
                    </v-col>
                    <v-col cols="12">
                        <v-select
                            v-model="status"
                            :items="statusValues"
                            item-text="title"
                            item-value="name"
                            :label="$t('bs-status')"
                            multiple
                            solo
                            class="rounded-lg"
                            background-color="input"
                            hide-details
                            chips
                        >
                        </v-select>
                    </v-col>
                </v-row>
            </v-container>
        </v-card>
    </v-menu>
</template>

<script>
import { mapMutations } from "vuex";
export default {
    data() {
        return {
            clientChatDepartments: [],
            timeOut: Function,
            statusValues: [
                {name: 'ROBOT', title: this.$t('bs-creating')},
                {name: 'OPENED', title: this.$t('bs-in-queue')},
                {name: 'IN_PROGRESS', title: this.$t('bs-in-progress')},
                {name: 'CLOSED', title: this.$t('bs-closed')},
                {name: 'RESOLVED', title: this.$t('bs-resolved')},
            ]
        }
    },
    computed: {
        departments: {
            get() {
                return this.$store.state.chatFilterDepartment;
            },
            set(v) {
                this.$store.state.chatFilterDepartment = v;
            }
        },
        status: {
           get() {
                return this.$store.state.chatFilterStatus;
            },
            set(v) {
                this.$store.state.chatFilterStatus = v;
            } 
        }
    },
    watch: {
        departments() {
            this.filter();
        },
        status() {
            this.filter();  
        }
    },
    methods: {
        ...mapMutations(["getClientChats", "clearChats"]),
        filter() {
            clearTimeout(this.timeOut);
            this.timeOut = setTimeout(() => {
                this.$store.state.homeChatsPage = 1;
                this.clearChats();
                this.getClientChats();
            }, 1000)  
        },
        getDepartmentsByClientChats(menu_opened) {
            if (menu_opened) {
                var vm = this;
                var url = `${vm.$store.state.baseURL}/get-departments-by-client-chats`;
                axios.get(url)
                .then(({data}) => {
                    if (data.success) {
                        vm.clientChatDepartments = data.departments;
                    }
                })
            }
        }
    },
}
</script>

<style  scoped>
.theme--dark .v-list {
    background: #292935;
}

</style>