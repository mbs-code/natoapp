// @see https://github.com/meteorlxy/eslint-plugin-prettier-vue
module.exports = {
  extends: [
    'plugin:vue/recommended',
    'plugin:prettier-vue/recommended',
    // Do not add `'prettier/vue'` if you don't want to use prettier for `<template>` blocks
    // 'prettier/vue',
  ],

  settings: {
    'prettier-vue': {
      SFCBlocks: {
        // If set to `false`, remember not to `extends: ['prettier/vue']`,
        // as you need the rules from `eslint-plugin-vue` to lint `<template>` blocks
        template: false,
        script: true,
        style: true,
        customBlocks: {
          docs: { lang: 'markdown' },
          config: { lang: 'json' },
          module: { lang: 'js' },
          comments: false,
        },
      },

      usePrettierrc: true,
    },
  },

  rules: {
    'prettier-vue/prettier': ['error'],

    // html の attribute について
    'vue/max-attributes-per-line': [
      'error',
      {
        singleline: 4,
        multiline: {
          max: 1,
          allowFirstLine: false,
        },
      },
    ],

    // 一行で書かれている要素を改行する
    'vue/singleline-html-element-content-newline': 'off',
  },
}
