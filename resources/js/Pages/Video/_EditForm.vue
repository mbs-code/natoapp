<template>
  <v-form ref="form" @submit.prevent="submit">
    <v-text-field
      v-model="form.code"
      label="動画ID"
      required
      counter="255"
      :error-messages="errors.code"
    />
  </v-form>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'

export default {
  props: {
    item: {
      type: Object,
      default: () => {},
    },
  },

  data: function () {
    const video = this.item || {}
    const id = video.id

    return {
      form: this.$inertia.form(
        {
          _method: id ? 'PUT' : 'POST',
          code: video.code,
        },
        {
          bag: 'submit',
          resetOnSuccess: false,
        }
      ),
      errors: {},
    }
  },

  methods: {
    submit: function () {
      const video = this.item || {}
      const id = video.id

      const url = id ? this.route('videos.update', { id }) : this.route('videos.store')
      this.form
        .post(url, {
          preserveScroll: true,
        })
        .then(() => {
          // 取り出したらエラーを消す
          this.errors = this.$page.errors
          this.$page.errors = {}

          if (Object.keys(this.errors).length === 0) {
            this.$emit('success')
          } else {
            this.$emit('error')
          }
        })
    },

    reset: function () {
      this.form.reset()
      this.errors = {}
    },
  },
}
</script>
