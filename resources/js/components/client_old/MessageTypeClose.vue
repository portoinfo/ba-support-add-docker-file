<template>
  <b-row class="mb-3 mt-3">
    <b-col cols="12" class="msg-event text-center">
      <hr />
      <strong>
          <i>{{ $t(message.content) }}</i>
      </strong>
      <b-progress
        v-if="show"
        :value="value"
        :max="max"
        class="mx-auto w-75"
        :variant="variant"
        :striped="striped"
        :animated="animated"
      ></b-progress>
      <hr />
    </b-col>
  </b-row>
</template>

<script>
export default {
  props: {
    message: Object,
    index: Number,
    chat_history: Array,
  },
  data() {
    return {
      showProgress: Boolean,
      value: 0,
      max: 60,
      diff: "",
      variant: "danger",
      animated: true,
      striped: true,
      show: Boolean,
    };
  },
  watch: {
    chat_history: function () {
      this.checkChatHistory();
    },
    showProgress() {
      if (this.showProgress) {
        this.variant = "danger";
      } else {
        this.variant = "transparent";
        this.animated = false;
        this.striped = false;
      }
    },
  },
  mounted() {
      if (this.message.content == "bs-the-chat-will-end-due-to-inactivity") {
    this.checkChatHistory();
    this.calcProgress(this.message.created_at);
    this.progressBar();
      }
  },
  methods: {
    calcProgress(created_at) {
      axios.get("/get-diff", {
          params: {
            created_at: created_at
          },
      }).then(({ data }) => {
        this.diff = data
        this.loopOneMinute(60 - data);
      });
    },
    loopOneMinute(diff) {
      if (diff <= 60) {
        let time = diff;
        this.value = this.diff;
        const timeValue = setInterval((interval) => {
          this.value = this.value + 1;
          time = time - 1;
          if (time <= 0) {
            clearInterval(timeValue);
          }
        }, 1000);
      } else {
        this.value = 60;
      }
    },
    progressBar(message) {
        this.show = true;
    },
    checkChatHistory() {
      if (typeof this.chat_history[this.index + 1] !== "undefined") {
        this.showProgress = false;
      } else {
        this.showProgress = true;
      }
    },
  },
};
</script>

<style scoped>
.msg-event strong {
  font: normal normal 800 15px/31px Muli;
  letter-spacing: 0px;
  color: #707070;
}
</style>
