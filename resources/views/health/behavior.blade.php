@extends('map')
@section('title', 'พฤติกรรมสุขภาพ')
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
			<div class="panel panel-default" style="max-height: 600px;">

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
			  		</form>
			  		<hr>
			  		<div style="height: 300px;">
			  			<div id="table-wrapper">
			  				<input id="checkAll" type="checkbox" checked style="margin-left: 6px;"> <small style="font-size: smaller;">เลือกทั้งหมด</small>
						  <div id="table-scroll">
						    <table id="table" class="table table-striped" style="font-size: smaller;">
						        
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

