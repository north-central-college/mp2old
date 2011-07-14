<?php

/**
 * 
 * KG and PA
 *
 * After logging in with LDAP, the cookie from the site must be deleted before logging in
 * again. This should probably be implemented eventually in the logout function.
 */

class IndexController extends Zend_Controller_Action
{
    public function init()
    {
    }

    /**
     * KG and PA
     * called when index page loads. This function creates the view for the login page.
     */
    public function indexAction()
    {
    	//If there is an error flag set, retrieve it so we can display the error
    	$this->view->error_flag = $this->getRequest()->getParam('error_flag');
		  	
        // Retrieve the form and assign it to the view
		$this->view->form = $this->getForm();
		
	 }
    
    /**
     * KG and PA
     * creates and returns the login form
     */
    public function getForm()
    {
    	return new Application_Model_LoginForm(array(
			/* don't think next two lines are needed */
    		// 'action' => 'login/process',  
    		// 'method' => 'post',
    	));
    }
    
    
    /**
     * KG and PA
     * 
     *  If the user is already authenticated, but has not requested to logout,
     *  we should redirect to the home page
     *  If the user is not authenticated, but has requested to logout, 
     *  we should redirect to the login page
     */

    public function preDispatch()
    { // checks first to see if user is logged in, if not exits function
      if (Zend_Auth::getInstance()->hasIdentity())
    	{

    		// If the user is logged in, we do not want to show the login form;
    		// however, the logout action should be still available
    		if ('logout' != $this->getRequest()->getActionName())
    		{   // redirector('action name', 'controller')
    			$this->_helper->redirector('index', 'index');
    		}
    		else
    		{
    			// If they aren't they can't logout, so that action should
    			// redirect to the login form
    			if ('logout' == $this->getRequest()->getActionName())
    			{
    				$this->_helper->redirector('logout');
    			}
    		}
    	}
    	print ("in predispatch");
    }
    
    
    /**
     * KG and PA
     * Log the user in and display the correct homepage
     * This function is called when the login button is pressed.
     * 
     * First it checks the entered username and password to determine 
     * whether the inputs could be considered valid (long enough username and password,
     * only alpha-numeric characters, etc...)
     * If not, it informs the user and stops executing
     * 
     * Then it queries the matrix user database to determine if the user is actually
     * in the database. If the user was found, it verifies their password with LDAP
     *
     * If the username/password combination was correct, the function queries the user database
     * to determine whether to display the student or faculty page. Otherwise, 
     * take the user back to the login screen and inform them of error.
     */
    
    public function processAction()
    {
    
    	// check if login data entered
    	$request = $this->getRequest();
    	
        // If we don't have a POST request, go back to login 
        if (!$request->isPost()) {
            return $this->_helper->redirector('index');
        }
        
        // Get our form and validate it
        $form = $this->getForm();
        // Validate username and password for matching criteria

        if (!$form->isValid($request->getPost())) 
        {
            // Redirect to the login page and set error flag	
			$this->_redirect('/index/index/error_flag/TRUE');
        	exit();
        }
        //Get username and password
        $username = $form->getValue('username');
        $password = $form->getValue('password');
        	
 $this->_helper->redirector('index', 'student');
        	
        /*
        
        //check whether user exists in the user table
        $userService = new App_UserService();
        $valid = $userService->ValidUser($username);
        
        //If the user exists, validate password with LDAP
        if($valid)
        {
	        $auth = Zend_Auth::getInstance();
	        $authAdapter = new Zend_Auth_Adapter_Ldap(
	                                   array(
	                                           'server' => array(
	                                           'host' => 'ldap.nccnet.noctrl.edu',
	                                           'baseDn' => 'OU=Napvil,O=NCC',
	                                           'bindRequiresDn' => true,
	                                                                   ),
	                                   ), $username, $password
	                           );
	      	$authResult = $auth->authenticate($authAdapter);
	      	if ($authResult->isValid())
	       	{
	       		$valid = TRUE;
	       	} 
	       	else
	       	{
	       		$valid = FALSE;
	       	}
        }
        
        
        if ($valid)
        {
        	$this->view->error_flag = FALSE;
        	$userRole = $userService->GetUserRole($username);
       		
        	if ($userRole == 'U' || $userRole == 'L' || $userRole == 'G')
        	//user is a student
        	{
        		$this->_helper->redirector('index', 'student');
        	}
        	else
        	//user is faculty
        	{
        		$this->_helper->redirector('index', 'faculty');
        	}
        	
        }
        else
        {
        	// Redirect to the login page and set error flag	
			$this->_redirect('/index/index/error_flag/TRUE');
        	exit();
        }
    	*/
      }
     
   
    
    /**
     * Call this function to log out of session
     * 
     * 
     */
	public function logoutAction()
	{
		Zend_Auth::getInstance()->clearIdentity();
		// Go back to the login page
		$this->_helper->redirector('index', 'index'); 
	}
}