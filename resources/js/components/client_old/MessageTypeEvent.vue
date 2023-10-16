<template>
    <b-row v-if="isShow" class="mb-3 mt-3">
        <b-col cols="12" class="msg-event text-center">
            <strong v-if="message.content == 'bs-the-chat-ended-due-to-inactivity'">{{ $t(message.content) }}</strong>
            <strong v-else>{{  $t(message.name) ? $t(message.name) : "" || $t(message.user_name) ? $t(message.user_name) : $t(message.client_name) }}: {{ $t(message.content) }}</strong>
        </b-col>
    </b-row>
</template>

<script>
export default {
    data() {
        return {
            isShow: true,
        }
    },
    props:{
        message: Object,
        settings: Object,
        type: String,
    },
    mounted(){
        if(this.type == 'CHAT'){
            this.checkIsShowEventChat();
        }else{
            this.checkIsShowEventTicket();
        }
    },
    methods:{
        checkIsShowEventTicket(){
            if(this.settings.ticket.events == undefined){
                return this.isShow = true;
            }

            if(this.settings.ticket.events){
                for (let index = 0; index < this.settings.ticket.selectedEvents.length; index++) {
                    this.isShow = false;
                    if(this.settings.ticket.selectedEvents[index] == this.message.content){
                        this.isShow = true;
                        break;
                    }
                }  
            }else{
                return this.isShow = false;
            }
        },
        checkIsShowEventChat(){
            if('bs-started-the-chat' == this.message.content){
                return this.isShow = true;
            }
            if(this.settings.chat.events == undefined){
                return this.isShow = true;
            }
            if(this.settings.chat.events){
                for (let index = 0; index < this.settings.chat.selectedEvents.length; index++) {
                    this.isShow = false;
                    if(this.settings.chat.selectedEvents[index] == this.message.content){
                        this.isShow = true;
                        break;
                    }
                }   
            }else{
                return this.isShow = false;
            }
        }
    }
};
</script>

<style scoped>
.msg-event strong {
    font: normal normal 800 15px/31px Muli;
    letter-spacing: 0px;
    color: #707070;
}
</style>
