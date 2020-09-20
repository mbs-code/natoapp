<template>
  <v-row justify="center">
    <v-col v-for="twitter of twitters" :key="twitter.id" cols="auto">
      <TwitterCard :twitter="twitter" />
    </v-col>
  </v-row>
</template>

<script>
import DefaultLayout from '../../Layouts/DefaultLayout'
import TwitterCard from '../../Components/TwitterCard'

export default {
  layout: DefaultLayout,

  components: { TwitterCard },

  props: {
    twitters: {
      type: Array,
      default: () => [],
    },
  },
}
</script>
