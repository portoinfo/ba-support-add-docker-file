<template>
    <v-app-bar elevate-on-scroll color="white" elevation="0" app clipped-left>
        <v-app-bar-nav-icon v-if="!ticketOpened" @click.stop="$store.state.drawer = !$store.state.drawer" class="d-none d-lg-flex d-xl-flex mr-2"> 
            <v-icon>
                ${{ $store.state.drawer ? 'menu_burger' : 'menu_burger_open'}}
            </v-icon>
        </v-app-bar-nav-icon>
        <v-toolbar-title class="px-0 pt-2">
            <img 
                v-if="location != 'hs.builderall.com'" 
                :src="`/images/${$vuetify.theme.dark ? 'BA_WHITE' : 'BA_NORMAL'}.png`"
                alt="Builderall"
                height="32"
            />
        </v-toolbar-title>
        <v-toolbar-title class="ml-2 text-overline">
            {{ $store.state.csname }}
        </v-toolbar-title>
        <v-spacer></v-spacer>

        <!-- <v-app-bar-nav-icon @mouseover="upHere(true)" @mouseleave="upHere(false)" class="d-none d-lg-flex d-xl-flex mr-1 mt-1"> 
            <v-icon>
                ${{ $vuetify.theme.dark ? 'calendar_today' : 'calendar_today_d' }}
            </v-icon>
        </v-app-bar-nav-icon> -->
        
        <v-switch
            class="mt-5"
            dense
            v-model="$vuetify.theme.dark"
            :prepend-icon="!$vuetify.theme.dark ? '$sun' : 'mdi-weather-night'"
        ></v-switch>

        <v-list max-width="260" class="nav-user-info" color="transparent">
            <v-list-item :class="$store.state.isMobile ? 'px-0 mx-0' : ''">
                <v-list-item-avatar tile :class="$store.state.isMobile ? 'px-0 mx-0' : ''">
                    <v-gravatar
                        :email="$store.state.user.email"
                        :name="$ct($store.state.user.name)"  
                        :status="$status.get($store.state.user.id)"
                        size="40"
                    ></v-gravatar>
                </v-list-item-avatar>
                <v-list-item-content v-if="!$store.state.isMobile">
                    <v-list-item-title class="font-weight-medium">
                        {{ $ct($store.state.user.name) }}
                    </v-list-item-title>
                    <v-list-item-subtitle>
                        {{ $formatEmail($store.state.user.email) }}
                    </v-list-item-subtitle>
                </v-list-item-content>
                <v-list-item-action class="px-0 mx-0">
                    <v-menu offset-y content-class="menu-profile" :close-on-content-click="false">
                        <template v-slot:activator="{ on, attrs }">
                            <v-icon right v-bind="attrs" v-on="on"> mdi-chevron-down </v-icon>
                        </template>
                        <client-menu-profile></client-menu-profile>
                    </v-menu>
                </v-list-item-action>
            </v-list-item>
        </v-list>
    </v-app-bar>
</template>

<script>
export default {
    data() {
        return {
            location: window.location.hostname,
        }
    },
    watch: {
        '$vuetify.theme.dark': function(newValue) {
            clearTimeout(this.timeOut);
            this.timeOut = setTimeout(() => {
                this.setUserDarkMode(newValue);
            }, 1000)  
        },
    },
    computed: {
        user() {
            return this.$store.state.user;
        },
        ticketOpened() {
            return this.$route.name == 'ticket-opened';
        }
    },
    methods: {
        upHere($bool){
            if($bool){
                console.log('entrou');
            }else{
                console.log('saiu');
            }
        },
        setUserDarkMode(dark_mode) {
            var url = `${this.$store.state.baseURL}/set-user-dark-mode`;
            axios.post(url,{dark_mode});
            this.$store.state.user.dark_mode = dark_mode;
        },
    },
}
</script>

<style scoped>
.nav-user-info.v-list,
.nav-user-info .v-list-item__content {
	padding: 0px;
}
.menu-profile {
	top: 69px !important;
	border-radius: 10px;
	width: fit-content;
}
</style>