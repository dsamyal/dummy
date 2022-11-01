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



  </style>
</head>

<body>


<script>



document.addEventListener('click', function handleClickOutsideBox(event) {
  const box = document.getElementById('box');

  if (!box.contains(event.target)) {
    box.style.display = 'none';
  }
});


  function showDiv() {
   document.getElementById('box').style.display = "block";
}


</script>
<div id="box" style="height: 150px; width: 150px; background-color: red; text-align: center;">Menu</div>


<div id="box"  style="display:none;" class="answer_list" > WELCOME</div>
<input type="button" name="answer" value="Show Div" onclick="showDiv()" />


<script>





document.addEventListener('click', function handleClickOutsideBox(event) {
  const box = document.getElementById('box');

  if (!box.contains(event.target)) {
    box.style.display = 'none';
  }
});
</script>
</body>
</html>



















