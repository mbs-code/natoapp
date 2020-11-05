<template>
  <v-form ref="form" @submit.prevent="submit">
    <v-text-field
      v-model="form.name"
      label="名前*"
      required
      counter="32"
      :error-messages="errors.name"
    />

    <v-text-field
      v-model="form.kana"
      label="ふりがな"
      required
      counter="64"
      :error-messages="errors.kana"
    />

    <v-textarea
      v-model="form.description"
      label="メモ"
      required
      auto-grow
      rows="1"
      counter="65535"
      :error-messages="errors.description"
    />

    <VTagCombobox
      v-model="form.tags"
      label="タグ"
      :items="dbTags"
      :error-messages="errors.tags"
    />

    <VTagCombobox
      v-model="form.twitters"
      label="Twitter"
      :items="dbTwitters"
      :error-messages="errors.twitters"
    />

    <VTagCombobox
      v-model="form.youtubes"
      label="Youtube"
      :items="dbYoutubes"
      :error-messages="errors.youtubes"
    />
  </v-form>
</template>

<script>
import IntierFormMixin from '@/Mixins/InertiaFormMixin'
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'
import VTagCombobox from '@/Components/forms/VTagCombobox'

export default {
  components: { VTagCombobox },

  mixins: [IntierFormMixin],

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
          _route: 'profiles',
          _params: { id },
          name: profile.name,
          kana: profile.kana,
          description: profile.description,
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

  mounted: async function () {
    const { data: tags } = await this.$http.get(route('api.tags'))
    this.dbTags = tags

    const { data: twitters } = await this.$http.get(route('api.twitters'))
    this.dbTwitters = twitters

    const { data: youtubes } = await this.$http.get(route('api.youtubes'))
    this.dbYoutubes = youtubes
  },
}
</script>
