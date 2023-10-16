<template>
	<div class="ba-hd__right-sidebar2 modal-custom">
		<div class="ba-hd__card-content">
			<header class="pr-2 pl-2">
				<div style="display: grid; grid-template: auto / auto  auto;">
					<div class="text-left d-table">
						<b class="d-table-cell align-middle">{{ $t('Banco de dados') }}</b>
					</div>
					<div @click="closeModal" class="text-right pt-1">
						<span class="bs-ico cursor-pointer">&#xe5cd;</span>
					</div>
				</div>
			</header>

			<div class="pl-3 pr-3 pt-2">
				<b-form-group>
					<input
						type="search"
						v-model="searchQuery"
						class="form-control"
						:placeholder="$t('bs-search')"
						v-on:keyup.enter="addSearch"
					/>
					<b-form-radio-group
						id="radio-group-1"
						v-model="selected"
						:options="options"
						name="radio-options"
					></b-form-radio-group>
					<span v-for="(item, index) in search" :key="index" :value="item.value">
						<b-badge :variant="item[1] == 'category' ? 'primary' : 'warning'">X {{ item[0] }}</b-badge>
					</span>
				</b-form-group>
			</div>

			<div class="pl-3">
				<b-row>
					<b-col class="scrollcust" cols="auto">
						<table class="table">
							<tbody>
								<tr v-for="(item, index) in TicketorChat" :key="index" :value="item.value" >
									<th scope="row" @click="getChatHistory(item)">{{item.ticket_id}}</th>
									<td v-if="!showDescription" class="caret" @click="getChatHistory(item)">
										<span class="caret">
											<div class="output ql-snow">
												<div class="ql-editor pt-0 pl-0" v-viewer v-html="`<b>${$t('bs-description')}:</b> ${item.description}`"></div>
											</div>
										</span>
									</td>
								</tr>
							</tbody>
						</table>
					</b-col>
					<b-col class="scrollcust borderdescription" v-if="showDescription">			
						<template>
							<div class="output ql-snow">
								<div class="ql-editor pt-0 pl-0" v-viewer v-html="`<b>${$t('bs-description')}:</b> ${itemselected.description}`"></div>
							</div>
						</template>
						<span v-for="(item, index) in chat_history_before" :key="index+'aa'">
								<!-- FALA FUNCIONARIO -->
								<span v-if="item.company_user_company_department_id != null">
									<!-- TIPO TEXTO -->
									<template v-if="item.type === 'TEXT'">
										<b style="text-transform: capitalize;">
											<span style="color:#B40404;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ $t(item.name) }}:</span>
										</b>
										<div class="output ql-snow">
											<div class="ql-editor" v-viewer v-html="item.content"></div>
										</div>
									</template>
									<!-- TIPO EVENT -->
									<template v-if="item.type === 'EVENT'">
										<center>
											<span>
												{{ translate(item.content) }}<br>
											</span>
										</center>
									</template>
									<!-- TIPO IMAGE (PADRAO ANTIGO) -->
									<template v-if="item.type === 'IMAGE'">
										<b style="text-transform: capitalize;">
											<span style="color:#B40404;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ $t(item.name) }}:</span>
										</b>
										<img style="max-width: 40%" :src="getFile(item.id, JSON.parse(item.content).unique_name)"/>
										<br>
									</template>
								</span>
								<!-- FALA CLIENTE -->
								<span v-else>
									<!-- TIPO TEXT -->
									<template v-if="item.type === 'TEXT'">
										<b style="text-transform: capitalize;">
											<span style="color:#0B0B61;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ $t(item.name) }}:</span>
										</b>
										<div class="output ql-snow">
											<div class="ql-editor" v-viewer v-html="item.content"></div>
										</div>
									</template>
									<!-- TIPO EVENT -->
										<template v-if="item.type === 'EVENT'">
										<center>
											<span v-linkified>
												{{ translate(item.content) }}<br>
											</span>
										</center>
									</template>
									<template v-if="item.type === 'IMAGE'">
										<b style="text-transform: capitalize;">
											<span style="color:#0B0B61;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ $t(item.name) }}:</span>
										</b>
										<img style="max-width: 40%" :src="getFile(item.id, JSON.parse(item.content).unique_name)"/>
									</template>
								</span>
						</span>
						<span v-for="(item, index) in chat_history.slice().reverse()" :key="index+'bb'">
							<!-- FALA FUNCIONARIO -->
							<span v-if="user.id == item.created_by">
								<template v-if="item.type === 'TEXT'">
									<b style="text-transform: capitalize;">
										<span style="color:#B40404;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ $t(item.name) }}:</span>
									</b>
									<div class="output ql-snow">
										<div class="ql-editor" v-viewer v-html="item.content"></div>
									</div>
								</template>
								<template v-if="item.type === 'FILE'">
									<b style="text-transform: capitalize;">
										<span style="color:#B40404;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ $t(item.name) }}:</span>
									</b>
									<span v-linkified>
										<span style="white-space: pre-line;">{{ translate(item.content.message) }}</span>
										<br>
										<span v-for="(image, index) in item.content.files" :key="index+'bb'">
											<b-link variant="secondary" :href="getFile(item.id, image.unique_name)" target="_blank">
												<b-button variant="primary"> {{image.original_name}}</b-button>
											</b-link>
										</span><br>
									</span>
								</template>
								<template v-if="item.type === 'EVENT'">
									<center>
										<span>
											{{ translate(item.content) }}<br>
										</span>
									</center>
								</template>
							</span>
							<!-- FALA CLIENTE -->
							<span v-else>
								<template v-if="item.type === 'TEXT'">
									<b style="text-transform: capitalize;">
										<span style="color:#0B0B61;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ $t(item.name) }}:</span>
									</b>
									<div class="output ql-snow">
										<div class="ql-editor" v-viewer v-html="item.content"></div>
									</div>
								</template>
								<template v-if="item.type === 'FILE'">
									<b style="text-transform: capitalize;">
										<span style="color:#0B0B61;" :data-tooltip="UTCtoClientTZ2( item.created_at)">{{ $t(item.name) }}:</span>
									</b>
									<span v-linkified>
										<span style="white-space: pre-line;">
											<div class="output ql-snow">
												<div class="ql-editor" v-viewer v-html="translate(item.content.message)"></div>
											</div>
										</span>
										<br>
										<span v-for="(image, index) in item.content.files" :key="index+'cc'">
											<b-link variant="secondary" :href="getFile(item.id, image.unique_name)" target="_blank">
												<b-button variant="primary"> {{image.original_name}}</b-button>
											</b-link><br>
										</span>
									</span>
								</template>
								<template v-if="item.type === 'EVENT'">
									<center>
										<span>
											{{ translate(item.content) }}<br>
										</span>
									</center>
								</template>
							</span>
						</span>
					</b-col>
				</b-row>
			</div>

			<hr style="width:100%;text-align:left;margin-left:0">

			<div class="pl-3 pr-3 scroll">
				<label>
					<b>{{ $t("bs-departments") }}</b>
				</label>

				<b-form-group v-slot="{ ariaDescribedby }">
					<b-form-checkbox-group id="checkbox-group-2"
						:aria-describedby="ariaDescribedby" name="flavour-2">
						<b-form-checkbox >
							marlos
						</b-form-checkbox>
					</b-form-checkbox-group>
				</b-form-group>
			</div>

			<hr style="width:100%;text-align:left;margin-left:0">

			<!-- <div class="pl-3 pr-3">
				<filter-all :is_admin="is_admin" :session_user="user"
					:session_user_departments="user_departments_id" :session_user_cucd="session_user_cucd"
					:session_user_company="cs" :session_user_permissions="restriction"></filter-all>
			</div> -->

		</div>
	</div>
