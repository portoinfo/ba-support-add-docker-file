<template>
    <v-row no-gutters justify="start">
        <v-col cols="11" xl="6" lg="7" md="9" sm="9" class="text-right">
            <v-row no-gutters class="py-1">
                <v-col class="w-fc mr-5">
                    <v-gravatar
                        v-show="!$store.state.isMobile"
                        :size="$store.state.isMobile ? '30' : '40'"
                        :robot="true"
                        v-if="!content.click_title"
                    ></v-gravatar>
                </v-col>
                <v-col style="width: 1px;" :class="!content.click_title ? 'mt-1' : 'mt-1 ml-cc'">
                    <div :class="!content.click_title ? 'bubble-left' : 'bubble-left b-l'" style="max-width: 100%;">
                        <v-list two-line class="py-0" rounded color="transparent">
                            <v-list-item class="px-1">
                                <v-list-item-content>
                                    <v-list-item-title v-if="content.description == 'off'">
                                        <v-row no-gutters>
                                            <v-col class="text-truncate text-left text-subtitle-2">
                                                {{ $store.state.name_robot }} {{ $root.$refs.ClientChatOpened.pasteTools == '' ? '' : ' - '+$root.$refs.ClientChatOpened.pasteTools}}
                                            </v-col>
                                            <v-col class="text-right w-fc text-caption">
                                                <small>{{ $formatDate(message.created_at) }}</small>
                                            </v-col>
                                        </v-row>
                                    </v-list-item-title>
                                    <v-list-item-subtitle class="text-body-2 text-left text-wrap"  style="word-break: break-word !important;">
                                        <span v-if="content.click_title" @click="getChildren(content, content.showDescription)" class="faq-title">
                                            {{ content.title }}
                                        </span>
                                        <span v-if="!content.click_title">
                                            <div class="output ql-snow caret">
                                                <div class="ql-editor px-0 pb-0 pt-2" v-html="content.title"></div>
                                            </div>
                                        </span>
                                        <div class="output ql-snow caret" v-if="content.click_title && content.showDescription && content.description != 'off'" @click="getChildren(content, content.showDescription)">
                                            <div class="ql-editor px-0 pb-0 pt-2" v-html="content.description"></div>
                                        </div>
                                        <span v-if="content.showMultipleAnswer">
                                            <v-row no-gutters v-for="(item, index) in convertArray(content.description)" :key="index">
                                                <v-col class="mt-1" @click="showAnswer(item,index)">
                                                    <span class="caret clickable">{{ item }}</span>
                                                </v-col>
                                            </v-row>
                                        </span>
                                        <span v-else>
                                            <div class="output ql-snow" v-if="!content.click_title && content.showDescription && content.description != 'off' && !checkIsLoom">
                                                <div class="ql-editor px-0 pb-0 pt-2" v-html="content.description"></div>
                                            </div>
                                        </span>
                                        <div class="output ql-snow" v-if="!content.click_title && content.showDescription && content.description != 'off' && checkIsLoom">
                                            <div class="ql-editor px-0 pb-0 pt-2" v-html="removeLinkLoom(content.description)"></div>
                                            <iframe id="iframe-content" style="width: 100%; height: 100%;" :src="loomLink" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                        </div>
                                        <div class="mt-2" v-if="content.question_final">
                                            <v-btn depressed color="primary" v-if="showEvaluate" @click="saveEvaluation(content, true)">{{$t('bs-yes')}}</v-btn>
                                            <v-btn depressed color="primary" v-if="showEvaluate" @click="saveEvaluation(content, false)">{{$t('bs-not')}}</v-btn>
                                        </div>
                                        <div class="mt-2" v-if="content.quit_continue">
                                            <v-btn depressed color="primary" v-if="showEvaluate" @click="exitContinue(false)">{{$t('bs-finish')}}</v-btn>
                                            <v-btn depressed color="primary" v-if="showEvaluate" @click="exitContinue(true)">{{$t('bs-continue')}}</v-btn>
                                        </div>

                                        <div :class="display" v-if="content.not_options">
                                            <v-btn depressed small color="primary" class="custom-button" v-if="showEvaluate" @click="allSolutionFaqRobot(content, true)">{{$t('bs-more-options')}}</v-btn>
                                            <v-btn depressed small color="primary" class="custom-button" v-if="showEvaluate" @click="allSolutionFaqRobot(content, false)">{{$t('bs-talk-to-specialist')}}</v-btn>
                                        </div>

                                        <div :class="display" v-if="content.sub_not_options">
                                            <v-btn depressed small color="primary" class="custom-button" v-if="showEvaluate" @click="getChildren(content, true, true)">{{$t('bs-more-options')}}</v-btn>
                                            <v-btn depressed small color="primary" class="custom-button" v-if="showEvaluate" @click="allSolutionFaqRobot(content, false)">{{$t('bs-talk-to-specialist')}}</v-btn>
                                        </div>

                                        <div :class="display" v-if="content.reset_chat">
                                            <v-btn depressed small color="primary" class="custom-button" v-if="showEvaluate" @click="FaqRobot()">{{$t('bs-more-options')}}</v-btn>
                                            <v-btn depressed small color="primary" class="custom-button" v-if="showEvaluate" @click="allSolutionFaqRobot(content, false)">{{$t('bs-talk-to-specialist')}}</v-btn>
                                        </div>

                                        <div class="mt-2" v-if="content.select_tool">
                                            <v-btn depressed small color="primary" class="custom-button" v-if="showEvaluate" @click="FaqRobot()">{{$t('bs-select-tool')}}</v-btn>
                                            <v-btn depressed small color="primary" class="custom-button" v-if="showEvaluate" @click="allSolutionFaqRobot(content, false)">{{$t('bs-talk-to-specialist')}}</v-btn>
                                        </div>
                                        
                                        <div class="mt-2" v-if="content.create_chat">
                                            <v-btn depressed small color="primary" v-if="showEvaluate" @click="allSolutionFaqRobot(content, false)">{{$t('bs-talk-to-specialist')}}</v-btn>
                                        </div>
                                    </v-list-item-subtitle>
                     

                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </div>
                </v-col>
            </v-row>
        </v-col>
    </v-row>
