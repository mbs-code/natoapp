<template>
  <!-- TODO: breakpoint を可変にしたい -->
  <v-data-table
    :headers="headers"
    :items="profiles"
    item-key="id"
    mobile-breakpoint="1000"
  >
    <template v-slot:[`item.links`]="{ item }">
      <v-avatar class="ma-2" color="grey" size="48">
        <img :src="item.thumbnail_url" alt="twitter_thumbnail">
      </v-avatar>
    </template>

    <template v-slot:[`item.tags`]="{ item: { tags } }">
      <v-chip
        v-for="tag in tags"
        :key="tag.id"
        class="ma-2"
        outlined
        label
        small
        :color="tag.color"
      >
        {{ tag.name }}
      </v-chip>
    </template>

    <template v-slot:[`item.actions`]="{ item }">
      <v-btn
        class="ma-1"
        color="success"
        small
        outlined
        @click="openEditDialog(item)"
      >
        <v-icon small>mdi-pencil</v-icon>
      </v-btn>
      <v-btn
        class="ma-1"
        color="error"
        small
        outlined
        @click="openDeleteDialog(item)"
      >
        <v-icon small>mdi-delete</v-icon>
      </v-btn>
    </template>
  </v-data-table>
</template>

<script>
import FormDialog from '@/Components/CommonParts/FormDialog'
import ConfirmDialog from '@/Components/CommonParts/ConfirmDialog'
import StringFormatter from '@/Mixins/StringFormatter'
import EditForm from './_EditForm'

export default {
  mixins: [StringFormatter],

  props: {
    profiles: {
      type: Array,
      default: () => [],
    },
  },

  computed: {
    headers: function () {
      return [
        { text: '', value: 'links', sortable: false },
        { text: '名前', value: 'name' },
        { text: 'タグ', value: 'tags' },
        { text: '', value: 'actions', sortable: false },
      ]
    },
  },

  methods: {
    openEditDialog: async function (profile) {
      const title = profile ? `「${profile.name}」の編集` : 'Profile情報の作成'
      await this.$dialog.show(FormDialog, {
        title: title,
        formComponent: EditForm,
        item: profile,
        persistent: true,
        showClose: false,
        waitForResult: true,
        width: 640,
      })
    },

    openDeleteDialog: async function (profile) {
      const res = await this.$dialog.show(ConfirmDialog, {
        title: '確認',
        message: `「${profile.name}」を削除しますか？`,
        showClose: false,
        waitForResult: true,
      })
      if (res) {
        this.$inertia.delete(route('profiles.destroy', { id: profile.id }))
      }
    },
  },
}
</script>