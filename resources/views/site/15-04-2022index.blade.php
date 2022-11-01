<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.js"></script>
@extends('layouts.front')
@section('content')
<style>
  
  .owl-drag { position: relative;}
  .owl-stage-outer {    width: calc( 100% - 60px);    margin-left: 30px;    overflow: hidden;}
    .owl-item { display: inline-block;    }
    .owl-nav { margin: 0;  position: absolute; top: 50%; -ms-transform: translateY(-50%);  transform: translateY(-50%);width: 100%; }
    .owl-next, .owl-prev {width:24px !important;height:24px !important; background-repeat:no-repeat; opacity: 1 !important; background-size: 100%; display: inline-block; font-size: 0; border:0px solid transparent; background-color: transparent;}
    .owl-prev { float: left; background-image:url("../assets/img/Icon-01.png"); }
    .owl-prev.disabled , .owl-next.disabled { display: none;}
    .owl-next {  float: right; background-image:url("../assets/img/Icon-02.png");  }

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
                    <ul class="col-xs-12 text-left filter-tags-bar menu ctm_menu">
                        <li class="item filter-gallery-groups prozak-light-family filter-gallery-toggle-category slick-current text-uppercase @if($filter_value == "All") filter-gallery-toggle-active @endif " data-type="All" data-filter-type="type" tabindex="0" autofocus>All</li>
                        @foreach($types as $count => $type)
                            <li class="item filter-gallery-groups filter-gallery-toggle-category @if($filter_value == $type->type) filter-gallery-toggle-active @endif" data-type="{{$type->type}}" data-cat-type="cat" data-filter-type="type" tabindex="{{$count + 1}}">{{$type->type}}</li>
                        @endforeach  
                    </ul>
                    <!-- <div class="paddles">
                        <button class="left-paddle paddle hidden slick-prev slick-arrow">
                            <
                        </button>
                        <button class="right-paddle paddle slick-next slick-arrow">
                            >
                        </button>
                    </div> -->
                </div>

            </div>
            <div class="homepage-filter-bar">
                <div class="row">
                    <div class="mob-space">
                        <div class="@if($filter_value !='All') product-filter-disable subcategory-name @else filter-gallery-toggle @endif" @if($filter_value !='All') style="padding-right:10px;" @endif data-type="{{$filter_value}}" data-filter-type="type">{{$filter_value}} @if($filter_value !='All') @if($subcategories && $subcategories->count() > 0) {{ ':' }} @endif<span class="filter-disabled"><div>x</div></span> @endif</div>
                        @if($subcategories && $subcategories->count() > 0)
                            <div class="menu-wrapper subcategory">
                                <ul class="col-xs-12 text-left filter-tags-bar menu owl-slide-subcat" >
                                    @foreach($subcategories as $count => $subcategory)
                                        <li class="item filter-gallery-groups filter-gallery-toggle-category @if($filter_subvalue && in_array($subcategory->title,$filter_subvalue)) filter-gallery-toggle-active @endif" data-cat-type="subcat" data-filter-type="type" data-type="{{ $filter_value }}" data-subtype="{{ $subcategory->title }}" tabindex="{{ $count+1 }}">{{ $subcategory->title }}</li>
                                    @endforeach
                                </ul>
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
                        @if ($filter_user_name !='')
                            <span class="filter-gallery-toggle-active product-filter-disable  prozak-light-family text-uppercase" style="padding-right:10px;" data-type="{{$filter_user_name}}" data-filter-type="user">{{$filter_user_name}} <span class="filter-disabled"><div>x</div></span></span>
                        @endif
                    </div>

                    <div class="randomBlock text-right">
                        @if($post_type == 'sale')
                            <span class="product-filter-order filter-gallery-toggle" data-type="all" data-filter-type="ordering">All</span>
                        @else
                            <span class="product-filter-order filter-gallery-toggle" data-type="sale" data-filter-type="ordering">For Sale</span>
                        @endif

                        @if($gallery_type == 'random')
                            <span class="product-filter-order filter-gallery-toggle" data-type="latest" data-filter-type="ordering">Latest</span>
                        @else
                            <span class="product-filter-order filter-gallery-toggle" data-type="random" data-filter-type="ordering">Random</span>
                        @endif

                        @if($gallery_view == 'justified-with')
                            <span class="filter-gallery-icon" data-type="square" data-show="justified-with">
                               <!--  <img class="img-responsive" src="{{ asset('assets/img/icon_gallery_square_new.png') }}" /> -->
                              <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="33" height="4.8" rx="1" fill="#EAEAED"/>
                                <rect y="28.2" width="33" height="4.8" rx="1" fill="#EAEAED"/>
                                <rect y="8.60001" width="33" height="15.8" rx="1" fill="#EAEAED"/>
                             </svg>
                            </span>
                        @elseif($gallery_view == 'square')
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
                        @else
                            <span class="filter-gallery-icon" data-type="justified-with" data-show="justified-without">
                               <!--  <img class="img-responsive" src="{{ asset('assets/img/icon_gallery_info_new.png') }}" /> -->
                             <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="14.6667" height="19.1172" rx="3" fill="#EAEAED"/>
                                    <rect y="22.9862" width="14.6667" height="10.0138" rx="3" fill="#EAEAED"/>
                                    <rect x="18.5625" width="14.4375" height="33" rx="3" fill="#EAEAED"/>
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section id="gallery-single-section" style="margin-top:200px;">
        <div class="container-fluid">
            <div class="mange-space">
                <div id="post-container" data-count= "{{ $count }}">
                    @include('site._post')
                </div>
            </div>
        </div>
        <input type="hidden" id = "toggle_options_url" value="{{url('toggle_options')}}">
    </section>
    <!-- End gallery single section -->
@endsection

@section('scripts')
    <link rel='stylesheet' href='https://rawgit.com/kenwheeler/slick/master/slick/slick.css'>
    <link rel='stylesheet' href='https://rawgit.com/kenwheeler/slick/master/slick/slick-theme.css'>
    <link rel='stylesheet' href="{{ asset('assets/css/menu.css') }}">
    <script src="{{ asset('assets/js/menu.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('.filter-gallery-icon').click(function(){
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
        });

        $(".search-icon").click(function(){
            $("#main-search-icon").addClass('hidden');
            $("#main-search-filter").removeClass('hidden');
            $("#main-search-button").removeClass('hidden');
            $("#main-search-filter").focus();

        });

        $('.filter-gallery-toggle-category, .product-filter-order').click(function(){

            var catOrSubCat = $(this).data('cat-type');            
            var filterType = $(this).data('filter-type');
            var gallery_type = $(this).attr('data-type');
            var post_type = $(this).attr('data-type');
            var gallery_subtype = $(this).attr('data-subtype');
            // $('.filter-gallery-toggle-category, .product-filter-order').removeClass('filter-gallery-toggle-active');
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
                data: {catOrSubCat:catOrSubCat,type:filterType, gallery_type:gallery_type,post_type:post_type, gallery_subtype: gallery_subtype,is_subcat_selected:is_subcat_selected},
                success:function(response) {
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
                    window.location.reload();
                }
            });
        });
        $('.main-page-user-click').on("click",function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var user_id = $(this).data('user-id');
            var url = $("#toggle_options_url").val();
            $.ajax({
                type: "POST",
                url: url,
                data: {type:"user", user_id:user_id},
                success:function(response)
                {
                    window.location.reload();
                }
            });
        });
    </script>
@endsection