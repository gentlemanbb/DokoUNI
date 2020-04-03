<?php

	/**
	* Class php:LogLevel
	* @package App\Enums
	*/
	final class ReturnCode
	{
		public const SUCCESS = 1;
		public const CANCEL = 2;
		public const ERROR = -1;
		
		private $value;
		
		// ***
		// * コンストラクタ
		// ************
		public function __construct(int $value)
		{
			$this->value = $value;
		}
		
		// ***
		// * 値を取得
		// ******
		public function getValue()
		{
			return $this->value;
		}
		
		// ***
		// * 
		// *****
		public function equalsTo(Suit $other)
		{
			return $this->value === $other->getValue();
		}
		
	}
?>