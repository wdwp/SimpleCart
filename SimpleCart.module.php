<?php
#-------------------------------------------------------------------------
# Fork of Module: Cart Made Simple - An Order Intake module for CMS - CMS Made Simple
# Copyright (c) 2008 by Duketown
# Forked by Yuri Haperski (wdwp@yandex.ru)
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

$prog = cms_join_path(dirname(__FILE__), 'library', 'orders.api.php');
include_once($prog);
$prog = cms_join_path(dirname(__FILE__), 'library', 'admin.functions.php');
include_once($prog);

class SimpleCart extends CMSModule
{
	var $orders;

	function SimpleCart()
	{
		parent::CMSModule();
		$this->orders = new CMSOrders($this);
		$this->InitializeFrontend();
	}

	function GetName()
	{
		return 'SimpleCart';
	}

	function GetFriendlyName()
	{
		return $this->Lang('friendlyname');
	}

	function GetVersion()
	{
		return '1.1';
	}

	function GetHelp()
	{
		return $this->Lang('help');
	}

	function GetAuthor()
	{
		return 'Duketown';
	}

	function GetAuthorEmail()
	{
		// For spam reasons the mail address has been left out
		return '';
	}

	function GetChangeLog()
	{
		return file_get_contents(dirname(__FILE__) . '/changelog.inc');
	}

	function IsPluginModule()
	{
		return true;
	}

	function HasAdmin()
	{
		return true;
	}

	function GetAdminSection()
	{

		return 'ecommerce';
	}

	function GetAdminDescription()
	{
		return $this->Lang('moddescription');
	}

	function VisibleToAdminUser()
	{
		return $this->CheckPermission('Use SimpleCart') ||
			$this->CheckPermission('Delete SimpleCartOrders') ||
			$this->CheckPermission('Modify Templates') ||
			$this->CheckPermission('Modify SimpleCart');
	}

	function GetDependencies()
	{
		return array('SimpleShop' => '1.0', 'FrontEndUsers' => '3.0.0');
	}

	function MinimumCMSVersion()
	{
		return '2.1.0';
	}

	function MaximumCMSVersion()
	{
		return '3.0.0';
	}

	function InitializeFrontend()
	{
		$this->RegisterRoute(
			'/[sS]imple[cC]art\/cart\/(?P<returnid>[0-9]+)$/',
			array('action' => 'cart', 'perfaction' => '')
		);
		$this->RegisterRoute(
			'/[sS]imple[cC]art\/addproduct\/(?P<category_id>[0-9]+)\/(?P<product_id>[0-9]+)\/(?P<attribute_id>[0-9]+)\/(?P<qty>[0-9]+)\/(?P<returnid>[0-9]+)$/',
			array('action' => 'cart', 'perfaction' => 'add_product')
		);
		$this->RegisterRoute(
			'/[sS]imple[cC]art\/checkout0\/(?P<returnid>[0-9]+)$/',
			array('action' => 'order', 'perfaction' => 'request_ship_to_info')
		);
		$this->RegisterRoute(
			'/[sS]imple[cC]art\/checkout1\/(?P<returnid>[0-9]+)$/',
			array('action' => 'order', 'perfaction' => 'request_emailcheckout')
		);
		$this->RegisterRoute(
			'/[sS]imple[cC]art\/checkout2\/(?P<returnid>[0-9]+)$/',
			array('action' => 'order', 'perfaction' => 'request_speedcheckout')
		);
		$this->RegisterRoute(
			'/[sS]imple[cC]art\/cont\/(?P<returnid>[0-9]+)$/',
			array('action' => 'continueshopping')
		);
		// Order handling routes
		$this->RegisterRoute(
			'/[sS]imple[cC]art\/oa\/(?P<returnid>[0-9]+)$/',
			array('action' => 'orderaddress', 'perfaction' => 'request_ship_to_info')
		);
		$this->RegisterRoute(
			'/[sS]imple[cC]art\/od\/(?P<order_id>[0-9]+)\/(?P<agreetoterms>[0-9]+)\/(?P<returnid>[0-9]+)$/',
			array('action' => 'orderdelivery', 'perfaction' => 'request_ship_to_info')
		);
		$this->RegisterRoute(
			'/[sS]imple[cC]art\/rmv\/(?P<product_id>[0-9]+)\/(?P<attribute_id>[0-9]+)\/(?P<returnid>[0-9]+)$/',
			array('action' => 'cart', 'perfaction' => 'remove_product')
		);
		$this->RegisterRoute(
			'/[sS]imple[cC]art\/updprod\/(?P<product_id>[0-9]+)\/(?P<attribute_id>[0-9]+)\/(?P<qty>[0-9]+)\/(?P<returnid>[0-9]+)$/',
			array('action' => 'cart', 'perfaction' => 'update_product')
		);
		/*		$this->RegisterRoute('/[sS]imple[cC]art\/cnforder\/(?P<returnid>[0-9]+)$/',
			array('action'=>'order', 'perfaction' => 'request_confirmation'));
*/
	}

