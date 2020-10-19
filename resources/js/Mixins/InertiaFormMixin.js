export default {
  data: function () {
    return {
      form: {},
      errors: {},
    }
  },

  methods: {
    submit: function () {
      const method = (this.form._method || '').toUpperCase()
      const routePrefix = (this.form._route || '').toLowerCase()
      const params = this.form._params || {}

      const routeSuffix = this._methodToSuffix(method)
      const url = `${routePrefix}.${routeSuffix}`
      console.log(url)
      this.form
        .post(route(url, params), {
          preserveScroll: true,
        })
        .then(() => {
          // 取り出したらエラーを消す
          this.errors = this.$page.errors
          this.$page.errors = {}

          if (Object.keys(this.errors).length === 0) {
            this.$emit('success')
          } else {
            this.$emit('error')
          }
        })
    },

    _methodToSuffix: function (method) {
      const upper = (method || '').toUpperCase()
      switch (upper) {
        case 'PUT':
          return 'update'
        case 'POST':
          return 'store'
        case 'DELETE':
          return 'destroy'
        default:
          return 'index'
      }
    },

    reset: function () {
      this.form.reset()
      this.errors = {}
    },
  },
}
