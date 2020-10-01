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

    <!-- <VImageSelect
      v-model="form.thumbnail_url"
      label="thumbnail"
      :size="100"
      :image-urls="imageUrls"
    />

    <VTagCombobox
      v-model="form.tags"
      label="tags"
      :items="tags"
    /> -->

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

  // components: { VImageSelect, VTagCombobox },

  props: {
    twitter: {
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
    const twitter = this.twitter || {}
    const id = twitter.id

    return {
      form: this.$inertia.form(
        {
          _method: id ? 'PUT' : 'POST',
          name: twitter.name,
          // description: profile.description,
          // thumbnail_url: profile.thumbnail_url,
          // tags: profile.tags || [],
        },
        {
          bag: 'onSubmit',
          resetOnSuccess: false,
        }
      ),
    }
  },

  // computed: {
  //   imageUrls: function () {
  //     const profile = this.profile || {}

  //     const urls = []
  //     const twitters = (profile.twitters || []).map((e) => e.thumbnail_url)
  //     urls.push(...twitters)

  //     const youtubes = (profile.youtubes || []).map((e) => e.thumbnail_url)
  //     urls.push(...youtubes)
  //     return urls
  //   },
  // },

  methods: {
    onSubmit: function () {
      const twitter = this.twitter || {}
      const id = twitter.id

      const url = id ? this.route('twitters.update', { id }) : this.route('twitters.store')
      this.form.post(url, {
        preserveScroll: true,
      })
    },
  },
}
</script>
