<?php

$gCms = cmsms();
if (!is_object($gCms)) exit;

//TODO: check template logic template | template_name ?
if (!$this->CheckPermission('Use SimpleCart')) {
	$params = array('tab_message' => $this->Lang('needpermission'), 'active_tab' => $params['active_tab']);
	$this->Redirect($id, 'defaultadmin', '', $params);
}

if (!isset($params['template']) || !isset($params['defaultprefname'])) {
	$params = array('tab_message' => $this->Lang('internalerror'), 'active_tab' => $params['active_tab']);
	$this->Redirect($id, 'defaultadmin', '', $params);
}


$this->SetPreference($params['defaultprefname'], $params['template']);

$params = array('tab_message' => $this->Lang('defaulttempalteupdated'), 'active_tab' => $params['active_tab']);
$this->Redirect($id, 'defaultadmin', '', $params);
