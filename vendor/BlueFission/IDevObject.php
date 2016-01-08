<?php
namespace BlueFission;

interface IDevObject
{
	public function field( $var, $value = null );
	public function clear();
}