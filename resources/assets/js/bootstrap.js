
window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

window.$ = window.jQuery = require('jquery');

require('bootstrap-sass');

/**
 * Vue is a modern JavaScript library for building interactive web interfaces
 * using reactive data binding and reusable components. Vue's API is clean
 * and simple, leaving you to focus on building your next great project.
 */

//window.Vue = require('vue');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.toastr = require('toastr');
window.WOW = require('wow.js');
window.selectize = require('selectize');
window.datetimepicker = require('eonasdan-bootstrap-datetimepicker');
window.knob = require('jquery-knob');
window.GoogleMapsLoader = require('google-maps');
window.echarts = require('echarts');

require('./components/config');
require('./components/dauphin');
require('./components/main');
require('./components/employers');
require('./components/rates');
require('./components/addresses');
require('./components/meters_registration');
require('./components/meters_structure');
require('./components/meter_instructions');
require('./components/move_meter');
require('./components/monitoring');
require('./components/consumption_report');
require('./components/other_meters');
require('./components/select_childs');
require('./components/map');

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from "laravel-echo"

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });
