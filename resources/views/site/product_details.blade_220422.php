@extends('layouts.front')
@section('content')
<style>

	/*
    #main_slider .slick-list{
        max-height: 600px !important;
    }

    #main_slider .slick-slide {
        text-align:center !important;
        max-height: 600px !important;
        overflow:hidden;
    }
    */
	#main_slider .slick-slide {
		text-align:center !important;
		overflow:hidden;
	}

    #main_slider .slick-slide img,
    #main_slider .slick-slide video {
        display: inline-block !important;
        /*min-height: 700px !important;*/
    }

    /*.slider-nav .slick-list{*/
    /*    max-height: 170px !important;    */
    /*}*/

    /*.slider-nav .slick-slide {*/
    /*    text-align:center !important;*/
    /*    max-height: 170px !important;*/
    /*}*/

    /*.slider-nav .slick-slide img{*/
    /*    display: inline-block !important;*/
    /*    max-height: 170px !important;*/
    /*    width:100%;*/
    /*}*/

    #body-content {
        padding-bottom: 0px !important;
    }

    @media(max-width:767px){
        #body-content {
            padding-bottom: 146px !important;
        }
    }

    .slider-nav-item-block.thumbnails,
    .slider-nav-item-block.thumbnails img{
        overflow:hidden;
        height: 80px !important;
        width: 80px !important;
		-webkit-background-size: cover;
		-moz-background-size: cover;
		-o-background-size: cover;
		background-position-y: center;
		background-position-x: right;
		cursor: pointer;
    }

    #product_details_page{
        /*min-height: 100vh;*/
    }
	.slick-list {
		padding: 0px!important;
	}
    /*.slider-nav-item-block img{*/
    /*    width:100%;*/
    /*}*/

	.slider-nav .slick-track {
/*
		width: fit-content !important;
		width: intrinsic !important;
		width: -moz-fit-content !important;
*/


		width:100% !important;
		display:flex !important;

		transform: translate3d(0, 0, 0) !important;
		margin:0 !important;
		justify-content: center;
		align-items: center;
	}
	#product_details_page .slider-nav {
		margin-left: 0px!important;
	}
