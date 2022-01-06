<?php
class CMSOrders
{

	var $module;
	var $taboptarray;

	function CMSOrders(&$module)
	{
		$this->module = $module;
		// mysql-specific, but ignored by other database
		$this->taboptarray = array('mysql' => 'ENGINE=MyISAM');
	}

	function ClearCart()
	{
		$db = cmsms()->GetDb();

		$query = 'DELETE FROM ' . cms_db_prefix() . 'module_cartms_carts
			WHERE session_id = \'' . cartms_utils::GetSessionId() . '\'';
		$db->Execute($query);
	}

	function ShowCart(&$entryarray, &$totalcost, $id, $returnid)
	{
		$db = cmsms()->GetDb();
		$ShopMS = $this->module->GetModuleInstance('SimpleShop');

		$query = "SELECT cart.cart_id, cart.product_id, cart.attribute_id, cart.qty, cart.category_id,
			cat.name AS cat_name, cat.description AS cat_description,
			prd.name AS prd_name, prd.description AS prd_description,
			prd.price AS prd_price, attr.attribute_id AS attr_attribute_id, attr.name AS attr_name,
			attr.description AS attr_description, attr.priceadjustment AS attr_priceadjustment,
			attr.priceadjusttype AS attr_priceadjusttype
			FROM " . cms_db_prefix() . "module_cartms_carts cart
			LEFT OUTER JOIN " . cms_db_prefix() . "module_sms_categories cat ON
			cart.category_id = cat.category_id
			LEFT OUTER JOIN " . cms_db_prefix() . "module_sms_products prd ON
			cart.product_id = prd.product_id
			LEFT OUTER JOIN " . cms_db_prefix() . "module_sms_product_attributes attr ON
			cart.attribute_id = attr.attribute_id
			WHERE cart.session_id = '" . cartms_utils::GetSessionId() . "'
			ORDER BY cart.cart_id ASC, prd.product_id ASC, attr.attribute_id ASC";
		$dbresult = $db->Execute($query);
		$rowclass = 'row1';
		$entryarray = array();
		$totalcost = 0;

		while ($dbresult && $row = $dbresult->FetchRow()) {

			$onerow = new stdClass();
			$onerow->id = $row['cart_id'];
			//$onerow->category_id = $row['category_id'];
			$onerow->category_name = $row['cat_name'];
			$onerow->category_description = $row['cat_description'];
			$onerow->product_id = $row['product_id'];
			$onerow->prd_name = $row['prd_name'];

			$onerow->prd_link = $ShopMS->CreateLink(
				$id,
				'fe_product_detail',
				$returnid,
				$row['prd_name'],
				array('category_id' => $row['category_id'], 'product_id' => $row['product_id']),
				'',
				true,
				false,
				'',
				true
			);

			$onerow->prd_description = $row['prd_description'];
			$onerow->attribute_id = $row['attribute_id'];
			$onerow->attr_name = $row['attr_name'];
			$onerow->attr_description = $row['attr_description'];
			if ($row['attribute_id'] == 0) {
				$onerow->price = $this->FormatAmount($row['prd_price']);
				$onerow->lineamount = $this->FormatAmount($row['qty'] * $row['prd_price']);
				// Increment the total cost of items
				$totalcost += ($row['qty'] * $row['prd_price']);
			} else {
				$price = 0;
				if ($ShopMS) {
					$price = $ShopMS->CalculateAttributePrice(
						$row['prd_price'],
						$row['attr_priceadjusttype'],
						$row['attr_priceadjustment']
					);
				}
				$onerow->price = $this->FormatAmount($price);
				$onerow->lineamount = $this->FormatAmount($row['qty'] * $price);
				// Increment the total cost of items
				$totalcost += ($row['qty'] * $price);
			}
			// Prepare the possible values of quantities
			// First check if inventory is tracked
			$quantityonstock = $this->module->InventoryOnStockAvail(
				$row['product_id'],
				$row['attribute_id']
			);

			$qtydropdown = array();
			for ($i = $quantityonstock[1]; $i <= $quantityonstock[0]; $i++) {
				if ($row['qty'] == $i) {
					$onerow->myqty = $i;
				}
				$qtydropdown[$i] = $i;
			}
			$onerow->qtydropdown = $qtydropdown;
			$prettyurl = 'SimpleCart/rmv/' . $row['product_id'] . '/' . $row['attribute_id'] . '/';
			$prettyurl .= ((isset($detailpage) && $detailpage != '') ? $detailpage : $returnid);
			$onerow->deletelink = $this->module->CreateLink(
				$id,
				'cart',
				$returnid,
				#$this->module->$themeObject->DisplayImage('icons/system/delete.gif', $this->module->Lang('delete'),'','','systemicon'),
				$this->module->Lang('remove_product_from_cart'),
				array(
					'perfaction' => 'remove_product', 'product_id' => $row['product_id'],
					'attribute_id' => $row['attribute_id']
				),
				'',
				false,
				true,
				'',
				true,
				$prettyurl
			);

			$entryarray[] = $onerow;

			$onerow->rowclass = $rowclass;
			($rowclass == "row1" ? $rowclass = "row2" : $rowclass = "row1");
		}
	}

