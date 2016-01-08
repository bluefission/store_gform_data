<?php
namespace BlueFission\Behavioral\Behaviors;

use BlueFission\Collections\Collection;
use BlueFission\Exceptions\NotImplementedException;

class HandlerCollection extends Collection
{
	public function add(&$handler, $priority = null)
	{
		$handler->priority($priority);
		$this->_value->append($handler);
		$this->prioritize();
	}

	public function has( $behaviorName )
	{
		foreach ($this->_value as $c)
		{
			if ($c->name() == $behaviorName)
				return true;
		}
	}

	public function get( $behaviorName )
	{
		throw new NotImplementedException('Function Not Implemented');
	}

	public function raise($behavior, $sender, $args)
	{
		if (is_string($behavior))
			$behavior = new Behavior($behavior);

		$behavior->_target = $behavior->_target ? $behavior->_target : $sender;

		foreach ($this->_value as $c)
		{
			if ($c->name() == $behavior->name())
			{
				$c->raise($behavior, $args);
			}
		}
	}

	private function prioritize()
	{
		$compare = $this->_value->uasort( function( $a, $b ) {
			if ( !($a instanceof Handler) || !($b instanceof Handler ) )
				return -1;

			if ($a->priority() == $b->priority()) 
			{
				return 0;
			}
			return ($a->priority() < $b->priority()) ? -1 : 1;
		});

		return $compare;
	}
}