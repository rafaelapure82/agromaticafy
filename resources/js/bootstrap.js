import _ from 'lodash';
window._ = _;

import Swal from 'sweetalert2';
window.Swal = Swal;

// jQuery is now loaded via script tag in the layout to ensure global availability for AdminLTE
// import jQuery from 'jquery';
// window.$ = window.jQuery = jQuery;

import 'bootstrap';
import 'admin-lte';



import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
window.axios.defaults.withCredentials = true;
window.axios.defaults.withXSRFToken = true;



/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });
