@extends('layouts.front')
@section('content')
    <style>
        #main_slider {
            text-align: center !important;
            overflow: hidden;
        }

        #main_slider .slick-slide img,
        #main_slider .slick-slide video {
            display: inline-block !important;
        }

        .slider-nav-item-block.thumbnails,
        .slider-nav-item-block.thumbnails img {
            overflow: hidden;
            height: 80px !important;
            width: 80px !important;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-position-y: center;
            background-position-x: right;
            cursor: pointer;
        }

        .slider-nav {
            width: 100% !important;
            display: flex !important;
            transform: translate3d(0, 0, 0) !important;
            margin: 0 !important;
            justify-content: center;
            align-items: center;
        }

        #product_details_page .slider-nav {
            margin-left: 0px !important;
        }

        .product_details_page__btn {
            position: absolute;
            letter-spacing: 5px;
            top: 8px;
            left: 10px;
            z-index: 999;
        }

        .logo__img {
            width: 45px;
            height: 45px;
            margin-right: 5px;
            float: left;
            border-radius: 25px;
            border: 1px solid #FDFDFD;
        }

        .slider-logo {
            margin-bottom: 21px;
            display: flex;
        }

        .slider-logo span {
            color: gray;
            line-height: 12px;
        }

        .logo__title {
            margin-top: 0;
            margin-bottom: 0;
            font-weight: normal;
        }

        .slider-logo span {
            font-size: 12px;
        }

        .table--custom table {
            height: auto;
        }

        .table--custom tbody tr {
            display: flex;
            justify-content: space-between;
        }

        .side_bar {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .table--custom tbody tr {
            border: none !important;
            padding: 0px;
        }

        .table--custom tbody tr td {
            border: none !important;
            padding: 0px;
            margin-bottom: 17px;
        }

        .table--custom tbody tr td div {
            border: none !important;
            padding: 5px 0px 2px 0px;
            margin: 0px;
            font-weight:  normal;
            line-height: 18px;
            color: #B7B7B7;
        }

        .table--custom {
            height: auto !important;
            display: flex;
            justify-content: space-between;
            flex-direction: column;
        }

        .table--custom .table__title {
            color: #fff;
            font-weight: 600;
        }

        .globe-icon {
            margin-top: 1px;
        }

        .btn-icon {
            margin-right: 10px;
        }

        .slider-row {
            overflow: hidden;
        }

        table.table--custom {
            font-size: 15px;
        }

        .table--custom tbody tr.one-td td {
            padding-top: 0;
            line-height: 18px;
        }

        .table--custom tbody tr.one-td .table__title {
            padding-bottom: 0;
            line-height: 15px;
        }

        .sidebar__buttons {
            margin-bottom: 31px;
        }

        .logo__text {
            display: flex;
            flex-direction: column;
            margin-top: 2px;
        }

        .side-bar-right {
            padding: 30px 20px 20px 20px !important;
        }

        .detailed-button {
            margin-bottom: 7px;
        }

        @media (max-width: 1400px) {
            .product_details_page__btn {
                top: unset;
                bottom: 52px;
            }
        }

        @media (max-width: 992px) {
            .product_details_page__btn {
                bottom: -127px;
            }
            .sidebar__buttons {
                margin-bottom: 0;
            }
        }

    </style>
    <!-- ===================================
    ///// Begin Product Details section /////
    ==================================== -->
    <section id="product_details_page">
        <div class="container-fluid">
            <div class="row slider-row">
                <div class="col-xl-9 col-md-9 slider-main-lg slider-main-product">
                    <!-- <a href="{{ url()->previous() }}" class="product_details_page__btn prozak_family h3" style="background-color: #393939; height:35px; padding:5px 5px 5px 11px; border-radius: 6px;border-top-left-radius: 2em 5em;border-top-left-radius: 0px; border-top-right-radius: 0px;">BACK</a> -->
                    <a href="{{ url('/') }}" class="product_details_page__btn prozak_family h3" style="background-color: #393939; height:35px; padding:5px 5px 5px 11px; border-radius: 6px;border-top-left-radius: 2em 5em;border-top-left-radius: 0px; border-top-right-radius: 0px;">BACK</a>
                    <div class="slider slider-for" id="main_slider" style="border-radius: 6px;">
                        <?php if(count($product_details->shop_product_files) >= 2){ ?>
                            @foreach($product_details->shop_product_files as $product_file)
                                <?php
                                $size = @getimagesize(public_path('images/post/new_images') . '/' . $product_file->file_name) ?? 0;
                                ?>
                                @if($product_file->file_type == 0)
                                    <div class="ctm_multiple_img">
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
                                    <div>
                                        <video controls>
                                            <source
                                                src="{{ asset('public/images/post/new_images') }}/{{ $product_file->file_name }}">
                                        </video>
                                    </div>
                                @endif
                            @endforeach
                        <?php } else { ?>
                            @foreach($product_details->shop_product_files as $product_file)
                                <?php
                                $size = @getimagesize(public_path('images/post/new_images') . '/' . $product_file->file_name) ?? 0;
                                ?>
                                @if($product_file->file_type == 0)
                                    <div id="jacascript-resize" class="ctm_single_img">
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
                                    <div>
                                        <video controls>
                                            <source
                                                src="{{ asset('public/images/post/new_images') }}/{{ $product_file->file_name }}">
                                        </video>
                                    </div>
                                @endif
                            @endforeach
                        <?php } ?>
                    </div>
                    <div class="col-xs-12 stick-view-thumb">
                        <div class="slider slider-nav ">
                            @foreach($product_details->shop_product_files as $product_file)
                                <div class="mr-30 slider-nav-item-block thumbnails"
                                     style="background-image: url({{ asset('images/post/new_images') }}/{{ $product_file->file_name }})">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="info_detail">
                        <a href="{{ url('/index') }}" class="product_details_page__btn prozak_family h3" id="close_side2">
                            <svg width="100%" height="100%" viewBox="0 0 4267 4267" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:1.5;"><g><circle cx="2133.33" cy="2133.33" r="2083.33"/><g><g><path d="M1176.14,2138.44l1424.15,1424.15" style="fill:none;stroke:#c8c8c8;stroke-width:145.83px;"/><path d="M2600.29,704.077l-1424.15,1424.15" style="fill:none;stroke:#c8c8c8;stroke-width:145.83px;"/></g></g></g></svg>
                        </a>
                        <a href="#" id="detail_in">info</a>
                        <?php if(!isset($_COOKIE['infocookie'])){?>
                        	<span id="infolabel" class="helplabel infolabel"> <p>See image details.</p>
                            <button type="button" onclick="helplabel('info','infocookie',1);">OK</button></span>
                		<?php } ?>

                    </div>
                </div>
                <div class="col-xl-3 col-md-3 side_bar side-bar-right sidebar-main-lg">
                    <a href="#" id="remove_side">
                        <!-- <a href="#" id="remove_side"> -->
                       <svg width="100%" height="100%" viewBox="0 0 4267 4267" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:1.5;"><g><circle cx="2133.33" cy="2133.33" r="2083.33"/><g><g><path d="M1176.14,2138.44l1424.15,1424.15" style="fill:none;stroke:#c8c8c8;stroke-width:145.83px;"/><path d="M2600.29,704.077l-1424.15,1424.15" style="fill:none;stroke:#c8c8c8;stroke-width:145.83px;"/></g></g></g></svg>
                    </a>

                    <a href="{{ url('/index') }}" class="product_details_page__btn prozak_family h3" id="close_side1">
                        <svg width="100%" height="100%" viewBox="0 0 4267 4267" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:1.5;"><g><circle cx="2133.33" cy="2133.33" r="2083.33"/><g><path d="M1091.67,3175l2083.33,-2083.33" style="fill:none;stroke:#c8c8c8;stroke-width:145.83px;"/><path d="M1091.67,1091.67l2083.33,2083.33" style="fill:none;stroke:#c8c8c8;stroke-width:145.83px;"/></g></g></svg>
                    </a>



                    <div class="sidebar__top">
                        <a class="slider-logo white-color product-page-user-click ibm-regular-400-family" data-type="user" data-user-id="{{$product_details->user_id}}" href="/{{$product_details->user->tagname}}">
                            <img class="logo__img" src="{{ $product_details->shop_profile->shop_image_url }}" alt="logo" style="border: 1px solid #e5e5e5;">
                            <div class="logo__text">
                                <h2 class="logo__title shop_name" style="font-family: 'IBM Plex Sans Condensed'; font-size: 18px; color: #e5e5e5; font-weight: 600; letter-spacing: 1px;">{{ $product_details->user->shopProfile->shop_name }}</h2>
                                <span style="font-family: 'IBM Plex Sans Condensed'; font-size: 12px; color: #A09FA4; font-weight: normal; letter-spacing: 1px;">&commat;{{$product_details->user->tagname}}</span>
                            </div>
                        </a>
                        <table class="table table--custom">
                            <tbody>
                            <tr>
                                <td class="table__title">Posted:</td>
                                <td>{{ date('d.m.Y', strtotime($product_details->created_at)) }}</td>
                            </tr>
                            <tr>
                                <td class="table__title" style="width: 130px !important;">Product name:</td>
                                <td>{{ $product_details->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="table__title">Artist:</td>
                                <td>{{ $product_details->artist_name ?? '-' }}</td>
                            </tr>
                            @if($product_details['shop_product_details'])
                                @foreach($product_details['shop_product_details'] as $key => $shop_product_details)
                                    <?php if($shop_product_details->title != 'Artist'){ ?>
                                        <tr class="one-td">
                                            <td class="table__title">{{ $shop_product_details->title}}</td>
                                            <td>{{ $shop_product_details->value}}</td>
                                        </tr>
                                    <?php } ?>
                                @endforeach
                            @endif

                            @if($product_details->sale == 'yes')
                            <tr>
                                <td class="table__title">Price:</td>
                                @if ((is_null($product_details->price)) ||  ($product_details->price=='') ||  ($product_details->price=='0'))
                                    <td>Contact seller for price</td>
                                @else
                                    <td>{{ $product_details->currency.' '.$product_details->price }}</td>
                                @endif
                            </tr>
                            <?php if($product_details->price > 0){ ?>
                                <tr>
                                    <td class="table__title">Shipping:</td>
                                    <td>{{ $product_details->shipping_included ?? '-' }}</td>
                                </tr>
                            <?php } ?>
                            @endif


                            @if($product_details->description != null)
                            <tr class="one-td">
                                <td class="table__title">Description:
                                    <div><?php echo nl2br($product_details->description); ?></div>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td class="table__title">H/W/D:</td>
                                <td>{{ $product_details->package_size ?? '-'}}</td>
                            </tr>
                            @if($product_details->sale == 'yes')
                            <tr>
                                <td class="table__title">Weight:</td>
                                <td>{{ ($product_details->package_weight) ? $product_details->package_weight . ' kg' : '-'}}</td>
                            </tr>
                            <tr>
                                <td class="table__title">Quantity for sale:</td>
                                <td>{{ $product_details->quantity ?? '-' }}</td>
                            </tr>
                            @endif
                            <tr class="one-td">
                                <td class="table__title"><div class="table__title _title_in">Categories:</div>
                                <?php
                                    $explode__category = explode('.', $product_details->category_type);
                                    $count__category = count($explode__category);
                                    $v = 1;
                                ?>

                                    <?php foreach ($explode__category as $key => $value_1) { ?>
                                        <div class="item filter-gallery-groups filter-gallery-toggle-category" data-cat-type="subcat" data-filter-type="type" data-type="{{ $product_details->type }}" data-subtype="{{ $value_1 }}" style="background-color: transparent;cursor: pointer;padding: 0px 0px 2px 0px;">
                                            <?php if($v < $count__category ){ ?>
                                                <?php echo $value_1.', '; ?>
                                            <?php } else { ?>
                                                <?php echo $value_1; ?>
                                            <?php } ?>
                                        </div>
                                    <?php $v++; } ?>
                                    {{-- <div> {{ ($product_details->category_type) ? str_replace('.', ', ', $product_details->category_type) : '-' }} </div> --}}
                                </td>
                            </tr>
                            <tr class="one-td">
                                <td class="table__title"><div class="table__title">Tags:</div>
                                <?php
                                    $explode__tags = explode(',', $product_details->tags);
                                ?>

                                    <?php foreach ($explode__tags as $key => $value_2) { ?>
                                        <div class="details__filter filter-gallery-groups" style="background-color: transparent;cursor: pointer;"><?php echo $value_2; ?></div>
                                    <?php } ?>
                                    {{-- <div>{{ ($product_details->tags) ? str_replace(',', ' ', $product_details->tags) : '-' }}</div> --}}

                                    <form method="get" class="ctm__product_details_form" action="{{ url('/index') }}" style="visibility: hidden;height: 0;">
                                        {{ csrf_field() }}
                                        <div class="search_form_div">
                                            <input type="text" name="main-search-filter" id="main-search-filter" class=" main-search-filter" placeholder="">
                                        </div>

                                        <button class=" search-icon" id="main-search-button" type="submit"><i class="fa fa-search"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="one-td">
                                <td class="table__title" style="margin-top: 30px;">About "{{ $product_details->user->shopProfile->shop_name }}":
                                    <div><?php echo nl2br($product_details->user->shopProfile->shop_description); ?></div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php //echo '<pre>'; print_r($product_details->user->commission); exit; ?>
                    <div class="sidebar__buttons">
                        <?php if($product_details->user->commission==1){ ?>
                           <button class="detailed-button" data-toggle="modal" onclick="iccustomfun('commissionModal')" data-target="#commissionModal">Open for Commission</button>
                        <?php } ?>
                            <button class="detailed-button" data-toggle="modal" onclick="iccustomfun('emailModal')" data-target="#emailModal">Contact user</button>
                        <?php
                            $ctm__links = $product_details->website_link;
                            $explode_ctm__links = explode(',', $ctm__links);
                        ?>

                        @foreach($explode_ctm__links as $url)
                            <?php if (strpos(strtolower($url), 'instagram') !== false) { ?>
                                <button class="detailed-button" data-toggle="modal" onclick="window.open('http://{{ $url }}')">
                                    <img class="btn-icon" style="margin-bottom:3px;" width="28" height="28" src="/images/some_icon_instagram.svg" alt="Instagram">Go to Instagram
                                </button>
                            <?php } else if(strpos(strtolower($url), 'twitter') !== false){ ?>
                                <button class="detailed-button" data-toggle="modal" onclick="window.open('http://{{ $url }}')">
                                    <img class="btn-icon" style="margin-bottom:3px;" width="28" height="28" src="/images/some_icon_twitter.svg" alt="Twitter">Go to Twitter
                                </button>
                            <?php } else if(strpos(strtolower($url), 'bandcamp') !== false){ ?>
                                <button class="detailed-button" data-toggle="modal" onclick="window.open('http://{{ $url }}')">
                                    <img class="btn-icon" style="margin-bottom:3px;" width="28" height="28" src="/images/some_icon_bandcamp.svg" alt="Bandcamp">Go to Bandcamp
                                </button>
                            <?php } else if(strpos(strtolower($url), 'facebook') !== false){ ?>
                                <button class="detailed-button" data-toggle="modal" onclick="window.open('http://{{ $url }}')">
                                    <img class="btn-icon" style="margin-bottom:4px;" width="25" height="25" src="/images/some_icon_facebook.svg" alt="Facebook">Go to Facebook
                                </button>
                            <?php } else if(strpos(strtolower($url), 'onlyfans') !== false){ ?>
                                <button class="detailed-button" data-toggle="modal" onclick="window.open('http://{{ $url }}')">
                                    <img class="btn-icon" style="margin-bottom:4px;" width="25" height="25" src="/images/some_icon_onlyfans.svg" alt="OnlyFans">Go to OnlyFans
                                </button>
                            <?php } else if(strpos(strtolower($url), 'patreon') !== false){ ?>
                                <button class="detailed-button" data-toggle="modal" onclick="window.open('http://{{ $url }}')">
                                    <img class="btn-icon" style="margin-bottom:3px;" width="24" height="24" src="/images/some_icon_patreon.svg" alt="Patreon">Go to Patreon
                                </button>
                            <?php } else if(strpos(strtolower($url), 'twitch') !== false){ ?>
                                <button class="detailed-button" data-toggle="modal" onclick="window.open('http://{{ $url }}')">
                                    <img class="btn-icon" style="margin-bottom:3px;" width="25" height="25" src="/images/some_icon_twitch.svg" alt="Twitch">Go to Twitch
                                </button>
                            <?php } else if(strpos($url, 'youtube') !== false){ ?>
                                <button class="detailed-button" data-toggle="modal" onclick="window.open('http://{{ $url }}')">
                                    <img class="btn-icon" style="margin-bottom:4px;" width="27" height="27" src="/images/some_icon_youtube.svg" alt="YouTube">Go to YouTube
                                </button>
                            <?php } else { ?>
                                <button class="detailed-button" data-toggle="modal" onclick="window.open('http://{{ $url }}')">
                                    <img class="btn-icon btn-icon--website" style="margin-bottom:4px;" width="22" height="22" src="/images/some_icon_website.svg" alt="Website">Go to Website
                                </button>
                            <?php } ?>
                        @endforeach

                        {{-- @foreach($product_details->user->shopUrls as $url)

                            <button class="detailed-button" data-toggle="modal" onclick="window.open('http://{{ $url->url }}')">
                                @if($url->is_bandcamp)
                                    <img class="btn-icon" style="margin-bottom:3px;" width="28" height="28" src="/images/some_icon_bandcamp.svg" alt="Bandcamp">Go to Bandcamp
                                @elseif($url->is_facebook)
                                    <img class="btn-icon" style="margin-bottom:4px;" width="25" height="25" src="/images/some_icon_facebook.svg" alt="Facebook">Go to Facebook
                                @elseif(strtolower($url)->is_instagramm)
                                    <img class="btn-icon" style="margin-bottom:3px;" width="28" height="28" src="/images/some_icon_instagram.svg" alt="Instagram">Go to Instagram
                                @elseif($url->is_onlyfans)
                                    <img class="btn-icon" style="margin-bottom:4px;" width="25" height="25" src="/images/some_icon_onlyfans.svg" alt="OnlyFans">Go to OnlyFans
                                @elseif($url->is_patreon)
                                    <img class="btn-icon" style="margin-bottom:3px;" width="24" height="24" src="/images/some_icon_patreon.svg" alt="Patreon">Go to Patreon
                                @elseif($url->is_twitch)
                                    <img class="btn-icon" style="margin-bottom:3px;" width="25" height="25" src="/images/some_icon_twitch.svg" alt="Twitch">Go to Twitch
                                @elseif($url->is_twitter)
                                    <img class="btn-icon" style="margin-bottom:3px;" width="28" height="28" src="/images/some_icon_twitter.svg" alt="Twitter">Go to Twitter
                                @elseif($url->is_youtube)
                                    <img class="btn-icon" style="margin-bottom:4px;" width="27" height="27" src="/images/some_icon_youtube.svg" alt="YouTube">Go to YouTube
                                @else
                                    <img class="btn-icon btn-icon--website" style="margin-bottom:4px;" width="22" height="22" src="/images/some_icon_website.svg" alt="Website">Go to Website
                                @endif
                            </button>
                        @endforeach --}}


                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- ===================================
    ///// Begin Product Details section /////
    ==================================== -->
    <!-- Modal -->
    <!-- commission form -->
    <div class="modal fade ctm_modal_comman" id="commissionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h5 class="modal-title" id="exampleModalLabel">Modal title</h5> -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                        <svg width="100%" height="100%" viewBox="0 0 4267 4267" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:1.5;"><g><circle cx="2133.33" cy="2133.33" r="2083.33"/><g><path d="M1091.67,3175l2083.33,-2083.33" style="fill:none;stroke:#c8c8c8;stroke-width:145.83px;"/><path d="M1091.67,1091.67l2083.33,2083.33" style="fill:none;stroke:#c8c8c8;stroke-width:145.83px;"/></g></g></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal_heading">
                        <center><span class="logo-text">CONTACT {{ $product_details->user->shopProfile->shop_name }}</span></center>
                    </div>
                    <div class="modal_form">
                        <form method="POST" action="{!! action('SiteController@commission_modal') !!}" id="commissionForm">
                            <input type="hidden" name="contact_url" value="{{ url()->current() }}">
                            {{ csrf_field() }}

                            <span class="help-block commission-success" style="display:none; margin-bottom: 15px;">
                                <strong style="color:green;"></strong>
                            </span>

                            <div class="form-group">
                                <span class="form_icon"><img class="btn-icon" style="margin-left: 3px;" width="23" height="23" src="/images/icon_user.svg" alt="Name"></span>
                                <input type="text" class="form-control" name="contact_name" placeholder="NAME" required value="{{ old('contact_name') }}">
                            </div>
                            <div class="form-group">
                                <span class="form_icon"><img class="btn-icon" style="margin-left: 3px;" width="23" height="23" src="/images/icon_envelope.svg" alt="Email address"></span>
                                <input type="email" class="form-control" name="contact_email" placeholder="EMAIL ADDRESS" required value="{{ old('contact_email') }}">
                            </div>
                            <input type="hidden" name="post_user_mail" value="{{ $product_details->contact_email }}">
                            <div class="form-group">
                                <textarea placeholder="MESSAGE REGARDING COMMISSION" rows="10" name="contact_message" class="form-control form-control_message" required>{{ old('contact_message') }}</textarea>
                            </div>

                            <div class="captcha_main form-group{{ $errors->has('captcha3') ? ' has-error' : '' }}">

                                <!-- <div class="captcha captcha_inner">
                                    <span>{!! captcha_img() !!}</span>
                                    <button type="button" class="btn btn-refresh">
                                    <svg fill="#000000" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 30 30" width="30px" height="30px"><path d="M 15 3 C 12.031398 3 9.3028202 4.0834384 7.2070312 5.875 A 1.0001 1.0001 0 1 0 8.5058594 7.3945312 C 10.25407 5.9000929 12.516602 5 15 5 C 20.19656 5 24.450989 8.9379267 24.951172 14 L 22 14 L 26 20 L 30 14 L 26.949219 14 C 26.437925 7.8516588 21.277839 3 15 3 z M 4 10 L 0 16 L 3.0507812 16 C 3.562075 22.148341 8.7221607 27 15 27 C 17.968602 27 20.69718 25.916562 22.792969 24.125 A 1.0001 1.0001 0 1 0 21.494141 22.605469 C 19.74593 24.099907 17.483398 25 15 25 C 9.80344 25 5.5490109 21.062074 5.0488281 16 L 8 16 L 4 10 z"/></svg>
                                    </button>
                                </div>
                                <input id="captcha3" type="text" class="form-control captcha_field @error('captcha3', 'post') is-invalid @enderror" placeholder="ENTER CAPTCHA" name="captcha3"> -->

                                <div id="ic_commissionModal"></div>
                                @if ($errors->has('captcha3'))
                                    <span class="help-block3">
                                        <strong>{{ $errors->first('captcha3') }}</strong>
                                    </span>
                                @endif

                                    <span class="help-block email-form-captcha3" style="display:none;">
                                        <strong></strong>
                                    </span>
                            </div>

                            <input type="submit" class="detailed-button" name="submit" value="SEND MESSAGE">
                            <!-- <button class="detailed-button" name="submit" value="send">SEND MESSAGE</button> -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade ctm_modal_comman" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h5 class="modal-title" id="exampleModalLabel">Modal title</h5> -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                        <svg width="100%" height="100%" viewBox="0 0 4267 4267" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:1.5;"><g><circle cx="2133.33" cy="2133.33" r="2083.33"/><g><path d="M1091.67,3175l2083.33,-2083.33" style="fill:none;stroke:#c8c8c8;stroke-width:145.83px;"/><path d="M1091.67,1091.67l2083.33,2083.33" style="fill:none;stroke:#c8c8c8;stroke-width:145.83px;"/></g></g></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal_heading">
                        <center><span class="logo-text">CONTACT {{ $product_details->user->shopProfile->shop_name }}</span></center>
                    </div>
                    <div class="modal_form">
                        <form method="POST" action="{!! action('SiteController@email_modal') !!}" id="emailContactForm">
                            <input type="hidden" name="contact_url" value="{{ url()->current() }}">
                            {{ csrf_field() }}

                            <span class="help-block email-success" style="display:none; margin-bottom: 15px;">
                                <strong style="color:green;"></strong>
                            </span>

                            <div class="form-group">
                                <span class="form_icon"><img class="btn-icon" style="margin-left: 3px;" width="23" height="23" src="/images/icon_user.svg" alt="Name"></span>
                                <input type="text" class="form-control" name="contact_name_emailModal" placeholder="NAME" required value="{{ old('contact_name_emailModal') }}">
                            </div>
                            <div class="form-group">
                                <span class="form_icon"><img class="btn-icon" style="margin-left: 3px;" width="23" height="23" src="/images/icon_envelope.svg" alt="Email address"></span>
                                <input type="email" class="form-control" name="contact_email_emailModal" placeholder="EMAIL ADDRESS" required value="{{ old('contact_email_emailModal') }}">
                            </div>
                            <input type="hidden" name="post_user_mail" value="{{ $product_details->contact_email }}">
                            <div class="form-group">
                                <textarea placeholder="MESSAGE" rows="10" name="contact_message_emailModal" class="form-control form-control_message" required>{{ old('contact_message_emailModal') }}</textarea>
                            </div>

                            <div class="captcha_main form-group{{ $errors->has('captcha4') ? ' has-error' : '' }}">

                                <!-- <div class="captcha captcha_inner">
                                    <span>{!! captcha_img() !!}</span>
                                    <button type="button" class="btn btn-refresh">
                                    <svg fill="#000000" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 30 30" width="30px" height="30px"><path d="M 15 3 C 12.031398 3 9.3028202 4.0834384 7.2070312 5.875 A 1.0001 1.0001 0 1 0 8.5058594 7.3945312 C 10.25407 5.9000929 12.516602 5 15 5 C 20.19656 5 24.450989 8.9379267 24.951172 14 L 22 14 L 26 20 L 30 14 L 26.949219 14 C 26.437925 7.8516588 21.277839 3 15 3 z M 4 10 L 0 16 L 3.0507812 16 C 3.562075 22.148341 8.7221607 27 15 27 C 17.968602 27 20.69718 25.916562 22.792969 24.125 A 1.0001 1.0001 0 1 0 21.494141 22.605469 C 19.74593 24.099907 17.483398 25 15 25 C 9.80344 25 5.5490109 21.062074 5.0488281 16 L 8 16 L 4 10 z"/></svg>
                                    </button>
                                </div>
                                <input id="captcha4" type="text" class="form-control captcha_field @error('captcha4', 'post') is-invalid @enderror" placeholder="ENTER CAPTCHA" name="captcha4"> -->

                                <div id="ic_emailModal"></div>
                                @if ($errors->has('captcha4'))
                                    <span class="help-block4">
                                        <strong>{{ $errors->first('captcha4') }}</strong>
                                    </span>
                                @endif

                                    <span class="help-block email-form-captcha4" style="display:none;">
                                        <strong></strong>
                                    </span>
                            </div>


                            <input type="submit" class="detailed-button" name="submit" value="SEND MESSAGE">
                            <!-- <button class="detailed-button" name="submit" value="send">SEND MESSAGE</button> -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <link rel="stylesheet" href="/assets/css/slick.css">
    <link rel="stylesheet" href="/assets/css/slick-theme.css">
    <link rel='stylesheet' href='https://rawgit.com/kenwheeler/slick/master/slxxick/slick.css'>
    <link rel='stylesheet' href='https://rawgit.com/kenwheeler/slick/master/slxxick/slick-theme.css'>
    <script src='https://kenwheeler.github.io/slick/slick/slick.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js'></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // CONTACT EMAIL FORM
            $('#emailContactForm input[type="submit"]').click(function(submitEvent) {
                submitEvent.preventDefault();

                let contact_name_emailModal = $('#emailContactForm input[name="contact_name_emailModal"]').val();
                let contact_email_emailModal = $('#emailContactForm input[name="contact_email_emailModal"]').val();
                let contact_message_emailModal = $('#emailContactForm textarea[name="contact_message_emailModal"]').val();
                let captcha4 = $('#captcha4').val();
                let token = $('#emailContactForm input[name="_token"]').val();

                let action = $('#emailContactForm').attr('action');

                jQuery('.email-form-captcha4').hide(0);

                $.ajax({
                    url:action,
                    type:'POST',
                    data:{
                        'contact_name_emailModal':contact_name_emailModal,
                        'contact_email_emailModal':contact_email_emailModal,
                        'contact_message_emailModal':contact_message_emailModal,
                        'captcha4':captcha4,
                        '_token':token,
                    },
                    accepts: {
                        text: "application/json"
                    },
                    success:function(response) {
                        jQuery('.email-success').show(0);
                        jQuery('.email-success strong').html("Your message has been successfully sent");

                        jQuery('#emailContactForm *').each(function(index) {
                            $(this).attr('disabled', true);
                        });

                        $(window).scrollTop(0);
                    },
                    error:function(err) {
                        console.log(err);

                        if (typeof err.responseJSON.errors.captcha4 !== undefined) {
                            jQuery('.email-form-captcha4').show(0);
                            jQuery('.email-form-captcha4 strong').html("Invalid captcha code email modal.");
                        }

                        refresh_captcha();
                    },
                });
            });


            // COMMISSION FORM
            $('#commissionForm input[type="submit"]').click(function(submitEvent) {
                submitEvent.preventDefault();

                let contact_name = jQuery('#commissionForm input[name="contact_name"]').val();
                let contact_email = jQuery('#commissionForm input[name="contact_email"]').val();
                let contact_message = jQuery('#commissionForm textarea[name="contact_message"]').val();
                let captcha3 = jQuery('#captcha3').val();
                let token = jQuery('#commissionForm input[name="_token"]').val();

                let action = jQuery('#commissionForm').attr('action');

                jQuery('.email-form-captcha3').hide(0);

                $.ajax({
                    url:action,
                    type:'POST',
                    data:{
                        'contact_name':contact_name,
                        'contact_email':contact_email,
                        'contact_message':contact_message,
                        'captcha3':captcha3,
                        '_token':token,
                    },
                    accepts: {
                        text: "application/json"
                    },
                    success:function(response) {
                        jQuery('.commission-success').show(0);
                        jQuery('.commission-success strong').html("Your message has been successfully sent");

                        jQuery('#commissionForm *').each(function(index) {
                            $(this).attr('disabled', true);
                        });

                        $(window).scrollTop(0);
                    },
                    error:function(err) {
                        console.log(err);

                        if (typeof err.responseJSON.errors.captcha3 !== undefined) {
                            jQuery('.email-form-captcha3').show(0);
                            jQuery('.email-form-captcha3 strong').html("Invalid captcha code commission modal.");
                        }

                        refresh_captcha();
                    },
                });
            });
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(window).on("load", function () {
            $('.filter-gallery-toggle-category').click(function(){
                // var type_switch_two = jQuery('input[name="switch-two"]:checked').data("type");

                var catOrSubCat = $(this).data('cat-type');
                var filterType = $(this).data('filter-type');
                var gallery_type = $(this).attr('data-type');
                var post_type = $(this).attr('data-type');
                var gallery_subtype = $(this).attr('data-subtype');
                //$('.filter-gallery-toggle-category, .product-filter-order').removeClass('filter-gallery-toggle-active');

                // $(this).toggleClass('filter-gallery-toggle-active');

                var is_subcat_selected = '';
                // if($('.subcategory ul li').hasClass('filter-gallery-toggle-active')) {
                    is_subcat_selected = 'not empty';
                /*} else {
                    is_subcat_selected = 'empty';
                }*/

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
                            // gallery_view: type_switch_two
                        },
                    success:function(response){
                        // return false;
                        if (gallery_type == "All") {
                            window.location.href="{{ route('site.index') }}";
                        } else {
                            window.location.href="{{ url('/index') }}";
                        }
                    }
                });
            });

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

            $('.product-page-user-click').click(function (e) {

                e.preventDefault();
                e.stopImmediatePropagation();
                var user_id = $(this).data('user-id');

                $.ajax({
                    type: "POST",
                    url: "{{url('toggle_options')}}",
                    data: {user_id: user_id, type: "user", action: "user-selection"},
                    success: function (response) {
                        window.location.href = "{{ route('site.index') }}";
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

            $("#email_seller_id, #hire_id").click(function () {
                $("#emailForm")[0].reset();
                $("#error_messages").html('')
                var site_button_id = $(this).data("id");
                var shop_product_id = $(this).data("product-id");
                $("#site_button_id").val(site_button_id);
                $("#shop_product_id").val(shop_product_id);

                $("#emailModal").modal('show');
            });


            $("#email-form-sending").click(function () {
                if ($("#emailForm").valid()) {
                    var url = '{{route('send-product-email')}}';
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
                        success: function (response) {
                            if (response.success == false) {
                                $("#error_messages").html(response.error);
                            }
                            if (response.success == true) {
                                $("#emailModal").modal('hide');
                            }
                        },
                        error: function () {
                            $("#error_messages").html('System error');
                        }
                    });
                }
                ;
            });

            $(".detailed-button").click(function () {
                var url = '{{route('set-product-click')}}';
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        site_button_id: $("#site_button_id").val(),
                        shop_product_id: $("#shop_product_id").val(),
                    },
                    success: function (response) {
                        console.log("set-product-click:success")
                    },
                    error: function () {
                        console.log("set-product-click:error")
                    }
                });
            });


        });

        $('.slider-for').on('init', function () {

        });

        jQuery(document).ready(function(){
            jQuery("#detail_in").click(function(){
              jQuery(".side_bar").addClass("side_bar_show");
            });
            jQuery("#remove_side").click(function(){
              jQuery(".side_bar").removeClass("side_bar_show");
            });
        });
    </script>

    <script type="text/javascript">
        /* jQuery(".btn-refresh").click(function(){
            jQuery.ajax({
                type:'GET',
                url:'/refresh_captcha',
                success:function(data){
                    jQuery(".captcha span").html(data.captcha);
                }
            });
        }); */

        function refresh_captcha(){
            jQuery.ajax({
                type:'GET',
                url:'/refresh_captcha',
                success:function(data){
                    jQuery(".captcha span").html(data.captcha);
                }
            });
        }

        jQuery(window).on('load', function() {
            if(jQuery('.help-block4 strong').text()){
                if(jQuery('.help-block4 strong').text() == 'The captcha4 field is required.'){
                    jQuery('.help-block4 strong').html('The captcha field is required.');
                    jQuery('#emailModal').modal('show');
                    iccustomfun('emailModal');
                } else if(jQuery('.help-block4 strong').text() == 'validation.captcha'){
                    jQuery('.help-block4 strong').html('Invalid captcha code.');
                    jQuery('#emailModal').modal('show');
                    iccustomfun('emailModal');
                }
            }
            if(jQuery('.help-block3 strong').text()){
                if(jQuery('.help-block3 strong').text() == 'The captcha3 field is required.'){
                    jQuery('.help-block3 strong').html('The captcha field is required.');
                    jQuery('#commissionModal').modal('show');
                    iccustomfun('commissionModal');
                } else if(jQuery('.help-block3 strong').text() == 'validation.captcha'){
                    jQuery('.help-block3 strong').html('Invalid captcha code.');
                    jQuery('#commissionModal').modal('show');
                    iccustomfun('commissionModal');
                }
            }
        });

        function iccustomfun (formid) {
            if(formid == 'commissionModal') {
                var customhtml = '<div class="captcha captcha_inner"><span>{!! captcha_img() !!}</span><button type="button" onclick="refresh_captcha()" class="btn btn-refresh"><svg fill="#000000" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 30 30" width="30px" height="30px"><path d="M 15 3 C 12.031398 3 9.3028202 4.0834384 7.2070312 5.875 A 1.0001 1.0001 0 1 0 8.5058594 7.3945312 C 10.25407 5.9000929 12.516602 5 15 5 C 20.19656 5 24.450989 8.9379267 24.951172 14 L 22 14 L 26 20 L 30 14 L 26.949219 14 C 26.437925 7.8516588 21.277839 3 15 3 z M 4 10 L 0 16 L 3.0507812 16 C 3.562075 22.148341 8.7221607 27 15 27 C 17.968602 27 20.69718 25.916562 22.792969 24.125 A 1.0001 1.0001 0 1 0 21.494141 22.605469 C 19.74593 24.099907 17.483398 25 15 25 C 9.80344 25 5.5490109 21.062074 5.0488281 16 L 8 16 L 4 10 z"/></svg></button></div><input id="captcha3" type="text" class="form-control captcha_field @error("captcha3", "post") is-invalid @enderror" placeholder="ENTER CAPTCHA" name="captcha3">';
                jQuery('#ic_commissionModal').html(customhtml);
            }
            if(formid == 'emailModal') {
                var customhtml = '<div class="captcha captcha_inner"><span>{!! captcha_img() !!}</span><button type="button" onclick="refresh_captcha()" class="btn btn-refresh"><svg fill="#000000" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 30 30" width="30px" height="30px"><path d="M 15 3 C 12.031398 3 9.3028202 4.0834384 7.2070312 5.875 A 1.0001 1.0001 0 1 0 8.5058594 7.3945312 C 10.25407 5.9000929 12.516602 5 15 5 C 20.19656 5 24.450989 8.9379267 24.951172 14 L 22 14 L 26 20 L 30 14 L 26.949219 14 C 26.437925 7.8516588 21.277839 3 15 3 z M 4 10 L 0 16 L 3.0507812 16 C 3.562075 22.148341 8.7221607 27 15 27 C 17.968602 27 20.69718 25.916562 22.792969 24.125 A 1.0001 1.0001 0 1 0 21.494141 22.605469 C 19.74593 24.099907 17.483398 25 15 25 C 9.80344 25 5.5490109 21.062074 5.0488281 16 L 8 16 L 4 10 z"/></svg></button></div><input id="captcha4" type="text" class="form-control captcha_field @error("captcha4", "post") is-invalid @enderror" placeholder="ENTER CAPTCHA" name="captcha4">';
                jQuery('#ic_emailModal').html(customhtml);
            }
        }

        jQuery(document).ready(function(){
            jQuery("#commissionModal").on("hidden.bs.modal", function () {
                jQuery('#ic_commissionModal').html('');
            });
            jQuery("#emailModal").on("hidden.bs.modal", function () {
                jQuery('#ic_emailModal').html('');
            });
        })
    </script>
@endsection
