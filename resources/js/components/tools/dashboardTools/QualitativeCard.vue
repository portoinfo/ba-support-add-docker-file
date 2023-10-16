<template>
    <b-card no-body class="overflow-hidden qualitative-card">
        <b-container fluid class="py-2">
            <b-row no-gutters align-h="start" align-v="start" class="flex-nowrap mb-3">
                <b-col>
                    <span class='qlc-title' v-html="title"></span>
                </b-col>
            </b-row>
            <b-row no-gutters align-h="center" align-v="center" class="flex-nowrap mb-2">
                <b-form-rating v-model="value_1" readonly precision="2" no-border variant="warning"></b-form-rating> 
                <b-col class="text-center">
                    {{count_1}}
                </b-col> 
            </b-row>
            <b-row no-gutters align-h="center" align-v="center" class="flex-nowrap mb-2">
                <b-form-rating v-model="value_2" readonly precision="2" no-border variant="warning"></b-form-rating> 
                <b-col class="text-center">
                    {{count_2}}
                </b-col> 
            </b-row>
            <b-row no-gutters align-h="center" align-v="center" class="flex-nowrap mb-2">
                <b-form-rating v-model="value_3" readonly precision="2" no-border variant="warning"></b-form-rating> 
                <b-col class="text-center">
                    {{count_3}}
                </b-col> 
            </b-row>
            <b-row no-gutters align-h="center" align-v="center" class="flex-nowrap mb-2">
                <b-form-rating v-model="value_4" readonly precision="2" no-border variant="warning"></b-form-rating> 
                <b-col class="text-center">
                    {{count_4}}
                </b-col> 
            </b-row>
            <b-row no-gutters align-h="center" align-v="center" class="flex-nowrap mb-2">
                <b-form-rating v-model="value_5" readonly precision="2" no-border variant="warning"></b-form-rating> 
                <b-col class="text-center">
                    {{count_5}}
                </b-col> 
            </b-row>
            <b-row no-gutters align-h="center" align-v="center" class="flex-nowrap mb-2">
                <b-col class="text-center">
                    {{$t('bs-total')}} <span class='qlc-percentage' v-html="count_geral"></span>
                </b-col>
                <b-form-rating v-model="media_stars" readonly precision="2" no-border variant="warning"></b-form-rating>
                <b-col class="text-center">
                    {{media_stars}}
                </b-col> 
            </b-row>
        </b-container>
    </b-card>
</template>

<script>
    export default {
        data(){
            return {
                value_1: 1,
                value_2: 2,
                value_3: 3,
                value_4: 4,
                value_5: 5,
            }
        },
        name: 'qualitative-card',
        props: {
            title: {
                type: String,
                default: 'title'
            },
            // positiveLabel: {
            //     type: String,
            //     default: 'Good'
            // },
            // positiveValue: {
            //     type: Number,
            //     default: 6
            // },
            // negativeLabel: {
            //     type: String,
            //     default: 'Bad'
            // },
            // negativeValue: {
            //     type: Number,
            //     default: 4
            // },
            count_1: '',
            count_2: '',
            count_3: '',
            count_4: '',
            count_5: '',
            count_geral: '',
            media_stars: '',
        },
        // computed: {
        //     satisfactionPercentage: function() {
        //         let total = this.positiveValue + this.negativeValue
        //         return total == 0 ? '0%' : `${ ((this.positiveValue/total) * 100).toFixed(2) }%`
        //     }
        // },
        mounted() {
        }
    }
</script>

<style lang="scss" scoped>
    .qualitative-card{
        height: 100%;
        .container-fluid{
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-evenly;
            align-items: stretch;
        }
        .qlc-title{
            font-family: Muli;
            font-size: 16px;
            font-weight: 800;
            line-height: 1;
            letter-spacing: 0px;
            color: #333333;
        }
        .qlc-percentage,
        .qlc-positive-value,
        .qlc-negative-value{
            font-family: Muli;
            font-size: 23.04px;
            line-height: 1.5;
            letter-spacing: 0px;
            letter-spacing: 0px;
            color: #333333;
        }
        .qlc-negative-label,
        .qlc-positive-label{
            font-family: Muli;
            font-size: 12px;
            font-weight: 800;
            line-height: 1;
            letter-spacing: 0px;
            color: #333333;
        }
    }
    .negative-mb{
        margin-bottom: -12px;
    }
</style>