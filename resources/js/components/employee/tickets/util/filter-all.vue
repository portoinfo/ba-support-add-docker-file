<template>
    <div>
        <b-form-group class="mt-4" v-slot="{ ariaDescribedby }">
            <b-form-radio
                @change="setTypeSelected"
                v-model="$root.$refs.FullTicket2.selected"
                :aria-describedby="ariaDescribedby"
                name="some-radios"
                value="filter-nameComplete"
                >{{ $t("bs-full-name") }}
            </b-form-radio>
            <b-form-radio
                @change="setTypeSelected"
                v-model="$root.$refs.FullTicket2.selected"
                :aria-describedby="ariaDescribedby"
                name="some-radios"
                value="filter-id"
            >
                ID
            </b-form-radio>
            <b-form-radio
            @change="setTypeSelected"
                v-model="$root.$refs.FullTicket2.selected"
                :aria-describedby="ariaDescribedby"
                name="some-radios"
                value="filter-description"
            >
                {{ $t("bs-description") }}
            </b-form-radio>
            <b-form-radio
                @change="setTypeSelected"
                v-model="$root.$refs.FullTicket2.selected"
                :aria-describedby="ariaDescribedby"
                name="some-radios"
                value="filter-email"
            >
                E-mail
            </b-form-radio>
            <b-form-radio
                v-show="!$root.$refs.FullTicket2.filter_my_tickets"
                @change="setTypeSelected"
                v-model="$root.$refs.FullTicket2.selected"
                :aria-describedby="ariaDescribedby"
                name="some-radios"
                value="filter-operator"
            >
                {{ $t("bs-operator") }}
            </b-form-radio>
            <b-form-radio
                @change="setTypeSelected"
                v-model="$root.$refs.FullTicket2.selected"
                :aria-describedby="ariaDescribedby"
                name="some-radios"
                value="filter-comment"
            >
                {{ $t("bs-comment") }}
            </b-form-radio>
        </b-form-group>

        <div class="input-group mt-2" v-show="$root.$refs.FullTicket2.selected">
            <div class="form-outline w-100">
                <input
                    type="search"
                    ref="searchf"
                    v-model="$root.$refs.FullTicket2.searchQuery"
                    class="form-control"
                    :placeholder="placeholder"
                />
            </div>
        </div>
    </div>
</template>

<script>

export default {
	data(){
		return {
			admin: this.session_user_permissions[0]["chat_admin"],
			company_department: {},
			departments: [],
			title: this.$t("bs-ticket-chat-filtering"),
			isMobile: false,
			showMenuMobile: false,
			list: [],
			searchInterval: null,
			searchIntervalTimeout: 2000, // 2s
			typeOptions: [
			{ value: 'TICKET', text: this.$t("bs-ticket") },
			],
			typeSelected: 'TICKET',
			statusOptions: [
			{ value: 'ALL', text: this.$t("bs-all") },
      { value: 'IN_PROGRESS', text: this.$t("bs-in-progress") },
			{ value: 'CLOSED', text: this.$t("bs-closed-s") },
			{ value: 'RESOLVED', text: this.$t("bs-resolved-s") },
			{ value: 'CANCELED', text: this.$t("bs-canceled-s") },
			],
			statusSelected: 'ALL',
			departmentSelected: 'ALL',
		}
	},
	props: {
	    session_user: Object,
	    session_user_company: Object,
	    session_user_cucd: Array,
	    session_user_departments: Array,
	    session_user_permissions: Array,
        is_admin: Number,
  	},
    mounted(){
        this.$refs["searchf"].focus();
    },
    computed: {
        placeholder() {
            var selected = this.$root.$refs.FullTicket2.selected;
            switch (selected) {
                case 'filter-nameComplete':
                    var placeholder = this.$t("bs-full-name");
                    break;
                case 'filter-id':
                    var placeholder = 'ID'
                    break;
                case 'filter-description':
                    var placeholder = this.$t("bs-description")
                    break;
                case 'filter-email':
                    var placeholder = 'E-mail'
                    break;
                case 'filter-operator':
                    var placeholder = this.$t("bs-operator")
                    break;
                case 'filter-comment':
                    var placeholder = this.$t("bs-comment")
                    break;
            }
            return placeholder;
        }
    },
  	created() {
  		if(this.session_user_permissions[0].ticket_close == 0){
  			this.statusOptions = [
				  { value: 'ALL', text: this.$t("bs-all") },
  				{ value: 'CLOSED', text: this.$t("bs-closed-s") },
          { value: 'IN_PROGRESS', text: this.$t("bs-in-progress") },
          { value: 'RESOLVED', text: this.$t("bs-finalized-s") },
  				{ value: 'CANCELED', text: this.$t("bs-canceled-s") },
        ];
  		}

	    if (localStorage.getItem("fullfilterselected") == null) {
	      localStorage.setItem("fullfilterselected", "filter-id");
	    } else {
	      this.$root.$refs.FullTicket2.selected = localStorage.getItem("fullfilterselected");
	    }
	  },
	methods: {
    statusFormat(status) {
      switch (status) {
        default:
        case "CLOSED":
          return this.$t("bs-closed");
          break;
        case "RESOLVED":
          return this.$t("bs-resolved");
          break;
        case "CANCELED":
          return this.$t("bs-canceled");
          break;
      }
    },
    setTypeSelected() {
      localStorage.setItem("fullfilterselected", this.$root.$refs.FullTicket2.selected);
    },
    searchIntervalFunc: function (vm = this) {
      clearInterval(vm.searchInterval);
      vm.searchInterval = null;
    },
  },

};
</script>

<style scoped>
.ticket-information::v-deep {
	overflow-y: auto!important;
	padding: 10px 0;
	width: 100%!important;
}

.selected{
	color: blue;
	font-size: 20px;
}
</style>
