<template>
	<div>
        <div class='ba-card-breadcrumb ba-card'>
            <nav class='ba-breadcrumb'>
                <ol class='ba-breadcrumb-ol'>
                    <li class='ba-breadcrumb-item ba-main-item'>
                        <a href='#'>
                            <span class='ba-icon'><icons-custom name="logo-head-icon" width="22"></icons-custom></span>
                        </a>
                    </li>
                    <li class='ba-breadcrumb-item'>
                        <a href='#'>{{ $t('bs-select-company') }}</a>
                    </li>
                </ol>
            </nav>
        </div>







   </div>
</template>

<script>
import iconsCustom from '../../util/icons/iconsCustom.vue';
export default {
    components:{
        iconsCustom,
    },
	data(){
		return {
			nome: "THEBESLOKOSOM!",
		}
	},
	mounted(){
	},
	methods: {
	},
};
</script>

<style scoped>
</style>