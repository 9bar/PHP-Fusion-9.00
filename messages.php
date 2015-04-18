<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: messages.php
| Author: Nick Jones (Digitanium)
| Co-Author: Frederick MC Chan (Hien)
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
if (!iMEMBER) {	redirect("index.php"); }

// Saving options
if (isset($_POST['save_options'])) {
	$pm_email_notify = isset($_POST['pm_email_notify']) && isnum($_POST['pm_email_notify']) ? "1" : "0";
	$pm_save_sent = isset($_POST['pm_save_sent']) && isnum($_POST['pm_save_sent']) ? "1" : "0";
	$result = dbquery("INSERT INTO ".DB_MESSAGES_OPTIONS." (user_id, pm_email_notify, pm_save_sent, pm_inbox, pm_savebox, pm_sentbox)
						VALUES ('".$userdata['user_id']."', '$pm_email_notify', '$pm_save_sent', '0', '0', '0')
						ON DUPLICATE KEY UPDATE pm_email_notify='$pm_email_notify', pm_save_sent='$pm_save_sent'");
	redirect(FUSION_SELF."?folder=inbox");
}
define('RIGHT_OFF', 1);
require_once THEMES."templates/header.php";
include LOCALE.LOCALESET."messages.php";
include THEMES."templates/global/messages.php";

add_to_title($locale['global_200'].$locale['400']);

$msg_settings = dbarray(dbquery("SELECT * FROM ".DB_MESSAGES_OPTIONS." WHERE user_id='0'"));

// Admins have unlimited storage
if (iADMIN || $userdata['user_id'] == 1) {
	$msg_settings['pm_inbox'] = 0;
	$msg_settings['pm_savebox'] = 0;
	$msg_settings['pm_sentbox'] = 0;
}

$info['chat_rows'] = 0;
$info['channel'] = '';

// Check if the folder name is a valid one
if (!isset($_GET['folder']) || !preg_check("/^(inbox|outbox|archive|options)$/", $_GET['folder'])) {
	$_GET['folder'] = "inbox";
}

if (isset($_POST['msg_send']) && isnum($_POST['msg_send'])) {
	$_GET['msg_send'] = $_POST['msg_send'];
}
if (isset($_POST['msg_to_group']) && isnum($_POST['msg_to_group'])) {
	$_GET['msg_to_group'] = $_POST['msg_to_group'];
}
$error = "";
$msg_ids = "";
$check_count = 0;
if (isset($_POST['check_mark'])) {
	if (is_array($_POST['check_mark']) && count($_POST['check_mark']) > 1) {
		foreach ($_POST['check_mark'] as $thisnum) {
			if (isnum($thisnum)) $msg_ids .= ($msg_ids ? "," : "").$thisnum;
			$check_count++;
		}
	} else {
		if (isnum($_POST['check_mark'][0])) $msg_ids = $_POST['check_mark'][0];
		$check_count = 1;
	}
}

// Archive, Delete and Saves Options.
if (isset($_GET['msg_read']) && isnum($_GET['msg_read'])) {
	if (isset($_POST['save'])) {
		$archive_total = dbcount("(message_id)", DB_MESSAGES, "message_to='".$userdata['user_id']."' AND message_folder='2'");
		$_sresult = dbquery("SELECT message_subject FROM ".DB_MESSAGES." WHERE message_id='".$_GET['msg_read']."' LIMIT 1");
		if (dbrows($_sresult) > 0) {
			$sdata = dbarray($_sresult);
			$c_sresult = dbquery("SELECT * FROM ".DB_MESSAGES." WHERE message_subject='".$sdata['message_subject']."'
			AND message_user='".$userdata['user_id']."'	ORDER BY message_datestamp ASC");
			if (dbrows($c_sresult) > 0) {
				while ($cdata = dbarray($c_sresult)) {
					// reformat conversation
					if ($cdata['message_folder'] == 1) {
						$person_a = $cdata['message_from'];
						$person_b = $cdata['message_to'];
						$cdata['message_to'] = $person_a;
						$cdata['message_from'] = $person_b;
					}
					$cdata['message_folder'] = 2;
					dbquery_insert(DB_MESSAGES, $cdata, 'update', array('noredirect' => 1));
				}
			}
		}
		redirect(FUSION_SELF."?folder=archive".($error ? "&error=$error" : ""));
	}
	elseif (isset($_POST['unsave'])) {
		// do we even need unarchive? doesn't look like very useful.
		//$inbox_total = dbcount("(message_id)", DB_MESSAGES, "message_to='".$userdata['user_id']."' AND message_folder='0'");
		//if ($msg_settings['pm_inbox'] == "0" || ($inbox_total+1) <= $msg_settings['pm_inbox']) {
		//	$result = dbquery("UPDATE ".DB_MESSAGES." SET message_folder='0' WHERE message_id='".$_GET['msg_id']."' AND message_to='".$userdata['user_id']."'");
		//} else {
		//	$error = "1";
		//}
		//redirect(FUSION_SELF."?folder=archive".($error ? "&error=$error" : ""));
	} elseif (isset($_POST['delete'])) {
		$_sresult = dbquery("SELECT message_subject FROM ".DB_MESSAGES." WHERE message_id='".$_GET['msg_read']."' LIMIT 1");
		if (dbrows($_sresult) > 0) {
			$sdata = dbarray($_sresult);
			$c_sresult = dbquery("SELECT * FROM ".DB_MESSAGES." WHERE message_subject='".$sdata['message_subject']."'
			AND message_user='".$userdata['user_id']."'
			ORDER BY message_datestamp ASC");
			if (dbrows($c_sresult) > 0) {
				while ($cdata = dbarray($c_sresult)) {
					dbquery_insert(DB_MESSAGES, $cdata, 'delete', array('noredirect' => 1));
				}
			}
		}
		redirect(FUSION_SELF."?folder=".$_GET['folder']);
	}
}

// Read or Unread Message
if ($msg_ids && $check_count > 0) {
	$msg_ids = explode(',', $msg_ids);
	if (isset($_POST['read_msg'])) {
		foreach($msg_ids as $id) {
			$_sresult = dbquery("SELECT message_subject FROM ".DB_MESSAGES." WHERE message_id='".$id."' LIMIT 1");
			if (dbrows($_sresult) > 0) {
				$sdata = dbarray($_sresult);
				$c_sresult = dbquery("SELECT * FROM ".DB_MESSAGES." WHERE message_subject='".$sdata['message_subject']."'
				AND message_user='".$userdata['user_id']."' AND message_folder='".$sdata['message_folder']."'
				ORDER BY message_datestamp ASC");
				if (dbrows($c_sresult) > 0) {
					while ($cdata = dbarray($c_sresult)) {
						$cdata['message_read'] = 1;
						dbquery_insert(DB_MESSAGES, $cdata, 'update', array('noredirect' => 1));
					}
				}
			}
		}
	}
	elseif (isset($_POST['unread_msg'])) {
		foreach($msg_ids as $id) {
			$_sresult = dbquery("SELECT message_subject FROM ".DB_MESSAGES." WHERE message_id='".$id."' LIMIT 1");
			if (dbrows($_sresult) > 0) {
				$sdata = dbarray($_sresult);
				$c_sresult = dbquery("SELECT * FROM ".DB_MESSAGES." WHERE message_subject='".$sdata['message_subject']."'
				AND message_user='".$userdata['user_id']."' AND message_folder='".$sdata['message_folder']."'
				ORDER BY message_datestamp ASC");
				if (dbrows($c_sresult) > 0) {
					while ($cdata = dbarray($c_sresult)) {
						$cdata['message_read'] = 0;
						dbquery_insert(DB_MESSAGES, $cdata, 'update', array('noredirect' => 1));
					}
				}
			}
		}
	}
	elseif (isset($_POST['save_msg'])) {
		foreach($msg_ids as $id) {
			$_sresult = dbquery("SELECT message_subject FROM ".DB_MESSAGES." WHERE message_id='".$id."' LIMIT 1");
			if (dbrows($_sresult) > 0) {
				$sdata = dbarray($_sresult);
				$c_sresult = dbquery("SELECT * FROM ".DB_MESSAGES." WHERE message_subject='".$sdata['message_subject']."'
				AND message_user='".$userdata['user_id']."'
				ORDER BY message_datestamp ASC");
				if (dbrows($c_sresult) > 0) {
					while ($cdata = dbarray($c_sresult)) {
						if ($cdata['message_folder'] == 1) {
							$person_a = $cdata['message_from'];
							$person_b = $cdata['message_to'];
							$cdata['message_to'] = $person_a;
							$cdata['message_from'] = $person_b;
						}
						$cdata['message_folder'] = 2;
						dbquery_insert(DB_MESSAGES, $cdata, 'update', array('noredirect' => 1));
					}
				}
			}
		}
	}
	elseif (isset($_POST['delete_msg'])) {
		foreach($msg_ids as $id) {
			$_sresult = dbquery("SELECT message_subject FROM ".DB_MESSAGES." WHERE message_id='".$id."' LIMIT 1");
			if (dbrows($_sresult) > 0) {
				$sdata = dbarray($_sresult);
				$c_sresult = dbquery("SELECT * FROM ".DB_MESSAGES." WHERE message_subject='".$sdata['message_subject']."'
				AND message_user='".$userdata['user_id']."'
				ORDER BY message_datestamp ASC");
				if (dbrows($c_sresult) > 0) {
					while ($cdata = dbarray($c_sresult)) {
						dbquery_insert(DB_MESSAGES, $cdata, 'delete');
					}
				}
			}
		}
	}
	redirect(FUSION_SELF."?folder=".$_GET['folder']);

	// Old codes with checks...
	/*if (isset($_POST['save_msg'])) {
		$archive_total = dbcount("(message_id)", DB_MESSAGES, "message_to='".$userdata['user_id']."' AND message_folder='2'");
		if ($msg_settings['pm_savebox'] == "0" || ($archive_total+$check_count) <= $msg_settings['pm_savebox']) {
			$result = dbquery("UPDATE ".DB_MESSAGES." SET message_folder='2' WHERE message_id IN(".$msg_ids.") AND message_to='".$userdata['user_id']."'");
		} else {
			$error = "1";
		}
	} elseif (isset($_POST['unsave_msg'])) {
		$inbox_total = dbcount("(message_id)", DB_MESSAGES, "message_to='".$userdata['user_id']."' AND message_folder='0'");
		if ($msg_settings['pm_inbox'] == "0" || ($inbox_total+$check_count) <= $msg_settings['pm_inbox']) {
			$result = dbquery("UPDATE ".DB_MESSAGES." SET message_folder='0' WHERE message_id IN(".$msg_ids.") AND message_to='".$userdata['user_id']."'");
		} else {
			$error = "1";
		}
	} elseif (isset($_POST['read_msg'])) {
		$result = dbquery("UPDATE ".DB_MESSAGES." SET message_read='1' WHERE message_id IN(".$msg_ids.") AND message_to='".$userdata['user_id']."'");
	} elseif (isset($_POST['unread_msg'])) {
		$result = dbquery("UPDATE ".DB_MESSAGES." SET message_read='0' WHERE message_id IN(".$msg_ids.") AND message_to='".$userdata['user_id']."'");
	} elseif (isset($_POST['delete_msg'])) {
		$result = dbquery("DELETE FROM ".DB_MESSAGES." WHERE message_id IN(".$msg_ids.") AND message_to='".$userdata['user_id']."'");
	}
	redirect(FUSION_SELF."?folder=".$_GET['folder'].($error ? "&error=$error" : ""));*/
}

// Reply and Send actions
if (isset($_POST['send_message'])) {
	$personal_settings = dbarray(dbquery("SELECT * FROM ".DB_MESSAGES_OPTIONS." WHERE user_id='".$userdata['user_id']."'"));
	$my_settings['pm_save_sent'] = $personal_settings['pm_save_sent'] ? : $msg_settings['pm_save_sent'];
	$my_settings['pm_email_notify'] = $personal_settings['pm_email_notify'] ? : $msg_settings['pm_email_notify'];
	$postdata['message_from'] = $userdata['user_id'];
	$postdata['message_subject'] = form_sanitizer($_POST['subject'], '', 'subject');
	$postdata['message_message'] = form_sanitizer($_POST['message'], '', 'message');
	$postdata['message_smileys'] = isset($_POST['chk_disablesmileys']) || preg_match("#(\[code\](.*?)\[/code\]|\[geshi=(.*?)\](.*?)\[/geshi\]|\[php\](.*?)\[/php\])#si", $postdata['message_message']) ? "n" : "y";
	$postdata['message_read'] = 0;
	$postdata['message_datestamp'] = time();
	$postdata['message_folder'] = 0;
	require_once INCLUDES."sendmail_include.php";
	if (!defined('FUSION_NULL')) {
		// Send to Group
		if (iADMIN && isset($_POST['chk_sendtoall']) && isnum($_POST['msg_to_group'])) {
			$msg_to_group = $_POST['msg_to_group'];
			if ($msg_to_group == "101" || $msg_to_group == "102" || $msg_to_group == "103") {
				$result = dbquery("SELECT u.user_id, u.user_name, u.user_email, mo.pm_email_notify FROM ".DB_USERS." u
				LEFT JOIN ".DB_MESSAGES_OPTIONS." mo USING(user_id)
				WHERE user_level>='".$msg_to_group."' AND user_status='0'");
				if (dbrows($result)) {
					while ($data = dbarray($result)) {
						if ($data['user_id'] != $userdata['user_id']) {
							$postdata['message_to'] = $data['user_id'];
							$postdata['message_user'] = $data['user_id'];
							dbquery_insert(DB_MESSAGES, $postdata, 'save', array('noredirect' => 1));
							$message_content = str_replace("[SUBJECT]", $postdata['message_subject'], $locale['626']);
							$message_content = str_replace("[USER]", $userdata['user_name'], $message_content);
							$send_email = isset($data['pm_email_notify']) ? $data['pm_email_notify'] : $msg_settings['pm_email_notify'];
							if ($send_email == "1") {
								$template_result = dbquery("SELECT template_key, template_active FROM ".DB_EMAIL_TEMPLATES." WHERE template_key='PM' LIMIT 1");
								if (dbrows($template_result)) {
									$template_data = dbarray($template_result);
									if ($template_data['template_active'] == "1") {
										sendemail_template("PM", $postdata['message_subject'], trimlink($postdata['message_message'], 150), $userdata['user_name'], $data['user_name'], "", $data['user_email']);
									} else {
										sendemail($data['user_name'], $data['user_email'], $settings['siteusername'], $settings['siteemail'], $locale['625'], $data['user_name'].$message_content);
									}
								} else {
									sendemail($data['user_name'], $data['user_email'], $settings['siteusername'], $settings['siteemail'], $locale['625'], $data['user_name'].$message_content);
								}
							}
						}
					}
				} else {
					redirect(FUSION_SELF."?folder=inbox");
				}
			} else {
				$result = dbquery("SELECT u.user_id, u.user_name, u.user_email, mo.pm_email_notify FROM ".DB_USERS." u
				LEFT JOIN ".DB_MESSAGES_OPTIONS." mo USING(user_id)
				WHERE user_groups REGEXP('^\\\.{$msg_to_group}$|\\\.{$msg_to_group}\\\.|\\\.{$msg_to_group}$') AND user_status='0'");
				if (dbrows($result)) {
					while ($data = dbarray($result)) {
						if ($data['user_id'] != $userdata['user_id']) {
							$result2 = dbquery("INSERT INTO ".DB_MESSAGES." (message_to, message_from, message_subject, message_message, message_smileys, message_read, message_datestamp, message_folder) VALUES('".$data['user_id']."','".$userdata['user_id']."','".$subject."','".$message."','".$smileys."','0','".time()."','0')");
							$message_content = str_replace("[SUBJECT]", $subject, $locale['626']);
							$message_content = str_replace("[USER]", $userdata['user_name'], $message_content);
							$send_email = isset($data['pm_email_notify']) ? $data['pm_email_notify'] : $msg_settings['pm_email_notify'];
							if ($send_email == "1") {
								$template_result = dbquery("SELECT template_key, template_active FROM ".DB_EMAIL_TEMPLATES." WHERE template_key='PM' LIMIT 1");
								if (dbrows($template_result)) {
									$template_data = dbarray($template_result);
									if ($template_data['template_active'] == "1") {
										sendemail_template("PM", $subject, trimlink($message, 150), $userdata['user_name'], $data['user_name'], "", $data['user_email']);
									} else {
										sendemail($data['user_name'], $data['user_email'], $settings['siteusername'], $settings['siteemail'], $locale['625'], $data['user_name'].$message_content);
									}
								} else {
									sendemail($data['user_name'], $data['user_email'], $settings['siteusername'], $settings['siteemail'], $locale['625'], $data['user_name'].$message_content);
								}
							}
						}
					}
				} else {
					redirect(FUSION_SELF."?folder=inbox");
				}
			}
		} elseif (isnum($_GET['msg_send'])) {
			// Send to Individuals.
			require_once INCLUDES."flood_include.php";
			//if (!flood_control("message_datestamp", DB_MESSAGES, "message_from='".$userdata['user_id']."'")) {
			$result = dbquery("SELECT u.user_id, u.user_name, u.user_email, u.user_level, mo.pm_email_notify, s.pm_inbox, COUNT(message_id) as message_count
				FROM ".DB_USERS." u
				LEFT JOIN ".DB_MESSAGES_OPTIONS." mo USING(user_id)
				LEFT JOIN ".DB_MESSAGES_OPTIONS." s ON s.user_id='0'
				LEFT JOIN ".DB_MESSAGES." ON message_to=u.user_id AND message_folder='0'
				WHERE u.user_id='".$_GET['msg_send']."' GROUP BY u.user_id");
			if (dbrows($result)) {
				$data = dbarray($result);
				if ($data['user_id'] != $userdata['user_id']) {
					if ($data['user_id'] == 1 || $data['user_level'] < USER_LEVEL_MEMBER || $data['pm_inbox'] == "0" || ($data['message_count']+1) <= $data['pm_inbox']) {
						$postdata['message_to'] = $data['user_id'];
						$postdata['message_user'] = $data['user_id'];
						dbquery_insert(DB_MESSAGES, $postdata, 'save', array('noredirect' => 1));
						$send_email = isset($data['pm_email_notify']) ? $data['pm_email_notify'] : $msg_settings['pm_email_notify'];
						if ($send_email == "1") {
							$message_content = str_replace("[SUBJECT]", $postdata['message_subject'], $locale['626']);
							$message_content = str_replace("[USER]", $userdata['user_name'], $message_content);
							$template_result = dbquery("SELECT template_key, template_active FROM ".DB_EMAIL_TEMPLATES." WHERE template_key='PM' LIMIT 1");
							if (dbrows($template_result)) {
								$template_data = dbarray($template_result);
								if ($template_data['template_active'] == "1") {
									sendemail_template("PM", $postdata['message_subject'], trimlink($postdata['message_message'], 150), $userdata['user_name'], $data['user_name'], "", $data['user_email']);
								} else {
									sendemail($data['user_name'], $data['user_email'], $settings['siteusername'], $settings['siteemail'], $locale['625'], $data['user_name'].$message_content);
								}
							} else {
								sendemail($data['user_name'], $data['user_email'], $settings['siteusername'], $settings['siteemail'], $locale['625'], $data['user_name'].$message_content);
							}
						}
					} else {
						$error = "2";
					}
				}
			} else {
				redirect(FUSION_SELF."?folder=inbox&error=noresult");
			}
		}
	}
	if (!$error && !defined('FUSION_NULL')) {
		$cdata['outbox_count'] = 0;
		$cdata = dbarray(dbquery("SELECT COUNT(message_id) AS outbox_count, MIN(message_id) AS last_message
		FROM ".DB_MESSAGES." WHERE message_to='".$userdata['user_id']."' AND message_folder='1' GROUP BY message_to"));
		if ($my_settings['pm_save_sent']) {
			// deactivate message deletion, because we are going to delete one whole chunk now.
			if ($cdata['outbox_count'] && ($cdata['outbox_count']+1) > $msg_settings['pm_sentbox']) {
				//$result = dbquery("DELETE FROM ".DB_MESSAGES." WHERE message_id='".$cdata['last_message']."' AND message_to='".$userdata['user_id']."'");
			}
			if (isset($_POST['chk_sendtoall']) && isnum($_POST['msg_to_group'])) {
				$postdata['message_from'] = $userdata['user_id'];
			} elseif (isset($_GET['msg_send']) && isnum($_GET['msg_send'])) {
				$postdata['message_from'] = $_GET['msg_send'];
			}
			if ($postdata['message_from'] && $postdata['message_from'] != $userdata['user_id']) {
				$postdata['message_to'] = $userdata['user_id'];
				$postdata['message_user'] = $userdata['user_id'];
				$postdata['message_folder'] = 1;
				dbquery_insert(DB_MESSAGES, $postdata, 'save', array('noredirect' => 1));
			}
		}
	} else {
		if ($error == 2) {
			notify($locale['628'], '');
		} else {
			notify($locale['488'], "$error");
		}
	}
	redirect(BASEDIR."messages.php?folder=".$_GET['folder']."".(isset($_GET['msg_read']) ? "&amp;msg_read=".$_GET['msg_read']."" : ''));
}

// Error Section.
if (isset($_GET['error'])) {
	if ($_GET['error'] == "1") {
		$message = $locale['629'];
	} elseif ($_GET['error'] == "2") {
		$message = $locale['628'];
	} elseif ($_GET['error'] == "noresult") {
		$message = $locale['482'];
	} elseif ($_GET['error'] == "flood") {
		$message = sprintf($locale['487'], $settings['flood_interval']);
	} else {
		$message = "";
	}
	add_to_title($locale['global_201'].$locale['627']);
	opentable($locale['627']);
	echo "<div style='text-align:center'>".$message."</div>\n";
	closetable();
}

// Callback Section
$folders = array("inbox" => $locale['402'], "outbox" => $locale['403'], "archive" => $locale['404'], "options" => $locale['425']);

$_GET['rowstart'] = (isset($_GET['rowstart']) && isnum($_GET['rowstart'])) ? : 0;

$info['inbox_total'] = dbrows(dbquery("SELECT count('message_id') as total FROM ".DB_MESSAGES." WHERE message_user='".$userdata['user_id']."' AND message_folder='0' GROUP BY message_subject"));
$info['outbox_total'] = dbrows(dbquery("SELECT count('message_id') as total FROM ".DB_MESSAGES." WHERE message_user='".$userdata['user_id']."' AND message_folder='1' GROUP BY message_subject"));
$info['archive_total'] = dbrows(dbquery("SELECT message_id FROM ".DB_MESSAGES." WHERE message_user='".$userdata['user_id']."' AND message_folder='2' GROUP BY message_subject"));

/* Global Buttons */
$info['button']['new'] = array('link'=>FUSION_SELF."?folder=".$_GET['folder']."&amp;msg_send=0", 'name'=>$locale['401']);
$info['button']['options'] = array('link'=>FUSION_SELF."?folder=options", 'name'=>$locale['425']);

/* Inbox Channelling */
if ($_GET['folder'] == "inbox" || $_GET['folder'] == 'options') {
	if ($_GET['folder'] == 'options') {
		$c_result = dbquery("SELECT * FROM ".DB_MESSAGES_OPTIONS." WHERE user_id='".$userdata['user_id']."'");
		if (dbrows($c_result)) {
			$info += dbarray($c_result);
		} else {
			$options = dbarray(dbquery("SELECT pm_save_sent, pm_email_notify FROM ".DB_MESSAGES_OPTIONS." WHERE user_id='0' LIMIT 1"));
			$info['pm_save_sent'] = $options['pm_save_sent'];
			$info['pm_email_notify'] = $options['pm_email_notify'];
		}
	} else {
		if ($info['inbox_total'] > 0 ) {
			$result = dbquery("SELECT m.message_id, m.message_subject, m.message_folder, m.message_datestamp, m.message_from, m.message_read,
			u.user_id, u.user_name, u.user_status, u.user_avatar,
			up.user_id as contact_id, up.user_name as contact_name, up.user_status as contact_status, up.user_avatar as contact_avatar,
			max(m.message_id) as last_message
			FROM ".DB_MESSAGES." m
			LEFT JOIN ".DB_USERS." u ON (m.message_to=u.user_id)
			LEFT JOIN ".DB_USERS." up ON (m.message_from=up.user_id)
			WHERE m.message_user='".$userdata['user_id']."' AND m.message_folder ='0'
			GROUP BY m.message_from
			ORDER BY m.message_datestamp ASC
			");
			$info['chat_rows'] = dbrows($result);
			if (dbrows($result)>0) {
				add_to_title($locale['global_201'].$folders[$_GET['folder']]);
				while ($data = dbarray($result)) { // threads
					$data['contact_user'] = array('user_id'=>$data['contact_id'], 'user_name'=>$data['contact_name'], 'user_status'=>$data['contact_status'], 'user_avatar'=>$data['contact_avatar']);
					$data['message'] = array('link'=>BASEDIR."messages.php?folder=inbox&amp;msg_user=".$data['contact_id'], 'name'=>$data['message_subject']);
					$info['chat_list'][$data['contact_id']] = $data; // group by contact_id.
				}

				// channeling - to reduce scope of sql search.
				if (isset($_GET['msg_user']) && isnum($_GET['msg_user'])) {
					$result = dbquery("SELECT m.*, u.user_id, u.user_name, u.user_status, u.user_avatar
						FROM ".DB_MESSAGES." m
						LEFT JOIN ".DB_USERS." u ON m.message_from=u.user_id
						WHERE m.message_user='".$userdata['user_id']."' AND m.message_from='".$_GET['msg_user']."' and message_folder='0'
						GROUP BY message_subject ORDER BY message_datestamp DESC
						");
					$info['max_rows'] = dbrows($result);
					if ($info['max_rows']>0) {
						while ($topics = dbarray($result)) {
							$info['item'][] = $topics;
							$info['channel'] = profile_link($topics['user_id'], $topics['user_name'], $topics['user_status']); // bah let it loop
						}
					}
				} else {
					$info['channel'] = $locale['467'];
				}
				// end channeling.
			}
		}
	}
}
/* Outbox Channeling */
elseif ($_GET['folder'] == "outbox") {
	add_to_title($locale['global_201'].$folders[$_GET['folder']]);
	if ($info['outbox_total'] > 0 ) {
		$result = dbquery("SELECT m.message_id, m.message_subject, m.message_folder, m.message_datestamp, m.message_from, m.message_read,
			u.user_id, u.user_name, u.user_status, u.user_avatar,
			up.user_id as contact_id, up.user_name as contact_name, up.user_status as contact_status, up.user_avatar as contact_avatar,
			max(m.message_id) as last_message
			FROM ".DB_MESSAGES." m
			LEFT JOIN ".DB_USERS." u ON (m.message_to=u.user_id)
			LEFT JOIN ".DB_USERS." up ON (m.message_from=up.user_id)
			WHERE m.message_user='".$userdata['user_id']."' AND m.message_folder ='1'
			GROUP BY m.message_to
			ORDER BY m.message_datestamp ASC
			");
		$info['chat_rows'] = dbrows($result);
		if (dbrows($result)>0) {
			add_to_title($locale['global_201'].$folders[$_GET['folder']]);
			while ($data = dbarray($result)) { // threads
				$data['contact_user'] = array('user_id'=>$data['contact_id'], 'user_name'=>$data['contact_name'], 'user_status'=>$data['contact_status'], 'user_avatar'=>$data['contact_avatar']);
				$data['message'] = array('link'=>BASEDIR."messages.php?folder=outbox&amp;msg_user=".$data['contact_id'], 'name'=>$data['message_subject']);
				$info['chat_list'][$data['contact_id']] = $data; // group by contact_id.
			}
			// Outbox channeling - to reduce scope of sql search.
			if (isset($_GET['msg_user']) && isnum($_GET['msg_user'])) {
				$result = dbquery("SELECT m.*, u.user_id, u.user_name, u.user_status, u.user_avatar,
						up.user_id as contact_id, up.user_name as contact_name, up.user_status as contact_status
						FROM ".DB_MESSAGES." m
						LEFT JOIN ".DB_USERS." u ON m.message_to=u.user_id
						LEFT JOIN ".DB_USERS." up ON m.message_from=up.user_id
						WHERE m.message_user='".$userdata['user_id']."' AND m.message_from='".$_GET['msg_user']."' and message_folder='1'
						GROUP BY message_subject ORDER BY message_datestamp DESC
						");
				$info['max_rows'] = dbrows($result);
				if ($info['max_rows']>0) {
					while ($topics = dbarray($result)) {
						$info['item'][] = $topics;
						$info['channel'] = profile_link($topics['contact_id'], $topics['contact_name'], $topics['contact_status']); // bah let it loop
					}
				}
			} else {
				$info['channel'] = $locale['467'];
			}
			// end channeling.
		}
	}
}
/* Archive Channeling */
elseif ($_GET['folder'] == "archive") {

	if ($info['archive_total'] > 0 ) {
		$result = dbquery("SELECT m.message_id, m.message_subject, m.message_folder, m.message_datestamp, m.message_from, m.message_read,
			up.user_id as contact_id, up.user_name as contact_name, up.user_status as contact_status, up.user_avatar as contact_avatar,
			max(m.message_id) as last_message
			FROM ".DB_MESSAGES." m
			LEFT JOIN ".DB_USERS." up ON IF(m.message_from='".$userdata['user_id']."', m.message_to=up.user_id, m.message_from=up.user_id)
			WHERE m.message_user='".$userdata['user_id']."' AND m.message_folder ='2'
			GROUP BY m.message_subject
			ORDER BY m.message_datestamp ASC
			");
		$info['chat_rows'] = dbrows($result);
		if (dbrows($result)>0) {
			add_to_title($locale['global_201'].$folders[$_GET['folder']]);
			while ($data = dbarray($result)) { // threads
				$data['contact_user'] = array('user_id'=>$data['contact_id'], 'user_name'=>$data['contact_name'], 'user_status'=>$data['contact_status'], 'user_avatar'=>$data['contact_avatar']);
				$data['message'] = array('link'=>BASEDIR."messages.php?folder=archive&amp;msg_user=".$data['contact_id'], 'name'=>$data['message_subject']);
				$info['chat_list'][$data['contact_id']] = $data; // group by contact_id.
			}

			// channeling - to reduce scope of sql search.
			if (isset($_GET['msg_user']) && isnum($_GET['msg_user'])) {
				$result = dbquery("SELECT m.*, u.user_id, u.user_name, u.user_status, u.user_avatar
						FROM ".DB_MESSAGES." m
						LEFT JOIN ".DB_USERS." u ON IF(m.message_from='".$userdata['user_id']."', m.message_to=u.user_id, m.message_from=u.user_id)
						WHERE m.message_user='".$userdata['user_id']."' AND (m.message_to='".$_GET['msg_user']."' or m.message_from='".$_GET['msg_user']."') AND message_folder='2'
						GROUP BY message_subject ORDER BY message_datestamp DESC
						");
				$info['max_rows'] = dbrows($result);
				if ($info['max_rows']>0) {
					while ($topics = dbarray($result)) {
						$info['item'][] = $topics;
						$info['channel'] = profile_link($topics['user_id'], $topics['user_name'], $topics['user_status']); // bah let it loop
					}
				}
			} else {
				$info['channel'] = $locale['467'];
			}
			// end channeling.
		}
	}
}


if ((isset($_GET['msg_read']) && isnum($_GET['msg_read'])) && ($_GET['folder'] == "inbox" || $_GET['folder'] == "archive" || $_GET['folder'] == "outbox")) {
	// real full pm - debug success - nothing here.
	$p_result = dbquery("SELECT message_id, message_subject FROM ".DB_MESSAGES." WHERE message_id='".$_GET['msg_read']."' LIMIT 1");
	if (dbrows($p_result) > 0) {
		$p_data = dbarray($p_result);
		$message_subject = $p_data['message_subject'];
		// Messages Query in msg_read.
		if ($_GET['folder'] == 'inbox') {
			$result = dbquery("SELECT  m.message_id, m.message_to, m.message_from, m.message_subject, m.message_message, m.message_smileys,
				m.message_datestamp, m.message_folder, u.user_id, u.user_name, u.user_status, u.user_avatar
				FROM ".DB_MESSAGES." m
				LEFT JOIN ".DB_USERS." u ON IF(message_folder=1, m.message_to=u.user_id, m.message_from=u.user_id)
				WHERE message_from !='".$userdata['user_id']."' AND message_subject='$message_subject' AND message_folder='0' OR
				message_to='".$userdata['user_id']."' AND message_subject='".$p_data['message_subject']."' AND message_folder='1' AND message_user='".$userdata['user_id']."'
				ORDER BY message_datestamp ASC");
		} elseif ($_GET['folder'] == 'outbox') {
			$result = dbquery("SELECT  m.message_id, m.message_subject, m.message_message, m.message_smileys,
				m.message_datestamp, m.message_folder, u.user_id, u.user_name, u.user_status, u.user_avatar
				FROM ".DB_MESSAGES." m
				LEFT JOIN ".DB_USERS." u ON m.message_to=u.user_id
				WHERE message_to='".$userdata['user_id']."' AND message_subject='".$p_data['message_subject']."' AND message_folder='1' AND message_user='".$userdata['user_id']."'
				ORDER BY message_datestamp ASC");
		} elseif ($_GET['folder'] == 'archive') {
			$result = dbquery("SELECT  m.message_id, m.message_to, m.message_from, m.message_subject, m.message_message, m.message_smileys,
				m.message_datestamp, m.message_folder, u.user_id, u.user_name, u.user_status, u.user_avatar
				FROM ".DB_MESSAGES." m
				LEFT JOIN ".DB_USERS." u ON IF(message_folder=1, m.message_to=u.user_id, m.message_from=u.user_id)
				WHERE message_from='".$userdata['user_id']."' AND message_subject='$message_subject' AND message_folder='2' OR
					  message_to='".$userdata['user_id']."' AND message_subject='".$p_data['message_subject']."' AND message_folder='2' AND message_user='".$userdata['user_id']."'
				ORDER BY message_datestamp ASC");
		}
		if (dbrows($result) > 0) {
			while ($data = dbarray($result)) {
				if ($data['message_smileys'] == "y") $data['message_message'] = parsesmileys($data['message_message']);
				$info['message'][] = $data;
			}
			$result = dbquery("UPDATE ".DB_MESSAGES." SET message_read='1' WHERE message_id='".$p_data['message_id']."'");
			add_to_title($locale['global_201'].$locale['431']);
		} else {
			//echo 'no message found inner';
			redirect(BASEDIR."messages.php");
		}
	} else {
		redirect(BASEDIR."messages.php");
	}
}

//print_p($info); // var_dump(info);
render_inbox($info);
add_to_jquery("
var l_height = $('.left_pm').height(), r_height = $('.right_pm').height();
if (l_height > r_height) { $('.right_pm').height(l_height); } else { $('.left_pm').height(r_height); }
$('#setcheck_all').bind('click', function() { $('.checkbox').prop('checked', true); });
$('#setcheck_none').bind('click', function() { $('.checkbox').prop('checked', false); });
");
require_once THEMES."templates/footer.php";
?>