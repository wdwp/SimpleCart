<?php
# Module: Cart Made Simple - An Order Intake module for CMS - CMS Made Simple
# Copyright (c) 2008 by Duketown
#
# This function allows the administrator to update the preferences
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

if (!$this->CheckPermission('Modify SimpleCart')) {
	return;
}

// Save the entered values and return
$this->SetPreference('cartcurrency', trim($params['cartcurrency']));
$this->SetPreference('numberformatdecimals', (int) trim($params['numberformatdecimals']));
$this->SetPreference('numberformatdec_point', trim($params['numberformatdec_point']));
$this->SetPreference('numberformatthousand_sep', trim($params['numberformatthousand_sep']));
$this->SetPreference('mandatorystate', 0);
if (isset($params['mandatorystate'])) {
	$this->SetPreference('mandatorystate', 1);
}
$this->SetPreference('mandatorytelephone', 0);
if (isset($params['mandatorytelephone'])) {
	$this->SetPreference('mandatorytelephone', 1);
}
$this->SetPreference('admincostadd', 0);
if (isset($params['admincostadd'])) {
	$this->SetPreference('admincostadd', 1);
}
$this->SetPreference('admincost', trim($params['admincost']));
$this->SetPreference('admincostminamount', trim($params['admincostminamount']));
$this->SetPreference('freeshippingboundary', trim($params['freeshippingboundary']));
$this->SetPreference('contentthankyou', $params['contentthankyou']);
$this->SetPreference('contenttradeterms', $params['contenttradeterms']);
$this->SetPreference('contenttradetext', $params['contenttradetext']);
$this->SetPreference('orderhandlingtype', $params['orderhandlingtype']);
$this->SetPreference('extdoc_invoice', trim($params['extdoc_invoice']));
$this->SetPreference('invoice_prefix', trim($params['invoice_prefix']));
$this->SetPreference('invoiceno', trim($params['lastusedinvoicenumber']));
$extdoc_invoice = 'invoice_sample';
if ($params['extdoc_invoice'] != '') {
	$extdoc_invoice = trim($params['extdoc_invoice']);
}
$this->SetPreference('extdoc_invoice', $extdoc_invoice);

// Prepare fields to hold VAT rates
$this->SetPreference('vat0name', trim($params['vat0name']));
$this->SetPreference('vat1name', trim($params['vat1name']));
$this->SetPreference('vat2name', trim($params['vat2name']));
$this->SetPreference('vat3name', trim($params['vat3name']));
$this->SetPreference('vat4name', trim($params['vat4name']));
$this->SetPreference('vat0perc', trim(str_replace('%', '', $params['vat0perc'])));
$this->SetPreference('vat1perc', trim(str_replace('%', '', $params['vat1perc'])));
$this->SetPreference('vat2perc', trim(str_replace('%', '', $params['vat2perc'])));
$this->SetPreference('vat3perc', trim(str_replace('%', '', $params['vat3perc'])));
$this->SetPreference('vat4perc', trim(str_replace('%', '', $params['vat4perc'])));
$sql = 'UPDATE ' . cms_db_prefix() . 'module_cartms_orders_seq SET id = ?';
$dbresult = $db->Execute($sql, array($params['lastusedordernumber']));

$params = array('tab_message' => $this->Lang('optionsupdated'), 'active_tab' => 'cart');
$this->Redirect($id, 'defaultadmin', '', $params);
