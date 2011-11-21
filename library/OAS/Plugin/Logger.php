<?php
/**
 * 處理使用Zend_Log細節部份
 * 使用DB或是file紀錄log方式
 * 設定格式
 * 設定字串內容（主要訊息作參數傳入）
 * 
 * ----------------------------------------
 * Date 2010-01-19
 * 1.預定項目：開發 logger->recordPositionStatus 
 * 紀錄使用者點選到選單位置
 * 2.預定項目：開發 logger->recordSQLProfiler
 * 紀錄INSERT、UPDATE、DELETE與使用ADquery時呼叫
 * 3.預定項目：開發 logger->recordException
 * 紀錄系統所有觸發Exception時的處理
 * 參考 http://plog.longwin.com.tw/programming/2009/01/14/php-get-directory-file-path-dirname-2008
 * 1.紀錄內容：__FILE__、__LINE__、__FUNCTION__、訊息、帳號或logId、發生時間。
 * 2.規則：在寫入（資料庫或檔案）/更新/刪除時需要！
 * 3.在try{}catch{}的catch區塊要回到或引導到特定位置並觸發紀錄產生。
 * 
 * @date 2010-09-26
 * 重新設計Logger內容，規則如下：
 * 1. Logger 可選擇寫入DB[logger、logger_detail]、File[PATH:/temp/logs/]、Firebug[判斷Browser?]
 * 2. 使用預設ERR、WARN、NOTICE、INFO、DEBUG等項目作為預設type分類判斷
 * ERR		發生exception、php warning、php error事件
 * WARN		發生php warning以上
 * NOTICE	發生操作流程非正常顯示問題，需程式內判斷紀錄
 * INFO		
 * DEBUG
 * 
 * @date 2010-10-12
 * 重新設計logger Table，不使用Zend_Db_Table方式，直接透過Zend_Log寫入
 * 
 * @date 2010-12-12
 * 配合使用Zend_Application下引用bootstrap方式初始化logger物件
 * 將logger預設項目寫於Bootstrap.php內 
 */
 
class OAS_Plugin_Logger  extends Zend_Controller_Plugin_Abstract
{
	//定義messages內部份使用到的動作項目、
    const IDENTIFY   = '身份';  	//不同系統定義的角色
    const STATUS	 = '狀態';  // Alert: action must be taken immediately
    const POSITION   = '位置';  // Critical: critical conditions
    
    // Error: error conditions
    const ERR     = 3;
	// Warning: warning conditions
    const WARN    = 4;
    // Notice: normal but significant condition [進入畫面或功能未給必要變數值]
    const NOTICE  = 5;
	// Informational: informational messages [一般操作紀錄等級]
    const INFO    = 6;
	// Debug: debug messages [發生Exception時使用！]
    const DEBUG   = 7;  

	//Log紀錄器物件	
	public  $_logger;
	
	//從Bootstrap呼叫傳入
	function __construct( $logger )
	{
		$this->_logger = $logger;
	}
	
	public function preDispatch()
	{
		if( isset($_REQUEST) ){
			$this->_logger->setEventItem('params', Zend_Json::encode($_REQUEST) );
		}
		
	}	
	
	public function postDispatch()
	{
		//清掉設定參數Item
		if( isset($_REQUEST) ){
			$this->_logger->setEventItem('params', NULL);
		}
	}
}
?>