<?php
namespace BlueFission\Behavioral\Behaviors;

class Action extends Behavior
{
	const ACTIVATE = 'DoActivate';
	const UPDATE = 'DoUpdate';

	public function __construct( $name )
	{
		parent::__construct( $name, 0, false, true );
	}
}