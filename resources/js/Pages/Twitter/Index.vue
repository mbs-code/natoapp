<template>
  <ContainerLayout>
    <template v-slot:toolbar>
      <v-row no-gutters justify="end">
        <v-btn class="mx-1" @click="$refs.list.openEditDialog()">
          <v-icon color="grey darken-2">mdi-plus-box</v-icon>
        </v-btn>
      </v-row>
    </template>

    <v-row justify="center">
      <TwitterList ref="list" :twitters="twitters" />
    </v-row>
  </ContainerLayout>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'
import TwitterList from './_List'

export default {
  layout: [DefaultLayout],

  components: { ContainerLayout, TwitterList },

  props: {
    twitters: {
      type: Array,
      default: () => [],
    },
  },
}
</script>
