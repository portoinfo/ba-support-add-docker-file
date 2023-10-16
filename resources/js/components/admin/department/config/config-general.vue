<template>
  <div>
    <b-container fluid>
      <b-form-group class="bs-label" id="input-group-3" label-for="input-3">
        <template v-slot:label>
          <span id="tooltip-general-language">
            {{ lbPais }}&nbsp;<i class="fa fa-question-circle" aria-hidden="false"></i>
          </span>
        </template>
        <v-select
          v-model="settings.userLang"
          :options="options"
          @input="updateOptions"
          class="mb-3"
          multiple
          :close-on-select="false"
          :searchable="false"
          ref="vSelectLanguage"
        >
        <template #selected-option="{ label, code }">
            <img
              v-if="code != 'ALL'"
              :src="`https://flagcdn.com/24x18/${code.toLowerCase()}.png`"
              class="mr-2"
            />
            {{ label }}
          </template>
          <template v-slot:option="{ label, code }">
            <img
              v-if="code != 'ALL'"
              :src="`https://flagcdn.com/24x18/${code.toLowerCase()}.png`"
              class="mr-2"
            />
            {{ label }}
          </template>
        </v-select>
      </b-form-group>
      <b-tooltip
        target="tooltip-general-language"
        triggers="hover"
        placement="right"
        variant="secondary"
      >
        {{ $t("bs-tooltip-general-language") }}
      </b-tooltip>

      <b-form-group class="bs-label" id="input-group-3" label-for="input-3">
        <template v-slot:label>
          <span id="tooltip-general-region">
            {{ lbRegion }}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
          </span>
        </template>
        <base-timezone-selector
          :timezones="timezones"
          v-model="settings.language"
        ></base-timezone-selector>
      </b-form-group>
      <b-tooltip
        target="tooltip-general-region"
        triggers="hover"
        placement="right"
        variant="secondary"
      >
        {{ $t("bs-tooltip-general-region") }}
      </b-tooltip>

      <b-form-group
        class="bs-label"
        id="input-group-3"
        :label="lbModule"
        label-for="input-3"
      >
        <template v-slot:label>
          <span id="tooltip-general-module">
            {{ lbModule }}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
          </span>
        </template>
        <v-select v-model="settings.module" :options="optionsModule" class="mb-3">
        </v-select>
      </b-form-group>
      <b-tooltip
        target="tooltip-general-module"
        triggers="hover"
        placement="right"
        variant="secondary"
      >
        {{ $t("bs-tooltip-general-module") }}
      </b-tooltip>

      <b-form-group class="bs-label" id="input-group-1" label-for="input-1">
        <template v-slot:label>
            <span id="tooltip-auto-answer-command">
                {{lbKey}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
            </span>
        </template>
        <b-form-input
        id="input-1"
        @input="removeSpaces"
        v-model="settings.keyDepartment"
        required
        class="bs-input"
        ></b-form-input>
    </b-form-group>

      
    </b-container>
    <br /><br /><br />
  </div>
</template>

<script>
export default {
  data() {
    return {
      preferencesTimezone: "",
      languages: [],
      lbPais: this.$t("bs-language"),
      lbModule: this.$t("bs-module"),
      lbRegion: this.$t("bs-region"),
      lbKey: this.$t("bs-key"),
      userLang: "",
      selected: "ALL",
      options: [],
      optionsModule: [
        { code: "ALL", label: "ALL" },
        { code: "CHAT", label: "CHAT" },
        { code: "TICKET", label: "TICKET" },
      ],
      aux: [],
      countrySelected: "",
      languages: this.$store.state.languages,
      keydepartment: '',
    };
  },
  created() {
    this.translateOptionDefault();
    this.getCountryOptions();
  },
  props: {
    itemselected: Object,
    settings: Object,
    idSettings: String,
    timezones: Object,
    base_url: {
      type: String,
      default: "",
    },
  },
  methods: {
    removeSpaces() {
      // Remove espaços em branco do valor do campo
      this.settings.keyDepartment = this.settings.keyDepartment.replace(/\s/g, '');
    },
    getCountryOptions() {
      // push no "Todos" na primeira posição com key 0 (default);
      this.options.push({ key: 0, code: "ALL", label: this.$t("bs-all") });

      // array de languages do store.js
      if (this.languages.length) {
        var key = 1;
        this.languages.forEach((element) => {
          // pego apenas o codigo do país, ex: pt_BR -> BR;
          let code = element.key.split("_")[1];
          this.options.push({ key: key, code: code, label: element.desc });
          key++;
        });
      }
    },
    updateOptions(value) {
      value.forEach((element) => {
        if (element.code == "ALL" && value[0] !== "ALL") {
          this.settings.userLang = value.filter(this.filterAll);
        } else {
          this.settings.userLang = value.filter(this.filter);
        }

        setTimeout(() => {
          let index = this.settings.userLang.findIndex((item) => item.code === "ALL");

          if (index !== -1) {
            const searchEl = this.$refs.vSelectLanguage.searchEl;
            if (searchEl) {
              searchEl.blur();
            }
          }
        }, 4);
      });
    },
    filterAll(obj) {
      if ("code" in obj && obj.code === "ALL") {
        return true;
      }
    },
    filter(obj) {
      if ("code" in obj && obj.code !== "ALL") {
        return true;
      }
    },
    translateOptionDefault() {
      this.settings.userLang.forEach((element) => {
        if (element.code == "ALL") {
          element.label = this.$t("bs-all");
        }
      });
    },
  },
};
</script>

<style scoped></style>
