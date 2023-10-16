<!-- <template>
	<div>
		<b-container fluid="lg">
			<b-row>
				<b-col cols="auto" class="mr-auto p-3 title">Cadastrar Companhia  
					<b-card-text class="subtitle">
						cadastre uma nova companhia
					</b-card-text></b-col>

					<b-col cols="auto mt-4">
						<b-button @click="btnBack" variant="light btn-back">CANCELAR</b-button>
						<b-button @click="onSubmit" variant="btn btn-save"><i class="fa fa-floppy-o" aria-hidden="true"></i> SALVAR</b-button>
					</b-col>
				</b-row>
				<br><br>
				<div class="body col-lg-8">
					<div>
						<b-form @submit="onSubmit" @reset="onReset" v-if="show" class="label">

							<b-form-group id="input-group-1" label="Name:" label-for="input-2">
								<b-form-input
								id="input-1"
								v-model="form.name"
								required
								placeholder="Enter name"
								class="bs-input"
								:state="vName"
								></b-form-input>
							</b-form-group>

							<b-form-group id="input-group-2" label="Endereço:" label-for="input-2">
								<b-form-input
								id="input-2"
								v-model="form.address"
								required
								placeholder="Address"
								class="bs-input"
								:state="vAddress"
								></b-form-input>
							</b-form-group>

							<b-form-group id="input-group-3" label="Email:" label-for="input-1">
								<b-form-input
								id="input-3"
								v-model="form.email"
								type="email"
								required
								placeholder="Enter email"
								class="bs-input"
								:state="vEmail"
								></b-form-input>
							</b-form-group>

							<b-form-group id="input-group-4" label="Website:" label-for="input-1">
								<b-form-input
								id="input-4"
								v-model="form.website"
								required
								placeholder="Link"
								class="bs-input"
								:state="vWebSite"
								></b-form-input>
							</b-form-group>
							<b-row>
								<b-col sm="5">
									<b-form-group id="input-group-5" label="Telefone/Celular:" label-for="input-1">
										<b-row>
											<b-col>
												<b-form-input
												id="input-5"
												v-model="form.phone"
												required
												placeholder="Number"
												class="bs-input"
												:state="vPhone"
												></b-form-input>
											</b-col>
										</b-row>
									</b-form-group>
								</b-col>
								<b-col>
									<b-form-group id="input-group-6" label="Logo:" label-for="input-1">
										<b-form-input
										id="input-6"
										v-model="form.logo"
										required
										placeholder="Link"
										class="bs-input"
										:state="vLogo"
										></b-form-input>
										<b-form-group
											id="fieldset-horizontal"
											description="Formato: JPEG, PNG ou GIF. O arquivo poderá ter até 4MB."
											label-for="input-horizontal"
											>
										</b-form-group>
									</b-form-group>
							</b-col>
						</b-row>
						<b-form-group id="input-group-4" label="Descrição:" label-for="input-1">
							<b-form-textarea
							id="textarea"
							v-model="form.description"
							placeholder="..."
							class="bs-input"
							rows="3"
							max-rows="6"
							></b-form-textarea>
						</b-form-group>
					</b-form>

				</div>
			</div>
			<vue-snotify></vue-snotify>
		</b-container>
	</div>
</template>

