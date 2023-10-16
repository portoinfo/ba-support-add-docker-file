window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');
console.log(window);
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';



/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */


import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

// aqui é a gambi
/*
window.Echo = new Echo({
    broadcaster: process.env.MIX_BROADCAST_DRIVER,
    encrypted: true,
    key: 'eyJpdiI6InBpNWJsVUtNZ0kvMEJDUFFaeldCWXc9PSIsInZhbHVlIjoiVENRbU82WkU2RjlNWGFaNmw1MWIzQT09IiwibWFjIjoiNDc3MTNkZWQ2NDYzMjA3NTYwMDMwN2NlMzVjNDFlMWI2NmVkODcwYjdlN2M2ZTk4NDMwOTFmNjRhYTliNDdjOCJ9',
    wsHost: process.env.MIX_PUSHER_APP_HOST,
    wsPort: process.env.MIX_PUSHER_APP_PORT,
    wssPort: process.env.MIX_PUSHER_APP_PORT,
    disableStats: true,
    forceTLS: true,
    enabledTransports: ["ws", "wss"],
});
*/
// aqui é o certo


if (process.env.MIX_PUSHER_APP_HOST) {
    console.log('eae');
    window.Echo = new Echo({
        broadcaster: process.env.MIX_BROADCAST_DRIVER,
        encrypted: true,
        key: 'eyJpdiI6InBpNWJsVUtNZ0kvMEJDUFFaeldCWXc9PSIsInZhbHVlIjoiVENRbU82WkU2RjlNWGFaNmw1MWIzQT09IiwibWFjIjoiNDc3MTNkZWQ2NDYzMjA3NTYwMDMwN2NlMzVjNDFlMWI2NmVkODcwYjdlN2M2ZTk4NDMwOTFmNjRhYTliNDdjOCJ9',
        wsHost: process.env.MIX_PUSHER_APP_HOST,
        wsPort: process.env.MIX_PUSHER_APP_PORT,
        wssPort: process.env.MIX_PUSHER_APP_PORT,
        disableStats: true,
        forceTLS: true,
        enabledTransports: ["ws", "wss"],
    });
} else {
    // LOCALHOST //
    window.Echo = new Echo({
        broadcaster: process.env.MIX_BROADCAST_DRIVER,
        encrypted: true,
        key: 'eyJpdiI6InBpNWJsVUtNZ0kvMEJDUFFaeldCWXc9PSIsInZhbHVlIjoiVENRbU82WkU2RjlNWGFaNmw1MWIzQT09IiwibWFjIjoiNDc3MTNkZWQ2NDYzMjA3NTYwMDMwN2NlMzVjNDFlMWI2NmVkODcwYjdlN2M2ZTk4NDMwOTFmNjRhYTliNDdjOCJ9',
        wsHost: window.location.hostname,
        wsPort: process.env.MIX_PUSHER_APP_PORT,
        disableStats: true,
        forceTLS: false,
    });
}