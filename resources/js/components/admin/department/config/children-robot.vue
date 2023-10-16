<template>
	<div>
        <span v-for="(item, index) in children" :key="index">
            
            <!-- {{item}} -->
            <span v-if="!showclick">

                <span v-for="(item2, index) in item.type" :key="index">
                    <span v-if="item2 == 'text'">
                        <span style="font-size: 20px">{{item.text}}</span>
                    </span>

                    <span v-if="item2 == 'default_button'">
                        <b-button variant="success" @click="click(item)">{{item.text}}</b-button>
                    </span>
                </span>

                <span v-if="item.children && item.children.length > 1" >
                    <children-robot :children="item.children"></children-robot>
                </span>

            </span>
            <br>
            <span v-if="item.id == itemselected.id && itemselected.children && showclick" >
                <children-robot :children="itemselected.children"></children-robot>
            </span>
        </span>
	</div>
</template>

<script>

export default {
	data(){
		return {
			showclick: false,
            itemselected: [],
		}
	},
    props:{
        children: Array,
    },
    mounted(){

    },
    methods: {
        click(item){
            this.showclick = true;
             console.log(item);
            this.itemselected = item;
        }
    }
};
</script>

<style scoped>
</style>