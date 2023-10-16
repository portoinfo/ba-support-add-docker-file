<template>
    <div :class="!menuRecall ? 'ba-sidebar' : 'ba-sidebar bsc'">
        <!-- NAV BAR -->
        <ul class='ba-nav'>
            <li class='caret' style="margin-left: 23px;margin-bottom: 15px;">
                <span @click="openMenu()" class="ba-link">
                    <icons-custom name="menu-button-lines"></icons-custom>
                </span>
            </li>
            <li class='ba-nav-item'>
                <router-link :to="'/new/dashboard'" :class="is_active.dashboard ? 'ba-link ba-is-active' : 'ba-link'">
                    <icons-custom :active="is_active.dashboard" name="dashboard-icon"></icons-custom>
                    <span class='ba-title' v-show="!menuRecall">{{ $t('bs-dashboard') }}</span>
                </router-link>
            </li>
            <li class='ba-nav-item'>
                <input type='checkbox' checked class='ba-nav-item-input-status' id='ba-toggle-expandable-content-3'>
                <label :class="!menuRecall ? 'ba-nav-item-expandable' : 'ba-nav-item-expandable box-shadow-remove'" for='ba-toggle-expandable-content-3'>
                    <div class='ba-nav-item-expandable-content'>
                        <icons-custom :active="is_active.chat||is_active.ticket||is_active.archive||is_active.filter" name="online-support-gray-icon"></icons-custom>
                        <span class='ba-title ft-700' v-show="!menuRecall">{{$t('bs-support')}} <icons-custom style="padding-left: 40%;" :active="false" name="down-icon" width="12"></icons-custom></span>
                    </div>
                    <ul class='ba-nav-subitems'>
                        <hr class='ba-hr-row'>
                        <li class='ba-nav-subitem'>
                            <router-link :to="'/new/chat'" :class="is_active.chat ? 'ba-nav-subitem-link ba-mg-l-1 ba-is-active' : 'ba-nav-subitem-link ba-mg-l-1'">
                                <icons-custom :active="is_active.chat" name="chat-icon"></icons-custom>
                                <span class='ba-title ft-700' v-show="!menuRecall">{{ $t('bs-chat') }}</span>
                            </router-link>
                        </li>
                        <li class='ba-nav-subitem'>
                            <router-link :to="'/new/ticket'" :class="is_active.ticket ? 'ba-nav-subitem-link ba-mg-l-1 ba-is-active' : 'ba-nav-subitem-link ba-mg-l-1'">
                                <icons-custom width="26" :active="is_active.ticket" name="ticket-icon"></icons-custom>
                                <span class='ba-title ft-700' v-show="!menuRecall">{{$t('bs-ticket')}}</span>
                            </router-link>
                        </li>
                        <li class='ba-nav-subitem'>
                            <router-link :to="'/new/archive'" :class="is_active.archive ? 'ba-nav-subitem-link ba-mg-l-1 ba-is-active' : 'ba-nav-subitem-link ba-mg-l-1'">
                                <icons-custom :active="is_active.archive" name="archive-icon"></icons-custom>
                                <span class='ba-title ft-700' v-show="!menuRecall">{{$t('bs-archive')}}</span>
                            </router-link>
                        </li>
                        <li class='ba-nav-subitem'>
                            <router-link :to="'/new/filter'" :class="is_active.filter ? 'ba-nav-subitem-link ba-mg-l-1 ba-is-active' : 'ba-nav-subitem-link ba-mg-l-1'">
                                <icons-custom :active="is_active.filter" width="26" name="filter-icon"></icons-custom>
                                <span class='ba-title ft-700' v-show="!menuRecall">{{$t('bs-filter-categories')}}</span>
                            </router-link>
                        </li>
                    </ul>
                </label>
            </li>
            <li class='ba-nav-item'>
                <router-link :to="'/new/settings'" :class="is_active.settings ? 'ba-link ba-is-active' : 'ba-link'">
                    <icons-custom :active="is_active.settings" name="settings-icon"></icons-custom>
                    <span class='ba-title' v-show="!menuRecall">{{ $t('bs-settings') }}</span>
                </router-link>
            </li>
            <li class='ba-nav-item'>
                <router-link :to="'/new/select-company'" :class="is_active.selectCompany ? 'ba-link ba-is-active' : 'ba-link'">
                    <icons-custom :active="is_active.selectCompany" name="select-company-icon"></icons-custom>
                    <span class='ba-title' v-show="!menuRecall">{{ $t('bs-change-company') }}</span>
                </router-link>
            </li>
        </ul>

        <div v-if="!menuRecall" class='ba-bottom'>
            <div class='ba-overview'>
                <div class='ba-video-thumb'>
                    <img src='https://placehold.co/159x107/000000/AAAAAA' alt='Overview Tools Name'>	
                    <button class='ba-button'>
                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-arrows-angle-expand' viewBox='0 0 16 16'>
                            <path fill-rule='evenodd' d='M5.828 10.172a.5.5 0 0 0-.707 0l-4.096 4.096V11.5a.5.5 0 0 0-1 0v3.975a.5.5 0 0 0 .5.5H4.5a.5.5 0 0 0 0-1H1.732l4.096-4.096a.5.5 0 0 0 0-.707zm4.344-4.344a.5.5 0 0 0 .707 0l4.096-4.096V4.5a.5.5 0 1 0 1 0V.525a.5.5 0 0 0-.5-.5H11.5a.5.5 0 0 0 0 1h2.768l-4.096 4.096a.5.5 0 0 0 0 .707z'/>
                        </svg>
                    </button>
                </div>
                <span class='ba-title'>Help Desk </span>
                <span class='ba-description'>Improve your business's value perception with the most advanced support tool available on the market.</span>
                <button @click="openLink" class='ba-btn ba-sm ba-blue ba-w-100'>
                    More Info
                    <svg xmlns='http://www.w3.org/2000/svg' width='10' height='10' fill='#fff' class='bi bi-arrow-up-right' viewBox='0 0 16 16'>
                        <path fill-rule='evenodd' d='M14 2.5a.5.5 0 0 0-.5-.5h-6a.5.5 0 0 0 0 1h4.793L2.146 13.146a.5.5 0 0 0 .708.708L13 3.707V8.5a.5.5 0 0 0 1 0v-6z'/>
                    </svg>
                </button>
            </div>
            <!-- <ul class='ba-bottom-list'>
                <li>
                    <a href='#' class='ba-flutuant-link'>
                        <span class='ba-icon'>
                            YOUR_ICON
                        </span>
                        <span class='ba-text'>Example</span>
                    </a>
                </li>
                <li>
                    <a href='#' class='ba-flutuant-link'>
                        <span class='ba-icon'>
                            YOUR_ICON
                        </span>
                        <span class='ba-text'>Example</span>
                    </a>
                </li>
            </ul> -->
        </div>
        <span style="border: 1px solid #D5DEE9;"></span>
        <ul class='ba-nav'>
            <builderall-help v-show="!menuRecall"></builderall-help>
            <builderall-help collapsed="true" v-show="menuRecall"></builderall-help>
        </ul>
    </div>
