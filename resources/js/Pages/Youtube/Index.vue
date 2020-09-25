<template>
  <v-row justify="center">
    <v-col v-for="youtube of youtubes" :key="youtube.id" cols="auto">
      <YoutubeCard :youtube="youtube" />
    </v-col>
  </v-row>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'
import YoutubeCard from '@/Components/YoutubeCard'

export default {
  layout: [DefaultLayout, ContainerLayout],

  components: { YoutubeCard },

  props: {
    youtubes: {
      type: Array,
      default: () => [],
    },
  },
}
</script>
