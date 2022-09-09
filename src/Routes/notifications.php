<?php

use marcusvbda\vstack\Controllers\NotificationsController;

Route::group(['prefix' => "admin"], function () {
    Route::group(['prefix' => "vstack"], function () {
        Route::group(['middleware' => ['web', 'auth']], function () {
            Route::group(['prefix' => "notifications"], function () {
                Route::post('{user}', [NotificationsController::class, 'get'])->middleware(['hashids:user', 'bindings'])->name("notifications.get");
                Route::delete('{user}/{id}/destroy', [NotificationsController::class, 'destroy'])->middleware(['hashids:user', 'bindings'])->name("notifications.destroy");
            });
        });
    });
});
