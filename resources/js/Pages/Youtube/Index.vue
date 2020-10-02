<template>
  <ContainerLayout>
    <template v-slot:toolbar>
      <v-row no-gutters justify="end">
        <v-btn class="mx-1" @click="openEditDialog()">
          <v-icon color="grey darken-2">mdi-plus-box</v-icon>
        </v-btn>
      </v-row>
    </template>

    <v-row justify="center">
      <!-- TODO: breakpoint を可変にしたい -->
      <v-data-table
        :headers="headers"
        :items="youtubes"
        item-key="id"
        mobile-breakpoint="1000"
      >
        <template v-slot:[`item.links`]="{ item }">
          <v-avatar class="ma-2" color="grey" size="48">
            <img :src="item.thumbnail_url" alt="youtube_thumbnail">
          </v-avatar>
        </template>

        <template v-slot:[`item.subscribers`]="{ item: { subscribers } }">
          {{ subscribers | numberDigit }}
        </template>
        <template v-slot:[`item.views`]="{ item: { views } }">
          {{ views | numberDigit }}
        </template>
        <template v-slot:[`item.videos`]="{ item: { videos } }">
          {{ videos | numberDigit }}
        </template>
        <template v-slot:[`item.published_at`]="{ item: { published_at } }">
          <span style="white-space: nowrap;">
            {{ published_at | toDatetime }}
            <br>
            ({{ published_at | daysToNow }}, {{ published_at | datetimeHumanuzed }})
          </span>
        </template>

        <template v-slot:[`item.actions`]="{ item }">
          <v-btn
            class="ma-2"
            outlined
            small
            icon
            color="red"
            :href="item.link"
            target="_blank"
          >
            <v-icon small>mdi-youtube</v-icon>
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
    </v-row>
  </ContainerLayout>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'
import EditForm from '@/Pages/Youtube/_EditForm'
import FormDialog from '@/Components/CommonParts/FormDialog'
import ConfirmDialog from '@/Components/CommonParts/ConfirmDialog'
import StringFormatter from '@/Mixins/StringFormatter'

export default {
  layout: [DefaultLayout],

  components: { ContainerLayout },

  mixins: [StringFormatter],

  props: {
    youtubes: {
      type: Array,
      default: () => [],
    },
  },

  computed: {
    headers: function () {
      return [
        { text: '', value: 'links', sortable: false },
        { text: 'チャンネルID', value: 'code' },
        { text: '名前', value: 'name' },
        { text: '登録者数', value: 'subscribers' },
        { text: '再生数', value: 'views' },
        { text: '動画数', value: 'videos' },
        { text: '公開日時', value: 'published_at' },
        { text: '', value: 'actions', sortable: false },
      ]
    },
  },
  methods: {
    openEditDialog: async function (youtube) {
      const title = youtube ? `「${youtube.name}」の編集` : 'Youtube情報の作成'
      await this.$dialog.show(FormDialog, {
        title: title,
        formComponent: EditForm,
        item: youtube,
        persistent: true,
        showClose: false,
        waitForResult: true,
        width: 640,
      })
    },

    openDeleteDialog: async function (youtube) {
      const res = await this.$dialog.show(ConfirmDialog, {
        title: '確認',
        message: `「${youtube.name}」を削除しますか？`,
        showClose: false,
        waitForResult: true,
      })
      if (res) {
        this.$inertia.delete(route('youtubes.destroy', { id: youtube.id }))
      }
    },
  },
}
</script>
