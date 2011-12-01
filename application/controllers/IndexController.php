<?php
/**
 * IndexController.php
 * 系統預設入口邏輯
 * 
 * @date 2011-11-01
 * @author k1
 * feature::
 * 1. 提供登入畫面
 * 2. 顯示預設登入後畫面
 * 
 * --------------------------------------------------
 * @date 2011-11-01
 * 轉換成Zend_Application格式！
 * @date 2011-11-10
 * 修改成Zend_View使用方式
 */

class IndexController extends Zend_Controller_Action
{

	private $_logger;
	private $_db;
    private $_config;
	function init()
	{
		$this->_logger = $this->getInvokeArg('bootstrap')->getResource('logger');
		$this->_config = new Application_Model_Configure();
		$this->_db =$this->_config->getDefaultAdapter();
	}


	/**
	 * position:系統首頁畫面
	 * feature::登入、顯示登入表單功能
	 */	
	 
	 private function getloginForm()
	 {
	 	//creat login form
    	$form=new Zend_Form();
		$form->setAction('/oas/index/login');
		$form->setMethod('post');
		$form->setDescription("enter the account");
		$form->setName('getloginform');
		
		
		//username
		$form->addElement('text', 'username');
        $usernameElement = $form->getElement('username');
	  	$usernameElement->setLabel('帳號:');
		$usernameElement->setRequired(TRUE);
       
		//password
		$form->addElement('password','password');
		$passwordElement = $form->getElement('password');
	    $passwordElement->setLabel('密碼:');
		$passwordElement->setRequired(TRUE);
		
		//submit
		$form->addElement('submit','submit');
		$submitButton = $form->getElement('submit');
		$submitButton->setLabel('登入');
		
		return $form;
	
		
		
		 
	 }
	 
	 
    public function indexAction()	
    {
    	
		
		$this->view->form=$this->getloginForm();
    }
	//login un finish
	public function loginAction()
	{
		$form = $this->getloginForm();
		
		
		
		if ($form->isValid($_POST)) {
			$username=$form->getValue("username");
			$password=$form->getValue("password");
			
			
			
		} else {
			
			
		}
		
		
	}


	/** 
	 * feature::先紀錄登出狀態後再進行清除動作
	 */
	
	public function logoutAction()
	{
		//Step1. 紀錄目前使用者登出紀錄！
		$this->_logger->info('使用者登出成功！');

		//Step2. 清除Session資訊
		Zend_Session::destroy(true);

		//Step3. 回到首頁
		$this->_redirect('/');
	}

}
?>