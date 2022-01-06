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

// Based upon the status, the orders will be shown. Default the confirmed orders are shown
if (isset($params['orderstatus'])) {
	$orderstatus = $params['orderstatus'];
} else {
	$orderstatus = 'CNF';
}
$withselected = '';
if (isset($params['submit'])) {
	$withselected = $params['withselected'];
}

$alloweddeletion = $this->CheckPermission('Modify SimpleCart');
$newstatus = '';
if (isset($params['submit']) && isset($withselected)) {
	$orderinfo = array();
	$orderinfo['oldstatus'] = $orderstatus;
	$orderinfo['orderstatus'] = $orderstatus;
	$orderinfo['newstatus'] = substr($withselected, 2, 3);
	switch ($withselected) {
		case 'MADel':
			if (!$alloweddeletion) {
				echo $this->ShowErrors($this->Lang('needpermission', array('Delete Order')));
			} else
			if (isset($params['sel']) && is_array($params['sel']) && count($params['sel']) > 0) {
				foreach ($params['sel'] as $order_id) {
					$this->orders->DeleteOrder($order_id);
				}
			}
			break;
		case 'MAINT':
		case 'MACNF':
		case 'MAPAY':
		case 'MASHP':
		case 'MAINV':
			if (isset($params['sel']) && is_array($params['sel']) && count($params['sel']) > 0) {
				foreach ($params['sel'] as $order_id) {
					$orderinfo['order_id'] = $order_id;
					$this->orders->SwitchStatus($orderinfo);
				}
			}
			break;
		default:
			break;
	}
}

// Show for each status the orders by order number, but on invoiced orders eldest at bottom
$orderby = ' ORDER BY order_id';
if ($orderstatus == 'INV') {
	$orderby = ' ORDER BY modified_date DESC';
}

$listorders = array();
$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_cartms_orders, ' . cms_db_prefix() . 'module_feusers_users
	WHERE customer_id = id and status = ?'
	. $orderby;
$dbresult = $db->Execute($query, array($orderstatus));

$rowclass = 'row1';
$entryarray = array();

