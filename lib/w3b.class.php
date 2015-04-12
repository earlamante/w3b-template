<?php
	include('dm.class.php');
	include('cfg/settings.php');
	
	class W3B extends Data_Manager {
		var $site = array();
		var $pages = array();
		var $rewrite = array();
		var $config = array();
		var $load_data = TRUE;
		var $template_file;
		var $is_admin;
		
		public function __construct() {
			// List of functions to prepare the required data
			$this->_set_site_data();
			$this->_set_config();
			$this->_rewrite();
			$this->_set_template();
		}
		
		private function _set_site_data() {
			$this->init_data('data/admin.png', TRUE);
		}
		
		private function _set_config() {
			global $config;
			$this->config = (object) $config;
			$this->data_path = str_replace('index.php','data/',$_SERVER['SCRIPT_FILENAME']);
		}
		
		private function _rewrite() {
			$array = explode('index.php', $_SERVER['PHP_SELF']);
			
			$this->rewrite = array(
				'rewrite_base'	=> $array[0],
				'uri'			=> strip_trailing_slash( ((sizeof($array)>1)? $array[1]:''), '', TRUE )
			);
		}
		
		private function _set_template() {
			if($uri = $this->rewrite['uri']){
				$filename = preg_replace('/\/?\?.*$/', '', $uri);
				$filename = str_replace('/', '_', $filename);
			}
			else
				$filename = $this->config->default_filename;
				
			$this->data_file = $filename . '.png';
			$this->template_file = $filename;
			
			// Check if all layout files are present
			$max=sizeof($this->config->page_layout);
			for($y=0; $y<$max ;$y++) {
				$filename = $this->config->page_layout[$y];
				if( $this->config->page_layout[$y] === 'body' ) {
					$filename = $this->template_file;
					
					if($this->_check_admin()) {
						$this->config->page_layout = array('data/admin.tpl.php');
						$this->_start_admin();
						$this->load_data = FALSE;
						break;
					}
					elseif( !file_exists($this->config->template_dir . $filename . $this->config->file_extension) ) {
						$this->load_data = FALSE;
						
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
			
			if($this->load_data)
				$this->init_data();
		}
		
		private function _apply_templating($content, $data=array()) {
			if(!$this->get_data()) return $content;
			$data = empty($data)? $this->get_data():$data;
			
			return str_replace($this->_prepare_template_pattern($data), $data, $content);
		}
		
		private function _prepare_template_pattern($data) {
			$pattern = array();
			foreach($data as $key => $value)
				$pattern[] = '{{'.$key.'}}';
			
			return $pattern;
		}
		
		// Admin
		private function _check_admin() {
			preg_match( '/^admin\/?/', $this->rewrite['uri'], $matches);
			return !empty($matches);
		}
		
		private function _get_target() {
			return preg_replace('/^admin\/?/', '', $this->rewrite['uri']);
		}
		
		private function _prepare_page_list($data) {
			$pages = array();
			$page_list = $this->_prepare_page_list_cleanup($data);
			$pages = $this->_prepare_page_list_sort($page_list);
			return $pages;
		}
		
		private function _prepare_page_list_sort($data, $prefix='', &$pages=array()) {
			foreach($data as $page_name => $val) {
				if( is_array($val) )
					$this->_prepare_page_list_sort($val, $page_name.'/', $pages);
				else
					$pages[] = $prefix.$val;
			}
			return $pages;
		}
		
		private function _prepare_page_list_cleanup($data) {
			$page_list = array();
			foreach($data as $page_name => $val) {
				if( is_array($val) ) {
						
					if( array_key_exists('subpage', $val) ) {
						$page_list[$page_name] = $this->_prepare_page_list_cleanup($val['subpage']);
					}
					else
						$page_list[] = $page_name;
				}
			}
			return $page_list;
		}
		
		private function _login($input) {
			if( $this->get_data('username') == $input['username'] && $this->get_data('password') == hash('sha256',$input['password']) )
				$_SESSION['is_admin'] = TRUE;
		}
		
		private function _start_admin() {
			session_start();
			
			require_once('cfg/pages.php');
			$this->pages['page_schema'] = $pages;
			$this->pages['page_list'] = $this->_prepare_page_list($pages);
			
			$this->init_data();
			$this->set_data($this->site['site_name'], 'site_name');
			
			if( ($target=$this->_get_target()) == 'logout' ) {
				if( !empty($_SESSION['is_admin']) )
					unset($_SESSION['is_admin']);
					
				$msg = '<p class="notif">Sucessfully logged out</p>';
				
				if($target) {
					$filename = preg_replace('/\/?\?.*$/', '', $target);
					$filename = str_replace('/', '_', $filename);
					
					if($target=='homepage')
						$filename = $this->config->default_filename;
				}
				else
					$filename = 'admin';
				
				$this->data_file = ($filename) . '.png';
			}
			
			if( !empty($_REQUEST['login']) ) {
				$this->_login($_REQUEST);
			}
			
			$this->set_data((empty($msg)? '':$msg), 'msg');
			
			if( $this->is_admin = !empty($_SESSION['is_admin']) ) {
				$this->set_data($this->get_template('data/page_list.tpl.php', $this->pages), 'page_list');
				
				if($target) {
					$key = $target;
					if($target=='homepage')
						$key = 'index';
					
					$nodes = $pages;
					foreach( explode('/', $key) as $node ) {
						if( array_key_exists('subpage', $nodes) )
							$nodes = $nodes['subpage'][$node];
						else
							$nodes = $nodes[$node];
					}
					
					$inputs = array(
								'inputs'		=> $nodes,
								'form_title'	=> 'Edit ' . clean_text(($target),TRUE),
								'action'		=> ''
					);
				}
				else {
					$inputs = array(
								'inputs'		=> array(
													'site_name'				=> 'text',
													'old_password'			=> 'password',
													'new_password'			=> 'password',
													'repeat_password'		=> 'password',
													'edit_site_settings'	=> 'submit'
												),
								'form_title'	=> 'Edit Site Settings',
								'action'		=> ''
					);
				}
				
				$this->set_data($this->get_template('data/form_content.tpl.php', $inputs), 'form_content');
			}
			else {
				$this->set_data(
					$this->get_template('data/form_content.tpl.php', array(
						'inputs'		=> array(
											'username'	=> 'text',
											'password'	=> 'password',
											'login'		=> 'submit'
										),
						'form_title'	=> 'Login',
						'action'		=> site_url().'admin/'
				)), 'form_content');
				
				$this->set_data('', 'page_list');
			}
		}
		
		// Front Page
		public function run() {
			$this->print_output($this->get_data());
		}
		
		public function print_output($data) {
			echo $this->get_output($data);
		}
		
		public function get_output($data) {
			if($data)
				extract($data);
			
			$content = '';
			foreach($this->config->page_layout as $page)
				$content .= $this->get_template($page);
			
			return $this->_apply_templating($content);
		}
		
		public function get_template($page, $data=array()) {
			if($data)
				extract($data);
			
			ob_start();
			include($page);
			return ob_get_clean();
		}
	}
	
	$w3b = new W3B();
	
	// Helpers Section
	function site_url() {
		global $config;
		return strip_trailing_slash($config['site_url'],'/');
	}
	
	function strip_trailing_slash($text, $append='', $both=FALSE) {
		if($both)
			return preg_replace('/^\/|\/$/', '', $text).$append;
		return preg_replace('/\/$/', '', $text).$append;
	}
	
	function clean_text($text, $cap=FALSE) {
		$text = str_replace(array('_','/'), array(' ',' / '), $text);
		if($cap)
			return ucwords($text);
		return $text;
	}

?>