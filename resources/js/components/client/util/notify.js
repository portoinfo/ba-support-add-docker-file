import store from '../vue/client';
import { router } from "../../../client-app";
import { i18n } from "../../../client-app.js";
export const notify = function(options = {}) {
    var iconHtml = undefined;
    var iconColor = undefined;
    if (options.icon) {
        switch (options.icon) {
            case 'chat':    
                iconHtml  = '<?xml version="1.0" encoding="UTF-8"?> <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="40px" height="40px" viewBox="0 0 40 40" version="1.1"> <g id="surface1"> <path style=" stroke:none;fill-rule:nonzero;fill:rgb(23.137255%,50.980392%,96.470588%);fill-opacity:1;" d="M 19.296875 5.515625 C 15.457031 5.515625 11.898438 7.015625 9.273438 9.738281 C 6.757812 12.347656 5.316406 15.898438 5.316406 19.488281 C 5.316406 21.765625 5.96875 24.179688 7.207031 26.46875 C 7.753906 27.398438 7.855469 28.53125 7.496094 29.605469 L 7.007812 31.238281 L 8.351562 30.839844 C 8.757812 30.710938 9.175781 30.644531 9.589844 30.644531 C 10.832031 30.644531 11.800781 31.230469 12.449219 31.625 C 14.355469 32.746094 16.902344 33.414062 19.265625 33.414062 C 22.761719 33.414062 26.257812 32.042969 28.855469 29.652344 C 30.84375 27.820312 33.214844 24.558594 33.214844 19.441406 C 33.214844 11.761719 26.972656 5.515625 19.296875 5.515625 M 19.296875 3.410156 C 28.417969 3.410156 35.320312 10.886719 35.320312 19.441406 C 35.320312 29.363281 27.230469 35.519531 19.265625 35.519531 C 16.632812 35.519531 13.710938 34.8125 11.367188 33.429688 C 10.546875 32.929688 9.859375 32.5625 8.972656 32.851562 L 5.730469 33.816406 C 4.914062 34.074219 4.175781 33.429688 4.414062 32.5625 L 5.492188 28.960938 C 5.667969 28.460938 5.636719 27.929688 5.378906 27.511719 C 3.996094 24.972656 3.210938 22.191406 3.210938 19.488281 C 3.210938 11.03125 9.96875 3.410156 19.296875 3.410156 Z M 19.296875 3.410156 "/> </g> </svg>';
                iconColor = 'transparent';
                delete options.icon;
                break;
        
            default:
                break;
        }
    }

    if ('sound' in options && options.sound) {
        if ('soundName' in options) {
            switch (options.soundName) {
                case 'add':
                    var add_audio = new Audio(`${store.state.baseURL}/media/add.mp3`);
                    add_audio.play();
                    break;
    
                case 'remove':
                    var remove_audio = new Audio(`${store.state.baseURL}/media/remove.mp3`);
                    remove_audio.play();
                    break;
            
                default:
                    break;
            }
        }
    }

    if ('goToChat' in options && options.goToChat) {
        options.showConfirmButton = true;
        options.confirmButtonText = i18n.t('bs-view')
        var is_chat = true;
    }
    
    var swal = {
        iconHtml,
        iconColor,
        toast: true,
        backdrop: false,
        timer: 'timer' in options ? options.timer : '5000',
        showConfirmButton: 'showConfirmButton' in options ? options.showConfirmButton : false,
        position: 'position' in options ? options.position : 'top',
        showCloseButton: true,
        ...options
    };

    Vue.swal.fire(swal).then((result) => {
        if (result.isConfirmed) {
            if (is_chat) {
                var chat_opened = router.currentRoute.name == 'chat-opened' && router.currentRoute.params.id == options.chatId;
                if (!chat_opened) {
                    router.push({name: 'chat-opened', params: {'id': options.chatId}})
                }
            }
        }
    })
}