<template>
  <v-form ref="form" @submit.prevent="submit">
    <v-text-field
      v-model="form.code"
      label="チャンネルID"
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
    const youtube = this.item || {}
    const id = youtube.id

    return {
      form: this.$inertia.form(
        {
          _method: id ? 'PUT' : 'POST',
          _route: 'youtubes',
          _params: { id },
          code: youtube.code,
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
