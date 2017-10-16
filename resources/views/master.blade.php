<html>
    <head>
        <meta charset="utf-8"  name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title')</title>
        <link rel="stylesheet" href="assets/css/main.css" />
        <link rel="stylesheet" href="assets/css/table.css">
        @yield('css')
        <style type="text/css">
        	.navbar-default .navbar-nav > .open > a, .navbar-default .navbar-nav > .open > a:hover, .navbar-default .navbar-nav > .open > a:focus {
			    background-color: rgba(231, 231, 231, 0.16);
			}
        </style>
    </head>
    <body class="left-sidebar">
    	
    	<div id="page-wrapper">
		    
		    <!-- Header -->
			<div id="header-wrapper">
				<div id="header" class="container" style="padding-top: 70px;padding-bottom: 100px;">
					
					<!-- Logo -->
						<h1 id="logo" style="color: #888888;letter-spacing: 5px;">สารสนเทศเชิงพื้นที่ของผู้สูงอายุ</h1>

					<!-- Nav -->
						<nav id="nav" style="padding-top: 80px;">
							<ul>
								<li><a class="icon fa-angle-left" href="{{url('')}}"><span>บันทึกสุขภาพผู้สูงอายุ</span></a></li>
								<li class="@yield('first')"><a class="icon fa-home" href="{{url('')}}"><span>ตำแหน่งพิกัดของบ้าน</span></a></li>
								<li class="@yield('second')"><a class="icon fa-child" href="behavior"><span>พฤติกรรมสุขภาพ</span></a></li>
								<li class="@yield('third')">
									<a href="#" class="icon fa-bar-chart-o"><span>การประเมินสุขภาพผู้สูงอายุ</span></a>
									<ul>
										<li><a href="adl">การดำเนินชีวิตประจำวัน</a></li>
										<li><a href="osteoarthritis">ข้อเข่าเสื่อม</a></li>
									</ul>
								</li>
								<li class="">
									<a href="#" class="icon fa-sign-out"><span>หฤษฎ์ คงทอง</span></a>
									<ul>
										<li><a href="adl">ออกจากระบบ</a></li>
									</ul>
								</li>
							</ul>
						</nav>
				</div>
			</div>
				
				
		    <div id="main-wrapper" style="background-color: #f9f9f9;padding-top: 30px;padding-bottom: 60px;">
				<div id="main" class="container">
	        	@yield('content')
	        	</div>
	        </div>
	        
	        <!-- Footer -->
			<div id="footer">
				<div id="copyright" class="container" style="padding-top: 40px;">
					<ul class="links">
						<li>&copy; Software Engineering, School of Infomatics, Walailak University</li>
					</ul>
				</div>
			</div>
		
		</div>
        
        <script src="js/jquery-3.2.1.js"></script>
        <script src="js/jquery.redirect.js"></script>
        <script src="assets/js/jquery.dropotron.min.js"></script>
		<script src="assets/js/skel.min.js"></script>
		<script src="assets/js/skel-viewport.min.js"></script>
		<script src="assets/js/util.js"></script>
		<!--[if lte IE 8]><script src="assets/js/ie/respond.min.js"></script><![endif]-->
		<script src="assets/js/main.js"></script>
		<script type="text/javascript">
		$(function(){
		    // โหลด สคริป google map api เมื่อเว็บโหลดเรียบร้อยแล้ว
		    // ค่าตัวแปร ที่ส่งไปในไฟล์ google map api
		    // v=3.2&sensor=false&language=th&callback=initialize
		    //  v เวอร์ชัน่ 3.2
		    //  sensor กำหนดให้สามารถแสดงตำแหน่งทำเปิดแผนที่อยู่ได้ เหมาะสำหรับมือถือ ปกติใช้ false
		    //  language ภาษา th ,en เป็นต้น
		    //  callback ให้เรียกใช้ฟังก์ชันแสดง แผนที่ initialize
		    $("<script/>", {
		      "type": "text/javascript",
		      src: "http://maps.google.com/maps/api/js?v=3.2&key=AIzaSyDXPSZi00oTyASzmu_SzAoA9r2H4zQqT6U&callback=myMap"
		    }).appendTo("body");    
		});
		</script>
        @yield('js')
    </body>
</html>