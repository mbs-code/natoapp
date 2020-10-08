<template>
  <v-form ref="form" @submit.prevent="submit('videos')">
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
import IntierFormMixin from '@/Mixins/InertiaFormMixin'
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'

export default {
  mixins: [IntierFormMixin],

  props: {
    item: {
      type: Object,
      default: () => {},
    },
  },

  data: function () {
    const video = this.item || {}

    return {
      form: this.$inertia.form(
        {
          _method: id ? 'PUT' : 'POST',
          _route: 'videos',
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
}
</script>
