<?php
namespace BlueFission;

use ArrayAccess;

class DevArray extends DevValue implements IDevValue, ArrayAccess {
	protected $_type = "array";

	public function __construct( $value = null ) {
		parent::__construct( $value );
		$this->_value = $this->_toArray();
	}

	public function _isHash( ) {
		$var = $this->_value;
		return ((is_array( $var )) && !is_numeric( implode( array_keys( $var ))));
	}

	public function _isAssoc( ) {
		return $this->_isHash();
	}

	// checks to see if a variable is a numerically indexed array
	public function _isIndexed( ) {
		$var = $this->_value;
		return ((is_array( $var )) && is_numeric( implode( array_keys( $var ))));
	}

	// get value for given key in an array if it exists
	public function get( $key ) {
		$var = $this->_value;
		$keys = array_keys( $var );
		if ( in_array( $key, $keys ) )
		{
			return $var[$key];
		}
	}

	// set value for given key in an array if it exists
	public function set( $key, $value ) {
		$this->_value[$key] = $value;
	}

	// get the largest integer value from an array
	public function _max( ) {
		$array = $this->_value;
		if (sort($array)) {
			$max = (int)array_pop($array);
		}
		return $max;
	}

	//get the lowest integer value from an array
	public function _min( ) {
		$array = $this->_value;
		if (rsort($array)) {
			$max = (int)array_pop($array);
		}
		return $max;
	}

	//outputs any value as an array element or returns value if it is an array
	//$value argument takes any mixed variable
	//returns an array
	public function _toArray( $allow_empty = false) {
		$value = $this->_value;
		$value_r = array();
		if (!is_string($value) || (!$value == '' || $allow_empty))
			(is_array($value)) ? $value_r = $value : $value_r[] = $value;
		return $value_r;
	}

	public function value() {
		return $this->_toArray();
	}

	// Remove duplicate values from an array as a reference
	public function _removeDuplicates( ) {
		$array = $this->_value;
		if (is_array($array)) {
			$hold = array();
			foreach ($array as $a=>$b) {
				if (!in_array($b, $hold, true))	{ 
					$hold[$a] = $b;
				}
			}
			$array = $hold;
			unset($hold);
			return true;
		}
		else 
			return false;
	}

	// Case insensitive remove duplicate values from an array as a reference
	public function _iRemoveDuplicates( ) {
		$array = $this->_value;
		if (is_array($array)) 
		{
			$hold = array();
			 foreach ($array as $a=>$b) 
			 {
				if (!in_array(strtolower($b), $hold) && !is_array($b)) 
				{ 
					$hold[$a] = strtolower($b); 
				}
			}
			$array = $hold;
			unset($hold);
			return true;
		} 
		else 
			return false;
	}

	public function offsetExists ( $offset ) {
		return isset( $this->_value[$offset] );
	}
	public function offsetGet ( $offset ) {
		return $this->get( $offset );

	}
	public function offsetSet ( $offset , $value ) {
		$this->set($offset, $value);
	}
	public function offsetUnset ( $offset ) {
		if ( $this->offsetExists ( $offset ) )
			unset( $this->_value[$offset] );
	}
}