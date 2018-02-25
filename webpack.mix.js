let mix = require('laravel-mix');

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

mix
    // Compilar asset JS
    .js('resources/assets/js/app.js', 'public/js')

    // Compilar asset SCSS
    .sass('resources/assets/sass/app.scss', 'public/css')

    // Unificar CSS
    .styles([
     //'public/slider/css/style.css',
     'public/css/app.css'
    ], 'public/css/app.css')

    // Unificar JS
    .scripts([
     //'public/js/jquery.js',
     //'public/js/main.js',
     //'public/slider/js/main.js',
     //'public/js/bootstrap.min.js',
     'public/js/app.js'
    ], 'public/js/app.js')