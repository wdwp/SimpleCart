<?php
/**
 * Function to omit some menu items
 */
function filterMenu($omitarray){

	global $gCms;

	$config = $gCms->config;
	$themeObject = $gCms->variables['admintheme'];
	$menudropdown = array();
	$haselements  = count($omitarray);
	foreach ($themeObject->menuItems as $key=>$menuItem)
	{
		if ($menuItem['parent'] == -1)
		{
			if($haselements > 0){
				if(!in_array($key,$omitarray))
				$menudropdown[$key]= $key;
			}
			else{
				$menudropdown[$key]= $key;
			}

		}
	}

	return $menudropdown;

}


/*
 * This is lazy, I should have made 2 functions
 * @params self = this
 * @param template_prefix - full name or prefix for template
 */
function addTemplate($_self, $template_prefix){


    global $params, $id;
    $active_tab = "";
    $template_name ="";

    //update or add
    if($params['mode']=='updatedb')
        $active_tab =  substr($params['templatename'],0,strpos($params['templatename'],"_")+1) . "template";
    else
        $active_tab = $template_prefix  . "template";
        
    if( isset( $params['cancel'] ) ) {        
        $_params = array('active_tab'=>$active_tab);
        $_self->Redirect($id, 'defaultadmin', '', $_params);
    }
    
    
    if( isset( $params['templatename'] ) && strcmp($params['templatename'],"")!=0) {

        if($params['mode']=='updatedb')
            $template_name  = trim($params['templatename']);
        else
            $template_name  = $template_prefix . trim($params['templatename']);        

    }
    else {
        // No name given for template
        $_SESSION['templatecontent']=  $params['templatecontent'];
        $action =  $params['origaction'];
        $params['errors'] = $_self->Lang("notitlegiven");
        $params['active_tab']=$active_tab;
        $params['mode']  = $action;
        $_self->Redirect($id,'edittemplate','',$params);
        return;
    }

    // Is it an add or an update?
    if($params['mode']!='updatedb' && $_self->GetTemplate($template_name)!="") {
        $_SESSION['templatecontent']=  $params['templatecontent'];
        $action =  $params['origaction'];
        $params['errors'] = $_self->Lang("templateexists");
        $params['active_tab']=$active_tab;
        $params['mode']  = $action;
        $_self->Redirect($id,'edittemplate','',$params);
        return;
    }
    else {
        $_self->SetTemplate($template_name,$params['templatecontent']);
    }

}

?>