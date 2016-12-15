<?php
namespace DrdPlus\Tests\Person\Attributes;

use DrdPlus\Person\Attributes\Name;

class NameTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function I_can_create_it()
    {
        $name = new Name($value = 'foo');
        self::assertInstanceOf(Name::class, $name);
        self::assertSame($value, $name->getValue());
        self::assertSame($value, (string)$name);
    }

    /**
     * @test
     */
    public function I_can_detect_if_is_empty()
    {
        $emptyName = new Name('');
        self::assertTrue($emptyName->isEmpty());
        $filledName = new Name('foo');
        self::assertFalse($filledName->isEmpty());
    }
}