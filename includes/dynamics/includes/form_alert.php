<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: form_alert.php
| Author: Frederick MC CHan (Hien)
| Co-Author : Tyler Hurlbut
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
function form_alert($title, $text, $array = FALSE) {
	// <a href="#" class="alert-link">...</a>
	if (isset($title) && ($title !== "")) {
		$title = stripinput($title);
	} else {
		$title = "";
	}
	//if (isset($text) && ($text !=="")) { $text = stripinput($text); } else { $text = ""; }
	if (!is_array($array)) {
		$class = '';
		$dismiss = '';
	} else {
		$class = (array_key_exists('class', $array)) ? $array['class'] : "";
		$dismiss = (array_key_exists('dismiss', $array)) ? $array['dismiss'] : "";
	}
	if ($dismiss == "1") {
		$html = "<div class='alert alert-dismissable $class'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button><strong>$title</strong> $text</div>";
	} else {
		$html = "<div class='alert $class'><strong>$title</strong> $text</div>";
	}
	add_to_jquery("
    $('div.alert a').addClass('alert-link');
    ");
	return $html;
}

?>