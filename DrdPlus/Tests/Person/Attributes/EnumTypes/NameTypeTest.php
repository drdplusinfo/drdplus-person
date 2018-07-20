<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Person\Attributes\EnumTypes;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrineum\Tests\SelfRegisteringType\AbstractSelfRegisteringTypeTest;
use DrdPlus\Person\Attributes\EnumTypes\NameType;
use DrdPlus\Person\Attributes\Name;

class NameTypeTest extends AbstractSelfRegisteringTypeTest
{

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function setUp(): void
    {
        NameType::registerSelf();
    }

    /**
     * @test
     * @throws \Doctrine\DBAL\DBALException
     */
    public function I_can_convert_it_to_name(): void
    {
        $nameType = Type::getType($this->getExpectedTypeName());
        $phpValue = $nameType->convertToPHPValue($value = 'some string', $this->getPlatform());
        self::assertInstanceOf(Name::class, $phpValue);
        self::assertEquals($value, (string)$phpValue);
    }

    /**
     * @return \Mockery\MockInterface|AbstractPlatform
     */
    private function getPlatform()
    {
        return $this->mockery(AbstractPlatform::class);
    }

    /**
     * @test
     * @throws \Doctrine\DBAL\DBALException
     */
    public function I_get_null_if_name_value_is_null(): void
    {
        $nameType = Type::getType($this->getExpectedTypeName());
        $phpValue = $nameType->convertToPHPValue(null, $this->getPlatform());
        self::assertNull($phpValue);
    }

    /**
     * @test
     * @throws \Doctrine\DBAL\DBALException
     */
    public function I_can_convert_name_to_value_for_database(): void
    {
        /** @var NameType $nameType */
        $nameType = Type::getType($this->getExpectedTypeName());
        self::assertSame('foo', $nameType->convertToDatabaseValue(Name::getIt('foo'), $this->getPlatform()));
    }

    /**
     * @test
     * @throws \Doctrine\DBAL\DBALException
     */
    public function I_get_null_as_null_for_database(): void
    {
        $nameType = Type::getType($this->getExpectedTypeName());
        self::assertNull($nameType->convertToDatabaseValue(null, $this->getPlatform()));
    }
}