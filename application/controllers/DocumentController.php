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
        
        $this->setPartial();
 
    }
    
    // Determines the partial to use
    private function setPartial()
    {
        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity())
        {
            $role = $auth->getIdentity()->role;
            
            if ($role === App_Roles::ADMIN)
            {
                $this->view->pView = 'partial/viewDocForAdmin.phtml';
                $this->view->add = true;
            }
            else
            {
                $this->view->pView = 'partial/viewDocForMember.phtml';
                $this->view->add = false;
            }
        }
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
        $this->view->pageTitle = "Upload Document";
        
        $this->view->form = new Application_Model_Document_UploadForm();
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