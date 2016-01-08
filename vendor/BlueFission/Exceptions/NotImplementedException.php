<?php
namespace BlueFission;

class NotImplementedException extends \Exception
{
	public function NotImplementedException( $message = "" )
	{
		parent::__construct( $message );
	}
}