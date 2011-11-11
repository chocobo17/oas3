<?php
/**
 * Month_ManageController.php
 * 月報模組下預設介面顯示
 * 
 * @date 2011-11-10
 * @author k1
 * 1. 初始化程式。
 * 
 * --------------------------------------------------
 */


class Month_ManageController extends Zend_Controller_Action
{
	private $_logger;
	
	function init()
	{
		$this->_logger = $this->getInvokeArg('bootstrap')->getResource('logger');
	}    
	
	/**
	 * feature::
	 * called by::
	 * /month/xxxx/xxxxx 
	 */
	public function tempAction()
	{
	}
}
?>