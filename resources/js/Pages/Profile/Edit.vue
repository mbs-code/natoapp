<template>
  <v-form @submit.prevent="onSubmit">
    <v-text-field
      v-model="form.name"
      :counter="10"
      label="name"
      required
    />

    <v-btn type="submit">submit</v-btn>
  </v-form>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'

export default {
  layout: [DefaultLayout, ContainerLayout],

  props: {
    profile: {
      type: Object,
      default: () => {},
    },
  },

  data: function () {
    return {
      form: this.$inertia.form(
        {
          _method: 'PUT',
          name: this.profile.name,
          // email: this.email,
          // photo: null,
        },
        {
          bag: 'onSubmit',
          resetOnSuccess: false,
        }
      ),
    }
  },

  methods: {
    onSubmit: function () {
      const id = this.profile.id
      this.form.post(this.route('profiles.update', { id }), {
        preserveScroll: true,
      })
    },
  },
}
</script>
