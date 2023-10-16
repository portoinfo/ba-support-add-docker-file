<template>
    <v-app class="login ov-y" style="height: 100%">
		<v-main class="d-flex justify-center align-center">
			<v-col cols="11" xl="4" lg="5" md="6" sm="10" class="mx-auto">
				<v-card
					class="dt-login-card text-center mx-auto"
                    :class="{'py-12': !isIframe}"
					elevation="0"
					max-width="616"
				>
                    <slot></slot>
                </v-card>
			</v-col>
		</v-main>
	</v-app>
</template>

<script>
export default {
    computed: {
        isIframe() {
            return window.top !== window.self;
        }
    },
}
</script>
