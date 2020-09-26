<template>
  <v-snackbars :objects.sync="objects">
    <template v-slot:default="{ message: { icon, text, color } }">
      <v-row no-gutters align="center">
        <v-icon v-if="icon" :color="color" left>{{ icon }}</v-icon>
        <span style="white-space: pre;">{{ text }}</span>
      </v-row>
    </template>
    <template v-slot:action="{ close }">
      <v-btn text @click="close()">
        <v-icon small>mdi-close</v-icon>
      </v-btn>
    </template>
  </v-snackbars>
</template>

<script>
import VSnackbars from 'v-snackbars'
import delay from 'delay'

export default {
  components: { 'v-snackbars': VSnackbars },

  data: function () {
    return {
      objects: [],
      interval: 500,
      designes: {
        info: { color: 'info', icon: 'mdi-information', timeout: 10000 },
        success: { color: 'success', icon: 'mdi-check-circle', timeout: 10000 },
        error: { color: 'white', background: 'red darken-4', icon: 'mdi-alert-circle', timeout: 0 },
      },
    }
  },

  methods: {
    appendToast: async function (toasts) {
      if (!toasts) return
      const items = Array.isArray(toasts) ? toasts : [toasts]

      for (const item of items) {
        const message = item.message || '!!! no-text !!!'
        const design = this.designes[item.type] || {}

        this.objects.push({
          message: {
            icon: design.icon,
            color: design.color,
            text: message,
          },
          bottom: true,
          right: true,
          color: design.background || 'grey darken-4',
          timeout: design.timeout,
        })

        await delay(this.interval)
      }
    },
  },
}
</script>
