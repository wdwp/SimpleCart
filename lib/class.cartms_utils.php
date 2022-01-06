<?php
#-------------------------------------------------------------------------
# Module: Cart Made Simple - An Order Intake module for CMS - CMS Made Simple
# Copyright (c) 2012 by Duketown
#
# This function supports the back end for the module Service Desk
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

class cartms_utils
{


	/**
	 * This function adds a product/attribute to the cart
	 * @param int category_id category that the product belongs to
	 * @param int product_id product id that will be added
	 * @param int attribute_id attribute id that will be added, defaults to 0
	 * @param int quantity to be added
	 * @return boolean true
	 */
	function AddProduct($category_id, $product_id, $attribute_id = 0, $qty)
	{
		$session_id = cartms_utils::GetSessionId();

		$db = cmsms()->GetDb();

		// Check if the product/attribute is already in the cart
		$query = 'SELECT COUNT(*) FROM ' . cms_db_prefix() . 'module_cartms_carts
			WHERE session_id = ? AND category_id = ? AND product_id = ? AND attribute_id= ? ';
		$dbresult = $db->Execute($query, array(
			$session_id, $category_id,
			$product_id, $attribute_id
		));
		$numRows = 0;
		if ($dbresult && $row = $dbresult->FetchRow()) {
			$numRows = $row['COUNT(*)'];
		}

		if ($numRows == 0 || $numRows == '0') {
			// This item doesn't exist in the users cart, we will add it with an insert query
			$cart_id = $db->GenID(cms_db_prefix() . 'module_cartms_carts_seq');
			// Creation time can be used to later reorganize the carts table (remove none actual requests)
			$time = $db->DBTimeStamp(time());
			$query = 'INSERT INTO ' . cms_db_prefix() . 'module_cartms_carts (cart_id,
				session_id, category_id, product_id, attribute_id, qty, create_date)
				VALUES( ?, ?, ?, ?, ?, ?, ' . $time . ' )';
			$db->Execute($query, array(
				$cart_id, $session_id, $category_id,
				$product_id, $attribute_id, $qty
			));
		} else {
			// This item already exists in the users cart, we will update it instead
			$db = cmsms()->GetDb();

			$query = 'UPDATE ' . cms_db_prefix() . 'module_cartms_carts SET qty = ?
			WHERE session_id = ? AND product_id = ? AND attribute_id = ?';

			$db->Execute($query, array($qty, $session_id, $product_id, $attribute_id));
			//cartms_utils::UpdateProduct($session_id, $product_id, $attribute_id, $qty);
		}
		return true;
	}

	function FillOrderStatImages()
	{

		$images = array();
		$root = cmsms()->config['root_url'];
		$images['inta'] = cms_join_path($root, 'modules', 'SimpleCart', 'images', 'ord_int_a.png');
		$images['inti'] = cms_join_path($root, 'modules', 'SimpleCart', 'images', 'ord_int_i.png');
		$images['cnfa'] = cms_join_path($root, 'modules', 'SimpleCart', 'images', 'ord_cnf_a.png');
		$images['cnfi'] = cms_join_path($root, 'modules', 'SimpleCart', 'images', 'ord_cnf_i.png');
		$images['paya'] = cms_join_path($root, 'modules', 'SimpleCart', 'images', 'ord_pay_a.png');
		$images['payi'] = cms_join_path($root, 'modules', 'SimpleCart', 'images', 'ord_pay_i.png');
		$images['shpa'] = cms_join_path($root, 'modules', 'SimpleCart', 'images', 'ord_shp_a.png');
		$images['shpi'] = cms_join_path($root, 'modules', 'SimpleCart', 'images', 'ord_shp_i.png');
		$images['inva'] = cms_join_path($root, 'modules', 'SimpleCart', 'images', 'ord_inv_a.png');
		$images['invi'] = cms_join_path($root, 'modules', 'SimpleCart', 'images', 'ord_inv_i.png');

		return $images;
	}

	/**
	 * This function calculates the next date only using business days
	 * Holidays are not looked at
	 * @param date startdate The start date
	 * @param int duedays Number of days to add
	 * @return date Calculated due date
	 */
	function CalculateDueDate($p_startdate, $p_duedays)
	{
		$t_datecalc = $p_startdate;

		$i = 1;
		while ($i <= $p_duedays) {
			$t_datecalc  += 86400; // Add a day.
			$t_date_info  = getdate($t_datecalc);

			if (($t_date_info["wday"] == 0) or ($t_date_info["wday"] == 6)) {
				$t_datecalc += 86400; // Add a day.
				continue;
			}

			$i++;
		}

		return $t_datecalc;
	}

	/**
	 * A function to generate a random user password
	 * @param integer length The length of password to be generated
	 * @return char password The generated password
	 */
	function GenerateUserPassword($length = 8)
	{
		// Script taken from Jon Haworth
		// Start with a blank password
		$password = '';
		// Define possible characters
		$possible = "0123456789bcdfghjkmnpqrstvwxyz";
		// Set up a counter
		$i = 0;
		// Add random characters to $password until $length is reached
		while ($i < $length) {
			// Pick a random character from the possible ones
			$char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);
			// We don't want this character if it's already in the password
			if (!strstr($password, $char)) {
				$password .= $char;
				$i++;
			}
		}
		return $password;
	}

	/**
	 * A function to retrieve order header information
	 *
	 * @param int $order_id The internal id of the order
	 * @return array All the fields from the order header
	 */
	function GetOrderHeader($order_id)
	{
		$result = array();
		$db = cmsms()->GetDb();
		$query = "SELECT order_id,
				totalproduct,
				totaldiscount,
				totalshipping,
				totaladmincost,
				shipmode,
				`comment`,
				`status`,
				customer_id,
				currency,
				paymethod,
				totalnetweight,
				totalvat0amount,
				totalvat1amount,
				totalvat2amount,
				totalvat3amount,
				totalvat4amount,
				termsagreed,
				remark,
				coupon_code,
				invoiceno,
				delivery_date,
				create_date
			FROM " . cms_db_prefix() . "module_cartms_orders
					WHERE order_id=?";
		$row = $db->GetRow($query, array($order_id));
		if ($row) {
			$result = $row;
		} else {
			$result = false;
		}
		return $result;
	}

	/**
	 * This function will generate an encrypted string and will set it as a cookie using set_cookie. This will
	 * also be used as the cookieId field in the cart table
	 * @return char session id
	 */
	function GetSessionId()
	{
		if (isset($_COOKIE["session_id"])) {
			return $_COOKIE["session_id"];
		} else {
			// There is no cookie set. We will set the cookie and return the value of the users session ID
			// Expiration time of cookie is set to 30 days
			@session_start();
			setcookie("session_id", session_id(), time() + ((3600 * 24) * 30));
			return session_id();
		}
	}

	/**
	 * Function that will remove product or attribute from the cart of visitor
	 * @param int product_id the product id that is to be removed
	 * @param int attribute_id the ide of the attribute to be removed (product id should be included)
	 */
	function RemoveProduct($product_id, $attribute_id)
	{
		$db = cmsms()->GetDb();

		$query = 'DELETE FROM ' . cms_db_prefix() . 'module_cartms_carts
			WHERE session_id = ? AND product_id = ? AND attribute_id = ?';
		$db->Execute($query, array(cartms_utils::GetSessionId(), $product_id, $attribute_id));
	}

	function StoreDeliveryInfo($params)
	{
		// In the third step of the order processing, the visitor has entered a delivery method
		// Now that we know it, store it in the order
		$db = cmsms()->GetDb();
		$deliverymethod = $params['deliverymethod'];
		$agreetoterms = $params['agreetoterms'];
		$order_id = $params['order_id'];

		// Retrieve the order header information which contains the net weight. The weight is used as a factor in delivery cost
		$orderheader = array();
		$orderheader = cartms_utils::GetOrderHeader($order_id);

		// Retrieve the shipping price, so it can be added in the order
		$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_cartms_shippingprovider WHERE shipprovcode = ? ';
		$dbresult = $db->Execute($query, array($deliverymethod));
		if ($dbresult && $row = $dbresult->FetchRow()) {
			$totalshipping = $row['shipprovprice'] + $row['shippriceperweight'] * $orderheader['totalnetweight'];
			$shipworkdays = $row['shipworkdays'];
		} else {
			$totalshipping = 0;
			$shipworkdays = 0;
		}
		if ($shipworkdays == 0) {
			$newdeliverydate = time();
		} else {
			$newdeliverydate = cartms_utils::CalculateDueDate(time(), $shipworkdays);
		}
		$newdeliverydate = trim($db->DBTimeStamp($newdeliverydate), "'");

		$query = 'UPDATE ' . cms_db_prefix() . 'module_cartms_orders set shipmode = ?, totalshipping = ?, termsagreed = ?, delivery_date = ?
			WHERE order_id = ?';
		$db->Execute($query, array($deliverymethod, $totalshipping, $agreetoterms, $newdeliverydate, $order_id));

		return;
	}

	function StoreShipInfo($params)
	{
		// In the second step of the order processing, the visitor has entered a ship to address
		// Now that we know it, store it using FEU for later usage
		// At this moment we're allowing instant registration (so no checks)
		$modops = cmsms()->GetModuleOperations();
		$feu = $modops->get_module_instance('FrontEndUsers');
		$smarty = cmsms()->GetSmarty();
		if (!$feu) {
			// Set the id of the current user
			$user_id = 0;
		} else {
			$db = cmsms()->GetDb();
			// Check what the preferences are for the username in FEU

			$username_is_email = $feu->get_settings()->username_is_email;

			if ($username_is_email != 0) {
				$username = $params['email'];
			} else {
				$username = $params['firstname'] . ' ' . $params['lastname'];
				$username = str_replace(' ', '_', $username);
			}

			$params['addressstate'] = isset($params['addressstate']) ? $params['addressstate'] : '';
			$params['billaddressstate'] = isset($params['billaddressstate']) ? $params['billaddressstate'] : '';
			$params['addressstreet'] = isset($params['addressstreet']) ? $params['addressstreet'] : '';
			$params['addresscity'] = isset($params['addresscity']) ? $params['addresscity'] : '';
			$params['addresszip'] = isset($params['addresszip']) ? $params['addresszip'] : '';
			$params['addresscountry'] = isset($params['addresscountry']) ? $params['addresscountry'] : '';
			$params['billfirstname'] = isset($params['billfirstname']) ? $params['billfirstname'] : $params['firstname'];
			$params['billlastname'] = isset($params['billlastname']) ? $params['billlastname'] : $params['lastname'];
			$params['billaddresscity'] = isset($params['billaddresscity']) ? $params['billaddresscity'] : $params['addresscity'];
			$params['billaddressstreet'] = isset($params['billaddressstreet']) ? $params['billaddressstreet'] : $params['addressstreet'];
			$params['billaddresszip'] = isset($params['billaddresszip']) ? $params['billaddresszip'] : $params['addresszip'];
			$params['billaddresscountry'] = isset($params['billaddresscountry']) ? $params['billaddresscountry'] : $params['addresscountry'];

			// Now check if the user already exists
			$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_feusers_users WHERE username = ?';
			$row = $db->GetRow($query, array($username));
			if (!$row) {
				$group_id = $feu->GetGroupID('SimpleCart');
				//echo '<pre>Reached line: ' . __LINE__ . ' in source: ' . __FILE__ . '</pre>';
				$password = cartms_utils::GenerateUserPassword();
				//echo '<pre>Reached line: ' . __LINE__ . ' in source: ' . __FILE__ . '</pre>';
				$expires = strtotime('+5 years');
				// Add the customer
				$result = $feu->AddUser($username, $password, $expires);
				// Retrieve the id
				$query = 'SELECT `id` FROM ' . cms_db_prefix() . 'module_feusers_users WHERE `username` = ?';
				$row = $db->GetRow($query, array($username));
				$user_id = $row['id'];
				// Save the password and name so it can be used in the first confirmation mail
				$query = 'INSERT INTO ' . cms_db_prefix() . 'module_cartms_newuserpasswords (user_id, password, username)
					VALUES(?, ?, ?)';
				$db->Execute($query, array($user_id, $password, $username));
				// Connect customer to group
				#$result = $feu->AssignUserToGroup( $user_id, $group_id );
				$query = "INSERT INTO " . cms_db_prefix() . "module_feusers_belongs VALUES (?,?)";
				$db->Execute($query, array($user_id, $group_id));
				// Address id (read user_id) is known
				// Save the address in the connected property fields of customer
				$result = $feu->SetUserPropertyFull('email', $params['email'], $user_id);
			} else {
				$user_id = $row['id'];
			}
			if (isset($params['firstname']) && !empty($params['firstname'])) $result = $feu->SetUserPropertyFull('firstname', $params['firstname'], $user_id);
			if (isset($params['lastname']) && !empty($params['lastname'])) $result = $feu->SetUserPropertyFull('surname', $params['lastname'], $user_id);
			if (isset($params['addressstreet']) && !empty($params['addressstreet'])) $result = $feu->SetUserPropertyFull('addressstreet', $params['addressstreet'], $user_id);
			if (isset($params['addresscity']) && !empty($params['addresscity'])) $result = $feu->SetUserPropertyFull('addresscity', $params['addresscity'], $user_id);
			if (isset($params['addressstate']) && !empty($params['addressstate'])) $result = $feu->SetUserPropertyFull('addressstate', $params['addressstate'], $user_id);
			if (isset($params['addresszip']) && !empty($params['addresszip'])) $result = $feu->SetUserPropertyFull('addresszip', $params['addresszip'], $user_id);
			if (isset($params['addresscountry']) && !empty($params['addresscountry'])) $result = $feu->SetUserPropertyFull('addresscountry', $params['addresscountry'], $user_id);
			if (isset($params['telephone']) && !empty($params['telephone'])) $result = $feu->SetUserPropertyFull('telephone', $params['telephone'], $user_id);
			if (isset($params['email']) && !empty($params['email'])) $result = $feu->SetUserPropertyFull('email', $params['email'], $user_id);
			if (isset($params['billfirstname']) && !empty($params['billfirstname'])) $result = $feu->SetUserPropertyFull('billfirstname', $params['billfirstname'], $user_id);
			if (isset($params['billlastname']) && !empty($params['billlastname'])) $result = $feu->SetUserPropertyFull('billsurname', $params['billlastname'], $user_id);
			if (isset($params['billaddressstreet']) && !empty($params['billaddressstreet'])) $result = $feu->SetUserPropertyFull('billaddressstreet', $params['billaddressstreet'], $user_id);
			if (isset($params['billaddresscity']) && !empty($params['billaddresscity'])) $result = $feu->SetUserPropertyFull('billaddresscity', $params['billaddresscity'], $user_id);
			if (isset($params['billaddressstate']) && !empty($params['billaddressstate'])) $result = $feu->SetUserPropertyFull('billaddressstate', $params['billaddressstate'], $user_id);
			if (isset($params['billaddresszip']) && !empty($params['billaddresszip'])) $result = $feu->SetUserPropertyFull('billaddresszip', $params['billaddresszip'], $user_id);
			if (isset($params['billaddresscountry']) && !empty($params['billaddresscountry'])) $result = $feu->SetUserPropertyFull('billaddresscountry', $params['billaddresscountry'], $user_id);
			// Save the user_id for later
			return $user_id;
		}
	}

	/**
	 * This function adds a product/attribute to the cart
	 * @param char session_id session that needs to be changed
	 * @param int product_id product that needs a change to quantity
	 * @param int attribute_id attribute that needs change to quantity
	 * @param int quantity to be added
	 * @return boolean true
	 */
	function UpdateProduct($session_id, $product_id, $attribute_id, $qty)
	{
		$db = cmsms()->GetDb();

		$query = 'UPDATE ' . cms_db_prefix() . 'module_cartms_carts SET qty = ?
			WHERE session_id = ? AND product_id = ? AND attribute_id = ?';

		$db->Execute($query, array($qty, $session_id, $product_id, $attribute_id));

		return true;
	}
}
