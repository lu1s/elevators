<!DOCTYPE html>
<html lang="en">
<head>
  <title>Elevator - @yield('title')</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="{!! asset('css/app.css') !!}">
  <style>
    /* Set height of the grid so .sidenav can be 100% (adjust if needed) */
    .row.content {height: 1500px}
    
    /* Set gray background color and 100% height */
    .sidenav {
      background-color: #f1f1f1;
      height: 100%;
    }
    
    /* Set black background color, white text and some padding */
    footer {
      background-color: #555;
      color: white;
      padding: 15px;
    }
    
    /* On small screens, set height to 'auto' for sidenav and grid */
    @media screen and (max-width: 767px) {
      .sidenav {
        height: auto;
        padding: 15px;
      }
      .row.content {height: auto;} 
    }
    ul.nav-stacked li{
        width: 100%;
        padding: 1em 0.5em;
    }
    ul.nav-stacked li span.request_elevator{
        border:solid 1px #ccc;
        margin:3px;
        padding:5px;
        font-weight:800;
        cursor:pointer;
    }
    ul.nav-stacked li span.request_elevator:hover{
        border-color:red;
        color:red;
    }
    ul.nav-stacked li span.request_elevator.selected{
        color:orange;
        border-color:orange;
    }
    div.elevator{
        border:solid 1px #ccc;
    }
    .clearboth{
        clear:both;
    }
    .little_floor_button{
        float:right;
        margin:3px;
        padding:4px;
        border:solid 1px #ddd;
        cursor:pointer;
        width:23px;
    }
    .little_floor_button:hover{
        border-color:red;
    }
    .little_floor_button.selected{
        border-color:green;
        color:green;
    }
    .little_floor_button.current{
        border-color:orange;
        color:orange;
    }
    .little_floor_button.queued{
        border-color:blue;
        color:blue;
    }
  </style>
</head>
<body>

<div class="container-fluid">
    @yield('content')
</div>

<footer class="container-fluid">
  <p>Elevators</p>
</footer>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script type="text/javascript" src="{!! asset('js/app.js') !!}"></script>
@yield('scripts')
</body>
</html>
