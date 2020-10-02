<template>
  <v-card>
    <v-card-title color="green">
      <v-row no-gutters align="center" class="headline">
        <v-icon v-if="icon" left>{{ icon }}</v-icon>
        {{ title }}
        <v-spacer />
        <v-btn icon @click="onCancel">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-row>
    </v-card-title>

    <v-divider />

    <v-card-text class="text--primary">
      <v-spacer />
      {{ message }}
      <!-- <component :is="formComponent" ref="form" :item="item" @success="close" /> -->
    </v-card-text>

    <v-divider />

    <v-card-actions>
      <v-row no-gutters justify="end">
        <v-btn color="red darken-1" text @click="onCancel">キャンセル</v-btn>
        <v-btn color="green darken-1" outlined @click="close">ＯＫ</v-btn>
      </v-row>
    </v-card-actions>
  </v-card>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      default: 'ダイアログ',
    },
    icon: {
      type: String,
      default: 'mdi-information-outline',
    },
    message: {
      type: String,
      default: '',
    },
  },

  methods: {
    onCancel: function () {
      this.$emit('submit', false)
    },

    close: function () {
      this.$emit('submit', true)
    },
  },
}
</script>
