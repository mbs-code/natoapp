<template>
  <v-dialog v-model="show" max-width="320">
    <template v-for="(_, slot) of $scopedSlots" v-slot:[slot]="scope">
      <slot :name="slot" v-bind="scope" />
    </template>
    <!-- <template v-for="(value, name) in $slots" v-slot:[name]>
      <slot :name="name" />
    </template> -->
    <v-card>
      <v-card-title class="headline">
        <slot name="title">
          <v-icon left>mdi-alert-circle</v-icon>
          確認
        </slot>
      </v-card-title>
      <v-card-text v-if="this.$slots.text" class="text--primary">
        <v-spacer />
        <slot name="text" />
      </v-card-text>

      <v-divider />

      <v-card-actions>
        <v-row no-gutters justify="end">
          <v-btn color="red darken-1" text @click="onCancel">いいえ</v-btn>
          <v-btn color="green darken-1" outlined @click="onConfirm">はい</v-btn>
        </v-row>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  data: function () {
    return {
      show: false,
    }
  },

  methods: {
    onConfirm: function () {
      this.show = false
      this.$emit('ok')
    },
    onCancel: function () {
      this.show = false
      this.$emit('cancel')
    },
  },
}
</script>
