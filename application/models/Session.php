<?php
/**
 * @package OAS
 * @version v3.0
 * @author k1
 * 
 * 2011-11-01
 * 初始化Models結構-session
 * 
 */

 
class Application_Model_Session extends Zend_Db_Table_Abstract
{
	protected $_name = 'apps_session';
	protected $_primary = 'id';
	
	public function save($data) {
        $this->_setupPrimaryKey (); 
        $cols = array_intersect_key ( $data, array_flip ( $this->_getCols () ) ); 
        if (array_intersect ( ( array ) $this->_primary, array_keys ( array_filter ( $cols ) ) )) { 
            if (is_array ( $this->_primary )) {
        		$a = array ();
                foreach ( $this->_primary as $pk ) {
                    $a [] = $cols [$pk];
                }
                if (count ( $this->_primary ) == count ( $a )) {   
                    $rows = call_user_func_array ( array ($this, 'find' ), $a );
                } else {
                    throw new Zend_Db_Table_Exception ( 'Invalid primary key.(Primary key is composed, but incomplete)' );
                }                        
			} else {
                $rows = $this->find ( $cols [$this->_primary] );
            }
            
            if (1 == $rows->count ()) {     
        		$pk =$rows->current ()->setFromArray($cols)->save();
            } elseif (0 == $rows->count ()) { 
                $pk = $this->insert ( $cols );
            } else {
		        throw new Zend_Db_Table_Exception ( 'Error updating requested row.(More than 1 row or invalid Id?!)' );
			}
        } else { 
            $pk = $this->insert ( $v = array_diff_key ( $cols, array_flip ( ( array ) $this->_primary ) ) );
        }
        return $pk;
    }
}
?>