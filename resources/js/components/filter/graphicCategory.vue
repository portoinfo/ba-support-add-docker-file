<template>
    <div>
        <div id="ba-hd__header" class="filter">
            <div class="header_title d-table h-100 w-100">
                <span class="ba-hd__title d-table-cell align-middle">
                    {{ title }}
                </span>
            </div>
        </div>
        <div class="ml-2 mr-2">

            <div id="ba-hd__main">
                <div class="ba-hd__card-content sr">

                    <div class="ml-3 mr-3 mt-1 mb-2">
                        <b-row>
                            <b-col>
                                {{$t('bs-filter-by-level')}}: 
                                <b-form-input
                                id="input-1"
                                v-model="form.level"
                                :placeholder="$t('bs-level')"
                                ></b-form-input>
                            </b-col>
                            <b-col>
                                {{$t('bs-initial-date')}}:
                                <b-form-datepicker id="example" v-model="form.dateInitial" placeholder="--" class="pt-0 pb-0"></b-form-datepicker>
                            </b-col>
                            <b-col>
                                {{$t('bs-final-date')}}:
                                <b-form-datepicker id="example-datepicker" v-model="form.dateFinal" placeholder="--" class="pt-0 pb-0"></b-form-datepicker>
                            </b-col>
                            <b-col>
                                {{$t('bs-search')}}:
                                <b-form-input
                                id="input-2"
                                v-model="form.search"
                                @keyup.enter="getinfoCategory"
                                ></b-form-input>
                            </b-col>
                            <b-col>
                                <label class="mb-2">{{$t('bs-type')}}:</label>
                                <b-form-radio-group
                                    v-model="table_grap"
                                    :options="options_table_grap"
                                    name="radio-inline"
                                ></b-form-radio-group>
                            </b-col>
                            <b-col cols="auto">
                                <b-button style="float:right;" class="mt-3" variant="primary" @click="getinfoCategory">{{$t('bs-search')}}</b-button>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <label class="mt-2">{{$t('bs-departments')}}:</label>
                                <multiselect
                                    v-model="form.departments"
                                    deselect-label=""
                                    selectLabel=""
                                    track-by="name"
                                    :multiple="true"
                                    label="name"
                                    :placeholder="$t('bs-all')"
                                    :options='departments'
                                    :searchable='false'
                                    :allow-empty='true'
                                    @input='checkDepartments'
                                    id="departments">
                                </multiselect>
                            </b-col>
                        </b-row>
                    </div>

                    <div v-if="table_grap == 'grap'">
                        <bar-chart
                            v-if="chart2.loadedData"
                            :chartData="{
                                labels: chart2.xLabels,
                                datasets: chart2.lineData
                            }"
                            :options="{
                                responsive: true,
                                maintainAspectRatio: false,
                                legend: {
                                    align: 'start',
                                    labels: {
                                        boxWidth: 10,
                                        fontFamily: 'Muli, Lato, Havelica',
                                        fontSize: 14,
                                        fullWidth: false
                                    }
                                },
                                scales: {
                                    yAxes: [{
                                        gridLines: {
                                            borderDash: [1,2],
                                            drawBorder: false,
                                        },
                                        ticks: {
                                            beginAtZero: true, // inicia de 0
                                            suggestedMin: 0,
                                            min: 0, // sem valores negativos
                                        },
                                    }],
                                    xAxes: [{
                                        gridLines: {
                                            display: false,
                                        },
                                    }],
                                }
                            }"
                            :styles="{ // parent node
                                position: 'relative',
                                width: '100%',
                                height: 'auto',
                            }"
                            :width="534"
                            :height="400"
                            :plugins="[
                                {
                                    id: 'my-plugin',
                                    beforeInit: function (chart) {
                                        chart.legend.afterFit = function() {
                                            this.height = this.height + 30;
                                        };
                                    }
                                }
                            ]"
                        ></bar-chart>
                    </div>

                    <div class="back" v-if="table_grap == 'table'">
                        <b-table responsive bordered borderless striped hover
                        class="local-striped-table"
                        head-variant="light"
                        table-variant="light"
                        :items="items" 
                        :fields="fields"
                        show-empty
                        >
                            <template #cell(categoria_pai)="row">
                                {{row.item.categoria_pai == null ? '--' : row.item.categoria_pai}}
                            </template>
                            <template #cell(view)="row">
                                <div class="c-blue caret" @click="showInfoCategories(row.item.categoria, row.item.category_id)">
                                    <vue-material-icon name="visibility" :size="30"/>
                                </div>
                            </template>
                        </b-table>
                    </div>

                    <modal-client-history :dataInfo="true" :category_name="category_name" :chat="chat" :user="user_fake" :clientChatHistory="clientChatHistory" :clientTicketHistory="clientTicketHistory" :isMobile="isMobile"/>

                </div>
            </div>

        </div>
    </div>
