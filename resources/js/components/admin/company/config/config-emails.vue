<template>
    <div>
        <textarea
            style="display: none;"
            class="form-control"
            id="idEditorContent"
            name="content"
            rows="6"
            placeholder=""
            required
            :value="valueHtml"
        ></textarea>

        <div id="mail-editor" style="width: 100%;"></div>
        <b-button :disabled="isDisable" style="float:right;" @click="saveModelEmail" variant="btn bs-btn-save"><i class="fa fa-floppy-o" aria-hidden="true"></i> {{$t('bs-save')}}</b-button>
    </div>
  </template>
  
  <script>
  export default {
    data(){
		return {
			mbEditor: null,
            isDisable: false,
        }
	},
    props:{
        usuario:Object,
        selected: String,
        defaultLanguage:String,
        valueHtml: String,
        nameSender: String,
        emailSender: String,
        title: String,
    },
    created() {
        const scriptJquery = document.createElement('script');
        scriptJquery.src = 'https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js';
        scriptJquery.async = true;
        scriptJquery.onload = this.loadMbeditorScript;
        document.head.appendChild(scriptJquery);
        var vm = this;
        var textarea = document.getElementById('idEditorContent');
        console.log(vm.valueHtml);
        textarea.value = vm.valueHtml;
        this.loadEmail();
    },
    watch: {
        valueHtml(){
            console.log(this.valueHtml);
        }
    },
    methods: {
        saveModelEmail: async function(value) {
            this.isDisable = true;
            if (this.selected == '') {
                this.isDisable = false;
                return;
            }
            this.$root.$refs.CompanyConfig.blockSelect = true;

            var textarea = await this.aguardarVariavel();

            if (textarea.value.includes("Lorem Ipsum is simply dummied")) {
                if (value != 'try') {
                    await this.saveModelEmail('try');
                }else{
                    this.$snotify.info(this.$t("bs-please-try-again")+'.', this.$t("bs-info"));
                    this.isDisable = false;
                }
                return;
            } else {
                if (textarea.value == '' || textarea.value == undefined) {
                    if (value != 'try') {
                        await this.saveModelEmail('try');
                    }else{
                        this.$snotify.info(this.$t("bs-please-try-again"), this.$t("bs-info"));
                        this.isDisable = false;
                    }
                } else {
                    this.valueHtml = textarea.value
                    axios.post('company-config/any-custom-email', {
                        type: this.selected,
                        emailHtml: this.valueHtml,
                        defaultLanguage: this.defaultLanguage,
                        nameSender: this.nameSender,
                        emailSender: this.emailSender,
                        title: this.title,
                    }).then(({ data }) => {
                        if (data.success) {
                            this.isDisable = false;
                            this.$snotify.success(this.$t("bs-saved-successfully"), this.$t("bs-success"));
                        }else{
                            this.isDisable = false;
                        }
                        
                        this.$root.$refs.CompanyConfig.blockSelect = false;
                    }).catch(err => {
                        console.error(err);
                    });
                }
            }
        },
        aguardarVariavel() {
            return new Promise(function(resolve, reject) {
                // Simulando uma operação assíncrona que atribui valor à variável
                setTimeout(function() {
                    var minhaVariavel = document.getElementById('idEditorContent');
                    resolve(minhaVariavel);
                }, 1000); // Aguarda 2 segundos antes de atribuir o valor
            });
        },
        loadEmail(){
            this.initMbeditor();
        },
        loadMbeditorScript() {
            const scriptMbeditor = document.createElement('script');
            scriptMbeditor.src = 'https://member.mailingboss.com/mbeditor/api/mbeditor.min.js';
            scriptMbeditor.async = true;
            scriptMbeditor.onload = this.initMbeditor;
            document.head.appendChild(scriptMbeditor);
        },
        initMbeditor() {
            this.mbEditor = jQuery('#mail-editor').mbeditor('idEditorContent', {
                autoChangeContent: true,
                baseUrl: 'https://member.mailingboss.com'
            });
        },
    },
  };
  </script>