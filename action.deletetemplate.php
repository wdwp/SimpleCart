<?php

$active_tab = $params['active_tab'];

if (!$this->CheckPermission('Modify Templates')) {
	$params = array('tab_message' => $this->Lang('accessdenied'), 'active_tab' => $active_tab);
	$this->Redirect($id, 'defaultadmin', '', $params);
}

if (!(isset($params['templatename']))) {
	$params = array('errors' => 'Internal Error', 'tab_message' => $this->Lang('accessdenied'), 'active_tab' => $active_tab);
	$this->Redirect($id, 'defaultadmin', '', $params);
}

$this->DeleteTemplate($params['templatename']);

$params = array('tab_message' => $this->Lang('templatedeleted'), 'active_tab' => $active_tab);
$this->Redirect($id, 'defaultadmin', '', $params);
