<?php
#-------------------------------------------------------------------------
# Module: Cart Made Simple - An Order Intake module for CMS - CMS Made Simple
# Copyright (c) 2008 by Duketown
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
# The module's homepage is: http://dev.cmsmadesimple.org/projects/cartms/
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

$config = cmsms()->GetConfig();

if (isset($params['perfaction'])) $action = $params['perfaction'];
if (isset($params['category_id'])) $category_id = $params['category_id'];
if (isset($params['product_id'])) $product_id = $params['product_id'];
$attribute_id = 0;
if (isset($params['attribute_id'])) $attribute_id = $params['attribute_id'];
if (isset($params['qty'])) $qty = $params['qty'];

if (isset($action))
	switch ($action) {
		case 'add_product': {
				cartms_utils::AddProduct($category_id, $product_id, $attribute_id, $qty);
				break;
			}
		case 'update_product': {
				$sessionid = cartms_utils::GetSessionId();
				cartms_utils::UpdateProduct($sessionid, $product_id, $attribute_id, $qty);
				$this->orders->ShowCart($entryarray, $totalcost, $id, $returnid);
				break;
			}
		case 'remove_product': {
				cartms_utils::RemoveProduct($product_id, $attribute_id);
				break;
			}
		default: {
			}
	}
$this->orders->ShowCart($entryarray, $totalcost, $id, $returnid);

// Set variable for easy reading
$productcount = count($entryarray);

# Prepare the template values
$smarty->assign('productcount', $productcount);
$smarty->assign('products', $entryarray);
$smarty->assign('label_product_count', $this->Lang('label_product_count', $productcount));
$currency = $this->GetPreference('cartcurrency', 'Eur');
$formattedamount = $this->orders->FormatAmount($totalcost);

$smarty->assign('total_amount', $formattedamount);
$smarty->assign('currency', $currency);
$smarty->assign('label_total_amount', $this->Lang('label_total_amount', $formattedamount, $currency));
$smarty->assign('noproductsincart', $this->Lang('noproductsincart'));
$smarty->assign('productqtytext', $this->Lang('productqtytext'));
$smarty->assign('productnametext', $this->Lang('productnametext'));
$smarty->assign('productpricetext', $this->Lang('productpricetext'));
$smarty->assign('lineamounttext', $this->Lang('lineamounttext'));

if ($productcount > 0) {
	// Prepare connection to module FrontEndUsers
	$feu = &$this->GetModuleInstance('FrontEndUsers');
	if (!$feu) {
		echo $this->Lang('feumodulenotinstalled');
		return;
	}

	// FrontEndUsers module is installed. Now check if current visitor is known (read logged in)
	$userid = $feu->LoggedInId();
	$logged_in = !(!isset($uid) || $uid <= 0);
	$smarty->assign('logged_in',  $logged_in); // do this with CustomContent?

	if ($logged_in) {
		$smarty->assign('startcheckout', $this->Lang('continue_checkout_process'));
	} else {
		$orderhandlingtype = $this->GetPreference('orderhandlingtype', 'normal');
		switch ($orderhandlingtype) {
			case 'email':
				$prettyurl = 'SimpleCart/checkout1/' . ((isset($detailpage) && $detailpage != '') ? $detailpage : $returnid);
				$smarty->assign('startcheckout', $this->CreateLink(
					$id,
					'order',
					$returnid,
					$this->Lang('start_checkout_process'),
					array('perfaction' => 'request_emailcheckout'),
					'',
					false,
					true,
					'',
					true,
					$prettyurl
				));
				$smarty->assign('btn_checkout_startform', $this->CreateFrontendFormStart(
					$id,
					$returnid,
					'order',
					'post',
					'',
					true,
					'',
					array('perfaction' => 'request_emailcheckout')
				));
				break;
			case 'speed':
				$prettyurl = 'SimpleCart/checkout2/' . ((isset($detailpage) && $detailpage != '') ? $detailpage : $returnid);
				$smarty->assign('startcheckout', $this->CreateLink(
					$id,
					'order',
					$returnid,
					$this->Lang('start_checkout_process'),
					array('perfaction' => 'request_speedcheckout'),
					'',
					false,
					true,
					'',
					true,
					$prettyurl
				));
				$smarty->assign('btn_checkout_startform', $this->CreateFrontendFormStart(
					$id,
					$returnid,
					'order',
					'post',
					'',
					true,
					'',
					array('perfaction' => 'request_speedcheckout')
				));
				break;
			default:
				$prettyurl = 'SimpleCart/checkout0/' . ((isset($detailpage) && $detailpage != '') ? $detailpage : $returnid);
				$smarty->assign('startcheckout', $this->CreateLink(
					$id,
					'order',
					$returnid,
					$this->Lang('start_checkout_process'),
					array('perfaction' => 'request_ship_to_info'),
					'',
					false,
					true,
					'',
					true,
					$prettyurl
				));
				$smarty->assign('btn_checkout_startform', $this->CreateFrontendFormStart(
					$id,
					$returnid,
					'order',
					'post',
					'',
					true,
					'',
					array('perfaction' => 'request_ship_to_info')
				));
				break;
		}
	}
}
$image = '';
$image = cms_join_path('modules', $this->GetName(), 'images', 'checkout.png');
// Check if an image is available to use for button
$smarty->assign('btn_checkout', $this->CreateInputSubmit(
	$id,
	'btn_checkout',
	$this->Lang('start_checkout_process'),
	$returnid,
	$image
));
$smarty->assign('btn_checkout_endform', $this->CreateFormEnd());

// Continue shopping submit link to be prepared. The line below can be used as part of it.
$prettyurl = 'SimpleCart/cont/' . ((isset($detailpage) && $detailpage != '') ? $detailpage : $returnid);
$smarty->assign('continueshopping', $this->CreateLink(
	$id,
	'continueshopping',
	$returnid,
	$this->Lang('continueshopping'),
	array(),
	'',
	false,
	true,
	'',
	true,
	$prettyurl
));

$smarty->assign('btn_continue_startform', $this->CreateFrontendFormStart($id, $returnid, 'continueshopping'));
// Include image if available
$image = '';
$image = cms_join_path('modules', $this->GetName(), 'images', 'continue.png');
$smarty->assign('btn_continueshopping', $this->CreateInputSubmit(
	$id,
	'btn_continueshopping',
	$this->Lang('continueshopping'),
	$returnid,
	$image
));
$smarty->assign('btn_continue_endform', $this->CreateFormEnd());
// Set parameter for using pretty URL
$smarty->assign('useprettyurl', 'no');
// if (isset($config['url_rewriting']) && $config['url_rewriting'] == 'mod_rewrite') {
// 	$smarty->assign('useprettyurl', 'yes');
// 	$prettyurl = 'SimpleCart/updprod/' . $params['product_id'] . '/' .
// 		$params['attribute_id'] . '/';
// 	$smarty->assign('prettyurl', $prettyurl);
// }
// Display template
if (isset($params['cart_template'])) {
	$template = 'cart_' . $params['cart_template'];
} else {
	$template = $this->GetPreference('default_cart_template');
}
// If a template is known, use it. Else notify administator
if ($template != '') {
	echo $this->ProcessTemplateFromDatabase($template);
} else {
	// Not nice to notify a visitor in this way, but the administrator will know what to do
	echo 'No template passed from page and no default template available.<br>Please inform webmaster.<br>';
}
