<template>
  <div class="h-100 zoom-80" :class="{'with-tab': numberOfTabs >= 1 }">
    <b-row class="ml-custom mobile">
      <b-container fluid>
        <!-- <div
          v-if="isMobile && !showMenuMobile && !showCreateTicket && !showTicket"
          @click="goBackToMenu()"
          class="p-0 bg-transparent border-0 text-primary btn-back"
        >
          <i class="material-icons" style="font-size: 40px">keyboard_arrow_left</i>
          {{ $t("bs-back").toUpperCase() }}
        </div> -->
        <h1>
          <span
            v-if="isMobile && !showMenuMobile && !showCreateTicket && !showTicket"
            @click="goBackToMenu()"
            class="p-0 bg-transparent border-0 text-primary btn-back"
          >
            <i class="material-icons" style="font-size: 40px">keyboard_arrow_left</i>
            {{ $t("bs-back").toUpperCase() }}
          </span>
          <template v-if="isMobile && !showMenuMobile && !showCreateTicket && !showTicket">
              &nbsp;|&nbsp;
          </template>
          {{ title }}
        </h1>
        <!-- {{restriction}} -->
      </b-container>
    </b-row>
    <!-- <b-row
      v-show="isMobile && !showMenuMobile && !showCreateTicket && !showTicket"
      class="mobile pt-2"
      cols="1"
    >
      <b-col>
        <div
          @click="goBackToMenu()"
          class="p-0 bg-transparent border-0 text-primary btn-back"
        >
          <i class="material-icons" style="font-size: 40px">keyboard_arrow_left</i
          >{{ $t("bs-back").toUpperCase() }}
        </div>
      </b-col>
    </b-row> -->
    <b-row v-show="showTree" class="h-100 pt-2 ml-custom mobile">
      <b-col class="left-sidebar" v-show="!isMobile || (isMobile && showMenuMobile)">
        <b-card id="card-tree" class="h-100">
          <ul class="list-group myUL">
            <li class="list-group-item pr-0 pl-0 pb-0 br5 li-body">
              <div class="level-1 h-48">
                <i class="material-icons local_arrow">keyboard_arrow_down</i>&nbsp;{{
                  $t("bs-all-tickets")
                }}
              </div>
              <ul class="list-group">
                <li
                  :class="[
                    'list-group-item p-0 li-hover',
                    isSelectedDropdownActive ? 'selected' : '',
                  ]"
                >
                  <div
                    @click="treeActiveTickets"
                    id="active"
                    class="caret level-2 list-group-item-header"
                  >
                    <i v-if="showdrop_ActiveTickets" class="material-icons local_arrow"
                      >keyboard_arrow_down</i
                    >
                    <i v-else class="material-icons local_arrow">keyboard_arrow_right</i>
                    &nbsp;{{ $t("bs-active") }}&nbsp;{{
                      tickets.length > 0 ? `(${tickets.length})` : ""
                    }}
                  </div>
                  <ul v-show="showdrop_ActiveTickets" class="list-group">
                    <li
                      @click="markAsActive(0)"
                      :class="[
                        'list-group-item caret li-hover',
                        activeMenuControls.active.opened ? 'selected' : '',
                      ]"
                    >
                      <span class="level-3">
                        <i class="material-icons local_arrow">query_builder</i>&nbsp;{{
                          $t("bs-opened")
                        }}&nbsp;{{ countfixed.open > 0 ? `(${countfixed.open})` : "" }}
                      </span>
                      <span v-if="notify.onQueue" class="badge badge-primary badge-pill"
                        >on</span
                      >
                    </li>
                    <li
                      @click="markAsActive(1)"
                      :class="[
                        'list-group-item caret li-hover',
                        activeMenuControls.active.inProgress ? 'selected' : '',
                      ]"
                    >
                      <span class="level-3">
                        <i class="material-icons local_arrow">question_answer</i>&nbsp;{{
                          $t("bs-in-progress")
                        }}
                        &nbsp;{{
                          countfixed.in_progress > 0 ? `(${countfixed.in_progress})` : ""
                        }}
                      </span>
                      <span
                        v-if="notify.inProgress"
                        class="badge badge-primary badge-pill"
                        >on</span
                      >
                    </li>
                    <!--  <span v-if="restriction[0].ticket_alllist == 1">
                      <li @click="markAsActive(8);" :class="['list-group-item caret li-hover', activeMenuControls.active.alltickets ? 'selected' : '']">
                        <span class="level-3">
                          <i class="material-icons local_arrow">confirmation_number</i>&nbsp; Todos os tickets &nbsp;
                        </span>
                      </li>
                    </span> -->
                    <li
                      @click="markAsActive(2)"
                      :class="[
                        'list-group-item caret li-hover',
                        activeMenuControls.active.overdue ? 'selected' : '',
                      ]"
                    >
                      <span class="level-3">
                        <i class="material-icons local_arrow">report_problem</i>&nbsp;{{
                          $t("bs-overdue")
                        }}
                        &nbsp;{{
                          countfixed.overdue > 0 ? `(${countfixed.overdue})` : ""
                        }}
                      </span>
                    </li>
                  </ul>
                </li>
                <span v-if="restriction[0].ticket_close == 1">
                  <li
                    @click="markAsActive(3)"
                    :class="[
                      'list-group-item pl-0 pr-0 li-hover',
                      activeMenuControls.closed ? 'selected' : '',
                    ]"
                  >
                    <span class="caret level-2">
                      <i class="material-icons local_arrow">done</i>&nbsp;{{
                        $t("bs-closed-s")
                      }}&nbsp;{{ countfixed.closed > 0 ? `(${countfixed.closed})` : "" }}
                    </span>
                    <span v-if="notify.closed" class="badge badge-primary badge-pill"
                      >on</span
                    >
                  </li>
                </span>
                <li
                  @click="markAsActive(4)"
                  :class="[
                    'list-group-item pl-0 pr-0 li-hover',
                    activeMenuControls.resolved ? 'selected' : '',
                  ]"
                >
                  <span class="caret level-2">
                    <i class="material-icons local_arrow">done_all</i>&nbsp;
                    <span v-if="restriction[0].ticket_close == 1">
                      {{ $t("bs-resolved-s") }}
                    </span>
                    <span v-else>
                      {{ $t("bs-finalized-s") }}
                    </span>
                    &nbsp;{{ countfixed.resolved > 0 ? `(${countfixed.resolved})` : "" }}
                  </span>
                  <span v-if="notify.resolved" class="badge badge-primary badge-pill"
                    >on</span
                  >
                </li>
                <li
                  @click="markAsActive(5)"
                  :class="[
                    'list-group-item pl-0 pr-0 li-hover',
                    activeMenuControls.canceled ? 'selected' : '',
                  ]"
                >
                  <span class="caret level-2">
                    <i class="material-icons local_arrow">cancel</i>&nbsp;{{
                      $t("bs-canceled-s")
                    }}&nbsp;{{
                      countfixed.canceled > 0 ? `(${countfixed.canceled})` : ""
                    }}
                  </span>
                  <span v-if="notify.canceled" class="badge badge-primary badge-pill"
                    >on</span
                  >
                </li>
                <li
                  @click="markAsActive(9)"
                  :class="[
                    'list-group-item pl-0 pr-0 li-hover',
                    activeMenuControls.filters ? 'selected' : '',
                  ]"
                >
                  <span class="caret level-2">
                    <i class="material-icons local_arrow">filter</i>
                    &nbsp;Filtros&nbsp;
                    {{ countfixed.filters > 0 ? `(${countfixed.filters})` : "" }}
                  </span>
                  <span v-if="notify.canceled" class="badge badge-primary badge-pill"
                    >on</span
                  >
                </li>
                <!--
                 <li :class="['list-group-item p-0 li-hover', isSelectedDropdownMyTickets ? 'selected' : '']">

					<div  @click="treeMyTickets" id="active" class="caret level-2 list-group-item-header">
						<i v-if="showdrop_MyTickets" class="material-icons local_arrow">keyboard_arrow_down</i>
						<i v-else class="material-icons local_arrow">keyboard_arrow_right</i>
						&nbsp;{{$t('bs-my-tickets')}}
					</div>
					<ul v-show="showdrop_MyTickets" class="list-group">
						<li @click="markAsActive(6);" :class="['list-group-item caret li-hover', activeMenuControls.myTickets.myActiveTickets ? 'selected' : '']">
							<span class="level-3">
								<i class="material-icons local_arrow">keyboard_arrow_right</i>&nbsp;{{$t('bs-my-active-tickets')}}&nbsp;({{countfixed.mytickets}})
							</span>
						</li>
						<li @click="markAsActive(7);" :class="['list-group-item caret li-hover', activeMenuControls.myTickets.myActiveGroups ? 'selected' : '']">
							<span class="level-3">
								<i class="material-icons local_arrow">keyboard_arrow_right</i>&nbsp;{{$t('bs-my-active-groups')}}&nbsp;(0)
							</span>
						</li>
					</ul>
				</li> -->
                <li
                  @click="openCreateTicket"
                  class="list-group-item p-0 customOpen list-group-item-header"
                >
                  <div class="caret level-2">
                    <i class="bbi bbi-message-more-white bbi-28"></i>&nbsp;
                    <span>{{ $t("bs-create-new-ticket") }}</span>
                  </div>
                </li>
              </ul>
            </li>
          </ul>
        </b-card>
      </b-col>

      <b-col
        v-show="
          (isMobile && !showMenuMobile && showListTicket) || (!isMobile && showListTicket)
        "
        class="ml-n3 h-90"
      >
        <b-col class="col-btn-back">
          <b-button
            class="p-0 bg-transparent border-0 text-primary"
            @click="goBackToMenu"
          >
            <vue-material-icon name="keyboard_arrow_left" :size="40" />
          </b-button>
        </b-col>
        <multiselect
          id="departments"
          v-model="company_department"
          track-by="name"
          label="name"
          deselect-label=""
          selectLabel=""
          :selectedLabel="$t('bs-selected')"
          deselectGroupLabel=""
          selectGroupLabel=""
          group-values="options"
          group-label="title"
          :group-select="true"
          :multiple="true"
          :placeholder="$t('bs-filter-by-department')"
          :options="departments"
          :searchable="false"
          :allow-empty="false"
          :limit="5"
          class="mb-1"
        >
        </multiselect>

        <b-card show id="list" class="h-100">
          <table class="table table-borderless table-striped">
            <thead>
              <tr v-if="activeMenuControls.active.opened">
                <th scope="col" class="brlt">#</th>
                <th scope="col" class="brrt">{{ $t("bs-client") }}</th>
                <th scope="col" class="brlt">{{ $t("bs-waiting-time") }}</th>
                <th scope="col">{{ $t("bs-opening") }}</th>
                <th scope="col" class="brrt">{{ $t("bs-department") }}</th>
              </tr>
              <tr v-else-if="activeMenuControls.active.inProgress">
                <th scope="col" class="brlt">#</th>
                <th scope="col" class="brrt">{{ $t("bs-client") }}</th>
                <th scope="col">{{ $t("bs-opening") }}</th>
                <th scope="col">{{ $t("bs-duration") }}</th>
                <th scope="col">{{ $t("bs-department") }}</th>
                <th scope="col" class="brrt">{{ $t("bs-operator") }}</th>
              </tr>
              <tr v-else-if="activeMenuControls.active.overdue">
                <th scope="col" class="brlt">#</th>
                <th scope="col" class="brrt">{{ $t("bs-client") }}</th>
                <th scope="col">{{ $t("bs-opening") }}</th>
                <th scope="col">{{ $t("bs-duration") }}</th>
                <th scope="col">{{ $t("bs-department") }}</th>
                <th scope="col" class="brrt">{{ $t("bs-operator") }}</th>
              </tr>
              <tr v-else-if="activeMenuControls.active.alltickets">
                <th scope="col" class="brlt">#</th>
                <th scope="col" class="brrt">{{ $t("bs-client") }}</th>
                <th scope="col" class="brlt">Status</th>
                <th scope="col">{{ $t("bs-opening") }}</th>
                <th scope="col">{{ $t("bs-duration") }}</th>
                <th scope="col">{{ $t("bs-department") }}</th>
                <th scope="col" class="brrt">{{ $t("bs-operator") }}</th>
              </tr>
              <tr v-else-if="activeMenuControls.closed">
                <th scope="col" class="brlt">#</th>
                <th scope="col" class="brrt">{{ $t("bs-client") }}</th>
                <th scope="col">{{ $t("bs-department") }}</th>
                <th scope="col">{{ $t("bs-start") }}</th>
                <th scope="col">{{ $t("bs-end") }}</th>
                <th scope="col">{{ $t("bs-duration") }}</th>
              </tr>
              <tr v-else-if="activeMenuControls.resolved">
                <th scope="col" class="brlt">#</th>
                <th scope="col" class="brrt">{{ $t("bs-client") }}</th>
                <th scope="col">{{ $t("bs-department") }}</th>
                <th scope="col">{{ $t("bs-start") }}</th>
                <th scope="col">{{ $t("bs-end") }}</th>
                <th scope="col">{{ $t("bs-duration") }}</th>
              </tr>
              <tr v-else-if="activeMenuControls.canceled">
                <th scope="col" class="brlt">#</th>
                <th scope="col" class="brrt">{{ $t("bs-client") }}</th>
                <th scope="col">{{ $t("bs-department") }}</th>
                <th scope="col">{{ $t("bs-start") }}</th>
                <th scope="col">{{ $t("bs-end") }}</th>
                <th scope="col">{{ $t("bs-duration") }}</th>
              </tr>
              <tr v-else-if="activeMenuControls.filters">
                <th scope="col" class="brlt">#</th>
                <th scope="col" class="brrt">{{ $t("bs-client") }}</th>
                <th scope="col">{{ $t("bs-opening") }}</th>
                <th scope="col">{{ $t("bs-duration") }}</th>
                <th scope="col">{{ $t("bs-department") }}</th>
                <th scope="col" class="brrt">{{ $t("bs-operator") }}</th>
              </tr>
              <tr v-else-if="activeMenuControls.myTickets.myActiveTickets">
                <th scope="col" class="brlt">#</th>
                <th scope="col" class="brrt">{{ $t("bs-client") }}</th>
                <th scope="col">{{ $t("bs-opening") }}</th>
                <th scope="col">{{ $t("bs-duration") }}</th>
                <th scope="col">{{ $t("bs-department") }}</th>
                <th scope="col" class="brrt">{{ $t("bs-operator") }}</th>
              </tr>
              <tr v-else-if="activeMenuControls.myTickets.myActiveGroups">
                <th scope="col" class="brlt">#</th>
                <th scope="col" class="brrt">{{ $t("bs-client") }}</th>
                <th scope="col">{{ $t("bs-opening") }}</th>
                <th scope="col">{{ $t("bs-duration") }}</th>
                <th scope="col">{{ $t("bs-department") }}</th>
                <th scope="col" class="brrt">{{ $t("bs-operator") }}</th>
              </tr>
            </thead>
            <tbody>
              <!-- SWITCH SUBTITLES -->
              <tr v-if="activeMenuControls.active.opened">
                <td class="bg-white td-title" colspan="8">
                  {{ $t("bs-opened") }}
                </td>
              </tr>
              <tr v-else-if="activeMenuControls.active.inProgress">
                <td class="bg-white td-title" colspan="8">
                  {{ $t("bs-in-progress") }}
                </td>
              </tr>
              <tr v-else-if="activeMenuControls.active.overdue">
                <td class="bg-white td-title" colspan="8">
                  {{ $t("bs-overdue") }}
                </td>
              </tr>
              <tr v-else-if="activeMenuControls.active.alltickets">
                <td class="bg-white td-title" colspan="8">{{$t('bs-all-tickets')}}</td>
              </tr>
              <tr v-else-if="activeMenuControls.closed">
                <td class="bg-white td-title" colspan="8">
                  {{ $t("bs-closed-s") }}
                </td>
              </tr>
              <tr v-else-if="activeMenuControls.resolved">
                <td class="bg-white td-title" colspan="8">
                  {{ $t("bs-resolved-s") }}
                </td>
              </tr>
              <tr v-else-if="activeMenuControls.canceled">
                <td class="bg-white td-title" colspan="8">
                  {{ $t("bs-canceled-s") }}
                </td>
              </tr>
              <tr v-else-if="activeMenuControls.filters">
                <td class="bg-white td-title" colspan="8">{{$t('bs-filters')}}</td>
              </tr>
              <tr v-else-if="activeMenuControls.myTickets.myActiveTickets">
                <td class="bg-white td-title" colspan="8">
                  {{ $t("bs-my-active-tickets") }}
                </td>
              </tr>
              <tr v-else-if="activeMenuControls.myTickets.myActiveGroups">
                <td class="bg-white td-title" colspan="8">
                  {{ $t("bs-my-active-groups") }}
                </td>
              </tr>

              <!-- SWITCH COLUMNS DATA -->
              <template v-for="(item, key) in tickets">
                <!-- <td><b-button style="min-width:150px;"size="sm" variant="ligth" @click="openTicket(item)">{{ filterType(item.type) }} {{filterStatus(item.status)}}</b-button></td> -->

                <!-- OPENED  -->
                <tr
                  v-if="activeMenuControls.active.opened"
                  :key="key"
                  class="caret"
                  @click="viewTicket(item)"
                >
                  <td>
                    <b-button size="sm" @click="openTicket(item)">{{
                      $t("bs-take").toUpperCase()
                    }}</b-button
                    >&nbsp; #{{ item.id }}
                  </td>
                  <td>{{ $t(item.name_created) }}</td>
                  <td>
                    <span :id="'time-elapsed-queue-' + item.id">
                      {{
                        calculateWaitingTime(
                          UTCtoClientTZ(item.created_at, tz, "YYYY-MM-DD HH:mm:ss"),
                          "time-elapsed-queue-" + item.id
                        )
                      }}
                    </span>
                  </td>
                  <td>{{ UTCtoClientTZ2_translated(item.created_at, tz) }}</td>
                  <td v-if="item.department_type == 'builderall-mentor'">
                    {{ $t(item.department) }} <img src="/images/icons/icon_vip.svg" height="20" alt="">
                  </td>
                  <td v-else>
                    {{ $t(item.department) }}
                  </td>
                </tr>

                <!-- IN PROGRESS  -->
                <tr
                  v-else-if="activeMenuControls.active.inProgress"
                  :key="key"
                  class="caret"
                  @click="viewTicket(item)"
                >
                  <!-- v-bind:class="{ answered: item.answered }" -->
                  <td v-bind:class="{ answered: item.answered }">
                    <b-button size="sm" @click="openTicket(item)">{{
                      $t("bs-open").toUpperCase()
                    }}</b-button
                    >&nbsp; #{{ item.id }}
                  </td>
                  <td v-bind:class="{ answered: item.answered }">
                    {{ item.name_created }}
                  </td>
                  <td v-bind:class="{ answered: item.answered }">
                    {{ UTCtoClientTZ2_translated(item.created_at, tz) }}
                  </td>
                  <td
                    v-bind:class="{ answered: item.answered }"
                    :id="'time-elapsed-progress-' + item.id"
                  >
                    {{
                      calculateWaitingTime(
                        UTCtoClientTZ(item.created_at, tz, "YYYY-MM-DD HH:mm:ss"),
                        "time-elapsed-progress-" + item.id
                      )
                    }}
                  </td>
                  <td v-bind:class="{ answered: item.answered }" v-if="item.department_type == 'builderall-mentor'">
                    {{ $t(item.department) }} <img src="/images/icons/icon_vip.svg" height="20" alt="">
                  </td>
                  <td v-bind:class="{ answered: item.answered }" v-else>
                    {{ $t(item.department) }}
                  </td>
                  <td v-bind:class="{ answered: item.answered }">{{ item.name }}</td>
                </tr>

                <!-- OVERDUE  -->
                <tr
                  v-else-if="activeMenuControls.active.overdue"
                  :key="key"
                  class="caret"
                  @click="viewTicket(item)"
                >
                  <!-- v-bind:class="{ answered: item.answered }" -->
                  <td v-bind:class="{ answered: item.answered }">
                    <b-button size="sm" @click="openTicket(item)">{{
                      $t("bs-open").toUpperCase()
                    }}</b-button
                    >&nbsp; #{{ item.id }}
                  </td>
                  <td v-bind:class="{ answered: item.answered }">
                    {{ $t(item.name_created) }}
                  </td>
                  <td v-bind:class="{ answered: item.answered }">
                    {{ UTCtoClientTZ2_translated(item.created_at, tz) }}
                  </td>
                  <td
                    v-bind:class="{ answered: item.answered }"
                    :id="'time-elapsed-overdue-' + item.id"
                  >
                    {{
                      calculateWaitingTime(
                        UTCtoClientTZ(item.created_at, tz, "YYYY-MM-DD HH:mm:ss"),
                        "time-elapsed-overdue-" + item.id
                      )
                    }}
                  </td>
                  <td v-bind:class="{ answered: item.answered }" v-if="item.department_type == 'builderall-mentor'">
                    {{ $t(item.department) }} <img src="/images/icons/icon_vip.svg" height="20" alt="">
                  </td>
                  <td v-bind:class="{ answered: item.answered }" v-else>
                    {{ $t(item.department) }}
                  </td>
                  <td v-bind:class="{ answered: item.answered }">{{ item.name }}</td>
                </tr>
                <!-- OVERDUE  -->

                <tr
                  v-else-if="activeMenuControls.active.alltickets"
                  :key="key"
                  class="caret"
                  @click="viewTicket(item)"
                >
                  <!-- v-bind:class="{ answered: item.answered }" -->
                  <td>
                    <b-button size="sm" @click="openTicket(item)">{{
                      $t("bs-open").toUpperCase()
                    }}</b-button
                    >&nbsp; #{{ item.id }}
                  </td>
                  <td>{{ $t(item.name_created) }}</td>
                  <td>{{ filterStatus(item.status) }}</td>
                  <td>{{ UTCtoClientTZ2_translated(item.created_at, tz) }}</td>
                  <td :id="'time-elapsed-alltickets-' + item.id">
                    {{
                      calculateWaitingTime(
                        UTCtoClientTZ(item.created_at, tz, "YYYY-MM-DD HH:mm:ss"),
                        "time-elapsed-alltickets-" + item.id
                      )
                    }}
                  </td>
                  <td v-if="item.department_type == 'builderall-mentor'">
                    {{ $t(item.department) }} <img src="/images/icons/icon_vip.svg" height="20" alt="">
                  </td>
                  <td v-else>
                    {{ $t(item.department) }}
                  </td>
                  <td>{{ item.name }}</td>
                </tr>

                <!-- CLOSED -->
                <tr
                  v-else-if="activeMenuControls.closed"
                  :key="key"
                  class="caret"
                  @click="viewTicket(item)"
                >
                  <!-- v-bind:class="{ answered: item.answered }" -->
                  <td>
                    <b-button size="sm" @click="openTicket(item)">{{
                      $t("bs-to-see").toUpperCase()
                    }}</b-button
                    >&nbsp; #{{ item.id }}
                  </td>
                  <td>{{ $t(item.name_created) }}</td>
                  <td v-if="item.department_type == 'builderall-mentor'">
                    {{ $t(item.department) }} <img src="/images/icons/icon_vip.svg" height="20" alt="">
                  </td>
                  <td v-else>
                    {{ $t(item.department) }}
                  </td>
                  <td v-html="format_LT(item.created_at)"></td>
                  <td v-html="format_LT(item.updated_at)"></td>
                  <td v-html="diffTime(item.created_at, item.updated_at)"></td>
                </tr>

                <!-- RESOLVED -->
                <tr
                  v-else-if="activeMenuControls.resolved"
                  :key="key"
                  d
                  class="caret"
                  @click="viewTicket(item)"
                >
                  <!-- v-bind:class="{ answered: chat.answered }" -->
                  <td>
                    <b-button size="sm" @click="openTicket(item)">{{
                      $t("bs-to-see").toUpperCase()
                    }}</b-button
                    >&nbsp; #{{ item.id }}
                  </td>
                  <td>{{ $t(item.name_created) }}</td>
                  <td v-if="item.department_type == 'builderall-mentor'">
                    {{ $t(item.department) }} <img src="/images/icons/icon_vip.svg" height="20" alt="">
                  </td>
                  <td v-else>
                    {{ $t(item.department) }}
                  </td>
                  <td v-html="format_LT(item.created_at)"></td>
                  <td v-html="format_LT(item.updated_at)"></td>
                  <td v-html="diffTime(item.created_at, item.updated_at)"></td>
                </tr>

                <!-- CANCELED -->
                <tr
                  v-else-if="activeMenuControls.canceled"
                  :key="key"
                  class="caret"
                  @click="viewTicket(item)"
                >
                  <!-- v-bind:class="{ answered: chat.answered }" -->
                  <td>
                    <b-button size="sm" @click="openTicket(item)">{{
                      $t("bs-to-see").toUpperCase()
                    }}</b-button
                    >&nbsp; #{{ item.id }}
                  </td>
                  <td>{{ $t(item.name_created) }}</td>
                  <td v-if="item.department_type == 'builderall-mentor'">
                    {{ $t(item.department) }} <img src="/images/icons/icon_vip.svg" height="20" alt="">
                  </td>
                  <td v-else>
                    {{ $t(item.department) }}
                  </td>
                  <td v-html="format_LT(item.created_at)"></td>
                  <td v-html="format_LT(item.updated_at)"></td>
                  <td v-html="diffTime(item.created_at, item.updated_at)"></td>
                </tr>

                <!-- FILTERS  -->
                <tr
                  v-else-if="activeMenuControls.filters"
                  :key="key"
                  class="caret"
                  @click="viewTicket(item)"
                >
                  <!-- v-bind:class="{ answered: item.answered }" -->
                  <td v-bind:class="{ answered: item.answered }">
                    <b-button size="sm" @click="openTicket(item)">{{
                      $t("bs-open").toUpperCase()
                    }}</b-button
                    >&nbsp; #{{ item.id }}
                  </td>
                  <td v-bind:class="{ answered: item.answered }">
                    {{ $t(item.name_created) }}
                  </td>
                  <td v-bind:class="{ answered: item.answered }">
                    {{ UTCtoClientTZ2_translated(item.created_at, tz) }}
                  </td>
                  <td
                    v-bind:class="{ answered: item.answered }"
                    :id="'time-elapsed-progress-' + item.id"
                  >
                    {{
                      calculateWaitingTime(
                        UTCtoClientTZ(item.created_at, tz, "YYYY-MM-DD HH:mm:ss"),
                        "time-elapsed-progress-" + item.id
                      )
                    }}
                  </td>
                  <td v-bind:class="{ answered: item.answered }" v-if="item.department_type == 'builderall-mentor'">
                    {{ $t(item.department) }} <img src="/images/icons/icon_vip.svg" height="20" alt="">
                  </td>
                  <td v-bind:class="{ answered: item.answered }" v-else>
                    {{ $t(item.department) }}
                  </td>
                  <td v-bind:class="{ answered: item.answered }">{{ item.name }}</td>
                </tr>

                <!-- My Active Tickets -->
                <tr
                  v-else-if="activeMenuControls.myTickets.myActiveTickets"
                  :key="key"
                  class="caret"
                  @click="viewTicket(item)"
                >
                  <!-- v-bind:class="{ answered: chat.answered }" -->
                  <td>
                    <b-button size="sm" @click="openTicket(item)">{{
                      $t("bs-open").toUpperCase()
                    }}</b-button
                    >&nbsp; #{{ item.id }}
                  </td>
                  <td>{{ $t(item.name_created) }}</td>
                  <td>{{ UTCtoClientTZ2_translated(item.created_at, tz) }}</td>
                  <td :id="'time-elapsed-my-active-tickets-' + item.id">
                    {{
                      calculateWaitingTime(
                        UTCtoClientTZ(item.created_at, tz, "YYYY-MM-DD HH:mm:ss"),
                        "time-elapsed-my-active-tickets-" + item.id
                      )
                    }}
                  </td>
                  <td>{{ $t(item.department) }}</td>
                  <td>{{ item.name }}</td>
                </tr>

                <!-- My Active Groups -->
                <tr
                  v-else-if="activeMenuControls.myTickets.myActiveGroups"
                  :key="key"
                  class="caret"
                  @click="viewTicket(item)"
                >
                  <!-- v-bind:class="{ answered: chat.answered }" -->
                  <td>
                    <b-button size="sm" @click="openTicket(item)">{{
                      $t("bs-open").toUpperCase()
                    }}</b-button
                    >&nbsp; #{{ item.id }}
                  </td>
                  <td>{{ $t(item.name_created) }}</td>
                  <td>{{ UTCtoClientTZ2_translated(item.created_at, tz) }}</td>
                  <td :id="'time-elapsed-my-active-groups-' + item.id">
                    {{
                      calculateWaitingTime(
                        UTCtoClientTZ(item.created_at, tz, "YYYY-MM-DD HH:mm:ss"),
                        "time-elapsed-my-active-groups-" + item.id
                      )
                    }}
                  </td>
                  <td>{{ $t(item.department) }}</td>
                  <td>{{ item.name }}</td>
                </tr>
              </template>

              <!-- INFINITE LOAD -->
              <!-- <infinite-loading
                v-if="showListTicket"
                @infinite="loadTicket"
                spinner="spiral"
                ref="loadTable"
              > -->
              <!-- criar chave de tradução -->
              <!-- <div slot="no-more" class="mt-2 mb-2">
                  <span :style="{ color: '#6E6E6E' }">{{
                    $t("bs-no-more-results")
                  }}</span>
                </div> -->
              <!-- criar chave de tradução -->
              <!-- <div slot="no-results" class="mt-2 mb-2">
                  <span :style="{ color: '#6E6E6E' }">{{
                    $t("bs-no-results-found")
                  }}</span>
                </div>
              </infinite-loading> -->
            </tbody>

            <div class="row ml-2" style="text-aling: right">
              <b-pagination
                v-model="currentPage"
                :total-rows="quantPages"
                :per-page="quantRows"
                class="mt-4"
              >
                <!-- <template #first-text><span class="text-success" @click="loadTicket">First</span></template>
                    <template #prev-text><span class="text-danger" @click="loadTicket">Prev</span></template>
                    <template #next-text><span class="text-warning" @click="loadTicket">Next</span></template>
                    <template #last-text><span class="text-info" @click="loadTicket">Last</span></template> -->

                <template #first-text
                  ><span class="text-dark">{{ $t("bs-first") }}</span></template
                >
                <template #prev-text
                  ><span class="text-dark">{{ $t("bs-prev") }}</span></template
                >
                <template #next-text
                  ><span class="text-dark">{{ $t("bs-next") }}</span></template
                >
                <template #last-text
                  ><span class="text-dark">{{ $t("bs-last") }}</span></template
                >

                <template #ellipsis-text>
                  <b-spinner small type="grow"></b-spinner>
                  <b-spinner small type="grow"></b-spinner>
                  <b-spinner small type="grow"></b-spinner>
                </template>

                <template #page="{ page, active }">
                  <b v-if="active">
                    {{ page }}
                  </b>
                  <i v-else>{{ page }}</i>
                </template>

                <!-- <template #currentPage="{ currentPage }">
                        {{currentPage}}
                    </template> -->
              </b-pagination>
              <span style="margin-top: 28px">
                <span style="opacity: 0.6; width: 50px">
                  {{$t('bs-number-of-tickets-per-page')}}
                  <input
                    style="opacity: 0.8; width: 50px"
                    @click="setQuantidadePorPagina"
                    v-model="quantRows"
                    type="number"
                    value="8"
                  />
                </span>
              </span>
            </div>
          </table>
        </b-card>
      </b-col>

      <!-- PROVAVELMENTE LIXO/BACKUP  -->
      <b-col class="ml-n3" v-show="showTicketOld && !isMobile">
        <b-row class="col-chat">
          <b-col cols="12">
            <div class="card h-60" id="chat">
              <div class="card-header translator">
                <span>
                  <img src="images/icons/language.svg" />
                  Idioma Detectado
                </span>
                <b-form-select
                  id="language"
                  class="ml-5 mr-5 w-25"
                  v-model="selected"
                  :options="options"
                ></b-form-select>
                <b-button id="btn-discard">Descartar</b-button>
                <b-button id="btn-translate">Traduzir</b-button>
              </div>

              <div class="card-body p-0 m-0 card-chat-content">
                <b-list-group-item
                  class="msg d-flex justify-content-between align-items-center"
                >
                  <b-row class="w-100">
                    <b-col>
                      <h2>Cliente</h2>
                    </b-col>
                    <b-col class="msg-hour"> 12:44 </b-col>
                    <b-col cols="12" class="content">
                      Lorem Ipsum is simply dummy text of the printing and typesetting
                      industry. Lorem Ipsum has been the industry's standard dummy text
                      ever since the 1500s, when an unknown printer took a galley of type
                      and scrambled it to make a type specimen book. It has survived not
                      only five centuries, but also the leap into electronic typesetting,
                      remaining essentially unchanged. It was popularised in the 1960s
                      with the release of Letraset sheets containing Lorem Ipsum passages,
                      and more recently with desktop publishing software like Aldus
                      PageMaker including versions of Lorem Ipsum. Why do we use it? It is
                      a long established fact that a reader will be distracted by the
                      readable content of a page when looking at its layout. The point of
                      using Lorem Ipsum is that it has a more-or-less normal distribution
                      of letters, as opposed to using 'Content here, content here', making
                      it look like readable English. Many desktop publishing packages and
                      web page editors now use Lorem Ipsum as their default model text,
                      and a search for 'lorem ipsum' will uncover many web sites still in
                      their infancy. Various versions have evolved over the years,
                      sometimes by accident, sometimes on purpose (injected humour and the
                      like).
                    </b-col>
                  </b-row>
                </b-list-group-item>

                <b-list-group-item
                  class="msg d-flex justify-content-between align-items-center"
                >
                  <b-row class="w-100">
                    <b-col>
                      <h2>Suporte Financeiro BR</h2>
                    </b-col>
                    <b-col class="msg-hour"> 12:44 </b-col>
                    <b-col cols="12" class="content">
                      The standard chunk of Lorem Ipsum used since the 1500s is reproduced
                      below for those interested. Sections 1.10.32 and 1.10.33 from "de
                      Finibus Bonorum et Malorum" by Cicero are also reproduced in their
                      exact original form, accompanied by English versions from the 1914
                      translation by H. Rackham.
                    </b-col>
                  </b-row>
                </b-list-group-item>

                <b-list-group-item
                  class="msg d-flex justify-content-between align-items-center"
                >
                  <b-row class="w-100">
                    <b-col>
                      <h2>Cliente</h2>
                    </b-col>
                    <b-col class="msg-hour"> 12:44 </b-col>
                    <b-col cols="12" class="content">
                      It is a long established fact that a reader will be distracted by
                      the readable content of a page when looking at its layout. The point
                      of using Lorem Ipsum is that it has a more-or-less normal
                      distribution of letters, as opposed to using 'Content here, content
                      here', making it look like readable English. Many desktop publishing
                      packages and web page editors now use Lorem Ipsum as their default
                      model text, and a search for 'lorem ipsum' will uncover many web
                      sites still in their infancy
                    </b-col>
                  </b-row>
                </b-list-group-item>

                <b-list-group-item
                  class="msg msg-event d-flex justify-content-between align-items-center"
                >
                  <b-row class="w-100">
                    <b-col class="content">
                      <h2>Jessica Nepomuceno entrou.</h2>
                    </b-col>
                    <b-col class="msg-hour"> 12:44 </b-col>
                  </b-row>
                </b-list-group-item>

                <b-list-group-item
                  class="msg msg-event d-flex justify-content-between align-items-center"
                >
                  <b-row class="w-100">
                    <b-col class="content">
                      <h2>
                        Jessica Nepomuceno enviou uma solicitação de classificação de
                        chat.
                      </h2>
                    </b-col>
                    <b-col class="msg-hour"> 12:44 </b-col>
                  </b-row>
                </b-list-group-item>
              </div>
            </div>

            <br />

            <div id="chat" class="card h-40">
              <div class="card-header translator">
                <span class="mr-4">
                  <img src="images/icons/chat/format_color.svg" />
                </span>
                <span>
                  <img src="images/icons/chat/format_bold.svg" />
                </span>
                <span>
                  <img src="images/icons/chat/format_italic.svg" />
                </span>
                <span>
                  <img src="images/icons/chat/format_underlined.svg" />
                </span>

                <span class="ml-4 mr-4">
                  <img src="images/icons/chat/attach_file.svg" />
                  Anexar
                </span>
                <span class="ml-4 mr-4">
                  <img src="images/icons/chat/save_as.svg" />
                  Salvar como
                </span>
                <span class="ml-4 mr-4">
                  <img src="images/icons/chat/build.svg" />
                  Ação
                </span>
                <span class="ml-4 mr-4">
                  <img src="images/icons/chat/edit.svg" />
                  Inserir Assinatura
                </span>
              </div>
              <div class="card-body">
                <b-row class="h-100 w-100">
                  <b-col cols="11">
                    <textarea class="w-100 h-100 border-0"></textarea>
                  </b-col>
                  <b-col cols="1" class="col-btn-send">
                    <b-button variant="outline-light" id="btn-send">
                      <img src="images/icons/chat/send.svg" height="30px" width="30px" />
                    </b-button>
                  </b-col>
                </b-row>
              </div>
            </div>
          </b-col>
        </b-row>
      </b-col>

      <!-- RIGHT CARD -->
      <b-col v-show="showInformation && !isMobile" cols="2" class="ml-n3 h-100">
        <ticket-information
          :itemselected="itemselected"
          :user="user"
          :openClientHistory="openClientHistory"
        ></ticket-information>
      </b-col>

      <b-col v-show="showFilters && !isMobile" cols="2" class="ml-n3 h-100">
        <b-card
          show
          class="h-100 ticket-information"
          header="Procurar"
          header-tag="header"
        >
          <!-- FILTER CARD -->
          <filter-all
            :is_admin="is_admin"
            :session_user="user"
            :session_user_departments="user_departments_id"
            :session_user_cucd="session_user_cucd"
            :session_user_company="cs"
            :session_user_permissions="restriction"
            :activeMenuControls="activeMenuControls"
            v-on:ticketsFilter2="ticketsFilter2"
          ></filter-all>
        </b-card>
      </b-col>
    </b-row>

    <template v-if="showCreateTicket" class="mobile">
      <ticket-create
        :user="user"
        :cs="cs"
        v-on:saveRow="saveRow"
        v-on:back="back"
      ></ticket-create>
    </template>

    <template class="h-100 mobile" v-if="showTicket">
      <ticket-ticket
        :is-mobile="isMobile"
        :user="user"
        :restriction="restriction"
        :is_admin="is_admin"
        :itemselected="itemselected"
        v-on:back="back"
        :openClientHistory="openClientHistory"
        v-on:ticket_ticket="ticket_ticket"
      ></ticket-ticket>
    </template>

    <modal-client-history
      v-if="!isMobile"
      :chat="itemselected"
      :clientChatHistory="clientChatHistory"
      :clientTicketHistory="clientTicketHistory"
      :user="user"
    />

    <!-- modalDepartmentNot -->
    <alert-not-department title="ticket"></alert-not-department>

  </div>
