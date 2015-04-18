<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| File Category: Core Rewrite Modules for 7.03
| Author: Hien (Frederick MC Chan)
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
if (!defined("IN_FUSION")) { die("Access Denied"); }
$regex = array("%user_id%" => "([0-9]+)", "%user_name%" => "([0-9a-zA-Z._\W]+)");
$pattern = array("profile/%user_id%/%user_name%" => "profile.php?lookup=%user_id%");
$dbname = DB_USERS;
$dbid = array("%user_id%" => "user_id");
$dbinfo = array("%user_name%" => "user_name");

?>