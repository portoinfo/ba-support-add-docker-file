<template>
  <div>
    <b-navbar
      toggleable="lg"
      type="light"
      variant="light"
      fixed="top"
      class="bb-navbar-client"
    >
      <!-- toggle menu mobile -->
      <div v-b-toggle.sidebar-1 class="d-sm-none toggle-icon">
        <burger-icon />
      </div>

      <!-- toggle menu mini -->
      <div class="toggle-icon d-none d-sm-flex" @click="toggleSidebarMini">
        <!-- <img :src="cslogo" width="50" height="50" /> -->
        <!-- <i :class="['bbi', !sidebar_is_mini ? 'bbi-menu-burger' : 'bbi-menu-burger-open']"></i> -->
        <burger-icon :open="sidebar_is_mini" />
      </div>

      <b-navbar-brand href="home">
        <!--  <img src="images/meta/logo.png" alt="Builderall Booking" class="mx-4 mb-1 d-none d-sm-inline-block"> -->
        <div
          v-if="cslogo == ''"
          style="margin-left: 10px; font-weight: bold; color: white"
        >
          <!-- <img src="images/meta/logo.png" alt="Builderall Booking" class="mx-4 mb-1 d-none d-sm-inline-block"> -->
          <!-- {{ $store.state.companyselect }} -->
        </div>
        <div v-else>
          <!-- <img
                    :src="cslogo"
                    v-bind="mainProps"
                    alt="Builderall Booking"
                    class="mx-3 mb-1 d-none d-sm-inline-block"
                    /> -->
        </div>

        <!-- <img src="images/meta/logo-icon.png" alt="Builderall Booking" class="mx-3 mb-1 d-sm-none"> -->
      </b-navbar-brand>

      <span class="flex-fill text-center nav-title">{{
        $store.state.companyselect
      }}</span>
      <div class="mx-3 mx-sm-4 d-flex align-items-center">
        <b-navbar-nav>
          <b-nav-item-dropdown no-caret right class="bb-dropdown-user">
            <template v-slot:button-content>
              <gravatar
                :email="clearmail2(user.email)"
                :status="$status.get(user.id)"
                size="40px"
                :name="$t(user.name)"
                :ba_acct_data="user.builderall_account_data"
              />
            </template>
            <b-dropdown-item href="#">
              <div class="d-flex profile-dropdown align-items-center">
                <gravatar
                  :email="clearmail2(user.email)"
                  :status="$status.get(user.id)"
                  size="60px"
                  :name="$t(user.name)"
                  :ba_acct_data="user.builderall_account_data"
                />
                <div class="flex-fill d-flex flex-column m-3">
                  <span>{{ $t(user.name) }}</span>
                  <span>{{ user.email | clearmail }}</span>
                </div>
              </div>
            </b-dropdown-item>

            <b-dropdown-divider></b-dropdown-divider>

            <b-dropdown-item
              v-if="editPerfilClient && user_uuid == null"
              tabindex="-1"
              link-class="py-2"
              @click="openModalProfile()"
            >
              <img src="/images/icons/edit-profile.svg" class="pl-3 pr-3" />
              <!-- {{ $t("bs-favorite-lang") }} -->
              {{ $t("bs-edit-profile") }}
            </b-dropdown-item>

            <b-dropdown-item tabindex="-1" link-class="py-2">
              <i class="bbi bbi-language bbi-22 mx-3"></i> {{ $t("bs-favorite-lang") }}
            </b-dropdown-item>
            <v-select
              style="background-color: #f2f2f2"
              class="mt-1 mx-3 mb-3"
              :clearable="false"
              :options="languages"
              label="desc"
              @input="saveLanguage()"
              v-model="userLang"
              :reduce="(value) => value.key"
            >
              <template #selected-option="{ key, desc }">
                <img height="24" class="mx-3" :src="`images/flags/${key}.svg`" alt="" />
                {{ desc }}
              </template>
              <template #option="{ key, desc }">
                <img height="24" class="mx-2" :src="`images/flags/${key}.svg`" alt="" />
                {{ desc }}
              </template>
            </v-select>

            <b-dropdown-divider v-if="show_logout == 'true'"></b-dropdown-divider>

            <!-- <b-dropdown-item tabindex="-1" href="#" link-class="py-2">
                        <i class="bbi bbi-gear bbi-22 mx-3"></i> {{ $t("bs-preferences") }}
                    </b-dropdown-item> -->

            <b-dropdown-item
              tabindex="-1"
              href="/logout-client"
              link-class="py-2"
              v-if="show_logout == 'true'"
              @click="logout"
            >
              <i class="bbi bbi-logout bbi-22 mx-3"></i> {{ $t("bs-exit") }}
            </b-dropdown-item>

            <form id="logout-form" action="logout" method="GET" style="display: none">
              <input type="hidden" name="_token" :value="csrf" />
            </form>
          </b-nav-item-dropdown>
        </b-navbar-nav>
      </div>
      <vue-snotify></vue-snotify>
    </b-navbar>

    <!-- Modal Profile -->
    <div class="modal" tabindex="-1" id="modalProfile" data-backdrop="static">
      <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content pr-3 pl-3">
          <div class="modal-header border-0">
            <h5 class="modal-title">{{ $t("bs-profile") }}</h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
              @click="cleanFormProfile()"
            >
              <span aria-hidden="true">
                <img src="images/icons/close_modal.svg" alt="" />
              </span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-12">
                <center>
                    <gravatar
                        :email="user.email"
                        :status="'false'"
                        size="130px"
                        :name="user.name"
                        />
                </center>
                <button
                  class="rounded-circle btn-gravatar"
                  @click="goToGravatar()"
                  title="Gravatar"
                >
                  <vue-material-icon name="edit" size="18" />
                </button>
              </div>
            </div>
            <div class="row mt-3">
              <div class="col-12">
                <b-form class="bs-label">
                  <b-form-group
                    id="input-group-1"
                    :label="$t('bs-name')"
                    label-for="input-1"
                  >
                    <b-form-input
                      id="input-1"
                      v-model="form.name"
                      :state="vName"
                      :placeholder="$t('bs-enter-name')"
                      required
                      class="bs-input"
                      autocomplete="off"
                    ></b-form-input>
                  </b-form-group>

                  <b-form-group id="input-group-2" label="Email" label-for="input-2">
                    <b-form-input
                      id="input-2"
                      v-model="form.email"
                      :state="vEmail"
                      :placeholder="$t('bs-enter-email')"
                      required
                      class="bs-input"
                      type="email"
                    ></b-form-input>
                  </b-form-group>

                  <b-form-group>
                    <label for="">{{ $t("bs-country") }}</label>
                    <v-select
                      :clearable="false"
                      :options="subsidiaries"
                      v-model="form.subsidiary_id"
                      :reduce="(value) => value.key"
                      class="select-country"
                    >
                      <template #selected-option="{ key, label, code }">
                        <img
                          :src="`https://flagcdn.com/24x18/${code.toLowerCase()}.png`"
						  class="mr-2"
                        />
                        {{ label }}
                      </template>
                      <template v-slot:option="{ key, label, code }">
                        <img
                          :src="`https://flagcdn.com/24x18/${code.toLowerCase()}.png`"
						  class="mr-2"
                        />
                        {{ label }}
                      </template>
                    </v-select>
                  </b-form-group>
                  <span class="password" @click="openModalPassword()">
                    {{ $t("bs-change-password") }}
                  </span>
                </b-form>
              </div>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button
              type="button"
              class="btn btn-ligth text-capitalize"
              data-dismiss="modal"
              @click="cleanFormProfile()"
            >
              {{ $t("bs-cancel") }}
            </button>
            <button @click="onSubmitProfile()" type="button" class="btn btn-success">
              {{ $t("bs-save") }}
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- fim modal profile   -->

    <!-- modal password  -->
    <div class="modal" tabindex="-1" id="modalPassword" data-backdrop="static">
      <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content pr-3 pl-3">
          <div class="modal-header border-0">
            <h5 class="modal-title">{{ $t("bs-change-password") }}</h5>
            <button
              @click="cleanFormPassword()"
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">
                <img src="images/icons/close_modal.svg" alt="" />
              </span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-12">
                <b-form class="bs-label">
                  <b-form-group
                    id="input-group-3"
                    :label="$t('bs-current-password')"
                    label-for="input-current-password"
                  >
                    <b-form-input
                      v-if="showCurrentPassword"
                      @click="preventAutocomplete()"
                      id="input-current-password"
                      v-model="form.current_password"
                      type="text"
                      required
                      :placeholder="$t('bs-password')"
                      class="bs-input bodersimple"
                    ></b-form-input>
                  </b-form-group>
                  <b-form-group
                    id="input-group-3"
                    :label="$t('bs-new-password')"
                    label-for="input-1"
                    :description="$t('bs-must-contain-at-least-6-characters')"
                  >
                    <b-form-input
                      id="input-3"
                      v-model="form.password"
                      type="password"
                      autocomplete="off"
                      required
                      :placeholder="$t('bs-password')"
                      class="bs-input bodersimple"
                      :state="vPassword"
                      :readonly="form.current_password == ''"
                    ></b-form-input>
                  </b-form-group>
                  <b-form-group
                    id="input-group-4"
                    :label="$t('bs-confirm-password')"
                    label-for="input-1"
                  >
                    <b-form-input
                      id="input-4"
                      v-model="form.password2"
                      type="password"
                      autocomplete="off"
                      required
                      :placeholder="$t('bs-password')"
                      class="bs-input bodersimple"
                      :state="vPassword2"
                      :readonly="vPassword == false || vPassword == null"
                    >
                    </b-form-input>
                  </b-form-group>
                </b-form>
              </div>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button
              @click="backToModalProfile()"
              type="button"
              class="btn btn-ligth text-capitalize"
              data-dismiss="modal"
            >
              {{ $t("bs-back") }}
            </button>
            <button @click="onSubmitProfile()" type="button" class="btn btn-success">
              {{ $t("bs-save") }}
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- fim modal password  -->

    <privacy-terms :user="user" :userLang="userLang"></privacy-terms>

    <cookies-accept v-if="cookies_not_accepted" :usuario="user"></cookies-accept>

  </div>
