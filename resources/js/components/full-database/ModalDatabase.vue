<template>
	<div class="ba-hd__right-sidebar2 modal-custom"
	:class="{
		'is_mobile': isMobile,
	}">
		<div class="ba-hd__card-content flexcustom">
		<header class="header pr-2 pl-2">
			<div style="display: grid; grid-template: auto / auto  auto;">
				<div class="text-left d-table">
					<b class="d-table-cell align-middle">{{ $t('bs-filter')+' '+$t('bs-categorize')}}</b>
				</div>
				<div @click="closeModal" class="text-right pt-1">
					<span class="bs-ico cursor-pointer">&#xe5cd;</span>
				</div>
			</div>
		</header>
		
		<div class="form-group header">
			<div class="form-outline">
				<input
					type="search"
					ref="searchf"
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
			</div>
			<span v-for="(item, index) in search" :key="index+'ee'" :value="item.value">
				<b-badge class="caret" :variant="item[0] == 'category' ? 'primary' : 'warning'" @click="removeFilter(item)">X {{ item[1] }}</b-badge>
			</span>
			<b-alert v-model="showDismissibleAlert" variant="danger" dismissible>
				<center>{{$t('bs-please-enter-the-information')}} - {{$t('bs-invalid-input')}}</center>
			</b-alert>
			<center v-if="TicketorChat.length == 0">
				{{$t('bs-no-results-found')}}
			</center>
		</div>
	
		<div v-if="TicketorChat.length != 0" class="content scrollcust">
			<b-row>
				<b-col cols="auto">
					<div class="list-c">
						<table class="table table-responsive table-hover table-show-empty">
							<thead v-if="!TicketorChat">
								<tr>
								<th scope="col">#</th>
								<th v-if="!showDescription" scope="col">{{$t('bs-description')}}</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item, index) in TicketorChat" :key="index" :value="item.value" >
									<th :class="item.class" scope="row" @click="getChatHistory(item)">{{type == 'CHAT'? item.chat_id: item.ticket_id}}</th>
									<td v-if="!showDescription" class="caret" @click="getChatHistory(item)">
										<span class="caret" v-if="type == 'TICKET'">
											<div class="output ql-snow">
												<div class="ql-editor pt-0 pl-0" v-viewer v-html="`<b>${$t('bs-description')}:</b> ${item.description}`"></div>
											</div>
											<p><b-badge variant="secondary"></b-badge></p>
										</span>
										<span v-else>
											<span class="fl" v-if="checkJson(item) != false">
												<b>{{$t('bs-message')}}: {{$t('bs-image')}}</b>
											</span>
											<span v-else>
												<div class="output ql-snow">
													<div class="ql-editor pt-0 pl-0" v-viewer v-html="`<b>${$t('bs-message')}:</b> ${$t(item.content)}`"></div>
												</div>
											</span>
										</span>
									</td>
									<td v-if="!showDescription" @click="isFavorite(item, index)">
										<i v-if="item.company_user_id != null" class="fa fa-star" style="color:#FACC2E;"  aria-hidden="true"></i>
										<i v-else class="fa fa-star-o" style="color:#FACC2E;" aria-hidden="true"></i>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</b-col>
				<b-col v-if="showDescription">			
					<div class="shadowdescription">
						<template v-if="type == 'TICKET'">
							<header class="header">
								<div style="display: grid; grid-template: auto / auto  auto;">
									<div class="text-left d-table">
										<b class="d-table-cell align-middle">{{ $t('bs-description') }}:</b>
									</div>
									<div @click="closeChat" class="text-right pt-1">
										<span class="bs-ico cursor-pointer">&#xe5cd;</span>
									</div>
								</div>
							</header>
							<div class="output ql-snow">
								<div class="ql-editor pt-0 pl-0 hovercopy" v-viewer v-html="`${itemselected.description}`" 
                        			@click="copyToClipboard(itemselected.description, 'description')" :id="`desc-${_uid}`"></div>
							</div>
						</template>
						<template v-else>
							<header class="header">
								<div style="display: grid; grid-template: auto / auto  auto;">
									<div class="text-left d-table">
										<b class="d-table-cell align-middle">{{ itemselected.chat_id }}:</b>
									</div>
									<div @click="closeChat" class="text-right pt-1">
										<span class="bs-ico cursor-pointer">&#xe5cd;</span>
									</div>
								</div>
							</header>
						</template>
					
				
					<!-- MODAL QUESTIONARY GENERIC-->
					<div v-for="(row, index) in questions" :key="index">
						<div
							class="grid-container2"
						>
							<div class="item3 pr-1" v-linkified>
								{{ $t(row.question) }}
							</div>
						</div>
				
						<div class="output ql-snow pa-0" v-if="isRichText(row.answer)" v-linkified>
							<div class="ql-editor pa-0" v-html="row.answer"></div>
						</div>
						<div class="item3 pr-1" v-else v-linkified>
							{{ row.answer }}
						</div>

					</div>
					<!-- MODAL QUESTIONARY GENERIC-->

					<span class="card-body" v-for="(item, index) in chat_history_before" :key="index+'axa'">
						<span v-if="item.type == 'TEXT'">
							<message-type-text 
							:comp_user_comp_depart_id_current="item.company_user_company_department_id"
							:message="$t(item)" 
							:formatTime="formatTime"></message-type-text>
						</span>
						<span v-if="item.type == 'EVENT'">
							<message-type-event
							:message="$t(item)" 
							:formatTime="formatTime"></message-type-event>
						</span>
						<span v-if="item.type == 'OPEN'">
							<message-type-open-agent
							:message="$t(item)" 
							:formatTime="formatTime"></message-type-open-agent>
						</span>
						<span v-if="item.type == 'IMAGE'">
							<message-type-image-agent
							:message="$t(item)" 
							:formatTime="formatTime"></message-type-image-agent>
						</span>
						<span v-if="item.type == 'FILE'">
							<message-type-file-agent
							:message="$t(item)" 
							:formatTime="formatTime"></message-type-file-agent>
						</span>
					</span>
					</div>
				</b-col>
			</b-row>
			<!-- copy -->
			<div class="hidden-input">
				<textarea :ref="`input-${_uid}`"></textarea>
			</div>
		</div>
		</div>
	</div>
