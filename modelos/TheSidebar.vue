<template>
	<div>
		<div :class="['bb-sidebar d-none d-sm-flex flex-column justify-content-between', sidebar_is_mini ? 'bb-mini' : '']">
			<b-nav vertical>
				<b-nav-item
					v-for="(menu, k) in menus"
					:key="k"
					:href="menu.href"
					:active="menu.routes.includes(current)"
					:id="`sidebar-menu-${k}`">
					<b-tooltip
						:disabled="!sidebar_is_mini"
						:target="`sidebar-menu-${k}`"
						placement="right"> {{menu.title}}
					</b-tooltip>
					<i :class="['bbi bbi-20', menu.icon]"></i>
					<span>{{menu.title}}</span>
					<span v-if="menu.badge" v-b-tooltip.hover :title="$t('bb-you-have-pending-schedules', {schedules: pending})" class="sidebar-badge"></span>
				</b-nav-item>
			</b-nav>

			<b-nav vertical class="pb-4 pt-3 mt-3 bb-sidebar-bottom">
				<b-nav-item
					v-for="(menu, k) in bottom_menus"
					:key="k"
					:href="menu.href"
					:id="`sidebar-menu-bottom-${k}`"
					:active="menu.routes.includes(current)"
					:target="menu.target">
					<b-tooltip
						:disabled="!sidebar_is_mini"
						:target="`sidebar-menu-bottom-${k}`"
						placement="right"> {{menu.title}}
					</b-tooltip>
					<i :class="['bbi', menu.icon]"></i>
					<span>{{menu.title}}</span>
					<span v-if="menu.badge" class="sidebar-badge" :style="`background-color: ${menu.badge}`"></span>
				</b-nav-item>
			</b-nav>
		</div>

		<!-- mobile sidebar -->
		<b-sidebar id="sidebar-mobile" class="bb-sidebar-mobile" shadow backdrop no-header :right="$isRTL">
			<div class="bb-sidebar-mobile-header">
				<div class="d-flex justify-content-end">
					<b-button-close text-variant="white" v-b-toggle.sidebar-mobile></b-button-close>
				</div>
				<b-avatar variant="default" size="4rem" :src="user.gravatar"></b-avatar>
				<div class="d-flex justify-content-between align-items-center mt-3">
					<div>
						<span class="name">{{user.name}}</span>
						<small>{{user.email}}</small>
					</div>
					<a href="/user-preferences"><i class="bbi bbi-gear bbi-white bbi-20"></i></a>
				</div>
			</div>
			<div class="pt-2">
				<b-nav vertical sticky>
					<b-nav-item
						v-for="(menu, k) in menus"
						:key="k"
						:href="menu.href"
						:active="menu.routes.includes(current)">
						<i :class="['bbi ', menu.icon]"></i>
						<span>{{menu.title}}</span>
					</b-nav-item>
				</b-nav>
			</div>
		</b-sidebar>

	</div>
</template>

<script>

import { mapState } from 'vuex'

