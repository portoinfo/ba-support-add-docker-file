<template>
    <div
        class="grid-container"
    >
        <div class="item1">
            <img
                v-if="message.hidden_for_client"
                src="images/icons/chat/eye_active_message.svg"
            />
            {{ $t('bs-robot')}}
        </div>
        <div class="item2">
            <gravatar
                email="green"
                size="32px"
                :name="$t('bs-robot')"
                status="false"
            />
        </div>
        <div class="item3">
            <template v-if="message.content_translated == null">
                <span class="ml-1">{{ JSON.parse(this.message.content).title }}</span>
                <br>
                <div class="output ql-snow" v-if="JSON.parse(this.message.content).description != 'off'">
                    <div class="ql-editor px-0 pb-0 pt-2" v-html="JSON.parse(this.message.content).description"></div>
                </div>
            </template>
        </div>
        <div class="item4">{{ formatTime(message.created_at) }}</div>
    </div>
</template>
<script>
export default {
    props: {
        comp_user_comp_depart_id_current: String,
        message: Object,
        formatTime: ""
    },
    created(){
        console.log(this.message);
    },
    methods: {
        email(message) {
            if (message.company_user_company_department_id == null) {
                return message.client_email;
            } else {
                return message.user_email;
            }
        },
        id(message) {
            if (message.company_user_company_department_id == null) {
                return message.client_id;
            } else {
                return message.user_id;
            }
        },
        name(message) {
            if (message.company_user_company_department_id == null) {
                return this.$t(message.client_name);
            } else {
                return message.user_name;
            }
        },
        color(message) {
            if (message.company_user_company_department_id == null) {
                return "light";
            } else {
                return "primary";
            }
        },
        accountData(message) {
            if (message.company_user_company_department_id == null) {
                return message.builderall_account_data;
            } else {
                return null;
            }
        },
        isRichText(str) {
            let tag         = str.slice(0, 1) == '<'    && str.slice(-1) == '>';
            let paragraph   = str.slice(0, 2) == '<p'   || str.slice(-4) == '</p>';
            let list        = str.slice(0, 3) == '<ul'  || str.slice(-5) == '</ul>';
            let code        = str.slice(0, 4) == '<pre' || str.slice(-6) == '</pre>';
            
            return tag && (paragraph || list || code);
        }
    }
};
</script>

<style scoped>

.item1 {
    grid-area: name;
    color: #0080fc;
    font-size: 15px;
    font-stretch: 100%;
    font-weight: 800;
    text-rendering: optimizeLegibility;
    line-height: 22px;
    padding-left: 5px;
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
    /* color: #707070;
    font-size: 0.9rem;
    font-stretch: 100%;
    font-weight: 600;
    text-rendering: optimizeLegibility;
    -webkit-font-feature-settings: "kern" 1;
    line-height: 19px;
    padding-bottom: 5px;
    padding-right: 5px;
    padding-left: 5px;
    word-break: break-word; */
}

.client .item3 {
    color: #707070;
    font-size: 0.9rem;
    font-stretch: 100%;
    font-weight: 600;
    text-rendering: optimizeLegibility;
    -webkit-font-feature-settings: "kern" 1;
    line-height: 19px;
    word-break: break-word;
}

.item3 .ql-editor {
    padding-top: 0px !important;
    padding-right: 0px !important;
    padding-left: 5px !important;
    padding-bottom: 5px !important;
    overflow: hidden !important;
}

.item4 {
    grid-area: time;
    text-align: right;
    color: #6e6e6e;
    opacity: 1;
    font-size: 11px;
    line-height: 20px;
    text-rendering: optimizeLegibility;
    font-weight: 700;
    padding-right: 5px;
}

.grid-container {
    display: grid;
    grid-template-areas:
        "gravatar name time"
        "gravatar content content";
    grid-template-columns: 45px auto 60px;
    border-top: 1px solid rgba(215, 222, 230, 0.2);
}

a {
    font-style: italic !important;
}

.incognito-background {
    background-color: #ff9f5f59 !important;
}

.incognito-background .item1 {
    color: #ff6600 !important;
}

.incognito-background .item1 {
    color: #ff6600 !important;
}

.incognito-background .item3 {
    color: #333333 !important;
}

.client .item1 {
    color: #333333;
}

.incognito-background.grid-container {
    border-top: 1px solid rgba(255, 102, 0, 0.1);
}

@media screen and (max-width: 992px) {
    .item3 {
        font-size: 16px;
    }
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
