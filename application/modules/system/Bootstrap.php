<?php

class System_Bootstrap extends Zend_Application_Module_Bootstrap
{
	private $_session;	//session
	
	private $_config;	//資料表
	
	private $_db;		//Zend_Db from Application_Model_Configure

	public function _initConstruct()
	{
		$this->_session = new Zend_Session_Namespace( APPLICATION_SESSION );
		$this->_config = new Application_Model_Configure();
		$this->_db = $this->_config->getDefaultAdapter();
	}

	/**
	 * feature::處理系統設定[session]常數
	 */
	protected function _initSessionConst()
	{
		//2011-01-08 San:加入判斷是否已登入才處理設定CONST!
		if( is_null($this->_session->AUTH) ){
			return false;
		}

		$data = $this->_config->fetchAll("configType = 'SYSTEM'")->toArray();
		if( !is_array($this->_session->CONST->SYSTEM) ){
			if( count($data) == 0){
				//先寫入初始化設定值部份！
				$this->_initConfigure();
				$data = $this->_config->fetchAll("configType = 'SYSTEM'")->toArray();
			}
			
			foreach($data as $key => $value){
				$this->_session->CONST->SYSTEM[$value['configkey']]=$value['value'];
			}
		}elseif(count($data) > 0){
			foreach($data as $key => $value){
				if( $this->_session->CONST->SYSTEM[$value['configkey']] !=$value['value']){
					$this->_session->CONST->SYSTEM[$value['configkey']]=$value['value'];
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
		//判斷若無設定檔則直接略過
		if( !file_exists(realpath(dirname(__FILE__)).'/configure.xml')){
			return FALSE;
		//若已有設定值，則不再處理此區塊！
		}else if( is_array($this->_session->CONST->SYSTEM) ){
			return TRUE;
		}
	
		$xml = new Zend_Config_Xml( realpath(dirname(__FILE__)).'/configure.xml', 'bootstrap' );
		$data = $xml->toArray();
		
		if( is_array($data['key']['item']) && count($data['key']['item']) > 0){
			foreach( $data['key']['item'] as $k => $v){
				if( $k !=0 ){
					//避免重複寫入Configure!
					$row = $this->_config->fetchRow("configkey = '{$v['name']}'");
					if( !is_object($row)){
						$this->_config->save(array(
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

	protected function _initDefine()
	{
    }	
}
?>