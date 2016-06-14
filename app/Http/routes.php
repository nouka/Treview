<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// トップページ
Route::get('/', function () {
    return view('welcome');
});

// レビュー参加者の集計
Route::get('/board/assigner/key/{key}/token/{token}/board/{id}', 'BoardsController@assigner');

// バーンダウンチャートの表示
Route::get('/board/burndown', 'BoardsController@burndown');

// テスト
Route::get('/test', function() {
    return '<a href="http://rc.hapitas.jp">http://rc.hapitas.jp</a>';
});

