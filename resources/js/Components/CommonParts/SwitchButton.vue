<template>
  <v-sheet
    elevation="2"
    rounded
    width="64"
    height="36"
    v-bind="$attrs"
  >
    <v-row
      no-gutters
      justify="center"
      align="center"
      class="fill-height"
    >
      <v-switch
        v-model="isOn"
        class="ma-0 pa-0"
        hide-details="true"
        @change="$emit('change', isOn)"
      />
    </v-row>
  </v-sheet>
</template>

<script>
export default {
  model: {
    event: 'change',
  },

  props: {
    value: {
      type: Boolean,
      default: false,
    },
  },

  data: function () {
    return {
      isOn: this.value,
    }
  },
}
</script>
