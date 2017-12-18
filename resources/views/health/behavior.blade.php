@extends('map')
@section('title', 'พฤติกรรมสุขภาพ')
@section('css')
	<link rel="stylesheet" href="{{ url('css/jquery.tagsinput.css') }}" />
	<style>
		div.tagsinput span.tag {
		    border: 1px solid #333;
		    -moz-border-radius: 2px;
		    -webkit-border-radius: 2px;
		    display: block;
		    float: left;
		    padding: 5px;
		    text-decoration: none;
		    background: #2e6da4;
		    color: #ffffff;
		    margin-right: 5px;
		    margin-bottom: 5px;
		    font-family: helvetica;
		    font-size: 13px;
		}

		#table-scroll {
		    height: 270px;
		}
	</style>
@endsection
@section('menu')
<li>
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">การประเมิน<b class="caret"></b></a>
	<ul class="dropdown-menu dropdown-menu-right">
		<?php foreach ($evoluationPart as $part){ 
			$form = $evoluationForm[$part->id];
			$size = sizeof($form);
			$isShowSubMenu = ($size>0)?true:false;
		?>
			<?php if($isShowSubMenu){ ?>
			<li class="dropdown-submenu">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">{{$part->name}}</a>
				<ul class="dropdown-menu">
				<?php for ($i=0;$i<$size;$i++){ ?>
					<li><a href="form/{{$form[$i]['id']}}">{{$form[$i]['name']}}</a></li>
				<?php } ?>
				</ul>
			</li>
			<?php } ?>
		<?php } ?>
	</ul>
</li>
@endsection
@section('content')

<div class="page-header">
	<h2>พฤติกรรมสุขภาพ</h2>
</div>

<div class="row">

	<!-- Sidebar -->
	<div id="sidebar" class="col-md-4">
		<section>
			<div class="panel panel-default" style="max-height: auto;">

				<div class="panel-heading">
					พฤติกรรมสุขภาพ
				</div>
	
			  	<div class="panel-body">
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
						<hr>
						<div class="form-group">
							<label for="Sickhistory">ครั้งที่บันทึก</label>
							<select id="time" name="time" class="form-control" placeholder="เลือกครั้งที่">
<!-- 								<option value="0">เลือกครั้งที่บันทึก</option> -->
								<?php foreach ($times as $val){ ?>
									<option value="{{$val->Time}}" <?php echo ($time == $val->Time)?"selected":""; ?>>{{"ครั้งที่ ".$val->Time}}</option>
								<?php } ?>
							</select>
						</div>
						<div class="form-group">
							<label for="Sickhistory">ประวัติการเจ็บป่วยในอดีต</label>
			                <input type="text" class="form-control" name="pastillness" id="pastillness" placeholder="ประวัติการเจ็บป่วยในอดีต" value="{{$pastillness}}">
						</div>
						<div class="form-group">
							<label for="Sickhistory">ประวัติการผ่าตัด</label>
							<input class="form-control" id="historysurgery" type="text" name="historysurgery" placeholder="ประวัติการผ่าตัด" value="{{$historysurgery}}">
						</div>
						<div class="form-group">
							<label for="Sickhistory">โรคประจำตัว</label>
							<input class="form-control" id="congenital" type="text" name="congenital" placeholder="โรคประจำตัว" value="{{$congenital}}">
						</div>
						<div class="form-group">
							<label for="Sickhistory">การสูบบุหรี่</label>
						    <select id="cigarette" name="cigarette" class="form-control">
								<option value="">เลือกการสูบบุหรี่</option>
							<?php foreach ($arrCigaratte as $key => $val){ ?>
			  					<option value="{{$key}}" {{($cigarette == $key)?"selected":""}}>{{$val}}</option>
			  				<?php } ?>
							</select>
						</div>
						<div class="form-group">
							<label for="Sickhistory">การดื่มแอลกฮอล์</label>
						    <select id="drink" name="drink" class="form-control">
								<option value="">เลือกการดื่มแอลกฮอล์</option>
							<?php foreach ($arrDrink as $key => $val){ ?>
			  					<option value="{{$key}}" {{($drink == $key)?"selected":""}}>{{$val}}</option>
			  				<?php } ?>
							</select>
						</div>
						<div class="text-right">
							 จำนวนที่ค้นพบ {{$numberOfHomeNo}} รายการ &nbsp;&nbsp;  <button type="submit" id="search" class="btn btn-info">ค้นหา</button>
						</div>
			  		</form>
			  		<hr>
			  		<div style="height: 300px;">
			  			<div id="table-wrapper">
			  				<input id="checkAll" type="checkbox" checked style="margin-left: 6px;"> <small style="font-size: smaller;">เลือกทั้งหมด</small>
						  <div id="table-scroll">
						    <table id="table" class="table table-striped" style="font-size: smaller;">
						        <?php 
					            $i=0; foreach ($arrHomeNo as $obj){ ?>
					        		<tr> 
					        			<td style="width: 15px;">
					        				<input class="checked" type="checkbox" checked data-lat="{{$obj->ygis}}" data-lng="{{$obj->xgis}}" data-home="{{$obj->HomeNo}}">
					        			</td> 
					        			<td>
					        				<span class="home" data-no="{{$i++}}">บ้านเลขที่ {{$obj->HomeNo}}</span>
					        			</td> 
					        		</tr>
					        	<?php } ?>
						    </table>
						  </div>
						</div>
			  		</div>
			  	</div>
			</div>
		</section>
	</div>

	<!-- Content -->
	<div id="content" class="col-md-8" style="padding-left: 10px;">
		<section>
			<div id="googleMap" style="width:100%;height:700px;"></div>
		</section>
	</div>

