<template>
    <company-body
		:usuario="usuario"
		:is_helpdesk="is_helpdesk"
		:csid="csid"
        :base_url="base_url"
		:viewcontact="viewcontact"
        :company_list="companyList"
        @create-company="saveRedirect"
        @go-back="cancelRedirect"
        @update-company="updateRedirect"
    ></company-body>
</template>

<script>
export default {
    namd: 'company-edit-controller',
	props:{
        usuario: Object,
        is_helpdesk: String,
        csid: String,
        viewcontact: Boolean,
        cancelHref: String,
        saveHref: String,
        updateHref: String,
        base_url: {
            type: String,
            default: ''
        },
    },
    data: function() {
        return {
            companyList: []
        }
    },
    methods: {
        saveRedirect(){
            window.open(this.saveHref, '_self')
        },
        cancelRedirect(){
            window.open(this.cancelHref, '_self')
        },
        updateRedirect(){
            //no redirect
            //window.open(this.updateHref, '_self')
        },

    },
	mounted(){
		var vm = this;
		var url = `${this.base_url}/company/get-company`;
		axios.get(url).then(function(r_resposta){
			vm.companyList = r_resposta.data.result;
		}).catch(function (error) {
			console.log(error);
		});
	},
}
</script>

<style lang="scss" scoped>
</style>