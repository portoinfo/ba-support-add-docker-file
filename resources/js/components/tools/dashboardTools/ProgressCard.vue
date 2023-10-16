<template>
    <b-card class="mb-2 progress-card">
        <!-- header -->
        <b-card-header class="px-0 py-2 mb-3 pgc-header" v-html="title"></b-card-header>
        <b-card-text>
            <b-container fluid class="px-0 ">
                <!-- PERCENT increase/decrease-->
                <b-row align-h="between" align-v="end" cols='2' no-gutters class='mb-2 pgc-percent-row'>
                    <b-col>
                        <p class="pgc-diff" v-html="`${diff}`"></p>
                    </b-col>
                    <b-col class="text-right">
                        <p :class="['pgc-percent', colorClass]" >
                            {{percent}}%<i :class="['fa fa-arrow-right arrow-settings ml-1',  arrowClass]" aria-hidden="true"></i>
                        </p>
                    </b-col>
                </b-row>
                <!-- TODAY -->
                <b-row class="mb-2 pgc-progress-bar-row">
                    <b-col>
                        <b-row>
                            <b-col>
                                <b-progress :height="todayBarHeight"  :max="totalToday">
                                    <b-progress-bar  :value="doneToday" :variant="todayBarVariant"></b-progress-bar>
                                </b-progress>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col class="text-right progress-bar-label">
                               {{ $t('bs-today') }} : {{doneToday}} / {{totalToday}}
                            </b-col>
                        </b-row>
                    </b-col>
                </b-row>
                <!-- YESTERDAY -->
                <b-row class="pgc-progress-bar-row">
                    <b-col>
                        <b-row>
                            <b-col>
                                <b-progress :height="yesterdayBarHeight"  :max="totalYesterday">
                                    <b-progress-bar  :value="doneYesterday" :variant="yesterdayBarVariant"></b-progress-bar>
                                </b-progress>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col class="text-right progress-bar-label">
                                {{ $t('bs-yesterday') }}: {{doneYesterday}} / {{totalYesterday}}
                            </b-col>
                        </b-row>
                    </b-col>
                </b-row>
            </b-container>
        </b-card-text>
    </b-card>
</template>

<script>
    export default {
        name: 'progress-card',
        props: {
            title: {
                type: String,
                default: 'Example Title'
            },
            doneToday: {
                type: Number,
                default: 200
            },
            
            doneYesterday: {
                type: Number,
                default: 300
            },
            totalToday: {
                type: Number,
                default: 700
            },
            totalYesterday: {
                type: Number,
                default: 700
            },
            todayBarVariant: {
                type: String,
                default: 'primary'
            },
            todayBarHeight: {
                type: String,
                default: '6px'
            },
            yesterdayBarVariant: {
                type: String,
                default: 'secondary'
            },
            yesterdayBarHeight: {
                type: String,
                default: '6px'
            },       
        },
        computed: {
            diff: function() {
                return this.doneToday - this.doneYesterday
            },
            percent: function () {
                if(this.doneYesterday == 0){
                    return this.doneToday == 0 ? 0 :  this.doneToday > 0 ? 100 : -100
                }
                return ( ((this.doneToday/this.doneYesterday) -1) * 100 ).toFixed(2)
            },
            arrowClass: function() {
                if(this.diff > 0) {
                    return 'diagonal-up'
                } else if(this.diff < 0) {
                    return 'diagonal-down'
                } else {
                    return 'd-none'
                }
            },
            colorClass: function() {
                if(this.diff > 0) {
                    return 'percent-increase-color'
                } else if(this.diff < 0) {
                    return 'percent-decrease-color'
                } else {
                    return ''
                }
            },
        }
    }
</script>

<style lang="scss" scoped>
    .progress-card{
        .pgc-header{
            background-color: transparent!important;
            font: normal normal bold 16px/19px Muli;
            letter-spacing: 0px;
            line-height: 1.2;
            color: #333;
        }
        .pgc-percent-row{
            .pgc-diff{
                font-family: Muli;
                font-weight: 600;
                font-size: 16px;
                line-height: 1.2;
            }
            .pgc-percent{
                font-family: Muli;
                font-size: 12px;
                line-height: 1.2;
                
                .arrow-settings{
                    font-size: 0.8em!important;
                }
                .diagonal-up{ /* seta diagonal right up */
                    transform: rotate(-45deg);
                }
                .diagonal-down{
                    transform: rotate(45deg);        
                }
            }
            .percent-increase-color{
                color: #34AA44;
            }
            .percent-decrease-color{
                color: #E6492D;
            }
        }
        .pgc-progress-bar-row{
            .progress-bar-label {
                font-family: Muli;
                font-size: 10px;
                font-weight: bold;
            }
        }
    }
</style>