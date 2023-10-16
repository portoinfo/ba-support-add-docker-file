<template>
	<div>
		<b-container fluid="lg" v-if="showSelect">
			<b-row>
				<b-col cols="auto" class="mr-auto p-3 bs-title">{{$t('bs-select-the-company')}}
					<b-card-text class="bs-subtitle">
						{{$t('bs-select-a-company')}}
					</b-card-text>
				</b-col>
				<b-col cols="auto mt-4">
			    	<b-row>
			    		<b-col sm="auto">
							<b-form-input  variant="light" class="bs-input icon" v-model="searchQuery"
							:placeholder="buscar"></b-form-input>
			    			<i class="fa fa-search iconButton" aria-hidden="true"></i>
						</b-col>
			    		<b-col>
			    			<span v-if="usuario.builderall_status == 'ACTIVE'">
								<div v-if="usuario.can_create_company == 1">
									<b-button @click="toggleSelectCompany" variant="btn bs-btn-add">
										<i class="fa fa-plus" aria-hidden="true"></i> {{$t('bs-register-new-company')}}
									</b-button>
								</div>
			    			</span>
						</b-col>
					</b-row>
				</b-col>
			</b-row>
			<br><br>
			<b-row>
				<div v-for="(item, index) in resultQuery" :key="index">
					<span v-if="item.is_admin">
						<b-col cols="auto">
							<div class="mb-2 companycard stylecompany">
								<span>
									<b-badge variant="primary"><i class="fa fa-star" aria-hidden="true"></i> Admin</b-badge>
									<i @click="selectCompany(item, 'config')" class="fa fa-cog bs-cog caret" style="float:right;" aria-hidden="true"></i>
									<i @click="steep1(item)" class="fa fa-trash bs-trash caret mr-2" style="float:right;" aria-hidden="true"></i>
								</span>
								<img
									@click="selectCompany(item)"
									class="caret mt-2"
									:src="companyImg(item.logo)"
									:alt="item.name"
									width="180"
									height="180"
									@error="setAltImg"
								>
								<hr>
								<center class="text-center mt-3">{{item.name}}</center>
							</div>
						</b-col>
					</span>
					<span v-else>
						<b-col cols="auto">
							<div class="mb-2 companycard stylecompany">
								<span><b-badge variant="secondary">{{$t('bs-attendant')}}</b-badge></span><br>
								<img
									@click="selectCompany(item)"
									class="caret mt-2"
									:src="companyImg(item.logo)"
									:alt="item.name"
									width="180"
									height="180"
									@error="setAltImg"
								>
								<hr>
								<center class="text-center mt-3">{{item.name}}</center>
							</div>
						</b-col>
					</span>
				</div>
			</b-row>
	</b-container>
	<div v-if="showCreate">
		<company-body :usuario="usuario" v-on:create-company="toggleSelectCompany" v-on:go-back="toggleSelectCompany" v-on:new-company="newCompany" :company_list="company" :viewcontact="true" :base_url="base_url" ></company-body>
	</div>

	<block-system :user="usuario"></block-system>


	<div class="modal fade" id="showDeletecompanyModal" tabindex="-1" aria-labelledby="showDeletecompanyModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<span v-if="steeps">
					<div class="border-0 p-0">
						<center><h5 class="modal-title" id="showDeletecompanyModalLabel">{{$t('bs-he-is-sure')}}</h5></center>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-12">
								<center><h5>{{$t('bs-you-cannot-reverse-this')}}</h5></center>
							</div>
						</div>
					</div>

					<b-row class="mb-3">
						<b-col>
							<center>
								<button
								type="button"
								class="text-capitalize btn btn btn-primary"
								data-dismiss="modal"
								>
								{{ $t("bs-cancel").toUpperCase() }}
								</button>
							</center>
						</b-col>
						<b-col>
							<center>
								<button
								type="button"
								id="btn-new-chat"
								class="btn btn-danger"
								@click="steep2"
								>
								{{ $t("bs-delete").toUpperCase() }}
								</button>
							</center>
						</b-col>
					</b-row>
				</span>
				<span v-if="!steeps">
					<div class="border-0 p-0">
						<center><h5 class="modal-title" id="showDeletecompanyModalLabel">{{$t('bs-type-your-password')}} </h5></center>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-12">
								<b-form-input type="password" id="passComp" autocomplete="new-password" v-model="password">{{$t('bs-password')}}</b-form-input>
							</div>
						</div>
					</div>

					<b-row class="mb-3">
						<b-col>
							<center>
								<button
								type="button"
								class="text-capitalize btn btn btn-primary"
								data-dismiss="modal"
								@click="cancelModal"
								>
								{{ $t("bs-cancel").toUpperCase() }}
								</button>
							</center>
						</b-col>
						<b-col>
							<center>
								<button
								type="button"
								id="btn-new-chat"
								class="btn btn-danger"
								@click="deleteCompany"
								>
								{{ $t("bs-delete").toUpperCase() }}
								</button>
							</center>
						</b-col>
					</b-row>
				</span>

			</div>
		</div>
	</div>


