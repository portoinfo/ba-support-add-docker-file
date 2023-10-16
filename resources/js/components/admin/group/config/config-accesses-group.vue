<template>
	<div>
		<h3>{{$t('bs-chat')}}</h3>
		<div class="topcard">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-admin')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-grants-access-to-all-chat-functionality')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-chat-admin">
							<span class="textcardtitle">{{$t('bs-admin')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-chat-admin" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-grants-access-to-all-chat-functionality')}}
						</b-tooltip>
					</span>
				</b-col>
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfigFF(0, 'ADMIN')" @click="sendSettingsFF(0, 'ADMIN')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
		<div class="topcard">
			<b-row class="ml-3">
				<b-col>
					<span class="pb-2" v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-full-line')}}</span><br>
						<span class="textcardsubtitle">{{$t('bs-full-line-description')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-full-line">
							<span class="textcardtitle">{{$t('bs-full-line')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-full-line" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-full-line-description')}}
						</b-tooltip>
					</span>
				</b-col>
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfigFF(0, 'QUEUECOMPLETE')" @click="sendSettingsFF(0, 'QUEUECOMPLETE')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
		<div class="topcard">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-turn-into-ticket')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-turn-into-ticket-description')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-turn-into-ticket">
							<span class="textcardtitle">{{$t('bs-turn-into-ticket')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-turn-into-ticket" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-turn-into-ticket-description')}}
						</b-tooltip>
					</span>
				</b-col>
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfigFF(0, 'TRANSFORM')" @click="sendSettingsFF(0, 'TRANSFORM')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
		<div class="topcard">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-see-the-complete-list')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-see-the-complete-list-description')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-see-the-complete-list">
							<span class="textcardtitle">{{$t('bs-see-the-complete-list')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-see-the-complete-list" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-see-the-complete-list-description')}}
						</b-tooltip>
					</span>
				</b-col>
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfigFF(0, 'ALLLIST')" @click="sendSettingsFF(0, 'ALLLIST')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
		<div class="topcard">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-take')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-take-description')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-take">
							<span class="textcardtitle">{{$t('bs-take')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-take" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-take-description')}}
						</b-tooltip>
					</span>
				</b-col>
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfigFF(0, 'ABRIR')" @click="sendSettingsFF(0, 'ABRIR')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
		<div class="topcard">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-close')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-close-description')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-close">
							<span class="textcardtitle">{{$t('bs-close')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-close" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-close-description')}}
						</b-tooltip>
					</span>
				</b-col>
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfigFF(0, 'FECHAR')" @click="sendSettingsFF(0, 'FECHAR')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
		<div class="topcard">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-transfer')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-transfer-description')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-transfer">
							<span class="textcardtitle">{{$t('bs-transfer')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-transfer" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-transfer-description')}}
						</b-tooltip>
					</span>
				</b-col>
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfigFF(0, 'MOVER')" @click="sendSettingsFF(0, 'MOVER')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
		<div class="topcard">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-block')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-block-description-chat')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-block-chat">
							<span class="textcardtitle">{{$t('bs-block')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-block-chat" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-block-description-chat')}}
						</b-tooltip>
					</span>
				</b-col>
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfigFF(0, 'BLOCK')" @click="sendSettingsFF(0, 'BLOCK')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
        <div class="topcard">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-delete')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-delete-description-chat')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-delete-chat">
							<span class="textcardtitle">{{$t('bs-delete')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-delete-chat" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-delete-description-chat')}}.
						</b-tooltip>
					</span>
				</b-col>
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfigFF(0, 'DELETE')" @click="sendSettingsFF(0, 'DELETE')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
		<br>
		<h3>{{$t('bs-ticket')}}</h3>
		<div class="topcard">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-admin')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-admin-description-t')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-admin">
							<span class="textcardtitle">{{$t('bs-admin')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-admin" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-admin-description-t')}}
						</b-tooltip>
					</span>
				</b-col>
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfigFF(1, 'ADMIN')" @click="sendSettingsFF(1, 'ADMIN')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
		<div class="topcard">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-create-ticket')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-create-ticket-description')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-create-ticket">
							<span class="textcardtitle">{{$t('bs-create-ticket')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-create-ticket" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-create-ticket-description')}}
						</b-tooltip>
					</span>
				</b-col>
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfigFF(1, 'CREATE')" @click="sendSettingsFF(1, 'CREATE')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
		<div class="topcard">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-see-the-complete-list')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-see-the-complete-list-description-t')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-see-the-complete-list-t">
							<span class="textcardtitle">{{$t('bs-see-the-complete-list')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-see-the-complete-list-t" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-see-the-complete-list-description-t')}}
						</b-tooltip>
					</span>
				</b-col>
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfigFF(1, 'ALLLIST')" @click="sendSettingsFF(1, 'ALLLIST')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
		<div class="topcard">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-take')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-take-description-t')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-take-t">
							<span class="textcardtitle">{{$t('bs-take')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-take-t" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-take-description-t')}}
						</b-tooltip>
					</span>
				</b-col>
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfigFF(1, 'ABRIR')" @click="sendSettingsFF(1, 'ABRIR')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
		<div class="topcard">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-close')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-close-description-t')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-close-t">
							<span class="textcardtitle">{{$t('bs-close')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-close-t" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-close-description-t')}}
						</b-tooltip>
					</span>
				</b-col>
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfigFF(1, 'FECHAR')" @click="sendSettingsFF(1, 'FECHAR')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
		<div class="topcard">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-resolved')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-resolved-description-t')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-resolved-t">
							<span class="textcardtitle">{{$t('bs-resolved')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-resolved-t" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-resolved-description-t')}}
						</b-tooltip>
					</span>
				</b-col>
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfigFF(1, 'RESOLVED')" @click="sendSettingsFF(1, 'RESOLVED')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
		<div class="topcard">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-transfer')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-transfer-description-t')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-transfer-t">
							<span class="textcardtitle">{{$t('bs-transfer')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-transfer-t" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-transfer-description-t')}}
						</b-tooltip>
					</span>
				</b-col>
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfigFF(1, 'MOVER')" @click="sendSettingsFF(1, 'MOVER')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
		<div class="topcard">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-block')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-block-description-ticket')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-block-ticket">
							<span class="textcardtitle">{{$t('bs-block')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-block-ticket" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-block-description-ticket')}}
						</b-tooltip>
					</span>
				</b-col>
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfigFF(1, 'BLOCK')" @click="sendSettingsFF(1, 'BLOCK')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
        <div class="topcard">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-delete')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-delete-description-ticket')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-delete-ticket">
							<span class="textcardtitle">{{$t('bs-delete')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-delete-ticket" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-delete-description-ticket')}}.
						</b-tooltip>
					</span>
				</b-col>
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfigFF(1, 'DELETE')" @click="sendSettingsFF(1, 'DELETE')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
		<div class="topcard">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-return-ticket-to-queue')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-return-to-queue-description')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-return-to-queue-ticket">
							<span class="textcardtitle">{{$t('bs-return-to-queue')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-return-to-queue-ticket" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-return-to-queue-description')}}.
						</b-tooltip>
					</span>
				</b-col>
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfigFF(1, 'RETURN-QUEUE')" @click="sendSettingsFF(1, 'RETURN-QUEUE')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
		<div class="topcard">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-reopen-ticket')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-reopen-ticket-description')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-reopen-ticket">
							<span class="textcardtitle">{{$t('bs-reopen-ticket')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-reopen-ticket" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-reopen-ticket-description')}}.
						</b-tooltip>
					</span>
				</b-col>
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfigFF(1, 'REOPEN-TICKET')" @click="sendSettingsFF(1, 'REOPEN-TICKET')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
		<br>
		<h3>{{$t('bs-controllers')}}</h3>
		<div :class="show.company ? 'tcard' : 'topcard'">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-company')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-company-description')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-company">
							<span class="textcardtitle">{{$t('bs-company')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-company" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-company-description')}}
						</b-tooltip>
					</span>
				</b-col>
				<!-- <i class="fa fa-eye fa-2x mt-2 cz" aria-hidden="true" @click="showPerson('company')"></i> -->
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfig(0, 'view')" @click="sendSettings(0, 'view')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
			<div class="details-c" v-if="show.company">
				<hr class="rounded">
				<b-row class="ml-3">
					<b-col>
						Dashboard companhia
					</b-col> 
					<b-col cols="auto">
						<b-form-checkbox
							id="edit-c-1"
							v-model="details.company.dashboard"
							name="edit-c-1"
							>
						</b-form-checkbox>
					</b-col>
				</b-row>
				<b-row class="ml-3 mt-2">
					<b-col>
						Editar companhia
					</b-col> 
					<b-col cols="auto">
						<b-form-checkbox
							id="edit-c-1"
							v-model="details.company.editCompany"
							name="edit-c-1"
							>
						</b-form-checkbox>
					</b-col>
				</b-row>
				<div v-if="details.company.editCompany">
					<b-row class="ml-5">
						<b-col>
							- Nome da companhia
						</b-col> 
						<b-col cols="auto">
							<b-form-checkbox
								id="edit-c-2"
								v-model="details.company.nameCompany"
								name="edit-c-2"
								>
							</b-form-checkbox>
						</b-col>
					</b-row>
					<b-row class="ml-5">
						<b-col>
							- LogoTipo
						</b-col> 
						<b-col cols="auto">
							<b-form-checkbox
								id="edit-c-3"
								v-model="details.company.logoTipo"
								name="edit-c-3"
								>
							</b-form-checkbox>
						</b-col>
					</b-row>
					<b-row class="ml-5">
						<b-col>
							- Descrição
						</b-col> 
						<b-col cols="auto">
							<b-form-checkbox
								id="edit-c-4"
								v-model="details.company.description"
								name="edit-c-4"
								>
							</b-form-checkbox>
						</b-col>
					</b-row>
				</div>
				<b-row class="ml-3 mt-2">
					<b-col>
						Configuração de companhia
					</b-col> 
					<b-col cols="auto">
						<b-form-checkbox
							id="checkbox-2"
							v-model="details.company.configCompany"
							name="checkbox-2"
							>
						</b-form-checkbox>
					</b-col>
				</b-row>
				<div v-if="details.company.configCompany">
					<b-row class="ml-5">
						<b-col>
							- Geral
						</b-col> 
						<b-col cols="auto">
							<b-form-checkbox
								id="checkbox-3"
								v-model="details.company.general"
								name="checkbox-3"
								>
							</b-form-checkbox>
						</b-col>
					</b-row>
					<b-row class="ml-5">
						<b-col>
							- Origens liberada
						</b-col> 
						<b-col cols="auto">
							<b-form-checkbox
								id="checkbox-4"
								v-model="details.company.origemLiberada"
								name="checkbox-4"
								>
							</b-form-checkbox>
						</b-col>
					</b-row>
					<b-row class="ml-5">
						<b-col>
							- Chat
						</b-col> 
						<b-col cols="auto">
							<b-form-checkbox
								id="checkbox-5"
								v-model="details.company.chat"
								name="checkbox-5"
								>
							</b-form-checkbox>
						</b-col>
					</b-row>
					<b-row class="ml-5">
						<b-col>
							- Ticket
						</b-col> 
						<b-col cols="auto">
							<b-form-checkbox
								id="checkbox-6"
								v-model="details.company.ticket"
								name="checkbox-6"
								>
							</b-form-checkbox>
						</b-col>
					</b-row>
					<b-row class="ml-5">
						<b-col>
							- Categorias
						</b-col> 
						<b-col cols="auto">
							<b-form-checkbox
								id="checkbox-7"
								v-model="details.company.categories"
								name="checkbox-7"
								>
							</b-form-checkbox>
						</b-col>
					</b-row>		
				</div>
			</div>
		</div>
		<div :class="show.department ? 'tcard' : 'topcard'">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-department')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-department-description')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-department">
							<span class="textcardtitle">{{$t('bs-department')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-department" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-department-description')}}
						</b-tooltip>
					</span>
				</b-col>
				<!-- <i class="fa fa-eye fa-2x mt-2 cz" aria-hidden="true" @click="showPerson('department')"></i> -->
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfig(1, 'view')" @click="sendSettings(1, 'view')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
			<div class="details-c" v-if="show.department">
				<hr class="rounded">
				<b-row class="ml-3">
					<b-col>
						Dashboard departamentos
					</b-col> 
					<b-col cols="auto">
						<b-form-checkbox
							id="edit-d-1"
							v-model="details.department.dashboard"
							name="edit-d-1"
							>
						</b-form-checkbox>
					</b-col>
				</b-row>
				<b-row class="ml-3 mt-2">
					<b-col>
						Cadastro de departamentos
					</b-col> 
					<b-col cols="auto">
						<b-form-checkbox
							id="edit-d-2"
							v-model="details.department.register"
							name="edit-d-2"
							>
						</b-form-checkbox>
					</b-col>
				</b-row>
				<b-row class="ml-3 mt-2">
					<b-col>
						Configuração de departamentos
					</b-col> 
					<b-col cols="auto">
						<b-form-checkbox
							id="edit-d-3"
							v-model="details.department.config"
							name="edit-d-3"
							>
						</b-form-checkbox>
					</b-col>
				</b-row>
				<div v-if="details.department.config">
					<b-row class="ml-5">
						<b-col>
							- General
						</b-col> 
						<b-col cols="auto">
							<b-form-checkbox
								id="edit-d-4"
								v-model="details.department.general"
								name="edit-d-4"
								>
							</b-form-checkbox>
						</b-col>
					</b-row>
					<b-row class="ml-5">
						<b-col>
							- Gerenciar Atendentes
						</b-col> 
						<b-col cols="auto">
							<b-form-checkbox
								id="edit-d-5"
								v-model="details.department.manageAgents"
								name="edit-d-5"
								>
							</b-form-checkbox>
						</b-col>
					</b-row>
					<b-row class="ml-5">
						<b-col>
							- Atalhos de texto
						</b-col> 
						<b-col cols="auto">
							<b-form-checkbox
								id="edit-d-6"
								v-model="details.department.textShortcuts"
								name="edit-d-6"
								>
							</b-form-checkbox>
						</b-col>
					</b-row>
					<b-row class="ml-5">
						<b-col>
							- Limitações/Quantidades
						</b-col> 
						<b-col cols="auto">
							<b-form-checkbox
								id="edit-d-7"
								v-model="details.department.limitQuant"
								name="edit-d-7"
								>
							</b-form-checkbox>
						</b-col>
					</b-row>
					<b-row class="ml-5">
						<b-col>
							- Robô
						</b-col> 
						<b-col cols="auto">
							<b-form-checkbox
								id="edit-d-8"
								v-model="details.department.robo"
								name="edit-d-8"
								>
							</b-form-checkbox>
						</b-col>
					</b-row>
					<b-row class="ml-5">
						<b-col>
							- Avaliação
						</b-col> 
						<b-col cols="auto">
							<b-form-checkbox
								id="edit-d-9"
								v-model="details.department.avaliation"
								name="edit-d-9"
								>
							</b-form-checkbox>
						</b-col>
					</b-row>
					<b-row class="ml-5">
						<b-col>
							- Chat
						</b-col> 
						<b-col cols="auto">
							<b-form-checkbox
								id="edit-d-0"
								v-model="details.department.chat"
								name="edit-d-0"
								>
							</b-form-checkbox>
						</b-col>
					</b-row>
					<b-row class="ml-5">
						<b-col>
							- Ticket
						</b-col> 
						<b-col cols="auto">
							<b-form-checkbox
								id="edit-d-10"
								v-model="details.department.ticket"
								name="edit-d-10"
								>
							</b-form-checkbox>
						</b-col>
					</b-row>
				</div>
				<b-row class="ml-3 mt-2">
					<b-col>
						Editar departamentos
					</b-col> 
					<b-col cols="auto">
						<b-form-checkbox
							id="edit-d-11"
							v-model="details.department.editDepart"
							name="edit-d-11"
							>
						</b-form-checkbox>
					</b-col>
				</b-row>
				<b-row class="ml-3 mt-2">
					<b-col>
						Excluir departamentos
					</b-col> 
					<b-col cols="auto">
						<b-form-checkbox
							id="edit-d-12"
							v-model="details.department.delete"
							name="edit-d-12"
							>
						</b-form-checkbox>
					</b-col>
				</b-row>
				<b-row class="ml-3 mt-2">
					<b-col>
						Ativar ou desativar departamentos
					</b-col> 
					<b-col cols="auto">
						<b-form-checkbox
							id="edit-d-13"
							v-model="details.department.enableDisable"
							name="edit-d-13"
							>
						</b-form-checkbox>
					</b-col>
				</b-row>
			</div>
		</div>
		<div :class="show.group ? 'tcard' : 'topcard'">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-group')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-group-description')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-group">
							<span class="textcardtitle">{{$t('bs-group')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-group" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-group-description')}}
						</b-tooltip>
					</span>
				</b-col>
				<!-- <i class="fa fa-eye fa-2x mt-2 cz" aria-hidden="true" @click="showPerson('group')"></i> -->
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfig(2, 'view')" @click="sendSettings(2, 'view')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
			<div class="details-c" v-if="show.group">
				<hr class="rounded">
				<b-row class="ml-3">
					<b-col>
						Dashboard departamentos
					</b-col> 
					<b-col cols="auto">
						<b-form-checkbox
							id="edit-d-1"
							v-model="details.group.dashboard"
							name="edit-d-1"
							>
						</b-form-checkbox>
					</b-col>
				</b-row>
			</div>
		</div>
		<div :class="show.agents ? 'tcard' : 'topcard'">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-agents')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-agents-description')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-agents">
							<span class="textcardtitle">{{$t('bs-agents')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-agents" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-agents-description')}}
						</b-tooltip>
					</span>
				</b-col>
				<!-- <i class="fa fa-eye fa-2x mt-2 cz" aria-hidden="true" @click="showPerson('agents')"></i> -->
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfig(3, 'view')" @click="sendSettings(3, 'view')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
		<div :class="show.integration ? 'tcard' : 'topcard'">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-integration')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-integration-description')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-integration">
							<span class="textcardtitle">{{$t('bs-integration')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-integration" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-integration-description')}}
						</b-tooltip>
					</span>
				</b-col>
				<!-- <i class="fa fa-eye fa-2x mt-2 cz" aria-hidden="true" @click="showPerson('integration')"></i> -->
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfig(4, 'view')" @click="sendSettings(4, 'view')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
		<div :class="show.client ? 'tcard' : 'topcard'">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-client')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-client-description')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-client">
							<span class="textcardtitle">{{$t('bs-client')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-client" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-client-description')}}
						</b-tooltip>
					</span>
				</b-col>
				<!-- <i class="fa fa-eye fa-2x mt-2 cz" aria-hidden="true" @click="showPerson('client')"></i> -->
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfig(5, 'view')" @click="sendSettings(5, 'view')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
		<div :class="show.monitoring ? 'tcard' : 'topcard'">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-monitoring')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-monitoring-description')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-monitoring">
							<span class="textcardtitle">{{$t('bs-monitoring')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-monitoring" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-monitoring-description')}}
						</b-tooltip>
					</span>
				</b-col>
				<!-- <i class="fa fa-eye fa-2x mt-2 cz" aria-hidden="true" @click="showPerson('monitoring')"></i> -->
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfig(6, 'view')" @click="sendSettings(6, 'view')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div>
		<!-- <div class="topcard">
			<b-row class="ml-3">
				<b-col>
					<span v-if="windowWidth >= activetootips">
						<span class="textcardtitle">{{$t('bs-analyze')}}</span><br>
						<span class="textcardsubtitle" >{{$t('bs-analyze-description')}}.</span>
					</span>
					<span v-else>
						<span id="tooltip-analyze">
							<span class="textcardtitle">{{$t('bs-analyze')}}</span>
							&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
						</span>
						<b-tooltip target="tooltip-analyze" triggers="hover" placement="right" variant="secondary">
							{{$t('bs-analyze-description')}}
						</b-tooltip>
					</span>
				</b-col>
				<b-col cols="auto" class="mt-2 mr-2">
					<label class="switch">
						<input type="checkbox" :checked="checkedConfig(7, 'view')" @click="sendSettings(7, 'view')">
						<span class="slider round"></span>
					</label>
				</b-col>
			</b-row>
		</div> -->
	</div>
</template>

<script>
export default {
	data(){
		return {
			accesseShow: true,
			companyDetails: false,
			departmentDetails: false,
			groupDetails: false,
			agentsDetails: false,
			integrationDetails: false,
			clientDetails: false,
		 	// fieldsConfig: ['controllers', 'VIEW', 'INSERT', 'EDIT', 'DELETE', 'ACTION'],
		 	fieldsConfig: [
				{ key: 'controllers', label: this.$t('bs-controllers').toUpperCase() },
				{ key: 'ACCESS', label: this.$t('bs-access').toUpperCase() }
			],
	        itemsConfig: [
				{ controllers: this.$t('bs-company'), 		},
				{ controllers: this.$t('bs-department'), 	},
				{ controllers: this.$t('bs-group'), 		},
				{ controllers: this.$t('bs-agents'), 		},
				{ controllers: this.$t('bs-integration'), 	},
				{ controllers: this.$t('bs-client'), 	},
				{ controllers: this.$t('bs-monitoring'), 	},
				{ controllers: this.$t('bs-analyze'), 	},
	        ],
	        fieldsCT: [
				{ key: 'controllers', label: this.$t('bs-controllers').toUpperCase() },
				{ key: 'QUEUECOMPLETE', label: 'FILA COMPLETA' },
				{ key: 'TRANSFORM', label: this.$t('bs-turn-into-ticket').toUpperCase() },
				{ key: 'ALLLIST', label: this.$t('bs-see-the-complete-list').toUpperCase() },
				{ key: 'CREATE', label: this.$t('bs-create-ticket').toUpperCase() },
				{ key: 'ADMIN', label: this.$t('bs-admin').toUpperCase() },
				{ key: 'OPEN', label: this.$t('bs-take').toUpperCase() },
				{ key: 'CLOSE', label: this.$t('bs-close').toUpperCase() },
				{ key: 'RESOLVED', label: this.$t('bs-resolved').toUpperCase() },
				{ key: 'TO_MOVED', label: this.$t('bs-transfer').toUpperCase() }
			],
			itemsCT: [
				{ controllers: 'CHAT', 		},
				{ controllers: 'TICKET', 	},
	        ],
	        fieldsDepartment: [
				{ key: 'name_config', label: this.$t('bs-setting-name').toUpperCase() },
				{ key: 'VIEW', label: this.$t('bs-view').toUpperCase() },
				{ key: 'INSERT', label: this.$t('bs-insert').toUpperCase() },
				{ key: 'EDIT', label: this.$t('bs-edit').toUpperCase() },
				{ key: 'DELETE', label: this.$t('bs-delete').toUpperCase() }
			],
			itemsDepartment: [
				{ name_config: this.$t('bs-general'), 								},
				{ name_config: this.$t('bs-manage-assistants'), 					},
				{ name_config: this.$t('bs-automatic-answer'), 						},
				{ name_config: this.$t('bs-limitations')+'/'+this.$t('bs-quantities'),	},
				{ name_config: this.$t('bs-robot'), 								},
				{ name_config: this.$t('bs-evaluation'), 							},
				{ name_config: 'CHAT', 												},
				{ name_config: 'TICKET', 											}
	        ],
			permissions:{
				"company":{"view": false,"insert": false,"edit": false,"delete": false},
				"department":{"view": false,"insert": false,"edit": false,"delete": false},
				"group":{"view": false,"insert": false,"edit": false,"delete": false},
				"agents":{"view": false,"insert": false,"edit": false,"delete": false},
				"integration":{"view": false,"insert": false,"edit": false,"delete": false},
				"client":{"view": false,"insert": false,"edit": false,"delete": false},
				"monitoring":{"view": false,"insert": false,"edit": false,"delete": false},
				"analyze":{"view": false,"insert": false,"edit": false,"delete": false},
				"chat":{"transform":false,"alllist":false,"admin":false,"open":false,"resolved":false,"close":false,"moved":false,"queue_full_control":false},
				"ticket":{"transform":false,"alllist":false,"admin":false,"open":false,"resolved":false,"close":false,"moved":false,"create":false},
			},
			windowWidth: window.innerWidth,
			activetootips: 992,
			show: {
				company: false,
				department: false,
				group: false,
				agents: false,
				integration: false,
				client: false,
				monitoring: false,
			},
			details:{
				company:{
					dashboard: true,
					editCompany: true,
					configCompany: true,
					nameCompany: true,
					logoTipo: true,
					description: true,
					general: true,
					origemLiberada: true,
					chat: true,
					ticket: true,
					categories: true,
				},
				department:{
					dashboard: true,
					register: true,
					config: true,
					general: true,
					manageAgents: true,
					textShortcuts: true,
					limitQuant: true,
					robo: true,
					avaliation: true,
					chat: true,
					ticket: true,
					editDepart: true,
					delete: true,
					enableDisable: true,
				},
				group:{
					dashboard: true,
				},
				agents:{
					dashboard: true,
				}
			}
		}
	},
	props:{
		itemselected: Object,
		base_url: {
			type: String,
			default: ''
		},
	},
	watch: {
        windowWidth(newHeight, oldHeight) {
			this.windowWidth = newHeight;
        }
    },
	mounted(){

		if(this.itemselected.settings.permissions != null){
			this.permissions = this.itemselected.settings.permissions;
		}

		//adicionado a config caso não tenha setada ainda.
		if(this.permissions.integration == undefined){
			this.permissions.integration = {"view": false,"insert": false,"edit": false,"delete": false};
		}

		//adicionado a config caso não tenha setada ainda.
		if(this.permissions.client == undefined){
			this.permissions.client = {"view": false,"insert": false,"edit": false,"delete": false};
		}

		//adicionado a config caso não tenha setada ainda.
		if(this.permissions.monitoring == undefined){
			this.permissions.monitoring = {"view": false,"insert": false,"edit": false,"delete": false};
		}

		//adicionado a config caso não tenha setada ainda.
		if(this.permissions.analyze == undefined){
			this.permissions.analyze = {"view": false,"insert": false,"edit": false,"delete": false};
		}

		//console.log(this.permissions);


		//console.log(this.permissions);

		this.$nextTick(() => {
            window.addEventListener('resize', this.onResize);
        })
	},
	beforeDestroy() {
        window.removeEventListener('resize', this.onResize);
    },
	methods:{
		onResize() {
            this.windowWidth = window.innerWidth
        },
		showPerson(type){
			switch (type) {
				case 'company':
				this.show.company = !this.show.company;
					break;
				case 'department':
				this.show.department = !this.show.department;
					break;
				case 'group':
				this.show.group = !this.show.group;
					break;
				case 'agents':
				this.show.agents = !this.show.agents;
					break;
				case 'integration':
				this.show.integration = !this.show.integration;
					break;
				case 'client':
				this.show.client = !this.show.client;
					break;
				case 'monitoring':
				this.show.monitoring = !this.show.monitoring;
					break;
				default:
					break;
			}
		},
		sendSettings(index, value){
			var vm = this;
			if(index == 0){
				if(value == 'view'){
					vm.permissions.company.view = !vm.permissions.company.view;
				}
				if(value == 'insert'){
					vm.permissions.company.insert = !vm.permissions.company.insert;
				}
				if(value == 'edit'){
					vm.permissions.company.edit = !vm.permissions.company.edit;
				}
				if(value == 'delete'){
					vm.permissions.company.delete = !vm.permissions.company.delete;
				}
			}

			if(index == 1){
				if(value == 'view'){
					vm.permissions.department.view = !vm.permissions.department.view;
				}
				if(value == 'insert'){
					vm.permissions.department.insert = !vm.permissions.department.insert;
				}
				if(value == 'edit'){
					vm.permissions.department.edit = !vm.permissions.department.edit;
				}
				if(value == 'delete'){
					vm.permissions.department.delete = !vm.permissions.department.delete;
				}
			}

			if(index == 2){
				if(value == 'view'){
					vm.permissions.group.view = !vm.permissions.group.view;
				}
				if(value == 'insert'){
					vm.permissions.group.insert = !vm.permissions.group.insert;
				}
				if(value == 'edit'){
					vm.permissions.group.edit = !vm.permissions.group.edit;
				}
				if(value == 'delete'){
					vm.permissions.group.delete = !vm.permissions.group.delete;
				}
			}

			if(index == 3){
				if(value == 'view'){
					vm.permissions.agents.view = !vm.permissions.agents.view;
				}
				if(value == 'insert'){
					vm.permissions.agents.insert = !vm.permissions.agents.insert;
				}
				if(value == 'edit'){
					vm.permissions.agents.edit = !vm.permissions.agents.edit;
				}
				if(value == 'delete'){
					vm.permissions.agents.delete = !vm.permissions.agents.delete;
				}
			}

			if(index == 4){
				if(value == 'view'){
					vm.permissions.integration.view = !vm.permissions.integration.view;
				}
				if(value == 'insert'){
					vm.permissions.integration.insert = !vm.permissions.integration.insert;
				}
				if(value == 'edit'){
					vm.permissions.integration.edit = !vm.permissions.integration.edit;
				}
				if(value == 'delete'){
					vm.permissions.integration.delete = !vm.permissions.integration.delete;
				}
			}

			if(index == 5){
				if(value == 'view'){
					vm.permissions.client.view = !vm.permissions.client.view;
				}
				if(value == 'insert'){
					vm.permissions.client.insert = !vm.permissions.client.insert;
				}
				if(value == 'edit'){
					vm.permissions.client.edit = !vm.permissions.client.edit;
				}
				if(value == 'delete'){
					vm.permissions.client.delete = !vm.permissions.client.delete;
				}
			}

			if(index == 6){
				if(value == 'view'){
					vm.permissions.monitoring.view = !vm.permissions.monitoring.view;
				}
				if(value == 'insert'){
					vm.permissions.monitoring.insert = !vm.permissions.monitoring.insert;
				}
				if(value == 'edit'){
					vm.permissions.monitoring.edit = !vm.permissions.monitoring.edit;
				}
				if(value == 'delete'){
					vm.permissions.monitoring.delete = !vm.permissions.monitoring.delete;
				}
			}

			if(index == 7){
				if(value == 'view'){
					vm.permissions.analyze.view = !vm.permissions.analyze.view;
				}
				if(value == 'insert'){
					vm.permissions.analyze.insert = !vm.permissions.analyze.insert;
				}
				if(value == 'edit'){
					vm.permissions.analyze.edit = !vm.permissions.analyze.edit;
				}
				if(value == 'delete'){
					vm.permissions.analyze.delete = !vm.permissions.analyze.delete;
				}
			}

			vm.checkedConfig(index, value);
			vm.$emit('permission', vm.permissions);
			vm.permissions = vm.itemselected.settings.permissions;
		},
		checkedConfig(index, value){
			var vm = this;
			if(index == 0){
				if(vm.permissions.company.view == true){
					if(value == 'view'){
						return 'checked';
					}
				}
				if(vm.permissions.company.insert == true){
					if(value == 'insert'){
						return 'checked';
					}
				}
				if(vm.permissions.company.edit == true){
					if(value == 'edit'){
						return 'checked';
					}
				}
				if(vm.permissions.company.delete == true){
					if(value == 'delete'){
						return 'checked';
					}
				}
				return;
			}

			if(index == 1){
				if(vm.permissions.department.view == true){
					if(value == 'view'){
						return 'checked';
					}
				}
				if(vm.permissions.department.insert == true){
					if(value == 'insert'){
						return 'checked';
					}
				}
				if(vm.permissions.department.edit == true){
					if(value == 'edit'){
						return 'checked';
					}
				}
				if(vm.permissions.department.delete == true){
					if(value == 'delete'){
						return 'checked';
					}
				}
				return;
			}
			if(index == 2){
				if(vm.permissions.group.view == true){
					if(value == 'view'){
						return 'checked';
					}
				}
				if(vm.permissions.group.insert == true){
					if(value == 'insert'){
						return 'checked';
					}
				}
				if(vm.permissions.group.edit == true){
					if(value == 'edit'){
						return 'checked';
					}
				}
				if(vm.permissions.group.delete == true){
					if(value == 'delete'){
						return 'checked';
					}
				}
				return;
			}

			if(index == 3){
				if(vm.permissions.agents.view == true){
					if(value == 'view'){
						return 'checked';
					}
				}
				if(vm.permissions.agents.insert == true){
					if(value == 'insert'){
						return 'checked';
					}
				}
				if(vm.permissions.agents.edit == true){
					if(value == 'edit'){
						return 'checked';
					}
				}
				if(vm.permissions.agents.delete == true){
					if(value == 'delete'){
						return 'checked';
					}
				}
				return;
			}

			if(index == 4){
				if(vm.permissions.integration.view == true){
					if(value == 'view'){
						return 'checked';
					}
				}
				if(vm.permissions.integration.insert == true){
					if(value == 'insert'){
						return 'checked';
					}
				}
				if(vm.permissions.integration.edit == true){
					if(value == 'edit'){
						return 'checked';
					}
				}
				if(vm.permissions.integration.delete == true){
					if(value == 'delete'){
						return 'checked';
					}
				}
				return;
			}

			if(index == 5){
				if(vm.permissions.client.view == true){
					if(value == 'view'){
						return 'checked';
					}
				}
				if(vm.permissions.client.insert == true){
					if(value == 'insert'){
						return 'checked';
					}
				}
				if(vm.permissions.client.edit == true){
					if(value == 'edit'){
						return 'checked';
					}
				}
				if(vm.permissions.client.delete == true){
					if(value == 'delete'){
						return 'checked';
					}
				}
				return;
			}

			if(index == 6){
				if(vm.permissions.monitoring.view == true){
					if(value == 'view'){
						return 'checked';
					}
				}
				if(vm.permissions.monitoring.insert == true){
					if(value == 'insert'){
						return 'checked';
					}
				}
				if(vm.permissions.monitoring.edit == true){
					if(value == 'edit'){
						return 'checked';
					}
				}
				if(vm.permissions.monitoring.delete == true){
					if(value == 'delete'){
						return 'checked';
					}
				}
				return;
			}

			if(index == 7){
				if(vm.permissions.analyze.view == true){
					if(value == 'view'){
						return 'checked';
					}
				}
				if(vm.permissions.analyze.insert == true){
					if(value == 'insert'){
						return 'checked';
					}
				}
				if(vm.permissions.analyze.edit == true){
					if(value == 'edit'){
						return 'checked';
					}
				}
				if(vm.permissions.analyze.delete == true){
					if(value == 'delete'){
						return 'checked';
					}
				}
				return;
			}

			return;
		},
		sendSettingsFF(index, value){
			var vm = this;
			//CHAT
			if(index == 0){
				if(value == 'QUEUECOMPLETE'){
					vm.permissions.chat.queue_full_control = !vm.permissions.chat.queue_full_control;
				}
				if(value == 'TRANSFORM'){
					vm.permissions.chat.transform = !vm.permissions.chat.transform;
				}
				if(value == 'ALLLIST'){
					vm.permissions.chat.alllist = !vm.permissions.chat.alllist;
				}
				if(value == 'ADMIN'){
					vm.permissions.chat.admin = !vm.permissions.chat.admin;
					// SETA TODOS OS OUTROS VALORES TRUE - MAS PODE ATRAPALHAR COM UM CLICK SEM QUERER '-'
					// if(vm.permissions.chat.admin){
					// 	vm.permissions.chat.queue_full_control = true;
					// 	vm.permissions.chat.transform = true;
					// 	vm.permissions.chat.alllist = true;
					// 	vm.permissions.chat.create = true;
					// 	vm.permissions.chat.open = true;
					// 	vm.permissions.chat.close = true;
					// 	vm.permissions.chat.resolved = true;
					// 	vm.permissions.chat.moved = true;
					// }
				}
				if(value == 'CREATE'){
					vm.permissions.chat.create = !vm.permissions.chat.create;
				}
				if(value == 'ABRIR'){
					vm.permissions.chat.open = !vm.permissions.chat.open;
				}
				if(value == 'FECHAR'){
					vm.permissions.chat.close = !vm.permissions.chat.close;
				}
				if(value == 'RESOLVED'){
					vm.permissions.chat.resolved = !vm.permissions.chat.resolved;
				}
				if(value == 'MOVER'){
					vm.permissions.chat.moved = !vm.permissions.chat.moved;
				}
				if(value == 'BLOCK'){
					vm.permissions.chat.blocked = !vm.permissions.chat.blocked;
				}
                if(value == 'DELETE'){
					vm.permissions.chat.delete = !vm.permissions.chat.delete;
				}
			}


			//TICKET
			if(index == 1){
				if(value == 'TRANSFORM'){
					vm.permissions.ticket.transform = !vm.permissions.ticket.transform;
				}
				if(value == 'ALLLIST'){
					vm.permissions.ticket.alllist = !vm.permissions.ticket.alllist;
				}
				if(value == 'ADMIN'){
					vm.permissions.ticket.admin = !vm.permissions.ticket.admin;
					// SETA TODOS OS OUTROS VALORES TRUE - MAS PODE ATRAPALHAR COM UM CLICK SEM QUERER '-'
					// if(vm.permissions.ticket.admin){
					// 	vm.permissions.ticket.transform = true;
					// 	vm.permissions.ticket.alllist = true;
					// 	vm.permissions.ticket.create = true;
					// 	vm.permissions.ticket.open = true;
					// 	vm.permissions.ticket.close = true;
					// 	vm.permissions.ticket.resolved = true;
					// 	vm.permissions.ticket.moved = true;
					// }
				}
				if(value == 'CREATE'){
					vm.permissions.ticket.create = !vm.permissions.ticket.create;
				}
				if(value == 'ABRIR'){
					vm.permissions.ticket.open = !vm.permissions.ticket.open;
				}
				if(value == 'FECHAR'){
					vm.permissions.ticket.close = !vm.permissions.ticket.close;
				}
				if(value == 'RESOLVED'){
					vm.permissions.ticket.resolved = !vm.permissions.ticket.resolved;
				}
				if(value == 'MOVER'){
					vm.permissions.ticket.moved = !vm.permissions.ticket.moved;
				}
				if(value == 'BLOCK'){
					vm.permissions.ticket.blocked = !vm.permissions.ticket.blocked;
				}
                if(value == 'DELETE'){
					vm.permissions.ticket.delete = !vm.permissions.ticket.delete;
				}
				if(value == 'RETURN-QUEUE'){
					vm.permissions.ticket.returnQueue = !vm.permissions.ticket.returnQueue;
				}
				if(value == 'REOPEN-TICKET'){
					vm.permissions.ticket.reopenTicket = !vm.permissions.ticket.reopenTicket;
				}
			}

			vm.checkedConfigFF(index, value);
			vm.$emit('permission', vm.permissions);
			vm.permissions = vm.itemselected.settings.permissions;
		},
		checkedConfigFF(index, value){
			var vm = this;
			//CHAT
			if(index == 0){
				if(vm.permissions.chat.queue_full_control == true){
					if(value == 'QUEUECOMPLETE'){
						return 'checked';
					}
				}
				if(vm.permissions.chat.transform == true){
					if(value == 'TRANSFORM'){
						return 'checked';
					}
				}
				if(vm.permissions.chat.alllist == true){
					if(value == 'ALLLIST'){
						return 'checked';
					}
				}
				if(vm.permissions.chat.admin == true){
					if(value == 'ADMIN'){
						return 'checked';
					}
				}
				if(vm.permissions.chat.create == true){
					if(value == 'CREATE'){
						return 'checked';
					}
				}
				if(vm.permissions.chat.open == true){
					if(value == 'ABRIR'){
						return 'checked';
					}
				}
				if(vm.permissions.chat.close == true){
					if(value == 'FECHAR'){
						return 'checked';
					}
				}
				if(vm.permissions.chat.resolved == true){
					if(value == 'RESOLVED'){
						return 'checked';
					}
				}
				if(vm.permissions.chat.moved == true){
					if(value == 'MOVER'){
						return 'checked';
					}
				}
				if(vm.permissions.chat.blocked == true){
					if(value == 'BLOCK'){
						return 'checked';
					}
				}
                if(vm.permissions.chat.delete == true){
					if(value == 'DELETE'){
						return 'checked';
					}
				}
				return;
			}

			//TICKET
			if(index == 1){
				if(vm.permissions.ticket.transform == true){
					if(value == 'TRANSFORM'){
						return 'checked';
					}
				}
				if(vm.permissions.ticket.alllist == true){
					if(value == 'ALLLIST'){
						return 'checked';
					}
				}
				if(vm.permissions.ticket.admin == true){
					if(value == 'ADMIN'){
						return 'checked';
					}
				}
				if(vm.permissions.ticket.create == true){
					if(value == 'CREATE'){
						return 'checked';
					}
				}
				if(vm.permissions.ticket.open == true){
					if(value == 'ABRIR'){
						return 'checked';
					}
				}
				if(vm.permissions.ticket.close == true){
					if(value == 'FECHAR'){
						return 'checked';
					}
				}
				if(vm.permissions.ticket.resolved == true){
					if(value == 'RESOLVED'){
						return 'checked';
					}
				}
				if(vm.permissions.ticket.moved == true){
					if(value == 'MOVER'){
						return 'checked';
					}
				}
				if(vm.permissions.ticket.blocked == true){
					if(value == 'BLOCK'){
						return 'checked';
					}
				}
                if(vm.permissions.ticket.delete == true){
					if(value == 'DELETE'){
						return 'checked';
					}
				}
				if(vm.permissions.ticket.returnQueue == true){
					if(value == 'RETURN-QUEUE'){
						return 'checked';
					}
				}
				if(vm.permissions.ticket.reopenTicket == true){
					if(value == 'REOPEN-TICKET'){
						return 'checked';
					}
				}
				return;
			}
		},
		sendSettingsDepartment(index, value){
			var vm = this;

			if(index == 0){
				if(value == 'view'){
					vm.permissions.department.config.general.view = !vm.permissions.department.config.general.view;
				}
				if(value == 'insert'){
					vm.permissions.department.config.general.insert = !vm.permissions.department.config.general.insert;
				}
				if(value == 'edit'){
					vm.permissions.department.config.general.edit = !vm.permissions.department.config.general.edit;
				}
				if(value == 'delete'){
					vm.permissions.department.config.general.delete = !vm.permissions.department.config.general.delete;
				}
			}

			if(index == 1){
				if(value == 'view'){
					vm.permissions.department.config.management.view = !vm.permissions.department.config.management.view;
				}
				if(value == 'insert'){
					vm.permissions.department.config.management.insert = !vm.permissions.department.config.management.insert;
				}
				if(value == 'edit'){
					vm.permissions.department.config.management.edit = !vm.permissions.department.config.management.edit;
				}
				if(value == 'delete'){
					vm.permissions.department.config.management.delete = !vm.permissions.department.config.management.delete;
				}
			}

			if(index == 2){
				if(value == 'view'){
					vm.permissions.department.config.autoAnswer.view = !vm.permissions.department.config.autoAnswer.view;
				}
				if(value == 'insert'){
					vm.permissions.department.config.autoAnswer.insert = !vm.permissions.department.config.autoAnswer.insert;
				}
				if(value == 'edit'){
					vm.permissions.department.config.autoAnswer.edit = !vm.permissions.department.config.autoAnswer.edit;
				}
				if(value == 'delete'){
					vm.permissions.department.config.autoAnswer.delete = !vm.permissions.department.config.autoAnswer.delete;
				}
			}

			if(index == 3){
				if(value == 'view'){
					vm.permissions.department.config.quantLimitation.view = !vm.permissions.department.config.quantLimitation.view;
				}
				if(value == 'insert'){
					vm.permissions.department.config.quantLimitation.insert = !vm.permissions.department.config.quantLimitation.insert;
				}
				if(value == 'edit'){
					vm.permissions.department.config.quantLimitation.edit = !vm.permissions.department.config.quantLimitation.edit;
				}
				if(value == 'delete'){
					vm.permissions.department.config.quantLimitation.delete = !vm.permissions.department.config.quantLimitation.delete;
				}
			}

			if(index == 4){
				if(value == 'view'){
					vm.permissions.department.config.robot.view = !vm.permissions.department.config.robot.view;
				}
				if(value == 'insert'){
					vm.permissions.department.config.robot.insert = !vm.permissions.department.config.robot.insert;
				}
				if(value == 'edit'){
					vm.permissions.department.config.robot.edit = !vm.permissions.department.config.robot.edit;
				}
				if(value == 'delete'){
					vm.permissions.department.config.robot.delete = !vm.permissions.department.config.robot.delete;
				}
			}

			if(index == 5){
				if(value == 'view'){
					vm.permissions.department.config.evalution.view = !vm.permissions.department.config.evalution.view;
				}
				if(value == 'insert'){
					vm.permissions.department.config.evalution.insert = !vm.permissions.department.config.evalution.insert;
				}
				if(value == 'edit'){
					vm.permissions.department.config.evalution.edit = !vm.permissions.department.config.evalution.edit;
				}
				if(value == 'delete'){
					vm.permissions.department.config.evalution.delete = !vm.permissions.department.config.evalution.delete;
				}
			}

			if(index == 6){
				if(value == 'view'){
					vm.permissions.department.config.chat.view = !vm.permissions.department.config.chat.view;
				}
				if(value == 'insert'){
					vm.permissions.department.config.chat.insert = !vm.permissions.department.config.chat.insert;
				}
				if(value == 'edit'){
					vm.permissions.department.config.chat.edit = !vm.permissions.department.config.chat.edit;
				}
				if(value == 'delete'){
					vm.permissions.department.config.chat.delete = !vm.permissions.department.config.chat.delete;
				}
			}

			if(index == 7){
				if(value == 'view'){
					vm.permissions.department.config.ticket.view = !vm.permissions.department.config.ticket.view;
				}
				if(value == 'insert'){
					vm.permissions.department.config.ticket.insert = !vm.permissions.department.config.ticket.insert;
				}
				if(value == 'edit'){
					vm.permissions.department.config.ticket.edit = !vm.permissions.department.config.ticket.edit;
				}
				if(value == 'delete'){
					vm.permissions.department.config.ticket.delete = !vm.permissions.department.config.ticket.delete;
				}
			}

			vm.checkedConfig(index, value);
			vm.$emit('permission', vm.permissions);
			vm.permissions = vm.itemselected.settings.permissions;
		},
		checkedConfigDepartment(index, value){
			var vm = this;
			if(index == 0){
				if(vm.permissions.department.config.general.view == true){
					if(value == 'view'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.general.insert == true){
					if(value == 'insert'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.general.edit == true){
					if(value == 'edit'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.general.delete == true){
					if(value == 'delete'){
						return 'checked';
					}
				}
				return;
			}

			if(index == 1){
				if(vm.permissions.department.config.management.view == true){
					if(value == 'view'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.management.insert == true){
					if(value == 'insert'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.management.edit == true){
					if(value == 'edit'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.management.delete == true){
					if(value == 'delete'){
						return 'checked';
					}
				}
				return;
			}

			if(index == 2){
				if(vm.permissions.department.config.autoAnswer.view == true){
					if(value == 'view'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.autoAnswer.insert == true){
					if(value == 'insert'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.autoAnswer.edit == true){
					if(value == 'edit'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.autoAnswer.delete == true){
					if(value == 'delete'){
						return 'checked';
					}
				}
				return;
			}

			if(index == 3){
				if(vm.permissions.department.config.quantLimitation.view == true){
					if(value == 'view'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.quantLimitation.insert == true){
					if(value == 'insert'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.quantLimitation.edit == true){
					if(value == 'edit'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.quantLimitation.delete == true){
					if(value == 'delete'){
						return 'checked';
					}
				}
				return;
			}

			if(index == 4){
				if(vm.permissions.department.config.robot.view == true){
					if(value == 'view'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.robot.insert == true){
					if(value == 'insert'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.robot.edit == true){
					if(value == 'edit'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.robot.delete == true){
					if(value == 'delete'){
						return 'checked';
					}
				}
				return;
			}

			if(index == 5){
				if(vm.permissions.department.config.evalution.view == true){
					if(value == 'view'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.evalution.insert == true){
					if(value == 'insert'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.evalution.edit == true){
					if(value == 'edit'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.evalution.delete == true){
					if(value == 'delete'){
						return 'checked';
					}
				}
				return;
			}

			if(index == 6){
				if(vm.permissions.department.config.chat.view == true){
					if(value == 'view'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.chat.insert == true){
					if(value == 'insert'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.chat.edit == true){
					if(value == 'edit'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.chat.delete == true){
					if(value == 'delete'){
						return 'checked';
					}
				}
				return;
			}

			if(index == 7){
				if(vm.permissions.department.config.ticket.view == true){
					if(value == 'view'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.ticket.insert == true){
					if(value == 'insert'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.ticket.edit == true){
					if(value == 'edit'){
						return 'checked';
					}
				}
				if(vm.permissions.department.config.ticket.delete == true){
					if(value == 'delete'){
						return 'checked';
					}
				}
				return;
			}

			return;
		},
		detailsold(value){
			var vm = this;
			if(value == 0){
				vm.accesseShow = false
				vm.companyDetails = true
				vm.departmentDetails = false
				vm.groupDetails = false
				vm.agentsDetails = false
				vm.integrationDetails = false
				vm.clientDetails = false
			}else if(value == 1){
				vm.accesseShow = false
				vm.companyDetails = false
				vm.departmentDetails = true
				vm.groupDetails = false
				vm.agentsDetails = false
				vm.integrationDetails = false
				vm.clientDetails = false
			}else if(value == 2){
				vm.accesseShow = false
				vm.companyDetails = false
				vm.departmentDetails = false
				vm.groupDetails = true
				vm.agentsDetails = false
				vm.integrationDetails = false
				vm.clientDetails = false
			}else if(value == 3){
				vm.accesseShow = false
				vm.companyDetails = false
				vm.departmentDetails = false
				vm.groupDetails = false
				vm.agentsDetails = true
				vm.integrationDetails = false
				vm.clientDetails = false
			}else if(value == 4){
				vm.accesseShow = false
				vm.companyDetails = false
				vm.departmentDetails = false
				vm.groupDetails = false
				vm.agentsDetails = false
				vm.integrationDetails = true
				vm.clientDetails = false
			}else if(value == 5){
				vm.accesseShow = false
				vm.companyDetails = false
				vm.departmentDetails = false
				vm.groupDetails = false
				vm.agentsDetails = false
				vm.integrationDetails = false
				vm.clientDetails = true
			}else{
				vm.accesseShow = true
				vm.companyDetails = false
				vm.departmentDetails = false
				vm.groupDetails = false
				vm.agentsDetails = false
				vm.clientDetails = false
			}
		},
	},
};
</script>

<style scoped>
.cz{
	color:#bbb;
}
.tcard{
	border: 1px solid #DEE3EA;
	padding: 12px;
	padding-left: 18px;
	background: #F7F8FC 0% 0% no-repeat padding-box;
}

/* stopcard */
.topcard{
	height: 78px;
	border: 1px solid #DEE3EA;
	padding: 12px;
	padding-left: 18px;
	background: #F7F8FC 0% 0% no-repeat padding-box;
}

hr.rounded {
  border-top: 2px solid #bbb;
  border-radius: 3px;
}

.textcardtitle{
	text-align: left;
	font: normal normal 600 20px/24px Lato;
	letter-spacing: 0px;
	color: #6E6E6E;
	opacity: 1;
}

.textcardsubtitle{
	text-align: left;
	font: normal normal 800 15px/19px Muli;
	letter-spacing: 0px;
	color: #434343;
	opacity: 0.5;
	font-size:12px;
}

.details-c{
	text-align: left;
	letter-spacing: 0px;
	color: #434343;
}



</style>

