<?php
/**
* Class implements all functionality needed for documents. Provides
* functions for uploading a doc, linking to external doc, and 
* creating the view for the documents.
*/
class DocumentController extends Zend_Controller_Action
{
    /*
	* Initalizes any global data for DocumentController
	*
	* @return null
	*/
    public function init()
    {
        /* Initialize action controller here */
    }
    
	/*
	* Provides the interface for displaying the list
	* of documents to the user
	*
	* @return null
	*/
    public function listAction()
    {
        $this->view->pageTitle = "Document List";
        
        $service = new App_Service_DocumentService();
        $this->view->docs = $service->getDocuments();
        
        $this->setPartial();
    }
    
	/*
	* Determines which partial to use based on the current users role.
	* This partial is responsible for displaying the list of documents
	* and providing the roles their proper functions pertaining to documents
	*
	* @return null
	*/
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
    
    /*
	* Handles the interface for uploading a document. GET displays the
	* form while POST validates the form
	*
	* @return null
	*/
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
	
    /**
	 * Handles logic for validating a file upload form
	 *
	 * @param Application_Model_Document_UploadForm $form Upload form to be validated
	 *
	 * @return null
	 */
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
    /*
	* Handles logic for saving a file to disk
	*
	* @param Application_Model_Document_UploadForm $form Validated form holding document
	*
	* @return null
	*/
    private function saveFile($form)
    {
        // Set up file transfer        
        $upload = new Zend_File_Transfer_Adapter_Http();
        $uploadDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
        $upload->setDestination($uploadDir);
        
	// Create the document
        $doc = new Application_Model_Impl_Document();
        $doc
            ->setId(null)
            ->setUrl($_FILES['url']['name'])
            ->setName($form->getValue('name'))
            ->setInternal(1);
        
        // get the files extension
        $ext = (false === $pos = strrpos($doc->getUrl(), '.')) ? '' : substr($doc->getUrl(), $pos);
        
        // get the files name
        $fileName = basename($doc->getUrl(),$ext);
        
        // added number for name to fix naming conflicts
        $fileNum = 0;
        
        // Get the next number for this file
        while( file_exists($uploadDir . $doc->getUrl()) )
        {
            $fileNum = $fileNum + 1;
            // Change the documents URL
            $doc->setUrl($fileName . '(' . $fileNum . ')' . $ext);
        }
        
        // Set up file transfer        
        $upload = new Zend_File_Transfer_Adapter_Http();
        $upload->setDestination($uploadDir);
        
        // Rewrite destination with the file number appended
        if( $fileNum != 0)
        {
            $upload->addFilter('Rename', array('target' => $uploadDir . $doc->getUrl(),
                                               'overwrite' => true));
        }
        
        $upload->receive();

        // Persist document to database
        $service = new App_Service_DocumentService();
        $service->createDocument($doc);

        // Redirect user
        $this->_forward('index', App_Resources::REDIRECT, null,
                    Array( 'msg' => 'SVDP Document Uploaded Successfully!',
                           'time' => 3,
                           'controller' => App_Resources::DOCUMENT,
                           'action' => 'list'));
    }
    
    /*
	* Handles logic for adding external document (URL). GET displays form
	* and POST validates form
	*
	* @return null
	*/
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
	/*
	* Handles logic for displaying a document
	*
	* @return null
	*/
    public function displayAction()
    {
        $request = $this->getRequest();
        $docId = $request->getParam('id');
        
        // If theres no param go back to index
        if(!$docId)
            return $this->_helper->redirector('index');
         
		// Get the information about the document
        $service = new App_Service_DocumentService();
        $doc = $service->getDocument($docId);
        if($doc)
        {
            $this->_helper->viewRenderer->setNoRender(true);
            $this->view->layout()->disableLayout();
            
            $filename = APPLICATION_PATH . DIRECTORY_SEPARATOR .
                'uploads' . DIRECTORY_SEPARATOR . $doc->getUrl();
            
            // Get mime
            $mime = App_MimeConverter::getMimeType($filename);
            $downloadName = str_replace(' ','_',$doc->getName());
			// Set file properties
            $this->getResponse()
                ->setHeader('Content-Disposition', 'inline; filename=' . $downloadName)
                ->setHeader('Content-Type', $mime)
                ->setHeader('Expires', '', true)
                ->setHeader('Cache-Control', 'private', true)
                ->setHeader('Cache-Control', 'max-age=3800')
                ->setHeader('Pragma', '', true);
            readfile($filename);
            return;
        }
        
        return $this->_helper->redirector('index');
    }
	/*
	* Handles validation logic for form to add new document from URL
	*
	* @param Application_Model_Document_AddForm $form Form holding file information
	*
	* @return null
	*/
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
    
	/*
	* Deletes a document which is specifed by the Id passed in the GET query
	*
	* @return null
	*/
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
			// Delete doc in proper way
            if($doc->isInternal())
                $this->removeInternal($doc);
            else
                $this->removeExternal($doc);    
        }
        else // File did not exist (according to database)
            return $this->_helper->redirector('index');
            
        
    }
	/*
	* Handles logic for removing an external document from the site
	*
	* @param int $doc id of the document to remove from database
	*
	* @return null
	*/
    private function removeExternal($doc)
    {
        $service = new App_Service_DocumentService();
        
        $service->deleteDocument($doc);
        
        return $this->_helper->redirector('list',App_Resources::DOCUMENT);
    }
	/*
	* Handles logic for removing an internal document from the site
	*
	* @param int $doc id of the document to remove from database
	*
	* @return null
	*/
    private function removeInternal($doc)
    {
        $file = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $doc->getUrl();

        unlink($file);
        
        $service = new App_Service_DocumentService();
        
        $service->deleteDocument($doc);

        return $this->_helper->redirector('list',App_Resources::DOCUMENT);
    }

}
