<?php

	class DBAccess
	{
		private $dbh;
		
		function __construct()
		{
		
		}
		
		public function DBConnect()
		{
			// $server = 'sv1.php.xdomain.ne.jp';
			$dsn = 'mysql:dbname=zawanet_dokouni;host=mysql5023.xserver.jp';
			$user = 'zawanet_admin';
			$password = '9700SawaOishi';
			try
			{
				$this->dbh = new PDO($dsn, $user, $password);
			}
			catch(PDOException $e)
			{
				print('Error:'.$e->getMessage());
				die();
			}
			
			return $dbh;
		}
		
		public function DBConnect2()
		{
			try
			{
				$user = 'zawanet_admin';
				$password = '9700SawaOishi';
				$connectionString = sprintf(
					'mysql:host=%s;dbname=%s;'
					,'mysql5023.xserver.jp'
					, 'zawanet_dokouni');
				
				$pdo = new PDO(
					$connectionString,
					$user,
					$password,
				
				array(PDO::ATTR_EMULATE_PREPARES => false));
			}
			catch(PDOException $e)
			{
				exit('接続失敗'.$e->getMessage());
			}
			return $pdo;
		}
	
		// --------------------
		//  ＳＥＬＥＣＴ
		// --------------------
		public function Select($query)
		{
			$result = $this->dbh->query($query);
			
			if (!$result)
			{
				return null;
			}
			
			return $result;
		}
		
		// --------------------
		//  ＩＮＳＥＲＴ
		// --------------------
		public function Insert($query)
		{
			$result = $this->dbh->query($query);
			if (!$result) {
				print($query);
				die();
			}
		
			return $result;
		}
	
		// --------------------
		//  ＵＰＤＡＴＥ
		// --------------------
		public function Update($query)
		{
			$result = $this->dbh->query($query);
			
			if (!$result) {
				print('Query Error');
				die();
			}
		
			return $result;
		}
	
		// --------------------
		//  ＤＥＬＥＴＥ
		// --------------------
		public function Delete($query)
		{
			$result = $this->dbh->query($query);
		
			if (!$result)
			{
				die('query error');
			}
		
			return $result;
		}
	}

	class DataRows
	{
		public $Rows;
		function __construct()
		{
			$this->Rows = array();
		}
	
		function Add($newRow)
		{
			array_push($this->Rows, $newRow);
		}
	}

?>
