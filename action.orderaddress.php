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

if (isset($params['continue'])) {
	// Store the entered shipping information
	$params['user_id'] = cartms_utils::StoreShipInfo($params);

	// Generate order from cart information
	$params['session_id'] = cartms_utils::GetSessionId();

	$params['order_id'] = $this->orders->GenerateOrder($params);

	// Shipping information filled, commence to delivery information
	$params['perfaction'] = 'request_delivery_info';
	$this->RedirectForFrontend($id, $returnid, 'order', $params, true);
}


if (isset($params['addresscountry'])) {
	// First time here, show the first country of the selectable countries
	$val = $params['addresscountry'];
} else {
	// Set the selected country (otherwise first one will be shown)
	$selectedcountry = -1;
	$val = '';
}

if (isset($params['telephone'])) $telephone = $params['telephone'];

// Check if the user allready has logged in. If not temporary save the customer address.
$feusers = &$this->GetModuleInstance('FrontEndUsers');
$userloggedin = $feusers->loggedin();
$this->smarty->assign('userloggedin', $userloggedin);
if (!$userloggedin) {
	$this->smarty->assign('welcometitle', $this->Lang('title_welcomelogin'));
	$errorfound = false;
	// Perform a check on all the entries. All are mandatory
	if (isset($params['submit'])) {
		// Validate first name of visitor
		if (isset($params['firstname'])) {
			$firstname = $params['firstname'];
			$this->smarty->assign('firstname_error', '');
			if ($firstname == '') {
				$this->smarty->assign('firstname_error', $this->Lang('errorfirstnameblank'));
				$errorfound = true;
			}
		}

		// Validate last name of visitor
		if (isset($params['lastname'])) {
			$lastname = $params['lastname'];
			$this->smarty->assign('lastname_error', '');
			if ($lastname == '') {
				$this->smarty->assign('lastname_error', $this->Lang('errorlastnameblank'));
				$errorfound = true;
			}
		}

		// Validate email address of visitor
		if (isset($params['email'])) {
			$email = $params['email'];
			$this->smarty->assign('email_error', '');
			if ($email == '') {
				$this->smarty->assign('email_error', $this->Lang('erroremailblank'));
				$errorfound = true;
			}
			// Check if the format of the entered email is correct
			if (!$this->ValidateEmailAddress($email)) {
				$this->smarty->assign('email_error', $this->Lang('erroremailformat'));
				$errorfound = true;
			}
		}

		// Validate street address of visitor
		if (isset($params['addressstreet'])) {
			$addressstreet = $params['addressstreet'];
			$this->smarty->assign('addressstreet_error', '');
			if ($addressstreet == '') {
				$this->smarty->assign('addressstreet_error', $this->Lang('erroraddressstreetblank'));
				$errorfound = true;
			}
		}

		// Validate city of visitor
		if (isset($params['addresscity'])) {
			$addresscity = $params['addresscity'];
			$this->smarty->assign('addresscity_error', '');
			if ($addresscity == '') {
				$this->smarty->assign('addresscity_error', $this->Lang('erroraddresscityblank'));
				$errorfound = true;
			}
		}

		// Validate state of visitor
		if (isset($params['addressstate']) && $this->GetPreference('mandatorystate', false)) {
			$addressstate = $params['addressstate'];
			$this->smarty->assign('addressstate_error', '');
			if ($addressstate == '') {
				$this->smarty->assign('addressstate_error', $this->Lang('erroraddressstateblank'));
				$errorfound = true;
			}
		} else {
			$params['addressstate'] = '';
		}

		// Validate Zip/Postal code of visitor
		if (isset($params['addresszip'])) {
			$addresszip = $params['addresszip'];
			$this->smarty->assign('addresszip_error', '');
			if ($addresszip == '') {
				$this->smarty->assign('addresszip_error', $this->Lang('erroraddresszipblank'));
				$errorfound = true;
			}
		}

		// Validate country of visitor
		if (isset($params['addresscountry'])) {
			$addresscountry = $params['addresscountry'];
			$this->smarty->assign('addresscountry_error', '');
			if ($addresscountry == '') {
				$this->smarty->assign('addresscountry_error', $this->Lang('erroraddresscountryblank'));
				$errorfound = true;
			}
		}

		// Validate telephone of visitor
		if (isset($params['telephone']) && $this->GetPreference('mandatorytelephone', false)) {
			$telephone = $params['telephone'];
			$this->smarty->assign('telephone_error', '');
			if ($telephone == '') {
				$this->smarty->assign('telephone_error', $this->Lang('errortelephoneblank'));
				$errorfound = true;
			}
		}

		// Fill billing street address of visitor
		if (isset($params['billfirstname']) && $params['billfirstname'] == '') {
			$billfirstname = $params['firstname'];
		} else {
			$billfirstname = $params['billfirstname'];
		}
		if (isset($params['billlastname']) && $params['billlastname'] == '') {
			$billlastname = $params['lastname'];
		} else {
			$billlastname = $params['billlastname'];
		}
		if (isset($params['billaddressstreet']) && $params['billaddressstreet'] == '') {
			$billaddressstreet = $params['addressstreet'];
		} else {
			$billaddressstreet = $params['billaddressstreet'];
		}
		if (isset($params['billaddresscity']) && $params['billaddresscity'] == '') {
			$billaddresscity = $params['addresscity'];
		} else {
			$billaddresscity = $params['billaddresscity'];
		}
		if (isset($params['billaddressstate']) && $params['billaddressstate'] == '') {
			$billaddressstate = $params['addressstate'];
		} else {
			$billaddressstate = isset($params['billaddressstate']) ? $params['billaddressstate'] : '';
		}
		if (isset($params['billaddresszip']) && $params['billaddresszip'] == '') {
			$billaddresszip = $params['addresszip'];
		} else {
			$billaddresszip = $params['billaddresszip'];
		}
		if (isset($params['billaddresscountry']) && $params['billaddresscountry'] == '') {
			$billaddresscountry = $params['addresscountry'];
		} else {
			$billaddresscountry = $params['billaddresscountry'];
		}

		// Fill separate message for header
		if (!$errorfound) {
			$this->smarty->assign('welcometitle', $this->Lang('checkshipinfo'));
			$this->smarty->assign('continue', $this->CreateInputSubmit($id, 'continue', $this->Lang('continuestep2')));
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

// Validate coupon code used
$smarty->assign('SCouponsAvail', false);
$SCoupons = &$this->GetModuleInstance('SCoupons');
if ($SCoupons) {
	$smarty->assign('SCouponsAvail', true);
	if (isset($params['coupon_code']) && $params['coupon_code'] != '') {
		$coupon_code = trim($params['coupon_code']);
		$this->smarty->assign('coupon_code_error', '');
		if (!$SCoupons->CheckCouponCode($coupon_code)) {
			$this->smarty->assign('coupon_code_error', $this->Lang('errornonvalidcoupon'));
			$errorfound = true;
		}
	}
}

if (isset($params['orderremark'])) $orderremark = $params['orderremark'];

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
$this->smarty->assign('startform', $this->CreateFormStart($id, 'orderaddress', $returnid));
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
$this->smarty->assign('billaddresscountry_input', $this->CreateInputText(
	$id,
	'billaddresscountry',
	$billaddresscountry,
	40,
	40
));
$this->smarty->assign('orderremark_label', $this->Lang('orderremark_label'));
$this->smarty->assign('orderremark_input', $this->CreateTextArea(false, $id, $orderremark, 'orderremark', '', '', '', '', 40, 5));
$this->smarty->assign('coupon_code_label', $this->Lang('coupon_code_label'));
$this->smarty->assign('coupon_code_input', $this->CreateInputText($id, 'coupon_code', $coupon_code, 12, 12));

$this->smarty->assign('hidden', '');
$this->smarty->assign('register', $this->CreateInputSubmit($id, 'sumbit_register', $this->Lang('customer_register')));
$this->smarty->assign('login', $this->CreateInputSubmit($id, 'submit_login', $this->Lang('customer_login')));
$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('submit')));
$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));
$this->smarty->assign('endform', $this->CreateFormEnd());

// Set default template. If past as parameter, show that one
$template = 'cart_fe_shipping_info';
if (isset($params['template_shipping_info'])) {
	$template = 'cart_' . $params['template_shipping_info'];
}
echo $this->ProcessTemplateFromDatabase($template);
