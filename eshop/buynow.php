<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: buynow.php
| Author: Joakim Falk (Domi)
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
require_once dirname(__FILE__)."../maincore.php";
require_once THEMES."templates/header.php";
if (isset($_REQUEST['id']) && !isnum($_REQUEST['id'])) die("Denied");
include INCLUDES."eshop_functions_include.php";

add_to_title($locale['ESHPCHK159']);
if (iMEMBER) { $username = $userdata['user_id']; } else { $username = $_SERVER['REMOTE_ADDR']; }

if (!iMEMBER) {
opentable($locale['ESHPB100']);
buildeshopheader();
echo "<div class='admin-message'><br /><img src='".BASEDIR."eshop/img/restricted.gif' alt='' /> <br />".$locale['ESHPB101']." <a class='eshpbutton green' href='".BASEDIR."register.php'><b>".$locale['ESHPB102']."</b></a> ".$locale['ESHPB103']." <br /><br /></div>";
closetable();
require_once THEMES."templates/footer.php";
} 
if (iMEMBER) {
if (isset($_POST['buynow'])) {
opentable($locale['ESHPCHK159']);
buildeshopheader();

//register the order
saveorder();

echo "<center><div style = 'font-family : Verdana, Arial, Helvetica, sans-serif;
	text-align: center;
    margin-top : 20px;
	width: 550px;
	font-size: 20px;
	font-weight: bold;
	padding: 5px 0 5px 20px;
	margin-bottom: 5px;
	color: #05B;
	border: 1px solid #9CC0EE;
	-webkit-border-bottom-right-radius: 5px;
	-webkit-border-bottom-left-radius: 5px;
	-moz-border-radius-bottomright: 5px;
	-moz-border-radius-bottomleft: 5px;
	border-bottom-right-radius: 5px;
	border-bottom-left-radius: 5px;
	-webkit-border-top-left-radius: 5px;
	-webkit-border-top-right-radius: 5px;
	-moz-border-radius-topleft: 5px;
	-moz-border-radius-topright: 5px;
	border-top-left-radius: 5px;
	border-top-right-radius: 5px;';> ".$locale['ESHPB104']." </div></center><br /><br />";

echo "<center><div style = 'font-family : Verdana, Arial, Helvetica, sans-serif;
	text-align: center;
	width: 350px;
	font-size: 11px;
	font-weight: bold;
	padding: 5px 0 5px 20px;
	margin-bottom: 5px;
	color: #05B;
	border: 1px solid #9CC0EE;
	-webkit-border-bottom-right-radius: 5px;
	-webkit-border-bottom-left-radius: 5px;
	-moz-border-radius-bottomright: 5px;
	-moz-border-radius-bottomleft: 5px;
	border-bottom-right-radius: 5px;
	border-bottom-left-radius: 5px;
	-webkit-border-top-left-radius: 5px;
	-webkit-border-top-right-radius: 5px;
	-moz-border-radius-topleft: 5px;
	-moz-border-radius-topright: 5px;
	border-top-left-radius: 5px;
	border-top-right-radius: 5px;';> ".$locale['ESHPB105']."<span id='counter'>0</span> ".$locale['ESHPB111']."</div></center>";

	echo "<script type='text/javascript'>
function countdown() {
    var i = document.getElementById('counter');
    i.innerHTML = parseInt(i.innerHTML)+1;
}
setInterval(function(){ countdown(); },1000);
</script>";

$data = dbarray(dbquery("SELECT * FROM ".DB_ESHOP." WHERE id='".$_POST['id']."'"));

echo '
<form name="paypal" id="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post" />
<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="business" value="'.$settings['eshop_ppmail'].'" />
<input type="hidden" name="item_name" value="'.$data['title'].'" />
<input type="hidden" name="page_style" value="PayPal" />
<input type="hidden" name="no_shipping" value="1" />
<input type="hidden" name="return" value="'.$settings['siteurl'].'eshop/purchasescripts/'.($data['rpage'] ? $data['rpage'] : $settings['eshop_returnpage']).'" />
<input type="hidden" name="cancel_return" value="'.$settings['siteurl'].'" />
<input type="hidden" name="currency_code" value="'.$settings['eshop_currency'].'" />
<input type="hidden" name="cancel_return" value="'.$settings['siteurl'].'eshop/eshop.php" />
<input type="hidden" name="amount" value="'.($data['xprice'] ? $data['xprice'] : $data['price']).'" />
<br /><center><input name="mySubmit" type="submit" id="mySubmit" value="Click here to continue if you are not automatically redirected" /></center>';
if ($settings['eshop_vat_default'] == "0") { echo '<input type="hidden" name="tax_rate" value="'.$settings['eshop_vat'].'" />'; }

echo "</form>";
closetable();
echo '<script type="text/javascript">';
echo 'window.onload = function(){document.paypal.submit()}';
echo '</script>';
} else {
opentable($locale['ESHPB106']);
buildeshopheader();

$firstname = "";
$lastname = "";
$dob = "";
$country_code = "";
$region = "";
$city = "";
$address = "";
$address2 = "";
$postcode = "";
$phone = "";
$fax = "";
$email = "";

$cdata = dbarray(dbquery("SELECT * FROM ".DB_ESHOP_CUSTOMERS." WHERE cuid = '".$userdata['user_id']."'"));
if ($cdata) {
$firstname = $cdata['cfirstname'];
$lastname = $cdata['clastname'];
$dob = $cdata['cdob'];
$country_code = $cdata['ccountry_code'];
$region = $cdata['cregion'];
$city = $cdata['ccity'];
$address = $cdata['caddress'];
$address2 = $cdata['caddress2'];
$postcode = $cdata['cpostcode'];
$phone = $cdata['cphone'];
$fax = $cdata['cfax'];
$email = $cdata['cemail'];
 }

echo "<div style='width:685px !important;' class='center'><form name='inputform' method='post' action='".FUSION_SELF."' enctype='multipart/form-data' onSubmit='return ValidateForm(this);'>";
echo "<div style='float:left;margin-top:5px;padding:1px;'>";
echo"<fieldset style='width:100%;padding:2px;'><legend style='width:90% !important;'>&nbsp; ".$locale['ESHPCHK102']." &nbsp;</legend>";
echo "<table width='310' align='center' cellspacing='2' cellpadding='2' border='0'>";
echo "<tr>";

echo "
<td align='left' style='width:100px;'>".$locale['ESHPCHK103']."<span style='color:#ff0000;'>*</span></td>
<td align='left'><input type='text' name='firstname' value='$firstname' class='textbox' style='width:180px; !important'>
<input type='hidden' name='id' value='".$_GET['id']."' readonly></td>
</tr>";
echo "<tr>
<td align='left' style='width:100px;'>".$locale['ESHPCHK104']."<span style='color:#ff0000;'>*</span></td>
<td align='left'><input type='text' name='lastname' value='$lastname' class='textbox' style='width:180px; !important'></td>
</tr>";
echo "<tr>
<td align='left' style='width:100px;'>".$locale['ESHPCHK105']."</td>
<td align='left'><input type='text' name='dob' value='$dob' class='textbox' style='width:180px; !important'></td>
</tr>";
echo "<td align='left' style='width:100px;'>".$locale['ESHPCHK106']."<span style='color:#ff0000;'>*</span></td>
<td align='left'><select name='country_code' class='textbox' style='width:180px; !important'>";
echo "<option value=''>".$locale['ESHPCHK107']."</option>";
echo "<option value='AF'".($country_code == "AF" ? " selected='selected'" : "").">".$locale['ccode_af']."</option>";
echo "<option value='AL'".($country_code == "AL" ? " selected='selected'" : "").">".$locale['ccode_al']."</option>";
echo "<option value='DZ'".($country_code == "DZ" ? " selected='selected'" : "").">".$locale['ccode_dz']."</option>";
echo "<option value='AS'".($country_code == "AS" ? " selected='selected'" : "").">".$locale['ccode_as']."</option>";
echo "<option value='AD'".($country_code == "AD" ? " selected='selected'" : "").">".$locale['ccode_ad']."</option>";
echo "<option value='AO'".($country_code == "AO" ? " selected='selected'" : "").">".$locale['ccode_ao']."</option>";
echo "<option value='AI'".($country_code == "AI" ? " selected='selected'" : "").">".$locale['ccode_ai']."</option>";
echo "<option value='AQ'".($country_code == "AQ" ? " selected='selected'" : "").">".$locale['ccode_aq']."</option>";
echo "<option value='AG'".($country_code == "AG" ? " selected='selected'" : "").">".$locale['ccode_ag']."</option>";
echo "<option value='AR'".($country_code == "AR" ? " selected='selected'" : "").">".$locale['ccode_ar']."</option>";
echo "<option value='AM'".($country_code == "AM" ? " selected='selected'" : "").">".$locale['ccode_am']."</option>";
echo "<option value='AW'".($country_code == "AW" ? " selected='selected'" : "").">".$locale['ccode_aw']."</option>";
echo "<option value='AU'".($country_code == "AU" ? " selected='selected'" : "").">".$locale['ccode_au']."</option>";
echo "<option value='AT'".($country_code == "AT" ? " selected='selected'" : "").">".$locale['ccode_at']."</option>";
echo "<option value='AZ'".($country_code == "AZ" ? " selected='selected'" : "").">".$locale['ccode_az']."</option>";
echo "<option value='BS'".($country_code == "BS" ? " selected='selected'" : "").">".$locale['ccode_bs']."</option>";
echo "<option value='BH'".($country_code == "BH" ? " selected='selected'" : "").">".$locale['ccode_bh']."</option>";
echo "<option value='BD'".($country_code == "BD" ? " selected='selected'" : "").">".$locale['ccode_bd']."</option>";
echo "<option value='BB'".($country_code == "BB" ? " selected='selected'" : "").">".$locale['ccode_bb']."</option>";
echo "<option value='BY'".($country_code == "BY" ? " selected='selected'" : "").">".$locale['ccode_by']."</option>";
echo "<option value='BE'".($country_code == "BE" ? " selected='selected'" : "").">".$locale['ccode_be']."</option>";
echo "<option value='BZ'".($country_code == "BZ" ? " selected='selected'" : "").">".$locale['ccode_bz']."</option>";
echo "<option value='BJ'".($country_code == "BJ" ? " selected='selected'" : "").">".$locale['ccode_bj']."</option>";
echo "<option value='BM'".($country_code == "BM" ? " selected='selected'" : "").">".$locale['ccode_bm']."</option>";
echo "<option value='BT'".($country_code == "BT" ? " selected='selected'" : "").">".$locale['ccode_bt']."</option>";
echo "<option value='BO'".($country_code == "BO" ? " selected='selected'" : "").">".$locale['ccode_bo']."</option>";
echo "<option value='BA'".($country_code == "BA" ? " selected='selected'" : "").">".$locale['ccode_ba']."</option>";
echo "<option value='BW'".($country_code == "BW" ? " selected='selected'" : "").">".$locale['ccode_bw']."</option>";
echo "<option value='BV'".($country_code == "BV" ? " selected='selected'" : "").">".$locale['ccode_bv']."</option>";
echo "<option value='BR'".($country_code == "BR" ? " selected='selected'" : "").">".$locale['ccode_br']."</option>";
echo "<option value='IO'".($country_code == "IO" ? " selected='selected'" : "").">".$locale['ccode_io']."</option>";
echo "<option value='BN'".($country_code == "BN" ? " selected='selected'" : "").">".$locale['ccode_bn']."</option>";
echo "<option value='BG'".($country_code == "BG" ? " selected='selected'" : "").">".$locale['ccode_bg']."</option>";
echo "<option value='BF'".($country_code == "BF" ? " selected='selected'" : "").">".$locale['ccode_bf']."</option>";
echo "<option value='BI'".($country_code == "BI" ? " selected='selected'" : "").">".$locale['ccode_bi']."</option>";
echo "<option value='KH'".($country_code == "KH" ? " selected='selected'" : "").">".$locale['ccode_kh']."</option>";
echo "<option value='CM'".($country_code == "CM" ? " selected='selected'" : "").">".$locale['ccode_cm']."</option>";
echo "<option value='CA'".($country_code == "CA" ? " selected='selected'" : "").">".$locale['ccode_ca']."</option>";
echo "<option value='CV'".($country_code == "CV" ? " selected='selected'" : "").">".$locale['ccode_cv']."</option>";
echo "<option value='KY'".($country_code == "KY" ? " selected='selected'" : "").">".$locale['ccode_ky']."</option>";
echo "<option value='CF'".($country_code == "CF" ? " selected='selected'" : "").">".$locale['ccode_cf']."</option>";
echo "<option value='TD'".($country_code == "TD" ? " selected='selected'" : "").">".$locale['ccode_td']."</option>";
echo "<option value='CD'".($country_code == "CD" ? " selected='selected'" : "").">".$locale['ccode_cd']."</option>";
echo "<option value='CL'".($country_code == "CL" ? " selected='selected'" : "").">".$locale['ccode_cl']."</option>";
echo "<option value='CN'".($country_code == "CN" ? " selected='selected'" : "").">".$locale['ccode_cn']."</option>";
echo "<option value='CX'".($country_code == "CX" ? " selected='selected'" : "").">".$locale['ccode_cx']."</option>";
echo "<option value='CS'".($country_code == "CS" ? " selected='selected'" : "").">".$locale['ccode_cs']."</option>";
echo "<option value='CO'".($country_code == "CO" ? " selected='selected'" : "").">".$locale['ccode_co']."</option>";
echo "<option value='CC'".($country_code == "CC" ? " selected='selected'" : "").">".$locale['ccode_cc']."</option>";
echo "<option value='KM'".($country_code == "KM" ? " selected='selected'" : "").">".$locale['ccode_km']."</option>";
echo "<option value='CG'".($country_code == "CG" ? " selected='selected'" : "").">".$locale['ccode_cg']."</option>";
echo "<option value='CK'".($country_code == "CK" ? " selected='selected'" : "").">".$locale['ccode_ck']."</option>";
echo "<option value='CR'".($country_code == "CR" ? " selected='selected'" : "").">".$locale['ccode_cr']."</option>";
echo "<option value='CI'".($country_code == "CI" ? " selected='selected'" : "").">".$locale['ccode_ci']."</option>";
echo "<option value='HR'".($country_code == "HR" ? " selected='selected'" : "").">".$locale['ccode_hr']."</option>";
echo "<option value='CU'".($country_code == "CU" ? " selected='selected'" : "").">".$locale['ccode_cu']."</option>";
echo "<option value='CB'".($country_code == "CB" ? " selected='selected'" : "").">".$locale['ccode_cb']."</option>";
echo "<option value='CY'".($country_code == "CY" ? " selected='selected'" : "").">".$locale['ccode_cy']."</option>";
echo "<option value='CZ'".($country_code == "CZ" ? " selected='selected'" : "").">".$locale['ccode_cz']."</option>";
echo "<option value='DK'".($country_code == "DK" ? " selected='selected'" : "").">".$locale['ccode_dk']."</option>";
echo "<option value='DJ'".($country_code == "DJ" ? " selected='selected'" : "").">".$locale['ccode_dj']."</option>";
echo "<option value='DM'".($country_code == "DM" ? " selected='selected'" : "").">".$locale['ccode_dm']."</option>";
echo "<option value='DO'".($country_code == "DO" ? " selected='selected'" : "").">".$locale['ccode_do']."</option>";
echo "<option value='TM'".($country_code == "TM" ? " selected='selected'" : "").">".$locale['ccode_tm']."</option>";
echo "<option value='EC'".($country_code == "EC" ? " selected='selected'" : "").">".$locale['ccode_ec']."</option>";
echo "<option value='EG'".($country_code == "EG" ? " selected='selected'" : "").">".$locale['ccode_eg']."</option>";
echo "<option value='SV'".($country_code == "SV" ? " selected='selected'" : "").">".$locale['ccode_sv']."</option>";
echo "<option value='GQ'".($country_code == "GQ" ? " selected='selected'" : "").">".$locale['ccode_gq']."</option>";
echo "<option value='ER'".($country_code == "ER" ? " selected='selected'" : "").">".$locale['ccode_er']."</option>";
echo "<option value='EE'".($country_code == "EE" ? " selected='selected'" : "").">".$locale['ccode_ee']."</option>";
echo "<option value='ET'".($country_code == "ET" ? " selected='selected'" : "").">".$locale['ccode_et']."</option>";
echo "<option value='FK'".($country_code == "FK" ? " selected='selected'" : "").">".$locale['ccode_fk']."</option>";
echo "<option value='FO'".($country_code == "FO" ? " selected='selected'" : "").">".$locale['ccode_fo']."</option>";
echo "<option value='FJ'".($country_code == "FJ" ? " selected='selected'" : "").">".$locale['ccode_fj']."</option>";
echo "<option value='FI'".($country_code == "FI" ? " selected='selected'" : "").">".$locale['ccode_fi']."</option>";
echo "<option value='FR'".($country_code == "FR" ? " selected='selected'" : "").">".$locale['ccode_fr']."</option>";
echo "<option value='GF'".($country_code == "GF" ? " selected='selected'" : "").">".$locale['ccode_gf']."</option>";
echo "<option value='PF'".($country_code == "PF" ? " selected='selected'" : "").">".$locale['ccode_pf']."</option>";
echo "<option value='TF'".($country_code == "TF" ? " selected='selected'" : "").">".$locale['ccode_tf']."</option>";
echo "<option value='GA'".($country_code == "GA" ? " selected='selected'" : "").">".$locale['ccode_ga']."</option>";
echo "<option value='GM'".($country_code == "GM" ? " selected='selected'" : "").">".$locale['ccode_gm']."</option>";
echo "<option value='GE'".($country_code == "GE" ? " selected='selected'" : "").">".$locale['ccode_ge']."</option>";
echo "<option value='DE'".($country_code == "DE" ? " selected='selected'" : "").">".$locale['ccode_de']."</option>";
echo "<option value='GH'".($country_code == "GH" ? " selected='selected'" : "").">".$locale['ccode_gh']."</option>";
echo "<option value='GI'".($country_code == "GI" ? " selected='selected'" : "").">".$locale['ccode_gi']."</option>";
echo "<option value='GR'".($country_code == "GR" ? " selected='selected'" : "").">".$locale['ccode_gr']."</option>";
echo "<option value='GL'".($country_code == "GL" ? " selected='selected'" : "").">".$locale['ccode_gl']."</option>";
echo "<option value='GD'".($country_code == "GD" ? " selected='selected'" : "").">".$locale['ccode_gd']."</option>";
echo "<option value='GP'".($country_code == "GP" ? " selected='selected'" : "").">".$locale['ccode_gp']."</option>";
echo "<option value='GU'".($country_code == "GU" ? " selected='selected'" : "").">".$locale['ccode_gu']."</option>";
echo "<option value='GT'".($country_code == "GT" ? " selected='selected'" : "").">".$locale['ccode_gt']."</option>";
echo "<option value='GN'".($country_code == "GN" ? " selected='selected'" : "").">".$locale['ccode_gn']."</option>";
echo "<option value='GW'".($country_code == "GW" ? " selected='selected'" : "").">".$locale['ccode_gw']."</option>";
echo "<option value='GY'".($country_code == "GY" ? " selected='selected'" : "").">".$locale['ccode_gy']."</option>";
echo "<option value='HT'".($country_code == "HT" ? " selected='selected'" : "").">".$locale['ccode_ht']."</option>";
echo "<option value='HM'".($country_code == "HM" ? " selected='selected'" : "").">".$locale['ccode_hm']."</option>";
echo "<option value='HN'".($country_code == "HN" ? " selected='selected'" : "").">".$locale['ccode_hn']."</option>";
echo "<option value='HK'".($country_code == "HK" ? " selected='selected'" : "").">".$locale['ccode_hk']."</option>";
echo "<option value='HU'".($country_code == "HU" ? " selected='selected'" : "").">".$locale['ccode_hu']."</option>";
echo "<option value='IS'".($country_code == "IS" ? " selected='selected'" : "").">".$locale['ccode_is']."</option>";
echo "<option value='IN'".($country_code == "IN" ? " selected='selected'" : "").">".$locale['ccode_in']."</option>";
echo "<option value='ID'".($country_code == "ID" ? " selected='selected'" : "").">".$locale['ccode_id']."</option>";
echo "<option value='IR'".($country_code == "IR" ? " selected='selected'" : "").">".$locale['ccode_ir']."</option>";
echo "<option value='IQ'".($country_code == "IQ" ? " selected='selected'" : "").">".$locale['ccode_iq']."</option>";
echo "<option value='IE'".($country_code == "IE" ? " selected='selected'" : "").">".$locale['ccode_ie']."</option>";
echo "<option value='IL'".($country_code == "IL" ? " selected='selected'" : "").">".$locale['ccode_il']."</option>";
echo "<option value='IT'".($country_code == "IT" ? " selected='selected'" : "").">".$locale['ccode_it']."</option>";
echo "<option value='JM'".($country_code == "JM" ? " selected='selected'" : "").">".$locale['ccode_jm']."</option>";
echo "<option value='JP'".($country_code == "JP" ? " selected='selected'" : "").">".$locale['ccode_jp']."</option>";
echo "<option value='JO'".($country_code == "JO" ? " selected='selected'" : "").">".$locale['ccode_jo']."</option>";
echo "<option value='KZ'".($country_code == "KZ" ? " selected='selected'" : "").">".$locale['ccode_kz']."</option>";
echo "<option value='KE'".($country_code == "KE" ? " selected='selected'" : "").">".$locale['ccode_ke']."</option>";
echo "<option value='KI'".($country_code == "KI" ? " selected='selected'" : "").">".$locale['ccode_ki']."</option>";
echo "<option value='KP'".($country_code == "KP" ? " selected='selected'" : "").">".$locale['ccode_kp']."</option>";
echo "<option value='KR'".($country_code == "KR" ? " selected='selected'" : "").">".$locale['ccode_kr']."</option>";
echo "<option value='KW'".($country_code == "KW" ? " selected='selected'" : "").">".$locale['ccode_kw']."</option>";
echo "<option value='KG'".($country_code == "KG" ? " selected='selected'" : "").">".$locale['ccode_kg']."</option>";
echo "<option value='LA'".($country_code == "LA" ? " selected='selected'" : "").">".$locale['ccode_la']."</option>";
echo "<option value='LV'".($country_code == "LV" ? " selected='selected'" : "").">".$locale['ccode_lv']."</option>";
echo "<option value='LB'".($country_code == "LB" ? " selected='selected'" : "").">".$locale['ccode_lb']."</option>";
echo "<option value='LS'".($country_code == "LS" ? " selected='selected'" : "").">".$locale['ccode_ls']."</option>";
echo "<option value='LR'".($country_code == "LR" ? " selected='selected'" : "").">".$locale['ccode_lr']."</option>";
echo "<option value='LY'".($country_code == "LY" ? " selected='selected'" : "").">".$locale['ccode_ly']."</option>";
echo "<option value='LI'".($country_code == "LI" ? " selected='selected'" : "").">".$locale['ccode_li']."</option>";
echo "<option value='LT'".($country_code == "LT" ? " selected='selected'" : "").">".$locale['ccode_lt']."</option>";
echo "<option value='LU'".($country_code == "LU" ? " selected='selected'" : "").">".$locale['ccode_lu']."</option>";
echo "<option value='MO'".($country_code == "MO" ? " selected='selected'" : "").">".$locale['ccode_mo']."</option>";
echo "<option value='MK'".($country_code == "MK" ? " selected='selected'" : "").">".$locale['ccode_mk']."</option>";
echo "<option value='MG'".($country_code == "MG" ? " selected='selected'" : "").">".$locale['ccode_mg']."</option>";
echo "<option value='MY'".($country_code == "MY" ? " selected='selected'" : "").">".$locale['ccode_my']."</option>";
echo "<option value='MW'".($country_code == "MW" ? " selected='selected'" : "").">".$locale['ccode_mw']."</option>";
echo "<option value='MV'".($country_code == "MV" ? " selected='selected'" : "").">".$locale['ccode_mv']."</option>";
echo "<option value='ML'".($country_code == "ML" ? " selected='selected'" : "").">".$locale['ccode_ml']."</option>";
echo "<option value='MT'".($country_code == "MT" ? " selected='selected'" : "").">".$locale['ccode_mt']."</option>";
echo "<option value='MH'".($country_code == "MH" ? " selected='selected'" : "").">".$locale['ccode_mh']."</option>";
echo "<option value='MQ'".($country_code == "MQ" ? " selected='selected'" : "").">".$locale['ccode_mq']."</option>";
echo "<option value='MR'".($country_code == "MR" ? " selected='selected'" : "").">".$locale['ccode_mr']."</option>";
echo "<option value='MU'".($country_code == "MU" ? " selected='selected'" : "").">".$locale['ccode_mu']."</option>";
echo "<option value='YT'".($country_code == "YT" ? " selected='selected'" : "").">".$locale['ccode_yt']."</option>";
echo "<option value='MX'".($country_code == "MX" ? " selected='selected'" : "").">".$locale['ccode_mx']."</option>";
echo "<option value='FM'".($country_code == "FM" ? " selected='selected'" : "").">".$locale['ccode_fm']."</option>";
echo "<option value='MD'".($country_code == "MD" ? " selected='selected'" : "").">".$locale['ccode_md']."</option>";
echo "<option value='MC'".($country_code == "MC" ? " selected='selected'" : "").">".$locale['ccode_mc']."</option>";
echo "<option value='MN'".($country_code == "MN" ? " selected='selected'" : "").">".$locale['ccode_mn']."</option>";
echo "<option value='ME'".($country_code == "ME" ? " selected='selected'" : "").">".$locale['ccode_me']."</option>";
echo "<option value='MS'".($country_code == "MS" ? " selected='selected'" : "").">".$locale['ccode_ms']."</option>";
echo "<option value='MA'".($country_code == "MA" ? " selected='selected'" : "").">".$locale['ccode_ma']."</option>";
echo "<option value='MZ'".($country_code == "MZ" ? " selected='selected'" : "").">".$locale['ccode_mz']."</option>";
echo "<option value='MM'".($country_code == "MN" ? " selected='selected'" : "").">".$locale['ccode_mm']."</option>";
echo "<option value='NA'".($country_code == "NA" ? " selected='selected'" : "").">".$locale['ccode_na']."</option>";
echo "<option value='NR'".($country_code == "NR" ? " selected='selected'" : "").">".$locale['ccode_nr']."</option>";
echo "<option value='NP'".($country_code == "NP" ? " selected='selected'" : "").">".$locale['ccode_np']."</option>";
echo "<option value='AN'".($country_code == "AN" ? " selected='selected'" : "").">".$locale['ccode_an']."</option>";
echo "<option value='NL'".($country_code == "NL" ? " selected='selected'" : "").">".$locale['ccode_nl']."</option>";
echo "<option value='NC'".($country_code == "NC" ? " selected='selected'" : "").">".$locale['ccode_nc']."</option>";
echo "<option value='NZ'".($country_code == "NZ" ? " selected='selected'" : "").">".$locale['ccode_nz']."</option>";
echo "<option value='NI'".($country_code == "NI" ? " selected='selected'" : "").">".$locale['ccode_ni']."</option>";
echo "<option value='NE'".($country_code == "NE" ? " selected='selected'" : "").">".$locale['ccode_ne']."</option>";
echo "<option value='NG'".($country_code == "NG" ? " selected='selected'" : "").">".$locale['ccode_ng']."</option>";
echo "<option value='NU'".($country_code == "NU" ? " selected='selected'" : "").">".$locale['ccode_nu']."</option>";
echo "<option value='NF'".($country_code == "NF" ? " selected='selected'" : "").">".$locale['ccode_nf']."</option>";
echo "<option value='NO'".($country_code == "NO" ? " selected='selected'" : "").">".$locale['ccode_no']."</option>";
echo "<option value='MP'".($country_code == "MP" ? " selected='selected'" : "").">".$locale['ccode_mp']."</option>";
echo "<option value='OM'".($country_code == "OM" ? " selected='selected'" : "").">".$locale['ccode_om']."</option>";
echo "<option value='PK'".($country_code == "PK" ? " selected='selected'" : "").">".$locale['ccode_pk']."</option>";
echo "<option value='PW'".($country_code == "PW" ? " selected='selected'" : "").">".$locale['ccode_pw']."</option>";
echo "<option value='PS'".($country_code == "PS" ? " selected='selected'" : "").">".$locale['ccode_ps']."</option>";
echo "<option value='PA'".($country_code == "PA" ? " selected='selected'" : "").">".$locale['ccode_pa']."</option>";
echo "<option value='PG'".($country_code == "PG" ? " selected='selected'" : "").">".$locale['ccode_pg']."</option>";
echo "<option value='PY'".($country_code == "PY" ? " selected='selected'" : "").">".$locale['ccode_py']."</option>";
echo "<option value='PE'".($country_code == "PE" ? " selected='selected'" : "").">".$locale['ccode_pe']."</option>";
echo "<option value='PH'".($country_code == "PH" ? " selected='selected'" : "").">".$locale['ccode_ph']."</option>";
echo "<option value='PN'".($country_code == "PN" ? " selected='selected'" : "").">".$locale['ccode_pn']."</option>";
echo "<option value='PL'".($country_code == "PL" ? " selected='selected'" : "").">".$locale['ccode_pl']."</option>";
echo "<option value='PT'".($country_code == "PT" ? " selected='selected'" : "").">".$locale['ccode_pt']."</option>";
echo "<option value='PR'".($country_code == "PR" ? " selected='selected'" : "").">".$locale['ccode_pr']."</option>";
echo "<option value='QA'".($country_code == "QA" ? " selected='selected'" : "").">".$locale['ccode_qa']."</option>";
echo "<option value='RE'".($country_code == "RE" ? " selected='selected'" : "").">".$locale['ccode_re']."</option>";
echo "<option value='RO'".($country_code == "RO" ? " selected='selected'" : "").">".$locale['ccode_ro']."</option>";
echo "<option value='RU'".($country_code == "RU" ? " selected='selected'" : "").">".$locale['ccode_ru']."</option>";
echo "<option value='RW'".($country_code == "RW" ? " selected='selected'" : "").">".$locale['ccode_rw']."</option>";
echo "<option value='SH'".($country_code == "SH" ? " selected='selected'" : "").">".$locale['ccode_sh']."</option>";
echo "<option value='KN'".($country_code == "KN" ? " selected='selected'" : "").">".$locale['ccode_kn']."</option>";
echo "<option value='LC'".($country_code == "LC" ? " selected='selected'" : "").">".$locale['ccode_lc']."</option>";
echo "<option value='PM'".($country_code == "PM" ? " selected='selected'" : "").">".$locale['ccode_pm']."</option>";
echo "<option value='VC'".($country_code == "VC" ? " selected='selected'" : "").">".$locale['ccode_vc']."</option>";
echo "<option value='WS'".($country_code == "WS" ? " selected='selected'" : "").">".$locale['ccode_ws']."</option>";
echo "<option value='SM'".($country_code == "SM" ? " selected='selected'" : "").">".$locale['ccode_sm']."</option>";
echo "<option value='ST'".($country_code == "ST" ? " selected='selected'" : "").">".$locale['ccode_st']."</option>";
echo "<option value='SA'".($country_code == "SA" ? " selected='selected'" : "").">".$locale['ccode_sa']."</option>";
echo "<option value='SN'".($country_code == "SN" ? " selected='selected'" : "").">".$locale['ccode_sn']."</option>";
echo "<option value='SC'".($country_code == "SC" ? " selected='selected'" : "").">".$locale['ccode_sc']."</option>";
echo "<option value='XS'".($country_code == "XS" ? " selected='selected'" : "").">".$locale['ccode_xs']."</option>";
echo "<option value='SL'".($country_code == "SL" ? " selected='selected'" : "").">".$locale['ccode_sl']."</option>";
echo "<option value='SG'".($country_code == "SG" ? " selected='selected'" : "").">".$locale['ccode_sg']."</option>";
echo "<option value='SK'".($country_code == "SK" ? " selected='selected'" : "").">".$locale['ccode_sk']."</option>";
echo "<option value='SI'".($country_code == "SI" ? " selected='selected'" : "").">".$locale['ccode_si']."</option>";
echo "<option value='SB'".($country_code == "SB" ? " selected='selected'" : "").">".$locale['ccode_sb']."</option>";
echo "<option value='OI'".($country_code == "OI" ? " selected='selected'" : "").">".$locale['ccode_oi']."</option>";
echo "<option value='ZA'".($country_code == "ZA" ? " selected='selected'" : "").">".$locale['ccode_za']."</option>";
echo "<option value='GS'".($country_code == "GS" ? " selected='selected'" : "").">".$locale['ccode_gs']."</option>";
echo "<option value='ES'".($country_code == "ES" ? " selected='selected'" : "").">".$locale['ccode_es']."</option>";
echo "<option value='LK'".($country_code == "LK" ? " selected='selected'" : "").">".$locale['ccode_lk']."</option>";
echo "<option value='SD'".($country_code == "SD" ? " selected='selected'" : "").">".$locale['ccode_sd']."</option>";
echo "<option value='SR'".($country_code == "SR" ? " selected='selected'" : "").">".$locale['ccode_sr']."</option>";
echo "<option value='SJ'".($country_code == "SJ" ? " selected='selected'" : "").">".$locale['ccode_sj']."</option>";
echo "<option value='SZ'".($country_code == "SZ" ? " selected='selected'" : "").">".$locale['ccode_sz']."</option>";
echo "<option value='SE'".($country_code == "SE" ? " selected='selected'" : "").">".$locale['ccode_se']."</option>";
echo "<option value='CH'".($country_code == "CH" ? " selected='selected'" : "").">".$locale['ccode_ch']."</option>";
echo "<option value='SY'".($country_code == "SY" ? " selected='selected'" : "").">".$locale['ccode_sy']."</option>";
echo "<option value='TA'".($country_code == "TA" ? " selected='selected'" : "").">".$locale['ccode_ta']."</option>";
echo "<option value='TW'".($country_code == "TW" ? " selected='selected'" : "").">".$locale['ccode_tw']."</option>";
echo "<option value='TJ'".($country_code == "TJ" ? " selected='selected'" : "").">".$locale['ccode_tj']."</option>";
echo "<option value='TZ'".($country_code == "TZ" ? " selected='selected'" : "").">".$locale['ccode_tz']."</option>";
echo "<option value='TH'".($country_code == "TH" ? " selected='selected'" : "").">".$locale['ccode_th']."</option>";
echo "<option value='TG'".($country_code == "TH" ? " selected='selected'" : "").">".$locale['ccode_tg']."</option>";
echo "<option value='TK'".($country_code == "TK" ? " selected='selected'" : "").">".$locale['ccode_tk']."</option>";
echo "<option value='TO'".($country_code == "TO" ? " selected='selected'" : "").">".$locale['ccode_to']."</option>";
echo "<option value='TT'".($country_code == "TT" ? " selected='selected'" : "").">".$locale['ccode_tt']."</option>";
echo "<option value='TN'".($country_code == "TN" ? " selected='selected'" : "").">".$locale['ccode_tn']."</option>";
echo "<option value='TR'".($country_code == "TR" ? " selected='selected'" : "").">".$locale['ccode_tr']."</option>";
echo "<option value='TM'".($country_code == "TM" ? " selected='selected'" : "").">".$locale['ccode_tm']."</option>";
echo "<option value='TC'".($country_code == "TC" ? " selected='selected'" : "").">".$locale['ccode_tc']."</option>";
echo "<option value='TV'".($country_code == "TV" ? " selected='selected'" : "").">".$locale['ccode_tv']."</option>";
echo "<option value='UG'".($country_code == "UG" ? " selected='selected'" : "").">".$locale['ccode_ug']."</option>";
echo "<option value='UA'".($country_code == "UA" ? " selected='selected'" : "").">".$locale['ccode_ua']."</option>";
echo "<option value='AE'".($country_code == "AE" ? " selected='selected'" : "").">".$locale['ccode_ae']."</option>";
echo "<option value='GB'".($country_code == "GB" ? " selected='selected'" : "").">".$locale['ccode_gb']."</option>";
echo "<option value='UM'".($country_code == "UM" ? " selected='selected'" : "").">".$locale['ccode_um']."</option>";
echo "<option value='US'".($country_code == "US" ? " selected='selected'" : "").">".$locale['ccode_us']."</option>";
echo "<option value='UY'".($country_code == "UY" ? " selected='selected'" : "").">".$locale['ccode_uy']."</option>";
echo "<option value='UZ'".($country_code == "UZ" ? " selected='selected'" : "").">".$locale['ccode_uz']."</option>";
echo "<option value='VU'".($country_code == "VU" ? " selected='selected'" : "").">".$locale['ccode_vu']."</option>";
echo "<option value='VA'".($country_code == "VA" ? " selected='selected'" : "").">".$locale['ccode_va']."</option>";
echo "<option value='VE'".($country_code == "VE" ? " selected='selected'" : "").">".$locale['ccode_ve']."</option>";
echo "<option value='VN'".($country_code == "VN" ? " selected='selected'" : "").">".$locale['ccode_vn']."</option>";
echo "<option value='VG'".($country_code == "VG" ? " selected='selected'" : "").">".$locale['ccode_vg']."</option>";
echo "<option value='VI'".($country_code == "VI" ? " selected='selected'" : "").">".$locale['ccode_vi']."</option>";
echo "<option value='WF'".($country_code == "WF" ? " selected='selected'" : "").">".$locale['ccode_wf']."</option>";
echo "<option value='EH'".($country_code == "EH" ? " selected='selected'" : "").">".$locale['ccode_eh']."</option>";
echo "<option value='YE'".($country_code == "YE" ? " selected='selected'" : "").">".$locale['ccode_ye']."</option>";
echo "<option value='YU'".($country_code == "YU" ? " selected='selected'" : "").">".$locale['ccode_yu']."</option>";
echo "<option value='ZR'".($country_code == "ZR" ? " selected='selected'" : "").">".$locale['ccode_zr']."</option>";
echo "<option value='ZM'".($country_code == "ZM" ? " selected='selected'" : "").">".$locale['ccode_zm']."</option>";
echo "<option value='ZW'".($country_code == "ZW" ? " selected='selected'" : "").">".$locale['ccode_zw']."</option>";
echo "</select></td>";
echo "</tr>";
echo "<tr>
<td align='left' style='width:100px;'>".$locale['ESHPCHK108']."<span style='color:#ff0000;'>*</span></td>
<td align='left'><input type='text' name='region' value='$region' class='textbox' style='width:180px; !important'></td>
</tr>";
echo "<tr>
<td align='left' style='width:100px;'>".$locale['ESHPCHK109']."<span style='color:#ff0000;'>*</span></td>
<td align='left'><input type='text' name='city' value='$city' class='textbox' style='width:180px; !important'></td>
</tr>";
echo "<tr>
<td align='left' style='width:100px;'>".$locale['ESHPCHK110']."<span style='color:#ff0000;'>*</span></td>
<td align='left'><input type='text' name='address' value='$address' class='textbox' style='width:180px; !important'></td>
</tr>";
echo "<tr>
<td align='left' style='width:100px;'>".$locale['ESHPCHK111']."</td>
<td align='left'><input type='text' name='address2' value='$address2' class='textbox' style='width:180px; !important'></td>
</tr>";
echo "<tr>
<td align='left' style='width:100px;'>".$locale['ESHPCHK112']."<span style='color:#ff0000;'>*</span></td>
<td align='left'><input type='text' name='postcode' value='$postcode' class='textbox' style='width:180px; !important'></td>
</tr>";
echo "<tr>
<td align='left' style='width:100px;'>".$locale['ESHPCHK113']."</td>
<td align='left'><input type='text' name='phone' value='$phone' class='textbox' style='width:180px; !important'></td>
</tr>";
echo "<tr>
<td align='left' style='width:100px;'>".$locale['ESHPCHK114']."</td>
<td align='left'><input type='text' name='fax' value='$fax' class='textbox' style='width:180px; !important'></td>
</tr>";
echo "<tr>
<td align='left' style='width:100px;'>".$locale['ESHPCHK115']."<span style='color:#ff0000;'>*</span></td>
<td align='left'><input type='text' name='email' value='$email' class='textbox' style='width:180px; !important'></td>
</tr>";

echo "<tr>
<td align='center' colspan='2' style='padding-top:10px;'> ".$locale['ESHPCHK117']." <span style='color:#ff0000;display:inline !important;'>*</span> ";
echo "<input type='checkbox' name='agreement' value='1' /> <a class='terms' href='#terms_content' style='font-size:12px !important; vertical-align:middle !important;'> ".$locale['ESHPCHK119']."</a></td>";
echo "</tr>";

echo "<tr><td class='tbl' align='center' colspan='2' style='padding-top:20px;'><span style='color:#ff0000;'>*</span> ".$locale['ESHPCHK118']."</td>";
echo "</tr>";
echo "</table></fieldset></div>";

echo "<div style='display:none'><div id='terms_content' style='padding:10px;text-align:left'>";
echo stripslashes(nl2br($settings['eshop_terms']));
echo "</div></div>";


//Item to purchase
echo "<div style='float:right;margin-top:5px;padding:1px;'>";
echo"<fieldset style='width:100%;padding:2px;'><legend style='width:90% !important;'>&nbsp; ".$locale['ESHPB107']." &nbsp;</legend>";
echo "<table width='345' height='100%' align='center' cellspacing='0' cellpadding='2' border='0'>";

	$itemdata = dbarray(dbquery("SELECT * FROM ".DB_ESHOP." WHERE id='".$_GET['id']."'"));

    echo "<tr><td class='tbl' align='left'  width='25%'><a href='".BASEDIR."eshop/eshop.php?product=".$itemdata['id']."'><img src='".($itemdata['thumb'] ? "".checkeShpImageExists(INFUSIONS."eshop/pictures/".$itemdata['thumb']."")."" : "".BASEDIR."eshop/img/nopic_thumb.gif")."' alt='' width='60' border='0' /></a></td>";
 	if ($settings['eshop_pretext'] == "1") {
	echo "<td class='tbl' align='left' width='50%'><div style='padding:4px;word-wrap: break-word;vertical-align:middle;width:".$settings['eshop_pretext_w'].";'>".parseubb(nl2br($itemdata['introtext']))."</div></td>";
	} else {
	echo "<td class='tbl' align='left' width='50%'><div style='padding:4px;word-wrap: break-word;vertical-align:middle;'>".$itemdata['title']."</div></td>";
	}
    echo "<td class='tbl' align='left' width='25%'>".($itemdata['xprice'] ? $itemdata['xprice'] : $itemdata['price'])." ".$settings['eshop_currency']."</td>
	</tr>";
echo "<tr><td><div style='margin-top:5px;'></div></td></tr>";
echo "</table></fieldset></div>";


//Each payment option
echo "<div style='float:right;margin-top:5px;padding:1px;'>";
echo"<fieldset style='width:100%;padding:2px;'><legend style='width:90% !important;'>&nbsp; ".$locale['ESHPCHK120']." &nbsp;</legend>";
echo "<table width='345' height='100%' align='center' cellspacing='0' cellpadding='2' border='0'>";
  
$result = dbquery("SELECT * FROM ".DB_ESHOP_PAYMENTS."");
$rows = dbrows($result);

if ($rows != 0) {
$result = dbquery("SELECT * FROM ".DB_ESHOP_PAYMENTS." WHERE active='1' AND pid = '2' LIMIT 0,1");
while ($data = dbarray($result)) {
      echo "<tr><td class='tbl' align='left'  width='5%'><input type='radio' name='paymethod' id='payment_".$data['pid']."' value='".$data['pid']."' checked /></td>
      <td class='tbl' align='left' width='20%'><img src='".BASEDIR."eshop/paymentimgs/".$data['image']."' border='0' alt='' width='40' /></td>
	  <td class='tbl' align='left' width='55%'>".$data['method']." </td>
	  <td class='tbl' align='left' width='25%'>".$locale['ESHPCHK121']." <br /> ".$data['surcharge']." ".$settings['eshop_currency']."</td>
	  </tr>";
echo "<tr><td><div style='margin-top:5px;'></div></td></tr>";
 } 
} else { 
	echo $locale['ESHPCHK122']; 
}
echo "</table></fieldset></div>";

//Each shipping option
echo "<div style='float:right;margin-top:5px;padding:1px;'>";
echo"<fieldset style='width:100%;padding:2px;'><legend style='width:90% !important;'>&nbsp; ".$locale['ESHPCHK123']." &nbsp;</legend>";
echo "<table width='345' height='100%' align='center' cellspacing='0' cellpadding='2' border='0'>";

    echo "<tr><td class='tbl' align='left' width='5%'><input type='radio' name='shipping' id='0' value='0' checked /></td>
    <td class='tbl' align='center' width='70%'>".$locale['ESHPB108']."</td>
	</tr>";
    echo "<tr><td><div style='margin-top:5px;'></div></td></tr>";

echo "</table></fieldset></div>";
$price = ($itemdata['xprice'] ? $itemdata['xprice'] : $itemdata['price']);
$vat = ($price / 100) * $vat;
if ($settings['eshop_vat_default'] == "0") {
$totalincvat = $price + $vat;
} else {
$totalincvat = $price;
}

echo "<div style='float:right;margin-top:5px;padding:1px;'>";
echo"<fieldset style='width:100%;padding:2px;'><legend style='width:90% !important;'>&nbsp; ".$locale['ESHPCHK127']." &nbsp;</legend>";
echo "<table width='345' height='100%' align='center' cellspacing='0' cellpadding='2' border='0'>";
echo "<tr><td class='tbl' align='left'>
".$locale['ESHPCHK128']." 1 ".$locale['ESHPCHK129']."".($settings['eshop_vat_default'] =="1" ? "".number_format($totalincvat, 2)."" : "".number_format($price, 2)."")."  ".$settings['eshop_currency']."<br />
".$locale['ESHPCHK130']."".$settings['eshop_vat']."% : ( ".number_format($vat, 2)."  ".($settings['eshop_vat_default'] == "1" ? $locale['ESHPB109'] : $locale['ESHPB110'])." )<br />";
echo "".$locale['ESHPCHK132']." 0 ".$settings['eshop_currency']."<br />";
echo "".$locale['ESHPCHK133']." 0 ".$settings['eshop_currency']."<br />";
echo "".$locale['ESHPCHK134']."".number_format($totalincvat, 2)." ".$settings['eshop_currency']."";
echo "</td></tr>";
echo "</table></fieldset></div>";
echo "<div class='clear'></div>";
echo "<br /><center><input type='submit' name='buynow' value='".$locale['ESHPCHK135']."' class='button' /></center></form>";

echo "<script type='text/javascript'>
function ValidateForm(frm) {
	
	var acheck = $('input[name=agreement]');
    var agreementcheck = acheck.filter(':checked').val();

	if (!agreementcheck) {
		alert('".$locale['ESHPCHK189']."');
		return false;
	}
	
	
	if(frm.firstname.value=='') {
		alert('".$locale['ESHPCHK139']."');
		return false;
	}

	if(frm.lastname.value=='') {
		alert('".$locale['ESHPCHK140']."');
		return false;
	}

	if(frm.country_code.value=='') {
		alert('".$locale['ESHPCHK141']."');
		return false;
	}

	if(frm.region.value=='') {
		alert('".$locale['ESHPCHK142']."');
		return false;
	}

	if(frm.city.value=='') {
		alert('".$locale['ESHPCHK143']."');
		return false;
	}

	if(frm.address.value=='') {
		alert('".$locale['ESHPCHK144']."');
		return false;
	}

	if(frm.postcode.value=='') {
		alert('".$locale['ESHPCHK145']."');
		return false;
	}

	if(frm.email.value=='') {
		alert('".$locale['ESHPCHK146']."');
		return false;
	}
}
</script>";

closetable();
 }
} //member check
require_once THEMES."templates/footer.php";
?>