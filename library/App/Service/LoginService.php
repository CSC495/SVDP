<?php
/**
 *Servive file providing the login controller database access.
 */
class App_Service_LoginService {
    
    /**
     *Database adapter for service methods.
     *
     * @var Zend_Db_Adapter_Abstract
    */
    private $_db;
    
    /**
     *Creates a connection to the DB available to the class.
     *
     *@return void
    */
    function __construct()
    {
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }

    /**
     *Updates a user's password.
     *
     *@param string id of user's password to change
     *@param int | string new password before salting and hashing
     *@return void
    */
    public function updateUserPassword($userId, $password)
    {
        $shaker = new App_Password();
        //salt the password
        $saltedPass = $shaker->saltIt($password);
        $hashPass =  hash('SHA256', $saltedPass);
        $change = array(
                'password' => $hashPass,
                'change_pswd' => '0');
        $where = $this->_db->quoteInto('user_id = ?', $userId);
        $this->_db->update('user', $change, $where);
    }
    
    /**
     *Updates a particular document.
     *Updates the indicated document's information with the information stored
     *in the passed document object
     *
     *@param int id of document to update
     *@param Application_Model_Impl_Document
    */
    public function updateDocument($id, $doc)
    {
        $docData = $this->disassembleDocument($doc);
        $docData['doc_id'] = $id;
        $where = $this->_db->quoteInto('doc_id = ?', $id);
        $this->_db->update('documents', $docData, $where);
    }
    
    /**
     *Gets the indicated user's information.
     *Returns a User object populated with the indicated user's information
     *
     *@param string user's id
     *@return Application_Model_Impl_User
    */
    public function getUserInfo($userId)
    {
        $select = $this->_db->select()
                ->from('user')
                ->where('user_id = ?', $userId);
        $results = $this->_db->fetchRow($select);
        return $this->buildMemeber($results);
    }
    
    /**
     *Gets the indicated document's information.
     *Returns a document object populated with the indicated document's information
     *
     *@param int document's id
     *@return Application_Model_Impl_Document
    */
    public function getDocumentById($id)
    {
        $select = $this->_db->select()
                ->from('documents')
                ->where('doc_id = ?', $id);
        $results = $this->_db->fetchRow($select);
        return $this->buildDocument($results);
    }
    
    /**
     *Deletes the indicated document.
     *
     *@param int id of the document to be deleted
     *@return void
    */
    public function deleteDocument($id)
    {
        $where = $this->_db->quoteInto('doc_id = ?', $id);
        $this->_db->delete('documents', $where);
    }
    
    /**
     *Creates and configures an auth adapter for the login.
     *
     *@param string id of the user loging in
     *@param int | string user's password
     *@return configured Zend_Auth_Adapter_DbTable
    */
    public function getAuthAdapter($userId, $password)
    {
        // Get the database adapter
        $db = Zend_Db_Table::getDefaultAdapter();
        $adapter = new Zend_Auth_Adapter_DbTable($db);

        // Set the parameters, user must be active.
        $adapter
            ->setTableName('user')
            ->setIdentityColumn('user_id')
            ->setCredentialColumn('password')
            ->setCredentialTreatment('? and active_flag="1"');
        $adapter
            ->setIdentity($userId)
            ->setCredential( hash('SHA256', App_Password::saltIt($password)) );
        return $adapter;
    }
        
    /**
     * Builds a User object from row result.
     * 
     * @param mixed[] data to populated User object
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
            ->setChangePswdFlag($row['change_pswd'])
            ->setActive($row['active_flag']);
        return($user);
    }
    
    /**
     * Builds a User object from row result.
     * 
     * @param mixed[] data to populated Document object
     * @return Application_Model_Impl_Document
     */
    private function buildDocument($row){
        $doc = new Application_Model_Impl_Document();
        $doc
            ->setId($row['doc_id'])
            ->setUrl($row['url'])
            ->setName($row['filename'])
            ->setInternal($row['internal_flag']);
        return $doc;
    }
    
    /**
     *Extracts properties of a ParishParams object.
     *
     *@return mixed[string]
    */
    private function disassembleDocument($doc){
        $docData = array(
                         'filename' => $doc->getName(),
                         'url' => $doc->getUrl(),
                         'internal_flag' => $doc->isInternal());
        return $docData;
    }
}
