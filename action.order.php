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

$db = cmsms()->GetDb();

if (isset($params['perfaction'])) $action = $params['perfaction'];

// Check if pretty URL's are requested
$p_url = false;
// if (isset($this->config['url_rewriting']) && $this->config['url_rewriting'] <> 'none') {
// 	$p_url = true;
// }

switch ($action) {
	case 'request_ship_to_info':
		// Visitor is not logged in. Login needed, since FEU controls address information for orders
		if ($p_url) {
			$prettyurl = 'SimpleCart/oa/' . ($detailpage != '' ? $detailpage : $returnid);
			redirect($this->create_url(
				$id,
				'orderaddress',
				$returnid,
				$params,
				false,
				false,
				$prettyurl
			));
		}
		$this->RedirectForFrontend($id, $returnid, 'orderaddress', $params);
		break;
	case 'request_delivery_info':
		$params['continue'] = NULL;
		// Visitor is customer since he/she is known in FEU (and thus 0 or more orders processed earlier)
		// Request how order is to be delivered
		if ($p_url) {
			$prettyurl = 'SimpleCart/od/' . $params['order_id'] . '/' . $params['agreetoterms'] . '/' .
				($detailpage != '' ? $detailpage : $returnid);
			redirect($this->create_url(
				$id,
				'orderdelivery',
				$returnid,
				$params,
				false,
				false,
				$prettyurl
			));
		}
		$this->RedirectForFrontend($id, $returnid, 'orderdelivery', $params);
		break;
	case 'request_payment_method':
		$params['continue'] = NULL;
		// We now know how delivery is to be done, but how will it be paid
		if ($p_url) {
			$prettyurl = 'SimpleCart/op/' . ($detailpage != '' ? $detailpage : $returnid);
			redirect($this->create_url(
				$id,
				'orderpayment',
				$returnid,
				$params,
				false,
				false,
				$prettyurl
			));
		}
		$this->RedirectForFrontend($id, $returnid, 'orderpayment', $params);
		break;
	case 'request_confirmation':
		// All information available, request confirmation of order
		if ($p_url) {
			$prettyurl = 'SimpleCart/oc/' . ($detailpage != '' ? $detailpage : $returnid);
			redirect($this->create_url(
				$id,
				'orderconfirm',
				$returnid,
				$params,
				false,
				false,
				$prettyurl
			));
		}
		$this->RedirectForFrontend($id, $returnid, 'orderconfirm', $params);
		break;
	case 'request_emailcheckout':
		// Only a limited set of information is needed: only email address
		if ($p_url) {
			$prettyurl = 'SimpleCart/oc1/' . ($detailpage != '' ? $detailpage : $returnid);
			redirect($this->create_url(
				$id,
				'orderemailcheckout',
				$returnid,
				$params,
				false,
				false,
				$prettyurl
			));
		}
		$this->RedirectForFrontend($id, $returnid, 'orderemailcheckout', $params);
		break;
	case 'request_speedcheckout':
		// This part only used if first request or something wrong during speed checkout
		if ($p_url) {
			$prettyurl = 'SimpleCart/oc2/' . ($detailpage != '' ? $detailpage : $returnid);
			redirect($this->create_url(
				$id,
				'orderspeedcheckout',
				$returnid,
				$params,
				false,
				false,
				$prettyurl
			));
		}
		$this->RedirectForFrontend($id, $returnid, 'orderspeedcheckout', $params);
		break;
	case 'please_come_back':
		// State that the goods will be shipped very soon and that
		// the order has been much appriciated
		if ($p_url) {
			$prettyurl = 'SimpleCart/ot/' . ($detailpage != '' ? $detailpage : $returnid);
			redirect($this->create_url(
				$id,
				'orderthankyou',
				$returnid,
				$params,
				false,
				false,
				$prettyurl
			));
		}
		$this->RedirectForFrontend($id, $returnid, 'orderthankyou', $params);
		break;
	default:
}
$this->orders->ShowCart($entryarray, $totalcost, $id, $returnid);

# Prepare the template values
$this->smarty->assign('productcount', count($entryarray));
$this->smarty->assign('products', $entryarray);
$this->smarty->assign('label_product_count', $this->Lang('label_product_count', count($entryarray)));
$this->smarty->assign('label_total_amount', $this->Lang('label_total_amount', 'Eur', number_format($totalcost, 2, ".", ",")));
$this->smarty->assign('noproductsincart', $this->Lang('noproductsincart'));
$this->smarty->assign('productqtytext', $this->Lang('productqtytext'));
$this->smarty->assign('productnametext', $this->Lang('productnametext'));
$this->smarty->assign('productpricetext', $this->Lang('productpricetext'));
$this->smarty->assign('lineamounttext', $this->Lang('lineamounttext'));

// Prepare connection to module FrontEndUsers
$feu = &$this->GetModuleInstance('FrontEndUsers');
if (!$feu) {
	echo $this->DisplayErrorMessage($this->Lang('feumodulenotinstalled'));
	return;
}

// FrontEndUsers module is installed. Now check if current visitor is known (read logged in)
$userid = $feu->LoggedInId();
$logged_in = !(!isset($uid) || $uid <= 0);
$smarty->assign('logged_in',  $logged_in); // do this with CustomContent?

if ($logged_in) {
	$this->smarty->assign('startcheckout', $this->Lang['continue_checkout_process']);
} else {
	$orderhandlingtype = $this->GetPreference('orderhandlingtype', 'normal');
	switch ($orderhandlingtype) {
		case 'speed':
			$this->smarty->assign('startcheckout', $this->CreateLink(
				$id,
				'order',
				$returnid,
				$this->Lang('start_checkout_process'),
				array('perfaction' => 'request_speedcheckout')
			));
			break;
		default:
			$this->smarty->assign('startcheckout', $this->CreateLink(
				$id,
				'order',
				$returnid,
				$this->Lang('start_checkout_process'),
				array('perfaction' => 'request_ship_to_info')
			));
			break;
	}
}

# Prepare link to payments module. There could be various modules.
# Link to module Payment Made Simple
$modops = cmsms()->GetModuleOperations();
$PayMS = $modops->get_module_instance('SimplePayment');

if ($PayMS) {
	# Payment model installed and active, prepare the link
	$this->smarty->assign('startcheckout', $this->CreateLink(
		$id,
		'orderdelivery',
		$returnid,
		$this->Lang('title_welcomshipment'),
		array(
			'step' => 'step1'
		)
	));
}

#Display template
$template = 'fe_showcart.tpl';
if (isset($params['detailtemplate'])) {
	$template = 'detail' . $params['detailtemplate'];
}
echo $this->ProcessTemplate($template);
