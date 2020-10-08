<template>
  <v-simple-table dense class="fixed-table">
    <template v-slot:default>
      <thead>
        <tr>
          <th class="text-left" width="120">Key</th>
          <th class="text-left">Value</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in items" :key="item.name">
          <td class="grey--text text--darken-2">
            <v-icon v-if="item.icon">{{ item.icon }}</v-icon>
            {{ item.key }}
          </td>
          <!-- eslint-disable-next-line vue/no-v-html -->
          <td v-if="item.html || '-'" v-html="item.value" />
          <td v-else>{{ item.value || '-' }}</td>
        </tr>
      </tbody>
    </template>
  </v-simple-table>
</template>

<script>
import StringFormatter from '@/Mixins/StringFormatter'

export default {
  filters: {
    arrayToString: function (ary) {
      if (Array.isArray(ary)) {
        return ary.join(', ')
      }
      return ary
    },
  },

  mixins: [StringFormatter],

  props: {
    youtube: {
      type: Object,
      default: () => {},
    },
  },

  computed: {
    items: function () {
      const items = []
      const yt = this.youtube
      const f = this.$options.filters

      items.push({ key: 'ID', value: yt.code })
      items.push({ key: '名前', value: yt.name })
      items.push({ key: '公開日時', value: f.formatHelper(yt.published_at, 'DF (DH, DN)') })
      items.push({ key: 'サムネイル', value: f.htmlLinker(yt.thumbnail_url), html: true })
      items.push({ key: 'バナー', value: f.htmlLinker(yt.banner_url), html: true })
      items.push({ key: '登録者数', value: f.numberDigit(yt.subscribers) })
      items.push({ key: '動画数', value: f.numberDigit(yt.videos) })
      // items.push({ key: 'コメント数', value: f.numberDigit(yt.comments) }) // 大抵0なのでコメントアウト
      items.push({ key: 'タグ', value: f.arrayToString(yt.tags) })
      items.push({ key: '取得開始日時', value: f.formatHelper(yt.created_at, 'DF (DH)') })
      items.push({ key: '最終更新日時', value: f.formatHelper(yt.updated_at, 'DF (DH)') })
      items.push({ key: '備考', value: f.htmlLinker(yt.description), html: true })

      return items
    },
  },
}
</script>
