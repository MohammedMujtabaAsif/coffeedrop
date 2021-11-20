<?php

declare (strict_types = 1);

namespace App\Enums;

class Days
{
    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;
    const SUNDAY = 7;

    /**
     * Get the name of the day from it's id
     */
    public static function getNameFromId($id)
    {
        switch ($id) {
            case self::MONDAY:
                return 'Monday';
                break;
            case self::TUESDAY:
                return 'Tuesday';
                break;
            case self::WEDNESDAY:
                return 'Wednesday';
                break;
            case self::THURSDAY:
                return 'Thursday';
                break;
            case self::FRIDAY:
                return 'Friday';
                break;
            case self::SATURDAY:
                return 'Saturday';
                break;
            case self::SUNDAY:
                return 'Sunday';
                break;
            default:
                return 'Undefined';
        }
    }

    /**
     * Get the id of the day from it's name
     */
    public static function getIdFromName($name)
    {
        switch (strtolower($name)) {
            case 'monday':
                return self::MONDAY;
                break;
            case 'tuesday':
                return self::TUESDAY;
                break;
            case 'wednesday':
                return self::WEDNESDAY;
                break;
            case 'thursday':
                return self::THURSDAY;
                break;
            case 'friday':
                return self::FRIDAY;
                break;
            case 'saturday':
                return self::SATURDAY;
                break;
            case 'sunday':
                return self::SUNDAY;
                break;
            default:
                return 'Undefined';
        }
    }
}
