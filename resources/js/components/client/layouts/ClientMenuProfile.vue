<template>
	<v-list class="pa-0">
		<v-card class="pa-0" color="white">
			<v-container>
				<v-list color="white">
					<v-list-item>
						<v-list-item-avatar size="52" tile>
							<v-gravatar
								:email="$store.state.user.email"
								:name="$ct($store.state.user.name)"
								:status="$status.get($store.state.user.id)"
								size="52"
							></v-gravatar>
						</v-list-item-avatar>
						<v-list-item-content>
							<v-list-item-title>
								{{ $ct($store.state.user.name) }}
							</v-list-item-title>
							<v-list-item-subtitle>
								{{ $formatEmail($store.state.user.email) }}
							</v-list-item-subtitle>
						</v-list-item-content>
					</v-list-item>
				</v-list>
			    <v-divider></v-divider>
                <!-- <v-simple-table fixed-header :class="{'no-data': empty }" style="background-color: transparent">
                    <template v-slot:default>
                        <thead>
                            <tr>
                                <th class="text-center">dom</th>
                                <th class="text-center">seg</th>
                                <th class="text-center">ter</th>
                                <th class="text-center">qua</th>
                                <th class="text-center">qui</th>
                                <th class="text-center">sex</th>
                                <th class="text-center">sab</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>8:00<br>16:00</td>
                                <td>8:00<br>16:00</td>
                                <td>8:00<br>16:00</td>
                                <td>8:00<br>16:00</td>
                                <td>8:00<br>16:00</td>
                                <td>8:00<br>16:00</td>
                                <td>8:00<br>16:00</td>
                            </tr>
                        </tbody>
                    </template>
                </v-simple-table> -->
			</v-container>
			<v-list dense color="white">
                    <template v-for="(item, i) in items">
					    <v-list-item :key="i" v-if="item.show" :inactive="item.type !== 'item'" @click="executeAction(item)">
                            <template v-if="item.type == 'select-lang'">
                                <v-list-item-content class="pa-1">
                                    <v-select
                                       :items="$store.state.languages"
                                       v-model="$store.state.user.language"
                                       solo
                                       item-value="key"
                                       background-color="select"
                                       hide-details
                                       class="mb-2"
                                   >
                                    <template v-slot:selection="{ item }">
                                        <v-list-item class="select" inactive>
                                            <v-list-item-avatar tile>
                                                <img :src="`${$store.state.baseURL}/images/flags/${item.key}.svg`"/>
                                            </v-list-item-avatar>
                                            <v-list-item-content class="text-subtitle-2" v-text="item.desc"></v-list-item-content>
                                        </v-list-item>
                                    </template>
                                    <template v-slot:item="{ item }">
                                        <v-list-item @click="saveLanguage(item.key)">
                                            <v-list-item-avatar tile>
                                                <img :src="`${$store.state.baseURL}/images/flags/${item.key}.svg`"/>
                                            </v-list-item-avatar>
                                            <v-list-item-content class="text-subtitle-2" v-text="item.desc"></v-list-item-content>
                                        </v-list-item>
                                    </template>
                                   </v-select>
                                </v-list-item-content>
                            </template>
                            <template v-else>
                                <v-list-item-icon>
                                    <v-icon size="20" color="#94A3B8" v-text="item.icon"></v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title class="text-body-2" v-text="item.text"></v-list-item-title>
                                </v-list-item-content>
                            </template>
				    	</v-list-item>
                    </template>
			</v-list>
		</v-card>
        <client-edit-profile-dialog></client-edit-profile-dialog>
	</v-list>
</template>

<script>
export default {
	data() {
		return {
            editPerfilClient: Boolean,
            user_uuid: "",
		};
	},
    created() {
        this.getProfileSettings();
        this.CheckUser_uuid();
    },
    computed: {
        items() {
            return [
                { 
                    text: this.$t('bs-edit-profile'), 
                    icon: "$edit_profile", 
                    type: "item", 
                    action: 'EDIT_PROFILE',
                    show: this.editPerfilClient && this.user_uuid == null
                },
                { 
                    text: this.$t('bs-preferences'), 
                    icon: "mdi-tune-variant", 
                    type: "item", 
                    action: 'PREFERENCES',
                    show: true,
                },
				{ 
                    text: this.$t('bs-favorite-lang'), 
                    icon: "$language", 
                    type: "label", 
                    action: null,
                    show: true,
                },
				{ 
                    text: "", 
                    icon: "", 
                    type: "select-lang", 
                    action: null,
                    show: true,
                },
				{ 
                    text: this.$t('bs-exit'), 
                    icon: "$logout_1", 
                    type: "item", 
                    action: 'LOGOUT',
                    show: true,
                },
			]
        }
    },
    methods: {
        executeAction(item) {
            switch (item.action) {
                case 'EDIT_PROFILE':
                    this.$root.$refs.editProfileDialog.show = true;
                    break;

                case 'PREFERENCES':
                    this.$root.$refs.preferencesDialog.show = true;
                    break;

                case 'LOGOUT':
                    this.$router.push({name: 'logout'});
                    break;
            
                default:
                    break;
            }
        },
        saveLanguage(language) {
            var vm = this;
            var url = `${vm.$store.state.baseURL}/agents/update-language`;
            axios
            .post(url, {
                id: vm.$store.state.user.id,
                language,
                })
            .then(function (response) {
                if (response.data.success) {
                    location.reload();
                } else {
                    vm.$notify({
                        title: this.$t("bs-error-save"),
                        icon: 'error'
                    });
                }
            })
        },
        getProfileSettings() {
            var vm = this;
            var url = `${vm.$store.state.baseURL}/company-config/general-settings`
            axios.get(url, {
                params: {
                    company_id: vm.$store.state.company
                }
            })
            .then(({data}) => {
                vm.editPerfilClient = data.editPerfilClient;
            })
        },
        CheckUser_uuid() {
            var vm = this;
            var url = `${vm.$store.state.baseURL}/user/get-user-uuid`
            axios.get(url).then(({ data }) => {
                vm.user_uuid = data.user_uuid;
            });
        },
    },
};
</script>

<style scoped>
.v-list-item--disabled {
    opacity: 1 !important;
}

.v-menu__content.theme--dark .v-list {
    background: #292935;
}
.v-list--dense .v-list-item.select {
    max-height: 30px !important;
}
</style>