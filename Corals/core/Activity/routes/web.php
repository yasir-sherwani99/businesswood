<?php

Route::group(['prefix' => ''], function () {
    Route::post('activities/bulk-action', 'ActivitiesController@bulkAction');
    Route::resource('activities', 'ActivitiesController', ['only' => ['index', 'show', 'destroy']]);
});
