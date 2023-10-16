<template>
  <div>
    <b-container fluid="lg" v-if="show.show">
      <b-row>
        <b-col cols="auto" class="mr-auto p-3 bs-title"
          >{{ title }}
          <b-card-text class="bs-subtitle">
            {{ subtitle }}
          </b-card-text>
        </b-col>
        <b-col cols="auto mt-4">
          <div v-if="viewcontact">
            <b-button @click="btnBack" variant="light bs-btn-back">{{
              $t("bs-cancel")
            }}</b-button>
            <b-button @click="onSubmit" variant="btn bs-btn-save"
              ><i class="fa fa-floppy-o" aria-hidden="true"></i>
              {{ $t("bs-save") }}</b-button
            >
          </div>
          <div v-else>
            <b-button @click="itemConfig" variant="btn bs-btn-add"
              ><i class="fa fa-cog" aria-hidden="true"></i>
              {{ $t("bs-configuration") }}</b-button
            >
            <b-button @click="btnBack" variant="light bs-btn-back">{{
              $t("bs-cancel")
            }}</b-button>
            <b-button @click="onSubmit" class="btn btn-success"
              ><i class="fa fa-floppy-o" aria-hidden="true"></i>
              {{ $t("bs-save") }}</b-button
            >
          </div>
        </b-col>
      </b-row>
      <br />

      <div v-if="show.show" class="row">
        <div class="col-sm-12">
          <div v-if="viewcontact">
            <a v-on:click.stop="showIB(1)" href="#" :class="ss.ss1">{{
              $t("bs-register-company")
            }}</a>
          </div>
          <div v-else>
            <span v-if="messageNeworConfig == 'new'">
              <b-alert fade show variant="primary" class="bui-alert">
                <slot>
                  <h5>{{ $t("bs-successfully-registered-company") }}.</h5>
                  <p>
                    {{ $t("bs-Its-time-to-set-it-up") }}. <br />
                    {{ $t("bs-click-on-config-to-have-it-your") }}
                  </p>
                </slot>
                <!-- <div v-if="showLimite" class="d-flex justify-content-end mt-n1 flex-md-row flex-column">
									Para adquirir cotas &nbsp
									<b-link href="#comprar-mais-cotas" style="color: white;text-decoration: underline;">clique aqui</b-link>
								</div> -->
              </b-alert>
            </span>
            <!-- <a v-on:click.stop="showIB(1)" href="#" :class="ss.ss1">{{$t('bs-edit-company')}}</a> -->
            <!-- <a v-on:click.stop="showIB(2)" href="#" :class="ss.ss2">{{$t('bs-contact')}}</a> -->
          </div>
        </div>
      </div>
      <br /><br />

      <div v-if="show.show1">
        <div class="body">
          <b-form @submit="onSubmit" class="bs-label">
            <b-form-group id="input-group-14" :label="lbname" label-for="input-14">
              <b-form-input
                id="input-14"
                v-model="form.name"
                required
                :placeholder="phName"
                class="bs-input"
                :state="vName"
              ></b-form-input>
            </b-form-group>
            <!-- <b-form-group id="input-group-23" :label="lbAddress" label-for="input-23">
								<b-form-input
								id="input-23"
								v-model="form.address"
								required
								:placeholder="phAddress"
								class="bs-input"
								:state="vAddress"
								></b-form-input>
							</b-form-group> -->
            <b-form-group id="input-group-6" :label="lblogo" label-for="input-6">
              <b-form-input
                id="input-6"
                v-model="form.logo"
                required
                placeholder="Link"
                @change="checklink"
                class="bs-input"
                :state="vLogo"
              ></b-form-input>
              <b-form-group
                id="fieldset-horizontal"
                :description="linkdescripton"
                label-for="input-horizontal"
              >
              </b-form-group>
            </b-form-group>
            <b-form-group id="input-group-7" :label="lbdescription" label-for="input-7">
              <b-form-textarea
                id="textarea"
                v-model="form.description"
                placeholder="..."
                class="bs-input"
                :state="vDescription"
                rows="3"
              ></b-form-textarea>
            </b-form-group>
            <template
              v-if="title == $t('bs-register-company') || title == $t('bs-welcome')"
            >
              <b-form inline class="mt-4 w-100">
                <!-- <b-form-group class="w-100"> -->
                <span id="tooltip-informe-domain">
                  <template v-if="domains.length > 0">
                    {{ $t('bs-is-there-any-domain-missing') }}
                  </template>
                  <template v-else>
                    {{$t('bs-add-one-or-more-of-your-connected-domains')}}
                  </template>
                  &nbsp;
                  <i class="fa fa-question-circle" aria-hidden="true"></i>
                </span>
                <br />
                <b-tooltip
                  target="tooltip-informe-domain"
                  triggers="hover"
                  placement="right"
                  variant="secondary"
                >
                  {{ $t("bs-informe-domain-description") }}
                </b-tooltip>
                <b-row class="w-100">
                  <b-col cols="10">
                    <b-form-input
                      id="inline-form-input-name"
                      class="inputstyle mr-sm-2 mb-sm-0 mt-1 w-100"
                      :placeholder="$t('bs-ex') + ' builderall.com'"
                      v-model="new_domain"
                    ></b-form-input>
                  </b-col>
                  <b-col cols="2" class="p-0">
                    <b-button
                      @click="addDomain()"
                      class="mt-1 pr-0 pl-0"
                      variant="primary"
                      style="font-size: 10px; max-height: 36px; width: 96px"
                    >
                      <i class="fa fa-plus-circle" aria-hidden="true"></i>
                      ADD
                    </b-button>
                  </b-col>
                </b-row>
                <!-- </b-form-group> -->
              </b-form>
              <label for="" class="mt-3" v-if="domains.length > 0">
                {{ $t("bs-select-one-or-more-of-your-domains") }}
              </label>
              <b-list-group id="list-domains" class="pl-0 pr-0" v-if="domains.length > 0">
                <b-list-group-item
                  v-for="domain in domains"
                  :key="domain"
                  class="d-flex justify-content-between pt-2 pb-0 pr-0 pl-0 align-items-center w-100"
                >
                  <div class="row w-100 p-0 m-0">
                    <div class="col pt-1" style="max-width: 40px">
                      <img
                        :src="`https://www.google.com/s2/favicons?domain=${domain}`"
                        width="20"
                        height="20"
                      />
                    </div>
                    <div class="col pt-1">
                      <span>{{ domain }}</span>
                    </div>
                    <div class="col" style="max-width: 80px">
                      <label class="switch">
                        <input type="checkbox" checked @click="checkDomain(domain)" />
                        <span class="slider round"></span>
                      </label>
                    </div>
                  </div>
                </b-list-group-item>
              </b-list-group>
            </template>
          </b-form>
        </div>
      </div>
      <div v-if="show.show2">
        <div class="body">
          <div>
            <b-form @submit="onSubmit" class="bs-label">
              <b-row>
                <b-col sm="3">
                  <b-form-group id="input-group-8" :label="type" label-for="input-8">
                    <b-form-select
                      id="input-8"
                      v-model="form.contact"
                      :options="typecontact"
                      required
                    ></b-form-select>
                  </b-form-group>
                </b-col>
                <b-col>
                  <b-form-group id="input-group-9" :label="contato" label-for="input-9">
                    <b-form-input
                      id="input-9"
                      v-model="form.textcontact"
                      required
                      :placeholder="phName"
                      class="bs-input"
                      :state="vContact"
                    ></b-form-input>
                  </b-form-group>
                </b-col>

                <b-col sm="auto">
                  <b-button
                    @click="saveContact"
                    variant="primary"
                    style="margin-top: 26px"
                    >{{ $t("bs-add").toUpperCase() }}</b-button
                  >
                </b-col>
              </b-row>
            </b-form>
          </div>
        </div>
        <div>
          <b-table
            responsive
            bordered
            borderless
            striped
            hover
            class="local-striped-table"
            head-variant="light"
            table-variant="light"
            :fields="fields"
            :items="contact"
          >
            <template #cell(actions)="row">
              <b-link size="lg" @click="itemDelete(row.item, row.index)">
                <i class="fa fa-trash-o bs-trash fa-2x" aria-hidden="true"></i>
              </b-link>
            </template>
          </b-table>
        </div>
      </div>

      <br />

      <vue-snotify></vue-snotify>
    </b-container>

    <div v-if="showConfig">
      <company-config
        v-on:back="back"
        :csid="csid"
        :is_helpdesk="is_helpdesk"
        :base_url="base_url"
        :usuario="usuario"
      ></company-config>
    </div>

    <!-- 	<modalcomponent
	ref="confirm"
	title="actionConfirm.title"
	message="actionConfirm.message"
	confirmText="actionConfirm.text"
	confirmVariant="actionConfirm.variant"
	cancelText="cancel"
	></modalcomponent> -->

    <!-- copy -->
    <div
      class="modal fade"
      id="modalScript"
      tabindex="-1"
      aria-labelledby="PrivacyTermsLabel"
      aria-hidden="true"
      data-backdrop="static"
      data-keyboard="false"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <center>
            <h5 class="modal-title" id="PrivacyTermsLabel">
              {{$t('bs-your-company-is-ready')}}
            </h5>
          </center>
          <br />
          <div class="modal-body">
            <div class="row">
              <div class="col-12">
                <div class="form-group textsubb">
                  <label for="exampleFormControlSelect1">
                    {{$t('bs-copy-the-script-below-and-add-it')}}
                  </label>
                </div>
              </div>
            </div>
            <b-row>
              <b-col cols="9">
                <b-form-input
                  id="input-11"
                  type="text"
                  required
                  disabled
                  v-model="script"
                ></b-form-input>
                <input type="hidden" id="testing-code" :value="script">
              </b-col>
              <b-col cols="auto">
                  <b-button @click.stop.prevent="copyTestingCode" class="pl-4 pr-4 pt-2 pb-2" variant="primary">
                     <i class="material-icons">content_copy</i> {{$t('bs-copy')}}
                   </b-button>
              </b-col>
            </b-row>
          </div>
          <div class="modal-footer border-0">
            <button
              type="button"
              id="btn-department"
              class="btn btn-success"
              data-dismiss="modal"
            >
              Ok
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      ss: {
        ss1: "tab active",
        ss2: "tab",
      },
      show: {
        show: true,
        show1: true,
        show2: false,
      },
      lbname: this.$t("bs-name"),
      lblogo: this.$t("bs-logo"),
      lbdescription: this.$t("bs-description"),
      // lbAddress: this.$t('bs-address'),
      phName: this.$t("bs-enter-name"),
      // phAddress: this.$t('bs-enter-address'),
      linkdescripton:
        "250x250 " +
        this.$t("bs-format") +
        ": JPEG, PNG " +
        this.$t("bs-or") +
        " GIF." +
        this.$t("bs-the-file-may-even-have") +
        " 4MB.",
      type: this.$t("bs-type"),
      contato: this.$t("bs-contact"),
      form: {
        email: "",
        name: "",
        // address: '',
        website: "",
        phone: "",
        logo: "",
        description: "",
        // address: '',
        contact: null,
        textcontact: "",
      },
      formCompany: {
        name: false,
        email: false,
        // address: false,
        website: false,
        phone: false,
        logo: false,
        description: false,
        // address: false,
        textcontact: false,
      },
      typecontact: [
        { text: this.$t("bs-select-one"), value: null },
        "PHONE",
        "EMAIL",
        "WHATSAPP",
        "WEBSITE",
      ],
      fields: [
        { key: "type", sortable: true, label: this.$t("bs-type") },
        {
          key: "description",
          sortable: true,
          label: this.$t("bs-description"),
        },
        { key: "actions", sortable: true, label: this.$t("bs-actions") },
      ],
      company: [],
      contact: [],
      showConfig: false,
      messageNeworConfig: "",
      domains_selected: [],
      domains: [],
      new_domain: "",
      company_hash_code: false,
      script: "",
    };
  },
  props: {
    usuario: Object,
    csid: String,
    is_helpdesk: String,
    viewcontact: Boolean,
    base_url: {
      type: String,
      default: "",
    },
    company_list: {
      type: Array,
      default: [],
    },
  },
  mounted() {
    var vm = this;
    if (vm.usuario.language == "pt_BR") {
      vm.form.phone = "+55 ";
    }
    var url_string = window.location.href;
    var url = new URL(url_string);
    this.messageNeworConfig = url.searchParams.get("action");

    this.company_hash_code = url.searchParams.get("hc");
    var new_company_created = localStorage.getItem('new-company-created');
    if (new_company_created)  {
        if (this.company_hash_code) {
            $("#modalScript").modal("show");
            this.script = `<script id="ba-helpdesk__script" src="${
                process.env.MIX_INTEGRATION_SCRIPT_URL + "?v=" + Math.random()
            }" hc="${this.company_hash_code}" module="all" chat-type="system"><\/script>`;
        }
        localStorage.removeItem('new-company-created');
    }

    if (vm.csid != undefined && vm.csid != null && vm.csid.trim() != "") {
      var url = `${this.base_url}/company/selected-company/${vm.csid}`;
      axios
        .get(url)
        .then(function (r_resposta) {
          //console.log(r_resposta.data.result[0]);

          vm.form.name = r_resposta.data.result[0].name;
          vm.form.description = r_resposta.data.result[0].description;
          // vm.form.address = r_resposta.data.result[0].address;
          vm.form.logo = r_resposta.data.result[0].logo;
          vm.show.show1 = true;
        })
        .catch(function (error) {
          console.log(error);
        });
    }
    // this.itemConfig(); // ATIVOS SOMENTE PARA TESTES
    this.getDomains();
    localStorage.removeItem('filter_departments');
  },
  methods: {
    checklink(){
      // fetch(this.form.logo, { method: 'HEAD' })
      // .then(res => {
      //     if (res.ok) {
      //         // console.log('Image exists.');
      //     } else {
      //         // console.log('Image does not exist.');
      //         this.form.logo = "";
      //         this.$snotify.info(this.$t("bs-the-image-does-not-exist"), this.$t("bs-info"));
      //     }
      // }).catch(
      //   err => {
      //     console.log('Error:', err);
      //     this.form.logo = "";
      //     this.$snotify.info(this.$t("bs-the-image-does-not-exist"), this.$t("bs-info"));
      //   });
    },
    copyTestingCode () {
          let testingCodeToCopy = document.querySelector('#testing-code')
          testingCodeToCopy.setAttribute('type', 'text')
          testingCodeToCopy.select()

          try {
            var successful = document.execCommand('copy');
            var msg = successful ? 'successful' : 'unsuccessful';
            this.$snotify.info(this.$t("bs-successfully-copied"), this.$t("bs-info"));
          } catch (err) {
            alert('Oops, unable to copy');
          }

          /* unselect the range */
          testingCodeToCopy.setAttribute('type', 'hidden')
          window.getSelection().removeAllRanges()
        },
    addDomain() {
      if (this.new_domain !== "") {
        let index = this.domains.findIndex(
          (item) => item === this.treatDomain(this.new_domain)
        );
        if (index !== -1) {

        } else {
          this.domains.push(this.treatDomain(this.new_domain));
          this.domains_selected.push(this.treatDomain(this.new_domain));
          this.new_domain = "";
        }
      }
    },
    treatDomain(domain) {
      try {
        let url = new URL(domain);
        return url.host;
      } catch (e) {
        return domain;
      }
    },
    getDomains() {
      axios.get("/builderall/domains").then((res) => {
        this.domains = res.data.data;
        if (res.data.data.length > 0) {
          res.data.data.forEach((element) => {
            this.domains_selected.push(element);
          });
        }
      });
    },
    checkDomain(domain) {
      let index = this.domains_selected.findIndex((item) => item === domain);
      if (index !== -1) {
        this.domains_selected.splice(index, 1);
      } else {
        this.domains_selected.push(domain);
      }
    },
    mostramodal() {
      this.$refs.confirm.open(function () {
        alert("oi");
      });
    },
    saveContact() {
      var vm = this;
      var url = `${this.base_url}/company/contact`;
      //console.log(vm.form.contact);
      if (vm.form.contact == null || vm.form.textcontact == "") {
        vm.$snotify.info(
          vm.$t("bs-please-enter-the-information"),
          vm.$t("bs-invalid-input")
        );
        return;
      }
      if (vm.formCompany.textcontact == false) {
        vm.$snotify.info(
          vm.$t("bs-maximum-number-of-characters-exceeded") + " (250)",
          vm.$t("bs-invalid-input")
        );
      }

      axios
        .post(url, {
          itemselected: vm.csid,
          type: vm.form.contact,
          description: vm.form.textcontact,
        })
        .then(function (response) {
          //console.log(response.data.created);
          if (response.data.success) {
            var my_object = {
              id: response.data.id,
              type: vm.form.contact,
              description: vm.form.textcontact,
            };

            vm.contact.push(my_object);

            vm.form.contact = "";
            vm.form.textcontact = "";

            vm.$snotify.success(vm.$t("bs-saved-successfully"), vm.$t("bs-success"));
          } else {
            vm.$snotify.error(vm.$t("bs-error-updating"), vm.$t("bs-error"));
          }
        })
        .catch(function () {
          console.log("FAILURE!!");
        });
    },
    showIB(item) {
      var vm = this;
      vm.show.show1 = false;
      vm.show.show2 = false;
      vm.ss.ss1 = "tab";
      vm.ss.ss2 = "tab";
      if (item == 1) {
        vm.show.show1 = true;
        vm.ss.ss1 = "tab active";
      } else if (item == 2) {
        vm.show.show2 = true;
        vm.ss.ss2 = "tab active";

        var url = `${this.base_url}/company/contact/${vm.csid}`;
        axios
          .get(url)
          .then(function (r_resposta) {
            //console.log(r_resposta.data.result);
            vm.contact = r_resposta.data.result;
          })
          .catch(function (error) {
            console.log(error);
          });
      }
    },
    onSubmit(evt) {
      var vm = this;
      //alert(JSON.stringify(this.form));
      evt.preventDefault();
      if (this.formCompany.name && this.formCompany.description) {
        if (vm.csid != undefined && vm.csid != null && vm.csid.trim() != "") {
          var url = `${this.base_url}/company/update`;
          axios
            .post(url, {
              id: vm.csid,
              name: vm.form.name,
              description: vm.form.description,
              // address: vm.form.address,
              logo: vm.form.logo,
            })
            .then(function (response) {
              //console.log(response.data.created);
              if (response.data.success) {
                vm.$store.commit("changenamecompany", vm.form.name);
                vm.$snotify.success(
                  vm.$t("bs-update-saved-successfully"),
                  vm.$t("bs-success")
                );
                vm.$emit("update-company");
              } else {
                vm.$snotify.error(vm.$t("bs-error-updating"), vm.$t("bs-error"));
              }
            })
            .catch(function () {
              console.log("FAILURE!!");
            });
        } else {
          var url = `${this.base_url}/company/create-company`;
          axios
            .post(url, {
              name: vm.form.name,
              description: vm.form.description,
              // address: vm.form.address,
              logo: vm.form.logo,
              base_url: this.base_url,
              language: vm.usuario.language,
              domains: this.domains_selected,
              timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            })
            .then(function (response) {
              if (response.data.success) {
                var my_object = {
                  id: response.data.id,
                  company_user_id: response.data.company_user_id,
                  name: vm.form.name,
                  address: "address",
                  description: vm.form.description,
                  logo: vm.form.logo,
                  hash_code: response.data.hash_code,
                  is_admin: 1,
                  status: "ACTIVE",
                };

                vm.$emit("new-company", my_object);
                vm.$snotify.success(
                  vm.$t("bs-company-successfully-created"),
                  vm.$t("bs-success")
                );
                vm.$emit("create-company");
                localStorage.setItem('new-company-created', 1);
              } else {
                vm.$snotify.error(vm.$t("bs-error-creating-company"), vm.$t("bs-error"));
              }
            })
            .catch(function () {
              console.log("FAILURE!!");
            });
        }
      } else {
        vm.$snotify.info(vm.$t("bs-invalid-fields"), vm.$t("bs-info"));
      }
    },
    btnBack() {
      var vm = this;
      vm.$emit("go-back");
    },
    itemConfig() {
      var vm = this;
      vm.show.show = false;
      vm.showConfig = true;
    },
    back() {
      var vm = this;
      vm.show.show = true;
      vm.showConfig = false;
    },
    itemDelete(item, index) {
      Swal.fire({
        title: this.$t("bs-are-you-sure"),
        text: this.$t("bs-you-wont-be-able-to-revert-this"),
        icon: "warning",
        showCancelButton: true,
        cancelButtonText: this.$t("bs-cancel"),
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.$t("bs-yes-delete-it"),
      }).then((result) => {
        if (result.isConfirmed) {
          var vm = this;
          var url = `${this.base_url}/company/contact-delet`;
          //console.log(item);
          axios
            .post(url, {
              item: item.id,
            })
            .then(function (response) {
              //console.log(response.data.created);
              if (response.data.success) {
                vm.contact.splice(index, 1);
                vm.$snotify.success(
                  vm.$t("bs-successfully-deleted"),
                  vm.$t("bs-success")
                );
                Swal.fire(
                  vm.$t("bs-deleted"),
                  vm.$t("bs-your-file-has-been-deleted"),
                  "success"
                );
              } else {
                vm.$snotify.error(vm.$t("bs-error-while-deleting"), vm.$t("bs-error"));
              }
            })
            .catch(function () {
              console.log("FAILURE!!");
            });
        }
      });
    },
  },
  computed: {
    vName() {
      this.formCompany.name = this.form.name.length > 1 && this.form.name.length < 100;
      return this.formCompany.name;
    },
    // vAddress() {
    // 	this.formCompany.address = this.form.address.length < 100;
    // 	return this.formCompany.address;
    // },
    vEmail() {
      var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      if (this.form.email == "") {
        return true;
      }
      this.formCompany.email = re.test(this.form.email);
      return this.formCompany.email;
    },
    vWebSite() {
      this.formCompany.website = this.form.website.length < 100;
      return this.formCompany.website;
    },
    vPhone() {
      this.formCompany.phone = this.form.phone.length < 100;
      return this.formCompany.phone;
    },
    vLogo() {
      if (this.form.logo != null) {
        this.formCompany.logo = this.form.logo.length < 250;
        return this.formCompany.logo;
      }
    },
    vDescription() {
      this.formCompany.description =
        this.form.description.length > 1 && this.form.description.length < 250;
      return this.formCompany.description;
    },
    // vAddress(){
    // 	this.formCompany.address = this.form.address.length > 4 && this.form.address.length < 250;
    // 	return this.formCompany.address;
    // },
    vContact() {
      this.formCompany.textcontact =
        this.form.textcontact.length > 1 && this.form.textcontact.length < 250;
      return this.formCompany.textcontact;
    },

    title: function () {
      return this.company_list.length == 0
        ? this.$t("bs-welcome")
        : this.csid !== undefined && this.csid !== null && this.csid.trim() != ""
        ? this.$t("bs-edit-company")
        : this.$t("bs-register-company");
    },
    subtitle: function () {
      return this.company_list.length == 0
        ? this.$t("bs-make-your-first-company-registration")
        : this.csid !== undefined && this.csid !== null && this.csid.trim() != ""
        ? this.$t("bs-edit-company")
        : this.$t("bs-register-new-company");
    },
  },
};
</script>

