<form>
	<label for="page">Edit Page Data:</label>
	<select id="page" name="page">
		<?php
			echo '<option value="'.site_url().'admin" '.(($this->_get_target()=='')? 'selected="selected"':'').'>Site Settings</option>';
			foreach($page_list as $page_name) {
				$page_name = $page_name==$this->config->default_filename?'homepage':$page_name;
				$selected = ($this->_get_target()==$page_name)? 'selected="selected"':'';
				echo '<option value="'.site_url().'admin/'.$page_name.'" '.$selected.'>'.clean_text($page_name,TRUE).'</option>';
			}
		?>
	<input type="button" id="w3b_edit_page" value="Edit Page" />
	<input type="button" id="w3b_logout" value="Logout" onclick="javascript:window.location.href='<?php echo site_url(); ?>admin/logout'" />
	</select>
</form>
<script type="text/javascript">
	$(document).ready(function($) {
		$('#w3b_edit_page').on('click', function() {
			window.location.href=$('#page').val();
		});
	});
</script>