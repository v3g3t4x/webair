<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

class SchedulingResponse {
	
	public $schedulingId=''; 
    public $commandId='';
    public $schedulingType='';
	public $schedulingValue='';  
	public $schedulingTypeDescription='';
    public $startTimeStamp=''; 
    public $endTimeStamp='';
	public $priority;
	public $description;
	public $validityRangeStart;
	public $validityRangeEnd;
	public $commandDescription;
}//End class...

?>