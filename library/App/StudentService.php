<?php 

class App_StudentService {
	protected $db;
   
	protected $user;
	protected $artifact;
	protected $artifact_rating;
	protected $artifact_indicator_status;
	protected $reflective_statement;
	protected $reflective_statement_rating;
	protected $reflective_statement_status;
	protected $role;
	protected $standard;
	protected $indicator;
   
	function __construct(){
     	$options = array(
   	 		'host' => 'localhost', 
   			'username' => 'cstclair', 
   			'password' => 'cstclair',
   			'dbname' => 'matrix_stclair'
   		);
   		$this->db = Zend_Db::factory('PDO_MYSQL', $options);
   		Zend_Db_Table_Abstract::setDefaultAdapter($this->db);
   		$this->user = new App_UserTable();
   		$this->artifact = new App_ArtifactTable();
   		$this->artifact_rating = new App_ArtifactRatingTable();
   		$this->artifact_indicator_status = new App_ArtifactIndicatorStatusTable();
   		$this->reflective_statement = new App_ReflectiveStatementTable();
   		$this->reflective_statement_rating = new App_ReflectiveStatementRatingTable();
   		$this->reflective_statement_status = new App_ReflectiveStatementStatusTable();
   		$this->role = new App_RoleTable();
   		$this->standard = new App_StandardTable();
   		$this->indicator = new App_IndicatorTable();
	}

	//Database function to retrieve the full name of the user with the given id number
	public function GetUserName($student_id)
	{
		$select = $this->user->select()->where('user_id = ?', $student_id);
		return $this->user->fetchRow($select);
	}
	
	//Database function to retrieve the role of the user with the given id number
	public function GetRoleName($student_id)
	{
		$select = $this->user->select()
			->from(array('u' =>'user'), array('u.role'))
			->where('user_id = ?', $student_id);
		$desc = $this->role->select()
			->from(array('r' =>'role'), array('r.description'))
			->where('role_code = ?', $select);
		return $this->role->fetchRow($desc);
	}
   
   //Database function to retrieve the uploaded artifacts for the user with the given id number
   //will return only those records that are in the artifact table but NOT in the
   //artifact_indicator_table, meaning those that are not yet linked
   //will return only the five most recent uploads 
	public function GetUploads($student_id)
	{
   		$selectAIS = $this->artifact_indicator_status->select()
   			->from(array('ais' => 'artifact_indicator_status'),array('ais.artifact_id'));
   		$select = $this->artifact->select()->where('student_id = ?', $student_id)
   			->where('artifact_id NOT IN (?)', $selectAIS)
   			->order('artifact_id DESC')
   			->limit(5);
   		return $this->artifact->fetchAll($select);
	}
   
	//Database function to retrieve the linked artifacts for the user with the given id number
    //will return only those records that are in the artifact_indicator_status table 
    //and have a status of 'L' (linked)
    //will return only the five most recent links 
	public function GetLinkedArtifacts($student_id)
	{
   		$select = $this->db->select()
   			->from(array('a' => 'artifact'), array('artifact_id', 'artifact_title'))
   			->join(array('ais'=>'artifact_indicator_status'), 'a.artifact_id = ais.artifact_id')
   			->join(array('c'=>'course'), 'a.course_id = c.course_id')
   			->join(array('i'=> 'indicator'), 'i.indicator_id = ais.indicator_id')
   			->join(array('s'=> 'standard'), 'i.standard_id = s.standard_id')
   			->where('ais.status_code = ?', 'L')
   			->where('a.student_id = ?', $student_id)
   			->order('artifact_indicator_status_id DESC')
   			->limit(5);
   			
		$result = $this->db->fetchAll($select);
		return $result;
	}
   
