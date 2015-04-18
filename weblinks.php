<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: weblinks.php
| Author: Nick Jones (Digitanium)
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
require_once "maincore.php";
if (!db_exists(DB_WEBLINKS)) {
	$_GET['code'] = 404;
	require_once __DIR__.'/error.php';
	exit;
}
require_once THEMES."templates/header.php";
include LOCALE.LOCALESET."weblinks.php";
include THEMES."templates/global/weblinks.php";
if (isset($_GET['weblink_id']) && isnum($_GET['weblink_id'])) {
	$res = 0;
	$data = dbarray(dbquery("SELECT weblink_url,weblink_cat, weblink_visibility FROM ".DB_WEBLINKS." WHERE weblink_id='".$_GET['weblink_id']."'"));
	if (checkgroup($data['weblink_visibility'])) {
		$res = 1;
		$result = dbquery("UPDATE ".DB_WEBLINKS." SET weblink_count=weblink_count+1 WHERE weblink_id='".$_GET['weblink_id']."'");
		redirect($data['weblink_url']);
	}
	if ($res == 0) {
		redirect(FUSION_SELF);
	}
}
add_to_title($locale['global_200'].$locale['400']);
add_to_breadcrumbs(array('link'=>BASEDIR.'weblinks.php', 'title'=>$locale['400']));

if (!isset($_GET['cat_id']) || !isnum($_GET['cat_id'])) {
	$info['item'] = array();
	$result = dbquery("SELECT wc.weblink_cat_id, wc.weblink_cat_name, wc.weblink_cat_description 
	FROM ".DB_WEBLINK_CATS." wc 
	".(multilang_table("WL") ? "WHERE weblink_cat_language='".LANGUAGE."'" : "")."
	ORDER BY weblink_cat_name");

	$rows = dbrows($result);
	$info['weblink_cat_rows'] = $rows;
	if ($rows != 0) {
		while ($data = dbarray($result)) {
			$itemcount = dbcount("(weblink_id)", DB_WEBLINKS, "weblink_cat='".$data['weblink_cat_id']."' AND ".groupaccess('weblink_visibility'));
			if ($itemcount > 0) {
				$data['weblink_item'] = array('link'=>FUSION_SELF."?cat_id=".$data['weblink_cat_id'], 'name'=>$data['weblink_cat_name']);
				$data['weblink_count'] = $itemcount;
				$info['item'][$data['weblink_cat_id']] = $data;
			}
		}
	}
	render_weblinks($info);

} elseif (isset($_GET['cat_id']) && isnum($_GET['cat_id'])) {
	$info = array();
	$info['item'] = array();
	$result = dbquery("SELECT weblink_cat_name, weblink_cat_sorting FROM
	".DB_WEBLINK_CATS." ".(multilang_table("WL") ? "WHERE weblink_cat_language='".LANGUAGE."' AND" : "WHERE")." weblink_cat_id='".$_GET['cat_id']."'");
	if (dbrows($result) != 0) {
		$cdata = dbarray($result);
		$info = $cdata;
		add_to_title($locale['global_201'].$cdata['weblink_cat_name']);
		add_to_breadcrumbs(array('link'=>'', 'title'=>$cdata['weblink_cat_name']));

		$max_rows = dbcount("(weblink_id)", DB_WEBLINKS, "weblink_cat='".$_GET['cat_id']."' AND ".groupaccess('weblink_visibility'));
		$_GET['rowstart'] = isset($_GET['rowstart']) && isnum($_GET['rowstart']) && $_GET['rowstart']<= $max_rows ? $_GET['rowstart'] : 0;
		if ($max_rows != 0) {
			$result = dbquery("SELECT weblink_id, weblink_name, weblink_description, weblink_datestamp, weblink_count FROM ".DB_WEBLINKS." WHERE 
			".groupaccess('weblink_visibility')." AND weblink_cat='".$_GET['cat_id']."' ORDER BY ".$cdata['weblink_cat_sorting']." LIMIT ".$_GET['rowstart'].",".$settings['links_per_page']);
			$numrows = dbrows($result);
			$info['weblink_rows'] = $numrows;
			$info['page_nav'] = $max_rows > $settings['links_per_page'] ? makepagenav($_GET['rowstart'], $settings['links_per_page'], $rows, 3, BASEDIR."weblinks.php?cat_id=".$_GET['cat_id']."&amp;") : 0;
			if (dbrows($result)>0) {
				while ($data = dbarray($result)) {
					$data['new'] = ($data['weblink_datestamp']+604800 > time()+($settings['timeoffset']*3600)) ? 1 : 0;
					$data['weblink'] = array('link'=>BASEDIR."weblinks.php?cat_id=".$_GET['cat_id']."&amp;weblink_id=".$data['weblink_id'], 'name'=>$data['weblink_name']);
					$info['item'][$data['weblink_id']] = $data;
				}
			}
			render_weblinks_item($info);
		}
	} else {
		redirect(FUSION_SELF);
	}
}
require_once THEMES."templates/footer.php";
?>