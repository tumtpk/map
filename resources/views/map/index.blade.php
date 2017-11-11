@extends('map')
@section('title', 'ตำแหน่งพิกัดของบ้าน')
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
@section('first', 'active')
<div class="page-header">
	<h1>ตำแหน่งพิกัดบ้าน</h1>
</div>
<div class="row">

	<!-- Sidebar -->
		<div id="sidebar" class="col-md-4">
			<section>
				<div class="panel panel-default" style="max-height: 600px;">

					<div class="panel-heading">
						ข้อมูลทั่วไป
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
							<div class="text-right">
							 	จำนวนที่ค้นพบ .. รายการ &nbsp;&nbsp;  <button type="submit" id="search" class="btn btn-info">ค้นหา</button>
							</div>
				  		</form>
				  		<hr>
				  		<div style="height: 300px;">
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
				</div>
			</section>
		</div>

	<!-- Content -->
		<div id="content" class="col-md-8" style="padding-left: 10px;">
			<section>
				<div id="googleMap" style="width:100%;height:600px;"></div>
			</section>
		</div>

</div>


@endsection

@section('js')
	<script>

		function myMap() {
		    var uluru = {lat: -25.363, lng: 131.044};
		    var map = new google.maps.Map(document.getElementById('googleMap'), {
		      zoom: 4,
		      center: uluru
		    });
		    var marker = new google.maps.Marker({
		      position: uluru,
		      map: map
		    });
		}
		
	</script>
@endsection

