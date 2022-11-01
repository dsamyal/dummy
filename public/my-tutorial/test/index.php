<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Test page</title>
  <meta name="description" content="A simple HTML5 Template for new projects.">
  <meta name="author" content="SitePoint">

  <meta property="og:title" content="A Basic HTML5 Template">
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://www.sitepoint.com/a-basic-html5-template/">
  <meta property="og:description" content="A simple HTML5 Template for new projects.">
  <meta property="og:image" content="image.png">

  <link rel="icon" href="/favicon.ico">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">

  <link rel="stylesheet" href="css/styles.css?v=1.0">


  <style type="text/css">
    
* {
    padding: 0px;
    margin:  0px;
  }




window.addEventListener('mousedown', function(event) {
  var dropd = document.getElementById('dropdown');
  var img = document.getElementById('menu');

  if (event.target === img && dropd.style.display === "none") {
    dropd.style.display = "block";
  } else if (event.target != dropd && event.target.parentNode != dropd) {
    dropd.style.display = 'none';
  }
};



  </style>
</head>

<body>
<h2>Sizes:</h2>

<p id="screen"></p>



<script>
   var screenWidth = screen.width;
   var screenHeight = screen.height;

</script>



<!-- iPhone 8 Plus example -->
<?php 


$ImagePath = "img/deleted.jpg";
list($width, $height) = getimagesize($ImagePath);
echo "<br>Image height: ".$height;
echo "<br>Image width: ".$width;

$xxxx = "<script>document.write(screenHeight);</script>";

echo "<br><br>Javascript<br>Screen width:"; echo "<script>document.write(screenWidth);</script>px";
echo "<br>Screen height:"; echo '<script>document.write(screenHeight);</script>px';

/*
$ScreenHeight = '<script>document.write(screenHeight);</script>';
$ScreenWidth = '<script>document.write(screenWidth);</script>';
*/

$ScreenHeight = '736';
$ScreenWidth = '414';

$ScreenRatio = $ScreenHeight/$ScreenWidth; // 1.7777778
$ImageRatio = $height/$width; // 1.4354 and 2.7354

if ($ScreenRatio > $ImageRatio) { 
  $NewImageWidth = round($ScreenWidth, 10); 
  $NewImageHeight = round($height/($width/$ScreenWidth), 10); 
  $NewImageHeight = round($height/($width/$ScreenWidth), 10); 
  echo "<br>Width safe"; 
} else { 
  $NewImageHeight = $ScreenHeight; 
  $NewImageWidth = $width/($height/$ScreenHeight); 
  echo "<br>Height safe"; 
}



echo "<br><br>
Screen height: "; echo '<script>document.write(screenHeight);</script>';
echo "<br>Screen ratio: " . $ScreenRatio . "<br>
Image ratio: " . $ImageRatio . "<br>
New width: " . $NewImageWidth . "<br>
New height: " . $NewImageHeight . "<br><br>"; 

?>

<img id="menu" alt="img" />
<ul id="dropdown" class="dropdown-breadcrumb" style="width: 40px;">
  <li>First</li>
  <li>Second</li>
</ul>


<div style="display: table-cell; border: 1px solid #000; height:736px; width:414px; text-align: center; vertical-align: middle;"><img src="<?php echo $ImagePath; ?>" height="<?php echo $NewImageHeight; ?>" width="<?php echo $NewImageWidth; ?>"></div>


<script>
   var screenWidth = screen.width;
   var screenHeight = screen.height;


</script>


</body>
</html>



















