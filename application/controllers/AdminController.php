<?php
/**
* This class implements the following funcitonality for the admin.
* Basic functionality includes User Management (Create and Modify), 
* and the Adjustment of Paraish Parameters
*/
class AdminController extends Zend_Controller_Action
{  
	/**
	* Initalizes any data needed globally in the controller.
	*
	* @return null
	*/
    public function init()
    {
        /* Initialize action controller here */
        $this->view->pageTitle = "Admin Controller";
    }
    
    /**
	* Default landing page for the admin. Redirects to the list view of users
	*
	* @return null
	*/
    public function indexAction()
    {
        $this->view->pageTitle = "Admin Page";
        return $this->_helper->redirector('users');
    }
    
	/**
	* Takes an AdjustForm and populates it with the current Paraish_Param 
	* values pulled from registry.
	*
	* @param Application_Model_Admin_AdjustForm $form Form to be populated
	*
	* @return null
	*/
    private function initAdjustmentForm($form)
    {
        $config = Zend_Registry::get('config');

        $form->aid->setValue($config->getLifeTimeLimit());
        $form->casefund->setValue($config->getCaseFundLimit());
        $form->lifetimecases->setValue($config->getCaseLimit());
        $form->yearlycases->setValue($config->getYearlyLimit());
        
        $this->view->headScript()->appendFile($this->view->baseUrl('admin.js'));
        $this->view->headScript()->appendFile($this->view->baseUrl('utility.js'));
    }
    /**
	* Handles all admin interactions with the parameter adjustment page. 
	* When requested with a GET, form will simply be populated and displayed.
	* When 'requested' with a POST, form will be validated and redisplayed if
	* there are any errors
	*
	* @return null
	*/
    public function adjustAction()
    {
        $request = $this->getRequest();
        
        $this->view->pageTitle = "Admin Limit Adjustments";
        
		// Create form and assign it to the view
        $form = new Application_Model_Admin_AdjustForm();
        $this->view->form = $form;
        
        // Check the form if post
        if( $request->isPost() ){
            return $this->handleAdjustmentForm($form);
        }
        else // GET request. Initalize form and display it
            $this->initAdjustmentForm($form);
            
        return;
    }
	
    /**
	* Takes an AdjustForm and checks to ensure it is valid. If valid persists data
	* to the config in the registry and the database. If invalid sets errors on form.
	*
	* @param Application_Model_Admin_AdjustForm $form Form to be validated
	*
	* @return null
	*/
    private function handleAdjustmentForm($form)
    {
		// Check if form valid
        if( $form->isValid($_POST))
        {
            // Get Form Values
            $lifetimeLimit = $form->getValue('aid');
            $yearlyLimit = $form->getValue('yearlycases');
            $lifetimeCases = $form->getValue('lifetimecases');
            $caseFund = $form->getValue('casefund');
            
			// Update values in the Registry
            $config = Zend_Registry::get('config');
            $config->setYearlyLimit($yearlyLimit);
            $config->setLifeTimeLimit($lifetimeLimit);
            $config->setCaseLimit($lifetimeCases);
            $config->setCaseFundLimit($caseFund);
            
			// Update values in the database
            $service = new App_Service_AdminService();
            $service->updateParishParams($config);
        
            // Redirect user
            $this->_forward('index', App_Resources::REDIRECT, null,
                    Array( 'msg' => 'Limits have been adjusted successfully!',
                           'time' => 3,
                           'controller' => App_Resources::ADMIN,
                           'action' => 'adjust'));
        }
    }

    /**
	* Displays all the users information
	*
	* @return null
	*/
    public function usersAction()
    {
        $this->view->pageTitle = "Admin Viewing Users";

		// retrieve the list of users
        $service = new App_Service_AdminService();
        $users   = $service->getAllUsers();

        $this->view->users = array();
        $lastRowLetter     = null;

		// Set which users will have anchors on the list page
        foreach ($users as $userId => $user) {
            $firstName = $user->getFirstName();

			// If the last's row letter is not equal to the current user
			// set the last and row letter as this current letter.
            if ($lastRowLetter !== $firstName[0]) {
                $lastRowLetter = $rowLetter = $firstName[0];
            } else { // Assign last to null since this letter has alreayd been assigned
                $rowLetter = null;
            }
			// Store array of the user and their row letter
            $this->view->users[$userId] = array(
                'user' => $user,
                'rowLetter' => $rowLetter,
            );
        }
    }
	
