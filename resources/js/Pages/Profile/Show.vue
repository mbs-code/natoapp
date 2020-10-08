<template>
  <ContainerLayout>
    <template v-slot:toolbar>
      <v-row no-gutters justify="end">
        <v-btn
          :href="route('profiles.edit', { id: profile.id })"
          @click.stop.prevent="$inertia.visit(route('profiles.edit', { id: profile.id }))"
        >
          <v-icon color="grey darken-2">mdi-pencil-box</v-icon>
        </v-btn>
      </v-row>
    </template>

    <v-row justify="start">
      <v-col cols="12">
        <ProfileCard :profile="profile" />
      </v-col>
      <v-col cols="12">
        <ProfileTabCard :profile="profile" />
      </v-col>
    </v-row>
  </ContainerLayout>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'
import ProfileCard from './_Card'
import ProfileTabCard from './_InfoTabs/_InfoTab'

export default {
  layout: [DefaultLayout],

  components: { ContainerLayout, ProfileCard, ProfileTabCard },

  props: {
    profile: {
      type: Object,
      default: () => {},
    },
  },
}
</script>
