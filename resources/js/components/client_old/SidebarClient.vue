<template>
  <div>
    <div
      :class="[
        'bb-sidebar d-none d-sm-flex flex-column justify-content-between',
        !sidebar_is_mini ? 'bb-mini' : '',
      ]"
    >
      <b-nav vertical>
        <b-nav-item
          v-for="(menu, k) in menus"
          :key="k"
          :href="menu.href"
          :active="isActive(current, menu.href)"
          :id="`sidebar-menu-${k}`"
        >
          <b-tooltip
            :disabled="!sidebar_is_mini"
            :target="`sidebar-menu-${k}`"
            placement="right"
          >
            {{ menu.title }}
          </b-tooltip>
          <!-- <vue-material-icon :name="menu.icon" :size="30" /> -->
          <i class="material-icons" :style="{ fontSize: '30px!important' }">{{
            menu.icon
          }}</i>
          <span>{{ menu.title }}</span>
          <span
            v-if="menu.badge"
            v-b-tooltip.hover
            :title="$t('bb-you-have-pending-schedules', { schedules: pending })"
            class="sidebar-badge"
          ></span>
        </b-nav-item>

        <!--         <b-nav-item v-if="current == 'client-chat'">
            <b-tooltip :disabled="!sidebar_is_mini" placement="right">
                Chat
            </b-tooltip>
            <vue-material-icon name="add_circle" :size="30" />
            <span>NOVO CHAT</span>
            </b-nav-item> -->
      </b-nav>

      <b-nav
        vertical
        class="pb-4 pt-3 mt-3 bb-sidebar-bottom"
        v-show="bottom_menus.length"
      >
        <b-nav-item
          v-for="(menu, k) in bottom_menus"
          :key="k"
          :href="menu.href"
          :id="`sidebar-menu-bottom-${k}`"
          :active="menu.routes.includes(current)"
          :target="menu.target"
        >
          <b-tooltip
            :disabled="!sidebar_is_mini"
            :target="`sidebar-menu-bottom-${k}`"
            placement="right"
          >
            {{ menu.title }}
          </b-tooltip>
          <img :src="menu.icon" />
          <span>{{ menu.title }}</span>
          <span
            v-if="menu.badge"
            class="sidebar-badge"
            :style="`background-color: ${menu.badge}`"
          ></span>
        </b-nav-item>
      </b-nav>
    </div>

    <!-- mobile sidebar -->
    <b-sidebar id="sidebar-1" class="bb-sidebar-mobile" shadow backdrop no-header>
      <div class="p-0">
        <div class="sidebar-header text-center">
          <div class="d-flex justify-content-end">
            <b-button-close text-variant="white" v-b-toggle.sidebar-1></b-button-close>
          </div>
          <!-- <img src="/images/Logo-Builderall01.png" width="100" height="100" /> -->
          <div class="d-flex justify-content-between align-items-center mt-3">
            <span class="name">{{ user.name }}</span>
          </div>
        </div>

        <!-- menus -->
        <b-nav vertical>
          <b-nav-item
            v-for="(menu, k) in menus"
            :key="k"
            :href="menu.href"
            :active="isActive(current, menu.href)"
            :id="`sidebar-menu-${k}`"
          >
            <b-tooltip
              :disabled="!sidebar_is_mini"
              :target="`sidebar-menu-${k}`"
              placement="right"
            >
              {{ menu.title }}
            </b-tooltip>
            <!-- <vue-material-icon :name="menu.icon" :size="30" /> -->
            <i class="material-icons" :style="{ fontSize: '30px!important' }">{{
              menu.icon
            }}</i>
            <span>{{ menu.title }}</span>
            <span
              v-if="menu.badge"
              v-b-tooltip.hover
              :title="$t('bb-you-have-pending-schedules', { schedules: pending })"
              class="sidebar-badge"
            ></span>
          </b-nav-item>
        </b-nav>

        <!-- bottom menus -->
        <b-nav vertical class="pb-4 pt-3 mt-5 bb-sidebar-bottom">
          <b-nav-item
            v-for="(menu, k) in bottom_menus"
            :key="k"
            :href="menu.href"
            :id="`sidebar-menu-bottom-${k}`"
            :active="menu.routes.includes(current)"
            :target="menu.target"
          >
            <b-tooltip
              :disabled="!sidebar_is_mini"
              :target="`sidebar-menu-bottom-${k}`"
              placement="right"
            >
              {{ menu.title }}
            </b-tooltip>
            <img :src="menu.icon" />
            <span>{{ menu.title }}</span>
            <span
              v-if="menu.badge"
              class="sidebar-badge"
              :style="`background-color: ${menu.badge}`"
            ></span>
          </b-nav-item>
        </b-nav>
      </div>
    </b-sidebar>
  </div>
