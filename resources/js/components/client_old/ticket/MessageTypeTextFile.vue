<template>
  <!-- criado para teste -->
  <!-- <b-row class="mb-3 mt-3">
    <b-col cols="12" class="col-msg-name">
      <h4 class="msg-name-employee m-0">
        {{ name(message) }}
      </h4>
    </b-col>
    <b-col class="col-msg-img">
      <b-avatar variant="primary ml-5" :text="LI(avatar_name)" size="53px"></b-avatar>
    </b-col>
    <b-col class="col-msg-content">
      <div class="card bg-transparent">
        <div class="card-body cursor-pointer" v-linkified>
          {{ getContent(message) }}
        </div>

          <span v-for="(item, index) in message.content.files">
            <a :href="getFile(item)" target="_blank">{{item.original_name}}</a>
          </span>

      </div>
    </b-col>
  </b-row> --> 
  <div></div>
</template>

<script>
export default {
  data() {
    return {
      avatar_name: "",
    };
  },
  props: {
    message: Object,
  },
  created(){
    this.message.content = JSON.parse(this.message.content);
  },
  methods: {
    getContent(message){
      return this.message.content.message;
    },
    LI(value) {
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
    getFile(item) {
      //console.log(item);
      return `chat/files/${this.message.chat_id}/${item.unique_name}`;
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
.msg-name-employee {
  font: normal normal bold 15px/31px Muli;
  letter-spacing: 0px;
  color: black;
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
  transition-duration: 1s !important;
}
.col-msg-content .card-footer {
  transition-duration: 1s !important;
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
