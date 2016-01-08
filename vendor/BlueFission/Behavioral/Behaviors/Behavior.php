<?php
namespace BlueFission\Behavioral\Behaviors;

class Behavior
{
	private $_name;

	protected $_persistent;
	protected $_passive;
	protected $_priority;
	
	public $_target;
	public $_context;

	public function __construct($name, $priority = 0, $passive = true, $persistent = true)
	{
		$this->_name = $name;
		$this->_persistent = $persistent;
		$this->_passive = $passive;
		$this->_priority = $priority;
		$this->_target = null;
	}	
	
	public function name()
	{
		return $this->_name;
	}

	public function is_persistent()
	{
		return $this->_persistent;
	}

	public function is_passive()
	{
		return $this->_passive;
	}

	public function priority()
	{
		return $this->_priority;
	}

	public function __toString()
	{
		return $this->name();
	}
}