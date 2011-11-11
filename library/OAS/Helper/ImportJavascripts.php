<?php
/**
 * date 2010-07-19
 * @author San
 * 
 * 需求：
 * 1. 將特定jQuery套件需要引入的css、js檔集合成一個引入函式負責處理載入的css、js陣列
 *
 * 主要功能：
 * 1. 取得css、js引入位置的陣列變數
 * 
 * 主要函式分類：
 * 1. editors:文字編輯器
 * 2. 
 * 
 * 內部處理：
 * 1. _set設定js、css陣列變數 
 * 2. _default_path設定不同呼叫模組預設傳入該模組下若需獨立javascript檔引入的前置路徑 
 */


class OAS_Helper_ImportJavascripts extends Zend_Controller_Action_Helper_Abstract 
{
	private $SCRIPTS_PATH;
	
	private $extend;
	
	public function _setRequest( &$request ){
		
		$this->M = $request->getModuleName();
		$this->C = $request->getControllerName();
		$this->A = $request->getActionName();
		
		$this->SCRIPTS_PATH = "../../application/modules/{$this->M}/views/scripts/{$this->C}/"; 
	} 
	
	/**
	 * feature::判斷不同action傳入固定js檔案
	 */
	public function mvc_defaultpath(){
		$this->extend = Zend_Registry::get('ext');
		
		array_push( $this->extend['js'],
					$this->SCRIPTS_PATH.$this->A.'.js' 
				  );
				  
		Zend_Registry::set('ext', $this->extend);
	}
	
	/**
	 * feature::由不同action傳入特定javascript檔案載入
	 * 
	 * 2011-08-23
	 * San
	 * 修改可判斷是否檔案存在於固定規則路徑還是給予絕對路徑。
	 */
	public function mvc_custompath( $jsFile, $inViewsScripts=true ){
		$this->extend = Zend_Registry::get('ext');
		
		if( $inViewsScripts ){
			array_push( $this->extend['js'], $this->SCRIPTS_PATH.$jsFile );
		}else{
			array_push( $this->extend['js'], $jsFile );
		}
				  
		Zend_Registry::set('ext', $this->extend);
	}
	
	function detect_timezone()
	{
		$this->extend = Zend_Registry::get('ext');
		
		array_push( $this->extend['js'],
					'jplug/detect_timezone.min.js'
		);
		
		Zend_Registry::set('ext', $this->extend);
	}
	
	/**
	 * 
	 */
	public function editors_jwysiwyg()
	{
		$this->extend = Zend_Registry::get('ext');
		//套件跟目錄
		$default = '../richeditor/jwysiwyg/';
		
		array_push( $this->extend['css'],
					$default.'jquery.wysiwyg.css' 
		);
				  
		array_push( $this->extend['js'],
					$default.'jquery.wysiwyg.js',
					$default.'controls/wysiwyg.image.js',
					$default.'controls/wysiwyg.link.js',
					$default.'controls/wysiwyg.table.js',
					
					$default.'controls/default.js',
					
					'admin/editors.js'
		);
		
		Zend_Registry::set('ext', $this->extend);
	}
	
	/**
	 * 
	 */
	public function editors_markitup()
	{
		$this->extend = Zend_Registry::get('ext');
		//套件跟目錄
		$default = '../richeditor/markitup/';
		
		array_push( $this->extend['css'],
					$default.'skins/markitup/style.css',
					$default.'style.css'
				  );
				  
		array_push( $this->extend['js'],
					$default.'jquery.markitup.js',
					
					'admin/editors.js'
				  );
				  
		Zend_Registry::set('ext', $this->extend);
	}
	
	/**
	 * feature::表單驗證機制
	 * 2011-09-21
	 * @param string $code 傳入語系代碼
	 */
	public function form_validEngine( $code )
	{
		$this->extend = Zend_Registry::get('ext');
		
		//套件跟目錄
		$defualt = 'jplug/validationEngine/';
		
		array_push( $this->extend['css'],
					'../js/'.$defualt.'template.css',
					'../js/'.$defualt.'validationEngine.jquery.css' 
				  );

		array_push( $this->extend['js'],
					$defualt.'jquery.validationEngine-'.$code.'.js',
					$defualt.'jquery.validationEngine.js',
					
					'formValidation.js'
				  );
		
		Zend_Registry::set('ext', $this->extend);
	}
	
