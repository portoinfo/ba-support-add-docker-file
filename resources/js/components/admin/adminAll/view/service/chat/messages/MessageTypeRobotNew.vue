<template>
    <div class="ba-mg-t-1"> 
        <div class="ba-flex ba-gp-1 ba-mg-t-1">
            <div>
                <gravatar-new 
                size="48px" 
                cEmail="robot" 
                :name="$t('bs-robot')"
                :ba_acct_data="null"
                ></gravatar-new>
            </div>
            <div>
                {{ message.content.text }}
                <div class="ba-flex">
                    <template  v-for="(item, idx) in message.content.children">
                        <button
                            v-if="item.type !== 'text'"
                            :class="item.selected ? 'ba-btn ba-md ba-green' : 'ba-btn ba-md ba-blue'" 
                            :key="idx"
                            disabled
                        >
                            {{ item.text }}
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import GravatarNew from "../../../../util/gravatar/GravatarNew.vue";
import Vue from 'vue';
import linkify from 'vue-linkify';
Vue.directive('linkified', linkify);
export default {
    components:{
        GravatarNew,
    },
    props: {
        message: Object,
        formatTime: "",
    },
    created(){
        console.log(this.message);
    },
    methods: {
    },
    computed: {
        isDad() {
            return this.message.children && this.message.children.length > 0;
        }
    },
};
</script>

<style scoped>
.disabled {
    opacity: 1 !important;
}

.item1 {
    grid-area: name;
    color: #0080fc;
    font-size: 15px;
    font-stretch: 100%;
    font-weight: 800;
    text-rendering: optimizeLegibility;
    line-height: 22px;
    padding-left: 5px;
}

.item2 {
    grid-area: gravatar;
    display: flex;
    align-items: initial;
    justify-content: center;
    padding-top: 8px;
}
.item3 {
    grid-area: content;
    color: #707070;
    font-size: 0.9rem;
    font-stretch: 100%;
    font-weight: 600;
    text-rendering: optimizeLegibility;
    -webkit-font-feature-settings: "kern" 1;
    line-height: 19px;
    padding-bottom: 5px;
    padding-right: 5px;
    padding-left: 5px;
    word-break: break-all;
}
.item4 {
    grid-area: time;
    text-align: right;
    color: #6e6e6e;
    opacity: 1;
    font-size: 11px;
    line-height: 20px;
    text-rendering: optimizeLegibility;
    font-weight: 700;
    padding-right: 5px;
}

.grid-container {
    display: grid;
    grid-template-areas:
        "gravatar name time"
        "gravatar content content";
    grid-template-columns: 45px auto 60px;
    border-top: 1px solid rgba(215, 222, 230, 0.2);
}

a {
    font-style: italic !important;
}

@media screen and (max-width: 992px) {
    .item3 {
        font-size: 16px;
    }
}
</style>
