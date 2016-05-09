<?php
namespace DrdPlus\Person\Attributes;
use Doctrineum\String\StringEnum;

/**
 * @method static Name getEnum(string $name)
 */
class Name extends StringEnum
{

    /**
     * @param string $name
     *
     * @return Name
     */
    public static function getIt($name)
    {
        return static::getEnum($name);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->getValue() === '';
    }
}
