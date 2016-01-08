<?php
namespace BlueFission;

class DevNumber extends DevValue implements IDevValue {
	protected $_type = "double";

	public function __construct( $value ) {
		$this->_value = $value;
		if ( $this->_type ) {
			$clone = $this->_value;
			settype($clone, $type);
			$remainder = $clone % 1;
			$this->_type = $remainder ? $this->_type : "int";
			settype($this->_value, $this->_type);
		}
	}

	public function _isValid($allow_zero = true) {
		$number = $this->_value;
		return (is_numeric($number) && ((DevValue::isNotNull($number) && $number != 0) || $allow_zero));
	}

	// return the ratio between two values
	public function _ratio($part = 0, $percent = false) {
		$whole = $this->_value;
		if (!DevNumber::isValid($part)) $part = 0;
		if (!DevNumber::isValid($whole)) $whole = 1;
		
		$ratio = ($part * 100)/$whole;
		
		return $ratio*(($percent) ? 100 : 1);
	}
}