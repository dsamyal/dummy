<!-- third CHECK -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.css">

<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/custom1.css') }}">

<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.js"></script>
@extends('layouts.front')
@section('content')
    <style>  
        .owl-drag { position: relative;}
        .owl-stage-outer { width: calc( 100% - 15px); overflow: hidden; position: relative; z-index: 9999999;}
        .owl-stage-outer { z-index: unset !important; }
        .owl-item { display: inline-block; }
        .owl-nav { margin: 0;  position: absolute; top: 50%; -ms-transform: translateY(-50%);  transform: translateY(-50%);width: 100%; }
        .owl-next, .owl-prev {width:36px !important;height:36px !important; background-repeat:no-repeat; opacity: 1 !important; background-size: 100%; display: inline-block; font-size: 0; border:0px solid transparent; background-color: transparent;}
        .homepage-filter-bar .owl-next, .homepage-filter-bar .owl-prev {width:33px !important;height:33px !important; background-repeat:no-repeat; opacity: 1 !important; background-size: 100%; display: inline-block; font-size: 0; border:0px solid transparent; background-color: transparent;}
        .owl-prev { float: left; }
        .owl-prev.disabled , .owl-next.disabled { display: none;}
        .owl-next {  float: right; }
        @media (max-width:767px){
            .owl-stage-outer {    width: 100%;    margin-left: 0px; }
            .owl-nav { display: none;}
        }
    </style>
    <div class="header-fixed">
        @include('layouts.frontheader')
        <div class="container-fluid">
            <div class="homepage-filter-tags-bar"> 
                <div class="menu-wrapper">
                    <div class="owl-carousel owl-theme">
                        @if ($filter_user_name !='')
                            <span class="filter-gallery-toggle-active product-filter-disable  prozak-light-family text-uppercase user_filter" style="padding-right:10px;" data-type="{{$filter_user_name}}" data-filter-type="user">{{$filter_user_name}} <span class="filter-disabled_1"><div class="clear_user_filter"><i class="fa fa-times-circle" aria-hidden="true"></i></div></span></span>
                        @else
                            <div class="item filter-gallery-groups prozak-light-family filter-gallery-toggle-category slick-current text-uppercase @if($filter_value == 'All') filter-gallery-toggle-active @endif " data-type="All" data-filter-type="type" tabindex="0" autofocus>
                                All
                            </div>
                        @endif
                        @foreach($types as $count => $type)
                            <div class="item filter-gallery-groups filter-gallery-toggle-category @if($filter_value == $type->type) filter-gallery-toggle-active @endif" data-type="{{$type->type}}" data-cat-type="cat" data-filter-type="type" tabindex="{{$count + 1}}">{{$type->type}}</div>
                        @endforeach
                    </div>
                    <!-- <ul class="col-xs-12 text-left filter-tags-bar menu">
                        <li class="item filter-gallery-groups prozak-light-family filter-gallery-toggle-category slick-current text-uppercase @if($filter_value == "All") filter-gallery-toggle-active @endif " data-type="All" data-filter-type="type" tabindex="0" autofocus>All</li>
                        @foreach($types as $count => $type)
                            <li class="item filter-gallery-groups filter-gallery-toggle-category @if($filter_value == $type->type) filter-gallery-toggle-active @endif" data-type="{{$type->type}}" data-cat-type="cat" data-filter-type="type" tabindex="{{$count + 1}}">{{$type->type}}</li>
                        @endforeach  
                    </ul> -->
                    <!-- <div class="paddles">
                        <button class="left-paddle paddle slick-prev slick-arrow">
                            <
                        </button>
                        <button class="right-paddle paddle slick-next slick-arrow">
                            >
                        </button> -->
                </div>
            </div>
        </div>
        <div class="homepage-filter-bar">
            <div class="row">
                <div class="mob-space">
                    <div class="@if($filter_value !='All') product-filter-disable subcategory-name @else filter-gallery-toggle @endif" @if($filter_value !='All') style="padding-right:10px;" @endif data-type="{{$filter_value}}" data-filter-type="type">{{$filter_value}} @if($filter_value !='All') @if($subcategories && $subcategories->count() > 0) {{ ':' }} @endif<span class="filter-disabled"><div>x</div></span> @endif</div>
                    @if($subcategories && $subcategories->count() > 0)
                        <div class="menu-wrapper subcategory">
                            <div class="owl-carousel_1 owl-theme col-xs-12 text-left filter-tags-bar menu ">
                                <?php $temp_array = []; ?>
                                @foreach($subcategories as $count => $subcategory)

                                    <?php $a = explode('.', $subcategory->title); ?>

                                    <?php foreach ($a as $key => $value) { 
                                        if(in_array($value, $temp_array)){

                                        } else {
                                            $temp_array[] = $value; ?>
                                            <div class="item filter-gallery-groups filter-gallery-toggle-category @if($filter_subvalue && in_array($value,$filter_subvalue)) filter-gallery-toggle-active @endif" data-cat-type="subcat" data-filter-type="type" data-type="{{ $filter_value }}" data-subtype="{{ $value }}" tabindex="{{ $count+1 }}">{{ $value }}</div>
                                        <?php } ?>
                                    <?php } ?>
                                @endforeach
                            </div>
                            <!-- <ul class="col-xs-12 text-left filter-tags-bar menu owl-slide-subcat" >
                                @foreach($subcategories as $count => $subcategory)
                                    <li class="item filter-gallery-groups filter-gallery-toggle-category @if($filter_subvalue && in_array($subcategory->title,$filter_subvalue)) filter-gallery-toggle-active @endif" data-cat-type="subcat" data-filter-type="type" data-type="{{ $filter_value }}" data-subtype="{{ $subcategory->title }}" tabindex="{{ $count+1 }}">{{ $subcategory->title }}</li>
                                @endforeach
                            </ul> -->
                            <!-- <div class="paddles">
                                <button class="left-paddle paddle hidden slick-prev slick-arrow">
                                    &lt;
                                </button>
                                <button class="right-paddle paddle slick-next slick-arrow">
                                    &gt;
                                </button>
                            </div> -->
                        </div>
                    @endif
                </div>
            </div>
			<div class="randomBlock text-right">
                <div class="filter_btn">
                    <img src="{{ asset('assets/img/new_icon_filter.png') }}" width="40" height="40">
                </div>
                <div class="filter_text">
                    <h2 class="m-0 prozak-light-family">Done</h2>
                </div>
                <div class="hide_show_filter">
				    <div class="filter_row">
                        <h2 class="m-0 prozak-light-family">Show Prices</h2>
                        <div class="radio0">
                            <div class="wrap">
                                <input type="radio" id="radio_one_show_price" class="ctm_price" name="show_price" value="no" />
                                <label for="radio_one_show_price">No</label>

                                <input type="radio" id="radio_two_show_price" class="ctm_price" name="show_price" value="yes" />
                                <label for="radio_two_show_price">Yes</label>

                                <div class="bar"></div>
                            </div>
                        </div>
					</div>
					<div class="filter_row">
                        <h2 class="m-0 prozak-light-family">For Sale Only</h2>
                        <div class="radio1 <?php if($post_type == 'sale'){ ?> radio_two_sale_only <?php } ?>">
                            <div class="wrap">
                                <input type="radio" id="radio_one_sale_only" name="sale_only" class="ctm_filter" value="no" checked data-type="all" data-filter-type="ordering" />
                                <label for="radio_one_sale_only">No</label>

                                <input type="radio" id="radio_two_sale_only" name="sale_only" class="ctm_filter" <?php if($post_type == 'sale'){ ?> checked <?php } ?> value="yes" data-type="sale" data-filter-type="ordering" />
                                <label for="radio_two_sale_only">Yes</label>

                                <div class="bar"></div>
                            </div>
                        </div>
	               </div>
	               <div class="filter_row">
                        <h2 class="m-0 prozak-light-family">Sort By</h2>
                        <div class="radio2 <?php if($gallery_type == 'latest'){ ?> radio_two_sort_by <?php } ?>">
                            <div class="wrap">
                                <input type="radio" id="radio_one_sort_by" name="sort_by" class="ctm_filter" value="random" checked data-type="random" data-filter-type="ordering" />
                                <label for="radio_one_sort_by">Random</label>

                                <input type="radio" id="radio_two_sort_by" name="sort_by" class="ctm_filter" <?php if($gallery_type == 'latest'){ ?> checked <?php } ?> value="latest" data-type="latest" data-filter-type="ordering" />
                                <label for="radio_two_sort_by">Latest</label>

                                <div class="bar"></div>
                            </div>
                        </div>
                    </div>
                    <form class="form">
                        <div class="switch-field">
                            <input type="radio" id="radio-four" name="switch-two" class="ctm_filter_gallery" value="maybe" data-type="justified-without" data-show="square" <?php if($gallery_view == 'justified-without'){ ?> checked <?php } ?> />
                            <label for="radio-four" class="<?php if($gallery_view == 'justified-without'){ ?> active <?php } ?>">
                                <svg width="100%" height="100%" viewBox="0 0 4267 4267" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g id="Profile-gallery" serif:id="Profile gallery"><path d="M2000,291.667c0,-160.976 -130.691,-291.667 -291.667,-291.667l-1416.67,0c-160.976,0 -291.667,130.691 -291.667,291.667l-0,2250c-0,160.975 130.691,291.666 291.667,291.666l1416.67,0c160.976,0 291.667,-130.691 291.667,-291.666l0,-2250Z" style="fill:#8b8a8f;"/><path d="M2000,3391.67c0,-160.976 -130.691,-291.667 -291.667,-291.667l-1416.67,-0c-160.976,-0 -291.667,130.691 -291.667,291.667l0,583.333c0,160.975 130.691,291.667 291.667,291.667l1416.67,-0c160.976,-0 291.667,-130.692 291.667,-291.667l0,-583.333Z" style="fill:#8b8a8f;"/><path d="M4266.67,291.667c-0,-160.976 -130.692,-291.667 -291.667,-291.667l-1416.67,0c-160.975,0 -291.666,130.691 -291.666,291.667l-0,3683.33c-0,160.975 130.691,291.667 291.666,291.667l1416.67,-0c160.975,-0 291.667,-130.692 291.667,-291.667l-0,-3683.33Z" style="fill:#8b8a8f;"/></g></svg>
                            </label>
                            
                            <input type="radio" id="radio-three" name="switch-two" class="ctm_filter_gallery" value="yes" data-type="square" data-show="justified-with" <?php if($gallery_view == 'square'){ ?> checked <?php } ?> />
                            <label for="radio-three" class="<?php if($gallery_view == 'square'){ ?> active <?php } ?>">
                                <svg width="100%" height="100%" viewBox="0 0 4267 4267" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g id="Profile-gallery" serif:id="Profile gallery"><path d="M2000,291.667c0,-160.976 -130.691,-291.667 -291.667,-291.667l-1416.67,0c-160.976,0 -291.667,130.691 -291.667,291.667l-0,1416.67c-0,160.976 130.691,291.667 291.667,291.667l1416.67,0c160.976,0 291.667,-130.691 291.667,-291.667l0,-1416.67Z" style="fill:#8b8a8f;"/><path d="M2000,2558.33c0,-160.975 -130.691,-291.666 -291.667,-291.666l-1416.67,-0c-160.976,-0 -291.667,130.691 -291.667,291.666l-0,1416.67c-0,160.975 130.691,291.667 291.667,291.667l1416.67,-0c160.976,-0 291.667,-130.692 291.667,-291.667l0,-1416.67Z" style="fill:#8b8a8f;"/><path d="M4266.67,2558.33c-0,-160.975 -130.692,-291.666 -291.667,-291.666l-1416.67,-0c-160.975,-0 -291.666,130.691 -291.666,291.666l-0,1416.67c-0,160.975 130.691,291.667 291.666,291.667l1416.67,-0c160.975,-0 291.667,-130.692 291.667,-291.667l-0,-1416.67Z" style="fill:#8b8a8f;"/><path d="M4266.67,291.667c-0,-160.976 -130.692,-291.667 -291.667,-291.667l-1416.67,0c-160.975,0 -291.666,130.691 -291.666,291.667l-0,1416.67c-0,160.976 130.691,291.667 291.666,291.667l1416.67,0c160.975,0 291.667,-130.691 291.667,-291.667l-0,-1416.67Z" style="fill:#8b8a8f;"/></g></svg>
                            </label>

                            <input type="radio" id="radio-five" name="switch-two" class="ctm_filter_gallery" value="no" data-type="justified-with" data-show="justified-without" <?php if($gallery_view == 'justified-with'){ ?> checked <?php } ?>  />
                            <label for="radio-five" class="<?php if($gallery_view == 'justified-with'){ ?> active <?php } ?>">
                                <svg width="100%" height="100%" viewBox="0 0 4267 4267" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g id="gallery-info" serif:id="gallery info"><path d="M4266.67,1183.33c-0,-160.975 -130.692,-291.666 -291.667,-291.666l-3683.33,-0c-160.976,-0 -291.667,130.691 -291.667,291.666l-0,1900c-0,160.976 130.691,291.667 291.667,291.667l3683.33,-0c160.975,-0 291.667,-130.691 291.667,-291.667l-0,-1900Z" style="fill:#8b8a8f;"/><path d="M4266.67,291.667c-0,-160.976 -130.692,-291.667 -291.667,-291.667l-3683.33,-0c-160.976,-0 -291.667,130.691 -291.667,291.667l-0,125c-0,114.982 93.351,208.333 208.333,208.333l3850,0c114.983,0 208.334,-93.351 208.334,-208.333l-0,-125Z" style="fill:#8b8a8f;"/><path d="M4266.67,3850c-0,-114.982 -93.351,-208.333 -208.334,-208.333l-3850,-0c-114.982,-0 -208.333,93.351 -208.333,208.333l-0,125c-0,160.975 130.691,291.667 291.667,291.667l3683.33,-0c160.975,-0 291.667,-130.692 291.667,-291.667l-0,-125Z" style="fill:#8b8a8f;"/></g></svg>
                                <!-- <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="33" height="4.8" rx="1" fill="#282828"/>
                                    <rect y="28.2" width="33" height="4.8" rx="1" fill="#282828"/>
                                    <rect y="8.60001" width="33" height="15.8" rx="1" fill="#282828"/>
                                </svg> -->
                            </label>
                        </div>

                        
                        {{-- <span class="filter-gallery-icon" data-type="square" data-show="justified-with">
                           <!--  <img class="img-responsive" src="{{ asset('assets/img/icon_gallery_square_new.png') }}" /> -->
                            <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="33" height="4.8" rx="1" fill="#EAEAED"/>
                                <rect y="28.2" width="33" height="4.8" rx="1" fill="#EAEAED"/>
                                <rect y="8.60001" width="33" height="15.8" rx="1" fill="#EAEAED"/>
                            </svg>
                            </span>
                        
                            <span class="filter-gallery-icon" data-type="justified-without" data-show="square">
                                <!-- <img class="img-responsive" src="{{ asset('assets/img/icon_gallery_justified_new.png') }}" />
                                     -->
                                <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="15.4" height="15.4" rx="3" fill="#EAEAED"/>
                                    <rect y="17.6" width="15.4" height="15.4" rx="3" fill="#EAEAED"/>
                                    <rect x="17.6" width="15.4" height="15.4" rx="3" fill="#EAEAED"/>
                                    <rect x="17.6" y="17.6" width="15.4" height="15.4" rx="3" fill="#EAEAED"/>
                                </svg>
                            </span>
                        
                            <span class="filter-gallery-icon" data-type="justified-with" data-show="justified-without">
                                <!--  <img class="img-responsive" src="{{ asset('assets/img/icon_gallery_info_new.png') }}" /> -->
                                <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="14.6667" height="19.1172" rx="3" fill="#EAEAED"/>
                                    <rect y="22.9862" width="14.6667" height="10.0138" rx="3" fill="#EAEAED"/>
                                    <rect x="18.5625" width="14.4375" height="33" rx="3" fill="#EAEAED"/>
                                </svg>
                        </span> --}}
                        
                    </form>
                </div>


                    {{-- @if($post_type == 'sale')
                        <span class="product-filter-order filter-gallery-toggle" data-type="all" data-filter-type="ordering">All</span>
                    @else
                        <span class="product-filter-order filter-gallery-toggle" data-type="sale" data-filter-type="ordering">For Sale</span>
                    @endif

                    @if($gallery_type == 'random')
                        <span class="product-filter-order filter-gallery-toggle" data-type="latest" data-filter-type="ordering">Latest</span>
                    @else
                        <span class="product-filter-order filter-gallery-toggle" data-type="random" data-filter-type="ordering">Random</span>
                    @endif --}}
                </div>
        </div>
    </div>
    <!-- </div> -->
    <section id="gallery-single-section" style="margin-top:200px;">
        <div class="container-fluid">
            <div class="mange-space">
                <div id="post-container" data-count= "{{ $count }}">
                    @include('site._post')
                </div>
            </div>
        </div>
        <input type="hidden" id = "toggle_options_url" value="{{url('toggle_options')}}">
        <input type="hidden" id = "author_filter_url" value="{{url('author_filter')}}">
        <input type="hidden" id = "home_url" value="{{ url('/') }}">
    </section>
    <!-- End gallery single section -->
