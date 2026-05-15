<?php

use Illuminate\Support\Facades\Route;

/*
| Sobre nosotros: debe declararse *antes* de /{slug}. Si no, "sobre-nosotros" matchea el
| parámetro {slug} y PageController hace firstOrFail() en CMS → 404.
*/
Route::permanentRedirect('/about-us', '/sobre-nosotros');
Route::get('/sobre-nosotros', 'FrontEnd\HomeController@about')->name('about');

Route::get('/{slug}', 'FrontEnd\PageController@page')
  ->name('dynamic_page')
  ->where('slug', '^(?!sobre-nosotros$)[^/]+$');

Route::fallback(function () {
  return view('errors.404');
});
