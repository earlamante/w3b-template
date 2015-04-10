<?php
	class W3B {
		var $site_settings = array();
		var $rewrite = array();
		var $config = array();
		var $page_data = array();
		var $data_file;
		var $template_file;
		
		public function __construct() {
			// List of functions to prepare the required data
			$this->_set_config();
			$this->_rewrite();
			$this->_set_template();
		}
		
		private function _set_config() {
			include('admin/config.php');
			$this->config = (object) $config;
		}
		
		private function _rewrite() {
			$rewrite_base	= str_replace( '/', '\/', preg_replace('/\/[^\/]*$/', '/', $_SERVER['PHP_SELF']) );
			$uri			= preg_replace('/^'.$rewrite_base.'/', '', $_SERVER['REQUEST_URI']);
			
			$this->rewrite = array(
				'rewrite_base'	=> $rewrite_base,
				'uri'			=> $uri
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
						if( !file_exists($this->config->template_dir . '404' . $this->config->file_extension) ) {
							$this->config->page_layout = array('data/404.html');
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
		}
		
		// Front Page
		public function run() {			
			$this->print_output();
		}
		
		public function print_output() {
			ob_start();
			foreach($this->config->page_layout as $page)
				include($page);
			$content = ob_get_contents();
			ob_end_clean();
			
			echo $content;
		}
	}
	
	$w3b = new W3B();
?>