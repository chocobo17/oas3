<?php
/**
 * ErrorController.php
 * 系統處理例外事件與程式錯誤顯示與引導
 * 
 * @date 2011-11-01
 * @author k1
 * feature::
 * 1. 顯示exception
 * 2. 引導至error/error.html
 * 
 * --------------------------------------------------
 * 配合引入Zend_Application架構！
 */


class ErrorController extends Zend_Controller_Action
{
    public function init()
    {
	}

    public function defaultAction()
    {
        $errors = $this->_getParam('error_handler');
		$exceptions = $errors->exception;
        
		switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
				$this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
				
			 	$content = "The page you requested was not found. ";
				$content .= $exceptions->getMessage();
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
				
				$content = "Other problems.";
                break;
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
			//顯示錯誤呼叫堆疊
			$this->view->trace = $exceptions->getTraceAsString();
	 
	        // pass the request to the view
	        $this->view->request   = $errors->request;
			
			// pass the environment to the view script so we can conditionally 
	        // display more/less information
	        $this->view->env       = $this->getInvokeArg('env'); 
        }
		
        // Clear previous content
        $this->getResponse()->clearBody();
        $this->view->content = $content;
		
//        // Log exception, if logger available
//        if ($log = $this->getLog()) {
//            $log->crit($this->view->message, $errors->exception);
//        }
    }
	
	/**
	 * 
	 * @param
	 * @return 
	 */
    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if($bootstrap->hasResource('logger') ) {
			$log = $bootstrap->getResource('Log');
        	return $log;
        }
        return false;
    }
}
?>