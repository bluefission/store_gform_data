<?php
namespace BlueFission;

class DevObject implements IDevObject
{
	protected $_data;
	protected $_type;

	public function __construct() {
		if (!isset($this->_data))
			$this->_data = array();
		$this->_type = get_class();
	}

	public function field($field, $value = null) {
		if ( DevValue::isNotNull($value) ) {
			$this->_data[$field] = $value;
		} else {
			$value = (isset($this->_data[$field])) ? $this->_data[$field] : null;
		}
		return $value;
	}

	public function clear()
	{
		//$this->_data = array();
		array_walk($this->_data, function(&$value, $key) { 
			$value = ""; 
		});
	}
	
	public function __get($field)
	{
		return $this->field($field);
	}

	public function __set($field, $value)
	{
		$this->field($field, $value);
	}

	public function __isset( $field )
	{
		return isset ( $this->_data[$field] );
	}

	public function __unset( $field )
	{
		unset ( $this->_data[$field] );
	}

	public function __sleep()
	{
		return array_keys( $this->_data );
	}

	public function __wakeup()
	{
		
	}

	public function __toString()
	{
		return get_class( $this );
	}	
}