<?php
/** WORK */
Route::get('/createMeters/{count}', ['as' => 'create.meters', 'uses' => 'WorkController@createMeters']);
Route::get('/createQueries/{count}', ['as' => 'create.queries', 'uses' => 'WorkController@createQueries']);
Route::get('/dropValueTables', ['as' => 'backend.value.tables', 'uses' => 'WorkController@dropValuesTables']);
Route::get('/createValuesTables/{count}/{filling?}', ['as' => 'create.value.tables', 'uses' => 'WorkController@createValuesTables']);

Route::get('/{operator}/{request}', ['as' => 'backend', 'uses' => 'Bootstrap@index']);