</template>

<script>
import { languages } from "../../../../static/translation/select";
// import { saveLanguage } from '../../services/user';
import { mapState, mapMutations } from "vuex";

export default {
  data() {
    return {
      mainProps: { blank: true, width: 50, height: 50, class: "m1" },
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
      languages: languages,
      userLang: this.$i18n.locale,
      //sidebar_is_mini: false,
      editPerfilClient: Boolean,
      user_uuid: "",
      email_prefix: this.user.email.split("_")[0]+"_"+this.user.email.split("_")[1]+"_",
      form: {
        name: "",
        email: "",
        country: "",
        subsidiary_id: "",
        current_password: "",
        password: "",
        password2: "",
      },
      formState: {
        name: false,
        email: false,
        country: false,
        password: null,
        password2: null,
      },
      subsidiaries: [],
      showCurrentPassword: true,
      cookies_not_accepted: false,
      isVip: false,
    };
  },
  props: {
    user: Object,
    csname: String,
    cslogo: String,
    show_logout: String,
    gravatar: String,
    session_dtype: "",
    user_attendant: String,
  },
  mounted() {
    if (localStorage.getItem("profile-updated") == 1) {
      this.$snotify.success(this.$t("bs-profile-updated"), this.$t("bs-saved"));
      localStorage.removeItem("profile-updated");
    }

    if (localStorage.getItem("ticket_module") == 1) {
      window.location.href = "/client-ticket"
      localStorage.removeItem("ticket_module");
      localStorage.setItem('open_modal', 1);
    }

    if (localStorage.getItem("chat_module") == 1) {
      window.location.href = "/client-chat"
      localStorage.removeItem("chat_module");
      localStorage.setItem('open_modal', 1);
    }


    if (!this.languageAvaliable) {
      this.userLang = "en_US";
    }

    this.$store.commit("changenamecompany", this.csname);
    // this.$root.$on('toggleSidebar', () => {
    //     this.sidebar_is_mini = !this.sidebar_is_mini
    // })

    this.getProfileSettings();
    this.CheckUser_uuid();

    if (!this.user.cookies_accepted) {
        this.cookies_not_accepted = true;
    }

     if (this.session_dtype !== "null") {
        var session_dtype = JSON.parse(this.session_dtype);
        session_dtype.forEach((element) => {
           if (element == "builderall-mentor") {
                this.isVip = true;
            }
        });
      }
  },
  methods: {
    ...mapMutations(["toggleSidebarMini"]),

    saveLanguage() {
      var vm = this;
      var url = "/agents/update-language";
      axios
        .post(url, {
          id: this.user.id,
          language: this.userLang,
        })
        .then(function (response) {
          //console.log(response.data.created);
          if (response.data.success) {
            location.reload();
          } else {
            vm.$snotify.error(this.$t("bs-error-save"), this.$t("bs-error"));
          }
        })
        .catch(function (error) {
          console.log(error);
          console.log("FAILURE!!");
        });

      // this.$loading.show()
      // saveLanguage({language: this.userLang})
      // .then(res => {
      //     this.$snotify.success(this.$t('bb-done'))
      //     location.reload()
      // })
      // .catch(res => {
      //     console.error(res);
      // })
      // .finally(() => {
      //     this.$loading.hide()
      // });
    },
    LI(value) {
      if (value == null) {
        return "LI";
      }
      return value.substr(0, 2);
    },
    // toggleSidebarMini(){
    //     let mainElement = document.getElementById('main')
    //     if (mainElement){
    //         mainElement.classList.toggle('mini');
    //         this.$root.$emit('toggleSidebar');
    //     }
    // },
    getProfileSettings() {
      axios
        .get("/company-config/general-settings", {
          params: {
            company_id: this.$store.state.company,
          },
        })
        .then(({ data }) => {
          this.editPerfilClient = data.editPerfilClient;
        });
    },
    CheckUser_uuid() {
      axios.get("user/get-user-uuid", {}).then(({ data }) => {
        this.user_uuid = data.user_uuid;
      });
    },
    openModalPassword() {
      $("#modalProfile").modal("hide");
      $("#modalPassword").modal("show");
    },
    cleanFormProfile() {
      this.form.name = "";
      this.form.email = "";
      this.form.subsidiary_id = "";
      this.form.current_password = "";
      this.form.password = "";
      this.form.password2 = "";
    },
    cleanFormPassword() {
      this.form.current_password = "";
      this.form.password = "";
      this.form.password2 = "";
      this.backToModalProfile();
    },
    openModalProfile() {
      axios
        .get("/client/department/get-subsidiary", {})
        .then((res) => {
          this.subsidiaries = res.data.result;
        })
        .catch((err) => {
          console.error(err);
        });
      if (this.form.name == "") this.form.name = this.user.name;
      if (this.form.email == "") this.form.email = this.clearmail2(this.user.email);
      if (this.form.subsidiary_id == "")
        this.form.subsidiary_id = this.user.subsidiary_id;
      $("#modalProfile").modal("show");
    },
    clearmail2(srt) {
      var aux = srt.split("_");
      aux = aux.splice(2);
      var concat = "";

      for (var i = 0; i < aux.length; i++) {
        concat += aux[i] + "_";
      }

      var email = concat.slice(0, -1)
      email = email.toUpperCase();
      if(email.startsWith('PREFIX_WL_')) {
        email = email.replace('PREFIX_WL_', '').replace(/^\w+_/, '');
      }
      return email.toLowerCase();
    },
    onSubmitProfile() {
      if (this.formState.name == false) {
        this.$snotify.error(this.$t("bs-name"), this.$t("bs-invalid-field"));
      } else if (this.formState.email == false) {
        this.$snotify.error("Email", this.$t("bs-invalid-field"));
      } else if (this.form.current_password != "" && (this.formState.password == false || this.formState.password == null)) {
        this.$snotify.error(this.$t("bs-new-password"), this.$t("bs-invalid-field"));
      } else if (this.form.current_password != "" && (this.formState.password2 == false || this.formState.password2 == null)) {
        this.$snotify.error(this.$t("bs-confirm-password"), this.$t("bs-invalid-field"));
      } else {
        axios
          .post("user/update", {
            name: this.form.name,
            email: this.email_prefix+this.form.email,
            subsidiary_id: this.form.subsidiary_id,
            current_password: this.form.current_password,
            new_password: this.form.password,
          })
          .then((res) => {
            if (res.status == 203) {
                this.$snotify.error(this.$t("bs-current-password"), this.$t("bs-invalid-field"));
            } else if (res.status == 200) {
              localStorage.setItem("profile-updated", 1);
              location.reload();
            }
          });
      }
    },
    backToModalProfile() {
      $("#modalPassword").modal("hide");
      $("#modalProfile").modal("show");
    },
    goToGravatar() {
      window.open("https://gravatar.com/");
    },
    preventAutocomplete() {
        if (this.form.current_password.trim() === '') {
             this.showCurrentPassword = false;
            setTimeout(() => {
                this.showCurrentPassword = true;
            }, 4);
            setTimeout(() => {
                $('#input-current-password').focus();
            }, 200);
        }
    },
    logout(){
      localStorage.setItem("loginAuto", false);
    },
  },
  computed: {
    ...mapState(["sidebar_is_mini"]),

    languageAvaliable() {
      return this.languages.find((el) => el.key == this.$i18n.locale);
    },
    vName() {
      this.formState.name = this.form.name.length > 1 && this.form.name.length < 100;
      return this.formState.name;
    },
    vEmail() {
      var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      this.formState.email = re.test(this.form.email);
      return this.formState.email;
    },
    vPassword() {
      this.formState.password =
        this.form.password.length > 5 && this.form.password.length < 255;
      if (this.form.password == "") this.formState.password = null;
      return this.formState.password;
    },
    vPassword2() {
      this.formState.password2 = this.form.password == this.form.password2;
      if (this.form.password2 == "") this.formState.password2 = null;
      return this.formState.password2;
    },
    typeCurrentPassword() {

        if (this.form.current_password) {
            return "password"
        } else {
            return "text"
        }
    }
  },
  filters: {
    clearmail(srt) {
      var aux = srt.split("_");
      aux = aux.splice(2);
      var concat = "";

      for (var i = 0; i < aux.length; i++) {
        concat += aux[i] + "_";
      }

      var email = concat.slice(0, -1)
      email = email.toUpperCase();
      if(email.startsWith('PREFIX_WL_')) {
        email = email.replace('PREFIX_WL_', '').replace(/^\w+_/, '');
      }
      return email.toLowerCase();
    },
  },
  watch: {
      'form.current_password': function (){
        if (this.form.current_password.trim() === '') {
            document.getElementById('input-current-password').readOnly = true;
            setTimeout(() => {
                document.getElementById('input-current-password').type = "text";
                document.getElementById('input-current-password').readOnly = false;
                $('#input-current-password').keydown();
            }, 200);
        } else {
            document.getElementById('input-current-password').type = "password";
        }
    },
  },
};
</script>

