<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: news.php
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
require_once "../maincore.php";
pageAccess('N');
require_once THEMES."templates/admin_header.php";
include LOCALE.LOCALESET."admin/news.php";
add_to_breadcrumbs(array('link'=>FUSION_SELF.$aidlink, 'title'=>$locale['news_0000']));
if (isset($_POST['cancel'])) { redirect(FUSION_SELF.$aidlink); }
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['news_id']) && isnum($_GET['news_id'])) {
	$del_data['news_id'] = $_GET['news_id'];
	$result = dbquery("SELECT news_image, news_image_t1, news_image_t2 FROM ".DB_NEWS." WHERE news_id='".$del_data['news_id']."'");
	if (dbrows($result)) {
		$data = dbarray($result);
		if (!empty($data['news_image']) && file_exists(IMAGES_N.$data['news_image'])) {
			unlink(IMAGES_N.$data['news_image']);
		}
		if (!empty($data['news_image_t1']) && file_exists(IMAGES_N_T.$data['news_image_t1'])) {
			unlink(IMAGES_N_T.$data['news_image_t1']);
		}
		if (!empty($data['news_image_t2']) && file_exists(IMAGES_N_T.$data['news_image_t2'])) {
			unlink(IMAGES_N_T.$data['news_image_t2']);
		}
		$result = dbquery("DELETE FROM ".DB_NEWS." WHERE news_id='".$_POST['news_id']."'");
		$result = dbquery("DELETE FROM ".DB_COMMENTS."  WHERE comment_item_id='".$_POST['news_id']."' and comment_type='N'");
		$result = dbquery("DELETE FROM ".DB_RATINGS." WHERE rating_item_id='".$_POST['news_id']."' and rating_type='N'");
		dbquery_insert(DB_NEWS, $del_data, 'delete');
		addNotice('warning', $locale['news_0102']);
		redirect(FUSION_SELF.$aidlink);
	} else {
		redirect(FUSION_SELF.$aidlink);
	}
}
$allowed_pages = array('news', 'nform');
$_GET['section'] = isset($_GET['section']) && in_array($_GET['section'], $allowed_pages) ? $_GET['section'] : 'news';
$edit = (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['news_id']) && isnum($_GET['news_id'])) ? 1 : 0;

$master_title['title'][] = $locale['news_0000'];
$master_title['id'][] = 'news';
$master_title['icon'] = '';

$master_title['title'][] = $edit ? $locale['news_0003'] : $locale['news_0002'];
$master_title['id'][] = 'nform';
$master_title['icon'] = '';

$tab_active =  tab_active($master_title, $_GET['section'] , 1);

opentable($locale['news_0001']);
echo opentab($master_title, $tab_active, 'news', 1);
echo opentabbody($master_title['title'][0], 'news', $tab_active, 1);
news_listing();
echo closetabbody();
if ($_GET['section'] == 'nform') {
	add_to_breadcrumbs(array('link'=>'', 'title'=>$edit ? $locale['news_0003'] : $locale['news_0002']));
	echo opentabbody($master_title['title'][1], 'nform', $tab_active, 1);
	news_form();
	echo closetabbody();
}
echo closetab();
closetable();

