const path = require('path');
let mix = require('laravel-mix');

mix.js('resources/js/thp-core.js', 'assets/js/thp-core.js')
  .sass('resources/scss/thp-core.scss', 'assets/css/thp-core.css')
  .options({
    processCssUrls: false,
    postCss: [
      require('autoprefixer')
    ]
  })
  .setPublicPath('/')
  .disableNotifications();

mix.webpackConfig({
  module: {
    rules: [
      {
        test: /\.scss$/,
        use: [
          { loader: 'glob-import-loader' },
          { loader: 'sass-loader', options: { implementation: require('sass') } }
        ]
      }
    ]
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources/scss')
    }
  }
});
