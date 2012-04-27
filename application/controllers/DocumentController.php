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
        $this->view->pageTitle = "Document Controller";
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