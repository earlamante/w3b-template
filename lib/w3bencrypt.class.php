<?php
	class W3B_Code {
		var $codering = array();
		var $sep = '{[||]}';
		
		public function __construct() {
			if( !defined('W3B_CODERING') )
				die('W3B_CODERING is not defined');
		}
		
		public function encode($data) {
			$sep = '%%%%%';
			$data = strrev(str_repeat($sep, (strlen($data)%2)).$data);
			$group = str_split($data, strlen($data)/2);
			$return = array(
				$group[1],
				$group[0]
			);
			for($y=0; $y<sizeof($return) ;$y++) {
				$data = $return[$y];
				$data = $data.str_repeat(strrev($sep), (strlen($data)%2));
				$group = str_split($data, strlen($data)/2);
				
				$return[$y] = array(
					$group[1],
					$group[0]
				);
				$return[$y] = implode('', $return[$y]);
			}
			return str_replace($sep, '', implode('', $return));
		}
		
		public function hash($data) {
			$key = explode($this->sep, base64_decode(W3B_CODERING));
			if(sizeof($key) === 2) {
				if(!$this->codering) {
					$code = array();
					for($y=0; $y<sizeof($key) ;$y++)
						$key[$y] = str_split($key[$y]);
					for($y=0; $y<sizeof($key[0]) ;$y++)
						$code[$key[0][$y]] = $key[1][$y];
					$this->codering = $code;
				}
				$codering = $this->codering;
				$datas = str_split($data);
				for($y=0; $y<sizeof($datas) ;$y++) {
					if( ctype_alnum($datas[$y]) )
						$datas[$y] = (empty($codering[$datas[$y]]))? $datas[$y]:$codering[$datas[$y]];
				}
				return implode('', $datas);
			}
			return FALSE;
		}
		
		public function encrypt($data) {
			if($data) {
				$data = $this->encode($data);
				return $this->hash($data);
			}
			return FALSE;
		}
		
		public function generate_key($data) {
			$salt = preg_replace('/[^a-zA-Z]/','',$data);
			$salt = str_replace('Z', '', implode('', array_unique(str_split($salt))));
			$salt .= str_repeat('Z', strlen($salt)%2);
			$code = array();
			$key = str_split($salt);
			for($y=0; $y<sizeof($key) ;$y++) {
				if( !empty($code[$y]) )
					continue;
				$num = rand($y+1, sizeof($key)-1);
				if( !empty($code[$num]) )
					$y--;
				else {
					$code[$y] = $key[$num];
					$code[$num] = $key[$y];
				}
			}
			ksort($code);
			$code = implode('', $code);
			$naked = $code.$this->sep.$salt;
			return base64_encode($naked);
		}
	}
?>