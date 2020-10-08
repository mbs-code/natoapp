<template>
  <ContainerLayout>
    <template v-slot:toolbar>
      <v-row no-gutters justify="end">
        <v-btn
          :href="route('profiles.edit', { id: profile.id })"
          @click.stop.prevent="$inertia.visit(route('profiles.edit', { id: profile.id }))"
        >
          <v-icon color="grey darken-2">mdi-pencil-box</v-icon>
        </v-btn>
      </v-row>
    </template>

    <v-row justify="start">
      <v-col cols="6">
        <v-col cols="12">
          <ProfileCard :profile="profile" />
        </v-col>
        <v-col cols="12">
          <ProfileTabCard :profile="profile" />
        </v-col>
      </v-col>
      <v-col cols="6">
        <v-row justify="center">
          <v-col v-for="video of dbVideos" :key="video.id" cols="12">
            <VideoCard :video="video" card-link="true" />
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </ContainerLayout>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'
import ProfileCard from './_Card'
import ProfileTabCard from './_InfoTabs/_InfoTab'
import VideoCard from '@/Pages/Video/_Card'

export default {
  layout: [DefaultLayout],

  components: { ContainerLayout, ProfileCard, ProfileTabCard, VideoCard },

  props: {
    profile: {
      type: Object,
      default: () => {},
    },
  },

  data: function () {
    // const profile = this.item || {}
    // const id = profile.id

    return {
      dbVideos: [],
    }
  },

  mounted: async function () {
    const profile = this.profile || {}

    const { data } = await this.$http.get(route('api.videos'), {
      params: {
        channel: profile.youtubes.map((e) => e.id),
        sort: 'start_time',
        order: 'desc',
        page: '1',
        limit: '10',
      },
    })
    this.dbVideos = data
  },
}
</script>