</template>

<script>
import iconsCustom from '../util/icons/iconsCustom.vue';
export default {
    components: {
        iconsCustom,
    },
	data(){
		return {
			services: [
                { active:true, key: 'dashboard',  label: this.$t('DashBoard') },
                { active:true, key: 'chat',  label: this.$t('bs-chat') },
                { active:true, key: 'ticket',  label: this.$t('bs-ticket') },
                { active:true, key: 'archive',  label: this.$t('bs-archive') },
                { active:true, key: 'filter',  label: this.$t('bs-filter') },
                { active:true, key: 'settings',  label: this.$t('bs-settings') },
            ],
            is_active: {
                dashboard: false,
                chat: true,
                ticket: false,
                archive: false,
                filter: true,
                settings: false,
                selectCompany: false,
            },
            menuRecall: false,
		}
	},
    beforeRouteUpdate(to, from, next) {
        // Aqui você pode chamar sua função toda vez que a rota for atualizada
        this.handleRouteUpdate(to, from);
        next();
    },
	created(){
        this.handleRouteUpdate(this.$route, this.$route.path);
        if(this.is_active.chat||this.is_active.ticket){
            this.menuRecall = true;
        }
	},
    watch: {
        // Assista às mudanças na rota para atualizações contínuas
        $route(to, from) {
            this.handleRouteUpdate(to, from);
        },
    },
    props:{
    },
    computed: {
        linkClass() {
            return (item) => {
                const activeClass = 'ba-link ba-is-active';
                const inactiveClass = 'ba-link';
                if (this.$route.path === '/new/' + item.key) {
                return activeClass;
                } else {
                return inactiveClass;
                }
            };
        }
    },
	methods: {
        openMenu(){
            this.menuRecall = !this.menuRecall;
        },
        handleRouteUpdate(to, from){
            // console.log("De:"+from.fullPath)
            // console.log("Para:"+to.fullPath)

            for (const key in this.is_active) {
                this.is_active[key] = false;
            }

            switch (to.fullPath) {
                case '/new/dashboard':
                    this.is_active.dashboard = true;
                    break;
                case '/new/chat':
                    this.menuRecall = true;
                    this.is_active.chat = true;
                    break;
                case '/new/ticket':
                    this.menuRecall = true;
                    this.is_active.ticket = true;
                    break;
                case '/new/archive':
                    this.is_active.archive = true;
                    break;
                case '/new/filter':
                    this.is_active.filter = true;
                    break;
                case '/new/settings':
                    this.is_active.settings = true;
                    break;
                case '/new/select-company':
                    this.is_active.selectCompany = true;
                    break;
                default:
                    break;
            }

        },
        openLink() {
            // Replace the link below with the correct URL
            window.open('https://knowledgebase.builderall.com/docs-category/helpdesk/', '_blank');
        },
        optionsNav(event, item){
            alert('eae');
            if (event !== null) {
                event.preventDefault();
            }
            this.services.forEach(element => {
                if(item.key == element.key){
                    element.active = true;
                    // window.history.pushState({}, "", "http://localhost:8000/new/"+element.key);
                    // this.$root.$refs.adminAll.checkRouteActive(element.key);
                }else{
                    element.active = false;
                }
            });
        }
	},
};
</script>

<style scoped>

    .bsc{
        width: 74px !important;
        box-shadow: none !important;
    }

    .ft-700{
        font-weight:700 !important;
    }
</style>