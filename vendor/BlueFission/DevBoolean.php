<?php
namespace BlueFission;

class DevBoolean extends DevValue implements IDevValue {
	
	protected $_type = "boolean";

	// return the opposite value of a boolean variable
	public function _opposite() {
		$bool = $this->_value;
	    return (!($bool === true));
	}
}