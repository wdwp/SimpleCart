<?php
# Module: Cart Made Simple - An Order Intake module for CMS - CMS Made Simple
# Copyright (c) 2008 by Duketown
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
# The module's homepage is: http://dev.cmsmadesimple.org/projects/cartms
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------

$gCms = cmsms();
if (!is_object($gCms)) exit;

$db = cmsms()->GetDb();

if (isset($params['cancel'])) {
	// Redirect needed to cart
	$this->RedirectForFrontend($id, $returnid, 'cart', $params, true);
}

if (isset($params['submit_register'])) {
	$submit_register = $params['submit_register'];
}

if (isset($params['submit_login'])) {
	$submit_login = $params['submit_login'];
}

if (!isset($params['addresscountry'])) {
	// First time here, show the first country of the selectable countries
	$selectedcountry = -1;
	$val = '';
} else {
	// Set the selected country (otherwise first one will be shown)
	$val = $params['addresscountry'];
}

if (isset($params['firstname'])) $firstname = trim($params['firstname']);
if (isset($params['lastname'])) $lastname = trim($params['lastname']);
if (isset($params['email'])) $email = trim($params['email']);
if (isset($params['addressstreet'])) $addressstreet = trim($params['addressstreet']);
if (isset($params['addresscity'])) $addresscity = trim($params['addresscity']);
if (isset($params['addressstate'])) $addressstate = trim($params['addressstate']);
if (isset($params['addresszip'])) $addresszip = trim($params['addresszip']);
if (isset($params['addresscountry'])) $addresscountry = $params['addresscountry'];
if (isset($params['telephone'])) $telephone = trim($params['telephone']);
if (isset($params['deliverymethod'])) $deliverymethod = $params['deliverymethod'];
if (isset($params['agreetoterms'])) $agreetoterms = $params['agreetoterms'];
if (isset($params['paymentmethod'])) $paymentmethod = $params['paymentmethod'];
if (isset($params['coupon_code'])) $coupon_code = trim($params['coupon_code']);

// Handle any errors coming from orderspeedsave
if (isset($params['efn']) && $params['efn']) {
	$this->smarty->assign('firstname_error', $this->Lang('errorfirstnameblank'));
}
if (isset($params['eln']) && $params['eln']) {
	$this->smarty->assign('lastname_error', $this->Lang('errorlastnameblank'));
}
if (isset($params['eem']) && $params['eem']) {
	$this->smarty->assign('email_error', $this->Lang('erroremailformat'));
}
if (isset($params['eas']) && $params['eas']) {
	$this->smarty->assign('addressstreet_error', $this->Lang('erroraddressstreetblank'));
}
if (isset($params['eac']) && $params['eac']) {
	$this->smarty->assign('addresscity_error', $this->Lang('erroraddresscityblank'));
}
if (isset($params['east']) && $params['east']) {
	$this->smarty->assign('addressstate_error', $this->Lang('erroraddressstateblank'));
}
if (isset($params['eaz']) && $params['eaz']) {
	$this->smarty->assign('addresszip_error', $this->Lang('erroraddresszipblank'));
}
if (isset($params['eaco']) && $params['eaco']) {
	$this->smarty->assign('addresscountry_error', $this->Lang('erroraddresscountryblank'));
}
if (isset($params['ete']) && $params['ete']) {
	$this->smarty->assign('telephone_error', $this->Lang('errortelephoneblank'));
}
if (isset($params['eatt']) && $params['eatt']) {
	$contenttradetext = $this->GetPreference('contenttradetext', '');
	if ($contenttradetext != '') {
		// Retrieve the page containing the trading terms information
		$query = 'SELECT content_id, content_alias FROM ' . cms_db_prefix() . 'content WHERE menu_text = ?';
		$dbresult = $db->Execute($query, array($contenttradetext));
		$row = $dbresult->FetchRow();
		if ($row) {
			$contenttradetext = $this->GetPreference('contenttradetext', $this->Lang('erroragreetotermsblank'));
			$agreecontentlink = '<a href=' . $config['root_url'] . '/index.php?page=' . $row['content_alias'] . ' target="_blank">' . $contenttradetext . '</a>';
			// Prepare hyperlink to the the page as set up in the options
			$agreetotermstext = $agreecontentlink;
		} else {
			$agreetotermstext = $this->Lang('erroragreetotermsblank');
		}
	} else {
		$agreetotermstext = $this->Lang('erroragreetotermsblank');
	}
	$this->smarty->assign('agreetoterms_error', $agreetotermstext);
	$this->smarty->assign('agreeterms_error', $this->Lang('erroragreetoterms'));
}
if (isset($params['edc']) && $params['edc']) {
	$this->smarty->assign('coupon_code_error', $this->Lang('errornonvalidcoupon'));
}
// Validate coupon code used
$smarty->assign('SCouponsAvail', false);
$SCoupons = &$this->GetModuleInstance('SCoupons');
if ($SCoupons) {
	$smarty->assign('SCouponsAvail', true);
}

