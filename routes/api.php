<?php

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Model\News;
use App\Model\Topic;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('news', function() {
    // If the Content-Type and Accept headers are set to 'application/json', 
    // this will return a JSON structure. This will be cleaned up later.
    return News::all();
});

Route::post('create_news', 'ApiController@createNews');
Route::post('update_news', 'ApiController@updateNews');
Route::post('delete_news', 'ApiController@deleteNews');
Route::post('filter_news', 'ApiController@filterNews'); // filter news by status
Route::post('search_news', 'ApiController@searchNews'); // search news by topic

Route::get('topic', function() {
    // If the Content-Type and Accept headers are set to 'application/json', 
    // this will return a JSON structure. This will be cleaned up later.
    return Topic::all();
});

Route::post('create_topic', 'ApiController@createTopic');
Route::post('update_topic', 'ApiController@updateTopic');
Route::post('delete_topic', 'ApiController@deleteTopic');

Route::get('test',function(){
    return response([1,2,3,4],200);   
});
