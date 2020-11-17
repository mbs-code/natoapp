<template>
  <ContainerLayout v-resize="onResize">
    <template v-slot:toolbar>
      <v-row no-gutters justify="end">
        <SwitchButton
          v-model="videoTableMode"
          class="mx-1"
        />
        <v-btn class="mx-1" @click="$refs.list.openEditDialog()">
          <v-icon color="grey darken-2">mdi-plus-box</v-icon>
        </v-btn>
      </v-row>
    </template>

    <v-row justify="center" no-gutters>
      <template v-if="videoTableMode">
        <VideoList
          ref="list"
          :videos="videos"
          :server-items-length="total"
          :height="innerHeight"
          :loading="loading"
          @change="getDataFromApi"
        />
      </template>
      <template v-else>
        <v-col v-for="video of videos" :key="video.id" cols="12">
          <VideoCard :video="video" card-link="true" />
        </v-col>
      </template>
    </v-row>
  </ContainerLayout>
</template>

<script>
import DefaultLayout from '@/Layouts/DefaultLayout'
import ContainerLayout from '@/Layouts/ContainerLayout'
import SwitchButton from '@/Components/CommonParts/SwitchButton'
import VideoList from './_List'
import VideoCard from './_Card'

import { get as dataGet } from 'dot-prop'

export default {
  layout: [DefaultLayout],

  components: { ContainerLayout, SwitchButton, VideoList, VideoCard },

  data: function () {
    return {
      loading: false,
      videos: [],
      total: 0,
      innerHeight: 0,
    }
  },

  computed: {
    videoTableMode: {
      get() {
        return this.$store.getters['config/getVideoTableMode']
      },
      set(value) {
        this.$store.commit('config/setVideoTableMode', value)
      },
    },
  },

  mounted: async function () {
    this.onResize()
    this.getDataFromApi()
  },

  methods: {
    onResize: function () {
      this.innerHeight = parseInt(window.innerHeight) - 184 // とりあえずハードコート
    },

    getDataFromApi: async function (options = {}) {
      this.loading = true

      console.log(options)

      const sort = dataGet(options, 'sortBy.0') || ''
      const order = dataGet(options, 'sortDesc.0') ? 'desc' : 'asc'

      const { data } = await this.$http.get(route('api.videos'), {
        params: {
          sort: sort,
          order: order,
          page: options.page || 1,
          perPage: options.itemsPerPage || 10,
        },
      })

      this.videos = data.data
      this.total = data.total
      this.loading = false
    },
  },
}
</script>
