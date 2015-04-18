<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: Venus/header.php
| Author: PHP-Fusion Inc.
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/

echo "<section id='acp-header' class='clearfix' data-spy='affix' data-offset-top='0' data-offset-bottom='0'>\n";
echo "<div class='brand pull-left'>\n";
echo "<img src='".IMAGES."php-fusion-icon.png'>\n";
echo "<h4 class='brand-text'>Administrator</h4>\n";
echo "</div>\n";
echo "<nav>\n";
echo "<ul class='venus-toggler'>\n";
echo "<li><a id='toggle-canvas' class='pointer' style='border-left:none;'><i class='fa fa-ellipsis-v fa-lg'></i></a></li>\n";
echo "</ul>\n";
echo "<ul class='hidden-xs pull-right m-r-15'>\n";
if (count($enabled_languages) > 1) {
	echo "<li class='dropdown'><a class='dropdown-toggle pointer' data-toggle='dropdown' title='".$locale['282']."'><i class='fa fa-flag fa-lg'></i><span class='caret'></span></a>\n";
	echo "<ul class='dropdown-menu' role='lang-menu'>\n";
	foreach($language_opts as $languages) {
		echo "<li style='width:100%;'><a class='display-block' style='width:100%' href='".FUSION_REQUEST."&amp;lang=$languages'><img class='m-r-5' src='".BASEDIR."locale/$languages/$languages-s.png'> $languages</a></li>\n";
	}
	echo "</ul>\n";
	echo "</li>\n";
}

echo "<li><a title='".$locale['view']." ".$settings['sitename']."' href='".BASEDIR."'><i class='fa fa-home fa-lg'></i></a></li>\n";
echo "<li><a title='".$locale['message']."' href='".BASEDIR."messages.php'><i class='fa fa-inbox fa-lg'></i></a></li>\n";
echo "<li><a title='".$locale['settings']."' href='".ADMIN."settings_main.php".$aidlink."'><i class='fa fa-cog fa-lg'></i></a></li>\n";
echo "<li class='dropdown'><a class='dropdown-toggle pointer strong' data-toggle='dropdown'>".display_avatar($userdata, '18px', '', '', '')." ".$locale['logged'].$userdata['user_name']." <span class='caret'></span></a>\n";
echo "<ul class='dropdown-menu' role='menu'>\n";
echo "<li style='width:100%;'><a class='display-block' style='width:100%' href='".BASEDIR."edit_profile.php'>".$locale['edit']." ".$locale['profile']."</a></li>\n";
echo "<li style='width:100%;'><a class='display-block' style='width:100%' href='".BASEDIR."profile.php?lookup=".$userdata['user_id']."'>".$locale['view']." ".$locale['profile']."</a></li>\n";
echo "<li class='divider display-block'>\n</li>\n";
echo "<li style='width:100%;'><a class='display-block' style='width:100%' href='".FUSION_REQUEST."&amp;logout'>".$locale['admin-logout']."</a></li>\n";
echo "<li style='width:100%;'><a class='display-block' style='width:100%' href='".BASEDIR."index.php?logout=yes'>".$locale['logout']."</a></li>\n";
echo "</ul>\n";
echo "</li>\n";
echo "</ul>\n";
echo "</nav>\n";
echo "</section>\n";

add_to_head("<script src='".THEMES."admin_templates/Venus/includes/jquery.slimscroll.min.js'></script>");
add_to_jquery("
$('#adl').slimScroll({
        height: '950px',
        width: '100%'
    });
$('#toggle-canvas').bind('click', function(e) {
	$('#acp-left').toggleClass('in');
	setTimeout(function() {
		$('#acp-main').toggleClass('in');
		$('#admin-panel').toggleClass('in');
	}, 30);
	panel_state = $('#acp-left').hasClass('in');
	if (panel_state) {
		$.cookie('Venus', '1', {expires: 7});
	} else {
		$.cookie('Venus', '0', {expires: 7});
	 }
});
");

?>