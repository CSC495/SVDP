<?php
/**
 *@package ServiceFilePackage
*/
/**
 *Admin Service File
 *
 *Holds methods that the admin controller needs to access the database
 *@package ServiceFilePackage
 */
class App_Service_AdminService {
    /**
     *Holds connection to DB
    */
    private $_db;
    
    /**
     *Creates a connection to the DB available to the class
     *@return void
    */
    function __construct(){
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }
    
    /**
     *Gets the Parish Aid Limits.
     *
     *Function retrieves the parish parameters which indicate global limits
     *when evaluating if a case is to be accepted or not
     *@return Application_Model_Impl_ParishParams
     */
    public function getParishParams(){
        $select = $this->_db->select()->from('parish_funds');
        
        $results = $this->_db->fetchRow($select);
        
        return $this->buildParishParams($results);
    }
    
    /**
     *Gets the indicated user's information.
     *
     *Returns a User object populated with the indicated user's information
     *@param string the user_id of the indicated user
     *@return Application_Model_Impl_User
    */
    public function getUserById($userId){
	$select = $this->_db->select()
		->from('user')
		->where('user_id = ?', $userId);
	$results = $this->_db->fetchRow($select);
	return $this->buildUserModel($results);
    }
    
    /**
     *Builds a ParishParams object.
     *Creates a Parish funds object from the result set
     *@param mixed[string]
     *@return Application_Model_Impl_ParishParams
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
    /**
     *Updates parish aid limits.
     *Takes a parish params object and updates the
     *table with the respective values
     *@param mixed[string]
     *@return void
     */
    public function updateParishParams($params)
    {
        $data = $this->disassembleParishParams($params);
        $this->_db->update('parish_funds',$data,'1');
    }
    
    /**
     *Updates user information.
     *Updates indicated user's information with the data contained
     *in the User object
     *@param Application_Model_Impl_User
     *@return void
     */    
    public function updateUserInfo($user){
	$userData = $this->disassembleUserModel($user);
	$where = $this->_db->quoteInto('user_id = ?', $user->getUserId());
	$this->_db->update('user', $userData, $where);
    }
    
    /**
     *Updates user password.
     *Updates the indicated user's password with the given password after salting and hashing
     *@param string indicated user's _id
     *@param string | int new password before salting and hash
     *@return void
     */  
    public function updateUserPassword($userId, $newPass)
    {
	$hashPass =  hash('SHA256', App_Password::saltIt($newPass));
	$change = array(
		    'password' => $hashPass,
		    'change_pswd' => '0');
	$where = $this->_db->quoteInto('user_id = ?', $userId);
	$this->_db->update('user', $change, $where);
    }

    /**
     *Gets all current users.
     *Returns information of all users in an array of User objects
     *@return array of Application_Model_Impl_User
    */
    public function getAllUsers()
    {
        $select = $this->_db->select()
		->from('user')
		->order('first_name ASC');
	$results = $this->_db->fetchAll($select);
	return $this->buildUserList($results);
    }
    
    /**
     *	Builds an array of populated User objects.
     *	
     *  Builds an array of all memebers of the parish
     *  from a row set
     *	@param mixed[string]
     *  @return array of Application_Model_Impl_User
     */
    private function buildUserList($results){
        $users = array();
        foreach($results as $row)
        {
            $user = $this->buildUserModel($row);        
            $users[] = $user;
        }
        return $users;
    }
    
    /**
     *	Builds a populated User object.
     *	
     *  Builds a User object populated with information from
     *  from a row set
     *  @param mixed[string]
     *  @return Application_Model_Impl_User
     */
    private function buildUserModel($results){
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
    
    //DUPLICATE FUNCTION, THIS FUNCTION IS DOES NOT SEEM TO BE USED
    /*public function createUser($user, $password)
    {
	$userData = $this->disassembleUserModel($user);
	$hashPass =  hash('SHA256', App_Password::saltIt($password));
	$userData['password'] = $hashPass;
	$userData['change_pswd'] = '1';
	$this->_db->insert('user', $userData);
    }*/
    
    /**
     *Inserts a new user into the database.
     *@param Application_Model_Impl_User
     *@param int | string password before salting and hashing
     *@return void
    */
    public function createParishMemeber($user,$password)
    {
        $params = array('user_id'     => $user->getUserId(),
                        'password'    => hash('SHA256', App_Password::saltIt($password)),
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
    
    /**
     *  Updates a users information.
     *	@param Application_Model_Impl_User
     *  @return void
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
    
    /**
     *  Resets a users password
     *	@param string user_id
     *	@param int | string password before salting and hashing
     *  @return void
     */
    public function resetUserPassword($userId,$password)
    {
        $data = array( 'password'      => hash('SHA256', App_Password::saltIt($password)),
                       'change_pswd'  => 1);
        
        $this->_db->update('user',$data,"user_id ='" . $userId ."'");
    }
    
    /**
     *Gets the next available trailing number for a given user_id.
     *
     *Given a new user id will return null if the id is not already
     *in the database, if present will return the next available
     *number to append after the id
     *@param string user_id
     *@return int | void the next available trailing number if not void
    */
    public function getNextIdNum($userId)
    {
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
            return '';
        }else{
            return '';
        }
    }
    
    /**
     *Returns the number of admin users.
     *
     *@return int number of admin users
     */
    public function getNumAdmins(){
        $select = $this->_db->select()
                ->from('user', array('numAdmins' => 'COUNT(*)'))
                ->where('role = ?', App_Roles::ADMIN)
                ->where('active_flag = 1') ;
        $result = $this->_db->fetchRow($select);
        return $result['numAdmins'];
    }
    
    /**
     * Build User object from row result
     *
     * @return Application_Model_Impl_User
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
    
    /**
     *Extract properties of a ParishParams object
     *
     *@return mixed[string]
    */
    private function disassembleParishParams($params){
	$paramData = array(
		    'year_limit' => $params->getYearlyLimit(),
                    'lifetime_limit' => $params->getLifeTimeLimit(),
                    'case_limit' => $params->getCaseLimit(),
                    'casefund_limit' => $params->getCaseFundLimit());
	return $paramData;
    }
    
    /**
     *Extract properties of a User object
     *
     *@return mixed[string]
    */
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
