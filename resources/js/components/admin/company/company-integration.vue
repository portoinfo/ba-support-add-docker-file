<template>
<div>
    <b-container fluid="lg">
        <b-row>
            <b-col cols="auto" class="mr-auto p-3 bs-title">{{$t('bs-released-domains')}}
                <b-card-text class="bs-subtitle">
                    {{$t('bs-domains-released-for-system-access')}}.
                </b-card-text>
            </b-col>
            <b-col cols="auto mt-4 mb-3">
                <b-button href="company" variant="light bs-btn-back">{{$t('bs-back')}}</b-button>
            </b-col>
        </b-row>
        <b-alert
            fade
            show variant="success"
            class="bui-alert"
        >
            <slot>
            <h5>
                <!-- {{$t('bs-released-domains')}} -->
                <!-- <span v-if="showLimite">
                   {{$t('bs-you-reached-your-released-domain-limit')}}
                </span> -->
            </h5>
            <p>
                {{$t('bs-to-use-the-system-you-must-define-which')}} <br>
            </p>
            </slot>
            <!-- <div v-if="showLimite" class="d-flex justify-content-end mt-n1 flex-md-row flex-column">
                {{$t('bs-to-purchase-shares')}} &nbsp
                <b-link href="#comprar-mais-cotas" style="color: white;text-decoration: underline;">{{$t('bs-click-here')}}</b-link>
            </div> -->
        </b-alert>
        <b-row>
            <!-- <b-col>
                <div class="cardcota" >
                    <b-row>
                        <b-col class="textcota" style="color:black;"><b>{{$t('bs-cota')}}</b></b-col>
                        <b-col class="">
                            <b-button @click="showModal" size="sm" variant="primary" style="font-size:10px;">
                                <i class="fa fa-plus-circle" aria-hidden="true"></i> {{$t('bs-add-quota')}}
                            </b-button>
                        </b-col>
                    </b-row>
                    <b-row class="mt-3 textcota">
                        <b-col>{{$t('bs-available')}}: {{limitCota-items.length}}</b-col>
                        <b-col>{{$t('bs-used')}}: {{items.length}}</b-col>
                    </b-row>
                </div>
            </b-col> -->
            <b-col cols="auto" >
                 <b-form @submit="saveDomain" inline class="mt-4">
                    <b-form-group>
                    <span id="tooltip-informe-domain">
                        {{textInformDomain}} &nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
                    </span><br>
                    <b-tooltip target="tooltip-informe-domain" triggers="hover" placement="right" variant="secondary">
                        {{$t('bs-informe-domain-description')}}
                    </b-tooltip>
                    <b-form-input
                    id="inline-form-input-name"
                    class="inputstyle mr-sm-2 mb-sm-0 mt-1"
                    placeholder="builderall.com"
                    v-model="link"
                    ></b-form-input>

                    <b-button class="mt-1" type="submit" variant="primary"  style="font-size:10px;"><i class="fa fa-plus-circle" aria-hidden="true"></i> {{$t('bs-add')}}</b-button>
                    </b-form-group>
                </b-form>
            </b-col>
        </b-row>
        <br>
        <div>
            <b-table
                :bordered="bordered"
                :small="small"
                :items="items"
                :fields="fields"
                :head-variant="headVariant"
                :table-variant="tableVariant"
                show-empty
            >
                <template #cell(domain)="row">
                   <img
                        :src="`https://www.google.com/s2/favicons?domain=${row.item.domain}`"
                        width="20"
                        height="20"
                      />
                   <span style="textlink" class="ml-1"> {{row.item.domain}}</span>
                </template>
                <template #cell(actions)="row">
                    <b-link size="lg" @click="itemDelete(row.item, row.index)">
                        <center><i class="fa fa-trash-o bs-trash fa-2x" aria-hidden="true"></i></center>
                    </b-link>
                </template>
                <template #empty="scope">
					<div class="text-center">{{$t('bs-no-domain-registered')}}. </div>
				</template>
            </b-table>
        </div>
        <br>
    </b-container>
