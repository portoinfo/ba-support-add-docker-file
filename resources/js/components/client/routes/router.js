// Components
import ClientDashboard      from '../home/ClientDashboard.vue'
import ClientChatHome       from '../chats/ClientChatHome.vue'
import ClientChatOpened     from '../chats/ClientChatOpened.vue'
import ClientTicketHome     from '../tickets/ClientTicketHome.vue'
import ClientTicketOpened   from '../tickets/ClientTicketOpened.vue'
import ClientLogout         from '../auth/ClientLogout.vue'

// Guards
import { guardTicketRoute }     from './guard/ticket-guard';
import { guardChatRoute }       from './guard/chat-guard';
import { guardAnonymousAccess } from './guard/anonymous-access-guard';
import { guardPopup }           from './guard/popup-guard';

// Routes
const routes = [
    {
        path:'/customer-home',
        name:'home',
        component: ClientDashboard,
        beforeEnter: (to, from, next) => {
            guardAnonymousAccess(to, from, next);
            guardPopup(to, from, next);
        }
    },
    {
        path:'/customer-chat',
        name:'chat',
        component: ClientChatHome,
        beforeEnter : guardChatRoute,
    },
    {
        path:'/customer-chat/:id',
        name:'chat-opened',
        component: ClientChatOpened,
        beforeEnter : guardChatRoute,
    },
    {
        path:'/customer-ticket',
        name:'ticket',
        component: ClientTicketHome,
        beforeEnter : guardTicketRoute,
    },
    {
        path:'/customer-ticket/:id',
        name:'ticket-opened',
        component: ClientTicketOpened,
        beforeEnter : guardTicketRoute,
    },
    {
        path:'/logout',
        name:'logout',
        component: ClientLogout
    },
];

export default routes;
