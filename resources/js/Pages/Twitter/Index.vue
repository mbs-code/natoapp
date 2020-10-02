<template>
  <v-row justify="center">
    <!-- TODO: breakpoint を可変にしたい -->
    <v-data-table
      :headers="headers"
      :items="twitters"
      item-key="id"
      mobile-breakpoint="1000"
    >
      <template v-slot:[`item.links`]="{ item }">
        <v-btn
          class="ma-2"
          outlined
          small
          icon
          color="light-blue"
          :href="item.screen_name | toTwitterUserLink"
          target="_blank"
        >
          <v-icon small>mdi-twitter</v-icon>
        </v-btn>
      </template>

      <template v-slot:[`item.friends`]="{ item: { friends } }">
        {{ friends | numberDigit }}
      </template>
      <template v-slot:[`item.followers`]="{ item: { followers } }">
        {{ followers | numberDigit }}
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
          class="ma-1"
          color="success"
          small
          outlined
          @click="openEditDialog(item)"
        >
          <v-icon small>mdi-pencil</v-icon>
        </v-btn>
        <v-btn class="ma-1" color="error" small outlined>
          <v-icon small>mdi-delete</v-icon>
        </v-btn>
      </template>
    </v-data-table>
  </v-row>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'
import FormDialog from '@/Components/CommonParts/FormDialog'
import EditTwitterForm from '@/Components/Forms/EditTwitterForm'
import StringFormatter from '@/Mixins/StringFormatter'

export default {
  layout: [DefaultLayout, ContainerLayout],

  filters: {
    toTwitterUserLink: function (userName) {
      return 'https://twitter.com/' + userName
    },
  },

  mixins: [StringFormatter],

  props: {
    twitters: {
      type: Array,
      default: () => [],
    },
  },

  computed: {
    headers: function () {
      return [
        { text: '', value: 'links', sortable: false },
        { text: '@', value: 'screen_name' },
        { text: '名前', value: 'name' },
        { text: 'フォロー', value: 'friends' },
        { text: 'フォロワー', value: 'followers' },
        { text: '公開日時', value: 'published_at' },
        { text: '', value: 'actions', sortable: false },
      ]
    },
  },
  methods: {
    openEditDialog: async function (item) {
      const name = item.screen_name || 'プロファイル'
      await this.$dialog.show(FormDialog, {
        title: `${name} の編集`,
        formComponent: EditTwitterForm,
        item: item,
        persistent: true,
      })
    },
  },
}
</script>
