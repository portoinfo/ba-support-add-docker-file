<template>
    <b-container fluid="lg">
        <b-row>
            <b-col cols="auto" class="mr-auto p-3 bs-title">{{$t('bs-customer-listing')}}
                <b-card-text class="bs-subtitle">
                    {{$t('bs-quickly-manage-your-companys-customers')}}
                </b-card-text>
            </b-col>
            <b-col cols="auto mb-3">
                <b-row>
                    <b-col cols="auto" class="mr-auto p-3 bs-title">
                        <b-card-text class="bs-subtitle">
                        </b-card-text>
                    </b-col>
                    <b-col cols="auto mt-4">
                        <b-row>
                            <b-col>
                                <b-form-select v-model="selected" :options="options" @change="selectStatus"></b-form-select>
                            </b-col>
                            <b-col sm="auto">
                                <b-form-input  variant="light" class="bs-input icon" v-on:keyup="searchCLient" v-model="filtername" :placeholder="phBuscar"></b-form-input>
                                <i class="fa fa-search iconButton" aria-hidden="true"></i>
                            </b-col>
                        </b-row>
                    </b-col>
                </b-row>
            </b-col>
        </b-row>
        <br>
        <template>
        <div class="scroll">
            <b-table
                borderless
                hover
                outlined
                :items="items"
                :fields="fields"
                table-variant="light"
                show-empty
            >
            <template #cell(name)="row">
                {{$t(row.item.name)}}
            </template>
            <template #cell(email)="row">
                {{row.item.email}}
            </template>
            <template #cell(status)="row">
                <span v-if="row.item.status">
                    <!-- <b-button variant="success" size="sm">{{$t('bs-released')}}</b-button> -->
                    <b-badge class="caret" @click="reditect('group')" variant="success">{{$t('bs-released')}}</b-badge>
                </span>
                <span v-else>
                    <!-- <b-button variant="danger" size="sm">{{$t('bs-blocked')}}</b-button> -->
                    <b-badge class="caret" @click="reditect('group')" variant="danger">{{$t('bs-blocked')}}</b-badge>
                </span>
			</template>
            <template #cell(actions)="row">
                <span v-if="row.item.status">
                    <b-link href="#" @click="showClientHistory(row.item)">
                        <i class="bbi bbi-mid-ticket bbi-22" aria-hidden="true"></i>
                    </b-link>
                    <b-link class="ml-1" href="#" @click="showModalReason(row.item)">
                        <i class="fa fa-unlock fa-2x" aria-hidden="true"></i>
                    </b-link>
                </span>
                <span v-else>
                    <b-link href="#" @click="showClientHistory(row.item)">
                        <i class="bbi bbi-mid-ticket bbi-22" aria-hidden="true"></i>
                    </b-link>
                    <b-link class="ml-1" href="#" @click="showModalReason(row.item)">
                        <i class="fa fa-lock fa-2x" aria-hidden="true"></i>
                    </b-link>
                </span>
			</template>
            <template #empty="scope">
					<div class="text-center">{{$t('bs-no-registered-customers')}}</div>
				</template>
            </b-table>
        </div>

        <div class="row ml-1" style="text-aling: right">
            <b-pagination
            v-model="currentPage"
            :total-rows="quantPages"
            :per-page="quantRows"
            class="mt-2"
            >
                <template #first-text
                  ><span class="text-dark">{{ $t("bs-first") }}</span></template
                >
                <template #prev-text
                  ><span class="text-dark">{{ $t("bs-prev") }}</span></template
                >
                <template #next-text
                  ><span class="text-dark">{{ $t("bs-next") }}</span></template
                >
                <template #last-text
                  ><span class="text-dark">{{ $t("bs-last") }}</span></template
                >

                <template #ellipsis-text>
                  <b-spinner small type="grow"></b-spinner>
                  <b-spinner small type="grow"></b-spinner>
                  <b-spinner small type="grow"></b-spinner>
                </template>

                <template #page="{ page, active }">
                  <b v-if="active">
                    {{ page }}
                  </b>
                  <i v-else>{{ page }}</i>
                </template>
            </b-pagination>
        </div>
    </template>

    <modal-block v-if="showmodal" :showmodal="showmodal" :client_id="itemselected.id" :itemselected="itemselected" :type="'SYSTEM'"></modal-block>

    <modal-client-history
      :chat="itemselected"
      :clientChatHistory="clientChatHistory"
      :clientTicketHistory="clientTicketHistory"
      :user="user"
    />


	</b-container>
</template>

<script>

