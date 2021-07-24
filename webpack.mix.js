const mix = require('laravel-mix');

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

mix.js('resources/js/app.js', 'public/js')
    .copy('node_modules/datatables.net-jqui',
         'public/vendors/datatables.net-jqui')
    .copy('node_modules/datatables.net-responsive/js',
         'public/vendors/datatables.net-responsive/js')
    .copy('node_modules/jquery/dist',
         'public/vendors/jquery/dist')
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('tailwindcss'),
    ]);

if (mix.inProduction()) {
    mix.version();
}
