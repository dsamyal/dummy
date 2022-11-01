@extends('layouts.front')
@section('content')

	<!-- ===================================
	///// Begin gallery single section /////
	==================================== -->
	<section id="gallery-single-section">
	    <div class="container-fluid">
		    <div class="isotope-wrap"> <!-- add/remove class "tt-wrap" to enable/disable element boxed layout (class "tt-boxed" is required in <body> tag! ) -->
			
			<!-- Begin isotope
			===================
			* Use class "gutter-1", "gutter-2" or "gutter-3" to add more space between items.
			* Use class "col-1", "col-2", "col-3", "col-4", "col-5" or "col-6" for columns.
			-->
			<div class="isotope col-4 gutter-3">

				<!-- Begin isotope top content -->
				<div class="isotope-top-content gallery-share-on">

					<!-- Begin gallery share button 
					================================
					* Use class "gs-right" to align gallery share button.
					-->
					<!-- <a href="#0" class="gallery-share gs-right" data-toggle="modal" data-target="#modal-64253091" title="Share this gallery"><i class="fas fa-share-alt"></i></a> -->
					<!-- End gallery share button -->

					<!-- Begin modal 
					=================
					* Use class "modal-center" to enable modal center position (use for short content only!).
					* Use class "modal-left" to enable left sidebar modal.
					* Use class "modal-right" to enable right sidebar modal.
					-->
					
					<!-- End modal -->

				</div>
				<!-- End isotope top content -->

				<!-- Begin isotope items wrap 
				==============================
				* Use classes "gsi-color", "gsi-zoom" or "gsi-simple" to change gallery single item cover variations.
				-->
				<div class ="gallery-full-width">
				<div id="post-container" data-count= "{{ $count }}">
			
					@include('site._post')
					
						</div>
						</div>
			
				<!-- End isotope items wrap -->


				<!-- Begin isotope pagination 
				============================== -->
<!-- 				<div class="isotope-pagination"> -->
<!-- 					<div class="iso-load-more"> -->
<!-- 						<a class="btn btn-dark-bordered btn-lg" href="">View More <i class="fas fa-refresh"></i></a> -->
<!-- 					</div> -->
<!-- 				</div> -->
				<!-- End isotope pagination -->

			</div>
			<!-- End isotope -->

		</div> <!-- /.isotope-wrap -->
		</div>
	</section>
	<!-- End gallery single section -->
@endsection