</template>

<script>
import { mapState, mapMutations } from "vuex";
export default {
  data() {
    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
      param_url: "",
      menus: [
        {
          title: "Chat",
          href: "client-chat" + this.param_url,
          routes: ["chat"],
          icon: "forum",
          function: "openChat",
        },
        {
          title: "Ticket",
          href: "client-ticket" + this.param_url,
          routes: ["ticket"],
          icon: "email",
          function: "openTicket",
        },
      ],
      bottom_menus: [],
      //sidebar_is_mini: true,
    };
  },
  props: {
    current: String,
    is_admin: String,
    user: Object,
    csucid: String,
    csid: String,
    csid2: String,
    csname: String,
    cslogo: String,
    show_logout: String,
    restriction_client: Array,
    session_dtype: "",
  },
  created() {
    var refreshIntervalId = setInterval(() => {
      if (!navigator.onLine) {
        document.location.reload();
        clearInterval(refreshIntervalId);
      }
    }, 250);
    this.notificationConnect(this.csid, this.csucid);
    this.$store.state.company = this.csid;
    this.online();
  },
  mounted() {
    if (this.show_logout == "true") {
      this.bottom_menus.push({
        title: this.$t("bs-exit"),
        href: "logout-client",
        routes: ["login"],
        icon: "images/logout2.svg",
      });
      if (this.session_dtype !== "null") {
        var session_dtype = JSON.parse(this.session_dtype);
        session_dtype.forEach((element) => {
          if (element === "checkout") {
            document.getElementById("sidebar-menu-1").remove();
          }
        });
      }
    }

    this.$store.state.csid = this.csid2;

    //MANTEM OS PARAMETROS NA URL
    var query = location.search.slice(1);
    if (query == "") {
      this.param_url = "";
    } else {
      this.param_url = "?" + query;
    }

    if (
      !this.restriction_client[0].ticket_hide &&
      !this.restriction_client[0].chat_hide
    ) {
      this.menus = [
        {
          title: "Chat",
          href: "client-chat" + this.param_url,
          routes: ["chat"],
          icon: "forum",
          function: "openChat",
        },
        {
          title: "Ticket",
          href: "client-ticket" + this.param_url,
          routes: ["ticket"],
          icon: "email",
          function: "openTicket",
        },
      ];
    }

    if (this.restriction_client[0].ticket_hide && this.restriction_client[0].chat_hide) {
      this.menus = [];
    }

    if (this.restriction_client[0].ticket_hide && !this.restriction_client[0].chat_hide) {
      this.menus = [
        {
          title: "Chat",
          href: "client-chat" + this.param_url,
          routes: ["chat"],
          icon: "forum",
          function: "openChat",
        },
      ];
    }

    if (this.restriction_client[0].chat_hide && !this.restriction_client[0].ticket_hide) {
      this.menus = [
        {
          title: "Ticket",
          href: "client-ticket" + this.param_url,
          routes: ["ticket"],
          icon: "email",
          function: "openTicket",
        },
      ];
    }
  },
  methods: {
    ...mapMutations(["online"]),
    ...mapMutations(["offline"]),
    isActive(a, b) {
      if (a == b) {
        return true;
      }
    },
    notificationConnect($companyId, $userClientId) {
      Echo.join(`client-notification.${$companyId}.${$userClientId}`).listen(
        "ClientNotification",
        (event) => {
          this.notificationSend(event);
        }
      );
    },
    notificationChat(event) {
      this.notificationSend(event);
    },
    // fazer logica dos tickets
    notificationTicket(event) {
      this.notificationSendTickets(event);
    },
    // função de disparo de notificação
    notificationSend(event) {
      let audio = "";
      audio = new Audio("/media/new-message.mp3");
      audio.play();
      let small_title = `${this.$t(event.notification.title)} #${
        event.notification.number
      }`;
      let options = {
        // O corpo(mensagem) da notificação.
        body: this.$t(event.notification.body),
        // A URL da imagem usada como um ícone da notificação.
        icon: event.notification.icon,
      };

      if (event.notification.body === "bs-the-chat-will-end-due-to-inactivity") {
        this.$snotify.warning(options.body, small_title, {
          timeout: 10000,
          showProgressBar: true,
          closeOnClick: true,
        });
      } else {
        this.$snotify.info(options.body, small_title);
      }

      //this.$snotify.info(options.body, small_title);

      //   if (event.notification.body === "bs-new-message-received") {
      //     audio = new Audio("/media/new-message.mp3");
      //     audio.play();
      //   } else {
      //     audio = new Audio("/media/new-item.mp3");
      //     audio.play();
      //   }
      //   // O título da notificação
      //   let title = `BA-Support | ${this.$t(event.notification.title)} #${
      //     event.notification.number
      //   }`;

      //   let small_title = `${this.$t(event.notification.title)} #${event.notification.number}`;
      //   // As opções da notificação
      //   let options = {
      //     // O corpo(mensagem) da notificação.
      //     body: this.$t(event.notification.body),
      //     // A URL da imagem usada como um ícone da notificação.
      //     icon: event.notification.icon,
      //   };
      //   // A url para onde a notificação irá redirecionar
      //   let url = event.notification.url !== "" ? event.notification.url : false;

      //   // Verifica se o browser suporta notificações
      //   if (!("Notification" in window)) {
      //     console.log("Este browser não suporta notificações de Desktop");
      //   }

      //   // Let's check whether notification permissions have already been granted
      //   else if (Notification.permission === "granted") {
      //     // If it's okay let's create a notification
      //     let n = new Notification(title, options);
      //     audio.play();
      //     this.$snotify.info(options.body, small_title);
      //     if (url) {
      //       n.onclick = function (event) {
      //         event.preventDefault(); // prevent the browser from focusing the Notification's tab
      //         window.open(url);
      //       };
      //     }
      //   }
      //   // Otherwise, we need to ask the user for permission
      //   else if (Notification.permission !== "denied") {
      //     Notification.requestPermission(function (permission) {
      //       // If the user accepts, let's create a notification
      //       if (permission === "granted") {
      //         let n = new Notification(title, options);
      //         audio.play();
      //         this.$snotify.info(options.body, small_title);
      //         if (url) {
      //           n.onclick = function (event) {
      //             event.preventDefault(); // prevent the browser from focusing the Notification's tab
      //             window.open(url);
      //           };
      //         }
      //       }
      //     });
      //   }
    },
    notificationSendTickets(event) {
      let audio = "";
      audio = new Audio("/media/tickets-new-message.mp3");
      audio.play();
      let small_title = `${this.$t(event.notification.title)} #${
        event.notification.number
      }`;
      let options = {
        // O corpo(mensagem) da notificação.
        body: this.$t(event.notification.body),
        // A URL da imagem usada como um ícone da notificação.
        icon: event.notification.icon,
      };

      if (event.notification.body === "bs-the-chat-will-end-due-to-inactivity") {
        this.$snotify.warning(options.body, small_title, {
          timeout: 10000,
          showProgressBar: true,
          closeOnClick: true,
        });
      } else {
        this.$snotify.info(options.body, small_title);
      }
    },
  },
  computed: {
    ...mapState(["sidebar_is_mini"]),
    //this.$store.state.sidebar_is_mini
  },
  destroyed() {
    this.offline();
  },
};
</script>

