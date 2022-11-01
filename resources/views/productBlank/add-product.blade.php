<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add product</title>
    {{-- <link rel="stylesheet" href="{{url('css/custom.css')}}">--}}
    <link rel="stylesheet" href={{asset('/assets/css/theme.css')}}>
    <link rel="stylesheet" href={{asset('/assets/css/custom.css')}}>


    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
    <!-- <div class="header-inner artheader" id="header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-3 col-sm-2 height-object-fit"></div>
                <div class="col-xs-6 col-sm-8 text-center height-object-fit">
                    <div id="header-center">
                        <a href="https://artfora.net/index" class="logo-dark">
                            <span class="logo-text text-logo">ARTfora SHOP</span>
                        </a>
                    </div>
                </div>
                <div class="col-xs-3 col-sm-2 text-right height-object-fit" id="header-right">
                    <div class="header-logo">
                        <a href="#">
                            <img src="https://artfora.net/assets/img/logo.png" class="img-fluid" alt="logo">
                        </a>
                    </div>
                </div>
            </div>-->

    <!--   <div class="row">
                <div class="col-lg-12 col-sm-12 col-xs-12">
                    <div class="addproduct-btn-container">
                        <span class="addproduct-btn">ADD PRODUCT</span>
                    </div>
                </div>
            </div> -->
    <!--   </div>
    </div>
 -->
    <div class="header-fixed">
        <header id="header" class="header-show-hide-on-scroll1 menu-align-right">
            <!-- Begin header inner -->
            <div class="header-inner">
                <div class="container-fluid header-padding">
                    <div class="row">
                        <div class="col-xs-3 col-sm-2 height-object-fit"></div>
                        <div class="col-xs-6 col-sm-8 text-center height-object-fit">
                            <div id="header-center">
                                <a href="https://artfora.net/index" class="logo-dark">
                                    <span class="logo-text">ARTfora SHOP</span>
                                </a>
                            </div>
                        </div>
                        <div class="col-xs-3 col-sm-2 text-right height-object-fit" id="header-right">
                            <div class="img_footer_div logo-position"><a href="#"><img src="https://artfora.net/assets/img/logo.png" alt="logo"></a></div>
                        </div>
                    </div>
                    <div class="row add-product-wrapper">
                        <div class="col-lg-12 col-sm-12 col-xs-12">
                            <div class="addproduct-btn-container">
                                <span class="addproduct-btn">ADD PRODUCT</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
    </div>

    <!-- form page top header -->
    <div class="container-fluid form-container">
        <div class="container form-top-margin">

            @if(session()->has('message'))
            <div class="alert alert-success" id="successMessage">
                {{ session()->get('message') }}
            </div>
            @endif
            <div class="row">
                <div class="col-lg-5 col-xs-5 input-posotion">
                    <form action="{{url('contactus_email')}}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group input-cmn">
                            <label for="artist-name">Your artist name:</label>
                            <input type="text" class="form-control" id="artist-name" required="" name="name">
                        </div>
                        <div class="form-group input-cmn">
                            <label for="description">Artist description(optional):</label>
                            <textarea class="form-control" id="description" rows="3 " name="description"></textarea>
                        </div>
                        <div class="form-group input-cmn">
                            <label for="work">Name of work:</label>
                            <input type="text" class="form-control" id="work" required="" name="NameOfWork">
                        </div>
                        <div class="form-group input-cmn">
                            <label for="media">Media (oil on canvas, acrylic on canvas etc) :</label>
                            <input type="text" class="form-control" id="media" required="" name="media">
                        </div>
                        <!-- product size -->
                        <div class="form-group input-cmn ">
                            <label for="product-size">Product size in cm (Height /Width /Depth):</label>
                            <div class="product-size-box">
                                <input type="number" class="form-control" id="product-size" required="" name="height" min="1">
                                <input type="number" class="form-control" id="product-size" required="" name="width" min="1">
                                <input type="number" class="form-control" id="product-size" required="" name="depth" min="1">
                            </div>

                        </div>
                        <!-- currency -->
                        <div class="form-group input-cmn">
                            <label for="currency">Currency:</label>
                            <select id="currency" class="form-control" required="" name="currency">
                                <option selected="EUR">EUR</option>
                                <option>USD</option>
                                <option>CNY</option>
                                <option>INR</option>
                                <option>JPY</option>
                            </select>
                        </div>
                        <!-- price -->
                        <div class="form-group input-cmn">
                            <label for="price">Price:</label>
                            <input type="number" class="form-control" id="price" required="" name="price" min="0">
                        </div>
                        <!-- email -->
                        <div class="form-group input-cmn">
                            <label for="email-address">Contact email address:</label>
                            <input type="email" class="form-control" id="email-address" required="" name="email">
                        </div>
                        <!-- website address -->
                        <div class="form-group input-cmn">
                            <label for="website-address">Website address (optional) :</label>
                            <input type="text" class="form-control" id="website-address" value="https://" name="websuteAdress">
                        </div>

                        <!--file drop  -->
                        <div class="form-group input-cmn dropfile-conatiner">
                            <div class="drop-header">
                                <p>At least 1 image(minimum 2500px):</p>
                            </div>

                            <div class="field " align="left">
                                <div class="input-inner-title">
                                    <p>Drage and drop your files hear or click to browse</p>
                                </div>

                                <input type="file" id="files" multiple class="changed-dropfile" required="" title=" " name="mulimages" />

                            </div>
                        </div>
                        <button type="submit" class="submit-btn">SUBMIT YOUR WORK</button>
                        {{-- <input type="submit" class="submit-btn" value="SUBMIT YOUR WORK">--}}
                    </form>
                </div>
            </div>

        </div>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script>
        /* dropfile solution*/
        $(document).ready(function() {
            if (window.File && window.FileList && window.FileReader) {
                $("#files").on("change", function(e) {
                    var files = e.target.files,
                        filesLength = files.length;
                    for (var i = 0; i < filesLength; i++) {
                        var f = files[i]
                        var fileReader = new FileReader();
                        fileReader.onload = (function(e) {
                            var file = e.target;
                            $("<span class=\"pip\">" +
                                "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
                                "<br/><span class=\"remove\">Remove image</span>" +
                                "</span>").insertAfter("#files");
                            $(".remove").click(function() {
                                $(this).parent(".pip").remove();
                            });

                        });
                        fileReader.readAsDataURL(f);
                    }
                    console.log(files);
                });
            } else {
                alert("Your browser doesn't support to File API")
            }
        });

        // $(document).ready(function() {
        //     setTimeout(function() {
        //         $('#successMessage').fadeOut('fast');
        //     }, 2000);
        // });
    </script>



</body>

</html>