<template>
<div>
  <b-row class="mb-3 mt-3">
    <b-col cols="12" class="msg-event text-center">
      <strong>{{ $t(user.name) }}: {{ $t('bs-answered-the-questionnaire') }}</strong>
    </b-col>
  </b-row>
  <div v-for="(row, index) in questionary" :key="index">
  <b-row class="mb-3 mt-3" v-if="row.answer">
    <b-col cols="12" class="col-msg-name">
      <h4 class="msg-name-employee m-0">
       {{ department_name }}
      </h4>
    </b-col>
    <b-col class="col-msg-img">
      <gravatar
          email="null"
          status="false"
          size="53px"
          :name="department_name"
          color="light"
        />
    </b-col>
    <b-col class="col-msg-content">
      <div class="card bg-transparent">
        <div class="card-body">
          {{ $t(row.question) }}
        </div>
      </div>
    </b-col>
  </b-row>
  <b-row class="mb-3 mt-3" v-if="row.answer">
    <b-col cols="12" class="col-msg-name">
      <h4 class="msg-name m-0">
        {{  $t(user.name) }}
      </h4>
    </b-col>
    <b-col class="col-msg-img">
        <gravatar
          :email="user.email"
          :status="$status.get(user.id)"
          size="53px"
          :name="$t(user.name)"
          :ba_acct_data="user.builderall_account_data"
        />
    </b-col>
    <b-col class="col-msg-content">
      <div class="card bg-transparent">
        <div class="card-body">
          {{ row.answer }}
        </div>
      </div>
    </b-col>
  </b-row>
  </div>
</div>
</template>

<script>
export default {
  props: {
    questionary: Array,
    department_name: '',
    user: ''
  },
  data() {
    return {}
  },
  methods:{
    LI(value){
      if(value == null){
        return 'LI';
      }
      return value.substr(0, 2);
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
.msg-name-employee {
  font: normal normal bold 15px/31px Muli;
  letter-spacing: 0px;
  color: black;
}
.msg-name {
  font: normal normal bold 15px/31px Muli;
  letter-spacing: 0px;
  color: #0294ff;
}
.msg-event strong {
  font: normal normal 800 15px/31px Muli;
  letter-spacing: 0px;
  color: #707070;
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
