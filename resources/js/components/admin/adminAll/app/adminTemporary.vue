<template>
    <div style="height: 100%; position: absolute; bottom: 0; width: 100%; overflow: hidden !important;">
        <iframe :src="linkURL" frameborder="0" width="100%" height="100%"></iframe>
        <!-- <iframe
                sandbox="
                    allow-storage-access-by-user-activation
                    allow-scripts
                    allow-same-origin
                    allow-forms
                    allow-modals
                    allow-pointer-lock
                    allow-popups
                    allow-popups-to-escape-sandbox
                    allow-top-navigation
                    allow-top-navigation-by-user-activation
                "
                width="100%"
                height="100%"
                src="http://localhost:8000"
                title="BA HelpDesk"
            >
        </iframe> -->
    </div>
</template>

<script>

export default {
	data(){
		return {
            linkURL: '',
		}
	},
	created(){

       


        const urlString = window.location.href;
        const url = new URL(urlString);
        const urlPrincipal = url.protocol + "//" + url.host;
        
        //VERIFICAÇÃO PARA IR PARA TELA DE SELECIONAR COMPANHIA
        if(this.csid == ''){
            this.linkURL = urlPrincipal+'/select-company';
        }else{
            // CASO DER F5 CAIR NA ULTIMA TELA SELECIONADA.
            this.linkURL = urlPrincipal;
            if(localStorage.getItem("lastPageLocation") != null){
                if (localStorage.getItem("lastPageLocation").includes('module=chat')) {
                    this.linkURL = urlPrincipal+'/customer-service?module=chat';
                }else if (localStorage.getItem("lastPageLocation").includes('module=ticket')) {
                    this.linkURL = urlPrincipal+'/customer-service?module=ticket';
                }else if (localStorage.getItem("lastPageLocation").includes('module=filter')) {
                    this.linkURL = urlPrincipal+'/customer-service?module=filter';
                }else if (localStorage.getItem("lastPageLocation").includes('module=category')) {
                    this.linkURL = urlPrincipal+'/customer-service?module=category';
                }else if (localStorage.getItem("lastPageLocation").includes('company')) {
                    this.linkURL = urlPrincipal+'/company';
                }else if (localStorage.getItem("lastPageLocation").includes('department')) {
                    this.linkURL = urlPrincipal+'/department';
                }else if (localStorage.getItem("lastPageLocation").includes('group')) {
                    this.linkURL = urlPrincipal+'/group';
                }else if (localStorage.getItem("lastPageLocation").includes('agents')) {
                    this.linkURL = urlPrincipal+'/agents';
                }else if (localStorage.getItem("lastPageLocation").includes('company-integration')) {
                    this.linkURL = urlPrincipal+'/company-integration';
                }else if (localStorage.getItem("lastPageLocation").includes('user-client')) {
                    this.linkURL = urlPrincipal+'/user-client';
                } 
            }

            if(this.type == 'client'){
                
                if(urlString.includes('customer-chat')){
                    this.linkURL = urlPrincipal+'/customer-chat';
                } else if(urlString.includes('customer-ticket')){
                    this.linkURL = urlPrincipal+'/customer-ticket';
                }else{
                    this.linkURL = urlPrincipal+'/customer-home';
                }
                
                // Obtenha a URL da página atual
                const currentURL = new URL(window.location.href);

                // Obtenha os parâmetros da URL
                const params = new URLSearchParams(currentURL.search);

                // Verifique se o parâmetro "fast-ticket" existe na URL
                if (params.has("fast-ticket")) {
                    this.linkURL = urlPrincipal+'/create-fast-ticket';
                }

                setTimeout(() => {
                    // Selecione o elemento que você deseja remover
                    const headerMessage = document.querySelector('.header-message');
                    // Verifique se o elemento foi encontrado
                    if (headerMessage) {
                    // Remova o elemento
                        headerMessage.remove();
                    } else {
                    console.log('O elemento .header-message não foi encontrado.');
                    }
                }, 3000);


            }
        }

        console.log(this.linkURL);
	},
    props:{
        csid: String,
        type: String,
    },
	methods: {
        alterar_url(nova){
            history.pushState({}, null, nova);
        }
	},
};
</script>

<style scoped>
</style>