</div>

@endsection

@section('js')
	
	<script src="{{ url('js/jquery.tagsinput.js') }}"></script>
	<script>

		$('#pastillness').tagsInput({
			width:'auto',
			height:'80px',
		});
	
		$('#historysurgery').tagsInput({
			width:'auto',
			height:'80px',
		});
	
		$('#congenital').tagsInput({
			width:'auto',
			height:'80px',
		});

		$('#village').change(function(){
			clearSearch();
		});

		clearSearch();

		function clearSearch() {
			var village = $('#village').val();
			if(village == 0){
				$("#homeNo").prop('disabled', true);
				$("#homeNo").val('');
				
				$("#cigarette").prop('disabled', true);
				$("#cigarette").prop('selectedIndex', 0);
				$("#drink").prop('disabled', true);
				$("#drink").prop('selectedIndex', 0);

				$("#time").append('<option value="0">เลือกครั้งที่บันทึก</option>');
				$("#time").prop('disabled', true);
				$("#time option[value='0']").attr('selected', true);

				$("#pastillness").importTags('');
				$("#historysurgery").importTags('');
				$("#congenital").importTags('');
				
				$(".tagsinput").css("pointer-events", "none");
				$(".tagsinput").css("background-color", "#eeeeee");
			}else{
				$("#homeNo").prop('disabled', false);
				$("#cigarette").prop('disabled', false);
				$("#drink").prop('disabled', false);

				$("#time option[value='0']").remove();
				$("#time").prop('disabled', false);

				$(".tagsinput").css("pointer-events", "initial");
				$(".tagsinput").css("background-color", "#fff");
			}
			
	   	}

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
		        	var icon = '{{ url('images/white.png') }}';
		        	marker = new google.maps.Marker({
						position: new google.maps.LatLng(arrCenterCoord[index].lat, arrCenterCoord[index].lng),
						map: map,
						icon: icon,
						label: "หมู่ที่ "+arrVillage[index]
					});
			        bermudaTriangle.setMap(map);

			        google.maps.event.addListener(bermudaTriangle, 'click', function (event) {
			            $.redirect("behavior",{ 'village': this.index, '_token': '{{ csrf_token() }}'}); 
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
				var icon = '{{ url('images/1.png') }}';
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
							'<td style=\"border: 0px; padding-top: 1px;\">{{$stringLocation}}</td>'+
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
										'<th style=\"text-align: center;\">อายุ</th>'+
										'<th style=\"text-align: center;\">ประวัติการเจ็บป่วย</th>'+
										'<th style=\"text-align: center;\">ประวัติการผ่าตัด</th>'+
										'<th style=\"text-align: center;\">โรคประจำตัว</th>'+
										'<th style=\"text-align: center;\">การสูบบุหรี่</th>'+
										'<th style=\"text-align: center;\">แอลกฮอล์</th>'+
									'</tr>';
									
									dataPatient[data.homeNo].forEach(function(patient) {
										content += '<tr>'+
											'<td>'+patient.name+'</td>'+
											'<td style=\"text-align: center;\">'+patient.age+'</td>'+
											'<td style=\"text-align: center;\">'+patient.pastillness+'</td>'+
											'<td style=\"text-align: center;\">'+patient.historysurgery+'</td>'+
											'<td style=\"text-align: center;\">'+patient.congenital+'</td>'+
											'<td style=\"text-align: center;\">'+patient.cigarette+'</td>'+
											'<td style=\"text-align: center;\">'+patient.drink+'</td>'+
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
			})
			
		};
	</script>
@endsection
