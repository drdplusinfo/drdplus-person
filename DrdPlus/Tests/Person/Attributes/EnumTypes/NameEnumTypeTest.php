<?php
namespace DrdPlus\Tests\Person\Attributes\EnumTypes;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use DrdPlus\Person\Attributes\EnumTypes\NameType;
use DrdPlus\Person\Attributes\Name;
use Granam\Tests\Tools\TestWithMockery;

class NameEnumTypeTest extends TestWithMockery
{

    /**
     * @test
     */
    public function I_can_get_expected_type_name()
    {
        self::assertSame('name', NameType::NAME);
        self::assertSame('name', NameType::getTypeName());
    }

    /**
     * @test
     */
    public function I_can_registered_it()
    {
        NameType::registerSelf();
        self::assertTrue(Type::hasType(NameType::getTypeName()));
    }

    /**
     * @test
     */
    public function I_can_convert_it_to_name()
    {
        $nameType = Type::getType(NameType::getTypeName());
        $phpValue = $nameType->convertToPHPValue($value = 'some string', $this->getPlatform());
        self::assertInstanceOf(Name::class, $phpValue);
        self::assertEquals($value, "$phpValue");
    }

    /**
     * @return \Mockery\MockInterface|AbstractPlatform
     */
    private function getPlatform()
    {
        return $this->mockery(AbstractPlatform::class);
    }
}
