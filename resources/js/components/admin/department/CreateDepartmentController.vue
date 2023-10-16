<template>
    <department-create
		@save="saveRedirect"
		@back="goBackRedirect"
        :base_url="base_url"
	></department-create>
</template>

<script>
export default {
    name: 'create-department-controller',
    props: {
        save_redirect_url: {
            type: String,
            default: '#'
        },
        go_back_redirect_url: {
            type: String,
            default: '#'
        },
        base_url: {
            type: String,
            default: ''
        },
    },
    methods: {
        saveRedirect: function() {
            window.open(this.save_redirect_url, '_self')
        },
        goBackRedirect: function () {
            window.open(this.go_back_redirect_url, '_self')
        }
    }
};
</script>