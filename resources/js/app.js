/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * specific Vue component containers only. This prevents Vue from trying
 * to compile the entire page including script tags.
 */

// Only mount Vue if there are Vue component containers on the page
$(document).ready(function() {
    // Check if there are any Vue components to mount
    if ($('.vue-app').length > 0) {
        const app = new Vue({
            el: '.vue-app',
        });
        console.log('Vue.js mounted to .vue-app containers');
    } else {
        console.log('No Vue.js components found, skipping Vue mounting');
    }
});

