<?php
Route::group(['prefix' => "admin"], function () {
    Route::group(['middleware' => ['web', 'auth']], function () {
        Route::post('store', '\marcusvbda\vstack\Controllers\ResourceController@store')->name("resource.store");
        Route::get('{resource}', '\marcusvbda\vstack\Controllers\ResourceController@index')->name("resource.index");
        Route::get('{resource}/create', '\marcusvbda\vstack\Controllers\ResourceController@create')->name("resource.create");
        Route::get('{resource}/export', '\marcusvbda\vstack\Controllers\ResourceController@export')->name("resource.export");
        Route::get('{resource}/import', '\marcusvbda\vstack\Controllers\ResourceController@import')->name("resource.import");
        Route::post('{resource}/import/check_file', '\marcusvbda\vstack\Controllers\ResourceController@checkFileImport')->name("resource.import.check_file");
        Route::post('{resource}/import/submit', '\marcusvbda\vstack\Controllers\ResourceController@importSubmit')->name("resource.import.submit");

        Route::get('{resource}/custom-cards', '\marcusvbda\vstack\Controllers\ResourceController@customCard')->name("resource.customcard");
        Route::get('{resource}/custom-cards/create', '\marcusvbda\vstack\Controllers\ResourceController@customCardCreate')->name("resource.customcard.create");
        Route::post('{resource}/custom-cards/store', '\marcusvbda\vstack\Controllers\ResourceController@customCardStore')->name("resource.customcard.store");
        Route::delete('{resource}/custom-cards/{code}/destroy', '\marcusvbda\vstack\Controllers\ResourceController@customCardDestroy')->middleware(['hashids:code'])->name("resource.customcard.destroy");
        Route::get('{resource}/custom-cards/{code}/edit', '\marcusvbda\vstack\Controllers\ResourceController@customCardEdit')->middleware(['hashids:code'])->name("resource.customcard.edit");
        Route::post('{resource}/custom-metric-calculate/{code}', '\marcusvbda\vstack\Controllers\ResourceController@customMetricCalculate')->middleware(['hashids:code'])->name("resource.customcard.calculate");

        Route::get('{resource}/{code}', '\marcusvbda\vstack\Controllers\ResourceController@view')->middleware(['hashids:code'])->name("resource.view");
        Route::get('{resource}/{code}/edit', '\marcusvbda\vstack\Controllers\ResourceController@edit')->middleware(['hashids:code'])->name("resource.edit");
        Route::delete('{resource}/{code}/destroy', '\marcusvbda\vstack\Controllers\ResourceController@destroy')->middleware(['hashids:code'])->name("resource.destroy");
        Route::post('inputs/option_list', '\marcusvbda\vstack\Controllers\ResourceController@option_list')->name("resource.inputs.option_list");
        Route::post('globalsearch', '\marcusvbda\vstack\Controllers\ResourceController@globalSearch')->name("resource.globalsearch");
        Route::post('{resource}/metric-calculate/{key}', '\marcusvbda\vstack\Controllers\ResourceController@metricCalculate')->name("resource.metricCalculate");
    });
});