export default {
    props: {
        current: {
            type: String,
            required: false,
            default: ''
        },
        pending: {
            type: Number,
            required: false,
            default: 0
        },
    },

    data () {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            menus: [
                {
                    // title  : 'Dashboard',
                    title  : this.$t('bb-menu-dashboard'),
                    href   : '/',
                    routes : ['dashboard'],
                    icon   : 'bbi-menu-dashboard'
                },
                {
                    title  : this.$t('bb-menu-calendars'),
                    href   : '/calendars',
                    routes : ['calendar.wizard', 'calendars', 'subscribers'],
                    icon   : 'bbi-menu-calendars'

                },
                {
                    title  : this.$t('bb-menu-schedule'),
                    href   : this.agendaLink(),
                    routes : ['schedules', 'schedules-calendar'],
                    icon   : 'bbi-menu-agenda',
                    badge  : this.pending > 0 ? '#FFB244' : false

                },
                {
                    title  : this.$t('bb-menu-hosts'),
                    href   : '/hosts',
                    routes : ['hosts'],
                    icon   : 'bbi-menu-hosts'
                },
                {
                    title  : this.$t('bb-integrations'),
                    href   : '/integrations',
                    routes : ['integrations'],
                    icon   : 'bbi-menu-integrations'
                }
            ],
            bottom_menus: [
                {
                    title  : this.$t('bb-release-notes'),
                    href   : this.releaseNotesLink(),
                    routes : ['roadmap'],
                    icon   : 'bbi-menu-megaphone bbi-22',
                    target : '_blank'
                },
                {
                    title  : 'Backoffice',
                    href   : 'https://office.builderall.com/',
                    routes : [],
                    icon   : 'bbi-menu-backoffice bbi-22',
                    target : '_blank'
                },
            ]
        }
	},

    methods: {
        agendaLink() {
            return localStorage.getItem('prefer_agenda_view') == 'calendar'
				? '/schedules-calendar'
				: '/schedules'
		},
		releaseNotesLink() {
			return this.$i18n.locale == 'pt_BR'
				? 'https://docs.google.com/document/d/1Ku6R9Ql4zgUpr9UzwwW6__O3H0RZVkNM4YdyGF8dMH4/edit?usp=sharing'
				: 'https://docs.google.com/document/d/1MWWk2T1QwZyOJYGbSgmUX3wIhRfx6oqU1tZmIeoMGD8/edit?usp=sharing'
		}
	},

	computed: {
		...mapState(['sidebar_is_mini', 'user'])
	}

}
</script>

<style lang="scss">

@import './resources/sass/variables';

.bb-sidebar {
    position: fixed;
    width: $sidebar-width;
    min-height: calc(100vh - #{$header-height});
    border-right: 1px solid #dedede;
    box-shadow: 0 0 3px rgba(38,36,36,.14);
    background: #fff 0 0 no-repeat padding-box;
    transition: $sidebar-transition;
    &.bb-mini {
        width: $sidebar-width-mini;
        .nav-item .nav-link {
            justify-content: center;
            span:not(.sidebar-badge) {
                display: none;
            }
        }
    }
}

.bb-sidebar, .bb-sidebar-mobile {
    .nav .nav-item .nav-link {
		padding: 18px 23px 18px 20px;
		color: #68768c;
		justify-content: flex-start;
		display: flex;
		align-items: center;
		font-size: 15px;
		min-height: 51px;
		font-weight: var(--semibold);
		font-size: 14px;
		border-left: 3px solid transparent;
		transition: .4s;
		position: relative;
		.bbi {
			filter: opacity(0.7) grayscale(0.6);
			transition: inherit;
		}
		span {
			margin-left: 20px;
		}
		&:hover, &.active {
			background-color: #F4F7FC;
			color: var(--primary);
			.bbi {
				filter: unset;
			}
			.sidebar-badge {
				border-color: #F4F7FC;
			}
		}
		&.active {
			border-left: 3px solid #1D5EF5;
		}
		.sidebar-badge {
			background: #FFB244;
			position: absolute;
			width: 14px;
			height: 14px;
			border-radius: 100%;
			border: 3px solid #fff;
			left: -7px;
			top: 14px;
		}
    }
}

.bb-sidebar-mobile {
    .bb-sidebar-mobile-header {
		background: url(/images/images/alert/corner.png) left top no-repeat,
					url(/images/images/alert/wave.png) right bottom/100% no-repeat,
					transparent linear-gradient(180deg, #5E81F4 0%, #1665D8 100%);
        display: flex;
        flex-direction: column;
        padding: 20px;
        color: #fff;
        span.name {
            font-size: 19px;
            margin-top: 1rem;
        }
    }
    .nav {
        .nav-item {
            .nav-link {
                color: #585858;
                i {
                    width: 20px;
                    height: 20px;
                }
            }
        }
    }
}

.bb-sidebar-bottom {
    border-top: 1px solid #BED1EA;
    overflow: hidden;
    .bbi-menu-backoffice {
        height: 19px !important;
    }
}
</style>
