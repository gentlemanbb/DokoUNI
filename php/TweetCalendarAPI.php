<?php
	
	// ライブラリの読み込み
	require "DBAccess.php";
	require "TweetAPI.php";
	require "LogUtility.php";
	
	// DBアクセサ
	$dbAccess = new DBAccess();
	$pdo = $dbAccess->DBConnect2();
	header('Content-type: application/json');
	
	try
	{
		$upload_data = $_POST['upload_data'];

		if($upload_data == null ||  $upload_data == '')
		{
			$result = [
				'RESULT' => false,
				'MESSAGE' => 'ファイルが添付されていません。' 
			];
			return json_encode($result);
		}

		$new_file_name = "../img/calendar/" . date("Ym").".png";

		if (file_exists($new_file_name))
		{
			unlink($new_file_name);
		}

		$fp = fopen($new_file_name,'w');
		fwrite($fp,base64_decode($upload_data));
		fclose($fp);

		WriteLog('TweetCalendarAPI', 'ファイルの移動に成功しました。');

		$tweetStr = '今月のイベントカレンダー';
		$tweetResult = TweetWithFile($tweetStr, $new_file_name);

		$result = null;

		if($tweetResult)
		{
			WriteLog('TweetCalendarAPI', $tweetStr . ' / ' . $new_file_name);

			$result = [
				'RESULT' => true,
				'MESSAGE' => '成功しました。' 
			];
		}
		else
		{
			$result = [
				'RESULT' => false,
				'MESSAGE' => 'ツイートに失敗しました。' 
			];
		}

	    echo json_encode($result);
	}
    catch(Exception $ex)
    {
		WriteLog('TweetCalendarAPI', $ex->getMessage());	

    	$result = [
    		'RESULT' => false,
    		'MESSAGE' => $ex.getMessage()
    	];
    	
    	echo json_encode($result);
    }
?>