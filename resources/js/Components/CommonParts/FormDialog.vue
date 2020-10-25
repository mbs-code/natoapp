<template>
  <v-card>
    <v-card-title class="light-green lighten-5">
      <v-row no-gutters align="center">
        <v-icon v-if="icon" left>{{ icon }}</v-icon>
        {{ title }}
        <v-spacer />
        <v-btn icon @click="onCancel">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-row>
    </v-card-title>

    <v-divider />

    <v-card-text class="text--primary">
      <v-spacer />
      <component :is="formComponent" ref="form" :item="item" @success="close" />
    </v-card-text>

    <v-divider />

    <v-card-actions>
      <v-row no-gutters>
        <v-btn color="grey darken-1" text @click="onReset">リセット</v-btn>
        <v-spacer />
        <v-btn color="red darken-1" text @click="onCancel">キャンセル</v-btn>
        <v-btn color="green darken-1" outlined @click="onConfirm">保存</v-btn>
      </v-row>
    </v-card-actions>
  </v-card>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      default: 'ダイアログ',
    },
    icon: {
      type: String,
      default: 'mdi-pencil',
    },
    width: {
      type: [String, Number],
      default: 640,
    },

    formComponent: {
      type: Object,
      default: null,
    },
    item: {
      type: Object,
      default: null,
    },
  },

  created: function () {
    // dialog prop の上書き
    const parent = ((this.$parent || {}).$parent || {}).$parent || {}
    parent.showClose = false
    parent.waitForResult = false
    parent.width = this.width
  },

  methods: {
    onConfirm: function () {
      const form = this.$refs.form
      if (form) {
        form.submit()
      }
    },

    onReset: function () {
      const form = this.$refs.form
      if (form) {
        form.reset()
      }
    },

    onCancel: function () {
      this.$emit('submit', false)
    },

    close: function () {
      this.$emit('submit', true)
    },
  },
}
</script>
