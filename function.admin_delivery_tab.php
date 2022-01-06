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

$themeObject = \cms_utils::get_theme_object();

$desclength = $this->GetPreference('desclength', 40);

// Select all the shipping providers
$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_cartms_shippingprovider ORDER BY shipprovcode';
$dbresult = $db->Execute($query);

$rowclass = 'row1';
$entryarray = array();

while ($dbresult && $row = $dbresult->FetchRow()) {
	$onerow = new stdClass();

	$onerow->shipprov_id = $row['shipprov_id'];
	$onerow->shipprovcode = $this->CreateLink($id, 'editshipprov', $returnid, $row['shipprovcode'], array('shipprov_id' => $row['shipprov_id']));
	// if (strlen($row['shipprovdesc']) > $desclength) {
	// 	$row['shipprovdesc'] = substr($row['shipprovdesc'], 0, $desclength) . '...';
	// } else {
	// 	$row['shipprovdesc'] = $row['shipprovdesc'];
	// }
	$onerow->shipprovdesc = $this->CreateLink($id, 'editshipprov', $returnid, $row['shipprovdesc'], array('shipprov_id' => $row['shipprov_id']));
	$onerow->shipprovprice = number_format($row['shipprovprice'], 2, ".", ",");
	if ($row['active'] == 1) {
		$onerow->status = $this->CreateLink(
			$id,
			'switchstatus',
			$returnid,
			$themeObject->DisplayImage('icons/system/true.gif', $this->Lang('status_active'), '', '', 'systemicon'),
			array(
				'oldstatus' => 'status_active',
				'shipprov_id' => $row['shipprov_id'],
				'active_tab' => 'delivery'
			)
		);
	} else {
		$onerow->status = $this->CreateLink(
			$id,
			'switchstatus',
			$returnid,
			$themeObject->DisplayImage('icons/system/false.gif', $this->Lang('status_inactive'), '', '', 'systemicon'),
			array(
				'oldstatus' => 'status_inactive',
				'shipprov_id' => $row['shipprov_id'],
				'active_tab' => 'delivery'
			)
		);
	}
	$onerow->editlink = $this->CreateLink($id, 'editshipprov', $returnid, $themeObject->DisplayImage('icons/system/edit.gif', $this->Lang('edit'), '', '', 'systemicon'), array('shipprov_id' => $row['shipprov_id']));
	$onerow->deletelink = $this->CreateLink($id, 'deleterow', $returnid, $themeObject->DisplayImage('icons/system/delete.gif', $this->Lang('delete'), '', '', 'systemicon'), array('table' => 'ShipProv', 'shipprov_id' => $row['shipprov_id']), $this->Lang('areyousureshipprov'));

	$onerow->rowclass = $rowclass;
	$entryarray[] = $onerow;

	($rowclass == "row1" ? $rowclass = "row2" : $rowclass = "row1");
}
$this->smarty->assign_by_ref('items', $entryarray);
$this->smarty->assign('itemcount', count($entryarray));

$this->smarty->assign('shipcodetext', $this->Lang('title_shipcode'));
$this->smarty->assign('descriptiontext', $this->Lang('title_description'));
$this->smarty->assign('shipprovpricetext', $this->Lang('shipprovprice'));

// Setup links
$this->smarty->assign('addshipprovlink', $this->CreateLink($id, 'addshipprov', $returnid, $this->Lang('addshipprov'), array(), '', false, false, 'class="pageoptions"'));
$this->smarty->assign('addshipprovlink', $this->CreateLink($id, 'addshipprov', $returnid, $themeObject->DisplayImage('icons/system/newobject.gif', $this->Lang('addshipprov'), '', '', 'systemicon'), array(), '', false, false, '') . ' ' . $this->CreateLink($id, 'addshipprov', $returnid, $this->Lang('addshipprov'), array(), '', false, false, 'class="pageoptions"'));

#Display template
echo $this->ProcessTemplate('listshippingproviders.tpl');
