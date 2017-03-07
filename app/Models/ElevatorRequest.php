<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ElevatorRequest extends Model
{
    public static function find_and_add_to_best_elevator($floor, $direction){
    	if(\App\ElevatorRequest::floor_is_in_queue($floor,$direction)){
    		return false;
    	}
		$req = new \App\ElevatorRequest;
		$req->direction = $direction;
		$req->current_floor = $floor;
    	$stand = \App\Elevator::where('direction','stand')->inRandomOrder()->get()->first();
    	if($stand != null){
    		$stand->add_to_queue($floor, $direction);
    		$req->queued_to_elevator = $stand->id;
    		$req->save();
    		return $req;
    	}
    	else{
    		$ltgt = $direction == "down" ? ">=" : "<=";
    		$elevator_dir = \App\Elevator::where('direction', $direction)->where('current_floor', $ltgt, $floor)->orderBy("current_floor")->get()->first();
    		if($elevator_dir != null){
    			$elevator_dir->add_to_queue($floor, $direction);
    			$req->queued_to_elevator = $elevator_dir->id;
    			$req->save();
    			return $req;
    		}
    		else{
    			$els = DB::select('select * from elevators order by length(queue_'.$direction.')');
    			if(!empty($els)){
    				$el = \App\Elevator::find($els[0]->id);
    				$el->add_to_queue($floor, $direction);
    				$req->queued_to_elevator = $el->id;
    				$req->save();
    				return $req;
    			}
    		}
    	}
    }
    public static function remove_request_of($floor, $direction){
    	$er = \App\ElevatorRequest::where('current_floor', $floor)->where('direction', $direction)->first();
    	if($er != null){
    		$er->delete();
    	}
    }
    public static function floor_is_in_queue($floor,$direction){
    	$r = \App\ElevatorRequest::where('current_floor', $floor)->where('direction', $direction)->first();
    	if($r == null){
    		return false;
    	}
    	return true;
    }
}
