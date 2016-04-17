<?php
namespace DrdPlus\Person\EnumTypes;

use Drd\Genders\EnumTypes\GendersEnumRegistrar;
use DrdPlus\Exceptionalities\EnumTypes\ExceptionalitiesEnumRegistrar;
use DrdPlus\Person\Attributes\EnumTypes\NameType;
use DrdPlus\Person\Background\EnumTypes\PersonBackgroundEnumRegistrar;
use DrdPlus\Person\GamingSession\EnumTypes\GamingSessionEnumRegistrar;
use DrdPlus\Person\ProfessionLevels\EnumTypes\ProfessionLevelsEnumRegistrar;
use DrdPlus\Professions\EnumTypes\ProfessionsEnumRegistrar;
use DrdPlus\Properties\EnumTypes\PropertiesEnumRegistrar;
use DrdPlus\Races\EnumTypes\RacesEnumRegistrar;

class PersonEnumRegistrar
{
    public static function registerAll()
    {
        RacesEnumRegistrar::registerAll();
        GendersEnumRegistrar::registerAll();
        PropertiesEnumRegistrar::registerAll();
        PersonBackgroundEnumRegistrar::registerAll();
        GamingSessionEnumRegistrar::registerAll();
        ProfessionsEnumRegistrar::registerAll();
        ProfessionLevelsEnumRegistrar::registerAll();
        ExceptionalitiesEnumRegistrar::registerAll();

        NameType::registerSelf();
    }
}