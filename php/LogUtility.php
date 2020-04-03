<?php
	
	function WriteLog($fileName, $str)
	{
		// 今日の日付取得
		$now = date('Y-m-d');
		
		// ファイル名は日付のテキストファイル
		$fileName = 'log/' . $now . '_' . $fileName . '.txt';
		
		//  ファイルを開く
		$fp = fopen($fileName, 'a');

		$date = date('Y-m-d H:i:s');

		// 内容を書き込む
		fwrite($fp, $date . ' ' . $str . "\n");
		
		// ファイルを閉じる
		fclose($fp);
	}

	//
	// エラーログを出力する
	// 
	function WriteErrorLog($exception)
	{
		// 今日の日付取得
		$now = date('Y-m-d');
		
		// ファイル名は日付のテキストファイル
		$fileName = 'log' . $now . '_Error.txt';
		
		//  ファイルを開く
		$fp = fopen($fileName, 'a');

		$date = date('Y-m-d H:i:s');

		// 内容を書き込む
		fwrite($fp, $date . ' ' . $exception->getMessage());
		
		// ファイルを閉じる
		fclose($fp);
	}
?>