// Check if the user allready has logged in. If not temporary save the customer address.
$feusers = &$this->GetModuleInstance('FrontEndUsers');
$userloggedin = $feusers->loggedin();
$this->smarty->assign('userloggedin', $userloggedin);
if (!$userloggedin) {
	$this->smarty->assign('welcometitle', $this->Lang('title_welcomelogin'));
	$errorfound = false;
	// Perform a check on all the entries. All are mandatory
	if (isset($params['submit'])) {

		// Fill separate message for header
		if (!$errorfound) {
			$this->smarty->assign('welcometitle', $this->Lang('checkshipinfo'));
		}
	}
} else {
	$user_id = $feusers->LoggedInId();
	$firstname = $feusers->GetUserPropertyFull('firstname', $user_id, false);
	$lastname = $feusers->GetUserPropertyFull('surname', $user_id, false);
	$email = $feusers->GetUserPropertyFull('email', $user_id, false);
	$addressstreet = $feusers->GetUserPropertyFull('addressstreet', $user_id, false);
	$feusers->SetUserPropertyFull('addressstreet', $addressstreet, $user_id);
	$addresscity = $feusers->GetUserPropertyFull('addresscity', $user_id, false);
	$feusers->SetUserPropertyFull('addresscity', $addresscity, $user_id);
	$addressstate = $feusers->GetUserPropertyFull('addressstate', $user_id, false);
	$feusers->SetUserPropertyFull('addressstate', $addressstate, $user_id);
	$addresszip = $feusers->GetUserPropertyFull('addresszip', $user_id, false);
	$feusers->SetUserPropertyFull('addresszip', $addresszip, $user_id);
	$addresscountry = $feusers->GetUserPropertyFull('addresscountry', $user_id, false);
	$feusers->SetUserPropertyFull('addresscountry', $addresscountry, $user_id);
	// Check if the telephone has been filled

	// Try to retrieve it from the customers properties
	$telephone = $feusers->GetUserPropertyFull('telephone', $user_id, false);
	if ($telephone == '' && $this->GetPreference('mandatorytelephone', false)) {
		$this->smarty->assign('telephone_error', $this->Lang('errortelephoneblank'));
		$errorfound = true;
	} else {
		$feusers->SetUserPropertyFull('telephone', $telephone, $user_id);
	}

	$billfirstname = $feusers->GetUserPropertyFull('billfirstname', $user_id, false);
	if ($billfirstname == '') {
		$billfirstname = $firstname;
		$feusers->SetUserPropertyFull('billfirstname', $billfirstname, $user_id);
	}
	$billlastname = $feusers->GetUserPropertyFull('billsurname', $user_id, false);
	if ($billlastname == '') {
		$billlastname = $lastname;
		$feusers->SetUserPropertyFull('billsurname', $billlastname, $user_id);
	}
	$billaddressstreet = $feusers->GetUserPropertyFull('billaddressstreet', $user_id, false);
	if ($billaddressstreet == '') {
		$billaddressstreet = $addressstreet;
		$feusers->SetUserPropertyFull('billaddressstreet', $billaddressstreet, $user_id);
	}
	$billaddresscity = $feusers->GetUserPropertyFull('billaddresscity', $user_id, false);
	if ($billaddresscity == '') {
		$billaddresscity = $addresscity;
		$feusers->SetUserPropertyFull('billaddresscity', $billaddresscity, $user_id);
	}
	$billaddressstate = $feusers->GetUserPropertyFull('billaddressstate', $user_id, false);
	if ($billaddressstate == '') {
		$billaddressstate = $addressstate;
		$feusers->SetUserPropertyFull('billaddressstate', $billaddressstate, $user_id);
	}
	$billaddresszip = $feusers->GetUserPropertyFull('billaddresszip', $user_id, false);
	if ($billaddresszip == '') {
		$billaddresszip = $addresszip;
		$feusers->SetUserPropertyFull('billaddresszip', $billaddresszip, $user_id);
	}
	$billaddresscountry = $feusers->GetUserPropertyFull('billaddresscountry', $user_id, false);
	if ($billaddresscountry == '') {
		$billaddresscountry = $addresscountry;
		$feusers->SetUserPropertyFull('billaddresscountry', $billaddresscountry, $user_id);
	}
	$this->smarty->assign('welcometitle', $this->Lang('checkshipinfo'));
	$this->smarty->assign('continue', $this->CreateInputSubmit($id, 'continue', $this->Lang('continuestep2')));
}

