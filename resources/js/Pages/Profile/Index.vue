<template>
  <v-row justify="center">
    <v-col v-for="profile of profiles" :key="profile.id" cols="12">
      <ProfileCard :profile="profile" :has-link="true" />
    </v-col>
  </v-row>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'
import ProfileCard from '@/Components/ProfileCard'

export default {
  layout: [DefaultLayout, ContainerLayout],

  components: { ProfileCard },

  props: {
    profiles: {
      type: Array,
      default: () => [],
    },
  },
}
</script>
