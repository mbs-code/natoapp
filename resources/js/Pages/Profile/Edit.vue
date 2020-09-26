<template>
  <v-form @submit.prevent="onSubmit">
    <v-text-field
      v-model="form.name"
      label="name"
      required
      counter="32"
    />

    <v-textarea
      v-model="form.description"
      label="description"
      required
      counter="32"
    />

    <VImageSelect
      v-model="form.thumbnail_url"
      label="thumbnail"
      :size="100"
      :image-urls="imageUrls"
    />

    <v-btn type="submit">submit</v-btn>
  </v-form>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'
import VImageSelect from '@/COmponents/Forms/VImageSelect'

export default {
  layout: [DefaultLayout, ContainerLayout],

  components: { VImageSelect },

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
          description: this.profile.description,
          thumbnail_url: this.profile.thumbnail_url,
        },
        {
          bag: 'onSubmit',
          resetOnSuccess: false,
        }
      ),
    }
  },

  computed: {
    imageUrls: function () {
      const urls = []
      const twitters = (this.profile.twitters || []).map((e) => e.thumbnail_url)
      urls.push(...twitters)

      const youtubes = (this.profile.youtubes || []).map((e) => e.thumbnail_url)
      urls.push(...youtubes)
      return urls
    },
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
