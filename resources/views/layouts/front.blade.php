<!DOCTYPE html>

<!--
   Template:   Sepia - Photography Portfolio HTML Website Template
   Author:     Themetorium
   URL:        https://themetorium.net  
-->

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>

		<!-- Title -->
		<title>ARTfora - A place for artists and art lovers</title>

		<!-- Meta -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="description" content="Responsive Photography HTML5 Website Template">
		<meta name="keywords" content="HTML5, CSS3, Bootsrtrap, Responsive, Photography, Portfolio, Template, Theme, Website, Themetorium" />
		<meta name="author" content="themetorium.net">

		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta id="token" name="csrf-token" content="{{ csrf_token() }}">

		<!-- Favicon (http://www.favicon-generator.org/) -->
		<link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
		<link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

		<meta name="p:domain_verify" content="9de434df85e3c90c2cfd7f934da58f3e"/>

		<!-- Google fonts (https://www.google.com/fonts) -->
		<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet"> <!-- Global font -->

		 <link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Condensed:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

		<!-- Bootstrap CSS (http://getbootstrap.com) -->
		<link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}"> <!-- bootstrap CSS (http://getbootstrap.com) -->

		<!-- Libs and Plugins CSS -->
		<link rel="stylesheet" href="{{ asset('assets/vendor/animsition/css/animsition.min.css') }}"> <!-- Animsition CSS (http://git.blivesta.com/animsition/) -->
		<link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome/css/fontawesome-all.min.css') }}"> <!-- Font Icons CSS (https://fontawesome.com) Free version! -->
		<link rel="stylesheet" href="{{ asset('assets/vendor/lightgallery/css/lightgallery.min.css') }}"> <!-- lightGallery CSS (http://sachinchoolur.github.io/lightGallery) -->
		<link rel="stylesheet" href="{{ asset('assets/vendor/owl-carousel/css/owl.carousel.min.css') }}"> <!-- Owl Carousel CSS (https://owlcarousel2.github.io/OwlCarousel2/) -->
		<link rel="stylesheet" href="{{ asset('assets/vendor/owl-carousel/css/owl.theme.default.min.css') }}"> <!-- Owl Carousel CSS (https://owlcarousel2.github.io/OwlCarousel2/) -->
		<link rel="stylesheet" href="{{ asset('assets/vendor/ytplayer/css/jquery.mb.YTPlayer.min.css') }}"> <!-- YTPlayer CSS (more info: https://github.com/pupunzi/jquery.mb.YTPlayer) -->
		<link rel="stylesheet" href="{{ asset('assets/vendor/animate.min.css') }}"> <!-- Animate libs CSS (http://daneden.me/animate) -->
        <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css"/>-->
		<!-- Template master CSS -->
		<link rel="stylesheet" href="{{ asset('assets/css/helper.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

		<!-- Template dark style CSS (just uncomment line below to enable dark style) -->
			<!-- <link rel="stylesheet" href="assets/css/dark-style.css"> -->

		<!-- Template round style CSS (just uncomment line below to enable round style) -->
			<!-- <link rel="stylesheet" href="assets/css/round-style.css"> -->

		<!-- Template color skins CSS (just uncomment line below to enable color skin. One line at a time!) -->
			<!-- <link rel="stylesheet" href="assets/css/color-skins/skin-red.css"> -->
			<!-- <link rel="stylesheet" href="assets/css/color-skins/skin-green.css"> -->
			<!-- <link rel="stylesheet" href="assets/css/color-skins/skin-blue.css"> -->
			<!-- <link rel="stylesheet" href="assets/css/color-skins/skin-orange.css"> -->
			<!-- <link rel="stylesheet" href="assets/css/color-skins/skin-purple.css"> -->
			<!-- <link rel="stylesheet" href="assets/css/color-skins/skin-pink.css"> -->
			<!-- <link rel="stylesheet" href="assets/css/color-skins/skin-brown.css"> -->

		<!-- Template RTL mode CSS (just uncomment all 3 lines below to enable right to left mode) -->
			<!-- <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap-rtl/bootstrap-rtl.min.css"> -->
			<!-- <link rel="stylesheet" href="assets/css/rtl/theme-rtl.css"> -->
			<!-- <link rel="stylesheet" href="assets/css/rtl/helper-rtl.css"> -->
			

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-203085821-2"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());

			gtag('config', 'UA-203085821-2');
		</script>
		<!-- END Global site tag (gtag.js) - Google Analytics -->

	</head>

	<!-- ===========
	///// Body /////
	================
	* Use class "animsition" to enable page transition while page loads.
	* Use class "tt-boxed" to enable page boxed layout globally (affects all elements containing class "tt-wrap").
	-->
	<body id="body" class="animsition tt-boxed">
        @yield('frontheader')
		<!-- *************************************
		*********** Begin body content *********** 
        ************************************** -->
        <div id="body-content">
            @yield('content')
        </div>
		<!-- End body content -->        
        <div class="fotter ">
            <div class="container-fluid footer-bar">
			    <div class="row">
				<div class="col-sm-4 text-left">
    		        <p class="white-color d-inline" style="margin:29px 5px 10px 0px">We accept</p>
    		        <img src="{{ asset('assets/img/visa-mastercard.png') }}" class="d-inline">
				</div>
				<div class="col-sm-8">
					<div class="row float_right">
						 <div class="col-md-3 text_center" id="close">
							<div class="img_footer_div"></div>
							<p class="white-color" style="margin-left: 10px;">CLOSE</p>
						</div>
						<div class="col-md-3 text_center" id="open">
							<div class="img_footer_div"></div>
							<p class="white-color" style="margin-left: 10px;">OPEN</p>
						</div>
					</div>		
					<div class="row float_right" id="div-hide">
						<div class="d-inline-flex2">
							<div class="img_footer">
							    <a href="#">
							        <div class="img_footer_div">
        							    <img src="{{ asset('assets/img/profile.jpg') }}" class="" height=" ">
        							</div>
        							<p class="white-color text_center">PROFILE</p>
    							</a>
    						</div>
    
    						<div class="img_footer">
    						    <a href="#">
        							<div class="img_footer_div">
        							    <img src="{{ asset('assets/img/feed.jpg') }}" height=" ">
        						    </div>
        							<p class="white-color text_center">SEARCH</p>
    							</a>
    						</div>
    
    						<div class="img_footer">
    						    <a href="#">
    						        <div class="img_footer_div">
        							    <img src="{{ asset('assets/img/feed.jpg') }}" class="">
        							</div>
        							<p class="white-color text_center">FEED</p>	
    							</a>
    						</div>
						</div>
					</div>
				</div>
			</div>
			</div>
		</div>
		<!-- ====================
		///// Scripts below /////
		===================== -->

		<!-- Core JS -->
		<script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script> <!-- jquery JS (https://jquery.com) -->
		<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.min.js') }}"></script> <!-- bootstrap JS (http://getbootstrap.com) -->

		<!-- Libs and Plugins JS -->
		<script src="{{ asset('assets/vendor/animsition/js/animsition.min.js') }}"></script> <!-- Animsition JS (http://git.blivesta.com/animsition/) -->
		<script src="{{ asset('assets/vendor/jquery.easing.min.js') }}"></script> <!-- Easing JS (http://gsgd.co.uk/sandbox/jquery/easing/) -->
		<script src="{{ asset('assets/vendor/isotope.pkgd.min.js') }}"></script> <!-- Isotope JS (http://isotope.metafizzy.co) -->
		<script src="{{ asset('assets/vendor/imagesloaded.pkgd.min.js') }}"></script> <!-- ImagesLoaded JS (https://github.com/desandro/imagesloaded) -->
		<script src="{{ asset('assets/vendor/owl-carousel/js/owl.carousel.min.js') }}"></script> <!-- Owl Carousel JS (https://owlcarousel2.github.io/OwlCarousel2/) -->
		<script src="{{ asset('assets/vendor/jquery.mousewheel.min.js') }}"></script> <!-- A jQuery plugin that adds cross browser mouse wheel support (https://github.com/jquery/jquery-mousewheel) -->
		<script src="{{ asset('assets/vendor/ytplayer/js/jquery.mb.YTPlayer.min.js') }}"></script> <!-- YTPlayer JS (more info: https://github.com/pupunzi/jquery.mb.YTPlayer) -->

		<script src="{{ asset('assets/vendor/lightgallery/js/lightgallery-all.min.js') }}"></script> <!-- lightGallery Plugins JS (http://sachinchoolur.github.io/lightGallery) -->
		
		<!-- Theme master JS -->
		<script src="{{ asset('assets/js/theme.js') }}"></script>
		
		<!-- Custom Js -->
		<script src="{{ asset('assets/js/custom.js') }}"></script>
		<!--==============================
		///// Begin Google Analytics /////
		============================== -->

		<!-- Paste your Google Analytics code here. 
		Go to http://www.google.com/analytics/ for more information. -->

		<!--==============================
		///// End Google Analytics /////
		============================== -->
		@yield('scripts')
	</body>
</html>