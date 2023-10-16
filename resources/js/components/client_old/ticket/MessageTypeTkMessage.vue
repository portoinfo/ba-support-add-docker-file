<template>
    <b-card class="mb-3 mt-3 p-3 card-message message-ticket" body-class="p-0">
        <b-card-header header-class="p-0 bg-transparent no-border">
            <b-row no-gutters align-v="center">
                <b-col cols="auto">
                    <span v-if="!email(message)">
                        <b-avatar :variant="message.company_user_company_department_id ? 'secondary' : 'primary'" :text="LI($t(department_name))" size="48px"></b-avatar>
                    </span>
                    <span v-else>
                        <gravatar
                        :email="email(message)"
                        :status="$status.get(id(message))"
                        :name="name(message)"
                        size="53px"
                        :color="color(message)"
                        :ba_acct_data="user.builderall_account_data"
                        />
                    </span>
                </b-col>
                <b-col class="pl-2">
                    <b-row no-gutters align-v="center">
                        <b-col cols="12" class="person-name">{{$t(name(message))}}</b-col>
                        <b-col cols="12" class="occupation">{{ checksubtitle() }}</b-col>
                    </b-row>
                </b-col>
            </b-row>

        </b-card-header>

        <b-card-body v-if="is_show_robot" body-class="text">
            <div class="output ql-snow">
                <div class="ql-editor" v-viewer v-html="content"></div>
            </div>
        </b-card-body>

        <b-card-body v-else body-class="text">
            <div class="output ql-snow">
                <div class="ql-editor" v-viewer v-html="content.text"></div>
            </div>
            <template v-for="(item, idx) in content.children">
                <span v-if="item.selected" :key="idx">
                    <b-button class="mt-1" variant='primary'>{{ item.text }}</b-button>
                </span>
            </template>
        </b-card-body>

        <b-card-footer footer-class="p-0 bg-transparent no-border">
            <b-row no-gutters class="mx-4" align-v="center">
                <b-col class="attachment-container">
                    <div
                        v-for="(attachment, index) in attachments"
                        :key="index"
                        :class="[ 'attachment m-1', message.company_user_company_department_id ? 'bg-secondary' : 'bg-primary']"
                        @click="getFile(message.chat_id, attachment.unique_name)"
                    >{{attachment.original_name}}</div>
                </b-col>
                <b-col cols="auto" class="message-date">{{ UTCtoClientTZ2(message.created_at, tz) }}</b-col>
            </b-row>
        </b-card-footer>
    </b-card>
</template>

<script>
export default {
    name:  'message-type-tk-message',
    props: {
        user: Object,
        message: Object,
        language: String,
        department_name: String,
    },
    data () {
        return {
            avatar_name: "",
            tz: "",
            is_show_robot: true,
        }
    },
    computed: {
        attachments: function(){
            if(this.message.type == 'TEXT') { // somente texto
                return []
            } else if(this.message.type == 'IMAGE') { // imagem modelo antigo (imagem em mensagem separada)
                return [
                    this.message.content
                ]
            } if(this.message.type == 'FILE') {
                if(this.message.content.original_name != undefined && this.message.content.original_name != null) { // modelo antigo de anexo (arquivo em mensagem separada)
                    return [
                        this.message.content
                    ]
                } else { // modelo novo de anexo
                    return this.message.content.files
                }
            } else { // outros tipos
                return []
            }
        },
        content: function() {
            this.is_show_robot = true;
            if(this.message.type == 'TEXT') { // somente texto
                return this.message.content
            } else if(this.message.type == 'FILE') {
                if(this.message.content.original_name != undefined && this.message.content.original_name != null) { // modelo antigo de anexo (arquivo em mensagem separada)
                    return ''
                } else { // modelo novo de anexo
                    return this.message.content.message
                }
            } else if(this.message.type == 'ROBOT') {
                this.is_show_robot = false;
                return this.message.content


            } else { // outros tipos
                return ''
            }
        }
    },
    methods:{
        checksubtitle(){
            if(this.message.company_user_company_department_id){
                if(this.message.department){
                    return this.$t(this.message.department);
                }else{
                    return this.$t(this.department_name);
                }
            }else{
                return this.$t('bs-client');
            }
        },
        LI(value){
            return value.substr(0, 2);
        },
        name(message) {
            let name = "";
            if (message.name) {
                name = message.name;
            } else if (message.user_name) {
                name = message.user_name;
            } else if (message.client_name) {
                name = message.client_name;
            }
            this.avatar_name = name;
            return name;
        },
        email(message) {
            // console.log(message);
            if (message.user_email) {
                return message.user_email;
            }else if (message.company_user_company_department_id == null) {
                return message.client_email;
            } else {
                return message.email;
            }
        },
        id(message) {
            if (message.company_user_company_department_id == null) {
                return message.client_id;
            } else {
                return message.user_id;
            }
        },
        color(message) {
            if (message.company_user_company_department_id == null) {
                return "light";
            } else {
                return "primary";
            }
        },
        UTCtoClientTZ2(h, tz) {
            let h_format = moment(h, "YYYY-MM-DD HH:mm:ss").format(
                "YYYY-MM-DD HH:mm:ss"
            );
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
            let dateUTC = new Date(
                Date.UTC(year, month_integer, day, hour, minute, second)
            );
            let converted_time = dateUTC.toLocaleString("pt-BR", {
                timeZone: tz,
            });
            var mt = require("moment-timezone");
            return mt(converted_time, "DD/MM/YYYY HH:mm:ss")
                .tz(tz)
                .locale(this.user.language)
                .format('L LT');
            },
        getFile(chat_id, unique_name) {
            window.open(`chat/files/${chat_id}/${unique_name}`, '_blank');
        },
    },
    created() {
        this.name(this.message)
        this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
    }
}
</script>

<style lang="scss" scoped>
    .card-message{
        border-radius: 14px !important;
        margin: 15px 32px !important;
    }
    .no-border{
        border: none;
    }
    .person-name{
        font-family: Muli;
        font-weight: bold;
        font-size: 19px;
        color: #434343;
    }
    .occupation{
        font-family: Muli;
        font-size: 15px;
        color: #434343;
    }
    .text{
        // font-family: Muli;
        // font-size: 16px;
        // color: #707070;
        // text-align: justify;
        // padding: 20px 56px;
        // white-space: pre-line;
        padding: 0px !important;
        padding-left: 56px !important;
    }
    .attachment{
        border-radius: 5px;
        box-shadow: 0.62px 0.79px 2px #1E120D1A;

        color: #F8FAFD;
        font-family: Muli;
        font-size: 14px;
        font-weight: 800;
        display: inline-block;
        &:hover{
            cursor: pointer;
        }
    }
    .message-date{
        color: #707070;
        font-family: Muli;
        font-size: 19px;
        text-align: right;
    }

</style>
