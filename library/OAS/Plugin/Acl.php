<?php
/**
 * date 2010-06-22
 * @author San
 * 
 * 需求：
 * 1. 設定role、resource、role_acces_map項目。
 * 2. 根據Auth傳入的身份別判斷該身份別所屬權限表設定成全域陣列變數。
 * 
 * 主要功能：
 * 1. 資料庫存取role、access、role_access等table。
 * 2. 組成Zend_Registry::set('ACL'); 直接產生對應選單。
 * 
 * 2011-04-08
 * @author San
 * 修改原先使用方式，將Sanhung_Acl正式改為Sanhuang_Plugin_Acl
 * 主要功能從資料庫讀入建立特定身份別的ACL清單
 * 
 * 內部處理：
 * 1. 判斷角色若為預設角色（guest身份，未登入）則僅取出access為選單部份的資源
 * 2. 根據role對應role_access找可使用的permission
 * 3. 建立MENUS應用！
 * 
 *  
 */

class Sanhuang_Plugin_Acl extends Zend_Controller_Plugin_Abstract{
	
	private $defaultPath = array(
							'/default/index/index'
						   );
	
	//設定略過判斷區塊（後台首頁、登入、登出流程）
	private $adminArray = array(
							'/system/index/main',
							'/system/index/check',
							'/system/index/logout'
						  );
	
	//設定掠過判斷區塊（前台首頁、會員註冊、會員登入、登出相關流程）
	
	private $_role;
	private $_roleAccess;
	private $_access;
	
	function __construct(){
		//判斷若已登入驗證成功，則啟用ACL設定選單機制！
		$this->SESSION = new Zend_Session_Namespace( APPLICATION_SESSION );
	}
	
	
	/**
	 * feature::設定預設群組（系統管理者、訪客）、資源對應權限
	 * 1. 後台若登入為資料庫使用者可以直接操作前台
	 * 2. 前台使用者若轉入後台會判斷身分！
	 * 3. 後台使用者若為超級使用者轉到前台，則直接登出。 
	 * 
	 * 目前將前後台SESSION->AUTH內容判斷統一集中於此
	 * 尚未對resource做設定及細部權限判斷
	 */
	public function preDispatch()
	{
		$this->_role = new System_Model_Role();
		$this->_roleAccess = new System_Model_RoleAccess();
		$this->_access = new System_Model_Access();
		
		$this->M = $this->getRequest()->getModuleName();
		$this->C = $this->getRequest()->getControllerName();
		$this->A = $this->getRequest()->getActionName();
		$this->PATH = '/'.$this->M.'/'.$this->C.'/'.$this->A;
		
		// $access = $this->_access->fetchRow("accesstype='adminmenu' AND accessKey = '{$this->PATH}'");
		
	}
	
	/**
	 * feature::若在Action內設定特定變數，則直接重導或(Ajax)給予重導判斷
	 */
	public function postDispatch()
	{
		//偵測是否已發生例外，導向到
		// $this->getResponse()->isException() Bool
		// $this->getResponse()->isRedirect() Bool
		
		//若已設定重導向路徑，則取消重導向
		
		//如何處理當Action為Ajax類型發生例外錯誤的狀況！
		// 1. 是否能用php Header方式重導頁面？
		// 2. 將例外內容儲存於SESSION，然後傳遞變數給Javascript做處理導向！
		// 2. 將例壞事件整個回應給Javascript
	}
}
?>