<?php
namespace DrdPlus\Person\Attributes;

class NameTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function I_can_create_it()
    {
        $name = Name::getIt($value = 'foo');
        self::assertInstanceOf(Name::class, $name);
        self::assertSame($name, Name::getEnum($value));
        self::assertSame($value, $name->getValue());

        $anotherName = Name::getIt($anotherValue = 'bar');
        self::assertNotEquals($name, $anotherName); // different in value
        self::assertSame($anotherValue, $anotherName->getValue());
    }

    /**
     * @test
     */
    public function I_can_detect_if_is_empty()
    {
        $emptyName = Name::getEnum('');
        self::assertTrue($emptyName->isEmpty());
        $filledName = Name::getEnum('foo');
        self::assertFalse($filledName->isEmpty());
    }
}
