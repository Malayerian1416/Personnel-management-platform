import _ from 'lodash';
window._ = _;
window.$ = window.jQuery = require('jquery');
window.Popper = require('@popperjs/core');
window.bootstrap = require('bootstrap');
import Inputmask from 'inputmask';
window.Inputmask = Inputmask;
window.resizableSafe = require('jquery-resizable-dom');
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
let token = document.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

Pusher.logToConsole = false;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true,
    encrypted: false,
    channelAuthorization: {
        endpoint: "/broadcasting/auth",
        headers: { "X-CSRF-Token": token },
    }
});
