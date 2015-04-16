<h2><?php echo $form_title; ?></h2>

<form action="<?php echo $action; ?>" method="post">

<div class="form--body">
	<?php
		$content = '';
		foreach($inputs as $name => $type) {
			if( $form_title === 'Login' )
				$value = empty($_REQUEST[$name])? '':$_REQUEST[$name];
			else
				$value = $data=$this->get_data($name);
				
			$content .= $this->_apply_templating(
							$this->get_template('helpers/input_'.$type.'.tpl.php'),
							array(
								'field_name'	=> $name,
								'value'			=> $value,
								'label'			=> clean_text($name, TRUE)
							)
			);
		}
		echo $content;
	?>
</div>

<div class="form--footer">
	<input type="hidden" name="target" value="<?php echo empty($target)? '':$target; ?>" />
	<input type="submit" class="form--footer-submit" value="<?php echo empty($submit)? 'Submit':clean_text($submit); ?>" />
</div>

</form>