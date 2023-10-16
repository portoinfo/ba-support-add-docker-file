<template>
    <div :class='
        size == "56px" ? "ba-avatar ba-xl" : 
        size == "48px" ? "ba-avatar ba-lg" : 
        size == "40px" ? "ba-avatar ba-md" : 
        size == "32px" ? "ba-avatar ba-sm" : 
        "ba-avatar image"+size
    ' >
        <span :class='"ba-image ba-notification img-img "+ status'>
            <span 
                ba-tooltip-position='left' 
                :ba-tooltip-title='name'
            >
                <img v-if="cEmail != 'robot'" :src='gravatar' alt='avatar'>
                <img v-else :src="`${$store.state.baseURL}/images/client/robo.png`" alt='avatar'>
            </span>
            
            <span 
                ba-tooltip-position='left' 
                :ba-tooltip-title='status'
            >
                <span v-if="status == 'online'" class='ba-circle ba-green'></span>
                <span v-if="status == 'appear_away'" class='ba-circle ba-yellow'></span>
                <span v-if="status == 'busy'" class='ba-circle ba-red'></span>
            </span>
        </span>
    </div>
</template>
<script>
export default {
	data(){
		return {
            gravatar: '',
            status: ''
		}
	},
    props: {
        cEmail: "",
        id: "",
        size: String,
        name: String,
        ba_acct_data: null,
    },
	created(){
        this.getforceCleanEmail();
        this.get(this.id);
        // this.checkBAData();
	},
	methods: {
        get(id) {
            let online_index = this.$store.state.online_users.findIndex((item) => item.id === id || item.hash_id === id);
            if (online_index !== -1) {
                this.status = this.$store.state.online_users[online_index].status;
            } else {
                this.status = "offline";
            }
        },
        checkBAData() {
            if (this.ba_acct_data) {
                let data = JSON.parse(this.ba_acct_data);
                if (data.is_vip) {
                this.vip = true;
                }
            }
        },
        getforceCleanEmail() {
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
	},
};
</script>

<style scoped>
    .image56{
        min-width: 56px;
        max-width: 56px;
        min-height:56px;
        max-height:56px;
        font-size: 22.4px;
    }
    .image48{
        min-width: 18px;
        max-width: 18px;
        min-height:18px;
        max-height:18px;
        font-size: 119.2px;
    }
    .image40{
        min-width: 18px;
        max-width: 18px;
        min-height:18px;
        max-height:18px;
        font-size: 16px;
    }
    .image32{
        min-width: 18px;
        max-width: 18px;
        min-height:18px;
        max-height:18px;
        font-size: 11.2px;
    }
    .image18{
        min-width: 18px;
        max-width: 18px;
        min-height:18px;
        max-height:18px;
        font-size: 10px;
    }

    .ba-avatar .ba-notification:after {
        content: "";
        border-radius: 50%;
        position: absolute;
    }

    .ba-avatar .ba-image img{
        padding: unset !important;
    }
    .img-img {
        border-radius: 100%;
    }
</style>