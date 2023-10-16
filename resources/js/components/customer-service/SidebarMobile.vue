<template>
    <div v-if="!showChat" class="sidebar-footer d-lg-none d-xl-none p-0">
        <div class="sidebar-footer-item" @click="goToPage('home')">
            <span class="bs-ico">
                &#xe88a; <!-- home -->
            </span>
        </div>
        <div class="sidebar-footer-item" :class="{'active': chat}" @click="goToPage('chat')">
            <span class="bs-ico">
                &#xe0bf;
            </span>
        </div>
        <div class="sidebar-footer-item" :class="{'active': ticket}" @click="goToPage('ticket')">
            <span class="bs-ico">
                &#xe0be;
            </span>
        </div>
        <div class="sidebar-footer-item" :class="{'active': filter}" @click="goToPage('filter')">
            <span class="bs-ico">
                &#xe156; <!-- arquivo -->
            </span>
        </div>
        <div class="sidebar-footer-item" v-b-toggle.sidebar-mobile>
            <span class="bs-ico">
                &#xe5d4; <!-- mais opções -->
            </span>
        </div>
    </div>
</template>

<script>
export default {
  data () {
    return {
        module: "",
    }
  },
    props: {
        base_url: String
    },
    methods: {
        goToPage(href) {
            if (href == "chat" || href == "ticket" || href == "filter" || href == "category") {
                if (href == "chat") {
                    this.module = "chat"
                    this.$store.commit("openChats");
                } else if (href == "ticket") {
                    this.module = "ticket"
                    this.$store.commit("openTickets");
                } else if (href == "filter") {
                    this.module = "filter"
                    this.$store.commit("openFilters");
                } else if (href == "category") {
                    this.module = "category"
                    this.$store.commit("openCategory");
                }

                window.history.replaceState({}, "", `${this.base_url}/customer-service?module=${this.module}`);

            } else {
                window.location.href = `${this.base_url}/${href}`;
            }
        },
    },
    computed: {
        chat() {
            return this.module == "chat" || window.location.search == "?module=chat";
        },
        ticket() {
            return this.module == "ticket" || window.location.search == "?module=ticket";
        },
        filter() {
            return this.module == "filter" ||  window.location.search == "?module=filter";
        },
        category() {
            return this.module == "category" ||  window.location.search == "?module=category";
        },
        showChat() {
            if (window.location.search == "?module=chat")
            return this.$root.$refs.FullChat2.showChat;
        }
    },
};
</script>

<style scoped>
.sidebar-footer {
    background: white;
    box-shadow: 0px 0px 9px #26242424;
    height: 60px;
    width: 100%;
    z-index: 0;
    position: fixed;
    bottom: 0;
    display: grid;
    grid-template-columns: auto auto auto auto auto;
}

.sidebar-footer-item {
    text-align: center;
    display: table;
    color: #A5B9D5;
    cursor: pointer;
    min-height: 100% !important;
    width: 100%;
}

.sidebar-footer-item:hover,
.sidebar-footer-item.active {
    color: #0080FC;
    background: #0294FF33;
}

.sidebar-footer-item span{
    display: table-cell;
    vertical-align: middle;
}
</style>
