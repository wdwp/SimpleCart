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

if (!$this->CheckPermission('SimpleCart')) {
	return;
}

$this->SetPreference('sendconfirmationmail', 0);
if (isset($params['sendconfirmationmail'])) {
	$this->SetPreference('sendconfirmationmail', 1);
}
$this->SetPreference('custmail_subject', trim($params['custmail_subject']));
$this->SetTemplate('custmail_template', $params['custmail_template']);
$this->SetPreference('admin_emailaddress', trim($params['admin_emailaddress']));
$this->SetPreference('admin_subject', trim($params['admin_subject']));
$this->SetTemplate('admin_template', $params['admin_template']);
$this->SetPreference('sendinvoicemail', 0);
if (isset($params['sendinvoicemail'])) {
	$this->SetPreference('sendinvoicemail', 1);
}
$this->SetPreference('cust_invmail_subject', trim($params['cust_invmail_subject']));
$this->SetTemplate('cust_invmail_template', $params['cust_invmail_template']);

$params = array('tab_message' => $this->Lang('mailsupdated'), 'active_tab' => 'mails');
$this->Redirect($id, 'defaultadmin', '', $params);
