<?php
namespace DrdPlus\Person\Attributes;
use Doctrineum\Scalar\ScalarEnum;
use Granam\Scalar\Tools\ToString;

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

    protected static function convertToEnumFinalValue($enumValue)
    {
        return ToString::toString($enumValue);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->getValue() === '';
    }
}
