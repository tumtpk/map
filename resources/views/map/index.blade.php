@extends('master')

@section('title', 'ตำแหน่งพิกัดของบ้าน')
	<link rel="stylesheet" href="css/bootstrap.css">
	<style type="text/css">
		.row {
			margin-right: 0px; 
			margin-left: 0px; 
			padding-top: 10px
		}
	</style>
@section('css')

@endsection

@section('content')

@section('first', 'active')
<h1>ตำแหน่งพิกัดบ้าน</h1>
<div class="row">

	<!-- Sidebar -->
		<div id="sidebar" class="4u 12u(mobile)">
			<section>
				<div class="panel panel-default" style="max-height: 600px;">

					<div class="panel-heading">
						ข้อมูลทั่วไป
					</div>
		
				  	<div class="panel-heading">
				  		<form action="" method="post">
				  			{{ csrf_field() }}
							<div class="form-group">
							    <select id="village" name="village" class="form-control" placeholder="เลือกหมู่บ้าน">
									<option value="0">เลือกหมู่บ้าน</option>
									<?php foreach ($villages as $village){ ?>
										<option value="{{$village->village}}" <?php echo ($select == $village->village)?"selected":""; ?>>{{"หมู่ที่ ".$village->village}}</option>
									<?php } ?>
								</select>
							</div>
							<div class="form-group">
							    <!-- <label for="exampleInputPassword1">Password</label> -->
							    <input type="text" id="homeNo" name="homeNo" class="form-control" placeholder="บ้านเลขที่" value="{{$homeNo}}" disabled="disabled">
							</div>
							<div class="form-group">
							    <!-- <label for="exampleInputPassword1">Password</label> -->
							    <input type="text" id="firstname" name="firstname" class="form-control" placeholder="ชื่อ" value="{{$firstname}}" disabled="disabled">
							</div>
							<div class="form-group">
							    <!-- <label for="exampleInputEmail1">Email address</label> -->
							    <input type="text" id="lastname" name="lastname" class="form-control" placeholder="นามสกุล" value="{{$lastname}}" disabled="disabled">
							</div>
							<button type="submit" id="search" class="btn btn-info">ค้นหา</button> &nbsp; จำนวนที่ค้นพบ {{$number}} รายการ
				  		</form>
		
				  		</div>
		
				  		<div class="panel-body" style="height: 500px;">
				  			<div id="table-wrapper">
				  				<input id="checkAll" type="checkbox" checked style="margin-left: 6px;"> <small style="font-size: smaller;">เลือกทั้งหมด</small>
							  <div id="table-scroll">
							    <table id="table" class="table table-striped" style="font-size: smaller;">
							        <thead>

							            <?php 
							            $i=0; foreach ($people as $obj){ ?>
							        		<tr> 
							        			<td style="width: 15px;">
							        				<input class="checked" type="checkbox" checked data-lat="{{$obj->ygis}}" data-lng="{{$obj->xgis}}" data-home="{{$obj->HomeNo}}">
							        			</td> 
							        			<td>
							        				<span class="home" data-no="{{$i++}}">{{$obj->HomeNo}} หมู่ที่ {{$obj->Village}}</span>
							        			</td> 
							        		</tr>
							        	<?php } ?>
							        </thead>
							        <tbody>
							        	
							        </tbody>
							    </table>
							  </div>
							</div>
				  		</div>
				</div>
			</section>
		</div>

	<!-- Content -->
		<div id="content" class="8u 12u(mobile) important(mobile)" style="padding-left: 10px;">
			<section>
				<div id="googleMap" style="width:100%;height:600px;"></div>
			</section>
		</div>

</div>


@endsection

@section('js')
	
