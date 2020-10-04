<template>
  <ContainerLayout>
    <template v-slot:toolbar>
      <v-row no-gutters justify="end">
        <SwitchButton
          v-model="buttonMode"
          class="mx-1"
        />
        <v-btn class="mx-1" @click="$refs.list.openEditDialog()">
          <v-icon color="grey darken-2">mdi-plus-box</v-icon>
        </v-btn>
      </v-row>
    </template>

    <v-row justify="center">
      <template v-if="buttonMode">
        <List ref="list" :profiles="profiles" />
      </template>
      <template v-else>
        <v-col v-for="profile of profiles" :key="profile.id" cols="12">
          <ProfileCard :profile="profile" />
        </v-col>
      </template>
    </v-row>
  </ContainerLayout>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'
import SwitchButton from '@/Components/CommonParts/SwitchButton'
import ProfileCard from '@/Components/ProfileCard'
import List from './_List'

export default {
  layout: [DefaultLayout],

  components: { ContainerLayout, SwitchButton, List, ProfileCard },

  props: {
    profiles: {
      type: Array,
      default: () => [],
    },
  },

  data: function () {
    return {
      buttonMode: true, // true で card のリンクを button にする
    }
  },
}
</script>
