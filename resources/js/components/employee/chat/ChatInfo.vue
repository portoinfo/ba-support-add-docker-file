<template>
    <b-list-group id="info" v-if="chat.show && form.answers.loaded" v-scrollbar="{alwaysShowTracks: true}">
        <b-list-group-item v-if="chat.id" variant="dark" class="title pt-2 pb-2">{{ $t("bs-ticket") }}</b-list-group-item>
        <b-list-group-item v-else variant="dark" class="title pt-2 pb-2">{{ $t("bs-chat") }}</b-list-group-item>

        <!-- ====================================== NUMBER  ====================================== -->
        <b-list-group-item
            class="pl-2 pt-1 pb-1 pr-2 d-flex justify-content-between align-items-center"
        >
            <span class="bs-ico ico-info">&#xe9ef;</span>
            <span class="text-info-custom pl-2 pr-1">{{ chat.number }}</span>
            <b-badge
                v-show="chat.number !== undefined"
                class="cursor-pointer badge-copy"
                variant="light"
                pill
                @click="copyToClipboard(chat.number, 'idChat')"
                :id="`id-chat-${_uid}`"
            >
                <span class="bs-ico">&#xe14d;</span>
            </b-badge>
            <b-tooltip
                :target="`id-chat-${_uid}`"
                :show.sync="tooltips.idChat"
                triggers="manual"
                placement="right"
                :variant="tooltipVariant2"
                :container="''._uid"
            >
                {{ $t("bs-copied") }}
            </b-tooltip>
        </b-list-group-item>
        <!-- ======================================  FIM - NUMBER  ====================================== -->

        <!-- ====================================== BUTTONS TAKE/HISTORY  ====================================== -->
        <slot name="modalComments"></slot>

        <b-list-group-item
            v-if="!showChat && !chat.id"
            class="pb-1 d-flex justify-content-between align-items-center"
            :disabled="checkBtnDisabled()"
            @click="btnAction()"
        >
            <b-button size="sm" class="w-100" variant="light">{{ btn_chat }}</b-button>
        </b-list-group-item>

        <b-list-group-item
            class="pt-1 pb-1 d-flex justify-content-between align-items-center"
        >
            <b-button @click="openClientHistory(chat.client.id ? chat.client.id : chat.client_id)" size="sm" class="w-100" variant="primary">
                {{ $t("bs-view-customer-history") }}
            </b-button>
        </b-list-group-item>

        <template v-if="!chat.id">
            <b-list-group-item
                v-if="(restriction.chat_delete === 1 || restriction.chat_admin == 1)"
                class="pt-1 d-flex justify-content-between align-items-center"
            >
                <b-button @click="deleteChatTicket(chat.chat_id, 'CHAT')" size="sm" class="w-100" variant="outline-danger">
                    {{ $t("bs-delete") }}
                </b-button>
            </b-list-group-item>

            <b-list-group-item
                class="pt-1 d-flex justify-content-between align-items-center"
            >
                <b-button @click="openCategory(chat, 'TICKET')" size="sm" class="w-100" variant="outline-info">
                    {{ $t("bs-categorize") }}
                </b-button>
            </b-list-group-item>
        </template>

        <template v-else>
            <b-list-group-item
                v-if="(restriction.ticket_delete === 1 || restriction.ticket_admin == 1)"
                class="pt-1 d-flex justify-content-between align-items-center"
            >
                <b-button @click="deleteChatTicket(chat.id, 'TICKET')" size="sm" class="w-100" variant="outline-danger">
                    {{ $t("bs-delete") }}
                </b-button>
            </b-list-group-item>

            <b-list-group-item
                class="pt-1 d-flex justify-content-between align-items-center"
            >
                <b-button @click="openCategory(chat, 'TICKET')" size="sm" class="w-100" variant="outline-info">
                    {{ $t("bs-categorize") }}
                </b-button>
            </b-list-group-item>

            <b-list-group-item
                v-if="chat.status == 'OPENED' && chat.is_merged == 1 || chat.status == 'CLOSED' && chat.is_merged == 1 || chat.status == 'IN_PROGRESS' && chat.is_merged == 1"
                class="pt-0 d-flex justify-content-between align-items-center"
            >
                <b-button @click="getMergedTicket(chat, 'TICKET')" size="sm" class="w-100" variant="warning">
                    {{ $t("bs-merged") }}
                </b-button>
            </b-list-group-item>

            <b-list-group-item
                v-if="chat.status == 'OPENED' || chat.status == 'IN_PROGRESS'"
                class="pt-0 d-flex justify-content-between align-items-center"
            >
                <b-button @click="mergeTicket(chat, 'TICKET')" size="sm" class="w-100" :variant="setFirstMerge == false ? 'outline-warning' : 'warning'">
                    {{ $t("bs-merge") }} <span v-if="setFirstMerge">Com</span>
                </b-button>
            </b-list-group-item>

        </template>


        <b-list-group-item
            v-if="show_client_tickets && !chat.id"
            class="pl-2 pt-1 pb-1 pr-2 d-flex justify-content-between align-items-center text-lowercase font-italic"
        >
            <span class="bs-ico ico-info">&#xe88e;</span>
            <span class="text-info-custom pl-2 pr-1 text-danger">

                <template v-if="client_tickets_queue >= 1 && client_tickets_progress == 0">
                    {{ $t('bs-this-client-has') }}
                    {{ ` ${client_tickets_queue} ${client_tickets_queue > 1 ? $t('bs-tickets') : $t('bs-ticket')} ${$t('bs-in-queue')}` }}
                </template>

                <template v-else-if="client_tickets_progress >= 1 && client_tickets_queue == 0">
                    {{ $t('bs-this-client-has') }}
                    {{ ` ${client_tickets_progress} ${client_tickets_progress > 1 ? $t('bs-tickets') : $t('bs-ticket')} ${$t('bs-in-progress')}` }}
                </template>

                <template v-else-if="client_tickets_progress >= 1 && client_tickets_queue >= 1">
                    {{ $t('bs-this-client-has') }}
                    {{ ` ${client_tickets_queue} ${client_tickets_queue > 1 ? $t('bs-tickets') : $t('bs-ticket')} ${$t('bs-in-queue')} ` }}
                    {{ $t('bs-and') }}
                    {{ ` ${client_tickets_progress} ${client_tickets_progress > 1 ? $t('bs-tickets') : $t('bs-ticket')} ${$t('bs-in-progress')}` }}
                </template>

            </span>
        </b-list-group-item>
        <!-- ====================================== FIM - BUTTONS TAKE/HISTORY  ====================================== -->

        <!-- ====================================== QUESTIONS  ====================================== -->
        <template v-if="form.answers.length">
            <b-list-group-item variant="dark" class="title pt-2 pb-2">
                {{ `${form.titulo} - ` }}
            <i class="fa fa-question-circle title-answer-form" aria-hidden="true" id="answer-form-title"></i>
                <b-tooltip
                    target="answer-form-title"
                    triggers="hover"
                    placement="bottom"
                    variant="secondary"
                    container="info"
                >
                    {{ $t("bs-tooltip-answer") }}
                </b-tooltip>
            </b-list-group-item>

            <div v-for="(answer, index) in form.answers" :key="index">
                <b-list-group-item
                    class="pl-2 pt-1 pb-0 pr-2 d-flex justify-content-between align-items-center"
                >
                    <span class="text-info-custom">{{ $t(answer.question) }}</span>
                </b-list-group-item>

                <b-list-group-item
                    class="pl-2 pt-1 pb-1 pr-2 d-flex justify-content-between align-items-center"
                >
                    <span class="text-info-custom answer pl-2">{{ answer.answer.replace(/<\/?[^>]+(>|$)/g, "") }}</span>
                    <b-badge
                        class="cursor-pointer badge-copy"
                        variant="light"
                        pill
                        v-show="answer.answer !== undefined"
                        @click="copyToClipboard(answer.answer, 'answers', index)"
                        :id="`answer-${_uid}-${index}`"
                    >
                        <span class="bs-ico">&#xe14d;</span>
                    </b-badge>
                    <b-tooltip
                        :target="`answer-${_uid}-${index}`"
                        :show.sync="tooltips.answers[index]"
                        triggers="manual"
                        placement="right"
                        :variant="tooltipVariant2"
                        :container="''._uid"
                    >
                        {{ $t("bs-copied") }}
                    </b-tooltip>
                </b-list-group-item>
            </div>
        </template>
        <!-- ====================================== FIM - QUESTION  ====================================== -->

        <!-- ====================================== TICKET DESCRIPTION  ====================================== -->
        <template>
            <b-list-group-item variant="dark" class="title pt-2 pb-2">
                {{$t('bs-description')}}
            </b-list-group-item>
            <b-list-group-item
                    class="pl-2 pt-1 pb-1 pr-2 d-flex justify-content-between align-items-center text-break"
                >
     
                    <div class="output ql-snow">
                        <div class="ql-editor" v-viewer v-html="chat.description != null ? chat.description : $t('bs-without-description')"></div>
                    </div>

                    <!-- <b-badge
                        class="cursor-pointer badge-copy"
                        variant="light"
                        pill
                        v-show="chat.description !== undefined"
                        @click="copyToClipboard(chat.description, 'description')"
                        :id="`description-${_uid}`"
                    >
                        <span class="bs-ico">&#xe14d;</span>
                    </b-badge>
                    <b-tooltip
                        :target="`description-${_uid}`"
                        :show.sync="tooltips.description"
                        triggers="manual"
                        placement="right"
                        :variant="tooltipVariant2"
                        :container="''._uid"
                    >
                        {{ $t("bs-copied") }}
                    </b-tooltip> -->
                </b-list-group-item>
            <!-- ====================================== FIM - TICKET DESCRIPTION   ====================================== -->

        </template>

        <b-list-group-item v-if="chat.client.builderall_account_data == null || JSON.parse(chat.client.builderall_account_data)['virtual_assistant'] == undefined ? false : true" variant="dark" class="title pt-2 pb-2">{{$t('bs-virtual-assistant')}}:</b-list-group-item>
        
        <b-list-group-item
            v-if="chat.client.builderall_account_data == null || JSON.parse(chat.client.builderall_account_data)['virtual_assistant'] == undefined ? false : true"
            class="pl-2 pt-1 pb-1 pr-2 d-flex justify-content-between align-items-center"
        >
            <span class="bs-ico ico-info">&#xe39f;</span>
            <span class="text-info-custom pl-2 pr-1">{{JSON.parse(chat.client.builderall_account_data)['virtual_assistant'].first_name}}</span>
            <b-badge
                class="cursor-pointer badge-copy"
                variant="light"
                pill
                v-show="chat.client.builderall_account_data !== undefined"
                @click="copyToClipboard($t(JSON.parse(chat.client.builderall_account_data)['virtual_assistant'].first_name), 'creatorName')"
                :id="`creator-name-${_uid}`"
            >
                <span class="bs-ico">&#xe14d;</span>
            </b-badge>
            <b-tooltip
                :target="`creator-name-${_uid}`"
                :show.sync="tooltips.creatorName"
                triggers="manual"
                placement="right"
                :variant="tooltipVariant2"
                :container="''._uid"
            >
                {{ $t("bs-copied") }}
            </b-tooltip>
        </b-list-group-item>

        <b-list-group-item variant="dark" class="title pt-2 pb-2">{{ $t("bs-chat-created-by") }}:</b-list-group-item>
        
        <!-- ====================================== NAME  ====================================== -->
        <b-list-group-item
            class="pl-2 pt-1 pb-1 pr-2 d-flex justify-content-between align-items-center"
        >
            <gravatar
                v-if="showData"
                :email="chat.client.email"
                :status="$status.get(chat.client.id)"
                size="20px"
                :name="$t(chat.client.name)"
                color="light"
                :ba_acct_data="chat.client.builderall_account_data"
            />
            <span class="text-info-custom pl-2 pr-1">{{ $t(chat.client.name) }}</span>
            <b-badge
                class="cursor-pointer badge-copy"
                variant="light"
                pill
                v-show="chat.client.name !== undefined"
                @click="copyToClipboard($t(chat.client.name), 'creatorName')"
                :id="`creator-name-${_uid}`"
            >
                <span class="bs-ico">&#xe14d;</span>
            </b-badge>
            <b-tooltip
                :target="`creator-name-${_uid}`"
                :show.sync="tooltips.creatorName"
                triggers="manual"
                placement="right"
                :variant="tooltipVariant2"
                :container="''._uid"
            >
                {{ $t("bs-copied") }}
            </b-tooltip>
        </b-list-group-item>
        <!-- ====================================== FIM - NAME  ====================================== -->

        <!-- ====================================== EMAIL  ====================================== -->
        <b-list-group-item
            class="pl-2 pt-1 pb-1 pr-2 d-flex justify-content-between align-items-center"
        >
            <span class="bs-ico ico-info">&#xe0be;</span>
            <span class="text-info-custom pl-2 pr-1">{{ chat.client.name == 'bs-user' ? '--' : chat.client.email }}</span>
            <b-badge
                class="cursor-pointer badge-copy"
                variant="light"
                pill
                v-show="chat.client.email !== undefined && chat.client.name !== 'bs-user'"
                @click="copyToClipboard(chat.client.email, 'creatorEmail')"
                :id="`creator-email-${_uid}`"
            >
                <span class="bs-ico">&#xe14d;</span>
            </b-badge>
            <b-tooltip
                :target="`creator-email-${_uid}`"
                :show.sync="tooltips.creatorEmail"
                triggers="manual"
                placement="right"
                :variant="tooltipVariant2"
                :container="''._uid"
            >
                {{ $t("bs-copied") }}
            </b-tooltip>
        </b-list-group-item>
        <!-- ====================================== FIM - EMAIL  ====================================== -->

        <!-- ====================================== DEPARTMENT  ====================================== -->
        <b-list-group-item
            class="pl-2 pt-1 pb-1 pr-2 d-flex justify-content-between align-items-center"
        >
            <span class="bs-ico ico-info">&#xf233;</span>
            <span class="text-info-custom pl-2 pr-1">{{ $t(chat.department) }}</span>
            <b-badge
                class="cursor-pointer badge-copy"
                variant="light"
                pill
                v-show="chat.department !== undefined"
                @click="copyToClipboard($t(chat.department), 'department')"
                :id="`department-${_uid}`"
            >
                <span class="bs-ico">&#xe14d;</span>
            </b-badge>
            <b-tooltip
                :target="`department-${_uid}`"
                :show.sync="tooltips.department"
                triggers="manual"
                placement="right"
                :variant="tooltipVariant2"
                :container="''._uid"
            >
                {{ $t("bs-copied") }}
            </b-tooltip>
        </b-list-group-item>
        <!-- ====================================== FIM - DEPARTMENT  ====================================== -->

        <!-- ====================================== OFFICE creatorAccountData  ====================================== -->
        <template v-if="chat.client.builderall_account_data && showData">

            <b-list-group-item variant="dark" class="title pt-2 pb-2">{{ $t('bs-builderall-office-data') }}:</b-list-group-item>
            
            <!-- ====================================== VIP  ====================================== -->
            <b-list-group-item
                v-if="JSON.parse(chat.client.builderall_account_data)['is_vip']"
                class="pl-2 pt-1 pb-1 pr-2 d-flex justify-content-between align-items-center"
            >
                <span class="bs-ico ico-info">&#xead5;</span>
                <span class="text-left w-100 pl-2">
                    <img src="images/icons/vip.svg" alt="vip" class="img_vip">
                </span>
            </b-list-group-item>
            <!-- ====================================== FIM - VIP  ====================================== -->

            <!-- ====================================== OFFICE ID  ====================================== -->
            <b-list-group-item
                class="pl-2 pt-1 pb-1 pr-2 d-flex justify-content-between align-items-center"
            >
                <span class="bs-ico ico-info">&#xe8a6;</span>
                <span class="text-info-custom pl-2 pr-1">{{ JSON.parse(chat.client.builderall_account_data)["id"] }}</span>
                <b-badge
                    class="cursor-pointer badge-copy"
                    variant="light"
                    pill
                    v-show="chat.client.email !== undefined"
                    @click="copyToClipboard(JSON.parse(chat.client.builderall_account_data)['id'], 'creatorAccountData')"
                    :id="`creator-builderall_account_data-${_uid}`"
                >
                    <span class="bs-ico">&#xe14d;</span>
                </b-badge>
                <b-tooltip
                    :target="`creator-builderall_account_data-${_uid}`"
                    :show.sync="tooltips.creatorAccountData"
                    triggers="manual"
                    placement="right"
                    :variant="tooltipVariant2"
                    :container="''._uid"
                >
                    {{ $t("bs-copied") }}
                </b-tooltip>
            </b-list-group-item>
            <!-- ====================================== FIM - OFFICE ID  ====================================== -->

            <!-- ====================================== OFFICE UUID  ====================================== -->
            <b-list-group-item
                class="pl-2 pt-1 pb-1 pr-2 d-flex justify-content-between align-items-center"
            >
                <span class="bs-ico ico-info">&#xe90d;</span>
                <span class="text-info-custom pl-2 pr-1">{{ JSON.parse(chat.client.builderall_account_data)["uuid"] }}</span>
                <b-badge
                    class="cursor-pointer badge-copy"
                    variant="light"
                    pill
                    v-show="chat.client.email !== undefined"
                    @click="copyToClipboard(JSON.parse(chat.client.builderall_account_data)['uuid'], 'creatorAccountData2')"
                    :id="`creator-builderall_account_data-2-${_uid}`"
                >
                    <span class="bs-ico">&#xe14d;</span>
                </b-badge>
                <b-tooltip
                    :target="`creator-builderall_account_data-2-${_uid}`"
                    :show.sync="tooltips.creatorAccountData2"
                    triggers="manual"
                    placement="right"
                    :variant="tooltipVariant2"
                    :container="''._uid"
                >
                    {{ $t("bs-copied") }}
                </b-tooltip>
            </b-list-group-item>
            <!-- ====================================== FIM - OFFICE UUID  ====================================== -->

        </template>
        <!-- ====================================== FIM - OFFICE creatorAccountData ====================================== -->

        <hr />

        <!-- ====================================== CREATED  ====================================== -->
        <b-list-group-item
            class="pl-2 pt-1 pb-1 pr-2 d-flex justify-content-between align-items-center"
        >
            <span class="bs-ico ico-info">&#xe8df;</span>
            <span class="text-info-custom pl-2 pr-1">{{ format_L(chat.created_at) }}</span>
            <b-badge
                class="cursor-pointer badge-copy"
                variant="light"
                pill
                v-show="chat.created_at !== undefined"
                @click="copyToClipboard(format_L(chat.created_at), 'creationDate')"
                :id="`creation-date-${_uid}`"
            >
                <span class="bs-ico">&#xe14d;</span>
            </b-badge>
            <b-tooltip
                :target="`creation-date-${_uid}`"
                :show.sync="tooltips.creationDate"
                triggers="manual"
                placement="right"
                :variant="tooltipVariant2"
                :container="''._uid"
            >
                {{ $t("bs-copied") }}
            </b-tooltip>
        </b-list-group-item>
        <!-- ====================================== FIM - CREATED  ====================================== -->

        <!-- ====================================== HOUR  ====================================== -->
        <b-list-group-item
            class="pl-2 pt-1 pb-1 pr-2 d-flex justify-content-between align-items-center"
        >
            <span class="bs-ico ico-info">&#xe8b5;</span>
            <span class="text-info-custom pl-2 pr-1">{{ format_LT(chat.created_at) }}</span>
            <b-badge
                class="cursor-pointer badge-copy"
                variant="light"
                pill
                v-show="chat.created_at !== undefined"
                @click="copyToClipboard(format_LT(chat.created_at), 'creationHour')"
                :id="`creation-hour-${_uid}`"
            >
                <span class="bs-ico">&#xe14d;</span>
            </b-badge>
            <b-tooltip
                :target="`creation-hour-${_uid}`"
                :show.sync="tooltips.creationHour"
                triggers="manual"
                placement="right"
                :variant="tooltipVariant2"
                :container="''._uid"
            >
                {{ $t("bs-copied") }}
            </b-tooltip>
        </b-list-group-item>
        <!-- ====================================== FIM - HOUR  ====================================== -->

        <!-- ====================================== BROWSER  ====================================== -->
        <b-list-group-item
            class="pl-2 pt-1 pb-1 pr-2 d-flex justify-content-between align-items-center"
        >
            <span class="bs-ico ico-info">&#xe894;</span>
            <span class="text-info-custom pl-2 pr-1">{{ chat.client.browser }}</span>
            <b-badge
                class="cursor-pointer badge-copy"
                variant="light"
                pill
                v-show="
                    chat.client.browser !== undefined &&
                    chat.client.browser !== null &&
                    chat.client.browser.trim() != ''
                "
                @click="copyToClipboard(chat.client.browser, 'userAgent')"
                :id="`user-agent-${_uid}`"
            >
                <span class="bs-ico">&#xe14d;</span>
            </b-badge>
            <b-tooltip
                :target="`user-agent-${_uid}`"
                :show.sync="tooltips.userAgent"
                triggers="manual"
                placement="right"
                :variant="tooltipVariant2"
                :container="''._uid"
            >
                {{ $t("bs-copied") }}
            </b-tooltip>
        </b-list-group-item>
        <b-list-group-item
            class="pt-1 d-flex justify-content-between align-items-center"
        >
            <b-button @click="exportChatTicket(chat)" size="sm" class="w-100" variant="success">
                {{ $t("bs-export") }} {{ chat.id ? $t("bs-ticket") : $t("bs-chat") }}
            </b-button>
        </b-list-group-item>

        <!-- ====================================== FIM - BROWSER  ====================================== -->

        <div class="hidden-input">
            <textarea :ref="`input-${_uid}`"></textarea>
        </div>

    </b-list-group>
