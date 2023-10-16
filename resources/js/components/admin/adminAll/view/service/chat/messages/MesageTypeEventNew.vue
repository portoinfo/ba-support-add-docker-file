<template>
    <div class="grid-container ba-mg-t-2 ba-mg-l-4">
        <div class="item3 pt-3 pb-3 pl-5">
            <center v-if="message.content == 'bs-the-chat-ended-due-to-inactivity'">
                {{ $t(message.content) }}
            </center>
            <center v-else>
                {{  $t(message.name) ? $t(message.name) : "" || $t(message.user_name) ? $t(message.user_name) : $t(message.client_name) }}: {{ $t(message.content) }}
            </center>
        </div>
        <!-- <div class="item4">{{ formatTime(message.created_at) }}</div> -->
    </div>
</template>

<script>
export default {
  props:{
    message: Object,
    formatTime: "",
  }
};
</script>

<style scoped>
.item3 {
    grid-area: content;
    color: #707070;
    font-size: 15px;
    font-stretch: 100%;
    font-weight: 700;
    text-rendering: optimizeLegibility;
    -webkit-font-feature-settings: "kern" 1;
    line-height: 19px;
    padding-bottom: 5px;
    padding-right: 5px;
    padding-left: 5px;
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
    grid-template-areas: "content time";
    grid-template-columns: auto 60px;
    border-top: 1px solid rgba(215, 222, 230, 0.2);
}

</style>
