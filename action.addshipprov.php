<?php
# Module: Cart Made Simple - An Order Intake module for CMS - CMS Made Simple
# Copyright (c) 2008 by Duketown
#
# This function will handle adding a delivery method
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

if (!$this->CheckPermission('Use SimpleCart')) {
	echo $this->ShowErrors($this->Lang('accessdenied', array('Use SimpleCart')));
	return;
}

$shipprovdesc = '';
if (!empty($params['shipprovdesc'])) {
	$shipprovdesc = trim($params['shipprovdesc']);
}

$shipprovprice = 0;
if (!empty($params['shipprovprice'])) {
	$shipprovprice = trim($params['shipprovprice']);
}

$shippriceperweight = 0;
if (!empty($params['shippriceperweight'])) {
	$shippriceperweight = trim($params['shippriceperweight']);
}

$agreetoterms = 0;
if (!empty($params['agreetoterms'])) {
	$agreetoterms = $params['agreetoterms'];
}

$shipworkdays = 0;
if (!empty($params['shipworkdays'])) {
	$shipworkdays = trim($params['shipworkdays']);
}

$status = 1;
if (isset($params['status'])) {
	$status = $params['status'];
}

if (isset($params['cancel'])) {
	$params = array('active_tab' => 'delivery');
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}

$shipprovcode = '';
if (isset($params['shipprovcode'])) {
	$shipprovcode = $params['shipprovcode'];
	if ($shipprovcode != '' && $shipprovdesc != '') {
		$shipprov_id = $db->GenID(cms_db_prefix() . 'module_cartms_shippingprovider_seq');
		$shipprovcode = strtoupper($shipprovcode);
		$query = 'INSERT INTO ' . cms_db_prefix() . 'module_cartms_shippingprovider (shipprov_id, shipprovcode, shipprovdesc, shipprovprice,
			agreetoterms, shipworkdays, shippriceperweight, active) VALUES (?,?,?,?,?,?,?,?)';
		// echo $query . '<br>';
		// echo $shipprov_id . '<br>';
		// echo $shipprovcode . '<br>';
		// echo $shipprovdesc . '<br>';
		// echo $shipprovprice . '<br>';
		// echo $agreetoterms . '<br>';
		// echo $shipworkdays . '<br>';
		// echo $shippriceperweight . '<br>';
		// echo $status;
		// exit;

		$db->Execute($query, array(
			$shipprov_id, $shipprovcode, $shipprovdesc, $shipprovprice, $agreetoterms, $shipworkdays,
			$shippriceperweight, $status
		));

		$params = array('tab_message' => $this->Lang('shipcodeadded'), 'active_tab' => 'delivery');
		$this->Redirect($id, 'defaultadmin', $returnid, $params);
	} else {
		if ($shipprovcode == '') echo $this->ShowErrors($this->Lang('noshipcodegiven'));
		if ($shipprovdesc == '') echo $this->ShowErrors($this->Lang('noshipdescgiven'));
	}
}

$statusdropdown = array();
$statusdropdown[$this->Lang('status_active')] = 1;
$statusdropdown[$this->Lang('status_inactive')] = 0;

#Display template
$this->smarty->assign('startform', $this->CreateFormStart($id, 'addshipprov', $returnid));
$this->smarty->assign('endform', $this->CreateFormEnd());
$this->smarty->assign('shipprovcodetext', '*' . $this->Lang('title_shipcode'));
$this->smarty->assign('inputshipprovcode', $this->CreateInputText($id, 'shipprovcode', $shipprovcode, 3, 3, 'class="defaultfocus"'));
$this->smarty->assign('shipprovdesctext', $this->Lang('title_description'));
$this->smarty->assign('inputshipprovdesc', $this->CreateInputText($id, 'shipprovdesc', '', 80, 255));
$this->smarty->assign('shipprovpricetext', $this->Lang('shipprovprice'));
$this->smarty->assign('inputshipprovprice', $this->CreateInputText($id, 'shipprovprice', $shipprovprice, 10, 10));
$this->smarty->assign('shippriceperweighttext', $this->Lang('shippriceperweight'));
$this->smarty->assign('inputshippriceperweight', $this->CreateInputText($id, 'shippriceperweight', $shippriceperweight, 10, 10));

$this->smarty->assign('shipworkdaystext', $this->Lang('title_shipworkdays'));
$this->smarty->assign('inputshipworkdays', $this->CreateInputText($id, 'shipworkdays', $shipworkdays, 10, 10));
$this->smarty->assign('agreetotermstext', $this->Lang('title_agreetoterms'));
$this->smarty->assign('inputagreetoterms', $this->CreateInputCheckbox($id, 'agreetoterms', false, $agreetoterms));
$this->smarty->assign('statustext', $this->Lang('status'));
$this->smarty->assign('inputstatus', $this->CreateInputDropdown($id, 'status', $statusdropdown, -1, $status));
$this->smarty->assign('hidden', '');
$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));
echo $this->ProcessTemplate('editshipprov.tpl');