<style lang="scss">
@import "./resources/sass/variables";

.bb-navbar-client {
  box-shadow: 0 1px 2px rgba(38, 36, 36, 0.14);
  border: none;
  height: $header-height;
  flex-flow: row nowrap;
  justify-content: flex-start;
  padding: 0;
  background: #0294ff !important;

  .dropdown-menu {
    position: absolute;
    .dropdown-item.active,
    .dropdown-item:active {
      i.bbi {
        filter: brightness(3);
      }
    }
  }

  .toggle-icon {
    width: calc(#{$sidebar-width-mini} - 1px);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    background: #0294ff;
    height: calc(#{$header-height} - 2px);
    border: none;
    i {
      width: 29px;
      transition: $sidebar-transition;
    }
  }

  .divider-vertical {
    display: block;
    height: 25px;
    border-left: 1px solid #bed1ea;
  }

  .notifications-toggle {
    cursor: pointer;
    .bbi-bell {
      position: relative;
    }
    &.new .bbi-bell:after {
      width: 10px;
      height: 10px;
      content: close-quote;
      background: #ff3636;
      display: block;
      border-radius: 50%;
      border: 2px solid #fff;
      position: absolute;
      right: 1px;
      top: 1px;
    }
  }

  .bb-dropdown-user {
    .dropdown-menu {
      box-shadow: 0px 0px 5px #26242459;
      border-radius: 10px;
      border: none;
      .dropdown-item {
        display: flex;
        align-items: center;
        .profile-dropdown {
          text-align: initial;
          div span {
            &:nth-child(1) {
              font-size: 18px;
            }
            &:nth-child(2) {
              font-size: 13px;
            }
          }
        }
      }
    }
  }
}

.img-nav {
  height: 40px;
  width: 40px;
  border-radius: 100%;
}

.dropdown-toggle::after {
  display: none;
}

.nav-title {
  font: normal normal bold 20px/30px Muli;
  letter-spacing: 0px;
  color: #f4f4f4;
}

@media only screen and (max-width: 575px) {
  .nav-title {
    zoom: 60%;
  }
}

#modalProfile label,
#modalPassword label {
  font: normal normal normal 14px/17px Lato;
  letter-spacing: 0px;
  color: #212529;
  font-weight: bold;
}

#modalProfile input,
#modalPassword input {
  font: normal normal normal 14px/35px Muli;
  letter-spacing: 0px;
  color: #656565;
  font-weight: bold;
}

