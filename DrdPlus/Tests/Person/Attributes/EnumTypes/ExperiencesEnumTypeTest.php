<?php
namespace DrdPlus\Tests\Person\Attributes\EnumTypes;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use DrdPlus\Person\Attributes\EnumTypes\ExperiencesType;
use DrdPlus\Person\Attributes\Experiences;
use Granam\Tests\Tools\TestWithMockery;

class ExperiencesEnumTypeTest extends TestWithMockery
{

    /**
     * @test
     */
    public function I_can_get_expected_type_name()
    {
        self::assertSame('experiences', ExperiencesType::EXPERIENCES);
        self::assertSame('experiences', ExperiencesType::getTypeName());
    }

    /**
     * @test
     */
    public function I_can_registered_it()
    {
        ExperiencesType::registerSelf();
        self::assertTrue(Type::hasType(ExperiencesType::getTypeName()));
    }

    /**
     * @test
     */
    public function I_can_convert_it_to_experiences()
    {
        $experiencesType = Type::getType(ExperiencesType::getTypeName());
        $phpValue = $experiencesType->convertToPHPValue($value = '123', $this->getPlatform());
        self::assertInstanceOf(Experiences::class, $phpValue);
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
