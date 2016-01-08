<?php
use \BlueFission;
@include_once('Loader.php');
$loader = BlueFission\Loader::instance();
$loader->load('com.bluefission.develation.functions.common');
$loader->load('com.bluefission.develation.Configurable');

class Programmable extends Configurable
{
	protected $_tasks;

	public function __construct( )
	{
		parent::__construct();
		$this->_tasks = array();
	}

	public function __call($name, $args) 
	{
		if (method_exists ( $this , $name ))
		{
			return call_user_func_array(array($this, $name), $args);
		}
		if (isset($this->_tasks[$name]) && is_callable($this->_tasks[$name]))
		{
			return call_user_func_array($this->_tasks[$name], $args);
		}
		else 
		{
			throw new \RuntimeException("Method {$name} does not exist");
		}
	}

	public function learn($task, $function, $behavior = null )
	{
		if ( is_callable($function)
			&& (!array_key_exists($task, $this->_tasks) 
			&& $this->is( BlueFission\State::DRAFT )) )
		{
			$this->_tasks[$task] = $function->bindTo($this, $this);

			if ($behavior)
			{
				$this->behavior($behavior, $this->_tasks[$task]);
			}

			return true;
		}
		else
			return false;
	}

	public function forget($task)
	{
		if ( $this->is( BlueFission\State::DRAFT ) && isset( $this->_tasks[$task] ) )
			unset( $this->_tasks[$task] );
	}

	public function __set($field, $value)
	{
		if (is_callable($value))
			$this->learn($field, $value);
		else
			parent::__set($field, $value);
	}
}