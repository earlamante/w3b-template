<?php
	class Data_Manager {
		var $data_path;
		
		public function __construct() {
			// List of functions to prepare the required data
			$this->_set_config();
		}
	}
?>