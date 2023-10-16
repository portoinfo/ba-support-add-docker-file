<template>
    <div class="modal fade" id="ModalCategory" tabindex="-1" aria-labelledby="ModalCategoryLabel" aria-hidden="true"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div style="overflow-y: auto;max-height: 40rem;">
                    <div v-if="$store.state.showAlertCategory" class="alert alert-danger" role="alert">
                        <center v-if="cttype == 'TICKET'">
                            <h5>{{$t('bs-please-categorize-before-closing-the-ticke')}}.</h5>
                        </center>
                        <center v-else>
                            <h5>{{$t('bs-please-categorize-before-closing-the-chat')}}.</h5>
                        </center>
                    </div>
                    <div class="modal-header border-0 p-0">
                        <h5 class="modal-title" id="ModalCategoryLabel">
                            {{$t('bs-linked-categories')}}:
                        </h5>
                    </div>
                    <div class="modal-body">
                        <h5 v-if="restartCategories">
                            <b-list-group-item v-for="(item, index) in registeredCategories" :key="index">
                                <span v-for="(item2, index2) in item" :key="index2">
                                    <span v-if="index2 > 0">
                                        - 
                                    </span> 
                                    <!-- {{item2.description}}  -->
                                    <span v-for="(item3, index3) in JSON.parse(item2.description)" :key="index3">
                                        <span v-if="item3.language == user.language && item3.description == ''">
                                            {{JSON.parse(item2.description)[1].description}}
                                        </span>

                                        <span v-if="item3.language == user.language && item3.description != ''">
                                            {{item3.description}}
                                        </span>
                                    </span>
                                    <b-badge v-if="item2.count == 0" class="caret" style="float:right" variant="danger" @click="removeCategory(item2.chat_category_id, index)">X</b-badge>
                                </span>
                                <br>
                            </b-list-group-item>
                        </h5>
                    
                    </div>

                    <div class="modal-header border-0 p-0">
                        <h5 class="modal-title" id="ModalCategoryLabel">
                            {{$t('bs-select-a-category')}}:
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <h5><b>
                                        <b-list-group-item v-show="concatCategory.length != 0">
                                            <span @click="homeCategories" class="c-blue caret">{{$t('bs-clean')}}</span>
                                            <span class="c-grey" v-for="(item, index) in concatCategory" :key="index">
                                                <span class="caret" @click="addCategory(item, 'select')"> - 
                                                <span v-for="(item3, index3) in JSON.parse(item.description)" :key="index3">
                                                    <span v-if="item3.language == user.language && item3.description == ''">
                                                        {{JSON.parse(item.description)[1].description}}
                                                    </span>

                                                    <span v-if="item3.language == user.language && item3.description != ''">
                                                        {{item3.description}}
                                                    </span>
                                                </span>
                                                </span>
                                            </span>
                                        </b-list-group-item>
                                    </b></h5>
                                    <div v-if="!showSaveCategory" >
                                        <span class="mt-3 mb-3"><b><h5>{{$t('bs-options')}}:</h5></b></span>
                                        <div>
                                            <span class="mt-3 mb-1">
                                                <b-list-group v-for="(item, index) in categoriesSelected" :key="index">
                                                    <b-list-group-item class="caret hoverlist f-18" @click="addCategory(item, 'list')">
                                                    <span v-for="(item3, index3) in JSON.parse(item.description)" :key="index3">
                                                        <span v-if="item3.language == user.language && item3.description == ''">
                                                            {{JSON.parse(item.description)[1].description}}
                                                        </span>

                                                        <span v-if="item3.language == user.language && item3.description != ''">
                                                            {{item3.description}}
                                                        </span>
                                                    </span>
                                                    </b-list-group-item>
                                                </b-list-group>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ml-2 mr-2 mt-2 mb-3 border-0">
                    <button type="button" class="text-capitalize btn btn-info" @click="backCategory">
                        {{ $t('bs-back') }} {{ $t('bs-category') }}
                    </button>
                    <button style="float:right;" type="submit" v-if="showSaveCategory" @click="saveCategory" id="btn-avaliation" class="ml-1 btn btn-primary" :disabled="disableSave">
                        {{ $t("bs-add") }}
                    </button>
                    <button style="float:right;" type="button" class="text-capitalize btn btn-danger" data-dismiss="modal" @click="cancelModal">
                        {{ $t('bs-cancel') }}
                    </button>
                </div>
            </div>
          
        </div>
    </div>
</template>

<script>