<!------------------------------------------------------------Interação Externa ------------------------------------------------------------>
    <b-container fluid="lg">
        <b-row>
            <b-col cols="auto" class="mr-auto p-3 bs-title">{{$t('bs-integration-external')}}
                <b-card-text class="bs-subtitle">
                    {{$t('bs-integration-between-the-system-and')}}
                </b-card-text>
            </b-col>
            <b-col cols="auto mt-4">
                <!-- <b-button href="company" variant="light bs-btn-back">{{$t('bs-back')}}</b-button> -->
            </b-col>
        </b-row>

        <b-alert
            fade
            show variant="success"
            class="bui-alert"
        >
            <slot>
            <p>
                {{$t('bs-integration-external-tip')}} <br>
            </p>
            </slot>
            <!-- <div v-if="showLimite" class="d-flex justify-content-end mt-n1 flex-md-row flex-column">
                {{$t('bs-to-purchase-shares')}} &nbsp
                <b-link href="#comprar-mais-cotas" style="color: white;text-decoration: underline;">{{$t('bs-click-here')}}</b-link>
            </div> -->
        </b-alert>
        <b-row  align-v="stretch">
            <div class="card bs-m-spacing" >
                <div>
                    <span class="texttitle">{{$t('bs-integration-with-customers')}}</span><br>
                    <span class="textsub">{{$t('bs-select-options-to-customize-the-script-the')}}</span>

                    <b-row class="mb-4 mt-2 p-1">
                        <b-col>
                            <span id="tooltip-general-module">
                                {{$t('bs-module')}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
                            </span>
                            <b-form-select @change="gerateLink" class="mt-2" v-model="selected" :options="optionsModule"></b-form-select>
                            <b-tooltip target="tooltip-general-module" triggers="hover" placement="right" variant="secondary">
                                {{$t('bs-tooltip-general-module-2')}}
                            </b-tooltip>
                        </b-col>
                        <b-col>
                            <span id="tooltip-type-chat">
                                {{$t('bs-chat-type')}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
                            </span>
                            <b-form-select @change="gerateLink" class="mt-2" v-model="selected2" :options="optionsChat"></b-form-select>
                            <b-tooltip target="tooltip-type-chat" triggers="hover" placement="right" variant="secondary">
                                {{$t('bs-tooltip-type-chat')}}
                            </b-tooltip>
                        </b-col>
                    </b-row>

                    <b-row>
                        <b-col>
                            <b-form-input
                                id="input-11"
                                v-model="cadastro"
                                type="text"
                                required
                                disabled
                            ></b-form-input>
                        </b-col>
                        <b-col style="" cols="auto">
                            <template v-if="Bcadastro">
                                <b-button class="pl-4 pr-4 pt-2 pb-2" @click="copyToClipboard(cadastro, 'Cadastro')" variant="primary">
                                    <i class="material-icons">content_copy</i> {{$t('bs-copy')}}
                                </b-button>
                            </template>
                            <template v-else>
                                <b-button class="pl-4 pr-4 pt-2 pb-2" @click="copyToClipboard(login, 'Cadastro')" variant="success">
                                    <i class="material-icons">done_all</i> {{$t('bs-copy')}}
                                </b-button>
                            </template>
                        </b-col>
                    </b-row>
                    <span class="textalert">{{$t('bs-the-tag-above-should-be-incorporated-into')}}</span>
                </div>
            </div>
        </b-row>


      <div
        class="modal fade"
        id="modalCota"
        tabindex="-1"
        aria-labelledby="modalCota"
        aria-hidden="true"
        data-backdrop="static"
        data-keyboard="false"
        >
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                            <h5 class="modal-title" id="exampleModalLabel">{{$t('bs-no-quota-available')}}.</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                X
                            </button>
                        </div>
                    <div class="border-0 p-0">
                    </div>
                    <br>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group textsubb">
                                    <center>
                                        <h4>
                                            {{$t('bs-you-used-all-your-free-domain-quota')}}. <br>
                                            {{$t('bs-to-add-a-domain-remove-a-domain-from-the')}}..
                                        </h4>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="text-capitalize btn" data-dismiss="modal">
                            {{$t('bs-back')}}
                        </button>
                        <button type="button" id="btn-department" class="btn btn-success">
                            {{$t('bs-acquire-quota')}}
                        </button>
                    </div>

                    <br>
                </div>
            </div>
        </div>
    </b-container>
<!------------------------------------------------------------Interação com Clientes ------------------------------------------------------------>
    <b-container fluid="lg">
        <b-row>
            <b-col cols="auto" class="mr-auto p-3 bs-title">{{$t('bs-interaction-with-customers')}}
                <b-card-text class="bs-subtitle">
                    {{$t('bs-configuration-for-interaction-customers')}}
                </b-card-text>
            </b-col>
            <b-col cols="auto mt-4">
                <!-- <b-button href="company" variant="light bs-btn-back">{{$t('bs-back')}}</b-button> -->
            </b-col>
        </b-row>

        <b-alert
            fade
            show variant="success"
            class="bui-alert"
        >
            <slot>
            <p>
                {{$t('bs-through-the-settings-below-you-can-define')}}. <br>
            </p>
            </slot>
            <!-- <div v-if="showLimite" class="d-flex justify-content-end mt-n1 flex-md-row flex-column">
                {{$t('bs-to-purchase-shares')}} &nbsp
                <b-link href="#comprar-mais-cotas" style="color: white;text-decoration: underline;">{{$t('bs-click-here')}}</b-link>
            </div> -->
        </b-alert>
        <b-row  align-v="stretch">
            <div class="card bs-m-spacing" >
                <b-row>
                    <b-col>
                        <b-form class="bs-label">
                            <b-form-group id="input-group-13" label-for="input-13" :label="linkLogout">
                                <b-form-input @change="saveFunctions" id="input-13" v-model="logout.link" type="text" class="bs-input" required
                                :placeholder="phLink">
                                </b-form-input>
                                <div class="justify_switch">
                                    <label class="switch" style="margin-top:5px;">
                                        <input type="checkbox" @change="saveFunctions" v-model="logout.active">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </b-form-group>
                        </b-form>
                    </b-col>
                    <b-col class="mt-4" cols="auto">
                        <template v-if="copiarLogout">
                            <b-button class="pl-4 pr-4 pt-2 pb-2" @click="copyToClipboard(logout.link, 'logout-link')" variant="primary">
                                <i class="material-icons">content_copy</i> {{$t('bs-copy')}}
                            </b-button>
                        </template>
                        <template v-else>
                            <b-button class="pl-4 pr-4 pt-2 pb-2" @click="copyToClipboard(logout.link, 'logout-link')" variant="success">
                                <i class="material-icons">done_all</i> {{$t('bs-copy')}}
                            </b-button>
                        </template>
                    </b-col>
                </b-row>
                <div>
                   <b-form class="bs-label">
                        <b-form-group id="input-group-14" label-for="input-14" label="Acesso">
                            <b-form-input disabled="disabled" id="input-15" value="Permitir acesso Anônimo" type="text" class="bs-input" required
                            :placeholder="phLink">
                            </b-form-input>
                            <div class="justify_switch">
                                <label class="switch" style="margin-top:5px;">
                                    <input type="checkbox" @change="saveFunctions" v-model="acesso_anonymous">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                            <span class="textsub">{{$t('bs-in-anonymous-access-the-customer-will-not')}}.</span>
                        </b-form-group>
                    </b-form>
                </div>
                <div>
                   <b-form class="bs-label">
                        <b-form-group id="input-group-16" label-for="input-16" :label="$t('bs-customer-profile')">
                            <b-form-input disabled="disabled" id="input-16" :value="$t('bs-activate-profile')+' '+$t('bs-client')" type="text" class="bs-input" required
                            :placeholder="phLink">
                            </b-form-input>
                            <div class="justify_switch">
                                <label class="switch" style="margin-top:5px;">
                                    <input type="checkbox" @change="saveFunctions" v-model="editPerfilClient">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                            <span class="textsub">{{$t('bs-if-activated-the-customer-can-edit-the-pr')}}</span>
                        </b-form-group>
                    </b-form>
                </div>
            </div>
        </b-row>
    </b-container>
<!------------------------------------------------------------Interação com Atendentes ------------------------------------------------------------>
    <b-container fluid="lg">
        <b-row>
            <b-col cols="auto" class="mr-auto p-3 bs-title">{{$t('bs-interaction-with-attendants')}}
                <b-card-text class="bs-subtitle">
                    {{$t('bs-configuration-for-interaction-attendants')}}
                </b-card-text>
            </b-col>
            <b-col cols="auto mt-4">
                <!-- <b-button href="company" variant="light bs-btn-back">{{$t('bs-back')}}</b-button> -->
            </b-col>
        </b-row>

        <b-alert
            fade
            show variant="success"
            class="bui-alert"
        >
            <slot>
            <p>
                {{$t('bs-bs-through-the-settings-below-you-can-def')}}. <br>
            </p>
            </slot>
            <!-- <div v-if="showLimite" class="d-flex justify-content-end mt-n1 flex-md-row flex-column">
                {{$t('bs-to-purchase-shares')}} &nbsp
                <b-link href="#comprar-mais-cotas" style="color: white;text-decoration: underline;">{{$t('bs-click-here')}}</b-link>
            </div> -->
        </b-alert>
        <b-row  align-v="stretch">
            <div class="card bs-m-spacing" >
                <div>
                    <span class="texttitle">{{$t('bs-attendant-access')}}</span><br>
                    <span class="textsub">{{$t('bs-for-your-attendants-to-login-use-the-link')}}.</span>
                    <b-row class="mt-2">
                        <b-col class="">
                            <b-form-input
                                id="input-33"
                                v-model="loginatendente"
                                type="text"
                                required
                                disabled
                            ></b-form-input>
                        </b-col>
                        <b-col class="" cols="auto">
                            <template v-if="Bloginatendente">
                                <b-button class="pl-4 pr-4 pt-2 pb-2" @click="copyToClipboard(loginatendente, 'Loginatendente')" variant="primary">
                                    <i class="material-icons">content_copy</i> {{$t('bs-copy')}}
                                </b-button>
                            </template>
                            <template v-else>
                                <b-button class="pl-4 pr-4 pt-2 pb-2" @click="copyToClipboard(login, 'Loginatendente')" variant="success">
                                    <i class="material-icons">done_all</i> {{$t('bs-copy')}}
                                </b-button>
                            </template>
                        </b-col>
                    </b-row>
                    <!-- <span class="textalert">{{$t('bs-the-link-above-can-be-used-as-a-reference')}}.</span> -->
                </div>
                <br>
                <div>
                   <b-form class="bs-label">
                        <b-form-group id="tooltip-perfil-atendente" label-for="input-12" :label="$t('bs-attendant-profile')">
                            <!-- <i class="fa fa-question-circle" aria-hidden="true"></i> -->
                            <b-form-input disabled="disabled" id="input-12" :value="$t('bs-activate-profile')+' '+$t('bs-attendant')" type="text" class="bs-input" required
                            :placeholder="phLink">
                            </b-form-input>
                            <div class="justify_switch">
                                <label class="switch" style="margin-top:5px;">
                                    <input type="checkbox" @change="saveFunctions" v-model="editPerfilAttendants">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                            <span class="textsub">{{$t('bs-if-activated-the-attendant-can-edit-the-pr')}}</span>
                            <b-tooltip target="tooltip-perfil-atendente" triggers="hover" placement="right" variant="secondary">
                                {{$t('bs-if-activated-the-attendant-can-edit-the-pr')}}
                            </b-tooltip>
                        </b-form-group>
                    </b-form>
                </div>
            </div>
        </b-row>
    </b-container>

    <!-- copy -->
    <div class="hidden-input">
        <textarea :ref="`input-${_uid}`"></textarea>
    </div>

</div>
</template>

<script>

export default {
    data(){
        return {
            textInformDomain: this.$t('bs-enter-a-domain') + ":",
            cadastro: this.$t('bs-register') + "!",
            Bcadastro: true,
            login: this.$t('bs-login') + "!",
            Blogin: true,
            loginatendente: this.$t('bs-attendant-login') + "!",
            Bloginatendente: true,
            copiarLogout: true,
            striped: true,
            bordered: true,
            borderless: true,
            outlined: true,
            small: false,
            hover: true,
            dark: true,
            fixed: true,
            footClone: true,
            headVariant: 'secondary',
            tableVariant: 'light',
            noCollapse: false,
            fields: [
                { key: 'domain', sortable: true, label: this.$t('bs-released-domains') },
                { key: 'actions', label: '' }
            ],
            items: [],
            link: '',
            limitCota: 5,
            showLimite: false,
            selected: 'all',
            selected2: 'pop-up',
	        optionsModule: [
	          { value: 'all', text: this.$t('bs-all') },
	          { value: 'chat', text: 'Chat' },
	          { value: 'ticket', text: 'Ticket' }
	        ],
            optionsChat: [
	          { value: 'pop-up', text: 'Popup' },
	          { value: 'system', text: 'System' }
	        ],
            logout: {
				link: '',
				active: false,
			},
            phLink: this.$t('bs-enter-link'),
            linkLogout: this.$t('bs-logout-redirect-link')+' ('+this.$t('bs-client')+')',
            acesso_anonymous: true,
            showTicket: false,
			showChat: false,
			editPerfilClient: true,
			editPerfilAttendants: true,
            chatSimCli: 0,
            chatDepartAutomaticNumberLimit: 0,
			ticketSimCli: 0,
            domainReleased: [],
			domainBlocked: [],
        }
    },
    props:{
        csid: String,
        usuario: Object,
        hash_code: String,
        base_url: {
            type: String,
            default: ''
        },
    },
    mounted(){
        var vm = this;

		var url = `${this.base_url}/company-config/domains-settings/${vm.csid}`;
		axios.get(url).then(function(r_resposta){
            // console.log(r_resposta.data);
            vm.domainReleased = r_resposta.data.released ? r_resposta.data.released : [];
			vm.domainBlocked = r_resposta.data.blocked ? r_resposta.data.blocked : [];
			vm.ticketSimCli = r_resposta.data.settings_ticket;
			vm.showChat = r_resposta.data.general.showChat;
			vm.showTicket = r_resposta.data.general.showTicket;
			vm.logout.link   = r_resposta.data.general.client_logout.link ? r_resposta.data.general.client_logout.link: '';
			vm.logout.active = r_resposta.data.general.client_logout.active ? r_resposta.data.general.client_logout.active: '';
			vm.editPerfilClient  = r_resposta.data.general.editPerfilClient;
			vm.editPerfilAttendants  = r_resposta.data.general.editPerfilAttendants;
			vm.acesso_anonymous  = r_resposta.data.general.acesso_anonymous;
           
            
            if(Array.isArray(r_resposta.data.settings_chat)){
				vm.chatSimCli = r_resposta.data.settings_chat[0].chatSimCli;
				vm.chatDepartAutomaticNumberLimit = r_resposta.data.settings_chat[1].chatDepartAutomaticNumberLimit;
			}else{
				vm.chatSimCli = r_resposta.data.settings_chat;
			}
            
		}).catch(function (error) {
			console.log(error);
		});

        axios.get('company-domains/'+vm.hash_code,{
        }).then(function(r_resposta){

            vm.items = r_resposta.data.data;

        }).catch(function (error) {
            console.log(error);
        });

        vm.getBaseUrl(vm.hash_code);

    },
    methods:{
        gerateLink(){
            var vm = this;
            vm.getBaseUrl(vm.hash_code);
        },
        showModal(){
            $("#modalCota").modal("show");
        },
        saveFunctions(){

            var vm = this;
			var url = `${this.base_url}/company-config/chat-ticket`;
			axios.post(url, {
                settings_chat: JSON.stringify([
					{ chatSimCli: vm.chatSimCli },
					{ chatDepartAutomaticNumberLimit: vm.chatDepartAutomaticNumberLimit },
				]),
				ticketSimCli: vm.ticketSimCli,
				csid: vm.csid,
				logout: vm.logout,
                showChat: vm.showChat,
				showTicket: vm.showTicket,
                acesso_anonymous: vm.acesso_anonymous,
                editPerfilClient: vm.editPerfilClient,
                editPerfilAttendants: vm.editPerfilAttendants,
			}).then(function(response){
				if(response.data.success){
                    vm.$snotify.success(vm.$t('bs-saved-successfully'), vm.$t('bs-success'), {
                    timeout: 1000,
                    showProgressBar: false,
                    // closeOnClick: false,
                    pauseOnHover: true
                    });
				}else{
					vm.$snotify.error( vm.$t('bs-error-trying-to-save') , vm.$t('bs-error'));
				}
			}).catch(function(){
				console.log('FAILURE!!');
			});
        },
        saveDomain(e){
            e.preventDefault();
            var vm = this;
            if(vm.link.trim() !== ""){
                // if(vm.limitCota-vm.items.length > 0){

                    //console.log(vm.csid);
                    axios.post('company-domains', {
                        domain: this.treatDomain(vm.link),
                        company: vm.hash_code,
                    })
                    .then(res => {

                        try {
                            if(res.response.data.error == "DomainNotValidException" || res.response.data.error == "DomainAlreadyRegisteredException" ){
                                vm.$snotify.error(vm.$t(res.response.data.translation+""), vm.$t('bs-error'));
                            }else if(res.response.data.errors.domain.length >= 1){
                                vm.$snotify.error(vm.$t(res.response.data.errors.domain[0]), vm.$t('bs-error'));
                            }
                        }
                        catch (e) {
                            var my_object = {
                                id: res.data.data.id,
                                domain: res.data.data.domain,
                            };
                            vm.$snotify.success(vm.$t('bs-saved-successfully'), vm.$t('bs-success'));
                            vm.items.push(my_object);
                            vm.link = "";
                            vm.showLimite = false;
                        }

                    })
                    .catch(err => {
                        console.log(err);
                    })

                // }else{
                //     vm.$snotify.error('Limite máximo de cota atigindo', vm.$t('bs-error'));
                //     vm.showLimite = true;
                // }
            }else{
                vm.$snotify.info(vm.$t('bs-empty-field'), vm.$t('bs-info'));
            }
        },
        itemDelete(item, index){

			Swal.fire({
				title: this.$t('bs-are-you-sure'),
				text: this.$t('bs-you-wont-be-able-to-revert-this'),
				icon: 'warning',
				showCancelButton: true,
				cancelButtonText: this.$t('bs-cancel'),
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: this.$t('bs-yes-delete-it'),
			}).then((result) => {
				if (result.isConfirmed) {

					var vm = this;

					//console.log(item);
					axios.delete('company-domains/'+item.id)
                    .then(function(response){



						if(response.data.success){

							vm.items.splice(index, 1);
							vm.$snotify.success(vm.$t('bs-successfully-deleted'), vm.$t('bs-success'));
							Swal.fire(
								vm.$t('bs-deleted'),
								vm.$t('bs-your-file-has-been-deleted'),
								'success'
							);
						}else{
							vm.$snotify.error(vm.$t('bs-error-while-deleting'), vm.$t('bs-error'));
						}
					})
					.catch(function(){
						console.log('FAILURE!!');
					});

				}
			});
		},
        copyToClipboard: function (text, modalFlag) {
			const elem = this.$refs[`input-${this._uid}`];
			elem.value = text;
			elem.select();
			document.execCommand('copy');

            this.$snotify.info(this.$t('bs-successfully-copied'), this.$t('bs-info'));

            if(modalFlag == 'Cadastro'){
                this.Bcadastro = false;
                const timeValue = setInterval((interval) => {
                    this.Bcadastro = true;
                    clearInterval(timeValue);
                }, 1000);

            }
            if(modalFlag == 'Login'){
                this.Blogin = false;
                const timeValue = setInterval((interval) => {
                    this.Blogin = true;
                    clearInterval(timeValue);
                }, 1000);
            }
            if(modalFlag == 'Loginatendente'){
                this.Bloginatendente = false;
                const timeValue = setInterval((interval) => {
                    this.Bloginatendente = true;
                    clearInterval(timeValue);
                }, 1000);
            }
            if(modalFlag == 'logout-link'){
                this.copiarLogout = false;
                const timeValue = setInterval((interval) => {
                    this.copiarLogout = true;
                    clearInterval(timeValue);
                }, 1000);
            }

		},
        getBaseUrl(hash_code){
		    var path = 'client';
	        var login = 'login';

            this.cadastro       = `<script id="ba-helpdesk__script" src="${process.env.MIX_INTEGRATION_SCRIPT_URL + '?v=' + Math.random()}" hc="${hash_code}" module="${this.selected}" chat-type="${this.selected2}"><\/script>`;
            this.login          = process.env.MIX_APP_URL + "/" + path + "/" + hash_code  + "/" +login;
            this.loginatendente = process.env.MIX_APP_URL + "/" +login;
        },
        treatDomain(domain){
            try {
                let url = new URL(domain);
                return url.host;
            } catch (e) {
                return domain;
            }
        }
    }
};
</script>

<style lang="scss" scoped>

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
.justify_switch{
	text-align: right;
	margin-top: -38px;
	margin-right: 4px;
}

.cardcota{
    top: 371px;
    left: 358px;
    width: 307px;
    height: 110px;
    background: #FFFFFF 0% 0% no-repeat padding-box;
    border: 1px solid #DEE3EA;
    opacity: 1;
    padding: 20px;

}

.textlink{
    text-align: left;
    font: normal normal 600 16px/19px Lato;
    letter-spacing: 0px;
    color: #6E6E6E;
}
.textcota{
    text-align: left;
    font: normal normal 600 18px/24px Lato;
    letter-spacing: 0px;
    color: #6E6E6E;
    opacity: 1;
}

.bui-alert {
  background: url(/images/meta/corner.png) left top no-repeat,
    url(/images/meta/wave.png) right bottom/100% no-repeat,
    transparent linear-gradient(180deg, #5e81f4 0%, #1665d8 100%);
  border-radius: 16px;
  border: none;
  padding-top: 1.2rem;
  padding-left: 2rem;
  color: #fff;
  p {
    font-size: 0.85rem;
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.8);
  }
  .btn {
    background: #ffffff38;
    border: unset;
    font-weight: normal !important;
    text-transform: capitalize;
    border-radius: 8px;
  }
}


.hidden-input{
    width: 1px;
    height: 1px;
    max-width: 1px;
    max-height: 1px;
    overflow: hidden;
    opacity: 0;
}

.coppy{
    background-color: #4D90FD;
}

.texttitle{
    text-align: left;
    font: normal normal bold 14px/20px Muli;
    letter-spacing: 0px;
    color: #707070;
    opacity: 1;
}

.textsub{
    text-align: left;
    font: normal normal 600 14px/20px Muli;
    letter-spacing: 0px;
    color: #707070;
    opacity: 0.75;
}

.textalert{
    text-align: left;
    font: normal normal 600 14px/20px Muli;
    letter-spacing: 0px;
    color: #707070;
    opacity: 0.75;
}


.inputstyle{
    min-width: 500px;
    margin-bottom: 50px;
    margin-right: 50px;
}

.bs-m-spacing{
	padding: 41px;
	margin-left: 1px;
}

.card{
    width: 1449px;
    background: #FFFFFF 0% 0% no-repeat padding-box;
    border-radius: 4px;
    opacity: 1;
    border: none;
}

@media screen and (max-width: 576px) {

    .inputstyle{
        min-width: 100%;
        margin-top: -20px;
        margin-bottom: 10px;
    }

    .bs-m-spacing{
        padding: 11px;
        margin-left: 1px;
    }

    .card{
        width: 1449px;
        background: #FFFFFF 0% 0% no-repeat padding-box;
        border-radius: 4px;
        opacity: 1;
        border: none;
    }
}

@media screen and (max-width: 1024px) {

    .inputstyle{
        min-width: 300px;
        margin-bottom: 10px;
    }

    .bs-m-spacing{
        padding: 21px;
        margin-left: 1px;
    }

    .card{
        width: 1449px;
        background: #FFFFFF 0% 0% no-repeat padding-box;
        border-radius: 4px;
        opacity: 1;
        border: none;
    }
}
// -------------------------------------------------- TUDO MODAL AQUI PRA BAIXO

.textsubb {
    font: normal normal bold 14px/20px Muli;
    letter-spacing: 0px;
    opacity: 1;
    background-color: white;
    padding: 20px;
    border: 1px solid #a4a4a4;
}

h2{
	font: normal normal bold 18px/20px Muli;
	letter-spacing: 0px;
	color: #434343;
}

h4{
	font: normal normal bold 14px/20px Muli;
	letter-spacing: 0px;
	color: #434343;
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

    //REFERENCE - https://ayltoninacio.com.br/blog/como-colocar-popup-aviso-cookies-privacidade-sem-plugins
}
</style>
