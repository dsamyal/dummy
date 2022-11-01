<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
</head>
<body>
	<p><?php echo @$data['sender_copy']; ?></p>
	<p>Name: <?php echo $data['name']; ?></p>
	<p>Email: <?php echo $data['email']; ?></p>
	<p>URL: <?php echo @$data['url']; ?></p>
	<p>Message from website: <br><?php echo $data['message']; ?></p>
</body>
</html>