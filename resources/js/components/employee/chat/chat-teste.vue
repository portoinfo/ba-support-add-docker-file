<template>
  <div class="container h-50 d-inline-block" style="margin-top: 15px">
    <hr />
    <div class="card h-100">
      <div class="card-body">
        <h5 class="card-title">{{$t('bs-chat-test-client').toUpperCase()}}</h5>
        <div v-for="(message, index) in chat_history" :key="index">
          <h6>
            {{ message.user_name ? message.user_name : message.client_name }}:
            <span class="card-subtitle mb-2 text-muted">{{message.content}}</span>
          </h6>
        </div>
      </div>
    </div>
    <hr />
    <textarea
      v-model="newMessage"
      type="text"
      name="message"
      class="form-control"
      cols="100"
      rows="5"
    ></textarea>
    <hr />
    <button class="btn btn-primary">{{$t('bs-send-message').toUpperCase()}}</button>
  </div>
</template>

<script>
export default {
  props: {},
  data() {
    return {
      chat_history: [],
    };
  },
  created() {
    this.getChatHistory("SFRSOUg3VjZNWkpZNTBrZkRvcldGUT09");
    window.Echo.join("chat")
      /*
      .here((user) => {
        this.users = this.user;
      })
      */
      .listen("MessageSent", (event) => {
        this.chat_history.push(event.message);
      });
    /*
      .listenForWhisper("typing", (e) => {
        this.activeChat = true;

        if (this.activeChat) {
          this.arrayChat.indexOf(e.meeting) === -1
            ? this.arrayChat.push({ meeting: e.meeting, user: e.user.name })
            : "";
        }

        if (this.meeting_id == e.meeting) {
          this.activeUser = e.user;
        }

        if (this.typingTimer) {
          clearTimeout(this.typingTimer);
        }
        this.typingTimer = setTimeout(() => {
          this.activeUser = false;
          this.activeChat = false;

          Array.prototype.remove = function () {
            var what,
              a = arguments,
              L = a.length,
              ax;
            while (L && this.length) {
              what = a[--L];
              while ((ax = this.indexOf(what)) !== -1) {
                this.splice(ax, 1);
              }
            }
            return this;
          };
          this.arrayChat = [];
        }, 300);
      });
      */
  },
  methods: {
    setMessageComponent(type) {
      return this.messageComponent[type];
    },
    setMessageProps(message, index) {
      return {
        message: this.chat_history[index],
      };
      //formatTime: this.formatTime,
      //getTodayHour: this.getTodayHour,
    },

    getChatHistory() {
      axios
        .get("chat_history/getChatHistory", {
          params: {
            id: "SFRSOUg3VjZNWkpZNTBrZkRvcldGUT09",
          },
        })
        .then((response) => {
          this.chat_history = response.data;
        });
    },

    waitingTime(param) {
      let array = param.split("/");

      let date = new Date(array[2], array[1], array[0]);

      let waitingTime = {
        interval: null,
        days: 0,
        hours: 0,
        minutes: 0,
        seconds: 0,
        intervals: {
          second: 1000,
          minute: 1000 * 60,
          hour: 1000 * 60 * 60,
          day: 1000 * 60 * 60 * 24,
        },
      };

      //lets figure out our diffs
      let diff = Math.abs(Date.now() - date.getTime());
      waitingTime.days = Math.floor(diff / waitingTime.intervals.day);
      diff -= waitingTime.days * waitingTime.intervals.day;
      waitingTime.hours = Math.floor(diff / waitingTime.intervals.hour);
      diff -= waitingTime.hours * waitingTime.intervals.hour;
      waitingTime.minutes = Math.floor(diff / waitingTime.intervals.minute);
      diff -= waitingTime.minutes * waitingTime.intervals.minute;
      waitingTime.seconds = Math.floor(diff / waitingTime.intervals.second);

      return (
        waitingTime.days +
        " " +
        this.$t('bs-days') +
        waitingTime.hours +
        " " +
        this.$t('bs-hours') +
        waitingTime.minutes +
        " " +
        this.$t('bs-minutes') +
        waitingTime.seconds +
        " " +
        this.$t('bs-seconds')
      );
    },
  },
};
</script>

<style scoped>
textarea {
  font: normal normal bold 14px/18px Muli;
  letter-spacing: 0px;
  color: #707070;
  opacity: 1;
  resize: none;
}

@media screen and (max-width: 1366px) and (min-width: 576px) {
  .ml-custom {
    margin-left: 8px;
  }
}
</style>
