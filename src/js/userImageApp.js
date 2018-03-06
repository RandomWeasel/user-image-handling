exports = {
    /**
     * First we will load all of this project's JavaScript dependencies which
     * includes Vue and other libraries. It is a great starting point when
     * building robust, powerful web applications using Vue and Laravel.
     */

//require('./bootstrap');

    require('./interactions'
)
;

//window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
//
//Vue.component('example', require('./components/Example.vue'));
Vue.component('image-editor', require('./image-editor.vue'));
Vue.component('field-errors', require('./field-errors.vue'));
Vue.component('image-display', require('./image-display.vue'));
Vue.component('image-rotation', require('./image-rotation.vue'));

////vue components
//require('./components/characterLimit.js');

Vue.prototype.$fetchHeaders = {
    "Content-Type": "application/json",
    "X-Request-With": "XMLHttpRequest",
    "X-CSRF-TOKEN": document.head.querySelector('meta[name="csrf-token"]').content,
    "Accept": "application/json"
}

window.bus = new Vue();

//var app = new Vue({
//    el: '#app',
//
//    mounted: function(){
//        console.log('main vue mounted');
//        //console.log(this.$fetchHeaders);
//    },
//
//    methods: {
//
//    }
//});


////enable use of promises in older browsers
//import Promise from 'promise-polyfill';
//
//// To add to window
//if (!window.Promise) {
//    window.Promise = Promise;
//}

}
