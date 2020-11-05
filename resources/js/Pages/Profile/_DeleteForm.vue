<template>
  <v-form ref="form" @submit.prevent="submit">
    <div class="subtitle-1">「{{ item.name }}」を削除しますか？</div>

    <v-checkbox
      v-model="form.withProfilable"
      label="関連データも削除する (Twitterなど)"
      messages="※他で使われている場合は削除されません。"
      :error-messages="errors.withProfilable"
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
    const profile = this.item || {}
    const id = profile.id

    return {
      form: this.$inertia.form(
        {
          _method: 'DELETE',
          _route: 'profiles',
          _params: { id },
          withProfilable: true,
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
