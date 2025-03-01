<?php

declare(strict_types=1);

namespace hub\utils\time;

use InvalidArgumentException;

/**
 * Class Timer
 * @package hub\utils
 */
final class Timer
{
    
    /**
     * @param string $duration
     * @throws InvalidArgumentException
     * @return int
     */
    public static function time(string $duration): int
    {
        if (preg_match('/^(\d+)(h|m|s)$/', $duration, $matches)) {
            $valor = (int)$matches[1];
            $unidad = strtolower($matches[2]);
            switch ($unidad) {
                case 'h':
                    return $valor * 3600;
                case 'm':
                    return $valor * 60;
                case 's':
                    return $valor;
                default:
                    return 0;
            }
        } else {
            return 0;
        }
    }
    
    /**
     * @param int $time
     * @return string
     */
    public static function date(int $time): string
    {
        $weeks = $time / 604800 % 52;
        $hours = $time / 3600 % 24;
        $minutes = $time / 60 % 60;
        $seconds = $time % 60;
        
        return $weeks . ' week(s), ' . $hours . ' hour(s), ' . $minutes . ' minute(s) and ' . $seconds . ' second(s)';
    }
    
    /**
     * @param int $time
     * @return string
     */
    public static function format(int $time): string
    {
        if ($time >= 3600)
            return gmdate('H:i:s', $time);
        elseif ($time < 60)
            return $time . 's';
        return gmdate('i:s', $time);
    }
    
    public static function convert(int $time): string
    {
        if ($time < 60)
            return $time . 's';
        elseif ($time < 3600) {
            $minutes = intval($time / 60) % 60;
            return $minutes . 'm';
        } elseif ($time < 86400) {
            $hours = (int)($time / 3600) % 24;
            return (int)$hours . 'h';
        } else {
            $days = floor($time / 86400);
            return $days . 'd';
        }
    }

    public static function getTimeToFullString(int $time) : string {
		$s = $time % 60;	
		$m = null;		
		$h = null;		
		$d = null;
		
		if($time >= 60){			
			$m = floor(($time % 3600) / 60);		
			if($time >= 3600){				
				$h = floor(($time % 86400) / 3600);				
				if($time >= 3600 * 24){					
					$d = floor($time / 86400);					
				}			
			}		
		}		
		return ($m !== null ? ($h !== null ? ($d !== null ? "$d days " : "")."$h hours " : "")."$m minutes " : "")."$s seconds";
	}
}