while ($dbresult && $row = $dbresult->FetchRow()) {
	$onerow = new stdClass();

	$onerow->order_id = $this->CreateLink($id, 'ordershowdetail', $returnid, $row['order_id'], array('order_id' => $row['order_id'], 'orderstatus' => $orderstatus));
	$onerow->shipmode = $this->CreateLink($id, 'ordershowdetail', $returnid, $row['shipmode'], array('order_id' => $row['order_id'], 'orderstatus' => $orderstatus));
	$onerow->customername = $this->CreateLink($id, 'ordershowdetail', $returnid, $row['username'], array('order_id' => $row['order_id'], 'orderstatus' => $orderstatus));
	if ($orderstatus == 'INT') {
		$onerow->modificationdate = $row['create_date'];
	} else {
		$onerow->modificationdate = $row['modified_date'];
	}
	// Show the icons needed for editing, deleting
	$onerow->viewlink = $this->CreateLink(
		$id,
		'ordershowdetail',
		$returnid,
		$themeObject->DisplayImage('icons/system/view.gif', $this->Lang('ordershowdetail'), '', '', 'systemicon'),
		array('order_id' => $row['order_id'], 'orderstatus' => $orderstatus)
	);
	if ($orderstatus != 'INV') {
		$onerow->switchlink = $this->CreateLink($id, 'switchstatus', $returnid, $themeObject->DisplayImage(
			'icons/system/accept.gif',
			$this->Lang('statusorderfrom' . $orderstatus),
			'',
			'',
			'systemicon'
		), array('oldstatus' => $orderstatus, 'order_id' => $row['order_id'], 'active_tab' => 'order'));
	}
	$onerow->checked = $this->CreateInputCheckbox($id, 'sel[]', $row['order_id']);
	// Only allow deletion on entered and invoiced orders and of course if allowed
	if ($alloweddeletion) {
		if ($orderstatus == 'INT' || $orderstatus == 'INV') {
			$onerow->deletelink = $this->CreateLink(
				$id,
				'deleterow',
				$returnid,
				$themeObject->DisplayImage('icons/system/delete.gif', $this->Lang('delete'), '', '', 'systemicon'),
				array('table' => 'Orders', 'order_id' => $row['order_id'], 'orderstatus' => $orderstatus),
				$this->Lang('areyousureorder')
			);
		}
	}
	if ($orderstatus == 'INV') {
		// Check if the invoice exists
		$create_date = 'I' . substr($row['create_date'], 0, 4) . substr($row['create_date'], 5, 2)
			. substr($row['create_date'], 8, 2);
		$invoicepath = cms_join_path($this->config['uploads_path'], $this->getName(), $row['invoiceno'] . '.pdf');
		if (file_exists($invoicepath)) {
			// Invoice available, allow to mail it or preview it
			$onerow->extdocsend = $this->CreateLink(
				$id,
				'externaldocument',
				$returnid,
				'<img src="' . $gCms->config['root_url'] . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR .
					$this->GetName() . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'mail.gif"
				class="systemicon" alt="' . $this->Lang('sendexternaldocument', 'invoice') .
					'" title="' . $this->Lang('sendinvoice') . '" />',
				array('type' => 'invoice_send', 'order_id' => $row['order_id'], 'orderstatus' => $orderstatus)
			);
			$thefile = cms_join_path(
				$gCms->config['root_url'],
				'uploads',
				$this->GetName(),
				$row['invoiceno'] . '.pdf'
			);
			$theimage = cms_join_path($gCms->config['root_url'], 'modules', $this->GetName(), 'images', 'pdf.jpg');
			$onerow->extdocpreview = '<a href="' . $thefile . '" target="_blank" /><img src="' .
				$theimage . '" class="systemicon" alt="' . $this->Lang('previewinvoice') .
				'" title="' . $this->Lang('previewinvoice') . '" /></a>';
			$image = cms_join_path($gCms->config['root_url'], 'modules', $this->getName(), 'images', 'pdfprep.gif');
			$onerow->extdocprep = $this->CreateLink(
				$id,
				'externaldocument',
				$returnid,
				'<img src="' . $image . '" class="systemicon" alt="' . $this->Lang('prepareinvoice') .
					'" title="' . $this->Lang('prepareinvoice') . '" />',
				array(
					'type' => 'invoice_prep', 'order_id' => $row['order_id'],
					'create_date' => $create_date
				)
			);
		} else {
			// Invoice never generated, have the user generate it
			$onerow->extdocsend = '';
			$onerow->extdocpreview = '';
			$image = cms_join_path($gCms->config['root_url'], 'modules', $this->getName(), 'images', 'pdfprep.gif');
			$onerow->extdocprep = $this->CreateLink(
				$id,
				'externaldocument',
				$returnid,
				'<img src="' . $image . '" class="systemicon" alt="' . $this->Lang('prepareinvoice') .
					'" title="' . $this->Lang('prepareinvoice') . '" />',
				array(
					'type' => 'invoice_prep', 'order_id' => $row['order_id'],
					'create_date' => $create_date
				)
			);
		}
	}

	$onerow->rowclass = $rowclass;

	$entryarray[] = $onerow;

	($rowclass == "row1" ? $rowclass = "row2" : $rowclass = "row1");
}
$smarty->assign_by_ref('orders', $entryarray);

$smarty->assign('title_ordercount', $this->Lang('title_ordercountstat' . $orderstatus));
$smarty->assign('ordercount', count($entryarray));

// Prepare list of possible mass updates
$massactionlist = array();
$massactionlist = $this->GetMassActions($orderstatus);
$smarty->assign('title_withselected', $this->Lang('title_withselected'));
$smarty->assign('input_withselected', $this->CreateInputDropdown($id, 'withselected', $massactionlist, -1));

