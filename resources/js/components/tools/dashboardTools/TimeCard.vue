<template>
    <b-card no-body class="overflow-hidden time-card">
        <b-container fluid class="py-2">
            <b-row no-gutters align-h="start" align-v="start" class="flex-nowrap mb-3">
                <b-col>
                    <span class='tc-title' v-html="title"></span>
                </b-col>
            </b-row>
            <b-row no-gutters align-h="center" align-v="center" class="flex-nowrap mb-2">
                <b-col class="text-center">
                    <span :class="[durationFormated != $t('bs-no-data-period') ? 'tc-value' : 'tc-value-no-data']" v-html="durationFormated"></span>
                </b-col>
            </b-row>
        </b-container>
    </b-card>
</template>

<script>
    export default {
        name: 'time-card',
        props: {
            title: {
                type: String,
                default: 'Tempo médio de Fila'
            },
            durationInSeconds: {
                type: Number,
                default: 3667
            }
        },
        computed: {
            durationFormated: function() {
                if( this.durationInSeconds > 0) {
                    let d = moment.duration(this.durationInSeconds, 'seconds')

                    let seconds = Math.round(d.asSeconds()) > 0 ? `${d.seconds()}${this.$t('bs-second-abbreviation')}` : ''
                    let minutes = Math.round(d.asMinutes()) > 0 ? `${d.minutes()}${this.$t('bs-minute-abbreviation')}` : ''                
                    let hours = Math.round(d.asHours()) > 0 ? `${d.hours()}${this.$t('bs-hour-abbreviation')}` : ''

                    let days = Math.round(d.asDays()) > 0 ? `${d.days()}${this.$t('bs-day-abbreviation')}` : ''
                    let months = Math.round(d.asMonths()) > 0 ? `${d.months()}${this.$t('bs-month-abbreviation')}` : ''
                    let years = Math.round(d.asYears()) > 0 ? `${d.years()}${this.$t('bs-year-abbreviation')}` : ''

                    let p2 = `${years} ${months} ${days}`
                    p2 = p2.trim() == '' ? '' : `${p2}</br>`
                    
                    
                    return `${p2}${hours} ${minutes} ${seconds}`
                } else {
                    return this.$t('bs-no-data-period')
                }
            }
        },
        mounted() {
        }
    }
</script>

<style lang="scss" scoped>
    .time-card{
        height: 100%;
        .container-fluid{
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-evenly;
            align-items: stretch;
        }
        .tc-title{
            font-family: Muli;
            font-size: 16px;
            font-weight: 800;
            line-height: 1;
            letter-spacing: 0px;
            color: #333333;
        }
        .tc-value{
            font-family: Muli;
            font-size: 23.04px;
            line-height: 1.5;
            letter-spacing: 0px;
            color: #333333;
        }
        .tc-value-no-data{
            font-size: 20px;
        }
    }
</style>