#modalProfile .modal-content,
#modalPassword .modal-content {
  background: #f3f7ff 0% 0% no-repeat padding-box;
  box-shadow: 0px 14px 32px #00000040;
  border-radius: 10px;
  border: none;
  min-width: 360px;
}

#modalProfile .modal-header .modal-title,
#modalPassword .modal-header .modal-title {
  font: normal normal bold 20px/26px Muli;
  letter-spacing: 0px;
  color: #434343;
}

#modalProfile .img-profile {
  height: 168px;
  width: 168px;
  border-radius: 100%;
}

#modalProfile .btn-gravatar {
  border: 2px solid #f3f7ff;
  height: 28px;
  width: 28px;
  background: #bad9f7;
  color: #0080fc;
  position: absolute;
  bottom: -2px;
  right: 185px;
}

#modalProfile .btn-gravatar span {
  margin-top: 3px !important;
  margin-left: -2px !important;
}

#modalProfile .select-country .vs__search {
  max-width: 1px !important;
  margin: 0 !important;
  padding: 0 !important;
}

#modalProfile .select-country .vs__dropdown-menu {
  max-height: 130px;
}

#modalProfile span.password {
  text-decoration: underline;
  font: normal normal normal 12px/15px Muli;
  letter-spacing: 0px;
  color: #0f7bff;
  opacity: 0.7;
  cursor: pointer;
}

#img-vip-sm {
    position: fixed;
    right: 10px;
    top: 10px;
}

#img-vip-lg {
    position: absolute;
    left: 53px;
    top: 11px;
}

.caret {
	cursor: pointer;
	-webkit-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
}
</style>
