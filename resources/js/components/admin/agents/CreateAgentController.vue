<template>
    <agents-create
        :csid="csid"
        :base_url="base_url"
        @back="goBack"
        @save="saveRedir"
    ></agents-create>
</template>

<script>
export default {
    name: 'create-agent-controller',
    props:{
        csid: String,
        base_url: {
            type: String,
            default: ''
        },
        go_back_url: {
            type: String,
            default: ''
        },
        save_url: {
            type: String,
            default: ''
        }
    },
    methods: {
        goBack() {
            window.open(this.go_back_url, '_self')
        },
        saveRedir(){
            window.open(this.save_url, '_self')
        }
    }
}
</script>