</template>

<script>
export default {
    data() {
        return {
            tz: "",
            form: {
                titulo: this.$t("bs-initial-form"),
                answers: Array(0),
                loaded: false
            },
            tooltipVariant2: "info",
            tooltips: {
                idChat: false,
                answers: [],
                creatorName: false,
                creatorEmail: false,
                creatorAccountData: false,
                creatorAccountData2: false,
                department: false,
                creationDate: false,
                creationHour: false,
                userAgent: false,
                description: false,
                comments: false
            },
            showData: true,
            update: false,
            client_tickets_progress: Number(0),
            client_tickets_queue: Number(0),
            show_client_tickets: true,
            setFirstMerge: false,
        };
    },
    props: {
        chat: Object,
        showChat: Boolean,
        user: "",
        footerActiveChat: "",
        openClientHistory: "",
        openCategory: "",
        chat_admin: Number,
        chat_queue_full_control: Number,
        chats_on_queue: "",
        chats_resolved: "",
        chats_canceled: "",
        openChat: "",
        restriction: Object,
    },
    created() {
        this.$root.$refs.ChatInfo = this;
        this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
    },
    mounted() {
        this.getAnswers(this.chat.chat_id);
        if(!this.chat.id) {
            this.getNumberOfTicketsInProgress().then((show) => {
                this.show_client_tickets = show;
            });
        }
        localStorage.removeItem('merge_ticket');
    },
    methods: {
        exportChatTicket(selected){
            var vm = this;
            axios.post('generate-excel-chat-ticket',{
                chat_id: selected.chat_id,
                timeZone: this.tz,
            }).then(({data}) => {
                if(data.success) {
                    window.open(data.data, '_blank');
                }
            })
            .catch(err => {
                console.error(err);
            });
        },
        getNumberOfTicketsInProgress() {
            return new Promise((resolve, reject) => {
                var vm = this;
                vm.client_tickets_progress = Number(0);
                vm.client_tickets_queue = Number(0);
                vm.show_client_tickets = true;

                axios.get('tickets/counter-per-client', {
                    params: {
                        client_id: vm.chat.client_id,
                    }
                })
                .then(({data}) => {
                    if (data.success) {
                        vm.client_tickets_progress = Number(data.in_progress);
                        vm.client_tickets_queue = Number(data.queue);
                        resolve(data.show)
                    }
                })
            })
        },
        btnAction() {
            let item = this.chat;
            switch (item.status) {
                case "OPENED":
                    let i_queue = this.chats_on_queue.findIndex(
                        element => element.chat_id === item.chat_id
                    );

                    if (i_queue !== -1) {
                        // document.getElementById("btnChat").disabled = true;
                        this.$root.$refs.TableQueue.callCatchChat(
                            i_queue,
                            this.chats_on_queue[i_queue]
                        );
                    }

                    break;
                case "IN_PROGRESS":
                    let i_progress = this.$store.state.chats_in_progress.findIndex(
                        element => element.chat_id === item.chat_id
                    );

                    if (i_progress !== -1) {
                        this.$root.$refs.FullChat2.openChat(
                            this.$store.state.chats_in_progress[i_progress]
                        );
                    }

                    break;
                case "CLOSED":
                case "RESOLVED":
                    let i_resolved = this.chats_resolved.data.findIndex(
                        element => element.chat_id === item.chat_id
                    );

                    if (i_resolved !== -1) {
                        this.$root.$refs.TableResolved.callOpenChat(
                            this.chats_resolved.data[i_resolved]
                        );
                    }

                    break;
                case "CANCELED":
                    let i_canceled = this.chats_canceled.data.findIndex(
                        element => element.chat_id === item.chat_id
                    );

                    if (i_canceled !== -1) {
                        this.$root.$refs.TableCanceled.callOpenChat(
                            this.chats_canceled.data[i_canceled]
                        );
                    }

                    break;
            }

        },
        checkBtnDisabled() {
            let item = this.chat;
            if (item.status == "OPENED") {
                let i = this.chats_on_queue.findIndex(
                    element => element.chat_id === item.chat_id
                );

                if (item.dep_type == "builderall-mentor") {
                    return false;
                } else if (this.chat_admin) {
                    return false;
                } else if (this.chat_queue_full_control) {
                    return false;
                } else if (i !== 0 || this.chats_on_queue.current_page > 1) {
                    return true;
                }
            } else {
                return false;
            }
        },
        getAnswers(id) {
            return new Promise((resolve, reject) => {
                let vm = this;
                axios
                    .get("ticket-chat-answer/agent/get-ticket-chat-answers", {
                        params: {
                            id: id,
                            reference: "chat_id"
                        }
                    })
                    .then(response => {
                        if (response.data.status) {
                            vm.$set(vm.form, "answers", response.data.result);
                            vm.fillTooltipFlags();
                            vm.form.answers.loaded = true;
                            resolve();
                        }
                    });
            });
        },
        toUTC(h) {
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
                timeZone: this.tz
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
                .format("LT");
        },
        copyToClipboard: function(text, modalFlag, index = null) {
            const elem = this.$refs[`input-${this._uid}`];
            elem.value = text;
            elem.select();
            document.execCommand("copy");
            elem.value = "";
            this.openTooltip(modalFlag, index);
            elem.blur();
        },
        openTooltip(modalFlag, index = null) {
            if (index != null) {
                this.$set(this.tooltips[modalFlag], index, true);
            } else {
                this.tooltips[modalFlag] = true;
            }
            setTimeout(this.closeTootip, 2000, this, modalFlag, index);
        },
        closeTootip(vm, modalFlag, index = null) {
            if (index != null) {
                this.$set(vm.tooltips[modalFlag], index, false);
            } else {
                vm.tooltips[modalFlag] = false;
            }
        },
        fillTooltipFlags() {
            let len = this.form.answers.length;
            for (let i = 0; i < len; i++) {
                this.tooltips.answers.push(false);
            }
            //console.log(this.tooltips.answers);
        },
        getMergedTicket(itemselected, type){
            this.openClientHistory(itemselected.id, true)
        },
        mergeTicket(itemselected, type){

            if(localStorage.getItem("merge_ticket") == null){
                localStorage.setItem("merge_ticket", itemselected.id+"--"+itemselected.client.id+"--"+'false');
                this.setFirstMerge = true;
            }else{

                var ticket_merge = localStorage.getItem("merge_ticket").split("--")[0];
                var client_merge = localStorage.getItem("merge_ticket").split("--")[1];
                var invert = localStorage.getItem("merge_ticket").split("--")[2];

                var ticket_id = itemselected.id;
                var client_id = itemselected.client.id;

                // console.log(invert);

                if(invert == 'false'){
                    var textMerge = this.$t('bs-do-you-want-to-merge-the-ticket') +' '+ ticket_merge +' '+ this.$t('bs-with-the-ticket') +' '+ ticket_id
                    + '? '+ this.$t('bs-in-this-case-the-ticket') +' '+ ticket_merge+' '+ this.$t('bs-will-disappear')+'.';
                }else{
                    var textMerge = this.$t('bs-do-you-want-to-merge-the-ticket') +' '+ ticket_id +' '+ this.$t('bs-with-the-ticket') +' '+ ticket_merge
                    + '? '+ this.$t('bs-in-this-case-the-ticket') +' '+ ticket_id+' '+ this.$t('bs-will-disappear')+'.';
                }

                // VERIFICAR SE É O MESMO TICKET
                if(ticket_merge != ticket_id){
                    // VERIFICAR SE É O MESMO CLIENTE
                    if(client_merge == client_id){
                        Swal.fire({
                            title: this.$t('bs-are-you-sure'),
                            text: textMerge,
                            showDenyButton: true,
                            showCancelButton: true,
                            confirmButtonText: 'Sim, mesclar!',
                            denyButtonText: `Inverter`,
                            cancelButtonText: this.$t('bs-cancel'),
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                        width: 600,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                localStorage.removeItem('merge_ticket');
                                this.setFirstMerge = false;
                                axios.post('ticket-merge',{
                                    ticket_merge: ticket_merge,
                                    client_merge: client_merge,
                                    invert: invert,
                                    ticket_id: ticket_id,
                                    client_id: client_id,
                                })
                                .then(({data}) => {
                                    if(data.success) {
                                        this.$root.$refs.FullTicket2.getChatsOnQueue();
                                        Swal.fire(
                                            this.$t('Saved!'),
                                            this.$t('O ticket foi mesclado com sucesso!'),
                                            'success'
                                        );
                                        this.$root.$refs.FullTicket2.clearActiveChat();
                                    }
                                })
                                .catch(err => {
                                    console.error(err);
                                })
                            } else if (result.isDenied) {
                                if(invert == 'false'){
                                    localStorage.setItem("merge_ticket", ticket_merge+"--"+client_merge+"--"+'true');
                                }else{
                                    localStorage.setItem("merge_ticket", ticket_merge+"--"+client_merge+"--"+'false');
                                }
                                
                                this.mergeTicket(itemselected, type);
                            }else{
                                Swal.fire('Merge cancelado', '', 'error');
                                localStorage.removeItem('merge_ticket');
                                this.setFirstMerge = false;
                            }
                        })
                    }else{
                        localStorage.removeItem('merge_ticket');
                        this.setFirstMerge = false;
                        Swal.fire(
                            this.$t('bs-error'),
                            this.$t('bs-select-the-same-customer')+'!',
                            'error'
                        )
                    } 
                }else{
                    localStorage.removeItem('merge_ticket');
                    this.setFirstMerge = false;
                    Swal.fire(
                        this.$t('bs-error'),
                        this.$t('bs-select-different-tickets')+'!',
                        'error'
                    )
                } 
            }
        },
        deleteChatTicket(id, type) {
            var vm = this;
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
                if (type == 'CHAT') {
                    if (result.isConfirmed) {
                        axios.post('chat/delete',{
                            chat_id: id
                        })
                        .then(({data}) => {
                            this.$root.$refs.FullChat2.clearActiveChat();
                            if(data.success) {
                                Swal.fire(
                                    vm.$t('bs-deleted'),
                                    vm.$t('bs-the-chat-has-been-deleted'),
                                    'success'
                                )
                            }
                        })
                        .catch(err => {
                            console.error(err);
                        })
                    }
                }

                if (type == 'TICKET') {
                    if (result.isConfirmed) {
                        axios.post('ticket-delete',{
                            ticket_id: id
                        })
                        .then(({data}) => {
                            this.$root.$refs.FullTicket2.clearActiveChat();
                            if(data.success) {
                                Swal.fire(
                                    vm.$t('bs-deleted'),
                                    vm.$t('bs-the-ticket-has-been-deleted'),
                                    'success'
                                )
                            }
                        })
                        .catch(err => {
                            console.error(err);
                        })
                    }
                }
            });
        },
    },
    computed: {
        // watch the entire as a new object
        // por fazer referencia ao mesmo objeto
        // no watch deep newVal.chat_id era igual a oldVal.chat_id
        computedChat: function() {
            return JSON.parse(JSON.stringify(this.chat)); // copy object and remove reactivity
        },
        computedNumber: function() {
            return JSON.parse(JSON.stringify(this.chat.number)); // copy object and remove reactivity
        },
        btn_chat() {
            var status = this.chat.status;
            switch (status) {
                case "OPENED":
                    return this.$t("bs-take");
                    break;
                case "IN_PROGRESS":
                    if (this.chat.description) {
                        return this.$t("bs-open");
                    } else {
                        return this.$t("bs-chat");
                    }
                    break;
                case "CLOSED":
                case "RESOLVED":
                case "CANCELED":
                    return this.$t("bs-view");
                    break;
            }
        },
    },
    watch: {
        computedChat: {
            deep: true,
            handler: function(newVal, oldVal) {
                if (newVal.chat_id != oldVal.chat_id) {
                    if (newVal.show) {
                        this.getAnswers(newVal.chat_id);
                        if(!this.chat.id) {
                            this.getNumberOfTicketsInProgress().then((show) => {
                                this.show_client_tickets = show;
                            });
                        }
                    } else {
                        this.$set(this.form, "answers", []);
                        this.$set(this.tooltips, "answers", []);
                    }
                }
            }
        },
        computedNumber: {
            deep: true,
            handler: function(newVal, oldVal) {
                if(newVal != oldVal) {
                    this.showData = false;
                    this.form.answers.loaded = false;
                    setTimeout(() => {
                        this.showData = true;
                    }, 4);
                }
            }
        },
        form: {
            deep: true,
            handler: function(newVal, oldVal) {

            }
        },
    }
};
</script>