// Prepare the links to see the orders per status
$images = array();
$images = cartms_utils::FillOrderStatImages();
$iint = '<img src="' . $images['inti'] . '" title="' . $this->Lang("showenteredorders") . '" />';
$icnf = '<img src="' . $images['cnfi'] . '" title="' . $this->Lang("showconfirmedorders") . '" />';
$ipay = '<img src="' . $images['payi'] . '" title="' . $this->Lang("showpaidorders") . '" />';
$ishp = '<img src="' . $images['shpi'] . '" title="' . $this->Lang("showshippedorders") . '" />';
$iinv = '<img src="' . $images['invi'] . '" title="' . $this->Lang("showinvoicedorders") . '" />';
switch ($orderstatus) {
	case 'INT':
		$iint = '<img src="' . $images['inta'] . '" title="' . $this->Lang("showenteredorders") . '" />';
		break;
	case 'CNF':
		$icnf = '<img src="' . $images['cnfa'] . '" title="' . $this->Lang("showconfirmedorders") . '" />';
		break;
	case 'PAY':
		$ipay = '<img src="' . $images['paya'] . '" title="' . $this->Lang("showpaidorders") . '" />';
		break;
	case 'SHP':
		$ishp = '<img src="' . $images['shpa'] . '" title="' . $this->Lang("showshippedorders") . '" />';
		break;
	case 'INV':
		$iinv = '<img src="' . $images['inva'] . '" title="' . $this->Lang("showinvoicedorders") . '" />';
		break;
}
$smarty->assign('selectINTorderslink', $this->CreateLink(
	$id,
	'defaultadmin',
	$returnid,
	$iint,
	array('active_tab' => 'order', 'orderstatus' => 'INT'),
	'',
	false,
	false,
	'class="pageoptions"'
));
$smarty->assign('selectCNForderslink', $this->CreateLink(
	$id,
	'defaultadmin',
	$returnid,
	$icnf,
	array('active_tab' => 'order', 'orderstatus' => 'CNF'),
	'',
	false,
	false,
	'class="pageoptions"'
));
$smarty->assign('selectPAYorderslink', $this->CreateLink(
	$id,
	'defaultadmin',
	$returnid,
	$ipay,
	array('active_tab' => 'order', 'orderstatus' => 'PAY'),
	'',
	false,
	false,
	'class="pageoptions"'
));
$smarty->assign('selectSHPorderslink', $this->CreateLink(
	$id,
	'defaultadmin',
	$returnid,
	$ishp,
	array('active_tab' => 'order', 'orderstatus' => 'SHP'),
	'',
	false,
	false,
	'class="pageoptions"'
));
$smarty->assign('selectINVorderslink', $this->CreateLink(
	$id,
	'defaultadmin',
	$returnid,
	$iinv,
	array('active_tab' => 'order', 'orderstatus' => 'INV'),
	'',
	false,
	false,
	'class="pageoptions"'
));

$smarty->assign('form2start', $this->CreateFormStart($id, 'defaultadmin', $returnid));
$smarty->assign('form2end', $this->CreateFormEnd());
$smarty->assign('ordertext', $this->Lang('title_orders'));
$smarty->assign('shipmodetext', $this->Lang('title_shipmode'));
$smarty->assign('customernametext', $this->Lang('title_customername'));

// Don't show column header modification date on only entered orders
if ($orderstatus == 'INT') {
	$smarty->assign('moddatetext', $this->Lang('title_creationdate'));
} else {
	$smarty->assign('moddatetext', $this->Lang('title_modificationdate'));
}

$smarty->assign('orderstatus', $orderstatus);
// Allow deletion for those that are authorized
//if ( $alloweddeletion ) {
//	if ( $orderstatus == 'INT' || $orderstatus == 'INV') {
$smarty->assign('selecttext', $this->Lang('title_selectallorders'));
$smarty->assign('inputselectall', $this->CreateInputCheckBox($id, "tagall", "tagall", "", "onclick='selectall();'"));
$smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('submit')));
//	}
//}

// Display template
echo $this->ProcessTemplate('listorders.tpl');
