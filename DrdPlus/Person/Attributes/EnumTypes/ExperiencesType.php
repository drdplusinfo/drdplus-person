<?php
namespace DrdPlus\Person\Attributes\EnumTypes;

use Doctrineum\Integer\IntegerEnumType;
use DrdPlus\Person\Attributes\Experiences;

class ExperiencesType extends IntegerEnumType
{
    const EXPERIENCES = Experiences::EXPERIENCES;
}
