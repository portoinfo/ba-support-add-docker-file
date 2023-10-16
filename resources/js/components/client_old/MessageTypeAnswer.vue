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
        />
    </b-col>
    <b-col class="col-msg-content">
      <div class="card bg-transparent">
        <div class="card-body">
          {{ message.content }}
        </div>
      </div>
    </b-col>
  </b-row>
</template>
<script>
export default {
  data () {
    return {
      avatar_name: ""
    }
  },
  props: {
    message: Object,
    comp_user_comp_depart_id_current: String,
  },
  methods:{
    LI(value){
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
  }
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
  border-radius: 14px !important;
  padding: 0px;
  max-width: 75%;
}

.col-msg-content .card .card-body {
  background: #f2f2f2;
  border-radius: 14px !important;
  font: normal normal normal 16px/20px Muli;
  letter-spacing: 0px;
  color: #707070;
  opacity: 1;
  text-align: justify;
  justify-content: space-between;
  max-width: fit-content;
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
