<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Elevator extends Model
{
    public function beautify_queue() {
    	if(empty($this->queue)){
    		return "No queue";
    	}
    	$queue = explode(",",$this->queue);
    	$current = $this->current_floor;
    	$direction = $this->direction;
    	if($direction == "down"){
    		rsort($queue);
    	}
    	else{
    		sort($queue);
    	}
    	$out = $direction.": ";
    	foreach($queue as $req){
    		if($req == $current){
    			$out.= "[[".$req."]]";
    		}
    		else{
    			$out.= $req."";
    		}
    		if($req != last($queue)){
    			$out.= ", ";
    		}
    	}
    	return $out;
    }
    public function add_to_queue($floor){
    	if(!empty($this->queue)){
    		$queue = explode(",",$this->queue);
    		if(in_array($floor, $queue)){
    			return;
    		}
    		array_push($queue, $floor);
    		$queue = implode(",", $queue);
    		$this->queue = $queue;
    		$this->save();

    	}
    	else{
    		$this->queue = $floor;
    		if($floor == $this->current_floor){
    			return;
    		}
    		else if($floor > $this->current_floor){
    			$this->direction = "up";
    		}
    		else{
    			$this->direction = "down";
    		}
    		$this->save();
    	}
    	return $this;
    }
    public function remove_from_queue($floor){
    	if(!empty($this->queue)){
    		$queue = explode(",", $this->queue);
    		if(in_array($floor, $queue)){
    			unset($queue[array_search($floor, $queue)]);
    			$this->queue = implode(",",$queue);
    			$this->save();
    		}
    	}
    	return $this;
    }
    public function go_to_next_floor(){
    	if(empty($this->queue)){
    		$this->direction = "stand";

    		$this->save();
    		return $this;
    	}
    	$current_floor = $this->current_floor;
    	$queue = explode(",", $this->queue);
    	sort($queue);
    	if(sizeof($queue) == 1){
    		$dir = $this->direction;
    		$is_one_matching = \App\ElevatorRequest::where('current_floor', $this->current_floor)->where('queued_to_elevator', $this->id)->get()->first();
    		if($is_one_matching != null){
    			$is_one_matching->delete();
    		}
    		$this->direction = "stand";
    		$this->current_floor = $queue[0];
    		$this->queue = "";
    		$this->save();
    		return $this;
    	}
    	else if($this->direction == "up"){
    		if($current_floor > max($queue)){
    			$this->direction = "down";
    			$this->current_floor = max($queue);
    			unset($queue[array_search(max($queue), $queue)]);
    			$this->queue = implode(",",$queue);
    			$this->save();
    		$is_one_matching = \App\ElevatorRequest::where('current_floor', $this->current_floor)->where('queued_to_elevator', $this->id)->get()->first();
    		if($is_one_matching != null){
    			$is_one_matching->delete();
    		}
    			return $this;
    		}
    		else if(array_search($current_floor, $queue) == (count($queue)-1)){
    			$this->direction = "down";
    		}
    		else if(array_search($current_floor, $queue) != false){
    			$this->current_floor = $queue[array_search($current_floor, $queue)+1];
    			unset($queue[array_search($current_floor, $queue)]);
    			$this->queue = implode(",",$queue);
	    		$is_one_matching = \App\ElevatorRequest::where('current_floor', $this->current_floor)->where('queued_to_elevator', $this->id)->get()->first();
	    		if($is_one_matching != null){
	    			$is_one_matching->delete();
	    		}
    			$this->save();
    		}
    		else{
    			foreach($queue as $q){
    				if($q > $this->current_floor){
    					$this->current_floor = $q;
    					unset($queue[array_search($q,$queue)]);
    					$this->queue = implode(",",$queue);
    					$this->save();
			    		$is_one_matching = \App\ElevatorRequest::where('current_floor', $this->current_floor)->where('queued_to_elevator', $this->id)->get()->first();
			    		if($is_one_matching != null){
			    			$is_one_matching->delete();
			    		}
    					return $this;
    				}
    			}
    		}
    	}
    	if($this->direction == "down"){
    		if(array_search($current_floor, $queue) == 0){
    			if(count($queue) > 1){
	    			$this->direction = "up";
	    			$this->current_floor = $queue[1];
	    			\App\ElevatorRequest::remove_request_of($this->current_floor, "down");
    			}
    			else{
    				$this->direction = "stand";
    			}
    			unset($queue[0]);
    			$this->queue = implode(",",$queue);
    			$this->save();
    		}
    		else{
    			$this->current_floor = $queue[array_search($current_floor, $queue) - 1];
    			unset($queue[array_search($current_floor, $queue)]);
    			$this->queue = implode(",",$queue);
    			$this->save();
    			\App\ElevatorRequest::remove_request_of($this->current_floor, "down");
    		}
    	}
    	if(empty($this->queue)){
    		$this->direction = "stand";
    		$this->save();
    	}
    	return $this;
    }
    public function floor_is_in_queue($floor, $direction){
    	if(empty($this->queue)){
    		return false;
    	}
    	$q = explode(",",$this->queue);
    	return (array_search($floor, $q) != false && $this->direction == $direction);
    }
}