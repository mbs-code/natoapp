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
      <!-- <template v-if="buttonMode"> -->
        <List ref="list" :videos="videos" />
      <!-- </template> -->
      <!-- <template v-else> -->
        <!-- <v-col v-for="video of videos" :key="video.id" cols="12">
          <div>{{ video.title }}</div> -->
          <!-- <ProfileCard :profile="profile" card-link="true" /> -->
        <!-- </v-col> -->
      <!-- </template> -->
    </v-row>
  </ContainerLayout>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'
import SwitchButton from '@/Components/CommonParts/SwitchButton'
// import ProfileCard from '@/Components/ProfileCard'
import List from './_List'

export default {
  layout: [DefaultLayout],

  components: { ContainerLayout, SwitchButton, List },

  props: {
    videos: {
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
