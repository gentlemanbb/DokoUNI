<?php

	require_once "./Utility/LogUtility.php";
		
	final class DBAccessor
	{
		// データベースハンドル
		private $dbh;
		private $logger;
		
		// ***
		// * コンストラクタ
		// *****************
		function __construct()
		{
			$logger = new Logger();
		}
		
		// ***
		// * DB接続をします。
		// *******************
		public function DBConnect()
		{
			try
			{
				// DBユーザー
				$user = 'zawanet_admin';
				
				// DBパスワード
				$password = '9700SawaOishi';
				
				// 接続文字列の生成
				$connectionString = sprintf(
					'mysql:host=%s;dbname=%s;'
					,'mysql5023.xserver.jp'
					, 'zawanet_dokouni');
					
				$pdo = new PDO(
					$connectionString,
					$user,
					$password,
					array(PDO::ATTR_EMULATE_PREPARES => false)
				);
			}
			catch(PDOException $e)
			{
				// 接続に失敗した場合
				exit('接続失敗'.$e->getMessage());
			}
			
			return $pdo;
		}
		
		// --------------------
		//  ＳＥＬＥＣＴ
		// --------------------
		public function Select($query)
		{
			// DBハンドルを介してクエリを実行
			$result = $this->dbh->query($query);
			
			if (!$result)
			{
				// 実行に失敗した場合
				return null;
			}
			
			// 結果を返す
			return $result;
		}
		
		// --------------------
		//  ＩＮＳＥＲＴ
		// --------------------
		public function Insert($query)
		{
			// DBハンドルを介してクエリを実行
			$result = $this->dbh->query($query);
			
			if (!$result)
			{
				// 実行に失敗した場合
				logger.WriteLog($query, LogLevel::ERROR);
				die('INSERT error');
			}
			
			return $result;
		}
		
		// --------------------
		//  ＵＰＤＡＴＥ
		// --------------------
		public function Update($query)
		{
			// DBハンドルを介してクエリを実行
			$result = $this->dbh->query($query);
			
			if (!$result)
			{
				// 実行に失敗した場合
				logger.WriteLog($query, LogLevel::ERROR);
				die('UPDATE error');
			}
			
			return $result;
		}
		
		// --------------------
		//  ＤＥＬＥＴＥ
		// --------------------
		public function Delete($query)
		{
			// DBハンドルを介してクエリを実行
			$result = $this->dbh->query($query);
			
			if (!$result)
			{
				// 実行に失敗した場合
				logger.WriteLog($query, LogLevel::ERROR);
				die('DELETE error');
			}
			
			return $result;
		}
	}
	
	// ***
	// データクラス
	// ******
	final class DataRows
	{
		// 行
		public $Rows;
		
		// コンストラクタ
		function __construct()
		{
			// 配列を初期化
			$this->Rows = array();
		}
		
		// 追加
		function Add($newRow)
		{
			array_push($this->Rows, $newRow);
		}
	}

?>

