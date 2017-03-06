<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class ElevatorQueueController extends Controller
{

    public function index() {
        $elevators = \App\Elevator::all();
        $elevator_requests = \App\ElevatorRequest::all();
        $floor_requests_arr_down = [];
        $floor_requests_arr_up = [];
        foreach($elevator_requests as $er){
            if($er->direction == "down"){
                array_push($floor_requests_arr_down, $er->current_floor);
            }
            else{
                array_push($floor_requests_arr_up, $er->current_floor);
            }  
        }
        return view('ElevatorQueue.index', ['elevators' => $elevators, 'elevator_requests' => $elevator_requests, 'floor_requests_arr_down' => $floor_requests_arr_down, 'floor_requests_arr_up' => $floor_requests_arr_up]);
    }
}
