<template>
  <b-row class="mb-3 mt-3">
    <b-col cols="12" class="col-msg-name">
      <h4 class="msg-name m-0">
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
          :ba_acct_data="message.builderall_account_data"
        />
    </b-col>
    <b-col class="col-msg-content">
      <div @mouseover="mouseover" @mouseleave="mouseleave" class="card bg-transparent">
        <div class="card-body cursor-pointer" v-linkified>
          {{ message.content }}
        </div>
        <div
          v-if="showDateTime"
          class="card-footer bg-transparent border-0 float-right pb-2 pt-2 text-right"
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
    LI(value) {
      return value.substr(0, 2);
    },
    // name(message) {
    //   let name = "";
    //   if (message.name) {
    //     name = message.name;
    //   } else if (message.user_name) {
    //     name = message.user_name;
    //   } else if (message.client_name) {
    //     name = message.client_name;
    //   }
    //   this.avatar_name = name;
    //   return name;
    // },
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
}
.col-msg-content .card-footer {
  font: normal normal normal 16px/17px Muli;
  letter-spacing: 0px;
  color: #707070;
  min-width: 200px;
  font-style: italic;
}
@media only screen and (max-width: 575px) {
  .col-msg-img {
    max-width: 60px !important;
  }
  .col-msg-name {
    padding-left: 80px;
  }
}
</style>
