<html>
	<head>
		<title>{{site_name}}</title>
		<script src="<?php echo site_url(); ?>data/js/jquery-1.8.2.min.js" type="text/javascript"></script>
		<link href="<?php echo site_url(); ?>data/css/style.css" rel="stylesheet" />
	</head>
	<body class="admin">
		<div>
			<h1><a href="<?php echo site_url(); ?>">{{site_name}} Administration</a></h1>
			
			{{page_list}}
			
			{{msg}}
			
			{{form_content}}
			
			<p>Presented by: <a href="http://w3bkit.com"><img src="<?php echo site_url(); ?>data/img/w3bkit.svg" alt="logo" /></a></p>
		</div>
		
	</body>
</html>