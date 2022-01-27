const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.disableNotifications();

mix.options({
    hmrOptions: {
        host: 'localhost',
        port: '8080'
    },
});

mix.js('resources/js/app.js', 'public/js')
    .vue({
        extractStyles: true,
        globalStyles: `resources/sass/_media.scss`
    })
    .sass('resources/sass/app.scss', 'public/css');