</template>
<script>
export default {
    data(){
		return {
			tz: "",
            showDescription: true,
            showEvaluate: true,
            checkIsLoom: false,
            loomLink: '',
            display: 'mt-2',
		}
	},
    props: {
        message: {
            type: Object,
            default: {}
        },
        index: '',
    },
    computed: {
        content() {
            var check = this.message.content.description == null ? '' : this.message.content.description;

            if (check.includes("loom.com/share/") || check.includes("loom.com/embed/")) {
                try {
                    var parser = new DOMParser();
                    var parsedHtml = parser.parseFromString(this.message.content.description, 'text/html');
                    var getlink = parsedHtml.querySelector('a').getAttribute('href');
                    this.loomLink = getlink.replace(/share/g, "embed");;
                    setTimeout(() => {
                        this.checkIsLoom = true;
                    }, 100);   
                } catch (error) {
                    return this.message.content;
                }
            } else {
                this.checkIsLoom = false;
            }

            if(typeof this.message.content == 'string'){
                return JSON.parse(this.message.content);
            }else{
                return this.message.content;
            }
        },
        is_clickable(){
            if(this.$root.$refs.ClientChatOpened.messages.length - 1 > this.index){
                return false;
            }
            return true;
        }
    },
    mounted () {
        this.onResize();
    },
    created() {
        if(typeof this.message.content == 'string'){
            this.message.content = JSON.parse(this.message.content);
        }

        if(this.message.content.title == null){

        }else{
            this.$root.$refs.ClientChatOpened.arrayMsgBot.push(this.message.content.title);
        }

        window.addEventListener("resize", this.onResize);
    },
    methods: {
        onResize(e) {
            // console.log($(window).width());
            if($(window).width() >= 960 && $(window).width() <= 1263 ){
                this.display = 'mt-2 dpg';
            }else{
                this.display = 'mt-2 ';
            }
        },
        showAnswer(item, index){
            const firstChar = item.charAt(0);
            this.$root.$refs.ClientChatOpened.showmutipleresults.status = true;
            this.$root.$refs.ClientChatOpened.chats[0].latest_ch = firstChar;
            this.$root.$refs.ClientChatOpened.newStr = firstChar;
            setTimeout(() => {
                this.$root.$refs.ClientChatOpened.faqRobotAction();
            }, 40);
        },
        convertArray(value){
            return value.split(/{{(.*?)}}/g).filter(Boolean);
        },
        FaqRobot(){
            this.$root.$refs.ClientChatOpened.faqRobot(true);
        },
        removeLinkLoom(stringComLink){
            // replace(/https?:\/\/[^\s]+/, ''); 
            const stringSemLink = stringComLink.replace(/<a\b[^>]*>(.*?)<\/a>/gi, '');
            return stringSemLink.trim();
        },
        allSolutionFaqRobot(item, bool){
            // if(!this.is_clickable){
            //     return;
            // }
            
            if(bool){
                const url = `${this.$store.state.baseURL}/get-faq-robot-tools`;
                return new Promise((resolve, reject) => {
                    axios.get(url, {
                        params: {
                            numberTools: item.numberTools,
                            skip: this.$store.state.skipGetFaqRobot,
                        }
                    }).then(res => {
                        this.$store.state.skipGetFaqRobot = this.$store.state.skipGetFaqRobot +1;
                        res.data.result.forEach(element => {
                            this.$root.$refs.ClientChatOpened.messages.push({
                                id: 1,
                                cucd_id: null,
                                type: 'FAQ_ROBOT',
                                content: {
                                    id: element.id,
                                    title: element.title,
                                    description: element.description,
                                    tool_status: element.tool_status,
                                    offline_tool_message: res.data.infos.offline_tool_message,
                                    click_title: true,
                                    showDescription: true,
                                },
                                created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                            });
                        });
                        setTimeout(() => {
                            this.$root.$refs.ClientChatOpened.messages.push({
                                id: 1,
                                cucd_id: null,
                                type: 'FAQ_ROBOT',
                                content: {
                                    id: 1,
                                    title: this.$t('bs-if-none-of-the-options-helped-you-can-open'),
                                    description: 'off',
                                    tool_status: 1,
                                    click_title: false,
                                    showDescription: false,
                                    reset_chat: true,
                                    // numberTools: tools.result3.topNumberTools,
                                },
                                created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                            }); 
                        }, 200);
                        setTimeout(() => {
                            const chatElement = document.getElementById('scrollable');
                            chatElement.scrollTop = chatElement.scrollHeight;
                        }, 200);
                    })
                    .catch(err => {
                        console.error(err); 
                    });
                });
            }else{
                // this.$root.$refs.ClientChatOpened.messages = [];
                this.$root.$refs.ClientChatOpened.showchatCustom = false;
                this.$root.$refs.ClientChatOpened.is_faqRobot = false;
                this.$root.$refs.ClientChatOpened.saveAllDada = true;
                this.$root.$refs.ClientChatOpened.openEmptyChat();  
            }
            
        },
        exitContinue(bool){
            this.$store.state.skipGetFaqRobot = 1;
            this.$store.state.skipGetFaqRobotID = 1;
            if(!this.is_clickable){
                return;
            }
            if(bool){
                this.$root.$refs.ClientChatOpened.messages.pop();
                this.$root.$refs.ClientChatOpened.faqRobot(true);
            }else{
                this.$router.push({name: 'chat'});
                this.$root.$refs.ClientChatOpened.messages = [];
            }
        },
        saveEvaluation(item, bool){
            if(!this.is_clickable){
                return;
            }
            this.showEvaluate = false;
            axios.post(`${this.$store.state.baseURL}/client/set-faq-user`,{
                id: item.id,
                bool: bool,
            }).then(({data}) => {
                // REMOVENDO ULTIMO ELEMENTO DO ARRAY A AVALIAÇÃO
                this.$root.$refs.ClientChatOpened.messages.pop();
                if(bool){
                    this.showEvaluate = true;
                    this.$root.$refs.ClientChatOpened.messages.push({
                        id: 1,
                        cucd_id: null,
                        type: 'FAQ_ROBOT',
                        content: {
                            id: 1,
                            title: this.$t('bs-do-you-want-to-end-the-chat-or-continue-ex'),
                            description: '',
                            click_title: false,
                            showDescription: false,
                            quit_continue: true,
                        },
                        created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                    });
                    
                    // setTimeout(() => {
                    //     this.$router.push({name: 'chat'});
                    // }, 4000);
                }else{
                    this.$root.$refs.ClientChatOpened.messages.push({
                        id: 1,
                        cucd_id: null,
                        type: 'FAQ_ROBOT',
                        content: {
                            id: 1,
                            title: this.$t('bs-allow-us-to-refer-you-to-a-specialist-who'),
                            description: '',
                            click_title: false,
                            showDescription: false,
                        },
                        created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                    });

                    this.$root.$refs.ClientChatOpened.storingChat = false;   
                    this.$root.$refs.ClientChatOpened.showchatCustom = false;
                    this.$root.$refs.ClientChatOpened.is_faqRobot = false;
                    this.$root.$refs.ClientChatOpened.saveAllDada = true;
                    this.$root.$refs.ClientChatOpened.openEmptyChat();  
                }
            }).catch(err => {
                this.$notify({
                    title: this.$t('bs-error-submitting-review'),
                    icon: 'error',
                });
            })
        },
        getChildren(item, bool, subTool = false){
            var vm = this;
            // console.log(item);
            // if(!this.is_clickable){
            //     return;
            // }
            if(bool){
                const url = `${vm.$store.state.baseURL}/get-faq-robot-tools-id`;
                axios.get(url, {
                    params: {
                        id: item.id,
                        skip: vm.$store.state.skipGetFaqRobotID,
                    }
                })
                .then(res => {
                    vm.$store.state.skipGetFaqRobotID = vm.$store.state.skipGetFaqRobotID +1;
                    if(item.tool_status){
                        if(!subTool){
                            vm.$root.$refs.ClientChatOpened.messages.push({
                                id: 1,
                                cucd_id: null,
                                type: 'FAQ_ROBOT',
                                content: {
                                    title: vm.$t('bs-tools-most-wanted-solutions')+' '+item.title,
                                    description: null,
                                    click_title: false,
                                    showDescription: true,
                                },
                                created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                            });
                        }
                        res.data.forEach(function(element, index) {
                            
                            vm.$root.$refs.ClientChatOpened.messages.push({
                                id: element.id,
                                cucd_id: null,
                                type: 'FAQ_ROBOT',
                                content: {
                                    id: element.id,
                                    title: element.title,
                                    description: element.description,
                                    click_title: true,
                                    showDescription: false,
                                },
                                created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                            });
                        });

                        if(!subTool){
                            setTimeout(() => {
                                vm.$root.$refs.ClientChatOpened.messages.push({
                                    id: 1,
                                    cucd_id: null,
                                    type: 'FAQ_ROBOT',
                                    content: {
                                        id: item.id,
                                        title: vm.$t('bs-if-the-above-options-did-not-help-you-can'),
                                        description: null,
                                        tool_status: 1,
                                        click_title: false,
                                        showDescription: false,
                                        sub_not_options: true,
                                        // numberTools: tools.result3.topNumberTools,
                                    },
                                    created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                                });
                            }, 250);
                            setTimeout(() => {
                                const chatElement = document.getElementById('scrollable');
                                chatElement.scrollTop = chatElement.scrollHeight;
                            }, 200);
                        }else{
                            if(res.data.length > 0 && res.data.length == 5){
                                setTimeout(() => {
                                    vm.$root.$refs.ClientChatOpened.messages.push({
                                        id: 1,
                                        cucd_id: null,
                                        type: 'FAQ_ROBOT',
                                        content: {
                                            id: item.id,
                                            title: vm.$t('bs-if-none-of-the-options-helped-you-can-ope'),
                                            description: 'off',
                                            tool_status: 1,
                                            click_title: false,
                                            showDescription: false,
                                            sub_not_options: true,
                                        },
                                        created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                                    });
                                }, 250);
                            }else{
                                setTimeout(() => {
                                    vm.$root.$refs.ClientChatOpened.messages.push({
                                        id: 1,
                                        cucd_id: null,
                                        type: 'FAQ_ROBOT',
                                        content: {
                                            id: item.id,
                                            title: vm.$t('bs-we-have-no-more-options-available'),
                                            description: 'off',
                                            tool_status: 1,
                                            click_title: false,
                                            showDescription: false,
                                            select_tool: true,
                                        },
                                        created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                                    });
                                }, 250);
                            }
                        }
                    }else{
                        // console.log(item);
                        vm.$root.$refs.ClientChatOpened.messages.push({
                            id: 1,
                            cucd_id: null,
                            type: 'FAQ_ROBOT',
                            content: {
                                id: 1,
                                title: item.offline_tool_message,
                                description: '',
                                click_title: false,
                                showDescription: false,
                            },
                            created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                        });
                        vm.showEvaluate = true;
                        vm.$root.$refs.ClientChatOpened.messages.push({
                            id: 1,
                            cucd_id: null,
                            type: 'FAQ_ROBOT',
                            content: {
                                id: item.id,
                                title: vm.$t('bs-was-the-above-answer-helpful-to-you-in-any'),
                                click_title: false,
                                showDescription: false,
                                question_final: true,
                            },
                            created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                        });
                    }
                    if(!subTool){
                        // LOGICÁ PARA TROCAR DE FERRAMENTA - DESATIVADA NO MOMENTO
                        // vm.$root.$refs.ClientChatOpened.pasteTools = item.title;
                    }
                })
                .catch(err => {
                    console.error(err); 
                });
            }else{
                if(!item.question_final){
                    vm.showEvaluate = false;
                    axios.post(`${vm.$store.state.baseURL}/client/set-user-click-count`,{
                        id: item.id,
                        bool: bool,
                    }).then(({data}) => {
                        item.question_final = false;
                    }).catch(err => {
                        vm.$notify({
                            title: vm.$t('bs-error-submitting-review'),
                            icon: 'error',
                        });
                    })

                    vm.$root.$refs.ClientChatOpened.messages.push({
                        id: 1,
                        cucd_id: null,
                        type: 'FAQ_ROBOT',
                        content: {
                            id: item.id,
                            title: item.title,
                            description: item.description,
                            click_title: false,
                            showDescription: true,
                        },
                        created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                    });

                    vm.$root.$refs.ClientChatOpened.messages.push({
                        id: 1,
                        cucd_id: null,
                        type: 'FAQ_ROBOT',
                        content: {
                            id: item.id,
                            title: this.$t('bs-was-the-above-answer-helpful-to-you-in-any'),
                            click_title: false,
                            showDescription: false,
                            question_final: true,
                        },
                        created_at: moment.utc(new Date()).format('YYYY-MM-DD HH:mm:ss'),
                    });
                }
            }

            setTimeout(() => {
                const element = document.getElementById('scrollable');
                element.scrollTop = element.scrollHeight; 
            }, 150);

        }
    },
}
</script>

<style scoped>
    .custom-button{
        width: 100%;
    }
    .dpg{
        display: grid !important;
    }
    .clickable{
        color: #7abdff;
        text-decoration: underline;
    }
    .ml-cc{
        margin-left: 2.5rem;
        /* background-color: red; */
    }
    .bubble-left.b-l.bubble-left:not(.ticket-msg):after {
        border: 0 !important;
        background-color: red !important;
    }

    .faq-title{
        color: #1a8cff !important;
        cursor: pointer;
        font-size: 0.875rem;
        font-weight: 500;
    }


    .caret {
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    }
</style>