</template>

<script>
export default {
  data() {
    return {
      //'OPENED','IN_PROGRESS','CLOSED','RESOLVED','CANCELED'
      countfixed: {
        open: 0,
        in_progress: 0,
        canceled: 0,
        resolved: 0,
        closed: 0,
        mytickets: 0,
        overdue: 0,
        filters: 0,
      },
      //badge notifications
      notify: {
        audio: new Audio("/media/ticket-notification.mp3"),
        onQueue: false,
        inProgress: false,
        transferred: false,
        closed: false,
        resolved: false,
        canceled: false,
      },
      statusTicket: "OPENED",
      dropdown: "bbi bbi-keyboard-arrow-right bbi-30 mx-1",
      showdrop_MyTickets: false,
      showdrop_ActiveTickets: true,
      title: "Tickets",
      showTree: true,
      showList: true,
      showListTicket: true,
      showTicketOld: false,
      showInformation: true,
      showCreateTicket: false,
      showTicket: false,
      showFilters: false,
      selected: null,
      itemselected: {},
      options: [{ value: null, text: "Português" }],
      tickets: [],
      activeMenuControls: {
        active: {
          opened: true,
          inProgress: false,
          overdue: false,
          alltickets: false,
        },
        closed: false,
        resolved: false,
        canceled: false,
        filters: false,
        myTickets: {
          myActiveTickets: false,
          myActiveGroups: false,
        },
      },
      isMobile: false,
      showMenuMobile: false,
      tz: "",
      skip: 0,
      clientChatHistory: [],
      clientTicketHistory: [],
      page: 1,
      currentPage: 1, // VALOR DA PAGINA SELECIONADA
      quantPages: 100, // QUANTIDADE TOTAL DE TICKETS
      quantRows: 8, // QUATIDADE DE TICKETS POR PAGINA
      totalPages: 1, // TOTAL PAGINAS
      numberOfTabs: 0,
      departments: [],
      company_department: {
        id: 0,
        name: this.$t("bs-all"),
      },
    };
  },
  created() {
    this.numberOfTabs = this.$store.state.chatsFooter.length;
    if (localStorage.getItem("ticketporpagina") == null) {
      this.quantRows = 8;
    } else {
      this.quantRows = localStorage.getItem("ticketporpagina");
    }

    axios.get('department/get-my-departments')
    .then(res => {

      if(localStorage.getItem("preselectDepartment")){
        this.company_department = JSON.parse(localStorage.getItem("preselectDepartment"));

        for (let i = 0; i < this.company_department.length; i++) {
          this.company_department[i].name = this.$t(this.company_department[i].name);
        }

        this.departments = [ 
          { 
            "title": "Todos", 
            "options": [] 
          } 
        ];
        var arrayDepart = res.data.result;
        for (let i = 0; i < arrayDepart.length; i++) {
          arrayDepart[i].name = this.$t(arrayDepart[i].name);
          this.departments[0].options.push(arrayDepart[i]);
        }
      }else{
        this.company_department = res.data.result;
        localStorage.setItem("preselectDepartment", JSON.stringify(res.data.result));
        this.departments = [ 
          { 
            "title": "Todos", 
            "options": [] 
          } 
        ];
        for (let i = 0; i < this.company_department.length; i++) {
          this.company_department[i].name = this.$t(this.company_department[i].name);

          this.departments[0].options.push(this.company_department[i]);
        }
      }

    })
    .catch(err => {
      console.error(err); 
    });


    //this.loadTicket();

    // window.onbeforeunload = function () {
    //   return "Você tem certeza que deseja sair?";
    // };

    //this.interval = setInterval(() => this.overduetickets(), 60000);
    this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
    window.addEventListener("resize", this.onResize);
    this.onResize();

    // canal para tickets abertos e transferidos para outro departamento
    Echo.join(`tickets-list.${this.cs.id}`).listen("TicketsListUpdate", (event) => {
      //console.log(event);
      switch (event.ticket.action) {
        case "push":
          switch (event.ticket.data.status) {
            case "OPENED":
              //console.log('Sixpence None The Richer - Breathe You');
              if (this.user_departments_id.includes(event.ticket.company_department_id)) {
                //console.log('DEPARTAMENTO CERTO?');
                if (this.activeMenuControls.active.opened) {
                  //console.log('CLICADO NO OPENED');
                  if (this.currentPage == this.totalPages) {
                    // SÓ DA O PUSH SE ESTIVER NA ULTIMA PAGINA
                    this.tickets.push(event.ticket.data);
                  }
                  this.countfixed.open = parseInt(this.countfixed.open) + 1;
                  this.notifyQueue();
                  this.notify.audio.play();
                } else {
                  //console.log('CLICADO EM QUALQUER OUTRA COISA');
                  this.countfixed.open = parseInt(this.countfixed.open) + 1;
                  this.notifyQueue();
                  this.notify.audio.play();
                }
              }
              break;
            default:
              /*
				  logica dos transferidos
				  */
              break;
          }
          break;
        case "remove":
          let index = this.tickets.findIndex((item) => item.id === event.ticket.id);
          //REMOVE O TICKET
          if (index !== -1) {
            this.tickets.splice(index, 1);
          }

          if (this.user.id === event.ticket.user_agent_id) {
            if (event.ticket.status == "OPENED") {
              this.countfixed.open = parseInt(this.countfixed.open) - 1;
            }

            if (event.ticket.status == "IN_PROGRESS") {
              this.countfixed.in_progress = parseInt(this.countfixed.in_progress) - 1;

              if (event.ticket.status_final == "CLOSED") {
                this.countfixed.closed = parseInt(this.countfixed.closed) + 1;
              }

              if (event.ticket.status_final == "RESOLVED") {
                this.countfixed.resolved = parseInt(this.countfixed.resolved) + 1;
              }
            }
          } else {
            if (event.ticket.status == "OPENED") {
              this.countfixed.open = parseInt(this.countfixed.open) - 1;
            }

            if (event.ticket.status == "IN_PROGRESS") {
              this.countfixed.in_progress = parseInt(this.countfixed.in_progress) - 1;
              this.notify.inProgress = false;
              //console.log('ENTROU AQUI');
            }
            //GAMBIARRA AQUI PORQUE NÃO SEI A SOLUÇÃO
            //this.title = 'Tickets ';
            //this.title = 'Tickets';
            //console.log("depois: "+ this.countfixed.open);
          }
          break;
        case "alert_status":
          let index2 = this.tickets.findIndex(
            (item2) => item2.id === event.ticket.id_ticket
          );
          //REMOVE O TICKET
          if (index2 !== -1) {
            this.tickets[index2].answered = true;
            //console.log(this.tickets[index2]);
          }

          this.notify.audio.play();
          this.notifyInProgress();
          break;
      }
    });

    // canal para tickets em progresso, fechados, resolvidos, cancelados e transferidos para outro atendente.
    Echo.join(`tickets-agent-list.${this.cs.id}.${this.user.id}`).listen(
      "TicketsAgentListUpdate",
      (event) => {
        switch (event.ticket.action) {
          case "push":
            switch (event.ticket.data.status) {
              case "IN_PROGRESS":
                if (this.activeMenuControls.active.inProgress) {
                  if (this.currentPage == this.totalPages) {
                    // SÓ DA O PUSH SE ESTIVER NA ULTIMA PAGINA
                    this.tickets.push(event.ticket.data);
                  }
                }

                //VERIFICAR O STATUS ATUAL DO TICKET PARA PODER REMOVER CORRETAMENTE

                if (event.ticket.status_original == "OPENED") {
                  //Remove duas vezes - ENTRA AQUI NO CASO DO Remove e Push - REFAZER
                  //this.countfixed.open = parseInt(this.countfixed.open) - 1;
                  this.itemselected.status = "IN_PROGRESS";
                }
                if (event.ticket.status_original == "CLOSED") {
                  this.countfixed.closed = parseInt(this.countfixed.closed) - 1;
                }

                this.countfixed.in_progress = parseInt(this.countfixed.in_progress) + 1;
                this.notifyInProgress();
                break;
              case "CLOSED":
                if (this.activeMenuControls.closed) {
                  if (this.currentPage == this.totalPages) {
                    // SÓ DA O PUSH SE ESTIVER NA ULTIMA PAGINA
                    this.tickets.push(event.ticket.data);
                  }
                }
                //VERIFICAR O STATUS ATUAL DO TICKET PARA PODER REMOVER CORRETAMENTE
                if (event.ticket.status_original == "IN_PROGRESS") {
                  this.countfixed.in_progress = parseInt(this.countfixed.in_progress) - 1;
                }

                this.countfixed.closed = parseInt(this.countfixed.closed) + 1;
                this.notifyClosed();
                break;
              case "RESOLVED":
                if (this.activeMenuControls.resolved) {
                  if (this.currentPage == this.totalPages) {
                    // SÓ DA O PUSH SE ESTIVER NA ULTIMA PAGINA
                    this.tickets.push(event.ticket.data);
                  }
                }
                //VERIFICAR O STATUS ATUAL DO TICKET PARA PODER REMOVER CORRETAMENTE
                if (event.ticket.status_original == "IN_PROGRESS") {
                  this.countfixed.in_progress = parseInt(this.countfixed.in_progress) - 1;
                }
                if (event.ticket.status_original == "CLOSED") {
                  this.countfixed.closed = parseInt(this.countfixed.closed) - 1;
                }

                this.countfixed.resolved = parseInt(this.countfixed.resolved) + 1;
                this.notifyResolved();
                break;
              case "CANCELED":
                if (this.activeMenuControls.canceled) {
                  if (this.currentPage == this.totalPages) {
                    // SÓ DA O PUSH SE ESTIVER NA ULTIMA PAGINA
                    this.tickets.push(event.ticket.data);
                  }
                }

                //VERIFICAR O STATUS ATUAL DO TICKET PARA PODER REMOVER CORRETAMENTE
                if (event.ticket.status_original == "OPENED") {
                  this.countfixed.open = parseInt(this.countfixed.open) - 1;
                }
                if (event.ticket.status_original == "IN_PROGRESS") {
                  this.countfixed.in_progress = parseInt(this.countfixed.in_progress) - 1;
                }

                this.countfixed.canceled = parseInt(this.countfixed.canceled) + 1;
                this.notifyCanceled();
                break;
              default:
                /*
					logica dos transferidos
					*/
                break;
            }
            break;
          case "remove":
            //console.log('remover');
            let index = this.tickets.findIndex((item) => item.id === event.ticket.id);

            if (index !== -1) {
              this.tickets.splice(index, 1);
            }

            // let status = this.tickets.findIndex(
            //     (item) => item.status === 'IN_PROGRESS'
            // );
            // if(status!== -1){
            //     this.countfixed.in_progress -=1;
            // }

            break;
        }
      }
    );
  },
  props: {
    user: Object,
    cs: Object,
    restriction: Array,
    is_admin: Number,
    user_departments_id: Array,
    session_user_cucd: Array,
  },
  watch: {
    company_department(){
      this.tickets = [];
      this.skip = this.currentPage * this.quantRows - this.quantRows;
      this.page = this.currentPage;
      localStorage.setItem("preselectDepartment", JSON.stringify(this.company_department));
      this.loadTicket();
    },
    currentPage() {
      this.tickets = [];
      this.skip = this.currentPage * this.quantRows - this.quantRows;
      this.page = this.currentPage;
      this.loadTicket();
    },
    statusMyTickets() {
      this.MyTicketfilterReset();
      this.loadTicket();
    },
    "$store.state.chatsFooter": function () {
        this.numberOfTabs = this.$store.state.chatsFooter.length;
    },
  },
  methods: {
    ticketsFilter2(tickets) {
      this.tickets = tickets;
    },
    loadTicket($state) {
      //method called in loop for infinite loading
      if (this.activeMenuControls.active.opened) {
        this.searchTicket($state, "OPENED");
      } else if (this.activeMenuControls.active.inProgress) {
        this.searchTicket($state, "IN_PROGRESS"); // IN_PROGRESS
      } else if (this.activeMenuControls.active.overdue) {
        this.searchTicket($state, "OVERDUE");
      } else if (this.activeMenuControls.closed) {
        this.searchTicket($state, "CLOSED");
      } else if (this.activeMenuControls.resolved) {
        this.searchTicket($state, "RESOLVED");
      } else if (this.activeMenuControls.canceled) {
        this.searchTicket($state, "CANCELED");
      } else if (this.activeMenuControls.myTickets.myActiveTickets) {
        //this.myTickets($state, "tickets");
      } else if (this.activeMenuControls.myTickets.myActiveGroups) {
        //this.myTickets($state, "groups");
      } else if (this.activeMenuControls.active.alltickets) {
        this.searchTicket($state, "ALLTICKET");
      } else if (this.activeMenuControls.filters) {
        this.showFilters = true;
        //$state.complete();
        return;
      }
      this.showFilters = false;
    },
    goBackToMenu() {
      if (this.isMobile) {
        this.showMenuMobile = true;
      }
    },
    goToTable() {
      if (this.isMobile) {
        this.showMenuMobile = false;
      }
    },
    onResize(e) {
      if (window.outerWidth < 1200) {
        if (!this.isMobile) {
          this.isMobile = true;

          this.goToTable(); // default
        }
      } else {
        if (this.isMobile) {
          this.isMobile = false;
        }
      }
    },
    clearAllActiveStatus() {
      for (let i in this.activeMenuControls) {
        if (typeof this.activeMenuControls[i] == "object") {
          for (let j in this.activeMenuControls[i]) {
            this.activeMenuControls[i][j] = false;
          }
        } else {
          this.activeMenuControls[i] = false;
        }
      }
    },
    markAsActive: function (option) {
      this.clearAllActiveStatus();
      this.itemselected = {};
      switch (option) {
        default:
        case 0: // opened
          this.activeMenuControls.active.opened = true;
          this.notify.onQueue = false;
          break;
        case 1: // in progress
          this.activeMenuControls.active.inProgress = true;
          this.notify.inProgress = false;
          break;
        case 2: // overdue
          this.activeMenuControls.active.overdue = true;
          break;
        case 3: // closed
          this.activeMenuControls.closed = true;
          this.notify.closed = false;
          break;
        case 4: // resolved
          this.activeMenuControls.resolved = true;
          this.notify.resolved = false;
          break;
        case 5: // canceled
          this.activeMenuControls.canceled = true;
          this.notify.canceled = false;
          break;
        case 6: // my active tickets
          this.activeMenuControls.myTickets.myActiveTickets = true;
          break;
        case 7: // my active groups
          this.activeMenuControls.myTickets.myActiveGroups = true;
          break;
        case 8: // my active groups
          this.activeMenuControls.active.alltickets = true;
          break;
        case 9: // filters
          this.activeMenuControls.filters = true;
          break;
      }

      //MyTicketfilterReset tive que add um if
      if (this.currentPage == 1) {
        this.page = 1;
        this.skip = 0;
        this.tickets = [];
        this.infiniteId += 1;
        this.loadTicket();
      } else {
        this.page = 1;
        this.currentPage = 1;
        this.skip = 0;
        this.tickets = [];
        this.infiniteId += 1;
      }

      this.goToTable();

      this.skip = 0;
      if (this.$refs.loadTable) {
        this.$refs.loadTable.stateChanger.reset();
      }
    },
    treeMyTickets() {
      this.showdrop_MyTickets = !this.showdrop_MyTickets;
    },
    treeActiveTickets() {
      this.showdrop_ActiveTickets = !this.showdrop_ActiveTickets;
    },
    // openTree() {
    // 	document.getElementById("all").click();
    // 	document.getElementById("active").click();
    // },
    // openChat() {
    // 	this.showList = !this.showList;
    // 	this.showListTicket = !this.showListTicket;
    // },
    openTicket(value) {
      var vm = this;

      if (vm.restriction[0].ticket_open == 1 || vm.is_admin == 1) {
        vm.$loading(true);
        axios
          .post(`ticket-add-chat`, {
            itemselected: value,
            restrictions: JSON.parse(value.settings)
          })
          .then(function (response) {
            if (response.data.result) {
              vm.$loading(false);
              Swal.fire({
                title: vm.$t("bs-ticket-already-linked-another-attendant"),
                text: vm.$t("bs-do-you-want-to-get-this-ticket"),
                footer:
                  vm.$t("bs-the-ticket-already-belongs-to-the-attendan") +
                  " <b>&nbsp " +
                  response.data.value.name +
                  " </b>",
                icon: "warning",
                showCancelButton: true,
                cancelButtonText: vm.$t("bs-cancel"),
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: vm.$t("bs-yes-get"),
              }).then((result) => {
                if (result.isConfirmed) {
                  axios
                    .post("update-user-ticket", {
                      user_ticket_id: response.data.value.id,
                    })
                    .then(function (response) {
                      if (response.data.success) {
                        vm.showTree = false;
                        vm.showList = false;
                        vm.showInformation = false;
                        vm.showListTicket = false;
                        vm.showCreateTicket = false;
                        vm.showTicket = true;
                        vm.$loading(false);
                        vm.itemselected = value;
                        vm.title = "";
                        vm.$snotify.success(
                          vm.$t("bs-ticket-successfully-linked") + "!",
                          vm.$t("bs-success")
                        );
                      } else {
                        vm.$snotify.error(
                          vm.$t("bs-error-when-getting-ticket") + "!",
                          vm.$t("bs-error")
                        );
                      }
                    })
                    .catch(function (erro) {
                      //console.log(erro);
                      console.log("FAILURE!!");
                    });
                }
              });
            } else {
              if (response.data.value == "not_department") {
                vm.$loading(false);
                vm.$snotify.error(
                  "Você não pertence a esse departamento!",
                  vm.$t("bs-error")
                );
              } else {
                if (response.data.value == "not_opened_limity") {
                  vm.$loading(false);
                  vm.$snotify.info( vm.$t('bs-active-ticket-limit-exceeded') , vm.$t('bs-info'));
                }else{
                  vm.showTree = false;
                  vm.showList = false;
                  vm.showInformation = false;
                  vm.showListTicket = false;
                  vm.showCreateTicket = false;
                  vm.showTicket = true;
                  vm.$loading(false);
                  vm.itemselected = value;
                  if (response.data.value == "opened") {
                    vm.itemselected.update_status_in_progress =
                      response.data.update_status_in_progress;
                  }
                  vm.title = "";
                }
              }
            }
          })
          .catch(function (erro) {
            vm.$snotify.error(vm.$t("bs-error-trying-to-save"), vm.$t("bs-error"));
            console.log(erro);
            console.log("FAILURE!!");
          });
      } else {
        vm.$snotify.info(
          vm.$t("bs-you-are-not-allowed-to-open-a-ticket") + "!",
          vm.$t("bs-info")
        );
      }
    },
    openCreateTicket() {
      // this.showTree = false;
      // this.showList = false;
      // this.showInformation = false;
      // this.showListTicket = false;
      // this.showTicket = false;

      if (
        this.restriction[0].ticket_create == null ||
        this.restriction[0].ticket_create == 0
      ) {
        this.$snotify.info(
          this.$t("bs-you-are-not-allowed-to-create-ticket") + "!",
          this.$t("bs-info")
        );
        return;
      }

      this.showCreateTicket = true;
      this.title = this.$t("bs-create-new-ticket");
    },
    back() {
      this.showTree = true;
      this.showList = true;
      this.showInformation = true;
      this.showListTicket = true;
      this.showCreateTicket = false;
      this.showTicket = false;
      this.itemselected = {};
      this.title = "Tickets";
    },
    ticket_ticket() {
      var vm = this;
      vm.back();
      vm.clearAllActiveStatus();
      vm.markAsActive(1);
    },
    saveRow(item) {
      var vm = this;
      vm.tickets.push(item);
      //console.log(item);
      this.back();
    },
    setTitle(value) {
      this.title = value;
    },
    viewTicket(value) {
      // limpar email
      var aux = value.email_created.split("_");

      if (aux[0] == "comp") {
        aux = aux.splice(2);
        var concat = "";
        for (var i = 0; i < aux.length; i++) {
          concat += aux[i] + "_";
        }
        value.email_created = concat.slice(0, -1);
      }

      this.itemselected = value;
    },
    searchTicket($state, type = "OPENED") {
      var vm = this;
      axios
        .get("tickets/get-tickets", {
          params: {
            type: type,
            skip: vm.skip,
            quantRows: vm.quantRows,
            departmentSelected: vm.company_department,
          },
        })
        .then(function (r_resposta) {
          if(r_resposta.data.department == 'not_department'){
            $("#modalDepartmentNot").modal("show");
          }

          if (r_resposta.data.result.length > 0) {
            vm.tickets = r_resposta.data.result;
            vm.skip = r_resposta.data.skip;
          }

          vm.countfixed.open = r_resposta.data.count[0].OPENED;
          vm.countfixed.canceled = r_resposta.data.count[0].CANCELED;
          vm.countfixed.resolved = r_resposta.data.count[0].RESOLVED;
          vm.countfixed.closed = r_resposta.data.count[0].CLOSED;
          vm.countfixed.in_progress = r_resposta.data.count[0].IN_PROGRESS;
          vm.countfixed.overdue = r_resposta.data.count[0].OVERDUE;

          //FAZER RECEBER O TOTAL DE LINHAS PARA CALCULAR A QUANTIDADE DE PAGINAS
          if (r_resposta.data.status == "OPENED") {
            vm.quantPages = vm.countfixed.open;
          } else if (r_resposta.data.status == "IN_PROGRESS") {
            vm.quantPages = vm.countfixed.in_progress;
          } else if (r_resposta.data.status == "RESOLVED") {
            vm.quantPages = vm.countfixed.resolved;
          } else if (r_resposta.data.status == "CLOSED") {
            vm.quantPages = vm.countfixed.closed;
          } else if (r_resposta.data.status == "CANCELED") {
            vm.quantPages = vm.countfixed.canceled;
          }

          if (type == "OVERDUE") {
            vm.overduetickets();
            vm.quantPages = vm.countfixed.overdue;
          }

          vm.totalPages = Math.ceil(vm.quantPages / vm.quantRows);
          if (vm.totalPages == 0) {
            vm.totalPages = 1;
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    overduetickets() {
      var vm = this;
      //vm.countfixed.overdue = 0;
      for (var i = vm.tickets.length - 1; i >= 0; i--) {
        var moment = require("moment-timezone");

        let client_tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
        //pega o fuso do cliente. Ex: "lAmerica/Sao_Pauo"

        let date_today = moment().tz(client_tz).utc().format("YYYY-MM-DD HH:mm:ss");
        // pego a data de hoje do dep no formato MM/DD/YYYY

        var date1 = moment(date_today);
        var date2 = moment(vm.tickets[i].updated_at);
        var diffInMinutes = date2.diff(date1, "minutes");

        //console.log(diffInMinutes*(-1));
        //console.log(vm.tickets[i].overdue);

        if (diffInMinutes * -1 >= vm.tickets[i].overdue && vm.tickets[i].overdue != 0) {
          vm.tickets[i].status = "OVERDUE";
          //vm.countfixed.overdue++;
        } else {
          vm.tickets.splice(i, 1);
        }
      }
    },
    myTickets($state, value) {
      //FUNÇÃO NÃO ULTILIZADA NO MOMENTO - ALTERAÇÃO DELA FEITA NO ADMIN - POR HORA DEIXA AI... TALVEZ EU PRECISE!!
      var vm = this;
      vm.showOver = false;
      if (value == "tickets") {
        axios
          .get("tickets/get-my-tickets", {
            params: {
              skip: vm.skip,
            },
          })
          .then(function (r_resposta) {
            //console.log(r_resposta.data.result);
            if (r_resposta.data.result.length > 0) {
              vm.skip = r_resposta.data.skip;
              vm.tickets.push(...r_resposta.data.result);
              vm.countfixed.mytickets = vm.tickets.length;

              $state.loaded();
            } else {
              $state.complete();
            }
          })
          .catch(function (error) {
            console.log(error);
          });

        this.statusTicket = this.user.id;
      }
      if (value == "groups") {
        this.statusTicket = "..";
        // do the seearch endpoint
        $state.complete();
      }
    },
    MyTicketfilterReset() {
      this.page = 1;
      this.currentPage = 1;
      this.skip = 0;
      this.tickets = [];
      this.infiniteId += 1;
    },
    filterStatus(str) {
      if (str == "OPENED") {
        return this.$t("bs-opened");
      } else if (str == "IN_PROGRESS") {
        return this.$t("bs-in-progress");
      } else if (str == "CLOSED") {
        return this.$t("bs-closed-s");
      } else if (str == "RESOLVED") {
        return this.$t("bs-resolved-s");
      } else if (str == "CANCELED") {
        return this.$t("bs-canceled-s");
      } else if (str == "OVERDUE") {
        return this.$t("bs-in-progress") + " - " + this.$t("bs-overdue");
      }
    },
    filterOver(str) {
      if (str == "OVERDUE") {
        return this.$t("bs-overdue") + " - ";
      } else {
        return "";
      }
    },
    filterType(str) {
      if (str == "TRANSFERED") {
        return "Transfered - ";
      } else {
        return "";
      }
    },
    UTCtoClientTZ(h, tz, format = "YYYY-MM-DD HH:mm:ss") {
      let h_format = moment(h, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD HH:mm:ss");
      let datetime = h_format.split(" ");
      let date = datetime[0];
      let time = datetime[1];
      let date_split = date.split("-");
      let time_split = time.split(":");
      let year = date_split[0];
      let month = date_split[1];
      let day = date_split[2];
      let hour = time_split[0];
      let minute = time_split[1];
      let second = time_split[2];
      var month_integer = parseInt(month, 10);
      if (month_integer >= 1) {
        month_integer--;
      }
      let dateUTC = new Date(Date.UTC(year, month_integer, day, hour, minute, second));
      let converted_time = dateUTC.toLocaleString("pt-BR", {
        timeZone: tz,
      });
      return moment(converted_time, "DD/MM/YYYY HH:mm:ss").format(format);
    },
    UTCtoClientTZ2(h, tz, format = "DD/MM/YYYY HH:mm:ss") {
      let h_format = moment(h, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD HH:mm:ss");
      let datetime = h_format.split(" ");
      let date = datetime[0];
      let time = datetime[1];
      let date_split = date.split("-");
      let time_split = time.split(":");
      let year = date_split[0];
      let month = date_split[1];
      let day = date_split[2];
      let hour = time_split[0];
      let minute = time_split[1];
      let second = time_split[2];
      var month_integer = parseInt(month, 10);
      if (month_integer >= 1) {
        month_integer--;
      }
      let dateUTC = new Date(Date.UTC(year, month_integer, day, hour, minute, second));
      let converted_time = dateUTC.toLocaleString("pt-BR", {
        timeZone: tz,
      });
      return moment(converted_time, "DD/MM/YYYY HH:mm:ss").format(format);
    },
    UTCtoClientTZ2_translated(h, tz) {
      let h_format = moment(h, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD HH:mm:ss");
      let datetime = h_format.split(" ");
      let date = datetime[0];
      let time = datetime[1];
      let date_split = date.split("-");
      let time_split = time.split(":");
      let year = date_split[0];
      let month = date_split[1];
      let day = date_split[2];
      let hour = time_split[0];
      let minute = time_split[1];
      let second = time_split[2];
      var month_integer = parseInt(month, 10);
      if (month_integer >= 1) {
        month_integer--;
      }
      let dateUTC = new Date(Date.UTC(year, month_integer, day, hour, minute, second));
      let converted_time = dateUTC.toLocaleString("pt-BR", {
        timeZone: tz,
      });

      var mt = require("moment-timezone");
      return mt(converted_time, "DD/MM/YYYY HH:mm:ss")
        .tz(tz)
        .locale(this.user.language)
        .calendar();
    },
    calculateWaitingTime(d, c) {
      var moment = require("moment-timezone");
      var startDateTime = moment
        .tz(d, Intl.DateTimeFormat().resolvedOptions().timeZone)
        .toDate();
      var startStamp = startDateTime.valueOf();

      var newDate = new Date();
      var newStamp = newDate.getTime();

      var timer;

      var diff_0 = false;

      let vm = this;

      function updateClock() {
        newDate = new Date();
        newStamp = newDate.getTime();
        var diff = Math.round((newStamp - startStamp) / 1000);

        var d = Math.floor(diff / (24 * 60 * 60));
        diff = diff - d * 24 * 60 * 60;
        var h = Math.floor(diff / (60 * 60));
        diff = diff - h * 60 * 60;
        var m = Math.floor(diff / 60);
        diff = diff - m * 60;
        var s = diff;

        if (h < 10) {
          h = "0" + h;
        }

        if (m < 10) {
          m = "0" + m;
        }

        if (s < 10) {
          s = "0" + s;
        }

        if (document.getElementById(c) !== null) {
          document.getElementById(c).innerHTML = `${d}&nbsp;${vm.$t(
            "bs-day-abbreviation"
          )}&nbsp;&nbsp;${h}:${m}:${s}&nbsp;${vm.$t("bs-hour-abbreviation")}`;
        }
      }

      setInterval(updateClock, 1000);
    },
    toUTC(h) {
      let h_format = moment(h, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD HH:mm:ss");
      let datetime = h_format.split(" ");
      let date = datetime[0];
      let time = datetime[1];
      let date_split = date.split("-");
      let time_split = time.split(":");
      let year = date_split[0];
      let month = date_split[1];
      let day = date_split[2];
      let hour = time_split[0];
      let minute = time_split[1];
      let second = time_split[2];
      var month_integer = parseInt(month, 10);
      if (month_integer >= 1) {
        month_integer--;
      }
      let dateUTC = new Date(Date.UTC(year, month_integer, day, hour, minute, second));
      let converted_time = dateUTC.toLocaleString("pt-BR", {
        timeZone: this.tz,
      });

      return converted_time;
    },
    format_L(h) {
      let converted_time = this.toUTC(h);
      var mt = require("moment-timezone");
      return mt(converted_time, "DD/MM/YYYY HH:mm:ss")
        .tz(this.tz)
        .locale(this.user.language)
        .format("L");
    },
    format_LT(h) {
      let converted_time = this.toUTC(h);
      var mt = require("moment-timezone");
      return mt(converted_time, "DD/MM/YYYY HH:mm:ss")
        .tz(this.tz)
        .locale(this.user.language)
        .format("L</br>LTS");
    },
    diffTime(t1, t2) {
      var ms = moment(t2, "YYYY-MM-DD HH:mm:ss").diff(moment(t1, "YYYY-MM-DD HH:mm:ss"));

      if (ms > 0) {
        let d = moment.duration(ms);

        let seconds =
          Math.round(d.asSeconds()) > 0
            ? `${d.seconds()}${this.$t("bs-second-abbreviation")}`
            : "";
        let minutes =
          Math.round(d.asMinutes()) > 0
            ? `${d.minutes()}${this.$t("bs-minute-abbreviation")}`
            : "";
        let hours =
          Math.round(d.asHours()) > 0
            ? `${d.hours()}${this.$t("bs-hour-abbreviation")}`
            : "";

        let days =
          Math.round(d.asDays()) > 0
            ? `${d.days()}${this.$t("bs-day-abbreviation")}`
            : "";
        let months =
          Math.round(d.asMonths()) > 0
            ? `${d.months()}${this.$t("bs-month-abbreviation")}`
            : "";
        let years =
          Math.round(d.asYears()) > 0
            ? `${d.years()}${this.$t("bs-year-abbreviation")}`
            : "";

        let p2 = `${years} ${months} ${days}`;
        p2 = p2.trim() == "" ? "" : `${p2}</br>`;

        return `${p2}${hours} ${minutes} ${seconds}`;
      } else {
        //valor negativo ou erro no calculo
        return "";
      }
    },
    notifyQueue() {
      if (!this.showTableQueue) {
        this.notify.onQueue = true;
      }
    },
    notifyInProgress() {
      if (!this.showTableInProgress) {
        this.notify.inProgress = true;
      }
    },
    notifyTransferred() {
      if (!this.showTableTransferred) {
        this.notify.transferred = true;
      }
    },
    notifyClosed() {
      if (!this.showTableClosed) {
        this.notify.closed = true;
      }
    },
    notifyResolved() {
      if (!this.showTableResolved) {
        this.notify.resolved = true;
      }
    },
    notifyCanceled() {
      if (!this.showTableCanceled) {
        this.notify.canceled = true;
      }
    },
    openClientHistory(id) {
      this.clientChatHistory = [];
      this.clientTicketHistory = [];
      
      const api = `client/get-client-history`;
      axios
        .get(api, {
          params: {
            client_id: id,
          },
        })
        .then(({ data }) => {
          //console.log(data);
          data.forEach((element) => {
            if (element.type === "TICKET" || element.type === "CHANGED_TO_TICKET") {
              this.clientTicketHistory.push(element);
            } else {
              this.clientChatHistory.push(element);
            }
          });
          $("#modalClientHistory").modal("show");
        });
    },
    setQuantidadePorPagina() {
      if (this.quantRows <= 99) {
        localStorage.setItem("ticketporpagina", this.quantRows);
      } else {
        this.quantRows = 100;
        this.$snotify.info(
          "Numero muito grande pode ocasionar mal funcionamento",
          this.$t("bs-info")
        );
      }
    },
  },
  filters: {
    formatData(value) {
      return moment(value).format("HH:mm");
      //var moment = require('moment-timezone');
      //return moment(value).tz("UTC").format('HH:mm:ss');
    },
  },
  computed: {
    isSelectedDropdownActive: function () {
      let ret = false;
      for (let i in this.activeMenuControls.active) {
        ret = ret || this.activeMenuControls.active[i];
      }
      return this.showdrop_ActiveTickets && ret;
    },
    isSelectedDropdownMyTickets: function () {
      let ret = false;
      for (let i in this.activeMenuControls.myTickets) {
        ret = ret || this.activeMenuControls.myTickets[i];
      }
      return this.showdrop_MyTickets && ret;
    },
  },
};
</script>

<style scoped lang="scss">
.h-90{
  height: 93%;
}

.fz-18 {
  font-size: 18px;
}

.customOpen {
  padding: 0.75em 0 !important;
  background-color: #6c757d;
  color: #fff;
  &:hover {
    background-color: #76818a;
  }
  div {
    display: flex;
    align-content: center;
  }
}

h1 {
  font: normal normal 800 25px/19px Muli;
  letter-spacing: 0px;
  color: #0080fc;
  opacity: 1;
}

h2 {
  font: normal normal 800 15px/31px Muli;
  letter-spacing: 0px;
  color: #0080fc;
  opacity: 1;
}

.card {
  box-shadow: 0px 0px 9px #26242424;
  border-radius: 5px;
  opacity: 1;
  border: none;
}

.br5 {
  border-radius: 5px;
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

.all-chats::before,
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
}

.level-1 {
  margin-left: 24px;
}

.level-2 {
  margin-left: 49px;
}

.level-3 {
  margin-left: 69px;
}

#list .card-body {
  padding: 0px;
}

#list {
  overflow: auto;
}

tr {
  width: 100%;
  display: inline-table;
  table-layout: fixed;
}

td {
  word-break: break-word;
  word-wrap: break-word;
}

table {
  height: 100%;
  display: -moz-groupbox;
  text-align: center;

  max-width: 100% !important;
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
  min-width: 80px;
  max-width: 100%;
  padding: 10px;
}

/* SCROLL */

// ::-webkit-scrollbar {
// 	width: 5px;
// }

// ::-webkit-scrollbar-track {
// 	border-radius: 10px;
// }

// ::-webkit-scrollbar-thumb {
// 	background: #0294ff33;
// 	border-radius: 10px;
// }

// ::-webkit-scrollbar-thumb:hover {
// 	background: #b30000;
// }

#info .card-body {
  padding-left: 0px;
  padding-right: 0px;
}

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

#info .list-group-item {
  border: none;
  font: normal normal normal 15px/24px Muli;
  letter-spacing: 0px;
  color: #656872;
  opacity: 1;
  padding: 0px;
  margin-left: 20px;
  margin-right: 20px;
}

#info img {
  width: 15px;
  margin-right: 5px;
}

.card-title {
  font: normal normal 600 16px/24px Muli;
  letter-spacing: 0px;
  color: #434343;
  opacity: 1;
  margin-left: 20px;
  margin-right: 20px;
}

.h-60 {
  height: 60% !important;
  min-height: 60% !important;
  max-height: 60% !important;
}

.h-40 {
  height: calc(40% - 20px) !important;
  max-height: calc(40% - 20px) !important;
  min-height: calc(40% - 20px) !important;
}

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

.msg {
  border: none;
  border-radius: 0px;
  border-bottom: 1px solid #d7dee6;
  padding-right: 0px;
}

.msg-hour {
  text-align: right;
  max-width: 80px;
  min-width: 80px;
  font: normal normal bold 16px/35px Muli;
  letter-spacing: 0px;
  color: #6e6e6e;
  opacity: 1;
}

.content {
  text-align: justify;
  font: normal normal bold 14px/18px Muli;
  letter-spacing: 0px;
  color: #707070;
  opacity: 1;
}

.msg-event h2 {
  text-align: center;
  font: normal normal 800 15px/31px Muli;
  letter-spacing: 0px;
  color: #707070;
  opacity: 1;
}

.card-chat-content {
  overflow: auto;
}

textarea {
  font: normal normal bold 14px/18px Muli;
  letter-spacing: 0px;
  color: #707070;
  opacity: 1;
  resize: none;
}

#btn-send {
  position: absolute;
  right: 0;
  bottom: 0;
  padding: 0px;
  margin: 0px;
  border: none;
}

.col-chat {
  max-height: 100% !important;
  min-height: 100% !important;
}

#chat {
  max-height: 500px !important;
}

#chat span {
  cursor: pointer;
}

