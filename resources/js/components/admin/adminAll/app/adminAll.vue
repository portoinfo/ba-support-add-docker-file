<template>
    <div>
        <builderall-banner 
            title='Help Desk Builderall'
            subtitle='Support your users with chat or ticket support.'
            description="Improve your business's value perception with the most advanced <br>support tool available on the market."
        />
        <!-- <div class='ba-banner'>
            <input type='checkbox' class='ba-input-status ' id='ba-toggle-info'>
            <div class='ba-header'>
                <div class='ba-header-left'>
                    <span class='ba-icon'>
                        <svg width="45" style="background-color: rgb(1, 212, 185);" height="45" viewBox="0 0 45 45" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="22.5" cy="22.5" r="22.5" fill="#01D4B9"/>
                            <g clip-path="url(#clip0_1_672)">
                            <path d="M12.9936 25.9131C11.3433 25.9131 10 24.6113 10 23.0111V22.9527C10 21.3526 11.3433 20.0508 12.9936 20.0508C13.5011 20.0508 13.9126 20.4534 13.9126 20.9499C13.9126 21.4464 13.5011 21.8491 12.9936 21.8491C12.3561 21.8491 11.8358 22.3435 11.8358 22.9527V23.0111C11.8358 23.6203 12.3561 24.1147 12.9936 24.1147C13.5011 24.1147 13.9126 24.5174 13.9126 25.0139C13.9126 25.5104 13.5011 25.9131 12.9936 25.9131Z" fill="white"/>
                            <path d="M32.0044 25.9131C31.497 25.9131 31.0854 25.5104 31.0854 25.0139C31.0854 24.5174 31.497 24.1147 32.0044 24.1147C32.642 24.1147 33.1622 23.6203 33.1622 23.0111V22.9527C33.1622 22.3435 32.642 21.8491 32.0044 21.8491C31.497 21.8491 31.0854 21.4464 31.0854 20.9499C31.0854 20.4534 31.497 20.0508 32.0044 20.0508C33.6547 20.0508 34.998 21.3526 34.998 22.9527V23.0111C34.998 24.6113 33.6547 25.9131 32.0044 25.9131Z" fill="white"/>
                            <path d="M25.2281 30.9701C24.8422 30.9701 24.4818 30.7302 24.356 30.3505C24.1982 29.879 24.4626 29.372 24.9445 29.2177C28.275 28.1537 30.5117 25.1328 30.5117 21.7031C30.5117 17.3449 26.8869 13.7983 22.4328 13.7983C17.9786 13.7983 14.356 17.3428 14.356 21.701C14.356 22.1975 13.9445 22.6001 13.437 22.6001C12.9296 22.6001 12.5181 22.1975 12.5181 21.701C12.5181 16.3519 16.9658 12 22.4328 12C27.8997 12 32.3475 16.3519 32.3475 21.701C32.3475 25.911 29.6012 29.6182 25.5138 30.9242C25.42 30.9534 25.324 30.968 25.2281 30.968V30.9701Z" fill="white"/>
                            <path d="M23.1538 33C21.5034 33 20.1602 31.6982 20.1602 30.0981V30.0396C20.1602 28.4395 21.5034 27.1377 23.1538 27.1377C24.8041 27.1377 26.1474 28.4395 26.1474 30.0396V30.0981C26.1474 31.6982 24.8041 33 23.1538 33ZM23.1538 28.936C22.5162 28.936 21.996 29.4305 21.996 30.0396V30.0981C21.996 30.7072 22.5162 31.2017 23.1538 31.2017C23.7913 31.2017 24.3115 30.7072 24.3115 30.0981V30.0396C24.3115 29.4305 23.7913 28.936 23.1538 28.936Z" fill="white"/>
                            <path d="M13.437 25.9131C12.9296 25.9131 12.5181 25.5104 12.5181 25.0139V21.9576C12.5181 21.4611 12.9296 21.0584 13.437 21.0584C13.9445 21.0584 14.356 21.4611 14.356 21.9576V25.0139C14.356 25.5104 13.9445 25.9131 13.437 25.9131Z" fill="white"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_1_672">
                            <rect width="25" height="21" fill="white" transform="translate(10 12)"/>
                            </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <span class="ba-span">Help Desk Builderall</span>
                </div>
                <label class='ba-label' for='ba-toggle-info'></label>
            </div>
            <div class="ba-tool-description mb-4">
                <span class="ba-span">Support your users with chat or ticket support.</span>
                <span class="ba-span">Improve your business's value perception with the most advanced <br>support tool available on the market.</span>
            </div>
        </div>  -->
        <div class='ba-app-wrapper ba-gp-1'> 
            <nav-bar-new></nav-bar-new>
            <router-view></router-view>
            <builderall-footer class="ba-mg-t-4"></builderall-footer>
        </div>
    </div>
</template>

<script>
import navBarNew from '../layout/navBarNew.vue';
import iconsCustom from '../util/icons/iconsCustom.vue';
import { mapMutations } from "vuex";

export default {
    components: {
        navBarNew,
        iconsCustom,
    },
	data(){
		return {
			showItem: 'dasboard',
            itensOptions: [
                { key: 'dasboard'},
                { key: 'service'},
                { key: 'general'},
                { key: 'activity'},
                { key: 'departments'},
                { key: 'groups'},
                { key: 'attendants'},
                { key: 'settings'},
                { key: 'registered'},
            ],
		}
	},
    props:{
        csid: String,
        is_admin: Number,
        session_user: Object,
        session_user_company: Object,
        session_user_cucd: Array,
        session_user_departments: Array,
        session_user_permissions: Array,
        restriction: Array,
    },
	created(){
        this.$root.$refs.adminAll = this;
        this.$store.state.csid = this.csid;
        this.$store.state.company = this.csid;
        this.$store.state.is_admin = this.is_admin
        this.$store.state.session_user = this.session_user
        this.$store.state.session_user_company = this.session_user_company
        this.$store.state.session_user_cucd = this.session_user_cucd
        this.$store.state.session_user_departments = this.session_user_departments
        this.$store.state.session_user_permissions = this.session_user_permissions
        this.$store.state.restriction = this.restriction
        // console.log(this.csid);
        this.online();
	},
	methods: {
        ...mapMutations(["online"]), // , "getClientChats", "joinClientQueueStatus", "joinClientTicketsList"
        ...mapMutations(["offline"]),
        checkRouteActive(name_r){
            // Obter a parte da URL após a barra
            var url = window.location.href;
            var posicaoBarra = url.indexOf("/");
            var parteAposBarra = url.substring(posicaoBarra + 1);

            if (parteAposBarra.includes(name_r)) {
                this.showItem = name_r;
            } else{
                this.showItem = 404;
            }


            // let loopFinished = false;
            // this.itensOptions.forEach(element => {
            //     if (parteAposBarra.includes(element.key)) {
            //         this.showItem = element;
            //     }
            //     // Verificar se o loop está completo
            //     if (element === this.itensOptions[this.itensOptions.length - 1]) {
            //         loopFinished = true;
            //     }
            // });
            // // Verificar se o loop forEach foi concluído
            // if (loopFinished) {
            //     this.showItem = 404;
            // }
        },
	},
};
</script>

<style scoped>
    .ba-banner {
        /* background: linear-gradient(92.25deg, #0072e1c4 31.46%, #25344500 85.86%); */
        background-image: url(/images/BG-Business-Center1.png);
        background-size: cover;
        background-repeat: no-repeat;
    }
</style>