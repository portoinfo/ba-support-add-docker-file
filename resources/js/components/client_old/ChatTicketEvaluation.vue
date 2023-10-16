<template>

    <div v-if="type == 'good_bad'" class="mb-2 d-flex noselect">

        <div class="text-center mr-3 cursor-pointer bad" @click="setStars(1)" :class="{'selected': selectedDown}">
            <span class="material-icons-two-tone"> thumb_down </span>
        </div>

        <div class="text-center mr-3 cursor-pointer good" @click="setStars(5)" :class="{'selected': selectedUp}">
            <span class="material-icons-two-tone"> thumb_up </span>
        </div>

    </div>

    <div v-else-if="type == 'stars'" class="mb-2 noselect"> 
        <span 
            :key="star"
            v-for="star in maxStars"
            class="material-icons-outlined text-warning" 
            @click="setStars(star)"
        >
            {{ star <= stars ? 'star' : 'star_rate' }}
        </span>
    </div>

</template>

<script>
export default {   
    props: {
        module: {
            type: String,
            default: ''
        },
        type: {
            type: String,
            default: 'stars'
        },
        toEvaluate: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            stars: 0,
            maxStars: 5,
        }
    },
    created () {
        this.$root.$refs.ChatTicketEvaluation = this;
    },
    methods: {
        setStars(star) {   
            if (this.module ==  'chat') {
                if (this.type == 'good_bad') {
                    if (this.toEvaluate == 'attendant') {
                        this.$root.$refs.ChatClient.stars_atendent = star;
                    } else if (this.toEvaluate == 'service') {
                        this.$root.$refs.ChatClient.stars_service = star;
                    }
                } else if (this.type == 'stars') {
                    if (typeof star === "number" && star !== this.stars) {
                        if (star <= this.maxStars && star >= 0) {
                            this.stars = this.stars === star ? star - 1 : star;
                            if (this.toEvaluate == 'attendant') {
                                this.$root.$refs.ChatClient.stars_atendent = this.stars;
                            } else if (this.toEvaluate == 'service') {
                                this.$root.$refs.ChatClient.stars_service = this.stars;
                            }
                        }
                    }
                }
            } else if (this.module == 'ticket') {
                if (this.type == 'good_bad') {
                    if (this.toEvaluate == 'attendant') {
                        this.$root.$refs.TicketClient.stars_atendent = star;
                    } else if (this.toEvaluate == 'service') {
                        this.$root.$refs.TicketClient.stars_service = star;
                    }
                } else if (this.type == 'stars') {
                    if (typeof star === "number" && star !== this.stars) {
                        if (star <= this.maxStars && star >= 0) {
                            this.stars = this.stars === star ? star - 1 : star;
                            if (this.toEvaluate == 'attendant') {
                                this.$root.$refs.TicketClient.stars_atendent = this.stars;
                            } else if (this.toEvaluate == 'service') {
                                this.$root.$refs.TicketClient.stars_service = this.stars;
                            }
                        }
                    }
                }
            }
        }
    },
    computed: {
        selectedUp() {
            if (this.module == 'chat') {
                if (this.toEvaluate == 'attendant') {
                    return this.$root.$refs.ChatClient.stars_atendent === 5;
                } else if (this.toEvaluate == 'service') {
                    return this.$root.$refs.ChatClient.stars_service === 5;
                }
            } else if (this.module == 'ticket') {
                if (this.toEvaluate == 'attendant') {
                    return this.$root.$refs.TicketClient.stars_atendent === 5;
                } else if (this.toEvaluate == 'service') {
                    return this.$root.$refs.TicketClient.stars_service === 5;
                }
            }
        },
        selectedDown() {
            if (this.module == 'chat') {
                if (this.toEvaluate == 'attendant') {
                    return this.$root.$refs.ChatClient.stars_atendent === 1;
                } else if (this.toEvaluate == 'service') {
                    return this.$root.$refs.ChatClient.stars_service === 1;
                }
            } else if (this.module == 'ticket') {
                if (this.toEvaluate == 'attendant') {
                    return this.$root.$refs.TicketClient.stars_atendent === 1;
                } else if (this.toEvaluate == 'service') {
                    return this.$root.$refs.TicketClient.stars_service === 1;
                }
            }
        }, 
    },
}
</script>

<style scoped>
    .material-icons,
    .material-icons-outlined {
        font-size: 30px !important;
        color: #656565;
    }

    .good:hover span,
    .good:hover b {
        color: #00C38E !important;
    }

    .bad:hover span, 
    .bad:hover b {
        color: #FF4872 !important;
    }

    .ok:hover span, 
    .ok:hover b {
        color: #0080FC !important;
    }

    .selected.good .material-icons-two-tone,
    .good:hover .material-icons-two-tone {
        filter: invert(61%) sepia(64%) saturate(1119%) hue-rotate(115deg) brightness(86%) contrast(101%) !important;
    }

    .selected.bad .material-icons-two-tone,
    .bad:hover .material-icons-two-tone {
        filter: invert(47%) sepia(34%) saturate(6188%) hue-rotate(321deg) brightness(103%) contrast(103%) !important;
    }

    .material-icons-two-tone {
        font-size: 30px !important;
        filter: invert(42%) sepia(0%) saturate(0%) hue-rotate(233deg) brightness(93%) contrast(93%);
    }


    .badge {
        background-color: #F3F7FF;
        box-shadow: 0px 3px 6px #00000029;
    }

    span b {
        font: normal normal bold 14px/14px Muli;
        letter-spacing: 0px;
        color: #656565;
    }
</style>