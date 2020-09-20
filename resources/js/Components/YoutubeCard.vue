<template>
  <v-card class="mx-auto" width="640">
    <v-list-item>
      <v-list-item-avatar color="grey" size="64">
        <img :src="youtube.thumbnail_url" alt="profile" />
      </v-list-item-avatar>
      <v-list-item-content>
        <v-list-item-title class="headline">{{ youtube.name }}</v-list-item-title>
        <v-list-item-subtitle>
          <a class="normal-link" :href="youtube.key | toYoutubeChannelLink">channel/{{ youtube.key }}</a>
        </v-list-item-subtitle>
        <v-row class="mx-0" align="center">
          <v-icon class="mr-1">mdi-account-arrow-right</v-icon>
          <span class="subheading mr-2">{{ youtube.subscribers | numberDigit }}</span>
          <v-icon class="mr-1">mdi-account-arrow-left</v-icon>
          <span class="subheading">{{ youtube.videos | numberDigit }}</span>
        </v-row>
      </v-list-item-content>
    </v-list-item>
  </v-card>
</template>

<script>
import StringFormatter from '@/Mixins/StringFormatter'

export default {
  mixins: [StringFormatter],

  filters: {
    toYoutubeChannelLink: (channelId) => {
      return 'https://www.youtube.com/channel/' + channelId
    },
  },

  props: {
    youtube: {
      type: Object,
      default: () => {},
    },
  },
}
</script>
