<!DOCTYPE html>
<html lang="en">
    <head>
    	<meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8"  name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title')</title>
        <link rel="stylesheet" href="../css/bootstrap.min.css" />
        <link rel="stylesheet" href="../css/bootstrap-theme.min.css" />
        <link rel="stylesheet" href="../css/dropdown.css" />
        <link rel="stylesheet" href="../css/table.css" />
        @yield('css')
    </head>
    <body>
 		 <nav class="navbar navbar-inverse navbar-fixed-top">
		      <div class="container">
		        <div class="navbar-header">
		          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
		            <span class="sr-only">Toggle navigation</span>
		            <span class="icon-bar"></span>
		            <span class="icon-bar"></span>
		            <span class="icon-bar"></span>
		          </button>
		          <a class="navbar-brand" href="#">
		          	<span class="glyphicon glyphicon-menu-left" aria-hidden="true"></span> 
		          	บันทึกสุขภาพผู้สูงอายุ
		          </a>
		        </div>
		        <div id="navbar" class="navbar-collapse collapse">
		          <ul class="nav navbar-nav">
		            <li class="active"><a href="../health">ตำแหน่งพิกัดของบ้าน</a></li>
		            <li><a href="../behavior">พฤติกรรมสุขภาพ</a></li>
		            <li><a href="../volunteer">อสม.</a></li>
		            @yield('menu')	
		          </ul>
		        </div><!--/.nav-collapse -->
		      </div>
		    </nav>
			
			<div class="container theme-showcase" role="main" style="padding-top: 70px;">
			    <!-- Main jumbotron for a primary marketing message or call to action -->
			    <div class="jumbotron">
			      <div class="container">
			        <h1 style="font-family: 'Arvo';font-weight: 700;color: #888888;text-shadow: 0.05em 0.075em 0 rgba(0, 0, 0, 0.1);text-align: -webkit-center;">
			        	สารสนเทศเชิงพื้นที่ของผู้สูงอายุ
			        </h1>
			      </div>
			    </div>
			    
			    
			    @yield('content')
				
				<hr>
		      <footer>
		        <p class="text-center">© Software Engineering, School of Infomatics, Walailak University</p>
		      </footer>
		    </div>
		
		      
        <script src="../js/jquery-3.2.1.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script src="../js/jquery.redirect.js"></script>
        
        <script async defer src="http://maps.google.com/maps/api/js?key=AIzaSyDXPSZi00oTyASzmu_SzAoA9r2H4zQqT6U&amp;callback=myMap"></script>
        
		<script type="text/javascript">
		
		$(function(){
// 		    $("<script/>", {
// 		      "type": "text/javascript",
// 		      src: "http://maps.google.com/maps/api/js?key=AIzaSyDXPSZi00oTyASzmu_SzAoA9r2H4zQqT6U&callback=myMap"
// 		    }).appendTo("body");    
		});
		</script>
        @yield('js')
    </body>
</html>