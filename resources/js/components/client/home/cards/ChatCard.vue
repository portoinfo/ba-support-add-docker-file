<template>
    <v-card id="card-chat" class="card-dash d-flex flex-column flex-nowrap" height="100%">
        <v-card-title class="shrink justify-center" v-if="!$store.state.isMobile">
            <v-avatar color="#FFAA00" height="118" width="120" class="chat-image">
                <img :src="`${$store.state.baseURL}/images/client/chats.png`">
            </v-avatar>
        </v-card-title>
        <v-list v-else color="transparent" class="pa-0 shrink">
            <v-list-item>
                <v-list-item-avatar class="pa-0" height="48"  min-width="50" width="50">
                    <v-avatar color="#FFAA00" height="48"  min-width="50" width="50" class="chat-image">
                        <img :src="`${$store.state.baseURL}/images/client/chats.png`">
                    </v-avatar>
                </v-list-item-avatar>
                <v-list-item-content>
                    <span class="text-subtitle-1 font-weight-bold">{{ $t('bs-chat') }}</span>
                </v-list-item-content>
            </v-list-item>
        </v-list>
        <v-card-text class="text-center grow">
            <p  v-if="!$store.state.isMobile" class="text-h6 font-weight-bold">{{ $t('bs-chat') }}</p>
            <div class="text-body-2" :class="{'h-100 d-flex flex-column align-center justify-center': $store.state.isMobile}">
                {{description}}
            </div>
        </v-card-text>
        <v-card-actions class="justify-center shrink pa-0">
            <div class="d-flex align-end h-100 pb-2">
                <v-btn  
                    large 
                    color="btnLight" 
                    class="text-capitalize btn-action pl-4"
                    :to="{ name: 'chat-opened', params: { id: 'create' } }"
                >
                    <span>{{ $t('bs-create') }}</span>
                    <v-icon right>
                        mdi-arrow-right
                    </v-icon>
                </v-btn>
            </div>
        </v-card-actions>
    </v-card>
</template>

<script>
export default {
	data(){
		return {
			description: "",
		}
	},
    mounted() {
        // this.getInfo();
        this.description = this.$t('bs-chat-in-real-time-with-one-of-our-experts');
    },
	methods: {
		getInfo(){
            var vm = this;
            axios.get(`client/get-config-company`)
            .then(function (response) {
                if(response.data.success){
                    if(response.data.value == 'false'){
                        vm.description = vm.$t('bs-chat-in-real-time-with-one-of-our-experts');
                    }else{
                        vm.description = response.data.value[0];
                    }
                }else{
                    vm.description = vm.$t('bs-chat-in-real-time-with-one-of-our-experts');
                }
            }).catch(function () {
            });
        },
	},
};
</script>

<style scoped>
.chat-image.v-avatar img {
    display: inline-flex !important;
    height: 94px !important;
    width: 72px !important;
    bottom: -12px !important;
    left: -6px !important;
    position: relative !important;
    border-radius: 0px !important;
}

@media screen and (max-width: 600px) {
    .chat-image.v-avatar img {
        height: 38.2px !important;
        width: 30px !important;
        bottom: -5px !important;
        left: -3px !important;
    } 
}
</style>