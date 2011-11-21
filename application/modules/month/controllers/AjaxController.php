<?php
/**
 * Month_AjaxController.php
 * 月報模組下處理部份ajax請求流程
 * 
 * @date 2011-11-10
 * @author k1
 * 1. 初始化程式。
 * 
 * --------------------------------------------------
 */


class Month_AjaxController extends Zend_Controller_Action
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
	public function sessionmonthAction()//月份切換以及年切換
	{
	}
	public function processAction()//新增或者刪除進行頁面的重新整理
	{
		
	}
}
?>