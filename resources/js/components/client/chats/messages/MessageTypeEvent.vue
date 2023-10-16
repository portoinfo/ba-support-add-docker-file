<template>
	<v-row v-if="isShow" no-gutters justify="center" class="my-5">
		<v-col cols="11" class="text-center d-flex justify-center">
			<v-sheet class="rounded-xl py-1 px-4" color="input" width="fit-content">
                <span class="text-caption font-weight-bold">
					{{ $ct(message.user_name) }}
				</span>
				<span class="text-caption">
					{{ $ct(message.content) }}
				</span>
			</v-sheet>
		</v-col>
	</v-row>
</template>

<script>
export default {
	data() {
        return {
            isShow: true,
        }
    },
	props: {
		message: {
			type: Object,
			default: {},
		},
        settings: Object,
	},
	mounted(){
        this.checkIsShowEventChat();
    },
	methods:{
        checkIsShowEventChat(){
            if('bs-started-the-chat' == this.message.content){
                return this.isShow = true;
            }
			// console.log(this.settings.chat.events);
            if(this.settings.chat.events == undefined){
                return this.isShow = true;
            }
            if(this.settings.chat.events){
                for (let index = 0; index < this.settings.chat.selectedEvents.length; index++) {
                    this.isShow = false;
                    if(this.settings.chat.selectedEvents[index] == this.message.content){
                        this.isShow = true;
                        break;
                    }
                }   
            }else{
                return this.isShow = false;
            }
        }
    }
};
</script>

<style scoped>
</style>