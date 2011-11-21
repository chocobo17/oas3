<?php

class System_Model_Access extends Zend_Db_Table_Abstract
{
	protected $_name = 'access';

	protected $_primary = 'accessId';

	protected $_dependentTables = array('System_Model_RoleAccess');
	
	public function fetchAll_withMenus()
	{
		$access = $this->fetchAll('accessParentId = 0', 'weight');
		$data=$access->toArray();
		$i=0;
		foreach($access as $row)
		{
			$menusRole = $row->findManyToManyRowset('User_Model_Role', 'System_Model_RoleAccess')->toArray();
			$data[$i]['roles']='';
			foreach( $menusRole as $key=>$value){
				$data[$i]['roles'] .= $value['roleName'];
				if( $key != count($menusRole)-1 ){
					$data[$i]['roles'] .=',';	
				}
			}
			
			$subaccess = $this->fetchAll("accessParentId = {$row->accessId}", 'weight');
			$subdata = $subaccess->toArray();
			$j=0;
			foreach( $subaccess as $subrow){
				$submenusRole = $subrow->findManyToManyRowset('User_Model_Role', 'System_Model_RoleAccess')->toArray();
				foreach( $submenusRole as $key=>$value){
					$subdata[$j]['roles'] .= $value['roleName'];
					if( $key != count($menusRole)-1 ){
						$subdata[$j]['roles'] .=',';	
					}
				}
				$j++;
			}
			
			$data[$i]['submenus'] = $subdata;
			$i++;
		}
		return $data; 
	}
	
	public function fetch_Access_Submenu_List()
	{
		$menuRowSets = $this->fetchAll('accessParentId = 0')->toArray();
		
		$db = $this->getAdapter();
		$select = $db->select()
			 		   ->from(array('A1' => $this->_name), '*' )
			 		   ->joinLeft(array('A2' => $this->_name), 'A1.accessParentId = A2.accessId', array('parentName'=>'resourceName') )
			 		   ->where("A1.layer = ?", 2);
					   
		return $db->fetchAll($select->__toString());
	}
	
	public function fetch_Access_Menu_List()
	{
		$menuRowSets = $this->fetchAll('accessParentId = 0')->toArray();
		
		return $menuRowSets;
	}

	/**
     * Smart save
     * @param array $data   Associative array with row data.
     * @return mixed Primary key value.
     */
    public function save($data) {
        $this->_setupPrimaryKey (); //method inherited from parent class(Zend_Db_Table_Abstact)
        
        $cols = array_intersect_key ( $data, array_flip ( $this->_getCols () ) ); // _getCols() - inherited from parent
         
        if (array_intersect ( ( array ) $this->_primary, array_keys ( array_filter ( $cols ) ) )) { //possible update
                

                if (is_array ( $this->_primary )) { //composed Pk     
                		$a = array ();
                        foreach ( $this->_primary as $pk ) {
                                $a [] = $cols [$pk];
                        }
                        if (count ( $this->_primary ) == count ( $a )) {   
                        		//eval(sprintf('$rows = $this->find(%s);', implode(', ', $a)));
                                $rows = call_user_func_array ( array ($this, 'find' ), $a );
                        } else {
                                throw new Zend_Db_Table_Exception ( 'Invalid primary key.(Primary key is composed, but incomplete)' );
                        }                        } else {
                        $rows = $this->find ( $cols [$this->_primary] );
                }
                
                if (1 == $rows->count ()) { // only one record    
                		$pk =$rows->current ()->setFromArray($cols)->save();
                } elseif (0 == $rows->count ()) { // or might want to insert a row with a certain id(like when you doing imports)
                        $pk = $this->insert ( $cols );
                } else {
                        throw new Zend_Db_Table_Exception ( 'Error updating requested row.(More than 1 row or invalid Id?!)' );
				}
        } else { //new row
                $pk = $this->insert ( $v = array_diff_key ( $cols, array_flip ( ( array ) $this->_primary ) ) );
        }
        return $pk;
    }
}
?>