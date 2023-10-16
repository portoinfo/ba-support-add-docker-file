const mix = require('laravel-mix');

require('laravel-mix-workbox');
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

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/client-app.js', 'public/js')
    .js('resources/js/admin-app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .sass('resources/sass/client/main.scss', 'public/css')
    .js('resources/js/checkout/support.js', 'public/js/checkout')
    .js('resources/js/api/app.js', 'public/js/api')
    .sass('resources/sass/checkout/support.scss', 'public/css/checkout')
    .options({
        processCssUrls: false
    })
    .generateSW({
        mode: 'production',
        maximumFileSizeToCacheInBytes: 30000000,
        exclude: ['/js/checkout', '/js/api', '/sass/checkout']
    })
    .version();