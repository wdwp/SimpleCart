<?php
#-------------------------------------------------------------------------
# Module: Cart Made Simple - An Order Intake module for CMS - CMS Made Simple
# Copyright (c) 2008 by Alan Ryan
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

if (isset($params['cancel'])) {
	$params = array('active_tab' => 'cart_template');
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}

if (!$this->CheckPermission('Modify Templates')) {
	$params = array('tab_message' => $this->Lang('accessdenied'), 'active_tab' => $params['defaulttemplatepref']);
	$this->Redirect($id, 'defaultadmin', '', $params);
}

// Show any found errors.
if (isset($params['errors'])) {
	echo $this->ShowErrors($params['errors']);
}

// For new templates we will pass userauth_ or cart_ or nnnn_ tp prefix to the template names
// in 'defaulttemplatepref' param from this we can separate them into cart / auth  / etc... templates
// tabs are identified by their TYPE_ prefix
if (isset($params['defaulttemplatepref'])) {
	$prefix = $params['defaulttemplatepref']; //prefix for new template of type 'defaulttemplatepref'
} else {
	$template = $params['template'];    //just editing; full template name to be retrieved from db is passed in
}

$params['origaction'] = $params['mode'];
$contents = "";
// Are we adding or updating a template
if (strstr($params['mode'], 'add')) {

	if ($params['mode'] == 'addtodb') { // Submit button has been hit
		addTemplate($this, $prefix . $template);
		$params = array('tab_message' => $this->Lang('templateadded'), 'active_tab' => 'cart_template');
		$this->Redirect($id, 'defaultadmin', '', $params);
		return;
	}

	$this->smarty->assign('formstart', $this->CreateFormStart($id, 'edittemplate', $returnid, 'post', '', false, '', $params));
	$this->smarty->assign('templatename', $this->CreateInputText($id, 'templatename', "", 40));
	$this->smarty->assign(
		'hidden',
		$this->CreateInputHidden($id, 'mode', 'addtodb')
	);

	if (!isset($_SESSION['templatecontent'])) {
		// Retrieve base template based upon the prefix passed from the list templates
		$fn = cms_join_path(dirname(__FILE__), 'templates', $prefix . 'template.tpl');
		if (file_exists($fn)) {
			$contents = @file_get_contents($fn);
		}
	} else
		$contents = $_SESSION['templatecontent'];
} else {
	if ($params['mode'] == 'updatedb') { //submitted
		addTemplate($this, $template);
		if (isset($params['submit'])) {
			$params = array('tab_message' => $this->Lang('templateupdated'), 'active_tab' => 'cart_template');
			$this->Redirect($id, 'defaultadmin', '', $params);
			//return;
		} else {
			$this->smarty->assign('message', $this->Lang('templateupdated'));
		}
	}

	$this->smarty->assign(
		'formstart',
		$this->CreateFormStart($id, 'edittemplate', $returnid, 'post', '', false, '', $params)
	);

	$tmp =  substr($template, strpos($template, "_") + 1);
	$this->smarty->assign('templatename', $tmp);

	$this->smarty->assign(
		'hidden',
		$this->CreateInputHidden($id, 'mode', 'updatedb') .
			$this->CreateInputHidden($id, 'templatename', $template)
	);
	$this->smarty->assign('apply', $this->CreateInputSubmit($id, 'apply', $this->Lang('apply')));

	if (!isset($_SESSION['templatecontent']))
		$contents = $this->GetTemplate($template);
	else
		$contents = $_SESSION['templatecontent'];
}

//$this->smarty->assign('title',$params['title']);
$this->smarty->assign('prompt_templatename', $this->Lang('prompt_templatename'));
$this->smarty->assign('prompt_template', $this->Lang('prompt_template'));

$this->smarty->assign('template', $this->CreateTextArea(false, $id, $contents, 'templatecontent', '', '', '', '', '', 20, '', '', 'style="width:100%"'));

$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('submit')));
$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));

$this->smarty->assign('formend', $this->CreateFormEnd());

unset($_SESSION['templatecontent']);

echo $this->ProcessTemplate('edittemplate.tpl');
