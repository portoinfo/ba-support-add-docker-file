<template>
    <b-row class="mb-3 mt-3">
        <b-col cols="12" class="col-msg-name">
        <h4 class="msg-name m-0">
            {{ $t('bs-robot') }}
        </h4>
        </b-col>
        <b-col class="col-msg-img">
        <gravatar
            email="grey"
            status="false"
            size="53px"
            :name="$t('bs-robot')"
        />
        </b-col>
        <b-col class="col-msg-content">
        <div class="card bg-transparent">
            <div class="card-body cursor-pointer" v-linkified>
                {{ message.text }}
            </div>
            <div
                v-if="isDad"
                class="p-2 buttons-grid"
                :class="{
                    'grid-1' : message.children.length <= 3,
                    'grid-2' : message.children.length >= 4,
                }"
            >
                <template v-for="(item, idx) in message.children">
                    <b-button
                        v-if="item.type !== 'text'"
                        variant="primary"
                        size="sm"
                        class="mb-1 w-100 font-weight-bold"
                        :key="idx"
                        :id="`${chat_history[index]['ch_id']}_${idx}`"
                        @click=" updateMessageSelected(idx); $root.$refs.ChatClient.executeRobotAction(item, message.inputTime)"
                    >
                        {{ item.text }}
                    </b-button>
                </template>
            </div>
        </div>
        </b-col>
    </b-row>
</template>

<script>
export default {
    props: {
        message: Object,
        chat_history: Array,
        index: Number
    },
    computed: {
        isDad() {
            return this.message.children && this.message.children.length > 0;
        }
    },
    mounted () {
        if (this.isDad && this.$root.$refs.ChatClient.chat.status == 'ROBOT') {
            this.message.children.forEach(item => {
                if (item.type == 'text') {
                    let i = this.chat_history.findIndex(el => el.content.id == item.id);
                    if (i == -1) {
                        this.$root.$refs.ChatClient.executeRobotAction(this.message, this.message.inputTime)
                    }
                }
            });
        }
    },
    methods: {
        updateMessageSelected(i) {
            var vm = this;
            document.getElementById(`${vm.chat_history[vm.index]['ch_id']}_${i}`).disabled = true;
            vm.chat_history[vm.index].content.children[i]['selected'] = true;

            axios.post('chat-history/client/update',{
                ch_id: vm.chat_history[vm.index]['ch_id'],
                content: vm.chat_history[vm.index].content
            })
            .then(res => {})
        }
    },
}
</script>

<style scoped>
.card {
  border: none;
  border-radius: 0px;
  padding-bottom: 50px;
}
.col-msg-name {
  padding-left: 120px;
}
.msg-name {
  font: normal normal bold 15px/31px Muli;
  letter-spacing: 0px;
  color: #0294ff;
}
.col-msg-img {
  max-width: 100px !important;
  padding: 0px;
  display: flex;
  align-items: flex-end;
}
.col-msg-content .card {
  background: #f2f2f2 !important;
  border-radius: 14px !important;
  font: normal normal normal 16px/20px Muli;
  letter-spacing: 0px;
  color: #707070;
  opacity: 1;
  text-align: justify;
  justify-content: space-between;
  max-width: fit-content;
  padding: 0px;
}
.col-msg-content .card-footer {
  font: normal normal normal 16px/17px Muli;
  letter-spacing: 0px;
  color: #707070;
  min-width: 200px;
  font-style: italic;
}

.buttons-grid {
    display: grid;
    column-gap: 6px;
}

.buttons-grid.grid-1 {
    grid-template-columns: auto;
}

.buttons-grid.grid-2 {
    grid-template-columns: auto auto;
}

@media only screen and (max-width: 575px) {
  .col-msg-img {
    max-width: 60px !important;
  }
  .col-msg-name {
    padding-left: 80px;
  }
}
</style>

