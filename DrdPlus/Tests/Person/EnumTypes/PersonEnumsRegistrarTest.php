<?php
namespace DrdPlus\Tests\Person\EnumTypes;

use Doctrine\DBAL\Types\Type;
use DrdPlus\Person\Attributes\EnumTypes\NameType;
use DrdPlus\Person\EnumTypes\PersonEnumsRegistrar;
use DrdPlus\Races\EnumTypes\RaceType;
use Granam\Tests\Tools\TestWithMockery;

class PersonEnumsRegistrarTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_register_required_enums_at_once()
    {
        PersonEnumsRegistrar::registerAll();

        self::assertTrue(Type::hasType(RaceType::RACE));
        self::assertTrue(Type::hasType(NameType::NAME));
        // ... and many others
    }
}