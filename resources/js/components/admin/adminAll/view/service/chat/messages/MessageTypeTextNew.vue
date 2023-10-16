<template>
    <div class="ba-mg-t-1">
        <div v-if="comp_user_comp_depart_id_current != null" class="ba-flex ba-gp-1 ba-jc-fe ba-mg-t-1" >
            <div class='ba-card'>
                <div class="output ql-snow">
                    <div class="ql-editor" v-viewer v-html="message.content"></div>
                </div>
            </div>
            <div class="">
                <gravatar-new 
                size="48px" 
                :cEmail="$cleanEmail(message.client_email)" 
                :id="message.client_id"
                :name="message.client_name"
                :ba_acct_data="null"
                ></gravatar-new>
            </div>
        </div>
        <div v-else class="ba-flex ba-gp-1 ba-mg-t-1" >
            <div>
                <gravatar-new 
                size="48px" 
                :cEmail="$cleanEmail(message.client_email)" 
                :id="message.client_id"
                :name="message.client_name"
                :ba_acct_data="null"
                ></gravatar-new>
            </div>
            <div class='ba-card'>
                <div class="output ql-snow">
                    <div class="ql-editor" v-viewer v-html="checkIsImg(message.content)"></div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import iconsCustom from '../../../../util/icons/iconsCustom.vue';
import GravatarNew from "../../../../util/gravatar/GravatarNew.vue";
import Vue from 'vue';
import Viewer from 'v-viewer';
// import Quill from 'quill';
// import { quillEditor } from 'vue-quill-editor';
// import { container, ImageExtend, QuillWatch } from 'quill-image-extend-module';
// import BlotFormatter from 'quill-blot-formatter';
// import ImageCompress from 'quill-image-compress';
// import AutoLinks from 'quill-auto-links';
Vue.use(Viewer);
export default {
 components:{
        iconsCustom,
        GravatarNew,
        // quillEditor,
    },
    props: {
        comp_user_comp_depart_id_current: String,
        message: Object,
        formatTime: "",
    },
    created(){
        // Quill.register('modules/blotFormatter', BlotFormatter);
        // Quill.register('modules/ImageExtend', ImageExtend)
        // Quill.register('modules/imageCompress', ImageCompress);
        // Quill.register('modules/autoLinks', AutoLinks);
        // Quill.register('modules/actions', function() {});
    },
    methods: {
        checkIsImg(aux){
            // Crie um elemento temporário para conter o conteúdo HTML
            var tempElement = document.createElement('div');
            tempElement.innerHTML = aux;

            // Verifique se o elemento contém uma tag <img>
            var hasImage = tempElement.querySelector('img') !== null;

            // Exemplo de como usar a variável hasImage
            if (hasImage) {
                return aux.replace(/(chat)/g, '/$1');
            } else {
                return aux;
            }
        }
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

<style lang="scss" scoped>
  .example {
    display: flex;
    flex-direction: column;
    .output {
      width: 100%;
      margin: 0;
      border: 1px solid #ccc;
      overflow-y: auto;
      resize: vertical;

      &.code {
        padding: 1rem;
      }

      &.ql-snow {
        border-top: none;
      }
    }
  }
</style>
