@if($gallery_view == 'justified-with')
<div class="isotope-wrap justified-with" >
    <div class="isotope col-4 gutter-3">
    	<div class="isotope-top-content gallery-share-on"></div>
	
	    <div class ="gallery-full-width">
            <div id="gallery" class="isotope-items-wrap lightgallery gsi-color post-container ctm__ratio1" style="position: relative;">
            	<!-- Grid sizer (do not remove!!!) -->
            	<div class="grid-sizer"></div>
            
            	@foreach($products as $product)

                <div class="isotope-item" style="margin-top: 10px;">
            		<div class="row">
                		<div class="col-md-12">
                            <div class="flex-box">
								<div class="profile-container">
									<a onclick="helplabel('title','titlecookie',1);" href="/{{$product->tagname}}" class="main-page-user-click detailed-user-tag-name-link" data-type="user" data-user-id="{{$product->user_id}}" data-tagname="{{ $product->tagname }}">
										<img src="{{ $product->shop_image_url }}" alt="img" style="border: 1px solid #e5e5e5;">
									</a>
								</div>
								<div class="full-wd">
									<div class="full-wd" style="font-size: 20px;">
										<a href="/{{$product->tagname}}" class="main-page-user-click detailed-user-name-link ibm-regular-400-family text-overflow-ellipsis" data-type="user" data-user-id="{{$product->user_id}}" data-tagname="{{ $product->tagname }}" style="font-family: 'IBM Plex Sans Condensed'; font-size: 18px; color: #e5e5e5; font-weight: 600; letter-spacing: 1px;">{{ $product->shop_name }}</a>
									</div>
									<div style="font-size: 12px; margin-top: -5px;">
										<a onclick="helplabel('title','titlecookie',1);" href="/{{$product->tagname}}" class="main-page-user-click detailed-user-tag-name-link" data-type="user" data-user-id="{{$product->user_id}}" data-tagname="{{ $product->tagname }}" style="font-family: 'IBM Plex Sans Condensed'; font-size: 14px; color: #A09FA4; font-weight: normal; letter-spacing: 1px;">{{ '@'.$product->tagname }}</a>
									</div>
								</div>

								<div class="ctm_product_{{ $product->id }}">
									<input type="hidden" name="like_ajax_action" id="like_ajax_action" value="{!! action('SiteController@like_ajax_action') !!}">
									
									<button class="like_icon" data-shop_product_id="{{ $product->id }}" data-product_user_id="{{ $product->user_id }}">
										<?php if(in_array($product->id, $liked_products)){ ?>
											<svg width="100%" height="100%" viewBox="0 0 4267 4267" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g id="Liked"><path d="M2124.98,3892.68l-80.679,-161.434c-125.159,-250.316 -440.15,-460.3 -773.675,-682.633c-517.43,-344.921 -1103.96,-735.946 -1103.96,-1395.62c0,-788.95 425.063,-1279.02 1109.19,-1279.02c485.795,-0 734.208,238.791 849.129,407.754c115,-168.963 363.329,-407.754 849.121,-407.754c684.17,-0 1109.23,490.066 1109.23,1279.02c-0,659.671 -586.534,1050.7 -1104,1395.62c-333.48,222.288 -648.471,432.354 -773.634,682.633l-80.712,161.434Z" style="fill:#f00;fill-rule:nonzero;"/></g></svg>
										<?php } else { ?>
											<svg width="100%" height="100%" viewBox="0 0 4267 4267" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g id="Like"><path d="M2133.32,3838.41l-78.191,-156.453c-121.297,-242.594 -426.57,-446.099 -749.806,-661.573c-501.466,-334.28 -1069.9,-713.242 -1069.9,-1352.56c0,-764.61 411.949,-1239.56 1074.97,-1239.56c470.808,-0 711.557,231.424 822.933,395.174c111.452,-163.75 352.12,-395.174 822.924,-395.174c663.064,-0 1075.01,474.947 1075.01,1239.56c0,639.319 -568.438,1018.28 -1069.94,1352.56c-323.191,215.429 -628.465,419.015 -749.766,661.573l-78.222,156.453Z" style="fill:#393939;fill-rule:nonzero;"/><path d="M1284.19,554.488c-581.479,-0 -928.642,410.658 -928.642,1098.51c0,563.071 520.375,909.988 1023.58,1245.46c299.321,199.571 584.63,389.741 754.192,620.941c169.596,-231.2 454.867,-421.45 754.188,-620.941c503.25,-335.471 1023.66,-682.388 1023.66,-1245.46c0,-687.854 -347.246,-1098.51 -928.729,-1098.51c-607.217,-0 -761.979,443.283 -763.471,447.717l-85.65,256.87l-85.612,-256.87c-6.175,-17.825 -161.475,-447.717 -763.517,-447.717m849.129,3338.2l-80.679,-161.434c-125.158,-250.316 -440.15,-460.3 -773.675,-682.633c-517.429,-344.921 -1103.96,-735.946 -1103.96,-1395.62c0,-788.95 425.063,-1279.02 1109.19,-1279.02c485.796,-0 734.208,238.791 849.129,407.754c115,-168.963 363.329,-407.754 849.121,-407.754c684.171,-0 1109.23,490.066 1109.23,1279.02c0,659.671 -586.533,1050.7 -1104,1395.62c-333.479,222.288 -648.471,432.354 -773.633,682.633l-80.713,161.434Z" style="fill:#c8c8c8;fill-rule:nonzero;"/></g></svg>
										<?php } ?>
									</button>
								</div>


                            </div>
                		</div>
            		</div>
            		<a href="{{ url('/') }}/{{ $product->tagname }}/{{ $product->id }}">
                		<div class="gallery-single-item isotope-product_image text-center">
                    		<img src="{{ asset('images/post/new_images/thumb') }}/{{ $product->thumb }}" class="gs-item-image w-100" alt="">
                    		@if(isset($product->comment))
                    		<div class="isotope-product-labels isotope-product-comment">{{ $product->comment }}</div>
                    		@endif
							@if((int)$product->price> 0)
                    		<div class="isotope-product-labels isotope-product-price" >{{ $product->price }} EUR</div>
							@endif
                		</div>
            		</a>
            		<div class="text-center">
						<?php if(!isset($_COOKIE['titlecookie'])){?>
							<span id="titlelabel" class="helplabel titlelabel ctm_above_title"> <p>Tap title to see image full screen </p><button type="button" onclick="helplabel('title','titlecookie',1);">OK</button></span>
						<?php } ?>
						<a href="{{ url('/') }}/{{ $product->tagname }}/{{ $product->id }}" class="detailed-user-name-link ibm-regular-400-family">
                        <!-- <a href="{{ url('/') }}/product_details/{{ $product->id }}" class="main-page-user-click detailed-user-name-link ibm-regular-400-family text-overflow-ellipsis"> -->
                			<h2 class="m-0 prozak-light-family" style="font-weight:normal; font-size:23px; letter-spacing:2px; line-height:1.1em; padding-top:9px;">{{ $product->name }}</h2>
                        </a>

                		<div class="full-wd" style="font-size: 20px;">
							<p class="byartist" style="font-family: 'IBM Plex Sans Condensed'; font-size: 18px; color: #e5e5e5; font-weight: 600; letter-spacing: 1px;">
								<?php if($product->artist_name){ echo "<span>By </span>"; } ?>{{ $product->artist_name }}
							</p>
						</div>
						<div class="full-wd ctm_titlecookie" style="font-size: 20px;">
							<a onclick="helplabel('title','titlecookie',1);" href="/{{$product->tagname}}" class="main-page-user-click detailed-user-name-link ibm-regular-400-family text-overflow-ellipsis" data-type="user" data-user-id="{{$product->user_id}}" data-tagname="{{ $product->tagname }}"><span>Added by </span>{{ $product->shop_name }}</a>
							<?php if(!isset($_COOKIE['titlecookie'])){?>
								<span id="titlelabel" class="helplabel titlelabel1 ctm_bottom_title"> <p>Tap name to see user's post </p><button type="button" onclick="helplabel('title','titlecookie',1);">OK</button></span>
							<?php } ?>
						</div>
            		</div>
            	</div>
            		
            	@endforeach
            	 
            </div>
        </div>
    </div>
