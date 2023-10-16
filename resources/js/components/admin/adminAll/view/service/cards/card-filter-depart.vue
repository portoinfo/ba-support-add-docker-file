<template>
	<div>
        <p @click="$parent.closeFilter()" class="ba-montserrat-700" style="color: #2A3C51">
            Filters
            <icons-custom class="fr caret" width="17" name="close-icon"></icons-custom>
        </p>
        <hr class="ba-hr-row ba-mg-t-1">
        <p class="ba-montserrat-500">
            <input type='checkbox' class='ba-checkbox' :checked="myChats">
            <label for="scales"> My chats</label>
        </p>
        <p class="ba-montserrat-500">
            <input type='checkbox' class='ba-checkbox' :checked="noCategorized">
            <label for="scales"> Not Categorized</label>
        </p>
        <hr class="ba-hr-row ba-mg-t-1">
        <p class="ba-montserrat-700" style="color: #2A3C51">
            Departments
        </p>
        <span class="max-w-s">
            <p  v-for="(item, index) in $store.state.departments" :key="index+'departs2'" class="ba-montserrat-500">
                <input type='checkbox'
                :value="item.id"
                class='ba-checkbox'
                @input="toggleDepartment(item.id)"
                :checked="selectedDepartments.includes(item.id)">
                <label for="scales" @click="pushDepartment(item.id)"> {{$t(item.name)}}</label>
            </p>
        </span>
	</div>
</template>

<script>
import iconsCustom from '../../../util/icons/iconsCustom.vue';
import Gravatar from '../../../../../../components/tools/Gravatar.vue';
export default {
    components:{
        iconsCustom,
        Gravatar,
    },
	data(){
		return {
			departments: [
                { id: 1, name: 'Department A', checked: false },
                { id: 2, name: 'Department B', checked: true },
                { id: 3, name: 'Department C', checked: false }
            ],
            selectedDepartments: [],
            myChats: false,
            noCategorized: false,
		}
	},
	created(){
        this.departmentsCheck();
	},
	methods: {
        toggleDepartment(id) {
            if (this.selectedDepartments.includes(id)) {
                this.selectedDepartments = this.selectedDepartments.filter(item => item !== id);
            } else {
                this.selectedDepartments.push(id);
            }
            const department = this.departments.find(item => item.id === id);
            if (department) {
                department.checked = this.selectedDepartments.includes(id);
            }
            localStorage.setItem("filter_departments2",JSON.stringify(this.selectedDepartments));
        },
        departmentsCheck() {
            let local_filter = JSON.parse(localStorage.getItem("filter_departments2"));
            if (local_filter) {
                local_filter.forEach((item, index) => {
                    this.toggleDepartment(item);
                    this.$store.state.filter_departments.push(item);
                });
            }
        }
	},
};
</script>

<style scoped>
    .ba-checkbox {
        border-radius: 10%;
        border: 1px solid #B4C4D7;
    }
    .max-w-s {
        display: block;
        height: 300px;
        overflow-x: auto;
    }
</style>