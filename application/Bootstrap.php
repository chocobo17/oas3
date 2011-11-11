<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	private $_session;	//session
	private $_config;	//資料表
	private $_db;		//Zend_Db from Application_Model_Configure

	public function _initConstruct()
	{
		//直接確認啟動db部份！
		//本專案不使用mail->bootstrap('mail');
		$this->bootstrap('db')->bootstrap('session');

		$this->_session = new Zend_Session_Namespace( APPLICATION_SESSION );
		$this->_config = new Application_Model_Configure();
		$this->_db = $this->getPluginResource('db')->getDbAdapter();
	}
	
	protected function _initBaseUrl()
	{
		$this->bootstrap('frontController');
        $front = $this->getResource('frontController');

		$this->_burl = Zend_Controller_Front::getInstance()->getBaseUrl();
	    if (!$this->_burl) {
	      $this->_burl = rtrim(preg_replace( '/([^\/]*)$/', '', $_SERVER['PHP_SELF'] ), '/\\');
	      $front->setBaseUrl($this->_burl);
	    }
		
		$request=new Zend_Controller_Request_Http();
        $front->setRequest($request);  
	}

	/**
	 * feature::處理系統設定[SESSION]常數
	 */
	protected function _initSessionConst()
	{
		//2011-01-08 San:加入判斷登入後多久設定AUTH驗證失效！
		// if( !is_null($this->_session->AUTH) ){
			// $this->_session->setExpirationSeconds(600, 'AUTH');
		// }
		
		$data = $this->_config->fetchAll("configType = 'APPLICATION'")->toArray();
		
		if( !is_array($this->_session->CONST->APPLICATION) ){
			if( count($data) == 0){
				//先寫入初始化設定值部份！
				$this->_initConfigure();
				$data = $this->_config->fetchAll("configType = 'APPLICATION'")->toArray();
			}
			
			foreach($data as $key => $value){
				$this->_session->CONST->APPLICATION[$value['configkey']]=$value['value'];
			}
		}elseif(count($data) > 0){

			foreach($data as $key => $value){
				if( $this->_session->CONST->APPLICATION[$value['configkey']] !=$value['value']){
					$this->_session->CONST->APPLICATION[$value['configkey']]=$value['value'];
				}
			}
		}			
	}

	/**
	 * feature::讀入模組內configure.xml屬bootstrap區塊
	 * 判斷資料庫內是否存在該筆資料，若未存在新增
	 */
	protected function _initConfigure()
	{
		//若已有設定值，則不再處理此區塊！
		if( is_array($this->_session->CONST->APPLICATION) ){
			return TRUE;
		}
		
		//從設定檔內取出針對一般使用者提供的選單清單寫入access
		$xml = new Zend_Config_Xml( realpath(dirname(__FILE__)).'/configs/application.xml', 'bootstrap' );
		$data = $xml->toArray();
		if( is_array($data['key']['item']) && count($data['key']['item']) > 0){
			foreach( $data['key']['item'] as $k => $v){
				if( preg_match('/FRONTMENUS/', $v['name']) ){
					//取出寫入資料
					$array = Zend_Json::decode($v['extenddata']);
					$array['moduleName'] = $data['key']['moduleName'];
					
					//寫入sys_access
					$sql = $this->_db->select()->from($v['table'],'*')->where("resourceName = '{$array['resourceName']}'");
					$result = $this->_db->query($sql)->fetchAll();
					if( count($result) == 0){
						try{
							//寫入Vocabulary
							$this->_db->insert($v['table'], $array);
						}catch(Zend_Db_Exception $zde){
							echo "FILE:".__FILE__.": LINE:".__LINE__." MESSAGE:".$zde->getMessage();
							die($this->_db->__toString());
						}
					}
				}elseif( $k !=0 ){
					//避免重複寫入Configure!
					$row = $this->_config->fetchRow("configkey = '{$v['name']}'");
					if( !is_object($row)){
						$this->_config->save(array(
							'configType'	=>	$data['key']['moduleName'],
							'configkey'		=>	$v['name'],
							'value'			=>	$v['default'],
							'description'	=>	$v['description'], 
						));
					}else{
						$this->_config->save(array(
							'configureId'		=>	$row->configureId,
							'configType'	=>	$data['key']['moduleName'],
							'configkey'		=>	$v['name'],
							'value'			=>	$v['default'],
							'description'	=>	$v['description'], 
						));
					}
				}
			}
		}
    }	
	
	protected function _initRole()
	{
		
		if( file_exists(realpath(dirname(__FILE__)).'/configs/application.xml')){
			//step1. 確認DB內無資料！
			$role = $this->_db->fetchAll("SELECT * FROM sys_role WHERE roleType = 'APPLICATION'");
			if( count($role) > 0 ){
				return false;
			}
			
			//step2. 讀取設定檔內role區段資料
			$xml = new Zend_Config_Xml( realpath(dirname(__FILE__)).'/configs/application.xml', 'role' );
			$data  =$xml->toArray();
			
			if( is_array($data['actor']) && count($data['actor']) > 0){
				foreach( $data['actor'] as $k => $v){
					if( $v['parentName']!="" ){
						$parent = $this->_db->fetchRow("SELECT roleId FROM sys_role WHERE roleName = '{$v['parentName']}'");
						if( $parent ){
							$v['roleParentId'] = $parent['roleId'];
						}else{
							$v['roleParentId'] = 0;
						}
					}
					
					if( $v['name']!="" ){
						$row = $this->_db->fetchRow("SELECT roleId FROM sys_role WHERE roleName = '{$v['name']}'");
						if( !$row ){
						$this->_db->insert('sys_role', array(
							'roleParentId'		=> $v['roleParentId'],
							'roleType'			=> 'APPLICATION',
							'roleName'			=> $v['name'],
							'description'		=> $v['description'],
							'delete'			=> 0
						));
						}else{
						$this->_db->update('sys_role', array(
							'roleParentId'		=> $v['roleParentId'],
							'roleType'			=> 'APPLICATION',
							'roleName'			=> $v['name'],
							'description'		=> $v['description'],
							'delete'			=> 0
						), "roleId = {$row['roleId']}");	
						}
					}
				}
			}
			
		//若檔案不存在，則不再處理此區塊！
		}else{
			return false;
		}
	}	


	//設定由預設的例外錯誤處理controller接受整個系統發生的例外事件！
	protected function _initErrorHandler()
	{
		$this->bootstrap('frontController');
        $front = $this->getResource('frontController');
        $front->registerPlugin( new Zend_Controller_Plugin_ErrorHandler(array(
																		    'module'     => 'default',
																		    'controller' => 'error',
																		    'action'     => 'default'
																	    )
							  										   ) 
							  );
	}
	
	protected function _initLogger()
	{
        // Create the Zend_Log object
        $logger = new Zend_Log();

		//設定log紀錄資料表的[logtype]欄位值
		$DBwriter = new Zend_Log_Writer_Db( $this->_db, 'apps_logger', 
											array(
												'loggerType'	=>	'defineType',
												'event'			=>	'priorityName',
												'refer'			=>	'refer',
												'href'			=>	'href',
												'input'			=>	'params',
												'output'		=>	'return',
												'messages'		=>	'message',
												'initTime'		=>	'timestamp'
											) );
		
		$logger->setEventItem('defineType', 'AUTH'); //自訂事件類型寫入資料庫欄位logType ( AUTH/MANAGE/OPERATOR/IO/CONFIG )
		
		$logger->setEventItem('refer', $_SERVER['HTTP_REFERER']);
		$logger->setEventItem('href', "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
		
		$logger->addWriter($DBwriter);
		
        // 處理顯示Firebug Logger - Development only
        if ($this->_application->getEnvironment() != 'online') {
            // Init firebug logging
            $fbWriter = new Zend_Log_Writer_Firebug();
            $logger->addWriter($fbWriter);

	        // Create the resource for the error log file
	        // if you dont want to use the error suppressor "@"
	        // you can always use: is_writable
	        // http://php.net/manual/function.is-writable.php
	        $stream = @fopen(ini_get(APPLICATION_PATH.'/error.log'), 'a');
	        if ($stream) {
	            $stdWritter = new Zend_Log_Writer_Stream($stream);
	            $stdWritter->addFilter(Zend_Log::DEBUG);
	            $stdWritter->addFilter(Zend_Log::INFO);
	            $logger->addWriter($stdWritter);
	        }
        }
		
		$this->bootstrap('frontController');
        $front = $this->getResource('frontController');
        $front->registerPlugin( new OAS_Plugin_Logger( $logger ) );

        return $logger;
	}

	/**
	 * 2011-06-10
	 * San
	 * 加入處理設定所有Action設定Helper library!
	 */
	protected function _initHelper()
	{
		//存取MVC架構所有Action()清單功能
		Zend_Controller_Action_HelperBroker::addHelper(new OAS_Helper_AssetsList() );
		//引入集合匯入jQuery套件功能
		Zend_Controller_Action_HelperBroker::addHelper(new OAS_Helper_ImportJavascripts() );
    }	
}