<?php
/**
 * This is the error 403 "Permission Denied" with the W3B Template skin
 *
 * @since Version 1.3
 */
	$url = str_replace('403.php', '', $_SERVER['PHP_SELF']);
?>
<html>
	<head>
		<title>W3B Template Error 403</title>
		<link href="<?php echo $url; ?>/css/style.css" rel="stylesheet" />
	</head>
	<body>
		<div>
			<h1>Error 403!!</h1>
			
			<p>You have no access to view the current file that you are trying to open.</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>Presented by: <a href="http://w3bkit.com"><img src="<?php echo $url; ?>img/w3bkit.svg" alt="logo" /></a></p>
		</div>
	</body>
</html>