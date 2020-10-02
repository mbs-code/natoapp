import Vue from 'vue'
import Vuetify from 'vuetify/lib'
import VuetifyDialog from 'vuetify-dialog'

import 'vuetify/dist/vuetify.min.css'
import 'vuetify-dialog/dist/vuetify-dialog.css'

Vue.use(Vuetify)
const vuetify = new Vuetify({})

Vue.use(VuetifyDialog, {
  context: {
    vuetify,
  },
})

export default vuetify
