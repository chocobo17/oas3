<?php
/**
 * AjaxController.php
 * 系統預設表單處理功能：首頁、登入驗證處理流程
 * --------------------------------------------------
 * @date 2011-11-01
 * @author k1
 * 
 * feature::
 * 1. 處理驗證機制。
 * 2. 
 * 
 */

class AjaxController extends Zend_Controller_Action
{
	private $_logger;

    public function init()
    {
		$this->_logger = $this->getInvokeArg('bootstrap')->getResource('logger');
	}

	/**
	 * feature::系統使用者登入驗證檢查機制 
	 * login process used by jQueryAjax
	 * 判斷是否為第一次登入，若是必須將帳號狀態改成啟用。
	 * called by::IndexController
	 * @param post:account passwd 取得傳遞的帳號密碼並判斷來自哪個功能！	
	 * @return JSON
	 */
	public function checkAction()
	{
    	if( $this->extend['SESSION']->unlogin ){
			$array=array(
				'callback'=>TRUE,
    			'result'=>'fail',
    			'msg'=>'您已超過限制登入嘗試次數！請於10分鐘後再次測試，或聯繫系統管理員。'
			);
			
			$this->_logger->notice('暫時禁止登入');
    	}
		else{
    	
		$adapter = new Zend_Auth_Adapter_DbTable();
		
		$adapter->setTableName('user')
				->setIdentityColumn('userId')
				->setCredentialColumn(  'password')
				->setCredentialTreatment('MD5(?)');
				
		$res = $adapter->setIdentity( $this->getRequest()->getParam('username') )
					   ->setCredential( $this->getRequest()->getParam('password') )
					   ->authenticate();
		
		$try_times = 5 - (int)$this->extend['SESSION']->numberOfPageRequests;
		
		switch( $res->getCode() )
		{
			case Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS:
				//帳號重複！顯示錯誤！
				$array=array(
					'callback'=>TRUE,
	    			'result'=>'fail',
	    			'msg'=>'登入帳號出現重複狀況！ 請確認此帳號：'.$_REQUEST['username']
				);
				
				$this->_logger->notice('登入帳號有問題！');
			break;
			case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
				//查無帳號！
				$array=array(
					'callback'=>TRUE,
	    			'result'=>'fail'
				);

				if( $try_times > 0){
					
					$this->extend['SESSION']->numberOfPageRequests++;

					$array['msg']='查無此登入帳號，請重新輸入（您剩下'.$try_times.'嘗試機會）';
					
					$this->_logger->notice('登入錯誤密碼'.$this->extend['SESSION']->numberOfPageRequests.'次');
					
				}else{
					$this->extend['SESSION']->unlogin = 'true';
					$this->extend['SESSION']->setExpirationSeconds(600, 'unlogin');
					$this->extend['SESSION']->lock();
					//將SESSION鎖住並防止限制在次常式登入時間內寫入！
					
					$array['msg']='您已超過限制登入嘗試次數！請於10分鐘後再次測試，或聯繫系統管理員。';
					
					$this->_logger->notice('暫時禁止登入');
				}
			
			break;
			case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
			//密碼比對錯誤！
			
				$array=array(
					'callback'=>TRUE,
	    			'result'=>'fail'
				);

				if( $try_times > 0){
					$this->extend['SESSION']->numberOfPageRequests++;

					$array['msg']='密碼驗證錯誤，請重新輸入（您剩下'.$try_times.'嘗試機會）';

					$this->_logger->notice('登入嘗試'.$this->extend['SESSION']->numberOfPageRequests.'次');
					
				}else{
					$this->extend['SESSION']->unlogin = TRUE;
					$this->extend['SESSION']->setExpirationSeconds(600, 'unlogin');
					$this->extend['SESSION']->lock();
					//將SESSION鎖住並防止限制在次常式登入時間內寫入！
					
					$array['msg']='您已超過限制登入嘗試次數！請於10分鐘後再次測試，或聯繫系統管理員。';
					
					$this->_logger->notice('暫時禁止登入');
				}
	
			break;
			case Zend_Auth_Result::SUCCESS:
				//驗證成功，產生Session['Zend_Auth']['storage']
				$auth = Zend_Auth::getInstance();
				$auth->setStorage( new Zend_Auth_Storage_Session( APPLICATION_SESSION, 'AUTH') );
				$auth->getStorage()->write($adapter->getResultRowObject(array(
					'userId', 'userName', 'email', 'email2', 'CompanyId', 'lastLoginTime'
				)));
				
				//2010-12-12 San:修正配合db內設定save方式修改欄位
				//將未啟用帳號狀態改成啟用.
				$_user = new User_Model_User();
				$_user->save(array(
					'userId'		=> $this->extend['SESSION']->AUTH->userId,
					'status'		=> 1,
					'lastLoginTime' => date('Y-m-d H:i:s')
				));

				$_userRole = new User_Model_UserRole();
				$row = $_userRole->fetchRow("UserId = '{$this->extend['SESSION']->AUTH->userId}'");
				
				$_role = new User_Model_Role();
				$role_row = $_role->fetchRow("roleId = {$row->RoleId}");
				
				$this->extend['SESSION']->AUTH->roleId=$role_row->roleId;
				$this->extend['SESSION']->AUTH->roleName=$role_row->roleName;
				//寫入$_SESSION[APPLICATION_SESSION]['AUTH']['roleId']身份
				
				$array=array(
					'callback'=>TRUE,
	    			'result'=>'success',
	    			'msg'=>'登入成功！'
				);
				
				//寫入訊息！
				$this->_logger->setEventItem('account', $this->extend['SESSION']->AUTH->userId);
				$this->_logger->info('使用者登入成功！');
			break;
		}

		}

		echo Zend_Json::encode($array);	//顯示正確或錯誤登入結果
	}
	
}
