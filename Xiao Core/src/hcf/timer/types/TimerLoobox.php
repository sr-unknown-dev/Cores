<?php

namespace hcf\timer\types;

use hcf\Loader;
use hcf\timer\Task\LooboxTask;

class TimerLoobox {
	
	/**
	 * SOTW Constructor.
	 * @param Loader $plugin
	 */

	/** @var bool */
	protected static $enable = false;
	
	/** @var Int */
	protected static $time = 0;

	public function __construct(){}
	
	/**
	 * @return bool
	 */
	public static function isEnable() : bool {
		return self::$enable;
	}
	
	/**
	 * @param bool $enable
	 */
	public static function setEnable(bool $enable){
		self::$enable = $enable;
	}
	
	/**
	 * @param Int $time
	 */
	public static function setTime(Int $time){
		self::$time = $time;
	}
	
	/**
	 * @return Int
	 */
	public static function getTime() : Int {
		return self::$time;
	}
	
	/**
	 * @return void
	 */
	public static function start(Int $time = 60) : void {
		self::setEnable(true);
		Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new LooboxTask($time), 20);
	}
	
	/**
	 * @return void
	 */
	public static function stop() : void {
		self::setEnable(false);
	}
}

?>