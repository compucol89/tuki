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
  .postCss('resources/css/app.css', 'public/css', [
    require('cssnano')({ preset: 'default' })
  ])
  // CSS por página
  .copy('public/assets/front/css/style-base.css', 'public/css/style-base.css')
  .copy('public/assets/front/css/home.css', 'public/css/home.css')
  .copy('public/assets/front/css/events.css', 'public/css/events.css')
  .copy('public/assets/front/css/detail-event.css', 'public/css/detail-event.css')
  .copy('public/assets/front/css/shop.css', 'public/css/shop.css')
  .copy('public/assets/front/css/blog.css', 'public/css/blog.css')
  .copy('public/assets/front/css/checkout.css', 'public/css/checkout.css')
  .version();
