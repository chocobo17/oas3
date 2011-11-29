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

	function init()
	{
		$this->_logger = $this->getInvokeArg('bootstrap')->getResource('logger');
	}


	/**
	 * position:系統首頁畫面
	 * feature::登入、顯示登入表單功能
	 */	
    public function indexAction()	
    {
    	//creat login form
    	$form=new Zend_Form();
		$form->setAction('logout');
		$form->setMethod('post');
		$form->setDescription("enter the account");
		
		//username
		$form->addElement('text', 'username');
        $usernameElement = $form->getElement('username');
	    $usernameElement = $form->setName('user');
       
		//password
		$form->addElement('password','password');
		$passwordElement = $form->getElement('password');
		
		//submit
		$form->addElement('submit','submit');
		$submitButton = $form->getElement('submit');
	
		
		$this->view->form=$form;
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