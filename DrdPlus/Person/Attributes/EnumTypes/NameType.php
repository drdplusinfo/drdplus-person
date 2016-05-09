<?php
namespace DrdPlus\Person\Attributes\EnumTypes;

use Doctrineum\Scalar\ScalarEnumType;

class NameType extends ScalarEnumType
{
    const NAME = 'name';

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }
}
