<template>
  <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert" :class="{home: !showChat}">
    <strong>{{$t('bs-customer-attention')}},</strong> {{$t('bs-new-support-beta-version')}}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <!-- <div class="row bg-danger">
    <div class="col-12 w-100">
      <span
        >Atenção Cliente, o nosso novo sistema de suporte está em versão BETA, portanto,
        anomalias e instabilidades estão suscetíveis a ocorrer. Contamos com sua
        compreensão! Muito obrigado!</span
      >
    </div>
  </div> -->
</template>

<script>
export default {
  props: {
    showChat: Boolean,
  },
};
</script>

<style scoped>
.alert.home {
  max-width: calc(100% + 109px);
  min-width: calc(100% + 109px);
  margin-top: -8px;
  margin-left: -54px;
}

.alert {
  border-radius: 0px;
  border: none;
  background: #ed1b24;
  color: white;
}

@media only screen and (max-width: 576px) {
  .alert.home {
    max-width: calc(100% + 50px) !important;
    min-width: calc(100% + 50px) !important;
    margin-left: -25px !important;
  }
}
</style>
