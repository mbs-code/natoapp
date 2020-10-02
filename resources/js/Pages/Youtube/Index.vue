<template>
  <ContainerLayout>
    <template v-slot:toolbar>
      <v-row no-gutters justify="end">
        <v-btn class="mx-1" @click="openEditDialog()">
          <v-icon color="grey darken-2">mdi-plus-box</v-icon>
        </v-btn>
      </v-row>
    </template>

    <v-row justify="center">
      <List :youtubes="youtubes" />
    </v-row>
  </ContainerLayout>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'
import List from './_List'

export default {
  layout: [DefaultLayout],

  components: { ContainerLayout, List },

  props: {
    youtubes: {
      type: Array,
      default: () => [],
    },
  },
}
</script>
