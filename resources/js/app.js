/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
import Vue from 'vue'
import Vuetify from 'vuetify';
import App from './components/App'
import VueRouter from 'vue-router'
import routes from "./router";
import * as VueGoogleMaps from 'vue2-google-maps'
import axios from 'axios'
import store from './store'

Vue.use(VueRouter)
Vue.use(Vuetify);

const router = new VueRouter({
    mode: 'history',
    routes
})

const vuetify = new Vuetify({
    theme: {
        themes: {
            light: {
                primary: '#3f51b5',
                secondary: '#b0bec5',
                accent: '#8c9eff',
                error: '#b71c1c',
            },
        },
    },
})
Vue.use(VueGoogleMaps, {
    load: {
        key: 'AIzaSyCz-HjH5RdNLUDtHj2QBXp-NmlQYbLx5rQ',
        libraries: 'places',
    },
    //// If you intend to programmatically custom event listener code
    //// (e.g. `this.$refs.gmap.$on('zoom_changed', someFunc)`)
    //// instead of going through Vue templates (e.g. `<GmapMap @zoom_changed="someFunc">`)
    //// you might need to turn this on.
    // autobindAllEvents: false,

    //// If you want to manually install components, e.g.
    //// import {GmapMarker} from 'vue2-google-maps/src/components/marker'
    //// Vue.component('GmapMarker', GmapMarker)
    //// then set installComponents to 'false'.
    //// If you want to automatically install all the components this property must be set to 'true':
    installComponents: true
})

const axiosConfig = {
    baseURL: '/api/',
    timeout: 30000,
};

Vue.prototype.$axios = axios.create(axiosConfig)

export default new Vue({
    el: '#app',
    render: h => h(App),
    router,
    vuetify,
    store
});
