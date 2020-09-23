<template>
  <v-row justify="start">
    <v-col cols="auto">
      <ProfileCard :profile="profile" />
    </v-col>
    <v-col cols="4">pad</v-col>
    <v-col cols="auto">
      <v-card>
        <v-tabs v-model="tab" class="pa-2">
          <template v-for="profileTab of profileTabs">
            <v-tab :key="profileTab.key" :href="'#' + profileTab.key">
              <v-icon left>{{ profileTab.icon }}</v-icon>
              {{ profileTab.name }}
            </v-tab>
            <v-tab-item :key="profileTab.key" :value="profileTab.key" class="pa-2">
              {{ profileTab.value }}
            </v-tab-item>
          </template>
        </v-tabs>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ProfileCard from '@/Components/ProfileCard'

export default {
  layout: DefaultLayout,

  components: { ProfileCard },

  props: {
    profile: {
      type: Object,
      default: () => {},
    },
  },

  data: () => {
    return {
      tab: null, // tab name
    }
  },

  computed: {
    profileTabs: function () {
      const tabs = []
      const profile = this.profile

      const twitters = profile.twitters || []
      for (let i = 0; i < twitters.length; i++) {
        const twitter = twitters[i]
        tabs.push({
          type: 'twitter',
          key: 'twitter-' + (i + 1),
          icon: 'mdi-twitter',
          name: '@' + twitter.name,
          value: twitter,
        })
      }

      const youtubes = profile.youtubes || []
      for (let i = 0; i < youtubes.length; i++) {
        const youtube = youtubes[i]
        tabs.push({
          type: 'youtube',
          key: 'youtube-' + (i + 1),
          icon: 'mdi-youtube',
          name: '@' + youtube.name,
          value: youtube,
        })
      }
      return tabs
    },
  },
}
</script>
