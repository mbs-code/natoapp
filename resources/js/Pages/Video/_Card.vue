<template>
  <v-hover v-slot:default="{ hover }">
    <v-card
      class="mx-auto"
      :elevation="hover && cardLink ? 12 : 2"
      :[cardHref]="route('videos.show', { id: video.id })"
      @[cardClick].stop.prevent="$inertia.visit(route('videos.show', { id: video.id }))"
    >
      <v-list-item>
        <v-list-item-action class="mr-3">
          <v-img
            :src="video.thumbnail_url"
            alt="video_thumbnail"
            :aspect-ratio="16/9"
            width="160"
          />
        </v-list-item-action>

        <v-list-item-content>
          <div class="text-h6">{{ video.title }}</div>
          <!-- <v-list-item-subtitle>
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
          </v-list-item-subtitle> -->
        </v-list-item-content>
      </v-list-item>
    </v-card>
  </v-hover>
</template>

<script>
import StringFormatter from '@/Mixins/StringFormatter'

export default {
  filters: {
    toYoutubeChannelLink: function (channelId) {
      return 'https://www.youtube.com/channel/' + channelId
    },
  },

  mixins: [StringFormatter],

  inheritAttrs: false,

  props: {
    video: {
      type: Object,
      default: () => {},
    },
    cardLink: {
      type: [Boolean, String],
      default: false,
    },
  },

  computed: {
    cardHref: function () {
      return this.cardLink ? 'href' : null
    },
    cardClick: function () {
      return this.cardLink ? 'click' : null
    },
  },
}
</script>
