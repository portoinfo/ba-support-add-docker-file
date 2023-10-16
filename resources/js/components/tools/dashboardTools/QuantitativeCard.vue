<template>
    <b-card no-body class="overflow-hidden quantitative-card">
        <b-container fluid class="py-2">
            <b-row no-gutters align-h="start" align-v="start" class="flex-nowrap mb-3">
                <b-col>
                    <span class='qtc-title' v-html="title"></span>
                </b-col>
            </b-row>
            <b-row no-gutters align-h="center" align-v="center" class="flex-nowrap mb-2">
                <b-col class="text-center">
                    <span class='qtc-value' v-html="value"></span>
                </b-col>
            </b-row>
        </b-container>
    </b-card>
</template>

<script>
    export default {
        name: 'quantitative-card',
        props: {
            title: {
                type: String,
                default: 'Título'
            },
            value: {
                type: Number,
                default: 10
            }
        },
        mounted() {
        }
    }
</script>

<style lang="scss" scoped>
    .quantitative-card{
        height: 100%;
        .container-fluid{
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-evenly;
            align-items: stretch;
        }
        .qtc-title{
            font-family: Muli;
            font-size: 16px;
            font-weight: 800;
            line-height: 1;
            letter-spacing: 0px;
            color: #333333;
        }
        .qtc-value{
            font-family: Muli;
            font-size: 23.04px;
            line-height: 1.5;
            letter-spacing: 0px;
            color: #333333;
            line-height: 1.5;
        }
    }
</style>