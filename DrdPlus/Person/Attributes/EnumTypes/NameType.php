<?php
namespace DrdPlus\Person\Attributes\EnumTypes;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrineum\SelfRegisteringType\AbstractSelfRegisteringType;
use DrdPlus\Person\Attributes\Name;

class NameType extends AbstractSelfRegisteringType
{
    const MAX_LENGTH_IN_BYTES = 256;

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'VARCHAR(' . self::MAX_LENGTH_IN_BYTES . ')';
    }

    const NAME = 'name';

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**+
     * @param string $value
     * @param AbstractPlatform $platform
     * @return string
     * @throws \DrdPlus\Person\Attributes\EnumTypes\Exceptions\NameIsTooLong
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $value = parent::convertToDatabaseValue($value, $platform);
        if (strlen($value) > self::MAX_LENGTH_IN_BYTES) {
            throw new Exceptions\NameIsTooLong(
                'Name can not exceed ' . NameType::MAX_LENGTH_IN_BYTES . ' bytes, got ' . strlen($value)
            );
        }

        return $value;
    }

    /**
     * @param string|null $value
     * @param AbstractPlatform $platform
     * @return Name|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return $value === null
            ? null
            : new Name($value);
    }

}