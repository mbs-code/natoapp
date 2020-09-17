<template>
  <v-row align="center" justify="center">
    <v-col class="text-center">
      <v-tooltip left>
        <template #activator="{ on }">
          <v-btn :href="source" icon large target="_blank" v-on="on">
            <v-icon large>mdi-code-tags</v-icon>
          </v-btn>
        </template>
        <span>Source</span>
      </v-tooltip>
    </v-col>
  </v-row>
</template>

<script>
// import AppLayout from './../Layouts/AppLayout'
// import Welcome from './../Jetstream/Welcome'
import DefaultLayout from '../Layouts/DefaultLayout'

export default {
  layout: DefaultLayout,

  // components: {
  //     AppLayout,
  //     Welcome,
  // },
}
</script>
