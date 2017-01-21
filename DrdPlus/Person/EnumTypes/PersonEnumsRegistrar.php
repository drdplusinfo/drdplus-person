<?php
namespace DrdPlus\Person\EnumTypes;

use DrdPlus\Codes\EnumTypes\GenderCodeType;
use DrdPlus\Equipment\EnumTypes\EquipmentEnumsRegistrar;
use DrdPlus\GamingSession\EnumTypes\GamingSessionEnumRegistrar;
use DrdPlus\Health\EnumTypes\HealthEnumsRegistrar;
use DrdPlus\Person\Attributes\EnumTypes\NameType;
use DrdPlus\Background\EnumTypes\BackgroundEnumRegistrar;
use DrdPlus\Person\ProfessionLevels\EnumTypes\ProfessionLevelsEnumRegistrar;
use DrdPlus\Professions\EnumTypes\ProfessionsEnumRegistrar;
use DrdPlus\Properties\EnumTypes\PropertiesEnumRegistrar;
use DrdPlus\PropertiesByFate\EnumTypes\PropertiesByFateEnumRegistrar;
use DrdPlus\Races\EnumTypes\RacesEnumRegistrar;
use DrdPlus\Stamina\EnumTypes\StaminaEnumsRegistrar;

class PersonEnumsRegistrar
{
    public static function registerAll()
    {
        RacesEnumRegistrar::registerAll();
        GenderCodeType::registerSelf();
        PropertiesEnumRegistrar::registerAll();
        BackgroundEnumRegistrar::registerAll();
        GamingSessionEnumRegistrar::registerAll();
        ProfessionsEnumRegistrar::registerAll();
        ProfessionLevelsEnumRegistrar::registerAll();
        PropertiesByFateEnumRegistrar::registerAll();
        StaminaEnumsRegistrar::registerAll();
        HealthEnumsRegistrar::registerAll();
        EquipmentEnumsRegistrar::registerAll();
        NameType::registerSelf();
    }
}