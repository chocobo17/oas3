<?php

class System_Model_RoleAccess extends Zend_Db_Table_Abstract
{
	protected $_name = 'role_access';
	
	protected $_primary = array('RoleId', 'AccessId');
	
	protected $_referenceMap = array(
									'Role' => array(
										'columns' 		=> array('RoleId'),
										'refTableClass' => 'User_Model_Role',
										'refColumns'	=> 'roleId',
										'onDelete'		=> self::CASCADE,
										'onUpdate'		=> self::CASCADE
									),
									'Access' => array(
										'columns' 		=> array('AccessId'),
										'refTableClass' => 'System_Model_Access',
										'refColumns'	=> 'accessId',
										'onDelete'		=> self::CASCADE,
										'onUpdate'		=> self::CASCADE
									)
		
	/**
	 * feature::在library/ACL.php下取得角色選單時會根據access設定排序
	 */						);
	public function fetchAll_withAccessOrder( $roleId )
	{
		$select = $this->select()->setIntegrityCheck(false);
		$select->from('role_access AS ra','*')
			   ->where('ra.RoleId = ?', $roleId)
			   ->where('a.enabled = 1')
			   ->joinLeft('access AS a', 'ra.AccessId = a.accessId')
			   ->joinLeft('role AS r', 'ra.RoleId = r.roleId')
			   ->order('layer')
			   ->order('a.accessParentId')
			   ->order('a.weight');
		$roleaccess = $this->fetchAll($select)->toArray();
		return $roleaccess;
	}			
	/**
	 * 
	 * @return 
	 */
	public function fetchAll_withMenus()
	{
		$roleaccess = $this->fetchAll();
		$data=array();
		$i=0;
		foreach($roleaccess as $row)
		{
			$role = $row->findParentRow('User_Model_Role');
			$access  = $row->findParentRow('System_Model_Access')->toArray();
			if( !array_key_exists($role->roleName, $data)){
				$data[$role->roleName]=array();
			}
			
			if( $access['accessParentId'] ){
				$data[$role->roleName]['menus'][$access['accessParentId']]['subemnus'][]=$access;
			}elseif( isset($data[$role->roleName]['menus'][$access['accessParentId']]['submenus'])){
				$tmp =$data[$role->roleName]['menus'][$access['accessId']]['submenus'];
				$data[$role->roleName]['menus'][$access['accessId']]=$access;
				$data[$role->roleName]['menus'][$access['accessId']]['submenus']=$tmp;
			}else{
				$data[$role->roleName]['menus'][$access['accessId']]=$access;
			}
			$i++;
		}
		return $data; 
	}
	
	/**
	 * 在新增身分權限之前，先將所有的對應清除掉！ 
	 */
	public function truncate()
	{
		$this->getAdapter()->query("TRUNCATE TABLE {$this->_name}");
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