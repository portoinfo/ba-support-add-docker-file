<template>
	<div>
        <b-row class="mb-2">
            <b-col>
                <b-button v-if="childrenSelected.length != 0" @click="back" variant="primary"><i class="fa fa-arrow-left" aria-hidden="true"></i> {{$t('bs-back')}}</b-button>
            </b-col>
            <b-col  cols="auto">
                <b-form-select @change="getFaqRobot" v-model="defaultLanguage" :options="optionsLanguages" size="sm"></b-form-select>
            </b-col>
            <!-- <b-col v-if="childrenSelected.length == 0">
                <b-form-checkbox
                    id="checkbox-m"
                    v-model="showQuestions"
                    name="checkbox-m"
                    class="mt-3"
                    >
                    {{ $t('bs-configuration')}}
                </b-form-checkbox>
            </b-col> -->
            <b-col v-if="!showQuestions && !showQuillEditor" cols="auto">
                <b-button @click="createrobot" block variant="info"><i class="fa fa-plus-circle" aria-hidden="true"></i> {{$t('bs-new')}}</b-button>
            </b-col>
            <b-col v-if="childrenSelected.length == 0" cols="auto">
                <b-button @click="showQuestions = !showQuestions" block class="config-color"><i class="fa fa-cog" aria-hidden="true"></i></b-button>
            </b-col>
        </b-row>
        
        <b-row v-if="showQuestions">
            <b-col>
                <b-row class="bodyNew">
                    <b-col lg>
                        <b-row class='ml-2'>
                            <b-col cols="auto mt-2" class="px-0 mx-0" id="tk-opened-simultaneously-customer">
                                {{$t('bs-activate-tool-for-customers')}}:
                                <!-- &nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i> -->
                            </b-col>
                            <b-tooltip target="tk-opened-simultaneously-customer" triggers="hover" placement="right" variant="secondary">
                                {{$t('bs-tooltip-tk-opened-simultaneously-customer')}}
                            </b-tooltip>
                            <b-col>
                            </b-col>
                            <b-col cols="auto">
                                <label class="switch">
                                    <input type="checkbox" v-model="is_active">
                                    <span class="slider round"></span>
                                </label>
                            </b-col>
                        </b-row>
 
                    </b-col>
                </b-row>
                <b-row class="bodyNew">
                    <b-col lg>
                        <b-row class='ml-2'>
                            <b-col cols="auto" class="px-0 mx-0" id="tk-opened-simultaneously-customer">
                                {{$t('bs-number-of-tools-that-will-appear-to-the-cu')}}
                                <!-- &nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i> -->
                            </b-col>
                            <b-tooltip target="tk-opened-simultaneously-customer" triggers="hover" placement="right" variant="secondary">
                                {{$t('bs-tooltip-tk-opened-simultaneously-customer')}}
                            </b-tooltip>
                            <b-col>
                            </b-col>
                            <b-col cols="auto">
                                <input type="number" v-model="topNumberTools" min="0" style="border: none;width: 60px;">
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
                            <b-col cols="auto" class="px-0 mx-0" id="tk-opened-simultaneously-customer">
                                {{$t('Número de Soluções que irá aparecer para o cliente:')}}
                                <!-- &nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i> -->
                            </b-col>
                            <b-tooltip target="tk-opened-simultaneously-customer" triggers="hover" placement="right" variant="secondary">
                                {{$t('bs-tooltip-tk-opened-simultaneously-customer')}}
                            </b-tooltip>
                            <b-col>
                            </b-col>
                            <b-col cols="auto">
                                <input type="number" v-model="topSubNumberTools" min="0" style="border: none;width: 60px;">
                                <b-link href="#foo" @click="addNumber(2)"><i class="fa fa-plus fa-1x add" aria-hidden="true"></i></b-link>
                                <b-link href="#foo" @click="removeNumber(2)"><i class="fa fa-minus fa-1x remove" aria-hidden="true"></i></b-link>
                            </b-col>
                        </b-row>
                        <div class="ml-2 spantext">
						    <span>{{$t('bs-if-it-is-set-to-0')}}</span>
                        </div>
                    </b-col>
                </b-row> 
                <b-form-group
                    id="input-group-10"
                    :label="$t('bs-robot-name')+':'"
                    label-for="input-10"
                    class="mt-1"
                >
                    <b-form-input
                    id="input-13"
                    v-model="formQuestions.name_robot"
                    type="text"
                    required
                    ></b-form-input>
                </b-form-group>
                <!-- <b-form-group
                    id="input-group-9"
                    :label="$t('bs-image-robot')+':'"
                    label-for="input-9"
                    class="mt-1"
                >
                    <b-form-file
                        id="input-9"
                        v-model="formQuestions.image_robot"
                        required
                    ></b-form-file>
                </b-form-group> -->
                <b-form-group
                    id="input-group-13"
                    :label="$t('bs-opening-message')"
                    label-for="input-13"
                    class="mt-1"
                >
                    <b-form-input
                    id="input-13"
                    v-model="formQuestions.initial_message"
                    type="text"
                    required
                    ></b-form-input>
                </b-form-group>
                <b-form-group
                    id="input-group-14"
                    :label="$t('bs-offline-tool-message')"
                    label-for="input-14"
                >
                    <b-form-input
                    id="input-14"
                    v-model="formQuestions.offline_tool_message"
                    type="text"
                    required
                    ></b-form-input>
                </b-form-group>
                <b-form-group
                    id="input-group-15"
                    :label="$t('bs-confirmation-keywords')"
                    label-for="input-15"
                >
                    <b-form-input
                    id="input-15"
                    v-model="form.confirm"
                    @keyup.enter="createConfirmations"
                    type="text"
                    required
                    ></b-form-input>
                    <div class="caret m-scroll">
                        <b-badge class="mr-1" v-for="(item, index) in formQuestions.confirmations" :key="index" variant="primary" @click=removeItemKeywordConfirm(item)>X {{item}} </b-badge>
                    </div>
                </b-form-group>
                <!-- <b-form-group
                    id="input-group-16"
                    :label="$t('bs-words-or-phrase-for-tool-change')"
                    label-for="input-16"
                >
                    <b-form-input
                    id="input-16"
                    v-model="form.confirmchangeTools"
                    @keyup.enter="createChangeTools"
                    type="text"
                    required
                    ></b-form-input>
                    <div class="caret m-scroll">
                        <b-badge class="mr-1" v-for="(item, index) in formQuestions.changeTools" :key="index" variant="primary" @click=removeItemChangeTools(item)>X {{item}} </b-badge>
                    </div>
                </b-form-group> -->
                <b-form-group
                    id="input-group-16"
                    :label="$t('bs-phrases-to-talk-to-an-attendant')"
                    label-for="input-16"
                >
                    <b-form-input
                    id="input-16"
                    v-model="form.talkAttendant"
                    @keyup.enter="createAttendantTalk"
                    type="text"
                    required
                    ></b-form-input>
                    <div class="caret m-scroll">
                        <b-badge class="mr-1" v-for="(item, index) in formQuestions.phrasesSpeakAttendants" :key="index" variant="primary" @click=removeItemTalkAttendant(item)>X {{item}} </b-badge>
                    </div>
                </b-form-group>
                <!-- <b-form-group
                    id="input-group-11"
                    :label="'Mensagem para direcionar o cliente ao um atendente, caso a solução não ajude.'"
                    label-for="input-11"
                >
                    <b-form-input
                    id="input-11"
                    v-model="formQuestions.direct_message_to_attendant"
                    type="text"
                    required
                    ></b-form-input>
                </b-form-group> -->

                <b-button type="submit" variant="success" @click="saveQuestion">{{$t('bs-save')}}</b-button>
                <!-- <b-button v-if="hostname == 'localhost' && showButtonUpRobot 
                    || hostname == 'ba-support.builderall.com' && showButtonUpRobot 
                    || hostname == 'ba-support.builderall.io' && showButtonUpRobot" 
                    type="submit" variant="warnig" @click="showModalUpRobot">
                    {{$t('bs-update')}} {{ $t('bs-robot') }}
                </b-button> -->
                <b-button v-if="showButtonUpRobot" 
                    type="submit" variant="warnig" @click="showModalUpRobot">
                    {{$t('bs-update')}} {{ $t('bs-robot') }}
                </b-button>

                <span class="ml-2" style="font-weight: bold;color: slategray;" v-if="!showButtonUpRobot">
                    {{$t('bs-this-may-take-a-few-minute')}}
                </span>
            </b-col>
        </b-row>
        <h3><b>{{titleSelected}}</b></h3>
        <b-table v-if="!showQuillEditor && !showQuestions" responsive bordered borderless striped hover
            class="local-striped-table"
            head-variant="light"
            table-variant="light"
            :items="itemsFilter"
            :fields="fields"
            show-empty
        >
            <template #cell(index)="row">
                {{ row.index + 1 }}
            </template>
            <template #cell(status)="row">
                <div @click="statusTool(row.item)" :class="row.item.tool_status ? 'online-indicator' : 'ofline-indicator'">
                    <span :class="row.item.tool_status ? 'online-blink' : 'ofline-blink'"></span>
                </div>
            </template>
            <!-- <template #cell(dad_description)="row">
                <span v-for="(item, index) in JSON.parse(row.item.dad_description)" :key="index">
                    <span v-if="item.language == usuario.language">
                        {{item.description}}
                    </span>
                </span>
            </template>
            <template #cell(description)="row">
                <span v-for="(item, index) in JSON.parse(row.item.description)" :key="index">
                    <span v-if="item.language == usuario.language">
                        {{item.description}}
                    </span>
                </span>
            </template> -->
            <template #cell(language)="row">
                <span v-if="row.item.language != null">
                    <img
                    :src="`https://flagcdn.com/24x18/${row.item.language.split('_')[1].toLowerCase()}.png`"
                    class="mr-2"
                    />
                </span>
            </template>
            <template #cell(view_robot)="row">
                <div class="c-blue caret" @click="viewChildren(row.item)">
                    <vue-material-icon name="visibility" :size="30"/>
                </div>
            </template>
            <template #cell(edit_robot)="row">
                <div class="c-info caret" @click="editrobot(row.item, 'children')">
                    <vue-material-icon name="edit" :size="30"/>
                </div>
            </template>
            <template #cell(active)="row">
                <label class="switch">
                    <input type="checkbox" @click="activeRobot(row.item, row.item.active)" v-model="row.item.active">
                    <span class="slider round"></span>
                </label>
            </template>
            <template #cell(delete_robot)="row">
                <div class="c-red caret" @click="deleterobot(row.item, 'children')">
                    <vue-material-icon name="delete" :size="30"/>
                </div>
            </template>
            <template #empty="scope">
                <div class="text-center">{{$t('bs-no-registered')}}.</div>
            </template>
        </b-table>

        <span v-if="showQuillEditor">
            <b-form-group
                id="input-group-1"
                :label="$t('bs-title')"
                label-for="input-1"
            >
                <b-form-input
                id="input-1"
                v-model="form.title"
                type="text"
                required
                ></b-form-input>
            </b-form-group>

            <b-form-group
                id="input-group-2"
                :label="$t('bs-key-phrase')"
                label-for="input-1"
            >
                <b-form-input
                id="input-1"
                v-model="form.keyword"
                type="text"
                required
                @keyup.enter="keywordConcat"
                ></b-form-input>
                <div class="caret m-scroll">
                    <b-badge class="mr-1" v-for="(item, index) in form.keywordAll" :key="index" variant="primary" @click=removeItemKeyword(item)>X {{item}} </b-badge>
                </div>
            </b-form-group>

            <quill-editor
                ref="TicketDescriptionQuillEditor"
                v-model="descriptionFaqRobot"
                :options="quillEditorOptions"
                id="q-editor-description"
                class="bg-white border mh-300"
            />

            <b-button @click="cancelEdit" variant="danger">{{$t('bs-cancel')}}</b-button>
            <b-button @click="save" variant="success">{{$t('bs-save')}}</b-button>
        </span>
       
        <div
        class="modal fade"
        id="modalFAQROBOT"
        tabindex="-1"
        aria-labelledby="modalFAQROBOT"
        aria-hidden="true"
        data-backdrop="static"
        data-keyboard="false"
        >
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                        <h4 class="modal-title" id="exampleModalLabel">{{$t('bs-edit')}}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> 
                            X
                        </button>
                    </div>
                    <div class="border-0 p-0">
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                            
                                <b-form-group
                                    id="input-group-1"
                                    :label="$t('bs-tool')"
                                    label-for="input-1"
                                >
                                    <b-form-input
                                    id="input-2"
                                    v-model="form.title"
                                    type="text"
                                    required
                                    ></b-form-input>
                                </b-form-group>

                                <b-form-group
                                    id="input-group-1"
                                    :label="$t('bs-description')"
                                    label-for="input-3"
                                    v-if="childrenSelected.length == 0"
                                >
                                    <b-form-input
                                    id="input-4"
                                    v-model="form.description"
                                    type="text"
                                    required
                                    ></b-form-input>
                                </b-form-group>

                                <b-form-group
                                    id="input-group-1"
                                    :label="$t('bs-keyword')"
                                    label-for="input-3"
                                    v-if="childrenSelected.length == 0"
                                >
                                    <b-form-input
                                    id="input-4"
                                    v-model="form.keyword"
                                    type="text"
                                    required
                                    @keyup.enter="keywordConcat"
                                    ></b-form-input>
                                </b-form-group>
                                <div class="caret m-scroll">
                                    <b-badge class="mr-1" v-for="(item, index) in form.keywordAll" :key="index" variant="primary" @click=removeItemKeyword(item)>X {{item}} </b-badge>
                                </div>

                                <b-form-group class="mt-2" id="input-group-3" :label="$t('bs-language')" label-for="input-3"
                                    v-if="childrenSelected.length == 0">
                                    <b-form-select
                                    id="input-3"
                                    size="sm"
                                    v-model="form.language"
                                    :options="optionsLanguages"
                                    required
                                    ></b-form-select>
                                </b-form-group>


                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="text-capitalize btn" data-dismiss="modal">
                            {{$t('bs-back')}}
                        </button>
                        <button type="button" id="btn-department" class="btn btn-success" @click="CreateFaq">
                            {{$t('bs-save')}}
                        </button>
                    </div>

                    <br>
                </div>
            </div>
        </div>

        <div
        class="modal fade"
        id="modalEditRobot"
        tabindex="-1"
        aria-labelledby="modalEditRobot"
        aria-hidden="true"
        data-backdrop="static"
        data-keyboard="false"
            >
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                        <h4 class="modal-title" id="exampleModalLabel">{{$t('bs-edit')}}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="cancelModal"> 
                            X
                        </button>
                    </div>
                    <div class="border-0 p-0">
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                            
                                <b-form-group
                                    id="input-group-1"
                                    :label="$t('bs-tool')"
                                    label-for="input-5"
                                >
                                    <b-form-input
                                    id="input-6"
                                    v-model="form.title"
                                    type="text"
                                    required
                                    ></b-form-input>
                                </b-form-group>

                                <b-form-group
                                    id="input-group-1"
                                    :label="$t('bs-description')"
                                    label-for="input-7"
                                >
                                    <b-form-input
                                    id="input-8"
                                    v-model="form.description"
                                    type="text"
                                    required
                                    ></b-form-input>
                                </b-form-group>

                                <b-form-group
                                    id="input-group-1"
                                    :label="$t('bs-keyword')"
                                    label-for="input-33"
                                >
                                    <b-form-input
                                    id="input-4"
                                    v-model="form.keyword"
                                    type="text"
                                    required
                                    @keyup.enter="keywordConcat"
                                    ></b-form-input>
                                </b-form-group>
                                <div class="caret m-scroll">
                                    <b-badge class="mr-1" v-for="(item, index) in form.keywordAll" :key="index" variant="primary" @click=removeItemKeyword(item)>X {{item}} </b-badge>
                                </div>

                                <b-form-group class="mt-2" id="input-group-3" :label="$t('bs-language')" label-for="input-3">
                                    <b-form-select
                                    id="input-3"
                                    size="sm"
                                    v-model="form.language"
                                    :options="optionsLanguages"
                                    required
                                    ></b-form-select>
                                </b-form-group>


                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="text-capitalize btn" data-dismiss="modal" @click="cancelModal">
                            {{$t('bs-back')}}
                        </button>
                        <button type="button" id="btn-department" class="btn btn-success" @click="editCreateFaq">
                            {{$t('bs-save')}}
                        </button>
                    </div>

                    <br>
                </div>
            </div>
        </div>

        <div
        class="modal fade"
        id="m-upRobot"
        tabindex="-1"
        aria-labelledby="m-upRobot"
        aria-hidden="true"
        data-backdrop="static"
        data-keyboard="false"
        >
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                        <h4 class="modal-title" id="exampleModalLabel">{{$t('bs-tools')}}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> 
                            X
                        </button>
                    </div>
                    <div class="border-0 p-0">
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">                      
                                <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{ $t('bs-tools') }}</th>
                                        <th>{{ $t('bs-last') }} {{$t('bs-size')}} {{ $t('bs-total') }}</th>
                                        <th>{{$t('bs-size')}} {{ $t('bs-total') }}</th>
                                        <th>{{$t('bs-date')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in faqrobot" :key="index" v-if="item.company_faq_robot_tool_id == null" >
                                        <td>
                                            <b-form-group v-slot="{ ariaDescribedby }">
                                                <b-form-radio v-if="item.company_faq_robot_tool_id == null" 
                                                        :aria-describedby="ariaDescribedby" name="some-radios"  v-model="toTrainTools" :value=item.id>
                                                    {{ item.title }}
                                                </b-form-radio>
                                            </b-form-group>
                                        </td>
                                        <td>{{ item.last_total }}</td>
                                        <td>{{ item.total }}</td>
                                        <td>{{ UTCtoClientTZ(item.created_at, tz) }}</td>
                                    </tr>
                                    <tr v-if="!faqrobot || faqrobot.length === 0">
                                        <td colspan="3" class="text-center">{{ $t('bs-no-data-yet') }}</td>
                                    </tr>
                                </tbody>
                                </table> 
                           

<!-- 
                                <b-form-group v-slot="{ ariaDescribedby }">
                                    <b-form-radio v-for="(item, index) in faqrobot" :key="index" 
                                            v-if="item.company_faq_robot_tool_id == null" 
                                            :aria-describedby="ariaDescribedby" name="some-radios"  v-model="toTrainTools" :value=item.id>
                                        {{ item.title }}
                                    </b-form-radio>
                                </b-form-group> -->


                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="text-capitalize btn" data-dismiss="modal">
                            {{$t('bs-back')}}
                        </button>
                        <button type="button" id="btn-department" class="btn btn-success" @click="updateRobot">
                            {{$t('bs-to-train')}}
                        </button>
                    </div>

                    <br>
                </div>
            </div>
        </div>

    <br><br>
    
  </div>
</template>

<script>
import Quill from 'quill';
import { quillEditor } from 'vue-quill-editor';
import 'quill/dist/quill.core.css';
import 'quill/dist/quill.snow.css';
import 'quill/dist/quill.bubble.css';
import "quill-emoji/dist/quill-emoji.css";
import * as Emoji from "quill-emoji";
import { container, ImageExtend, QuillWatch } from 'quill-image-extend-module';
import BlotFormatter from 'quill-blot-formatter';
import ImageCompress from 'quill-image-compress';
import AutoLinks from 'quill-auto-links';
import Delta, { AttributeMap } from 'quill-delta';
import { mapState, mapMutations } from "vuex";

export default {
    components: {
		quillEditor,
	},
	data(){
		return {
			fields: [
                {key: 'status', label: '#', sortable: true},
                {key: 'title', label: this.$t('bs-tool'), sortable: true},
                {key: 'description', label: this.$t('bs-description'), sortable: true},
                {key: 'language', label: this.$t('bs-language')},
                {key: 'view_robot', label: this.$t('bs-view') +' '+ this.$t('bs-robot')},
                {key: 'edit_robot', label:this.$t('bs-edit')},
                {key: 'active', label:this.$t('bs-active')},
                {key: 'delete_robot', label: this.$t('bs-delete')},
			],
            form: {
                title: '',
                description: '',
                language: '',
                keyword: '',
                keywordAll: [],
                confirm: '',
                confirmchangeTools: '',
                talkAttendant: '',
            },
            formQuestions: {
                name_robot: '',
                initial_message: '',
                offline_tool_message: '',
                direct_message_to_attendant: '',
                confirmations: [],
                changeTools: [],
                phrasesSpeakAttendants: [],
                image_robot: '',
            },
            itemsFilter: [],
            itemfaqrobot: [],
            fieldsFaqRobot: [
                {key: 'index', label: '#', sortable: true},
                {key: 'id', label: this.$t('bs-id'), sortable: true},
                {key: 'title', label: this.$t('bs-tool'), sortable: true},
                {key: 'company_faq_robot_tool_id', label: this.$t('bs-company_faq_robot_tool_id'), sortable: true},
			],
            faqrobot: [],
            childrenSelected: [],
            itemEditSelected: [],
            showQuillEditor: false,
            descriptionFaqRobot: '',
            quillEditorOptions: {
				placeholder: this.$t('bs-type-here')+'...',
				modules: {
					toolbar: [
						[
						'bold', 
						'italic', 
						'underline', 
						'strike', 
						'code-block', 
						'link', 
						'image', 
                        'video',
						{'list': 'bullet'},
						{'size': ['small', false, 'large', 'huge']},
						{'color': [] }, 
						{'background': []}, 
						{'align': []},
						'emoji',
						'clean'
						]                                         
					],
					"emoji-toolbar": true,
					"emoji-textarea": false,
					"emoji-shortname": true,
					autoLinks: {
						paste: true,
						type: true
					},
					ImageExtend: {
						loading: true,
						name: 'img',
					},
					imageCompress: {
						quality: 0.9,
						maxWidth: 2000,
						maxHeight: 2000,
						imageType: 'image/jpeg',
						debug: false,
						suppressErrorLogging: false,
					},
				}
			},
            content: '',
            optionsLanguages: [],
            defaultLanguage: '',
            showQuestions: false,
            topNumberTools: 3,
            topSubNumberTools: 3,
            is_active: false,
            hostname: '',
            toTrainTools: '',
            showButtonUpRobot: true,
            tz: '',
            titleSelected: '',
		}
	},
    props:{
        usuario:Object,
    },
	created(){
        this.hostname = window.location.hostname;
		this.$root.$refs.CompanyConfigFaqRobot = this;
        this.defaultLanguage = this.usuario.language;
        this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
        this.getFaqRobot();
        Quill.register('modules/blotFormatter', BlotFormatter);
		Quill.register('modules/ImageExtend', ImageExtend)
		Quill.register('modules/imageCompress', ImageCompress);
		Quill.register('modules/autoLinks', AutoLinks);
	},
	methods: {
        createConfirmations(){
            this.formQuestions.confirmations.push(this.form.confirm);
            this.form.confirm = ''
        },
        createChangeTools(){
            this.formQuestions.changeTools.push(this.form.confirmchangeTools);
            this.form.confirmchangeTools = ''
        },
        createAttendantTalk(){
            this.formQuestions.phrasesSpeakAttendants.push(this.form.talkAttendant);
            this.form.talkAttendant = ''
        },
        removeItemKeywordConfirm(stringRemove){
            var posicao = this.formQuestions.confirmations.indexOf(stringRemove);
            if (posicao !== -1) {
                this.formQuestions.confirmations.splice(posicao, 1);
            }
        },
        removeItemChangeTools(stringRemove){
            var posicao = this.formQuestions.changeTools.indexOf(stringRemove);
            if (posicao !== -1) {
                this.formQuestions.changeTools.splice(posicao, 1);
            }
        },
        removeItemTalkAttendant(stringRemove){
            var posicao = this.formQuestions.phrasesSpeakAttendants.indexOf(stringRemove);
            if (posicao !== -1) {
                this.formQuestions.phrasesSpeakAttendants.splice(posicao, 1);
            }
        },
        removeItemKeyword(stringRemove){
            var posicao = this.form.keywordAll.indexOf(stringRemove);
            if (posicao !== -1) {
                this.form.keywordAll.splice(posicao, 1);
            }
        },
        keywordConcat(){
            try {
                var array = JSON.parse(this.form.keyword);
                array.forEach(element => {
                    var string = element.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
                    console.log(this.form.keywordAll);
                    if(this.form.keywordAll == null){
                        this.form.keywordAll.push(string);
                        this.form.keyword = '';
                    }else{
                        var existe = (this.form.keywordAll.indexOf(string) !== -1);
                        if(existe){
                            this.$snotify.info(this.$t('bs-already-registered'), this.$t('bs-info'));
                        }else{
                            this.form.keywordAll.push(string);
                            this.form.keyword = '';
                        }
                    } 
                });
            } catch (error) {
                var string = this.form.keyword.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
                console.log(this.form.keywordAll);
                if(this.form.keywordAll == null){
                    this.form.keywordAll.push(string);
                    this.form.keyword = '';
                }else{
                    var existe = (this.form.keywordAll.indexOf(string) !== -1);
                    if(existe){
                        this.$snotify.info(this.$t('bs-already-registered'), this.$t('bs-info'));
                    }else{
                        this.form.keywordAll.push(string);
                        this.form.keyword = '';
                    }
                }
            } 
        },
        statusTool(item){
            axios.post('company-config/update-status-tool', {
                id: item.id,
                tool_status: item.tool_status,
            });
            item.tool_status = item.tool_status == 1 ? 0 : 1;
        },
        saveQuestion(){
            var formData = new FormData();
            formData.append("item", JSON.stringify(this.formQuestions));
            formData.append("language", this.defaultLanguage);
            formData.append("topNumberTools", this.topNumberTools);
            formData.append("topSubNumberTools", this.topSubNumberTools);
            formData.append("is_active", this.is_active);

            var files = [this.formQuestions.image_robot];
            
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                formData.append("files[" + i + "]", file);
            }

            var url = `${this.$store.state.baseURL}/company-config/set-question-robot`
            var vm = this;
            axios.post(url, formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            })
            .finally(() => {
                this.$snotify.success(this.$t('bs-saved-successfully'), this.$t('bs-success'));
            })

            // axios.post('company-config/set-question-robot', {
            //     item: this.formQuestions,
            //     language: this.defaultLanguage,
            //     topNumberTools: this.topNumberTools,
            //     topSubNumberTools: this.topSubNumberTools,
            //     is_active: this.is_active,
            // })
            // .then(({ data }) => {
            //     // this.showQuestions = false;
            //     this.$snotify.success(this.$t('bs-saved-successfully'), this.$t('bs-success'));
            // });
        },
        showModalUpRobot(){
        //     this.getFaqRobot();
        //     $('#m-upRobot').modal('show');
            this.updateRobot();
        },
        updateRobot(){
            this.showButtonUpRobot = false
            $('#m-upRobot').modal('hide');
            
            axios.post('company-config/update-robot', {
                toTrainTool: this.toTrainTools,
                hostname: this.hostname,
                defaultLanguage: this.defaultLanguage,
            }).then(({ data }) => {
                // console.log(data);
                if(data == undefined){

                }else if(data == 'the_training_is_not_over'){
                    this.$snotify.info(this.$t("bs-robo-is-still-in-training"), this.$t("bs-info"));
                }
                // this.toTrainTools = '';
                this.showButtonUpRobot = true;
            }).catch(err => {
                this.showButtonUpRobot = true;
            })
        },
        sendMessageTeste(){
            axios.get('company-config/sendMessageTeste', {
            })
            .then(({ data }) => {
            });
        },
        save(){
            var content = this.descriptionFaqRobot;
            if (content !== '') {
                var vm = this;
                vm.content = "";
                var url = `company-config/set-faq-robot`

                var images = this.extractImage(content);
                if (images.length) {
                    content = this.replaceImageSize(content)
                }

                axios.post(url, {
                    content,
                    title: this.form.title,
                    images,
                    keywordAll: this.form.keywordAll,
                    itemselected: this.childrenSelected,
                })
                .then(({ data }) => {
                    this.childrenSelected.title = this.form.title;
                    this.childrenSelected.description = data.content;
                    this.childrenSelected.keywordAll = this.form.keywordAll
                    this.showQuillEditor = false;
                });
            }
        },
        editCreateFaq(){
            var url = `company-config/update-faq-robot`
            axios.post(url, {
                title: this.form.title,
                description: this.form.description,
                keywordAll: this.form.keywordAll,
                language: this.form.language,
                itemselected: this.childrenSelected,
            })
            .then(({ data }) => {
                if(data.success){
                    this.childrenSelected.title = this.form.title;
                    this.childrenSelected.description = this.form.description;
                    this.childrenSelected.keywordAll = this.form.keywordAll;
                    this.childrenSelected.language = this.form.language;
                    $('#modalEditRobot').modal('hide');
                }
            });
        },
        CreateFaq(){
            var url = `company-config/create-faq-robot`

            axios.post(url, {
                title: this.form.title,
                description: this.form.description,
                keywordAll: this.form.keywordAll,
                language: this.defaultLanguage,
                itemselected: this.childrenSelected,
            })
            .then(({ data }) => {
                if(data.success){
                    this.faqrobot.push({
                        id: data.id,
                        company_faq_robot_tool_id: data.company_faq_robot_tool_id,
                        title: this.form.title,
                        description: this.form.description,
                        language: this.defaultLanguage,
                        tool_status: data.tool_status,
                    });
                    this.itemsFilter = [];
                    this.faqrobot.forEach(element => {
                        if(element.company_faq_robot_tool_id == data.company_faq_robot_tool_id){
                            this.itemsFilter.push(element);
                        }
                    });
                    $('#modalFAQROBOT').modal('hide');
                }
            });
        },
        extractImage(content) {
            var quotes = content.split('"');
            var images = [];
            var i = 0;
            quotes.forEach(element => {
                i++
                if (element.substring(0, 10) == 'data:image') {
                    images.push(element);
                }
            });

            return images;
        },
        replaceImageSize(content) {
            return content.replaceAll('><img', '><img  style="height: 150px;"');
        },
        cancelEdit(){
            this.showQuillEditor = false;
        },
        cancelModal(){
            this.childrenSelected = [];
            this.form.title = '';
            this.form.description = '';
            this.form.keyword = '';
            this.form.language = '';
        },
        viewChildren(item){
            this.titleSelected = item.title;
            this.childrenSelected = item;
            this.itemsFilter = [];
            this.fields= [
                {key: 'index', label: '#', sortable: true},
                {key: 'title', label: this.$t('bs-tool'), sortable: true},
                {key: 'edit_robot', label:this.$t('bs-edit')},
                {key: 'active', label:this.$t('bs-active')},
                {key: 'delete_robot', label: this.$t('bs-delete')},
			],
            this.faqrobot.forEach(element => {
                if(element.company_faq_robot_tool_id ==  item.id){
                    this.itemsFilter.push(element);
                }
            });
        },
        createrobot(){
            this.form.title = ''
            this.form.description = '';
            this.form.keyword = '';
            this.form.language = this.usuario.language;
            $('#modalFAQROBOT').modal('show');
        },
        editrobot(item, type = 'children'){
            if(item.company_faq_robot_tool_id == null){
                $('#modalEditRobot').modal('show');
                this.form.title = item.title;
                this.form.description = item.description;
                this.form.keywordAll = item.keywordAll == null ? [] : item.keywordAll;
                this.form.language = item.language;
            }else{
                this.form.title = item.title;
                this.descriptionFaqRobot = item.description;
                this.form.keywordAll = item.keywordAll == null ? [] : item.keywordAll;
                this.showQuillEditor = true;
            }
            this.childrenSelected = item;
        },
        activeRobot(item, bool){
            axios.post('company-config/active-faq-robot',{
                id: item.id,
                bool: bool,
            });
        },
        deleterobot(item, type = 'children'){
            var vm = this;
            Swal.fire({
                title: vm.$t('bs-are-you-sure'),
                text: vm.$t('bs-you-wont-be-able-to-revert-this'),
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: vm.$t('bs-cancel'),
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: vm.$t('bs-yes-delete-it'),
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('company-config/delete-faq-robot',{
                        id: item.id
                    }).then(({data}) => {
                        if(data.success) {
                            let index = vm.faqrobot.findIndex((elemt) => elemt.id === item.id);
                            if (index !== -1) {
                                vm.faqrobot.splice(index, 1);
                            }

                            let index2 = vm.itemsFilter.findIndex((elemt2) => elemt2.id === item.id);
                            if (index2 !== -1) {
                                vm.itemsFilter.splice(index2, 1);
                            }
                            
                            Swal.fire(
                                vm.$t('bs-deleted'),
                                vm.$t('bs-successfully-deleted'),
                                'success'
                            )
                        }else{
                            if(data.error == 'already_linked'){
                                vm.$snotify.info(vm.$t('bs-error'), vm.$t('bs-info'));
                            }
                        }
                    }).catch(err => {
                        this.$loading(false);
                    })
                } else {
                    this.$loading(false);
                }
            });
        },
		getFaqRobot(){
            var vm = this;
            axios.get(`company-config/get-faq-robot`, {
                params: {
                    language: this.defaultLanguage,
                }
            }).then(function(response){
                // console.log(response.data.result);
                vm.faqrobot = response.data.result;

                if(response.data.result2 == null){
                    vm.formQuestions.name_robot = '';
                    vm.formQuestions.initial_message = '';
                    vm.formQuestions.offline_tool_message = '';
                    vm.formQuestions.direct_message_to_attendant = '';
                }else{
                    vm.formQuestions.name_robot = response.data.result2.name_robot;
                    vm.formQuestions.initial_message = response.data.result2.initial_message;
                    vm.formQuestions.offline_tool_message = response.data.result2.offline_tool_message;
                    vm.formQuestions.direct_message_to_attendant = response.data.result2.direct_message_to_attendant;
                    vm.formQuestions.confirmations = response.data.result2.confirm_keywords == null ? [] : JSON.parse(response.data.result2.confirm_keywords);
                    vm.formQuestions.changeTools = response.data.result2.change_tools_keywords == null ? [] : JSON.parse(response.data.result2.change_tools_keywords);
                    vm.formQuestions.phrasesSpeakAttendants = response.data.result2.talk_to_attendant == null ? [] : JSON.parse(response.data.result2.talk_to_attendant);
                }

                if(response.data.result3 == null){
                    vm.is_active = false;
                    vm.topNumberTools = 3;
                    vm.topSubNumberTools = 3;
                }else{
                    vm.is_active = response.data.result3.is_active;
                    vm.topNumberTools = response.data.result3.topNumberTools == null ? 3 : response.data.result3.topNumberTools;
                    vm.topSubNumberTools = response.data.result3.topSubNumberTools == null ? 3 : response.data.result3.topSubNumberTools;
                }

                vm.back();
                vm.mountLanguage();
            }).catch(function (error) {
                console.log(error);
            });
        },
        mountLanguage(){
            this.$store.state.languages.forEach(item => {
                this.optionsLanguages.push({value: item.key, text: item.desc});
                // this.alledits.push({language: item.key, description: ''});
            });
        },
        back(){
            if(this.showQuillEditor == true){
                this.showQuillEditor = false;
            }else{
                this.childrenSelected = [];
                this.fields= [
                    {key: 'status', label: '#', sortable: true},
                    {key: 'title', label: this.$t('bs-tool'), sortable: true},
                    {key: 'description', label: this.$t('bs-description'), sortable: true},
                    {key: 'language', label: this.$t('bs-language')},
                    {key: 'view_robot', label: this.$t('bs-view')},
                    {key: 'edit_robot', label:this.$t('bs-edit')},
                    {key: 'active', label:this.$t('bs-active')},
                    {key: 'delete_robot', label: this.$t('bs-delete')},
                ],
                this.itemsFilter = [];
                this.titleSelected = '';
                this.faqrobot.forEach(element => {
                    if(element.company_faq_robot_tool_id == null){
                        this.itemsFilter.push(element);
                    }
                });
            }
        },
        addNumber(value){
			var vm = this;
			if(value == 1){
				vm.topNumberTools = parseInt(vm.topNumberTools) + 1;
			}
            if(value == 2){
				vm.topSubNumberTools = parseInt(vm.topSubNumberTools) + 1;
			}
		},
        removeNumber(value){
			var vm = this;
			if(value == 1){
				vm.topNumberTools = parseInt(vm.topNumberTools) - 1;
			}
            if(value == 2){
				vm.topSubNumberTools = parseInt(vm.topSubNumberTools) - 1;
			}
		},
        UTCtoClientTZ(h, tz) {
            if(moment(h, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD HH:mm:ss") == 'Invalid date'){
                return ' -- '
            }
            let h_format = moment(h, "YYYY-MM-DD HH:mm:ss").format(
                "YYYY-MM-DD HH:mm:ss"
            );
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
            let dateUTC = new Date(
                Date.UTC(year, month_integer, day, hour, minute, second)
            );
            let converted_time = dateUTC.toLocaleString("pt-BR", {
                timeZone: tz
            });
            var mt = require("moment-timezone");
            if(this.usuario.language == 'pt_BR'){
                return mt(converted_time, "DD/MM/YYYY HH:mm:ss")
                .tz(tz)
                .locale(this.usuario.language)
                .format("HH:mm DD/MM/YYYY");
            }else{
                return mt(converted_time, "DD/MM/YYYY HH:mm:ss")
                .tz(tz)
                .locale(this.usuario.language)
                .format("YYYY/MM/DD HH:mm");
            }
            
        },
	},
};
</script>

