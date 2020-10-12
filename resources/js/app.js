require('./bootstrap')

import Vue from 'vue'

import { InertiaApp } from '@inertiajs/inertia-vue'
import { InertiaForm } from 'laravel-jetstream'
import PortalVue from 'portal-vue'

import store from './stores'

import vuetify from './plugins/vuetify'
import axios from './plugins/axios' // loading

Vue.use(InertiaApp)
Vue.use(InertiaForm)
Vue.use(PortalVue)

// inject ziggy
Vue.mixin({ methods: { route: window.route } })

const app = document.getElementById('app')

new Vue({
  store,
  vuetify,
  render: (h) =>
    h(InertiaApp, {
      props: {
        initialPage: JSON.parse(app.dataset.page),
        resolveComponent: (name) => require(`./Pages/${name}`).default,
      },
    }),
}).$mount(app)
