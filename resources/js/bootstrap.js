// Load essential libraries
window._ = require('lodash');

try {
    // Use modern Popper.js for Bootstrap 5 compatibility
    const { createPopper } = require('@popperjs/core');
    window.Popper = createPopper;
    window.$ = window.jQuery = require('jquery');

    // Load Bootstrap 5 JavaScript and expose to global scope
    const bootstrap = require('bootstrap');
    window.bootstrap = bootstrap;
} catch (e) {
    console.warn('Bootstrap initialization failed:', e);
}

/**
 * Configure Axios HTTP library for CSRF protection
 * Updated to latest version with improved security
 */
window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

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
//     forceTLS: true
// });
