<template>
  <v-input v-bind="$attrs" hide-details="auto">
    <v-slide-group
      v-model="selectIndex"
      class="my-2"
      show-arrows
      mandatory
      @change="$emit('change', imageUrls[selectIndex])"
    >
      <v-slide-item
        v-for="imageUrl of imageUrls"
        :key="imageUrl"
        v-slot:default="{ active, toggle }"
      >
        <v-card
          :color="active ? 'primary' : 'white'"
          class="ma-2"
          :height="size"
          :width="size"
          @click="toggle"
        >
          <v-row
            class="fill-height"
            align="center"
            justify="center"
          >
            <v-img
              :src="imageUrl"
              aspect-ratio="1"
              :max-width="size - border"
              :max-height="size - border"
            />
          </v-row>
        </v-card>
      </v-slide-item>
    </v-slide-group>
  </v-input>
</template>

<script>
export default {
  model: {
    event: 'change',
  },

  props: {
    // url 要素
    value: {
      type: String,
      default: null,
    },
    size: {
      type: Number,
      default: 64,
    },
    imageUrls: {
      type: Array,
      default: () => [],
    },
  },

  data: function ({ value, imageUrls }) {
    return {
      border: 10,
      selectIndex: imageUrls.indexOf(value),
    }
  },
}
</script>