if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['news_id'])) {
	add_to_jquery("
		// change the name of the second tab and activate it.
		$('#tab-nformAdd-News').text('".$locale['news_0003']."');
		$('#news a:last').tab('show');
		");
}

require_once THEMES."templates/footer.php";

function news_listing() {
	global $aidlink, $locale;
	echo "<div class='m-t-20'>\n";
	echo opencollapse('news-list');
	// uncategorized listing
	echo "<div class='panel panel-default'>\n";
	echo "<div class='panel-heading clearfix'>\n";
	echo "<div class='overflow-hide'>\n";
	echo "<span class='display-inline-block strong'><a ".collapse_header_link('news-list', '0', '0', 'm-r-10').">".$locale['news_0202']."</a></span>\n";
	echo "<span class='badge m-r-10'>".dbcount("(news_id)", DB_NEWS, "news_cat='0'")."</span>";
	echo "<span class='text-smaller mid-opacity'>".LANGUAGE."</span>";
	echo "</div>\n";
	echo "</div>\n"; // end panel heading
	echo "<div ".collapse_footer_link('news-list','0', '0').">\n";
	echo "<ul class='list-group p-15'>\n";
	$result2 = dbquery("SELECT news_id, news_subject, news_image_t1, news_news, news_draft FROM ".DB_NEWS." WHERE ".(multilang_table("NS") ? "news_language='".LANGUAGE."' AND " : "")."news_cat='0' ORDER BY news_draft DESC, news_sticky DESC, news_datestamp DESC");
	if (dbrows($result2) > 0) {
		while ($data2 = dbarray($result2)) {
			echo "<li class='list-group-item'>\n";
			echo "<div class='pull-left m-r-10'>\n";
			$img_thumb = ($data2['news_image_t1']) ? IMAGES_N_T.$data2['news_image_t1'] : IMAGES."imagenotfound70.jpg";
			echo thumbnail($img_thumb, '50px');
			echo "</div>\n";
			echo "<div class='overflow-hide'>\n";
			echo "<div><span class='strong text-dark'>".$data2['news_subject']."</span><br/>".fusion_first_words($data2['news_news'], '50')."</div>\n";
			echo "<a href='".FUSION_SELF.$aidlink."&amp;action=edit&amp;section=nform&amp;news_id=".$data2['news_id']."'>".$locale['edit']."</a> -\n";
			echo "<a href='".FUSION_SELF.$aidlink."&amp;action=delete&amp;news_id=".$data2['news_id']."' onclick=\"return confirm('".$locale['news_0251']."');\">".$locale['delete']."</a>\n";
			echo "</div>\n";
			echo "</li>\n";
		}
	} else {
		echo "<div class='panel-body text-center'>\n";
		echo $locale['news_0254'];
		echo "</div>\n";
	}
	// news listing.
	echo "</ul>\n";
	echo "</div>\n"; // panel container
	echo "</div>\n"; // panel default

	$result = dbquery("SELECT cat.news_cat_id, cat.news_cat_name, cat.news_cat_image, cat.news_cat_language, count(news.news_id) as news_count
				FROM ".DB_NEWS_CATS." cat
				LEFT JOIN ".DB_NEWS." news on news.news_cat = cat.news_cat_id
				".(multilang_table("NS") ? "WHERE news_cat_language='".LANGUAGE."'" : "")." GROUP BY news_cat_id ORDER BY news_cat_name");
	if (dbrows($result) > 0) {
		while ($data = dbarray($result)) {
			echo "<div class='panel panel-default'>\n";
			echo "<div class='panel-heading clearfix'>\n";
			echo "<div class='btn-group pull-right m-t-5'>\n";
			echo "<a class='btn btn btn-default' href='".ADMIN."news_cats.php".$aidlink."&amp;action=edit&amp;section=nform&amp;cat_id=".$data['news_cat_id']."'>".$locale['edit']."</a>";
			echo "<a class='btn btn-danger' href='".ADMIN."news_cats.php".$aidlink."&amp;action=delete&amp;cat_id=".$data['news_cat_id']."' onclick=\"return confirm('".$locale['news_0252']."');\"><i class='fa fa-trash'></i> ".$locale['delete']."</a>\n";
			echo "</div>\n";
			echo "<div class='overflow-hide p-r-10'>\n";
			echo "<span class='display-inline-block strong'><a ".collapse_header_link('news-list', $data['news_cat_id'], '0', 'm-r-10').">".$data['news_cat_name']."</a></span>\n";
			echo "<span class='badge m-r-10'>".$data['news_count']."</span>";
			echo "<span class='text-smaller mid-opacity'>".LANGUAGE."</span>";
			echo "</div>\n"; /// end overflow-hide
			echo "</div>\n"; // end panel heading
			echo "<div ".collapse_footer_link('news-list', $data['news_cat_id'], '0').">\n";
			echo "<ul class='list-group p-15'>\n";
			$result2 = dbquery("SELECT news_id, news_subject, news_image_t1, news_news, news_draft FROM ".DB_NEWS." ".(multilang_table("NS") ? "WHERE news_language='".LANGUAGE."' AND" : "WHERE")." news_cat='".$data['news_cat_id']."' ORDER BY news_draft DESC, news_sticky DESC, news_datestamp DESC");
			if (dbrows($result2) > 0) {
				while ($data2 = dbarray($result2)) {
					echo "<li class='list-group-item'>\n";
					echo "<div class='pull-left m-r-10'>\n";
					$img_thumb = ($data2['news_image_t1']) ? IMAGES_N_T.$data2['news_image_t1'] : IMAGES."imagenotfound70.jpg";
					echo thumbnail($img_thumb, '50px');
					echo "</div>\n";
					echo "<div class='overflow-hide'>\n";
					echo "<div><span class='strong text-dark'>".$data2['news_subject']."</span><br/>".fusion_first_words($data2['news_news'], '50')."</div>\n";
					echo "<a href='".FUSION_SELF.$aidlink."&amp;action=edit&amp;section=nform&amp;news_id=".$data2['news_id']."'>".$locale['edit']."</a> -\n";
					echo "<a href='".FUSION_SELF.$aidlink."&amp;action=delete&amp;section=nform&amp;news_id=".$data2['news_id']."' onclick=\"return confirm('".$locale['news_0251']."');\">".$locale['delete']."</a>\n";
					echo "</div>\n";
					echo "</li>\n";
				}
			} else {
				echo "<div class='panel-body text-center'>\n";
				echo $locale['news_0254'];
				echo "</div>\n";
			}
			// news listing.
			echo "</ul>\n";
			echo "</div>\n"; // panel container
			echo "</div>\n"; // panel default
		}
	}
	echo closecollapse();
	echo "</div>\n";
}

function news_form() {
	global $userdata, $locale, $settings, $aidlink, $defender;
	$language_opts = fusion_get_enabled_languages();
	$formaction = FUSION_SELF.$aidlink."&amp;section=nform";

	$data = array(
		'news_draft' => 0,
		'news_sticky' => 0,
		'news_news' => '',
		'news_datestamp' => time(),
		'news_extended' => '',
		'news_keywords' => '',
		'news_breaks' => 'n',
		'news_allow_comments' => 1,
		'news_allow_ratings' => 1,
		'news_language' => LANGUAGE,
		'news_visibility' => 0,
		'news_subject' => '',
		'news_start' => '',
		'news_end' => '',
		'news_cat'	=> 0,
		'news_image'	=> '',
		'news_ialign' => 'pull-left',
	);


	if ($settings['tinymce_enabled']) {
		echo "<script language='javascript' type='text/javascript'>advanced();</script>\n";
		$data['news_breaks'] = 'n';
	} else {
		require_once INCLUDES."html_buttons_include.php";
		$data['news_breaks'] = 'y';
	}


	if (isset($_POST['save'])) {

		$data = array(
			'news_id' => form_sanitizer($_POST['news_id'], 0, 'news_id'),
			'news_subject' => form_sanitizer($_POST['news_subject'], '', 'news_subject'),
			'news_cat' => form_sanitizer($_POST['news_cat'], 0, 'news_cat'),
			'news_name' =>  $userdata['user_id'],
			'news_news' =>	addslash(preg_replace("(^<p>\s</p>$)", "", $_POST['news_news'])),
			'news_extended' =>	addslash(preg_replace("(^<p>\s</p>$)", "", $_POST['news_extended'])),
			'news_keywords'	=>	form_sanitizer($_POST['news_keywords'], '', 'news_keywords'),
			'news_datestamp' => form_sanitizer($_POST['news_datestamp'], time(), 'news_datestamp'),
			'news_start' => form_sanitizer($_POST['news_start'], 0, 'news_start'),
			'news_end' => form_sanitizer($_POST['news_end'], 0, 'news_end'),
			'news_visibility' => form_sanitizer($_POST['news_visibility'], 0, 'news_visibility'),
			'news_draft' => isset($_POST['news_draft']) ? "1" : "0",
			'news_sticky' => isset($_POST['news_sticky']) ? "1" : "0",
			'news_allow_comments' => isset($_POST['news_allow_comments']) ? "1" : "0",
			'news_allow_ratings' => isset($_POST['news_allow_ratings']) ? "1" : "0",
			'news_language' => form_sanitizer($_POST['news_language'], '', 'news_language')
		);

		if (isset($_FILES['news_image'])) {
			$upload = form_sanitizer($_FILES['news_image'], '', 'news_image');
			if (!empty($upload)) {
				$data['news_image'] = $upload['image_name'];
				$data['news_image_t1'] = $upload['thumb1_name'];
				$data['news_image_t2'] = $upload['thumb2_name'];
				$data['news_ialign'] = (isset($_POST['news_ialign']) ? $_POST['news_ialign'] : "pull-left");
			} else {
				$data['news_image'] = (isset($_POST['news_image']) ? $_POST['news_image'] : "");
				$data['news_image_t1'] = (isset($_POST['news_image_t1']) ? $_POST['news_image_t1'] : "");
				$data['news_image_t2'] = (isset($_POST['news_image_t2']) ? $_POST['news_image_t2'] : "");
				$data['news_ialign'] = (isset($_POST['news_ialign']) ? $_POST['news_ialign'] : "pull-left");
			}
		}

		if ($settings['tinymce_enabled'] != 1) {
			$data['news_breaks'] = isset($_POST['line_breaks']) ? "y" : "n";
		} else {
			$data['news_breaks'] = "n";
		}

		if ($data['news_sticky'] == "1") $result = dbquery("UPDATE ".DB_NEWS." SET news_sticky='0' WHERE news_sticky='1'"); // reset other sticky
		// delete image
		if (isset($_POST['del_image'])) {
			if (!empty($data['news_image']) && file_exists(IMAGES_N.$data['news_image'])) {
				unlink(IMAGES_N.$data['news_image']);
			}
			if (!empty($data['news_image_t1']) && file_exists(IMAGES_N_T.$data['news_image_t1'])) {
				unlink(IMAGES_N_T.$data['news_image_t1']);
			}
			if (!empty($data['news_image_t2']) && file_exists(IMAGES_N_T.$data['news_image_t2'])) {
				unlink(IMAGES_N_T.$data['news_image_t2']);
			}
			$data['news_image'] = "";
			$data['news_image_t1'] = "";
			$data['news_image_t2'] = "";
		}

		$rows = dbcount("('news_id')", DB_NEWS, "news_id='".$data['news_id']."'");
		if ($rows >0) {
			dbquery_insert(DB_NEWS, $data, 'update');
			addNotice('info', $locale['news_0101']);
			if (!defined('FUSION_NULL')) redirect(FUSION_SELF.$aidlink."&amp;status=su");
		} else {
			dbquery_insert(DB_NEWS, $data, 'save');
			addNotice('sucess', $locale['news_0100']);
			if (!defined('FUSION_NULL')) redirect(FUSION_SELF.$aidlink."&amp;status=sn");
		}
	}

	$result = dbquery("SELECT news_cat_id, news_cat_name FROM ".DB_NEWS_CATS." ".(multilang_table("NS") ? "WHERE news_cat_language='".LANGUAGE."'" : "")." ORDER BY news_cat_name");
	$news_cat_opts = array();
	$news_cat_opts['0'] = $locale['news_0202'];
	if (dbrows($result)) {
		while ($odata = dbarray($result)) {
			$news_cat_opts[$odata['news_cat_id']] = $odata['news_cat_name'];
		}
	}

	if ((isset($_GET['action']) && $_GET['action'] == "edit") && (isset($_POST['news_id']) && isnum($_POST['news_id'])) || (isset($_GET['news_id']) && isnum($_GET['news_id']))) {
		$result = dbquery("SELECT * FROM ".DB_NEWS." WHERE news_id='".(isset($_POST['news_id']) ? $_POST['news_id'] : $_GET['news_id'])."'");
		if (dbrows($result)) {
			$data = dbarray($result);
			$formaction = FUSION_SELF.$aidlink."&amp;section=nform&amp;action=edit&amp;news_id=".$data['news_id'];
		} else {
			redirect(FUSION_SELF.$aidlink);
		}
	}

	if (isset($_POST['preview'])) {
		$data['news_subject'] = form_sanitizer($_POST['news_subject'], '', 'news_subject');
		$data['news_cat'] = isnum($_POST['news_cat']) ? $_POST['news_cat'] : "0";
		$data['news_language'] = form_sanitizer($_POST['news_language'], '', 'news_language');
		$data['news_news'] = phpentities(stripslash($_POST['news_news']));
		$data['news_news'] = str_replace("src='".str_replace("../", "", IMAGES_N), "src='".IMAGES_N, stripslash($_POST['news_news']));
		$data['news_extended'] = '';
		if ($_POST['news_extended']) {
			$data['news_extended'] = phpentities(stripslash($_POST['news_extended']));
			$data['news_extended'] = str_replace("src='".str_replace("../", "", IMAGES_N), "src='".IMAGES_N, stripslash($_POST['news_extended']));
		}
		$data['news_keywords'] = form_sanitizer($_POST['news_keywords'], '', 'news_keywords');
		$data['news_breaks'] = "";
		if (isset($_POST['line_breaks'])) {
			$data['news_breaks'] = " 1";
			$data['news_news'] = nl2br($data['news_news']);
			if ($data['news_extended']) {
				$data['news_extended'] = nl2br($data['news_extended']);
			}
		}
		$data['news_start'] = (isset($_POST['news_start']) && $_POST['news_start']) ? $_POST['news_start'] : '';
		$data['news_end'] = (isset($_POST['news_end']) && $_POST['news_end']) ? $_POST['news_end'] : '';
		$data['news_image'] = isset($_POST['news_image']) ? $_POST['news_image'] : '';
		$data['news_image_t1'] = (isset($_POST['news_image_t1']) ? $_POST['news_image_t1'] : "");
		$data['news_image_t2'] = (isset($_POST['news_image_t2']) ? $_POST['news_image_t2'] : "");
		$data['news_ialign'] = (isset($_POST['news_ialign']) ? $_POST['news_ialign'] : "pull-left");
		$data['news_visibility'] = isnum($_POST['news_visibility']) ? $_POST['news_visibility'] : "0";
		$data['news_draft'] = isset($_POST['news_draft']) ? " 1" : "";
		$data['news_sticky'] = isset($_POST['news_sticky']) ? " 1" : "";
		$data['news_allow_comments'] = isset($_POST['news_allow_comments']) ? " 1" : "";
		$data['news_allow_ratings'] = isset($_POST['news_allow_ratings']) ? " 1" : "";
		$data['news_datestamp'] = isset($_POST['news_datestamp']) ? $_POST['news_datestamp'] : '';
		if (!defined('FUSION_NULL')) {
			echo openmodal('news_preview', 'News Preview');
			echo $data['news_news'];
			echo "<hr/>\n";
			if (isset($data['news_extended'])) {
				echo $data['news_extended'];
			}
			echo closemodal();
		}
	}

	echo "<div class='m-t-20'>\n";
	echo openform('inputform', 'post', $formaction, array('enctype' => 1, 'max_tokens' => 1));
	echo "<div class='row'>\n";
	echo "<div class='col-xs-12 col-sm-12 col-md-7 col-lg-8'>\n";
	echo form_text('news_subject', $locale['news_0200'], $data['news_subject'], array('required' => 1, 'max_length' => 200, 'error_text' => $locale['news_0250']));
	// move keywords here because it's required
	echo form_select('news_keywords', $locale['news_0205'], array(), $data['news_keywords'], array('max_length' => 320, 'width'=>'100%', 'error_text' => $locale['news_0255'], 'tags'=>1, 'multiple' => 1));
	echo "<div class='pull-left m-r-10 display-inline-block'>\n";
	echo form_datepicker('news_start', $locale['news_0206'], $data['news_start'], array('placeholder' => $locale['news_0208']));
	echo "</div>\n<div class='pull-left m-r-10 display-inline-block'>\n";
	echo form_datepicker('news_end', $locale['news_0207'], $data['news_end'], array('placeholder' => $locale['news_0208']));
	echo "</div>\n";
	echo "</div>\n";
	echo "<div class='col-xs-12 col-sm-12 col-md-5 col-lg-4'>\n";
	openside('');
	echo form_select_tree("news_cat", $locale['news_0201'], $data['news_cat'], array("parent_value" => $locale['news_0202'], "query" => (multilang_table("NS") ? "WHERE news_cat_language='".LANGUAGE."'" : "")), DB_NEWS_CATS, "news_cat_name", "news_cat_id", "news_cat_parent");
	echo form_button('cancel', $locale['cancel'], $locale['cancel'], array('class' => 'btn-default m-r-10'));
	echo form_button('save', $locale['news_0241'], $locale['news_0241'], array('class' => 'btn-success', 'icon'=>'fa fa-square-check-o'));
	closeside();
	echo "</div>\n</div>\n";

	// second row
	echo "<div class='row'>\n";
	echo "<div class='col-xs-12 col-sm-12 col-md-7 col-lg-8'>\n";
	openside('');
	if ($data['news_image'] != "" && $data['news_image_t1'] != "") {
		echo "<label><img src='".IMAGES_N_T.$data['news_image_t1']."' alt='".$locale['news_0216']."' /><br />\n";
		echo "<input type='checkbox' name='del_image' value='y' /> ".$locale['delete']."</label>\n";
		echo "<input type='hidden' name='news_image' value='".$data['news_image']."' />\n";
		echo "<input type='hidden' name='news_image_t1' value='".$data['news_image_t1']."' />\n";
		echo "<input type='hidden' name='news_image_t2' value='".$data['news_image_t2']."' />\n";
		$options = array('pull-left'=>$locale['left'], 'news-img-center'=>$locale['center'], 'pull-right'=>$locale['right']);
		echo form_select('news_ialign', $locale['news_0218'], $options, $data['news_ialign']);
	} else {
		$file_input_options = array(
			'max_width' => $settings['news_photo_max_w'],
			'max_height' => $settings['news_photo_max_h'],
			'max_byte' => $settings['news_photo_max_b'],
			// set thumbnail
			'thumbnail' => 1,
			'thumbnail_w' => $settings['news_thumb_w'],
			'thumbnail_h' => $settings['news_thumb_h'],
			'thumbnail_folder' => 'thumbs',
			'delete_original' => 0,
			// set thumbnail 2 settings
			'thumbnail2' => 1,
			'thumbnail2_w' => $settings['news_photo_w'],
			'thumbnail2_h' => $settings['news_photo_h'],
			'type' => 'image'
		);
		echo form_fileinput($locale['news_0216'], 'news_image', 'news_image', IMAGES_N, '', $file_input_options);
		echo "<div class='small m-b-10'>".sprintf($locale['news_0217'], parsebytesize($settings['news_photo_max_b']))."</div>\n";
		$options = array('pull-left'=>$locale['left'], 'news-img-center'=>$locale['center'], 'pull-right'=>$locale['right']);
		echo form_select('news_ialign', $locale['news_0218'], $options, $data['news_ialign']);
	}
	$fusion_mce = array();
	if (!$settings['tinymce_enabled']) {
		$fusion_mce = array('preview' => 1, 'html' => 1, 'autosize' => 1, 'form_name' => 'inputform');
	}
	closeside();
	echo form_textarea('news_news', $locale['news_0203'], $data['news_news'], $fusion_mce);
	echo form_textarea('news_extended', $locale['news_0204'], $data['news_extended'], $fusion_mce);
	echo "</div>\n<div class='col-xs-12 col-sm-12 col-md-5 col-lg-4'>\n";
	openside('');
	if (multilang_table("NS")) {
		echo form_select('news_language', $locale['global_ML100'], $language_opts, $data['news_language'], array('placeholder' => $locale['choose'], 'width' => '100%'));
	} else {
		echo form_hidden('', 'news_language', 'news_langugage', $data['news_language']);
	}
	echo form_hidden('', 'news_datestamp', 'news_datestamp', $data['news_datestamp']);
	echo form_select('news_visibility', $locale['news_0209'], getgroupOpts(), $data['news_visibility'], array('placeholder' => $locale['choose'], 'width' => '100%'));
	closeside();
	openside('');
	if ($settings['comments_enabled'] == "0" || $settings['ratings_enabled'] == "0") {
		$sys = "";
		if ($settings['comments_enabled'] == "0" && $settings['ratings_enabled'] == "0") {
			$sys = $locale['comments_ratings'];
		} elseif ($settings['comments_enabled'] == "0") {
			$sys = $locale['comments'];
		} else {
			$sys = $locale['ratings'];
		}
		echo "<span style='color:red;font-weight:bold;margin-right:5px;'>*</span>".sprintf($locale['news_0253'], $sys)."</span><br/>\n";
	}
	echo "<label><input type='checkbox' name='news_draft' value='yes'".($data['news_draft'] ? "checked='checked'" : "")." /> ".$locale['news_0210']."</label><br />\n";
	echo "<label><input type='checkbox' name='news_sticky' value='yes'".($data['news_sticky'] ? "checked='checked'" : "")."  /> ".$locale['news_0211']."</label><br />\n";
	if ($settings['tinymce_enabled'] != 1) {
		echo "<label><input type='checkbox' name='line_breaks' value='yes'".($data['news_breaks'] ? "checked='checked'" : "")." /> ".$locale['news_0212']."</label><br />\n";
	}
	echo "<label><input type='checkbox' name='news_allow_comments' value='yes' onclick='SetRatings();'".($data['news_allow_comments'] ? "checked='checked'" : "")." /> ".$locale['news_0213']."</label><br/>";
	echo "<label><input type='checkbox' name='news_allow_ratings' value='yes'".($data['news_allow_ratings'] ? "checked='checked'" : "")." /> ".$locale['news_0214']."</label>";
	closeside();
	if (isset($_GET['action']) && isset($_GET['news_id']) && isnum($_GET['news_id']) || (isset($_POST['preview']) && (isset($_POST['news_id']) && isnum($_POST['news_id']))) || (isset($_GET['news_id']) && isnum($_GET['news_id']))) {
		$news_id = isset($_GET['news_id']) && isnum($_GET['news_id']) ? $_GET['news_id'] : '';
		echo form_hidden('', 'news_id', 'news_id', $news_id);
	}
	echo "</div>\n</div>\n";
	echo form_button('preview', $locale['news_0240'], $locale['news_0240'], array('class' => 'btn-default m-r-10'));
	echo form_button('save', $locale['news_0241'], $locale['news_0241'], array('class' => 'btn-success', 'icon'=>'fa fa-square-check-o'));
	echo closeform();
	echo "</div>\n";
}
