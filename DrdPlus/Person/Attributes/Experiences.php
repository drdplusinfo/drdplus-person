<?php
namespace DrdPlus\Person\Attributes;

use Doctrineum\Integer\IntegerEnum;

class Experiences extends IntegerEnum
{
    const EXPERIENCES = 'experiences';

    /**
     * @param int $value
     * @return Experiences
     */
    public static function getIt($value)
    {
        return static::getEnum($value);
    }
}
