<template>
  <b-row class="mb-3 mt-3">
    <b-col cols="12" class="col-msg-name">
     <h4 v-if="message.company_user_company_department_id === null" class="msg-name m-0">
        {{ name(message) }}
      </h4>
      <h4 v-else class="msg-name m-0" style="color: black">
        {{ name(message) }}
      </h4>
    </b-col>
    <b-col class="col-msg-img">
      <gravatar
          :email="email(message)"
          :status="$status.get(id(message))"
          size="53px"
          :name="name(message)"
          :color="color(message)"
          :ba_acct_data="accountData(message)"
        />
    </b-col>
    <b-col class="col-msg-content">
      <div @mouseover="mouseover" @mouseleave="mouseleave" class="card bg-transparent">
        <div class="card-body cursor-pointer">
          <clazy-load :src="getFile(message)">
            <div v-viewer>
              <img :src="getFile(message)" />
            </div>
            <div class="preloader" slot="placeholder">
              <clip-loader :color="'#A9A9A9'" :size="'40px'" />
            </div>
          </clazy-load>
        </div>
        <div
          v-if="showDateTime"
          class="card-footer bg-transparent border-0 float-right pb-2 pt-0 text-right"
        >
          <span>{{ formatTime(message.created_at) }}</span>
        </div>
      </div>
    </b-col>
  </b-row>
</template>
<script>
export default {
  data() {
    return {
      avatar_name: "",
      showDateTime: false,
    };
  },
  props: {
    message: Object,
    formatTime: "",
    comp_user_comp_depart_id_current: String,
  },
  methods: {
    getFile(message) {
      return `chat/files/${message.chat_id}/${JSON.parse(message.content).unique_name}`;
    },
    LI(value) {
      if (value == null) {
        return "LI";
      }
      return value.substr(0, 2);
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
    },
    mouseover() {
      setTimeout(() => {
        this.showDateTime = true;
      }, 100);
    },
    mouseleave() {
      setTimeout(() => {
        this.showDateTime = false;
      }, 200);
    },
  },
};
</script>

<style scoped>
.card {
  border: none;
  border-radius: 0px;
  padding-bottom: 50px;
}
.col-msg-name {
  padding-left: 120px;
}
.msg-name {
  font: normal normal bold 15px/31px Muli;
  letter-spacing: 0px;
  color: #0294ff;
}
.col-msg-img {
  max-width: 100px !important;
  padding: 0px;
  display: flex;
  align-items: flex-end;
}
.col-msg-content .card {
  background: #f2f2f2 !important;
  border-radius: 14px !important;
  font: normal normal normal 16px/20px Muli;
  letter-spacing: 0px;
  color: #707070;
  opacity: 1;
  text-align: justify;
  justify-content: space-between;
  max-width: fit-content;
  padding: 0px;
  animation: fadein 2s;
  -moz-animation: fadein 2s; /* Firefox */
  -webkit-animation: fadein 2s; /* Safari and Chrome */
  -o-animation: fadein 2s; /* Opera */
}
.col-msg-content .card-footer {
  font: normal normal normal 16px/17px Muli;
  letter-spacing: 0px;
  color: #707070;
  min-width: 200px;
  font-style: italic;
  animation: fadein 2s;
  -moz-animation: fadein 2s; /* Firefox */
  -webkit-animation: fadein 2s; /* Safari and Chrome */
  -o-animation: fadein 2s; /* Opera */
}
@keyframes fadein {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
@-moz-keyframes fadein {
  /* Firefox */
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
@-webkit-keyframes fadein {
  /* Safari and Chrome */
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
@-o-keyframes fadein {
  /* Opera */
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
@media only screen and (max-width: 680px) {
  .col-msg-img {
    max-width: 60px !important;
  }
  .col-msg-name {
    padding-left: 80px;
  }
  img {
    width: 150px !important;
    max-height: 150px !important;
  }
}
img {
  width: 300px;
  max-height: 300px;
  object-fit: cover;
  object-position: 100% 34%;
}
</style>
