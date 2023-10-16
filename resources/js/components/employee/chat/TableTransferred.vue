<template>
  <b-col class="ml-n3 card-progress" :class="{ 'hide-small': hideOnSmall, 'smaller': footerActiveChat, 'h-100': !footerActiveChat }">
    <b-card show id="list" class="h-100 z80">
      <table class="table table-borderless table-striped">
        <thead>
          <tr>
            <th scope="col" colspan="8"></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="bg-white td-title text-center" colspan="8">
              <!-- {{ $t("bs-transferred-s") }} -->
              {{ $t('bs-select-an-option-to-continue') }}
            </td>
          </tr>
        </tbody>
      </table>
    </b-card>
  </b-col>
</template>

<script>
export default {
  data() {
    return {
      ref: "tableTransferred",
      tz: "",
    };
  },
  props: {
    hideOnSmall: Boolean,
    footerActiveChat: ""
  },
};
</script>

<style scoped>
.card {
  /*tirar*/
  box-shadow: 0px 0px 9px #26242424;
  border-radius: 5px;
  opacity: 1;
  border: none;
}

.answered {
  background: #ff4872;
  color: #f4f4f4;
}

#list .card-body {
  padding: 0px;
}

tr {
  width: 100%;
  display: inline-table;
  table-layout: fixed;
}

table {
  height: 100%;
  display: -moz-groupbox;
  text-align: center;
  max-width: 100% !important;
  zoom: 90%;
}

tbody {
  height: 100%;
  width: 100%;
}

thead {
  background: #f7f8fc;
  border: none;
  font: normal normal bold 16px/30px Muli;
  letter-spacing: 0px;
  color: #333333;
  opacity: 1;
}

.table-striped tbody tr:nth-of-type(odd) {
  background-color: #fdfdfd;
}

.brlt {
  border-radius: 5px 0px 0px 0px;
}

.brrt {
  border-radius: 0px 5px 0px 0px;
}

.td-title {
  font: normal normal 800 15px/16px Muli;
  letter-spacing: 0.45px;
  color: #333333;
  opacity: 1;
  text-align: left;
}

td {
  border: 1px solid #dee3ea;
  border-top: none;
  height: 63px;
  vertical-align: middle;
  font: normal normal 600 16px/19px Lato;
  letter-spacing: 0px;
  color: #6e6e6e;
  opacity: 1;
}

table button {
  background: #f4f4f4 0% 0% no-repeat padding-box;
  border-radius: 5px;
  opacity: 1;
  border: none;
  font: normal normal 800 12px/16px Muli;
  letter-spacing: 0.42px;
  color: #434343;
  text-transform: uppercase;
  opacity: 1;
  width: 80px;
  padding: 10px;
}

/* SCROLL */

::-webkit-scrollbar {
  width: 10px;
  height: 10px;
}

::-webkit-scrollbar-track {
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #0294ff33;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #0294ff33;
}

tbody tr:hover {
  background-color: #f7f8fc !important;
  cursor: pointer;
}

.card-progress {
  position: absolute;
  padding-bottom: 115px;
  /* left: 380px; */
  margin-left: 300px !important;
  margin-right: 330px !important;
  width: auto !important;
}

@media only screen and (max-width: 1279px) {
  .card-progress {
    margin-right: 0px !important;
    left: auto;
    margin-left: 2px !important;
    padding-bottom: 115px;
  }

  .hide-small {
    display: none;
  }

  table {
    min-width: 991px !important;
  }
}

@media only screen and (max-width: 576px) {
  .card-progress {
    width: calc(100% - 5px) !important;
  }

  table {
    min-width: 991px !important;
  }
}

.smaller {
  height: calc(100% - 90px);
}
</style>
