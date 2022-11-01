<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ARTfora</title>
        <link rel="stylesheet" href="css/screen.css">
        <link rel="stylesheet" href="/assets/vendor/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="/assets/css/theme.css">
        <link rel="stylesheet" href="/assets/css/custom.css">		
    </head>
    <body height="100%" class="cm-static-page">
        <header id="header" class="header-show-hide-on-scroll1 menu-align-right">
        	<!-- Begin header inner -->
        	<div class="header-inner">
        	    <div class="container-fluid">
            		<div class="row">
                    <div class="col-xs-3 col-sm-4"></div>
            			<div class="col-xs-6 col-sm-4 text-center">
            			    <div id="header-center" class="">
            					<a href="https://artfora.net/index" class="logo-dark">
            						<span class="logo-text">WELCOME TO ARTfora</span>
            					</a>
            				</div>
            			</div>
            			<div class="col-xs-3 col-sm-4 text-right" id="header-right"><div class="img_footer_div"><a href="#"><img src="https://artfora.net/assets/img/logo.png" alt="logo"></a></div></div>
            		</div>
        		</div>
        	</div>
        </header>
        <div class="container">
            <div class="resetpassword_main">
                <div class="inner_password">
                    <div class="card">
                        <div class="card-header" style="padding: 10px 0;">{{ __('Reset Password') }}</div>

                        <div class="card-body">
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <div class="card-body">
                                <form method="POST" action="{{ route('password.update') }}">
                                    @csrf

                                    <input type="hidden" name="token" value="{{ $token }}">

                                    <div class="form-group" style="position:relative">
                                        <!-- <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label> -->
                                        <span class="form_icon"><img class="btn-icon" style="margin-left: 3px;" width="23" height="23" src="/images/icon_envelope.svg" alt="Name"></span>
                                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus placeholder="{{ __('E-Mail Address') }}">

                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror

                                    </div>

                                    <div class="form-group" style="position:relative">
                                        <!-- <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label> -->

                                        <span class="form_icon"><img class="btn-icon" style="margin-left: 3px;" width="23" height="23" src="/images/icon_key.svg" alt="Name"></span>
                                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="{{ __('Password') }}">

                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        
                                    </div>

                                    <div class="form-group" style="position:relative">
                                        <!-- <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label> -->

                                        <span class="form_icon"><img class="btn-icon" style="margin-left: 3px;" width="23" height="23" src="/images/icon_key.svg" alt="Name"></span>
                                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('Confirm Password') }}">
                                        
                                    </div>

                                    <div class="form-group mb-0">
                                        <div class="">
                                            <button type="submit" class="button_l">
                                                {{ __('Reset Password') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="for-mobile cm-footer">
            <div valign="middle" align="center" style="font-size:14px; font-family:'IBM','Lucida Sans Unicode',Lucida Grande,sans-serif; color:#888888; line-height: 16px" class="rnb-text-center">
                <div>
                    <div style="font-family: 'prozak-bold';font-size: 32px; line-height: 32px;margin: 0 0 10px;letter-spacing: 3px;">ARTfora</div>
                    <div style=";margin: 0 0 4px;">
                        <a href="mailto:contact@artfora.net?subject=Message%20from%20newsletter" style="text-decoration: underline; color: rgb(102, 102, 102)">contact@artfora.net</a>
                    </div>
                    <div>
                        <a href="https://dev.artfora.net" target="_blank" style="text-decoration: underline; color: rgb(102, 102, 102);">www.artfora.net</a>
                    </div>
                </div>
            </div>
            <div cellpadding="0" border="0" cellspacing="0" class="rnb-social-align" >
                <a target="_blank" href="https://www.facebook.com/Artfora-2316175905262438/">
                    <img alt="Facebook" border="0" hspace="0" vspace="0" style="vertical-align:top;" target="_blank" src="http://dev.artfora.net/Email/images/rnb_ico_fb.png">
                </a>
                <a target="_blank" href="http://www.instagram.com/artfora_net">
                    <img alt="Instagram" border="0" hspace="0" vspace="0" style="vertical-align:top;" target="_blank" src="http://dev.artfora.net/Email/images/rnb_ico_ig.png">
                </a>
            </div>
            <div class="cm-copyright">© 2021 ARTfora</div>
            <table width="100%" cellpadding="0" border="0" bgcolor="#f9fafc" align="center" cellspacing="0" style="background-color: rgb(57, 57, 57);border-top: 1px solid #191919;">
                <tbody>
                   <tr>
                      <td height="10" style="font-size:1px; line-height:1px; mso-hide: all;">&nbsp;</td>
                   </tr>
                   <tr>
                      <td align="center" height="20" style="font-family:Arial,Helvetica,sans-serif; color:#666666;font-size:13px;font-weight:normal;text-align: center;">
                         <span style="color: rgb(102, 102, 102); text-decoration: underline;">
                         <a target="_blank" href="https://artfora.net/" style="text-decoration: underline; color: rgb(102, 102, 102);">View in browser</a></span>
                      </td>
                   </tr>
                   <tr>
                      <td height="10" style="font-size:1px; line-height:1px; mso-hide: all;">&nbsp;</td>
                   </tr>
                </tbody>
            </table>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <style>
            body.cm-static-page{overflow-y:hidden;}
            .text-content{text-align:center;padding-top:50px;}
            .text-content h2{font-weight:bold;font-family:'Prozak-Light';color:#C8C8C8;line-height:1.5;text-transform:uppercase;letter-spacing:5px;margin-bottom:15px;}
            .text-content p{font-family:'IBM-regular';}
            .for-mobile{display:none;}
            body .filter-gallery-toggle-active{background-color:#B0B0B0;border-color:#B0B0B0;}
            .cm-footer{text-align:center;}
            .cm-copyright{font-size:18px;color:#888888;font-weight:normal;text-align:center;font-family:'prozak-bold';letter-spacing:2px;margin-top:50px;margin-bottom:20px;}
            .rnb-social-align{margin-top:10px;}
            .cm-footer{text-align:center;border-top:10px solid #191919;padding-top:25px;margin-top:15px;}
            span.cm-s-text{display:block;margin-top:13px;}
            .homepage-filter-bar.for-desktop .prozak-light-family{border-radius:7px;padding:9px 27px 9px 27px;cursor:pointer;margin-right:15px;position:relative;top:0px;line-height:30px;font-size:23px;display:inline-block;outline:none!important;letter-spacing:3px;color:#393939;font-family:'prozak-bold',sans-serif;border-color:#B0B0B0;background-color:#b0b0b0;}
            @media(max-width:767px){
                .for-mobile{display:block;}
                .for-desktop{display:none;}
                #header{height:100px;}
                .btn-wrap.for-mobile{padding-top:10px;padding-bottom:30px;text-align:center;}
                /* .header-inner .row > div{width:100%;} */
                #header-right .img_footer_div{margin:0 auto;width:100%;height:100%;max-width:34px;}
                .header-inner .row{display: flex; /* padding-top: 25px; */ flex-flow: revert; justify-content: end; align-items: center;}
                #header{height:100%;box-shadow:none;}
                #header-center .logo-text{font-size:35px;line-height:2;top:0;padding-top:15px;display:block;}
                body.cm-static-page{overflow-y:auto;}
                .cm-des-img{margin-bottom:65px;}
                .text-content{text-align:left;padding-top:20px;}
                .text-content h2{margin-bottom:25px;font-size:26px;}
                .btn-wrap.for-mobile button{font-size:20px;font-family:'prozak-bold',sans-serif;text-align:center;color:#494949;font-weight:normal;background-color:#E5E4E9;border-radius:10px;min-height:60px;display:inline-block;box-sizing:border-box;padding:6px 46px;text-transform:uppercase;letter-spacing:2px;box-shadow:inset 0px -3px 4px rgb(0 0 0 / 40%);border:none;max-width:394px;margin:0 auto 10px;}
                .for-mobile.cm-des-img{text-align:center;}
            }

            button.button_l {
                    display: block;
                    width: 100%;
                    border-radius: 7px;
                    padding-top: 7px;
                    padding-bottom: 7px;
                    color: var(--main-bg-color);
                    text-transform: uppercase;
                    margin-bottom: 10px;
                    border: 1px solid var(--main-border-color);
                    font-weight: bold;
                    transition: all 0.3s;
                    font-size: 18px;
                    text-decoration: none;
                    font-family: 'prozak-bold', sans-serif;
                    text-align: center;
                    color: #494949;
                    font-weight: normal;
                    background-color: #E5E4E9;
                    border-radius: 10px;
                    height: 50px;
                    display: inline-block;
                    box-sizing: border-box;
                    padding: 6px 46px;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    box-shadow: inset 0px -3px 4px rgb(0 0 0 / 40%);
                }
                .resetpassword_main{display: flex; justify-content: center;padding-top:40px}
                .resetpassword_main .inner_password{width: 100%; max-width: 480px;}
                .resetpassword_main .form-control {
                        padding-left: 40px;
                        color: #eaeaed;
                    }
                    .resetpassword_main .form-control {
    background: transparent;
    border: 1px solid #eaeaed;
    border-radius: 8px;
}
.form_icon {
    position: absolute;
    left: 8px;
    top: 11px;
}
.form-group #password-confirm::placeholder, .form-group #password::placeholder, .form-group #email::placeholder{
    text-transform: uppercase !important;
}
        </style>
    </body>
</html>