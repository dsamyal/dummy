<div id="gallery" class="isotope-items-wrap lightgallery gsi-color post-container" style="position: relative;">
	<!-- Grid sizer (do not remove!!!) -->
	<div class="grid-sizer"></div>

	@foreach($products as $product)
	
    <div class="isotope-item" style="margin: 10px;">
		<div class="row">
    		<div class="col-md-12">
    			<div class="profile-container" style="display: table-cell; vertical-align: middle;">
    				<img src="{{ $product->shop_image_url }}" alt="img">
    			</div>
    			<div style="display: table-cell; vertical-align: middle;">
    				<div style="font-size: 18px; letter-spacing: 2px; font-weight: 600;">
    					{{ $product->company_name }}
    				</div>
    				<div style="font-size: 12px; margin-top: -5px;"> 
    					{{ '@'.$product->tagname }} 
    				</div>
    			</div>
    		</div>
		</div>
		<a href="{{ url('/') }}/product_details/{{ $product->id }}">
    		<div class="gallery-single-item isotope-product_image text-center">
        		<img src="{{ asset('images/post/new_images/thumb') }}/{{ $product->thumb }}" class="gs-item-image w-100" alt="">
        		@if(isset($product->comment))
        		<div class="isotope-product-labels isotope-product-comment">{{ $product->comment }}</div>
        		@endif
        		<div class="isotope-product-labels isotope-product-price">{{ $product->price }} EUR</div>
    		</div>
		</a>
		<div class="row">
			<div class="col-xs-6">
				<div class="like-container">
					<img src="{{ asset('assets/img/icon-like-1024.png') }}" alt="img"><span class="like"></span>
				</div>
			</div>
			<div class="col-xs-6">
				<div class="pull-right comment-container">
					<img src="{{ asset('assets/img/icon-comment-1024.png') }}" alt="img"><span class="comment"></span>
				</div>
			</div>	
		</div>
	</div>
		
	@endforeach
	 
</div>