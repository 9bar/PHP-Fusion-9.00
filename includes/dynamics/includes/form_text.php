<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: form_text.php
| Author: Frederick MC Chan (Hien)
| Co-Author: Dan C. (JoiNNN)
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/

/** 
 * Generates a text input
 * TODO: Document each option
 *
 * Generates the HTML for a textbox or password input
 *
 * @param string $input_name Name of the input, by
 * default it's also used as the ID for the input
 * @param string $label The label
 * @param string $input_value The value to be displayed
 * in the input, usually a value from DB prev. saved
 * @param array  $options Various options
 * @return string
 */
function form_text($input_name, $label = "", $input_value = "", array $options = array()) {
	global $defender, $locale;

	$html = "";

	// No need for isset() checks, an insufficient args error will pop if you don't pass a value
	$input_name = stripinput($input_name);

	//var_dump($options);
	$valid_types = array('text', 'number', 'password', 'email', 'url');

	$options += array(
		'type'				=> !empty($options['type']) && in_array($options['type'], $valid_types) ? $options['type'] : 'text',
		'required'			=> !empty($options['required']) && $options['required'] == 1 ? 1 : 0,
		'safemode'			=> !empty($options['safemode']) && $options['safemode'] == 1 ? 1 : 0,
		'regex'				=> !empty($options['regex']) ? $options['regex'] : FALSE,
		'callback_check'	=> !empty($options['callback_check']) ? $options['callback_check'] : FALSE,
		'input_id'			=> !empty($options['input_id']) ? $options['input_id'] : $input_name,
		'placeholder'		=> !empty($options['placeholder']) ? $options['placeholder'] : '',
		'deactivate'		=> !empty($options['deactivate']) && $options['deactivate'] == 1 ? 1 : 0,
		'width'				=> !empty($options['width']) ? $options['width'] : '100%',
		'class'				=> !empty($options['class']) ? $options['class'] : '',
		'inline'			=> !empty($options['inline']) ? $options['inline'] : '',
		'max_length'		=> !empty($options['max_length']) ? $options['max_length'] : '200',
		'icon'				=> !empty($options['icon']) ? $options['icon'] : '',
		'autocomplete_off'	=> !empty($options['autocomplete_off']) && $options['autocomplete_off'] == 1 ? 1 : 0,
		'tip'				=> !empty($options['tip']) ? $options['tip'] : '',
		'append_button'		=> !empty($options['append_button']) ? $options['append_button'] : '',
		'append_value'		=> !empty($options['append_value']) ? $options['append_value'] : '<i class="entypo search"></i>',
		'append_form_value' => !empty($options['append_form_value']) ? $options['append_form_value'] : '',
		'append_size'		=> !empty($options['append_size']) ? $options['append_size'] : '',
		'append_class'		=> !empty($options['append_class']) ? $options['append_class'] : 'btn-default',
		'append_type'		=> !empty($options['append_type']) ? $options['append_type'] : 'submit',
		'prepend_button'	=> !empty($options['prepend_button']) ? $options['prepend_button'] : '',
		'prepend_value'		=> !empty($options['prepend_value']) ? $options['prepend_value'] : '<i class="entypo search"></i>',
		'prepend_form_value'=> !empty($options['prepend_form_value']) ? $options['prepend_form_value'] : '',
		'prepend_size'		=> !empty($options['prepend_size']) ? $options['prepend_size'] : '',
		'prepend_class'		=> !empty($options['prepend_class']) ? $options['prepend_class'] : 'btn-default',
		'prepend_type'		=> !empty($options['prepend_type']) ? $options['prepend_type'] : 'submit',
		'error_text'		=> ''
	);

	// Error messages based on settings
	if ($options['type'] == 'password') {
		$options['error_text'] = empty($options['error_text']) ? $locale['error_input_password'] : $options['error_text'];
	} elseif ($options['type'] == 'email') {
		$options['error_text'] = empty($options['error_text']) ? $locale['error_input_email'] : $options['error_text'];
	} elseif ($options['type'] == 'number') {
		$options['error_text'] = empty($options['error_text']) ? $locale['error_input_number'] : $options['error_text'];
	} elseif ($options['type'] == 'url') {
		$options['error_text'] = empty($options['error_text']) ? $locale['error_input_url'] : $options['error_text'];
	} elseif ($options['regex']) {
		$options['error_text'] = empty($options['error_text']) ? $locale['error_input_regex'] : $options['error_text'];
	} elseif ($options['safemode']) {
		$options['error_text'] = empty($options['error_text']) ? $locale['error_input_safemode'] : $options['error_text'];
	} else {
		$options['error_text'] = empty($options['error_text']) ? $locale['error_input_default'] : $options['error_text'];
	}

	$error_class = $defender->inputHasError($input_name) ? "has-error " : "";

	$html .= "<div id='".$options['input_id']."-field' class='form-group ".$error_class.$options['class']." ".($options['icon'] ? 'has-feedback' : '')."'  ".($options['width'] && !$label ? "style='width: ".$options['width']." !important;'" : '').">\n";
	$html .= ($label) ? "<label class='control-label ".($options['inline'] ? "col-xs-12 col-sm-3 col-md-3 col-lg-3 p-l-0" : '')."' for='".$options['input_id']."'>$label ".($options['required'] ? "<span class='required'>*</span>" : '')." ".($options['tip'] ? "<i class='pointer fa fa-question-circle' title='".$options['tip']."'></i>" : '')."</label>\n" : "";
	$html .= ($options['inline']) ? "<div class='col-xs-12 col-sm-9 col-md-9 col-lg-9'>\n" : "";
	$html .= ($options['append_button'] || $options['prepend_button']) ? "<div class='input-group'>\n" : "";
	$html .= ($options['prepend_button']) ? "<span class='input-group-btn'>\n<button id='".$options['input_id']."-prepend-btn' name='p-submit-".$options['input_id']."' type='".$options['prepend_type']."' value='".$options['prepend_form_value']."' class='btn ".$options['prepend_size']." ".$options['prepend_class']."'>".$options['prepend_value']."</button></span>" : "";
	$html .= "<input type='".($options['type'] == "password" ? "password" : "text")."' data-type='".$options['type']."' class='form-control textbox' ".($options['width'] ? "style='width:".$options['width'].";'" : '')." ".($options['max_length'] ? "maxlength='".$options['max_length']."'" : '')." name='$input_name' id='".$options['input_id']."' value='$input_value' placeholder='".$options['placeholder']."' ".($options['autocomplete_off'] ? "autocomplete='off'" : '')." ".($options['deactivate'] ? 'readonly' : '').">";
	$html .= ($options['append_button']) ? "<span class='input-group-btn'><button id='".$options['input_id']."-append-btn' name='p-submit-".$options['input_id']."' type='".$options['append_type']."' value='".$options['append_form_value']."' class='btn ".$options['append_size']." ".$options['append_class']."'>".$options['append_value']."</button></span>" : "";
	$html .= ($options['icon']) ? "<div class='form-control-feedback' style='top:0;'><i class='glyphicon ".$options['icon']."'></i></div>\n" : "";
	$html .= ($options['append_button'] || $options['prepend_button']) ? "</div>\n" : "";
	$html .= (($options['required'] == 1 && $defender->inputHasError($input_name)) || $defender->inputHasError($input_name)) ? "<div id='".$options['input_id']."-help' class='label label-danger p-5 display-inline-block'>".$options['error_text']."</div>" : "";
	$html .= ($options['inline']) ? "</div>\n" : "";
	$html .= "</div>\n";

	// Add input settings in the SESSION
	$defender->add_field_session(array(
			'input_name'	=> $input_name,
			'title'			=> $input_name, // Line 792 of defender.inc.php required this
			'id'			=> $options['input_id'], // Line 793 of defender.inc.php required this
			'type'			=> $options['type'],
			'required'		=> $options['required'],
			'safemode'		=> $options['safemode'],
			'regex'			=> $options['regex'],
			'callback_check'=> $options['callback_check']
		));

	// This should affect all number inputs by type, not by ID
	if ($options['type'] == 'number' && !defined('NUMBERS_ONLY_JS')) {
		define('NUMBERS_ONLY_JS', TRUE);
		add_to_jquery("$('input[data-type=number]').keypress(function(e) {
		var key_codes = [46, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 0, 8];
		if (!($.inArray(e.which, key_codes) >= 0)) { e.preventDefault(); }
		});\n");
	}

	//var_dump($_SESSION['form_fields'][$_SERVER['PHP_SELF']][$input_name]);

	return $html;
}

?>