	/*---------------------------------------------------------
	   SetParameters()
	   Register the pretty URL's and allow only certain parameters on front end
	   ---------------------------------------------------------*/
	function SetParameters()
	{
		$this->RestrictUnknownParams();
		$this->SetParameterType('addresscity', CLEAN_STRING);
		$this->SetParameterType('addresscountry', CLEAN_STRING);
		$this->SetParameterType('addressstreet', CLEAN_STRING);
		$this->SetParameterType('addresszip', CLEAN_STRING);
		$this->SetParameterType('agreetoterms', CLEAN_STRING);
		$this->SetParameterType('attribute_id', CLEAN_INT);
		$this->SetParameterType('billaddresscity', CLEAN_STRING);
		$this->SetParameterType('billaddresscountry', CLEAN_STRING);
		$this->SetParameterType('billaddressstreet', CLEAN_STRING);
		$this->SetParameterType('billaddresszip', CLEAN_STRING);
		$this->SetParameterType('billfirstname', CLEAN_STRING);
		$this->SetParameterType('billlastname', CLEAN_STRING);
		$this->SetParameterType('cancel', CLEAN_STRING);
		$this->SetParameterType('category_id', CLEAN_INT);
		$this->SetParameterType('confirm', CLEAN_STRING);
		$this->SetParameterType('coupon_code', CLEAN_STRING);
		$this->SetParameterType('deliverymethod', CLEAN_STRING);
		$this->SetParameterType('email', CLEAN_STRING);
		$this->SetParameterType('firstname', CLEAN_STRING);
		$this->SetParameterType('lastname', CLEAN_STRING);
		$this->SetParameterType('name', CLEAN_STRING);
		$this->SetParameterType('oldstatus', CLEAN_STRING);
		$this->SetParameterType('order_id', CLEAN_INT);
		$this->SetParameterType('orderremark', CLEAN_STRING);
		$this->SetParameterType('orderstatus', CLEAN_STRING);
		$this->SetParameterType('paymentmethod', CLEAN_STRING);
		$this->SetParameterType('perfaction', CLEAN_STRING);
		$this->SetParameterType('product_id', CLEAN_INT);
		$this->SetParameterType('qty', CLEAN_INT);
		$this->SetParameterType('returnid', CLEAN_INT);
		$this->SetParameterType('returnmod', CLEAN_STRING);
		$this->SetParameterType('submit', CLEAN_STRING);
		$this->SetParameterType('submit_login', CLEAN_STRING);
		$this->SetParameterType('submit_register', CLEAN_STRING);
		$this->SetParameterType('template_email_checkout', CLEAN_STRING);
		$this->SetParameterType('template_shipping_info', CLEAN_STRING);
		$this->SetParameterType('template_speed_checkout', CLEAN_STRING);
	}

	/*---------------------------------------------------------
	   GetHeaderHTML()
	   This function inserts javascript (and links) into header of HTML
	  ---------------------------------------------------------*/
	function GetHeaderHTML()
	{
		// Include script so sorting of tables in backend is possible
		$javascript = '<script src="/modules/SimpleShop/js/jquery.tablesorter.min.js"></script>' . "\n";
		$javascript .= '<link href="/modules/SimpleShop/css/theme.metro-dark.min.css" rel="stylesheet">' . "\n";
		$javascript .= '<script id="js">jQuery(document).ready(function()
		{
			jQuery(".cms_sortable")
				.tablesorter({theme: "metro-dark"});
		}
		);
		</script>';

