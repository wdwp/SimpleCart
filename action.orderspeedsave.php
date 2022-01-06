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

// Validation
$errorfound = false;

// Validate first name of visitor
if (isset($params['firstname']) && $params['firstname'] == '') {
	$params['efn'] = true;
	$errorfound = true;
}

// Validate last name of visitor
if (isset($params['lastname']) && $params['lastname'] == '') {
	$params['eln'] = true;
	$errorfound = true;
}

// Validate email address of visitor
if (isset($params['email'])) {
	// Check if the format of the entered email is correct
	if (!$this->ValidateEmailAddress($params['email'])) {
		$params['eem'] = true;
		$errorfound = true;
	}
}

// Validate street address of visitor
if (isset($params['addressstreet']) && $params['addressstreet'] == '') {
	$params['eas'] = true;
	$errorfound = true;
}

// Validate city of visitor
if (isset($params['addresscity']) && $params['addresscity'] == '') {
	$params['eac'] = true;
	$errorfound = true;
}

// Validate state of visitor
if (isset($params['addressstate']) && $this->GetPreference('mandatorystate', false)) {
	if ($params['addressstate'] == '') {
		$params['east'] = true;
		$errorfound = true;
	}
}

// Validate Zip/Postal code of visitor
if (isset($params['addresszip']) && $params['addresszip'] == '') {
	$params['eaz'] = true;
	$errorfound = true;
}

// Validate country of visitor
if (isset($params['addresscountry']) && $params['addresscountry'] == '') {
	$params['eaco'] = true;
	$errorfound = true;
}

// Validate telephone of visitor
if (isset($params['telephone']) && $this->GetPreference('mandatorytelephone', false)) {
	if ($params['telephone'] == '') {
		$params['ete'] = true;
		$errorfound = true;
	}
}
// Check if agree to terms is mandatory for this delivery method
$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_cartms_shippingprovider WHERE shipprovcode = ?';
$rowitem = $db->GetRow($query, array($params['deliverymethod']));
if ($params['agreetoterms'] != 1 && $rowitem['agreetoterms'] == 1) {
	$params['eatt'] = true;
	$errorfound = true;
}

// Fill billing street address of visitor
// The country is allways filled, but should need be the correct one. As a result of
// this, first have to check if all but the country are blank
if (
	$params['billfirstname'] == '' && $params['billlastname'] == '' &&
	$params['billaddressstreet'] == '' && $params['billaddresscity'] == ''
) {
	$params['billaddresscountry'] = $params['addresscountry'];
}
if ($params['billfirstname'] == '') {
	$params['billfirstname'] = $params['firstname'];
}
if ($params['billlastname'] == '') {
	$params['billlastname'] = $params['lastname'];
}
if ($params['billaddressstreet'] == '') {
	$params['billaddressstreet'] = $params['addressstreet'];
}
if ($params['billaddresscity'] == '') {
	$params['billaddresscity'] = $params['addresscity'];
}
if ($params['billaddressstate'] == '') {
	$params['billaddressstate'] = $params['addressstate'];
}
if ($params['billaddresszip'] == '') {
	$params['billaddresszip'] = $params['addresszip'];
}
if ($params['billaddresscountry'] == '') {
	$params['billaddresscountry'] = $params['addresscountry'];
}
// Validate coupon code used
if (isset($params['coupon_code']) && $params['coupon_code'] != '') {
	// Validate coupon code used
	$smarty->assign('SCouponsAvail', false);
	$SCoupons = &$this->GetModuleInstance('SCoupons');
	if ($SCoupons && isset($params['coupon_code'])) {
		$coupon_code = $params['coupon_code'];
		if (!$SCoupons->CheckCouponCode($coupon_code)) {
			$params['edc'] = true;
			$errorfound = true;
		}
	}
}

if ($errorfound) {
	$params['perfaction'] = 'request_speedcheckout';
} else {
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
}

$this->RedirectForFrontend($id, $returnid, 'order', $params, true);
