<?php
/**
 * This is the main class of W3B Template
 * 
 * @package w3b-template
 * @since Version 1.3
 */
	/**
	 * Includes the file for the data-manager subpackage
	 */
	include('dm.class.php');
	
	class W3B extends Data_Manager {		
		var $site 		= array();	// General site data
		var $pages 		= array();	// Pages data
		var $rewrite 	= array();	// Rewrite data
		var $config 	= array();	// Site configuration data
		
		var $template_file;			// The template / data filename
		var $is_admin;				// Boolean to determine if you're logged in the admin pages
		
		/**
		 * Runs the function on load then calls the init functions
		 *
		 * @param String $data key, to be generated if blank
		 *
		 * @since Version 1.3
		 */
		public function __construct($key=FALSE) {
			parent::__construct($key);

			// List of functions to prepare the required data
			$this->_set_site_data();
			$this->_set_config();
			$this->_rewrite();
			$this->_set_template();
			$this->_set_page_schema();
		}

		/**
		 * Initialization of the general site data
		 *
		 * @since Version 1.3
		 */
		private function _set_site_data() {
			$this->init_data('data/admin.png', TRUE);
		}
		
		/**
		 * Intialization of the config and file data path
		 *
		 * @since Version 1.3
		 */
		private function _set_config() {
			include('cfg/settings.php');
			$this->config = (object) $config;
			$this->data_path = str_replace('index.php','data/',$_SERVER['SCRIPT_FILENAME']);
		}
		
		/**
		 * Initialization of the rewrite data
		 *
		 * @since Version 1.3
		 */
		private function _rewrite() {
			$array = explode('index.php', $_SERVER['PHP_SELF']);
			
			$this->rewrite = array(
				'rewrite_base'	=> $array[0],
				'uri'			=> strip_trailing_slash( ((sizeof($array)>1)? $array[1]:''), '', TRUE )
			);
		}
		
		/**
		 * Prepare page layout to be loaded
		 *
		 * @since Version 1.3
		 */
		private function _set_template() {
			if($uri = $this->rewrite['uri']){
				$filename = preg_replace('/\/?\?.*$/', '', $uri);
				$filename = str_replace(array(' ','/'), array('-','_'), $filename);
			}
			else
				$filename = $this->config->default_filename;
				
			$this->data_file = $filename . '.png';
			$this->template_file = $filename;
			
			// Check if all layout files are present
			$load_data = TRUE;
			$max=sizeof($this->config->page_layout);
			for($y=0; $y<$max ;$y++) {
				$filename = $this->config->page_layout[$y];
				if( $this->config->page_layout[$y] === 'body' ) {
					$filename = $this->template_file;
					
					if($this->in_admin=$this->_check_admin()) {
						$this->config->page_layout = array('data/admin.tpl.php');
						$load_data = FALSE;
						break;
					}
					elseif( !file_exists($this->config->template_dir . $filename . $this->config->file_extension) ) {
						$load_data = FALSE;
						
						if( !file_exists($this->config->template_dir . '404' . $this->config->file_extension) ) {
							$this->config->page_layout = array('data/404.php');
							$this->data = array(
								'url'	=> $this->rewrite['rewrite_base'].'data/'
							);
							break;
						}
						else
							$filename = '404';
					}
				}
				
				if( !file_exists($file_path = $this->config->template_dir . $filename . $this->config->file_extension) )
					unset($this->config->page_layout[$y]);
				else
					$this->config->page_layout[$y] = $file_path;
			}
			
			if($load_data)
				$this->init_data();
		}
		
		/**
		 * Applying the templating method for the content
		 *
		 * @param String $content
		 * @param Array $data information what will be displayed
		 * @return String
		 *
		 * @since Version 1.3
		 */
		private function _apply_templating($content, $data=array()) {
			if(!$this->get_data()) return $content;
			$data = empty($data)? $this->get_data():$data;
			
			return str_replace($this->_prepare_template_pattern($data), $data, $content);
		}
		
		/**
		 * Compile the patterns to be used for the templating method
		 *
		 * @param Array $data
		 * @return String
		 *
		 * @since Version 1.3
		 */
		private function _prepare_template_pattern($data) {
			$pattern = array();
			foreach($data as $key => $value)
				$pattern[] = '{{'.$key.'}}';
			
			return $pattern;
		}
		
		/**
		 * Set the page schema as an array derived from the pages config file
		 *
		 * @since Version 1.3
		 */
		private function _set_page_schema() {
			if( empty($this->pages['page_schema']) ) {
				require_once('cfg/pages.php');
				$this->pages['page_schema'] = $pages;
			}
		}
		
		/**
		 * Admin section starts here
		 */

		/**
		 * Check if you are accessing the admin pages
		 *
		 * @return Boolean
		 *
		 * @since Version 1.3
		 */
		private function _check_admin() {
			preg_match( '/^admin\/?/', $this->rewrite['uri'], $matches);
			return !empty($matches);
		}
		
		/**
		 * Get the specific admin page being accessed
		 *
		 * @return String
		 *
		 * @since Version 1.3
		 */
		private function _get_target() {
			return preg_replace('/^admin\/?/', '', $this->rewrite['uri']);
		}
		
		/**
		 * Compile the page schema to be used as the drop down
		 * selection on selecting the page to edit
		 *
		 * @param Array $data
		 *
		 * @since Version 1.3
		 */
		private function _prepare_page_list($data) {
			$pages = array();
			$page_list = $this->_prepare_page_list_cleanup($data);
			$pages = $this->_prepare_page_list_sort($page_list);
			return $pages;
		}
		
		/**
		 * Sort the cleaned up array of the page schema
		 *
		 * @param Array $data
		 * @param String $prefix
		 * @param Array (reference) $pages
		 * @return Array
		 *
		 * @since Version 1.3
		 */
		private function _prepare_page_list_sort($data, $prefix='', &$pages=array()) {
			foreach($data as $page_name => $val) {
				if( is_array($val) )
					$this->_prepare_page_list_sort($val, $page_name.'/', $pages);
				else
					$pages[] = $prefix.$val;
			}
			return $pages;
		}
		
		/**
		 * Cleanup the unwanted elemnts in the page schema
		 *
		 * @param Array $data
		 * @return Array
		 *
		 * @since Version 1.3
		 */
		private function _prepare_page_list_cleanup($data) {
			$page_list = array();
			foreach($data as $page_name => $val) {
				if( is_array($val) ) {
						
					if( array_key_exists('subpage', $val) ) {
						$page_list[] = $page_name;
						$page_list[$page_name] = $this->_prepare_page_list_cleanup($val['subpage']);
					}
					else
						$page_list[] = $page_name;
				}
			}
			return $page_list;
		}
		
		/**
		 * Login method
		 *
		 * @param Array $input This is the $_REQUEST from the login form
		 *
		 * @since Version 1.3
		 */
		private function _login($input) {
			if( $this->get_data('username') == $input['username'] && $this->get_data('password') == hash('sha256',$input['password']) )
				$_SESSION['is_admin'] = TRUE;
		}
		
		/**
		 * Initialize the admin functions
		 *
		 * @since Version 1.3
		 */
		private function _start_admin() {
			session_start();
			$this->pages['page_list'] = $this->_prepare_page_list($this->pages['page_schema']);
			
			if(($target=$this->_get_target())) {
				$filename = preg_replace('/\/?\?.*$/', '', $target);
				$filename = str_replace(array(' ','/'), array('-','_'), $filename);
				
				if($target=='homepage')
					$filename = $this->config->default_filename;
			}
			else
				$filename = 'admin';
			
			$this->data_file = ($filename) . '.png';
			$this->init_data();
			$this->set_data($this->site['site_name'], 'site_name');
			
			if( $target == 'logout' ) {
				if( !empty($_SESSION['is_admin']) )
					unset($_SESSION['is_admin']);
					
				$msg = 'Sucessfully logged out';
			}
			if( !empty($_REQUEST) ) {
				if( $_REQUEST['target'] === 'login' ) {
					$this->_login($_REQUEST);
				}
				elseif( !$target ) {
					$data = $this->get_data();
					
					if( !empty($_REQUEST['old_password']) || !empty($_REQUEST['new_password']) || !empty($_REQUEST['repeat_password']) ) {
						if( !empty($_REQUEST['new_password']) && ($_REQUEST['new_password'] === $_REQUEST['repeat_password']) )
							if( $data['password'] === hash('sha256', $_REQUEST['old_password']) ) {
								$msg = $this->update_field(array(
									'password'	=> hash('sha256', $_REQUEST['new_password'])
								));
							}
							else
								$msg = 'Old password is incorrect';
						else
							$msg = 'Passwords did not match';
					}
					
					$msg = $this->update_fields($_REQUEST, array(
						'site_name'
					));
					$this->set_data($this->get_data('site_name'), 'site_name');
				}
				else {
					$msg = $this->update_fields($_REQUEST);
				}
			}
			$this->set_data((empty($msg)? '':'<p class="notif">'.$msg.'</p>'), 'msg');
			
			if( $this->is_admin = !empty($_SESSION['is_admin']) ) {
				$this->set_data($this->get_template('data/page_list.tpl.php', $this->pages), 'page_list');
				
				if($target) {
					$key = $target;
					if($target=='homepage')
						$key = $this->config->default_filename;
					
					$nodes = $this->pages['page_schema'];
					foreach( explode('/', $key) as $node ) {
						if( array_key_exists('subpage', $nodes) )
							$nodes = $nodes['subpage'][$node];
						else
							$nodes = $nodes[$node];
					}
					unset($nodes['subpage']);
					
					$inputs = array(
								'inputs'		=> $nodes,
								'form_title'	=> 'Edit ' . clean_text(($target),TRUE),
								'action'		=> '',
								'target'		=> $target
					);
				}
				else {
					$inputs = array(
								'inputs'		=> array(
													'site_name'				=> 'text',
													'old_password'			=> 'password',
													'new_password'			=> 'password',
													'repeat_password'		=> 'password'
												),
								'submit'		=> 'Edit Settings',
								'form_title'	=> 'Edit Site Settings',
								'action'		=> ''
					);
				}
				
				$this->set_data($this->get_template('helpers/form_content.tpl.php', $inputs), 'form_content');
			}
			else {
				$this->set_data(
					$this->get_template('helpers/form_content.tpl.php', array(
						'inputs'		=> array(
											'username'	=> 'text',
											'password'	=> 'password'
										),
						'submit'		=> 'Login',
						'form_title'	=> 'Login',
						'action'		=> site_url().'admin/',
						'target'		=> 'login'
				)), 'form_content');
				
				$this->set_data('', 'page_list');
			}
		}
		
		/**
		 * Admin section ends here
		 */

		/**
		 * Front end section starts here
		 */
		
		/**
		 * The main function to trigger the output
		 *
		 * @since Version 1.3
		 */
		public function run() {
			if( file_exists('view/main.php') )
				require_once('view/main.php');
			if($this->in_admin)
				$this->_start_admin();
			$this->print_output($this->get_data());
		}
		
		/**
		 * print the compiled front end output
		 *
		 * @param Array $data
		 *
		 * @since Version 1.3
		 */
		public function print_output($data) {
			echo $this->get_output($data);
		}
		
		/**
		 * Compile the front end output then return the string output
		 *
		 * @param Array $data
		 * @return String
		 *
		 * @since Version 1.3
		 */
		public function get_output($data) {
			if($data)
				extract($data);
			$content = '';
			foreach($this->config->page_layout as $page)
				$content .= $this->get_template($page, $data);
			
			return $this->_apply_templating($content);
		}
		
		/**
		 * Fetch the template then convert it to string
		 *
		 * @param String $page the template to be included in the output
		 * @param Array $data
		 * @return String
		 *
		 * @since Version 1.3
		 */
		public function get_template($page, $data=array()) {
			if($data)
				extract($data);
			ob_start();
			include($page);
			return ob_get_clean();
		}

		/**
		 * Front end section ends here
		 */

		/**
		 * Helper section starts here
		 */
		
		/**
		 * Returns the site URL set in the configuration file
		 *
		 * @return String
		 *
		 * @since Version 1.3
		 */
		public function site_url() {
			return strip_trailing_slash($this->config->site_url,'/');
		}
		
		/**
		 * Update the $data cached in the data_manager subpackage
		 *
		 * @param Array $data
		 * @param array $fields specify the data to be set, will default to all elements
		 * @return String
		 *
		 * @since Version 1.3
		 */
		public function update_fields($data, $fields=array()) {
			foreach($data as $field_name => $value)
				if( in_array($field_name, $fields) || empty($fields) )
					$this->set_data($value, htmlentities($field_name) );
			$data = $this->_write_data();
			
			return "Successfully updated";
		}
		
		/**
		 * Update a single element of $data cached in the data_manager subpackage
		 *
		 * @param Array $data
		 * @return String
		 *
		 * @since Version 1.3
		 */
		public function update_field($data) {
			foreach($data as $field_name => $value)
				$this->set_data($value, htmlentities($field_name) );
			$data = $this->_write_data();
			
			return "Successfully updated";
		}
		
		/**
		 * Cleanup the page schema to be used normally for menu
		 *
		 * @return Array
		 *
		 * @since Version 1.3
		 */
		public function get_page_list() {
			return $this->_prepare_page_list_cleanup($this->pages['page_schema']);
		}

		/**
		 * Helper section ends here
		 */
	}
	
	$w3b = new W3B('IlMxZEZiMVpNYkZSQlZVcG5kbkZrV201UFNFUkNiVkZUVFZocGRVZDBZMUpEYzA1cllXWjVTWEpaZDBaNGFHcGxZbnBRY0h0YmZIeGRmV0ZpWTJSbFptZG9hV3ByYkcxdWIzQnhjbk4wZFhaM2VIbDZRVUpEUkVWR1IwaEpTa3RNVFU1UFVGRlNVMVJWVmxkWVdWbz0i');
	
	/**
	 * Helper function section starts here
	 */

	/**
	 * Returns the site URL set in the configuration file
	 *
	 * @return String
	 *
	 * @since Version 1.3
	 */
	function site_url() {
		global $w3b;
		return $w3b->site_url();
	}
	
	/**
	 * Get the array of pages to be used usually for menu
	 *
	 * @return Array
	 *
	 * @since Version 1.3
	 */
	function get_pages() {
		global $w3b;
		return $w3b->get_page_list();
	}
	
	/**
	 * Cleanup the trailing slashes of the string
	 *
	 * @param String $text
	 * @param String $append to be appended to the returned string
	 * @param Boolean $both will also clean the slashes at the start of the string
	 * @return String
	 * 
	 * @since Version 1.3
	 */
	function strip_trailing_slash($text, $append='', $both=FALSE) {
		if($both)
			return preg_replace('/^\/|\/$/', '', $text).$append;
		return preg_replace('/\/$/', '', $text).$append;
	}
	
	/**
	 * Cleanup the variable formatted string to a normal space separated string
	 * 
	 * @param String $text
	 * @param Boolean $cap will convert the first letter of each word to uppercase if true
	 * @return String
	 *
	 * @since Version 1.3
	 */
	function clean_text($text, $cap=FALSE) {
		$text = str_replace(array('_','-','/'), array(' ',' ',' / '), $text);
		if($cap)
			return ucwords($text);
		return $text;
	}

	/**
	 * Convert a text to a valid css class
	 * 
	 * @param String $text
	 * @return String
	 *
	 * @since Version 1.3
	 */
	function text_to_class($text) {
		$text = preg_replace('/[^\w- ]/', '', $text);
		$text = str_replace(array('_',' '), array('-','-'), $text);
		return strtolower($text);
	}

	/**
	 * Helper function section ends here
	 */

?>