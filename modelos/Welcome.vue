<template>
    <div>
        <b-container fluid>
            <div class="welcome-container">
                <div class="welcome-wrapper justify-content-md-around">
                    <div class="d-none d-md-flex align-items-end">
                        <img src="/images/images/welcome/man.svg" class="mb-5">
                    </div>
                    <div>
                        <form action="/set-settings" method="post">
                            <h1 class="bb-h1">{{ $t('bb-welcome') }}!</h1>
                            <p class="mt-1">{{ $t('bb-welcome-page-message') }}</p>
                            <b-alert variant="danger" :show="errors.length > 0">
                                <ul class="m-0" style="list-style-type: none;">
                                    <li v-for="(error, i) in errors" :key="i">
                                        {{ $t(error) }}
                                    </li>
                                </ul>
                            </b-alert>

                            <b-card class="mt-5" body-class="mx-sm-4">
                                <b-form-group :label="$t('bb-timezone')">
                                    <input type="hidden" name="timezone" v-model="selectedTimezone" />
                                    <input type="hidden" name="_token" :value="csrf">
                                    <timezone-selector
                                        :timezones="timezones"
                                        :selectedTimeZone="selectedTimezone"
                                        @change="selectedTimezone = $event"
                                    ></timezone-selector>
                                </b-form-group>
                            </b-card>

                            <b-card class="my-4" body-class="mx-sm-4">
                                <b-form-group :label="$t('bb-time-format')">
                                    <input type="hidden" name="time_format" v-model="selectedTimeFormat" />
                                    <v-select
                                        :dir="$dir"
                                        style="background-color: #fff; height: 44px"
                                        :clearable="false"
                                        :searchable="false"
                                        :options="timeFormatOptions"
                                        label="label"
                                        :reduce="value => value.value"
                                        v-model="selectedTimeFormat"
                                    >
                                        <template #option="{label, example}">
                                            {{label}}
                                            <small :class="$isRTL ? 'float-left' : 'float-right'">
                                            {{example}}</small>
                                        </template>
                                    </v-select>
                                </b-form-group>
                            </b-card>

                            <b-card class="my-4" body-class="mx-sm-4">
                                <b-form-group :label="$t('bb-url-path')">
                                    <input type="hidden" name="path" v-model="path" />
                                    <b-input-group :prepend="prependUrl">
                                        <b-form-input type="text" v-model="path" class="bg-white"></b-form-input>
                                    </b-input-group>
                                </b-form-group>
                            </b-card>

                            <div class="mt-5">
                                <b-button variant="primary" size="lg" type="submit">{{ $t('bb-lets-go') }}</b-button>
                            </div>
                        </form>
                    </div>
                    <div class="d-none d-md-block">
                        <img src="/images/images/welcome/woman.svg" class="mt-5">
                    </div>
                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
    export default {
        props: ['timezones', 'errors', 'user'],
        data() {
            return {
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                selectedTimezone: this.$moment.tz.guess(),
                selectedTimeFormat: '24h',
                path: '',
                prependUrl: location.hostname+'/c/',
                timeFormatOptions: [
                    {value: '12h', label: '12h (AM/PM) ', example: this.$moment().format('hh:mm a')}, 
                    {value: '24h', label: '24h ', example: this.$moment().format('HH:mm')}
                ]
            }
        },
        mounted() {
            document.body.style.backgroundColor = '#FAFAFA'
            this.path = this.user.path
        }
    }
</script>
<style lang="scss">
.welcome-container {
    display: flex;
    align-items: center;
    margin: 3rem 0;
    @media screen and (min-height: 768px) {
        height: 100vh;
        margin: 0;
    }
    p {
        font-size: 1rem;
    }
    .welcome-wrapper {
        display: flex;
        text-align: center;
        justify-content: center;
        flex: 1;
        > div:nth-child(2) {
            width: 80%;
            max-width: 600px;
        }
        img {
            height: 60%;
        }
        .card {
            box-shadow: 0px 2px 4px #0000001A;
            border-radius: 5px;
            text-align: left;
            legend {
                font-weight: bold;
                color: var(--secondary);
            }
		}
		.bb-h1 {
			color: var(--primary);
			font-weight: var(--extrabold);
		}
    }
}

</style>
