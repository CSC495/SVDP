<?php

class App_Service_AdminService {
    private $_db;
    
    function __construct(){
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }
    
    /**
     *  Function retrieves the parish parameters which indicate global limits
     *  when evaluating if a case is to be accepted or not
     */
    public function getParishParams(){
        $select = $this->_db->select()->from('parish_funds');
        
        $results = $this->_db->fetchRow($select);
        
        return $this->buildParishParams($results);
    }
    
    /****
     *  Function creates a Parish funds object from the result set
     */
    private function buildParishParams($result){
        $params = new Application_Model_Impl_ParishParams(
                                              $result['available_funds'],
                                              $result['year_limit'],
                                              $result['lifetime_limit'],
                                              $result['case_limit'],
                                              $result['casefund_limit']);
        
        return($params);
    }
    /******
     *  Function takes a parish params object and updates the
     *  table with the respective values
     */
    public function updateParishParams($params)
    {
        $data = array('available_funds'  => $params->getAvailableFunds(),
                      'year_limit'       => $params->getYearlyLimit(),
                      'lifetime_limit'   => $params->getLifeTimeLimit(),
                      'case_limit'       => $params->getCaseLimit(),
                      'casefund_limit'   => $params->getCaseFundLimit());
        $this->_db->update('parish_funds',$data,'1');
    }
    
    /***
     *  Function gets an array of all the 
     */
    public function getParishMembers()
    {
        $select = $this->_db->select()->from('user');
        
        $results = $this->_db->fetchAll($select);
        
        return $this->buildMemeberList($results);
    }
    
    /**
     *  Function builds an array of all memebers of the parish
     *  from a row set
     */
    private function buildMemeberList($rowset)
    {
        $list = array();
        
        foreach($rowset as $row)
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
                
            array_push($list,$user);
        }
        
        return($list);
    }
    
    /****
     *  Adds a user to the database
     */
    public function createParishMemeber($user,$password)
    {
        $params = array('user_id'     => $user->getUserId(),
                        'password'    => $password,
                        'first_name'  => $user->getFirstName(),
                        'last_name'   => $user->getLastName(),
                        'email'       => $user->getEmail(),
                        'cell_phone'  => App_Formatting::emptyToNull($user->getCellPhone()),
                        'home_phone'  => App_Formatting::emptyToNull($user->getHomePhone()),
                        'role'        => $user->getRole(),
                        'change_pswd' => 1,
                        'active_flag' => 1,
                        );
        $result = $this->_db->insert('user',$params);
    }
    
    /***
     *  Updates a users information
     */
    public function updateUserInformation($user)
    {
        $data = array(  
                        'first_name'  => $user->getFirstName(),
                        'last_name'   => $user->getLastName(),
                        'email'       => $user->getEmail(),
                        'cell_phone'  => App_Formatting::emptyToNull($user->getCellPhone()),
                        'home_phone'  => App_Formatting::emptyToNull($user->getHomePhone()),
                        'role'        => $user->getRole(),
                        'active_flag' => $user->getActive());
        
        $this->_db->update('user',$data,"user_id ='" . $user->getUserId() . "'");
    }
    
    /*****
     *  Resets a users password
     */
    public function resetUserPassword($userId,$password)
    {
        $data = array( 'password'      => $password,
                       'change_pswrd'  => 1);
        
        $this->_db->update('user',$data,"user_id ='" . $userId ."'");
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