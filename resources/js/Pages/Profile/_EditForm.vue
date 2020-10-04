<template>
  <v-form ref="form" @submit.prevent="submit">
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
      auto-grow
      rows="1"
      counter="65535"
    />

    <VTagCombobox
      v-model="form.tags"
      label="tags"
      :items="dbTags"
    />

    <VTagCombobox
      v-model="form.twitters"
      label="twitters"
      :items="dbTwitters"
    />

    <VTagCombobox
      v-model="form.youtubes"
      label="youtubes"
      :items="dbYoutubes"
    />

    <VImageSelect
      v-model="form.thumbnail_url"
      label="thumbnail"
      :size="100"
      :image-urls="imageUrls"
    />
  </v-form>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'
import VImageSelect from '@/Components/Forms/VImageSelect'
import VTagCombobox from '@/Components/forms/VTagCombobox'

export default {
  components: { VImageSelect, VTagCombobox },

  props: {
    item: {
      type: Object,
      default: () => {},
    },
  },

  data: function () {
    const profile = this.item || {}
    const id = profile.id

    return {
      form: this.$inertia.form(
        {
          _method: id ? 'PUT' : 'POST',
          name: profile.name,
          description: profile.description,
          thumbnail_url: profile.thumbnail_url,
          tags: profile.tags || [],
          twitters: profile.twitters || [],
          youtubes: profile.youtubes || [],
        },
        {
          bag: 'submit',
          resetOnSuccess: false,
        }
      ),
      errors: {},
      dbTags: [],
      dbTwitters: [],
      dbYoutubes: [],
    }
  },

  computed: {
    imageUrls: function () {
      const profile = this.item || {}

      const urls = []
      const twitters = (profile.twitters || []).map((e) => e.thumbnail_url)
      urls.push(...twitters)

      const youtubes = (profile.youtubes || []).map((e) => e.thumbnail_url)
      urls.push(...youtubes)
      return urls
    },
  },

  mounted: async function () {
    const { data: tags } = await this.$http.get(route('api.tags'))
    this.dbTags = tags

    const { data: twitters } = await this.$http.get(route('api.twitters'))
    this.dbTwitters = twitters

    const { data: youtubes } = await this.$http.get(route('api.youtubes'))
    this.dbYoutubes = youtubes
  },

  methods: {
    submit: function () {
      const profile = this.item || {}
      const id = profile.id

      const url = id ? this.route('profiles.update', { id }) : this.route('profiles.store')
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