</template>

<script>

export default {
	data(){
		return {
			itemselected: [],
			searchQuery: '',
			search: [],
			selected: 'category', // Must be an array reference!
			options: [
				{ text: 'Categoria', value: 'category' },
				{ text: 'Descrição', value: 'description' },
			],
			showDescription: false,
			TicketorChat: [],
			chat_history: [],
			chat_history_before: [],
			tz: '',
		}
	},
	props:{
		type: String,
        user: Object
	},
	mounted(){
		this.filterCategory();
		this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
	},
    methods: {
		filterCategory(){
			var vm = this;
			axios.get("tickets/get-database", {
				params: {
					search: vm.search,
					type: vm.type
				}
			}).then(function (r_resposta) {
				vm.TicketorChat = r_resposta.data.result;
			}).catch(function (error) {
				console.log(error);
			});
		},
		getChatHistory(item){
			this.itemselected = item;
			var vm = this;
			axios.get("tickets/get-chat-history", {
				params: {
					itemselected: item,
					type: vm.type
				}
			}).then(function (r_resposta) {

				vm.chat_history_before = r_resposta.data.result;
				vm.chat_history = r_resposta.data.result2;

				console.log(vm.chat_history_before)
				console.log(vm.chat_history)


				vm.open();
			}).catch(function (error) {
				console.log(error);
			});
		},
		open(){
			this.showDescription = !this.showDescription;
		},
		addSearch(){
			// this.selected.forEach(value => {
			// 	if(value == 'category'){
			// 		this.search.push([this.searchQuery, 'category']);
			// 	}
			// 	if(value == 'description'){
			// 		this.search.push([this.searchQuery, 'description']);
			// 	}
			// });
			this.search.push([this.searchQuery, this.selected]);
			this.searchQuery = '';
		},
        closeModal(){
			this.$root.$refs.FullTicket2.showDatabase = false;
		},
		getFile(chat_id, unique_name) {
            return `chat/files/${chat_id}/${unique_name}`;
        },
		translate: function (value) {
            if (!value) return ''
            else if(/^bs-/.test(value)) return  this.$t(value)
            return value
        },
		UTCtoClientTZ2(value = null){
			if(value === null) {
				return ''
			} else {
				let h_format = moment(value, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD HH:mm:ss");
				let datetime = h_format.split(" ");
				let date = datetime[0];
				let time = datetime[1];
				let date_split = date.split("-");
				let time_split = time.split(":");
				let year = date_split[0];
				let month = date_split[1];
				let day = date_split[2];
				let hour = time_split[0];
				let minute = time_split[1];
				let second = time_split[2];
				var month_integer = parseInt(month, 10);
				if (month_integer >= 1) {
					month_integer--;
				}
				let dateUTC = new Date(Date.UTC(year, month_integer, day, hour, minute, second));
				let converted_time = dateUTC.toLocaleString("pt-BR", {
					timeZone: this.tz,
				});

				var mt = require("moment-timezone");
				return mt(converted_time, "DD/MM/YYYY HH:mm:ss")
					.tz(this.tz)
					.locale(this.user.language)
					.format('L LTS');
			}
		},
    },
};
</script>

<style scoped>

	.bordateste{
		border: 1px solid red;
	}
	.bordateste1{
		border: 2px solid green;
		flex-wrap: inherit;
		overflow-y: scroll;
		max-height: 100%;
		bottom: 0;
	}
	.bordateste2{
		border: 1px solid blue;
	}
	.bordateste3{
		border: 1px solid blueviolet;
	}

	.scrollcust{
		overflow-y: scroll;
		/* height: 100vh; */
	}
	.fa-2x{
		font-size: 18px !important;
	}
	.fa-2x:hover{
		color: blue;
	}

	.modal-custom {
		margin-top: 2.3000000000000007rem;
		/* position: fixed !important; */
		/* top: 0 !important; */
		/* bottom: 0 !important; */
		width: 46vw !important;
		right: 0 !important;
		/* border-radius: 0px !important; */
		z-index: 9999;
		/* background-color: white; */
		/* box-shadow: 2px 2px 2px 2px rgb(119 119 119 / 83%); */
		position: fixed;
		/* width: 100%; */
		/* overflow: hidden; */
	}

	.borderdescription{
		box-shadow: 2px 2px 2px 2px rgb(119 119 119 / 83%);
	}

	.caret {
    	cursor: pointer;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
	}

	#quill-editor {
    word-break: break-word !important;
}

	.ba-hd__title {
		color: #0080fc;
		font-family: Muli;
		font-weight: 800;
		font-size: 1.4rem;
		letter-spacing: 0px;
		color: #0080fc;
		width: 0px;
	}


/* 
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
	font: normal normal bold 20px/26px Muli;
	letter-spacing: 0px;
	color: #434343;
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

	#btn-new-chat {
		max-width: 140px;
		padding-left: 6px;
		padding-right: 6px;
	}


} */

</style>