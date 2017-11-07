@extends('map')

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
							<button type="submit" id="search" class="btn btn-info">ค้นหา</button> &nbsp; จำนวนที่ค้นพบ .. รายการ
				  		</form>
		
				  		</div>
		
				  		<div class="panel-body" style="height: 500px;">
				  			<div id="table-wrapper">
				  				<input id="checkAll" type="checkbox" checked style="margin-left: 6px;"> <small style="font-size: smaller;">เลือกทั้งหมด</small>
							  <div id="table-scroll">
							    <table id="table" class="table table-striped" style="font-size: smaller;">
							        <thead>

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
	          	mapTypeId: 'terrain',
	          	draggable: true,
	          	zoomControl: true,
	          	streetViewControl: false
	        });

		}
				
			</script>
@endsection

