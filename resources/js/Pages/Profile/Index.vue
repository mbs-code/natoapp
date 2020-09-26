<template>
  <ContainerLayout>
    <template v-slot:toolbar>
      <v-row no-gutters justify="end">
        <v-btn
          :href="route('profiles.create')"
          @click.stop.prevent="$inertia.visit(route('profiles.create'))"
        >
          <v-icon color="grey darken-2">mdi-plus-box</v-icon>
        </v-btn>
        <!-- <v-btn
          :href="route('profiles.edit', { id: profile.id })"
          @click.stop.prevent="$inertia.visit(route('profiles.edit', { id: profile.id }))"
        >
          <v-icon color="grey darken-2">mdi-pencil-box</v-icon>
        </v-btn> -->
      </v-row>
    </template>

    <v-row justify="center">
      <v-col v-for="profile of profiles" :key="profile.id" cols="12">
        <ProfileCard :profile="profile" :has-link="true" />
      </v-col>
    </v-row>
  </ContainerLayout>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'
import ProfileCard from '@/Components/ProfileCard'

export default {
  layout: [DefaultLayout],

  components: { ContainerLayout, ProfileCard },

  props: {
    profiles: {
      type: Array,
      default: () => [],
    },
  },
}
</script>