// Set the error indicator so orderspeedsave can handle the perform navigation for errors
$params['errorfound'] = isset($errorfound) ? $errorfound : '';

if (isset($params['orderremark'])) $orderremark = $params['orderremark'];

// Retrieve the net weight, price of the cart.
// Weight is used for the delivery methods
// Price is used to show visitor the total cart amount
$cartinfo = $this->orders->GetCartInfo();

$currency = $this->GetPreference('cartcurrency', 'Eur');
// Check if VAT is to be calculated (is set up in SimpleShop)
$shopms = &$this->GetModuleInstance('SimpleShop');
if ($shopms) {
	$priceinclvat = (int) $shopms->GetPreference('pricesinclvat', 0);
} else {
	$priceinclvat = 1;
}

// Handle delivery part

$content_id = 0;
$contenttradeterms = $this->GetPreference('contenttradeterms', '');
if ($contenttradeterms != '') {
	// Retrieve the page containing the trading terms information
	$query = 'SELECT content_id, content_alias FROM ' . cms_db_prefix() . 'content WHERE menu_text = ?';
	$dbresult = $db->Execute($query, array($contenttradeterms));
	$row = $dbresult->FetchRow();
	$content_id = $row['content_id'];
	$contenttradetext = $this->GetPreference('contenttradetext', $this->Lang('erroragreetotermsblank'));
	$agreecontentlink = '<a href=' . $config['root_url'] . '/index.php?page=' . $row['content_alias'] . ' target="_blank">' . $contenttradetext . '</a>';
}

// Prepare a list of possible delivery methods
$firstmethodfound = false;
$deliverymethodlist = array();
$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_cartms_shippingprovider WHERE active = 1 ORDER BY shipprovdesc';
$dbresult = $db->Execute($query);

while ($dbresult && $row = $dbresult->FetchRow()) {

	$freightcost = $row['shipprovprice'] + $row['shippriceperweight'] * $cartinfo['totalnetweight'];
	$freightcost = $this->orders->FormatAmount($freightcost);
	// Place delivery cost in brackets on right side of delivery code
	$deliverymethodlist[$row['shipprovdesc'] . ' (' . $this->Lang('shipcostextra') . ' ' . $freightcost . ')'] = $row['shipprovcode'];

	// Save the agreements value of the first found delivery method, since this might be a mandatory situation
	if (!$firstmethodfound) {
		if ($row['agreetoterms'] == 1) {
			if ($content_id > 0) {
				// Prepare hyperlink to the the page as set up in the options
				$agreetotermstext = $agreecontentlink;
			} else {
				$agreetotermstext = $this->Lang('erroragreetotermsblank');
			}
			$this->smarty->assign('agreetoterms_error', $agreetotermstext);
		}
		$firstmethodfound = true;
	}
}

// Handle payment part
// Check how many payment methods are available.
// If zero, set to default and continue to confirmation
// if only one, use it and continue to confirmation
$paymentmethodlist = array();
$paymsmodule = &$this->GetModuleInstance('SimplePayment');
if ($paymsmodule) {
	$query = 'SELECT count(*) FROM ' . cms_db_prefix() . 'module_pms_gateways
		WHERE active > 0 ORDER BY gateway_code';
	$count = $db->GetOne($query);
	switch (intval($count)) {
		case 0:
			// No active payment methods found, set it to payment upfront
			$params['paymentmethod'] = 'PAYUF';
			$this->smarty->assign('paymentmethod_input', $this->Lang('paymentupfront'));
			break;
		case 1:
			// Exactly one payment method found, retrieve it and use it for this order
			$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_pms_gateways
				WHERE active > 0 ORDER BY gateway_code';
			$dbresult = $db->Execute($query);
			$row = $dbresult->FetchRow();
			$params['paymentmethod'] = $row['gateway_code'];
			$this->smarty->assign('paymentmethod_input', $row['description']);
			break;
		default:
			// More then one payment methods found, so up to customer which one to use
			$paymentmethodlist = array();
			$paymentmethod = isset($paymentmethod) ? $paymentmethod : '';
			$paymsmodule = &$this->GetModuleInstance('SimplePayment');
			if ($paymsmodule) {
				$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_pms_gateways
					WHERE active > 0 ORDER BY gateway_code';
				$dbresult = $db->Execute($query);

				while ($dbresult && $row = $dbresult->FetchRow()) {
					$paymentmethodlist[$row['description']] = $row['gateway_code'];
				}

				$this->smarty->assign('paymentmethod_input', $this->CreateInputDropdown($id, 'paymentmethod', $paymentmethodlist, 'PAYUF', $paymentmethod));
			}
	}
} else {
	$params['paymentmethod'] = 'PAYUF';
}

