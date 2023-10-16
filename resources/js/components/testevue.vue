<template>
	<div>
		<!-- <header>
			<h1>Record and Play Audio in JavaScript</h1>
		</header>

		<audio controls>
			<source src="/media/ticket-notification.mp3" type="audio/mpeg">
			Your browser does not support the audio element.
		</audio>
		<b-button @click=gg>Musica</b-button> -->

		<input type="text" v-model="text">
		<button @click="getRobot">send</button>

		<spanm v-for="(item, index) in array" :key="index" :value="item.value">
            {{ item }}
		</spanm>
	</div>
</template>

<script>
export default {
	data(){
		return {
			nome: "THEBESLOKOSOM!",
			audio: new Audio("/media/ticket-notification.mp3"),
			array: [],
			text: "",
		}
	},
	mounted(){
	},
	methods: {
		getRobot(){
			var data = `{\r\n    \"message\": \"${this.text}\"\r\n}`;
			// console.log(data);

			var config = {
				method: 'post',
				url: 'http://127.0.0.1:5000/send/message',
				headers: {
					'Content-Type': 'text/plain',
				},
				data: data
			};

			axios(config)
				.then(res => {
					console.log(res.data);

					// MessageService.send(
					// 	res.data, 
					// 	"text",
					// 	id,
					// 	chat.sectorID,
					// 	sessionStorage.getItem('operator_token'), true
					// );

					this.array.push([res.data]);


				})
				.catch(function (error) {
					console.log(error);
				});
		},
	},
};
</script>

<style scoped>
</style>