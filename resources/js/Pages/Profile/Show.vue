<template>
  <v-row justify="start">
    <v-col cols="12">
      <v-btn
        :href="route('profiles.edit', { id: profile.id })"
        @click.stop.prevent="$inertia.visit(route('profiles.edit', { id: profile.id }))"
      >
        <v-icon color="grey darken-2">mdi-pencil-box</v-icon>
      </v-btn>
    </v-col>
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
