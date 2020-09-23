<template>
  <v-row justify="center">
    <v-col v-for="profile of profiles" :key="profile.id" cols="auto">
      <ProfileCard :profile="profile" :has-link="true" />
    </v-col>
  </v-row>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ProfileCard from '@/Components/ProfileCard'

export default {
  components: { ProfileCard },

  layout: DefaultLayout,

  props: {
    profiles: {
      type: Array,
      default: () => [],
    },
  },
}
</script>
