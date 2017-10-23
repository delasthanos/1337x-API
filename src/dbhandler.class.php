<?php 
	// Connect to database | Singleton
	class dbhandler { 
			public $dbh; 
			public $dbCon; 
			public $dbConError; 
			private static $instance;
			
			private function __construct()
			{
				try {
					$this->dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";", DB_USER, DB_PASSWORD );
					// debug
					$this->dbh->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
					//$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					//$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
					$this -> dbCon = true; 
				}
				catch ( Exception $e )
				{
					$this -> dbCon = false; 
					$this -> dbConError = $e; 
				}
			}
			
			public static function getInstance()
			{
				if (!isset(self::$instance))
				{
				    $object = __CLASS__;
				    self::$instance = new $object;
				}
				return self::$instance;
			}
	}
?>
