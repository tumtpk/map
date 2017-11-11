<html>
    <head>
        <meta charset="utf-8"  name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title')</title>
        <link rel="stylesheet" href="css/bootstrap.min.css" />
        <link rel="stylesheet" href="css/bootstrap-theme.min.css" />
        
        @yield('css')
        <style type="text/css">
			.dropdown-submenu {
    position: relative;
}

.dropdown-submenu>.dropdown-menu {
    top: 0;
    left: 100%;
    margin-top: -6px;
    margin-left: -1px;
    -webkit-border-radius: 0 6px 6px 6px;
    -moz-border-radius: 0 6px 6px;
    border-radius: 0 6px 6px 6px;
}

.dropdown-submenu:hover>.dropdown-menu {
    display: block;
}

.dropdown-submenu>a:after {
    display: block;
    content: " ";
    float: right;
    width: 0;
    height: 0;
    border-color: transparent;
    border-style: solid;
    border-width: 5px 0 5px 5px;
    border-left-color: #ccc;
    margin-top: 5px;
    margin-right: -10px;
}

.dropdown-submenu:hover>a:after {
    border-left-color: #fff;
}

.dropdown-submenu.pull-left {
    float: none;
}

.dropdown-submenu.pull-left>.dropdown-menu {
    left: -100%;
    margin-left: 10px;
    -webkit-border-radius: 6px 0 6px 6px;
    -moz-border-radius: 6px 0 6px 6px;
    border-radius: 6px 0 6px 6px;
}
        </style>
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
		            <li class="active"><a href="#">ตำแหน่งพิกัดของบ้าน</a></li>
		            <li><a href="#">พฤติกรรมสุขภาพ</a></li>
		            <li><a href="#">อสม.</a></li>
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
		
		      
        <script src="js/jquery-3.2.1.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        
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