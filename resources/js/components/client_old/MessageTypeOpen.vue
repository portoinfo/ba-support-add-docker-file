<template>
  <b-row class="mb-3 mt-3">
    <b-col cols="12" class="msg-event text-center">
      <hr>
      <strong><i>{{ $t(message.content) }}</i></strong>
      <hr>
    </b-col>
  </b-row>
</template>

<script>
export default {
  props:{
    message: Object,
  }
};
</script>

<style scoped>
.msg-event strong {
  font: normal normal 800 15px/31px Muli;
  letter-spacing: 0px;
  color: #707070;
}
</style>
