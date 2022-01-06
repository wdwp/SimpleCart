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

// Prepare list of possible pages that handle the thank you page/trade terms
$contentlist = array();
$contentlist[$this->Lang('use_defaultcontent')] = '';
$query = 'SELECT * FROM ' . cms_db_prefix() . 'content ORDER BY menu_text';
$dbresult = $db->Execute($query);

while ($dbresult && $row = $dbresult->FetchRow()) {
	$contentlist[$row['menu_text']] = $row['menu_text'];
}

// Prepare some fields that handle the formatting of an amount
$exampleamount = -12345678 / 1000;
$formatdecimals = $this->GetPreference('numberformatdecimals', '2');
$formatdecimal_point = $this->GetPreference('numberformatdec_point', ',');
$formatthousand_sep = $this->GetPreference('numberformatthousand_sep', '.');
$formattedamount = number_format($exampleamount, $formatdecimals, $formatdecimal_point, $formatthousand_sep);

$smarty->assign('startform', $this->CreateFormStart($id, 'save_admin_options', $returnid));
// Prepare the fields to do with the formatting of an amount
$smarty->assign('title_fieldset_amount', $this->Lang('title_fieldset_amount'));
$smarty->assign('title_default_cartcurrency', $this->Lang('title_default_cartcurrency'));
$smarty->assign('input_default_cartcurrency', $this->CreateInputText($id, 'cartcurrency', $this->GetPreference('cartcurrency', 'Eur'), '10', '10'));
$smarty->assign('title_numberformatdecimals', $this->Lang('title_numberformatdecimals'));
$smarty->assign('input_numberformatdecimals', $this->CreateInputText(
	$id,
	'numberformatdecimals',
	$this->GetPreference('numberformatdecimals', '2'),
	'1',
	'1'
));
$smarty->assign('title_numberformatdec_point', $this->Lang('title_numberformatdec_point'));
$smarty->assign('input_numberformatdec_point', $this->CreateInputText(
	$id,
	'numberformatdec_point',
	$this->GetPreference('numberformatdec_point', ','),
	'1',
	'1'
));
$smarty->assign('title_numberformatthousand_sep', $this->Lang('title_numberformatthousand_sep'));
$smarty->assign('input_numberformatthousand_sep', $this->CreateInputText(
	$id,
	'numberformatthousand_sep',
	$this->GetPreference('numberformatthousand_sep', '.'),
	'1',
	'1'
));
$smarty->assign('output_example', $this->Lang(
	'exampleamountformatted',
	$exampleamount,
	$formattedamount,
	$this->GetPreference('cartcurrency', 'Eur')
));
$smarty->assign('output_parmexplanation', $this->Lang('exampleparmusage'));

