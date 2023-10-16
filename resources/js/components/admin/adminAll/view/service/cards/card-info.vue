
<template>
    <div>
        <p @click="$parent.closeInfos()" class="ba-montserrat-700" style="color: #2A3C51">
            Chat Information 
            <icons-custom class="fr caret" width="17" name="close-icon"></icons-custom>
        </p>
        <hr class="ba-hr-row ba-mg-t-1">
        <p style="color:black"># <span class="color-r-t fz-14">{{itemselected.number == undefined ? 0 : itemselected.number}}</span></p>

        <p class="ba-montserrat-700">{{ $t('bs-description') }}: </p>
        <p class="ba-montserrat-500">{{ removerTagsHTML(itemselected.description == undefined ? 'Sem description' : itemselected.description) }}</p>
        
        <p v-if="questionary.length > 0" class="ba-montserrat-700">Initial Form:</p>
        
        <span  v-for="(item, index) in questionary" :key="'quest'+index">
            <p class="ba-montserrat-500">{{item.question}}</p>
            <p class="color-r-t fz-14">
                <div class='ba-box-search-input'>
                    <input type='search' disabled class='ba-input' :placeholder='removerTagsHTML(item.answer)' />
                </div>
            </p>
        </span>
        <p class="ba-montserrat-700">Chat Created By:</p>
        <p >
            <div class="ba-flex ba-gp-1">
                <div>
                    <gravatar-new 
                    size="18px" 
                    :cEmail="itemselected.email" 
                    :id="itemselected.client_id"
                    :name="itemselected.name"
                    :ba_acct_data="null"
                    ></gravatar-new>
                </div>
                <div class="ba-montserrat-700 color-r fz-14 owbw">
                    {{itemselected.name}}
                </div>
            </div>
        </p>
        <p class="ba-montserrat-700 color-r fz-14">
            <icons-custom :active="false" name="ticket-icon" width="14"></icons-custom>
            <span class="ba-mg-l-1">{{itemselected.email}}</span>
        </p>
        <p>
            <div class="ba-flex ba-gp-1">
                <div>
                    <gravatar-new 
                    size="18px" 
                    :cEmail="itemselected.email" 
                    :id="itemselected.client_id"
                    :name="itemselected.name"
                    :ba_acct_data="null"
                    ></gravatar-new>
                </div>
                <div class="ba-montserrat-700 color-r fz-14 owbw">
                    {{itemselected.department}}
                </div>
            </div>
        </p>
        <p class="ba-montserrat-700">Builderall Office Data:</p>
        <p class="ba-montserrat-700 color-r fz-14">
            <icons-custom name="calendar-icon" width="14"></icons-custom>
            {{itemselected.date}}
        </p>
        <p class="ba-montserrat-700 color-r fz-14">
            <icons-custom name="hour-icon" width="14"></icons-custom>
            {{itemselected.time}}
        </p>
        <p class="ba-montserrat-700 color-r fz-14">
            <icons-custom name="global-info-icon" width="14"></icons-custom>
            {{itemselected.user_agent}}
        </p>
        <div style="text-align: -webkit-center">
            <button class='ba-btn ba-md ba-blue ba-w-100 ba-mg-t-1'>
                {{ $t("bs-view-customer-history") }}
            </button>
            <button class='ba-btn ba-md ba-red ba-w-100 ba-mg-t-1'>
                {{ $t("bs-delete") }}
            </button>
            <button class='ba-btn ba-md ba-green ba-w-100 ba-mg-t-1'>
                {{ $t("bs-categorize") }}
            </button>
        </div>
    </div>
</template>

<script>
import iconsCustom from '../../../util/icons/iconsCustom.vue';
import GravatarNew from "../../../util/gravatar/GravatarNew.vue";
export default {
    components:{
        iconsCustom,
        GravatarNew,
    },
	data(){
		return {
			nome: "THEBESLOKOSOM!"
		}
	},
    props:{
        type: String,
        questionary: Array,
        itemselected: '',
    },
	mounted(){
        console.log(this.itemselected);
	},
	methods: {
        removerTagsHTML(html) {
            if(html != null){
                return html.replace(/<[^>]*>/g, '');
            }
        }
	},
};
</script>

<style scoped>
    p{
        font-size: 14px;
    }
    .owbw{
        overflow-wrap: break-word;
    }
</style>