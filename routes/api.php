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
Route::get('/elevators/add_to_queue/{id}/{floor}/{direction}', function($id, $floor, $direction) {
	$ele = \App\Elevator::find($id);
	if($direction == "down"){
		$ele->add_to_queue_down($floor);
	}
	else{
		$ele->add_to_queue_up($floor);
	}
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
Route::get('/elevators/delete_request/{floor}/{direction}', function($floor, $direction){
	$req = \App\ElevatorRequest::where('current_floor', $floor)->where('direction', $direction)->get()->first();
	if($req != null){
		$req->delete();
		return "yes";
	}
	return "nop";
});

Route::get('/simplyrets/load_data/{id}', function($id){
    $url = 'https://api.simplyrets.com/properties/'.urlencode($id);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // curl_setopt($ch, CURLOPT_USERPWD, "simplyrets:simplyrets");
    // curl_setopt($ch, CURLOPT_USERPWD, "642882:heygo2navy1");
    curl_setopt($ch, CURLOPT_USERPWD, "657116:gohokies4");
    $out = curl_exec($ch);
    curl_close($ch);
    return array('success'=>true, 'data'=>json_decode($out));
});