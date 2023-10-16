<template>
    <div class="comments" v-if="chat.show">
        <header class="pr-2 pl-2">
            <div style="display: grid; grid-template: auto / auto  auto;" v-if="chat.show">
                <template v-if="!isMobile">
                    <div class="text-left d-table">
                        <b v-if="!info_minimized" class="d-table-cell align-middle">{{ $t('bs-comments') }}</b>
                    </div>
                    <div v-if="showChat" class="text-right">
                        <span  v-if="!info_minimized" class="bs-ico cursor-pointer" @click="info_minimized = true; $root.$refs.FullTicket2.rs_mouse = 'leave'">&#xe931;</span>
                        <span  v-else class="bs-ico cursor-pointer pt-1" @click="info_minimized = false; $root.$refs.FullTicket2.rs_mouse = 'over'">&#xe8f4;</span>
                    </div>
                    <div v-else class="text-right pt-1">
                        <span class="bs-ico cursor-pointer" @click="chat.show = false; $root.$refs.FullTicket2.rs_mouse = 'leave'">&#xe5cd;</span>
                    </div>
                </template>
            </div>
        </header>
        <div class="main" id="content-scroll-comments" v-if="comments && comments.length > 0 && loaded">
            <div class="grid-container pt-1" v-for="(item, index) in comments" :key="index">
                <div class="item1">
                    {{ item.name !== null ? item.name : $t('bs-unknown') }} 
                    <span 
                    @click="copyUrl(item.comment)"
                    class="bs-ico copycustom caret">&#xe14d;</span>
                </div>
                <div class="item2">
                    <gravatar
                        :email="item.email !== null ? item.email : ''"
                        :status="$status.get(item.id)"
                        size="40px"
                        :name="item.name !== null ? item.name : $t('bs-unknown')"
                    />
                </div>
                <div id="input-1" class="item3 pr-1" v-linkified>
                    {{ item.comment }} 
                </div>
                <div class="item4">{{ UTCtoClientTZ2(item.created_at) }}</div>
            </div>
        </div>
        <div class="main d-table text-center" v-else>
            <div class="d-table-cell align-middle pr-2 pl-2">
                <span class="item3">{{ $t('bs-no-comments-added-to-this-ticket') }}</span>
            </div>
        </div>
        <div class="footer pl-2 pr-2" v-if="!isMobile">
            <b-button @click="$root.$refs.FullTicket2.showComments = true" size="sm" class="w-100 text-center" variant="light" v-b-modal.modal-add-comment>
                <span class="bs-ico mr-2" style="color: #00C38E;">&#xe266;</span>
                <span style="color: #333333; padding-top: 1px;">{{ $t('bs-add') }}</span>
            </b-button>
        </div>

        <b-modal id="modal-add-comment" centered :title="$t('bs-comment')" @ok="handleOk" @cancel="openComments" @close="openComments" @hidden="resetModal">
            <div>
                <form ref="form" @submit.stop.prevent="handleSubmit">
                    <b-form-textarea
                        id="textarea"
                        v-model="comment"
                        :placeholder="$t('bs-type-here')"
                        rows="3"
                        max-rows="6"
                        required
                    ></b-form-textarea>
                </form>
            </div>
        </b-modal>
    </div>
</template>

