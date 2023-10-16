<template>
    <div
        :class="[!open ? 'burger-icon' : 'burger-icon-open']"
        :style="{ width: width, height: height }"
    >
        <div class="burger-bar" :style="{ backgroundColor: color }"></div>
        <div class="burger-bar" :style="{ backgroundColor: color }"></div>
        <div class="burger-bar" :style="{ backgroundColor: color }"></div>
    </div>
</template>

<script>
    export default {
        name: 'burger-icon',
        props: {
            width: {
                type: String,
                default: '24px'
            },
            height: {
                type: String,
                default: '15px'
            },
            color: {
                type: String,
                default: '#FFF'
            },
            open: {
                type: Boolean,
                default: false
            }
        }
    }
</script>

<style lang="scss" scoped>
    .burger-icon {
        display: grid;
        grid-template-rows: repeat(3, auto);
        grid-template-columns: auto;
        justify-content: stretch;
        justify-items:  stretch;
        align-items: stretch;
        align-content: stretch;
        row-gap: 20%;
        margin: 5px;
        
        .burger-bar{
            height: 100%;
            width: 100%;
            transition: all 0.2s ease;
        }
        .burger-bar:first-child{
            justify-self: start;
        }
        .burger-bar:last-child{
            justify-self: end;
        }
    }
    .burger-icon-open{
        display: grid;
        grid-template-rows: repeat(3, auto);
        grid-template-columns: auto;
        justify-content: stretch;
        justify-items:  stretch;
        align-items: stretch;
        align-content: stretch;
        row-gap: 20%;
        
        .burger-bar{
            height: 100%;
            width: 100%;
            transition: all 0.2s ease;
        }
        .burger-bar:first-child{
            justify-self: start;
            width: 50%;
        }
        .burger-bar:last-child{
            justify-self: end;
            width: 50%;
        }
    }
</style>