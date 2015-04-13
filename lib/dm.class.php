<?php
	class Data_Manager {
		var $data_path;
		var $data_file;
		var $in_admin;
		private $data;
		
		private function _encode($data, $encode=TRUE) {
			$split = $encode? ((strlen($data)%2) + strlen($data))/2:(strlen($data)-(strlen($data)%2))/2;
			$group = str_split($data, $split);
			$return = array(
				$group[1] . (empty($group[2])? '':$group[2]),
				$group[0]
			);
			
			for($y=0; $y<sizeof($return) ;$y++) {
				$data = $return[$y];
				$split = $encode? ((strlen($data)%2) + strlen($data))/2:(strlen($data)-(strlen($data)%2))/2;
				$group = str_split($data, $split);
				
				$return[$y] = array(
					$encode? $group[1] . (empty($group[2])? '':$group[2]):strrev($group[1] . (empty($group[2])? '':$group[2])),
					$encode? strrev($group[0]):$group[0]
				);
				$return[$y] = implode('', $return[$y]);
			}
			
			return implode('', $return);
		}
		
		public function _write_data() {
			if(!empty($data=$this->data)) {
				if( !$this->in_admin )
					unset($this->data['site_name']);
				
				$file = fopen($this->data_path.$this->data_file, 'w');
				fwrite($file, $this->_encode(json_encode($this->data)));
			}
		}
		
		public function init_data($data_file=FALSE, $admin=FALSE, $return=FALSE) {
			if(!$data_file)
				$data_file = $this->data_path.$this->data_file;
			if( file_exists($data_file) ) {
				$file = fopen($data_file, 'r+');
				$content = $this->_encode(fread($file,filesize($data_file)),FALSE);
				
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