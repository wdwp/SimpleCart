<?php
#-------------------------------------------------------------------------
# Module: Cart Made Simple - An Order Intake module for CMS - CMS Made Simple
# Copyright (c) 2008 by Alan Ryan
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
# The module's homepage is: http://dev.cmsmadesimple.org/projects/cartms/
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
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL#
#-------------------------------------------------------------------------

$gCms = cmsms();
if (!is_object($gCms)) exit;

$config = $gCms->config;
$themeObject = \cms_utils::get_theme_object();

$templates = array();
$templates2 = array();
$dbtemplates = $this->ListTemplates();
$rowclass = 'row1';



if (!isset($params['template'])) {
	echo $this->ShowErrors($this->Lang('Params', array('Internal Error')));
	return;
}

$template_type = $params['template'];

$falseimage1 = $themeObject->DisplayImage('icons/system/false.gif', $this->Lang('default'), '', '', 'systemicon');
$trueimage1 = $themeObject->DisplayImage('icons/system/true.gif', $this->Lang('makedefault'), '', '', 'systemicon');
$defaulttemplateprefname = 'default_' . $template_type . "template";

foreach ($dbtemplates as $template) {
	if (!strchr($template, $template_type))
		continue;

	$onerow = new stdClass();
	$onerow->rowclass = $rowclass;
	$tmp = substr($template, strlen($template_type));
	$onerow->name = $this->CreateLink($id, 'edittemplate', $returnid, $tmp, array('template' => $template, 'mode' => 'edit'));
	$onerow->editlink = $this->CreateLink($id, 'edittemplate', $returnid, $themeObject->DisplayImage('icons/system/edit.gif', $this->Lang('edittemplate'), '', '', 'systemicon'), array('template' => $template));
	$default = ($this->GetPreference($defaulttemplateprefname) == $template) ? true : false;

	if ($default) {
		$onerow->default = $trueimage1;
		$onerow->deletelink = '';
	} else {
		$onerow->default = $this->CreateLink(
			$id,
			'makedefaulttemplate',
			$returnid,
			$falseimage1,
			array(
				'template' => $template,
				'defaultprefname' => $defaulttemplateprefname,
				'active_tab' => $template_type . "template"
			)
		);
		$onerow->deletelink = $this->CreateLink(
			$id,
			'deletetemplate',
			$returnid,
			$themeObject->DisplayImage('icons/system/delete.gif', $this->Lang('deletetemplate'), '', '', 'systemicon'),
			array('defaultprefname' => $defaulttemplateprefname, 'active_tab' => $template_type . "template", 'templatename' => $template),
			$this->Lang('areyousure')
		);
	}

	$templates2[$template] = $template;
	$templates[] = $onerow;

	($rowclass == "row1" ? $rowclass = "row2" : $rowclass = "row1");
}

$this->smarty->assign('formstart', $this->CreateFormStart($id, 'changetemplate', $returnid));
$this->smarty->assign('defaultprompt', $this->Lang('defaultprompt'));
$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('submit')));
$this->smarty->assign('formend', $this->CreateFormEnd());

$this->smarty->assign_by_ref('items', $templates);

$this->smarty->assign('templatenametext', $this->Lang('templatenametext'));

$this->smarty->assign(
	'addlink',
	$this->CreateLink(
		$id,
		'edittemplate',
		$returnid,
		$themeObject->DisplayImage('icons/system/newobject.gif', $this->Lang('addtemplate'), '', '', 'systemicon'),
		array(),
		'',
		false,
		false,
		''
	) . ' ' .
		$this->CreateLink($id, 'edittemplate', $returnid, $this->Lang('addtemplate'), array('mode' => 'add', 'defaulttemplatepref' => $template_type), '', false, false, 'class="pageoptions"')
);
echo $this->ProcessTemplate('templatelist.tpl');