</template>

<script>

export default {
	data(){
		return {
			isMobile: false,
			itemselected: [],
			searchQuery: '',
			search: [],
			selected: 'category', // Must be an array reference!
			options: [
				{ text: 'Categoria', value: 'category' },
				// { text: 'Descrição', value: 'description' },
			],
			showDescription: false,
			TicketorChat: [],
			chat_history: [],
			chat_history_before: [],
			tz: '',
			showDismissibleAlert: false,
			tooltips: {
                desc: false,
                text: false,
            },
		}
	},
	props:{
		type: String,
        user: Object,
		chat: Object,
	},
	created(){
		window.addEventListener("resize", this.onResize);
	},
	mounted(){
		this.$refs["searchf"].focus();
		this.filterCategory();
		this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
		this.search = JSON.parse(localStorage.getItem("s_db_t"));
		if(this.search != null){
			if(this.search.length != 0){
				this.filterCategory();
			}
		}else{
			this.search = [];
		}
		
		this.onResize();
	},
    methods: {
		isRichText(str) {
			let tag         = str.slice(0, 1) == '<'    && str.slice(-1) == '>';
			let paragraph   = str.slice(0, 2) == '<p'   || str.slice(-4) == '</p>';
			let list        = str.slice(0, 3) == '<ul'  || str.slice(-5) == '</ul>';
			let code        = str.slice(0, 4) == '<pre' || str.slice(-6) == '</pre>';
			
			return tag && (paragraph || list || code);
		},
		isFavorite(item, index){
			axios.post('tickets/set-favorite',{
				chat_id: item.chat_id
			})
			.then(({data}) => {
				if(data.success) {
					item.company_user_id = data.value;
				}
			})
			.catch(err => {
				console.error(err);
				this.$loading(false);
			})
		},
		onResize(e) {
            if ($(window).width() <= 992) {
                this.isMobile = true;
            } else {
                this.isMobile = false;
            }
        },
		checkJson(item){
			try {
				JSON.parse(item.content);
			} catch (e) {
				return false;
			}
		},
		copyToClipboard: function (text, modalFlag, index = null) {
			// REMOVE TAGS
			var div = document.createElement("div");
			div.innerHTML = text;
			text = div.innerText;

			const elem = this.$refs[`input-${this._uid}`];
			elem.value = text;
			elem.select();
			document.execCommand("copy");
			elem.value = "";
			this.openTooltip(modalFlag, index);
		},
		openTooltip(modalFlag, index = null) {
			if (index != null) {
				this.$set(this.tooltips[modalFlag], index, true);
			} else {
				this.tooltips[modalFlag] = true;
			}
			setTimeout(this.closeTootip, 2000, this, modalFlag, index);
		},
		closeTootip(vm, modalFlag, index = null) {
            if (index != null) {
                this.$set(vm.tooltips[modalFlag], index, false);
            } else {
                vm.tooltips[modalFlag] = false;
            }
        },
		removeFilter(itemselect){
			let index = this.search.findIndex(
				(item) => item[1] === itemselect[1]
			);
			if (index !== -1) {
				this.search.splice(index, 1);
				localStorage.setItem("s_db_t", JSON.stringify(this.search));
				this.filterCategory();
			}
		},
		filterCategory(){
			var vm = this;
			if(vm.search.length != 0){
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
			}
		},
		getChatHistory(item){
			var vm = this;

			// DEMARCAR 
			vm.TicketorChat.forEach(element => {
				element.class = '';
			});
			// FECHAR ABA
			if(item == vm.itemselected){
				return vm.open();
			}
			// SELECIONA
			item.class = 'table-primary';
			vm.itemselected = item;
			
			//PEGA CHAT_HISTORY
			axios.get("tickets/get-chat-history", {
				params: {
					itemselected: item,
					type: vm.type
				}
			}).then(function (r_resposta) {
				vm.chat_history_before = r_resposta.data.result;
				vm.chat_history = r_resposta.data.result2;
				vm.questions = r_resposta.data.quests;
				vm.showDescription = true;
			}).catch(function (error) {
				console.log(error);
			});
		},
		open(){
			this.showDescription = !this.showDescription;
		},
		addSearch(){
			var vm = this;
			if(vm.search != null){
				vm.search.forEach(item => {
					console.log(item);
					if(item == vm.searchQuery){
						vm.searchQuery = '';
					}
				});
			}
			if(vm.searchQuery == ''){
				vm.showDismissibleAlert = true;
			}else{
				vm.showDismissibleAlert = false;
				vm.search.push([vm.selected, vm.searchQuery]);
				localStorage.setItem("s_db_t", JSON.stringify(vm.search));
				vm.searchQuery = '';
				vm.filterCategory();
			}
		},
        closeModal(){
			if(this.type == 'TICKET') {
				this.$root.$refs.FullTicket2.showDatabase = false;
			}else{
				this.$root.$refs.FullChat2.showDatabase = false;
			}
		},
		closeChat(){
			this.showDescription = false;
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
		formatTime(h) {
            let h_format = moment(h, "YYYY-MM-DD HH:mm:ss").format(
                "YYYY-MM-DD HH:mm:ss"
            );
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
            let dateUTC = new Date(Date.UTC(year, month, day, hour, minute, second));
            //pega o fuso do cliente. Ex: "America/Sao_Paulo"
            let client_tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
            let converted_time = dateUTC.toLocaleString("pt-BR", {
                timeZone: client_tz,
            });
            return moment(converted_time, "YYYY-MM-DD HH:mm:ss").format(
                "HH:mm YYYY-MM-DD"
            );
        },
    },
};
</script>

<style scoped>
	.fl{
		float: left;
	}
	.hidden-input {
		width: 1px;
		height: 1px;
		max-width: 1px;
		max-height: 1px;
		overflow: hidden;
		opacity: 0;
	}

	.hovercopy:hover{
		background-color: rgb(216, 216, 216);
		cursor: pointer;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
	}

	.flexcustom{
		display: flex;
		flex-direction: column;
	}

	.header{
		flex-shrink: 1;
	}
	.content{
		flex-grow: 1;
	}

	.scrollcust{
		overflow-y: scroll;
		height: 100%;
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
		z-index: 99999999 !important; 
		/* background-color: white; */
		/* box-shadow: 2px 2px 2px 2px rgb(119 119 119 / 83%); */
		position: fixed;
		/* width: 100%; */
		/* overflow: hidden; */
	}

	.shadowdescription{
		/* box-shadow: 0px 0px 8px 1px rgb(119 119 119 / 84%); */
		margin-left: -1.4rem;
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

	.is_mobile {
		width: 100% !important;
	}


		/* MODAL QUESTIONARY */
	.grid-container2 .item1 {
		grid-area: name;
		color: #0080fc;
		font-size: 15px;
		font-stretch: 100%;
		font-weight: 800;
		text-rendering: optimizeLegibility;
		line-height: 22px;
		padding-left: 5px;
	}

	.grid-container2 .item2 {
		grid-area: gravatar;
		display: flex;
		align-items: initial;
		justify-content: center;
		padding-top: 8px;
	}

	.grid-container2 .item3 {
		grid-area: content;
		color: #707070;
		font-size: 0.9rem;
		font-stretch: 100%;
		font-weight: 600;
		text-rendering: optimizeLegibility;
		-webkit-font-feature-settings: "kern" 1;
		line-height: 19px;
		padding-bottom: 5px;
		padding-right: 5px;
		padding-left: 5px;
	}
	.grid-container2 .item4 {
		grid-area: time;
		text-align: right;
		color: #6e6e6e;
		opacity: 1;
		font-size: 11px;
		line-height: 20px;
		text-rendering: optimizeLegibility;
		font-weight: 700;
		padding-right: 5px;
	}

	.grid-container2 {
		display: grid;
		grid-template-areas:
			"gravatar name time"
			"gravatar content content";
		grid-template-columns: 45px auto 60px;
		border-top: 1px solid rgba(215, 222, 230, 0.2);
	}
</style>