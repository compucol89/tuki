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
  // Font Awesome self-hosted — font-display: block nativo de FA6 (sin FOUT:
  // el glifo permanece oculto hasta que la fuente está lista, sin "salto"
  // de cuadrado-vacío → icono en cada navegación).
  .postCss('resources/css/fontawesome.css', 'public/css', [
    require('cssnano')({ preset: 'default' })
  ])
  .copy('node_modules/@fortawesome/fontawesome-free/webfonts', 'public/webfonts')
  .version();
