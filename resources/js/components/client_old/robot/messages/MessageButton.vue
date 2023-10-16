<template>
    <b-row class="mb-3 mt-3">
        <b-col cols="12" class="col-msg-name">
        <h4 class="msg-name m-0">
            {{ $t('bs-robot') }}
        </h4>
        </b-col>
        <b-col class="col-msg-img">
        <gravatar
            email="red"
            status="false"
            size="53px"
            :name="$t('bs-robot')"
        />
        </b-col>
        <b-col class="col-msg-content">
        <div class="card bg-transparent">
            <div class="p-2">
                <b-button @click="$root.$refs.ChatClient.executeRobotAction(message, null)" variant="primary" size="sm" class="mb-1 w-100 font-weight-bold">
                    {{ message.text }}
                </b-button>
            </div>
        </div>
        </b-col>
    </b-row>
</template>

<script>
export default {
    props: {
        message: Object
    },
}
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

