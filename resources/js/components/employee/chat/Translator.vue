<template>
  <div class="card-header translator">
    <span>
      <img src="images/icons/language.svg" />
      {{$t('bs-language-detected')}}
    </span>
    <b-form-select
      id="language"
      class="ml-5 mr-5 w-25"
      v-model="selected"
      :options="options"
    ></b-form-select>
    <b-button id="btn-discard">{{$t('bs-to-discard')}}</b-button>
    <b-button id="btn-translate">{{$t('bs-translate')}}</b-button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      selected: null,
      options: [{ value: null, text: this.$t('bs-portuguese') }],
    };
  },
};
</script>

<style scoped>
.translator {
  background: #f7f8fc 0% 0% no-repeat padding-box;
  opacity: 1;
  border: none;
  font: normal normal bold 16px/30px Muli;
  letter-spacing: 0px;
  color: #333333;
  text-transform: capitalize;
  opacity: 1;
}

#language {
  background-color: #f2f2f2;
  opacity: 1;
  font: normal normal normal 15px/18px Lato;
  letter-spacing: 0px;
  color: #434343;
  opacity: 1;
  border: none;
}

#btn-discard {
  font: normal normal 800 13px/16px Muli;
  letter-spacing: 0.39px;
  color: #5a5959;
  opacity: 1;
  background: transparent;
  border: none;
  text-transform: capitalize;
}

#btn-translate {
  background: #0080fc 0% 0% no-repeat padding-box;
  box-shadow: 0.62px 0.79px 2px #1e120d1a;
  border-radius: 5px;
  opacity: 1;
  font: normal normal 800 14px/16px Muli;
  letter-spacing: 0.42px;
  color: #f8fafd;
  text-transform: uppercase;
  border: none;
}
</style>