	function GetCartInfo()
	{
		// This function summerized the weigth of the products, while they are
		// still in the cart. This is part of speed checkout in which only at the
		// end the cart is promoted to become an order and hence the order total and
		// weight is not known during validation
		$db = cmsms()->GetDb();
		$totalnetweight = 0;

		$session_id = cartms_utils::GetSessionId();

		$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_cartms_carts WHERE session_id = ?';
		$dbresult = $db->Execute($query, array($session_id));

		while ($dbresult && $row = $dbresult->FetchRow()) {
			$product_id = $row['product_id'];
			// Retrieve item information
			$qryitem = 'SELECT * FROM ' . cms_db_prefix() . 'module_sms_products WHERE product_id = ?';
			$rowitem = $db->GetRow($qryitem, array($product_id));
			$totalnetweight += $row['qty'] * $rowitem['netweight'];
		}
		$cartinfo = array();
		$cartinfo['totalnetweight'] = $totalnetweight;

		return $cartinfo;
	}

	function CheckOrderExists($order_id)
	{
		$db = cmsms()->GetDb();
		//$dict = NewDataDictionary($db);
		$sql = 'SELECT count(*) cnt FROM ' . cms_db_prefix() . 'module_cartms_orders
					WHERE order_id=?';
		$dbresult = $db->Execute($sql, array($order_id));
		if (!$dbresult) {
			return false;
		} else {
			$row = $dbresult->FetchRow();
			if ($row['cnt'] > 0) return true;
		}
		return false;
	}

	function GetLastUsedOrderNumber()
	{
		$db = cmsms()->GetDb();
		//$dict = NewDataDictionary($db);
		$sql = 'SELECT id FROM ' . cms_db_prefix() . 'module_cartms_orders_seq';
		$dbresult = $db->Execute($sql);
		if ($dbresult) {
			$row = $dbresult->FetchRow();
			return $row['id'];
		} else {
			return 0;
		}
	}

	function GetOrderShipTo($customer_id, $countryformating = true)
	{
		// Retrieve the address from the module FrontEndUsers
		$feusers = &$this->module->GetModuleInstance('FrontEndUsers');
		if (!$feusers) {
		} else {
			$db = &$this->module->GetDb();
			$dict = NewDataDictionary($db);
			// Possible coding here on formating the address with regards to country formating
			// See http://bitboost.com/ref/international-address-formats.html for more info on formating
			$shipto['firstname'] = $feusers->GetUserPropertyFull('firstname', $customer_id);
			$shipto['surname'] = $feusers->GetUserPropertyFull('surname', $customer_id);
			$shipto['shiptoname'] = $shipto['firstname'] . ' ' . $shipto['surname'];
			$shipto['email'] = $feusers->GetUserPropertyFull('email', $customer_id);
			$shipto['addressstreet'] = $feusers->GetUserPropertyFull('addressstreet', $customer_id);
			$shipto['addresscity'] = $feusers->GetUserPropertyFull('addresscity', $customer_id);
			$shipto['addressstate'] = $feusers->GetUserPropertyFull('addressstate', $customer_id);
			$shipto['addresszip'] = $feusers->GetUserPropertyFull('addresszip', $customer_id);
			$shipto['addresscountry'] = $feusers->GetUserPropertyFull('addresscountry', $customer_id);
			$shipto['telephone'] = $feusers->GetUserPropertyFull('telephone', $customer_id);
			$shipto['billfirstname'] = $feusers->GetUserPropertyFull('billfirstname', $customer_id);
			$shipto['billsurname'] = $feusers->GetUserPropertyFull('billsurname', $customer_id);
			$shipto['billtoname'] = $shipto['billfirstname'] . ' ' . $shipto['billsurname'];
			$shipto['billaddressstreet'] = $feusers->GetUserPropertyFull('billaddressstreet', $customer_id);
			$shipto['billaddresscity'] = $feusers->GetUserPropertyFull('billaddresscity', $customer_id);
			$shipto['billaddressstate'] = $feusers->GetUserPropertyFull('billaddressstate', $customer_id);
			$shipto['billaddresszip'] = $feusers->GetUserPropertyFull('billaddresszip', $customer_id);
			$shipto['billaddresscountry'] = $feusers->GetUserPropertyFull('billaddresscountry', $customer_id);
			if ($countryformating) {
				// Address formating
				$shipto['shiptoname'] .= '<br>';
				$shipto['addressstreet'] .= '<br>';
				$shipto['addresszip'] .= '<br>';
				$shipto['addresscity'] .= '<br>';
				if ($shipto['addressstate'] != '') {
					$shipto['addressstate'] .= '<br>';
				}
				// Address formating for the billing address
				$shipto['billtoname'] .= '<br>';
				$shipto['billaddressstreet'] .= '<br>';
				$shipto['billaddresszip'] .= '<br>';
				$shipto['billaddresscity'] .= '<br>';
				if ($shipto['billaddressstate'] != '') {
					$shipto['billaddressstate'] .= '<br>';
				}
			}
			return $shipto;
		}
	}

