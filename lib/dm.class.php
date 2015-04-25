<?php
/**
 * This is the class that serves as the middle agent between the user 
 * and the data stored in the encrypted data files
 * 
 * @package w3b-template
 * @subpackage data-manager
 * @since Version 1.3
 */
	/**
	 * Includes the file for the w3b-encryption subpackage
	 */
	include('w3bencrypt.class.php');
	
	class Data_Manager extends W3B_Code {
		var $data_path;		// Path of the directory that stores the encrypted data files
		var $data_file;		// Filename of the encrypted data file that will be used by the page
		var $in_admin;		// Boolean to determine if you're inside the the admin pages or not
		private $data;		// Private variable that stores the data from the encrypted data file of the page
		
		/**
		 * Loads the parent construct on load
		 * 
		 * @param String $data key, to be generated if blank
		 * 
		 * @since Version 1.3
		 */
		public function __construct($key=FALSE) {
			parent::__construct($key);
		}
		
		/**
		 * Create the encrypted data file
		 *
		 * @since Version 1.3
		 */
		private function _create_file() {
			fopen($this->data_path.$this->data_file, 'w');
		}
		
		/**
		 * Open the encrypted data file
		 *
		 * @param String $data_file file location of the encrypted data file
		 * @return Integer
		 * 
		 * @since Version 1.3
		 */
		private function _open_data($data_file) {
			$file = fopen($data_file, 'r+');
			if($file_size = filesize($data_file))
				return $this->encrypt(fread($file,$file_size));
			else
				return FALSE;
		}
		
		/**
		 * Encrypt the $data and write it to the page encrypted data file
		 * 
		 * @since Version 1.3
		 */
		public function _write_data() {
			if(!empty($data=$this->data)) {
				if( !$this->in_admin )
					unset($this->data['site_name']);
				unset($this->data['msg']);
				$file = fopen($this->data_path.$this->data_file, 'w');
				fwrite($file, $this->encrypt(json_encode($this->data)));
			}
		}
		
		/**
		 * Initialize $data
		 *
		 * @param String $data_file file location of the encrypted data file
		 * @param Boolean $admin determine if the request comes from the admin pages
		 * @param Boolean @return return the encrypted value if TRUE
		 * @return Array
		 * 
		 * @since Version 1.3
		 */
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
		
		/**
		 * Update $data or a specific $data element
		 *
		 * @param Array $data the new data
		 * @param String $key the array key to be updated if specified
		 * @return Boolean
		 * 
		 * @since Version 1.3
		 */
		public function set_data($data, $key=FALSE) {
			if(!$key)
				return FALSE;
			elseif($key === TRUE)
				$this->data = $data;
			else
				$this->data[str_replace('field_', '', $key)] = $data;

			return TRUE;
		}
		
		/**
		 * Returns the whole $data or a specific $data element
		 *
		 * @param String $key the array key to be returned
		 * @return mixed
		 * 
		 * @since Version 1.3
		 */
		public function get_data($key=FALSE) {
			if($key) {
				return empty($this->data[$key])? FALSE:$this->data[$key];
			}
			else
				return empty($this->data)? FALSE:$this->data;
		}
	}
?>