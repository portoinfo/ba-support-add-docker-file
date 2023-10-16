<template>
	<div>
        <b-row>
            <b-col>
            	<span v-if="showfilter.id">
                  	<b-form-group id="input-group-2" label-for="input-2">
				        <b-form-input
				          id="input-2"
				          @change="filterId"
				          v-model="filter_id"
				          placeholder="Enter id"
				          type="number"
				          class="bs-input"
				        ></b-form-input>
			      	</b-form-group>
                </span>
            	<span v-if="showfilter.departamento">
	                <multiselect
	                    @input="filterQueue"
	                    v-model="filter_department"
	                    deselect-label=""
	                    selectLabel=""
	                    track-by="name"
	                    label="name"
	                    :placeholder="$t('bs-filter-by-department')"
	                    :options="departments"
	                    :searchable="false"
	                    :allow-empty="false"
	                    id="departments"
	                    :user="user"
	                    class="mb-2">
	                </multiselect>
                </span>
            </b-col>
<!--             <b-col cols="3">
                <multiselect
                    @input="selectTypeFilter"
                    v-model="selectedFilter"
                    deselect-label=""
                    selectLabel=""
                    track-by="name"
                    label="name"
                    :placeholder="$t('bs-filter-by-department')"
                    :options="filtersOptions"
                    :searchable="false"
                    :allow-empty="false"
                    id="selectedFilter"
                    :user="user"
                    class="mb-2">
                </multiselect>
            </b-col> -->
            <template v-if="restriction[0].ticket_admin == 1 || restriction[0].ticket_alllist == 1">
              <b-col cols="auto">
                   <b-form-checkbox
                      id="checkbox-1"
                      v-model="statusMyTickets"
                      @change="filterQueue"
                      name="checkbox-1"
                      class="mt-2 fz-18"
                    >
                          Meus Tickets
                    </b-form-checkbox>
              </b-col>
              <!-- <b-col cols="auto">
                   <b-button variant="primary">Filtrar</b-button>
              </b-col> -->
            </template>
      </b-row>
	</div>
</template>

<script>

