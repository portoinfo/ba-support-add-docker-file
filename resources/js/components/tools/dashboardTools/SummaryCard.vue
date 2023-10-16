<template>
    <b-card no-body class="overflow-hidden summary-card">
        <b-container fluid class="py-3" style="height: 100%">
            <b-row no-gutters align-h="start" align-v="center" class="flex-nowrap" style="height: 100%">
                <slot name='icon' :circularBgIcon="circularBgIcon"></slot>
                <b-col>
                    <span class='smc-title' v-html="title"></span>
                    <br/>
                    <span class='smc-value' v-html="value"></span>
                    <br/>
                    <span class='smc-notice' v-html="notice"></span>
                </b-col>
            </b-row>
        </b-container>
    </b-card>
</template>
<script>
export default {
    name: 'summary-card',
    props: {
        circularBgIcon: {
            type: Object,
            default: {
                icon: {
                    name: 'person',
                    size: 26,
                    color: 'white'
                },
                size: '67px',
                bgClass: 'default-bg'
            }
        },
        title: {
            type: String,
            default: "Summary Card"
        },
        value: {
            default: "10"
        },
        notice: {
            type: String,
            default: ''
        }
    }    
}
</script>
<style lang="scss" scoped>
    .summary-card{
        width: 100%!important;
        height: 100%!important;
        .smc-title{
            font: normal normal normal 17.6px/13.6px Muli;
            letter-spacing: 0px;
            color: #333;
            line-height: 1.5;
            word-wrap: break-word;
            word-break: break-word;
        }
        .smc-value{
            font: normal normal 800 16px/12.8px Muli;
            letter-spacing: 0px;
            color: #333;
            line-height: 1.5;
            word-wrap: break-word;
            word-break: break-word;
        }
        .smc-notice{
            font: normal normal bold 12px/15px Muli;
            letter-spacing: 0px;
            color: #707070;
            line-height: 1.5;
            margin-bottom: -12px;
            opacity: 0.75
        }
    }
</style>