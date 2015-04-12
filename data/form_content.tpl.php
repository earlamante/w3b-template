<h2><?php echo $form_title; ?></h2>

<form action="<?php echo $action; ?>" method="post">

<?php
	$content = '';
	foreach($inputs as $name => $type) {
		if( $form_title === 'Login' )
			$value = empty($_REQUEST[$name])? '':$_REQUEST[$name];
		elseif( $form_title === 'Edit Site Settings' )
			$value = empty($this->site[$name])? '':$this->site[$name];
		else
			$value = empty($data[$name])? '':$data[$name];
			
		$content .= $this->_apply_templating(
						$this->get_template('data/input_'.$type.'.tpl.php'),
						array(
							'field_name'	=> $name,
							'value'			=> $value,
							'label'			=> clean_text($name, TRUE)
						)
		);
	}
	echo $content;
?>

</form>