@endsection

@section('scripts')
    <!-- <link rel='stylesheet' href='https://rawgit.com/kenwheeler/slick/master/slick/slick.css'>
    <link rel='stylesheet' href='https://rawgit.com/kenwheeler/slick/master/slick/slick-theme.css'> -->
    <link rel='stylesheet' href="{{ asset('assets/css/menu.css') }}">
    <script src="{{ asset('assets/js/menu.js?ver=123') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(".search-icon").click(function(){
            $("#main-search-icon").addClass('hidden');

            $("#search_close").addClass('ctm_show');

            $(this).parent().addClass('ctm_form_class');
            
            $("#main-search-filter").removeClass('hidden');
            $("#main-search-button").removeClass('hidden');
            $("#main-search-filter").focus();
        });

        // $('.filter-gallery-toggle-category, .product-filter-order').click(function(){
        $('.filter-gallery-toggle-category').click(function(){
            var type_switch_two = jQuery('input[name="switch-two"]:checked').data("type");

            var catOrSubCat = $(this).data('cat-type');            
            var filterType = $(this).data('filter-type');
            var gallery_type = $(this).attr('data-type');
            var post_type = $(this).attr('data-type');
            var gallery_subtype = $(this).attr('data-subtype');
            //$('.filter-gallery-toggle-category, .product-filter-order').removeClass('filter-gallery-toggle-active');

            $(this).toggleClass('filter-gallery-toggle-active');

            var is_subcat_selected = '';
            if($('.subcategory ul li').hasClass('filter-gallery-toggle-active')) {
                is_subcat_selected = 'not empty';
            } else {
                is_subcat_selected = 'empty';
            }

            $.ajax({
                type: "POST",
                url: "{{url('toggle_options')}}",
                data: {
                        catOrSubCat:catOrSubCat,
                        type:filterType,
                        gallery_type:gallery_type,
                        post_type:post_type,
                        gallery_subtype: gallery_subtype,
                        is_subcat_selected:is_subcat_selected,
                        gallery_view: type_switch_two
                    },
                success:function(response){
                    // return false;
                    if (gallery_type == "All") {
                        window.location.href="{{ route('site.index') }}";
                    } else {
                        window.location.reload();
                    }
                }
            });
        });

        $('.product-filter-disable').click(function(){
            var gallery_type = $(this).attr('data-type');
            var filterType = $(this).data('filter-type');

            $.ajax({
                type: "POST",
                url: "{{url('toggle_options')}}",
                data: {gallery_type:gallery_type, type:filterType, action:"disable"},
                success:function(response)
                {
                    window.location.href="{{ route('site.index') }}";
                    // window.location.reload();
                }
            });
        });

        /*$(document).ready(function(){
            $('.filter-tags-bar').owlCarousel({
                autoWidth:true,
                dots:false,
                nav:true,
           })
           $('.owl-slide-subcat').owlCarousel({
                autoWidth:true,
                dots:false,
                loop: true,
                nav:true,
           })
        });*/

        $(function() {
            var owl = $(".owl-carousel");
            owl.owlCarousel({
                margin: 10,
                dots:false,
                autoWidth:true,
                loop: false,
                nav: true,
                navText : ['<i class="fa fa-caret-left" aria-hidden="true"></i>','<i class="fa fa-caret-right" aria-hidden="true"></i>']
            });
            owl.on('mousewheel', '.owl-stage', function (e) {
                if (e.deltaY>0) {
                    owl.trigger('next.owl');
                } else {
                    owl.trigger('prev.owl');
                }
                e.preventDefault();
            });

            var owl_1 = $(".owl-carousel_1");
            owl_1.owlCarousel({
                margin: 10,
                dots:false,
                autoWidth:true,
                loop: false,
                nav: true,
                navText : ['<i class="fa fa-caret-left" aria-hidden="true"></i>','<i class="fa fa-caret-right" aria-hidden="true"></i>']
            });
            owl_1.on('mousewheel', '.owl-stage', function (e1) {
                if (e1.deltaY>0) {
                    owl_1.trigger('next.owl');
                } else {
                    owl_1.trigger('prev.owl');
                }
                e1.preventDefault();
            });
        });

        /*$('.filter-gallery-icon').click(function(){
            var type = $(this).attr('data-type');
            var show = $(this).attr('data-show');

            $.ajax({
                type: "POST",
                url: "{{url('toggle_options')}}",
                data: {gallery_view:type},
                success:function(response)
                {
                    window.location.reload();
                }
            });
        });*/

        function getCookie(cookie_key) {
            let name = cookie_key + "=";
            let ca = document.cookie.split(';');
            for(let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }

        jQuery(document).ready(function(){
            jQuery(".switch-field label").click(function() {
                jQuery(".switch-field label").removeClass('active');
                jQuery(this).addClass('active');
            });

            jQuery('.hide_show_filter').hide();
            jQuery('.filter_text').hide();
            jQuery('.filter_btn img').click(function(){
                jQuery('.filter_btn').hide();
                jQuery('.filter_text').show();
                jQuery('.hide_show_filter').show();
            });

            setTimeout(function(){
                var ctm_price = getCookie("ctm_price");
                if(ctm_price == 'yes'){
                    jQuery(".isotope-product-price").show();
                    jQuery(".isotope-product-comment").show();
                    jQuery("#radio_two_show_price").prop("checked", true);
                    jQuery("#radio_two_show_price").parent().parent().addClass('radio_two_show_price');
                } else {
                    jQuery(".isotope-product-price").hide();
                    jQuery(".isotope-product-comment").hide();
                    jQuery("#radio_one_show_price").prop("checked", true);
                    jQuery("#radio_two_show_price").parent().parent().removeClass('radio_two_show_price');
                }
            }, 500);
            jQuery(".ctm_price").change(function () {
                if(jQuery(this).val() == 'yes'){
                    jQuery(".isotope-product-price").show();
                    jQuery(".isotope-product-comment").show();
                    document.cookie = "ctm_price=yes; path=/";
                } else {
                    jQuery(".isotope-product-price").hide();
                    jQuery(".isotope-product-comment").hide();
                    document.cookie = "ctm_price=no; path=/";
                }
            });

            jQuery(".filter_text").click(function(){
                var type_switch_two = jQuery('input[name="switch-two"]:checked').data("type");

                var filterType = 'ordering';
                var gallery_type = jQuery('input[name="sort_by"]:checked').data('type');
                var post_type = jQuery('input[name="sale_only"]:checked').data('type');

                var is_subcat_selected = '';
                if(jQuery('.subcategory ul li').hasClass('filter-gallery-toggle-active')) {
                    is_subcat_selected = 'not empty';
                } else {
                    is_subcat_selected = 'empty';
                }

                $.ajax({
                    type: "POST",
                    url: "{{url('toggle_options')}}",
                    data: { 
                            type: filterType,
                            gallery_type: gallery_type,
                            post_type: post_type,
                            is_subcat_selected: is_subcat_selected,
                            gallery_view: type_switch_two
                        },
                    success:function(response){
                        // return false;
                        if (gallery_type == "All") {
                            window.location.href="{{ route('site.index') }}";
                        } else {
                            window.location.reload();
                        }
                    }
                });

                jQuery('.filter_text').hide();
                jQuery('.filter_btn').show();
                jQuery('.hide_show_filter').hide(); 
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/2.2.0/anime.min.js"></script>
    <script>
        let radio_two_show_price = document.getElementById('radio_two_show_price');
        let radio_one_show_price = document.getElementById('radio_one_show_price');
        let radio = document.querySelector('.radio0');

        radio_two_show_price.addEventListener('click', () => {
            radio.classList.add('radio_two_show_price');
        });

        radio_one_show_price.addEventListener('click', () => {
            radio.classList.remove('radio_two_show_price');
        });


        let radio_two_sale_only = document.getElementById('radio_two_sale_only');
        let radio_one_sale_only = document.getElementById('radio_one_sale_only');
        let radio1 = document.querySelector('.radio1');

        radio_two_sale_only.addEventListener('click', () => {
            radio1.classList.add('radio_two_sale_only');
        });

        radio_one_sale_only.addEventListener('click', () => {
            radio1.classList.remove('radio_two_sale_only');
        });

        let radio_two_sort_by = document.getElementById('radio_two_sort_by');
        let radio_one_sort_by = document.getElementById('radio_one_sort_by');
        let radio2 = document.querySelector('.radio2');

        radio_two_sort_by.addEventListener('click', () => {
            radio2.classList.add('radio_two_sort_by');
        });

        radio_one_sort_by.addEventListener('click', () => {
            radio2.classList.remove('radio_two_sort_by');
        });
    </script>

@endsection
