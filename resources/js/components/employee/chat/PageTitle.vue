<template>
  <b-row class="ml-custom">
    <b-container fluid>
      <h1>
          <slot/>
      </h1>
    </b-container>
  </b-row>
</template>

<style scoped>
@media screen and (max-width: 1366px) and (min-width: 576px) {
  .ml-custom {
    margin-left: 8px;
  }
}

h1 {
  font: normal normal 800 25px/19px Muli;
  letter-spacing: 0px;
  color: #0080fc;
  opacity: 1;
}
</style>
