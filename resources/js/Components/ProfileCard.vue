<template>
  <v-hover v-slot:default="{ hover }">
    <!-- 非 buttonMode の時はリンクが有効 -->
    <v-card
      class="mx-auto"
      :elevation="hover && !buttonMode ? 12 : 2"
      :[cardHref]="route('profiles.show', { id: profile.id })"
      @[cardClick].stop.prevent="$inertia.visit(route('profiles.show', { id: profile.id }))"
    >
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

        <!-- draw when buttonMode -->
        <v-list-item-action v-if="buttonMode">
          <v-row>
            <v-btn
              height="64"
              color="info"
              outlined
              class="mx-1"
              :href="route('profiles.show', { id: profile.id })"
              @click.stop.prevent="$inertia.visit(route('profiles.show', { id: profile.id }))"
            >
              <v-icon>mdi-arrow-right-bold-box-outline</v-icon>
            </v-btn>
            <v-btn
              height="64"
              color="success"
              outlined
              class="mx-1"
              :href="route('profiles.edit', { id: profile.id })"
              @click.stop.prevent="$inertia.visit(route('profiles.edit', { id: profile.id }))"
            >
              <v-icon>mdi-pencil</v-icon>
            </v-btn>
            <ConfirmDialog @ok="$inertia.delete(route('profiles.destroy', { id: profile.id }))">
              <template v-slot:activator="{ attrs, on }">
                <v-btn
                  height="64"
                  color="error"
                  outlined
                  class="mx-1"
                  v-bind="attrs"
                  v-on="on"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
              <template v-slot:text>
                「{{ profile.name }}」を削除しますか？
              </template>
            </ConfirmDialog>
          </v-row>
        </v-list-item-action>
        <!-- end draw when buttonMode -->
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
            :href="twitter.link"
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
  </v-hover>
</template>

<script>
import ConfirmDialog from '@/Components/CommonParts/ConfirmDialog'
import StringFormatter from '@/Mixins/StringFormatter'

export default {
  components: { ConfirmDialog },

  filters: {
    toYoutubeChannelLink: function (channelId) {
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
    buttonMode: {
      // true で button, false で card link
      type: Boolean,
      default: false,
    },
  },

  computed: {
    cardHref: function () {
      return this.buttonMode ? null : 'href'
    },
    cardClick: function () {
      return this.buttonMode ? null : 'click'
    },
  },
}
</script>