</div>
</template>

<script>

export default {
	data(){
		return {
			buscar: this.$t('bs-search'),
			company: [],
			logoImage: '',
			showSelect: true,
			showCreate: false,
			title: this.$t('bs-register-company'),
			subtitle: this.$t('bs-register-new-company'),
			searchQuery: null,
			newcompanyBoolean: false,
			itemselected: [],
			steeps: true,
			password: "",
		}
	},
	props:{
		usuario: Object,
		is_admin: String,
        base_url: {
            type: String,
            default: ''
        },
	},
	mounted(){
		localStorage.removeItem('preselectDepartment'); // importante remover isso para não mostrar ticket de outro departamento.
		var vm = this;
		var url = `${this.base_url}/company/get-company`;
		axios.get(url).then(function(r_resposta){

			vm.company = r_resposta.data.result;

			if(vm.company.length == 0){
				// movido para dentro de company-body
				// vm.title = vm.$t('bs-welcome');
				// vm.subtitle = vm.$t('bs-make-your-first-company-registration');
				vm.toggleSelectCompany();
			}

		}).catch(function (error) {

			console.log(error);
		});
	},
	computed: {
		resultQuery(){
			if(this.searchQuery){
				return this.company.filter((item)=>{
					return this.searchQuery.toLowerCase().split(' ').every(v => item.name.toLowerCase().includes(v))
				})
			}else{
				return this.company;
			}
		}
	},
	methods:{
		steep1(item){
			this.itemselected = item;
			this.searchQuery = '';
			$('#showDeletecompanyModal').modal('show');
		},
		steep2(){
			this.steeps = false;
			this.searchQuery = '';
		},
		deleteCompany(){
			var vm = this;
			var url = `${this.base_url}/company/CompanyDelete`;
			//console.log('item = ', item);

			axios.post(url, {
				id: vm.itemselected.id,
				password: vm.password,
			}).then(function(response){
				if(response.data.success){
					$('#showDeletecompanyModal').modal('hide');
					vm.password = '';
						
					if(response.data.logout){
						window.location.href ='logout';
					}

					let index = vm.company.findIndex((item) => item.id === vm.itemselected.id);
					if (index !== -1) {
						vm.company.splice(index, 1);
						vm.searchQuery = '';
					}

					vm.$snotify.success(vm.$t('bs-company-successfully-deleted'), vm.$t('bs-success'));

				}else{
					if(response.data.error == 'password'){
						vm.$snotify.error(vm.$t('bs-invalid-password'), vm.$t('bs-error'));
						vm.password = '';
					}
				}
			})
			.catch(function(erro){
				// console.log(erro);
				console.log('FAILURE!!');
			});
		},
		selectCompany(item, action = 'home'){
			var vm = this;
			var url = `${this.base_url}/select-company`;
			//console.log('item = ', item);

			axios.post(url, {
				companyselected: item,
			}).then(function(response){
				//console.log(response.data.success);
				if(response.data.success){
					if(response.data.value == 'blocked'){
						// showmodal
						$("#BlockSystem").modal("show");
					}else{
						if(action == 'home'){
							window.location.href = "/";
						}else{
							window.location.href = "edit-company?action="+action+"&hc="+item.hash_code;
						}
					}
				}
			})
			.catch(function(){
				console.log('FAILURE!!');
			});
		},
		companyImg(link){
			var vm = this;
			//console.log(link);
			// var img = new Image();
			// img.onload = function() {
			// 	//  alert(this.width + 'x' + this.height);
			// 	if(this.width == this.height && this.height < 250 && this.width < 250){

			// 	}
			// }
			// img.src = 'https://img.ibxk.com.br/2020/01/30/30021141299110.jpg?w=1120&h=420&mode=crop&scale=both';

			// if(link === undefined || link == null || link.trim() == ''){
			// 	//https://cdn4.iconfinder.com/data/icons/flat-brand-logo-2/512/cocacola-512.png
			// 	//https://cdn3.iconfinder.com/data/icons/logos-and-brands-adobe/512/251_Pepsi-512.png
			// 	return 'https://office.builderall.com/internacional/public/images/default-user.png';
			// }else{

			// }

			if(link == null){
				link = "";
			}

			return link;
		},
		setAltImg(event){
			event.target.src = 'https://ba-support.builderall.io/images/icons/companhia.svg';
		},
		toggleSelectCompany(){
			var vm = this;
			vm.showSelect = !vm.showSelect;
			vm.showCreate = !vm.showCreate;
		},
		newCompany(item){
			var vm = this;
			//vm.company.push(item);
			vm.newcompanyBoolean = true;
			vm.selectCompany(item, 'new');
			//window.location.href = "/edit-company";
		},
		cancelModal(){
			this.steeps = true;
			this.password = '';
		},
	},
};
</script>