export default {
    data() {
        return {
            categoriesOrigin: [],
            categoriesSelected: [],
            catLinkAll: [],
            catLinkKids: [],
            concatCategory: [],
            showSaveCategory: false,
            disableSave: false,
            registeredCategories: [],
            agroupCategory: {},
            restartCategories: true,
            textShow: false,
            textClose: '',
        }
    },
    props: {
        user: Object,
        chat: Object,
        filter_not_category: Boolean,
        cttype: String,
    },
    created(){
        this.$root.$refs.modalCategory = this;
    },
    mounted() {
        this.getCategories();
        this.getCategoriesId();
    },
    methods:{
        backCategory(){
            this.concatCategory.splice(-1,1);

            let index = 0;
            if(this.concatCategory.length == 0){
                index = 0;
            }else{
                index = this.concatCategory.length-1
            }

            if(this.concatCategory.length == 0){
                this.addCategory([], 'select')
            }else{
                console.log(this.concatCategory[index]);
                this.addCategory(this.concatCategory[index], 'select')
            }
            
            // console.log(this.concatCategory);
        },
        saveCategory(){
            var vm = this;
            vm.disableSave = true;
            axios.post(`set-category`, {
                concatCategory: vm.concatCategory,
                chat_id: vm.chat.chat_id,
                filter_not_category: vm.filter_not_category,
                ticket_id: vm.chat.id,
                cttype: vm.cttype
            }).then(function (response) {
                if (response.data.success) {
                    vm.$snotify.success(vm.$t('bs-saved-successfully'), vm.$t('bs-success'));
                    vm.cancelModal();
                } else {
                    vm.$snotify.info(vm.$t(response.data.error), vm.$t('bs-info'));
                }
                vm.disableSave = false;
            }).catch(function (erro) {
                vm.disableSave = false;
                console.log(erro);
            });
        },
        removeCategory(id, index){
            var vm = this;
            axios.post(`remove-category`, {
                chat_category_id: id,
            }).then(function (response) {
                if (response.data.success) {
                    vm.restartCategories = false;
                    delete vm.registeredCategories[index];
                    vm.restartCategories = true;
                    Swal.fire(
                        vm.$t('bs-deleted'),
                        vm.$t('bs-successfully-deleted'),
                        'success'
                    )
                }
                vm.disableSave = false;
            }).catch(function (erro) {
                vm.disableSave = false;
                console.log(erro);
            }); 
        },  
        cancelModal(){
            this.concatCategory = [];
            this.showSaveCategory = false;

            if(this.cttype == 'TICKET'){
                this.$root.$refs.FullTicket2.showCategory = false;
            }else{
                this.$root.$refs.FullChat2.showCategory = false;
            }
            $("#ModalCategory").modal("hide");
        },
        homeCategories(){
            var my_object = {
                id: null,
            };
            this.addCategory(my_object, 'select');
        },
        addCategory(item, check){

            if(check == 'select'){
                this.concatCategory.forEach(element => {
                    if(element.id == item.id){

                    }else{
                        if(item.id == null){
                            this.concatCategory = [];
                        }else{
                            let index = this.concatCategory.findIndex((ele) => ele.id === item.id);
                            if (index !== -1) {
                                this.concatCategory.splice(index+1, 50); 
                            }
                        }
                    }
                });
            }else{
                this.concatCategory.push(item);
            }

            this.showSaveCategory = true;
            this.categoriesSelected = [];
            this.categories.forEach(element => {
                if(element.category_id == item.id){
                    this.categoriesSelected.push(element);
                    this.showSaveCategory = false;
                }
            });
        },
        getCategoriesId(){
            var vm = this;
            axios.get(`company-config/get-category-id`,{
                params: {
                    chat_id: vm.chat.chat_id,
                    cttype: vm.cttype
                }
            }).then(function(response){
                vm.catLinkAll = response.data.result;
                var quantidade = 0
                vm.catLinkAll.forEach(item => {
                    if(item.count == 0){
                        vm.catLinkKids.push(item);
                        quantidade++;
                    }
                });

                for (let index = 0; index < quantidade; index++) {
                    vm.registeredCategories.push(vm.catLinkKids[index]);
                    vm.procurarFilhos(vm.catLinkKids[index]);
                }

                var count = 0;
                vm.registeredCategories.slice(0).reverse().forEach(element => {
                    if(element.pai == null){
                        count++;
                        vm.agroupCategory[count] = [];
                    }
                    vm.agroupCategory[count].push(element);
                });

                vm.registeredCategories = vm.agroupCategory;

            }).catch(function (error) {
                console.log(error);
            });
        },
        procurarFilhos(finalCategory){
            this.catLinkAll.forEach(item => {
                if(item.category_id == finalCategory.pai){
                    this.registeredCategories.push(item);
                    this.procurarFilhos(item)
                }
            });
        },
        getCategories(){
            var vm = this;
            vm.categories = [];
            vm.categoriesSelected = [];
            axios.get(`company-config/get-category`)
            .then(function(response){
                vm.categories = response.data.result;
                vm.categories.forEach(element => {
                    if(element.category_id == null){
                        vm.categoriesSelected.push(element);
                    }
                });
                $("#ModalCategory").modal("show");
            }).catch(function (error) {
                console.log(error);
            });
        }
    }
};
</script>

<style scoped>

.f-18{
    font-size: 18px;
}

.c-blue{
    color: blue;
}
.c-grey{
    color: grey;
}

.hoverlist:hover{
    background-color: #d6d6d6;
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


@media only screen and (max-width: 575px) {
    .content {
        margin-right: 10px;
        margin-left: 10px;
    }

    .content-input {
        margin-left: 0px;
        margin-right: 0px;
        margin-bottom: 0px;
        border: none;
        border-radius: 0px;
    }

    .content-chat,
    .modal-content,

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
        margin-bottom: 250px;
        text-align: center;
        justify-content: center;
    }

}

@media only screen and (max-width: 255px) {

    .content-chat {
        overflow: auto;
        background-color: #e6e7e7;
    }

    .list-group-item p {
        font: normal normal normal 15px/18px Muli;
        letter-spacing: 0px;
        color: #434343;
    }

    .content-input {
        background: white;
        padding: 5px;
        margin-left: 50px;
        margin-right: 50px;
        margin-bottom: 15px;
        border-radius: 3px;
        border: 1px solid #dddddd;
    }

    .content-textarea {
        background: white;
        padding: 5px;
        margin-bottom: 15px;
        border-radius: 3px;
        border: 1px solid #dddddd;
    }

    .content {
        margin-right: 0;
        margin-left: 0;
    }

    .content-input {
        margin-left: 0px;
        margin-right: 0px;
        margin-bottom: 0px;
        border: none;
        border-radius: 0px;
    }
    .modal-content,


    .modal-dialog {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
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

    .modal-footer {
        margin-bottom: 150px;
        text-align: center;
        justify-content: center;
    }
}

.caret {
  cursor: pointer;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
.care {
    font: normal normal 800 15px/16px Muli;
    color: #434343 !important;
}
</style>