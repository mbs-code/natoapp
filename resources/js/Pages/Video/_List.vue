<template>
  <!-- TODO: breakpoint を可変にしたい -->
  <v-data-table
    :headers="headers"
    :items="videos"
    item-key="id"
    mobile-breakpoint="1000"
  >
    <template v-slot:[`item.links`]="{ item }">
      <v-img
        class="ma-1"
        :src="item.thumbnail_url"
        alt="video_thumbnail"
        :aspect-ratio="16/9"
        width="120"
      />
    </template>

    <template v-slot:[`item.title`]="{ item: { title } }">
      <span style="display:inline-block; max-width: 500px;">{{ title }}</span>
    </template>

    <template v-slot:[`item.duration`]="{ item: { duration } }">
      {{ duration | durationHumanized }}
    </template>
    <template v-slot:[`item.views`]="{ item: { views } }">
      {{ views | numberDigit }}
    </template>
    <template v-slot:[`item.good_rate`]="{ item }">
      {{ (item.likes / (item.likes + item.dislikes)) * 100 | numberToFixed(1) }}%
    </template>
    <template v-slot:[`item.start_time`]="{ item: { start_time } }">
      <span style="white-space: nowrap;">
        {{ start_time | toDatetime }}
        <br>
        ({{ start_time | datetimeHumanuzed }})
      </span>
    </template>

    <template v-slot:[`item.actions`]="{ item }">
      <v-btn
        class="ma-1"
        color="info"
        small
        outlined
        :href="route('videos.show', { id: item.id })"
        @click.stop.prevent="$inertia.visit(route('videos.show', { id: item.id }))"
      >
        <v-icon small>mdi-link-variant</v-icon>
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
    videos: {
      type: Array,
      default: () => [],
    },
  },

  computed: {
    headers: function () {
      return [
        { text: '', value: 'links', sortable: false },
        { text: '動画ID', value: 'code' },
        { text: '名前', value: 'title' },
        { text: '種別', value: 'type' },
        { text: '状態', value: 'status' },
        { text: '長さ', value: 'duration' },
        { text: '再生数', value: 'views' },
        { text: 'GOOD率', value: 'good_rate' },
        { text: '開始日時', value: 'start_time' },
        { text: '', value: 'actions', sortable: false },
      ]
    },
  },

  methods: {
    openEditDialog: async function (video) {
      const title = video ? `「${video.title}」の編集` : '動画情報の作成'
      await this.$dialog.show(FormDialog, {
        title: title,
        formComponent: EditForm,
        item: video,
        persistent: true,
        showClose: false,
        waitForResult: true,
        width: 640,
      })
    },

    openDeleteDialog: async function (video) {
      const res = await this.$dialog.show(ConfirmDialog, {
        title: '確認',
        message: `「${video.title}」を削除しますか？`,
        showClose: false,
        waitForResult: true,
      })
      if (res) {
        this.$inertia.delete(route('videos.destroy', { id: video.id }))
      }
    },
  },
}
</script>