</div>
@elseif($gallery_view == 'square')
<div class="isotope-wrap square">
    <div class="isotope col-4 gutter-3">
    	<div class="isotope-top-content gallery-share-on">
    	</div>
	
	    <div class ="gallery-full-width">
            <div id="gallery" class="isotope-items-wrap lightgallery gsi-color post-container ctm__ratio2" style="position: relative;">
            	<!-- Grid sizer (do not remove!!!) -->
            	<div class="grid-sizer"></div>

                	@foreach($products as $product)

                    <div class="ctm_square">
                        <div class="isotope-item gallery-single-item-square" style="margin: 10px; background-image:url({{ asset('images/post/new_images/thumb') }}/{{ $product->thumb }}) /* height:300px; overflow:hidden;*/ ">
                    		<a href="{{ url('/') }}/product_details/{{ $product->id }}">
                        		<div class="gallery-single-item isotope-product_image text-center squared-clickable-item" >
                            		<!--<img src="{{ asset('images/post/new_images/thumb') }}/{{ $product->thumb }}" style="min-height:300px;" class="gs-item-image w-100" alt="">-->
                        		</div>
                                @if((int)$product->price> 0)
                                <div class="isotope-product-labels isotope-product-price" >{{ $product->price }} EUR</div>
                                @endif
                    		</a>
                            <div class="ctm_square_hover ctm_product_{{ $product->id }}">
								
                                <div class="ctm_square_child">
									<?php if(!isset($_COOKIE['titlecookie'])){?>
										<span id="titlelabel" class="helplabel titlelabel"> <p>Tap title to see image full screen </p><button type="button" onclick="helplabel('title','titlecookie',1);">OK</button></span>
									<?php } ?>

									<input type="hidden" name="like_ajax_action" id="like_ajax_action" value="{!! action('SiteController@like_ajax_action') !!}">
								
									<button class="like_icon" data-shop_product_id="{{ $product->id }}" data-product_user_id="{{ $product->user_id }}">
										<?php if(in_array($product->id, $liked_products)){ ?>
											<svg width="100%" height="100%" viewBox="0 0 4267 4267" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g id="Liked"><path d="M2124.98,3892.68l-80.679,-161.434c-125.159,-250.316 -440.15,-460.3 -773.675,-682.633c-517.43,-344.921 -1103.96,-735.946 -1103.96,-1395.62c0,-788.95 425.063,-1279.02 1109.19,-1279.02c485.795,-0 734.208,238.791 849.129,407.754c115,-168.963 363.329,-407.754 849.121,-407.754c684.17,-0 1109.23,490.066 1109.23,1279.02c-0,659.671 -586.534,1050.7 -1104,1395.62c-333.48,222.288 -648.471,432.354 -773.634,682.633l-80.712,161.434Z" style="fill:#f00;fill-rule:nonzero;"/></g></svg>
										<?php } else { ?>
											<svg width="100%" height="100%" viewBox="0 0 4267 4267" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g id="Like"><path d="M2133.32,3838.41l-78.191,-156.453c-121.297,-242.594 -426.57,-446.099 -749.806,-661.573c-501.466,-334.28 -1069.9,-713.242 -1069.9,-1352.56c0,-764.61 411.949,-1239.56 1074.97,-1239.56c470.808,-0 711.557,231.424 822.933,395.174c111.452,-163.75 352.12,-395.174 822.924,-395.174c663.064,-0 1075.01,474.947 1075.01,1239.56c0,639.319 -568.438,1018.28 -1069.94,1352.56c-323.191,215.429 -628.465,419.015 -749.766,661.573l-78.222,156.453Z" style="fill:#393939;fill-rule:nonzero;"/><path d="M1284.19,554.488c-581.479,-0 -928.642,410.658 -928.642,1098.51c0,563.071 520.375,909.988 1023.58,1245.46c299.321,199.571 584.63,389.741 754.192,620.941c169.596,-231.2 454.867,-421.45 754.188,-620.941c503.25,-335.471 1023.66,-682.388 1023.66,-1245.46c0,-687.854 -347.246,-1098.51 -928.729,-1098.51c-607.217,-0 -761.979,443.283 -763.471,447.717l-85.65,256.87l-85.612,-256.87c-6.175,-17.825 -161.475,-447.717 -763.517,-447.717m849.129,3338.2l-80.679,-161.434c-125.158,-250.316 -440.15,-460.3 -773.675,-682.633c-517.429,-344.921 -1103.96,-735.946 -1103.96,-1395.62c0,-788.95 425.063,-1279.02 1109.19,-1279.02c485.796,-0 734.208,238.791 849.129,407.754c115,-168.963 363.329,-407.754 849.121,-407.754c684.171,-0 1109.23,490.066 1109.23,1279.02c0,659.671 -586.533,1050.7 -1104,1395.62c-333.479,222.288 -648.471,432.354 -773.633,682.633l-80.713,161.434Z" style="fill:#c8c8c8;fill-rule:nonzero;"/></g></svg>
										<?php } ?>
									</button>

                                    <a onclick="helplabel('title','titlecookie',1);" href="{{ url('/') }}/product_details/{{ $product->id }}">
                                        <h2 class="m-0 prozak-light-family" style="font-weight:normal; font-size:23px; letter-spacing:2px; line-height:.8; padding-top:9px;"><?php if(strlen($product->name) > 35){ echo '"'.substr($product->name, 0, 35).'..."'; } else { echo '"'.$product->name.'"'; } ?></h2>
                                    </a>
                                    <div class="full-wd" style="font-size: 20px;">
                                        <p style="font-family: 'IBM Plex Sans Condensed'; font-size: 18px; color: #e5e5e5; font-weight: 600; letter-spacing: 1px;"><?php if($product->artist_name){ echo "<span>By </span>"; } ?>{{ $product->artist_name }}</p>
                                    </div>
                                    <div class="full-wd" style="font-size: 20px;">
                                        <a onclick="helplabel('title','titlecookie',1);" href="/{{$product->tagname}}" class="main-page-user-click detailed-user-name-link ibm-regular-400-family text-overflow-ellipsis" data-type="user" data-user-id="{{$product->user_id}}" data-tagname="{{ $product->tagname }}"><span>Added by </span>{{ $product->shop_name }}</a>
										<?php if(!isset($_COOKIE['titlecookie'])){?>
                        					<span id="titlelabel" class="helplabel titlelabel1"> <p>Tap name to see user's post </p><button type="button" onclick="helplabel('title','titlecookie',1);">OK</button></span>
                						<?php } ?>
                                    </div>
                                </div>
                            </div>
                    	</div>
                    </div>

                	@endforeach
	 
            </div>
        </div>
    </div>
