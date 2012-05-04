<?php

class DocumentController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    }
    
    // Lists the documents
    public function listAction()
    {
        $this->view->pageTitle = "Document List";
        
        $service = new App_Service_DocumentService();
        $this->view->docs = $service->getDocuments();
    }
    
    // Edit an existing document
    public function editAction()
    {
        $this->view->pageTitle = "Document Modify Member";
        
        // Get request and passed parameter
        $request = $this->getRequest();
        $docId = $request->getParam('id');
        
        // If theres no param go back to index
        if(!$docId)
            return $this->_helper->redirector('login');
        
        $this->view->form = new Application_Model_Document_EditForm ();
        
    }
    
    // Upload a new document
    public function uploadAction()
    {
        
    }
    
    // Delete an exisiting document
    public function deleteAction()
    {
        
    }
    
    // Update an exisiting document with a newer version
    public function updateAction()
    {
        
    }
}