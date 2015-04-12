<?php
	class Data_Manager {
		var $data_path;
		var $data_file;
		private $data;
		
		public function _write_data() {
			if(!empty($this->data)) {
				$file = fopen($this->data_path.$this->data_file, 'w');
				fwrite($file, json_encode($this->data));
			}
		}
		
		public function init_data() {
			if( file_exists($this->data_path.$this->data_file) ) {
				$file = fopen($this->data_path.$this->data_file, 'r+');
				$content = fread($file,filesize($this->data_path.$this->data_file));
				$this->data = json_decode($content,TRUE);
			}
		}
		
		public function set_data($data, $key=FALSE) {
			if(!$key)
				return FALSE;
			elseif($key === TRUE)
				$this->data = $data;
			else
				$this->data[$key] = $data;

			return TRUE;
		}
		
		public function get_data($key=FALSE) {
			if($key) {
				return empty($this->data[$key])? FALSE:$this->data[$key];
			}
			else
				return empty($this->data)? FALSE:$this->data;
		}
	}
?>