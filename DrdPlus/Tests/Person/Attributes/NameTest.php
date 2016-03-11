<?php
namespace DrdPlus\Person\Attributes;

class NameTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function I_can_create_it()
    {
        $instance = Name::getEnum($value = 'foo');
        self::assertInstanceOf(Name::class, $instance);
        self::assertSame($value, $instance->getValue());
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
