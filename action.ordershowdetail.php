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
	$params = array('active_tab' => 'orders', 'orderstatus' => $params['orderstatus']);
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}

if (isset($params['order_id'])) {
	$order_id = $params['order_id'];
}

if (isset($params['submit'])) {
	// Set status of order to next status
	$params['oldstatus'] = $params['orderstatus'];
	$params['active_tab'] = 'order';
	#$this->orders->SwitchStatus( $params );
	$this->Redirect($id, 'switchstatus', $returnid, $params);
}

// Prepare shipping information
$orderheader = array();
$orderheader = cartms_utils::GetOrderHeader($order_id);
$orderstatus = $orderheader['status'];
$this->smarty->assign('welcometitle', $this->Lang('title_orderhandling' . $orderstatus));
$this->smarty->assign('orderdate', $orderheader['create_date']);
$this->smarty->assign('deliverydate', $orderheader['delivery_date']);
$this->smarty->assign('defaultdateformat', get_site_preference('defaultdateformat'));

$shipto = array();
$shipto = $this->orders->GetOrderShipTo($orderheader['customer_id']);

$this->smarty->assign('shiptoname', $shipto['shiptoname']);
$this->smarty->assign('shiptostreet', $shipto['addressstreet']);
$this->smarty->assign('shiptocity', $shipto['addresscity']);
$this->smarty->assign('shiptostate', $shipto['addressstate']);
$this->smarty->assign('shiptozip', $shipto['addresszip']);
$this->smarty->assign('shiptocountry', $shipto['addresscountry']);
$this->smarty->assign('shiptotelephone', $shipto['telephone']);
$this->smarty->assign('shiptoemail', $shipto['email']);
$this->smarty->assign('billtoname', $shipto['billtoname']);
$this->smarty->assign('billtostreet', $shipto['billaddressstreet']);
$this->smarty->assign('billtocity', $shipto['billaddresscity']);
$this->smarty->assign('billtostate', $shipto['billaddressstate']);
$this->smarty->assign('billtozip', $shipto['billaddresszip']);
$this->smarty->assign('billtocountry', $shipto['billaddresscountry']);
$this->smarty->assign('orderhandlingtype', $this->GetPreference('orderhandlingtype', 'normal'));

$this->smarty->assign('orderremark', $orderheader['remark']);

// Total weight
$shopms = &$this->GetModuleInstance('SimpleShop');
if ($shopms) {
	$umweight = $shopms->GetPreference('weightunitmeasure', 'Kg');
}
if ($orderheader['totalnetweight'] != '') {
	$this->smarty->assign('label_total_weight', $this->Lang('label_total_weight', $orderheader['totalnetweight'], $umweight));
} else {
	$this->smarty->assign('label_total_weight', $this->Lang('label_total_weight', 0, $umweight));
}

// Prepare overview of order lines
$products = array();
$products = $this->orders->GetOrderLines($order_id);
$this->smarty->assign_by_ref('products', $products);

// Prepare the VAT amounts
if ($orderheader['totalvat0amount'] <> 0) {
	$formattedamount = $this->orders->FormatAmount($orderheader['totalvat0amount']);
	$this->smarty->assign('label_vat0_amount', $this->GetPreference('vat0name') . ' ' . $this->GetPreference('vat0perc', 0));
	$this->smarty->assign('totalvat0amount', $formattedamount);
}
if ($orderheader['totalvat1amount'] <> 0) {
	$formattedamount = $this->orders->FormatAmount($orderheader['totalvat1amount']);
	$this->smarty->assign('label_vat1_amount', $this->GetPreference('vat1name') . ' ' . $this->GetPreference('vat1perc', 0));
	$this->smarty->assign('totalvat1amount', $formattedamount);
}
if ($orderheader['totalvat2amount'] <> 0) {
	$formattedamount = $this->orders->FormatAmount($orderheader['totalvat2amount']);
	$this->smarty->assign('label_vat2_amount', $this->GetPreference('vat2name') . ' ' . $this->GetPreference('vat2perc', 0));
	$this->smarty->assign('totalvat2amount', $formattedamount);
}
if ($orderheader['totalvat3amount'] <> 0) {
	$formattedamount = $this->orders->FormatAmount($orderheader['totalvat3amount']);
	$this->smarty->assign('label_vat3_amount', $this->GetPreference('vat3name') . ' ' . $this->GetPreference('vat3perc', 0));
	$this->smarty->assign('totalvat3amount', $formattedamount);
}
if ($orderheader['totalvat4amount'] <> 0) {
	$formattedamount = $this->orders->FormatAmount($orderheader['totalvat4amount']);
	$this->smarty->assign('label_vat4_amount', $this->GetPreference('vat4name') . ' ' . $this->GetPreference('vat4perc', 0));
	$this->smarty->assign('totalvat4amount', $formattedamount);
}
// Any discount involved?
if ($orderheader['totaldiscount'] <> 0) {
	$totaldiscount = 0 - $orderheader['totaldiscount'];
	$formattedamount = $this->orders->FormatAmount($totaldiscount);
	$currency = $this->GetPreference('cartcurrency', 'Eur');
	$this->smarty->assign('label_totaldiscount', $this->Lang('discountamount'));
	$this->smarty->assign('discount_amount', $formattedamount);
}
// Prepare the total amount
if ($orderheader['totaladmincost'] <> 0) {
	$formattedamount = $this->orders->FormatAmount($orderheader['totaladmincost']);
	$currency = $this->GetPreference('cartcurrency', 'Eur');
	$this->smarty->assign('label_admin_amount', $this->Lang('adminamount'));
	$this->smarty->assign('admin_amount', $formattedamount);
}
$formattedamount = $this->orders->FormatAmount($orderheader['totalproduct']
	- $orderheader['totaldiscount']
	+ $orderheader['totalshipping'] + $orderheader['totaladmincost']
	+ $orderheader['totalvat0amount'] + $orderheader['totalvat1amount'] + $orderheader['totalvat2amount']
	+ $orderheader['totalvat3amount'] + $orderheader['totalvat4amount']);