</style>
        <!-- ===================================
    	///// Begin Product Details section /////
    	==================================== -->
        <section id="product_details_page">
            <div class="container-fluid">
                <div class="row slider-row">
				<div class="col-xl-9 col-md-9 slider-main-lg slider-main-product">
					<!--<div class="slider mt-2" style="padding-left: 0px !important;">
	                      <div class="main">-->
	                          <div class="slider slider-for" id="main_slider" style="border-radius: 6px;">
	                              @foreach($product_details->shop_product_files as $product_file)
									  <?php
                                      $size = @getimagesize(public_path('images/post/new_images') .'/'.$product_file->file_name) ?? 0;
									  ?>
	                              @if($product_file->file_type == 0)
	                              <div>
                                      <img src="{{ asset('images/post/new_images') }}/{{ $product_file->file_name }}"
											 alt="..."
											@if($size[0] < $size[1])
											    class="main-slider-img-height main-slider-img"
											@elseif($size[0]> $size[1])
											    class="main-slider-img-width main-slider-img"
											@else
											    class="main-slider-img-square main-slider-img"
											@endif
                                    style="max-height: calc(100vh - 200px);"
									data="{{$product_file->file_width}}-{{ $product_file->file_height}}"
									  >
                                  </div>
	                              @else
	                              <div><video controls>
                                    <source src="{{ asset('public/images/post/new_images') }}/{{ $product_file->file_name }}">
                                    </video></div>
	                              @endif
	                              @endforeach

	                          </div>
							<div class="col-xs-12 stick-view-thumb">
	                          <div class="slider slider-nav ">
	                              @foreach($product_details->shop_product_files as $product_file)
	                              <div class="mr-30 slider-nav-item-block thumbnails" style="background-image: url({{ asset('images/post/new_images/thumb') }}/{{ $product_file->thumb }})">
									  <!--<img src="{{ asset('images/post/new_images/thumb') }}/{{ $product_file->thumb }}" class="w-100" />-->
								  </div>
	                              @endforeach
	                          </div>
							</div>
	                     <!-- </div>
	                </div>-->
				</div>
				<div class="col-xl-3 col-md-3 side_bar side-bar-right sidebar-main-lg">
					<span class="side-bar-right-container">
						<div class="row">
							<div class="col-md-6 col-xs-6">
								<h2 class="prozak_family" style="letter"><a href="{{ url('/') }}" class="grey-color"><i class="fa fa-caret-left"></i> BACK</a></h2>
							</div>
							<div class="col-md-6 col-xs-6">
								<div class="img_footer_div" style="float:right"><a href="#"><img src="{{ asset('assets/img/logo.png') }}" alt="logo"></a></div>
							</div>
						</div>
						<hr class="mb_0">
					<!-- 	<p class="grey-color ibm-light-family m_0" style="margin:-2px 0 0 0;">{{ $product_details->type }}</p> -->
						<h4 class="white-color prozak-light-family product-name-view">
							<span>{{ $product_details->name }}</span>
						</h4>
						<hr class="mb m_0">
						<div class="text-right">
						@if ((is_null($product_details->price)) ||  ($product_details->price=='') ||  ($product_details->price=='0'))
						<p class="white-color txt_prise prozak_family">Contact seller for price</p>
						@else
						<p class="white-color txt_prise prozak_family">{{ $product_details->price }} EUR</p>
						@endif
						</div>
						<hr class="mb m_0">
                        <p class="grey-color product-description m_0"><em>{{ $product_details->description }}</em></p>
                        <p class="product-details-tag m_0">{{ str_replace(",", " ", $product_details->tags) }}</p>
						<hr class="mb m_0 m-b-3">
						<!-- @foreach($buttons as $button)
							@if ((($button->name == "email_seller_id") || ($button->name == "go_to_website_id")) && ((!is_null($product_details->contact_email)) || ($product_details->contact_email !='')))
								<button class="detailed-button" data-id="{{$button->id}}" data-product-id="{{$product_details->id}}" id="{{$button->name}}" >{{$button->value}}</button>
							@elseif(($button->name == "hire_id"))
								<button class="detailed-button" data-id="{{$button->id}}" data-product-id="{{$product_details->id}}" id="{{$button->name}}" >{{$button->value}}</button>
							@endif
						@endforeach -->
						<button class="detailed-button" data-toggle="modal" data-target="#exampleModal">Buy From Artist</button>
						<button class="detailed-button" data-toggle="modal" data-target="#exampleModal">Hire Artist</button>

                        @foreach($product_details->user->shopUrls as $url)
                                <button class="detailed-button wd-lt-icon" onclick="window.open('http://{{ $url->url }}')">
                                    @if($url->is_instagramm)
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 0 0 24 24" height="512" viewBox="0 0 24 24" width="512"><linearGradient id="SVGID_1_" gradientTransform="matrix(0 -1.982 -1.844 0 -132.522 -51.077)" gradientUnits="userSpaceOnUse" x1="-37.106" x2="-26.555" y1="-72.705" y2="-84.047"><stop offset="0" stop-color="#fd5"/><stop offset=".5" stop-color="#ff543e"/><stop offset="1" stop-color="#c837ab"/></linearGradient><path d="m1.5 1.633c-1.886 1.959-1.5 4.04-1.5 10.362 0 5.25-.916 10.513 3.878 11.752 1.497.385 14.761.385 16.256-.002 1.996-.515 3.62-2.134 3.842-4.957.031-.394.031-13.185-.001-13.587-.236-3.007-2.087-4.74-4.526-5.091-.559-.081-.671-.105-3.539-.11-10.173.005-12.403-.448-14.41 1.633z" fill="url(#SVGID_1_)"/><path d="m11.998 3.139c-3.631 0-7.079-.323-8.396 3.057-.544 1.396-.465 3.209-.465 5.805 0 2.278-.073 4.419.465 5.804 1.314 3.382 4.79 3.058 8.394 3.058 3.477 0 7.062.362 8.395-3.058.545-1.41.465-3.196.465-5.804 0-3.462.191-5.697-1.488-7.375-1.7-1.7-3.999-1.487-7.374-1.487zm-.794 1.597c7.574-.012 8.538-.854 8.006 10.843-.189 4.137-3.339 3.683-7.211 3.683-7.06 0-7.263-.202-7.263-7.265 0-7.145.56-7.257 6.468-7.263zm5.524 1.471c-.587 0-1.063.476-1.063 1.063s.476 1.063 1.063 1.063 1.063-.476 1.063-1.063-.476-1.063-1.063-1.063zm-4.73 1.243c-2.513 0-4.55 2.038-4.55 4.551s2.037 4.55 4.55 4.55 4.549-2.037 4.549-4.55-2.036-4.551-4.549-4.551zm0 1.597c3.905 0 3.91 5.908 0 5.908-3.904 0-3.91-5.908 0-5.908z" fill="#fff"/></svg>
                                            Go TO Instagram
                                        </span>
                                    @elseif($url->is_facebook)
                                        <span style="fill: #4867aa">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" x="0px" y="0px" width="50" height="50" viewBox="0 0 50 50" style="null" class="icon icons8-Facebook-Filled" >    <path d="M40,0H10C4.486,0,0,4.486,0,10v30c0,5.514,4.486,10,10,10h30c5.514,0,10-4.486,10-10V10C50,4.486,45.514,0,40,0z M39,17h-3 c-2.145,0-3,0.504-3,2v3h6l-1,6h-5v20h-7V28h-3v-6h3v-3c0-4.677,1.581-8,7-8c2.902,0,6,1,6,1V17z"></path></svg>
                                        </span>
                                        Go To Facebook
                                    @else
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="#827f7f" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g><g><path d="M256,0C114.508,0,0,114.497,0,256c0,141.492,114.497,256,256,256c141.492,0,256-114.497,256-256    C512,114.508,397.503,0,256,0z M156.946,52.753c-7.747,21.099-14.25,42.547-19.499,64.295    c-15.341-4.661-30.432-10.25-45.243-16.762C110.958,80.583,132.812,64.54,156.946,52.753z M72.338,124.24    c19.171,8.878,38.791,16.3,58.802,22.253c-5.806,30.993-9.126,62.538-9.914,94.507H30.502    C33.265,198.559,47.728,158.39,72.338,124.24z M72.338,387.76C47.728,353.61,33.265,313.441,30.502,271h90.724    c0.788,31.969,4.108,63.514,9.914,94.507C111.129,371.46,91.51,378.882,72.338,387.76z M92.204,411.715    c14.811-6.513,29.901-12.102,45.243-16.762c5.25,21.748,11.752,43.196,19.499,64.295    C132.824,447.467,110.968,431.427,92.204,411.715z M241,481.498c-15.754-1.025-31.197-3.655-46.135-7.825    c-11.865-28.12-21.335-56.952-28.398-86.363c24.318-5.437,49.199-8.616,74.533-9.515V481.498z M241,347.777    c-27.448,0.907-54.405,4.307-80.751,10.175c-5.234-28.529-8.25-57.55-9.013-86.952H241V347.777z M241,241h-89.764    c0.763-29.402,3.779-58.423,9.013-86.952c26.346,5.868,53.303,9.268,80.751,10.175V241z M241,134.205    c-25.334-0.899-50.215-4.078-74.533-9.515C173.53,95.279,183,66.447,194.865,38.327c14.938-4.17,30.381-6.8,46.135-7.825V134.205z     M439.662,124.24c24.61,34.15,39.073,74.319,41.836,116.76h-90.724c-0.788-31.969-4.108-63.514-9.914-94.507    C400.871,140.54,420.49,133.118,439.662,124.24z M419.796,100.285c-14.811,6.513-29.901,12.102-45.243,16.762    c-5.25-21.748-11.752-43.196-19.499-64.295C379.176,64.533,401.032,80.573,419.796,100.285z M271,30.502    c15.754,1.025,31.197,3.655,46.135,7.825C329,66.447,338.47,95.279,345.533,124.69c-24.318,5.437-49.199,8.616-74.533,9.515    V30.502z M271,164.223c27.448-0.907,54.405-4.307,80.751-10.175c5.234,28.529,8.25,57.55,9.013,86.952H271V164.223z     M317.134,473.672c-14.937,4.171-30.38,6.801-46.134,7.826V377.795c25.334,0.899,50.215,4.078,74.533,9.515    C338.469,416.721,329,445.553,317.134,473.672z M271,347.777V271h89.764c-0.763,29.402-3.779,58.423-9.013,86.952    C325.405,352.084,298.448,348.684,271,347.777z M355.054,459.247c7.747-21.099,14.25-42.547,19.499-64.295    c15.341,4.661,30.432,10.25,45.243,16.762C401.042,431.417,379.188,447.46,355.054,459.247z M439.662,387.76    c-19.171-8.878-38.791-16.3-58.802-22.253c5.806-30.993,9.126-62.538,9.914-94.507h90.724    C478.735,313.441,464.272,353.61,439.662,387.76z"/></g></g></svg>
                                        </span>
                                        Go To Website
                                    @endif
                                </button>
                        @endforeach
						<div class="prozak-light-family about-seller">About The Seller</div>
						<hr class="mb_0 m-t-0">
						<div class="profile-container d-inline-flex">
							<a href="/{{$product_details->user->tagname}}" class="product-page-user-click" data-type="user" data-user-id="{{$product_details->user_id}}">
							<img src="{{ $product_details->shop_profile->shop_image_url }}" class="img_logo">
							</a>
							<div class="heading grey-color ml-2">
								<div class="detailed-shop-name">
									<a href="/{{$product_details->user->tagname}}" class="product-page-user-click detailed-user-name-link" data-type="user" data-user-id="{{$product_details->user_id}}">{{ $product_details->user->name }}</a>
								</div>
								<div class="detailed-user-name">
									<a href="/{{$product_details->user->tagname}}" class="product-page-user-click detailed-user-tag-name-link" data-type="user" data-user-id="{{$product_details->user_id}}">&commat;{{$product_details->user->tagname}}</a>
								</div>
								<div class="grey-color detailed-user-post">
									Painter
								</div>
							</div>

						</div>
						<hr class="mb_0">
						<div class="about-user-info">
							  <span class="more">
							      {{ $product_details->user->shopProfile->shop_description }}
                            </span>
						</div>
						<hr class="mb_0">
					</div>
				</div>
			</div>
			</div>
        </section>
        <!-- ===================================
    	///// Begin Product Details section /////
    	==================================== -->
		<!-- Modal -->
		<div id="emailModal" class="modal fade " role="dialog">
			<div class="modal-dialog ">
				<div class="table">
					<div class="table-cell">
				<!-- Modal content-->
				<div class="modal-content ">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Email {{ $product_details->shop_profile->company_name }}</h4>
					</div>
					<div class="modal-body">
						<form role="form" method="POST" action="#" id="emailForm">
							<div class="form-group">
								<div>
									<input type="email" class="form-control " name="email" id="email" placeholder="Your email">
								</div>
							</div>
							<div class="form-group">
								<div>
									<input type="text" class="form-control" name="name" id="name" placeholder="Your name">
								</div>
							</div>
							<div class="form-group">
								<div>
									<textarea class="form-control" rows ="6" placeholder="Your message" name="message" id="message"></textarea>
								</div>
							</div>
							<div class="form-group">
								<div id="error_messages" class="error_messages"></div>
							</div>
							<input type="hidden" name="site_button_id" value="0" id="site_button_id">
							<input type="hidden" name="shop_product_id" value="0" id="shop_product_id">
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="detailed-button" id ="email-form-sending" >Send email</button>
					</div>
				</div>
					</div>
				</div>
			</div>
		</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Test modal Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="margin: 15px">
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has</p>
                <form>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                        <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Password</label>
                        <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                        <label class="form-check-label" for="exampleCheck1">Check me out</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" style="margin: 0" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" style="margin: 0">Save changes</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<link rel='stylesheet' href='https://rawgit.com/kenwheeler/slick/master/slick/slick.css'>
    	<link rel='stylesheet' href='https://rawgit.com/kenwheeler/slick/master/slick/slick-theme.css'>
  <script src='https://kenwheeler.github.io/slick/slick/slick.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js'></script>

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(window).on("load", function() {

    $('.slider-for').slick({
      infinite: true,
      fade: true,
      cssEase: 'linear',
      //variableWidth: true,
      slidesToShow: 1,
      slidesToScroll: 1,
      arrows: true,
      asNavFor: '.slider-nav',
      autoplay: false,
      autoplaySpeed: 2000,
      centerMode: true,
     // adaptiveHeight: true,

    });

    $('.slider-nav').slick({
        centerMode: true,
        centerPadding: '0px',
        slidesToShow: 4,
      slidesToScroll: 1,
      asNavFor: '.slider-for',
      dots: false,
      focusOnSelect: true,
      arrows: false,
      autoplaySpeed: 2000,
      infinite: false,
      variableWidth: true,

    });

    $('a[data-slide]').click(function (e) {
      e.preventDefault();
      var slideno = $(this).data('slide');
      $('.slider-nav').slick('slickGoTo', slideno - 1);
    });
    $("#filter").click(function () {
      $(".filter-div").show();
    });
    $(".filter-div").mouseleave(function () {
      $(".filter-div").hide();
    });

    $('.product-page-user-click').click(function(e){

        e.preventDefault();
        e.stopImmediatePropagation();
        var user_id = $(this).data('user-id');

        $.ajax({
            type: "POST",
            url: "{{url('toggle_options')}}",
            data: {user_id:user_id, type:"user", action:"user-selection"},
            success:function(response)
            {
                window.location.href="{{ route('site.index') }}";
            }
        });
    });

    $("#emailForm").validate({
        rules: {
            name: "required",
            email: {
                required: true,
                email: true
            },
            message: "required"
        }
    });

    $("#email_seller_id, #hire_id").click(function(){
        $("#emailForm")[0].reset();
        $("#error_messages").html('')
        var site_button_id = $(this).data("id");
        var shop_product_id = $(this).data("product-id");
        $("#site_button_id").val(site_button_id);
        $("#shop_product_id").val(shop_product_id);

        $("#emailModal").modal('show');
    });


	$("#email-form-sending").click(function(){
	    if($("#emailForm").valid()) {
			var url='{{route('send-product-email')}}';
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    site_button_id: $("#site_button_id").val(),
                    shop_product_id: $("#shop_product_id").val(),
                    email: $("#email").val(),
                    name: $("#name").val(),
                    message: $("#message").val()
                },
                success:function(response)
                {
                    if(response.success ==false) {
                        $("#error_messages").html(response.error);
                    }
                    if(response.success ==true) {
                        $("#emailModal").modal('hide');
                    }
                },
				error:function() {
                    $("#error_messages").html('System error');
				}
            });
		};
	});

    $(".detailed-button").click(function(){
		var url='{{route('set-product-click')}}';
		$.ajax({
			type: "POST",
			url: url,
			data: {
				site_button_id: $("#site_button_id").val(),
				shop_product_id: $("#shop_product_id").val(),
			},
			success:function(response)
			{
				console.log("set-product-click:success")
			},
			error:function() {
				console.log("set-product-click:error")
			}
		});
    });


	});

	$('.slider-for').on('init', function() {

	});

  </script>
@endsection
