<template>
  <b-col
    cols="2"
    class="card-tree"
    :class="{
      hidden: !hidden || !hideOnSmall,
      showed: hidden && hideOnSmall,
      smaller: footerActiveChat,
      'h-100': !footerActiveChat,
    }"
  >
    <b-card id="card-tree" class="h-100 z80">
      <ul class="list-group myUL">
        <li class="list-group-item pr-0 pl-0 br5 li-body pb-0">
          <div class="h-48">
            <div class="row level-1">
              <div class="col" style="max-width: 24px">
                <vue-material-icon name="keyboard_arrow_down" :size="24" />
              </div>
              <div class="col">{{ $t("bs-all-chats") }}</div>
            </div>
          </div>
          <ul class="list-group">
            <li
              id="list-active-chats"
              :class="{
                selected:
                  showTableQueue ||
                  showTableInProgress ||
                  showTableTransferred ||
                  chatstatus == 'IN_PROGRESS',
              }"
              class="list-group-item pl-0 pr-0 li-hover"
            >
              <div v-on:click="setArrowDirection" class="row level-2 caret">
                <div class="col" style="max-width: 24px">
                  <vue-material-icon :name="arrow" :size="24" />
                </div>
                <div class="col">{{ $t("bs-active-s") }}</div>
              </div>
              <ul class="list-group nested active">
                <li
                  @click="callShowTableComponent('queue')"
                  id="queue"
                  class="list-group-item li-hover mt-3"
                  :class="{ selected: showTableQueue }"
                >
                  <div class="row level-3">
                    <div class="col" style="max-width: 24px">
                      <vue-material-icon name="query_builder" :size="24" />
                    </div>
                    <div class="col">
                      {{
                        `${$t("bs-in-queue")} ${countOnQueue ? `(${countOnQueue})` : ""}`
                      }}
                      <span v-if="notify.onQueue" class="badge badge-primary badge-pill"
                        >1</span
                      >
                    </div>
                  </div>
                </li>
                <li
                  @click="callShowTableComponent('inProgress')"
                  class="list-group-item li-hover"
                  :class="{
                    selected: showTableInProgress || chatstatus == 'IN_PROGRESS',
                  }"
                >
                  <div class="row level-3">
                    <div class="col" style="max-width: 24px">
                      <vue-material-icon name="question_answer" :size="24" />
                    </div>
                    <div class="col">
                      {{
                        `${$t("bs-in-progress")} ${
                          countInProgress ? `(${countInProgress})` : ""
                        }`
                      }}
                      <span
                        v-if="notify.inProgress"
                        class="badge badge-primary badge-pill"
                        >1</span
                      >
                    </div>
                  </div>
                </li>
                <!--
                <li
                  class="list-group-item li-hover"
                  :class="{ selected: showTableTransferred }"
                  @click="callShowTableComponent('transferred')"
                >
                  <div class="row level-3">
                    <div class="col" style="max-width: 24px">
                      <vue-material-icon name="swap_horiz" :size="24" />
                    </div>
                    <div class="col">
                      {{
                        `${$t("bs-transferred-s")} ${
                          countTransferred ? `(${countTransferred})` : ""
                        }`
                      }}
                      <span
                        v-if="notify.transferred"
                        class="badge badge-primary badge-pill"
                        >1</span
                      >
                    </div>
                  </div>
                </li>
                -->
              </ul>
            </li>
            <!--
            <li
              class="list-group-item pl-0 pr-0 li-hover"
              @click="callShowTableComponent('closed')"
              :class="{ selected: showTableClosed || chatstatus == 'CLOSED' }"
            >
              <div class="row level-2">
                <div class="col" style="max-width: 24px">
                  <vue-material-icon name="done" :size="24" />
                </div>
                <div class="col">
                  {{ `${$t("bs-closed-s")} ${countClosed ? `(${countClosed})` : ""}` }}
                  <span v-if="notify.closed" class="badge badge-primary badge-pill"
                    >1</span
                  >
                </div>
              </div>
            </li>
            -->
            <li
              class="list-group-item pl-0 pr-0 li-hover"
              @click="callShowTableComponent('resolved')"
              :class="{ selected: showTableResolved || chatstatus == 'RESOLVED' }"
            >
              <div class="row level-2">
                <div class="col" style="max-width: 24px">
                  <vue-material-icon name="done_all" :size="24" />
                </div>
                <div class="col">
                  {{
                    `${$t("bs-finished-s")} ${countResolved ? `(${countResolved})` : ""}`
                  }}
                  <span v-if="notify.resolved" class="badge badge-primary badge-pill"
                    >1</span
                  >
                </div>
              </div>
            </li>
            <li
              class="list-group-item pl-0 pr-0 li-hover"
              @click="callShowTableComponent('canceled')"
              :class="{ selected: showTableCanceled || chatstatus == 'CANCELED' }"
            >
              <div class="row level-2">
                <div class="col" style="max-width: 24px">
                  <vue-material-icon name="cancel" :size="24" />
                </div>
                <div class="col">
                  {{
                    `${$t("bs-lost-s")} ${countCanceled ? `(${countCanceled})` : ""}`
                  }}
                  <span v-if="notify.canceled" class="badge badge-primary badge-pill"
                    >1</span
                  >
                </div>
              </div>
            </li>
            <!--
            <li class="list-group-item pl-0 pr-0 li-hover" @click="say">
              <div class="row level-2">
                <div class="col" style="max-width: 24px">
                  <vue-material-icon name="report_problem" :size="24" />
                </div>
                <div class="col">Perdidos (0)</div>
              </div>
            </li>
            <li class="list-group-item pl-0 pr-0 li-hover" @click="say">
              <div class="row level-2">
                <div class="col" style="max-width: 24px">
                  <vue-material-icon name="keyboard_arrow_right" :size="24" />
                </div>
                <div class="col">Chat de grupo (0)</div>
              </div>
            </li>
            -->
          </ul>
        </li>
      </ul>
    </b-card>
  </b-col>
