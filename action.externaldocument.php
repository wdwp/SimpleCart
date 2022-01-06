<?php
# Module: Cart Made Simple - An Order Intake module for CMS - CMS Made Simple
# Copyright (c) 2010 by Duketown
#
# Program to control the preview/printing/sending of external documents
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

if (isset($params['type'])) $type = $params['type'];
$order_id = 0;
if (isset($params['order_id'])) $order_id = $params['order_id'];
$create_date = 100101;
if (isset($params['create_date'])) $create_date = $params['create_date'];

// Prepare or Send the generated pdf to the correct output
switch ($type) {
	case 'invoice_prep':
		$documenttype = $this->GetPreference('extdoc_invoice', 'invoice_sample');
		include(cms_join_path(dirname(__FILE__), 'library', 'extdoc.' . $documenttype . '.php'));
		$params = array('active_tab' => 'order', 'orderstatus' => 'INV');
		break;
	case 'invoice_send':
		$this->SendInvoiceAsAttach($order_id);
		$params = array('active_tab' => 'order', 'orderstatus' => 'INV', 'tab_message' => $this->Lang('extdocsend_invoice'));
}
// Redirect the user to the default admin screen
$this->Redirect($id, 'defaultadmin', $returnid, $params);
