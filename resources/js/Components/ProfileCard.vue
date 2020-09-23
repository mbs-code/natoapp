<template>
  <v-card class="mx-auto" width="640">
    <v-list-item>
      <v-list-item-avatar color="grey" size="64">
        <img :src="profile.thumbnail_url" alt="profile">
      </v-list-item-avatar>

      <v-list-item-content>
        <v-list-item-title class="headline">{{ profile.name }}</v-list-item-title>
        <v-list-item-subtitle>
          <v-chip
            v-for="tag in profile.tags"
            :key="tag.id"
            class="ma-2"
            outlined
            label
            small
            :color="tag.color"
          >
            {{ tag.name }}
          </v-chip>
        </v-list-item-subtitle>
      </v-list-item-content>

      <!-- draw when hasLink -->
      <v-list-item-action v-if="hasLink">
        <v-btn
          height="64"
          depressed
          color="white"
          :href="route('profiles.show', { id: profile.id })"
          @click.stop.prevent="$inertia.visit(route('profiles.show', { id: profile.id }))"
        >
          <v-icon color="grey darken-2">mdi-arrow-right-bold-box-outline</v-icon>
        </v-btn>
      </v-list-item-action>
      <!-- end draw when hasLink -->
    </v-list-item>

    <!-- separator -->

    <v-container>
      <v-row v-for="twitter in profile.twitters" :key="twitter.id" class="mx-0" align="center">
        <v-btn
          class="ma-2"
          outlined
          fab
          small
          color="light-blue"
          :href="twitter.screen_name | toTwitterUserLink"
          target="_blank"
        >
          <v-icon>mdi-twitter</v-icon>
        </v-btn>
        <span class="subheading ml-2 mr-4">@{{ twitter.screen_name }}</span>
        <span class="mx-2">
          <v-icon>mdi-account-arrow-right</v-icon>
          {{ twitter.friends | numberDigit }}
        </span>
        <span class="mx-2">
          <v-icon>mdi-account-arrow-left</v-icon>
          {{ twitter.followers | numberDigit }}
        </span>
      </v-row>
      <v-row v-for="youtube in profile.youtubes" :key="youtube.id" class="mx-0" align="center">
        <v-btn
          class="ma-2"
          outlined
          fab
          small
          color="red"
          :href="youtube.code | toYoutubeChannelLink"
          target="_blank"
        >
          <v-icon>mdi-youtube</v-icon>
        </v-btn>
        <span class="subheading ml-2 mr-4">@{{ youtube.name }}</span>
        <span class="mx-2">
          <v-icon>mdi-account</v-icon>
          {{ youtube.subscribers | numberDigit }}
        </span>
        <span class="mx-2">
          <v-icon>mdi-play</v-icon>
          {{ youtube.views | numberDigit }}
        </span>
        <span class="mx-2">
          <v-icon>mdi-video</v-icon>
          {{ youtube.videos | numberDigit }}
        </span>
      </v-row>
    </v-container>
  </v-card>
</template>

<script>
import StringFormatter from '@/Mixins/StringFormatter'

export default {
  filters: {
    toTwitterUserLink: (userName) => {
      return 'https://twitter.com/' + userName
    },
    toYoutubeChannelLink: (channelId) => {
      return 'https://www.youtube.com/channel/' + channelId
    },
  },

  mixins: [StringFormatter],

  inheritAttrs: false,

  props: {
    profile: {
      type: Object,
      default: () => {},
    },
    hasLink: {
      type: Boolean,
      default: false,
    },
  },
}
</script>
