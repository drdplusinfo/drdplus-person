<?php
namespace DrdPlus\Person\EnumTypes;

use DrdPlus\Codes\EnumTypes\GenderCodeType;
use DrdPlus\Equipment\EnumTypes\EquipmentEnumsRegistrar;
use DrdPlus\Exceptionalities\EnumTypes\ExceptionalitiesEnumRegistrar;
use DrdPlus\GamingSession\EnumTypes\GamingSessionEnumRegistrar;
use DrdPlus\Health\EnumTypes\HealthEnumsRegistrar;
use DrdPlus\Person\Attributes\EnumTypes\NameType;
use DrdPlus\Person\Background\EnumTypes\PersonBackgroundEnumRegistrar;
use DrdPlus\Person\ProfessionLevels\EnumTypes\ProfessionLevelsEnumRegistrar;
use DrdPlus\Professions\EnumTypes\ProfessionsEnumRegistrar;
use DrdPlus\Properties\EnumTypes\PropertiesEnumRegistrar;
use DrdPlus\Races\EnumTypes\RacesEnumRegistrar;
use DrdPlus\Stamina\EnumTypes\StaminaEnumsRegistrar;

class PersonEnumsRegistrar
{
    public static function registerAll()
    {
        RacesEnumRegistrar::registerAll();
        GenderCodeType::registerSelf();
        PropertiesEnumRegistrar::registerAll();
        PersonBackgroundEnumRegistrar::registerAll();
        GamingSessionEnumRegistrar::registerAll();
        ProfessionsEnumRegistrar::registerAll();
        ProfessionLevelsEnumRegistrar::registerAll();
        ExceptionalitiesEnumRegistrar::registerAll();
        StaminaEnumsRegistrar::registerAll();
        HealthEnumsRegistrar::registerAll();
        EquipmentEnumsRegistrar::registerAll();
        NameType::registerSelf();
    }
}