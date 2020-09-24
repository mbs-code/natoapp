import Vue from 'vue'
import Vuetify, { VSnackbar, VBtn, VIcon } from 'vuetify/lib'
import VuetifyToast from 'vuetify-toast-snackbar-ng'

Vue.use(Vuetify, {
  components: {
    VSnackbar,
    VBtn,
    VIcon,
  },
})

// ref: https://github.com/jaredhan418/vuetify-toast-snackbar-ng
// TODO: ng だと 色々と動いてないかも（event が発生してない？）
Vue.use(VuetifyToast, {
  x: 'right',
  y: 'top',
  timeout: 0, // 0: fixed
  color: 'grey darken-4',
  closeIcon: 'mdi-close',

  queueable: true,
  // multiLine: true,
  // vertical: true,
  shorts: {
    info: {
      // 5秒で消える, タッチで終了
      icon: 'mdi-information',
      textColor: 'white',
      iconColor: 'blue',
      timeout: 5000,
      dismissable: true,
      showClose: true,
    },

    success: {
      // 5秒で消える, タッチで終了
      icon: 'mdi-check-circle',
      textColor: 'white',
      iconColor: 'green',
      timeout: 5000,
      dismissable: true,
      showClose: true,
    },
    error: {
      // クリックして消える
      icon: 'mdi-alert',
      iconColor: 'red',
      timeout: 10000,
      dismissable: false,
      showClose: true,
    },
  },
})

const opts = {}

export default new Vuetify(opts)
