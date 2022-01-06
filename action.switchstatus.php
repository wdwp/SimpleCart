<?php
// Switch the order status to new one
// No direct access
$gCms = cmsms();
if (!is_object($gCms)) exit;

// Check permission
if (!$this->CheckPermission('Modify SimpleCart')) {
	// Show an error message
	echo $this->ShowError($this->Lang('access_denied'));
}
// User has sufficient privileges
else {
	$params['id'] = $id;
	switch ($params['oldstatus']) {
		case 'INT':
			$this->orders->SwitchStatus($params);
			//$params = array('active_tab' => 'order', 'orderstatus' => 'CNF');
			break;
		case 'CNF':
			$this->orders->SwitchStatus($params);
			//$params = array('active_tab' => 'order', 'orderstatus' => 'PAY');
			break;
		case 'PAY':
			$this->orders->SwitchStatus($params);
			//$params = array('active_tab' => 'order', 'orderstatus' => 'SHP');
			break;
		case 'SHP':
			// Generate an invoice number
			$invoiceno = $this->PrepareInvoiceNo();

			$query = 'UPDATE `' . cms_db_prefix() . 'module_cartms_orders` SET `status` = ?,
				`invoiceno` = ?, `modified_date` = ' . $db->DBTimeStamp(time()) . '
				WHERE `order_id` = ?';
			$db->Execute($query, array('INV', $invoiceno, $params['order_id']));
			// Prepare the invoice
			$params['type'] = 'invoice_prep';
			$documenttype = $this->GetPreference('extdoc_invoice', 'invoice_sample');
			include(cms_join_path(dirname(__FILE__), 'library', 'extdoc.' . $documenttype . '.php'));
			// Check if inventory to be decreased
			$this->InventoryDecrease($params['order_id'], 'INV');

			//$params = array('active_tab' => 'order', 'orderstatus' => 'INV');
			// TODO: Send the invoice
			break;
		case 'status_active':
			$query = 'UPDATE `' . cms_db_prefix() . 'module_cartms_shippingprovider` SET `active` = ? 
			WHERE `shipprov_id` = ?';
			$db->Execute($query, array(0, $params['shipprov_id']));
			break;
		case 'status_inactive':
			$query = 'UPDATE `' . cms_db_prefix() . 'module_cartms_shippingprovider` SET `active` = ? 
				WHERE `shipprov_id` = ?';
			$db->Execute($query, array(1, $params['shipprov_id']));
			break;
		default:
			break;
	}

	//Redirect the user to the default admin screen
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}
