<template>
	<div>
		<div class="container">
			<div class="row">
				<div class="col-sm">
					<label for="nameCompany">Name</label>
					<input type="text" class="form-control inputBa" v-model="nameCompany">
				</div>
				<div class="col-sm">
					<label for="descriptionCompany">Address</label>
					<input type="text" class="form-control inputBa"  v-model="descriptionCompany">
				</div>
				<button class="btn btn-success marginbutton" v-on:click.stop.prevent="updateCompany"><i class="fa fa-save" aria-hidden="true">Update</i></button>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-sm">
					<label for="name">Contact</label>
					<input type="text" class="form-control inputBa" v-model="contactText">
				</div>
				<div class="col-sm">
					<label for="address">Type</label>
					<select class="form-control inputBa" v-model="contactType">
						<option value="PHONE">PHONE</option>
						<option value="EMAIL">EMAIL</option>
						<option value="WHATSAPP">WHATSAPP</option>
						<option value="WEBSITE">WEBSITE</option>
					</select>
				</div>
				<button class="btn btn-success marginbutton" v-on:click.stop.prevent="saveContact"><i class="fa fa-plus" aria-hidden="true"></i></button>
			</div>
		</div>

		<table class="table">
			<thead>
				<tr>
					<th scope="col">#</th>
					<td scope="col">Description</td>
					<td scope="col">type</td>
					<td scope="col">Delete</td>
				</tr>
			</thead>
			<tr v-for="(item, index) in resultQuery" :key="index">
				<th>{{index + 1}}</th>
				<td>{{item.description}}</td>
				<td>{{item.type}}</td>
				<td><button class="btn btn-danger" v-on:click="del(index , item)"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
			</tr>
		</table>
	</div>
</template>

<script>

export default {
	data(){
		return {
			contact: [],
			contactText: "",
			contactType: "",
			nameCompany: this.itemselected.name,
			descriptionCompany: this.itemselected.address,
			searchQuery: null,
		}
	},
	props:{
		itemselected: Object,
        base_url: {
            type: String,
            default: ''
        },
	},
	mounted(){
		var vm = this;
		var url = `${this.base_url}/company/contact/${vm.itemselected.id}`;
		axios.get(url).then(function(r_resposta){

			vm.contact = r_resposta.data.result;

		}).catch(function (error) {

			console.log(error);
		});
	},
	computed: {
		resultQuery(){
			if(this.searchQuery){
				return this.contact.filter((item)=>{
					return this.searchQuery.toLowerCase().split(' ').every(v => item.name.toLowerCase().includes(v))
				})
			}else{
				return this.contact;
			}
		}
	},
	methods:{
		saveContact(){
			var vm = this;
			var url = `${this.base_url}/company/contact`;
			if(vm.contactType == "" || vm.contactText == ""){
				Toast.fire({
					icon: 'error',
					title: 'Empty Fields!'
				});

				return;
			}

			axios.post(url, {
				itemselected: vm.itemselected.id,
				type: vm.contactType,
				description: vm.contactText,
			}).then(function(response){
				//console.log(response.data.created);
				if(response.data.success){
					var my_object = {
						id: response.data.id,
						type: vm.contactType,
						description: vm.contactText,
						is_active: 1,
					};

					vm.contact.push(my_object);

					vm.contactType = "";
					vm.contactText = "";

					Toast.fire({
						icon: 'success',
						title: 'Company save success'
					});
				}else{
					Toast.fire({
						icon: 'error',
						title: 'Company not saved'
					});
				}
			})
			.catch(function(){
				console.log('FAILURE!!');
			});
		},
		back: function() {
			var vm = this;
			vm.$emit('back');
		},
		del(index, item){
			var vm = this;
			var url = `${this.base_url}/company/contact-delet`;

			axios.post(url, {
				item: item.id,
			}).then(function(response){
				//console.log(response.data.created);
				if(response.data.success){

					vm.contact.splice(index, 1);

					Toast.fire({
						icon: 'success',
						title: 'Contact removed success'
					});
				}else{
					Toast.fire({
						icon: 'error',
						title: 'Company not saved'
					});
				}
			})
			.catch(function(){
				console.log('FAILURE!!');
			});
		},
		updateCompany(){
			var vm = this;
			var url = `${this.base_url}/company/update`;
			axios.post(url, {
				id: vm.itemselected.id,
				name: vm.nameCompany,
				description: vm.descriptionCompany,
			}).then(function(response){
				//console.log(response.data.created);
				if(response.data.success){

					vm.$emit('back');
					vm.itemselected.name = vm.nameCompany;
					vm.itemselected.address = vm.descriptionCompany;

					Toast.fire({
						icon: 'success',
						title: 'Contact updated success.'
					});
				}else{
					Toast.fire({
						icon: 'error',
						title: 'Company not updated!'
					});
				}
			})
			.catch(function(){
				console.log('FAILURE!!');
			});
		}
	}
};
</script>

<style scoped>
.marginbutton{
	margin-top:35px;
	margin-bottom:15px;
}
.company{
	margin-left:8px;
	margin-top:5px;
}

</style>
