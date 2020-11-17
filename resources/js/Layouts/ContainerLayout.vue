<template>
  <v-row no-gutters class="flex-column fill-height">
    <!-- toolbar -->
    <v-col class="flex-grow-0">
      <v-sheet
        v-if="this.$slots.toolbar"
        class="pa-2"
        elevation="2"
        color="green lighten-5"
        tile
      >
        <slot name="toolbar" />
      </v-sheet>
    </v-col>
    <!-- end toolbar -->

    <v-col class="flex-grow-1">
      <v-container fluid class="flex flex-column fill-height flex-grow-1">
        <slot />
      </v-container>
    </v-col>
  </v-row>
</template>

<script>
export default {}
</script>
