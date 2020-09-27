<template>
  <v-combobox
    v-model="selects"
    :items="items"
    v-bind="$attrs"
    multiple
    return-object
    item-value="id"
    item-text="name"
    :search-input.sync="search"
    @keydown.enter="overrideAddTag"
    @change="$emit('change', selects)"
  >
    <template v-slot:selection="{ attrs, item, selected, disabled }">
      <v-chip
        v-bind="attrs"
        label
        close
        :input-value="selected"
        :disabled="disabled"
        @click:close="removeTags(item)"
      >
        <v-icon v-if="item.name" left>mdi-database</v-icon>
        {{ item.name || item }}
      </v-chip>
    </template>

    <template v-slot:item="{ item }">
      {{ item.name || item }}
    </template>
  </v-combobox>
</template>

<script>
export default {
  model: {
    event: 'change',
  },

  props: {
    value: {
      type: Array,
      default: () => [],
    },
    items: {
      type: Array,
      default: () => [],
    },
  },

  data: function () {
    return {
      search: '', //sync search text
      selects: this.value, // props.value の cache
      // selects: ['add-tags-with', 'enter', 'tab', 'paste'],
      // items: [
      //   { id: 10, name: 'てきすと' },
      //   { id: 20, name: 'さんぷる' },
      // ],
    }
  },

  methods: {
    overrideAddTag: function (value) {
      // 対象外の時は search = null にすることで追加処理を上書きする

      const search = this.search

      // 既に追加されているなら toast
      const find = this.value.find((e) => e === search || e.name === search)
      if (find) {
        // this.$toast() // TODO: toast をグローバルに置いておきたい
        console.log('already exists')

        this.search = null
        return
      }

      // もし items にあるならそれを使う
      const contain = this.items.find((e) => e.name === search)
      if (contain) {
        this.selects.push(contain)

        this.search = null
        return
      }

      // それ以外は通常追加
    },

    removeTags: function (item) {
      this.selects.splice(this.selects.indexOf(item), 1)
      this.selects = [...this.selects]
    },
  },
}
</script>
