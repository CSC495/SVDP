<?php

class App_Service_LoginService {
    private $_db;
    
    function __construct(){
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }

    public function updateUserPassword($userId, $password){ 
        //Salting goes here when it is to be implemented
	$hashPass =  hash('SHA256', $newPass);
	$change = array(
		    'password' => $hashPass,
		    'change_pswd' => '0');
	$where = $this->_db->quoteInto('user_id = ?', $userId);
	$this->_db->update('user', $change, $where);
    }
    
    public function updateDocument($id, $doc){
        $docData = $this->disassembleDocument($doc);
        $docData['doc_id'] = $id;
        $where = $this->_db->quoteInto('doc_id = ?', $id);
        $this->_db->update('documents', $docData, $where);
    }
    
    public function getUserInfo($userId){
        $select = $this->_db->select()
                ->from('user')
                ->where('user_id = ?', $userId);
        $results = $this->_db->fetchRow($select);
        return $this->buildMemeber($results);
    }
    
    public function getDocumentById($id){
        $select = $this->_db->select()
                ->from('documents')
                ->where('doc_id = ?', $id);
        $results = $this->_db->fetchRow($select);
        return $this->buildDocument($results);
    }
    
    public function deleteDocument($id){
        $where = $this->_db->quoteInto('doc_id = ?', $id);
        $this->_db->delete('documents', $where);
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
            ->setChangePswdFlag($row['change_pswd'])
            ->setActive($row['active_flag']);
        return($user);
    }
    
    private function buildDocument($row){
        $doc = new Application_Model_Impl_Document();
        $doc
            ->setId($row['doc_id'])
            ->setUrl($row['url'])
            ->setName($row['filename'])
            ->setInternal($row['internal_flag']);
        return $doc;
    }
    
    private function disassembleDocument($doc){
        $docData = array(
                         'filename' => $doc->getName(),
                         'url' => $doc->getUrl(),
                         'internal_flag' => $doc->isInternal());
        return $docData;
    }
}