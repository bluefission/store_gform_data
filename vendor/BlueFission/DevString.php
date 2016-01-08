<?php
namespace BlueFission;

class DevString extends DevValue implements IDevValue {
	protected $_type = "string";

	public function random($length = 8, $symbols = false) {
		$alphanum = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if ($symbols) $alphanum .= "~!@#\$%^&*()_+=";

		if ( $this->_value == "" ) {
			$this->_value = $alphanum;
		}
		$rand_string = '';
		for($i=0; $i<$length; $i++)
			$rand_string .= $this->_value[rand(0, strlen($this->_value)-1)];

		return $this->_value;
	}

	// truncate a string to a given number of words using space as a word boundary
	public function truncate($limit = 40) {
		$string = trim( $this->_value );
		$string_r = explode(' ', $string, ($limit+1));
		if (count($string_r) >= $limit && $limit > 0) array_pop($string_r);
		$output = implode (' ', $string_r);
		return $output;
	}

	// test if two strings match
	public function match($str2) {
		$str1 = $this->_value;
		return ($str1 == $str2);
	}

	// Encrypt a string
	public function encrypt($mode = null) {
		$string = $this->_value;
		switch ($mode) {
		default:
		case 'md5':
			$string = md5($string);
			break;
		case 'sha1':
			$string = sha1($string);
			break;
		}
		
		return $output;
	}

	// Reverse strpos
	public function strrpos($needle) {
		$haystack = $this->_value;
		$i = strlen($haystack);
		while ( substr( $haystack, $i, strlen( $needle ) ) != $needle ) 
		{
			$i--;
			if ( $i < 0 ) return false;
		}
		return $i;
	}

	// test is a string exists in another string
	public function has($needle) {
		$haystack = $this->_value;
		return (strpos($haystack, $needle) !== false);
	}
}