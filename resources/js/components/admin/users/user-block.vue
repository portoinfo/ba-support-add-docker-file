<template>
    <div class="top-bar" >
        <center>
            <i style="font-size: 160px;color: white;" class="fa fa-times-circle fa-5x" aria-hidden="false"></i><br>
            <span class="title">{{$t('bs-access-denied')}}</span><br>
            <span class="subtitle">{{$t('bs-hi-please-be-advised-that-your-account-ha')}}</span><br>
            <hr style="background:white;">
            
            <span class="subtitle">{{$t('bs-for-more-information-contact-support')}}: <a href="#" style="color: white;">{{email}}</a></span><br>
        </center>
    </div>
</template>

<script>

export default {
	data(){
		return {
			email: "financeiro.brasil@builderall.com",
		}
	},
    mounted(){
		var userLang = navigator.language || navigator.userLanguage; 
		this.$i18n.locale = userLang.replace('-', '_');

        if(this.$i18n.locale == 'pt_BR'){
            this.email = 'financeiro.brasil@builderall.com';
        }else{
            this.email = 'abuse@builderall.com';
        }
	},
};
</script>

<style scoped>

center {
  margin: 0;
    position: absolute;
    top: 50%;
    left: 50%;
    margin-right: -50%;
    transform: translate(-50%, -50%) 
}

 .top-bar {
    position: absolute;
    width: 100%;
    height: 100%;
    background-color: #F54B5E;
    margin: 0;
    padding: 0;
 }
 
.title{
    text-align: left;
    font: normal normal bold 50px/74px Muli;
    letter-spacing: 0px;
    color: #FFFFFF;
    opacity: 1;
}

.subtitle{
    text-align: left;
    font: normal normal normal 24px/35px Muli;
    letter-spacing: 0px;
    color: #FFFFFF;
    opacity: 1;
}

</style>