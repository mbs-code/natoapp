<template>
  <div>
    <!-- toolbar -->
    <v-sheet
      v-if="this.$slots.toolbar"
      elevation="2"
      class="pa-2"
      color="green lighten-5"
      tile
    >
      <slot name="toolbar" />
    </v-sheet>
    <!-- end toolbar -->

    <v-container fluid>
      <slot />
    </v-container>
  </div>
</template>

<script>
export default {}
</script>
