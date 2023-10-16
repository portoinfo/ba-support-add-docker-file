<template>
  <b-col
    class="col-absolute"
    :class="{
      'hide-small': hideOnSmall,
      smaller: footerActiveChat,
      'h-100': !footerActiveChat,
    }"
  >
    <b-row class="ml-1 mr-1 col-chat h-100">
      <div class="card h-100 bg-transparent w-100">
        <!-- <div class="card-header bg-danger"></div> -->
        <div class="card-body shadow p-0" v-chat-scroll>
          <slot name="messages"></slot>
        </div>
        <div class="card-footer shadow mt-3 p-0" v-if="chat.status === 'IN_PROGRESS'">
          <div class="translator"
            :class="{
              bg_grey: !incognito_mode,
              bg_orange: incognito_mode
            }">
            <slot name="chat-buttons"></slot>
          </div>
          <div class="row h-100 w-100">
            <slot name="select"></slot>
            <div class="col">
              <div class="chat-txt">
                <textarea
                  id="input"
                  class="js-autoresize pl-3 pb-2 pt-1"
                  :placeholder="phTypeHere"
                  v-model="chat.content"
                  @keydown.enter.prevent="callSendMessage"
                  @keydown.tab.prevent="replace"
                />
              </div>
            </div>
            <div class="col col-btn-send">
              <slot name="sent"></slot>
            </div>
          </div>
        </div>
      </div>
    </b-row>
  </b-col>
</template>

<script>
export default {
  data() {
    return {
      hash: "98E549D9B6DA26962E7D1B2BEE8064918353F3CB979F9550C39F97B098DFD0DB",
      phTypeHere: this.$t('bs-type-here')+'...',
    };
  },
  props: {
    hideOnSmall: Boolean,
    incognito_mode: Boolean,
    incognito_id: String,
    chat: Object,
    sendMessage: "",
    footerActiveChat: "",
    departmentCommands: "",
  },
  methods: {
    callSendMessage() {
      this.sendMessage();
    },
    replace() {
      var res = "";
      var input = "";
      var last_word = "";
      var l1 = 0; //inicializo com 0, é a variavel q vai receber o length da palavra 1;
      var l2 = 0; //inicializo com 0, é a variavel q vai receber o length da palavra 2;

      input = this.chat.content; //recebe todo o conteudo do input;
      last_word = input.slice(input.lastIndexOf(" ") + 1); // pega a última palavra do input;

      last_word = this.hash + last_word; //coloca uma hash na ultima palavra (pra nao substituir outras palavras);

      l1 = last_word.length; // pega o tamanho da ultima palavra com hash

      /*
      Obs: o tamanho é para saber se a ultima palavra é somente o comando...
      Ex: se o usuário digitar o comando juntamente com outra string, nao pode substiuir a palavra no meio de outra
      */

      input = input.substring(0, input.lastIndexOf(" ")) + " " + last_word; //substiui a ultima palavra por ela mesma com a hash;

      // loop em todos os comandos (do settings do department)
      this.departmentCommands.forEach((element) => {
        element.command = this.hash + element.command; // o comando do settings é hasheado;
        l2 = element.command.length; // pega o tamanho do comando hasheado;

        //verifica se o comando hasheado está presente no input e se o tamanho é o mesmo;
        if (input.includes(element.command) && l1 === l2) {
          // recebe todo o input com o comando substituido pela descrição;
          res = input.replace(element.command, element.description);
          this.chat.content = res;
        }

        //tira a hash do comando (pra nao dar erro no próximo loop);
        var command_with_hash = element.command;
        var command_without_hash = command_with_hash.replace(this.hash, "");
        element.command = command_without_hash;
      });

    },
  },
};
</script>

<style scoped>
.col-btn-send {
  max-width: 35px !important;
  min-width: 35px !important;
  height: calc(100% - 60px);
}

.chat-txt {
  max-height: 245px;
  min-height: 44px;
}

.js-autoresize {
  font: normal normal normal 16px/20px Muli;
  letter-spacing: 0px;
  color: #707070;
  max-height: 245px;
  min-height: 64px;
  width: 100%;
  border: none;
  resize: none;
  background: transparent;
}
.card {
  border: none !important;
}

.card-body {
  overflow-y: auto !important;
}

.card-footer {
  border: none;
}

.shadow {
  background: #ffffff 0% 0% no-repeat padding-box;
  box-shadow: 0px 0px 9px #26242424;
  border-radius: 5px;
  opacity: 1;
}

.chat-txt {
  max-height: 445px;
  min-height: 44px;
}

#chat {
  min-height: 0px !important;
  max-height: 500px !important;
}

.card-chat-content {
  overflow: auto;
}

.translator {
  border-radius: 5px 5px 0px 0px;
  opacity: 1;
  height: 40px;
  padding-top: 8px;
  border: none;
  font: normal normal bold 13px/24px Muli;
  letter-spacing: 0px;
  color: #333333;
  text-transform: capitalize;
  opacity: 1;
}

.bg_grey {
  background: #f7f8fc 0% 0% no-repeat padding-box;
}

.bg_orange {
  background: #FF9F5F59 0% 0% no-repeat padding-box;
}


/* SCROLL */

::-webkit-scrollbar {
  width: unset;
}

::-webkit-scrollbar-track {
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #0294ff33;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #0296ff80;
}

/* .h-60 {
  position: absolute;
  height: 60% !important;
  min-height: 60% !important;
  max-height: 60% !important;
  width: calc(100% - 30px) !important;
}

.h-40 {
  position: absolute;
  bottom: 0;
  height: calc(40% - 20px) !important;
  max-height: calc(40% - 20px) !important;
  min-height: calc(40% - 20px) !important;
  width: calc(100% - 30px) !important;
} */

@media only screen and (max-width: 1367px) {
  /* .card-body {
    zoom: 95%;
  } */
}

@media only screen and (max-width: 1279px) {
  .hide-small {
    display: none !important;
  }
}

.smaller {
  height: calc(100% - 90px);
}

@media only screen and (max-width: 1367px) {
  .smaller {
    height: calc(100% - 72px);
  }
}
</style>
