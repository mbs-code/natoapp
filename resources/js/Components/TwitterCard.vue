<template>
  <v-card class="mx-auto" width="640">
    <v-list-item>
      <v-list-item-avatar color="grey" size="64">
        <img :src="twitter.thumbnail_url" alt="profile" />
      </v-list-item-avatar>
      <v-list-item-content>
        <v-list-item-title class="headline">{{ twitter.name }}</v-list-item-title>
        <v-list-item-subtitle>
          <a class="normal-link" :href="twitter.screen_name | toTwitterUserLink">@{{ twitter.screen_name }}</a>
        </v-list-item-subtitle>
        <v-row class="mx-0" align="center">
          <v-icon class="mr-1">mdi-account-arrow-right</v-icon>
          <span class="subheading mr-2">{{ twitter.friends | numberDigit }}</span>
          <v-icon class="mr-1">mdi-account-arrow-left</v-icon>
          <span class="subheading">{{ twitter.followers | numberDigit }}</span>
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
    toTwitterUserLink: (userName) => {
      return 'https://twitter.com/' + userName
    },
  },

  props: {
    twitter: {
      type: Object,
      default: () => {},
    },
  },
}
</script>
