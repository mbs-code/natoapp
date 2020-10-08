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
    const twitter = this.item || {}
    const id = twitter.id

    return {
      form: this.$inertia.form(
        {
          _method: id ? 'PUT' : 'POST',
          _route: 'twitters',
          _params: { id },
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
}
</script>
