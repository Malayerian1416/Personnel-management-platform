const mix = require('laravel-mix');
mix.webpackConfig({
    stats: {
        children: true,
    },
});
mix.browserSync({
    proxy: 'http://127.0.0.1:8000/',
    injectChanges: true,
    files: ['public_html/**/*.css', 'resources/**/*']
});
mix.setPublicPath('public_html/');
mix.setResourceRoot('../');
mix.js('resources/js/app.js', 'js')
    .vue()
    .sass('resources/sass/app.scss', 'css').css("resources/css/app.css","css");
