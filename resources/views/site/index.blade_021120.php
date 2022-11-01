@extends('layouts.front')
@section('content')
    <style>
        .slick-prev, .slick-next {
            width:36px !important;
            height:36px !important;
            background-color: var(--main-bg-color) !important;;
            opacity: 1 !important;;
        }
        .slick-track {
            top:1px !important;;
        }
        .slick-prev {
            left: 15px !important;
            z-index: 1;
        }
        .slick-next {
            right: 15px !important;
            z-index: 1;
        }
    </style>
    <div class="header-fixed">
        @include('layouts.frontheader')

    <div class="container-fluid " >
        <div class="homepage-filter-bar">
            <div class="row">
                <div class="col-xs-3 text-left">
                    <form method="get">
                        {{ csrf_field() }}
                        <a href="#" class="search-icon @if($mainSearchFilter != '') hidden @endif" id="main-search-icon"><i class="fa fa-search"></i></a>
                        <input type="text" value="<?php echo htmlspecialchars($mainSearchFilter, ENT_QUOTES) ?>"
                               name="main-search-filter" id="main-search-filter" class="@if($mainSearchFilter == '') hidden @endif main-search-filter" placeholder="">
                        <button class="@if($mainSearchFilter == '') hidden @endif search-icon" id="main-search-button" type="submit"><i class="fa fa-search"></i></button>
                    </form>
                </div>
                <div class="col-xs-6 text-center">
                    <span class="filter-gallery-toggle-active @if($filter_value !='All') product-filter-disable @else filter-gallery-toggle @endif  prozak-light-family text-uppercase" @if($filter_value !='All') style="padding-right:34px;" @endif data-type="{{$filter_value}}" data-filter-type="type">{{$filter_value}} @if($filter_value !='All') <span class="filter-disabled"><div>x</div></span> @endif</span>
                    @if ($filter_user_name!='')
                        <span class="filter-gallery-toggle-active product-filter-disable  prozak-light-family text-uppercase" style="padding-right:34px;" data-type="{{$filter_user_name}}" data-filter-type="user">{{$filter_user_name}} <span class="filter-disabled"><div>x</div></span></span>
                    @endif
                </div>
                <div class="col-xs-3 text-right">
                    @if($gallery_type == 'random')
                        <span class="product-filter-order filter-gallery-toggle prozak-light-family font-weight-bold text-uppercase" data-type="latest" data-filter-type="ordering">Latest</span>
                    @else
                        <span class="product-filter-order filter-gallery-toggle prozak-light-family font-weight-bold text-uppercase" data-type="random" data-filter-type="ordering">Random</span>
                    @endif

                    @if($gallery_view == 'justified-with')
                    <span class="filter-gallery-icon" data-type="square" data-show="justified-without"><img class="img-responsive" src="{{ asset('assets/img/icon_gallery_square.png') }}" /></span>
                    @elseif($gallery_view == 'square')
                    <span class="filter-gallery-icon" data-type="justified-without" data-show="justified-with"><img class="img-responsive" src="{{ asset('assets/img/icon_gallery_justified.png') }}" /></span>
                    @else
                    <span class="filter-gallery-icon" data-type="justified-with" data-show="square"><img class="img-responsive" src="{{ asset('assets/img/icon_gallery_info.png') }}" /></span>
                    @endif
                </div>
            </div>
        </div>
        <div class="homepage-filter-tags-bar">
            <div class="row">
                <div class="col-xs-12 text-left filter-tags-bar">

                    <span class=" filter-gallery-groups prozak-light-family filter-gallery-toggle-category  text-uppercase @if($filter_value == "All") filter-gallery-toggle-active @endif" data-type="All" data-filter-type="type">All</span>
                    @foreach($types as $type)
                        <span class="filter-gallery-groups prozak-light-family filter-gallery-toggle-category text-uppercase @if($filter_value == $type->type) filter-gallery-toggle-active @endif " data-type="{{$type->type}}" data-filter-type="type">{{$type->type}}</span>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    </div>
	<section id="gallery-single-section" style="margin-top:180px;">
        <div class="container-fluid">

		    <div id="post-container" data-count= "{{ $count }}">
			@include('site._post')
			</div>
		</div>
        <input type="hidden" id = "toggle_options_url" value="{{url('toggle_options')}}">
	</section>
	<!-- End gallery single section -->
@endsection

@section('scripts')
    <link rel='stylesheet' href='https://rawgit.com/kenwheeler/slick/master/slick/slick.css'>
    <link rel='stylesheet' href='https://rawgit.com/kenwheeler/slick/master/slick/slick-theme.css'>
    <script src='https://kenwheeler.github.io/slick/slick/slick.js'></script>
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
       var gallery_type = $(this).attr('data-type');
       var filterType = $(this).data('filter-type');
        $.ajax({
            type: "POST",
            url: "{{url('toggle_options')}}",
            data: {gallery_type:gallery_type, type:filterType},
            success:function(response)
            {
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
    $('.filter-tags-bar').slick({
        variableWidth: true,
        slidesToScroll: 1,
        infinite: false

    });
</script>
@endsection