<template>
    <div
		class="tz-selector text-left"
		:class="[disabled ? 'disabled' : '', active ? 'tz-open' : '']"
		v-on-clickaway="close"
		>
        <div
			class="tz-selector-selected d-flex"
			@click="toggle"
			:class="active ? 'active' : ''"
			>
            <span>{{ timezoneLabel }}</span>
        </div>
        <div class="tz-selector-list" v-if="active">

            <b-form-input v-if="!searchDisable"
				ref="search"
				class="tz-selector-search"
				:placeholder="$t('bs-search')"
				v-model="search"
			></b-form-input>

            <div v-if="search == ''">
                <ul 
					class="tz-selector-ul"
					v-for="(group, index) in timezones"
					:key="group.value"
				>
                    <li class="tz-selector-li-group">
                        {{ index }}
                    </li>
                    <li 
						class="tz-selector-li"
						:class="index == selectedTimeZoneComputed ? 'active' : ''"
						v-for="(timezone, index) in group"
						:key="'a' + index"
						@click="input(index)"
					>
                        <span>{{ timezone }}</span> 
						<small class="tz-selector-time">
							{{ $moment.tz(index).format(userTimeFormat) }}
						</small>
                    </li>
                </ul>
            </div>
            <div v-else>
                <ul class="tz-selector-ul">
                    <li 
						class="tz-selector-li"
						:class="index == selectedTimeZoneComputed ? 'active' : ''"
						v-for="(timezone, index) in filteredTimezones"
						:key="index"
						@click="input(index)"
					>
                        <span>{{ timezone }}</span>
						<small class="tz-selector-time">
							{{ $moment.tz(index).format(userTimeFormat) }}
						</small>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</template>
<script>

import { directive as onClickaway } from 'vue-clickaway';

export default {

    directives: {
        onClickaway: onClickaway,
	},

	model: {
		prop: 'selectedTimeZone',
		event: 'click'
	},

	props: {
		timezones: {
			type: Object,
			required: true,
		},
		searchDisable: {
			type: Boolean,
		},
		selectedTimeZone: {
			type: String,
			required: false,
		},
		disabled: {
			type: Boolean,
			required: false,
			default: false,
		},
		default: {
			type: String,
			required: false,
		},
		timeFormat: {
			type: String,
			required: false,
			default: '24h',
			validator: (value) => {
				return ['24h', '12h'].includes(value)
			}
		}
	},

    data () {
        return {
            search: '',
            ungrupedTimezones: {},
            active: false
        }
	},

    methods: {

		input (tz) {
            this.$emit('click', tz);
            this.close();
        },

        close () {
            this.search = ''
            this.active = false;
		},
		
        toggle () {
            this.active = !this.active;
            var vm = this;
            if(this.active) {
                Vue.nextTick(function () {
                    vm.$refs.search.focus();
                });
            }
        }
	},
	
    computed: {

        filteredTimezones: function () {
            var timezones = {};
            for(var k in this.ungrupedTimezones) {
                if(this.ungrupedTimezones[k].toLowerCase().indexOf(this.search.toLowerCase()) > -1 || k.toLowerCase().indexOf(this.search.toLowerCase()) > -1) {
                    timezones[k] = this.ungrupedTimezones[k];
                }
            }
            return timezones;
		},
		
        selectedTimeZoneComputed () {
            return this.ungrupedTimezones.hasOwnProperty(this.selectedTimeZone) ? this.selectedTimeZone : this.default
		},
		
        userTimeFormat() {
            return this.timeFormat == '12h' ? 'hh:mm a' : 'HH:mm'
		},

		timezoneLabel() {
			return this.selectedTimeZoneComputed ? this.ungrupedTimezones[this.selectedTimeZoneComputed] : this.$t('bs-select-timezone')
		}

	},
	
    created () {
        for (var k_group in this.timezones) {
            for (var k in this.timezones[k_group]) {
                this.$set(this.ungrupedTimezones, k, this.timezones[k_group][k])
            }
        }
    },
};
</script>
<style lang="scss" scoped>
.tz-selector {

    position: relative;
    background: #fff url("./select-arrow-down.svg") no-repeat right 0.75rem center/8px 10px;

    &.tz-open {
        background: #fff url("./select-arrow-up.svg") no-repeat right 0.75rem center/8px 10px;
    }

    &.disabled {
        pointer-events: none;
        opacity: .7;
    }

    &.is-invalid .tz-selector-selected {
        border: 1px solid var(--danger);
    }

    .tz-selector-selected {
        cursor: pointer;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        line-height: 40px;
        padding: 0px 15px;
        border-radius: 3px;
        border: 1px solid #DDDDDD;
        font-weight: var(--semibold);
        color: var(--secondary);

        i {
            filter: grayscale(1);
            width: 6px;
        }

        &.active {
            border: 1px solid var(--blue);
        }
    }
    .tz-selector-list {
        position: absolute;
        max-height: 250px;
        background-color: #fff;
        box-shadow: 0px 10px 16px #D4D4D466;
        overflow-x: hidden;
        overflow-y: scroll;
        z-index: 1000;
        width: 100%;

        .tz-selector-search {
            margin: 15px;
            width: calc(100% - 30px) !important;
        }

        .tz-selector-ul {
            margin: 0;
            padding: 0;
            list-style: none;

            .tz-selector-li {
                line-height: 1.4;
                padding: 12px 15px;
                cursor: pointer;
                display: flex;
                justify-content: space-between;
                align-items: center;

                span:first-child {
                    margin-right: 5px;
                }

                &:hover, &.active {
                    background-color: var(--blue);
                    color: #fff;
                }
            }

            .tz-selector-li-group {
                height: 46px;
                line-height: 46px;
                padding: 0px 15px;
                font-weight: bolder;
                background-color: #f3f3f3;
                text-align: center;
            }

        }
    }
}
</style>
