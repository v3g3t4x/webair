<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';


class Response {
	 
    public $errorDescription; 
    public $message; 
    public $resultCode; 
    public $resultObj; 
    public $systemTime;
    
 	public function Response() {
 		 $this->systemTime=round(microtime(true) * 1000);
 	}
	
}//End class...

?>