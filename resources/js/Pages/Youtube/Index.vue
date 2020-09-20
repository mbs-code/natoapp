<template>
  <v-row justify="center">
    <v-col v-for="youtube of youtubes" :key="youtube.id" cols="auto">
      <YoutubeCard :youtube="youtube" />
    </v-col>
  </v-row>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import YoutubeCard from '@/Components/YoutubeCard'

export default {
  layout: DefaultLayout,

  components: { YoutubeCard },

  props: {
    youtubes: {
      type: Array,
      default: () => [],
    },
  },
}
</script>
