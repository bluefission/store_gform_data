<?php 
namespace DevonSample;

use BlueFission\DevObject;

class WPUserData extends DevObject {
	protected $user_id;

	/**
	 * Sets the wordpress user id for this operation
	 * @param int $id the id for the related Wordpress user
	 */
	public function setID( $id ) {
		$this->_user_id = $id;
	}

	/**
	 * Loads the matching meta data for the currently set user id
	 * @return boolean true on success and false on failure
	 */
	public function load() {
		if ( ! defined( 'WPINC' ) || ! $this->_user_id ) {
			return false;
		}
		$meta = get_post_meta( $this->_user_id );
		foreach ( $this->_data as $key ) {
			$this->_data[$key] = $meta[$key];
		}
		return true;
	}

	/**
	 * Save function stores class data as Wordpress meta data
	 * @return boolean true on success and false on failure
	 *
	 */
	public function save() {
		if ( ! defined( 'WPINC' ) || ! $this->_user_id ) {
			return false;
		}
		$status = false;
		foreach ( $this->_data as $key=>$value ) {
			$status = update_user_meta($this->_user_id, $key, $value);
		}
		return $status;
	}
}