$firstname = (isset($firstname) && !empty($firstname)) ? $firstname : '';
$lastname = (isset($lastname) && !empty($lastname)) ? $lastname : '';
$email  = (isset($email) && !empty($email)) ? $email : '';
$addressstreet  = (isset($addressstreet) && !empty($addressstreet)) ? $addressstreet : '';
$addresscity  = (isset($addresscity) && !empty($addresscity)) ? $addresscity : '';
$addressstate  = (isset($addressstate) && !empty($addressstate)) ? $addressstate : '';
$addresszip  = (isset($addresszip) && !empty($addresszip)) ? $addresszip : '';
$telephone  = (isset($telephone) && !empty($telephone)) ? $telephone : '';
$billfirstname  = (isset($billfirstname) && !empty($billfirstname)) ? $billfirstname : '';
$billlastname  = (isset($billlastname) && !empty($billlastname)) ? $billlastname : '';
$billaddressstreet  = (isset($billaddressstreet) && !empty($billaddressstreet)) ? $billaddressstreet : '';
$billaddresscity  = (isset($billaddresscity) && !empty($billaddresscity)) ? $billaddresscity : '';
$billaddressstate  = (isset($billaddressstate) && !empty($billaddressstate)) ? $billaddressstate : '';
$billaddresszip  = (isset($billaddresszip) && !empty($billaddresszip)) ? $billaddresszip : '';
$orderremark  = (isset($orderremark) && !empty($orderremark)) ? $orderremark : '';
$coupon_code  = (isset($coupon_code) && !empty($coupon_code)) ? $coupon_code : '';
$addresscountry = (isset($addresscountry) && !empty($addresscountry)) ? $addresscountry : '';
$billaddresscountry = (isset($billaddresscountry) && !empty($billaddresscountry)) ? $billaddresscountry : '';
$selectedcountry = (isset($selectedcountry)) ? $selectedcountry : -1;
$val = (isset($val)) ? $val : '';

