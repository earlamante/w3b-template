<?php
	include('dm.class.php');
	
	class W3B extends Data_Manager {
		var $site_settings = array();
		var $rewrite = array();
		var $config = array();
		var $page_data = array();
		var $template_file;
		var $error_page = FALSE;
		
		public function __construct() {
			// List of functions to prepare the required data
			$this->_set_config();
			$this->_rewrite();
			$this->_set_template();
		}
		
		private function _set_config() {
			include('cfg/config.php');
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
					
					if( !file_exists($this->config->template_dir . $filename . $this->config->file_extension) ) {
						$this->error_page = TRUE;
						
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
			
			if(!$this->error_page)
				$this->init_data();
		}
		
		private function _apply_templating($content) {
			if(!$this->get_data())
				return $content;
			return str_replace($this->_prepare_template_pattern(), $this->get_data(), $content);
		}
		
		private function _prepare_template_pattern() {
			if(!$this->get_data())
				return FALSE;
			
			$pattern = array();
			foreach($this->get_data() as $key => $value)
				$pattern[] = '{{'.$key.'}}';
			
			return $pattern;
		}
		
		// Front Page
		public function run() {
			$this->print_output($this->get_data());
		}
		
		public function print_output($data) {
			echo $this->get_output($data);
		}
		
		public function site_url() {
			return strip_trailing_slash($this->config->site_url,'/');
		}
		
		public function get_output($data) {
			if($data)
				extract($data);
				
			ob_start();
			foreach($this->config->page_layout as $page)
				include($page);
			$content = ob_get_clean();
			
			return $this->_apply_templating($content);
		}
	}
	
	$w3b = new W3B();
	
	// Helpers Section
	function site_url() {
		global $w3b;
		return $w3b->site_url();
	}
	
	function strip_trailing_slash($text, $append='', $both=FALSE) {
		if($both)
			return preg_replace('/^\/|\/$/', '', $text).$append;
		return preg_replace('/\/$/', '', $text).$append;
	}

?>