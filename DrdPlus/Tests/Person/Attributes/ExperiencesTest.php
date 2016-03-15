<?php
namespace DrdPlus\Tests\Person\Attributes;

use DrdPlus\Person\Attributes\Experiences;

class ExperiencesTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function I_can_create_it()
    {
        $experiences = Experiences::getIt($value = 123);
        self::assertInstanceOf(Experiences::class, $experiences);
        self::assertSame($experiences, Experiences::getEnum($value));
        self::assertSame($value, $experiences->getValue());

        $anotherExperiences = Experiences::getIt($anotherValue = 456);
        self::assertNotEquals($experiences, $anotherExperiences); // different in value
        self::assertSame($anotherValue, $anotherExperiences->getValue());
        self::assertNotSame($experiences->getValue(), $anotherExperiences->getValue());
    }

}
