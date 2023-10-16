<template>
    <span>
        <!-- {{ active }} -->
        <img v-if="name == 'logo-head-icon'" src="/images/layout6/logo-head.svg" :width="width"/>

        <img v-else-if="name == 'dashboard-icon'" 
        :src="active == true ? '/images/layout6/dashboard-icon-blue.svg' : '/images/layout6/dashboard-icon.svg' " width="26px"/>

        <img v-else-if="name == 'chat-icon'"         
        :src="active == true ? '/images/layout6/chat-blue.svg' : '/images/layout6/chat.svg'" width="26px"/>

        <img v-else-if="name == 'ticket-icon'" 
        :src="active == true ? '/images/layout6/ticket-blue.svg' : '/images/layout6/ticket.svg'" :width="width"/>

        <img v-else-if="name == 'archive-icon'" 
        :src="active == true ? '/images/layout6/archive-blue.svg' : '/images/layout6/archive.svg'" width="26px"/>
        
        <img v-else-if="name == 'filter-icon'" 
        :src="active == true ? '/images/layout6/categories-blue.svg' : '/images/layout6/categories.svg'" :width="width"/>

        <img v-else-if="name == 'settings-icon'" 
        :src="active == true ? '/images/layout6/settings-blue.svg' : '/images/layout6/settings.svg'" width="26px"/>

        <img v-else-if="name == 'select-company-icon'" 
        :src="active == true ? '/images/layout6/change-company-blue.svg' : '/images/layout6/change-company.svg'" width="26px"/>

        <div v-else-if="name == 'activity-1-icon'" class="join-images">
            <img src="/images/layout6/circle.svg" class="fix-circle" style="width: 100%; height: 100%;" />
            <img src="/images/layout6/user.svg" class="centralize-image" style="width: 50%; height: 50%;" />
        </div>

        <div v-else-if="name == 'activity-2-icon'" class="join-images">
            <img src="/images/layout6/circle.svg" class="fix-circle" style="width: 100%; height: 100%;" />
            <img src="/images/layout6/chat-bubble.svg" class="centralize-image" style="width: 50%; height: 50%;" />
        </div>

        <div v-else-if="name == 'activity-3-icon'" class="join-images">
            <img src="/images/layout6/circle.svg" class="fix-circle" style="width: 100%; height: 100%;" />
            <img src="/images/layout6/multiple-users.svg" class="centralize-image" style="width: 50%; height: 50%;" />
        </div>

        <div v-else-if="name == 'activity-4-icon'" class="join-images">
            <img src="/images/layout6/circle.svg" class="fix-circle" style="width: 100%; height: 100%;" />
            <img src="/images/layout6/call-service.svg" class="centralize-image" style="width: 50%; height: 50%;" />
        </div>

        <div v-else-if="name == 'activity-5-icon'" class="join-images">
            <img src="/images/layout6/circle.svg" class="fix-circle" style="width: 100%; height: 100%;" />
            <img src="/images/layout6/headphones.svg" class="centralize-image" style="width: 50%; height: 50%;" />
        </div>
        
        <div v-else-if="name == 'memberbytes-1-icon'" class="join-images">
            <img src="/images/layout6/circle.svg" class="fix-circle" style="width: 100%; height: 100%;" />
            <img src="/images/layout6/filled-message.svg" class="centralize-image" style="width: 50%; height: 50%;" />
        </div>

        <div v-else-if="name == 'memberbytes-2-icon'" class="join-images">
            <img src="/images/layout6/circle.svg" class="fix-circle" style="width: 100%; height: 100%;" />
            <img src="/images/layout6/group.svg" class="centralize-image" style="width: 50%; height: 50%;" />
        </div>

        <div v-else-if="name == 'memberbytes-3-icon'" class="join-images">
            <img src="/images/layout6/circle.svg" class="fix-circle" style="width: 100%; height: 100%;" />
            <img src="/images/layout6/online-support-gray.svg" class="centralize-image" style="width: 50%; height: 50%;" />
        </div>
        
        <img v-else-if="name == 'help-icon'" src="/images/layout6/help.svg" width="26px"/>

        <img v-else-if="name == 'online-support-icon'" src="/images/layout6/online-support.svg" width="26px"/>

        <img v-else-if="name == 'online-support-gray-icon'" 
        :src="active == true ? '/images/layout6/online-support.svg' : '/images/layout6/online-support-gray.svg'" width="26px"/>
        
        <img v-else-if="name == 'menu-button-lines'" src="/images/layout6/util/menu-button-lines.svg" width="26px"/>
        
        <img v-else-if="name == 'down-icon'" 
        :src='active == true ? "/images/layout6/util/down.svg" : "/images/layout6/util/down-gray.svg"' :width="width"/>
        
        <img v-else-if="name == 'close-icon'" src='/images/layout6/util/close.svg' :width="width"/>

        <img v-else-if="name == 'calendar-icon'" src='/images/layout6/calendar.svg' :width="width"/>

        <img v-else-if="name == 'hour-icon'" src='/images/layout6/hour.svg' :width="width"/>

        <img v-else-if="name == 'global-info-icon'" src='/images/layout6/global-info.svg' :width="width"/>

        <img v-else-if="name == 'send-icon'" src='/images/layout6/send.svg' :width="width"/>
        
        <img v-else-if="name == 'take-icon'" src='/images/layout6/take.svg' :width="width"/>

        <img v-else-if="name == 'view-icon'" src='/images/layout6/view.svg' :width="width"/>

        <img v-else-if="name == 'edit-icon'" src='/images/layout6/edit.svg' :width="width"/>
        
        <img v-else-if="name == 'ellipsis-icon'" src='/images/layout6/ellipsis.svg' :width="width"/>
        
    </span>
</template>
  
<script>
    export default {
        props: {
            name: String,
            active: Boolean,
            width: {
                type: String,
                default: '16'
            },
            height: {
                type: String,
                default: '16'
            },
            fill: {
                type: String,
                default: 'currentColor'
            },
            className: {
                type: String,
                default: ''
            },
            viewBox: {
                type: String,
                default: '0 0 16 16'
            },
        }
    };
</script>

<style scoped>
    .customteste{
        border: red 1 px solid;
    }
    .join-images{
        position: relative; 
        width: 30px; height: 30px;
    }
    .centralize-image{
        position: absolute; 
        top: 50%; 
        left: 50%; 
        transform: translate(-50%, -50%);
    }
    .fix-circle{
        position: absolute; 
        top: 0; 
        left: 0;
    }

</style>