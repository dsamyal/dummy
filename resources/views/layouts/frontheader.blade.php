<header id="header" class="header-show-hide-on-scroll1 menu-align-right" >
	<!-- Begin header inner -->
	<div class="header-inner">
	    <div class="container-fluid">
    		<div class="row">
    			<div class="col-xs-3 col-sm-4">
                    <div class="search-bar">
                       <form method="get" id="search_form" action="{{ url('search') }}">
                            {{-- {{ csrf_field() }} --}}

                            <a href="#" class="search-icon @if($mainSearchFilter != '') hidden @endif" id="main-search-icon"><i class="fa fa-search search-position"></i></a>

                            <div class="search_form_div">
                                <input type="text" value="<?php echo htmlspecialchars($mainSearchFilter, ENT_QUOTES) ?>"
                                       name="main-search-filter" id="main-search-filter" class="@if($mainSearchFilter == '') hidden @endif main-search-filter" placeholder="">

                                <button id="search_close" class="<?php if(@$_REQUEST['main-search-filter']){ echo 'ctm_show'; } ?>"><i class="fa fa-times-circle" aria-hidden="true"></i></button>
                            </div>

                            <button class="@if($mainSearchFilter == '') hidden @endif search-icon" id="main-search-button" type="submit"><i class="fa fa-search"></i></button>
                        </form>
                    </div>
                </div>
    			<div class="col-xs-6 col-sm-4 text-center">
    			    <div id="header-center">
    					<a href="{{ route('site.index') }}" class="logo-dark">
    						<span class="logo-text">ARTfora GALLERY</span>
    					</a>
    				</div>
    			</div>
    			<div class="col-xs-3 col-sm-4 text-right" id="header-right" >
                <?php if(!isset($_COOKIE['contactcookie'])){?>
                        <span id="contactlabel" class="helplabel contact"> <p>Contact us </p>
                            <button type="button" onclick="helplabel('contact','contactcookie',1);">OK</button>
                        </span>
                <?php } ?>

                    <div class="img_footer_div">
                        <?php if(@$get_user_filter && @$get_user_filter->profile_image_url){ ?>
                            <a href="#"  data-toggle="modal" data-target="#exampleModal_1">
                              <img src="<?php echo $get_user_filter->profile_image_url; ?>" alt="logo" class="header__logo">
                            </a>
                        <?php } else { ?>
                            <a href="#"  data-toggle="modal" data-target="#exampleModal_1">
                              <img src="{{ asset('assets/img/logo.png') }}" alt="logo" class="header__logo">
                            </a>
                        <?php } ?>
                    </div>

                    <!-- login Modal -->
                    <div class="modal fade" id="exampleModal_1" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="login__contact_hide_show">
                                        <?php $userId = Auth::id(); ?>
                                        <?php if($userId){ ?>
                                            <a class="dropdown-item login_r_button" href="{{ route('logout') }}"
                                                   onclick="event.preventDefault();
                                                                 document.getElementById('logout-form').submit();">
                                                    {{ __('Logout') }}
                                                </a>

                                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                                    @csrf
                                                </form>
                                        <?php } else { ?>
                                            <button type="button" class="btn btn-primary login_r_button" data-toggle="modal" data-target="#exampleModal">
                                                Login
                                            </button>
                                        <?php } ?>

                                        <a href="#" onclick="iccustomfun('logo_modal')" class="login_r_button login_r_button_contact" data-toggle="modal" data-target="#logo_modal">
                                            Contact Us
                                        </a>
                                        <?php if(empty($userId)){ ?>
                                            <button type="button" class="btn btn-primary login_r_button register_btn" data-toggle="modal" data-target="#register_modal">
                                                Register (free)
                                            </button>
                                        <?php } ?>
                                        <a href="#" onclick="iccustomfun('join_gallery')" class="login_r_button login_r_button_contact" data-toggle="modal" data-target="#join_gallery">
                                            Join gallery (free)
                                        </a>
                                        <?php if($userId && Auth::user()->role == 'admin'){ ?>
                                            <a href="/home" class="login_r_button login_r_button_contact" >
                                                Admin Panel
                                            </a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade ctm_modal_comman" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                        <center><span class="logo-text">LOGIN ARTfora</span></center>
                                    </div>
                                    <div class="modal_form">
                                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                                        @csrf

                                        <span class="invalid-feedback hidden-error-main" role="alert" style="display: none;">
                                            <strong></strong>
                                        </span>

                                        <?php if(@$_GET['ver'] == 1){ ?>
                                            <p style="margin-bottom:5px">Your email address has been verified. Please login.</p>
                                        <?php } ?>
                                        <div class="form-group email_message">
                                            <!-- <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label> -->
                                            <span class="form_icon"><img class="btn-icon" style="margin-left: 3px;" width="23" height="23" src="/images/icon_envelope.svg" alt="Name"></span>
                                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required placeholder="EMAIL">

                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror

                                                <span class="invalid-feedback hidden-error-email" role="alert" style="display: none;">
                                                    <strong></strong>
                                                </span>
                                        </div>

                                        <div class="form-group">
                                            <!-- <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label> -->
                                            <span class="form_icon"><img class="btn-icon" style="margin-left: 3px;" width="23" height="23" src="/images/icon_key.svg" alt="Name"></span>
                                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="PASSWORD">
                                                <i class="toggle-password fa fa-fw fa-eye-slash"></i>
                                                @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror

                                                <span class="invalid-feedback hidden-error-password" role="alert" style="display: none;">
                                                    <strong></strong>
                                                </span>
                                        </div>

                                        <div class="form-group m-0">
                                                <div class="form-check">
                                                    <input class="checkbox" type="checkbox" style="display: inline; float: left;" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                    <label class="checkbox-label" for="remember">
                                                        {{ __('Remember Me') }}
                                                    </label>
                                                </div>
                                        </div>

                                        <div class="form-group m-0">
                                            <div class="login_button_">
                                                <button type="submit" class="btn btn-primary button_l">
                                                    {{ __('Login') }}
                                                </button>
                                                {{-- @if (Route::has('password.request'))
                                                    <p class="btn btn-link forgot_in">
                                                        <!-- {{ __('Forgot Your Password?') }} -->
                                                        If you have forgotten your password,<br>open the ARTfora app, log out and choose<br>"Forgot password" from the LOG IN screen.
                                                    </p>
                                                @endif --}}
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                Don't have account?<button class="register_click_button btn-link" data-toggle="modal" data-target="#register_modal">Register</button>
                                <a href="/password/reset" class="forgot_a_tag">Forgot Password?</a>
                                </div>
                                <!-- <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary">Save changes</button>
                                </div> -->
                            </div>
                        </div>
                    </div>


                    <div class="modal fade ctm_modal_comman" id="register_modal" tabindex="-1" role="dialog" aria-labelledby="register_modalLabel" aria-hidden="true">
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
                                        <center><span class="logo-text">REGISTER ARTfora</span></center>
                                    </div>
                                    <div class="modal_form">
                                        <form method="POST" action="{{ route('user_register') }}" id="registerForm">
                                            @csrf
                                            <div class="form-group">
                                            <span class="form_icon"><img class="btn-icon" style="margin-left: 3px;" width="23" height="23" src="/images/icon_user.svg" alt="Name"></span>
                                            
                                                <input type="text" class="form-control" name="username" placeholder="NAME" required>

                                                <span class="invalid-feedback hidden-error-username" role="alert" style="display: none;">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                            <div class="form-group">
                                                <span class="form_icon"><img class="btn-icon" style="margin-left: 3px;" width="23" height="23" src="/images/icon_envelope.svg" alt="Name"></span>
                                                <input type="email" class="form-control" name="email" placeholder="EMAIL" required>

                                                <span class="invalid-feedback hidden-error-email" role="alert" style="display: none;">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                            <div class="form-group">
                                                <label style="font-size:14px">Minimum: 8 characters, 1 number and 1 capital letter.</label>
                                                <span class="icon_password">
                                                <span class="form_icon"><img class="btn-icon" style="margin-left: 3px;" width="23" height="23" src="/images/icon_key.svg" alt="Name"></span>
                                                <input type="password" class="form-control" name="password" placeholder="PASSWORD" required>
                                                </span>

                                                <span class="invalid-feedback hidden-error-password" role="alert" style="display: none;">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                            <div class="form-group">
                                                <span class="form_icon"><img class="btn-icon" style="margin-left: 3px;" width="23" height="23" src="/images/icon_key.svg" alt="Name"></span>
                                                <input type="password" class="form-control" name="c_password" placeholder="CONFIRM PASSWORD" required>

                                                <span class="invalid-feedback hidden-error-c_password" role="alert" style="display: none;">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                            <div class="form-group">
                                                <?php $j = 0; ?>
                                                @foreach($get_filters_radio as $filters_radion)
                                                    <input type="radio" class="radio-input" id="check_<?php echo $j+1; ?>" name="radio_filter" value="{{ $filters_radion->id }}" <?php if($j == 0){ echo 'checked'; } ?> >
                                                        <label for="check_<?php echo $j+1; ?>" class="radio-label"> <span class="radio-border"></span> {{ $filters_radion->filter_text }}</label>
                                                     <?php $j++; ?>
                                                @endforeach
                                            </div>
                                            
                                            <div class="form-group m-0">
                                                <div class="login_button_">
                                                    <button type="submit" class="btn btn-primary button_l">
                                                        {{ __('Submit') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade ctm_modal_comman" id="register_success_modal" tabindex="-1" role="dialog" aria-labelledby="register_modalLabel" aria-hidden="true">
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
                                        <center><span class="logo-text">ARTfora Gallery and App</span></center>
                                    </div>
                                    <div class="modal_form">
                                        <p>Register successful! Verification email is sent to your email address.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Modal -->
                    <div class="modal fade ctm_modal_comman" id="logo_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" >
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
                                        <center><span class="logo-text">CONTACT ARTfora</span></center>
                                    </div>
                                    <div class="modal_form">
                                        <form method="POST" action="{!! action('SiteController@contact_modal') !!}" id="contact_form_submit">
                                            {{ csrf_field() }}

                                            <span class="help-block contact-form-success" style="display:none; margin-bottom: 15px;">
                                                <strong style="color:green;"></strong>
                                            </span>

                                            <div class="form-group">
                                                <span class="form_icon"><img class="btn-icon" style="margin-left: 3px;" width="23" height="23" src="/images/icon_user.svg" alt="Name"></span>
                                                <input type="text" class="form-control" name="contact_name_con_modal" placeholder="NAME" required value="{{ old('contact_name_con_modal') }}">
                                            </div>
                                            <div class="form-group">
                                                <span class="form_icon"><img class="btn-icon" style="margin-left: 3px;" width="23" height="23" src="/images/icon_envelope.svg" alt="Email address"></span>
                                                <input type="email" class="form-control" name="contact_email_con_modal" placeholder="EMAIL ADDRESS" required value="{{ old('contact_email_con_modal') }}">
                                            </div>
                                            <div class="form-group">
                                                <textarea placeholder="MESSAGE" rows="10" name="contact_message_con_modal" class="form-control form-control_message" required>{{ old('contact_message_con_modal') }}</textarea>
                                            </div>

                                            <div class="captcha_main form-group{{ $errors->has('captcha2') ? ' has-error' : '' }}">
                                                <!-- <label for="password" class="control-label">Captcha</label> -->
                                                <!--<div class="captcha captcha_inner">
                                                    <span>{!! captcha_img() !!}</span>
                                                    <button type="button" class="btn btn-refresh">
                                                    <svg fill="#000000" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 30 30" width="30px" height="30px"><path d="M 15 3 C 12.031398 3 9.3028202 4.0834384 7.2070312 5.875 A 1.0001 1.0001 0 1 0 8.5058594 7.3945312 C 10.25407 5.9000929 12.516602 5 15 5 C 20.19656 5 24.450989 8.9379267 24.951172 14 L 22 14 L 26 20 L 30 14 L 26.949219 14 C 26.437925 7.8516588 21.277839 3 15 3 z M 4 10 L 0 16 L 3.0507812 16 C 3.562075 22.148341 8.7221607 27 15 27 C 17.968602 27 20.69718 25.916562 22.792969 24.125 A 1.0001 1.0001 0 1 0 21.494141 22.605469 C 19.74593 24.099907 17.483398 25 15 25 C 9.80344 25 5.5490109 21.062074 5.0488281 16 L 8 16 L 4 10 z"/></svg>
                                                    </button>
                                                </div> -->
                                                <!--<input id="captcha2" type="text" class="form-control captcha_field @error('captcha2', 'post') is-invalid @enderror" placeholder="ENTER CAPTCHA" name="captcha2">-->
                                                <div id="ic_logo_modal"></div>

                                                @if ($errors->has('captcha2'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('captcha2') }}</strong>
                                                    </span>
                                                @endif

                                                <span class="help-block contact-form-captcha2" style="display:none;">
                                                    <strong></strong>
                                                </span>
                                            </div>

                                            <input type="submit" value="send" name="submit" class="detailed-button">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- join gallery Modal -->
                    <div class="modal fade ctm_modal_comman" id="join_gallery" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" >
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
                                        <center><span class="logo-text">CONTACT ARTfora</span></center>
                                    </div>
                                    <p class="sub-heading">Our gallery is completely free to use for everyone, and any artist can join. Send us a message and we will send you an email with the details.</p> <p class="sub-heading"> We look forward to share your art for everybody to enjoy it!</p>
                                    <div class="modal_form">
                                        <form method="POST" action="{!! action('SiteController@join_gallery') !!}" id="joinGalleryForm">
                                            {{ csrf_field() }}

                                            <span class="help-block join-success" style="display:none; margin-bottom: 15px;">
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
                                            <div class="form-group">
                                                <textarea placeholder="MESSAGE" rows="10" name="contact_message" class="form-control form-control_message" required>{{ old('contact_message') }}</textarea>
                                            </div>

                                            <div class="captcha_main form-group{{ $errors->has('captcha1', 'post') ? ' has-error' : '' }}">
                                                <!-- <label for="password" class="control-label">Captcha</label> -->

                                                <!-- <div class="captcha captcha_inner">
                                                    <span>{!! captcha_img() !!}</span>
                                                    <button type="button" class="btn btn-refresh">
                                                    <svg fill="#000000" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 30 30" width="30px" height="30px"><path d="M 15 3 C 12.031398 3 9.3028202 4.0834384 7.2070312 5.875 A 1.0001 1.0001 0 1 0 8.5058594 7.3945312 C 10.25407 5.9000929 12.516602 5 15 5 C 20.19656 5 24.450989 8.9379267 24.951172 14 L 22 14 L 26 20 L 30 14 L 26.949219 14 C 26.437925 7.8516588 21.277839 3 15 3 z M 4 10 L 0 16 L 3.0507812 16 C 3.562075 22.148341 8.7221607 27 15 27 C 17.968602 27 20.69718 25.916562 22.792969 24.125 A 1.0001 1.0001 0 1 0 21.494141 22.605469 C 19.74593 24.099907 17.483398 25 15 25 C 9.80344 25 5.5490109 21.062074 5.0488281 16 L 8 16 L 4 10 z"/></svg>
                                                    </button>
                                                </div> -->

                                                <div id="ic_join_gallery"></div>
                                                <!-- <input id="captcha1" type="text" class="captcha_field form-control @error('captcha1', 'post') is-invalid @enderror " placeholder="ENTER CAPTCHA" name="captcha1"> -->

                                                    <!-- @error('captcha')
                                                        <div class="alert alert-danger help-block1">{{ $message }}</div>
                                                    @enderror -->
                                                @if ($errors->has('captcha1'))
                                                    <span class="help-block1">
                                                        <strong>{{ $errors->first('captcha1') }}</strong>
                                                    </span>
                                                @endif

                                                <span class="help-block contact-form-captcha1" style="display:none;">
                                                    <strong></strong>
                                                </span>
                                            </div>

                                            <input type="hidden" name="mail_subject" value="Join ARTfora gallery">
                                            <!-- <button class="detailed-button" name="submit" value="send">SEND MESSAGE</button>  -->
                                            <input type="submit" value="SEND MESSAGE" name="submit1" class="detailed-button">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
    		</div>
		</div>
	</div>
    <input type="hidden" id = "home_url_header" value="{{ url('/') }}">
    <script type="text/javascript">
        jQuery(document).ready(function(){
            // LOGIN FORM
            jQuery('#loginForm button[type="submit"]').click(function(submitEvent) {
                submitEvent.preventDefault();

                let email = jQuery('#loginForm input[name="email"]').val();
                let password = jQuery('#loginForm input[name="password"]').val();
                let remember = jQuery('#loginForm input[name="remember"]').val();
                let token = jQuery('#loginForm input[name="_token"]').val();

                let action = jQuery('#loginForm').attr('action');

                jQuery('.hidden-error-email').hide(0);

                $.ajax({
                    url:action,
                    type:'POST',
                    data:{
                        'email':email,
                        'password':password,
                        'remember':remember,
                        '_token':token,
                    },
                    accepts: {
                        text: "application/json"
                    },
                    success:function(response) {
                        location.assign('/');
                    },
                    error:function(err) {
                        console.log(err);

                        // jQuery('.hidden-error-main').show(0);
                        // jQuery('.hidden-error-main strong').html(err.responseJSON.message);

                        if (typeof err.responseJSON.errors.email !== undefined) {
                            jQuery('#exampleModal .hidden-error-email').show(0);
                            jQuery('#exampleModal .hidden-error-email strong').html(err.responseJSON.errors.email);
                        }

                        // if (typeof err.responseJSON.errors.password !== undefined) {
                        //     jQuery('.hidden-error-password').show(0);
                        //     jQuery('.hidden-error-password strong').html(err.responseJSON.errors.password);
                        // }

                        refresh_captcha();
                    },
                });
            });

            jQuery('#registerForm button[type="submit"]').click(function(submitEvent) {
                submitEvent.preventDefault();

                let username = jQuery('#registerForm input[name="username"]').val();
                let email = jQuery('#registerForm input[name="email"]').val();
                let password = jQuery('#registerForm input[name="password"]').val();
                let c_password = jQuery('#registerForm input[name="c_password"]').val();
                let token = jQuery('#registerForm input[name="_token"]').val();
                let radio_filter = jQuery('input[name="radio_filter"]:checked').val();

                let action = jQuery('#registerForm').attr('action');

                $.ajax({
                    url:action,
                    type:'POST',
                    data:{
                        'username':username,
                        'email':email,
                        'password':password,
                        'c_password':c_password,
                        'radio_filter':radio_filter,
                        '_token':token,
                    },
                    accepts: {
                        text: "application/json"
                    },
                    success:function(response) {
                        if(response.code == 100){
                            if(response.message.username !== undefined){
                                jQuery('#register_modal .hidden-error-username').show();
                                jQuery('#register_modal .hidden-error-username strong').text(response.message.username[0]);
                            } else {
                                jQuery('#register_modal .hidden-error-username').hide();
                            }
                            if(response.message.email !== undefined){
                                jQuery('#register_modal .hidden-error-email').show();
                                jQuery('#register_modal .hidden-error-email strong').text(response.message.email[0]);
                            } else {
                                jQuery('#register_modal .hidden-error-email').hide();
                            }
                            if(response.message.password !== undefined){
                                jQuery('#register_modal .hidden-error-password').show();
                                jQuery('#register_modal .hidden-error-password strong').text(response.message.password[0]);
                            } else {
                                jQuery('#register_modal .hidden-error-password').hide();
                            }
                            if(response.message.c_password !== undefined){
                                jQuery('#register_modal .hidden-error-c_password').show();
                                jQuery('#register_modal .hidden-error-c_password strong').text(response.message.c_password[0]);
                            } else {
                                jQuery('#register_modal .hidden-error-c_password').hide();
                            }
                        } else {
                            /* jQuery('#register_modal').modal('hide');
                            jQuery('#register_success_modal').modal('show'); */
                            document.cookie = "register_succcess=1";
                            location.reload();
                        }
                    },
                    error:function(err) {
                        console.log(err);
                    },
                });
            });

            jQuery(document).ready(function(){
                let register_succcess = getCookie("register_succcess");
                if(register_succcess == 1){
                    jQuery('#register_success_modal').modal('show');
                }

                jQuery('#register_success_modal .close').click(function(){
                    document.cookie = "register_succcess=;"
                });
                
            });

            function getCookie(cname) {
                let name = cname + "=";
                let decodedCookie = decodeURIComponent(document.cookie);
                let ca = decodedCookie.split(';');
                for(let i = 0; i <ca.length; i++) {
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


            // CONTACT FORM
            jQuery('#contact_form_submit input[type="submit"]').click(function(submitEvent) {
                submitEvent.preventDefault();

                let contact_name_con_modal = jQuery('#contact_form_submit input[name="contact_name_con_modal"]').val();
                let contact_email_con_modal = jQuery('#contact_form_submit input[name="contact_email_con_modal"]').val();
                let contact_message_con_modal = jQuery('#contact_form_submit textarea[name="contact_message_con_modal"]').val();
                let captcha2 = jQuery('#contact_form_submit input[name="captcha2"]').val();
                let token = jQuery('#contact_form_submit input[name="_token"]').val();

                let action = jQuery('#contact_form_submit').attr('action');

                jQuery('.contact-form-captcha2').hide(0);

                $.ajax({
                    url:action,
                    type:'POST',
                    data:{
                        'contact_name_con_modal':contact_name_con_modal,
                        'contact_email_con_modal':contact_email_con_modal,
                        'contact_message_con_modal':contact_message_con_modal,
                        'captcha2':captcha2,
                        '_token':token,
                    },
                    accepts: {
                        text: "application/json"
                    },
                    success:function(response) {
                        jQuery('.contact-form-success').show(0);
                        jQuery('.contact-form-success strong').html("Your message has been successfully sent");

                        jQuery('#contact_form_submit *').each(function(index) {
                            $(this).attr('disabled', true);
                        });

                        $(window).scrollTop(0);
                    },
                    error:function(err) {
                        console.log(err);

                        if (typeof err.responseJSON.errors.captcha2 !== undefined) {
                            jQuery('.contact-form-captcha2').show(0);
                            jQuery('.contact-form-captcha2 strong').html("Invalid captcha code contact modal.");
                        }

                        refresh_captcha();
                    },
                });
            });



            // JOIN GALLERY FORM
            jQuery('#joinGalleryForm input[type="submit"]').click(function(submitEvent) {
                submitEvent.preventDefault();

                let contact_name = jQuery('#joinGalleryForm input[name="contact_name"]').val();
                let contact_email = jQuery('#joinGalleryForm input[name="contact_email"]').val();
                let mail_subject = jQuery('#joinGalleryForm input[name="mail_subject"]').val();
                let contact_message = jQuery('#joinGalleryForm textarea[name="contact_message"]').val();
                let captcha1 = jQuery('#joinGalleryForm input[name="captcha1"]').val();
                let token = jQuery('#joinGalleryForm input[name="_token"]').val();

                let action = jQuery('#joinGalleryForm').attr('action');

                jQuery('.contact-form-captcha1').hide(0);

                $.ajax({
                    url:action,
                    type:'POST',
                    data:{
                        'contact_name':contact_name,
                        'contact_email':contact_email,
                        'contact_message':contact_message,
                        'mail_subject':mail_subject,
                        'captcha1':captcha1,
                        '_token':token,
                    },
                    accepts: {
                        text: "application/json"
                    },
                    success:function(response) {
                        jQuery('.join-success').show(0);
                        jQuery('.join-success strong').html("Your message has been successfully sent");

                        jQuery('#joinGalleryForm *').each(function(index) {
                            $(this).attr('disabled', true);
                        });

                        $(window).scrollTop(0);
                    },
                    error:function(err) {
                        console.log(err);

                        if (typeof err.responseJSON.errors.captcha1 !== undefined) {
                            jQuery('.contact-form-captcha1').show(0);
                            jQuery('.contact-form-captcha1 strong').html("Invalid captcha code join gallery.");
                        }

                        refresh_captcha
                    },
                });
            });
        });

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
            if(jQuery('.help-block strong').text()){
                if(jQuery('.help-block strong').text() == 'The captcha2 field is required.'){
                    jQuery('.help-block strong').html('The captcha field is required.');
                    jQuery('#logo_modal').modal('show');
                    iccustomfun('logo_modal');
                } else if(jQuery('.help-block strong').text() == 'validation.captcha'){
                    jQuery('.help-block strong').html('Invalid captcha code.');
                    jQuery('#logo_modal').modal('show');
                    iccustomfun('logo_modal');
                }
            }
            if(jQuery('.help-block1 strong').text()){
                if(jQuery('.help-block1 strong').text() == 'The captcha1 field is required.'){
                    jQuery('.help-block1 strong').html('The captcha field is required.');
                    jQuery('#join_gallery').modal('show');
                    iccustomfun('join_gallery');
                } else if(jQuery('.help-block1 strong').text() == 'validation.captcha'){
                    jQuery('.help-block1 strong').html('Invalid captcha code.');
                    jQuery('#join_gallery').modal('show');
                    iccustomfun('join_gallery');
                }
            }
        });

        function iccustomfun (formid) {
            if(formid == 'join_gallery') {
                var customhtml = '<div class="captcha captcha_inner"><span>{!! captcha_img() !!}</span><button type="button" onclick="refresh_captcha()" class="btn btn-refresh"><svg fill="#000000" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 30 30" width="30px" height="30px"><path d="M 15 3 C 12.031398 3 9.3028202 4.0834384 7.2070312 5.875 A 1.0001 1.0001 0 1 0 8.5058594 7.3945312 C 10.25407 5.9000929 12.516602 5 15 5 C 20.19656 5 24.450989 8.9379267 24.951172 14 L 22 14 L 26 20 L 30 14 L 26.949219 14 C 26.437925 7.8516588 21.277839 3 15 3 z M 4 10 L 0 16 L 3.0507812 16 C 3.562075 22.148341 8.7221607 27 15 27 C 17.968602 27 20.69718 25.916562 22.792969 24.125 A 1.0001 1.0001 0 1 0 21.494141 22.605469 C 19.74593 24.099907 17.483398 25 15 25 C 9.80344 25 5.5490109 21.062074 5.0488281 16 L 8 16 L 4 10 z"/></svg></button></div> <input id="captcha1" type="text" class="captcha_field form-control @error("captcha1", "post") is-invalid @enderror " placeholder="ENTER CAPTCHA" name="captcha1">';
                jQuery('#ic_join_gallery').html(customhtml);
            }
            if(formid == 'logo_modal') {
                var customhtml = '<div class="captcha captcha_inner"><span>{!! captcha_img() !!}</span><button type="button" onclick="refresh_captcha()" class="btn btn-refresh"><svg fill="#000000" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 30 30" width="30px" height="30px"><path d="M 15 3 C 12.031398 3 9.3028202 4.0834384 7.2070312 5.875 A 1.0001 1.0001 0 1 0 8.5058594 7.3945312 C 10.25407 5.9000929 12.516602 5 15 5 C 20.19656 5 24.450989 8.9379267 24.951172 14 L 22 14 L 26 20 L 30 14 L 26.949219 14 C 26.437925 7.8516588 21.277839 3 15 3 z M 4 10 L 0 16 L 3.0507812 16 C 3.562075 22.148341 8.7221607 27 15 27 C 17.968602 27 20.69718 25.916562 22.792969 24.125 A 1.0001 1.0001 0 1 0 21.494141 22.605469 C 19.74593 24.099907 17.483398 25 15 25 C 9.80344 25 5.5490109 21.062074 5.0488281 16 L 8 16 L 4 10 z"/></svg></button></div><input id="captcha2" type="text" class="form-control captcha_field @error("captcha2", "post") is-invalid @enderror" placeholder="ENTER CAPTCHA" name="captcha2">';
                jQuery('#ic_logo_modal').html(customhtml);
            }
        }

        var getUrlParameter = function getUrlParameter(sParam) {
            var sPageURL = window.location.search.substring(1),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                }
            }
            return false;
        };

        jQuery(document).ready(function(){
            jQuery("#logo_modal").on("hidden.bs.modal", function () {
                jQuery('#ic_logo_modal').html('');
            });
            jQuery("#join_gallery").on("hidden.bs.modal", function () {
                jQuery('#ic_join_gallery').html('');
            });

            jQuery(".toggle-password").click(function() {
                jQuery(this).toggleClass("fa-eye fa-eye-slash");
                input = jQuery(this).parent().find("input");
                if (input.attr("type") == "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });
            
            var ver = getUrlParameter('ver');
            if(ver == 1){
                setTimeout(() => {
                    jQuery('#exampleModal').modal('show');
                }, 500);
            }
        });
    </script>
</header>