<style lang="scss" scoped>
@import "./resources/sass/variables";

.bb-sidebar {
  z-index: 999;
  border: none !important;
  position: fixed;
  width: $sidebar-width;
  min-height: calc(100vh - #{$header-height});
  border-right: 1px solid #dedede;
  box-shadow: 0px 0px 9px #26242424;
  background: #fff 0 0 no-repeat padding-box;
  transition: $sidebar-transition;
  &.bb-mini {
    width: $sidebar-width-mini;
    .nav-item .nav-link {
      justify-content: center;
      span:not(.sidebar-badge) {
        display: none;
      }
    }
  }
}

.bb-sidebar,
.bb-sidebar-mobile {
  .nav .nav-item .nav-link {
    padding-left: 18px;
    color: #a5b9d5;
    font: normal normal 600 15px/19px Muli;
    letter-spacing: 0px;
    justify-content: flex-start;
    display: flex;
    align-items: center;
    height: 48px;
    border-left: 3px solid transparent;
    transition: 0.4s;
    position: relative;
    .bbi {
      filter: opacity(0.7) grayscale(0.6);
      transition: inherit;
    }
    span {
      margin-left: 20px;
    }
    &:hover,
    &.active {
      border-left: 3px solid #1d5ef5;
      background-color: #0294ff33;
      color: var(--primary);
      .bbi {
        filter: unset;
      }
      .sidebar-badge {
        border-color: #f4f7fc;
      }
    }
    &.active {
      border-left: 3px solid #1d5ef5;
    }
    .sidebar-badge {
      background: #ffb244;
      position: absolute;
      width: 14px;
      height: 14px;
      border-radius: 100%;
      border: 3px solid #fff;
      left: -7px;
      top: 14px;
    }
  }
}

.bb-sidebar-mobile {
  .bb-sidebar-mobile-header {
    background: url(/images/images/alert/corner.png) left top no-repeat,
      url(/images/images/alert/wave.png) right bottom/100% no-repeat,
      transparent linear-gradient(180deg, #5e81f4 0%, #1665d8 100%);
    /*transparent linear-gradient(180deg, #5e81f4 0%, #1665d8 100%);*/
    display: flex;
    flex-direction: column;
    padding: 20px;
    color: #fff;
    span.name {
      font-size: 19px;
      margin-top: 1rem;
    }
  }
  .nav {
    .nav-item {
      .nav-link {
        /*color: #585858;*/
        i {
          width: 20px;
          height: 20px;
        }
      }
    }
  }
}

.bb-sidebar-bottom {
  border-top: 1px solid #bed1ea;
  height: 154px;
  overflow: hidden;
  .bbi-menu-backoffice {
    height: 19px !important;
  }
}

strong {
  font: normal normal 600 15px/19px Muli;
  letter-spacing: 0px;
  color: #7c94b4;
  opacity: 1;
}

.sidebar-header {
  background: linear-gradient(180deg, #0294ff 0%, #1665d8 100%);

  display: flex;
  flex-direction: column;
  padding: 12px;
  color: #fff;
  span.name {
    font-size: 19px;
    padding-left: 16px;
  }
}

.list-group-item {
  border: none;
  background: transparent;
  font: normal normal 600 15px/19px Muli;
  letter-spacing: 0px;
  color: #7c94b4;
  opacity: 1;
  border-left: 3px solid transparent;
  border-radius: 0px;
}

.list-group-item:hover {
  background: #0294ff33;
  color: #0080fc;
  border-left: 3px solid #1d5ef5;
}

input:focus,
select:focus,
textarea:focus,
button:focus {
  outline: none;
}

input.middle:focus {
  outline-width: 0;
}
</style>
