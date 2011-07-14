<?php  
require_once 'Zend/Db/Table/Abstract.php';

class App_ArtifactTable extends Zend_Db_Table_Abstract {
	protected $_name = 'artifact';
	
	protected $_dependentTables = array('UserTable');
}

