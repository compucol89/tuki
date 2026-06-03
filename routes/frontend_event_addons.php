<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas de Add-ons por Evento
|--------------------------------------------------------------------------
| Endpoints públicos para que el cliente agregue add-ons al carrito
| y endpoints privados para que organizer/admin gestionen secciones y
| add-ons por evento. El lock de concurrencia y la validación de stock
| se aplican dentro de los controllers (lockForUpdate en transacción).
*/

// Público: cliente agrega add-ons al carrito del evento
Route::middleware('web')->prefix('event/{event}/addons')->group(function () {
  Route::post('add',    [App\Http\Controllers\FrontEnd\Event\EventAddonController::class, 'addToCart'])->name('event.addon.add');
  Route::post('remove', [App\Http\Controllers\FrontEnd\Event\EventAddonController::class, 'removeFromCart'])->name('event.addon.remove');
  Route::post('update', [App\Http\Controllers\FrontEnd\Event\EventAddonController::class, 'updateCart'])->name('event.addon.update');
  Route::post('update-ajax', [App\Http\Controllers\FrontEnd\Event\EventAddonController::class, 'updateCartAjax'])->name('event.addon.update-ajax');
});

// Organizer: CRUD de add-ons por evento
Route::middleware(['auth:organizer', 'admin.locale', 'Deactive:organizer', 'EmailStatus:organizer'])
  ->prefix('organizer/event/{event}/addons')->group(function () {
    Route::get('/',                                [App\Http\Controllers\BackEnd\Event\EventAddonManagementController::class, 'index'])->name('organizer.event.addons.index');
    Route::post('section',                         [App\Http\Controllers\BackEnd\Event\EventAddonManagementController::class, 'storeSection'])->name('organizer.event.addons.section.store');
    Route::put('section/{section}',                [App\Http\Controllers\BackEnd\Event\EventAddonManagementController::class, 'updateSection'])->name('organizer.event.addons.section.update');
    Route::delete('section/{section}',             [App\Http\Controllers\BackEnd\Event\EventAddonManagementController::class, 'destroySection'])->name('organizer.event.addons.section.destroy');
    Route::post('addon',                           [App\Http\Controllers\BackEnd\Event\EventAddonManagementController::class, 'storeAddon'])->name('organizer.event.addons.addon.store');
    Route::put('addon/{addon}',                    [App\Http\Controllers\BackEnd\Event\EventAddonManagementController::class, 'updateAddon'])->name('organizer.event.addons.addon.update');
    Route::delete('addon/{addon}',                 [App\Http\Controllers\BackEnd\Event\EventAddonManagementController::class, 'destroyAddon'])->name('organizer.event.addons.addon.destroy');
    Route::post('image-upload',                    [App\Http\Controllers\BackEnd\Event\EventAddonManagementController::class, 'uploadImage'])->name('organizer.event.addons.image.upload');
  });

// Admin: misma estructura con guard:admin
Route::middleware(['auth:admin', 'admin.locale'])
  ->prefix('admin/event/{event}/addons')->group(function () {
    Route::get('/',                                [App\Http\Controllers\BackEnd\Event\EventAddonManagementController::class, 'index'])->name('admin.event.addons.index');
    Route::post('section',                         [App\Http\Controllers\BackEnd\Event\EventAddonManagementController::class, 'storeSection'])->name('admin.event.addons.section.store');
    Route::put('section/{section}',                [App\Http\Controllers\BackEnd\Event\EventAddonManagementController::class, 'updateSection'])->name('admin.event.addons.section.update');
    Route::delete('section/{section}',             [App\Http\Controllers\BackEnd\Event\EventAddonManagementController::class, 'destroySection'])->name('admin.event.addons.section.destroy');
    Route::post('addon',                           [App\Http\Controllers\BackEnd\Event\EventAddonManagementController::class, 'storeAddon'])->name('admin.event.addons.addon.store');
    Route::put('addon/{addon}',                    [App\Http\Controllers\BackEnd\Event\EventAddonManagementController::class, 'updateAddon'])->name('admin.event.addons.addon.update');
    Route::delete('addon/{addon}',                 [App\Http\Controllers\BackEnd\Event\EventAddonManagementController::class, 'destroyAddon'])->name('admin.event.addons.addon.destroy');
    Route::post('image-upload',                    [App\Http\Controllers\BackEnd\Event\EventAddonManagementController::class, 'uploadImage'])->name('admin.event.addons.image.upload');
  });