</template>

<script>
export default {
  data() {
    return {
      arrow: "keyboard_arrow_down",
    };
  },
  props: {
    chatstatus: "",
    showTableComponent: "",
    showTableQueue: Boolean,
    showTableInProgress: Boolean,
    showTableTransferred: Boolean,
    showTableClosed: Boolean,
    showTableResolved: Boolean,
    showTableCanceled: Boolean,
    hidden: Boolean,
    hideOnSmall: Boolean,
    countOnQueue: "",
    countInProgress: "",
    countTransferred: "",
    countClosed: "",
    countResolved: "",
    countCanceled: "",
    notify: Object,
    footerActiveChat: "",
  },
  mounted() {
    this.tree();
    //this.openTree();
  },
  methods: {
    tree() {
      var toggler = document.getElementsByClassName("caret");
      var i;
      for (i = 0; i < toggler.length; i++) {
        toggler[i].addEventListener("click", function () {
          this.parentElement.querySelector(".nested").classList.toggle("active");
          this.classList.toggle("pb-0");
        });
      }
    },
    callShowTableComponent(table) {
      this.showTableComponent(table);
    },
    openTree() {
      //document.getElementById("all").click();
      //document.getElementById("active").click();
    },
    setArrowDirection() {
      var arrow_direction = this.arrow;
      if (arrow_direction == "keyboard_arrow_down") {
        this.arrow = this.arrow = "keyboard_arrow_right";
      } else {
        this.arrow = this.arrow = "keyboard_arrow_down";
      }
    },
    say() {
      alert(this.$t("bs-in-development-please-wait"));
    },
  },
};
</script>

<style scoped>
.card {
  background: #ffffff 0% 0% no-repeat padding-box;
  box-shadow: 0px 0px 9px #26242424;
  border-radius: 5px;
  opacity: 1;
  border: none;
}

.card-tree {
  min-width: 315px;
  max-width: 315px;
  position: absolute;
  padding-bottom: 115px;
}

@media only screen and (max-width: 1367px) {
  .card-tree {
    padding-bottom: 120px;
  }

  .smaller {
    height: calc(100% - 65px) !important;
  }
}

.smaller {
  height: calc(100% - 90px);
}

@media only screen and (max-width: 1279px) {
  .hidden {
    display: none;
  }
  .showed {
    display: unset;
  }
  .card-tree {
    min-width: calc(100% - 69px);
    max-width: calc(100% - 69px);
  }
}

@media only screen and (max-width: 576px) {
  .card-tree {
    min-width: calc(100% - 5px) !important;
    max-width: calc(100% - 5px) !important;
  }
}

#card-tree .card-body {
  padding: 0px;
}

ul,
.myUL {
  list-style-type: none;
  font: normal normal 600 15px/19px Muli;
  letter-spacing: 0px;
  color: #7c94b4;
  opacity: 1;
}

.caret {
  cursor: pointer;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

.caret::before {
  color: #a5b9d5;
}

.nested {
  display: none;
}

.active {
  display: block;
}

ul,
li {
  border-radius: 0px;
  border: none;
  cursor: pointer;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

.level-1 {
  margin-left: 9px;
}

.level-2 {
  margin-left: 34px;
}

.level-3 {
  margin-left: 40px;
}

.br5 {
  border-radius: 5px;
}

.li-hover:hover,
.selected {
  background-color: #0294ff33;
  color: #0080fc;
}

li a {
  height: 45px;
  display: table-cell;
  vertical-align: middle;
  float: right;
}

#list-active-chats {
  max-height: 160px;
}

.h-48 {
  height: 48px !important;
}

.li-body {
  border-radius: 5px 5px 0px 0px;
  background: #0296ff1e;
  color: #0080fc;
}

.badge {
  height: 10px;
  width: 10px;
  font-size: 1%;
  padding-top: 10px;
  margin-left: 13px;
}

.badge-danger {
  color: transparent;
}
</style>