	/**
	 * feature::表單驗證機制v2版
	 */
	public function form_validEngineV2( $code )
	{
		$this->extend = Zend_Registry::get('ext');
		
		//套件跟目錄
		$defualt = 'jplug/validationEngine2/';
		
		array_push( $this->extend['css'],
					'../js/'.$defualt.'css/template.css',
					'../js/'.$defualt.'css/validationEngine.jquery.css' 
				  );
				  
		array_push( $this->extend['js'],
					$defualt.'languages/jquery.validationEngine-en.js',
					$defualt.'languages/jquery.validationEngine-jp.js',
					$defualt.'languages/jquery.validationEngine-zh_TW.js',
					$defualt.'contrib/other-validations.js',
					$defualt.'jquery.validationEngine.js',
					
					'admin/formValidation.js'
				  );
		
		Zend_Registry::set('ext', $this->extend);
	}
	
	/**
	 * feature::圖檔上傳
	 */
	public function image_upload()
	{
		$this->extend = Zend_Registry::get('ext');
		
		$defualt = 'jplug/AjaxUpload/';
		
		array_push( $this->extend['css'],
					'../js/'.$defualt.'AjaxUpload.css'
		);
		
		array_push( $this->extend['js'],
					$defualt.'AjaxUpload.js',
					
					'admin/uploaderFunctions.js'
		);
		
		Zend_Registry::set('ext', $this->extend);
	}

	/**
	 * feature::圖檔/壓縮檔上傳（多檔）
	 * 多檔案上傳套件
	 */
	public function image_upload2()
	{
		$this->extend = Zend_Registry::get('ext');
		
		$defualt = 'jplug/PlUpload/';
		
		array_push( $this->extend['css'],
					'../js/'.$defualt.'jquery.plupload.queue/jquery.plupload.queue.css',
					'../js/'.$defualt.'jquery.ui.plupload/jquery.ui.plupload.css'
		);
		
		array_push( $this->extend['js'],
					//初始化Google Gear
					// $defualt.'plupload.gears_init.js',
					$defualt.'plupload.full.js',
					$defualt.'jquery.plupload.queue/jquery.plupload.queue.js',
					$defualt.'jquery.ui.plupload/jquery.ui.plupload.js'
		);
		
		Zend_Registry::set('ext', $this->extend);
	}
	
	/**
	 * feature::產生色票環套件
	 */
	public function color_farbtastic()
	{
		$this->extend = Zend_Registry::get('ext');
		
		$defualt = 'jplug/farbtastic/';
		
		array_push( $this->extend['css'],
					'../js/'.$defualt.'farbtastic.css'
		);
		
		array_push( $this->extend['js'],
					$defualt.'farbtastic.js'
		);
		
		Zend_Registry::set('ext', $this->extend);
	}
	
	/**
	 * feature::擴充jQueryUI-datepicker加入時間選擇
	 */
	public function jqueryui_datetimepicker()
	{
		$this->extend = Zend_Registry::get('ext');
		
		$defualt = 'jplug/datetimepicker/';
		
		array_push( $this->extend['css'],
					'../js/'.$defualt.'jqueryui.timepicker.addon.css'
		);
		
		array_push( $this->extend['js'],
					$defualt.'jqueryui.timepicker.addon.js'
		);
		
		Zend_Registry::set('ext', $this->extend);
	}
	
	
	/**
	 * feature::表格排序暨分頁設定功能
	 * 
	 * 2011-08-25
	 * San
	 * 增加表格按鈕功能函式、表格標題點選排序函式
	 * 
	 * 2011-09-04
	 * San
	 * 加入表格拖曳排序套件
	 */
	public function listtable_functions()
	{
		$this->extend = Zend_Registry::get('ext');
		
		//套件跟目錄
		$sorter = 'jplug/tablesorter/';
		
		//套件跟目錄
		$dnd = 'jplug/tablednd/';
		
		array_push( $this->extend['css'],
					'../js/'.$sorter.'css/style.css', 
					'../js/'.$sorter.'pager/jquery.tablesorter.pager.css',
					
					'../js/'.$dnd.'jquery.tablednd.css'
				  );
				  
		array_push( $this->extend['js'],
					$sorter.'jquery.metadata.js',
					$sorter.'jquery.tablesorter.min.js',
					$sorter.'pager/jquery.tablesorter.pager.js',
					
					$dnd.'jquery.tablednd.js',
					
					'tablelistFunctions.js'
				  );
				  
		Zend_Registry::set('ext', $this->extend);
	}
	
}
?>