.h-48 {
  height: 48px !important;
}

.li-body {
  border-radius: 5px 5px 0px 0px;
  background: #0296ff1e;
  color: #0080fc;
}

.local_arrow {
  font-size: 24px !important;
}

.myUL div:hover,
.myUL li:hover,
.myUL span:hover {
  cursor: pointer;
}

.selected {
  background-color: #0294ff33;
  color: #0080fc;
}

.list-group-item-header {
  padding: 0.75em 0;
}

tbody tr:hover {
  background-color: #f7f8fc !important;
  cursor: pointer;
}

.btn-back {
  font: normal normal 800 15px/31px Muli;
  letter-spacing: 0px;
  color: #0080fc;
  margin-left: 12px;
  i {
    font-size: 20px !important;
  }
  &:hover {
    cursor: pointer;
  }
}

.left-sidebar {
  min-width: 350px;
  max-width: 350px;
  height: 100%;
}

.left-sidebar .card {
  overflow-y: auto !important;
  height: 100% !important;
}

.answered {
  background: #ff4872;
  color: #f4f4f4;
}

.card {
  overflow-wrap: normal;
}

.fc {
  font: normal normal 600 16px/19px Lato;
  letter-spacing: 0px;
  color: #6e6e6e;
}

#wrapper {
  max-width: 90vh !important;
}