		return $javascript;
	}

	function GetEventDescription($eventname)
	{
		return $this->Lang('event_info_' . $eventname);
	}

	function GetEventHelp($eventname)
	{
		return $this->Lang('event_help_' . $eventname);
	}

	function InstallPostMessage()
	{
		return $this->Lang('postinstall');
	}

	function UninstallPostMessage()
	{
		return $this->Lang('postuninstall');
	}

	function UninstallPreMessage()
	{
		return $this->Lang('really_uninstall');
	}

	function ValidateEmailAddress($emailaddress)
	{
		if (!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $emailaddress)) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Translates a number to a short alhanumeric version
	 *
	 * Translated any number up to 9007199254740992
	 * to a shorter version in letters e.g.:
	 * 9007199254740989 --> PpQXn7COf
	 *
	 * specifiying the second argument true, it will
	 * translate back e.g.:
	 * PpQXn7COf --> 9007199254740989
	 *
	 * this function is based on any2dec && dec2any by
	 * fragmer[at]mail[dot]ru
	 * see: http://nl3.php.net/manual/en/function.base-convert.php#52450
	 *
	 * If you want the alphaID to be at least 3 letter long, use the
	 * $pad_up = 3 argument
	 *
	 * In most cases this is better than totally random ID generators
	 * because this can easily avoid duplicate ID's.
	 * For example if you correlate the alpha ID to an auto incrementing ID
	 * in your database, you're done.
	 *
	 * The reverse is done because it makes it slightly more cryptic,
	 * but it also makes it easier to spread lots of IDs in different
	 * directories on your filesystem. Example:
	 * $part1 = substr($alpha_id,0,1);
	 * $part2 = substr($alpha_id,1,1);
	 * $part3 = substr($alpha_id,2,strlen($alpha_id));
	 * $destindir = "/".$part1."/".$part2."/".$part3;
	 * // by reversing, directories are more evenly spread out. The
	 * // first 26 directories already occupy 26 main levels
	 *
	 * more info on limitation:
	 * - http://blade.nagaokaut.ac.jp/cgi-bin/scat.rb/ruby/ruby-talk/165372
	 *
	 * if you really need this for bigger numbers you probably have to look
	 * at things like: http://theserverpages.com/php/manual/en/ref.bc.php
	 * or: http://theserverpages.com/php/manual/en/ref.gmp.php
	 * but I haven't really dugg into this. If you have more info on those
	 * matters feel free to leave a comment.
	 *
	 * @author  Kevin van Zonneveld <kevin@vanzonneveld.net>
	 * @author  Simon Franz
	 * @author  Deadfish
	 * @copyright 2008 Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD Licence
	 * @version   SVN: Release: $Id: alphaID.inc.php 344 2009-06-10 17:43:59Z kevin $
	 * @link    http://kevin.vanzonneveld.net/
	 *
	 * @param mixed   $in    String or long input to translate
	 * @param boolean $to_num  Reverses translation when true
	 * @param mixed   $pad_up  Number or boolean padds the result up to a specified length
	 * @param string  $passKey Supplying a password makes it harder to calculate the original ID
	 *
	 * @return mixed string or long
	 */
	function alphaID($in, $to_num = false, $pad_up = false, $passKey = null)
	{
		$index = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if ($passKey !== null) {
			// Although this function's purpose is to just make the
			// ID short - and not so much secure,
			// with this patch by Simon Franz (http://blog.snaky.org/)
			// you can optionally supply a password to make it harder
			// to calculate the corresponding numeric ID

			for ($n = 0; $n < strlen($index); $n++) {
				$i[] = substr($index, $n, 1);
			}

			$passhash = hash('sha256', $passKey);
			$passhash = (strlen($passhash) < strlen($index))
				? hash('sha512', $passKey)
				: $passhash;

			for ($n = 0; $n < strlen($index); $n++) {
				$p[] =  substr($passhash, $n, 1);
			}

			array_multisort($p,  SORT_DESC, $i);
			$index = implode($i);
		}

		$base  = strlen($index);

		if ($to_num) {
			// Digital number  <<--  alphabet letter code
			$in  = strrev($in);
			$out = 0;
			$len = strlen($in) - 1;
			for ($t = 0; $t <= $len; $t++) {
				$bcpow = bcpow($base, $len - $t);
				$out   = $out + strpos($index, substr($in, $t, 1)) * $bcpow;
			}

			if (is_numeric($pad_up)) {
				$pad_up--;
				if ($pad_up > 0) {
					$out -= pow($base, $pad_up);
				}
			}
			$out = sprintf('%F', $out);
			$out = substr($out, 0, strpos($out, '.'));
		} else {
			// Digital number  -->>  alphabet letter code
			if (is_numeric($pad_up)) {
				$pad_up--;
				if ($pad_up > 0) {
					$in += pow($base, $pad_up);
				}
			}

			$out = "";
			for ($t = floor(log($in, $base)); $t >= 0; $t--) {
				$bcp = bcpow($base, $t);
				$a   = floor($in / $bcp) % $base;
				$out = $out . substr($index, $a, 1);
				$in  = $in - ($a * $bcp);
			}
			$out = strrev($out); // reverse
		}

		return $out;
	}

	/*---------------------------------------------------------
	   InventoryDecrease()
	   This function checks if one wants to track inventory.
	   If so the number of sold items is subtracted from the available quantity.
	   Quantity on stock is either per product or per attribute
	   These values are held in Simple Shop
	   If module Inventory Management is installed, transactions are prepared
	   Parameters:
	   	- order_id	used to get all order lines with products and quantities
	   	- status The status of the order at the moment it was passed here
	   ---------------------------------------------------------*/
	function InventoryDecrease($order_id, $status = 'CNF')
	{
		$db = cmsms()->GetDb();

		// Check if Shop Made Simple/Inventory Management are installed
		$modops = cmsms()->GetModuleOperations();
		$ShopMS = $modops->get_module_instance('SimpleShop');
		$DTI = $modops->get_module_instance('DTInventory');
		// Only process the order item quantities if this is correct timing
		if ($status != $ShopMS->GetPreference('salesinventtiming', 'CNF')) {
			return false;
		}
		if ($ShopMS) {
			$inventorytype = $ShopMS->GetSMSPreference('inventorytype', 'none');
			if ($inventorytype != 'none') {
				// Update inventory based upon order lines
				foreach ($this->orders->GetOrderLines($order_id) as $orderline) {
					$InventParms = array();
					switch ($inventorytype) {
						case 'prod':
							$sql = 'UPDATE ' . cms_db_prefix() . 'module_sms_products
								SET maxattributes = maxattributes - ?
								WHERE product_id = ?';
							$dbresult = $db->Execute($sql, array(
								$orderline->qty,
								$orderline->product_id
							));
							$item_id = $orderline->product_id;
							$description = substr($orderline->categoryname . ' | ' . $orderline->productname, 0, 80);
							break;
						case 'attr':
							$sql = 'UPDATE ' . cms_db_prefix() . 'module_sms_product_attributes
								SET maxallowed = maxallowed - ?
								WHERE attribute_id = ?';
							$dbresult = $db->Execute($sql, array(
								$orderline->qty,
								$orderline->attribute_id
							));
							$item_id = $orderline->attribute_id;
							$description = substr($orderline->productname . ' | ' . $orderline->attributename, 0, 80);
							break;
					}

					if ($DTI) {
						// Generate sales transaction for Inventory Management
						$InventParms['inventorytype'] = $inventorytype;
						$InventParms['item_id'] = $item_id;
						$InventParms['location_id'] = 0; // Default set in DTInventory
						$InventParms['quantity'] = $orderline->qty;
						$InventParms['type_code'] = $DTI->GetPreference('defaultsalestype_code');
						$InventParms['cost_price'] = $orderline->price;
						$InventParms['remark'] = $this->Lang('inventtransactionremark', $order_id);
						$InventParms['itemnumber'] = $orderline->itemnumber;
						$InventParms['description'] = $description;
						$transaction_id = $DTI->GenerateTransaction($InventParms);
						$DTI->AdjustQuantityOnLocation($InventParms);
					}
				}
			}
		}
	}

	/*---------------------------------------------------------
	   InventoryOnStockAvail()
	   This function checks if one wants to track inventory.
	   If so the number of available items is returned
	   with a maximum of 10 (this could change into a preference).
	   Quantity on stock is either per product or per attribute
	   These values are held in Simple Shop
	   Parameters:
	   	- category_id Can be used later if inventory held per category
	   	- product_id
	   	- attribute_id
	   ---------------------------------------------------------*/
	function InventoryOnStockAvail($product_id, $attribute_id)
	{
		$db = cmsms()->GetDb();
		$maxquantityperorderline = 20;
		$min_qty = 1;

		$ShopMS = &$this->GetModuleInstance('SimpleShop');
		if ($ShopMS) {
			$inventorytype = $ShopMS->GetSMSPreference('inventorytype', 'none');
			$maxquantityperorderline = $ShopMS->GetSMSPreference('maxquantityperorderline', 20);
			if ($inventorytype != 'none') {
				// Check inventory based type of inventory
				switch ($inventorytype) {
					case 'prod':
						$sql = 'SELECT maxattributes FROM ' . cms_db_prefix() . 'module_sms_products
							WHERE product_id = ?';
						$row = $db->GetRow($sql, array($product_id));
						if ($row) {
							$quantityonstock = $row['maxattributes'];
						}
						break;
					case 'attr':
						$sql = 'SELECT minallowed, maxallowed FROM ' . cms_db_prefix() . 'module_sms_product_attributes
							WHERE attribute_id = ?';
						$row = $db->GetRow($sql, array($attribute_id));
						if ($row) {
							$quantityonstock = $row['maxallowed'];
							$min_qty = ($row['minallowed'] > 0) ? $row['minallowed'] : 1;
						} else {
							$quantityonstock = $maxquantityperorderline;
						}
						break;
				}
				if ($quantityonstock < $maxquantityperorderline) {
					return array($quantityonstock, $min_qty);
				}
			}
		}
		return array($maxquantityperorderline, $min_qty);
	}

	/*---------------------------------------------------------
		Using a preference a new invoice number will be prepared and
		returned to calling statement
	  ---------------------------------------------------------*/
	function PrepareInvoiceNo()
	{
		$invoiceno = (int) $this->GetPreference('invoiceno', 0) + 1;
		$this->SetPreference('invoiceno', $invoiceno);
		$invoiceno = $this->GetPreference('invoice_prefix', 'I') . $invoiceno;
		return $invoiceno;
	}

	/*---------------------------------------------------------
		This function allow retrieval from preferences from this module
		when needed in another module
	  ---------------------------------------------------------*/
	function GetCartMSPreference($preference, $default = '')
	{
		return $this->GetPreference($preference, $default);
	}

	function SendInvoiceAsAttach($order_id)
	{
		$orderheader = array();
		$orderheader = cartms_utils::GetOrderHeader($order_id);
		$shipto = array();
		$shipto = $this->orders->GetOrderShipTo($orderheader['customer_id']);
		// Translations
		$invlabel_invoicefrom = $this->Lang('invoicefrom');
		$invlabel_invoice = $this->Lang('inv_invoice');
		$invlabel_order = $this->Lang('inv_order');
		$invoicepath = cms_join_path($this->config['uploads_path'], $this->getName(), $orderheader['invoiceno'] . '.pdf');
		// Send the invoice
		$cmsmailer = $this->GetModuleInstance('CMSMailer');

		// Fill the mail body. This will become data merged with a template
		if ($orderheader['order_id'] != '') $this->smarty->assign('order_id', $orderheader['order_id']);
		if ($orderheader['customer_id'] != '') $this->smarty->assign('customer_id', $orderheader['customer_id']);
		if ($orderheader['invoiceno'] != '') $this->smarty->assign('invoiceno', $orderheader['invoiceno']);
		if ($shipto['shiptoname'] != '') $this->smarty->assign('shiptoname', $shipto['shiptoname']);
		if ($shipto['addressstreet'] != '') $this->smarty->assign('shiptostreet', $shipto['addressstreet']);
		if ($shipto['addresscity'] != '') $this->smarty->assign('shiptocity', $shipto['addresscity']);
		if ($shipto['addressstate'] != '') $this->smarty->assign('shiptostate', $shipto['addressstate']);
		if ($shipto['addresszip'] != '') $this->smarty->assign('shiptozip', $shipto['addresszip']);
		if ($shipto['addresscountry'] != '') $this->smarty->assign('shiptocountry', $shipto['addresscountry']);
		if ($shipto['telephone'] != '') $this->smarty->assign('shiptotelephone', $shipto['telephone']);
		if ($shipto['billtoname'] != '') $this->smarty->assign('billtoname', $shipto['billtoname']);
		if ($shipto['billaddressstreet'] != '') $this->smarty->assign('billtostreet', $shipto['billaddressstreet']);
		if ($shipto['billaddresscity'] != '') $this->smarty->assign('billtocity', $shipto['billaddresscity']);
		if ($shipto['billaddressstate'] != '') $this->smarty->assign('billtostate', $shipto['billaddressstate']);
		if ($shipto['billaddresszip'] != '') $this->smarty->assign('billtozip', $shipto['billaddresszip']);
		if ($shipto['billaddresscountry'] != '') $this->smarty->assign('billtocountry', $shipto['billaddresscountry']);
		if ($shipto['email'] != '') $this->smarty->assign('email', $shipto['email']);
		$mailbody = $this->ProcessTemplateFromDatabase('cust_invmail_template');
		$cmsmailer->AddAddress($shipto['email']);
		$cmsmailer->SetBody($mailbody);
		$cmsmailer->IsHTML(true);
		$cmsmailer->SetSubject($this->GetPreference('cust_invmail_subject', $this->Lang('yourinvoice')));
		$cmsmailer->AddAttachment(
			$invoicepath,
			$invlabel_invoice . $orderheader['invoiceno'] . '.pdf',
			'base64',
			'application/octent-stream'
		);
		$mailsend = $cmsmailer->Send();
	}

	/*---------------------------------------------------------
		This function prepares a list of possible actions that can be taken
		Parameter orderstatus will be excluded from list
	  ---------------------------------------------------------*/
	function GetMassActions($orderstatus = '')
	{
		$massactionlist = array();

		if ($orderstatus == 'INT') {
			// Deletion allowed?
			if ($this->CheckPermission('Modify SimpleCart')) {
				$massactionlist[$this->Lang('massactiondelete')] = 'MADel';
			}
			$massactionlist[$this->Lang('massactionconfirmed')] = 'MACNF';
			$massactionlist[$this->Lang('massactionpaid')] = 'MAPAY';
			$massactionlist[$this->Lang('massactionshipped')] = 'MASHP';
			//$massactionlist[$this->Lang('massactioninvoiced')] = 'MAINV';
		}
		if ($orderstatus == 'CNF') {
			$massactionlist[$this->Lang('massactionpaid')] = 'MAPAY';
			$massactionlist[$this->Lang('massactionshipped')] = 'MASHP';
			//$massactionlist[$this->Lang('massactioninvoiced')] = 'MAINV';
			$massactionlist[$this->Lang('massactioninitialized')] = 'MAINT';
			// Deletion allowed?
			if ($this->CheckPermission('Modify SimpleCart')) {
				$massactionlist[$this->Lang('massactiondelete')] = 'MADel';
			}
		}
		if ($orderstatus == 'PAY') {
			$massactionlist[$this->Lang('massactionshipped')] = 'MASHP';
			//$massactionlist[$this->Lang('massactioninvoiced')] = 'MAINV';
			$massactionlist[$this->Lang('massactionconfirmed')] = 'MACNF';
			$massactionlist[$this->Lang('massactioninitialized')] = 'MAINT';
			// Deletion allowed?
			if ($this->CheckPermission('Modify SimpleCart')) {
				$massactionlist[$this->Lang('massactiondelete')] = 'MADel';
			}
		}
		if ($orderstatus == 'SHP') {
			//$massactionlist[$this->Lang('massactioninvoiced')] = 'MAINV';
			if ($this->CheckPermission('Modify SimpleCart')) {
				$massactionlist[$this->Lang('massactiondelete')] = 'MADel';
			}
			$massactionlist[$this->Lang('massactionpaid')] = 'MAPAY';
			$massactionlist[$this->Lang('massactionconfirmed')] = 'MACNF';
			$massactionlist[$this->Lang('massactioninitialized')] = 'MAINT';
			// Deletion allowed?

		}
		if ($orderstatus == 'INV') {
			// Deletion allowed?
			if ($this->CheckPermission('Modify SimpleCart')) {
				$massactionlist[$this->Lang('massactiondelete')] = 'MADel';
			}
			$massactionlist[$this->Lang('massactionshipped')] = 'MASHP';
			$massactionlist[$this->Lang('massactionconfirmed')] = 'MACNF';
			$massactionlist[$this->Lang('massactionpaid')] = 'MAPAY';
			$massactionlist[$this->Lang('massactioninitialized')] = 'MAINT';
		}
		//$massactionlist[$this->Lang('massactionexport')] = 'MAEXP';

		return $massactionlist;
	}

	/*---------------------------------------------------------
		This function obviously retrieves only a description of payment gateway
	  ---------------------------------------------------------*/
	function GetPaymentGatewayDescription($paymethod)
	{
		$db = cmsms()->GetDb();
		$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_pms_gateways
			WHERE active > 0 AND gateway_code = ?';
		$row = $db->GetRow($query, array($paymethod));
		if ($row) {
			return $row['description'];
		} else {
			return '';
		}
	}
}
