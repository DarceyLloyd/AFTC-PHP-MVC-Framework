<?php

namespace AFTC\Patterns;

trait Singleton
{
	public static $instance;
	public static int $classId = 0;

	public static function getInstance() {
		if (!(self::$instance instanceof self)) {
			self::$classId = rand(0, 99999999); // Instance ID
			//trace("NOTICE: Creating singleton instance of [" . get_called_class() . " ] ID:[" . self::$classId . "]");

            // Argument processor
            $args = false;
            $no_of_args = func_num_args();
            if ($no_of_args === 1){
                $args = func_get_arg(0);
                self::$instance = new static($args);
            } else if ($no_of_args > 1){
                self::$instance = new static(func_get_args());
            } else {
                self::$instance = new static();
            }
		}
		return self::$instance;
	}

	public static function getClassId(){
		return self::$classId;
	}

	public static function getInstanceVar(){
		return self::$instance;
	}
}