<template>
  <div
    class="modal fade"
    id="modalDepartmentNot"
    tabindex="-1"
    aria-labelledby="modalDepartmentNot"
    aria-hidden="true"
    data-backdrop="static"
    data-keyboard="false"
  >
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <b-row>
          <b-col cols="auto">
            <img src="/images/icons/cookie-01.svg" width="250px;" alt="cookie" />
          </b-col>
          <span class="borderline"></span>
          <b-col>
            <h2 id="modalDepartmentNot"><center>{{$t('bs-control-your-privacy')}}</center></h2>
            <div class="modal-body">
              <div class="row">
                <div class="col-12">
                  <div class="form-group textsubb">
                    <center for="exampleFormControlSelect1">
                      <h4>
                        {{$t('bs-we-use-cookies-to-customize-ads-generate')}}
                      </h4>
                    </center>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="form-group textsubb">
                  <label for="exampleFormControlSelect1">
                    <b-link @click="goToPrivacyPolicy()">
                        {{ $t("bs-terms-of-use") }}
                        &
                        {{$t("bs-privacy-policy")}}
                    </b-link>
                  </label>
                </div>
              </div>
            </div>
            <center>
              <button
                type="button"
                @click="purecookieDismissBA"
                id="btn-department"
                style="border-radius: 26px"
                class="btn btn-primary"
              >
                {{$t("bs-accepted")}}
              </button>
            </center>
            <br />
          </b-col>
        </b-row>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      politicaPrivacidade: "#terms-privacy-policy/" + this.userLang,
      termoUso: "#terms-of-use/" + this.userLang,
    };
  },
  props: {
    title: String,
    usuario: Object,
  },
  mounted() {
    this.cookieConsent();
  },
  methods: {
    goToPrivacyPolicy() {
        axios.post("user/privacy-policy", {
          locale: this.usuario.language,
        })
        .then(res => {
            window.open(res.data.terms_of_use_url, '_blank').focus();
        })
    },
    purecookieDismissBA() {
      axios
        .post("user/accept-cookies", {
          id: this.usuario.id,
        })
        .then((res) => {
          $("#modalDepartmentNot").modal("hide");
        });
    },
    pureFadeOut(e) {
      var o = document.getElementById(e);
      (o.style.opacity = 1),
        (function e() {
          (o.style.opacity -= 0.02) < 0
            ? (o.style.display = "none")
            : requestAnimationFrame(e);
        })();
    },
    pureFadeIn(e, o) {
      var i = document.getElementById(e);
      (i.style.opacity = 0),
        (i.style.display = o || "block"),
        (function e() {
          var o = parseFloat(i.style.opacity);
          (o += 0.02) > 1 || ((i.style.opacity = o), requestAnimationFrame(e));
        })();
    },
    cookieConsent() {
      $("#modalDepartmentNot").modal("show");
    },
  },
};
</script>

<style scoped>
.textsubb {
  text-align: center;
  font: normal normal bold 14px/20px Muli;
  letter-spacing: 0px;
  opacity: 1;
}

.caret {
  cursor: pointer;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

h2 {
  text-align: left;
  font: normal normal 800 32px/40px Muli;
  letter-spacing: 0px;
  color: #333333;
  opacity: 1;
}

h4 {
  text-align: left;
  font: normal normal normal 16px/24px Muli;
  letter-spacing: 0px;
  color: #333333;
  opacity: 1;
}

h5 {
  font: normal normal bold 16px/22px Muli;
  letter-spacing: 0px;
  color: #434343;
}

.content {
  margin-right: 40px;
  margin-left: 40px;
}

/* SCROLL */

::-webkit-scrollbar {
  width: 5px;
  height: 5px;
}

::-webkit-scrollbar-track {
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #0080fc;
  border-radius: 10px;
}

#modalDepartmentNot {
    z-index: 99999999;
}

.modal-content {
  background: #f3f7ff 0% 0% no-repeat padding-box;
  box-shadow: 0px 14px 32px #00000040;
  border-radius: 10px;
  border: none;
  padding-top: 40px;
  padding-left: 40px;
  padding-right: 40px;
}

.modal-title {
  font: normal normal bold 20px/26px Muli;
  letter-spacing: 0px;
  color: #434343;
}

.modal {
  background-color: #59607173;
}

.modal .close {
  background: #acb8d8;
  color: white;
  text-shadow: none;
  padding: 2px;
  margin-top: 1px;
  border-radius: 100%;
  font-size: 15px;
  height: 25px;
  width: 25px;
  margin-right: 2px;
}

.modal-body label {
  font: normal normal bold 14px/35px Muli;
  letter-spacing: 0px;
  color: #656565;
}

.modal-body select {
  background: #fafbfc 0% 0% no-repeat padding-box;
  border: 1px solid #dddddd;
  border-radius: 3px;
  height: 50px;
}

.example-open .modal-backdrop {
  background-color: red;
}

.borderline {
  border: 2px solid #919191;
  opacity: 1;
  margin: 10px;
  padding: 0px;
  height: 200px;
}

@media only screen and (max-width: 993px) {
  img {
    display: none;
  }

  .borderline {
    display: none;
  }
}

@media only screen and (max-width: 575px) {
  .content {
    margin-right: 10px;
    margin-left: 10px;
  }

  .card-header {
    padding-top: 20px;
    height: 80px;
    zoom: 120%;
  }

  .modal-dialog {
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
  }

  .modal-content {
    height: auto;
    min-height: 100%;
    border-radius: 0;
  }

  .modal-footer {
    padding-right: 0px;
    padding-left: 0px;
  }

  #btn-new-chat {
    max-width: 140px;
    padding-left: 6px;
    padding-right: 6px;
  }
}
</style>
