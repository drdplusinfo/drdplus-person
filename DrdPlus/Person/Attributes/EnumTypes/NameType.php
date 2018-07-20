<?php
declare(strict_types=1);

namespace DrdPlus\Person\Attributes\EnumTypes;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrineum\Scalar\ScalarEnumInterface;
use Doctrineum\SelfRegisteringType\AbstractSelfRegisteringType;
use DrdPlus\Person\Attributes\Name;
use Granam\Scalar\Tools\ToString;
use Granam\String\StringInterface;

class NameType extends AbstractSelfRegisteringType
{
    public const MAX_LENGTH_IN_BYTES = 256;

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'VARCHAR(' . self::MAX_LENGTH_IN_BYTES . ')';
    }

    public const NAME = 'name';

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**+
     * @param null|string|StringInterface $value
     * @param AbstractPlatform $platform
     * @return string|null
     * @throws \DrdPlus\Person\Attributes\EnumTypes\Exceptions\NameIsTooLong
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = ToString::toString($value);
        if (\strlen($value) > self::MAX_LENGTH_IN_BYTES) {
            throw new Exceptions\NameIsTooLong(
                'Name can not exceed ' . static::MAX_LENGTH_IN_BYTES . ' bytes, got ' . \strlen($value)
            );
        }

        return $value;
    }

    /**
     * @param string|null $value
     * @param AbstractPlatform $platform
     * @return ScalarEnumInterface|Name|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?ScalarEnumInterface
    {
        return $value === null
            ? null
            : Name::getEnum($value);
    }

}