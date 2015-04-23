<?php
/**
 * W3Bkit's homebrewed two-way encryption algorithm
 *
 * @package w3b-template
 * @subpackage w3b-encryption
 * 
 * @since Version 1.3
 */
	class W3B_Code {
		var $codering = array();	// The codering is used for encoding/decoding the data
		var $sep = '{[||]}';		// Separator for the key
		var $key;					// Unique key to be used by $codering
		
		/**
		 * Runs the function on load and will set the unique $key
		 * 
		 * @since Version 1.3
		 */
		public function __construct() {
			$this->key = base64_decode('cEtEaU5qdXJkZm5xdmtJYWxoUEZnbUpDenlPTXhjTHRIR293YkVCZUFze1t8fF19YWJjZGVmZ2hpamtsbW5vcHFyc3R1dnd4eXpBQkNERUZHSElKS0xNTk9Q==');
		}
		
		/**
		 * Manipulates the $data to be scrumbled / unscrumbled
		 *
		 * @param String @data
		 * @return String
		 * 
		 * @since Version 1.3
		 */
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
		
		/**
		 * Mask $data based from the codering
		 *
		 * @param String $data
		 * @return String
		 * 
		 * @since Version 1.3
		 */
		public function hash($data) {
			$key = explode($this->sep, $this->key);
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
		
		/**
		 * Will encode then hash the encoded $data
		 *
		 * @param String $data
		 * @return String
		 * 
		 * @since Version 1.3
		 */
		public function encrypt($data) {
			if($data) {
				$data = $this->encode($data);
				return $this->hash($data);
			}
			return FALSE;
		}
		
		/**
		 * Generate a new key string based on the charset provided
		 *
		 * @param String $char_set accepts alphanumeric characters only
		 * @return String
		 * 
		 * @since Version 1.3
		 */
		public function generate_key($char_set) {
			$salt = preg_replace('/[^a-zA-Z]/','',$char_set);
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