$smarty->assign('title_fieldset_mandatory', $this->Lang('title_fieldset_mandatory'));
// Prepare state is mandatory to fill or not
$smarty->assign('title_mandatorystate', $this->Lang('title_mandatorystate'));
$smarty->assign('input_mandatorystate', $this->CreateInputCheckbox($id, 'mandatorystate', true, $this->GetPreference('mandatorystate', false)));
// Prepare telephone is mandatory to fill or not
$smarty->assign('title_mandatorytelephone', $this->Lang('title_mandatorytelephone'));
$smarty->assign('input_mandatorytelephone', $this->CreateInputCheckbox($id, 'mandatorytelephone', true, $this->GetPreference('mandatorytelephone', false)));
// Prepare parameters belonging to administration cost per order
$smarty->assign('title_fieldset_admincost', $this->Lang('title_fieldset_admincost'));
$smarty->assign('title_admincostadd', $this->Lang('title_admincostadd'));
$smarty->assign('input_admincostadd', $this->CreateInputCheckbox($id, 'admincostadd', true, $this->GetPreference('admincostadd', false)));
$smarty->assign('title_admincost', $this->Lang('title_admincost'));
$smarty->assign('input_admincost', $this->CreateInputText($id, 'admincost', $this->GetPreference('admincost', 0), '10', '10'));
$smarty->assign('title_admincostminamount', $this->Lang('title_admincostminamount'));
$smarty->assign('input_admincostminamount', $this->CreateInputText($id, 'admincostminamount', $this->GetPreference('admincostminamount', 100), '10', '10'));
// Prepare parameters belonging to free shipping cost per order
$smarty->assign('title_fieldset_freeshipping', $this->Lang('title_fieldset_freeshipping'));
$smarty->assign('title_freeshippingboundary', $this->Lang('title_freeshippingboundary'));
$smarty->assign('input_freeshippingboundary', $this->CreateInputText($id, 'freeshippingboundary', $this->GetPreference('freeshippingboundary', 9999999), '10', '10'));
// Make it possible to select the correct content block to be selected
$smarty->assign('title_contentthankyou', $this->Lang('title_contentthankyou'));
$smarty->assign('input_contentthankyou', $this->CreateInputDropdown(
	$id,
	'contentthankyou',
	$contentlist,
	-1,
	$this->GetPreference('contentthankyou', '')
));
$smarty->assign('title_thankyouexplanation', $this->Lang('title_thankyouexplanation'));
// Make it possible to select the correct content block to be selected: Trading terms
$smarty->assign('title_contenttradeterms', $this->Lang('title_contenttradeterms'));
$smarty->assign('input_contenttradeterms', $this->CreateInputDropdown(
	$id,
	'contenttradeterms',
	$contentlist,
	-1,
	$this->GetPreference('contenttradeterms', '')
));
$smarty->assign('title_tradetermsexplanation', $this->Lang('title_tradetermsexplanation'));
$smarty->assign('title_contenttradetext', $this->Lang('title_contenttradetext'));
$smarty->assign('input_contenttradetext', $this->CreateInputText($id, 'contenttradetext', $this->GetPreference(
	'contenttradetext',
	$this->Lang('erroragreetotermsblank')
), '60', '60'));
// Allow a lot of VAT names and their percentages
$smarty->assign('title_fieldset_vat', $this->Lang('title_fieldset_vat'));
$smarty->assign('title_vatperc', $this->Lang('title_vatpercentage'));
$smarty->assign('title_vatcode0', $this->Lang('title_vatcode0'));
$smarty->assign('input_vat0name', $this->CreateInputText($id, 'vat0name', $this->GetPreference('vat0name', ''), '20', '20'));
$smarty->assign('input_vat0perc', $this->CreateInputText($id, 'vat0perc', $this->GetPreference('vat0perc', 0), '10', '10'));
$smarty->assign('title_vatcode1', $this->Lang('title_vatcode1'));
$smarty->assign('input_vat1name', $this->CreateInputText($id, 'vat1name', $this->GetPreference('vat1name', ''), '20', '20'));
$smarty->assign('input_vat1perc', $this->CreateInputText($id, 'vat1perc', $this->GetPreference('vat1perc', 0), '10', '10'));
$smarty->assign('title_vatcode2', $this->Lang('title_vatcode2'));
$smarty->assign('input_vat2name', $this->CreateInputText($id, 'vat2name', $this->GetPreference('vat2name', ''), '20', '20'));
$smarty->assign('input_vat2perc', $this->CreateInputText($id, 'vat2perc', $this->GetPreference('vat2perc', 0), '10', '10'));
$smarty->assign('title_vatcode3', $this->Lang('title_vatcode3'));
$smarty->assign('input_vat3name', $this->CreateInputText($id, 'vat3name', $this->GetPreference('vat3name', ''), '20', '20'));
$smarty->assign('input_vat3perc', $this->CreateInputText($id, 'vat3perc', $this->GetPreference('vat3perc', 0), '10', '10'));
$smarty->assign('title_vatcode4', $this->Lang('title_vatcode4'));
$smarty->assign('input_vat4name', $this->CreateInputText($id, 'vat4name', $this->GetPreference('vat4name', ''), '20', '20'));
$smarty->assign('input_vat4perc', $this->CreateInputText($id, 'vat4perc', $this->GetPreference('vat4perc', 0), '10', '10'));
// Prepare order handling type
$orderhandlingtypes = array(
	$this->Lang('orderhandlingtypenormal') => 'normal',
	$this->Lang('orderhandlingtypespeed') => 'speed',
	$this->Lang('orderhandlingtypemail') => 'email'
);
$smarty->assign('title_orderhandlingtype', $this->Lang('title_orderhandlingtype'));
$smarty->assign('input_orderhandlingtype', $this->CreateInputRadioGroup(
	$id,
	'orderhandlingtype',
	$orderhandlingtypes,
	$this->GetPreference('orderhandlingtype', 'normal'),
	'',
	'&nbsp;'
));
// Last used order number
$smarty->assign('title_lastusedordernumber', $this->Lang('title_lastusedordernumber'));
$lastusedordernumber = $this->orders->GetLastUsedOrderNumber();
$smarty->assign('input_lastusedordernumber', $this->CreateInputText($id, 'lastusedordernumber', $lastusedordernumber, '10', '10'));
// Handle the invoice related material
$smarty->assign('title_fieldset_invoice', $this->Lang('title_fieldset_invoice'));
$smarty->assign('title_invoice_prefix', $this->Lang('title_invoice_prefix'));
$smarty->assign('input_invoice_prefix', $this->CreateInputText($id, 'invoice_prefix', $this->GetPreference('invoice_prefix', 'I'), '5', '5'));
$smarty->assign('title_lastusedinvoicenumber', $this->Lang('title_lastusedinvoicenumber'));
$smarty->assign('input_lastusedinvoicenumber', $this->CreateInputText($id, 'lastusedinvoicenumber', $this->GetPreference('invoiceno'), '10', '10'));
$smarty->assign('title_extdoc_invoice', $this->Lang('title_extdoc_invoice'));
$smarty->assign('input_extdoc_invoice', $this->CreateInputText($id, 'extdoc_invoice', $this->GetPreference('extdoc_invoice', 'invoice_sample'), '20', '20'));

$smarty->assign('submit', $this->CreateInputSubmit($id, 'optionssubmitbutton', $this->Lang('submit')));
$smarty->assign('endform', $this->CreateFormEnd());

// Display the Admin options
echo $this->ProcessTemplate('adminoptions.tpl');
