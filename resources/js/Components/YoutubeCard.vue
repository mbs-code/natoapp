<template>
  <v-card class="mx-auto" width="640">
    <v-list-item>
      <v-list-item-avatar color="grey" size="64">
        <img :src="youtube.thumbnail_url" alt="profile" />
      </v-list-item-avatar>
      <v-list-item-content>
        <v-list-item-title class="headline">{{ youtube.name }}</v-list-item-title>
        <v-list-item-subtitle>
          <a class="normal-link" :href="youtube.code | toYoutubeChannelLink" target="_blank">
            channel/{{ youtube.code }}
          </a>
        </v-list-item-subtitle>
        <v-row class="mx-0" align="center">
          <v-icon class="mr-1">mdi-account</v-icon>
          <span class="subheading mr-2">{{ youtube.subscribers | numberDigit }}</span>
          <v-icon class="mr-1">mdi-play</v-icon>
          <span class="subheading mr-2">{{ youtube.views | numberDigit }}</span>
          <v-icon class="mr-1">mdi-video</v-icon>
          <span class="subheading">{{ youtube.videos | numberDigit }}</span>
        </v-row>
      </v-list-item-content>
    </v-list-item>
  </v-card>
</template>

<script>
import StringFormatter from '@/Mixins/StringFormatter'

export default {
  filters: {
    toYoutubeChannelLink: (channelId) => {
      return 'https://www.youtube.com/channel/' + channelId
    },
  },

  mixins: [StringFormatter],

  props: {
    youtube: {
      type: Object,
      default: () => {},
    },
  },
}
</script>
