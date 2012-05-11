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
    
    public function getUserById($userId){
	$select = $this->_db->select()
		->from('user')
		->where('user_id = ?', $userId);
	$results = $this->_db->fetchRow($select);
	return $this->buildUserModel($results);
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
        $data = $this->disassembleParishParams($params);
        $this->_db->update('parish_funds',$data,'1');
    }
    
    public function updateUserInfo($user){
	$userData = $this->disassembleUserModel($user);
	$where = $this->_db->quoteInto('user_id = ?', $user->getUserId());
	$this->_db->update('user', $userData, $where);
    }
    
    public function updateUserPassword($userId, $newPass){
	//Salting goes here when it is to be implemented
	$hashPass =  hash('SHA256', $newPass);
	$change = array(
		    'password' => $hashPass,
		    'change_pswd' => '0');
	$where = $this->_db->quoteInto('user_id = ?', $userId);
	$this->_db->update('user', $change, $where);
    }

    public function getAllUsers(){
        $select = $this->_db->select()
		->from('user')
		->order('first_name ASC');
	$results = $this->_db->fetchAll($select);
	return $this->buildUserList($results);
    }
    
    /**
     *  Function builds an array of all memebers of the parish
     *  from a row set
     */
    private function buildUserList($results){
        $users = array();
        foreach($results as $row)
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
                
            $users[] = $user;
        }
        return $users;
    }
    
    public function buildUserModel($results){
	$user = new Application_Model_Impl_User();
	$user->setUserId($results['user_id']);
	$user->setFirstName($results['first_name']);
	$user->setLastName($results['last_name']);
	$user->setEmail($results['email']);
	$user->setCellPhone($results['cell_phone']);
	$user->setHomePhone($results['home_phone']);
	$user->setRole($results['role']);
	$user->setActive($results['active_flag']);
	return $user;
    }
    
    public function createUser($user, $password){
	$userData = $this->disassembleUserModel($user);
	$hashPass =  hash('SHA256', $password);
	$userData['password'] = $hashPass;
	$userData['change_pswd'] = '1';
	$this->_db->insert('user', $userData);
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
    
    //Given a new user id will return null if the id is not already
    //in the database, if present will return the next available
    //number to append after the id
    public function getNextIdNum($userId){
        $idLen = strlen($userId);
        $select = $this->_db->select()
                ->from('user', 'user_id')
                ->where('user_id LIKE ?', $userId.'%')
                ->orWhere('user_id = ?', $userId)
                ->order('user_id DESC');
        $results = $this->_db->fetchAll($select);
        if($results){
            foreach($results as $row){
                $sub = substr($row['user_id'], $idLen);
                if(is_numeric($sub) || $sub == null)
                    return (intval($sub) + 1);
            }
            return null;
        }else{
            return null;
        }
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
    
    private function disassembleParishParams($params){
	$paramData = array(
		    'year_limit' => $params->getYearlyLimit(),
                    'lifetime_limit' => $params->getLifeTimeLimit(),
                    'case_limit' => $params->getCaseLimit(),
                    'casefund_limit' => $params->getCaseFundLimit());
	return $paramData;
    }
    
    public function disassembleUserModel($user){
	$userData = array(
		    'user_id' => $user->getUserId(),
		    'first_name' => $user->getFirstName(),
		    'last_name' => $user->getLastName(),
		    'email' => $user->getEmail(),
		    'cell_phone' => $user->getCellPhone(),
		    'home_phone' => $user->getHomePhone(),
		    'role' => $user->getRole(),
		    'active_flag' => $user->getActive());
	return $userData;
    }
}