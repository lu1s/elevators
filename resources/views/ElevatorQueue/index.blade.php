@extends('layouts.app')
@section('content')

<div class="row content">
    <div class="col-sm-2 sidenav">
      <h4>Floors</h4>
      <ul class="nav nav-pills nav-stacked">
      	@for ($i = 15; $i >= 0; $i--)
      		<li>
      			Floor {{ $i }} <span class="request_elevator  {{ in_array($i, $floor_requests_arr_up) ? 'selected' : '' }}" data-floor="{{ $i }}" data-direction="up">↑</span> <span class="request_elevator {{ in_array($i, $floor_requests_arr_down) ? 'selected' : '' }}" data-floor="{{ $i }}" data-direction="down">↓</span>
      		</li>
      	@endfor
      </ul><br>
    </div>

    <div class="col-sm-10" style="margin-top:20px">
    	<div class="elevators">
	    	@foreach ($elevators as $elevator)
	    		<div class="col-sm-4 elevator" data-id="{{ $elevator->id }}">
		    		<div class="col-sm-8" data-id="{{ $elevator->id }}">
		    			<h3>Elevator {{ $elevator->id }}</h3>
		    			<h4>Floor: <span class="elevator_floor" data-id="{{ $elevator->id }}">{{ $elevator->current_floor }}</span></h4>
		    			<h4>Direction: <span class="elevator_direction" data-id="{{ $elevator->direction }}">{{ $elevator->direction }}</span></h4>
		    			<h4>Queue: </h4>
		    			<p class="elevator_queue" data-id="{{ $elevator->id }}">
		    				{{ $elevator->beautify_queue() }}
		    			</p>
		    		</div>
		    		<div class="col-sm-4" class="elevator_little_buttons" data-elevator="{{ $elevator->id }}">
		    			@for($i = 15; $i >= 0; $i--)
		    				<div class="little_floor_button {{ $i == $elevator->current_floor ? "current" : (in_array((string) $i, explode(",",$elevator->queue)) ? "queued" : "") }}" data-elevator="{{ $elevator->id }}" data-floor="{{ $i }}">{{ $i }}</div>
		    			@endfor
		    			<div class="clearboth"></div>
		    		</div>
		    		<div class="col-sm-12" style="margin-bottom:15px;">
		    			<button class="btn btn-default go_to_next_floor" data-id="{{ $elevator->id }}">
		    				Go to next floor
		    			</button>
		    			<span class="elevator_moving" style="display:none">
		    				Elevator is moving...
		    			</span>
		    		</div>
	    		</div>
	    	@endforeach
	    </div>
	    <div class="elevator_requests">
	    	<table class="table table-stripped" id="elevator_requests_table">
	    		<tr>
	    			<th>Requested on</th>
	    			<th>Direction</th>
	    			<th>Floor</th>
	    			<th>For elevator</th>
	    		</tr>
	    		@foreach($elevator_requests as $request)
	    		<tr class="elevator_request" data-id="{{ $request->id }}">
	    			<td>{{ $request->updated_at }}</td>
	    			<td>{{ $request->direction }}</td>
	    			<td>{{ $request->current_floor }}</td>
	    			<td>{{ $request->queued_to_elevator }}</td>
	    		</tr>
	    		@endforeach
	    	</table>
	    </div>
    </div>
  </div>


@stop

