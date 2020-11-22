require('dotenv').config()
import Vue from 'vue';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'

Vue.use(BootstrapVue)
Vue.use(IconsPlugin)
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
// import VueSession from 'vue-session'
// import VueToast from 'vue-toast-notification'
// import 'vue-toast-notification/dist/theme-default.css';
// import GLightbox from 'glightbox'
// import 'glightbox/dist/css/glightbox.min.css'
// import InfiniteLoading from 'vue-infinite-loading';

window.axios = require('axios')
window.axios.defaults.headers.common['X-API_KEY-Header'] = process.env.API_KEY;
// window.GLightbox = GLightbox

// Vue.use(VueSession, {persist: true})
// Vue.use(require('vue-cookies'))
// Vue.use(InfiniteLoading)
// Vue.use(VueToast);

const files = require.context('./vue', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

window.Vue = Vue

if(document.getElementById("v-navbar")){
    new Vue({ }).$mount('#v-navbar')
}

// result page
if(document.getElementById("v-result")){
    new Vue({ }).$mount('#v-result')
}