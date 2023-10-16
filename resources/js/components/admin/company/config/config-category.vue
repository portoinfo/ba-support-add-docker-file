<template>
    <div>
        <b-row class="mb-2">
            <b-col>
                <b-button @click="back" variant="primary"><i class="fa fa-arrow-left" aria-hidden="true"></i> {{$t('bs-back')}}</b-button>
            </b-col>
            <b-col cols="auto">
                <b-button @click="moveCategory" block variant="primary"> <i class="fa fa-arrows-alt" aria-hidden="true"></i> {{$t('bs-to-move')}}</b-button>
            </b-col>
            <b-col cols="auto">
                <b-button @click="createDad('dad')" block variant="success"><i class="fa fa-plus-circle" aria-hidden="true"></i> {{$t('bs-create')}} {{$t('bs-category')}}</b-button>
            </b-col>
        </b-row>
        <h3><b>
            <span v-if="childrenSelected == ''">
            </span> 
            <span v-else>
                <span v-for="(item, index) in JSON.parse(childrenSelected.description)" :key="index">
                        <span v-if="item.language == usuario.language">
                        {{item.description}}
                    </span>
                </span>
            </span>
        </b></h3>
        <b-table responsive bordered borderless striped hover
            class="local-striped-table"
            head-variant="light"
            table-variant="light"
            :items="itemsFilter"
            :fields="fields"
            show-empty
        >
        <template #cell(index)="row">
            {{ row.index + 1 }}
        </template>
        <template #cell(dad_description)="row">
            <span v-for="(item, index) in JSON.parse(row.item.dad_description)" :key="index">
                <span v-if="item.language == usuario.language">
                    {{item.description}}
                </span>
            </span>
        </template>
        <template #cell(description)="row">
            <!-- {{row.item.description}} -->
            <span v-for="(item, index) in JSON.parse(row.item.description)" :key="index">
                <span v-if="item.language == usuario.language">
                    {{item.description}}
                </span>
            </span>
        </template>
        <template #cell(language)="row">
            <!-- {{row.item.description}} -->
            <span v-for="(item, index) in JSON.parse(row.item.description)" :key="index">
                <span v-if="item.language == usuario.language">
                    <img
                    :src="`https://flagcdn.com/24x18/${item.language.split('_')[1].toLowerCase()}.png`"
                    class="mr-2"
                    />
                </span>
            </span>
        </template>
        <template #cell(view_category)="row">
            <div class="c-blue caret" @click="viewChildren(row.item)">
                <vue-material-icon name="visibility" :size="30"/>
            </div>
        </template>
        <template #cell(add_category)="row">
            <div class="c-green caret" @click="createCategory(row.item, 'children')">
                <vue-material-icon name="add_circle" :size="30"/>
            </div>
        </template>
        <template #cell(edit_category)="row">
            <div class="c-info caret" @click="editCategory(row.item, 'children')">
                <vue-material-icon name="edit" :size="30"/>
            </div>
        </template>
        <template #cell(delete_category)="row">
            <div class="c-red caret" @click="deleteCategory(row.item, 'children')">
                <vue-material-icon name="delete" :size="30"/>
            </div>
        </template>
        <template #empty="scope">
            <div class="text-center">{{$t('bs-no-attendant-registered')}}.</div>
        </template>
    </b-table>

    <br>
    <br>
    <br>

    <div
    class="modal fade"
    id="modalEditcategory"
    tabindex="-1"
    aria-labelledby="modalEditcategory"
    aria-hidden="true"
    data-backdrop="static"
    data-keyboard="false"
    >
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 p-0">
                    <h4 class="modal-title" id="exampleModalLabel">{{$t('bs-edit')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        X
                    </button>
                </div>
                <div class="border-0 p-0">
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                          
                            <b-form-group
                                id="input-group-1"
                                :label="$t('bs-description')"
                                label-for="input-1"
                            >
                                <b-form-input
                                id="input-1"
                                v-model="form.description"
                                type="text"
                                required
                                ></b-form-input>
                            </b-form-group>

                            <b-form-group id="input-group-3" :label="$t('bs-language')" label-for="input-3">
                                <b-form-select
                                id="input-3"
                                size="sm"
                                v-model="select"
                                :options="options"
                                @change="newLanguage"
                                required
                                ></b-form-select>
                            </b-form-group>


                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="text-capitalize btn" data-dismiss="modal">
                        {{$t('bs-back')}}
                    </button>
                    <button type="button" id="btn-department" class="btn btn-success" @click="updateCategory">
                        {{$t('bs-save')}}
                    </button>
                </div>

                <br>
            </div>
        </div>
    </div>

    <div
    class="modal fade "
    id="moveCategory"
    tabindex="-1"
    aria-labelledby="moveCategory"
    aria-hidden="true"
    data-backdrop="static"
    data-keyboard="false"
    >
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 p-0">
                        <h4 class="modal-title" id="exampleModalLabel">{{$t('bs-edit')}}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            X
                        </button>
                    </div>
                <div class="border-0 p-0">
                </div>
                <br>
                <div class="modal-body scroll-modal">
                    <b-row>
                        <b-col>
                            <b-list-group>
                                <label>{{$t('bs-move-selected-categories-to')}}:</label>
                                <b-list-group-item :active="item.active" href="#some-link" v-for="(item, index) in listCategory" :key="'tt'+index" @click="clickItemMove(item, 1)">
                                    <span v-for="(item3, index3) in JSON.parse(item.dad_description)" :key="'gg'+index3">
                                        <span v-if="item3.language == usuario.language">
                                            {{item3.description}}:  
                                        </span>
                                    </span>
                                    <span v-for="(item2, index2) in JSON.parse(item.description)" :key="'ff'+index2">
                                        <span v-if="item2.language == usuario.language">
                                            <!-- {{item.id}} - {{item.category_id}} - -->
                                            {{item2.description}} 
                                        </span>
                                    </span>
                                </b-list-group-item>
                            </b-list-group>
                        </b-col>
                        <b-col v-if="!isMobile" cols="auto" class="center">
                            <i class="fa fa-arrows-alt fa-2x" aria-hidden="true"></i>
                        </b-col>
                        <b-col :class="isMobile == true ? 'mt-5' : ''">
                            <label>{{$t('bs-inside-of')}}:</label>
                            <b-list-group>
                                <b-list-group-item :variant="item.id == null ? 'info' : ''" :active="item.active2" href="#some-link" v-for="(item, index) in listCategory2" :key="'aa'+index" @click="clickItemMove(item, 2)">
                                    <span v-for="(item3, index3) in JSON.parse(item.dad_description)" :key="'bb'+index3">
                                        <span v-if="item3.language == usuario.language">
                                            {{item3.description}}:  
                                        </span>
                                    </span>
                                    <span v-if="item.id == null">
                                        {{item.description}}
                                    </span>
                                    <span v-else v-for="(item2, index2) in JSON.parse(item.description)" :key="'cc'+index2">
                                        <span v-if="item2.language == usuario.language">
                                            <!-- {{item.id}} - {{item.category_id}} - {{item2.description}} -->
                                            {{item2.description}}
                                        </span>
                                    </span>
                                </b-list-group-item>
                            </b-list-group>
                        </b-col>
                    </b-row>

                    <!-- <span v-for="(item, index) in selectedListCategory" :key="index">
                        <span v-for="(item2, index2) in JSON.parse(item.description)" :key="index2">
                            <span v-if="item2.language == usuario.language">
                                {{item.id}} - {{item.category_id}} - {{item2.description}}
                            </span>
                        </span>
                    </span> -->
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="text-capitalize btn" data-dismiss="modal">
                        {{$t('bs-back')}}
                    </button>
                    <button type="button" id="btn-department" class="btn btn-success" @click="updateMovedCategory">
                        {{$t('bs-to-move')}}
                    </button>
                </div>

                <br>
            </div>
        </div>
    </div>

    </div>
</template>

<script>

export default {
	data(){
		return {
            categories: [],
            fields: [
                {key: 'index', label: '#'},
                {key: 'description', label: this.$t('bs-description')},
                {key: 'language', label: this.$t('bs-language')},
                {key: 'view_category', label: this.$t('bs-view') +' '+ this.$t('bs-subcategory')},
                {key: 'add_category', label: this.$t('bs-add') +' '+ this.$t('bs-subcategory')},
                {key: 'edit_category', label:this.$t('bs-edit')},
                {key: 'delete_category', label: this.$t('bs-delete')},
			],
            searchQuery: null,
            itemsFilter: [],
            childrenSelected: [],
            itemEditSelected: [],
            options: [],
            select: '',
            form: {
                description: '',
                language: '',
            },
            alledits: [],
            listCategory: [],
            listCategory2: [],
            selectedListCategory: [],
            isMobile: false,
		}
	},
    props:{
        usuario:Object,
    },
    created(){
        this.getCategory();
        this.onResize();
        // this.moveCategory();
        window.addEventListener("resize", this.onResize);
    },
    watch:{
        "form.description": function() {
            this.alledits.forEach(item => {
                if(item.language == this.select){
                    item.description = this.form.description
                }
            });
        },
        isMobile(){
            // console.log(this.isMobile);
        }
    },
    methods: {
        getCategory(){
            var vm = this;
            axios.get(`company-config/get-category`)
            .then(function(response){
                vm.categories = response.data.result;
                
                vm.back();
                vm.mountLanguage();
            }).catch(function (error) {
                console.log(error);
            });
        },
        clickItemMove(item, table){
            if(table == 1){
                this.listCategory.forEach(element => {
                    if(item.id == element.id){
                        if(element.active == true){
                            let index = this.selectedListCategory.findIndex((elemt) => elemt.id === item.id);
                            if (index !== -1) {
                                this.selectedListCategory.splice(index, 1);
                            }
                            element.active = false;
                        }else{
                            this.selectedListCategory.push(item);
                            this.listCategory2.forEach(element2 => {
                                if(element2.active2 == true){
                                    element2.active2 = false;
                                }
                            });
                            element.active = true;
                        }

                        this.listCategory2.forEach(element2 => {
                            if(item.id == element2.id || item.id == element2.category_id){
                                element2.active2 = false;
                            }
                        });
                    }
                });
            }else{
                // console.log(item);
                this.listCategory2.forEach(element => {
                    if(item.id == element.id){
                        element.active2 = true;
                        this.listCategory.forEach(element2 => {
                            if(item.id == element2.id || item.category_id == element2.id){
                                let index = this.selectedListCategory.findIndex((elemt) => elemt.id === element2.id || elemt.category_id == element2.id);
                                if (index !== -1) {
                                    this.selectedListCategory.splice(index, 1);
                                }
                                element2.active = false;
                            }
                        });
                    }else{
                        element.active2 = false;
                    }
                });

                setTimeout(() => {
                    this.clear(item)
                }, 100);
            
            }
        },
        clear(item){
            this.listCategory.forEach(element => {
                if(item.category_id == element.id){
                    let index = this.selectedListCategory.findIndex((elemt) => elemt.id === element.id);
                    if (index !== -1) {
                        this.selectedListCategory.splice(index, 1);
                    }
                    element.active = false;
                    this.clear(element)
                }
            }); 
        },
        moveCategory(){
            var vm = this;
        	axios.get('company-config/get-category').then(function(response){
                vm.listCategory = response.data.result;
                vm.listCategory2 = response.data.result2;
                // console.log(vm.listCategory2);

                vm.listCategory2.unshift({
                    active: false, 
                    active2: false,
                    category_id: null,
                    dad_description: null,
                    description: vm.$t('bs-initial-category'),
                    id: null
                });

                vm.selectedListCategory = [];
                $('#moveCategory').modal('show');
            }).catch(function (error) {
                console.log(error);
            }); 
        },
        mountLanguage(){
            this.$store.state.languages.forEach(item => {
                this.options.push({value: item.key, text: item.desc});
                this.alledits.push({language: item.key, description: ''});
            });
        },
        back(){
            this.childrenSelected = [];
            this.fields= [
                {key: 'index', label: '#'},
                {key: 'description', label: this.$t('bs-description')},
                {key: 'language', label: this.$t('bs-language')},
                {key: 'view_category', label: this.$t('bs-view') +' '+ this.$t('bs-subcategory')},
                {key: 'add_category', label: this.$t('bs-add') +' '+ this.$t('bs-subcategory')},
                {key: 'edit_category', label:this.$t('bs-edit')},
                {key: 'delete_category', label: this.$t('bs-delete')},
			],
            this.itemsFilter = [];
            this.categories.forEach(element => {
                if(element.category_id ==  null){
                    this.itemsFilter.push(element);
                }
            });
        },
        newLanguage(){
            this.form.description = '';
            this.alledits.forEach(element => {
                if(element.language == this.select){
                    this.form.description = element.description;
                }
            });
        },
        editCategory(item, type = 'children'){
            this.alledits = JSON.parse(item.description);
            this.itemEditSelected = item;
            this.select = this.usuario.language
            JSON.parse(item.description).forEach(item => {
                if(item.language == this.usuario.language){
                    this.form.description = item.description
                }
            });

            $('#modalEditcategory').modal('show');
        },
        updateMovedCategory(){
            axios.post('company-config/update-moved-category',{
                selectedsItems: this.selectedListCategory,
                listCategory2: this.listCategory2,
            }).then(({data}) => {
                // console.log(data);
                if(data.success) {

                    this.getCategory();

                    $('#moveCategory').modal('hide');
                }else{

                }
            }).catch(err => {
                console.error(err);
                this.$loading(false);
            })
        },
        updateCategory(){
            var aux = JSON.stringify(this.alledits);
            axios.post('company-config/update-category',{
                itemEditSelected: this.itemEditSelected,
                description: aux,
            }).then(({data}) => {
                if(data.success) {
                    this.itemEditSelected.description = data.object
                    //adicionar atualização no pai - se pedirem.
                    $('#modalEditcategory').modal('hide');
                    Swal.fire(
                        this.$t('bs-updated'),
                        this.$t('bs-successfully-update'),
                        'success'
                    )
                }else{

                }
            }).catch(err => {
                console.error(err);
                this.$loading(false);
            })
        },
        viewChildren(item){
            this.childrenSelected = item;
            this.itemsFilter = [];
            this.fields= [
                {key: 'index', label: '#'},
                {key: 'dad_description', label: this.$t('bs-dad')},
                {key: 'description', label: this.$t('bs-description')},
                {key: 'language', label: this.$t('bs-language')},
                {key: 'view_category', label: this.$t('bs-view') +' '+ this.$t('bs-subcategory')},
                {key: 'add_category', label: this.$t('bs-add') +' '+ this.$t('bs-subcategory')},
                {key: 'edit_category', label: this.$t('bs-edit')},
                {key: 'delete_category', label: this.$t('bs-delete')},
			],
            this.categories.forEach(element => {
                if(element.category_id ==  item.id){
                    this.itemsFilter.push(element);
                }
            });
        },
        createDad(){
            this.createCategory(this.childrenSelected, 'dad');
        },
        createCategory(item, type = 'children'){
            var vm = this;
            var id = null;
            if(item != null){
                id = item.id; 
            }
            Swal.fire({
            title: vm.$t('bs-description'),
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            cancelButtonText: vm.$t('bs-cancel'),
            confirmButtonText: vm.$t('bs-create'),
            showLoaderOnConfirm: true,
            preConfirm: (description) => {
                if(description == ''){
                    vm.$snotify.info(vm.$t('bs-empty-fields'), vm.$t('bs-info'));
                }else{
                    var url = `company-config/create-category`;
                    axios.post(url, {
                        description: description,
                        selected: id,
                    }).then(function(response){
                        if(response.data.success){

                            vm.categories.push(
                                {id: response.data.id, 
                                category_id: response.data.category_id, 
                                description: response.data.object, 
                                dad_description: item ? item.description == undefined ? null : item.description : null}
                            );

                            if(type == 'dad'){
                                vm.itemsFilter.push(
                                    {id: response.data.id, 
                                    category_id: response.data.category_id, 
                                    description: response.data.object, 
                                    dad_description: item ? item.description == undefined ? null : item.description : null}
                                );
                            }
                        }else{
                            vm.$snotify.error( vm.$t('bs-error-trying-to-save') , vm.$t('bs-error'));
                        }
                    }).catch(function(){
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        )
                    });
                }
            },
            allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                }
            })
		},
        deleteCategory(item, type = 'children'){
            var vm = this;
            Swal.fire({
                title: vm.$t('bs-are-you-sure'),
                text: vm.$t('bs-you-wont-be-able-to-revert-this'),
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: vm.$t('bs-cancel'),
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: vm.$t('bs-yes-delete-it'),
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('company-config/delete-category',{
                        category_id: item.id
                    }).then(({data}) => {
                        if(data.success) {
                            let index = vm.categories.findIndex((elemt) => elemt.id === item.id);
                            if (index !== -1) {
                                vm.categories.splice(index, 1);
                            }

                            let index2 = vm.itemsFilter.findIndex((elemt2) => elemt2.id === item.id);
                            if (index2 !== -1) {
                                vm.itemsFilter.splice(index2, 1);
                            }
                            
                            Swal.fire(
                                vm.$t('bs-deleted'),
                                vm.$t('bs-successfully-deleted'),
                                'success'
                            )
                        }else{
                            if(data.error == 'already_linked'){
                                vm.$snotify.info(vm.$t('bs-the-category-is-linked'), vm.$t('bs-info'));
                            }
                        }
                    }).catch(err => {
                        console.error(err);
                        this.$loading(false);
                    })
                } else {
                    this.$loading(false);
                }
            });
        },
        onResize(e) {
            if ($(window).width() <= 992) {
                this.isMobile = true;
            } else {
                this.isMobile = false;
            }
        },
    },
    computed: {
		resultQuery(){
			if(this.searchQuery){
				return this.categories.filter((item)=>{
					return this.searchQuery.toLowerCase().split(' ').every(v => item.attendants.toLowerCase().includes(v))
				})
			}else{
				return this.categories;
			}
		}
	},
};
</script>

<style scoped>

.scroll-modal{
    overflow-y: scroll;
    max-height: 29rem;
}
.center{
    display: flex;
    justify-content: center;
    align-items: center;
}

.caret {
  cursor: pointer;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

.c-blue{
    color: #007bff !important;
}
.c-green{
    color: #28a745 !important;
}
.c-red{
    color: #c82333 !important;
}

.c-info{
    color: #016c70 !important;
}

.btn-outline-primary {
    color: #1665d8  !important;
    border-color: #1665d8 !important;
}

.btn-outline-primary:hover {
    background-color: #1665d8  !important;
    color:white !important;
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
	font: normal normal bold 22px/28px Muli;
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