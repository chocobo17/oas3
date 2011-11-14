<?php
/**
 * Month_AjaxLoadController.php
 * 月報模組下處理部份ajax載入區塊流程
 * 
 * @date 2011-11-10
 * @author k1
 * 1. 初始化程式。
 * 
 * --------------------------------------------------
 */


class Month_AjaxLoadController extends Zend_Controller_Action
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
	public function indexAction()
	{
	}
}
?>