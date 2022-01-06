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

/**
 * Create SimpleCart user type and group in FrontEndUsers
 */
$properties = array(
	array('name' => 'firstname', 'prompt' => 'Firstname', 'type' => 0, 'length' => 20, 'maxlength' => 20),
	array('name' => 'surname', 'prompt' => 'Surname', 'type' => 0, 'length' => 30, 'maxlength' => 30),
	array('name' => 'email', 'prompt' => 'Email Address', 'type' => 2, 'length' => 50, 'maxlength' => 255),
	array('name' => 'addressstreet', 'prompt' => 'Street', 'type' => 0, 'length' => 50, 'maxlength' => 200),
	array('name' => 'addresscity', 'prompt' => 'City', 'type' => 0, 'length' => 50, 'maxlength' => 100),
	array('name' => 'addressstate', 'prompt' => 'State/Province', 'type' => 0, 'length' => 20, 'maxlength' => 20),
	array('name' => 'addresszip', 'prompt' => 'Zip/Postal code', 'type' => 0, 'length' => 10, 'maxlength' => 10),
	// The country dropdown values are to be filled by site owner in group properties.
	// The countries define where the shop wants to ship to
	array('name' => 'addresscountry', 'prompt' => 'Country', 'type' => 4, 'length' => 0, 'maxlength' => 0),
	array('name' => 'telephone', 'prompt' => 'Telephone', 'type' => 0, 'length' => 35, 'maxlength' => 35),
	array('name' => 'billfirstname', 'prompt' => 'Firstname', 'type' => 0, 'length' => 20, 'maxlength' => 20),
	array('name' => 'billsurname', 'prompt' => 'Surname', 'type' => 0, 'length' => 30, 'maxlength' => 30),
	array('name' => 'billaddressstreet', 'prompt' => 'Street', 'type' => 0, 'length' => 50, 'maxlength' => 200),
	array('name' => 'billaddresscity', 'prompt' => 'City', 'type' => 0, 'length' => 50, 'maxlength' => 100),
	array('name' => 'billaddressstate', 'prompt' => 'State/Province', 'type' => 0, 'length' => 20, 'maxlength' => 20),
	array('name' => 'billaddresszip', 'prompt' => 'Zip/Postal code', 'type' => 0, 'length' => 10, 'maxlength' => 10),
	array('name' => 'billaddresscountry', 'prompt' => 'Country', 'type' => 4, 'length' => 0, 'maxlength' => 0)
);

$modops = cmsms()->GetModuleOperations();
$feu = $modops->get_module_instance('FrontEndUsers');
// Check if the group already exists
if (!$feu->GetGroupID('SimpleCart')) {
	$result = $feu->AddGroup('SimpleCart', 'SimpleCart Users');

	if ($result[0]) {
		$counter = 0;
		foreach ($properties as $property) {
			$feu->AddPropertyDefn(
				$property['name'],
				$property['prompt'],
				$property['type'],
				$property['length'],
				$property['maxlength']
			);
			$feu->AddGroupPropertyRelation($result[1], $property['name'], $counter, 0, 1);
			$counter++;
		}
	}
} else {
	/* TODO: Check that it has all the properties we require */
}

/**
 * Create all tables
 */
$db = cmsms()->GetDb();
$dict = NewDataDictionary($db);

// Table schema description for table: cart
$flds = "
	cart_id I KEY,
	session_id C(50),
	category_id I,
	product_id I,
	attribute_id I,
	qty I,
	create_date " . CMS_ADODB_DT . "
	";
// Create it. This should do error checking.
$sqlarray = $dict->CreateTableSQL(cms_db_prefix() . 'module_cartms_carts', $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);
// Create a sequence
$db->CreateSequence(cms_db_prefix() . 'module_cartms_carts_seq');

// Table schema description for table: Order
$flds = "
	order_id I KEY,
	totalproduct F,
	totaldiscount F,
	totalshipping F,
	totaladmincost F,
	shipmode C(3),
	comment C(255),
	status C(3),
	customer_id I,
	currency C(3),
	paymethod C(10),
	totalnetweight F,
	totalvat0amount F,
	totalvat1amount F,
	totalvat2amount F,
	totalvat3amount F,
	totalvat4amount F,
	termsagreed L,
	remark X,
	coupon_code C(12),
	invoiceno C(20),
	delivery_date D,
	create_date " . CMS_ADODB_DT . ",
	modified_date " . CMS_ADODB_DT . "
	";

// Create it. This should do error checking.
$sqlarray = $dict->CreateTableSQL(cms_db_prefix() . 'module_cartms_orders', $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);
// Create a sequence
$db->CreateSequence(cms_db_prefix() . 'module_cartms_orders_seq');

// Table schema description for table: Order lines
$flds = "
	orderline_id I KEY,
	order_id I,
	category_id I,
	product_id I,
	attribute_id I,
	description C(255),
	qty I,
	price F,
	lineamount F,
	comment C(255),
	status C(3),
	vatcode C(1),
	create_date " . CMS_ADODB_DT . ",
	modified_date " . CMS_ADODB_DT . "
	";

// Create it. This should do error checking.
$sqlarray = $dict->CreateTableSQL(cms_db_prefix() . 'module_cartms_order_lines', $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);
// Create a sequence
$db->CreateSequence(cms_db_prefix() . 'module_cartms_order_lines_seq');

