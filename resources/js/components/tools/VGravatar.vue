<template>
    <v-avatar v-if="robot" :size="size">
        <v-img :src="`${$store.state.baseURL}/images/client/robo.png`"></v-img>
    </v-avatar>
	<v-badge
        v-else-if="status !== 'false'"
		bottom
		:color="getBadgeStatusVariant()"
		dot
		offset-x="10"
		offset-y="10"
	>
		<v-avatar :size="size" :color="bg_color">
            <span :style="{'color':text_color}" class="absolute font-weight-bold">{{ $li(name) }}</span>
			<v-img :src="gravatar"></v-img>
		</v-avatar>
	</v-badge>
    <v-avatar v-else :size="size" :color="bg_color">
        <span :style="{'color':text_color}" class="absolute font-weight-bold">{{ $li(name) }}</span>
        <v-img :src="gravatar"></v-img>
    </v-avatar>
</template>

<script>
export default {
	props: {
		email: "",
		status: String,
		size: {
            type: String,
            default: '40'
        },
		name: String,
		color: undefined,
		ba_acct_data: null,
        robot: {
            type: Boolean,
            default: false
        },
	},
	computed: {
		cEmail: {
			get() {
				return this.email_aux;
			},
			set(value) {
				this.email_aux = value;
			},
		},
	},
	data() {
		return {
			gravatar_404: false,
			gravatar: "",
			variant: "primary",
			show_badge: true,
			show: true,
			email_aux: this.email,
            text_color: "",
            bg_color: ""
		};
	},
	created() {
        if (!this.robot) {
            if (this.color !== undefined) {
                this.variant = this.color;
            }
            this.forceCleanEmail();
            if (this.status == "false") {
                this.show_badge = false;
            }
            this.checkBAData();
        }
	},
    mounted () {
        if (!this.robot) {
            this.gravatarColor();
        }
    },
	watch: {
		"$store.state.csid": function (v) {
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
			setTimeout(() => {
				this.get_gravatar();
				this.show = true;
				this.checkBAData();
			}, 200);
		},
	},
	methods: {
		gravatarColor() {
            var stc = require("string-to-color");
            var tinycolor = require("tinycolor2");
            this.bg_color = stc(this.cEmail);

            var t_color = tinycolor(this.bg_color);
            if (t_color.isLight()) {
                this.text_color = "#333333";
            } else if (t_color.isDark()) {
                this.text_color = "white";
            }
		},
		checkBAData() {
			if (this.ba_acct_data) {
				let data = JSON.parse(this.ba_acct_data);
			}
		},
		getBadgeStatusVariant() {
			if (this.status == "online") {
				return "success";
			} else if (this.status == "busy") {
				return "error";
			} else if (this.status == "offline") {
				return "secondary";
			} else if (this.status == "appear_away") {
				return "warning";
			}
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
            if (this.cEmail) {
			    this.gravatar = `https://www.gravatar.com/avatar/${md5(this.cEmail)}?s=${this.size}&d=blank`;
            }
            this.gravatarColor();
		},
	},
};
</script>

<style scoped>
.absolute {
    position: absolute;
}
</style>