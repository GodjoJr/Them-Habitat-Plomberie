const path = require('path');
let mix = require('laravel-mix');

mix.js('resources/js/app.js', 'assets/js/app.js')
  .sass('resources/scss/app.scss', 'assets/css/app.css')
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
