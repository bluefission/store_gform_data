<?php 
namespace DevonSample;
use BlueFission\Behavioral\Configurable;
use BlueFission\Behavioral\Behaviors\State;

class WPUpdateable extends Configurable {
	protected $_post_id;

	/**
	 * Sets the wordpress post id for this operation
	 * @param int $id the id for the related Wordpress post
	 */
	public function setID( $id = null ) {
		if ( defined( 'WPINC' ) )
			$this->_post_id = $id ? $id : get_the_ID();
	}

	/**
	 * Loads the matching meta data for the currently set post id
	 * @return boolean true on success and false on failure
	 */
	public function load() {
		if ( ! defined( 'WPINC' ) || !$this->_post_id ) {
			return false;
		}
		
		$meta = get_post_custom( $this->_post_id );

		//var_dump( $meta);
		// $this->perform( State::DRAFT );
		foreach ( $this->_data as $key=>$value ) {
			// if ($key == 'icons') die('tried to load');
			$this->_data[$key] = isset( $meta[$key] ) ? $meta[$key][0] : null;
		}
		// $this->halt( State::DRAFT );

		return true;
	}

	/**
	 * Save function stores class data as Wordpress meta data
	 * @return boolean true on success and false on failure
	 *
	 */
	public function save() {
		if ( ! defined( 'WPINC' ) || ! $this->_post_id ) {
			return false;
		}
		$status = false;
		foreach ( $this->_data as $key=>$value ) {
			$status = update_post_meta($this->_post_id, $key, $value);
		}
		return $status;
	}

	/**
	 * Turns meta data labels into PHP useable data keys
	 * @param string $label the human readable wordpress label
	 * @return string the formated label used as an array has or object property
	 *
	 */
	public function formatAsKey( $label ) {
		$string = $label;
		$specialchars = ".,;<>?/-~!@#\$%^&*()+='\"";
		$string = strtolower( $string );
		$string = str_replace(array(" ", "-", "__"), array("_","_","_"), $string);
		$length = strlen($specialchars);
		for($i=0; $i<$length; $i++)
			$string = str_replace($specialchars[$i], '', $string);
		
		$string = str_replace("__", "_", $string);
		return $string;
	}

	/**
	 * Assign an array of variables to the object properties
	 *
	 */
	public function assign( $data ) {
		$this->perform( State::DRAFT );
		if ( $this->is(State::DRAFT) && is_array($data) )
		{
			$temp = array();
			foreach ( $data as $a=>$b )
			{
				$key = $this->formatAsKey($a);
				$temp[$key] = $b;
			}
			parent::assign( $temp );
			$this->halt( State::DRAFT );
		}
	}

	public function __sleep()
	{
		return $this->_data;
	}
}