<?php
namespace BlueFission\Behavioral\Behaviors;

use BlueFission\Collections\Collection;

class BehaviorCollection extends Collection {
	public function add( &$behavior, $label = null ) {
		if (!$this->has($behavior->name()))
			parent::add( $behavior );
	}

	public function get( $behaviorName ) {
		foreach ($this->_value as $c) {
			if ($c->name() == $behaviorName)
				return $c;
		}
	}

	public function has( $behaviorName ) {
		foreach ($this->_value as $c) {
			if ($c->name() == $behaviorName)
				return true;
		}
	}
}