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
        $this->assertInstanceOf(Name::class, $instance);
        $this->assertSame($value, $instance->getValue());
    }

    /**
     * @test
     */
    public function I_can_detect_if_is_empty()
    {
        $emptyName = Name::getEnum('');
        $this->assertTrue($emptyName->isEmpty());
        $filledName = Name::getEnum('foo');
        $this->assertFalse($filledName->isEmpty());
    }
}