<script>
export default {
	data() {
		return {
			form: {
				email: '',
				name: '',
				address: '',
				website: '',
				phone: '',
				logo: '',
				description: '',
			},
			formCompany: {
				name: false,
				email: false,
				address: false,
				website: false,
				phone: false,
				logo: false,
				description: false,
			},
			show: true,
		}
	},
	props:{
		usuario: Object,
        base_url: {
            type: String,
            default: ''
        },
	},
	mounted(){
		var vm = this;
		if(vm.usuario.language == 'pt_BR'){
			vm.form.phone = '+55 ';
		}
	},
	methods: {
		onSubmit(evt) {
			var vm = this;
			evt.preventDefault();
			if(this.formCompany.name && this.formCompany.email && this.formCompany.address && this.formCompany.website && this.formCompany.phone && this.formCompany.logo && this.formCompany.description ){
				alert(JSON.stringify(this.form));
			}else{
				vm.$snotify.success('Example body content', 'Example Title');

				alert('CAMPO INVALIDO!');
			}
		},
		onReset(evt) {
			evt.preventDefault()
		        // Reset our form values
		        this.form.email = '';
		        this.form.name = '';
		        this.form.food = null;
		        this.form.checked = [];
		        // Trick to reset/clear native browser form validation state
		        this.show = false;
		        this.$nextTick(() => {
	        	this.show = true;
	        });
	    },
	    btnBack(){
			var vm = this;
	    	vm.$emit('createCompany');
	    }
	},
		computed: {
			vName() {
				this.formCompany.name = this.form.name.length > 1 && this.form.name.length < 100;
				return this.formCompany.name;
			},
			vAddress() {
				this.formCompany.address = this.form.address.length > 1 && this.form.address.length < 100;
				return this.formCompany.address;
			},
			vEmail(){
				var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				this.formCompany.email = re.test(this.form.email);
				//console.log(this.formCompany.email);
				return this.formCompany.email;
			},
			vWebSite(){
				this.formCompany.website = this.form.website.length < 100;
				return this.formCompany.website;
			},
			vPhone(){
				this.formCompany.phone = this.form.phone.length < 100;
				return this.formCompany.phone;
			},
			vLogo(){
				this.formCompany.logo = this.form.logo.length < 100;
				return this.formCompany.logo;
			},
		}
	};
	</script>

	<style scoped>
	.title{
		text-align: left;
		font: normal normal 800 25px/31px Muli;
		letter-spacing: 0px;
		color: #0080FC;
		opacity: 1;
	}

	.subtitle{
		text-align: left;
		font: normal normal 800 15px/16px Muli;
		letter-spacing: 0.45px;
		color: #434343;
		opacity: 0.5;
	}

	.btn-back{
		background: #F8F8F8 0% 0% no-repeat padding-box;
		box-shadow: 0px 1px 1px #1E120D1A;
		border-radius: 5px;
		opacity: 1;
		font: normal normal 800 14px/16px Muli;
		color: #434343;
		text-transform: uppercase;
	}

	.btn-save{
		background-color: #4cf0fc;
		box-shadow: 0px 1px 1px #1E120D1A;
		color: white;
		font: normal normal 800 14px/16px Muli;
	}

	.body{
		background: #FFFFFF 0% 0% no-repeat padding-box;
		border-radius: 4px;
		opacity: 1;
		padding: 41px;
	}

	.label{
		text-align: left;
		font: normal normal bold 14px/18px Muli;
		letter-spacing: 0px;
		color: #707070;
		opacity: 1;
	}

	.bs-input{
		border: 1px solid #a4a4a4;
		padding-top: 18px;
		padding-bottom: 18px;
		padding-left: 20px;
		text-align: left;
		font: normal normal bold 14px/18px Muli;
		letter-spacing: 0px;
		opacity: 0.75;
	}
	</style>


<!-- <template>
	<div>
		<div class="container">
			<div class="row body">
				<div class="col-sm">
					<label for="name">Name</label>
					<input type="text" class="form-control inputBa" id="name" v-model="name" required>
				</div>
				<div class="col-sm">
					<label for="address">Address</label>
					<input type="text" class="form-control inputBa" id="address" v-model="address" required>
				</div>
				<button style="margin: 10px;margin-top:30px;margin-bottom: 15px;" class="btn btn-success" v-on:click.stop.prevent="saveCompany">Save</button>
			</div>
		</div>
	</div>
</template>

<script>

export default {
	data(){
		return {
			name: "",
			address: "",
		}
	},
	methods:{
		saveCompany(){
			var url = `${this.base_url}/company/createCompany`;
			var vm = this;

			if(vm.name == "" || vm.address == ""){
				Toast.fire({
					icon: 'error',
					title: 'Campos vazios'
				});

				return;
			}
			
			axios.post(url, {
				name: vm.name,
				address: vm.address
			}).then(function(response){
				console.log(response.data.created);

				if(response.data.success){
					var my_object = {
						id: response.data.id,
						name: vm.name,
						address: vm.address,
						created_at: response.data.created,
					};

					vm.$emit('save', my_object);

					vm.name = "";
					vm.address = "";

					Toast.fire({
						icon: 'success',
						title: 'Company create success'
					});
				}else{
					Toast.fire({
						icon: 'error',
						title: 'Company not create'
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
.body{
	padding:10px;
}
</style> --> -->