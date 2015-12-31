<?php
namespace DrdPlus\Person\Attributes\EnumTypes;

use Doctrineum\Scalar\ScalarEnumType;
use DrdPlus\Person\Attributes\Name;

class NameType extends ScalarEnumType
{
    const NAME = Name::NAME;
}