    /**
	* On a GET request simply displays the form to create a new user. On a
	* POST request, calls handleNewForm to validate form
	*
	* @return null
	*/
    public function newAction()
    {
        $request = $this->getRequest();
        
        $form = new Application_Model_Admin_NewUserForm();
        $this->view->pageTitle = "Admin New Member Contact";
        $this->view->form = $form;

        $this->view->headScript()->appendFile($this->view->baseUrl('admin.js'));
        $this->view->headScript()->appendFile($this->view->baseUrl('utility.js'));
        
        
        // Verify Post
        if( $request->isPost() ){
            $this->handleNewForm($form);
        }
        
    }
	/**
	* Takes an NewUserForm and checks to ensure it is valid. If valid
	* builds a new User_Impl object from the form data. 
	*
	* @param Application_Model_Admin_NewUserForm $form Form to be validated
	*
	* @return null
	*/
    private function handleNewForm($form)
    {
        $form->populate($_POST);
        
        $error = false;
        if(!$form->isValid($_POST))
        {
            $error = true;
        }   
        // Check to ensure atleast one phone number was provided
        if($form->getValue('cell') === '' && $form->getValue('home') === '')
        {
            $form->cell->addError('');
            $form->home->addError('Either cell or home phone must be provided');
            
            $error = true;
        }

        // If theres an error return
        if($error)
            return;
        
		// Create User
        $user = new Application_Model_Impl_User();
        $user // Normalize the first and last name
            ->setFirstName(ucfirst($form->getValue('firstname')))
            ->setLastName(ucfirst($form->getValue('lastname')))
            ->setEmail($form->getValue('email'))
            ->setCellPhone($form->getValue('cell'))
            ->setHomePhone($form->getValue('home'))
            ->setRole($form->getValue('role'))
            ->setActive(1); // Default user to active
        $this->createUser($user);
    }
    
	/**
	* Takes an Application_Model_Impl_User and takes care of the logic
	* for creating the actual user. Generates username, password, and 
	* persists the user's data to the database
	*
	* @param Application_Model_Impl_User $user User to be created
	*
	* @return null
	*/
    private function createUser($user)
    {
        $service = new App_Service_AdminService();
        
        // Generate the user name
        $userName = substr($user->getFirstName(),0,1);
        $userName = $userName . $user->getLastName();
        $userName = strtolower($userName);
        $userName = $userName . $service->getNextIdNum($userName);
        // Store user name
        $user->setUserId($userName);
        
        // Generate user password and create user
        $password = App_Password::generatePassword(10);
        $service->createParishMemeber($user,$password);
     
        // Send email for new user
        $mail = new Zend_Mail('utf-8');
        $transport = new App_Mail_Transport_AmazonSES(
        array(
            'accessKey' => Zend_Registry::get("AWS_ACCESS_KEY_ID"),
            'privateKey' => Zend_Registry::get("AWS_SECRET_ACCESS_KEY"),
        ));
        
        $mail->setBodyHtml('You have been added to the SVDP organization.' .
                           '<br/>Username: <b>' . $userName . '</b>' .
                           '<br/>Password: <b>' . $password . '</b>' . '</br></br> Please note ' .
                           'you will be required to change your password on first login.' .
                           '<br/><br/><i>If you believe you have received this message in error ' .
                           'please contact the sender.</i>');
        
        $mail->setFrom('noreply@raphaelsvdp.org', 'System');
        $mail->addTo($user->getEmail());
        $mail->setSubject('SVDP Account Created');
        $mail->send($transport);

        // Redirect user
        $this->_forward('index', App_Resources::REDIRECT, null,
                    Array( 'msg' => 'Member added successfully!',
                           'time' => 3,
                           'controller' => App_Resources::ADMIN,
                           'action' => 'users'));
    }
    
