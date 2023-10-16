<template>
    <div
        class="grid-container"
    >
        <div class="item1">
            {{ $t('bs-robot') }}
        </div>
        <div class="item2">
            <gravatar
                email="grey"
                status="false"
                size="32px"
                :name="$t('bs-robot')"
            />
        </div>
        <div class="item3 pr-1">
            <b-button
                variant="primary"
                size="sm"
                class="mr-1 mt-1 font-weight-bold"
                :key="idx"
                disabled
            >
                {{ message.text }}
            </b-button>
        </div>
        <div class="item4">{{ formatTime(created_at) }}</div>
    </div>
</template>

<script>
export default {
    props: {
        message: Object,
        formatTime: "",
        created_at: String
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
