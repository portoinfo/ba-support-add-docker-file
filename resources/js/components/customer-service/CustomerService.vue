<template>
    <div class="app3-content" :class="{'full-height': isMobile || !isMobile && !tabs_length, 'tabs-height': !isMobile && tabs_length}">
        <full-chat2
            v-if="showChat"
            :session_user="session_user"
            :session_user_departments="session_user_departments"
            :session_user_cucd="session_user_cucd"
            :session_user_permissions="session_user_permissions"
            :session_user_company="session_user_company"
            :restriction="restriction"
            :is_admin="is_admin">
        </full-chat2>
        <full-ticket2
            v-if="showTicket"
            :user="user"
            :user_departments_id="user_departments_id"
            :cs="cs"
            :restriction="restriction"
            :is_admin="is_admin"
            :session_user="session_user"
            :session_user_departments="session_user_departments"
            :session_user_cucd="session_user_cucd"
            :session_user_permissions="session_user_permissions"
            :session_user_company="session_user_company"
        >
        </full-ticket2>
        <filter-tickets-chats
            v-if="showFilter"
            :session_user="session_user"
            :session_user_departments="session_user_departments"
            :session_user_cucd="session_user_cucd"
            :session_user_permissions="session_user_permissions"
            :session_user_company="session_user_company"
        />
        <graphic-category
            v-if="showCategory"
            :session_user="session_user"
            :session_user_departments="session_user_departments"
            :session_user_cucd="session_user_cucd"
            :session_user_permissions="session_user_permissions"
            :session_user_company="session_user_company"
        />
    </div>
</template>

<script>
export default {
    data() {
        return {
            isMobile: false,
        }
    },
    created () {
        window.addEventListener("resize", this.onResize);
    },
    props: {
        session_user: Object,
        session_user_company: Object,
        session_user_cucd: Array,
        session_user_departments: Array,
        session_user_permissions: Array,
        restriction: Array,
        user: Object,
        cs: Object,
        is_admin: Number,
        user_departments_id: Array,
        base_url: String
    },
    computed: {
        tabs_length() {
            return this.$store.state.chatsFooter.length;
        },
        showChat() {
           return this.$store.state.showChat;
        },
        showTicket() {
           return this.$store.state.showTicket;
        },
        showFilter() {
           return this.$store.state.showFilter;
        },
        showCategory() {
           return this.$store.state.showCategory;
        },
        customerService() {
            return this.showFilter || this.showTicket || this.showChat || this.showCategory;
        }
    },
    mounted () {
        this.onResize();
        if(!this.customerService) {
            window.location.href = `${window.location.origin}/home`;
        };
    },
    methods: {
        onResize(e) {
            if ($(window).width() <= 992) {
                this.isMobile = true;
            } else {
                this.isMobile = false;
            }
        }
    },
}
</script>

<style scoped>
.app3-content {
    padding: 0px 10px 0px;
}

.full-height {
   /* height: calc(100vh - 60px); */
   min-height: 100%;
   max-height: 100%;
}

.tabs-height {
    height: calc(100% - 126px);
}

@media screen and (max-width: 992px) {
    .app3-content {
        padding-right: 0px;
        padding-left: 0px;
        padding-top: 4px;
    }
}
</style>
