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
		var $salt 	= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';	// This is the safest hash, complete lowercase and uppercase of the alphabet
		var $sep 		= '{[||]}';										// Separator for the key
		var $codering 	= array();										// The codering is used for encoding/decoding the data
		var $key;														// Unique key to be used by $codering
		
		/**
		 * Runs the function on load and will stop the operation if
		 * the $data key string is empty
		 *
		 * @param String $data key, to be generated if blank
		 * 
		 * @since Version 1.3
		 */
		public function __construct($data=FALSE) {
			if(!$data) {
				echo 'You did not supply a valid key edit the cfg/settings.php file and enter the encryption key below:<br /><br />';
				$key_string = json_encode($this->generate_key());
				echo base64_encode($key_string);
				die;
			}
			else {
				$this->key = base64_decode(json_decode(base64_decode($data)));
			}
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
			$sep = '%%%';
			$append = '===99';
			
			$string = base64_decode($this->hash($data));
			preg_match('/===99$/', $string, $matches);
			
			if($matches) {
				$data = preg_replace('/===99$/', '', $string);
				$append = FALSE;
			}
			
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
			
			$return = str_replace($sep, '', implode('', $return)).$append;
			if($append) {
				$return = base64_encode($return);
			}
			else
				$return = $this->hash($return);
			
			return $return;
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
		 * @return String
		 * 
		 * @since Version 1.3
		 */
		public function generate_key() {
			$salt = preg_replace('/[^a-zA-Z]/','',$this->salt);
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