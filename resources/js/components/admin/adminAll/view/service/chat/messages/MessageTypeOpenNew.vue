<template>
    <div class="ba-mg-t-1">
        <div class="ba-flex ba-gp-1 ba-mg-t-1" >
            <div>
                <gravatar-new 
                size="48px" 
                cEmail="robot" 
                :id="19"
                name="Marlos"
                :ba_acct_data="null"
                ></gravatar-new>
            </div>
            <div class='ba-card'>
                <div class="output ql-snow">
                    <div class="ql-editor" v-viewer v-html="message.content"></div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import iconsCustom from '../../../../util/icons/iconsCustom.vue';
import GravatarNew from "../../../../util/gravatar/GravatarNew.vue";
export default {
 components:{
        iconsCustom,
        GravatarNew,
    },
    props: {
        comp_user_comp_depart_id_current: String,
        message: Object,
        formatTime: "",
    },
    mounted(){
    },
    methods: {
        // email(message) {
        //     if (message.company_user_company_department_id == null) {
        //         return message.client_email;
        //     } else {
        //         return message.user_email;
        //     }
        // },
        // id(message) {
        //     if (message.company_user_company_department_id == null) {
        //         return message.client_id;
        //     } else {
        //         return message.user_id;
        //     }
        // },
        // name(message) {
        //     if (message.company_user_company_department_id == null) {
        //         return this.$t(message.client_name);
        //     } else {
        //         return message.user_name;
        //     }
        // },
        // color(message) {
        //     if (message.company_user_company_department_id == null) {
        //         return "light";
        //     } else {
        //         return "primary";
        //     }
        // },
        // accountData(message) {
        //     if (message.company_user_company_department_id == null) {
        //         return message.builderall_account_data;
        //     } else {
        //         return null;
        //     }
        // },
        // isRichText(str) {
        //     let tag         = str.slice(0, 1) == '<'    && str.slice(-1) == '>';
        //     let paragraph   = str.slice(0, 2) == '<p'   || str.slice(-4) == '</p>';
        //     let list        = str.slice(0, 3) == '<ul'  || str.slice(-5) == '</ul>';
        //     let code        = str.slice(0, 4) == '<pre' || str.slice(-6) == '</pre>';
            
        //     return tag && (paragraph || list || code);
        // }
    }
};
</script>

<style scoped>
    .ba-card{
        box-shadow: 1px 1px 4px 0px #00000029 !important;
    }
    .textAgent{
        float:right;
        margin-right: 50px;
    }

    .ql-editor {
        padding-top: 0px !important;
        padding-right: 0px !important;
        padding-left: 5px !important;
        padding-bottom: 5px !important;
        overflow: hidden !important;
    }

</style>

.
