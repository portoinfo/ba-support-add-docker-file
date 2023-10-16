<template>
    <center class="btn-send" :class="{'incognito_mode': incognito_mode}">
        <span  class="bs-ico cursor-pointer" @click="callSendMessage">&#xe163;</span>
    </center>
</template>
<script>
export default {
  props: {
    chat: Object,
    incognito_mode: Boolean,
    incognito_id: String,
    sendMessage: "",
  },
  methods:{
    callSendMessage(){
      this.sendMessage();
    },
  }
};
</script>
<style scoped>
.btn-send {
    height:40px;
    width: 40px;
    background: #0080FC;
    border-radius: 100%;
    padding-top: 9px
}

.btn-send.incognito_mode {
    background: #ff6600;
}

.btn-send .bs-ico {
    font-size: 21px;
    color: white;
}
</style>
