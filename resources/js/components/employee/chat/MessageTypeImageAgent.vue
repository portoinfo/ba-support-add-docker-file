<template>
    <div
        class="grid-container"
        :class="{
            'incognito-background': message.hidden_for_client,
            'client': message.company_user_company_department_id === null,
        }"
    >
        <div class="item1">
            <img
                v-if="message.hidden_for_client"
                src="images/icons/chat/eye_active_message.svg"
            />
            {{ name(message) }}
        </div>
        <div class="item2">
            <gravatar
                :email="email(message)"
                :status="$status.get(id(message))"
                size="32px"
                :name="name(message)"
                :color="color(message)"
                :ba_acct_data="accountData(message)"
            />
        </div>
        <div class="item3" v-viewer>
            <img class="img-message" :src="getFile(message)" />
        </div>
        <div class="item4">{{ formatTime(message.created_at) }}</div>
    </div>
</template>

<script>
export default {
  props: {
    message: Object,
    formatTime: "",
  },
  methods: {
    getFile(message) {
      return `chat/files/${message.chat_id}/${JSON.parse(message.content).unique_name}`;
    },
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
    }
  },
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
    color: #707070;
    font-size: 16px;
    font-stretch: 100%;
    font-weight: 600;
    text-rendering: optimizeLegibility;
    -webkit-font-feature-settings: "kern" 1;
    line-height: 19px;
    padding-bottom: 5px;
    padding-right: 5px;
    padding-left: 5px;
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

.item3 img {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border: 1px solid rgba(215, 222, 230, 0.2);
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
</style>
