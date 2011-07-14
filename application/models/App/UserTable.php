<?php
require_once 'Zend/Db/Table/Abstract.php';

class App_UserTable extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'user';
	
	/*added for the student landing page table*/
	protected $_referenceMap = array(
		'UserArtifacts' => array(
			'columns' => array('user_id'),
			'refTableClass' => 'ArtifactTable',
			'refColumns' => array('student_id')
		),
		'artifact' => array(
        		'columns' => array('user_id'),
        		'refTableClass' => 'ArtifactTable',
        		'refColumns' => array('student_id')
        	)
	);
}