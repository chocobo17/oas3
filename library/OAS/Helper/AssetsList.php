<?php
/**
 * @link http://blog.srmklive.com/2011/01/06/get-list-of-all-modules-with-respective-controllers-and-actions-in-zend-framework/
 */
 
class OAS_Helper_AssetsList extends Zend_Controller_Action_Helper_Abstract 
{
    public function direct() {}

    public function getList() {
        $module_dir = $this->getFrontController()->getControllerDirectory();
        $resources = array();

        foreach($module_dir as $dir=>$dirpath) {
            $diritem = new DirectoryIterator($dirpath);
            foreach($diritem as $item) {
                if($item->isFile()) {
                    if(strstr($item->getFilename(),'Controller.php')!=FALSE) {
                        include_once $dirpath.'/'.$item->getFilename();
                    }
                }
            }

            foreach(get_declared_classes() as $class){
                if(is_subclass_of($class, 'Zend_Controller_Action')) {
                    $functions = array();

                    foreach(get_class_methods($class) as $method) {
                        if(strstr($method, 'Action')!=false) {
                            array_push($functions,substr($method,0,strpos($method,"Action")));
                        }
                    }
                    $c = strtolower(substr($class,0,strpos($class,"Controller")));
                    $resources[$dir][$c] = $functions;
                }
            }
        }
        return $resources;
    }
}