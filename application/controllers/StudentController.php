<?php
 
class StudentController extends Zend_Controller_Action
{

	protected $studentService;
	protected $userID = 2; //using a test value of '2' for user id
		
	public function preDispatch()
	{
		 $this->studentService = new App_StudentService();
	}
	
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    $this->view->message = "in student index action";
    $this->view->userid = $this->userID;
	
        //assign to $username value of getUserName function
  	 	$username = $this->studentService->GetUserName($this->userID);
    	//create variable name for index.phtml to use
     	$this->view->name = $username;
   
    	//assign to roledesc value of GetRoleName function
    	$userprogram = $this->studentService->GetRoleName($this->userID);
    	//create variable roledesc for index.phtml to use under current program
    	$this->view->roledesc = $userprogram;
    	
    	//assign to uploadrows value from getUploads function
    	//uploadrows holds 5 latest upload artifacts unlinked
 		$uploadrows = $this->studentService->GetUploads($this->userID);
 		//create variable uploads for index.phtml to use under recent uploads
    	$this->view->uploads = $uploadrows;
    	
    	//assign to links value from GetLinkedArtifacts function
    	//uploadrows holds 5 latest linked artifacts
    	$linkedrows = $this->studentService->GetLinkedArtifacts($this->userID);
    	//create variable links for index.phtml to use under linked artifacts
    	$this->view->links = $linkedrows;
    	
    	//assign to submit value from GetSubmittedArtifacts function
    	//uploadrows holds 5 latest submitted artifacts
    	$submittedrows = $this->studentService->GetSubmittedArtifacts($this->userID);
    	//create variable submit for index.phtml to use under recent submissions
    	$this->view->submit = $submittedrows;
    	
		 //assign to evaluated value from GetEvaluatedArtifacts function
    	//uploadrows holds 5 latest evaluated artifacts
    	$evalrows = $this->studentService->GetEvaluatedArtifacts($this->userID);
    	//create variable evaluated for index.phtml to use under recent feedback
    	$this->view->evaluated = $evalrows;

    }
}