@extends('layouts.front')
@section('header_left_content')
<h2 class="prozak_family back-button" style="letter"><a href="{{ url('/') }}" class="grey-color"><i class="fa fa-caret-left"></i> BACK</a></h2>
@endsection
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
            <div class="container-fluid" style="padding-right:0px !important;">
                <div class="row">
				<div class="col-xl-9 col-md-9 slider-main-lg">
					<!--<div class="slider mt-2" style="padding-left: 0px !important;">
	                      <div class="main">-->
	                          <div class="slider slider-for" id="main_slider" style="border-radius: 6px;">
	                              @foreach($product_details->shop_product_files as $product_file)
									  <?php
									  $size = getimagesize(asset('images/post/new_images') .'/'.$product_file->file_name);
									  ?>
	                              @if($product_file->file_type == 0)
	                              <div><img src="{{ asset('images/post/new_images') }}/{{ $product_file->file_name }}"
											 alt="..."
											@if($size[0] < $size[1])
											class="main-slider-img-height main-slider-img"
											@elseif($size[0]> $size[1])
											class="main-slider-img-width main-slider-img"
											@else
											class="main-slider-img-square main-slider-img"
											@endif
									data="{{$product_file->file_width}}-{{ $product_file->file_height}}"
									  ></div>
	                              @else
	                              <div><video controls>
                                    <source src="{{ asset('public/images/post/new_images') }}/{{ $product_file->file_name }}">
                                    </video></div>
	                              @endif
	                              @endforeach

	                          </div>
							<div style="bottom:10px;position:fixed;" class="col-xl-9 col-md-9">
	                          <div class="slider slider-nav" style="padding-top: 20px;/**/">
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
					<div class="side-bar-right-container">
						<div class="profile-container d-inline-flex">
							<a href="/{{$product_details->user->tagname}}" class="product-page-user-click" data-type="user" data-user-id="{{$product_details->user_id}}">
							<img src="{{ $product_details->shop_profile->shop_image_url }}" class="img_logo">
							</a>
							<div class="heading grey-color ml-2">
								<div class="detailed-shop-name">
									<a href="/{{$product_details->user->tagname}}" class="product-page-user-click detailed-user-name-link" data-type="user" data-user-id="{{$product_details->user_id}}">{{ $product_details->shop_profile->company_name }}</a>
								</div>
								<div class="detailed-user-name">
									<a href="/{{$product_details->user->tagname}}" class="product-page-user-click detailed-user-tag-name-link" data-type="user" data-user-id="{{$product_details->user_id}}">&commat;{{$product_details->user->tagname}}</a>
								</div>
							</div>

						</div>
						<hr class="mb_0">
						<p class="grey-color ibm-light-family m_0">{{ $product_details->type }}</p>
						<h4 class="white-color prozak-light-family" style="font-size:25px; letter-spacing:2px; line-height:1;">
							<span>{{ $product_details->name }}</span>
						</h4>
						<hr class="mb m_0">
						<p class="grey-color m_0">Description</p>
						<p class="white-color m_0">{{ $product_details->description }}</p>
						<hr class="mb m_0">
						<p class="grey-color m_0">Tags</p>
						<p class="grey-color m_0">{{ str_replace(",", " ", $product_details->tags) }}</p>
						<hr class="mb m_0">
						<div class="text-right">
						@if ((is_null($product_details->price)) ||  ($product_details->price=='') ||  ($product_details->price=='0'))
						<p class="grey-color txt_prise prozak_family">Contact seller for price</p>
						@else
						<p class="grey-color txt_prise prozak_family">{{ $product_details->price }} EUR</p>
						@endif
						</div>
						<hr class="mb m_0">
						@foreach($buttons as $button)
						<button class="detailed-button" data-id="{{$button->id}}" data-product-id="{{$product_details->id}}" id="{{$button->name}}" >{{$button->value}}</button>
						@endforeach
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

    $('.slider-for').slick({
      infinite: true,
      fade: true,
      cssEase: 'linear',
      slidesToShow: 1,
      slidesToScroll: 1,
      arrows: true,
      asNavFor: '.slider-nav',
      autoplay: false,
      autoplaySpeed: 2000,
      //centerMode: true,
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
  </script>
@endsection