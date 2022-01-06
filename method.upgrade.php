<?php
# Module: Cart Made Simple - An Order Intake module for CMS - CMS Made Simple
# Copyright (c) 2008 by Duketown 
#
# This function will upgrade the module Cart Made Simple
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

$dict = NewDataDictionary($db);

$current_version = $oldversion;
switch ($current_version) {
	case '0.0.2':
		# Setup tinybox template
		$fn = cms_join_path(dirname(__FILE__), 'templates', 'tinybox.tpl');
		if (file_exists($fn)) {
			$template = @file_get_contents($fn);
			$this->SetPreference('default_cart_tinybox', $template);
			$this->SetTemplate('cart_tinybox', getDefaultTemplate('cart_'));
		}

		# Setup normalbox template
		$fn = cms_join_path(dirname(__FILE__), 'templates', 'normalbox.tpl');
		if (file_exists($fn)) {
			$template = @file_get_contents($fn);
			$this->SetPreference('default_cart_normalbox', $template);
			$this->SetTemplate('cart_normalbox', getDefaultTemplate('cart_'));
		}

		# Setup front end templates
		$fn = cms_join_path(dirname(__FILE__), 'templates', 'fe_delivery_info.tpl');
		if (file_exists($fn)) {
			$template = @file_get_contents($fn);
			$this->SetTemplate('cart_fe_delivery_info', $template);
		}
		$fn = cms_join_path(dirname(__FILE__), 'templates', 'fe_orderconfirm.tpl');
		if (file_exists($fn)) {
			$template = @file_get_contents($fn);
			$this->SetTemplate('cart_fe_orderconfirm', $template);
		}
		$fn = cms_join_path(dirname(__FILE__), 'templates', 'fe_payment_info.tpl');
		if (file_exists($fn)) {
			$template = @file_get_contents($fn);
			$this->SetTemplate('cart_fe_payment_info', $template);
		}
		$fn = cms_join_path(dirname(__FILE__), 'templates', 'fe_shipping_info.tpl');
		if (file_exists($fn)) {
			$template = @file_get_contents($fn);
			$this->SetTemplate('cart_fe_shipping_info', $template);
		}
		$fn = cms_join_path(dirname(__FILE__), 'templates', 'fe_showcart.tpl');
		if (file_exists($fn)) {
			$template = @file_get_contents($fn);
			$this->SetTemplate('cart_fe_showcart', $template);
		}

		$current_version = '0.0.3';

	case '0.0.3':
		// Remove an earlier prepared preference. It will come back with better naming convention
		$this->RemovePreference('addadmincost');
		// Add various preferences that control part of the module
		$this->SetPreference('admincost', 0);
		$this->SetPreference('admincostadd', false);
		$this->SetPreference('admincostminamount', 100);
		$this->SetPreference('cartcurrency', 'Eur');
		$this->SetPreference('mandatorystate', false);
		$this->SetPreference('numberformatdecimals', '2');
		$this->SetPreference('numberformatdec_point', ',');
		$this->SetPreference('numberformatthousands_sep', '.');
		$this->SetPreference('showinmenu', 'extensions');
		$this->SetPreference('storeshipaddress', true);

		// Table schema description for table: shippingprovider
		$flds = "
			shipprov_id I KEY,
			shipprovcode C(3),
			shipprovdesc C(255),
			shipprovprice F,
			active L(1)
			";

		// Create it. This should do error checking.
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix() . 'module_cartms_shippingprovider', $flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);
		// Create a sequence
		$db->CreateSequence(cms_db_prefix() . 'module_cartms_shippingprovider_seq');

		// Add field to hold administration cost per order
		$dict = NewDataDictionary($db);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_cartms_orders', 'totaladmincost F');
		$dict->ExecuteSQLArray($sqlarray);

		$current_version = '0.0.4';

	case '0.0.4':
		$this->SetPreference('contentthankyou', '');
		// Change fields to hold decimals
		$dict = NewDataDictionary($db);
		$sqlarray = $dict->AlterColumnSQL(cms_db_prefix() . 'module_cartms_order_lines', 'price F');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AlterColumnSQL(cms_db_prefix() . 'module_cartms_order_lines', 'lineamount F');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AlterColumnSQL(cms_db_prefix() . 'module_cartms_orders', 'totalproduct F');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AlterColumnSQL(cms_db_prefix() . 'module_cartms_orders', 'totalshipping F');
		$dict->ExecuteSQLArray($sqlarray);

		$current_version = '0.1.0';

	case '0.1.0':
		/**
		 * Create an example stylesheet for Cart Made Simple tags
		 */
		$new_css_id = $db->GenID(cms_db_prefix() . 'css_seq');
		$css_name = $this->Lang('module_example_stylesheet'); // Retrieve the name of the new stylesheet locate in the css directory
		$css_text = file_get_contents($this->cms->config['root_path'] . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $this->GetName() .
			DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'cartmsstylesheet.css');
		$media_type = '';
		# Add the stylesheet to the database
		$query = 'INSERT INTO ' . cms_db_prefix() . 'css (css_id, css_name, css_text, media_type, create_date, modified_date) VALUES (?, ?, ?, ?, ?, ?)';
		$result = $db->Execute($query, array($new_css_id, $css_name, $css_text, $media_type, $db->DBTimeStamp(time()), $db->DBTimeStamp(time())));
		// Add field to hold total netweight per order
		$dict = NewDataDictionary($db);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_cartms_orders', 'totalnetweight F');
		$dict->ExecuteSQLArray($sqlarray);
		// Add total vat amounts per vat code
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_cartms_orders', 'totalvat0amount F');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_cartms_orders', 'totalvat1amount F');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_cartms_orders', 'totalvat2amount F');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_cartms_orders', 'totalvat3amount F');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_cartms_orders', 'totalvat4amount F');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_cartms_order_lines', 'vatcode C(1)');
		$dict->ExecuteSQLArray($sqlarray);
		// Prepare fields to hold VAT rates
		$this->SetPreference('vat0name', '');
		$this->SetPreference('vat1name', '');
		$this->SetPreference('vat2name', '');
		$this->SetPreference('vat3name', '');
		$this->SetPreference('vat4name', '');
		$this->SetPreference('vat0perc', 0);
		$this->SetPreference('vat1perc', 0);
		$this->SetPreference('vat2perc', 0);
		$this->SetPreference('vat3perc', 0);
		$this->SetPreference('vat4perc', 0);
		// Prepare template so it can be used from database
		$fn = cms_join_path(dirname(__FILE__), 'templates', 'fe_thankyou_info.tpl');
		if (file_exists($fn)) {
			$template = @file_get_contents($fn);
			$this->SetTemplate('cart_fe_thankyou_info', $template);
		}

		$current_version = '0.1.1';

	case '0.1.1':
		$this->SetPreference('desclength', 40); // To be build in later in configuration 
		$this->CreatePermission('Modify SimpleCart', 'Modify Simple Cart');
		/**
		 * Prepare one delivery method (self collecting)
		 **/
		$shipprov_id = $db->GenID(cms_db_prefix() . 'module_cartms_shippingprovider_seq');
		$shipprovcode = 'SEC';
		$shipprovdesc = $this->Lang('deliverselfcollect');
		$shipprovprice = 0;
		$status = 1;
		$query = 'INSERT INTO ' . cms_db_prefix() . 'module_cartms_shippingprovider (shipprov_id, shipprovcode, shipprovdesc, shipprovprice, active)
		 VALUES (?,?,?,?,?)';
		$db->Execute($query, array($shipprov_id, $shipprovcode, $shipprovdesc, $shipprovprice, $status));

		$current_version = '0.1.2';

	case '0.1.2':
		$this->SetPreference('sendconfirmationmail', false);
		$this->SetPreference('custmail_subject', $this->Lang('yourorder'));
		$this->SetPreference('admin_subject', $this->Lang('neworderplaced'));
		$this->SetPreference('admin_emailaddress', 'me@_myshop_.com');
		# Setup mail templates
		$fn = cms_join_path(dirname(__FILE__), 'templates', 'orig_custmail_template.tpl');
		if (file_exists($fn)) {
			$template = @file_get_contents($fn);
			$this->SetTemplate('custmail_template', $template);
		}
		$fn = cms_join_path(dirname(__FILE__), 'templates', 'orig_admin_template.tpl');
		if (file_exists($fn)) {
			$template = @file_get_contents($fn);
			$this->SetTemplate('admin_template', $template);
		}

		$current_version = '0.1.3';

	case '0.1.3':
		$current_version = '0.1.4';

	case '0.1.4':
		// By special request of some users of the module, a telephone number is added as property.
		// Next to this immediatly the billing address has been build in
		global $gCms;
		// Tried to get a direct link to the object FrontEndUsers. This results in a 'call to member function on a non-object'
		// Since I don't get that easily out of the way, direct SQL is the path chosen
		$groupname = 'SimpleCart';
		$qry = 'SELECT * FROM ' . cms_db_prefix() . 'module_feusers_groups WHERE groupname = ?';
		$dbresult = $db->Execute($qry, array($groupname));
		if ($dbresult && $dbresult->RecordCount()) {
			$row = $dbresult->FetchRow();
			$groupid = $row['id'];
		}
		// Add the new properties, which gets 9 an onwards as sorting order (since this is same as with clean install)
		$qry = 'INSERT INTO ' . cms_db_prefix() . 'module_feusers_propdefn VALUES (?,?,?,?,?)';
		$p = array('telephone', 'Telephone', 0, 35, 35);
		$dbresult = $db->Execute($qry, $p);
		$p = array('billaddressstreet', 'Bill to street', 0, 50, 200);
		$dbresult = $db->Execute($qry, $p);
		$p = array('billaddresscity', 'Bill to city', 0, 50, 100);
		$dbresult = $db->Execute($qry, $p);
		$p = array('billaddressstate', 'Bill to state/Province', 0, 20, 20);
		$dbresult = $db->Execute($qry, $p);
		$p = array('billaddresszip', 'Bill to zip/Postal code', 0, 10, 10);
		$dbresult = $db->Execute($qry, $p);
		$p = array('billaddresscountry', 'Bill to country', 4, 3, 255);
		$dbresult = $db->Execute($qry, $p);
		// Make sure that the properties are connected to the group
		$qry = "INSERT INTO " . cms_db_prefix() . "module_feusers_grouppropmap VALUES(?,?,?,?,?)";
		$p = array('telephone', $groupid, 9, 1, -1);
		$dbresult = $db->Execute($qry, $p);
		$p = array('billaddressstreet', $groupid, 10, 1, -1);
		$dbresult = $db->Execute($qry, $p);
		$p = array('billaddresscity', $groupid, 11, 1, -1);
		$dbresult = $db->Execute($qry, $p);
		$p = array('billaddressstate', $groupid, 12, 1, -1);
		$dbresult = $db->Execute($qry, $p);
		$p = array('billaddresszip', $groupid, 13, 1, -1);
		$dbresult = $db->Execute($qry, $p);
		$p = array('billaddresscountry', $groupid, 14, 1, -1);
		$dbresult = $db->Execute($qry, $p);

		$this->SetPreference('mandatorytelephone', false);

		$current_version = '0.1.5';
	case '0.1.5':
		// Table schema description for table: newuserpasswords
		$flds = "
			user_id I KEY,
			password C(50)
			";

		// Create it. This should do error checking.
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix() . 'module_cartms_newuserpasswords', $flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);

		$current_version = '0.1.6';
	case '0.1.6':

		$current_version = '0.1.7';
	case '0.1.7':
		// Serious bug on handling payments and setting statusses solved. No changes in database.

		$current_version = '0.1.8';
	case '0.1.8':

		$current_version = '0.1.9';
	case '0.1.9':
		$this->SetPreference('contenttradeterms', '');
		// Add field to hold agreement of terms per order
		$dict = NewDataDictionary($db);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_cartms_orders', 'termsagreed L(1)');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_cartms_orders', 'delivery_date D');
		$dict->ExecuteSQLArray($sqlarray);
		$query = 'UPDATE ' . cms_db_prefix() . 'module_cartms_orders SET termsagreed = 1, delivery_date = create_date';
		$db->Execute($query);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_cartms_shippingprovider', 'agreetoterms L(1)');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_cartms_shippingprovider', 'shipworkdays I');
		$dict->ExecuteSQLArray($sqlarray);

		$current_version = '0.2.0';
	case '0.2.0':
		// No change to database

		$current_version = '0.2.1';
	case '0.2.1':

		$current_version = '0.2.2';
	case '0.2.2':

		$current_version = '0.2.3';
	case '0.2.3':
		$this->RemovePreference('showinmenu');
		// Improved usage of FEU username makes it necessary to temporary save username
		$dict = NewDataDictionary($db);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_cartms_newuserpasswords', 'username C(80)');
		$dict->ExecuteSQLArray($sqlarray);
		// Allow customer to add remark per order
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_cartms_orders', 'remark X');
		$dict->ExecuteSQLArray($sqlarray);
		// Add price per product weight
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_cartms_shippingprovider', 'shippriceperweight F');
		$dict->ExecuteSQLArray($sqlarray);

		$current_version = '0.2.4';
	case '0.2.4':

		$current_version = '0.2.5';
	case '0.2.5':
		// By special request of some users of the module, a bill to name is added as property.
		global $gCms;
		$groupname = 'SimpleCart';
		$qry = 'SELECT * FROM ' . cms_db_prefix() . 'module_feusers_groups WHERE groupname = ?';
		$dbresult = $db->Execute($qry, array($groupname));
		if ($dbresult && $dbresult->RecordCount()) {
			$row = $dbresult->FetchRow();
			$groupid = $row['id'];
		}
		// There are at least two FEU versions that have a different number of fields for property definition
		// For a MySQL database this is checkable. Not done however for other dbs
		$qry = 'DESCRIBE ' . cms_db_prefix() . 'module_feusers_propdefn';
		$dbresult = $db->Execute($qry);
		if ($dbresult->RecordCount() == 5) {
			$qry = 'INSERT INTO ' . cms_db_prefix() . 'module_feusers_propdefn VALUES (?,?,?,?,?)';
			$p = array('billfirstname', 'Firstname', 0, 20, 20);
			$dbresult = $db->Execute($qry, $p);
			$p = array('billsurname', 'Surname', 0, 30, 30);
			$dbresult = $db->Execute($qry, $p);
		} else {
			$qry = 'INSERT INTO ' . cms_db_prefix() . 'module_feusers_propdefn VALUES (?,?,?,?,?,?)';
			$p = array('billfirstname', 'Firstname', 0, 20, 20, NULL);
			$dbresult = $db->Execute($qry, $p);
			$p = array('billsurname', 'Surname', 0, 30, 30, NULL);
			$dbresult = $db->Execute($qry, $p);
		}
		// Add the new properties, which gets 9 an onwards as sorting order (since this is same as with clean install)
		// Make sure that the properties are connected to the group
		$qry = "INSERT INTO " . cms_db_prefix() . "module_feusers_grouppropmap VALUES(?,?,?,?,?)";
		$p = array('billfirstname', $groupid, 9, 1, -1);
		$dbresult = $db->Execute($qry, $p);
		$p = array('billsurname', $groupid, 11, 1, -1);
		$dbresult = $db->Execute($qry, $p);

		$fn = cms_join_path(dirname(__FILE__), 'templates', 'fe_speedcheckout_info.tpl');
		if (file_exists($fn)) {
			$template = @file_get_contents($fn);
			$this->SetTemplate('cart_fe_speedcheckout_info', $template);
		}

		$current_version = '0.2.6';
	case '0.2.6':

		$current_version = '0.2.7';
	case '0.2.7':

		$current_version = '0.2.8';
	case '0.2.8':
		// Add category id in cart and order lines, so name can be shown of category at line level
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_cartms_carts', 'category_id I');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_cartms_order_lines', 'category_id I');
		$dict->ExecuteSQLArray($sqlarray);
		// Include attributes to be in the cart/order as well
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_cartms_carts', 'attribute_id I');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_cartms_order_lines', 'attribute_id I');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_cartms_orders', 'invoiceno C(20)');
		$dict->ExecuteSQLArray($sqlarray);
		// Introducte new template for email order check out
		$fn = cms_join_path(dirname(__FILE__), 'templates', 'fe_emailcheckout_info.tpl');
		if (file_exists($fn)) {
			$template = @file_get_contents($fn);
			$this->SetTemplate('cart_fe_emailcheckout_info', $template);
		}
		// Build the path we will be uploading the external documents to
		$config = $gCms->GetConfig();
		// Prepare a directory that will contain the xml properties for charts
		$path = cms_join_path($this->config['uploads_path'], $this->getName());
		// Make sure the directory can be found. Create it, error handling will cover any problems found
		if (!file_exists($path)) mkdir($path, 0777);
		$this->SetPreference('extdoc_invoice', 'invoice_sample');

		$current_version = '0.3.0';
	case '0.3.0':

		$current_version = '0.3.1';
	case '0.3.1':

		$current_version = '0.3.2';
	case '0.3.2':
		$fn = cms_join_path(dirname(__FILE__), 'templates', 'orig_cust_invmail_template.tpl');
		if (file_exists($fn)) {
			$template = @file_get_contents($fn);
			$this->SetTemplate('cust_invmail_template', $template);
		}

		$current_version = '0.3.3';
	case '0.3.3':

		$current_version = '0.3.4';
	case '0.3.4':
		$current_version = '0.3.5';
	case '0.3.5':
		$current_version = '0.3.6';
	case '0.3.6':
		$current_version = '0.3.7';
	case '0.3.7':
		$current_version = '0.3.8';
	case '0.3.8':
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_cartms_orders', 'totaldiscount F');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_cartms_orders', 'coupon_code C(12)');
		$dict->ExecuteSQLArray($sqlarray);
		$current_version = '0.3.9';
	case '0.3.9':
		$current_version = '0.4.0';
	case '0.4.0':
		$current_version = '0.4.1';
	case '0.4.1':
		$this->SetPreference('freeshippingboundary', 9999999);
		$current_version = '0.4.2';
	case '0.4.2':
		$current_version = '0.4.3';
	case '0.4.3':
		$current_version = '0.4.4';
}

// Log the upgrade in the admin audit trail
$this->Audit(0, $this->Lang('friendlyname'), $this->Lang('upgraded', $this->GetVersion()));
