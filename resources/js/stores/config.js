export default {
  namespaced: true,

  state: {
    profileTableMode: false,
    videoTableMode: false,
  },

  mutations: {
    setProfileTableMode(state, value) {
      state.profileTableMode = value
    },
    setVideoTableMode(state, value) {
      state.videoTableMode = value
    },
  },

  actions: {},

  getters: {
    getProfileTableMode(state) {
      return state.profileTableMode
    },
    getVideoTableMode(state) {
      return state.videoTableMode
    },
  },
}
