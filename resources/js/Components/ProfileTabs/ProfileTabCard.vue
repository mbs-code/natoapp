<template>
  <v-card>
    <v-tabs v-model="showTabKey" class="pa-2">
      <template v-for="tab of tabs">
        <v-tab
          :key="tab.key"
          :href="'#' + tab.key"
          class="text-capitalize"
          color="red"
        >
          <v-icon left>{{ tab.icon }}</v-icon>
          {{ tab.name }}
        </v-tab>
        <v-tab-item
          :key="tab.key"
          :value="tab.key"
          class="pa-2"
          transition="fade-transition"
          reverse-transition="fade-transition"
        >
          <TwitterTab v-if="tab.type === 'twitter'" :twitter="tab.item" />
          <YoutubeTab v-else-if="tab.type === 'youtube'" :youtube="tab.item" />
          <div v-else>
            {{ tab.item }} <!-- !!! error !!! -->
          </div>
        </v-tab-item>
      </template>
    </v-tabs>
  </v-card>
</template>

<script>
import TwitterTab from '@/Components/ProfileTabs/TwitterTab'
import YoutubeTab from '@/Components/ProfileTabs/YoutubeTab'

export default {
  components: { TwitterTab, YoutubeTab },

  props: {
    profile: {
      type: Object,
      default: () => {},
    },
  },

  data: () => {
    return {
      showTabKey: null, // tab name
    }
  },

  computed: {
    tabs: function () {
      const tabs = []
      const profile = this.profile

      const twitters = profile.twitters || []
      for (let i = 0; i < twitters.length; i++) {
        const twitter = twitters[i]
        tabs.push({
          type: 'twitter',
          key: 'twitter-' + (i + 1),
          icon: 'mdi-twitter',
          name: '@' + twitter.screen_name,
          item: twitter,
        })
      }

      const youtubes = profile.youtubes || []
      for (let i = 0; i < youtubes.length; i++) {
        const youtube = youtubes[i]
        tabs.push({
          type: 'youtube',
          key: 'youtube-' + (i + 1),
          icon: 'mdi-youtube',
          name: youtube.name,
          item: youtube,
        })
      }
      return tabs
    },
  },
}
</script>

<style lang="scss">
.v-tabs-bar__content {
  border-bottom: solid 1px lightgrey;
}
</style>