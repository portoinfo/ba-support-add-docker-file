<template>
    <div
        :class="['rounded-circle circular-bg', bgClass]"
        :style="{
            width: size,
            height: size
        }"
    >
        <slot name='icon' :icon='icon'></slot>
    </div>
</template>

<script>
    export default {
        name: 'circular-bg-icon',
        props: {
            icon: {
                type: Object,
                default: {
                    name: 'person',
                    size: 33,
                    color: 'white'
                }
            },
            size: {
                type: String,
                default: '53px'
            },
            bgClass: {
                type: String,
                default: 'default-bg'
            }
        }
    }
</script>

<style lang="scss" scoped>
    .circular-bg{
        display: flex;
        justify-content: center;
        align-items: center;
    }
    /* solid colors  */
    .default-bg{
        background: black;
    }
    /* gradientes */
    .blue-gradiente-bg{
        background: transparent linear-gradient(33deg, #01AAFC 0%, #0294FF 100%) 0% 0% no-repeat padding-box;
    }
    .red-gradiente-bg{
        background: transparent linear-gradient(33deg, #FD1A7A 0%, #F67D34 100%) 0% 0% no-repeat padding-box;
    }
    .yellow-gradiente-bg{
        background: transparent linear-gradient(33deg, #DBFC01 0%, #FFDD02 100%) 0% 0% no-repeat padding-box;
    }
    .green-gradiente-bg{
        background: transparent linear-gradient(33deg, #01AAFC 0%, #00FDC4 100%) 0% 0% no-repeat padding-box;
    }
    .transparent-bg{
        background: transparent;
    }
    /* add more classes if needed  */
</style>