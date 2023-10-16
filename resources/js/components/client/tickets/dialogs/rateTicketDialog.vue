<template>
	<v-dialog v-model="rateDialogShown" scrollable persistent max-width="600" content-class="custom-dialog">
        <v-card class="card-dialog" color="whiteCard" elevation="15">
            <v-badge color="#0080FC" avatar overlap offset-y="15" offset-x="15">  
                <template v-slot:badge>
                    <v-avatar size="30" @click="rateDialogShown = false">
                        <v-icon size="20">mdi-close</v-icon>
                    </v-avatar>
                </template>
                <v-card-title class="text-h5 font-weight-bold justify-center">
                    {{ $t("bs-ticket-rating") }}
                </v-card-title>
            </v-badge>
            <v-form
                ref="formEvaluation"
                id="formEvaluation"
                v-model="form.valid"
                lazy-validation
                @submit.prevent="submit()"
            >
                <v-card-text>
                
                    <v-container>
                        <v-row>
                            <v-col cols="12" v-if="settingsAttenActive">
                                <span class="text-body-2">
                                    {{ $t("bs-evaluate-the-performance-of-our-attendant") }}
                                </span>
                                <v-input :value="form.stars_atendent" :rules="rules.rate">
                                    <v-rating
                                        v-if="typeEvaluation == 'stars'"
                                        background-color="orange lighten-3"
                                        color="orange"
                                        v-model="form.stars_atendent"
                                    ></v-rating>
                                    <template v-else-if="typeEvaluation == 'good_bad'">
                                        <v-btn icon @click="form.stars_atendent = 1" class="pa-5 mr-2" :class="form.stars_atendent == 1 ? 'enabled' : 'disabled'">
                                            <v-icon>$thumb_down</v-icon>
                                        </v-btn>
                                        <v-btn icon @click="form.stars_atendent = 5" class="pa-5 mr-2" :class="form.stars_atendent == 5 ? 'enabled' : 'disabled'">
                                            <v-icon>$thumb_up</v-icon>
                                        </v-btn>
                                    </template>
                                </v-input>
                            </v-col>
                            <v-col cols="12" v-if="settingsServActive">
                                <span class="text-body-2">
                                    {{ $t("bs-service-provided-solve-your-problem") }}
                                </span>
                                <v-input :value="form.stars_service" :rules="rules.rate">
                                    <v-rating
                                        v-if="typeEvaluation == 'stars'"
                                        background-color="orange lighten-3"
                                        color="orange"
                                        v-model="form.stars_service"
                                    ></v-rating>
                                    <template v-else-if="typeEvaluation == 'good_bad'">
                                        <v-btn icon @click="form.stars_service = 1" class="pa-5 mr-2" :class="form.stars_service == 1 ? 'enabled' : 'disabled'">
                                            <v-icon>$thumb_down</v-icon>
                                        </v-btn>
                                        <v-btn icon @click="form.stars_service = 5" class="pa-5 mr-2" :class="form.stars_service == 5 ? 'enabled' : 'disabled'">
                                            <v-icon>$thumb_up</v-icon>
                                        </v-btn>
                                    </template>
                                </v-input>
                            </v-col>
                            <v-col cols="12" v-if="settingsCommentActive">
                                <span class="text-body-2">
                                    {{ $t('bs-please-leave-a-comment') }}
                                </span>
                                <v-textarea
                                    class="text-body-2"
                                    auto-grow
                                    rows="1"
                                    v-model="form.evaluationComment"
                                    :rules="rules.comment"
                                    :label="$t('bs-comment')"
                                ></v-textarea>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-container class="text-center">
                        <v-btn dark color="#0080FC" class="rounded-xl" type="submit">
                            {{ $t("bs-submit-review") }}
                        </v-btn>
                    </v-container>
                </v-card-actions>
            </v-form>
        </v-card>
	</v-dialog>
</template>

<script>
export default {
    data() {
        return {
            form: {
                stars_atendent: 0,
                stars_service: 0,
                evaluationComment: "",
                valid: false
            },
            rules: {
                rate: [
                    (v) => (v && v > 0) || this.$t("bs-mandatory"),
                ],
                comment: [
                    (v) => (v && v !== "") || this.$t("bs-mandatory"),
                ]
            }
        }
    },
	computed: {
		rateDialogShown: {
			get() {
				return this.$root.$refs.ClientTicketOpened.rateDialogShown;
			},
			set(value) {
				this.$root.$refs.ClientTicketOpened.rateDialogShown = value;
			},
		},
        evaluated: {
			get() {
				return this.$root.$refs.ClientTicketOpened.evaluated;
			},
			set(value) {
				this.$root.$refs.ClientTicketOpened.evaluated = value;
			},
		},
        typeEvaluation() {
            if ('evaluation' in this.$root.$refs.ClientTicketOpened.departmentSettings) {
                var typeevaluation = this.$root.$refs.ClientTicketOpened.departmentSettings.evaluation.typeevaluation;
                if (typeevaluation == null) {
                    return 'stars';
                } else {
                    return typeevaluation;
                }
            }
        },
        settingsAttenActive() {
            if ('evaluation' in this.$root.$refs.ClientTicketOpened.departmentSettings) {
                return this.$root.$refs.ClientTicketOpened.departmentSettings.evaluation.text_atten_active;
            }
        },
        settingsServActive() {
            if ('evaluation' in this.$root.$refs.ClientTicketOpened.departmentSettings) {
                return this.$root.$refs.ClientTicketOpened.departmentSettings.evaluation.text_serv_active;
            }
        },
        settingsCommentActive() {
            if ('evaluation' in this.$root.$refs.ClientTicketOpened.departmentSettings) {
                return this.$root.$refs.ClientTicketOpened.departmentSettings.evaluation.text_comment_active;
            }
        },
        rateFormAction() {
            return this.$root.$refs.ClientTicketOpened.rateFormAction;
        }
	},
    methods: {
        submit() {
            if (this.$refs.formEvaluation.validate()) {
                switch (this.rateFormAction) {
                    case 'RATE':
                        this.$root.$refs.ClientTicketOpened.rateTicket(this.form).then(() => {
                            this.closeAndResetDialog();
                        })
                        break;

                    case 'RATE-AND-RESOLVE':
                        this.$root.$refs.ClientTicketOpened.rateTicket(this.form).then(() => {
                            this.closeAndResetDialog();
                            this.$root.$refs.ClientTicketOpened.resolveTicket();
                        });
                        break;
                }
            }
        },
        closeAndResetDialog() {
            this.evaluated = true;
            this.rateDialogShown = false;
            this.form.stars_atendent = 0,
            this.form.stars_service = 0,
            this.form.evaluationComment = "",
            this.form.valid = false
        }
    },
};
</script>

<style>
</style>