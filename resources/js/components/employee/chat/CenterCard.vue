<template>
  <b-col v-show="showChat" class="ml-n3">
    <b-card show id="list" class="h-100 z80">
      <slot></slot>
    </b-card>
  </b-col>
</template>

<script>
export default {
    props: {
        showChat: Boolean,

    },
    methods: {

    }
};
</script>

<style scoped>
.card {
  box-shadow: 0px 0px 9px #26242424;
  border-radius: 5px;
  opacity: 1;
  border: none;
}
#list .card-body {
  padding: 0px;
}

#list {
  overflow: hidden;
}
</style>