<script>
export default {
    props: {
        chat: Object,
        info_minimized: Boolean,
        showChat: Boolean,
        user: Object,
        isMobile: Boolean,
    },
    data() {
        return {
            comments: Array,
            loaded: false,
            comment: '',
            isActive: false,
        }
    },
    created() {
        this.$root.$refs.TicketComments = this;
        this.getComments().then(() => {
            this.joinOnChannel();
            this.scrollToBottom();
        });
    },
    destroy() {
    },
    methods: {
        copyUrl(text) {
            const el = document.createElement('textarea');  
            el.value = text;                                 
            el.setAttribute('readonly', '');                
            el.style.position = 'absolute';                     
            el.style.left = '-9999px';                      
            document.body.appendChild(el);                  
            const selected =  document.getSelection().rangeCount > 0  ? document.getSelection().getRangeAt(0) : false;                                    
            el.select();                                    
            document.execCommand('copy');                   
            document.body.removeChild(el);                  
            if (selected) {                                 
            document.getSelection().removeAllRanges();    
            document.getSelection().addRange(selected);   
            }
        },
        joinOnChannel() {
            /** begin */
            const channel = `ticket-comment`;
            const event = `TicketCommentUpdate`;
            var vm = this;
            /** join to the channel and listen events */
            Echo.leave(`${channel}.${this.chat.id}`);
            Echo.join(`${channel}.${this.chat.id}`).listen(
                event,
                e => {
                    if (e.message.ticket_id == this.chat.id) {
                        this.getComments();
                    }
                   
                    // vm.updateComments(e.message).then(() => {
                    //     vm.scrollToBottom();
                    // });
                }
            );
        },
        updateComments(event) {
            return new Promise((resolve, reject) => {
                if (event.ticket_id == this.chat.id) {
                    this.comments.push(event)
                    resolve();
                }
            });
        },
        checkFormValidity() {
            const valid = this.$refs.form.checkValidity()
            return valid
        },
        handleOk(bvModalEvt) {
            // Prevent modal from closing
            bvModalEvt.preventDefault()
            // Trigger submit handler
            this.handleSubmit()
            this.$root.$refs.FullTicket2.showComments = false;
        },
        handleSubmit() {
            // Exit when the form isn't valid
            if (!this.checkFormValidity()) {
                return
            }
            // Push the name to submitted names
            this.sendComment().then(() => {
                // Hide the modal manually
                this.$nextTick(() => {
                    this.$bvModal.hide('modal-add-comment')
                    this.$bvModal.show('modal-comments-mobile')
                })
            });
        },
        openComments() {
            this.$bvModal.show('modal-comments-mobile');
            this.$root.$refs.FullTicket2.showComments = false;
        },
        resetModal() {
            this.$root.$refs.FullTicket2.showComments = false;
        },
        sendComment() {
            return new Promise((resolve, reject) => {
                var vm = this;
                var url = "set-ticket-comments";
                var params = {
                    'comment': vm.comment,
                    'ticket_id': vm.chat.id,
                };

                axios.post(url, params)
                .then(res => {
                    vm.comment = "";
                    resolve();
                })
                .catch(err => {
                    console.error(err);
                })
            });
        },
        getComments() {
            return new Promise((resolve, reject) => {
                var vm = this;
                vm.loaded = false;
                var url = "get-ticket-comments";
                var params = { params: {'ticket_id': vm.chat.id}};

                axios.get(url, params)
                .then(res => {
                    vm.comments = res.data.result;
                    vm.loaded = true;
                    resolve();
                })
                .catch(err => {
                    console.error(err);
                })
            });
        },
        scrollToBottom() {
            // var element = document.getElementById('content-scroll-comments');
            // if(element) element.scrollTop = element.scrollHeight - element.clientHeight;

            // var element = document.getElementById('modal-comments-mobile___BV_modal_body_');
            // if(element) element.scrollTop = element.scrollHeight - element.clientHeight;
        },
        UTCtoClientTZ2(value = null){
            try {
                if(value === null) {
                    return ''
                } else {
                    let h_format = moment(value, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD HH:mm:ss");
                    let datetime = h_format.split(" ");
                    let date = datetime[0];
                    let time = datetime[1];
                    let date_split = date.split("-");
                    let time_split = time.split(":");
                    let year = date_split[0];
                    let month = date_split[1];
                    let day = date_split[2];
                    let hour = time_split[0];
                    let minute = time_split[1];
                    let second = time_split[2];
                    var month_integer = parseInt(month, 10);
                    if (month_integer >= 1) {
                        month_integer--;
                    }
                    let dateUTC = new Date(Date.UTC(year, month_integer, day, hour, minute, second));
                    let converted_time = dateUTC.toLocaleString("pt-BR", {
                        timeZone: this.tz,
                    });

                    var mt = require("moment-timezone");
                    return mt(converted_time, "DD/MM/YYYY HH:mm:ss")
                        .tz(Intl.DateTimeFormat().resolvedOptions().timeZone)
                        .locale(this.user.language)
                        .format('L LTS');
                }
            } catch (error) {
                return '';
            }
		},
    },
    computed: {
        computedChat() {
            return JSON.parse(JSON.stringify(this.chat));
        }
    },
    watch: {
        computedChat: {
            deep: true,
            handler: function(newVal, oldVal) {
                if (newVal.id !== oldVal.id) {
                    const channel = `ticket-comment`;
                    Echo.leave(`${channel}.${oldVal.id}`);
                    this.getComments().then(() => {
                        this.joinOnChannel();
                        this.scrollToBottom();
                    });
                }
            }
        }
    }
}
</script>

<style scoped>
.copycustom{
    float: right;
    font-size: 16px;
    margin-top: 3px;;
}


.caret {
	cursor: pointer;
	-webkit-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
}

.item1 {
    grid-area: name;
    color: #707070;
    font-size: 15px;
    font-stretch: 100%;
    font-weight: 800;
    text-rendering: optimizeLegibility;
    line-height: 22px;
    padding-left: 5px;
    text-overflow: ellipsis;
    overflow: hidden;
}

.item2 {
    grid-area: gravatar;
    display: flex;
    align-items: initial;
    justify-content: center;
    padding-top: 8px;
}
.item3 {
    grid-area: content;
    color: #FFB244;
    font-size: 0.9rem;
    font-stretch: 100%;
    font-weight: 600;
    text-rendering: optimizeLegibility;
    -webkit-font-feature-settings: "kern" 1;
    line-height: 19px;
    padding-right: 5px;
    padding-left: 5px;
    word-break: break-word;
}
.item4 {
    grid-area: time;
    text-align: left;
    color: #6e6e6e;
    opacity: 1;
    font-size: 11px;
    line-height: 20px;
    text-rendering: optimizeLegibility;
    font-weight: 700;
    padding-left: 5px;
}

.grid-container {
    display: grid;
    grid-template-areas:
        "gravatar name name"
        "gravatar content content"
        "gravatar time time";
    grid-template-columns: 50px auto 60px;
    border-top: 1px solid rgba(215, 222, 230, 0.5);
}

a {
    font-style: italic !important;
}

@media screen and (max-width: 992px) {
    .item3 {
        font-size: 16px;
    }
}

</style>
