<template>
  <ContainerLayout>
    <template v-slot:toolbar>
      <v-row no-gutters justify="end">
        <SwitchButton
          v-model="profileTableMode"
          class="mx-1"
        />
        <v-btn class="mx-1" @click="$refs.list.openEditDialog()">
          <v-icon color="grey darken-2">mdi-plus-box</v-icon>
        </v-btn>
      </v-row>
    </template>

    <v-row justify="center">
      <template v-if="profileTableMode">
        <ProfileList ref="list" :profiles="profiles" />
      </template>
      <template v-else>
        <v-col v-for="profile of profiles" :key="profile.id" cols="12">
          <ProfileCard :profile="profile" card-link="true" />
        </v-col>
      </template>
    </v-row>
  </ContainerLayout>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'
import SwitchButton from '@/Components/CommonParts/SwitchButton'
import ProfileList from './_List'
import ProfileCard from './_Card'

export default {
  layout: [DefaultLayout],

  components: { ContainerLayout, SwitchButton, ProfileList, ProfileCard },

  props: {
    profiles: {
      type: Array,
      default: () => [],
    },
  },

  computed: {
    profileTableMode: {
      get() {
        return this.$store.getters['config/getProfileTableMode']
      },
      set(value) {
        this.$store.commit('config/setProfileTableMode', value)
      },
    },
  },
}
</script>
