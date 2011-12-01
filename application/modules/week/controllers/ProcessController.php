<?php
/**
 * Month_PorcessController.php
 * 月報模組下處理server-side程序流程
 * 
 * @date 2011-11-10
 * @author k1
 * 1. 初始化程式。
 * 
 * --------------------------------------------------
 */


class Month_PorcessController extends Zend_Controller_Action
{
	private $_logger;
	private $_config;
	
	
	function init()
	{
		$this->_logger = $this->getInvokeArg('bootstrap')->getResource('logger');
		$this->_config = new Application_Model_Configure();
	}    
	
	/**
	 * feature::
	 * called by::
	 * /month/xxxx/xxxxx 
	 */
	public function tempAction()
	{
		$this->_config->fetchAll();
	}
	public function indexAction()
	{
	}
}
?>