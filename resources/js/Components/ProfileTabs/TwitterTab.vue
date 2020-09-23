<template>
  <v-simple-table dense>
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
  mixins: [StringFormatter],

  props: {
    twitter: {
      type: Object,
      default: () => {},
    },
  },

  computed: {
    items: function () {
      const items = []
      const tw = this.twitter
      const f = this.$options.filters

      items.push({ key: '名前', value: tw.name })
      items.push({ key: '＠', value: tw.screen_name })
      items.push({ key: '備考', value: f.htmlLinker(tw.description), html: true })
      items.push({ key: '場所', value: tw.location })
      items.push({ key: 'リンク', value: f.htmlLinker(tw.url), html: true })
      items.push({ key: '公開日時', value: f.formatHelper(tw.published_at, 'DF (DH, DN)') })
      items.push({ key: 'サムネイル', value: f.htmlLinker(tw.thumbnail_url), html: true })
      items.push({ key: 'バナー', value: f.htmlLinker(tw.banner_url), html: true })
      items.push({ key: 'フォロー数', value: f.numberDigit(tw.friends) })
      items.push({ key: 'フォロワー数', value: f.numberDigit(tw.followers) })
      items.push({ key: 'リスイン数', value: f.numberDigit(tw.listed) })
      items.push({ key: 'ツイート数', value: f.numberDigit(tw.statuses) })
      items.push({ key: 'いいね数', value: f.numberDigit(tw.favourites) })
      items.push({ key: '取得開始日時', value: f.formatHelper(tw.created_at, 'DF (DH)') })
      items.push({ key: '最終更新日時', value: f.formatHelper(tw.updated_at, 'DF (DH)') })

      return items
    },
  },
}
</script>
