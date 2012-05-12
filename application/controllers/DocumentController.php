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
    
    // Upload a new document
    public function uploadAction()
    {
        $request = $this->getRequest();
        $form = new Application_Model_Document_UploadForm();
        $this->view->pageTitle = "Upload Document";
        $this->view->form = $form;
        
        if($request->isPost())
        {
            $this->handleUploadForm($form);
        }
    }
    
    private function handleUploadForm($form)
    {
        $form->populate($_POST);

        $error = false;
        //Validate name
        if( !$form->name->isValid($form->getValue('name')) )
        {
            $error = true;
        }

        // Validate a file is provided
        if( !$_FILES['url']['size'])
        {
            $error = true;
            $form->err->addError('No file was provided.');
        }
        
        if($error)
            return;
        
        $this->saveFile($form);
        // return if not valid
        return;
    }
    
    private function saveFile($form)
    {
        $doc = new Application_Model_Impl_Document();
        $doc
            ->setId(null)
            ->setUrl($_FILES['url']['name'])
            ->setName($form->getValue('name'))
            ->setInternal(1);
            
        $service = new App_Service_DocumentService();
        $service->createDocument($doc);
        
        $upload = new Zend_File_Transfer_Adapter_Http();
        $upload->setDestination(APPLICATION_PATH . '/uploads/');
        $upload->receive();
        
        // Redirect user
        $this->_forward('index', App_Resources::REDIRECT, null,
                    Array( 'msg' => 'SVDP Document Uploaded Successfully!',
                           'time' => 3,
                           'controller' => App_Resources::DOCUMENT,
                           'action' => 'list'));
    }
    
    // Add's an external document
    public function addAction()
    {
        $request = $this->getRequest();
        $form = new Application_Model_Document_AddForm();
        $this->view->pageTitle = "Add External File";
        $this->view->form = $form;
        
        if($request->isPost())
        {
            $this->handleAddForm($form);
        }
    }
    
    private function handleAddForm($form)
    {
        if( $form->isValid($_POST) )
        {
            // Create the document
            $doc = new Application_Model_Impl_Document();
            $doc
                ->setId(null)
                ->setUrl($form->getValue('url'))
                ->setName($form->getValue('name'))
                ->setInternal(0);
            
            // Add file to database 
            $service = new App_Service_DocumentService();
            $service->createDocument($doc);
            
            // Redirect user
            $this->_forward('index', App_Resources::REDIRECT, null,
                    Array( 'msg' => 'External Document Added Successfully!',
                           'time' => 3,
                           'controller' => App_Resources::DOCUMENT,
                           'action' => 'list'));
        }
        
        //invalid form
        return;
    }
    
    // Delete an exisiting document
    public function deleteAction()
    {
        // Get request and passed parameter
        $request = $this->getRequest();
        $docId = $request->getParam('id');
        
        // If theres no param go back to index
        if(!$docId)
            return $this->_helper->redirector('index');
            
        $service = new App_Service_DocumentService();
        $doc = $service->getDocument($docId);
        
        if($doc)
        {
            if($doc->isInternal())
                $this->removeInternal($doc);
            else
                $this->removeExternal($doc);    
        }
        else
            return $this->_helper->redirector('index');
            
        
    }
    
    private function removeExternal($doc)
    {
        $service = new App_Service_DocumentService();
        
        $service->deleteDocument($doc);
        
        return $this->_helper->redirector('list',App_Resources::DOCUMENT);
    }
    
    private function removeInternal($doc)
    {
        $file = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $doc->getUrl();

        unlink($file);
        
        $service = new App_Service_DocumentService();
        
        $service->deleteDocument($doc);

        return $this->_helper->redirector('list',App_Resources::DOCUMENT);
    }

}