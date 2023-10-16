<template>
	<v-row no-gutters justify="center">
        <v-col cols="12" class="my-3"><v-divider></v-divider></v-col>
		<v-col cols="12" class="text-center d-flex justify-center">
			<v-sheet class="rounded-xl py-1 px-4" color="input" width="fit-content">
                <span class="text-caption font-weight-bold">
					{{ $ct(message.user_name) }}
				</span>
				<span class="text-caption">
					{{ $ct(test(message.content)) }}
				</span>
			</v-sheet>
		</v-col>
	</v-row>
</template>

<script>
export default {
	props: {
		message: {
			type: Object,
			default: {},
		},
	},
	methods: {
		test(item){
			if(item == 'bs-closed-the-ticket'){
				return 'bs-replied';
			}else{
				return item;
			}
		}
	},
};
</script>

<style scoped>
</style>