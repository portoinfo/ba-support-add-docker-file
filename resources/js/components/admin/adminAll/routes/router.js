// Components
import DashboardHelpDesk      from "../view/dashboard/dashboardHelpdesk.vue"
import ChatHome       from "../view/service/chat/chat.vue"
import TicketHome       from "../view/service/ticket/ticket.vue"
import SelectCompany     from "../view/company/selectCompany.vue"
// import ClientTicketHome     from '../tickets/ClientTicketHome.vue'
// import ClientTicketOpened   from '../tickets/ClientTicketOpened.vue'
// import ClientLogout         from '../auth/ClientLogout.vue'


// Guards
// import { guardTicketRoute }     from './guard/ticket-guard';
// import { guardChatRoute }       from './guard/chat-guard';
// import { guardAnonymousAccess } from './guard/anonymous-access-guard';
// import { guardPopup }           from './guard/popup-guard';

// Routes
const routes = [
    {
        path:'/new',
        name:'dashboard',
        component: DashboardHelpDesk,
    },
    {
        path:'/new/dashboard',
        name:'dashboard',
        component: DashboardHelpDesk,
    },
    {
        path:'/new/chat',
        name:'chat',
        component: ChatHome,
    },
    {
        path:'/new/ticket',
        name:'chat',
        component: TicketHome,
    },
    {
        path:'/new/select-company',
        name:'chat',
        component: SelectCompany,
    },
];

export default routes;
