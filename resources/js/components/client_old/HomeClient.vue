<template>
    <div class="m-5">
        <h1>{{$t('bs-home')}}</h1>
        <p>
            <span>
                {{$t('bs-welcome')}} {{$t('bs-to')}} {{$t('bs-support-system')}}, 
                {{$t('bs-choose-the-call-you-want-to-open')}}.
            </span> 
            
            <b-row class="mt-143 px-37" cols="1" cols-md="2" no-gutters>

                <template v-if="chat">
                    <!-- card1 -->
                    <b-col class="pr-75 py-3">
                        <b-row cols="1" class="local-card" no-gutters align-h="center">
                            <b-col class="lc-title py-2">{{$t('bs-chat')}}</b-col>
                            <b-col class="lc-subtitle py-2">{{$t('bs-chat-in-real-time-with-one-of-our-experts')}}</b-col>
                            <b-col class="lc-button py-2">
                                <b-button @click="redirect(1)" variant="primary">{{$t('bs-chat')}}</b-button>
                            </b-col>
                        </b-row>
                    </b-col>
                </template>

                <template v-if="ticket">
                <!-- card2 -->
                <b-col class="pl-75 py-3">
                    <b-row cols="1" class="local-card" no-gutters align-h="center">
                        <b-col class="lc-title py-2">{{$t('bs-ticket')}}</b-col>
                        <b-col class="lc-subtitle py-2">{{$t('bs-inform-your-problem-doubt')}}</b-col>
                        <b-col class="lc-button py-2">
                            <b-button @click="redirect(2)" variant="primary">{{$t('bs-ticket')}}</b-button>
                        </b-col>
                    </b-row>
               </b-col>
                </template>
            </b-row>
        </p>
    </div>
</template>

<script>
export default {
    data(){
        return {
            ticket: false,
            chat: false,
        }
    },
    props: {
        usuario: Object,
        restriction_client: Array,
    },
    mounted(){
        this.ticket = !this.restriction_client[0].ticket_hide;
        this.chat = !this.restriction_client[0].chat_hide;
    },
    methods: {
        redirect(valur){
            if(valur == 1){
                window.location.href = '/client-chat';
            }else{
                window.location.href = '/client-ticket';
            }
        }
    }
};
</script>
<style scoped lang="scss">
h1 {
    font: normal normal 800 1.5625rem/1.1875rem Muli;
    letter-spacing: 0px;
    color: #0080FC;
    opacity: 1;
}

p {
    font: normal normal 800 0.9375rem/1rem Muli;
    letter-spacing: 0.45px;
    color: #434343;
    span{
        opacity: 0.5;
    }
}


.mt-143{
    margin-top: 13.2407407vh; /* 143px */
}
.px-37{
    padding-left: 1.9270833vw; /* 37px */
    padding-right: 1.9270833vw; /* 37px */
}
.pl-75{
    padding-left: 3.90625vw; /* 75px */ 
}
.pr-75{
    padding-right: 3.90625vw; /* 75px */ 
}
.local-card{
    text-align: center;
    .lc-title{
        color: #0080FC;
        font-size: 1.5625rem;
        line-height: 2.1875rem;
        font-family: Muli;
        font-weight: 800;
        letter-spacing: 0px;
    }
    .lc-subtitle{
        color: #434343;
        font-size: 1rem;
        line-height: 1.25rem;
        font-family: Muli;
        font-weight: 800;
        letter-spacing: 0px;
    }
    .lc-button{
        font-size: 0.75rem;
        button{
            font-size: inherit!important;
        }
    }
}

@media only screen and (max-width: 767px) {
    .mt-143{
        margin-top: 1rem;
    }
    .px-37{
        padding-left: 0;
        padding-right: 0;
    }
    .pl-75{
        padding-left: 0;
    }
    .pr-75{
        padding-right: 0; 
    }
    
    h1{
        font-size: 1.25rem;
        line-height: 0.95rem;
    }
    p{
        font-size: 0.75rem;
        line-height: 0.8rem;
    }

    .local-card{
        .lc-title{
            font-size: 1.25rem;
            line-height: 1.75rem;
        }
        .lc-subtitle{
            font-size: 0.8rem;
            line-height: 1rem;
        }
        .lc-button{
            font-size: 0.6rem;
            button{
                font-size: inherit!important;
            }
        }
    }
}
</style>
