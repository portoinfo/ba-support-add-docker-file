<template>
    <!-- componente removido 15/02/2021-->
    <group-create
        :usuario="usuario"
        :base_url="base_url"
        @back="goBackRedir"
        @save="saveRedir"
    ></group-create>
</template>

<script>
export default {
    name: 'create-group-controller',
    props: {
        usuario: Object,
		base_url: {
			type: String,
			default: ''
        },
        go_back_redir_url: {
			type: String,
			default: ''
        },
        save_redir_url: {
			type: String,
			default: ''
		},
    },
    methods: {
        goBackRedir(){
            window.open(this.go_back_redir_url, '_self')
        },
        saveRedir(){
            window.open(this.save_redir_url, '_self')
        }
    }
}
</script>

<style lang="scss" scoped>
</style>