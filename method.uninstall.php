<?php
// Remove the tables
$dict = NewDataDictionary($db);
$sqlarray = $dict->DropTableSQL(cms_db_prefix() . 'module_cartms_carts');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->DropTableSQL(cms_db_prefix() . 'module_cartms_orders');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->DropTableSQL(cms_db_prefix() . 'module_cartms_order_lines');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->DropTableSQL(cms_db_prefix() . 'module_cartms_shippingprovider');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->DropTableSQL(cms_db_prefix() . 'module_cartms_newuserpasswords');
$dict->ExecuteSQLArray($sqlarray);
// Remove the sequences
$db->DropSequence(cms_db_prefix() . 'module_cartms_carts_seq');
$db->DropSequence(cms_db_prefix() . 'module_cartms_orders_seq');
$db->DropSequence(cms_db_prefix() . 'module_cartms_order_lines_seq');
$db->DropSequence(cms_db_prefix() . 'module_cartms_shippingprovider_seq');

// Remove all preferences for this module
$this->RemovePreference();

// Remove templates that belong to this module
$query = 'DELETE FROM ' . cms_db_prefix() . 'module_templates WHERE module_name = ?';
$db->Execute($query, array('SimpleCart'));

// Remove the permissions
$this->RemovePermission('Use SimpleCart');
$this->RemovePermission('Modify SimpleCart');

// Remove files
$dirname = __DIR__ . '/../../uploads/SimpleCart';
array_map('unlink', glob("$dirname/*.*"));
rmdir($dirname);

// Log the uninstall in admin audit trail
$this->Audit(0, $this->Lang('friendlyname'), $this->Lang('uninstalled'));
