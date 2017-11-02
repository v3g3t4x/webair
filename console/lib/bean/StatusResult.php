<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

class StatusResult {
	 
    public $deviceId='';
    public $deviceName=''; 
    public $uniqueId=''; 
    public $status='';
	public $description='';
	public $publicIp='';
	public $privateIp='';
	public $geolocation='';
	public $internalTemp='';
	public $internalHum='';
	public $externalTemp='';
	public $externalHum='';
	
	
}//End class...

?>