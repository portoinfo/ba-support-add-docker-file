<template>
    <div>
        <b-navbar toggleable="lg" type="light" variant="light" fixed="top" class="bg-white bb-navbar">

            <!-- toggle menu mobile -->
            <div v-b-toggle.sidebar-mobile class="d-sm-none toggle-icon">
                <i class="bbi bbi-menu-burger"></i>
            </div>

            <!-- toggle menu mini -->
            <div class="toggle-icon d-none d-sm-flex" @click="toggleSidebarMini">
                <i :class="['bbi', sidebar_is_mini ? 'bbi-menu-burger' : 'bbi-menu-burger-open']"></i>
            </div>

            <b-navbar-brand href="/" >
                <img src="/images/images/meta/logo.png" alt="Builderall Booking" class="mx-4 mb-1 d-none d-sm-inline-block">
                <img src="/images/images/meta/logo-icon.png" alt="Builderall Booking" class="mx-3 mb-1 d-sm-none">
            </b-navbar-brand>

            <span class="flex-fill"></span>

			<premium-badge class="d-none d-md-inline" v-if="$store.state.user.is_free"></premium-badge>

            <div class="mx-3 mx-sm-4 d-flex align-items-center">

            <span v-b-toggle.sidebar-notifications class="notifications-toggle" :title="$t('bb-notifications')">
                <i class="bbi bbi-bell bbi-18 mt-2 mx-1"></i>
            </span>

            <span class="divider-vertical mx-2"></span>

            <b-navbar-nav>
                <b-nav-item-dropdown :right="!$isRTL" no-caret class="bb-dropdown-user">

                    <template v-slot:button-content>
                        <b-avatar variant="default" :src="usuario.gravatar"></b-avatar>
                    </template>

                    <b-dropdown-item href="#">
                        <div class="d-flex profile-dropdown align-items-center">
                            <b-avatar :src="usuario.gravatar" variant="default" size="3rem"></b-avatar>
                            <div class="flex-fill d-flex flex-column m-3">
                                <span>{{ usuario.name }}</span>
                                <span>{{ usuario.email }}</span>
                            </div>
                        </div>
                    </b-dropdown-item>

					<template v-if="$store.state.user.is_admin">
						<b-dropdown-divider></b-dropdown-divider>
						<b-dropdown-item tabindex="-1" href="/admin" link-class="py-2">
							<i class="bbi bbi-gear bbi-22 mx-3"></i> Admin
						</b-dropdown-item>
					</template>

                    <b-dropdown-divider></b-dropdown-divider>

                    <b-dropdown-item tabindex="-1" link-class="py-2">
                        <i class="bbi bbi-language bbi-22 mx-3"></i> {{$t('bb-favorite-lang')}}
                    </b-dropdown-item>
                    <v-select
                        :dir="$dir"
                        style="background-color: #F2F2F2"
                        class="mt-1 mx-3 mb-3"
                        :clearable="false"
                        :options="languages"
                        label="desc"
                        @input="saveLanguage()"
                        v-model="userLang"
                        :reduce="value => value.key"
                    >
                        <template #selected-option="{key, desc}">
                            <img height="24" class="mx-3" :src="`/images/flags/${key}.svg`" alt="">
                            {{desc}}
                        </template>
                        <template #option="{key, desc}">
                            <img height="24" class="mx-2" :src="`/images/flags/${key}.svg`" alt="">
                            {{desc}}
                        </template>
                    </v-select>

                    <b-dropdown-divider></b-dropdown-divider>

                    <b-dropdown-item tabindex="-1" href="/user-preferences" link-class="py-2">
                        <i class="bbi bbi-gear bbi-22 mx-3"></i> {{ $t('bb-preferences') }}
                    </b-dropdown-item>

                    <b-dropdown-item tabindex="-1" href="/logout" link-class="py-2" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bbi bbi-logout bbi-22 mx-3"></i> {{ $t('bb-sign-out') }}
                    </b-dropdown-item>

                    <form id="logout-form" action="/logout" method="POST" style="display: none;">
                        <input type="hidden" name="_token" :value="csrf">
                    </form>

                </b-nav-item-dropdown>
            </b-navbar-nav>

            </div>

        </b-navbar>
    </div>
</template>

<script>

import { languages } from '../../../../static/translation/select';
import { saveLanguage } from '../../services/user';
import { mapState, mapMutations } from 'vuex';

export default {

	props: ['usuario'],

    data () {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            languages: languages,
            userLang: this.$i18n.locale
        }
	},

    mounted () {
        if (!this.languageAvaliable) {
            this.userLang = 'en_US'
        }
	},

    methods: {
		...mapMutations(['toggleSidebarMini']),

        saveLanguage () {
            this.$loading.show()
            saveLanguage({language: this.userLang})
            .then(res => {
                this.$snotify.success(this.$t('bb-done'))
                location.reload()
            })
            .catch(res => {
                console.error(res);
            })
            .finally(() => {
                this.$loading.hide()
            });
        },
	},

    computed: {
		...mapState(['sidebar_is_mini']),

        languageAvaliable () {
            return this.languages.find(el => el.key == this.$i18n.locale)
		}
    }
}
</script>

<style lang="scss">

@import './resources/sass/variables';

.bb-navbar {
    box-shadow: 0 1px 2px rgba(38,36,36,.14);
    border: 1px solid #dedede;
    height: $header-height;
    flex-flow: row nowrap;
    justify-content: flex-start;
    padding: 0;;

    .dropdown-menu {
        position: absolute;
        .dropdown-item.active, .dropdown-item:active {
            i.bbi {
                filter: brightness(3);
            }
        }
	}

    .toggle-icon {
        width: calc(#{$sidebar-width-mini} - 1px);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        background: rgba(244, 244, 244, 0.7);
        height: calc(#{$header-height} - 2px);
        border-right: 1px solid rgba(222, 222, 222, 0.7);
        i {
            width: 29px;
            transition: $sidebar-transition;
        }
	}

    .divider-vertical {
        display: block;
        height: 25px;
        border-left: 1px solid #BED1EA;
	}

    .notifications-toggle {
        cursor: pointer;
        .bbi-bell {
            position: relative;
        }
        &.new .bbi-bell:after {
            width: 10px;
            height: 10px;
            content: close-quote;
            background: #FF3636;
            display: block;
            border-radius: 50%;
            border: 2px solid #fff;
            position: absolute;
            right: 1px;
            top: 1px;
        }
	}
	
	.bb-dropdown-user {
		.dropdown-menu {
			box-shadow: 0px 0px 5px #26242459;
			border-radius: 10px;
			border: none;
			.dropdown-item {
				display: flex;
				align-items: center;
				.profile-dropdown {
					text-align: initial;
					div span {
						&:nth-child(1){
							font-size: 18px;
						}
						&:nth-child(2){
							font-size: 13px;
						}
					}
				}
			}
		}
	}
}
</style>