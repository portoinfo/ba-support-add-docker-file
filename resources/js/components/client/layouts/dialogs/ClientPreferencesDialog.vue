<template>
    <v-dialog v-model="show" scrollable max-width="600" content-class="custom-dialog">
        <v-card class="card-dialog" :class="{'rounded-all': firstAccess}" color="whiteCard" elevation="15">
            <v-badge color="#0080FC" avatar overlap offset-y="15" offset-x="15">
                <template v-slot:badge>
                    <v-avatar size="30" @click="show = false">
                        <v-icon size="20">mdi-close</v-icon>
                    </v-avatar>
                </template>
                <v-card-title class="text-h5 font-weight-bold justify-center">
                    <template v-if="firstAccess">
                        {{ $t('bs-welcome') }}!
                    </template>
                    <template v-else>
                        {{ $t("bs-preferences") }}
                    </template>
                </v-card-title>
                <v-card-subtitle v-if="firstAccess" class="text-center">
                    {{ $t('bs-before-starting-select-the-following-opts') }}
                </v-card-subtitle>
            </v-badge>
            <v-form>
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col cols="12">
                                <span class="text-subtitle-2">{{ $t('bs-choose-font-size') }}</span>
                                <v-select
                                    :items="items"
                                    solo
                                    item-value="size"
                                    v-model="size"
                                    background-color="white"
                                    hide-details
                                    class="mt-2"
                                >
                                    <template v-slot:selection="{ item }">
                                        <span :style="{'font-size': item.size}" class="mr-3">Aa</span>
                                        <span :style="{'font-size': item.size}">{{ item.title }}</span>
                                    </template>
                                    <template v-slot:item="{ item }">
                                        <span :style="{'font-size': item.size}" class="mr-3">Aa</span>
                                        <span :style="{'font-size': item.size}">{{ item.title }}</span>
                                    </template>
                                </v-select>
                            </v-col>
                            <v-col>
                                <span class="text-subtitle-2">{{ $t('bs-choose-the-theme-light-or-dark') }}</span>
                                <v-switch
                                    dense
                                    v-model="$vuetify.theme.dark"
                                    :prepend-icon="!$vuetify.theme.dark ? '$sun' : 'mdi-weather-night'"
                                ></v-switch>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-container class="text-center">
                        <v-btn dark color="btnPrimary" class="rounded-xl" @click="show = false">
                            {{ $t("bs-ok") }}
                        </v-btn>
                    </v-container>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
</template>

<script>
import { mapMutations } from 'vuex';
export default {
    data() {
        return {
            firstAccess: false,
            show: false,
            items: [
                {
                    title: this.$t('bs-very-small'),
                    size: '12px'
                },
                {
                    title: this.$t('bs-small'),
                    size: '14px'
                },
                {
                    title: this.$t('bs-medium-recommended'),
                    size: '16px'
                },
                {
                    title: this.$t('bs-large'),
                    size: '18px'
                },
                {
                    title: this.$t('bs-very-large'),
                    size: '20px'
                },
            ]
        }
    },
    created () {
        this.$root.$refs.preferencesDialog = this;
    },
    mounted () {
        this.firstAccess = this.$store.state.user.config == null;
        this.firstAccess && !this.$store.state.isPopup ? this.show = true : '';
        if(this.$store.state.isPopup){
            document.getElementById('root').style.fontSize = '15px';
        }else{
            this.size = this.firstAccess ? '16px' : JSON.parse(this.$store.state.user.config).fontSize;
        }
    },
    computed: {
        size: {
            get() {
                return this.$store.state.font_size;
            },
            set(v) {
                this.setFontSize(v);
            }
        }
    },
    watch: {
        show(newValue, oldValue) {
            if (newValue) {
                this.firstAccess = this.$store.state.user.config == null;
            }
        }
    },
    methods: {
        ...mapMutations(['setFontSize']),
    },
}
</script>

<style scoped>
.v-menu__content.theme--dark .v-list {
    background: #20202A;
}
</style>
