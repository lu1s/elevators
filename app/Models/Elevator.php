<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Elevator extends Model
{
	const MAX_FLOORS = 15;
    public function beautify_queue() {
    	if($this->queue_down == "" && $this->queue_up == ""){
    		return "No queue";
    	}
    	$queue_up = !(strlen($this->queue_up) == 0) ? explode(",",$this->queue_up) : [];
    	$queue_down = !(strlen($this->queue_down) == 0) ? explode(",",$this->queue_down) : [];
    	$current = $this->current_floor;
    	$direction = $this->direction;
    	$out = "Up: ";
    	if(empty($queue_up)){
    		$out.= " none";
    	}
    	foreach($queue_up as $req){
    		$out.= (string)$req;
    		if($req != last($queue_up)){
    			$out.= ", ";
    		}
    	}
    	$out.= ". Down: ";
    	if(empty($queue_down)){
    		$out.= " none.";
    	}
    	foreach($queue_down as $req){
    		$out.= $req."";
    		if($req != last($queue_down)){
    			$out.= ", ";
    		}
    		else{
    			$out.=".";
    		}
    	}
    	return $out;
    }
    public function whole_queue(){
    	$up = !(strlen($this->queue_up) == 0) ? explode(',',$this->queue_up) : [];
    	$down = !(strlen($this->queue_down) == 0) ? explode(',',$this->queue_down) : [];
    	return array_unique(array_merge($up,$down));
    }
    public function add_to_queue($floor, $direction){
    	if($direction == "up"){
    		return $this->add_to_queue_up($floor);
    	}
    	else{
    		return $this->add_to_queue_down($floor);
    	}
    }
    public function add_to_queue_up($floor){
    	if(!(strlen($this->queue_up) == 0)){
    		$queue = explode(",",$this->queue_up);
    		sort($queue);
    		if(in_array($floor, $queue)){
    			return;
    		}
    		array_push($queue, $floor);
    		$queue = implode(",", $queue);
    		$this->queue_up = $queue;
    		$this->save();
    	}
    	else{
    		$this->queue_up = $floor;
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
    public function add_to_queue_down($floor){
    	if(!(strlen($this->queue_down) == 0)){
    		$queue = explode(",",$this->queue_down);
    		rsort($queue);
    		if(in_array($floor, $queue)){
    			return;
    		}
    		array_push($queue, $floor);
    		$queue = implode(",", $queue);
    		$this->queue_down = $queue;
    		$this->save();
    	}
    	else{
    		$this->queue_down = $floor;
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
    public function remove_from_queue($floor, $direction){
    	if($direction == "down"){
	    	if(!(strlen($this->queue_down) == 0)){
	    		$queue = explode(",", $this->queue_down);
	    		if(in_array($floor, $queue)){
	    			unset($queue[array_search($floor, $queue)]);
	    			$this->queue_down = implode(",",$queue);
	    			$this->save();
	    		}
	    	}
	    	return $this;
    	}
    	else{
	    	if(!(strlen($this->queue_up) == 0)){
	    		$queue = explode(",", $this->queue_up);
	    		if(in_array($floor, $queue)){
	    			unset($queue[array_search($floor, $queue)]);
	    			$this->queue_up = implode(",",$queue);
	    			$this->save();
	    		}
	    	}
	    	return $this;
    	}
    }
    public function is_on_queue($floor, $direction){
    	if($direction == "up" && (strlen($this->queue_up) == 0)){
    		return false;
    	}
    	if($direction == "down" && (strlen($this->queue_down) == 0)){
    		return false;
    	}
    	$queue = $direction == "up" ? explode(',',$this->queue_up) : explode(',',$this->queue_down);
    	return array_search($floor, $queue) != false;
    }
	function getClosestUp($search, $arr) {
	   $closest = null;
	   foreach ($arr as $item) {
	      if (($closest === null || abs($search - $closest) > abs($item - $search)) && $search < $item) {
	         $closest = $item;
	      }
	   }
	   return $closest;
	}
	function getClosestDown($search, $arr) {
	   $closest = null;
	   foreach ($arr as $item) {
	      if (($closest === null || abs($search - $closest) > abs($item - $search)) && $search > $item) {
	         $closest = $item;
	      }
	   }
	   return $closest;
	}
    public function floor_is_in_queue($floor, $direction){
    	if($direction == "down"){
	    	if((strlen($this->queue_down) == 0)){
	    		return false;
	    	}
	    	$q = explode(",",$this->queue_down);
	    	return array_search($floor, $q) != false;	
    	}
    	else{
	    	if((strlen($this->queue_up) == 0)){
	    		return false;
	    	}
	    	$q = explode(",",$this->queue_up);
	    	return array_search($floor, $q) != false;	
    	}
    }
    public function go_to_next_floor_up(){
    	if((strlen($this->queue_up) == 0) && (strlen($this->queue_down) == 0)){
    		$this->direction = "stand";
    		$this->save();
    		return $this;
    	}
    	$queue = explode(",",$this->queue_up);
    	$this->current_floor = $this->current_floor+1;
    	$this->save();
    	if($this->floor_is_in_queue($this->current_floor, "up")){
    		$nu = $this->remove_from_queue($this->current_floor, $this->direction);
    		$er = \App\ElevatorRequest::where('current_floor', $this->current_floor)->where('direction', $this->direction)->get()->first();
    		if($er != null){
    			$er->delete();
    		}
    		$this->queue_up = $nu->queue_up;
    	}
    	return $this;
    }
    public function go_to_next_floor_down(){
    	if((strlen($this->queue_up) == 0) && (strlen($this->queue_down) == 0)){
    		$this->direction = "stand";
    		$this->save();
    		return $this;
    	}
    	$queue = explode(",",$this->queue_up);
    	$this->current_floor = $this->current_floor-1;
    	$this->save();
    	if($this->floor_is_in_queue($this->current_floor, "down")){
    		$nu = $this->remove_from_queue($this->current_floor, $this->direction);
    		$er = \App\ElevatorRequest::where('current_floor', $this->current_floor)->where('direction', $this->direction)->get()->first();
    		if($er != null){
    			$er->delete();
    		}
    		$this->queue_up = $nu->queue_down;
    	}
    	return $this;
    }
    public function go_to_next_floor(){
    	if($this->direction == "stand" && ((strlen($this->queue_down) == 0) || (strlen($this->queue_up) == 0))){
    		if(strlen($this->queue_down) == 0){
    			$this->direction = "up";
    			$this->save();
    		}
    		else if(strlen($this->queue_up) == 0){
    			$this->direction = "down";
    			$this->save();
    		}
    		return $this->go_to_next_floor();
    	}
		if((strlen($this->queue_up) == 0) && (strlen($this->queue_down) == 0)){
			$this->direction = "stand";
			$this->save();
			return $this;
		}
    	if($this->direction == "up"){
    		if((strlen($this->queue_up) == 0) && !(strlen($this->queue_down) == 0)){
    			$this->direction = "down";
    			$this->save();
    			return $this->go_to_next_floor_down();
    		}
    		if($this->current_floor == max(explode(',',$this->queue_up))){
    			$this->remove_from_queue($this->current_floor, $this->direction);
    		}
    		if($this->current_floor >= max(explode(',',$this->queue_up)) || $this->current_floor >= self::MAX_FLOORS){
    			return $this->go_to_next_floor_down();
    		}
    		else{
    			return $this->go_to_next_floor_up();
    		}
    	}
    	else if($this->direction == "down"){
    		if((strlen($this->queue_down) == 0)){
    			$this->direction = "up";
    			$this->save();
    			return $this->go_to_next_floor_up();
    		}
    		if($this->current_floor == min(explode(',',$this->queue_down))){
    			$this->remove_from_queue($this->current_floor, $this->direction);
    		}
    		if($this->current_floor <= min(explode(',',$this->queue_down)) || $this->current_floor <= 0){
    			return $this->go_to_next_floor_up();
    		}
    		else{
    			return $this->go_to_next_floor_down();
    		}
    	}
    }
}