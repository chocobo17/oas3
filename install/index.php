<?php

// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

//設定此應用系統SESSION名稱（通常用專案名）
defined('APPLICATION_SESSION') || define('APPLICATION_SESSION', 'HOTRENT');


// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
											    realpath(APPLICATION_PATH . '/../library'), 
												get_include_path()
											  )
						)
);

//要能讀入application.ini部份內容（資料庫、最高權限使用者帳號）做設定

//載入smarty產生安裝樣板檔

//執行SQL語法寫入資料庫

//顯示結果並提供連結引導。
?>