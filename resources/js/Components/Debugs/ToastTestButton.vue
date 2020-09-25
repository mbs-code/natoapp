<template>
  <v-btn @click="onTestToast">Toast テスト</v-btn>
</template>

<script>
export default {
  data: function () {
    return {
      samples: [
        { type: 'info', message: '通常メッセージです。' },
        { type: 'success', message: '成功メッセージです。' },
        { type: 'error', message: 'エラーメッセージです。' },
        // { type: 'info', message: '改行テストです。\n' + new Date().toLocaleString() },
        // TODO: 改行はレイアウトに失敗する模様
      ],
    }
  },

  methods: {
    onTestToast: function () {
      const samples = this.samples
      const test = samples[Math.floor(Math.random() * samples.length)]
      this.$inertia.post(this.route('debug.toast'), test, { preserveScroll: true })
    },
  },
}
</script>