</div>
@else
<div class="isotope-wrap else">
    <div class="isotope col-4 gutter-3">
    	<div class="isotope-top-content gallery-share-on">
    	</div>
	
	    <div class ="gallery-full-width"> 
            <div id="gallery" class="isotope-items-wrap lightgallery gsi-color post-container ctm__ratio3" style="position: relative;">
            	<!-- Grid sizer (do not remove!!!) -->
            	<div class="grid-sizer"></div>

            	@foreach($products as $product)
            	
                <div class="ctm_square">
                    <div class="isotope-item" style="overflow:hidden; margin-top: 10px;">
                		<a href="{{ url('/') }}/{{ $product->tagname }}/{{ $product->id }}">
                    		<div class="gallery-single-item isotope-product_image text-center">
                        		<img src="{{ asset('images/post/new_images/thumb') }}/{{ $product->thumb }}" class="gs-item-image w-100" alt="">
                    		</div>
                            @if((int)$product->price> 0)
                            <div class="isotope-product-labels isotope-product-price" >{{ $product->price }} EUR</div>
                            @endif
                		</a>
                        <div class="ctm_square_hover ctm_product_{{ $product->id }}">
                            <div class="ctm_square_child">
								<?php if(!isset($_COOKIE['titlecookie'])){ ?>
                        			<span id="titlelabel" class="helplabel titlelabel"> <p>Tap title to see image full screen </p><button type="button" onclick="helplabel('title','titlecookie',1);">OK</button></span>
                				<?php } ?>

								<input type="hidden" name="like_ajax_action" id="like_ajax_action" value="{!! action('SiteController@like_ajax_action') !!}">
								
								<button class="like_icon" data-shop_product_id="{{ $product->id }}" data-product_user_id="{{ $product->user_id }}">
									<?php if(!empty($liked_products)){ ?>
										<?php if(in_array($product->id, $liked_products)){ ?>
											<svg width="100%" height="100%" viewBox="0 0 4267 4267" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g id="Liked"><path d="M2124.98,3892.68l-80.679,-161.434c-125.159,-250.316 -440.15,-460.3 -773.675,-682.633c-517.43,-344.921 -1103.96,-735.946 -1103.96,-1395.62c0,-788.95 425.063,-1279.02 1109.19,-1279.02c485.795,-0 734.208,238.791 849.129,407.754c115,-168.963 363.329,-407.754 849.121,-407.754c684.17,-0 1109.23,490.066 1109.23,1279.02c-0,659.671 -586.534,1050.7 -1104,1395.62c-333.48,222.288 -648.471,432.354 -773.634,682.633l-80.712,161.434Z" style="fill:#f00;fill-rule:nonzero;"/></g></svg>
										<?php } else { ?>
											<svg width="100%" height="100%" viewBox="0 0 4267 4267" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g id="Like"><path d="M2133.32,3838.41l-78.191,-156.453c-121.297,-242.594 -426.57,-446.099 -749.806,-661.573c-501.466,-334.28 -1069.9,-713.242 -1069.9,-1352.56c0,-764.61 411.949,-1239.56 1074.97,-1239.56c470.808,-0 711.557,231.424 822.933,395.174c111.452,-163.75 352.12,-395.174 822.924,-395.174c663.064,-0 1075.01,474.947 1075.01,1239.56c0,639.319 -568.438,1018.28 -1069.94,1352.56c-323.191,215.429 -628.465,419.015 -749.766,661.573l-78.222,156.453Z" style="fill:#393939;fill-rule:nonzero;"/><path d="M1284.19,554.488c-581.479,-0 -928.642,410.658 -928.642,1098.51c0,563.071 520.375,909.988 1023.58,1245.46c299.321,199.571 584.63,389.741 754.192,620.941c169.596,-231.2 454.867,-421.45 754.188,-620.941c503.25,-335.471 1023.66,-682.388 1023.66,-1245.46c0,-687.854 -347.246,-1098.51 -928.729,-1098.51c-607.217,-0 -761.979,443.283 -763.471,447.717l-85.65,256.87l-85.612,-256.87c-6.175,-17.825 -161.475,-447.717 -763.517,-447.717m849.129,3338.2l-80.679,-161.434c-125.158,-250.316 -440.15,-460.3 -773.675,-682.633c-517.429,-344.921 -1103.96,-735.946 -1103.96,-1395.62c0,-788.95 425.063,-1279.02 1109.19,-1279.02c485.796,-0 734.208,238.791 849.129,407.754c115,-168.963 363.329,-407.754 849.121,-407.754c684.171,-0 1109.23,490.066 1109.23,1279.02c0,659.671 -586.533,1050.7 -1104,1395.62c-333.479,222.288 -648.471,432.354 -773.633,682.633l-80.713,161.434Z" style="fill:#c8c8c8;fill-rule:nonzero;"/></g></svg>
										<?php } ?>
									<?php } else { ?>
										<svg width="100%" height="100%" viewBox="0 0 4267 4267" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g id="Like"><path d="M2133.32,3838.41l-78.191,-156.453c-121.297,-242.594 -426.57,-446.099 -749.806,-661.573c-501.466,-334.28 -1069.9,-713.242 -1069.9,-1352.56c0,-764.61 411.949,-1239.56 1074.97,-1239.56c470.808,-0 711.557,231.424 822.933,395.174c111.452,-163.75 352.12,-395.174 822.924,-395.174c663.064,-0 1075.01,474.947 1075.01,1239.56c0,639.319 -568.438,1018.28 -1069.94,1352.56c-323.191,215.429 -628.465,419.015 -749.766,661.573l-78.222,156.453Z" style="fill:#393939;fill-rule:nonzero;"/><path d="M1284.19,554.488c-581.479,-0 -928.642,410.658 -928.642,1098.51c0,563.071 520.375,909.988 1023.58,1245.46c299.321,199.571 584.63,389.741 754.192,620.941c169.596,-231.2 454.867,-421.45 754.188,-620.941c503.25,-335.471 1023.66,-682.388 1023.66,-1245.46c0,-687.854 -347.246,-1098.51 -928.729,-1098.51c-607.217,-0 -761.979,443.283 -763.471,447.717l-85.65,256.87l-85.612,-256.87c-6.175,-17.825 -161.475,-447.717 -763.517,-447.717m849.129,3338.2l-80.679,-161.434c-125.158,-250.316 -440.15,-460.3 -773.675,-682.633c-517.429,-344.921 -1103.96,-735.946 -1103.96,-1395.62c0,-788.95 425.063,-1279.02 1109.19,-1279.02c485.796,-0 734.208,238.791 849.129,407.754c115,-168.963 363.329,-407.754 849.121,-407.754c684.171,-0 1109.23,490.066 1109.23,1279.02c0,659.671 -586.533,1050.7 -1104,1395.62c-333.479,222.288 -648.471,432.354 -773.633,682.633l-80.713,161.434Z" style="fill:#c8c8c8;fill-rule:nonzero;"/></g></svg>
									<?php } ?>
								</button>



                                <a href="{{ url('/') }}/{{ $product->tagname }}/{{ $product->id }}">
                                    <h2 class="m-0 prozak-light-family" style="font-weight:normal; font-size:23px; letter-spacing:2px; line-height:.8; padding-top:9px;"><?php if(strlen($product->name) > 35){ echo '"'.substr($product->name, 0, 35).'..."'; } else { echo '"'.$product->name.'"'; } ?></h2>
                                </a>
                                <div class="full-wd" style="font-size: 20px;">
                                    <p style="font-family: 'IBM Plex Sans Condensed'; font-size: 18px; color: #e5e5e5; font-weight: 600; letter-spacing: 1px;"><?php if($product->artist_name){ echo "<span>By </span>"; } ?>{{ $product->artist_name }}</p>
                                </div>
                                <div class="full-wd" style="font-size: 20px;">
                                    <a href="/{{$product->tagname}}" class="main-page-user-click detailed-user-name-link ibm-regular-400-family text-overflow-ellipsis" data-type="user" data-user-id="{{$product->user_id}}" data-tagname="{{ $product->tagname }}"><span>Added by </span>{{ $product->shop_name }}</a>
									<?php if(!isset($_COOKIE['titlecookie'])){?>
                        			<span id="titlelabel" class="helplabel titlelabel1"> <p>Tap name to see user's posts  </p><button type="button" onclick="helplabel('title','titlecookie',1);">OK</button></span>
									
                				<?php } ?>
                                </div>
                            </div>
                        </div>
                	</div>
                </div>
            		
            	@endforeach
            	 
            </div>
        </div>
    </div>
</div>
@endif

