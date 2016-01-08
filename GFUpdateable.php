<?php 
namespace DevonSample;
use BlueFission\Behavioral\Configurable;
use BlueFission\Exceptions\NotImplementedException;

require_once ( plugin_dir_path( __FILE__ ) . 'WPUpdateable.php' );

class GFUpdateable extends WPUpdateable {
	protected $post_id;

	/**
	 * Loads the matching meta data for the currently set form id
	 * @return boolean true on success and false on failure
	 */
	public function load() {
		if ( ! defined( 'WPINC' ) || !$this->_post_id || !class_exists('GFAPI')) {
			return false;
		}

		$lead = \GFAPI::get_entry($this->_post_id);
		$form = \GFAPI::get_form($lead['form_id']);

		$values = array();
		
		foreach ( $form['fields'] as $field ) {
			if ( isset($field["inputs"]) && is_array( $field['inputs'] ) ) {
				foreach ( $field['inputs'] as $input ) {
					// Extract best label
					$key = $input['label'] ? $input['label'] : \GFCommon::get_label($field, (string)$input["id"]);
					// Redundant formatting
					$key = strtolower( str_replace(array(' '), array('_'), $key) );

					$value = isset($lead[(string)$input['id']]) ? $lead[(string)$input['id']] : "";
					$values[$key] = htmlentities(stripslashes($value), ENT_QUOTES);
				}
			} elseif ( !rgar($field, 'displayOnly') ) {
				// Extract best label
				$key = isset( $field['adminLabel'] ) && $field['adminLabel'] != "" ? $field['adminLabel'] : ( $field['label'] ? $field['label'] : \GFCommon::get_label($field) );
				// More redundant formatting
				$key = strtolower( str_replace(array(' '), array('_'), $key) );
				
				$value = isset($lead[$field['id']]) ? $lead[$field['id']] : "";
				$values[$key] = htmlentities(stripslashes($value), ENT_QUOTES);
			}
		}

		try {
			$this->assign($values);
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}
		return true;
	}

	/**
	 * Save function stores class data as Wordpress meta data
	 * @return boolean true on success and false on failure
	 *
	 */

	public function save() {
		throw( new NotImplementedException() );
		if ( ! defined( 'WPINC' ) || !$this->_post_id || !class_exists('GFAPI')) {
			return false;
		}
		$lead = GFAPI::get_entry($this->_post_id);
		$form = GFAPI::get_form($lead['form_id']);

		$values = array();
		
		foreach ( $form['fields'] as $field ) {

			$key = $field['adminLabel'] ? $field['adminLabel'] : strtolower( $field['label'] );
			$value = $lead[$field['id']];

			$values[$key] = $value;
		}
		$success = GFAPI::update_entry( $this->_data, $this->_post_id );
		return $success;
	}
	
	static function select_box( $name = '', $form = '' ) {
		
		$name = $name ? $name : 'gravity_form';

		if ( class_exists('RGFormsModel') && method_exists('RGFormsModel', 'get_forms') ) {
			
			$forms = \RGFormsModel::get_forms( null, 'title' );
			echo "<select class='plugin_admin_form' name='" . $name . "' >";
			echo '<option value="">Choose a Form</option>';
	
			foreach( $forms as $formid ) {
				echo '<option '.(($formid->id == $form) ? 'selected="true"' : '').' value="' . $formid->id . '">' . $formid->title . '</option>';
			}
			echo '</select>';
		} else {
			echo "<input class='plugin_admin_form' name='" . $name . "' type='text' value='".esc_attr($form) ."' />";	
		}
	}
}