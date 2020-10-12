<template>
  <ContainerLayout>
    <template v-slot:toolbar>
      <v-row no-gutters justify="end">
        <SwitchButton
          v-model="videoTableMode"
          class="mx-1"
        />
        <v-btn class="mx-1" @click="$refs.list.openEditDialog()">
          <v-icon color="grey darken-2">mdi-plus-box</v-icon>
        </v-btn>
      </v-row>
    </template>

    <v-row justify="center">
      <template v-if="videoTableMode">
        <VideoList ref="list" :videos="videos" />
      </template>
      <template v-else>
        <v-col v-for="video of videos" :key="video.id" cols="12">
          <VideoCard :video="video" card-link="true" />
        </v-col>
      </template>
    </v-row>
  </ContainerLayout>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'
import SwitchButton from '@/Components/CommonParts/SwitchButton'
import VideoList from './_List'
import VideoCard from './_Card'

export default {
  layout: [DefaultLayout],

  components: { ContainerLayout, SwitchButton, VideoList, VideoCard },

  props: {
    videos: {
      type: Array,
      default: () => [],
    },
  },

  computed: {
    videoTableMode: {
      get() {
        return this.$store.getters['config/getVideoTableMode']
      },
      set(value) {
        this.$store.commit('config/setVideoTableMode', value)
      },
    },
  },
}
</script>
