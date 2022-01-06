<?php
class DeleteNotPaidOrdersTask implements CmsRegularTask
{
  const  LASTEXECUTE_SITEPREF   = 'DeleteNotPaidOrders_lastexecute';


  public function get_name()
  {
    return 'Delete Not Paid CartMS Orders';
  }


  public function get_description()
  {
    return 'Delete Not Paid Orders';
  }


  public function test($time = '')
  {
    // do we need to do this task.
    // we only do it daily.
    if( !$time ) $time = time();
    $last_execute = get_site_preference(self::LASTEXECUTE_SITEPREF,0);
    //if( ($time - 24*60*60) >= $last_execute ) {
    if( ($time ) >= $last_execute ) {
			return TRUE;
    } 
    return FALSE;
  }


  public function execute($time = '')
  {
    if( !$time ) $time = time();
    
    // do the task.
    // -----
		global $gCms;
		$db = cmsms()->GetDb();

		$cartms =& cmsms()->modules['SimpleCart']['object'];
		$orderwaitdays = $cartms->GetPreference('orderswaitdays', 30);
		// If set to more than three years, don't perform clean up 
		if ($orderwaitdays > 3*365) return true;
		$orderstatus = 'INT';
		// Prepare current date minus orderwaitdays
    $today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    $timeStamp = date("Y-m-d", $today - (86400 * $orderwaitdays));
		
		// Prepare the number of orders being removed and log it
		$query = 'SELECT COUNT(*) FROM '.cms_db_prefix().'module_cartms_orders 
			WHERE status IN ("INT", "CNF") AND create_date < ?';
		$dbresult = $db->Execute($query, array($timestamp) );
		$ordercount = 0;
		if ($dbresult && $row = $dbresult->FetchRow()) { 
			$ordercount = $row['COUNT(*)'];
		}
		if ($ordercount > 0) {
			$audittext = $cartms->Lang('pseudocrontaskdeleteorders', $ordercount);
			audit(0, 'Pseudocron task: DeleteNotPaidOrders', $audittext);
			$query = 'DELETE FROM '.cms_db_prefix().'module_cartms_orders 
				WHERE status IN ("INT", "CNF") AND create_date < ?';
			$dbresult = $db->Execute($query, array($timestamp) );
		}
		// ----- 
		// Process remaining part of task
    return TRUE;
  }


  public function on_success($time = '')
  {
    if( !$time ) $time = time();
    set_site_preference(self::LASTEXECUTE_SITEPREF,$time);
  }


  public function on_failure($time = '')
  {
    if( !$time ) $time = time();
    // nothing here.
  }
}