<style scoped lang="scss">
#info {
    // overflow: auto !important;
    height: calc(100% - 40px);
}

.list-group-item.title {
    border-top: 2px solid #dee2e6 !important;
    background-color: #F7F8FC !important;
    font-family: Muli;
    font-size: 0.9rem;
    line-height: 1.4;
    font-weight: 700;
}

.list-group-item {
    border-radius: 0px !important;
    border: none !important;
}

.badge-copy {
    min-height: 25px;
    max-height: 25px;
    min-width: 25px;
    max-width: 25px;
    border: 1px solid #A5B9D5;
}

.badge-copy .bs-ico {
    font-size: 13px !important;
    color: #A5B9D5 !important;
    position: relative;
    left: -2px;
    top: 2px;
}

.text-info-custom {
    width: 100%;
    text-align: left !important;
    font-family: Lato, Arial, sans-serif;
    color: #333333;
    font-weight: 600;
    font-size: 0.9rem;
    word-break: break-word;
}

.text-info-custom.answer {
    font-weight: 500;
}

button {
    font-family: Lato, Arial, sans-serif !important;
    font-weight: 600 !important;
    font-size: 0.9rem !important;
    padding-left: 2px !important;
    padding-right: 2px !important;
}

.ico-info {
   color: #A5B9D5 !important;
   font-size: 20px;
}

hr {
    width:100%;
    text-align:left;
    margin-left:0
}

.hidden-input {
  width: 1px;
  height: 1px;
  max-width: 1px;
  max-height: 1px;
  overflow: hidden;
  opacity: 0;
}

@media screen and (max-width: 992px) {
    .list-group-item.title, .text-info-custom, button {
        font-size: 16px;
    }

    .badge-copy {
        min-height: 35px;
        max-height: 35px;
        min-width: 35px;
        max-width: 35px;
    }

    .badge-copy .bs-ico {
        font-size: 16px !important;
        left: -1px;
        top: 6px;
    }
}
</style>
