<template>
	<div>
		<b-container fluid="lg">
			<b-row>
				<b-col cols="auto" class="mr-auto p-3 bs-title">{{$t('bs-company-configuration')}}
					<b-card-text class="bs-subtitle">
						{{$t('bs-configure-the-company')}}
					</b-card-text>
				</b-col>
				<b-col cols="auto mt-4">
					<b-button @click="btnBack" variant="light bs-btn-back">{{$t('bs-cancel')}}</b-button>
					<b-button v-if="!show.show7 && !show.show6" @click="onSubmit" variant="btn bs-btn-save"><i class="fa fa-floppy-o" aria-hidden="true"></i> {{$t('bs-save')}}</b-button>
				</b-col>
			</b-row>
			<br>
			<b-alert fade show variant="primary" class="bui-alert">
				<slot v-if="show.show0">
					<h5>
						{{$t('bs-general')}}
					</h5>
					<p>
						{{$t('bs-on-this-tab-you-can-perform-some-general')}}
					</p>
				</slot>
				<slot v-if="show.show1">
					<h5>
						{{$t('bs-released-origins')}}
					</h5>
					<p>
					</p>
				</slot>
				<slot v-if="show.show2">
					<h5>
						{{$t('bs-released-origins')}}
					</h5>
					<p>
					</p>
				</slot>
				<slot v-if="show.show3">
					<h5>
						{{$t('bs-chat')}}
					</h5>
					<p>
						{{$t('bs-some-general-purpose-settings-for-chat')}}
					</p>
				</slot>
				<slot v-if="show.show4">
					<h5>
						{{$t('bs-ticket')}}
					</h5>
					<p>
						{{$t('bs-on-this-tab-you-can-make-some-general-purp')}}
					</p>
				</slot>
				<slot v-if="show.show5">
					<h5>
						{{$t('bs-categories')}}
					</h5>
					<p>
						{{$t('bs-you-will-be-able-to-create-categories-of')}}
					</p>
				</slot>
				<slot v-if="show.show6">
					<h5>
						{{$t('bs-emails')}}
					</h5>
					<p>
						{{$t('bs-you-will-be-able-to-create-email-person')}}
					</p>
				</slot>
				<slot v-if="show.show7">
					<h5>
						{{$t('bs-error-solutions')}}
					</h5>
					<p>
						{{$t('Informações básicas de atendimento')}}
					</p>
				</slot>
			</b-alert>
			<b-row>
				<div class="bs-m-spacing">
					<a v-on:click.stop="showIB(0)" href="#" :class="ss.ss0">{{$t('bs-general')}}</a>
				</div>
				<div v-if="is_helpdesk == 'builderall'" class="bs-m-spacing">
					<a v-on:click.stop="showIB(1)" href="#" :class="ss.ss1">{{$t('bs-released-origins')}}</a>
				</div>
				<!-- <div class="bs-m-spacing">
					<a v-on:click.stop="showIB(2)" href="#" :class="ss.ss2">{{$t('bs-blocked-origins')}}</a>
				</div> -->
				<div class="bs-m-spacing">
					<a v-on:click.stop="showIB(3)" href="#" :class="ss.ss3">{{$t('bs-chat')}}</a>
				</div>
				<div class="bs-m-spacing">
					<a v-on:click.stop="showIB(4)" href="#" :class="ss.ss4">{{$t('bs-ticket')}}</a>
				</div>
				<div class="bs-m-spacing">
					<a v-on:click.stop="showIB(5)" href="#" :class="ss.ss5">{{$t('bs-categories')}}</a>
				</div>
				<div class="bs-m-spacing">
					<a v-on:click.stop="showIB(6)" href="#" :class="ss.ss6">{{$t('bs-emails')}} * </a>
				</div>
				<div v-if="is_helpdesk == 'builderall'" class="bs-m-spacing">
					<a v-on:click.stop="showIB(7)" href="#" :class="ss.ss7">{{$t('bs-error-solutions')}}</a>
				</div>
			</b-row>
			<br><br>
			<div v-if="show.show0">
				<div class="body">
					<div>
						<label id="title-tooltip" class="bs-label" for="name">{{$t('bs-title')}} &nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></label>
						<b-tooltip target="title-tooltip" triggers="hover" placement="right" :variant="tooltipVariant">
							{{$t('bs-customizing-the-title-for-customer-login')}}
						</b-tooltip>
						<label class="caret" @click="addCompanyName('title')" for="text"><b-badge variant="primary">{{$t('bs-company-name')}}</b-badge></label>
						<b-form-input
						id="input-8"
						v-model="titleLogin"
						required
						class="bs-input mb-3"
						></b-form-input>

						<label id="subtitle-tooltip" class="bs-label" for="name">{{$t('bs-subtitle')}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></label>
						<b-tooltip target="subtitle-tooltip" triggers="hover" placement="right" :variant="tooltipVariant">
							{{$t('bs-subtitle-customization-customer-login')}}
						</b-tooltip>
						<label class="caret" @click="addCompanyName('sub')" for="text"><b-badge variant="primary">{{$t('bs-company-name')}}</b-badge></label>
						<b-form-input
						id="input-8"
						v-model="subtitleLogin"
						required
						class="bs-input mb-3"
						></b-form-input>

						<!-- <label id="chat-tooltip" class="bs-label" for="name">{{$t('bs-description')}} {{$t('bs-chat')}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></label>
						<b-tooltip target="chat-tooltip" triggers="hover" placement="right" :variant="tooltipVariant">
							{{$t('bs-chat-tooltip')}}
						</b-tooltip>
						<b-form-input
						id="input-8"
						v-model="titlechatclient"
						required
						class="bs-input mb-3"
						></b-form-input>

						<label id="ticket-tooltip" class="bs-label" for="name">{{$t('bs-description')}} {{$t('bs-ticket')}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></label>
						<b-tooltip target="ticket-tooltip" triggers="hover" placement="right" :variant="tooltipVariant">
							{{$t('bs-ticket-tooltip')}}
						</b-tooltip>
						<b-form-input
						id="input-8"
						v-model="titleticketclient"
						required
						class="bs-input mb-3"
						></b-form-input> -->

						<!-- <label id="robot-tooltip" class="bs-label" for="name">{{$t('bs-robot-name')}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></label>
						<b-tooltip target="robot-tooltip" triggers="hover" placement="right" :variant="tooltipVariant">
							{{$t('bs-robot-name-description')}}
						</b-tooltip>
						<b-form-input
						id="input-8"
						v-model="nameRobot"
						required
						class="bs-input mb-3"
						></b-form-input> -->

						<b-form-group id="input-group-15" label-for="input-15" class="bs-label" label="Chat">
							<li class="list-group-item isactive">
								<div class="form-row align-items-center">
									<div class="col-auto bs-label" id="tooltip-chat-show-queue">
										{{($t('bs-disable'))}} {{$t('bs-chat')}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
									</div>
									<div class="col" style="text-align: right;margin-top:8px;">
										<label class="switch">
											<input type="checkbox" v-model="showChat">
											<span class="slider round"></span>
										</label>
									</div>
								</div>
								<b-tooltip target="tooltip-chat-show-queue" triggers="hover" placement="right" variant="secondary">
									{{$t('bs-disable-company-chats')}}
								</b-tooltip>
							</li>
						</b-form-group>
						<b-form-group id="input-group-16" label-for="input-16" class="bs-label" label="Ticket">
							<li class="list-group-item isactive">
								<div class="form-row align-items-center">
									<div class="col-auto bs-label" id="tooltip-ticket-show">
										{{($t('bs-disable'))}} {{$t('bs-ticket')}}&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
									</div>
									<div class="col" style="text-align: right;margin-top:8px;">
										<label class="switch">
											<input type="checkbox" v-model="showTicket">
											<span class="slider round"></span>
										</label>
									</div>
								</div>
								<b-tooltip target="tooltip-ticket-show" triggers="hover" placement="right" variant="secondary">
									{{$t('bs-disables-the-companys-tickets')}}
								</b-tooltip>
							</li>
						</b-form-group>

						<!-- <b-form-group id="input-group-17" label-for="input-17" class="bs-label" :label="$t('bs-admin')">
							<li class="list-group-item isactive">
								<div class="form-row align-items-center">
									<div class="col-auto bs-label" id="tooltip-remove-admin">
										{{($t('bs-remove-admin-as-attendant'))}} &nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
									</div>
									<div class="col" style="text-align: right;margin-top:8px;">
										<label class="switch">
											<input type="checkbox" v-model="showAdmin">
											<span class="slider round"></span>
										</label>
									</div>
								</div>
								<b-tooltip target="tooltip-remove-admin" triggers="hover" placement="right" variant="secondary">
									{{$t('bs-remove-admin-as-attendant-descr')}}
								</b-tooltip>
							</li>
						</b-form-group> -->

						<!-- <b-form-group id="input-group-17" label-for="input-17" class="bs-label" :label="$t('bs-attendant-profile')">
							<li class="list-group-item isactive">
								<div class="form-row align-items-center">
									<div class="col-auto bs-label" id="tooltip-perfil-atendente">
										{{$t('bs-activate-profile')}} ({{$t('bs-attendant')}})&nbsp;
										<i class="fa fa-question-circle" aria-hidden="true"></i>
									</div>
									<div class="col" style="text-align: right;margin-top:8px;">
										<label class="switch">
											<input type="checkbox" v-model="editPerfilAttendants">
											<span class="slider round"></span>
										</label>
									</div>
								</div>
								<b-tooltip target="tooltip-perfil-atendente" triggers="hover" placement="right" variant="secondary">
									{{$t('bs-if-activated-the-attendant-can-edit-the-pr')}}
								</b-tooltip>
							</li>
						</b-form-group>

						<b-form-group id="input-group-16" label-for="input-16" class="bs-label" :label="$t('bs-customer-profile')">
							<li class="list-group-item isactive">
								<div class="form-row align-items-center">
									<div class="col-auto bs-label" id="tooltip-perfil-cliente">
										{{$t('bs-activate-profile')}} ({{$t('bs-client')}})&nbsp;
										<i class="fa fa-question-circle" aria-hidden="true"></i>
									</div>
									<div class="col" style="text-align: right;margin-top:8px;">
										<label class="switch">
											<input type="checkbox" v-model="editPerfilClient">
											<span class="slider round"></span>
										</label>
									</div>
								</div>
								<b-tooltip target="tooltip-perfil-cliente" triggers="hover" placement="right" variant="secondary">
									{{$t('bs-if-activated-the-customer-can-edit-the-pr')}}
								</b-tooltip>
							</li>
						</b-form-group> -->
					</div>
				</div>
			</div>
			<div v-if="show.show1">
				<div class="body">
					<div>
						<b-form class="bs-label">
							<b-row>
								<b-col>
									<b-form-group id="input-group-8" :label="lbLinkdominion" label-for="input-8" :description="$t('bs-domain-or-ip-address')">
										<b-form-input
										id="input-8"
										v-model="link"
										:placeholder="phName"
										required
										class="bs-input"
										></b-form-input>
									</b-form-group>
								</b-col>
								<b-col>
									<b-form-group id="input-group-9" :label="lbDescription" label-for="input-9">
										<b-form-input
										id="input-9"
										v-model="description"
										:placeholder="phDescription"
										required
										class="bs-input"
										></b-form-input>
									</b-form-group>
								</b-col>

								<b-col sm="auto">
									<b-button @click="addDomainReleased" variant="primary" style="margin-top:26px;">{{$t('bs-add').toUpperCase()}}</b-button>
								</b-col>
							</b-row>
						</b-form>
					</div>

					<b-table responsive bordered borderless striped hover show-empty
						class="local-striped-table"
						head-variant="light"
						table-variant="light"
						:items="domainReleased"
						:fields="fields"
					>
					<template #cell(actions)="row">
						<b-link href="#" @click="itemDelete(1, row.item, row.index)">
							<i class="fa fa-trash-o fa-2x bs-trash" aria-hidden="true"></i>
						</b-link>
					</template>
					<template #empty="scope">
						<div class="text-center">{{ $t('bs-empty-released-origin-table') }}</div>
					</template>
				</b-table>
			</div>
		</div>

		<div v-if="show.show2">
			<div class="body">
				<div>
					<b-form class="bs-label">
						<b-row>
							<b-col>
								<b-form-group id="input-group-21" :label="lbLinkdominion" label-for="input-21" :description="$t('bs-domain-or-ip-address')">
									<b-form-input
									id="input-21"
									v-model="link"
									required
									:placeholder="phName"
									class="bs-input"
									></b-form-input>
								</b-form-group>
							</b-col>
							<b-col>
								<b-form-group id="input-group-22" :label="lbDescription" label-for="input-22">
									<b-form-input
									id="input-22"
									v-model="description"
									required
									:placeholder="phDescription"
									class="bs-input"
									></b-form-input>
								</b-form-group>
							</b-col>

							<b-col sm="auto">
								<b-button @click="addDomainBlocked" variant="primary" style="margin-top:26px;">{{$t('bs-add').toUpperCase()}}</b-button>
							</b-col>
						</b-row>
					</b-form>
				</div>

				<b-table responsive bordered borderless striped hover show-empty
					class="local-striped-table"
					head-variant="light"
					table-variant="light"
					:items="domainBlocked"
					:fields="fields"
				>
				<template #cell(actions)="row">
					<b-link href="#" @click="itemDelete(2, row.item, row.index)">
						<i class="fa fa-trash-o fa-2x bs-trash" aria-hidden="true"></i>
					</b-link>
				</template>
				<template #empty="scope">
					<div class="text-center">{{ $t('bs-empty-blocked-origin-table') }}</div>
				</template>
			</b-table>
		</div>
	</div>
	<div v-if="show.show3">
		<b-container fluid="lg">
			<b-row class="bodyNew">
				<b-col lg>
					<b-row class='ml-2'>
						<b-col class="px-0 mx-0" cols="auto" id="chats-opened-simultaneously-customer">
							{{$t('bs-chats-opened-simultaneously-customer')}}
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</b-col>
						<b-tooltip target="chats-opened-simultaneously-customer" triggers="hover" placement="right" :variant="tooltipVariant">
							{{$t('bs-tooltip-chats-opened-simultaneously-custom')}}
						</b-tooltip>
						<b-col>
						</b-col>
						<b-col cols="auto">
							<input type="number" v-model="chatSimCli" min="0" style="border: none;width: 60px;">
							<b-link href="#foo" @click="addNumber(1)"><i class="fa fa-plus fa-1x add" aria-hidden="true"></i></b-link>
							<b-link href="#foo" @click="removeNumber(1)"><i class="fa fa-minus fa-1x remove" aria-hidden="true"></i></b-link>
						</b-col>
					</b-row>
					<div class="ml-2 spantext">
						<span>{{$t('bs-if-it-is-set-to-0')}}</span>
					</div>
				</b-col>
			</b-row>
			<b-row class="bodyNew">
				<b-col lg>
					<b-row class='ml-2'>
						<b-col class="px-0 mx-0" cols="auto" id="chats-automatic">
							{{$t('bs-link-automatically-chats-with-attendants')}} ({{$t('bs-amount-of-chat-per-attendant')}})
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</b-col>
						<b-tooltip target="chats-automatic" triggers="hover" placement="right" :variant="tooltipVariant">
							{{$t('bs-tooltip-chat-automatic')}}
						</b-tooltip>
						<b-col>
						</b-col>
						<b-col cols="auto">
							<input type="number" v-model="chatDepartAutomaticNumberLimit" min="0" style="border: none;width: 60px;">
							<b-link href="#foo" @click="addNumber(3)"><i class="fa fa-plus fa-1x add" aria-hidden="true"></i></b-link>
							<b-link href="#foo" @click="removeNumber(3)"><i class="fa fa-minus fa-1x remove" aria-hidden="true"></i></b-link>
						</b-col>
					</b-row>
					<div class="ml-2 spantext">
						<span>{{$t('bs-if-it-is-set-to-0')}}</span>
					</div>
					<b-row class="ml-1 mt-1">
						<b-col>
							<b-button class="caret mr-2" @click="showOptionDepartments" variant="primary" size="sm">{{$t('bs-select-departments')}}</b-button>
							<b-button class="caret mr-2" @click="showOptionAgents" variant="primary" size="sm">{{$t('bs-select-attendants')}}</b-button>
						</b-col>
					</b-row>
				</b-col>
			</b-row>
			<div class="modal fade" id="modalEvents" tabindex="-1" aria-labelledby="modalEvents" aria-hidden="true" data-keyboard="false">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-header border-0 p-0">
								<h5 class="modal-title" id="modalEvents">{{$t('bs-chat-limits-per-department')}}</h5>
								<div cols="auto" class="px-0 mx-0" id="tk-opened-simultaneously-customer">
									&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
								</div>
								<b-tooltip target="tk-opened-simultaneously-customer" triggers="hover" placement="right" :variant="tooltipVariant">
									{{$t('bs-individually-limit-the-number-of-chats')}}
								</b-tooltip>
							</div>
							<br>
							<div class="modal-body-custom">
								<b-form-group v-slot="{ ariaDescribedby }">
									<b-form-checkbox-group
										id="checkbox-group-2"
										v-model="departmentsSelected"
										:aria-describedby="ariaDescribedby"
										multiple
										name="flavour-2"
										v-for="(item, index) in optionsDepart" :key="index"
									>
										<b-form-checkbox :value="item.id" class="mt-2">
											{{item.name}}
										</b-form-checkbox>
										<span style="float: right;">
											<input type="number" v-model="departmentsSelectedUnique[index].chatLimit" min="0" style="border: none;width: 60px;">
										</span>
									</b-form-checkbox-group>
								</b-form-group>
							</div>
							<div class="modal-footer border-0">
								<button type="button" data-dismiss="modal"  id="btn-department" class="btn btn-danger">
									{{$t('bs-close')}}
								</button>
							</div>
							<!-- <div>departmentsSelected: <strong>{{ departmentsSelected }}</strong></div> -->
							<!-- <div>departmentsSelectedUnique: <strong>{{ departmentsSelectedUnique }}</strong></div> -->
						</div>
					</div>
				</div>
				<div class="modal fade" id="modalAgents" tabindex="-1" aria-labelledby="modalAgents" aria-hidden="true" data-keyboard="false">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-header border-0 p-0">
								<h5 class="modal-title" id="modalAgents">{{$t('bs-agents')}}</h5>
							</div>
							<br>
							<div class="modal-body-custom">
								<b-form-group v-slot="{ ariaDescribedby }">
									<b-form-checkbox-group
										id="checkbox-group-2"
										v-model="agentsSelected"
										:aria-describedby="ariaDescribedby"
										name="flavour-2"
										v-for="(item, index) in optionsAgents" :key="index"
									>
										<b-form-checkbox :value="item.id">{{item.name}} <span style="color: #a4a4a4">- {{item.email}}</span></b-form-checkbox>
									</b-form-checkbox-group>
								</b-form-group>
							</div>
							<b-row class="mt-2">
								<b-col cols="4" style="padding-top:30px;">
									<span class="bs-label">{{ $t('bs-distribution') }}</span>
								</b-col>
								<b-col>
									<b-form-select 
										v-model="modelDistribution" 
										:options="optionsWS" 
										size="sm" class="mt-3"
									>
									</b-form-select>
								</b-col>
							</b-row>
							<div class="modal-footer border-0">
								<button type="button" data-dismiss="modal" id="btn-department" class="btn btn-danger">
									{{$t('bs-close')}}
								</button>
							</div>
							<!-- <div>Selected: <strong>{{ agentsSelected }}</strong></div> -->
						</div>
					</div>
				</div>
		</b-container>
	</div>
	<div v-if="show.show4">
		<b-container fluid="lg">
			<b-row class="bodyNew">
				<b-col lg>
					<b-row class='ml-2'>
						<b-col cols="auto" class="px-0 mx-0" id="tk-opened-simultaneously-customer">
							{{$t('bs-tk-opened-simultaneously-customer')}}
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</b-col>
						<b-tooltip target="tk-opened-simultaneously-customer" triggers="hover" placement="right" :variant="tooltipVariant">
							{{$t('bs-tooltip-tk-opened-simultaneously-customer')}}
						</b-tooltip>
						<b-col>
						</b-col>
						<b-col cols="auto">
							<input type="number" v-model="ticketSimCli" min="0" style="border: none;width: 60px;">
							<b-link href="#foo" @click="addNumber(2)"><i class="fa fa-plus fa-1x add" aria-hidden="true"></i></b-link>
							<b-link href="#foo" @click="removeNumber(2)"><i class="fa fa-minus fa-1x remove" aria-hidden="true"></i></b-link>
						</b-col>
					</b-row>
					<div class="ml-2 spantext">
						<span>{{$t('bs-if-it-is-set-to-0')}}</span>
					</div>
				</b-col>
			</b-row>
			<!-- <b-row class="bodyNew">
				<b-col lg>
					<b-row class='ml-2'>
						<b-col cols="auto" class="px-0 mx-0" id="tk-opened-simultaneously-customer">
							{{$t('bs-pre-selected-status-when-responding')}}:
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
							<div class="spantext">
								<span>{{$t('bs-when-responding-to-ticket-the-status')}}</span>
							</div>
						</b-col>
						<b-tooltip target="tk-opened-simultaneously-customer" triggers="hover" placement="right" :variant="tooltipVariant">
							{{$t('bs-tooltip-tk-opened-simultaneously-customer')}}
						</b-tooltip>
						<b-col>
						</b-col>
						<b-col cols="auto">
							<b-form-select v-model="selectedStatus" :options="optionsStatus"></b-form-select>
						</b-col>
					</b-row>
				</b-col>
			</b-row> -->
		</b-container>
	</div>

	<div v-if="show.show5">
		<config-category :usuario="usuario"></config-category>
	</div>

	<div v-if="show.show6">
		<b-row class="mt-1 mb-1">
			<b-col cols="6" v-show="showPassword">
				<b-form-group id="input-group-2" :label="$t('bs-sender-name')+': *'" label-for="input-2">
					<b-form-input
					id="input-2"
					v-model="form.nameSender"
					:placeholder="$t('bs-enter-name')"
					required
					></b-form-input>
				</b-form-group>
			</b-col>
			<b-col cols="6" v-show="showPassword">
				<b-form-group id="input-group-2" :label="$t('bs-sender-email')+': *'" label-for="input-2">
					<b-form-input
					id="input-2"
					v-model="form.emailSender"
					:placeholder="$t('bs-enter-email')"
					required
					></b-form-input>
				</b-form-group>
			</b-col>
			<b-col cols="12">
				<b-form-group id="input-group-2" :label="$t('bs-title')+': *'" label-for="input-2">
					<b-form-input
					id="input-2"
					v-model="form.title"
					:placeholder="$t('bs-enter-text')"
					required
					></b-form-input>
				</b-form-group>
			</b-col>
			<b-col cols="auto">
				<b-form-select v-model="comandSelected" :options="optionsComands" size="sm">
					<template #option="{ option, index }">
						<option style="color:#a4a4a4 !important" :value="option.value">{{ option.text }}</option>
					</template>
				</b-form-select>
            </b-col>
            <b-col>
                <b-form-select :disabled="blockSelect" @change="showLoadEmail" v-model="selected" :options="optionsEmail" size="sm"></b-form-select>
            </b-col>
            <b-col cols="auto">
                <b-form-select :disabled="blockSelect" @change="showLoadEmail" v-model="defaultLanguage" :options="optionsLanguages" size="sm"></b-form-select>
            </b-col>
        </b-row>
		<config-emails v-if="showEditor" :title="form.title" :nameSender="form.nameSender" :emailSender="form.emailSender" :valueHtml="valueHtml" :selected="selected" :defaultLanguage="defaultLanguage" :usuario="usuario"></config-emails>
	</div>

	<div v-if="show.show7">
		<config-faq-robot :usuario="usuario"></config-faq-robot>
	</div>
</b-container>
</div>
</template>

<script>
export default {
	data(){
		return {
			logout: {
				link: '',
				active: false,
			},
			showTicket: false,
			showChat: false,
			showAdmin: false,
			editPerfilClient: true,
			editPerfilAttendants: true,
			linkLogout: this.$t('bs-logout-redirect-link')+' ('+this.$t('bs-client')+')',
			lbLinkClient: 'Link para login de clientes',
			linkClient: '',
			lbLinkdominion: this.$t('bs-origin'),
			lbDescription: this.$t('bs-description'),
			phName: this.$t('bs-enter-the-origin'),
			phLink: this.$t('bs-enter-link'),
			phDescription: this.$t('bs-enter-description'),
			tooltipVariant: "secondary",
			ss: {
				'ss0': 'tab active',
				'ss1': 'tab',
				'ss2': 'tab',
				'ss3': 'tab',
				'ss4': 'tab',
				'ss5': 'tab',
				'ss6': 'tab',
				'ss7': 'tab',
			},
			show: {
				'show': true,
				'show0': true,
				'show1': false,
				'show2': false,
				'show3': false,
				'show4': false,
				'show5': false,
				'show6': false,
				'show7': false,
			},
			link: "",
			description: "",
			domainReleased: [],
			domainBlocked: [],
			fields: [
				{
					key: 'link',
					label: this.$t('bs-origin')
				},
				{
					key: 'description',
					label: this.$t('bs-description')
				},
				{
					key: 'actions',
					label: this.$t('bs-actions')
				}
			],
			openCompany: [
			{ am: '00:00', pm: '00:00'},
			{ am: '00:00', pm: '00:00'},
			{ am: '00:00', pm: '00:00'},
			{ am: '00:00', pm: '00:00'},
			{ am: '00:00', pm: '00:00'},
			{ am: '00:00', pm: '00:00'},
			{ am: '00:00', pm: '00:00'},
			],
			chatSimCli: 0,
			chatDepartAutomaticNumberLimit: 0,
			ticketSimCli: 0,
			acesso_anonymous: true,
			titleLogin: '',
			subtitleLogin: '',
			titlechatclient: '',
			titleticketclient: '',
			nameRobot: '',
			selectedStatus: 'IN_PROGRESS',
			optionsStatus: [
				{ value: 'IN_PROGRESS', text: this.$t('bs-in-progress') },
				{ value: 'CLOSED', text: this.$t('bs-closed') },
				{ value: 'RESOLVED', text: this.$t('bs-resolved') },
			],
			departmentsSelected: [],
			departmentsSelectedUnique: [],
			agentsSelected: [],
			optionsDepart: [],
			// SEND EMAIL
			optionsAgents: [],
            selected: '',
            defaultLanguage: '',
			optionsEmail: [
                { value: 'opened', text: this.$t('bs-ticket-created') },
                { value: 'replied', text: this.$t('bs-answered-ticket') },
                { value: 'closed', text: this.$t('bs-closed-ticket') },
                { value: 'password', text: this.$t('bs-password-change') },
                { value: 'attendant', text: this.$t('bs-notification-for-attendant') },
            ],
			optionsComands: [
                { value: 'name', text: '{name} - '+this.$t('bs-client-name') },
                { value: 'company', text: '{company} - '+this.$t('bs-company-name') },
                { value: 'ticket_id', text: '{ticket_id} - '+this.$t('bs-ticket') },
                { value: 'link_login', text: '{link_login} - '+this.$t('bs-link')+' '+this.$t('bs-attendant-login') },
                { value: 'redirect_url', text: '{redirect_url} - '+this.$t('bs-link')+' '+this.$t('bs-password-change') },
                // { value: 'attendant', text: this.$t('bs-notification-for-attendant') },
            ],
			comandSelected: 'name',
            optionsLanguages: [],
            showEditor: false,
			valueHtml: '',
			form: {
				nameSender: '',
				emailSender: '',
				title: '',
			},
			showPassword: true,
			blockSelect: false,
			modelDistribution: 'status',
			optionsWS: [
				{ value: 'status' , text: this.$t('bs-use-status') },
				{ value: 'schedule' , text: this.$t('bs-use-schedule') },
			],
		}
	},
	props:{
		usuario:Object,
		csid: String,
		is_helpdesk: String,
        base_url: {
            type: String,
            default: ''
        },
	},
	created(){
		this.$root.$refs.CompanyConfig = this;
	},
	mounted(){
		var vm = this;
		
		vm.getInfoGeneral();
		//Languages
		this.defaultLanguage = this.usuario.language;
		this.$store.state.languages.forEach(item => {
			this.optionsLanguages.push({value: item.key, text: item.desc});
		});
	},
	methods:{
		getInfoGeneral(){
			var vm = this;
			var url = `${this.base_url}/company-config/domains-settings/${vm.csid}`;
			axios.get(url).then(function(r_resposta){
				// console.log(r_resposta.data.general);
				vm.domainReleased = r_resposta.data.released ? r_resposta.data.released : [];
				vm.domainBlocked = r_resposta.data.blocked ? r_resposta.data.blocked : [];
				vm.ticketSimCli = r_resposta.data.settings_ticket;
				vm.showChat = r_resposta.data.general.showChat;
				vm.showTicket = r_resposta.data.general.showTicket;
				vm.showAdmin = r_resposta.data.general.showAdmin;
				vm.logout.link   = r_resposta.data.general.client_logout.link ? r_resposta.data.general.client_logout.link: '';
				vm.logout.active = r_resposta.data.general.client_logout.active ? r_resposta.data.general.client_logout.active: '';
				vm.editPerfilClient  = r_resposta.data.general.editPerfilClient;
				vm.editPerfilAttendants  = r_resposta.data.general.editPerfilAttendants;
				vm.acesso_anonymous  = r_resposta.data.general.acesso_anonymous;
				vm.titleLogin  = r_resposta.data.general.titleLogin;
				vm.subtitleLogin  = r_resposta.data.general.subtitleLogin;
				vm.titlechatclient  = r_resposta.data.general.titlechatclient;
				vm.titleticketclient  = r_resposta.data.general.titleticketclient;
				vm.nameRobot  = r_resposta.data.general.nameRobot;
				vm.selectedStatus  = r_resposta.data.general.selectedStatus;
				
				if (!Array.isArray(r_resposta.data.general.departmentsSelected)) {
					// Converte vm.departmentsSelected em um array
					vm.departmentsSelected = [r_resposta.data.general.departmentsSelected];
				}else{
					vm.departmentsSelected = r_resposta.data.general.departmentsSelected;
				}
				
				if (!Array.isArray(r_resposta.data.general.agentsSelected)) {
					// Converte vm.agentsSelected em um array
					vm.agentsSelected = [r_resposta.data.general.agentsSelected];
				}else{
					vm.agentsSelected = r_resposta.data.general.agentsSelected;
				}
				
				if(r_resposta.data.general.modelDistribution == undefined){
					vm.modelDistribution = 'status';
				}else{
					vm.modelDistribution = r_resposta.data.general.modelDistribution;
				}

				// vm.form.nameSender = r_resposta.data.sender.name_sender;
				// if(r_resposta.data.sender.name_sender.trim() == '' || r_resposta.data.sender.name_sender == null){
				// 	vm.form.nameSender = 'Builderall Suporte';
				// }

				// vm.form.emailSender = r_resposta.data.sender.email_sender;
				// if(r_resposta.data.sender.email_sender.trim() == '' || r_resposta.data.sender.email_sender == null){
				// 	vm.form.emailSender = 'noreply.ba-support@builderall.com';
				// }
				
				if(Array.isArray(r_resposta.data.settings_chat)){
					vm.chatSimCli = r_resposta.data.settings_chat[0].chatSimCli;
					vm.chatDepartAutomaticNumberLimit = r_resposta.data.settings_chat[1].chatDepartAutomaticNumberLimit;
				}else{
					vm.chatSimCli = r_resposta.data.settings_chat;
				}

				vm.departmentsSelectedUnique = [];
				if(r_resposta.data.general.departmentsSelectedUnique == undefined){
					for (let index = 0; index < vm.optionsDepart.length; index++) {
						vm.departmentsSelectedUnique.push({ id: vm.optionsDepart[index].id, chatLimit: vm.chatDepartAutomaticNumberLimit });
					}
				}else{
					for (let index = 0; index < r_resposta.data.general.departmentsSelectedUnique.length+20; index++) {
						vm.departmentsSelectedUnique.push({ 
							id: r_resposta.data.general.departmentsSelectedUnique[index].id, 
							chatLimit: r_resposta.data.general.departmentsSelectedUnique[index].chatLimit <= 0 ? 1 : r_resposta.data.general.departmentsSelectedUnique[index].chatLimit 
						});
					}
				}

				if(vm.titlechatclient == undefined || vm.titlechatclient == ''){
					vm.titlechatclient = vm.$t('bs-chat-in-real-time-with-one-of-our-experts');
				}
				
				if(vm.titleticketclient == undefined || vm.titleticketclient == ''){
					vm.titleticketclient = vm.$t('bs-inform-your-problem-doubt');
				}
			}).catch(function (error) {
				console.log(error);
			});
		},
		showLoadEmail(){
			if(this.selected == ''){
				return;
			}
			if(this.selected == 'password'){
				this.showPassword = false;
			}else{
				this.showPassword = true;
			}
			this.showEditor = false;
			this.blockSelect = true;
			axios.get(`company-config/any-custom-email`, {
                params: {
                    type: this.selected,
                    defaultLanguage: this.defaultLanguage,
                }
            }).then(({data}) => {
                setTimeout(() => {
					this.valueHtml = data[0];
					this.form.title = data[1];
					this.form.nameSender = data[2];
					this.form.emailSender = data[3];
					this.showEditor = true;
					
					setTimeout(() => {
						this.blockSelect = false;
					}, 100);
				}, 500);
            }).catch(err => {
                console.error(err);
            });
		},
		showOptionAgents(){
			var vm = this;
			axios.get('company-config/get-agents').then(function(r_resposta){
				// console.log(r_resposta.data.result)
				vm.optionsAgents = r_resposta.data.result;
				$("#modalAgents").modal("show");
			}).catch(function (error) {
				console.log(error);
			});
		},
		showOptionDepartments(){
			var vm = this;
			// axios.get('department/get-department').then(function(r_resposta){
			axios.get('company-config/get-departments').then(function(r_resposta){
				vm.optionsDepart = r_resposta.data.result;
				if(vm.departmentsSelectedUnique.length == 0){
					vm.departmentsSelectedUnique = [];
					for (let index = 0; index < r_resposta.data.result.length; index++) {
						vm.departmentsSelectedUnique.push({ id: vm.optionsDepart[index].id, chatLimit: vm.chatDepartAutomaticNumberLimit });
					}
				}
				
				$("#modalEvents").modal("show");
			}).catch(function (error) {
				console.log(error);
			});
		},
		getBaseUrl(hash_code) {
		    // Nome do host
		    var hostName = location.hostname;
		    var path = 'client';
	        var path2 = 'login';

		    if (hostName === "localhost") {
		        // Endereço após o domínio do site
		        var pathname = window.location.pathname;
		        // Separa o pathname com uma barra transformando o resultado em um array
		        var splitPath = pathname.split('/');

		        // Obtém o segundo valor do array, que é o nome da pasta do servidor local
		        //var path = splitPath[1];

		        var baseUrl = "http://" + hostName + "/" + path + "/" + hash_code  + "/" +path2;
		    } else {
		         baseUrl = "http://" + hostName + "/" + path + "/" + hash_code  + "/" +path2;
		    }

		    return baseUrl;
		},
		onSubmit(){
			var vm = this;
			var url = `${this.base_url}/company-config/chat-ticket`;

			if(this.showTicket == true && this.showChat == true){
				return vm.$snotify.info( vm.$t('bs-one-needs-to-be-activated-before-saving'), vm.$t('bs-info'));
			}

			axios.post(url, {
				settings_chat: JSON.stringify([
					{ chatSimCli: vm.chatSimCli },
					{ chatDepartAutomaticNumberLimit: vm.chatDepartAutomaticNumberLimit },
				]),
				ticketSimCli: vm.ticketSimCli,
				csid: vm.csid,
				logout: vm.logout,
				showChat: vm.showChat,
				showTicket: vm.showTicket,
				showAdmin: vm.showAdmin,
				editPerfilClient: vm.editPerfilClient,
				editPerfilAttendants: vm.editPerfilAttendants,
				acesso_anonymous: vm.acesso_anonymous,
				titleLogin: vm.titleLogin,
				subtitleLogin: vm.subtitleLogin,
				titlechatclient: vm.titlechatclient,
				titleticketclient: vm.titleticketclient,
				nameRobot: vm.nameRobot,
				selectedStatus: vm.selectedStatus,
				departmentsSelected: vm.departmentsSelected,
				departmentsSelectedUnique: vm.departmentsSelectedUnique,
				agentsSelected: vm.agentsSelected,
				modelDistribution: vm.modelDistribution,
			}).then(function(response){
				if(response.data.success){
					vm.$snotify.success( vm.$t('bs-saved-successfully'), vm.$t('bs-success'));
				}else{
					vm.$snotify.error( vm.$t('bs-error-trying-to-save') , vm.$t('bs-error'));
				}
			}).catch(function(){
				console.log('FAILURE!!');
			});
		},
		addDomainReleased(){
			var vm = this;
			var url = `${this.base_url}/company-config/domain-released`;

			if(vm.link == "" || vm.description == ""){
				vm.$snotify.info(vm.$t('bs-invalid-fields'), vm.$t('bs-info'));
				return;
			}

			for (var i = 0; i < vm.domainReleased.length; i++) {
				if(vm.domainReleased[i].link == vm.link){

					vm.$snotify.info(vm.$t('bs-link-already-registered'), vm.$t('bs-info'));
					vm.link = "";
					vm.description = "";
					return;
				}
			}

			var item = {
				link: vm.link,
				description: vm.description,
			};
			vm.domainReleased.push(item);

			axios.post(url, {
				domainReleased: vm.domainReleased,
				csid: vm.csid,

			}).then(function(response){
				//console.log(response.data.created);
				if(response.data.success){
					// var my_object = {
					// 	link: vm.link,
					// 	description: vm.description,
					// };

					// vm.domainReleased.push(my_object);
					vm.link = "";
					vm.description = "";
					vm.$snotify.success( vm.$t('bs-saved-successfully'), vm.$t('bs-success'));
				}else{
					vm.$snotify.error( vm.$t('bs-error-trying-to-save') , vm.$t('bs-error'));
				}
			}).catch(function(){
				console.log('FAILURE!!');
			});
		},
		addDomainBlocked(){
			var vm = this;
			var url = `${this.base_url}/company-config/domain-blocked`;

			if(vm.link == "" || vm.description == ""){
				vm.$snotify.info(vm.$t('bs-invalid-fields'), vm.$t('bs-info'));
				return;
			}

			for (var i = 0; i < vm.domainBlocked.length; i++) {

				if(vm.domainBlocked[i].link == vm.link){
					vm.$snotify.info(vm.$t('bs-link-already-registered'), vm.$t('bs-info'));
					vm.link = "";
					vm.description = "";
					return;
				}
			}

			var item = {
				link: vm.link,
				description: vm.description,
			};

			vm.domainBlocked.push(item);

			axios.post(url, {
				domainBlocked: vm.domainBlocked,
				csid: vm.csid,

			}).then(function(response){
				//console.log(response.data.created);
				if(response.data.success){
					// var my_object = {
					// 	link: vm.link,
					// 	description: vm.description,
					// };

					// vm.domainReleased.push(my_object);
					vm.link = "";
					vm.description = "";
					vm.$snotify.success( vm.$t('bs-saved-successfully'), vm.$t('bs-success'));
				}else{
					vm.$snotify.error( vm.$t('bs-error-trying-to-save') , vm.$t('bs-error'));
				}
			}).catch(function(){
				console.log('FAILURE!!');
			});
		},
		itemDelete(type, item, index){
			var vm = this;

			if(type == 1){

				Swal.fire({
					title: this.$t('bs-are-you-sure'),
					text: this.$t('bs-you-wont-be-able-to-revert-this'),
					icon: 'warning',
					showCancelButton: true,
					cancelButtonText: this.$t('bs-cancel'),
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: this.$t('bs-yes-delete-it'),
				}).then((result) => {
					if (result.isConfirmed) {

						vm.domainReleased.splice(index, 1);
						axios.post(`${this.base_url}/company-config/domain-released/delete`, {
							domainReleased: vm.domainReleased,
							csid: vm.csid,
						}).then(function(response){
							//console.log(response.data.created);
							if(response.data.success){
								vm.$snotify.success(vm.$t('bs-successfully-removed'), vm.$t('bs-success'));
								Swal.fire(
									vm.$t('bs-deleted'),
									vm.$t('bs-your-file-has-been-deleted'),
									'success'
								);
							}else{
								vm.$snotify.error(vm.$t('bs-error-removing'), vm.$t('bs-error'));
							}
						}).catch(function(){
							console.log('FAILURE!!');
						});
					}
				});
			}else if(type == 2){
				Swal.fire({
					title: this.$t('bs-are-you-sure'),
					text: this.$t('bs-you-wont-be-able-to-revert-this'),
					icon: 'warning',
					showCancelButton: true,
					cancelButtonText: this.$t('bs-cancel'),
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: this.$t('bs-yes-delete-it'),
				}).then((result) => {
					if (result.isConfirmed) {

						vm.domainBlocked.splice(index, 1);
						axios.post(`${this.base_url}/company-config/domain-blocked/delete`, {
							domainBlocked: vm.domainBlocked,
							csid: vm.csid,
						}).then(function(response){
							//console.log(response.data.created);
							if(response.data.success){

								vm.$snotify.success(vm.$t('bs-successfully-removed'), vm.$t('bs-success'));
								Swal.fire(
									vm.$t('bs-deleted'),
									vm.$t('bs-your-file-has-been-deleted'),
									'success'
								);
							}else{
								vm.$snotify.error(vm.$t('bs-error-removing'), vm.$t('bs-error'));
							}
						}).catch(function(){
							console.log('FAILURE!!');
						});
					}
				});
			}
		},
		showIB(item){
			var vm = this;
			vm.show.show0 = false;
			vm.show.show1 = false;
			vm.show.show2 = false;
			vm.show.show3 = false;
			vm.show.show4 = false;
			vm.show.show5 = false;
			vm.show.show6 = false;
			vm.show.show7 = false;
			vm.ss.ss0 = 'tab';
			vm.ss.ss1 = 'tab';
			vm.ss.ss2 = 'tab';
			vm.ss.ss3 = 'tab';
			vm.ss.ss4 = 'tab';
			vm.ss.ss5 = 'tab';
			vm.ss.ss6 = 'tab';
			vm.ss.ss7 = 'tab';
			if(item == 0){
				vm.show.show0 = true;
				vm.ss.ss0 = 'tab active';
			}else if(item == 1){
				vm.show.show1 = true;
				vm.ss.ss1 = 'tab active';
			}else if(item == 2){
				vm.show.show2 = true;
				vm.ss.ss2 = 'tab active';
			}else if(item == 3){
				vm.show.show3 = true;
				vm.ss.ss3 = 'tab active';
			}else if(item == 4){
				vm.show.show4 = true;
				vm.ss.ss4 = 'tab active';
			}else if(item == 5){
				vm.show.show5 = true;
				vm.ss.ss5 = 'tab active';
			}else if(item == 6){
				vm.show.show6 = true;
				vm.ss.ss6 = 'tab active';
			}else if(item == 7){
				vm.show.show7 = true;
				vm.ss.ss7 = 'tab active';
			}
		},
		btnBack(){
			var vm = this;
			vm.$emit('back');
		},
		addNumber(value){
			var vm = this;
			if(value == 1){
				vm.chatSimCli = parseInt(vm.chatSimCli) + 1;
			}else if(value == 2){
				vm.ticketSimCli = parseInt(vm.ticketSimCli) + 1;
			}else if(value == 3){
				vm.chatDepartAutomaticNumberLimit = parseInt(vm.chatDepartAutomaticNumberLimit) + 1;
			}
		},
		removeNumber(value){
			var vm = this;
			if(value == 1){
				vm.chatSimCli = parseInt(vm.chatSimCli) - 1;
			}else if(value == 2){
				vm.ticketSimCli = parseInt(vm.ticketSimCli) - 1;
			}else if(value == 3){
				vm.chatDepartAutomaticNumberLimit = parseInt(vm.chatDepartAutomaticNumberLimit) - 1;
			}
		},
		addCompanyName(company){
			if(company == 'title'){
				if(this.titleLogin == undefined){
					this.titleLogin = ''+ '{company_name}';
				}else{
					this.titleLogin = this.titleLogin + ' {company_name}';
				}
			}else{
				if(this.subtitleLogin == undefined){
					this.subtitleLogin = ''+ '{company_name}';
				}else{
					this.subtitleLogin = this.subtitleLogin + ' {company_name}';
				}
				
			}
		}
	}
};
</script>

<style lang="scss" scoped>
.caret {
  cursor: pointer;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

.bui-alert {
  background: url(/images/meta/corner.png) left top no-repeat,
    url(/images/meta/wave.png) right bottom/100% no-repeat,
    transparent linear-gradient(180deg, #5e81f4 0%, #1665d8 100%);
  border-radius: 16px;
  border: none;
  padding-top: 1.2rem;
  padding-left: 2rem;
  color: #fff;
  p {
    font-size: 0.85rem;
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.8);
  }
  .btn {
    background: #ffffff38;
    border: unset;
    font-weight: normal !important;
    text-transform: capitalize;
    border-radius: 8px;
  }
}

.isactive{
	margin:0;
	padding:10px;
	padding-top:0px;
	padding-bottom:0px;
	font: normal normal bold 14px/18px Muli;
	color: #707070;
}

.bs-input{
	border: 1px solid #a4a4a4;
	padding-top: 18px;
	padding-bottom: 18px;
	padding-left: 20px;
	text-align: left;
	font: normal normal bold 14px/18px Muli;
	letter-spacing: 0px;
	opacity: 0.75;
}
.justify_switch{
	text-align: right;
	margin-top: -38px;
	margin-right: 4px;
}


.tab{
	padding: 8px;
	color:#A4A4A4;
	border-bottom: 1px solid #BDBDBD;
	font-weight: bold;
	text-decoration: none;
	margin-left: 10px;
	text-transform: uppercase;
}
.active, .tab:hover{
	color: #0080fc;
	padding-left: 10px;
	border-bottom: 3px solid #0080fc;
}

.bodyNew{
	background-color: white;
	margin-bottom:10px;
	padding: 15px;
	padding-left: 1px;
	font-weight: bold;
	color: #0080FC;
}
.spantext{
	background: #FFFFFF 0% 0% no-repeat padding-box;
	border-radius: 5px;
	color: #a4a4a4;
	margin-top: 6px;
	font-size: 12px;
}

.add{
	color: #0080FC;
	padding-right: 4px;
}
.remove{
	color: red;
}

.bs-m-spacing{
	padding: 8px;
	text-transform: uppercase;
	margin-left: 1px;
}

@media screen and (max-width: 576px) {

	.bs-m-spacing{
		justify-content: center;
		text-align: center;
		padding: 8px;
		margin-left: 1px;
	}

	.active, .tab:hover{
		color: #0080fc;
		padding-left: 10px;
	}
}

@media screen and (max-width: 1024px) {

	.bs-m-spacing{
		justify-content: center;
		text-align: center;
		padding: 8px;
		margin-left: 1px;
	}

	.active, .tab:hover{
		color: #0080fc;
		padding-left: 10px;
	}
}
.modal-content {
    background: #f3f7ff 0% 0% no-repeat padding-box;
    box-shadow: 0px 14px 32px #00000040;
    border-radius: 10px;
    border: none;
    padding-top: 40px;
    padding-left: 40px;
    padding-right: 40px;
}

.modal-title {
    font: normal normal bold 20px/26px Muli;
    letter-spacing: 0px;
    color: #434343;
}

.modal {
    background-color: #59607173;
}

.modal .close {
    background: #acb8d8;
    color: white;
    text-shadow: none;
    padding: 2px;
    margin-top: 1px;
    border-radius: 100%;
    font-size: 15px;
    height: 25px;
    width: 25px;
    margin-right: 2px;
}

.modal-body label {
    font: normal normal bold 14px/35px Muli;
    letter-spacing: 0px;
    color: #656565;
}

.modal-body select {
    background: #fafbfc 0% 0% no-repeat padding-box;
    border: 1px solid #dddddd;
    border-radius: 3px;
}

.example-open .modal-backdrop {
    background-color: red;
}

@media only screen and (max-width: 575px) {
    .content {
        margin-right: 10px;
        margin-left: 10px;
    }

    .card-header {
        padding-top: 20px;
        height: 80px;
        zoom: 120%;
    }

    .modal-dialog {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
    }

    .modal-content {
        height: auto;
        min-height: 100%;
        border-radius: 0;
    }

    .modal-footer {
        padding-right: 0px;
        padding-left: 0px;
    }

    #btn-new-chat {
        max-width: 140px;
        padding-left: 6px;
        padding-right: 6px;
    }
}
</style>