<style scoped>
.caret {
  cursor: pointer;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

.stylecompany{
	max-width: 14rem;
	min-width: 14rem;
	min-height: 18rem;
	background-color: white;
}

.companycard{
	box-shadow: 0px 3px 6px #00000029;
	font: normal normal 600 18px/17px Muli;
	letter-spacing: 0px;
	color: #333333;
	opacity: 1;
	padding: 20px;
	padding-bottom: 0px;
	margin: 11px;
}

.icon{
	padding-left: 30px;
	font: normal normal normal 14px/17px Muli;
}

.iconButton{
	top: 11px;
	left: 26px;
	position: absolute;
	text-align: center;
	justify-content: center;
	color: black;
	opacity: 0.75;
}


.modal-content {
	background: #f3f7ff 0% 0% no-repeat padding-box;
	box-shadow: 0px 14px 32px #00000040;
	border-radius: 10px;
	border: none;
	padding-top: 40px;
	padding-left: 40px;
	padding-right: 40px;
}

.modal-title {
	font: normal normal bold 28px/32px Muli;
	letter-spacing: 0px;
	color: #201f1f;
}


.modal {
	background-color: #59607173;
}

.modal .close {
	background: #acb8d8;
	color: white;
	text-shadow: none;
	padding: 2px;
	margin-top: 1px;
	border-radius: 100%;
	font-size: 15px;
	height: 25px;
	width: 25px;
	margin-right: 2px;
}

.modal-body label {
	font: normal normal bold 14px/35px Muli;
	letter-spacing: 0px;
	color: #656565;
}

.modal-body select {
	background: #fafbfc 0% 0% no-repeat padding-box;
	border: 1px solid #dddddd;
	border-radius: 3px;
	height: 50px;
}

.example-open .modal-backdrop {
	background-color: red;
}

@media only screen and (max-width: 575px) {
	.content {
		margin-right: 10px;
		margin-left: 10px;
	}

	.card-header {
		padding-top: 20px;
		height: 80px;
		zoom: 120%;
	}

	.modal-dialog {
		width: 100%;
		height: 100%;
		margin: 0;
		padding: 0;
	}

	.modal-content {
		height: auto;
		min-height: 100%;
		border-radius: 0;
	}

	.modal-footer {
		padding-right: 0px;
		padding-left: 0px;
	}
}
</style>
