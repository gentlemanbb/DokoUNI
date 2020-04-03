<?php

// ***
// * ログ出力クラス
// ****************
final class Logger
{
	/**
	* Class php:LogLevel
	* @package App\Enums
	*/
	public const INFO = 1;
	public const WARNING = 2;
	public const ERROR = 3;
	public const DEBUG = 4;
	
	// ***
	// * コンストラクタ
	// ************
	public function __construct()
	{
	}
	// ***
	// * ログ出力
	// ********************
	function WriteLog(string $str, int $logLevel)
	{
		// ファイル名は日付のテキストファイル
		$fileName = sprintf("log/%s_%s.txt", date('y-m-d'), $fileName);
		Write($fileName, $str, $logLevel);
	}
	
	// ***
	// ログ出力
	// @param1 ファイル名
	// @param2 出力文字列
	// @param3 ログレベル
	// ********************
	function Write($fileName, $str, $logLevel)
	{
		$logLevelStr = "";
		
		// ログレベル
		switch($logLevel)
		{
			case INFO:
				$logLevelStr = "INFO";
				break;
				
			case WARNING:
				$logLevelStr = "WARN";
				break;
				
			case ERROR:
				$logLevelStr = "ERROR";
				break;
				
			case _DEBUG:
				$logLevelStr = "DEBUG";
				break;
		}
		
		//  ファイルを開く
		$fp = fopen($fileName, 'a');
		
		// 日付の文字列
		$date = date('Y-m-d H:i:s');
		
		// ログ文字列
		$logStr = sprintf('%s 【%s】 %s', $logLevelStr, $date, $str);
		
		// 内容を書き込む
		fwrite($fp, $str . "\n");
		
		// ファイルを閉じる
		fclose($fp);
	}
	
	// ***
	// * エラーログを出力する
	// *********************************
	function WriteErrorLog($exception)
	{
		// ファイル名は日付のテキストファイル
		$fileName = sprintf("log/%s_%s.txt", date('y-m-d'), $fileName);
		
		WriteLog($fileName, $exception->getMessage(), LogLevel::ERROR);
	}
}
?>