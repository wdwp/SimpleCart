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


if (isset($params['continue']) && $params['continue'] != '') {
	// Store the entered shipping information
	$this->orders->StorePaymentInfo($params);
	// Shipping information filled, commence to payment information
	$params['perfaction'] = 'request_confirmation';
	$this->RedirectForFrontend($id, $returnid, 'order', $params, true);
}

$this->smarty->assign('welcometitle', $this->Lang('title_welcomepayment'));
$errorfound = false;
// Perform a check on all the entries. All are mandatory
if (isset($params['submit'])) {
	// Validate payment method
	if (isset($params['paymentmethod'])) {
		$paymentmethod = $params['paymentmethod'];
		$this->smarty->assign('paymentmethod_error', '');
		if ($paymentmethod == '') {
			$this->smarty->assign('paymentmethod_error', $this->Lang('errorpaymentmethodblank'));
			$errorfound = true;
		}
	}

	// Fill separate message for header
	if (!$errorfound) {
		$this->smarty->assign('welcometitle', $this->Lang('checkpaymentinfo'));
		$this->smarty->assign('continue', $this->CreateInputSubmit($id, 'continue', $this->Lang('continuestep4')));
	}
}

// Prepare a list of possible active payment methods. These will come from the admin part/Payment Made Simple.
$paymentmethodlist = array();
$paymsmodule = &$this->GetModuleInstance('SimplePayment');
if ($paymsmodule) {
	$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_pms_gateways
		WHERE active > 0 ORDER BY gateway_code';
	$dbresult = $db->Execute($query);

	while ($dbresult && $row = $dbresult->FetchRow()) {
		$paymentmethodlist[$row['description']] = $row['gateway_code'];
	}
} else {
	$paymentmethodlist[$this->Lang('paymentupfront')] = 'PAYUF';
}

$paymentmethod = isset($paymentmethod) ? $paymentmethod : 'PayPal';

#Display template
$this->smarty->assign('startform', $this->CreateFormStart($id, 'orderpayment', $returnid));
$this->smarty->assign('paymentmethod_label', $this->Lang('paymentmethod_label'));
$this->smarty->assign('paymentmethod_input', $this->CreateInputDropdown($id, 'paymentmethod', $paymentmethodlist, -1, $paymentmethod));

$this->smarty->assign('hidden', $this->CreateInputHidden($id, 'order_id', $order_id));
$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('submit')));
$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));
$this->smarty->assign('endform', $this->CreateFormEnd());

// Set default template. If past as parameter, show that one
$template = 'cart_fe_payment_info';
if (isset($params['template_payment_info'])) {
	$template = 'cart_' . $params['template_payment_info'];
}
echo $this->ProcessTemplateFromDatabase($template);
