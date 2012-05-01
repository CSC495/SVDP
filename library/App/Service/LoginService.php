<?php

class App_Service_LoginService {
    private $_db;
    
    function __construct(){
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }
       
   
    /****
     *  Sets the users password
     */
    public function updateUserPassword($userId,$password)
    { 
        $data = array( 'password'      => $password,
                       'change_pswd'  => 0);

        $this->_db->update('user',$data, "user_id='" . $userId . "'");

    }
    
    /***
     *  Gets user information
     */
    public function getUserInfo($userId)
    {
        $select = $this->_db->select()->from('user')->where('user_id = ?',$userId);
        
        $results = $this->_db->fetchRow($select);
        return $this->buildMemeber($results);   
    }
    
    /***
     * Build User object from row result
     */
    private function buildMemeber($row)
    {
        $user = new Application_Model_Impl_User();
        $user
            ->setUserId($row['user_id'])
            ->setFirstName($row['first_name'])
            ->setLastName($row['last_name'])
            ->setEmail($row['email'])
            ->setCellPhone($row['cell_phone'])
            ->setHomePhone($row['home_phone'])
            ->setRole($row['role'])
            ->setActive($row['active_flag']);
        
        return($user);
    }
    
}