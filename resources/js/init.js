/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

// require('./bootstrap');

// window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

// Vue.component('RelacionLocal', require('./components/Local/RelacionTienda.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// const app = new Vue({
//     el: '#app',
// });

import "vue-form-wizard/dist/vue-form-wizard.min.css";

import Vue from "vue";

// import underscore from 'vue-underscore'

import vSelect from "vue-select";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import Element from "element-ui";
import VueFormWizard from "vue-form-wizard/src";
import { misMixins } from "./mixins/mixin.js";
import locale from "element-ui/lib/locale/lang/es";
import VueBootstrapTypeahead from "vue-bootstrap-typeahead";
import VueTypeaheadBootstrap from "vue-typeahead-bootstrap";

// Global registration
// Vue.use(underscore)
Vue.component("vue-bootstrap-typeahead", VueBootstrapTypeahead);
Vue.component("vue-typeahead-bootstrap", VueTypeaheadBootstrap);

Vue.component("v-select", vSelect);

// Vue.component('modal-permisos', mPermisos)

// VALIDACION DE RUTAS
router.beforeEach(async (to, from, next) => {
    if (to.matched.some((record) => record.meta.isUrlValid)) {
        store.dispatch("validarURL", to.fullPath);
        await store.dispatch("accessSession");
        // console.log("isValidURL", store.getters.isValidURL);
        // console.log("isSessionActive", store.getters.isSessionActive);
        // console.log("to", to.fullPath);
        if (store.getters.isValidURL && store.getters.isSessionActive) {
            if (to.fullPath != "/#") {
                if (to.fullPath == "/") {
                    next({ name: "inicio" });
                } else {
                    next();
                }
            }
        } else {
            if (
                to.fullPath != "/" &&
                !store.getters.isSessionActive &&
                store.getters.isValidURL
            ) {
                next({ name: "login" });
            } else if (
                to.fullPath != "/" &&
                store.getters.isSessionActive &&
                !store.getters.isValidURL
            ) {
                next({ name: "error500" });
            } else {
                next();
            }
        }
    } else {
        next();
    }
});

Vue.directive("focus-on-create", {
    inserted: function (el) {
        // console.log(el)
        el.focus();
    },
    // ,
    // update: function(el, binding) {
    //     console.log('2', el)
    //     // var value = binding.value
    //     // console.log(el +','+value)
    //     // if (value) {
    //         // Vue.nextTick(function() {
    //         el.focus()
    //         // })
    //     }
    // }
});

Vue.directive("focus", {
    inserted: function (el) {
        el.focus();
    },
    update: function (el) {
        Vue.nextTick(function () {
            console.log(el);
            el.focus();
        });
    },
});

Vue.use(Element, { locale });
Vue.use(VueFormWizard);

new Vue({
    // el: "#app",
    router,
    mixins: [misMixins],
    store,
    render: (h) => h(App),
    // components: {App},
    // template: "<App/>"
}).$mount("#app");
