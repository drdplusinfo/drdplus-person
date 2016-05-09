<?php
namespace DrdPlus\Tests\Person\Attributes\EnumTypes;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrineum\Tests\SelfRegisteringType\AbstractSelfRegisteringTypeTest;
use DrdPlus\Person\Attributes\EnumTypes\NameType;
use DrdPlus\Person\Attributes\Name;

class NameTypeTest extends AbstractSelfRegisteringTypeTest
{

    /**
     * @test
     */
    public function I_can_convert_it_to_name()
    {
        NameType::registerSelf();
        $nameType = Type::getType($this->getExpectedTypeName());
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
