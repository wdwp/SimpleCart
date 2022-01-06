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

$order_id = isset($params['order_id']) ? $params['order_id'] : 0;

$agreetoterms = 0;
if (isset($params['agreetoterms'])) {
	$agreetoterms = $params['agreetoterms'];
}

if (isset($params['continue']) && $params['continue'] != '') {
	// Store the entered shipping information
	cartms_utils::StoreDeliveryInfo($params);
	// Check how many payment methods are available.
	// If zero, set to default and continue to confirmation
	// if only one, use it and continue to confirmation
	$paymentmethodlist = array();
	$paymsmodule = &$this->GetModuleInstance('SimplePayment');
	if ($paymsmodule) {
		$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_pms_gateways
			WHERE active > 0 ORDER BY gateway_code';
		$count = $db->GetOne($query);
		switch (intval($count)) {
			case 0:
				// No active payment methods found, set it to payment upfront
				$params['paymentmethod'] = 'PAYUF';
				$this->orders->StorePaymentInfo($params);
				$params['perfaction'] = 'request_confirmation';
				break;
			case 1:
				// Exactly one payment method found, retrieve it and use it for this order
				$dbresult = $db->Execute($query);
				$row = $dbresult->FetchRow();
				$params['paymentmethod'] = $row['gateway_code'];
				$this->orders->StorePaymentInfo($params);
				$params['perfaction'] = 'request_confirmation';
				break;
			default:
				// More then one payment methods found, so up to customer which one to use
				// Shipping information filled, commence to payment information
				$params['perfaction'] = 'request_payment_method';
		}
	} else {
		$params['paymentmethod'] = 'PAYUF';
		$this->orders->StorePaymentInfo($params);
		$params['perfaction'] = 'request_confirmation';
	}
	$this->RedirectForFrontend($id, $returnid, 'order', $params, true);
}

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

$smarty->assign('welcometitle', $this->Lang('title_welcomedelivery'));
$errorfound = false;
// Perform a check on all the entries. All are mandatory
if (isset($params['submit'])) {
	// Validate delivery method
	if (isset($params['deliverymethod'])) {
		$deliverymethod = $params['deliverymethod'];
		$smarty->assign('deliverymethod_error', '');
		$smarty->assign('agreetoterms_error', '');
		if ($deliverymethod == '') {
			$smarty->assign('deliverymethod_error', $this->Lang('errordeliverymethodblank'));
			$errorfound = true;
		}
		// Check if agree to terms is mandatory for this delivery method
		$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_cartms_shippingprovider WHERE shipprovcode = ?';
		$rowitem = $db->GetRow($query, array($params['deliverymethod']));
		if ($agreetoterms != 1 && $rowitem['agreetoterms'] == 1) {

			if ($content_id > 0) {
				// Prepare hyperlink to the the page as set up in the options
				$agreetotermstext = $agreecontentlink;
			} else {
				$agreetotermstext = $this->Lang('erroragreetotermsblank');
			}
			$smarty->assign('agreetoterms_error', $agreetotermstext);
			$errorfound = true;
		}
	}

	// Fill separate message for header
	if (!$errorfound) {
		$smarty->assign('welcometitle', $this->Lang('checkdeliveryinfo'));
		$smarty->assign('continue', $this->CreateInputSubmit($id, 'continue', $this->Lang('continuestep3')));
	}
}

// Prepare a list of possible delivery methods
$firstmethodfound = false;
$deliverymethodlist = array();
$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_cartms_shippingprovider WHERE active = 1 ORDER BY shipprovdesc';
$dbresult = $db->Execute($query);

// Retrieve the order header information which contains the net weight. The weight is used as a factor in delivery cost
$orderheader = array();
$orderheader = cartms_utils::GetOrderHeader($order_id);

while ($dbresult && $row = $dbresult->FetchRow()) {

	if ($row['shippriceperweight'] > 0) {
		$additionalcost = $row['shipprovprice'] + $row['shippriceperweight'] * $orderheader['totalnetweight'];
		$additionalcost = $this->orders->FormatAmount($additionalcost);
		// Place delivery cost in brackets on right side of delivery code
		$deliverymethodlist[$row['shipprovdesc'] . ' (' . $this->Lang('shipcostextra') . ' ' . $additionalcost . ')'] = $row['shipprovcode'];
	} else {
		$deliverymethodlist[$row['shipprovdesc'] . ' (' . $this->Lang('shipcostextra') . ' ' . $row['shipprovprice'] . ')'] = $row['shipprovcode'];
	}
	// Save the agreements value of the first found delivery method, since this might be a mandatory situation
	if (!$firstmethodfound) {
		if ($row['agreetoterms'] == 1) {
			if ($content_id > 0) {
				// Prepare hyperlink to the the page as set up in the options
				$agreetotermstext = $agreecontentlink;
			} else {
				$agreetotermstext = $this->Lang('erroragreetotermsblank');
			}
			$smarty->assign('agreetoterms_error', $agreetotermstext);
		}
		$firstmethodfound = true;
	}
}

$shopms = &$this->GetModuleInstance('SimpleShop');
$smarty->assign('umweight', '');
if ($shopms) {
	$smarty->assign('umweight', $shopms->GetPreference('weightunitmeasure', 'Kg'));
}
$this->smarty->assign('totalnetweight', $orderheader['totalnetweight']);

#Display template
$smarty->assign('startform', $this->CreateFormStart($id, 'orderdelivery', $returnid));
$smarty->assign('deliverymethod_label', $this->Lang('deliverymethod_label'));
$deliverymethod = isset($deliverymethod) ? $deliverymethod : '';
$smarty->assign('deliverymethod_input', $this->CreateInputDropdown($id, 'deliverymethod', $deliverymethodlist, -1, $deliverymethod));

$smarty->assign('agreetoterms_label', $this->Lang('agreetoterms_label'));
$smarty->assign('agreetoterms_input', $this->CreateInputCheckbox($id, 'agreetoterms', true, $agreetoterms));

$smarty->assign('hidden', $this->CreateInputHidden($id, 'order_id', $order_id));
$smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('submit')));
$smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));
$smarty->assign('endform', $this->CreateFormEnd());

// Set default template. If past as parameter, show that one
$template = 'cart_fe_delivery_info';
if (isset($params['template_delivery_info'])) {
	$template = 'cart_' . $params['template_delivery_info'];
}
echo $this->ProcessTemplateFromDatabase($template);
