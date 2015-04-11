<?php
	// Default password 'ilovew3bkit'
	$config = array(
		'username'	=> 'admin',
		'password'	=> 'e3a37cdaa64efb8bd363912bde159295e2ea3847813dc660e6cd0cded0a47beb'
	);
	
	if( !file_exists('../data/admin_data.png') ) {
		$file = fopen('../data/admin_data.png', 'w');
		fwrite($file, json_encode($config));
	}
	else {
		$file = fopen('../data/admin_data.png', 'r+');
		$content = fread($file,filesize('../data/admin_data.png'));
	}
	
	print_r(json_decode($content,TRUE));
?>