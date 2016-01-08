<?php
namespace BlueFission;

class DevValue implements IDevValue {
	protected $_value;
	protected $_type = "";

	const MORPHING_METHOD_PREFIX = '_';

	public function __construct( $value = null ) {
		$this->_value = $value;
		if ( $this->_type ) {
			settype($this->_value, $this->_type);
		}
	}
	///
	//Variable value functions
	///////
	// ensure that a var is not null
	public function _isNotNull() {
		return (isset($this->_value) && $this->_value !== null && $this->_value != '');
	}

	// check if a var is null
	public function _isNull( ) {
		return !$this->isNotNull();
	}

	// check if a var has an empty value
	public function _isNotEmpty( ) {
		return ( $this->isNotNull( $this->_value ) || is_numeric( $this->_value) );
	}

	// check if a var has an empty value
	public function _isEmpty( ) {
		return !$this->isNotEmpty( $this->_value );
	}

	public function value() {
		return $this->_value;
	}

	public function __call( $method, $args ) {
		if ( method_exists($this, self::MORPHING_METHOD_PREFIX.$method) ) {
			$output = call_user_func_array(array($this, self::MORPHING_METHOD_PREFIX.$method), $args);
			return $output;
		} else {
			throw new Exception("Method not defined", 1);			
		}
	}

	public static function __callStatic( $method, $args ) {
		if ( method_exists(get_called_class(), self::MORPHING_METHOD_PREFIX.$method) ) {
			$class = get_called_class();
			$value = array_shift( $args );
			$var = new $class( $value );
			$output = call_user_func_array(array($var, self::MORPHING_METHOD_PREFIX.$method), $args);
			unset($var);
			return $output;
		} else {
			throw new Exception("Method not defined", 1);			
		}
	}
}