$currency = $this->GetPreference('cartcurrency', 'Eur');
$this->smarty->assign('label_total_amount', $this->Lang('ordertotals'));
$this->smarty->assign('currency', $currency);
$this->smarty->assign('total_amount', $formattedamount);
// Prepare delivery method
$this->smarty->assign('deliveryvia', '');
$this->smarty->assign('deliveryprice', 0);

if (!empty($orderheader['shipmode'])) {
	$deliverymethod = $orderheader['shipmode'];
	$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_cartms_shippingprovider WHERE shipprovcode = ? ';
	$dbresult = $db->Execute($query, array($deliverymethod));
	$row = $dbresult->FetchRow();

	$this->smarty->assign('deliveryvia', $row['shipprovdesc']);
	$formattedamount = $this->orders->FormatAmount($orderheader['totalshipping']);
	$this->smarty->assign('deliveryprice', $formattedamount);
}

// Prepare payment method
if (isset($orderheader['paymethod']) && $orderheader['paymethod'] != 'PAYUF') {
	$this->smarty->assign('paymentvia', $this->GetPaymentGatewayDescription($orderheader['paymethod']));
} else {
	$this->smarty->assign('paymentvia', $this->Lang('paymentupfrontbackend'));
}
// Prepare a print button --> Not in template since it performs the same as a browser print (so complete page)
$printtext = $this->Lang('printorder');
$this->smarty->assign('printbut', '<img src="' . $gCms->config['root_url'] . '/images/cms/printbutton.gif" border="0" alt="' . $printtext . '"/>');

// Display template
$this->smarty->assign('startform', $this->CreateFormStart($id, 'ordershowdetail', $returnid));
$this->smarty->assign('ordernumber_label', $this->Lang('title_orders'));
$this->smarty->assign('ordernumber', $order_id);
$this->smarty->assign('shipto_label', $this->Lang('title_shippingaddress'));
$this->smarty->assign('billto_label', $this->Lang('title_billingaddress'));
$this->smarty->assign('productqtytext', $this->Lang('productqtytext'));
$this->smarty->assign('productidtext', $this->Lang('productidtext'));
$this->smarty->assign('productitemnumbertext', $this->Lang('productitemnumbertext'));
$this->smarty->assign('productskutext', $this->Lang('productskutext'));
$this->smarty->assign('productnametext', $this->Lang('productnametext'));
$this->smarty->assign('productpricetext', $this->Lang('productpricetext'));
$this->smarty->assign('lineamounttext', $this->Lang('lineamounttext'));
$this->smarty->assign('deliveryvia_label', $this->Lang('deliverymethod_via'));
$this->smarty->assign('orderdate_label', $this->Lang('orderdate'));
$this->smarty->assign('deliverydate_label', $this->Lang('expecteddeliverydate'));
$this->smarty->assign('deliverydateexpl', $this->Lang('expecteddeliverydateexpl'));
$this->smarty->assign('paymentvia_label', $this->Lang('paymentmethod_via'));
$this->smarty->assign('orderremark_label', $this->Lang('orderremark_label'));

$this->smarty->assign('hidden', $this->CreateInputHidden($id, 'order_id', $order_id));
$this->smarty->assign('hiddenstatus', $this->CreateInputHidden($id, 'orderstatus', $orderstatus));
// Allow change of status from one status to the next, but not at moment that order has just been raised and when it has status invoiced
if ($orderstatus != 'INV') {
	$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('statusorderfrom' . $orderstatus)));
}
$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));
$this->smarty->assign('endform', $this->CreateFormEnd());

echo $this->ProcessTemplate('adminshoworder.tpl');
