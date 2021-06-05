/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

// bootstrap-vue
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
Vue.use(BootstrapVue)
Vue.use(IconsPlugin)
import 'bootstrap-vue/dist/bootstrap-vue.css'

import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'

Vue.use(ElementUI)

import lang from 'element-ui/lib/locale/lang/en'
import locale from 'element-ui/lib/locale'
locale.use(lang)

import { DataTables, DataTablesServer  } from 'vue-data-tables'
Vue.use(DataTables)
Vue.use(DataTablesServer)

require('jquery-powertip')
import 'jquery-powertip/dist/css/jquery.powertip.min.css'


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context('./components/', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    data() {
        return {
            name: 'BootstrapVue',
            isDropdown2Visible: false
        }
    },
    mounted: function () {
        this.$root.$on('bv::dropdown::show', bvEvent => {
            if(bvEvent.componentId === 'dropdown-2') {
                this.isDropdown2Visible = true;
            }
        })
        this.$root.$on('bv::dropdown::hide', bvEvent => {
            if(bvEvent.componentId === 'dropdown-2') {
                this.isDropdown2Visible = false;
            }
            if(bvEvent.componentId === 'dropdown-3') {
                this.isDropdown2Visible = false;
            }
            if(this.isDropdown2Visible) {
                bvEvent.preventDefault()
            }
        })
    }
});