export default {
	data(){
		return {
	      	filter_id: '',
	      	filter_department: {
	        	id: 0,
		        name: this.$t("bs-all"),
	      	},
	      	filtersOptions:[
	          	{ name: 'departamentos' },
	      		{ name: 'id'},
	          	{ name: 'name' },
	          	{ name: 'email' },
	          	{ name: 'description' },
          	],
	      	departments: [],
	      	statusMyTickets: false,
	      	ticketsLocal: [],
	      	TicketRetorno: [],
	      	showfilter: {
	      		id: false,
	      		departamento: true,
	      	},
	      	selectedFilter: [],
		}
	},
	props:{
		activeMenuControls: Object,
		tickets: Array,
		ticketsOriginal: Array,
		user: Object,
        restriction: Array,
	},
	created(){
		this.selectedFilter = [
			{ name: 'departamentos' }
		];
		this.getDepartmentsByAgent();
	},
	watch: {
	    // whenever question changes, this function will run
	    ticketsOriginal: function () {
	    	//console.log('atualizou');
	    	this.filterQueue();
	    	//this.filterQueue();
	    }
  	},
	methods: {
		selectTypeFilter(){
			if(this.selectedFilter.name == 'departamentos'){
				//console.log('eae');
			}
			if(this.selectedFilter.name == 'id'){
				this.showfilter.departamento = false;
				this.showfilter.id = true;
			}
		},
		filterId(){
			var vm = this;
			if (vm.activeMenuControls.active.opened) {
				vm.TicketRetorno = vm.ticketsLocal.filter(function (item) {
                    return item.id === parseInt(vm.filter_id);
                });
                vm.$emit("ticketsFilter", vm.TicketRetorno);
			}

			if (vm.activeMenuControls.active.inProgress) {
				vm.TicketRetorno = vm.ticketsLocal.filter(function (item) {
                    return item.id === vm.filter_id;
                });
                vm.$emit("ticketsFilter", vm.TicketRetorno);
			}
		},
    	filterQueue() {
        	var vm = this;
        	vm.ticketsLocal = vm.ticketsOriginal;

        	//VERIFICA SE A OPÇÃO MARCADA É "TODOS"
	        if(vm.filter_department.id == 0){
        		if(vm.statusMyTickets){
        			//console.log('marcou');
        			if (vm.activeMenuControls.active.opened) {
		                vm.TicketRetorno = vm.ticketsLocal.filter(function (item) {
		                	//console.log('entrou auqi');
		                    return item.status === 'OPENED';
		                });
		            }else if (vm.activeMenuControls.active.inProgress) {
	                    vm.TicketRetorno = vm.ticketsLocal.filter(function (item) {
	                        return item.status === 'IN_PROGRESS' && item.name === vm.user.name;
	                    });
	                } else if (vm.activeMenuControls.closed) {
	                    vm.TicketRetorno = vm.ticketsLocal.filter(function (item) {
	                        return item.status === 'CLOSED' && item.name === vm.user.name;
	                    });
	                } else if (vm.activeMenuControls.resolved) {
	                    vm.TicketRetorno = vm.ticketsLocal.filter(function (item) {
	                        return item.status === 'RESOLVED' && item.name === vm.user.name;
	                    });
	                } else if (vm.activeMenuControls.canceled) {
	                    vm.TicketRetorno = vm.ticketsLocal.filter(function (item) {
	                        return item.status === 'CANCELED' && item.name === vm.user.name;
	                    });
	                }
            	}else{
            		//console.log('desmarcou');
		            if (vm.activeMenuControls.active.opened) {
		                vm.TicketRetorno = vm.ticketsLocal.filter(function (item) {
		                    return item.status === 'OPENED';
		                });
		            } else if (vm.activeMenuControls.active.inProgress) {
		                vm.TicketRetorno = vm.ticketsLocal.filter(function (item) {
		                    return item.status === 'IN_PROGRESS';
		                });
		            } else if (vm.activeMenuControls.closed) {
		                vm.TicketRetorno = vm.ticketsLocal.filter(function (item) {
		                    return item.status === 'CLOSED';
		                });
		            } else if (vm.activeMenuControls.resolved) {
		                vm.TicketRetorno = vm.ticketsLocal.filter(function (item) {
		                    return item.status === 'RESOLVED';
		                });
		            } else if (vm.activeMenuControls.canceled) {
		                vm.TicketRetorno = vm.ticketsLocal.filter(function (item) {
		                    return item.status === 'CANCELED';
		                });
		            }
	            }

	            vm.$emit("ticketsFilter", vm.TicketRetorno);
	            return;
	        }else{
	            if(vm.ticketsLocal.length == 0){

	            }else{
	            	if(vm.statusMyTickets){
	            		if (vm.activeMenuControls.active.opened) {
		                    vm.TicketRetorno = vm.ticketsLocal.filter(function (item) {
		                        return item.department_id === vm.filter_department.id && item.status === 'OPENED';
		                    });
		                } else if (vm.activeMenuControls.active.inProgress) {
		                    vm.TicketRetorno = vm.ticketsLocal.filter(function (item) {
		                        return item.department_id === vm.filter_department.id && item.status === 'IN_PROGRESS' && item.name === vm.user.name;
		                    });
		                } else if (vm.activeMenuControls.closed) {
		                    vm.TicketRetorno = vm.ticketsLocal.filter(function (item) {
		                        return item.department_id === vm.filter_department.id && item.status === 'CLOSED' && item.name === vm.user.name;
		                    });
		                } else if (vm.activeMenuControls.resolved) {
		                    vm.TicketRetorno = vm.ticketsLocal.filter(function (item) {
		                        return item.department_id === vm.filter_department.id && item.status === 'RESOLVED' && item.name === vm.user.name;
		                    });
		                } else if (vm.activeMenuControls.canceled) {
		                    vm.TicketRetorno = vm.ticketsLocal.filter(function (item) {
		                        return item.department_id === vm.filter_department.id && item.status === 'CANCELED' && item.name === vm.user.name;
		                    });
		                }
	            	}else{
	            		if (vm.activeMenuControls.active.opened) {
		                    vm.TicketRetorno = vm.ticketsLocal.filter(function (item) {
		                        return item.department_id === vm.filter_department.id && item.status === 'OPENED';
		                    });
		                } else if (vm.activeMenuControls.active.inProgress) {
		                    vm.TicketRetorno = vm.ticketsLocal.filter(function (item) {
		                        return item.department_id === vm.filter_department.id && item.status === 'IN_PROGRESS';
		                    });
		                } else if (vm.activeMenuControls.closed) {
		                    vm.TicketRetorno = vm.ticketsLocal.filter(function (item) {
		                        return item.department_id === vm.filter_department.id && item.status === 'CLOSED';
		                    });
		                } else if (vm.activeMenuControls.resolved) {
		                    vm.TicketRetorno = vm.ticketsLocal.filter(function (item) {
		                        return item.department_id === vm.filter_department.id && item.status === 'RESOLVED';
		                    });
		                } else if (vm.activeMenuControls.canceled) {
		                    vm.TicketRetorno = vm.ticketsLocal.filter(function (item) {
		                        return item.department_id === vm.filter_department.id && item.status === 'CANCELED';
		                    });
		                }
	            	}
	            }
	            vm.$emit("ticketsFilter", vm.TicketRetorno);
	        }
	    },
    	getDepartmentsByAgent() {
      	axios
	        .get("company-user-company-department/get-department-by-agent")
	        .then((response) => {
	          this.departments = response.data;
	          this.departments.unshift({
	            id: 0,
	            name: this.$t("bs-all"),
	          });
	        });
    	},
	},
};
</script>

<style scoped>
.bs-input{
	border: none;
}
</style>
