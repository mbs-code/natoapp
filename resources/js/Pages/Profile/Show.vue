<template>
  <v-row justify="start">
    <v-col cols="12">
      <ProfileCard :profile="profile" />
    </v-col>
    <v-col cols="12">
      <ProfileTabCard :profile="profile" />
    </v-col>
  </v-row>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ProfileCard from '@/Components/ProfileCard'
import ProfileTabCard from '@/Components/ProfileTabs/ProfileTabCard'

export default {
  layout: DefaultLayout,

  components: { ProfileCard, ProfileTabCard },

  props: {
    profile: {
      type: Object,
      default: () => {},
    },
  },
}
</script>