	/**
	* Provides interface for modifying an exisiting user. When called with GET
	* request form will be displayed. When called with POST form will be
	* validated.
	*
	* @return null
	*/
    public function modifyAction()
    {
        $this->view->pageTitle = "Admin Modify Member";
        
        // Get request and passed parameter
        $request = $this->getRequest();
        
        $form = new Application_Model_Admin_ModifyUserForm();
        // Handle display of form
        if( $request->isGet() && $this->_hasParam('id') )
        {
            $userId = $request->getParam('id');
        
            
            // Get the users data
            $service = new App_Service_AdminService();
            $user = $service->getUserById($userId);
        
            $this->view->form = $form;
        
            // Set form default values
            $this->view->form->userid->setValue($user->getUserId());
            $this->view->form->firstname->setValue(ucfirst($user->getFirstName()));
            $this->view->form->lastname->setValue(ucfirst($user->getLastName()));
            $this->view->form->email->setValue($user->getEmail());
            $this->view->form->cell->setValue($user->getCellPhone());
            $this->view->form->home->setValue($user->getHomePhone());
            $this->view->form->role->setValue($user->getRole());
            $this->view->form->status->setValue($user->getActive());
            
            return;
        }
        elseif( $request->isPost())
        {
            $this->handleModifyForm($form);
            $this->view->form = $form;
            return;
        }
        
        return $this->_helper->redirector('index'); 
    }
    
	/**
	* Takes an Application_Model_Admin_ModifyUserForm and validates it and
	* sets any error messages if needed.
	*
	* @param Application_Model_Admin_ModifyUserForm $form From to be validated
	*
	* @return null
	*/
    private function handleModifyForm($form)
    {
        $error = false;
        // return if the form is not valid
        if( !$form->isValid($_POST) )
            $error = true;

        // Check to ensure atleast one phone number was provided
        if($form->getValue('cell') === null && $form->getValue('home') === null)
        {
            $form->cell->addError('');
            $form->home->addError('Either cell or home phone must be provided');
            
            $error = true;
        }
        
        $service = new App_Service_AdminService();
        $user = $service->getUserById($form->getValue('userid'));
        
        // Check if this is the only admin.. this is a bit ugly..
        $isOnlyAdmin = $service->getNumAdmins() == 1;
        $isOnlyAdmin = $isOnlyAdmin && ($user->getRole() === App_Roles::ADMIN);
        $isOnlyAdmin = $isOnlyAdmin && $user->isActive();

        // Check if the only admins role is being changed
        if( $isOnlyAdmin && $form->getValue('role') !== App_Roles::ADMIN )
        {
            $form->role->addError('');
            $form->roleErr->addError('Only admin cannot have role changed.');
            $error = true;
        }
        
        if( $isOnlyAdmin && $form->getValue('status') != 1 )
        {
            $form->status->addError('Only admin cannot be set to inactive.');
            $error = true;
        }
        
        // Return if there were any errors
        if($error)
            return $error;
        
        // Everything is good update in database
        
        $user = new Application_Model_Impl_User();
        $user
            ->setUserId($form->getValue('userid')) // Normalize first and last name
            ->setFirstName(ucfirst(strtolower($form->getValue('firstname'))))
            ->setLastName(ucfirst(strtolower($form->getValue('lastname'))))
            ->setEmail($form->getValue('email'))
            ->setCellPhone($form->getValue('cell'))
            ->setHomePhone($form->getValue('home'))
            ->setRole($form->getValue('role'))
            ->setActive($form->getValue('status'));
        // Persist to database
        $service->updateUserInformation($user);
        
        $this->_forward('index', App_Resources::REDIRECT, null,
                        Array( 'msg' => 'User Data Updated Successfully!',
                               'time' => 3,
                               'controller' => App_Resources::ADMIN,
                               'action' => 'users'));   
    }
}