<style scoped>

    .config-color{
        background-color: #0080FC;
    }

    .m-scroll{
        margin-top: 10px;
        margin-bottom: 10px;
        max-height: 100px;
        overflow: auto;
        background-color: #f5f5f5;
    }
    
    .mh-300{
        min-height: 300px;
    }
    .caret {
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    }

    .c-blue{
        color: #007bff !important;
    }
    .c-green{
        color: #28a745 !important;
    }
    .c-red{
        color: #c82333 !important;
    }

    .c-info{
        color: #016c70 !important;
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
	font: normal normal bold 22px/28px Muli;
	letter-spacing: 0px;
	color: #201f1f;
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
	height: 50px;
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
}
div.online-indicator {
  display: inline-block;
  width: 15px;
  height: 15px;
  margin-right: 10px;
  
  background-color: #0fcc45;
  border-radius: 50%;
  
  position: relative;
}
span.online-blink {
  display: block;
  width: 15px;
  height: 15px;
  
  background-color: #0fcc45;
  opacity: 0.7;
  border-radius: 50%;
  
  animation: blink 1s linear infinite;
}

div.ofline-indicator {
  display: inline-block;
  width: 15px;
  height: 15px;
  margin-right: 10px;
  
  background-color: #cc0f19;
  border-radius: 50%;
  
  position: relative;
}
span.ofline-blink {
  display: block;
  width: 15px;
  height: 15px;
  
  background-color: #cc0f19;
  opacity: 0.7;
  border-radius: 50%;
  
  animation: blink 1s linear infinite;
}

/*Animations*/
@keyframes blink {
  100% { transform: scale(2, 2); 
          opacity: 0;
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
.bodyNew{
	background-color: white;
	margin-bottom:10px;
	padding: 15px;
    margin-left: 1px;
    margin-right: 1px;
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

</style>