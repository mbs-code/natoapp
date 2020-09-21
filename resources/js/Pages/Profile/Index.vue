<template>
  <v-row justify="center">
    <v-col v-for="profile of profiles" :key="profile.id" cols="auto">
      <ProfileCard :profile="profile" />
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
    profiles: {
      type: Array,
      default: () => [],
    },
  },
}
</script>