	function GetOrderLines($order_id)
	{
		$db = cmsms()->GetDb();
		//$dict = NewDataDictionary($db);
		$sql = 'SELECT orderline_id,
				ol.product_id AS ol_product_id,
				ol.attribute_id AS ol_attribute_id,
				qty,
				ol.description AS ol_description,
				ol.price AS ol_price,
				c.name AS c_name,
				p.sku,
				p.itemnumber AS p_itemnumber,
				p.name AS p_name,
				a.itemnumber AS a_itemnumber,
				a.name AS a_name
			FROM ' . cms_db_prefix() . 'module_cartms_order_lines ol
			LEFT OUTER JOIN ' . cms_db_prefix() . 'module_sms_categories c ON ol.category_id = c.category_id
			LEFT OUTER JOIN ' . cms_db_prefix() . 'module_sms_products p ON ol.product_id = p.product_id
			LEFT OUTER JOIN ' . cms_db_prefix() . 'module_sms_product_attributes a ON ol.attribute_id = a.attribute_id
			WHERE ol.order_id = ?';
		$orderlinesby = $this->module->GetPreference('confirmlinesby', 'prodattrid');
		switch ($orderlinesby) {
			case 'prodattrid':
				$sql .= ' ORDER BY ol.product_id, ol.attribute_id';
				break;
			case 'prodattrname':
				$sql .= ' ORDER BY p.name, a.name';
				break;
			case 'orderline':
				$sql .= ' ORDER BY ol.orderline_id';
				break;
		}
		$dbresult = $db->Execute($sql, array($order_id));
		if (!$dbresult) {
			return false;
		}
		$entryarray = array();

		while ($dbresult && $row = $dbresult->FetchRow()) {
			$onerow = new stdClass();
			$onerow->orderline_id = $row['orderline_id'];
			$onerow->product_id = $row['ol_product_id'];
			$onerow->attribute_id = $row['ol_attribute_id'];
			$onerow->description = $row['ol_description'];
			$onerow->categoryname = $row['c_name'];
			$onerow->productname = $row['p_name'];
			$onerow->attributename = $row['a_name'];
			$onerow->sku = $row['sku'];
			if ($row['ol_attribute_id'] == '' || $row['ol_attribute_id'] == 0) {
				$onerow->itemnumber = $row['p_itemnumber'];
			} else {
				$onerow->itemnumber = $row['a_itemnumber'];
			}
			$onerow->qty = $row['qty'];
			$onerow->price = $this->FormatAmount($row['ol_price']);
			$onerow->lineamount = $this->FormatAmount($row['qty'] * $row['ol_price']);

			$entryarray[] = $onerow;
		}

		return $entryarray;
	}
	function DeleteOrder($order_id)
	{
		$db = cmsms()->GetDb();
		//$dict = NewDataDictionary($db);
		$sql = 'DELETE FROM ' . cms_db_prefix() . 'module_cartms_order_lines WHERE order_id = ?';
		$dbresult = $db->Execute($sql, array($order_id));
		if (!$dbresult) {
			return false;
		}
		$sql = 'DELETE FROM ' . cms_db_prefix() . 'module_cartms_orders WHERE order_id = ?';
		$dbresult = $db->Execute($sql, array($order_id));
		if (!$dbresult) {
			return false;
		}
	}

