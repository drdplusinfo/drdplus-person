<?php
namespace DrdPlus\Person\Attributes;
use Doctrineum\Scalar\ScalarEnum;

/**
 * @method static Name getEnum(string $name)
 */
class Name extends ScalarEnum
{
    const NAME = 'name';

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
