<template>
    <div class="option__desc d-flex">
        <div style="width: fit-content; text-align: center" class="h-100 mr-2">
            <b-badge class="p-2">
                <template v-if="props.option.type == 'good_bad'">
                    <span class="bad material-icons-two-tone mr-2"> thumb_down </span>
                    <span class="good material-icons-two-tone"> thumb_up </span>
                </template>

                <template v-else-if="props.option.type == 'stars'">
                    <span class="material-icons-outlined text-warning"> star </span>
                    <span class="material-icons-outlined text-warning"> star </span>
                    <span class="material-icons-outlined text-warning"> star </span>
                    <span class="material-icons-outlined text-warning"> star_rate </span>
                    <span class="material-icons-outlined text-warning"> star_rate </span>
                </template>
            </b-badge>
        </div>
        <div>
            <span class="option__title">{{ props.option.title }}</span>
            <br>
            <small>
                <span class="option__small">{{ `[ ${props.option.desc} ]` }}</span>
            </small>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        props: {
            type: Object,
            default:  {}
        },
    },
}
</script>

<style scoped>
    .material-icons,
    .material-icons-outlined {
        font-size: 24px !important;
        color: #434343;
        cursor: pointer;
    }

    .good{
        color: #00C38E;
    }

    .bad {
        color: #FF4872;
    }

    .ok {
        color: #0080FC;
    }

    .good.material-icons-two-tone {
        filter: invert(61%) sepia(64%) saturate(1119%) hue-rotate(115deg) brightness(86%) contrast(101%);
    }

    .bad.material-icons-two-tone {
        filter: invert(47%) sepia(34%) saturate(6188%) hue-rotate(321deg) brightness(103%) contrast(103%);
    }

    .badge {
        background-color: #F3F7FF;
        box-shadow: 0px 3px 6px #00000029;
    }
</style>