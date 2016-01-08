<?php
namespace BlueFission\Behavioral\Behaviors;

class State extends Behavior
{
	const DRAFT = 'IsDraft';
	const DONE = 'IsDone';
	const NORMAL = 'IsNormal';
	const READONLY = 'IsReadonly';
	const BUSY = 'IsBusy';

	public function __construct( $name )
	{
		parent::__construct( $name, 0, true, true );
	}
}