.content-body {
  display: grid;
  grid-template-columns: 100%;
  grid-template-rows: auto;
  justify-items: stretch;
  justify-content: stretch;
  align-items: stretch;
  align-content: stretch;
}

@media not screen and (hover: none) and (pointer: coarse) {
  /* hover somente em telas que tenham mouse */
  .li-hover:hover {
    background-color: #0294ff33;
    color: #0080fc;
  }
}
@media screen and (max-width: 1199px) {
  .ml-custom {
    margin-left: 8px;
  }
  .mobile {
    margin-left: 0 !important;
    margin-right: 0 !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
    box-sizing: border-box;
    .col,
    .container-fluid {
      margin-left: 0 !important;
      margin-right: 0 !important;
      padding-left: 0 !important;
      padding-right: 0 !important;
    }
  }
  table {
    min-width: 636px !important;
  }
  .content-body,
  .left-sidebar {
    min-width: 100%;
    max-width: 100%;
  }
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

.with-tab {
  height: calc(100vh - 58px) !important;
}
.zoom-80 {
  zoom: 80%;
}

@media (max-width: 1920px) {
  .with-tab {
    height: calc(100vh - 12px) !important;
  }
}

@media (max-width: 1366px) {
  .with-tab {
    height: calc(100vh - 60px) !important;
  }
}

.col-btn-back {
  max-width: 80px !important;
  min-width: 80px !important;
}

@media only screen and (min-width: 1279px) {
  .col-btn-back {
    display: none;
  }
}

@media only screen and (max-width: 1279px) {
  .col-btn-back {
    display: unset;
  }
}


</style>
