<template>
    <div class="grid-container">
        <div class="item3 pt-3 pb-3 pl-5">
            <center>
                {{ $t(message.content) }}
            </center>
        </div>
        <div class="item4">{{ formatTime(message.created_at) }}</div>
    </div>
</template>

<script>
export default {
  props: {
    message: Object,
    formatTime: "",
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
      show: Boolean
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
    //   if (this.message.content == "bs-the-chat-will-end-due-to-inactivity") {
    // this.checkChatHistory();
    // this.calcProgress(this.message.created_at);
    // this.progressBar();
    //   }
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
.item3 {
    grid-area: content;
    color: #707070;
    font-size: 15px;
    font-stretch: 100%;
    font-weight: 700;
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

.grid-container {
    display: grid;
    grid-template-areas: "content time";
    grid-template-columns: auto 60px;
    border-top: 1px solid rgba(215, 222, 230, 0.2);
}

</style>