// Assign variables, so admin may decide to use field on front end or not
$this->smarty->assign('mandatorystate', $this->GetPreference('mandatorystate', false));
$this->smarty->assign('mandatorytelephone', $this->GetPreference('mandatorytelephone', false));
#Display template
$this->smarty->assign('startform', $this->CreateFormStart($id, 'orderspeedsave', $returnid));
$this->smarty->assign('title_fieldset_ec', $this->Lang('title_fieldset_exist_customer'));
$this->smarty->assign('title_fieldset_nc', $this->Lang('title_fieldset_new_customer'));
$this->smarty->assign('title_username', $this->Lang('title_username'));
$this->smarty->assign('firstname_label', $this->Lang('firstname_label'));
$this->smarty->assign('firstname_input', $this->CreateInputText($id, 'firstname', $firstname, 40, 40));
$this->smarty->assign('lastname_label', $this->Lang('lastname_label'));
$this->smarty->assign('lastname_input', $this->CreateInputText($id, 'lastname', $lastname, 40, 40));
$this->smarty->assign('email_label', $this->Lang('email_label'));
$this->smarty->assign('email_input', $this->CreateInputText($id, 'email', $email, 40, 255));
// Ship to address
$this->smarty->assign('fieldsetshipto_label', $this->Lang('title_shippingaddress'));
$this->smarty->assign('addressstreet_label', $this->Lang('addressstreet_label'));
$this->smarty->assign('addressstreet_input', $this->CreateInputText($id, 'addressstreet', $addressstreet, 40, 40));
$this->smarty->assign('addresscity_label', $this->Lang('addresscity_label'));
$this->smarty->assign('addresscity_input', $this->CreateInputText($id, 'addresscity', $addresscity, 40, 40));
$this->smarty->assign('addressstate_label', $this->Lang('addressstate_label'));
$this->smarty->assign('addressstate_input', $this->CreateInputText($id, 'addressstate', $addressstate, 15, 15));
$this->smarty->assign('addresszip_label', $this->Lang('addresszip_label'));
$this->smarty->assign('addresszip_input', $this->CreateInputText($id, 'addresszip', $addresszip, 15, 15));
$this->smarty->assign('addresscountry_label', $this->Lang('addresscountry_label'));
$this->smarty->assign('addresscountry_input', $this->CreateInputDropdown(
	$id,
	'addresscountry',
	$feusers->GetSelectOptions('addresscountry', 1),
	$selectedcountry,
	$val
));
$this->smarty->assign('telephone_label', $this->Lang('telephone_label'));
$this->smarty->assign('telephone_input', $this->CreateInputText($id, 'telephone', $telephone, 35, 35));
// Bill to address
$this->smarty->assign('fieldsetbillto_label', $this->Lang('title_billingaddress'));
$this->smarty->assign('billtohide', $this->Lang('billtohide'));
$this->smarty->assign('billtoshow', $this->Lang('billtoshow'));
$this->smarty->assign('billfirstname_label', $this->Lang('firstname_label'));
$this->smarty->assign('billfirstname_input', $this->CreateInputText($id, 'billfirstname', $billfirstname, 40, 40));
$this->smarty->assign('billlastname_label', $this->Lang('lastname_label'));
$this->smarty->assign('billlastname_input', $this->CreateInputText($id, 'billlastname', $billlastname, 40, 40));
$this->smarty->assign('billaddressstreet_label', $this->Lang('billaddressstreet_label'));
$this->smarty->assign('billaddressstreet_input', $this->CreateInputText($id, 'billaddressstreet', $billaddressstreet, 40, 40));
$this->smarty->assign('billaddresscity_label', $this->Lang('billaddresscity_label'));
$this->smarty->assign('billaddresscity_input', $this->CreateInputText($id, 'billaddresscity', $billaddresscity, 40, 40));
$this->smarty->assign('billaddressstate_label', $this->Lang('billaddressstate_label'));
$this->smarty->assign('billaddressstate_input', $this->CreateInputText($id, 'billaddressstate', $billaddressstate, 15, 15));
$this->smarty->assign('billaddresszip_label', $this->Lang('billaddresszip_label'));
$this->smarty->assign('billaddresszip_input', $this->CreateInputText($id, 'billaddresszip', $billaddresszip, 15, 15));
$this->smarty->assign('billaddresscountry_label', $this->Lang('billaddresscountry_label'));
$this->smarty->assign('billaddresscountry_input', $this->CreateInputDropdown(
	$id,
	'billaddresscountry',
	$feusers->GetSelectOptions('addresscountry', 1),
	$selectedcountry,
	$val
));
$this->smarty->assign('orderremark_label', $this->Lang('orderremark_label'));
$this->smarty->assign('orderremark_input', $this->CreateTextArea(false, $id, $orderremark, 'orderremark', '', '', '', '', 40, 5));
$this->smarty->assign('coupon_code_label', $this->Lang('coupon_code_label'));
$this->smarty->assign('coupon_code_input', $this->CreateInputText($id, 'coupon_code', $coupon_code, 12, 12));

// Delivery
$this->smarty->assign('deliverymethod_label', $this->Lang('deliverymethod_label'));
$this->smarty->assign('deliverymethod_input', $this->CreateInputDropdown($id, 'deliverymethod', $deliverymethodlist, -1, ''));
$this->smarty->assign('agreetoterms_label', $this->Lang('agreetoterms_label'));
$this->smarty->assign('agreetoterms_input', $this->CreateInputCheckbox($id, 'agreetoterms', true, ''));
// Payment
$this->smarty->assign('paymentmethod_label', $this->Lang('paymentmethod_label'));
// Order amount
$this->smarty->assign('totalamount_label', $this->Lang('ordertotals'));
//$this->smarty->assign('totalamount', $this->orders->FormatAmount($cartinfo['totalnetprice']));
$this->smarty->assign('totalamount_currency', $currency);
if ($priceinclvat) {
	$this->smarty->assign('totalamount_vat', $this->Lang('vatincl'));
} else {
	$this->smarty->assign('totalamount_vat', $this->Lang('vatexcl'));
}
$this->smarty->assign('hidden', '');
$this->smarty->assign('register', $this->CreateInputSubmit($id, 'sumbit_register', $this->Lang('customer_register')));
$this->smarty->assign('login', $this->CreateInputSubmit($id, 'submit_login', $this->Lang('customer_login')));
$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('continue_checkout_process')));
$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));
$this->smarty->assign('endform', $this->CreateFormEnd());

// Set default template. If past as parameter, show that one
$template = 'cart_fe_speedcheckout_info';
if (isset($params['template_speed_checkout'])) {
	$template = 'cart_' . $params['template_speed_checkout'];
}
echo $this->ProcessTemplateFromDatabase($template);
