$(document).ready(function(){

    var boxWidth = 246;
		$("#close").click(function(){
			$("#div-hide").animate({
				width: "0"
			});
			$("#close").hide();
            $("#open").show();
		});
		
		$("#open").click(function(){
			$("#div-hide").animate({
				width: boxWidth
			});
			$("#open").hide();
            $("#close").show();
		});
    
//   $("#close").click(function(){
//     $("#div-hide").toggle(300);
//     $("#close").hide();
//     $("#open").show();
//   });
//   $("#open").click(function(){
//     $("#div-hide").toggle(300);
//     $("#open").hide();
//     $("#close").show();
//   });
});



// counter  
var incrementPlus;
var incrementMinus;

var buttonPlus  = $(".btn_plus");
var buttonMinus = $(".btn_mines");

var incrementPlus = buttonPlus.click(function() {
	var $n = $(this)
		.parent(".button-container")
		.parent(".div")
		.find(".input-count");
	$n.val(Number($n.val())+1 );
});

var incrementMinus = buttonMinus.click(function() {
		var $n = $(this)
		.parent(".button-container")
		.parent(".div")
		.find(".input-count");
	var amount = Number($n.val());
	if (amount > 1) {
		$n.val(amount-1);
	}
});



// text hide/show  

$(document).ready(function() {
    // Configure/customize these variables.
    var showChar = 130;  // How many characters are shown by default
    var ellipsestext = "...";
    var moretext = "Read More";
    var lesstext = "Read Less";
    

    $('.more').each(function() {
        var content = $(this).html();
 
        if(content.length > showChar) {
 
            var c = content.substr(0, showChar);
            var h = content.substr(showChar, content.length - showChar);
 
            var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';
 
            $(this).html(html);
        }
 
    });
 
    $(".morelink").click(function(){
        if($(this).hasClass("less")) {
            $(this).removeClass("less");
            $(this).html(moretext);
        } else {
            $(this).addClass("less");
            $(this).html(lesstext);
        }
        $(this).parent().prev().toggle();
        $(this).prev().toggle();
        return false;
    });
});

function helplabel(labeltype,cname,cvalue) {
  const d = new Date();
  d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
  let expires = "expires="+d.toUTCString();
    if (labeltype=='contact') {
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        document.getElementById('contactlabel').style.display="none";
    }
    if (labeltype=='filter') {
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        document.getElementById('filterlabel').style.display="none";
    }
    if (labeltype=='title') {
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        jQuery('.titlelabel').hide();
        jQuery('.titlelabel1').hide();
        // document.getElementById('titlelabel').style.display="none";
    }
    if (labeltype=='info') {
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        document.getElementById('infolabel').style.display="none";
    }

    
}


jQuery(document).ready(function(){
    jQuery('.like_icon').click(function(){
        var action = jQuery('#like_ajax_action').val();

        var shop_product_id = $(this).attr("data-shop_product_id");
        var product_user_id = $(this).attr("data-product_user_id");

        var my_class = '.ctm_product_'+shop_product_id;

        $.ajax({
            url:action,
            type:'POST',
            data:{
                'shop_product_id' : shop_product_id,
                'product_user_id' : product_user_id
            },
            accepts: {
                text: "application/json"
            },
            success:function(response) {
                console.log(response);
                if(response == 1){
                    // insert
                    jQuery(my_class+' .like_icon').html('<svg width="100%" height="100%" viewBox="0 0 4267 4267" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g id="Liked"><path d="M2124.98,3892.68l-80.679,-161.434c-125.159,-250.316 -440.15,-460.3 -773.675,-682.633c-517.43,-344.921 -1103.96,-735.946 -1103.96,-1395.62c0,-788.95 425.063,-1279.02 1109.19,-1279.02c485.795,-0 734.208,238.791 849.129,407.754c115,-168.963 363.329,-407.754 849.121,-407.754c684.17,-0 1109.23,490.066 1109.23,1279.02c-0,659.671 -586.534,1050.7 -1104,1395.62c-333.48,222.288 -648.471,432.354 -773.634,682.633l-80.712,161.434Z" style="fill:#f00;fill-rule:nonzero;"/></g></svg>');
                } else if(response == 2) {
                    // delete
                    jQuery(my_class+' .like_icon').html('<svg width="100%" height="100%" viewBox="0 0 4267 4267" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g id="Like"><path d="M2133.32,3838.41l-78.191,-156.453c-121.297,-242.594 -426.57,-446.099 -749.806,-661.573c-501.466,-334.28 -1069.9,-713.242 -1069.9,-1352.56c0,-764.61 411.949,-1239.56 1074.97,-1239.56c470.808,-0 711.557,231.424 822.933,395.174c111.452,-163.75 352.12,-395.174 822.924,-395.174c663.064,-0 1075.01,474.947 1075.01,1239.56c0,639.319 -568.438,1018.28 -1069.94,1352.56c-323.191,215.429 -628.465,419.015 -749.766,661.573l-78.222,156.453Z" style="fill:#393939;fill-rule:nonzero;"/><path d="M1284.19,554.488c-581.479,-0 -928.642,410.658 -928.642,1098.51c0,563.071 520.375,909.988 1023.58,1245.46c299.321,199.571 584.63,389.741 754.192,620.941c169.596,-231.2 454.867,-421.45 754.188,-620.941c503.25,-335.471 1023.66,-682.388 1023.66,-1245.46c0,-687.854 -347.246,-1098.51 -928.729,-1098.51c-607.217,-0 -761.979,443.283 -763.471,447.717l-85.65,256.87l-85.612,-256.87c-6.175,-17.825 -161.475,-447.717 -763.517,-447.717m849.129,3338.2l-80.679,-161.434c-125.158,-250.316 -440.15,-460.3 -773.675,-682.633c-517.429,-344.921 -1103.96,-735.946 -1103.96,-1395.62c0,-788.95 425.063,-1279.02 1109.19,-1279.02c485.796,-0 734.208,238.791 849.129,407.754c115,-168.963 363.329,-407.754 849.121,-407.754c684.171,-0 1109.23,490.066 1109.23,1279.02c0,659.671 -586.533,1050.7 -1104,1395.62c-333.479,222.288 -648.471,432.354 -773.633,682.633l-80.713,161.434Z" style="fill:#c8c8c8;fill-rule:nonzero;"/></g></svg>');
                } else {
                    jQuery('#exampleModal').modal('show');
                }
            },
            error:function(err) {
                console.log(err);
            },
        });
    });

    jQuery('.register_click_button').click(function(){
        jQuery("#exampleModal").modal('hide');
    });
});