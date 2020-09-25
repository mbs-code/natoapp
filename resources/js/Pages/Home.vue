<template>
  <v-row align="center" justify="center">
    <v-col cols="12" class="text-center">test</v-col>
    <v-col cols="12" class="text-center">
      <ToastTestButton />
    </v-col>
  </v-row>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'
import ToastTestButton from '@/Components/Debugs/ToastTestButton'

export default {
  layout: [DefaultLayout, ContainerLayout],

  components: { ToastTestButton },
}
</script>
