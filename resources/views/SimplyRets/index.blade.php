<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="">
		<title>SimplyRets Demo</title>
		<link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
		<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<style type="text/css">
			body{
				font-family:'Raleway', sans-serif;
			}
			.loading, .alert{
				position:fixed;
				top:50%;
				left:50%;
				margin-left:-210px;
				margin-top:-60px;
				width:420px;
				height:120px;
				font-size:20px;
				text-align:center;
				background:#111;
				color:white;
				padding-top:50px;
				display:none;
				border-radius:5px;
				z-index:2600;
			}
			.alert.success{
				color: #468847;
				background-color: #dff0d8;
				border-color: #d6e9c6;
			}
			.alert.danger,
			.alert.error {
				color: #b94a48;
				background-color: #f2dede;
				border-color: #eed3d7;
			}
			.alert.info {
				color: #3a87ad;
				background-color: #d9edf7;
				border-color: #bce8f1;
			}
			.img_thumb{
				max-width:450px;
				max-height:350px;
				float:left;
				margin:8px 10px;
			}
			.clear{
				clear:both;
			}
		</style>
</head>
<body>
    <div class="container">
      <div class="header clearfix">
        <nav>
          <ul class="nav nav-pills pull-right">
          </ul>
        </nav>
        <h3 class="text-muted">LHP SimplyRets</h3>
      </div>

      <div class="jumbotron">
      	<div class="col-lg-11 col-xs-11 col-sm-11 col-md-11">
		    <div class="input-group">
		      <input type="text" class="form-control" id="mlsid" placeholder="MLSID" value="1005252"/>
		      <span class="input-group-btn">
		        <button class="btn btn-default" id="go" type="button">Go!</button>
		      </span>
		    </div>
      	</div>
      	<div class="col-lg-1 col-xs-1 col-sm-1 col-md-1">
      		<button class="btn btn-default" id="clear">Clear</button>
      	</div>
      </div>

      <div class="row marketing">
        <div class="col-lg-12 col-xs-12 col-sm-12 col-md-12" id="details" style="display:none">
        	<div class="bootstrap-row">
	          <div class="col-lg-4 col-xs-4 col-sm-4 col-md-4">
	          	<h3>Private remarks</h3>
	          	<p class="private_remarks"></p>
	          </div>
	          <div class="col-lg-8 col-xs-8 col-sm-8 col-md-8">
	          	<h3>Property</h3>
	          	<div class="property"></div>
	          </div>
        	</div>
        	<div class="bootstrap-row">
        		<div class="col-lg-4 col-xs-4 col-sm-4 col-md-4">
        			<h4>Address</h4>
        			<p class="address"></p>
        		</div>
        		<div class="col-lg-4 col-xs-4 col-sm-4 col-md-4">
        			<h4>Agent</h4>
        			<p class="agent"></p>
        		</div>
        		<div class="col-lg-4 col-xs-4 col-sm-4 col-md-4">
        			<h4>School</h4>
        			<p class="school"></p>
        		</div>
        	</div>
        	<div class="bootstrap-row">
        		<div class="col-lg-12 col-xs-12 col-sm-12 col-md-12">
	        		<h4>Photos</h4>
	        		<div class="photos"></div>
        		</div>
        	</div>
        </div>
      </div>

    </div> <!-- /container -->
    <div class="loading">loading...</div>
    <div class="alert"></div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script type="text/javascript">
		var loading = false, alert_to = false;
		function clearData(){
			$("#details").fadeOut();
			$(".private_remarks").text('');
			$(".property").text('');
			$(".address").text('');
			$(".agent").text('');
			$(".school").text('');
			$(".photos").html('');
		}
		function getObjectList(obj){
			var out = '<ul>';
			for(var k in obj){
				if(obj[k] != null){
					if(typeof obj[k] === 'object'){
						out+=getObjectList(obj[k]);
					}
					else{
						out+='<li>'+k+': '+obj[k];
					}
				}
			}
			out+='</ul>';
			return out;
		}
		function getAddress(addr){
			return addr.full+'<br/>'+addr.city+', '+addr.state+'. '+addr.postalCode+'<br/>'+addr.country;
		}
		function getAgent(agent){
			return agent.firstName+' '+agent.lastName;
		}
		function getSchool(school){
			return 'Elementary: '+school.elementarySchool+'<br/>Middle: '+school.middleSchool+'<br/>High: '+school.highSchool;
		}
		function getPhotos(arr){
			var imgs = '';
			for(var i in arr){
				imgs += '<a href="'+arr[i]+'" target="_blank"><img src="'+arr[i]+'" class="img_thumb"/></a>';
			}
			return imgs+'<div class="clear"></div>';
		}
		function populateData(d){
			$(".private_remarks").text(d.privateRemarks);
			$(".property").html(getObjectList(d.property));
			$(".address").text(getAddress(d.address));
			$(".agent").text(getAgent(d.agent));
			$(".school").text(getSchool(d.school));
			$(".photos").html(getPhotos(d.photos));
			$("#details").fadeIn();
		}
		function showLoading(){
			$(".loading").show();
		}
		function hideLoading(){
			$(".loading").hide();
		}
		function showSuccess(msg){
			$(".alert").removeClass("error info danger").addClass("success").text(msg).show();
			window.clearTimeout(alert_to);
			alert_to = window.setTimeout(function(){
				$(".alert").text('').removeClass('success').hide();
			}, 3000);
		}
		function showError(msg){
			$(".alert").removeClass("success info danger").addClass("error").text(msg).show();
			window.clearTimeout(alert_to);
			alert_to = window.setTimeout(function(){
				$(".alert").text('').removeClass('error').hide();
			}, 3000);
		}
		function searchMlsid(){
			if(!loading){
				loading = true;
				showLoading();
				$.getJSON("/api/simplyrets/load_data/" + $("#mlsid").val(), function(d){
					hideLoading();
					if(d.success){
						populateData(d.data);
						hideLoading();
						loading = false;
					}
					else{
						showError(d.message);
						loading = false;
					}
				}).fail(function(){
					hideLoading();
					showError("oops.. something went wrong.");
					loading = false;
				});
			}
		}
		$(document).ready(function(){
			$("#go").click(function(){
				searchMlsid();
			});
			$("#clear").flick(function(){
				if(!loading){
					loading = true;
					clearData();
					loading = false;
				}
			})
			$("#mlsid").keyup(function(e){
				if(e.keyCode == 13){
					searchMlsid();
				}
			});
		});
	</script>
</body>
</html>