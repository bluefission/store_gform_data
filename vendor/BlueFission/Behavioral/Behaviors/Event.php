<?php
namespace BlueFission\Behavioral\Behaviors;

class Event extends Behavior
{
	const LOAD = 'OnLoad';
	const UNLOAD = 'OnUnload';
	const ACTIVATED = 'OnActivated';
	const CHANGE = 'OnChange';
	const COMPLETE = 'OnComplete';
	const SUCCESS = 'OnSuccess';
	const FAILURE = 'OnFailure';
	const MESSAGE = 'OnMessageUpdate';

	public function __construct( $name )
	{
		parent::__construct( $name, 0, true, false );
	}
}