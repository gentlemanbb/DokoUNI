<?php

/**
 * Implements the abstract base for all constant types
 **/
namespace App\Enums;

/**
 * Class Enum
 * @package App\Enums
 */
abstract class Enum
{
	/**
	* キャッシュ用の変数
	*
	* @var null
	*/
	private static $constCacheArray = NULL;

	/**
	 * Enum constructor.
	 *
	 * make sure there are never any instances created
	 */
	final private function __construct()
	{
	    throw new Exception( 'Enum and Subclasses cannot be instantiated.' );
	}

	/**
	 * Enum名の存在チェック
	 *
	 * @param $name
	 * @param bool $strict
	 * @return bool
	 * @throws \ReflectionException
	 */
	 public static function isValidName($name, $strict = false)
	 {
	 	$constants = self::getConstants();
		if ($strict) {
			return array_key_exists($name, $constants);
		}
		
		$keys = array_map('strtolower', array_keys($constants));
		return in_array(strtolower($name), $keys);
	}
	
	/**
	* Enumの値を存在チェック
	*
	* @param $value
	* @param bool $strict
	* @return bool
	* @throws \ReflectionException
	*/
	public static function isValidValue($value, $strict = true)
	{
		$values = array_values(self::getConstants());
		return in_array($value, $values, $strict);
	}
	
	/**
	* Enum名でEnumを取得
	* @param $name
	* @return bool
	* @throws \ReflectionException
	*/
	public static function fromString($name)
	{
		if (self::isValidName($name, $strict = true)) 
		{
			$constants = self::getConstants();
			return $constants[$name];
		}
		
		return false;
	}
	
	/**
	* EnumからEnum名を取得
	*
	* @param $value
	* @return bool|false|int|string
	* @throws \ReflectionException
	*/
	public static function toString($value)
	{
		if (self::isValidValue($value, $strict = true))
		{
			return array_search($value, self::getConstants());
		}
		
		return false;
	}
	
	/**
	* Enumの値を取得
	*
	* @return array
	* @throws \ReflectionException
	*/
	public static function getValues() {
		return array_values(self::getConstants());
	}
	
	/**
	* Enumを取得
	*
	* @return mixed
	* @throws \ReflectionException
	*/
	private static function getConstants()
	{
		if (self::$constCacheArray == NULL) {
			self::$constCacheArray = [];
		}
		
		$calledClass = get_called_class();
		if (!array_key_exists($calledClass, self::$constCacheArray))
		{
			$reflect = new \ReflectionClass($calledClass);
			self::$constCacheArray[$calledClass] = $reflect->getConstants();
		}
		
		return self::$constCacheArray[$calledClass];
	}
}