</template>

<script>

export default {
    data() {
        return {
            title: this.$t('bs-filter') + ' ' + this.$t('bs-categories'),
            form:{
                level: '0',
                dateInitial: '',
                dateFinal: '',
                search: '',
                departments: '',
            },
            departments: [],
            isMobile: false,
            chart2:{
                title: '',
                loadedData: false,
                xLabels: [
                    this.$t('bs-monday'), 
                    this.$t('bs-tuesday'), 
                    this.$t('bs-wednesday'), 
                    this.$t('bs-thursday'), 
                    this.$t('bs-friday'), 
                    this.$t('bs-saturday'), 
                    this.$t('bs-sunday'), 
                ],
                lineData: [
                    {
                        label: this.$t('bs-categories'),
                        backgroundColor: "#00C38E",
                        borderColor: "#00C38E",
                        fill: false,
                        data: []
                    },
                ]
            },
            items: [],
            fields: [
                { key: 'categoria_pai', sortable: true, label: this.$t('bs-categories') + ' ' + this.$t('bs-dad') },
                { key: 'categoria', sortable: true, label: this.$t('bs-categories') },
                { key: 'Count', sortable: true, label: this.$t('bs-total') },
                { key: 'view', sortable: true, label: this.$t('bs-view') }
            ],
            table_grap: 'table',
            options_table_grap: [
                { text: this.$t('bs-table'), value: 'table' },
                { text: this.$t('bs-graphic'), value: 'grap' }
            ],
            clientChatHistory: [],
            clientTicketHistory: [],
            chat: {
                client: {
                    browser: "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36",
                    builderall_account_data: "{\"is_vip\":false,\"id\":\"1634790\",\"uuid\":\"1f25f051-7bd1-4fb3-af57-39e72aa798c0\"}",
                    email: "marlos_gpi@live.com",
                    id: "aXlxamwyUlVXVm5QZkswN3l2RFRUZz09",
                    ip: "",
                    location: "",
                    name: "Marlos",
                    so: "",
                },
                builderall_account_data: "{\"is_vip\":false,\"id\":\"1634790\",\"uuid\":\"1f25f051-7bd1-4fb3-af57-39e72aa798c0\"}",
                chat_id: "Tis5Y3FMbUpJWXd3WGk3UXdXaFhidz09",
                client_id: "aXlxamwyUlVXVm5QZkswN3l2RFRUZz09",
                comp_user_comp_depart_id_current: "emt5bzNyUStuSU8rcENnTWNBKzkyZz09",
                companyDepartmentId: "NkE2VEhXeHdvelRrbVNaZ1FzUmYwQT09",
                content: "",
                created_at: "2023-05-15T17:07:31.000000Z",
                date: "15/05/2023",
                dep_type: "",
                department: "Bingo",
                email: "marlos_gpi@live.com",
                id: "",
                is_vip: false,
                name: "Marlos",
                number: 91877,
                show: true,
                sideContent: null,
                status: "RESOLVED",
                time: "17:07:31",
                turn_into_ticket_at_closing: 0,
                type: "DEFAULT",
            },
            user_fake: {
                builderall_account_data: null,
                builderall_status: "ACTIVE",
                can_create_company: 1,
                config: "{\"fontSize\":\"16px\",\"signature\":\"\",\"notification\":{\"email\":1,\"system\":1,\"telegram\":0},\"chat_id_favorite\":[91349]}",
                cookies_accepted: 1,
                created_at: "2021-01-03T13:05:37.000000Z",
                created_by: null,
                dark_mode: 0,
                deleted_at: null,
                deleted_by: null,
                email: "adminMaster@live.com",
                email_verified_at: null,
                hash_code: "YjM3RnBDSVJkYysvVFUvdjRIOUVNdz09",
                id: 19,
                is_anonymous: 0,
                language: this.session_user.language,
                name: "adminMaster",
                permanent_delete: 0,
                phone: null,
                subsidiary_id: 2,
                terms_user: 1,
                updated_at: "2023-06-13T14:55:07.000000Z",
                updated_by: 19,
                user_uuid: "123123",
            },
            isPreview: false,
            chatPreview: false,
            openingChat: false,
            category_name: '',
        }
    },
    props: {
        session_user: Object,
        session_user_company: Object,
        session_user_cucd: Array,
        session_user_departments: Array,
        session_user_permissions: Array,
    },
    created(){
        this.$root.$refs.categoryGraphic = this;
        window.addEventListener("resize", this.onResize);
        this.onResize();
        this.loadDepartments();
        this.getDepartments();
        this.getinfoCategory();
        this.checkDepartments();
    },
    methods: {
        loadDepartments(){
            if(localStorage.getItem('departCategories')){
                this.form.departments = JSON.parse(localStorage.getItem('departCategories'));
            }
        },
        checkDepartments(){
            localStorage.setItem('departCategories', JSON.stringify(this.form.departments));
            this.getinfoCategory();
        },
        getDepartments(){
            axios.get("get-departments-by-company", {
            }).then((response) => {
                response.data.forEach((item) => {
                    if (item.settings !== null) {
                        this.departments.push({name: this.$t(item.text), id: item.id});
                    }
                });
            });
        },
        getinfoCategory() {
            this.chart2.loadedData = false;
            var aux = [];
            if(this.form.departments == ''){
                aux = [];
            }else{
                aux = this.form.departments.map(x => x.id);
            }

            axios.get("get-category-graphic", {
                params: {
                    data: this.form,
                    departments: aux,
                    tz: Intl.DateTimeFormat().resolvedOptions().timeZone,
                }
            }).then((response) => {
                var concat = []
                response.data.result.forEach(element => {
                    concat.push(element.categoria_pai == null ? element.categoria : element.categoria_pai + '-' + element.categoria);
                });

                setTimeout(() => {
                    this.items = response.data.result;
                    this.chart2.xLabels = concat
                    this.chart2.lineData[0].data = response.data.result.map(x => x.Count);
                    this.chart2.loadedData = true;
                }, 100);

            });
        },
        showInfoCategories(category_name, id){
            var aux = [];
            if(this.form.departments == ''){
                aux = [];
            }else{
                aux = this.form.departments.map(x => x.id);
            }
            this.clientChatHistory = [];
            this.clientTicketHistory = [];
            this.category_name = category_name;

            axios.get("get-category-info", {
                params: {
                    id: id,
                    data: this.form,
                    departments: aux,
                    client_id: id,
                }
            }).then((response) => {
                response.data.result.forEach((element) => {
                    if (element.type === "TICKET" || element.type === "CHANGED_TO_TICKET") {
                        this.clientTicketHistory.push(element);
                    } else {
                        this.clientChatHistory.push(element);
                    }
                });
                $("#modalClientHistory").modal("show");
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
    // watch: {
    //     'form.departments': function (){
            
    //     },
    // },
};
</script>

<style scoped>

.c-blue{
    color: #007bff !important;
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
.sr{
    overflow: auto !important;
}

.ba-hd__card-content{
    background: none !important;
    box-shadow: none !important; /* 0px 0px 9px #26242424 */
}

.back {
    background: white !important;
    box-shadow: 0px 0px 9px #26242424 !important;
}

</style>