<?php
namespace BlueFission\Collections;

use ArrayAccess;
use ArrayObject;
use BlueFission\DevValue;
use BlueFission\DevArray;

class Collection extends DevArray implements ICollection, ArrayAccess {
	protected $_value;
	protected $_type = "";

	public function __construct( $value = null ) {
		parent::__construct( $value );
		if ( empty( $value ) )
		{
			$this->_value = new ArrayObject( );
		}
		else
			$this->_value = new ArrayObject( DevArray::toArray($this->_value) );		
	}

	public function get( $key ) {
		if (!is_scalar($key) && !is_null($key)) {
			throw new InvalidArgumentException('Label must be scalar');
		}
		if ($this->has( $key ))
			return $this->_value[$key];
		else 
			return null;		
	}

	public function toArray( $allow_empty = false ) {
		$value = $this->_value->getArrayCopy();
		return $value;
	}

	public function has( $key ) {
		if (!is_scalar($key) && !is_null($key)) {
			throw new InvalidArgumentException('Label must be scalar');
		}
		return array_key_exists( $key, $this->_value );
	}
	public function add( &$object, $key = null ) {
		if (!is_scalar($key) && !is_null($key)) {
			throw new InvalidArgumentException('Label must be scalar');
		}
		$this->_value[$key] = $object;
	}
	public function first()	{
		return end ( array_reverse ( $this->_value ) );
	}
	public function last() {
		return end( $this->_value );
	}
	public function contents() {
		return $this->_value->getArrayCopy();
	}
	public function remove( $key ) {
		if (!is_scalar($key) && !is_null($key)) {
			throw new InvalidArgumentException('Label must be scalar');
		}
		unset( $this->_value[$key]);
	}
	public function clear() {
		unset( $this->_value );
		$this->_value = new ArrayObject();
	}

	public function count() {
		return $this->_value->count();
	}

	public function serialize() {
        return serialize($this->_value);
    }

    public function unserialize($data) {
        $this->_value = unserialize($data);
    }

    // Array Access
    public function offsetExists ( $offset ) {
		return $this->has( $offset );
    }
	public function offsetGet ( $offset ) {
		return $this->get( $offset );
	}
	public function offsetSet ( $offset, $value ) {
		$this->add( $value, $offset );
	}
	public function offsetUnset ( $offset ) {
		$this->remove( $offset );
	}
}