	function GenerateOrder($params)
	{

		$db = cmsms()->GetDb();
		// Read all the products in the cart and copy them into order lines
		$order_id = $db->GenID(cms_db_prefix() . 'module_cartms_orders_seq');
		// If administrator reset the last used order to earlier order number,
		// keep on generating until not found
		while ($this->CheckOrderExists($order_id)) {
			$order_id = $db->GenID(cms_db_prefix() . 'module_cartms_orders_seq');
		}
		// Save the order id, so it can be used in other functions that will be called in sequence
		$params['order_id'] = $order_id;
		$totalproduct = 0; // Contains the total amount of the products
		$totaldiscount = 0; // Contains any discount given/taken via coupons
		$totalshipping = 0; // Will contain the total shipping/handling cost
		$totalnetweight = 0; // Total netweight from formula sum(qty*netweight)
		$totalvat0amount = 0; // Calculated vat amount for code 0
		$totalvat1amount = 0; // Calculated vat amount for code 1
		$totalvat2amount = 0; // Calculated vat amount for code 2
		$totalvat3amount = 0; // Calculated vat amount for code 3
		$totalvat4amount = 0; // Calculated vat amount for code 4
		// Retrieve the vat percentages
		$vat0perc = $this->module->GetPreference('vat0perc', 0);
		$vat1perc = $this->module->GetPreference('vat1perc', 0);
		$vat2perc = $this->module->GetPreference('vat2perc', 0);
		$vat3perc = $this->module->GetPreference('vat3perc', 0);
		$vat4perc = $this->module->GetPreference('vat4perc', 0);
		$session_id = $params['session_id'];
		// Creation time can be used to later build reports on number of orders processed in a period
		$time = $db->DBTimeStamp(time());
		$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_cartms_carts WHERE session_id = ?';
		$dbresult = $db->Execute($query, array($session_id));

		while ($dbresult && $row = $dbresult->FetchRow()) {
			// Fill all the fields in order line with found cart line information
			$orderline_id = $db->GenID(cms_db_prefix() . 'module_cartms_order_lines_seq');
			$category_id = $row['category_id'];
			$product_id = $row['product_id'];
			$attribute_id = $row['attribute_id'];
			$qty = $row['qty'];
			// Retrieve item information
			$qryitem = 'SELECT * FROM ' . cms_db_prefix() . 'module_sms_products WHERE product_id = ?';
			$rowitem = $db->GetRow($qryitem, array($product_id));
			$vatcode = $rowitem['vatcode'];
			$price = $rowitem['price'];
			$totalnetweight += $qty * $rowitem['netweight'];
			$ShopMS = &$this->module->GetModuleInstance('SimpleShop');
			if ($attribute_id != '' && $attribute_id != 0) {
				// Retrieve item information from attribute
				$qryitem = 'SELECT * FROM ' . cms_db_prefix() . 'module_sms_product_attributes
					WHERE attribute_id = ?';
				$rowitem = $db->GetRow($qryitem, array($attribute_id));
				$rowitem['vatcode'] = $vatcode;
				if ($ShopMS) {
					$price = $ShopMS->CalculateAttributePrice(
						$price,
						$rowitem['priceadjusttype'],
						$rowitem['priceadjustment']
					);
				}
			}

			$vatcode = isset($rowitem['vatcode']) ? $rowitem['vatcode'] : NULL;
			$lineamount = $qty * $price;
			$comment = '';
			$status = 'RCV'; // Received for processing
			// Check if VAT is to be calculated (is set up in SimpleShop)

			if ($ShopMS) {
				$priceinclvat = (int) $ShopMS->GetPreference('pricesinclvat', 0);
			} else {
				$priceinclvat = 1;
			}
			if ($priceinclvat == 0) {
				// Calculate the vat amount based upon the vatcode just set
				switch ($vatcode) {
					case '0':
						$totalvat0amount += ($lineamount * $vat0perc) / 100;
						break;
					case '1';
						$totalvat1amount += ($lineamount * $vat1perc) / 100;
						break;
					case '2':
						$totalvat2amount += ($lineamount * $vat2perc) / 100;
						break;
					case '3';
						$totalvat3amount += ($lineamount * $vat3perc) / 100;
						break;
					case '4';
						$totalvat4amount += ($lineamount * $vat4perc) / 100;
						break;
					default:
						break;
				}
			}

			$query = "INSERT INTO `" . cms_db_prefix() . "module_cartms_order_lines`
				(`orderline_id`, `order_id`, `category_id`, `product_id`, `attribute_id`, `description`, `qty`,
				`price`, `lineamount`, `comment`, `status`, `vatcode`, `create_date`, `modified_date`)
				VALUES( ?, ?, ?, ?, ?, '', ?, ?, ?, '', ?, ?, " . $time . ", " . $time . ")";
			$resutl = $db->Execute($query, array(
				$orderline_id,
				$order_id,
				$category_id,
				$product_id,
				$attribute_id,
				$qty,
				$price,
				$lineamount,
				$status,
				$vatcode
			));
			// Cumulate the total fields for the order header
			$totalproduct += $lineamount;
		}
		// Retrieve the discount based upon the coupon code
		$coupon_code = isset($params['coupon_code']) ? trim($params['coupon_code']) : '';
		$SCoupons = &$this->module->GetModuleInstance('SCoupons');
		if ($SCoupons) {
			$totaldiscount = $SCoupons->CalculateDiscount(
				$totalproduct,
				$coupon_code,
				$params['user_id']
			);
		}
		// Check and add administration cost if needed
		$totaladmincost = 0;
		$admincostadd = $this->module->GetPreference('admincostadd', false);
		if ($admincostadd) {
			// Check ordering amount against minimum order amount
			$sumamount = $totalproduct - $totaldiscount;
			if ($sumamount < $this->module->GetPreference('admincostminamount', 100)) {
				$totaladmincost = $this->module->GetPreference('admincost', 0);
			}
		}
		// Sum information to store in order header
		$shipmode = ''; // Will follow later in StoreDeliveryInfo as called from orderdelivery.php
		$comment = '';
		$status = 'INT'; // Initiated
		$customer_id = $params['user_id'];
		$currency = $this->module->GetPreference('cartcurrency', 'EUR');
		$orderremark = (!empty($params['orderremark'])) ? $params['orderremark'] : '';
		$paymethod = 'Cash'; // Will follow later in StorePaymentInfo as called from orderpayment.php
		$coupon_code = (isset($coupon_code) && !empty($coupon_code)) ? $coupon_code : '';
		$query = 'INSERT INTO ' . cms_db_prefix() . 'module_cartms_orders
			(order_id, totalproduct, totaldiscount, totalshipping, totaladmincost, shipmode,
			comment, status, customer_id, currency, paymethod, totalnetweight,
			totalvat0amount, totalvat1amount, totalvat2amount, totalvat3amount, totalvat4amount,
			remark, coupon_code, create_date, modified_date)
			VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ' . $time . ', ' . $time . ' )';
		$db->Execute($query, array(
			$order_id,
			$totalproduct,
			$totaldiscount,
			$totalshipping,
			$totaladmincost,
			$shipmode,
			$comment,
			$status,
			$customer_id,
			$currency,
			$paymethod,
			$totalnetweight,
			$totalvat0amount,
			$totalvat1amount,
			$totalvat2amount,
			$totalvat3amount,
			$totalvat4amount,
			$orderremark,
			$coupon_code
		));