	//Database function to retrieve the submitted artifacts for the user with the given id number
    //will return only those records that are in the artifact_indicator_status table 
    //and have a status of 'S' (submitted)
    //will return only the five most recent submissions 
	public function GetSubmittedArtifacts($student_id)
	{
   		$select = $this->db->select()
   			->from(array('a' => 'artifact'), array('artifact_id', 'artifact_title'))
   			->join(array('ais'=>'artifact_indicator_status'), 'a.artifact_id = ais.artifact_id')
   			->join(array('c'=>'course'), 'a.course_id = c.course_id')
   			->join(array('i'=> 'indicator'), 'i.indicator_id = ais.indicator_id')
   			->join(array('s'=> 'standard'), 'i.standard_id = s.standard_id')
   			->join(array('ar'=>'artifact_rating'), 'ar.artifact_id = ais.artifact_id')
   			->join(array('u'=>'user'), 'ar.rating_user_id = u.user_id')
   			->where('ais.status_code = ?', 'S')
   			->where('a.student_id = ?', $student_id)
   			->order('artifact_indicator_status_id DESC')
   			->limit(5);
   			
   		$result = $this->db->fetchAll($select);
		return $result;
	}
	
	//Database function to retrieve the evaluated artifacts for the user with the given id number
    //will return only those records that are in the artifact_indicator_status table 
    //and have a status of 'E' (evaluated)
    //will return only the five most recent evaluations 
	public function GetEvaluatedArtifacts($student_id)
	{
		$select = $this->db->select()
   			->from(array('a' => 'artifact'), array('artifact_id', 'artifact_title'))
   			->join(array('ais'=>'artifact_indicator_status'), 'a.artifact_id = ais.artifact_id')
   			->join(array('c'=>'course'), 'a.course_id = c.course_id')
   			->join(array('i'=> 'indicator'), 'i.indicator_id = ais.indicator_id')
   			->join(array('s'=> 'standard'), 'i.standard_id = s.standard_id')
   			->join(array('ar'=>'artifact_rating'), 'ar.artifact_id = ais.artifact_id')
   			->join(array('u'=>'user'), 'ar.rating_user_id = u.user_id')
   			->join(array('r' => 'rating'), 'ar.rating_code = r.rating_code')
   			->where('ais.status_code = ?', 'E')
   			->where('a.student_id = ?', $student_id)
   			->order('artifact_indicator_status_id DESC')
   			->limit(5);
   			
   		$result = $this->db->fetchAll($select);
		return $result;
	}
	
	//find the standards for a specific program
	//used to display student manage portfolio page
   public function GetStandardsbyProgram($program)
	{
		$select = $this->standard->select()
		->where('program = ?', $program);
		
		return $this->standard->fetchAll($select);
	}
	//find the role of a specific student
	//used to get role code for the standards page
	public function GetRolebyStudentId($student_id)
	{
	$select = $this->user->select()
			->where('user_id = ?', $student_id);
			
	return $this->user->fetchRow($select);
	}
	
	public function GetIndicatorsbyStandard($standard_id)
	{
		$select = $this->db->select()
		->from(array('i' => 'indicator'), array('i.indicator_id', 'i.description', 'i.indicator_number'))
		->where('i.standard_id = ?', $standard_id);
		
		return $this->db->fetchAll($select);
	}
	
	public function GetArtifactsbyIndicator($student_id, $indicator_id)
	{
		$select = $this->db->select()
   			->from(array('a' => 'artifact'), array('artifact_id', 'artifact_title'))
   			->join(array('ais'=>'artifact_indicator_status'), 'a.artifact_id = ais.artifact_id')
   			->where('ais.indicator_id = ?', $indicator_id)
   			->where('a.student_id = ?', $student_id);
   			
   		$result = $this->db->fetchAll($select);
		return $result;
	}
	
	public function GetIndicatorNumbyIndicatorId($indicator_id)
	{
		$select = $this->indicator->select()
		->where('indicator_id = ?', $indicator_id);
		return $this->indicator->fetchRow($select);
	}
}   
  
