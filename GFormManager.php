<?php 
namespace DevonSample;

use BlueFission\Net\HTTP;
use BlueFission\DevString;

require_once ( plugin_dir_path( __FILE__ ) . 'WPUpdateable.php' );

class GFormManager extends WPUpdateable {
	const CONFIG_META = '_form_config'; // you probably don't need this
	const SESSION_VAR = 'form_snapshot'; // server session variable

	protected $_data = array( // explicitly define each form field you will track
		'field_1'=>'',
		'field_2'=>'',
		'field_3'=>'',
		'field_4'=>'',
		'field_5'=>'',
	);

	public function snapshot() {
		$this->prepare();
		$this->font_declaration = $this->check_fonts();
		$info = serialize($this->_data);
		if ( !HTTP::session(self::SESSION_VAR, $info) ) {
			// TODO: Warn somebody
		}
	}

	public function develop() {
		$info = unserialize( HTTP::session(self::SESSION_VAR) );
		$this->fill_fields( $info );
		$this->assign($info);
	}

	public function load() { // you only need this if creating custom posts form the form
		// Get this post to also load in related gravity form data
		if ( parent::load() ) {

			$config_meta = self::CONFIG_META;

			$config = get_post_meta( $this->_post_id, $config_meta, true );

			$this->config($config);

			if ( !is_numeric($this->lead_id) ) {
				$this->lead_id = get_post_custom_values( 'lead_id' );
			}

			$lead = \GFAPI::get_entry($this->lead_id);

			if ( is_wp_error($lead) ) {
				return false;
			}
			$this->load_form_data( $lead, $lead['form_id'] );
		}
	}

	public function load_form_data( $lead, $form_id = '' ) {

		$form_id = is_numeric($form_id) ? $form_id : ( isset( $lead['form_id'] ) ? $lead['form_id'] : $lead['id'] );
		// No form id
		if ( !$form_id ) {
			return false;
		}

		$form = \GFAPI::get_form($form_id);

		// Invalid form ID
		if ( !$form ) {
			return false;
		}

		$file_fields = array();
		$url_field = array();

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
		$values['logo'] = $this->logo ? $this->logo : $values['logo']; // An image field being re-assign from post file data

		try {
			$this->assign($values);
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		if ( !$this->product_id && isset($lead['source_url']) ) { // only needed if used in conjunction with woocommerce fulfillment
			$this->product_id = url_to_postid( $lead['source_url'] );
		}

		return true;
	}

	private function fill_fields( &$data ) {
		//$message = new DevString($data['message']);

		// custom assign any field you want here. example:
		// $this->field_1 = "Something you want it to say";
		foreach ( $data as $key=>$value) {
			$data[$key] = html_entity_decode($value, ENT_QUOTES);
		}

		//print_r($data);
	}

	public function save() {
		$status = parent::save();

		$status = update_post_meta($this->_post_id, self::CONFIG_META, $this->_config);

		return $status;
	}

	public function get_data() {
		return $this->_data;
	}
}