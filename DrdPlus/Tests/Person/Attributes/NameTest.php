<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Person\Attributes;

use DrdPlus\Person\Attributes\Name;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{

    /**
     * @test
     */
    public function I_can_create_it(): void
    {
        $name = Name::getIt($value = 'foo');
        self::assertInstanceOf(Name::class, $name);
        self::assertSame($value, $name->getValue());
        self::assertSame($value, (string)$name);
    }

    /**
     * @test
     */
    public function I_can_detect_if_is_empty(): void
    {
        $emptyName = Name::getIt('');
        self::assertTrue($emptyName->isEmpty());
        $filledName = Name::getIt('foo');
        self::assertFalse($filledName->isEmpty());
    }
}