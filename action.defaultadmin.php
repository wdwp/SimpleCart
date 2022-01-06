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

if (isset($config['errors_on']) && $config['errors_on']) {
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);
}

$gCms = cmsms();
if (!is_object($gCms)) exit;

if (FALSE == empty($params['active_tab'])) {
	$tab = $params['active_tab'];
} else {
	$tab = 'orders';
}

// The tab headers

echo $this->StartTabHeaders();

if ($this->CheckPermission('Use SimpleCart')) {
	echo $this->SetTabHeader('order', $this->Lang('orderadmin'), ('order' == $tab) ? true : false);
	echo $this->SetTabHeader('delivery', $this->Lang('delivery'), ('delivery' == $tab) ? true : false);
	echo $this->SetTabHeader('mails', $this->Lang('title_mail'), ('mails' == $tab) ? true : false);
}

if ($this->CheckPermission('Modify Templates')) {
	echo $this->SetTabHeader('cart_template', $this->Lang('carttemplate'), ('cart_template' == $tab) ? true : false);
}

if ($this->CheckPermission('Modify SimpleCart')) {
	echo $this->SetTabHeader('cart', $this->Lang('cartadmin'), ('cart' == $tab) ? true : false);
}
echo $this->EndTabHeaders();

#
# The content of the tabs
#
echo $this->StartTabContent();
if ($this->CheckPermission('Use SimpleCart')) {
	// Handling orders based upon their status
	echo $this->StartTab('order', $params);
	include(dirname(__FILE__) . '/function.admin_orders_tab.php');
	echo $this->EndTab();

	// Basic handling of delivery methods
	echo $this->StartTab('delivery', $params);
	include(dirname(__FILE__) . '/function.admin_delivery_tab.php');
	echo $this->EndTab();

	// All the parameters needed for sending mails to customers and internal
	echo $this->StartTab('mails', $params);
	include(dirname(__FILE__) . '/function.admin_mails_tab.php');
	echo $this->EndTab();
}

if ($this->CheckPermission('Modify Templates')) {
	// All templates that are cart related
	echo $this->StartTab('cart_template', $params);
	$params['template'] = 'cart_';
	include(dirname(__FILE__) . '/function.admin_listtemplates.php');
	echo $this->EndTab();
}

if ($this->CheckPermission('Modify SimpleCart')) {
	// All the parameters/options that are valid for this module
	echo $this->StartTab('cart', $params);
	include(dirname(__FILE__) . '/function.admin_main_tab.php');
	echo $this->EndTab();
}

echo $this->EndTabContent();
