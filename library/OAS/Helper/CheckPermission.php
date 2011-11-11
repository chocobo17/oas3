<?php
/**
 * date 2010-06-22
 * @author San
 * 
 * 需求：
 * 1. 每次dispatch到特定程序前，預先檢查ACL規則。
 * 2. [初期]僅判斷isAllow ('view')權限。
 *
 * @date 2011-04-08
 * @author San
 * 
 * 主要功能：
 * 1. 取得ACL全域陣列變數
 * 2. 針對特殊處理判斷是否需要檢查ACL！
 * 
 * 主要函式分類：
 * 1. 
 * 
 * 內部處理：
 * 1. 
 *  
 */


class OAS_Helper_CheckPermission extends Zend_Controller_Action_Helper_Abstract 
{
	private $_acl;
	
	private $_role;
	
	function __construct($acl)
	{
		$this->_acl = Zend_Registry::get('acl');
		
		$session = new Zend_Session_Namespace( APPLICATION_SESSION );
		$this->_role = $session->AUTH->role;
	}	
	
	public function check()
	{
		// $this->_acl->is
	}
}
?>