<style lang="scss" scoped>
.tab {
  padding: 8px;
  color: #a4a4a4;
  border-bottom: 1px solid #bdbdbd;
  font-weight: bold;
  text-decoration: none;
  margin-left: 10px;
  text-transform: uppercase;
}
.active,
.tab:hover {
  color: #0080fc;
  padding-left: 10px;
  border-bottom: 3px solid #0080fc;
}

.bui-alert {
  background: url(/images/meta/corner.png) left top no-repeat,
    url(/images/meta/wave.png) right bottom/100% no-repeat,
    transparent linear-gradient(180deg, #5e81f4 0%, #1665d8 100%);
  border-radius: 16px;
  border: none;
  padding-top: 1.2rem;
  padding-left: 2rem;
  color: #fff;
  p {
    font-size: 0.85rem;
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.8);
  }
  .btn {
    background: #ffffff38;
    border: unset;
    font-weight: normal !important;
    text-transform: capitalize;
    border-radius: 8px;
  }
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
  opacity: 1;
  color: #434343;
}

.modal {
  background-color: #59607173;
  opacity: 1;
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
  opacity: 1;
}

.modal-body label {
  font: normal normal bold 14px/35px Muli;
  letter-spacing: 0px;
  color: black;
  opacity: 1;
}

.modal-body select {
  background: #fafbfc 0% 0% no-repeat padding-box;
  border: 1px solid #dddddd;
  border-radius: 3px;
  height: 50px;
  opacity: 1;
}

.example-open .modal-backdrop {
  background-color: red;
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

#list-domains {
  max-height: 400px;
  overflow: auto;
}

::-webkit-scrollbar {
  width: 8px !important;
  height: 8px !important;
}

::-webkit-scrollbar-track {
  background: #dadfed !important;
  border-radius: 0px !important;
}

::-webkit-scrollbar-thumb {
  background: #82c8fa !important;
  border-radius: 2px !important;
}

::-webkit-scrollbar-thumb:hover {
  background: #82c8fa !important;
}
</style>