<!-- 	<script async defer -->
<!--     src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDXPSZi00oTyASzmu_SzAoA9r2H4zQqT6U&callback=myMap"></script> -->
<!-- 	<script src="js/bootstrap.js"></script> -->
	
	<script>
		
		function myMap() {
			var markers = [];
			var map = new google.maps.Map(document.getElementById('googleMap'), {
	          	zoom: {{$zoom}},
	          	center: {lat: {{floatval($centerCoord[1])}}, lng: {{floatval($centerCoord[0])}}},
	          	mapTypeId: 'terrain',
	          	draggable: true,
	          	zoomControl: true,
	          	streetViewControl: false
	        });

        	// Define the LatLng coordinates for the polygon's path.
        	var triangleCoords = <?=$edgeCoord?>;

	        function coords() {
	            var pathCoordinates = [];
	            triangleCoords.forEach(function(index) {
	            	pathCoordinates.push(new google.maps.LatLng(index.lat, index.lng));
	        	});
	            return pathCoordinates;
	        }

	        function getLatLng(){
	        	var latLng = [];
	        	var row = "";
	        	$("table[id=table] tr").each(function(index) {
	        		row = $(this);
	        		var firstRow = row.find("td:first");
	        		var isCheck = firstRow.children().is(':checked');
	        		var lat = firstRow.children().data('lat');
	        		var lng = firstRow.children().data('lng');
	        		var homeNo = firstRow.children().data('home');
	        		if(isCheck){
	        			var temp = {
	        				lat: parseFloat(lat),
	        				lng: parseFloat(lng),
	        				homeNo: homeNo.toString()
	        			};
	        			latLng.push(temp);
	        		}
	        	});
	        	return latLng;
	        }

	        var bermudaTriangle;
	        var color = <?=$color?>;
	        if({{$isNotSelect}}){
				var arrEdgeCoords = <?=$arrEdgeCoords ?>;
			    var arrVillage = <?=$arrVillage?>;
			    var arrCenterCoord = <?=$arrCenterCoord?>;
			    var index = 0;
				arrEdgeCoords.forEach(function(data) {
				    
					triangleCoords = data;
		        	bermudaTriangle = new google.maps.Polygon({
			          	paths: coords(),
			          	strokeColor: 'red',
			          	strokeOpacity: 0.2,
			          	strokeWeight: 2,
			          	fillColor: color[index],
			          	fillOpacity: 0.4,
			          	index: arrVillage[index]
		          	
			        });
		        	var icon = "images/white.png"
		        	marker = new google.maps.Marker({
						position: new google.maps.LatLng(arrCenterCoord[index].lat, arrCenterCoord[index].lng),
						map: map,
						icon: icon,
						label: "หมู่ที่ "+arrVillage[index]
					});

			        bermudaTriangle.setMap(map);

			        google.maps.event.addListener(bermudaTriangle, 'click', function (event) {
			            $.redirect("",{ 'village': this.index, '_token': '{{ csrf_token() }}'}); 
			        });

			        index++;
	        	});
	        }else{
	        	bermudaTriangle = new google.maps.Polygon({
		          	paths: coords(),
		          	strokeColor: 'red',
		          	strokeOpacity: 0.2,
		          	strokeWeight: 2,
		          	fillColor: color,
		          	fillOpacity: 0.4
		        });
		        bermudaTriangle.setMap(map);
	        }

	        setLatLng();

			function setLatLng(){
				var marker, info;
				var icon = "images/green.png"
				var i=0;
				var dataPatient = <?=$dataPatient ?>;
				var village = $('#village').val();
				getLatLng().forEach(function(data) {
					marker = new google.maps.Marker({
					   position: new google.maps.LatLng(data.lat, data.lng),
					   map: map,
					   icon: icon,
					   title: data.homeNo
					});

					markers.push(marker);

					var content = '<table class=\"table\" style=\"margin-bottom: 0px;\">'+
						'<tr>'+
							'<td style=\"border: 0px;padding-top: 1px;text-align: right;\"\">บ้านเลขที่:</td>'+
							'<td style=\"border: 0px; padding-top: 1px;\"\"> '+data.homeNo+'</td>'+
						'</tr>'+
						'<tr>'+
							'<td style=\"border: 0px;padding-top: 1px;text-align: right;\">หมู่บ้าน:</td>'+
							'<td style=\"border: 0px; padding-top: 1px;\"> หมู่ที่ '+village+' ตำบล '+'{{$subdistrict}}'+' อำเภอ '+'{{$district}}'+' จังหวัด '+'{{$province}}'+'</td>'+
						'</tr>'+
						'<tr>'+
							'<td style=\"border: 0px;padding-top: 1px;text-align: right;\">ผู้สูงอายุ:</td>'+
							'<td style=\"border: 0px; padding-top: 1px;\"></td>'+
						'</tr>'+
						'<tr>'+
							'<td colspan=\"2\" style=\"border: 0px;padding-top: 1px;text-align: right;\">'+
								'<table class=\"table table-bordered table-striped"\">'+
									'<tr>'+
										'<th style=\"text-align: center;\">ชื่อ-สกุล</th>'+
										'<th style=\"text-align: center;\">วันเกิด</th>'+
										'<th style=\"text-align: center;\">อายุ</th>'+
									'</tr>';
									
									dataPatient[data.homeNo].forEach(function(patient) {
										content += '<tr>'+
											'<td>'+patient.name+'</td>'+
											'<td style=\"text-align: center;\">'+patient.birthday+'</td>'+
											'<td style=\"text-align: center;\">'+patient.age+'</td>'+
										'</tr>';
									});
									
						content += '</table>'+
							'</td>'+
						'</tr>'+
						'</table>';
					info = new google.maps.InfoWindow();

				  	google.maps.event.addListener(marker, 'click', (function(marker, i) {
						return function() {
					  		info.setContent(content);
					  		info.open(map, marker);
						}
				  	})(marker, i++));
				})
			}

			function init(){
				clearMaker();
				setLatLng();
			}

			$('.checked').click(function(){
				init();
			})

			function clearMaker() {
				for (var i = 0; i < markers.length; i++) {
					markers[i].setMap(null);
			    }
		   	}

		   	$('#checkAll').change(function(){
		   		if($(this).prop('checked')){
		   			$.each($('.checked'), function(index, obj){
		   				$(obj).prop('checked', true);
		   			});
		   		}else{
		   			$.each($('.checked'), function(index, obj){
		   				$(obj).prop('checked', false);
		   			});
		   		}
		   		init();
		   	});

		   	$('.home').click(function(){
		   		var index = $(this).data('no');
		   		var parent = $(this).parents();
		   		var check = parent[1].children[0].children[0].checked;
		   		if(check){
		   			google.maps.event.trigger(markers[index], "click");
		   		}
			});

			$('#village').change(function(val){
				clearSearch();
			});

			clearSearch();

			function clearSearch() {
				var village = $('#village').val();
				if(village == 0){
// 					$("#search").prop('disabled', true);
					$("#homeNo").prop('disabled', true);
					$("#homeNo").val('');
					$("#firstname").prop('disabled', true);
					$("#firstname").val('');
					$("#lastname").prop('disabled', true);
					$("#lastname").val('');
				}else{
// 					$("#search").prop('disabled', false);
					$("#homeNo").prop('disabled', false);
					$("#firstname").prop('disabled', false);
					$("#lastname").prop('disabled', false);
				}
		   	}
		   	
		}
				
			</script>
@endsection

