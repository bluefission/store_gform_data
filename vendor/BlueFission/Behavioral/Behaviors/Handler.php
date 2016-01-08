<?php
namespace BlueFission\Behavioral\Behaviors;

class Handler
{
	private $_behavior;
	private $_callback;
	private $_priority;

	public function __construct($behavior, $callback, $priority = 0) {
		$this->_behavior = $behavior;
		$this->_callback = $this->prepare($callback);
		$this->_priority = (int)$priority;
	}

	public function name() {
		return $this->_behavior->name();
	}

	public function raise($behavior, $args) {
		if ($this->_callback)
		{
			if ( $args == null )
			{
				$args = $behavior;
			}
			if ( !is_array($args) )
				$args = array( $args );
			
			call_user_func_array($this->_callback, $args);
		}
	}

	private function prepare($callback) {
		$process = '';
		if ( is_array( $callback ) ) {
			if ( count($callback) < 2 ) {
				$process = $callback[0];
			} else {
				$process = $callback;
			}
		} elseif ( is_string( $callback ) ) {
			$process = $callback;
			if ($pos = strpos($process, '('))
				$process = substr($process, 0, $pos);
		} else {
			$process = $callback;
		}
		
		if (!is_callable($process, true) ) {
			throw new InvalidArgumentException('Handler is not callable');
		}

		return $process;
	}

	public function priority( $int = null ) {
		if ( $int )
			$this->_priority = (int)$int;

		return $this->_priority;
	}

	public function callback() {
		return $this->_callback;
	}
	/*
	*/
}