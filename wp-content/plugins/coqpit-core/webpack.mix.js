const mix = require('laravel-mix');
const webpack = require('webpack')

mix.js('resources/js/app.js', 'assets/js/coqpit-core.js').vue({ version: 3 })
  .sass('resources/scss/app.scss', 'assets/css/coqpit-core.css')
  .sass('resources/scss/admin.scss', 'assets/css/coqpit-core-admin.css')
  .options({
    processCssUrls: false,
    postCss: [
      require('autoprefixer')
    ]
  })
  .setPublicPath('/')
  .disableNotifications();

mix.webpackConfig({
  plugins: [
    new webpack.DefinePlugin({
      __VUE_PROD_DEVTOOLS__: false,
      __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: false,
    }),
  ],
  module: {
    rules: [
      {
        test: /\.scss$/,
        loader: 'glob-import-loader'
      },
      {
        test: /\.scss$/,
        loader: "sass-loader",
        options: {
          additionalData: `@import "resources/scss/tools/_variables.scss";`
        }
      }
    ]
  }
});