// Table schema description for table: shippingprovider
$flds = "
	shipprov_id I KEY,
	shipprovcode C(3),
	shipprovdesc C(255),
	shipprovprice F,
	agreetoterms L,
	shipworkdays I,
	shippriceperweight F,
	active L
	";

// Create it. This should do error checking.
$sqlarray = $dict->CreateTableSQL(cms_db_prefix() . 'module_cartms_shippingprovider', $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);
// Create a sequence
$db->CreateSequence(cms_db_prefix() . 'module_cartms_shippingprovider_seq');

// Table schema description for table: newuserpasswords
$flds = "
	user_id I KEY,
	password C(50),
	username C(80)
	";

// Create it.
$sqlarray = $dict->CreateTableSQL(cms_db_prefix() . 'module_cartms_newuserpasswords', $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

/**
 * Create all preferences
 */
$this->SetPreference('admincost', 0);
$this->SetPreference('admincostadd', false);
$this->SetPreference('admincostminamount', 100);
$this->SetPreference('cartcurrency', 'EUR');
$this->SetPreference('mandatorystate', false);
$this->SetPreference('mandatorytelephone', false);
$this->SetPreference('numberformatdecimals', '2');
$this->SetPreference('numberformatdec_point', ',');
$this->SetPreference('numberformatthousands_sep', '.');
$this->SetPreference('storeshipaddress', true);
$this->SetPreference('contentthankyou', '');
$this->SetPreference('contenttradeterms', '');
$this->SetPreference('desclength', 40);
// VAT rates in Europe see:
// http://ec.europa.eu/taxation_customs/resources/documents/taxation/vat/how_vat_works/rates/vat_rates_en.pdf
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
$this->SetPreference('sendconfirmationmail', false);
$this->SetPreference('custmail_subject', $this->Lang('yourorder'));
$this->SetPreference('admin_emailaddress', 'me@_myshop_.com');
$this->SetPreference('admin_subject', $this->Lang('neworderplaced'));
$this->SetPreference('extdoc_invoice', 'invoice_sample');
$this->SetPreference('freeshippingboundary', 9999999);
/**
 * Create various example templates
 */
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
$fn = cms_join_path(dirname(__FILE__), 'templates', 'orig_cust_invmail_template.tpl');
if (file_exists($fn)) {
	$template = @file_get_contents($fn);
	$this->SetTemplate('cust_invmail_template', $template);
}

# Setup cart template
$fn = cms_join_path(dirname(__FILE__), 'templates', 'cart_template.tpl');
if (file_exists($fn)) {
	$template = @file_get_contents($fn);
	$this->SetTemplate('cart_template', $template);
}

# Setup tinybox template
$fn = cms_join_path(dirname(__FILE__), 'templates', 'tinybox.tpl');
if (file_exists($fn)) {
	$template = @file_get_contents($fn);
	$this->SetTemplate('cart_tinybox', $template);
}

# Setup normalbox template
$fn = cms_join_path(dirname(__FILE__), 'templates', 'normalbox.tpl');
if (file_exists($fn)) {
	$template = @file_get_contents($fn);
	$this->SetTemplate('cart_normalbox', $template);
}

# Setup front end templates
$fn = cms_join_path(dirname(__FILE__), 'templates', 'fe_delivery_info.tpl');
if (file_exists($fn)) {
	$template = @file_get_contents($fn);
	$this->SetTemplate('cart_fe_delivery_info', $template);
}
$fn = cms_join_path(dirname(__FILE__), 'templates', 'fe_emailcheckout_info.tpl');
if (file_exists($fn)) {
	$template = @file_get_contents($fn);
	$this->SetTemplate('cart_fe_emailcheckout_info', $template);
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
$fn = cms_join_path(dirname(__FILE__), 'templates', 'fe_speedcheckout_info.tpl');
if (file_exists($fn)) {
	$template = @file_get_contents($fn);
	$this->SetTemplate('cart_fe_speedcheckout_info', $template);
}
$fn = cms_join_path(dirname(__FILE__), 'templates', 'fe_showcart.tpl');
if (file_exists($fn)) {
	$template = @file_get_contents($fn);
	$this->SetTemplate('cart_fe_showcart', $template);
	$this->SetPreference('default_cart_template', 'cart_fe_showcart');
}
$fn = cms_join_path(dirname(__FILE__), 'templates', 'fe_thankyou_info.tpl');
if (file_exists($fn)) {
	$template = @file_get_contents($fn);
	$this->SetTemplate('cart_fe_thankyou_info', $template);
}

# Setup userauth template
$fn = cms_join_path(dirname(__FILE__), 'templates', 'userauth_template.tpl');
if (file_exists($fn)) {
	$template = @file_get_contents($fn);
	$this->SetTemplate('userauth_template', $template);
}

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

// Build the path we will be uploading the external documents to
$config = $gCms->GetConfig();
// Prepare a directory that will contain the xml properties for charts
$path = cms_join_path($this->config['uploads_path'], $this->getName());
// Make sure the directory can be found. Create it, error handling will cover any problems found
if (!file_exists($path)) mkdir($path, 0777);

/**
 * Create security permissions
 */
$this->CreatePermission('Use SimpleCart', 'Use Simple Cart');
$this->CreatePermission('Modify SimpleCart', 'Modify Simple Cart');

$this->RegisterModulePlugin(TRUE);
$this->RegisterSmartyPlugin('simplecart', 'function', 'function_plugin');

$this->Audit(0, $this->Lang('friendlyname'), $this->Lang('installed', $this->GetVersion()));
