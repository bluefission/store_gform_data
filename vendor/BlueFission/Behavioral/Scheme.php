<?php
namespace BlueFission\Behavioral;

use BlueFission\DevValue;
use BlueFission\Collections\Collection;
use BlueFission\Exceptions\NotImplementedException;
use BlueFission\Behavioral\Behaviors\Behavior;
use BlueFission\Behavioral\Behaviors\Event;
use BlueFission\Behavioral\Behaviors\State;
use BlueFission\Behavioral\Behaviors\Action;
// Scheme Class
class Scheme extends Dispatcher 
{
	protected $_history;
	protected $_state;
	protected $_final = false;
	protected $_multistate = true;

	public function __construct()
	{
		$this->_history = new Collection();
		$this->_state = new Collection();
		parent::__construct();

		$this->perform( State::DRAFT );
	}

	public function perform( )
	{
		$args = func_get_args();
		$behavior = array_shift( $args );

		if ( is_string($behavior) )
			$behavior = new Behavior( $behavior );

		if ( !($behavior instanceof Behavior) )
			throw new InvalidArgumentException("Invalid Behavior Type");

		$behaviorName = $behavior->name();

		if ( $this->can( $behaviorName ) )
		{
			$this->dispatch( $behavior, $args );
			$this->_history->add($behaviorName, $behaviorName);
			if ( $this->_behaviors->has( $behaviorName ) && $this->_behaviors->get( $behaviorName )->is_persistent() )
			{
				if ( !$this->_multistate )
					$this->_state->clear();
				$this->_state->add($behaviorName, $behaviorName);
			}
		}
		else
		{
			throw new NotImplementedException("Behavior '{$behaviorName}' is not implemented");
		}
	}

	public function can( $behaviorName )
	{
		$can = ( ( $this->_behaviors->has( $behaviorName ) || !$this->_final ) && !$this->is( State::BUSY ) );
		return $can;
	}

	public function is( $behaviorName = null )
	{
		if ( $behaviorName )
			return $this->_state->has( $behaviorName );
		else
			return $this->_state->last();
	}

	public function halt( $behaviorName )
	{
		$this->_state->remove( $behaviorName );
	}

	public function field($field, $value = null)
	{		
		if ( $this->is( State::READONLY ) )
			$value = null;

		if ( DevValue::isNotNull($value) ) 
			$this->dispatch( Event::CHANGE );
		
		return parent::field($field, $value);
	}

	protected function init()
	{
		parent::init();
		$this->behavior( new Event( Event::CHANGE ) );
		$this->behavior( new Event( Event::ACTIVATED ) );
		$this->behavior( new Event( Event::COMPLETE ) );
		$this->behavior( new Event( Event::SUCCESS ) );
		$this->behavior( new Event( Event::FAILURE ) );

		$this->behavior( new State( State::DRAFT ) );
		$this->behavior( new State( State::DONE ) );
		$this->behavior( new State( State::NORMAL ) );
		$this->behavior( new State( State::READONLY ) );
		$this->behavior( new State( State::BUSY ) );

		$this->behavior( new Action( Action::ACTIVATE ) );
		$this->behavior( new Action( Action::UPDATE ) );
	}
}