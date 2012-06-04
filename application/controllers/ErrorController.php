<?php
/**
 * Class catches all exceptions the application throws
 * and displays the appropriate error messages
 */
class ErrorController extends Zend_Controller_Action
{
	/**
	* Action catches exceptions and displays the appropriate message.
	* Action will also create messages in the log
	*
	* @return null
	*/
    public function errorAction()
    {
        $this->view->pageTitle = 'Error Page';
        $errors = $this->_getParam('error_handler');

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->message = 'Page not found';
                break;
            case App_ErrorType::FORBIDDEN:
                $this->getResponse()->setHttpResponseCode($errors->exception->getCode());
                $priority = Zend_Log::NOTICE;
                $this->view->message = $errors->exception->getMessage();
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->message = 'Sorry! There was an error processing request';
                break;
        }
        
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->log($this->view->message, $priority, $errors->exception);
            $log->log('Request Parameters', $priority, $errors->request->getParams());
        }

        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        $this->view->request   = $errors->request;
    }

	/**
	* Function returns the zend Log object
	*
	* @return Log
	*/
    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }


}

