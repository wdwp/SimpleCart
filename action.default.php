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

# The following api contains the majority of the coding
include_once(dirname(__FILE__) . '/library/orders.api.php');

# Retrieve all the info from what is currently in the cart
$this->orders->ShowCart($entryarray, $totalcost, $id, $returnid);

# Set the number of entries in the cart, so it can be used easily
$cartentries = count($entryarray);

if (!isset($params['template'])) {
	$params['template'] = 'normalbox';
}

# Display the populated template
if (isset($params['template'])) {
	$template = $params['template'];

	$prettyurl = 'SimpleCart/cart/' . $returnid;

	switch ($cartentries) {
		case 0:
			# Just show text that nothing is in the cart
			$this->smarty->assign('label_product_count', $this->Lang('noproductsincart'));
			break;
		case 1:
			# OK, it is only one product, but still using assign by reference
			$this->smarty->assign('productcount', $cartentries);
			$this->smarty->assign('products', $entryarray);
			$this->smarty->assign('label_product_count', $this->Lang('oneproductincart'));
			$this->smarty->assign('viewcart', $this->CreateLink(
				$id,
				'cart',
				$returnid,
				$this->Lang('viewcart'),
				array('perfaction' => ''),
				'',
				false,
				true,
				'',
				true,
				$prettyurl
			));
			break;
		default:
			# More than one product, show the product names and the count
			$this->smarty->assign('productcount', $cartentries);
			$this->smarty->assign('products', $entryarray);
			$this->smarty->assign('label_product_count', $this->Lang('label_product_count', $cartentries));
			$this->smarty->assign('viewcart', $this->CreateLink(
				$id,
				'cart',
				$returnid,
				$this->Lang('viewcart'),
				array('perfaction' => ''),
				'',
				false,
				true,
				'',
				true,
				$prettyurl
			));
			break;
	}
}
echo $this->ProcessTemplateFromDatabase('cart_' . $template);
