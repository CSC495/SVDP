<?php

class App_Service_DocumentService {
    private $_db;
    
    function __construct(){
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }  
    /***
     * Get list of all the documents
     */
    public function getDocuments()
    {
        $select = $this->_db->select()->from('documents');
        
        $results = $this->_db->fetchAll($select);
        
        return( $this->buildDocuments($results) );
    }
    
    /***
     * Builds the list of docuemnts from a row set
     */
    private function buildDocuments($rowset)
    {
        $list = array();

        foreach($rowset as $row)
        {
            $doc = $this->buildDocument($row);

            array_push($list,$doc);
        }
        return($list);
    }
    
    /***
     * Gets information about a single document
     */
    public function getDocument($id)
    {
        $select = $this->_db->select()->from('documents')->where('doc_id = ?',$id);
        
        $result = $this->_db->fetchRow($select);
        
        return( $this->buildDocument($result) );
    }
    
    /***
     * Builds a single document
     */
    private function buildDocument($row)
    {
        $doc = new Application_Model_Impl_Document();
        $doc
                ->setId($row['doc_id'])
                ->setUrl($row['url'])
                ->setName($row['filename'])
                ->setInternal($row['internal_flag']);
                
        return($doc);
    }
    // temp
    public function deleteDocument($doc)
    {
        $result = $this->_db->delete('documents','doc_id =' . $doc->getId());
        
        return $result;
    }
    /***
     * Updates information about a particular document
     */
    public function updateDocument($doc)
    {
        $data = array(  
                        'filename'    => $doc->getName(),
                        'url'         => $doc->getUrl(),
                        'internal_flag'    => $doc->isInternal());
        $where = "doc_id = " . $doc->getId();
        
        $this->_db->update('documents',$data,$where);
    }
    
    /**
     * Creates a new document
     */
    public function createDocument($doc)
    {
        $data = array(  'doc_id'      => null,
                        'filename'    => $doc->getName(),
                        'url'         => $doc->getUrl(),
                        'internal_flag'    => $doc->isInternal());
        $this->_db->insert('documents',$data);
    }
}