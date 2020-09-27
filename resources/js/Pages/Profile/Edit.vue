<template>
  <v-form @submit.prevent="onSubmit">
    <div>{{ $page.errors }}</div>
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

    <VTagCombobox
      v-model="form.tags"
      label="tags"
      :items="tags"
    />

    <v-btn type="submit">submit</v-btn>
  </v-form>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'
import VImageSelect from '@/Components/Forms/VImageSelect'
import VTagCombobox from '@/Components/forms/VTagCombobox'

export default {
  layout: [DefaultLayout, ContainerLayout],

  components: { VImageSelect, VTagCombobox },

  props: {
    profile: {
      type: Object,
      default: () => {},
    },
    tags: {
      // DBのタグ一覧
      type: Array,
      default: () => [],
    },
  },

  data: function () {
    const profile = this.profile || {}
    const id = profile.id

    return {
      form: this.$inertia.form(
        {
          _method: id ? 'PUT' : 'POST',
          name: profile.name,
          description: profile.description,
          thumbnail_url: profile.thumbnail_url,
          tags: profile.tags || [],
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
      const profile = this.profile || {}

      const urls = []
      const twitters = (profile.twitters || []).map((e) => e.thumbnail_url)
      urls.push(...twitters)

      const youtubes = (profile.youtubes || []).map((e) => e.thumbnail_url)
      urls.push(...youtubes)
      return urls
    },
  },

  methods: {
    onSubmit: function () {
      const profile = this.profile || {}
      const id = profile.id

      const url = id ? this.route('profiles.update', { id }) : this.route('profiles.store')
      this.form.post(url, {
        preserveScroll: true,
      })
    },
  },
}
</script>
