<?php
namespace BlueFission\Collections;

interface ICollection {
	public function contents();
	public function add( &$object, $label = null );
	public function has( $label );
	public function get( $label );
	public function remove( $label );
}