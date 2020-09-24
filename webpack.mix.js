/* eslint-disable prettier-vue/prettier */

const mix = require('laravel-mix')
require('laravel-mix-alias')
const VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.webpackConfig({
  plugins: [
    new VuetifyLoaderPlugin(),
  ],
});

mix.alias({
  '@': '/resources/js',
  '~': '/resources/sass',
})

mix
  .js('resources/js/app.js', 'public/js')
  .sass('resources/css/app.scss', 'public/css')
  // .postCss('resources/css/app.scss', 'public/css', [require('postcss-import'), require('tailwindcss')])
