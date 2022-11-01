<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>index_java</title>
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


  </style>
</head>

<body>



<canvas id="canvas"></canvas>

    <script>

    var screenWidth = screen.width;
    var screenHeight = screen.height;

    const canvas = document.getElementById("canvas");
    const ctx = canvas.getContext("2d");
    const img = new Image();
    img.src = "img/persefone2.jpg";

    img.onload = function () {
        canvas.height = screenHeight;
        canvas.width = screenWidth;

        const oc = document.createElement('canvas');
        const octx = oc.getContext('2d');

        ScreenProportion = canvas.height/canvas.width;
        ImageProportion = this.height/this.width;

        if (ScreenProportion > ImageProportion)
        {
        oc.width = canvas.width;
        oc.height = this.height/(this.width/canvas.width);
        MarginTop = (canvas.height-oc.height)/2;
        MarginLeft = 0;
        } else {
        oc.height = canvas.height;
        oc.width = this.width/(this.height/canvas.height);
        MarginTop = 0;
        MarginLeft = (canvas.width-oc.width)/2;
        }

        // step 2, resize to temporary size
        octx.drawImage(img, 0, 0, oc.width, oc.height);
        // step 3, resize to final size
        ctx.drawImage(oc, 0, 0, oc.width, oc.height, MarginLeft, MarginTop, oc.width, oc.height);

    }

    </script>


<!--

canvas 736x414, proportion = 1.7778

img/virgin.jpg: 714x531 = 556x414, proportion = 1.3446
img/deleted.jpg: 1236x1242 = , proportion = 0.9951
img/persefone.jpg: 3537x2464 = , proportion = 1.4354
img/persefone2.jpg: 3537x1293 = , proportion = 2.7354
img/madre.jpg: 545x720 = , proportion = 0.7569


alert(this.width + 'x' + this.height);

-->



</body>
</html>



