export default {
	data(){
		return {
            phBuscar: this.$t('bs-name')+' '+this.$t('bs-or')+' '+this.$t('bs-email'),
            selected: 'ALL',
            options: [
                { value: 'ALL', text: this.$t('bs-all') },
                { value: 'BLOCK', text: this.$t('bs-blocked') },
                { value: 'RELEASED', text: this.$t('bs-released') },
            ],
            fields: [
				{ key: 'name', sortable: true, label: this.$t('bs-name')  },
				{ key: 'email', sortable: true, label: this.$t('bs-email') },
				{ key: 'status', sortable: true, label: this.$t('bs-status') },
				{ key: 'actions', label: this.$t('bs-historic') + ' / ' + this.$t('bs-block') },
			],
            items: [],
            index_block: 0,
            showmodal: false,
            showselects: true,
            filtername: '',
            currentPage: 1, // VALOR DA PAGINA SELECIONADA
            quantPages: 20, // QUANTIDADE TOTAL DE TICKETS
            quantRows: 20, // QUATIDADE DE TICKETS POR PAGINA
            itemselected: {},
            clientChatHistory: [],
            clientTicketHistory: [],
            isPreview: false,
            chatPreview: null,
            openingChat: null,
		}
	},
    props:{
		user: Object,
		base_url: {
			type: String,
			default: ''
		},
		go_back_url: {
			type: String,
			default: ''
		}
	},
    created(){
        this.$root.$refs.UserClientBody = this;
    },
    mounted(){
        var vm = this;
        axios.get('get-clients-company', {
            params: {
                page: vm.currentPage,
            }
        })
        .then(function(r_resposta){
            // console.log(r_resposta.data.result);
            vm.items = r_resposta.data.result.data;
            if(r_resposta.data.result.data.length == 0){
                vm.showselects = false;
            }else{
                vm.quantPages = r_resposta.data.result.total;
                vm.showselects = true;
            }

        }).catch(function (error) {
            console.log(error);
        });
    },
    methods:{
        showClientHistory(item){
            this.openClientHistory(item.id);
            console.log(item);
            this.itemselected = item;
        },
        openClientHistory(id) {
            this.clientChatHistory = [];
            this.clientTicketHistory = [];
        
            const api = `client/get-client-history`;
            axios.get(api, {
                params: {
                    client_id: id,
                },
            }).then(({ data }) => {
                data.forEach((element) => {
                    if (element.type === "TICKET" || element.type === "CHANGED_TO_TICKET") {
                    this.clientTicketHistory.push(element);
                    } else {
                    this.clientChatHistory.push(element);
                    }
                });
                $("#modalClientHistory").modal("show");
            });
        },
        selectStatus(){
            var vm = this;
            vm.filtername = '';
            vm.currentPage = 1;
            axios.get('get-clients-company',{
                params: {
                    status: vm.selected,
                    page: vm.currentPage,
                },
            }).then(function(r_resposta){
                // console.log(r_resposta.data.result.data);
                vm.currentPage = 1;
                vm.items = r_resposta.data.result.data;
                if(r_resposta.data.result.data.length == 0){
                    vm.showmodal = false;
                    vm.showselects = false;
                }
            }).catch(function (error) {
                console.log(error);
            });
        },
        searchCLient: function(e) {
            if (e.keyCode === 13) {
                //  alert('Enter was pressed');
                var vm = this;
                vm.currentPage = 1;
                vm.selected = 'ALL';
                axios.get('get-clients-company',{
                    params: {
                        search: vm.filtername,
                        page: vm.currentPage,
                    },
                }).then(function(r_resposta){
                    vm.currentPage = 1;
                    // console.log(r_resposta.data.result);
                    vm.items = r_resposta.data.result.data;
                    if(r_resposta.data.result.data.length == 0){
                        vm.showmodal = false;
                        vm.showselects = false;
                    }
                }).catch(function (error) {
                    console.log(error);
                });
                
            } else if (e.keyCode === 50) {
                //  alert('@ was pressed');
            }      
            // this.log += e.key;
        },
        showModalReason(item){
            this.showmodal = true;
            this.itemselected = item;
        }
    },
    watch:{
        currentPage: function () {
            var vm = this;
            axios.get('get-clients-company', {
                params: {
                    page: vm.currentPage,
                }
            })
            .then(function(r_resposta){
                vm.items = r_resposta.data.result.data;
                if(r_resposta.data.result.data.length == 0){
                    vm.showmodal = false;
                    vm.showselects = false;
                }
            }).catch(function (error) {
                console.log(error);
            });
        },
    }
};
</script>

<style scoped>

    .scroll{
        overflow-y: scroll;
        max-height:380px;
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
}
</style>