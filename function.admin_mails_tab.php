<?php
# Module: Cart Made Simple - An Order Intake module for CMS - CMS Made Simple
# Copyright (c) 2009 by Duketown
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

$gCms = cmsms(); if( !is_object($gCms) ) exit;

$smarty->assign('startform', $this->CreateFormStart ($id, 'save_admin_mails', $returnid));
// Make it possible to prepare mail settings for both customer as well as internal used mails
$smarty->assign('title_fieldset_mail', $this->Lang('title_fieldset_mail'));
$smarty->assign('title_sendmail', $this->Lang('title_sendmail'));
$smarty->assign('input_sendmail', $this->CreateInputCheckbox($id, 'sendconfirmationmail', true, $this->GetPreference('sendconfirmationmail', false)));
$smarty->assign('title_custmail_subject', $this->Lang('title_custmail_subject'));
$smarty->assign('input_custmail_subject', $this->CreateInputText($id, 'custmail_subject', $this->GetPreference('custmail_subject', $this->Lang('yourorder')), '50', '50'));
$smarty->assign('title_custmail_template', $this->Lang('title_custmail_template'));
$smarty->assign('input_custmail_template',$this->CreateTextArea(false, $id, $this->GetTemplate('custmail_template'),'custmail_template'));
$smarty->assign('title_admin_emailaddress', $this->Lang('title_admin_emailaddress'));

$smarty->assign('input_admin_emailaddress', $this->CreateInputText($id, 'admin_emailaddress', $this->GetPreference('admin_emailaddress',''), '50', '50'));
$smarty->assign('title_admin_subject', $this->Lang('title_admin_subject'));
$smarty->assign('input_admin_subject', $this->CreateInputText($id, 'admin_subject', $this->GetPreference('admin_subject', $this->Lang('neworderplaced')), '68', '68'));
$smarty->assign('title_admin_template', $this->Lang('title_admin_template'));
$smarty->assign('input_admin_template',$this->CreateTextArea(false, $id, $this->GetTemplate('admin_template'),'admin_template'));

$smarty->assign('title_fieldset_invmail', $this->Lang('title_fieldset_invmail'));
$smarty->assign('title_send_invmail', $this->Lang('title_send_invmail'));
$smarty->assign('input_send_invmail', $this->CreateInputCheckbox($id, 'sendinvoicemail', true, $this->GetPreference('sendinvoicemail', false)));
$smarty->assign('title_cust_invmail_subject', $this->Lang('title_cust_invmail_subject'));
$smarty->assign('input_cust_invmail_subject', $this->CreateInputText($id, 'cust_invmail_subject', $this->GetPreference('cust_invmail_subject', $this->Lang('yourinvoice')), '50', '50'));
$smarty->assign('title_cust_invmail_template', $this->Lang('title_cust_invmail_template'));
$smarty->assign('input_cust_invmail_template',$this->CreateTextArea(false, $id, $this->GetTemplate('cust_invmail_template'),'cust_invmail_template'));

$smarty->assign('submit', $this->CreateInputSubmit ($id, 'mailssubmitbutton', $this->Lang('submit')));
$smarty->assign('endform', $this->CreateFormEnd ());

// Display the Admin Mail settings
echo $this->ProcessTemplate ('adminmails.tpl');
?>