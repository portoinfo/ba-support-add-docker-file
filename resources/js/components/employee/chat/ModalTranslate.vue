<template>
    <b-modal id="modal-translate" :title="$t('bs-translate-messages')" centered @show="show()" @hidden="hidden()" @ok="translate()">

        <b-form  class="pr-2 pl-2">

            <label class="font-weight-bold c434343">{{ $t('bs-my-messages') }}</label>
            <b-form-group>
                <b-form-checkbox
                    v-model="status_my_messages"
                >
                    {{ $t('bs-translate-my-messages') }}
                </b-form-checkbox>
            </b-form-group>

            <b-form-group class="pl-3 pr-3" :label="`${$t('bs-translate-into')}:`" :disabled="formMyMessagesDisabled">
                <b-form-select v-model="languageVisitorMessages" :options="options"></b-form-select>
            </b-form-group>

            <hr>

            <label class="font-weight-bold c434343">{{ $t('bs-visitor-s-messages') }}</label>
            <b-form-group>
                <b-form-checkbox
                    v-model="status_visitors_messages"
                >
                    {{ $t('bs-translate-visitor-s-messages') }}
                </b-form-checkbox>
            </b-form-group>

            <b-form-group class="pl-3 pr-3" :label="`${$t('bs-translate-into')}:`" :disabled="formVisitorMessagesDisabled">
                <b-form-select v-model="languageMyMessages" :options="options"></b-form-select>
            </b-form-group>

        </b-form>

    </b-modal>
</template>

<script>
export default {
    props: {
        chat: Object,
        user: Object,
        messages: Array
    },
    data() {
      return {
        languageMyMessages: this.init_language_my_messages(),
        languageVisitorMessages: this.init_language_visitor_messages(),
        options: [],
        status_my_messages: this.init_status_my_messages(),
        status_visitors_messages: this.init_status_visitors_messages(),
      }
    },
    created() {
        this.$root.$refs.ModalTranslate = this;
        this.getSystemLanguages();
    },
    watch: {
        messages: {
            handler(val, oldVal) {
                if (val != oldVal && val.length > 0) {
                    this.translate();
                }
            },
            deep: true
        },
        status_my_messages: function() {
			localStorage.setItem(`${this.chat.number}_translate_my_messages`, this.status_my_messages);
		},
        status_visitors_messages: function() {
			localStorage.setItem(`${this.chat.number}_translate_visitors_messages`, this.status_visitors_messages);
		},
        languageMyMessages: function() {
            localStorage.setItem(`${this.chat.number}_language_my_messages`, this.languageMyMessages);
        },
        languageVisitorMessages: function() {
            localStorage.setItem(`${this.chat.number}_language_visitor_messages`, this.languageVisitorMessages);
        }
    },
    computed: {
        formMyMessagesDisabled() {
            if (this.status_my_messages) {
               return false;
            } else {
                return true;
            }
        },
        formVisitorMessagesDisabled() {
            if (this.status_visitors_messages) {
               return false;
            } else {
                return true;
            }
        },
    },
    methods: {
        init_language_my_messages() {
            const stored = localStorage.getItem(`${this.chat.number}_language_my_messages`);
			if (stored === null) {
                return this.user.language.split('_')[0];
			} else {
				return stored;
			}
        },
        init_language_visitor_messages() {
            if (this.chat.number) {
                const stored = localStorage.getItem(`${this.chat.number}_language_visitor_messages`);
                if (stored == null) {
                    return "en";
                } else {
                    return stored;
                }
            }
        },
        init_status_my_messages: function() {
			const stored = localStorage.getItem(`${this.chat.number}_translate_my_messages`);
			if (stored === null) {
				return false;
			} else {
				return stored == 'true';
			}
		},
        init_status_visitors_messages: function() {
			const stored = localStorage.getItem(`${this.chat.number}_translate_visitors_messages`);
			if (stored === null) {
				return false;
			} else {
				return stored == 'true';
			}
		},
        getSystemLanguages() {
            var languages = this.$store.state.languages;
            languages.forEach(element => {
                this.options.push({'value': element.key.split('_')[0], 'text': element.desc})
            });

        },
        show() {
            //show
        },
        hidden() {
            //hidden
        },
        translate() {
            for (let index = this.messages.length - 1; index >= 0; index--) {
                if (this.messages[index].type == 'TEXT') {
                    if (this.messages[index].company_user_company_department_id == null) {
                        // mensagens do cliente
                        if (this.status_visitors_messages) {
                            this.checkTranslation(this.messages[index].ch_id, this.languageMyMessages).then((check) => {
                                if (check.exists) {
                                    this.messages[index].content_translated = check.content_translated;
                                    this.$root.$refs.FullChat2.chatScrollTop();
                                } else {
                                    this.$google.translate(this.messages[index].content, this.languageMyMessages).then((result) => {
                                        this.setTranslatedMessages(result.data.translations[0].translatedText, this.languageMyMessages, this.messages[index].ch_id).then((content_translated) => {
                                            content_translated.forEach(ct => {
                                                if (ct.language == this.languageMyMessages) {
                                                    this.messages[index].content_translated = ct.content;
                                                    this.$root.$refs.FullChat2.chatScrollTop();
                                                }
                                            });
                                        })
                                        .catch(err => {
                                            console.log("FAILURE");
                                        })
                                    })
                                }
                            })
                        } else {
                            this.messages[index].content_translated = null;
                        }
                    }
                }
            }

            this.$root.$refs.FullChat2.getQuestionary(this.chat.chat_id);
        },
        checkTranslation(ch_id, language) {
            return new Promise((resolve, reject) => {
                axios.get('chat-history/check-translation', {
                    params: {
                        ch_id: ch_id,
                        language: language
                    }
                })
                .then(({data}) => {
                    if (data.success) {
                        resolve({exists: data.exists, content_translated: data.content_translated});
                    }
                })
            })
        },
        setTranslatedMessages(message, language, ch_id) {
            return new Promise((resolve, reject) => {
                axios.post('chat-history/set-translated-messages', {
                    ch_id: ch_id,
                    message: message,
                    language: language
                })
                .then(({data}) => {
                    if(data.success) {
                        resolve(data.content_translated);
                    }
                })
                .catch(err => {
                    reject(err);
                })
            })
        },
        getClientLanguage() {
            return new Promise((resolve, reject) => {
                var vm = this;
                axios.get('get-user-auth-language-by-id', {
                    params: {
                        client_id: vm.chat.client_id
                    }
                })
                .then(({data}) => {
                    var language = data.language.split('_')[0];
                    resolve(language);
                })
            })
        }
    },
}
</script>

<style scoped>

</style>