@section('scripts')
	<script type="text/javascript">
		function add_to_requests_queue(data){
			var el = "<tr class='elevator_request' data-id='"+data.id+"'>";
			el += '<td>' + data.updated_at + '</td>';
			el += '<td>' + data.direction + '</td>';
			el += '<td>' + data.current_floor + '</td>';
			el += '<td>' + data.queued_to_elevator + '</td>';
			el += '</tr>';
			$("#elevator_requests_table").append(el);
		}
		function refresh_elevator(id){
			$.getJSON('/api/elevators/get/' + id, function(d){
				if(d){
					var el = $(".elevator[data-id='"+d.elevator.id+"']");
					el.find(".elevator_direction").text(d.elevator.direction);
					el.find(".elevator_floor").text(d.elevator.current_floor);
					el.find(".elevator_queue").text(d.beautified_queue);
					el.find(".elevator_little_buttons").find(".little_floor_button").removeClass("current");
					el.find(".elevator_little_buttons").find(".little_floor_button[data-floor='"+d.elevator.current_floor+"']").addClass("current");
					var queue = d.elevator.queue.split(',');
					$.each(el.find(".little_floor_button"), function(){
						if(queue.indexOf($(this).data('floor').toString()) > -1){
							$(this).addClass("queued");
						}
						else{
							$(this).removeClass("queued");
						}
						if($(this).data('floor') == d.elevator.current_floor){
							$(this).addClass("current");
						}
						else{
							$(this).removeClass("current");
						}
					});
				}
			});
		}
		function refresh_floors(){
			$.getJSON('/api/elevators/get_queued_floors', function(d){
				if(d){
					$(".request_elevator.selected").removeClass("selected");
					$(".elevator_request").remove();
					$.each(d, function(){
						$(".request_elevator[data-floor='"+this.current_floor+"'][data-direction='"+this.direction+"']").addClass("selected");
						add_to_requests_queue(this);
					});
				}
			})
		}
		var timeouts = [];
		function create_floor_animation_queue(id, dir, initial, end_floor){
			if(end_floor == initial.data('floor')){
				initial.removeClass("queued");
				initial.addClass("current");
				finish_animation(id);
				return;
			}
			timeouts.push(window.setTimeout(function(){
				initial.removeClass('current');
				if(dir == "up"){
					initial = initial.prev();
				}
				else{
					initial = initial.next();
				}
				initial.addClass("current");
				var el = $(".elevator[data-id='"+id+"']");
				el.find(".elevator_floor").text(initial.data('floor'));
				create_floor_animation_queue(id, dir, initial, end_floor);
			}, 1000));
		}
		function animate_elevator(src_el, id, end_floor){
			src_el.prop('disabled', 'disabled');
			var el = $(".elevator[data-id='"+id+"']");
			el.find(".elevator_moving").show();
			var initial = $(".little_floor_button.current[data-elevator='"+id+"']");
			var initial_floor = initial.data('floor');
			if(end_floor > initial_floor){
				create_floor_animation_queue(id, "up", initial, end_floor);
			}
			else{
				create_floor_animation_queue(id, "down", initial, end_floor);
			}
		}
		function finish_animation(id){
			$(".elevator[data-id='"+id+"']").find(".go_to_next_floor").prop("disabled", false);
			var el = $(".elevator[data-id='"+id+"']");
			el.find(".elevator_moving").hide();
			el.css({borderColor:"green", background: "#DCFFEA"});
			window.setTimeout(function(){
				el.css({borderColor:"#ccc", background: "white"});
			}, 1500);
		}
		$(document).ready(function(){
			$(".request_elevator").not('.selected').click(function(){
				if($(this).hasClass("selected")){
					alert("That floor has already a request to go " + $(this).data("direction"));
				}
				else{
					var r = confirm("Request an elevator on floor " + $(this).data("floor") + " to go "+ $(this).data("direction") + "?");
					if(r){
						var el = $(this);
						$.getJSON('/api/elevators/add_new_floor_request/' + $(this).data("floor") + '/' + $(this).data('direction'), function(data){
							if(data){
								refresh_elevator(data.queued_to_elevator);
								refresh_floors();
								el.addClass("selected");
							}
						}).fail(function(){
							alert("Something wrong happened :(");
						})
					}
				}
			});
			$(".go_to_next_floor").click(function(){
				var id = $(this).data('id');
				var el = $(this);
				$.getJSON('/api/elevators/go_to_next_floor/' + id, function(d){
					if(d){
						animate_elevator(el, id, d.current_floor);
						refresh_floors();
					}
				});
			});
		});
	</script>
@stop
