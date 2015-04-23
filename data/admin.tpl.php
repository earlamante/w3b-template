<?php
/**
 * Template Name: Admin Pages
 *
 * This is the template for the W3b Template admin pages.
 *
 * @since Version 1.3
 */
?>
<html>
	<head>
		<title>{{site_name}}</title>
		<script src="<?php echo site_url(); ?>data/js/jquery-1.8.2.min.js" type="text/javascript"></script>
		<script src="<?php echo site_url(); ?>data/js/tinymce/tinymce.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			tinymce.init({
				selector: ".wysiwyg",
				menubar: false,
				plugins: [
					"advlist autolink lists link image charmap print preview anchor",
					"searchreplace visualblocks code",
					"insertdatetime media table contextmenu paste"
				],
				toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
			});
		</script>
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