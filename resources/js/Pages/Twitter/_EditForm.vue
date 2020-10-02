<template>
  <v-form ref="form" @submit.prevent="submit">
    <v-text-field
      v-model="form.screen_name"
      label="@xxxxx"
      required
      counter="255"
      :error-messages="errors.screen_name"
    />
  </v-form>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'
import VImageSelect from '@/Components/Forms/VImageSelect'
import VTagCombobox from '@/Components/forms/VTagCombobox'

export default {
  props: {
    item: {
      type: Object,
      default: () => {},
    },
  },

  data: function () {
    const twitter = this.item || {}
    const id = twitter.id

    return {
      form: this.$inertia.form(
        {
          _method: id ? 'PUT' : 'POST',
          screen_name: twitter.screen_name,
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
      const twitter = this.item || {}
      const id = twitter.id

      const url = id ? this.route('twitters.update', { id }) : this.route('twitters.store')
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
