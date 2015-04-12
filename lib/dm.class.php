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
		
		public function init_data($data_file=FALSE, $admin=FALSE, $return=FALSE) {
			if(!$data_file)
				$data_file = $this->data_path.$this->data_file;
			if( file_exists($data_file) ) {
				$file = fopen($data_file, 'r+');
				$content = fread($file,filesize($data_file));
				
				// Set default site data
				if(!$admin) {
					if(!$return) {
						$this->data = json_decode($content,TRUE);
						$this->data['site_name'] = $this->site['site_name'];
					}
					else
						return json_decode($content,TRUE);
				}
				else
					$this->site = json_decode($content,TRUE);
				
				fclose($file);
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