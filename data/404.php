<?php
/**
 * This is the error 404 "Page Not Found" with the W3B Template skin
 *
 * @since Version 1.3
 */
	if(empty($url))
		$url = explode('index.php', $_SERVER['PHP_SELF'])[0].'data/';
?>
<html>
	<head>
		<title>W3B Template Error 404</title>
		<link href="<?php echo $url; ?>css/style.css" rel="stylesheet" />
	</head>
	<body>
		<div>
			<h1>Error 404!!</h1>
			
			<p>The file that you are trying to access is not found.</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>Presented by: <a href="http://w3bkit.com"><img src="<?php echo $url; ?>img/w3bkit.svg" alt="logo" /></a></p>
		</div>
	</body>
</html>