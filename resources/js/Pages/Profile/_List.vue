<template>
  <!-- TODO: breakpoint を可変にしたい -->
  <v-data-table
    :headers="headers"
    :items="profiles"
    item-key="id"
    mobile-breakpoint="1000"
  >
    <template v-slot:[`item.links`]="{ item }">
      <v-avatar class="ma-2" color="grey lighten-3" size="68">
        <v-avatar class="ma-2" color="grey" size="64">
          <img :src="item.thumbnail_url" alt="twitter_thumbnail">
        </v-avatar>
      </v-avatar>
    </template>

    <template v-slot:[`item.name`]="{ item: { name } }">
      <span class="text-nowrap">{{ name }}</span>
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

    <template v-slot:[`item.profiles`]="{ item }">
      <div>
        <v-btn
          v-for="twitter in item.twitters"
          :key="twitter.id"
          class="ma-1"
          outlined
          small
          rounded
          color="light-blue"
          :href="twitter.link"
          target="_blank"
        >
          <v-icon left small>mdi-twitter</v-icon>
          @{{ twitter.screen_name }}
        </v-btn>
      </div>
      <div>
        <v-btn
          v-for="youtube in item.youtubes"
          :key="youtube.id"
          class="ma-1"
          outlined
          small
          rounded
          color="red"
          :href="youtube.link"
          target="_blank"
        >
          <v-icon left small>mdi-youtube</v-icon>
          {{ youtube.name }}
        </v-btn>
      </div>
    </template>

    <template v-slot:[`item.actions`]="{ item }">
      <v-btn
        class="ma-1"
        color="info"
        small
        outlined
        :href="route('profiles.show', { id: item.id })"
        @click.stop.prevent="$inertia.visit(route('profiles.show', { id: item.id }))"
      >
        <v-icon small>mdi-link-variant</v-icon>
      </v-btn>
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
        { text: '関連', value: 'profiles' },
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