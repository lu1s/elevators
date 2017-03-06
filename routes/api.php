<?php

use Illuminate\Http\Request;

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

Route::get('/elevators', function(Request $request) {
	return \App\Elevator::all();
});

Route::get('/elevators/get/{id}', function($id) {
	$el = \App\Elevator::find($id);
	if($el == null){
		return "not found";
	}
	$b = $el->beautify_queue();
	return Array('elevator' => $el, 'beautified_queue' => $b);
});
Route::get('/elevators/add_to_queue/{id}/{floor}', function($id, $floor) {
	$ele = \App\Elevator::find($id);
	$ele->add_to_queue($floor);
	return $ele;
});
Route::get('/elevators/go_to_next_floor/{id}', function($id) {
	$ele = \App\Elevator::find($id);
	$ele->go_to_next_floor();
	return $ele;
});
Route::get('/elevators/add_new_floor_request/{floor}/{direction}', function($floor, $direction) {
	return \App\ElevatorRequest::find_and_add_to_best_elevator($floor, $direction);
});
Route::get('/elevators/get_queued_floors', function(){
	return \App\ElevatorRequest::all();
});
