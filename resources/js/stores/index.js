import Vue from 'vue'
import Vuex from 'vuex'
import createLogger from 'vuex/dist/logger'

import createPersistedState from 'vuex-persistedstate'
import VuexReset from '@ianwalter/vuex-reset'

import config from './config'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: { config },

  mutations: {
    clear: () => {
      localStorage.clear()
    },
  },

  plugins: [
    createLogger(),
    createPersistedState(),
    VuexReset({ trigger: 'clear' }), // when clear() is executed
  ],
})
