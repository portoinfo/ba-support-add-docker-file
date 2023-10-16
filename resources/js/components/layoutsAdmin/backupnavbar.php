<template>
  <div>
    <b-navbar
      toggleable="lg"
      type="light"
      variant="light"
      fixed="top"
      class="bg-white bb-navbar"
      style="z-index: 3 !important;"
    >
      <!-- toggle menu mobile -->
      <div v-if="!customer_service" v-b-toggle.sidebar-mobile :class="['toggle-icon', !customer_service ? 'd-sm-none' : 'd-lg-none']">
        <i class="bbi bbi-menu-burger"></i>
      </div>

      <!-- toggle menu mini -->
      <div :class="['toggle-icon', 'd-none', !customer_service ? 'd-sm-flex' : 'd-lg-flex']" @click="toggleSidebarMini">
        <i
          :class="['bbi', !sidebar_is_mini ? 'bbi-menu-burger' : 'bbi-menu-burger-open']"
        ></i>
      </div>

      <b-navbar-brand href="/home">
        <!--  <img src="images/meta/logo.png" alt="Builderall Booking" class="mx-4 mb-1 d-none d-sm-inline-block"> -->
        <div v-if="cslogo == ''">
          <!-- <img src="images/meta/logo.png" alt="Builderall Booking" class="mx-4 mb-1 d-none d-sm-inline-block"> -->
            <img src="/images/bs/LogoBuilderall_new.svg" alt="Builderall HelpDesk" height="33px">

        </div>
        <div v-else>
          <img src="/images/bs/LogoBuilderall_new.svg" alt="Builderall HelpDesk" height="33px">
          <!-- <img
            @error="setAltImg"
            :src="cslogo"
            v-bind="mainProps"
            width="180px"
            class="mx-3 mb-1 d-none d-sm-inline-block"
          /> -->

                </div>

                <!-- <img src="images/meta/logo-icon.png" alt="Builderall Booking" class="mx-3 mb-1 d-sm-none"> -->
            </b-navbar-brand>

            <span class="flex-fill"></span>

            <div class="mx-3 mx-sm-4 d-flex align-items-center">
                <b-badge variant="light" class="pl-2 pr-2 d-none d-sm-inline-block">
                    <div class="h-100 w-100 d-table">
                        <span class="bs-ico mr-1">&#xe0af;</span>
                        <span class="d-table-cell align-middle h-100 d-inline-block text-truncate" style="max-width: 200px;">{{ $store.state.companyselect }}</span>
                    </div>
                </b-badge>
                <span class="divider-vertical mx-2"></span>

                <b-navbar-nav>
                    <b-nav-item-dropdown
                        no-caret
                        right
                        class="bb-dropdown-user"
                    >
                        <template v-if="csname == ''" v-slot:button-content>
                            <b-avatar
                                variant="primary"
                                size="40px"
                                :text="LI(usuario.name)"
                            ></b-avatar>
                        </template>
                        <template v-else v-slot:button-content>
                            <gravatar
                                :email="usuario.email"
                                :status="$status.get(usuario.id)"
                                size="40px"
                                :name="usuario.name"
                            />
                        </template>

                        <div role="menuitem" class="dropdown-item profile-data">
                            <div
                                class="d-flex profile-dropdown align-items-center"
                            >
                                <!-- <a
                  @mouseover="avatarEdit = true"
                  @mouseleave="avatarEdit = false"
                  href="https://gravatar.com/"
                  target="_blank"
                > -->
                <template v-if="csname == ''" >
                            <b-avatar
                                v-show="!avatarEdit"
                                variant="primary"
                                size="60px"
                                :text="LI(usuario.name)"
                            ></b-avatar>
                        </template>
                        <template v-else>
                            <gravatar
                                v-show="!avatarEdit"
                                :email="usuario.email"
                                :status="$status.get(usuario.id)"
                                size="60px"
                                :name="usuario.name"
                            />
                        </template>
                                <div class="flex-fill d-flex flex-column m-3">
                                    <span>{{ usuario.name }}</span>
                                    <span>{{ usuario.email }}</span>
                                    <span v-if="csname != ''">
                                        <v-select
                                            style="background-color: #f2f2f2"
                                            class="select-status style-chooser"
                                            :clearable="false"
                                            :options="status_options"
                                            label="desc"
                                            :value="returnStatus()"
                                            :reduce="value => value.key"
                                            v-model="status_selected"
                                            @input="changeStatus()"
                                        >
                                            <template
                                                v-slot:option="{ key, desc }"
                                            >
                                                <span
                                                    v-if="key === 'online'"
                                                    class="badge badge-pill badge-status"
                                                    style="background-color: #01d4b9 !important"
                                                    >&nbsp;</span
                                                >
                                                <span
                                                    v-if="key === 'busy'"
                                                    class="badge badge-pill badge-status"
                                                    style="background-color: #fa4b57 !important"
                                                    >&nbsp;</span
                                                >
                                                <span
                                                    v-if="key === 'appear_away'"
                                                    class="badge badge-pill badge-status"
                                                    style="background-color: #ffb244 !important"
                                                    >&nbsp;</span
                                                >
                                                {{ desc }}
                                            </template>
                                        </v-select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <b-dropdown-divider></b-dropdown-divider>

                        <b-dropdown-item
                            v-if="editPerfilAttendants || is_admin"
                            tabindex="-1"
                            link-class="py-2"
                            @click="openModalProfile()"
                        >
                            <img
                                :src="
                                    `${base_url}/images/icons/edit-profile.svg`
                                "
                                class="pl-3 pr-3"
                            />
                            <!-- {{ $t("bs-favorite-lang") }} -->
                            {{ $t("bs-edit-profile") }}
                        </b-dropdown-item>



                        <b-dropdown-item tabindex="-1" link-class="py-2">
                            <i class="bbi bbi-language bbi-22 mx-3"></i>
                            {{ $t("bs-favorite-lang") }}
                        </b-dropdown-item>
                        <v-select
                            style="background-color: #f2f2f2"
                            class="mt-1 mx-3 mb-3"
                            :clearable="false"
                            :options="languages"
                            label="desc"
                            @input="saveLanguage()"
                            v-model="userLang"
                            :reduce="value => value.key"
                        >
                            <template #selected-option="{ key, desc }">
                                <img
                                    height="24"
                                    class="mx-3"
                                    :src="`${base_url}/images/flags/${key}.svg`"
                                    alt=""
                                />
                                {{ desc }}
                            </template>
                            <template #option="{ key, desc }">
                                <img
                                    height="24"
                                    class="mx-2"
                                    :src="`${base_url}/images/flags/${key}.svg`"
                                    alt=""
                                />
                                {{ desc }}
                            </template>
                        </v-select>

                        <b-dropdown-divider></b-dropdown-divider>

                        <!-- <b-dropdown-item tabindex="-1" href="#" link-class="py-2">
                        <i class="bbi bbi-gear bbi-22 mx-3"></i> {{ $t('bs-preferences') }}
                    </b-dropdown-item> -->

                        <b-dropdown-item
                            tabindex="-1"
                            @click="logout"
                            :href="`${base_url}/logout`"
                            link-class="py-2"
                        >
                            <i class="bbi bbi-logout bbi-22 mx-3"></i>
                            {{ $t("bs-exit") }}
                        </b-dropdown-item>

                        <form
                            id="logout-form"
                            action="logout"
                            method="GET"
                            style="display: none"
                        >
                            <input type="hidden" name="_token" :value="csrf" />
                        </form>
                    </b-nav-item-dropdown>
                </b-navbar-nav>
            </div>
        </b-navbar>
        <vue-snotify></vue-snotify>

        <!-- Modal Profile -->
        <div
            class="modal"
            tabindex="-1"
            id="modalProfile"
            data-backdrop="static"
        >
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
                                <img
                                    src="images/icons/close_modal.svg"
                                    alt=""
                                />
                            </span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <center>
                                    <gravatar
                                        :email="usuario.email"
                                        :status="'false'"
                                        size="130px"
                                        :name="usuario.name"
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

                                    <b-form-group
                                        id="input-group-2"
                                        label="Email"
                                        label-for="input-2"
                                    >
                                        <b-form-input
                                            id="input-2"
                                            v-model="form.email"
                                            :state="vEmail"
                                            :placeholder="$t('bs-enter-email')"
                                            required
                                            class="bs-input"
                                            type="email"
                                            disabled
                                        ></b-form-input>
                                    </b-form-group>

                                    <b-form-group>
                                        <label for="">{{
                                            $t("bs-country")
                                        }}</label>
                                        <v-select
                                            :clearable="false"
                                            :options="subsidiaries"
                                            v-model="form.subsidiary_id"
                                            :reduce="value => value.key"
                                            class="select-country"
                                        >
                                            <template
                                                #selected-option="{ key, label, code }">
                                                <img
									            :src="`https://flagcdn.com/24x18/${code.toLowerCase()}.png`"
									            class="mr-2"
                                                />
                                                {{ label }}
                                            </template>
                                            <template
                                                v-slot:option="{ key, label, code }">
                                                <img
									            :src="`https://flagcdn.com/24x18/${code.toLowerCase()}.png`"
									            class="mr-2"
                                                />
                                                {{ label }}
                                            </template>
                                        </v-select>
                                    </b-form-group>
                                    <span
                                        class="password"
                                        @click="openModalPassword()"
                                    >
                                        {{ $t("bs-change-password") }}
                                    </span>
                                    <span
                                        class="password"
                                        style="float:right;"
                                        @click="openModalTelegram()"
                                    >
                                        {{$t('bs-notification-setting')}}
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
                        <button
                            @click="onSubmitProfile()"
                            type="button"
                            class="btn btn-success"
                        >
                            {{ $t("bs-save") }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- fim modal profile   -->

        <!-- modal password  -->
        <div
            class="modal"
            tabindex="-1"
            id="modalPassword"
            data-backdrop="static"
        >
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content pr-3 pl-3">
                    <div class="modal-header border-0">
                        <h5 class="modal-title">
                            {{ $t("bs-change-password") }}
                        </h5>
                        <button
                            @click="cleanFormPassword()"
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">
                                <img
                                    src="images/icons/close_modal.svg"
                                    alt=""
                                />
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
                                        :description="
                                            $t(
                                                'bs-must-contain-at-least-6-characters'
                                            )
                                        "
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
                                            :readonly="
                                                form.current_password == ''
                                            "
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
                                            :readonly="
                                                vPassword == false ||
                                                    vPassword == null
                                            "
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
                        <button
                            @click="onSubmitProfile()"
                            type="button"
                            class="btn btn-success"
                        >
                            {{ $t("bs-save") }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- fim modal password  -->

        <!-- modal telegram  -->
        <div
            class="modal"
            tabindex="-1"
            id="modalTelegram"
            data-backdrop="static"
        >
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content pr-3 pl-3">
                    <div class="modal-header border-0">
                        <h5 class="modal-title">
                            {{ $t("bs-notification-setting") }} 
                        </h5>
                    </div>
                    <label class="pr-3 pl-3" for="description">{{$t('bs-check-the-options-you-want-to-receive-noti')}}</label>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <b-form>
                                    <label for="inline-form-input-name"><b>{{$t('bs-type')}}: </b></label>
                                    <b-form-group id="input-group-1"
                                        label-for="input-current-email"
                                        v-slot="{ ariaDescribedby }"
                                    >
                                        <b-form-checkbox-group
                                        v-model="selectConfigNoty"
                                        id="checkboxes-1"
                                        :aria-describedby="ariaDescribedby"
                                        >
                                        <b-form-checkbox v-if="is_hs == 'false'" class="ln" value="noty_telegram">
                                            {{$t('bs-telegram')}} - 
                                            <span v-if="valueEnv == 'sandbox'">
                                                <a href="https://web.telegram.org/z/#2024508259">{{$t('bs-link-account')}}</a>
                                            </span>
                                            <span v-else-if="valueEnv == 'local' || valueEnv == 'production'">
                                                <a href="https://web.telegram.org/z/#2056592771">{{$t('bs-link-account')}}</a>
                                            </span>
                                            <br>
                                        </b-form-checkbox>
                                        <b-form-checkbox class="ln" value="noty_email">{{$t('bs-email')}}</b-form-checkbox><br>
                                        </b-form-checkbox-group>
                                    </b-form-group>
                                    <label for="inline-form-input-name"><b>{{$t('bs-chat')}}: </b></label>
                                    <b-form-group id="input-group-2"
                                        label-for="input-current-ticket"
                                        v-slot="{ ariaDescribedby }"
                                    >
                                        <b-form-checkbox-group
                                        v-model="selectConfigTelegram"
                                        id="checkboxes-2"
                                        :aria-describedby="ariaDescribedby"
                                        >
                                        <b-form-checkbox class="ln" value="new_chat">{{$t('bs-new-chats')}}</b-form-checkbox><br>
                                        <!-- <b-form-checkbox value="chat_answered">Chats repondidos</b-form-checkbox> -->
                                        </b-form-checkbox-group>
                                    </b-form-group>
                                    <label for="inline-form-input-name"><b>{{$t('bs-ticket')}}: </b></label>
                                    <b-form-group id="input-group-3"
                                        label-for="input-current-ticket"
                                        v-slot="{ ariaDescribedby }"
                                    >
                                        <b-form-checkbox-group
                                        v-model="selectConfigTelegram"
                                        id="checkboxes-3"
                                        :aria-describedby="ariaDescribedby"
                                        >
                                            <b-form-checkbox class="ln" value="new_ticket">{{$t('bs-new-tickets')}}</b-form-checkbox><br>
                                            <!-- <b-form-checkbox value="ticket_answered">Tickets repondidos</b-form-checkbox> -->
                                        </b-form-checkbox-group>
                                    </b-form-group>
                                    <label for="inline-form-input-name"><b>{{$t('bs-departments')}}: </b></label>
                                    <b-form-group id="input-group-4"
                                        label-for="input-current-ticket"
                                        v-slot="{ ariaDescribedby }"
                                    >
                                        <b-form-checkbox-group
                                        v-model="selectedDepartments"
                                        id="checkboxes-4"
                                        :aria-describedby="ariaDescribedby"
                                        >
                                            <span v-for="(item, index) in DepartmentsOptions"  :key="index">
                                                <b-form-checkbox class="ln" :value="item.id">{{$t(item.name)}}</b-form-checkbox><br>
                                            </span>
                                        </b-form-checkbox-group>
                                    </b-form-group>
                                    <label for="inline-form-input-name"><b>{{$t('bs-receive-notifications')}}: </b></label>
                                    <b-form-group v-slot="{ ariaDescribedby }">
                                        <b-form-radio v-model="notyOff" class="ln" :aria-describedby="ariaDescribedby" name="some-radios" value="forever">{{$t('bs-ever')}}</b-form-radio>
                                        <b-form-radio v-model="notyOff" class="ln" :aria-describedby="ariaDescribedby" name="some-radios" value="online">{{$t('bs-only-when-offline')}}</b-form-radio>
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
                        <button
                            @click="onSubmitProfile()"
                            type="button"
                            class="btn btn-success"
                        >
                            {{ $t("bs-save") }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- fim modal telegram  -->

        <privacy-terms :user="usuario" :userLang="userLang"> </privacy-terms>

        <cookies-accept
            v-if="cookies_not_accepted"
            :usuario="usuario"
        ></cookies-accept>
    </div>
</template>

<script>
import { languages } from "../../../../static/translation/select";
// import { saveLanguage } from '../../services/user';
import { mapState, mapMutations } from "vuex";

export default {
    data() {
        return {
            avatarEdit: false,
            mainProps: { blank: true, width: 50, height: 50, class: "m1" },
            csrf: document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            languages: languages,
            userLang: this.$i18n.locale,
            //sidebar_is_mini: false,
            status_options: [
                {
                    key: "online",
                    desc: this.$t("bs-online")
                },
                {
                    key: "busy",
                    desc: this.$t("bs-busy")
                },
                {
                    key: "appear_away",
                    desc: this.$t("bs-appear-away")
                }
            ],
            status_selected: "",
            form: {
                name: "",
                email: "",
                country: "",
                subsidiary_id: "",
                current_password: "",
                password: "",
                password2: ""
            },
            formState: {
                name: false,
                email: false,
                country: false,
                password: null,
                password2: null
            },
            subsidiaries: [],
            showCurrentPassword: true,
            editPerfilAttendants: Boolean,
            user_uuid: "",
            cookies_not_accepted: false,
            customer_service: false,
            selectConfigNoty: [],
            selectConfigTelegram: [],
            valueEnv: '',
            is_hs: '',
            options: [
                { text: 'Novos Chats', value: 'new_chat' },
                { text: 'Chats repondidos', value: 'chat_answered' },
                { text: 'Novos Tickets', value: 'new_ticket' },
                { text: 'Tickets repondidos', value: 'ticket_answered' }
            ],
            notyOff: '',
            DepartmentsOptions: [],
            selectedDepartments: [],
            notLink: '',
            hiddenDate: Date,
        };
    },
    props: {
        session_user_company: Object,
        is_admin: String,
        usuario: Object,
        csname: String,
        cslogo: String,
        csid: String,
        gravatar: String,
        base_url: {
            type: String,
            default: ""
        }
    },
    created() {
        // this.status_selected = this.$status.get(this.usuario.id);
        if (window.location.search == "?module=chat" || window.location.search == "?module=ticket" || window.location.search == "?module=category") {
            this.customer_service = true;
        }

        document.addEventListener("visibilitychange", () => {
            const state = document.visibilityState;
            if (state === "hidden") {
                this.hiddenDate = new Date();
            }

            if (state === "visible") {
                var diff = Math.abs(new Date() - this.hiddenDate);
                var minutes_off =   Math.floor((diff/1000)/60);

                if (minutes_off > 1 && $(window).width() <= 992) {
                    window.location.reload();
                } else if (minutes_off > 14) {
                    window.location.reload();
                }
            }
        });

        if(this.usuario.config == null){
            this.usuario.config = {
                "fontSize": '16px',
                'signature': '',
                "notification": [
                    {"email": ""},
                    {"system":""},
                    {"telegram":""},
                    {"whatsapp":""},
                ],
            }
        }
        



    },
    mounted() {
        if (localStorage.getItem("profile-updated") == 1) {
            this.$snotify.success(
                this.$t("bs-profile-updated"),
                this.$t("bs-saved")
            );
            localStorage.removeItem("profile-updated");
        }
        if (!this.languageAvaliable) {
            this.userLang = "en_US";
        }
        this.$store.commit("changenamecompany", this.csname);
        //console.log('aqui', this.gravatar);
        // this.$root.$on('toggleSidebar', () => {
        //     this.sidebar_is_mini = !this.sidebar_is_mini
        // })
        this.$store.state.csid = this.csid;
        this.getProfileSettings();
        this.CheckUser_uuid();
        if (!this.usuario.cookies_accepted) {
            this.cookies_not_accepted = true;
        }
        this.valueEnv = process.env.MIX_APP_ENV;
        this.is_hs = process.env.MIX_APP_IS_HELPDESK;
    },
    methods: {
        ...mapMutations(["online"]),
        ...mapMutations(["toggleSidebarMini"]),
        changeStatus() {
            axios
                .post(`change-status`, {
                    status: this.status_selected
                })
                .then(({ data }) => {
                    if (data.status) {
                        this.online();
                    }
                });
        },
        openModalPassword() {
            $("#modalProfile").modal("hide");
            $("#modalPassword").modal("show");
        },
        openModalTelegram() {
            axios.get("/get/config-telegram", {
            }).then(res => {
                // console.log(res);
                try {
                    this.selectConfigTelegram = JSON.parse(res.data[0].config)[0];
                    this.notyOff = JSON.parse(res.data[0].config)[1];
                    this.selectedDepartments = JSON.parse(res.data[0].config)[2];
                    this.selectConfigNoty = JSON.parse(res.data[0].config)[3];
                    
                    this.session_user_company.telegram_chat_id = res.data[0].telegram_chat_id;
                } catch (e) {
                    console.log(e.message)
                }
                this.DepartmentsOptions = res.data[1];
         
                $("#modalProfile").modal("hide");
                $("#modalTelegram").modal("show");
            })
            .catch(err => {
                console.error(err);
            });
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
            axios.get("/department/get-subsidiary", {})
                .then(res => {
                    this.subsidiaries = res.data.result;
                })
                .catch(err => {
                    console.error(err);
                });
            if (this.form.name == "") this.form.name = this.usuario.name;
            if (this.form.email == "") this.form.email = this.usuario.email;
            if (this.form.subsidiary_id == "")
                this.form.subsidiary_id = this.usuario.subsidiary_id;
            $("#modalProfile").modal("show");
        },
        onSubmitProfile() {
            if (this.formState.name == false) {
                this.$snotify.error(
                    this.$t("bs-name"),
                    this.$t("bs-invalid-field")
                );
            } else if (this.formState.email == false) {
                this.$snotify.error("Email", this.$t("bs-invalid-field"));
            } else if (
                this.form.current_password != "" &&
                (this.formState.password == false ||
                    this.formState.password == null)
            ) {
                this.$snotify.error(
                    this.$t("bs-new-password"),
                    this.$t("bs-invalid-field")
                );
            } else if (
                this.form.current_password != "" &&
                (this.formState.password2 == false ||
                    this.formState.password2 == null)
            ) {
                this.$snotify.error(
                    this.$t("bs-confirm-password"),
                    this.$t("bs-invalid-field")
                );
            } else {
                axios
                    .post("user/update", {
                        user_uuid: this.user_uuid,
                        name: this.form.name,
                        email: this.form.email,
                        subsidiary_id: this.form.subsidiary_id,
                        current_password: this.form.current_password,
                        new_password: this.form.password,
                        selectConfigTelegram: [this.selectConfigTelegram, this.notyOff,  this.selectedDepartments, this.selectConfigNoty],
                    })
                    .then(res => {
                        if (res.status == 203) {
                            this.$snotify.error(
                                this.$t("bs-current-password"),
                                this.$t("bs-invalid-field")
                            );
                        } else if (res.status == 200) {
                            localStorage.setItem("profile-updated", 1);
                            location.reload();
                        }
                    });
            }
        },
        backToModalProfile() {
            $("#modalPassword").modal("hide");
            $("#modalTelegram").modal("hide");
            $("#modalProfile").modal("show");
        },
        checkstatusSave(){
            console.log(this.$status.get(this.usuario.id));
            axios.get("user/get-user-status", {}).then(({ data }) => {
                if(data == null){
                    return this.$status.get(this.usuario.id);
                }else{
                    return data.status+'';
                }
            });
        },
        returnStatus() {
            const checkPromise = new Promise((resolve, reject) => {
                if (this.$status.get(this.usuario.id) == "online") {
                    this.status_selected = this.$status.get(this.usuario.id);
                    resolve();
                } else if (this.$status.get(this.usuario.id) == "busy") {
                    this.status_selected = this.$status.get(this.usuario.id);
                    resolve();
                } else if (this.$status.get(this.usuario.id) == "appear_away") {
                    this.status_selected = this.$status.get(this.usuario.id);
                    resolve();
                } else if (this.$status.get(this.usuario.id) == "offline") {
                    this.status_selected = this.$status.get(this.usuario.id);
                    resolve();
                } 
            });
            
            checkPromise.then(() => {
                if (this.$status.get(this.usuario.id) == "online") {
                    return this.$t("bs-online");
                } else if (this.$status.get(this.usuario.id) == "busy") {
                    return this.$t("bs-busy");
                } else if (this.$status.get(this.usuario.id) == "appear_away") {
                    return this.$t("bs-appear-away");
                } else if (this.$status.get(this.usuario.id) == "offline") {
                    return "Offline";
                } 
            });
        },
        saveLanguage() {
            var vm = this;
            // base_url necessario para rotas de mais de 1 nivel
            // OBS.: tente mudar o idioma dentro da rota /agents/agent-info-dashboard sem ele
            var url = `${this.base_url}/agents/update-language`;
            axios
                .post(url, {
                    id: this.usuario.id,
                    language: this.userLang
                })
                .then(function(response) {
                    //console.log(response.data.created);
                    if (response.data.success) {
                        location.reload();
                    } else {
                        vm.$snotify.error(
                            this.$t("bs-error-save"),
                            this.$t("bs-error")
                        );
                    }
                })
                .catch(function(error) {
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
        goToGravatar() {
            window.open("https://gravatar.com/");
        },
        preventAutocomplete() {
            if (this.form.current_password.trim() === "") {
                this.showCurrentPassword = false;
                setTimeout(() => {
                    this.showCurrentPassword = true;
                }, 4);
                setTimeout(() => {
                    $("#input-current-password").focus();
                }, 200);
            }
        },
        getProfileSettings() {
            axios
                .get("/company-config/general-settings", {
                    params: {
                        company_id: this.$store.state.company
                    }
                })
                .then(({ data }) => {
                    if (data != undefined) {
                        this.editPerfilAttendants = data.editPerfilAttendants;
                    }
                });
        },
        CheckUser_uuid() {
            axios.get("user/get-user-uuid", {}).then(({ data }) => {
                this.user_uuid = data.user_uuid;
            });
        },
        setAltImg(event) {
            //PLANO FUTURO PARA A IMAGEM DO CLIENTE FICAR NO NAVBAR- CANCELADO POR FALTA DE TEMPO...
            // console.log(event);
            // var img = new Image();
            // img.onload = function() {
            //   // alert(this.width + 'x' + this.height);
            //   if(this.width == this.height){
            //   }
            // }
            // img.src = 'https://ba-support.builderall.io/images/icons/companhia.svg';

            event.target.src = "/images/bs/BA_Helpdesk_Logo.svg";
        },
        logout() {
            // importante remover isso para não mostrar ticket de outro departamento.
            localStorage.removeItem("preselectDepartment");
            localStorage.setItem("loginAuto", false);
        }
    },

    computed: {
        ...mapState(["sidebar_is_mini"]),

        languageAvaliable() {
            return this.languages.find(el => el.key == this.$i18n.locale);
        },
        vName() {
            this.formState.name =
                this.form.name.length > 1 && this.form.name.length < 100;
            return this.formState.name;
        },
        vEmail() {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            this.formState.email = re.test(this.form.email);
            return this.formState.email;
        },
        vPassword() {
            this.formState.password =
                this.form.password.length > 5 &&
                this.form.password.length < 255;
            if (this.form.password == "") this.formState.password = null;
            return this.formState.password;
        },
        vPassword2() {
            this.formState.password2 =
                this.form.password == this.form.password2;
            if (this.form.password2 == "") this.formState.password2 = null;
            return this.formState.password2;
        },
        typeCurrentPassword() {
            if (this.form.current_password) {
                return "password";
            } else {
                return "text";
            }
        },
    },
    watch: {
        "selectConfigTelegram": function() { // GAMBIARRA PARA SELECIONAR MAIS DE 1 CHECKBOX DE UMA VEZ.
            if(typeof this.selectConfigTelegram === typeof ''){
                this.selectConfigTelegram = ['new_chat', 'new_ticket'];
            }
        },
        "selectConfigNoty": function() { // GAMBIARRA PARA SELECIONAR MAIS DE 1 CHECKBOX DE UMA VEZ.
            if(typeof this.selectConfigNoty === typeof ''){
                this.selectConfigNoty = ['new_chat', 'new_ticket'];
            }
        },
        "selectedDepartments": function() {
            if(typeof this.selectedDepartments === typeof ''){
                this.selectedDepartments = [];
            }
        },
        "form.current_password": function() {
            if (this.form.current_password.trim() === "") {
                document.getElementById(
                    "input-current-password"
                ).readOnly = true;
                setTimeout(() => {
                    document.getElementById("input-current-password").type =
                        "text";
                    document.getElementById(
                        "input-current-password"
                    ).readOnly = false;
                    $("#input-current-password").keydown();
                }, 200);
            } else {
                document.getElementById("input-current-password").type =
                    "password";
            }
        },
    }
};
</script>

<style lang="scss">
@import "./resources/sass/variables";

.lb {
    font-weight: bold;
}

.ln {
    font-weight: normal;
}

.badge-status {
    font-size: 10px !important;
}

.select-status .vs__dropdown-menu,
.select-status .vs__dropdown-menu:hover {
    border: none;
    color: #394066;
    text-transform: lowercase;
    font-variant: small-caps;
}

.dropdown-item.profile-data.active,
.dropdown-item.profile-data:active,
.dropdown-item.profile-data:hover {
    color: #212529;
    text-decoration: none;
    background-color: transparent;
    cursor: default;
}

.select-status {
    background-color: white !important;
    height: 18px;
    width: fit-content;
}

.select-status .vs__open-indicator {
    fill: transparent !important;
}

.select-status .vs__dropdown-toggle {
    cursor: pointer !important;
    height: 18px;
    padding: 0px !important;
    border: none !important;
}

.select-status .vs__selected {
    margin: 0;
    padding: 0;
    font-size: 13px !important;
    color: #212529;
}

.select-status .vs__search {
    margin: 0 !important;
    padding: 0 !important;
}

.bb-navbar {
    box-shadow: 0 1px 2px rgba(38, 36, 36, 0.14);
    border: 1px solid #dedede;
    height: $header-height;
    flex-flow: row nowrap;
    justify-content: flex-start;
    padding: 0;

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
        background: rgba(244, 244, 244, 0.7);
        height: calc(#{$header-height} - 2px);
        border-right: 1px solid rgba(222, 222, 222, 0.7);
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
                        &:nth-child(3) {
                            font-size: 13px;
                        }
                    }
                }
            }
        }
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

.navbar-brand {
    padding-left: 1.5rem !important;
}

@media screen and (max-width: 992px) {
    .navbar-brand {
        padding-left: 0.5rem !important;
    }
}
</style>
