<?php

use BlueFission\Net\HTTP;

/**
 * Sample
 *
 * @package   WhatYouNeed
 * @author    Devon Scott <dscott@bluefission.com>
 * @license   GPL-2.0+
 * @link      http://bluefission.com
 * @copyright 2015 Devon Scott, BlueFission.com
 */

require_once ( realpath(plugin_dir_path( __FILE__ ) . 'GFormManager' ));

class GFormSessionPlugin {

	protected function __construct() { // init this however you like
		
		// Gravity Forms hooks
		add_filter( 'gform_pre_render', array( $this, 'populate_form') );
		add_action( 'gform_post_process', array( $this, 'load_save'), 10, 3 );
		add_filter( 'gform_after_submission', array( $this, 'process_form'), 10, 2 );
	}

	public function populate_form( $form ) {
		global $post;
		$gform = new DevonSample\GFormManager( );
		$gform->develop();

		$fieldnames = array();
		
		foreach($form["fields"] as &$field) {
			$key = isset( $field['adminLabel'] ) && $field['adminLabel'] != "" ? $field['adminLabel'] : strtolower( $field['label'] );
			$key = strtolower( str_replace(array(' '), array('_'), $key) );
			$field_value = $gform->$key;
			
			$inputs = array();
	        if ( is_array( $field['inputs'] ) ) {
				foreach ( $field['inputs'] as &$input ) {
					$key = $input['label'];
					$key = sanitize_title( $key );
					$key = strtolower( str_replace(array(' ', '-'), array('_','_'), $key) );
			
					$input["content"] = $gform->$key;
					$input["defaultValue"] = $gform->$key;
			
					$inputs[(string)$input["id"]] = $gform->$key;
				}
			}

			$value = ( count($inputs) && !$field_value ) ? $inputs : $field_value;
			if ( is_array($value) ) {
	        	$field["content"] = implode(' ', $value);
	        } else {
	        	$field["content"] = $value;
	        }
	        $field["defaultValue"] = $value;
	    }
	    return $form;
	}

	public function load_save($form, $page, $source) { 
		//print_r($form);
		$target_path = RGFormsModel::get_upload_path($form["id"]) . "/tmp/";
		$str_start = strpos($target_path, '/wp-content/');

		$gform = new DevonSample\GFormManager( );
		$gform->develop();

		$path = substr($target_path, $str_start);
        
		if ( isset($form['fields']) ) {
			foreach ( $form['fields'] as $field ) {
				if ( isset($field['type']) && $field['type'] == 'fileupload') {
					$input_name = 'input_'.$field['id'];
					$key = isset( $field['adminLabel'] ) && $field['adminLabel'] != "" ? $field['adminLabel'] : strtolower( $field['label'] );
					$key = $gform->formatAsKey($key);
					$file = RGFormsModel::get_temp_filename($form["id"], $input_name);
					if ( $file["temp_filename"] && file_exists($target_path.$file["temp_filename"]) ) {
						$gform->field($key, $path . $file["temp_filename"]);
					}
				}
			}
			$gform->snapshot();
		}
	}

	public function process_form( $entry, $form ) {
		// clever way to clear the session to blank
		$gform = new DevonSample\GFormManager( );
		$gform->snapshot();
	}
}