<template>
    <v-dialog v-model="show" scrollable max-width="600" content-class="custom-dialog">
        <v-card class="card-dialog" color="whiteCard" elevation="15">
            <v-badge color="#0080FC" avatar overlap offset-y="15" offset-x="15">  
                <template v-slot:badge>
                    <v-avatar size="30" @click="show = false">
                        <v-icon size="20">mdi-close</v-icon>
                    </v-avatar>
                </template>
                <v-card-title class="text-h5 font-weight-bold justify-center">
                    {{ $t("bs-edit-profile") }}
                </v-card-title>
            </v-badge>
            <v-form
                ref="formEditProfile"
                id="formEditProfile"
                v-model="form.valid"
                lazy-validation
                @submit.prevent="submit()"
            >
                <v-card-text>
                    <v-container>
                        <v-row>
                            <template v-if="!showChangePasswordForm">
                                <v-col cols="12">
                                    <v-text-field
                                        v-model="form.name"
                                        :rules="rules.name"
                                        :label="$t('bs-name')"
                                        validate-on-blur
                                        name="name"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        v-model="form.email"
                                        :rules="rules.email"
                                        :label="$t('bs-email')"
                                        validate-on-blur
                                        name="email"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <a @click="showChangePasswordForm = true" class="text-decoration-underline">
                                        {{$t("bs-change-password")}}
                                    </a>
                                </v-col>
                            </template>
                            <template v-else>
                                <v-col cols="12">
                                    <v-text-field
                                        :append-icon="form.current_passwordShow ? 'mdi-eye' : 'mdi-eye-off'"
                                        :rules="rules.password"
                                        :type="form.current_passwordShow ? 'text' : 'password'"
                                        :label="$t('bs-current-password')"
                                        v-model="form.current_password"
                                        class="input-group--focused"
                                        @click:append="form.current_passwordShow = !form.current_passwordShow"
                                        autocomplete="new-password"
                                        validate-on-blur
                                        name="current-password"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        :append-icon="form.passwordShow ? 'mdi-eye' : 'mdi-eye-off'"
                                        :rules="rules.new_password"
                                        :type="form.passwordShow ? 'text' : 'password'"
                                        :label="$t('bs-password')"
                                        v-model="form.password"
                                        @click:append="form.passwordShow = !form.passwordShow"
                                        autocomplete="new-password"
                                        validate-on-blur
                                        name="new-password"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        :append-icon="form.password2Show ? 'mdi-eye' : 'mdi-eye-off'"
                                        :rules="rules.new_password2"
                                        :type="form.password2Show ? 'text' : 'password'"
                                        :label="$t('bs-confirm-password')"
                                        v-model="form.password2"
                                        @click:append="form.password2Show = !form.password2Show"
                                        validate-on-blur
                                        name="confirm-password"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <a @click="showChangePasswordForm = false" class="text-decoration-underline">
                                        {{$t("bs-back")}}
                                    </a>
                                </v-col>
                            </template>
                        </v-row>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-container class="text-center">
                        <v-btn dark color="#0080FC" class="rounded-xl" type="submit">
                            {{ $t("bs-save") }}
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
            show: false,
            showChangePasswordForm: false,
            form: {
                name: this.$store.state.user.name,
                email: this.$formatEmail(this.$store.state.user.email),
                current_password: "",
                current_passwordShow: false,
                password: "",
                passwordShow: false,
                password2: "",
                password2Show: false,
                valid: false
            },
            rules: {
                name: [
                    (v) => (v && v !== "") || this.$t("bs-mandatory"),
                    (v) => (v && v.length >= 3) || this.$t("bs-invalid-field"),
                ],
                email: [
					(v) => !!v || this.$t("bs-mandatory"),
					(v) => /.+@.+\..+/.test(v) || this.$t("bs-invalid-field"),
				],
                password: [
                    (v) => (v == "" && this.form.password == "" || v.length >= 6) || this.$t("bs-invalid-field"),
				],
                new_password: [
                    (v) => (v == "" && this.form.current_password == "" || v.length >= 6) || this.$t("bs-invalid-field"),
				],
                new_password2: [
                    (v) => v == this.form.password || this.$t("bs-invalid-field"),
				]
            }
        }
    },
    created () {
        this.$root.$refs.editProfileDialog = this;
    },
    methods: {
        submit() {
            if (this.$refs.formEditProfile.validate()) {
                var vm = this;
                var url = `${vm.$store.state.baseURL}/user/update`;
                axios.post(url, {
                    name: vm.form.name,
                    email: vm.$store.state.email_prefix+vm.form.email,
                    subsidiary_id: vm.$store.state.user.subsidiary_id,
                    current_password: vm.form.current_password,
                    new_password: vm.form.password,
                })
                .then(res => {
                    if (res.status == 203) {
                        vm.$notify({
                            title: vm.$t("bs-invalid-field"),
                            text: vm.$t("bs-current-password"), 
                            icon: 'error'
                        });
                    } else if (res.status == 200) {
                        localStorage.setItem("profile-updated", 1);
                        location.reload();
                    }
                })
            }
        }
    },
}
</script>

<style>

</style>