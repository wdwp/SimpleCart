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
} else {
	// Set the selected country (otherwise first one will be shown)
	$val = $params['addresscountry'];
}

if (isset($params['email'])) $email = $params['email'];
if (isset($params['firstname'])) $firstname = $params['firstname'];
if (isset($params['lastname'])) $lastname = $params['lastname'];
if (isset($params['addressstreet'])) $addressstreet = $params['addressstreet'];
if (isset($params['addresscity'])) $addresscity = $params['addresscity'];
if (isset($params['addressstate'])) $addressstate = $params['addressstate'];
if (isset($params['addresszip'])) $addresszip = $params['addresszip'];
if (isset($params['addresscountry'])) $addresscountry = $params['addresscountry'];
if (isset($params['telephone'])) $telephone = $params['telephone'];
if (isset($params['paymentmethod'])) $paymentmethod = $params['paymentmethod'];
if (isset($params['orderremark'])) $orderremark = $params['orderremark'];
if (isset($params['coupon_code'])) $coupon_code = $params['coupon_code'];

// Handle any errors
if (isset($params['submit'])) {
	$errorfound = false;
	if ($firstname == '') {
		$smarty->assign('firstname_error', $this->Lang('errorfirstnameblank'));
		$errorfound = true;
	}
	if ($lastname ==  '') {
		$smarty->assign('lastname_error', $this->Lang('errorlastnameblank'));
		$errorfound = true;
	}
	if (!$this->ValidateEmailAddress($email)) {
		$smarty->assign('email_error', $this->Lang('erroremailformat'));
		$errorfound = true;
	}
}

// Check if the user allready has logged in. If not temporary save the customer address.
$feusers = &$this->GetModuleInstance('FrontEndUsers');
$userloggedin = $feusers->loggedin();
$this->smarty->assign('userloggedin', $userloggedin);
if (!$userloggedin) {
	$smarty->assign('welcometitle', $this->Lang('title_welcomelogin'));
	// Perform a check on all the entries. All are mandatory
	if (isset($params['submit'])) {
		// Fill separate message for header
		if ($params['telephone'] == '' && $this->GetPreference('mandatorytelephone', false)) {
			$smarty->assign('telephone_error', $this->Lang('errortelephoneblank'));
			$errorfound = true;
		}
		if (!$errorfound) {
			$smarty->assign('welcometitle', $this->Lang('checkshipinfo'));
			// Store the entered shipping information
			$params['user_id'] = cartms_utils::StoreShipInfo($params);
			// Generate order from cart information
			$params['session_id'] = cartms_utils::GetSessionId();
			$params['order_id'] = $this->orders->GenerateOrder($params);
			// Store the entered shipping information
			cartms_utils::StoreDeliveryInfo($params);
			// Store payment method
			$this->orders->StorePaymentInfo($params);

			// Order information stored, commence to confirmation of order
			$params['perfaction'] = 'request_confirmation';
			$this->RedirectForFrontend($id, $returnid, 'order', $params, true);
		}
	}
} else {
	$user_id = $feusers->LoggedInId();
	$firstname = $feusers->GetUserPropertyFull('firstname', $user_id, false);
	$lastname = $feusers->GetUserPropertyFull('surname', $user_id, false);
	$email = $feusers->GetUserPropertyFull('email', $user_id, false);
	// Check if the telephone has been filled
	// Try to retrieve it from the customers properties
	$telephone = $feusers->GetUserPropertyFull('telephone', $user_id, false);
	if ($telephone == '' && $this->GetPreference('mandatorytelephone', false)) {
		$smarty->assign('telephone_error', $this->Lang('errortelephoneblank'));
		$errorfound = true;
	} else {
		$feusers->SetUserPropertyFull('telephone', $telephone, $user_id);
	}
	$smarty->assign('welcometitle', $this->Lang('title_welcomelogin'));
	if (isset($params['submit'])) {
		// Fill separate message for header
		if (!$errorfound) {
			$smarty->assign('welcometitle', $this->Lang('checkshipinfo'));
			// Store the entered shipping information
			$params['user_id'] = cartms_utils::StoreShipInfo($params);
			// Generate order from cart information
			$params['session_id'] = cartms_utils::GetSessionId();
			$params['order_id'] = $this->orders->GenerateOrder($params);
			// Store the entered shipping information
			cartms_utils::StoreDeliveryInfo($params);
			// Store payment method
			$this->orders->StorePaymentInfo($params);

			// Order information stored, commence to confirmation of order
			$params['perfaction'] = 'request_confirmation';
			$this->RedirectForFrontend($id, $returnid, 'order', $params, true);
		}
	}
}

// Retrieve the net weight, price of the cart.
// Weight is used for the delivery methods
// Price is used to show visitor the total cart amount
//$cartinfo = $this->orders->GetCartInfo();
$currency = $this->GetPreference('cartcurrency', 'Eur');
// Check if VAT is to be calculated (is set up in SimpleShop)
$ShopMS = &$this->GetModuleInstance('SimpleShop');
if ($ShopMS) {
	$priceinclvat = (int) $ShopMS->GetPreference('pricesinclvat', 0);
} else {
	$priceinclvat = 1;
}

