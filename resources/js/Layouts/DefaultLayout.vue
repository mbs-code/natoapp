<template>
  <v-app id="app">
    <!-- Left Sidebar -->
    <v-navigation-drawer v-model="drawer" app clipped mobile-breakpoint="640">
      <v-list dense>
        <!-- aリンクを無効にしてclickでinertiaイベントを呼び出す -->
        <!-- class を上手く書きたい -->
        <v-list-item
          v-for="link in links"
          :key="link.icon"
          link
          color="green darken-3"
          :class="{ 'v-list-item--active': route().current().startsWith(link.route.split('.')[0]) }"
          :href="route(link.route)"
          @click.stop.prevent="$inertia.visit(route(link.route))"
        >
          <v-list-item-action>
            <v-icon>{{ link.icon }}</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>{{ link.text }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <!-- Top Appbar -->
    <v-app-bar
      app
      color="green"
      clipped-left
      dense
      dark
    >
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
      <v-toolbar-title>natoapp</v-toolbar-title>
    </v-app-bar>

    <!-- Page Content -->
    <v-main>
      <v-container fluid>
        <slot />
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
export default {
  data: function () {
    return {
      drawer: true,
      links: [
        { icon: 'mdi-home', text: 'Home', route: 'home' },
        { icon: 'mdi-card-account-details', text: 'Profile', route: 'profiles.index' },
        { icon: 'mdi-twitter', text: 'Twitter', route: 'twitter' },
        { icon: 'mdi-youtube', text: 'Youtube', route: 'youtube' },
      ],
      snackbar: true,
    }
  },

  updated: function () {
    // message flash
    // ref: https://readouble.com/laravel/8.x/ja/session.html
    // 定義は App/Helpers/Helper::messageFlash()
    const toasts = this.$page.flash.toasts
    if (toasts) {
      for (const item of toasts) {
        const type = item.type || 'info'
        const text = item.text || '!!! no-text !!!'

        const toast = this.$toast[type]
        if (!toast) {
          throw new ReferenceError(`"${type}" toast is not defined.`)
        }
        toast(text)
      }
    }
  },
}
</script>