		// Ready with the order so pass back the order is enough
		return $order_id;
	}

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

	function SendCustomerConfirmationMail($orderheader)
	{
		$db = cmsms()->GetDb();
		// Retrieve the password of the customer so we can check if this is a brand new customer
		$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_cartms_newuserpasswords WHERE user_id = ?';
		$row = $db->GetRow($query, array($orderheader['customer_id']));
		if ($row) {
			$this->module->smarty->assign('newcustomer', true);
			$this->module->smarty->assign('title_password', $this->module->Lang('title_password'));
			$this->module->smarty->assign('password', $row['password']);
			$this->module->smarty->assign('title_name', $this->module->Lang('title_username'));
			$this->module->smarty->assign('username', $row['username']);
			// Now that password has been assigned to smarty, delete the password
			$query = 'DELETE FROM ' . cms_db_prefix() . 'module_cartms_newuserpasswords WHERE user_id = ?';
			$db->Execute($query, array($orderheader['customer_id']));
		}
		$cmsmailer = $this->module->GetModuleInstance('CMSMailer');
		$shipto = array();
		$shipto = $this->GetOrderShipTo($orderheader['customer_id']);
		$orderlines = array();
		$orderlines = $this->GetOrderLines($orderheader['order_id']);
		$this->module->smarty->assign('products', $orderlines);
		$this->module->smarty->assign('productcount', count($orderlines));
		// Fill the mail body. This will become data merged with a template
		if ($orderheader['order_id'] != '') $this->module->smarty->assign('order_id', $orderheader['order_id']);
		if ($orderheader['customer_id'] != '') $this->module->smarty->assign('customer_id', $orderheader['customer_id']);
		if ($shipto['shiptoname'] != '') $this->module->smarty->assign('shiptoname', $shipto['shiptoname']);
		if ($shipto['addressstreet'] != '') $this->module->smarty->assign('shiptostreet', $shipto['addressstreet']);
		if ($shipto['addresscity'] != '') $this->module->smarty->assign('shiptocity', $shipto['addresscity']);
		if ($shipto['addressstate'] != '') $this->module->smarty->assign('shiptostate', $shipto['addressstate']);
		if ($shipto['addresszip'] != '') $this->module->smarty->assign('shiptozip', $shipto['addresszip']);
		if ($shipto['addresscountry'] != '') $this->module->smarty->assign('shiptocountry', $shipto['addresscountry']);
		if ($shipto['telephone'] != '') $this->module->smarty->assign('shiptotelephone', $shipto['telephone']);
		if ($shipto['billtoname'] != '') $this->module->smarty->assign('billtoname', $shipto['billtoname']);
		if ($shipto['billaddressstreet'] != '') $this->module->smarty->assign('billtostreet', $shipto['billaddressstreet']);
		if ($shipto['billaddresscity'] != '') $this->module->smarty->assign('billtocity', $shipto['billaddresscity']);
		if ($shipto['billaddressstate'] != '') $this->module->smarty->assign('billtostate', $shipto['billaddressstate']);
		if ($shipto['billaddresszip'] != '') $this->module->smarty->assign('billtozip', $shipto['billaddresszip']);
		if ($shipto['billaddresscountry'] != '') $this->module->smarty->assign('billtocountry', $shipto['billaddresscountry']);
		if ($shipto['email'] != '') $this->module->smarty->assign('email', $shipto['email']);

		if ($orderheader['paymethod'] != '' && $orderheader['paymethod'] != NULL)
			$this->module->smarty->assign('paymethod', $orderheader['paymethod']);
		if ($orderheader['delivery_date'] != '') $this->module->smarty->assign('deliverydate', $orderheader['delivery_date']);
		$this->module->smarty->assign('defaultdateformat', get_site_preference('defaultdateformat'));
		$this->module->smarty->assign('totalproduct', $this->FormatAmount($orderheader['totalproduct']));
		$this->module->smarty->assign('totaldiscount', $this->FormatAmount($orderheader['totaldiscount']));
		$this->module->smarty->assign('totalshipping', $this->FormatAmount($orderheader['totalshipping']));
		$this->module->smarty->assign('totaladmincost', $this->FormatAmount($orderheader['totaladmincost']));
		$this->module->smarty->assign('totalvat0amount', $this->FormatAmount($orderheader['totalvat0amount']));
		$this->module->smarty->assign('totalvat1amount', $this->FormatAmount($orderheader['totalvat1amount']));
		$this->module->smarty->assign('totalvat2amount', $this->FormatAmount($orderheader['totalvat2amount']));
		$this->module->smarty->assign('totalvat3amount', $this->FormatAmount($orderheader['totalvat3amount']));
		$this->module->smarty->assign('totalvat4amount', $this->FormatAmount($orderheader['totalvat4amount']));
		$totalvatamount = $this->FormatAmount($orderheader['totalvat0amount']
			+ $orderheader['totalvat1amount'] + $orderheader['totalvat2amount']
			+ $orderheader['totalvat3amount'] + $orderheader['totalvat4amount']);
		$this->module->smarty->assign('totalvatamount', $totalvatamount);
		$formattedamount = $this->FormatAmount($orderheader['totalproduct']
			- $orderheader['totaldiscount'] + $orderheader['totalshipping'] + $orderheader['totaladmincost']
			+ $orderheader['totalvat0amount'] + $orderheader['totalvat1amount'] + $orderheader['totalvat2amount']
			+ $orderheader['totalvat3amount'] + $orderheader['totalvat4amount']);
		$currency = $this->module->GetPreference('cartcurrency', 'Eur');
		$this->module->smarty->assign('total_amount', $currency . ' ' . $formattedamount);
		if ($orderheader['remark'] != '') $this->module->smarty->assign('remark', $orderheader['remark']);

		$mailbody = $this->module->ProcessTemplateFromDatabase('custmail_template');
		$cmsmailer->AddAddress($shipto['email']);
		$cmsmailer->SetBody($mailbody);
		$cmsmailer->IsHTML(true);
		$cmsmailer->SetSubject($this->module->GetPreference('custmail_subject', $this->module->Lang('yourorder')));
		$cmsmailer->Send();
		// Mail to customer send, now send one, if that is requested, to the order handler of the shop
		$admin_mail = $this->module->GetPreference('admin_emailaddress', '');
		if (isset($admin_mail) && $admin_mail != '') {
			$cmsmailer->reset();
			$cmsmailer->AddAddress($admin_mail);
			$mailbody = $this->module->ProcessTemplateFromDatabase('admin_template');
			$cmsmailer->SetBody($mailbody);
			$adminmailsubject = $this->module->GetPreference('admin_subject', $this->module->Lang('neworderplaced'));
			// Replace values in subject for more readability
			$adminmailsubject = str_ireplace('{$order_id}', $orderheader['order_id'], $adminmailsubject);
			$adminmailsubject = str_ireplace('{$shiptoname}', str_ireplace('<br>', '', $shipto['shiptoname']), $adminmailsubject);
			$adminmailsubject = str_ireplace('{$customer_id}', $orderheader['customer_id'], $adminmailsubject);
			$cmsmailer->SetSubject($adminmailsubject);
			$cmsmailer->Send();
		}
	}

	function HandlePayment($orderheader, $paymentdone = '', $returnid)
	{
		$gCms = cmsms();
		$config = $gCms->GetConfig();
		// Create new payment gateway class for the used payment method
		// This only works if the gateway is still installed, so check that again.
		// Check if payment module has been installed. If so, use the attributes to generate payment approval
		$paymsmodule = &$this->module->GetModuleInstance('SimplePayment');
		if ($paymsmodule) {
			$gateway_code = strtolower($orderheader['paymethod']);
			// Include the gateway so it can be used
			$gatewayfile = cms_join_path(dirname(__FILE__), '..', '..', 'SimplePayment', 'gw.' . $orderheader['paymethod'] . '.php');
			$gatewayfound = file_exists($gatewayfile);
			if ($gatewayfound) {
				include $gatewayfile;
				$pmsclass = 'pms' . $gateway_code . '_class';
				$pgw = new $pmsclass;
				switch ($gateway_code) {
					case 'ideal':
						$numberofcents = $pgw->getGateWayValue($gateway_code, 'numberofcents');
						$amounttopay = $orderheader['totalproduct'] * 100 -
							$orderheader['totaldiscount'] * 100 +
							$orderheader['totalshipping'] * 100 +
							$orderheader['totaladmincost'] * 100 +
							$orderheader['totalvat0amount'] * 100 +
							$orderheader['totalvat1amount'] * 100 +
							$orderheader['totalvat2amount'] * 100 +
							$orderheader['totalvat3amount'] * 100 +
							$orderheader['totalvat4amount'] * 100;
						$pgw->add_field('amount', round($amounttopay, $numberofcents));
						$pgw->add_field('purchaseID', $orderheader['order_id']);
						$pgw->add_field('description', 'Uw order');
						$pgw->add_field('itemNumber1', '1');
						$pgw->add_field('itemDescription1', 'Omschrijving artikel');
						$pgw->add_field('itemQuantity1', '1');
						$pgw->add_field('itemPrice1', round($amounttopay, $numberofcents));

						break;
					case 'idealpro':
						// iDEAL pro is also known as professional, advanced
						$text = '' . DIRECTORY_SEPARATOR;
						$text .= 'index.php?mact=SimplePayment,cntnt01,idealstep1,0';
						$text .= '&cntnt01order=' . $orderheader['order_id'] . '&cntnt01returnid=' . $returnid;
						redirect($config['root_url'] . $text);
						break;
					case 'paypal':
						// Set standard payment fields
						$pgw->fields['rm'] = '2';
						$pgw->fields['business'] = $pgw->getGateWayValue($gateway_code, 'business_email');
						$pgw->fields['return'] = $pgw->getGateWayValue($gateway_code, 'return');
						//$pgw->fields['return'] = $pgw->getGateWayValue ($gateway_code, 'return').'?order='.$orderheader['order_id'];
						$pgw->fields['cancel_return'] = $pgw->getGateWayValue($gateway_code, 'cancel_return');
						$pgw->fields['currency_code'] = $orderheader['currency'];
						$numberofcents = $pgw->getGateWayValue($gateway_code, 'numberofcents');
						switch ($numberofcents) {
							case '1':
								$orderheader['totalproduct'] = $orderheader['totalproduct'] * 10;
								$orderheader['totaldiscount'] = $orderheader['totaldiscount'] * 10;
								$orderheader['totalshipping'] = $orderheader['totalshipping'] * 10;
								$orderheader['totaladmincost'] = $orderheader['totaladmincost'] * 10;
								$orderheader['totalvat0amount'] = $orderheader['totalvat0amount'] * 10;
								$orderheader['totalvat1amount'] = $orderheader['totalvat1amount'] * 10;
								$orderheader['totalvat2amount'] = $orderheader['totalvat2amount'] * 10;
								$orderheader['totalvat3amount'] = $orderheader['totalvat3amount'] * 10;
								$orderheader['totalvat4amount'] = $orderheader['totalvat4amount'] * 10;
								break;
							case '0':
								$orderheader['totalproduct'] = $orderheader['totalproduct'] * 100;
								$orderheader['totaldiscount'] = $orderheader['totaldiscount'] * 100;
								$orderheader['totalshipping'] = $orderheader['totalshipping'] * 100;
								$orderheader['totaladmincost'] = $orderheader['totaladmincost'] * 100;
								$orderheader['totalvat0amount'] = $orderheader['totalvat0amount'] * 100;
								$orderheader['totalvat1amount'] = $orderheader['totalvat1amount'] * 100;
								$orderheader['totalvat2amount'] = $orderheader['totalvat2amount'] * 100;
								$orderheader['totalvat3amount'] = $orderheader['totalvat3amount'] * 100;
								$orderheader['totalvat4amount'] = $orderheader['totalvat4amount'] * 100;
								break;
							default:
								break;
						}

						// Prepare setting if only one line with amount or a cart situation with multiple lines possible
						if ($pgw->getGatewayValue('PayPal', 'cartstyle') == '0') {
							$pgw->fields['cmd'] = '_xclick';
							$pgw->fields['item_name'] = $pgw->getGateWayValue($gateway_code, 'itemdesc');
							$pgw->fields['amount'] = strtr(round($orderheader['totalproduct'], $numberofcents), ',', '.');
							if (round($orderheader['totaldiscount'], $numberofcents) > 0) {
								$pgw->fields['discount_amount'] = strtr(round($orderheader['totaldiscount'], $numberofcents), ',', '.');
							}
							if (round($orderheader['totalshipping'], $numberofcents) > 0) {
								$pgw->fields['shipping'] = strtr(round($orderheader['totalshipping'], $numberofcents), ',', '.');
							}
							if (round($orderheader['totaladmincost'], $numberofcents) > 0) {
								$pgw->fields['handling'] = strtr(round($orderheader['totaladmincost'], $numberofcents), ',', '.');
							}
							$totalvatamount = $orderheader['totalvat0amount'] + $orderheader['totalvat1amount'] + $orderheader['totalvat2amount']
								+ $orderheader['totalvat3amount'] + $orderheader['totalvat4amount'];
							if (round($totalvatamount, $numberofcents) > 0) {
								$pgw->fields['tax'] = strtr(round($totalvatamount, $numberofcents), ',', '.');
							}
						} else {
							// Details to be prepared for PayPal 'cart'
							$pgw->fields['cmd'] = '_cart';
							$pgw->fields['upload'] = '1';
							$orderlines = array();
							$orderlines = $this->GetOrderLines($orderheader['order_id']);
							$line = 1;
							foreach ($orderlines as $orderline) {
								$pgw->fields['item_name_' . $line] = $orderline->productname;
								if ($orderline->attributename != '') {
									$pgw->fields['item_name_' . $line] .= ' (' . $orderline->attributename . ')';
								}
								$pgw->fields['quantity_' . $line] = $orderline->qty;
								$pgw->fields['amount_' . $line] = strtr($orderline->price, ',', '.');
								$line++;
							}
							if (round($orderheader['totalshipping'], $numberofcents) > 0) {
								$pgw->fields['shipping_1'] = strtr(round($orderheader['totalshipping'], $numberofcents), ',', '.');
							}
							if (round($orderheader['totaladmincost'], $numberofcents) > 0) {
								$pgw->fields['handling_1'] = strtr(round($orderheader['totaladmincost'], $numberofcents), ',', '.');
							}
							$totalvatamount = $orderheader['totalvat0amount'] + $orderheader['totalvat1amount'] + $orderheader['totalvat2amount']
								+ $orderheader['totalvat3amount'] + $orderheader['totalvat4amount'];
							if (round($totalvatamount, $numberofcents) > 0) {
								$pgw->fields['tax_1'] = strtr(round($totalvatamount, $numberofcents), ',', '.');
							}
						}

						break;
					default:
						$pgw->submit_payment($orderheader);
						return $paymentdone = 'done';
						break;
				}
				// All fields prepared, make connection to gateway
				$pgw->submit_payment();
				return $paymentdone = 'done';
			} else {
				return $paymentdone = 'notdone';
			}
		} else {
			return $paymentdone = 'notdone';
		}
	}

	function SetExpectedDeliveryDate($order_id)
	{
		// Order has been paid, calculate and set the expected delivery date based upon delivery method
		$db = cmsms()->GetDb();
		$orderheader = array();
		$orderheader = cartms_utils::GetOrderHeader($order_id);
		$shipmode = $orderheader['shipmode'];
		$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_cartms_shippingprovider WHERE shipprovcode = ? ';
		$dbresult = $db->Execute($query, array($shipmode));

		if ($dbresult && $row = $dbresult->FetchRow()) {
			$shipworkdays = $row['shipworkdays'];
			if ($shipworkdays == 0) {
				$newdeliverydate = time();
			} else {
				$newdeliverydate = cartms_utils::CalculateDueDate(time(), $shipworkdays);
			}
			// Now that new expected delivery date is known, update the order
			$query = 'UPDATE ' . cms_db_prefix() . 'module_cartms_orders SET delivery_date = ' . $db->DBTimeStamp($newdeliverydate) . ',
				modified_date = ' . $db->DBTimeStamp(time()) . ' WHERE order_id = ?';
			$dbresult = $db->Execute($query, array($order_id));
		}
	}

	/*---------------------------------------------------------
	   SwitchStatus( $params )
	   This function:
	   - updates the inventory if according to setting in ShopMS
	   - sets the status of the order to new status
	   - Using parm newstatus, it is possible to reset to earlier status
	  ---------------------------------------------------------*/
	function SwitchStatus($params)
	{
		// Initialize the Database
		$db = cmsms()->GetDb();
		if (isset($params['id'])) {
			$id = $params['id'];
		}

		$query = 'UPDATE ' . cms_db_prefix() . 'module_cartms_orders SET status = ?,
			modified_date = ' . $db->DBTimeStamp(time()) . ' WHERE order_id = ?';
		switch ($params['oldstatus']) {
			case 'INT':
				// Order has been initiated
				if (isset($params['newstatus'])) {
					$newstatus = $params['newstatus'];
				} else {
					$newstatus = 'CNF';
				}
				break;
			case 'CNF':
				// Order has been confirmed
				if (isset($params['newstatus'])) {
					$newstatus = $params['newstatus'];
				} else {
					$newstatus = 'PAY';
					$this->SetExpectedDeliveryDate($params['order_id']);
				}
				break;
			case 'PAY':
				// Order has been paid
				if (isset($params['newstatus'])) {
					$newstatus = $params['newstatus'];
				} else {
					$newstatus = 'SHP';
				}
				break;
			case 'SHP':
				// Order has been shipped/collected
				if (isset($params['newstatus'])) {
					$newstatus = $params['newstatus'];
				} else {
					$newstatus = 'INV';
				}
				break;
			default:
				break;
		}
		if ($newstatus != '') {
			$db->Execute($query, array($newstatus, $params['order_id']));
			// Check if inventory to be decreased
			$this->module->InventoryDecrease($params['order_id'], $newstatus);
		}

		// Redirect the user to the default admin screen if requested, else leave up to calling program
		if (isset($params['active_tab'])) {
			$params = array('active_tab' => 'order', 'orderstatus' => $newstatus);
			$this->module->Redirect($id, 'defaultadmin', $returnid, $params);
		}
	}

	function StorePaymentInfo($params)
	{
		// In the fourth step of the order processing, the visitor has entered a payment method
		// Now that we know it, store it in the order
		$db = cmsms()->GetDb();
		//$dict = NewDataDictionary($db);

		$paymentmethod = $params['paymentmethod'];
		$order_id = $params['order_id'];

		$query = 'UPDATE ' . cms_db_prefix() . 'module_cartms_orders set paymethod = ? WHERE order_id = ?';
		$db->Execute($query, array($paymentmethod, $order_id));
	}

	function FormatAmount($amount)
	{
		$formatdecimals = $this->module->GetPreference('numberformatdecimals', '2');
		$formatdecimal_point = $this->module->GetPreference('numberformatdec_point', ',');
		$formatthousand_sep = $this->module->GetPreference('numberformatthousand_sep', '.');
		return number_format($amount, $formatdecimals, $formatdecimal_point, $formatthousand_sep);
	}
}