// Using email handling, there is no delivery part

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
			$smarty->assign('paymentmethod_input', $this->Lang('paymentupfront'));
			break;
		case 1:
			// Exactly one payment method found, retrieve it and use it for this order
			$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_pms_gateways
				WHERE active > 0 ORDER BY gateway_code';
			$dbresult = $db->Execute($query);
			$row = $dbresult->FetchRow();
			$params['paymentmethod'] = $row['gateway_code'];
			$smarty->assign('paymentmethod_input', $row['description']);
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

				$smarty->assign('paymentmethod_input', $this->CreateInputDropdown($id, 'paymentmethod', $paymentmethodlist, 'PAYUF', $paymentmethod));
			}
	}
} else {
	$params['paymentmethod'] = 'PAYUF';
}

// Validate coupon code used
$smarty->assign('SCouponsAvail', false);
$SCoupons = &$this->GetModuleInstance('SCoupons');
if ($SCoupons) {
	$smarty->assign('SCouponsAvail', true);
	if (isset($params['coupon_code']) && $params['coupon_code'] != '') {
		$coupon_code = $params['coupon_code'];
		$smarty->assign('coupon_code_error', '');
		if (!$SCoupons->CheckCouponCode($coupon_code)) {
			$smarty->assign('coupon_code_error', $this->Lang('errornonvalidcoupon'));
			$errorfound = true;
		}
	}
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
$smarty->assign('mandatorystate', $this->GetPreference('mandatorystate', false));
$smarty->assign('mandatorytelephone', $this->GetPreference('mandatorytelephone', false));
// Display template
$smarty->assign('startform', $this->CreateFormStart($id, 'orderemailcheckout', $returnid));
$smarty->assign('title_fieldset_ec', $this->Lang('title_fieldset_exist_customer'));
$smarty->assign('title_fieldset_nc', $this->Lang('title_fieldset_new_customer'));
$smarty->assign('fieldsetshipto_label', $this->Lang('title_shippingaddress'));
$smarty->assign('title_username', $this->Lang('title_username'));
$smarty->assign('firstname_label', $this->Lang('firstname_label'));
$smarty->assign('firstname_input', $this->CreateInputText($id, 'firstname', $firstname, 40, 40));
$smarty->assign('lastname_label', $this->Lang('lastname_label'));
$smarty->assign('lastname_input', $this->CreateInputText($id, 'lastname', $lastname, 40, 40));
$smarty->assign('email_label', $this->Lang('email_label'));
$smarty->assign('email_input', $this->CreateInputText($id, 'email', $email, 40, 255));
// Ship to address
$smarty->assign('fieldsetshipto_label', $this->Lang('title_shippingaddress'));
$smarty->assign('addressstreet_label', $this->Lang('addressstreet_label'));
$smarty->assign('addressstreet_input', $this->CreateInputText($id, 'addressstreet', $addressstreet, 40, 40));
$smarty->assign('addresscity_label', $this->Lang('addresscity_label'));
$smarty->assign('addresscity_input', $this->CreateInputText($id, 'addresscity', $addresscity, 40, 40));
$smarty->assign('addressstate_label', $this->Lang('addressstate_label'));
$smarty->assign('addressstate_input', $this->CreateInputText($id, 'addressstate', $addressstate, 15, 15));
$smarty->assign('addresszip_label', $this->Lang('addresszip_label'));
$smarty->assign('addresszip_input', $this->CreateInputText($id, 'addresszip', $addresszip, 15, 15));
$smarty->assign('addresscountry_label', $this->Lang('addresscountry_label'));
$smarty->assign('addresscountry_input', $this->CreateInputDropdown(
	$id,
	'addresscountry',
	$feusers->GetSelectOptions('addresscountry', 1),
	$selectedcountry,
	$val
));
$smarty->assign('telephone_label', $this->Lang('telephone_label'));
$smarty->assign('telephone_input', $this->CreateInputText($id, 'telephone', $telephone, 35, 35));
$smarty->assign('orderremark_label', $this->Lang('orderremark_label'));
$smarty->assign('orderremark_input', $this->CreateTextArea(false, $id, $orderremark, 'orderremark', '', '', '', '', 40, 5));
$smarty->assign('coupon_code_label', $this->Lang('coupon_code_label'));
$smarty->assign('coupon_code_input', $this->CreateInputText($id, 'coupon_code', $coupon_code, 12, 12));

// Payment
$smarty->assign('paymentmethod_label', $this->Lang('paymentmethod_label'));
// Order amount
$smarty->assign('totalamount_label', $this->Lang('ordertotals'));
//$smarty->assign('totalamount', $this->orders->FormatAmount($cartinfo['totalnetprice']));
$smarty->assign('totalamount_currency', $currency);
if ($priceinclvat) {
	$smarty->assign('totalamount_vat', $this->Lang('vatincl'));
} else {
	$smarty->assign('totalamount_vat', $this->Lang('vatexcl'));
}
if (isset($count) && $count > 1) {
	$smarty->assign('hidden', '');
} else {
	$smarty->assign('hidden', $this->CreateInputHidden($id, 'paymentmethod', $params['paymentmethod']));
}
$smarty->assign('register', $this->CreateInputSubmit($id, 'sumbit_register', $this->Lang('customer_register')));
$smarty->assign('login', $this->CreateInputSubmit($id, 'submit_login', $this->Lang('customer_login')));
$smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('continue_checkout_process')));
$smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));
$smarty->assign('endform', $this->CreateFormEnd());

// Set default template. If past as parameter, show that one
$template = 'cart_fe_emailcheckout_info';
if (isset($params['template_email_checkout'])) {
	$template = 'cart_' . $params['template_email_checkout'];
}
echo $this->ProcessTemplateFromDatabase($template);
