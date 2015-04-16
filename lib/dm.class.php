<?php
	include('w3bencrypt.class.php');
	
	class Data_Manager extends W3B_Code {
		var $data_path;
		var $data_file;
		var $in_admin;
		private $data;
		
		private function _create_file() {
			fopen($this->data_path.$this->data_file, 'w');
			return FALSE;
		}
		
		public function _write_data() {
			if(!empty($data=$this->data)) {
				if( !$this->in_admin )
					unset($this->data['site_name']);
				unset($this->data['msg']);
				$file = fopen($this->data_path.$this->data_file, 'w');
				fwrite($file, $this->encrypt(json_encode($this->data)));
			}
		}
		
		private function _open_data($data_file) {
			$file = fopen($data_file, 'r+');
			if($file_size = filesize($data_file))
				return $this->encrypt(fread($file,$file_size));
			else
				return FALSE;
		}
		
		public function init_data($data_file=FALSE, $admin=FALSE, $return=FALSE) {
			if(!$data_file)
				$data_file = $this->data_path.$this->data_file;
			if( file_exists($data_file) ) {
				$content = $this->_open_data($data_file);
				
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
			}
			else {
				$this->_create_file();
				return FALSE;
			}
		}
		
		public function set_data($data, $key=FALSE) {
			if(!$key)
				return FALSE;
			elseif($key === TRUE)
				$this->data = $data;
			else
				$this->data[str_replace('field_', '', $key)] = $data;

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