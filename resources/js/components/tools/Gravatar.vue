<template>
  <span
    class="b-avatar rounded-circle noselect p-0"
    :class="{'bordervip': vip}"
    :style="gravatarSizeColor()"
    v-if="show"
  >

    <span class="img-txt d-table p-0 pt-1 pb-1" :style="`font-size: calc(${calcPercentSize(40)}px);`">
      <span class="d-table-cell align-middle p-0">{{ LI(name) }}</span>
    </span>
    <span class="img-img">
      <img :src="gravatar" alt="avatar" />
    </span>

    <span
      v-if="show_badge"
      class="b-avatar-badge"
      :class="`badge-${getBadgeStatusVariant()}`"
      :style="`font-size: calc(${calcPercentSize(28)}px);`"
    />
    <span v-if="vip" class="b-avatar-badge-vip">
      <img src="images/icons/vip.svg" alt="vip" :height="calcPercentSize(30)"/>
    </span>
  </span>
</template>

<script>
export default {
  props: {
    email: "",
    status: String,
    size: String,
    name: String,
    color: undefined,
    ba_acct_data: null,
  },
  computed: {
      cEmail: {
          get() {
              return this.email_aux
          },
          set(value) {
              this.email_aux = value
          }
      }
  },
  data() {
    return {
      gravatar_404: false,
      gravatar: "",
      variant: "primary",
      show_badge: true,
      vip: false,
      show: true,
      email_aux: this.email
    };
  },
  created() {
    if (this.color !== undefined) {
      this.variant = this.color;
    }
    // if (this.cEmail != "") {
        this.forceCleanEmail();
    // }
    //this.get_gravatar();
    if (this.status == "false") {
      this.show_badge = false;
    }
    this.checkBAData();
  },
  watch: {
    "$store.state.csid": function () {
      this.forceCleanEmail();
      this.show = false;
        setTimeout(() => {
            this.get_gravatar();
            this.show = true;
        }, 4);
    },
    email(newVal, oldVal) {
        this.cEmail = newVal;
        this.show = false;
        this.vip = false;
        setTimeout(() => {
            this.get_gravatar();
            this.show = true;
            this.checkBAData();
        }, 200);
    }
  },
  methods: {
    gravatarSizeColor() {
        if(!this.vip) {
            var stc = require('string-to-color');
            var tinycolor = require("tinycolor2");
            var color = stc(this.cEmail);

            var t_color = tinycolor(color);
            if(t_color.isLight()) {
                var text_color = "#333333";
                var border_color = "rgba(51, 51, 51, 0.2)";
            } else if(t_color.isDark()) {
                var text_color = "white";
                var border_color = "white";
            }
        } else {
           var text_color = "#fca500";
           var border_color = "#fca500";
           var color = "#fff2db";
        }

       return `
            min-width: ${this.size};
            max-width: ${this.size};
            min-height: ${this.size};
            max-height: ${this.size};
            background-color: ${color};
            color: ${text_color};
            border: 1px solid ${border_color};
        `;
    },
    checkBAData() {
      if (this.ba_acct_data) {
        let data = JSON.parse(this.ba_acct_data);
        if (data.is_vip) {
          this.vip = true;
        }
      }
    },
    calcPercentSize(percentage) {
      let p = percentage / 100;
      return this.size.match(/(\d+)/)[0] * p;
    },
    getBadgeStatusVariant() {
      if (this.status == "online") {
        return "success";
      } else if (this.status == "busy") {
        return "danger";
      } else if (this.status == "offline") {
        return "secondary";
      } else if (this.status == "appear_away") {
        return "warning";
      }
    },
    LI(value) {
      if (value == null) {
        return "LI";
      }
      return value.substr(0, 2);
    },
    forceCleanEmail() {
      if (this.$store.state.csid.trim() !== "") {
        let prefix = "comp_" + this.$store.state.csid + "_";
        let canSplit = function (str, token) {
          return (str || "").split(token).length > 1;
        };
        if (canSplit(this.cEmail, prefix)) {
          this.cEmail = this.cEmail.replace(prefix, "");
        }

        this.get_gravatar();
      }
    },
    get_gravatar() {
      var md5 = require("md5");
      this.gravatar = `https://www.gravatar.com/avatar/${md5(this.cEmail)}?s=${this.size}&d=blank`;
    },
    // get404(url) {
    //   var xmlHttp = new XMLHttpRequest();
    //   xmlHttp.open("GET", url, false);
    //   xmlHttp.send(null);
    //   if (xmlHttp.status === 404) {
    //     this.gravatar_404 = true;
    //   } else {
    //     this.gravatar = url;
    //   }
    // },
  },
};
</script>

<style scoped>
.b-avatar-badge {
  bottom: 0px;
  right: 0px;
}

.b-avatar-badge-vip {
  position: absolute;
  padding: 0;
  margin: 0;
  z-index: 0;
  top: -5px;
  right: -25%;
}

.img-img {
    position: absolute;
    border-radius: 100%;
}

.img-img img {
    border-radius: 100%;
}

.img-txt {
    height: 100%;
    width: 100%;
    font-family: Lato;
    font-weight: bold;
    text-align: center;
    padding-top: 3px;
    border-radius: 100%;
    text-transform: uppercase;
}

.bordervip {
    -webkit-box-shadow: 0px 0px 0px 1px #fca500;
    box-shadow: 0px 0px 0px 1px #fca500;
}
</style>
