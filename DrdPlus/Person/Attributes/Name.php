<?php
declare(strict_types=1);

namespace DrdPlus\Person\Attributes;

use Doctrineum\Scalar\ScalarEnum;
use Doctrineum\Scalar\ScalarEnumInterface;
use Granam\Scalar\Tools\ToString;
use Granam\String\StringInterface;

class Name extends ScalarEnum implements StringInterface
{
    /**
     * @param string|StringInterface $enumValue
     * @return Name|ScalarEnumInterface
     */
    public static function getIt($enumValue): Name
    {
        return static::getEnum($enumValue);
    }

    /**
     * @param string|StringInterface $value
     * @throws \Granam\Scalar\Tools\Exceptions\WrongParameterType
     */
    protected function __construct($value)
    {
        parent::__construct(\trim(ToString::toString($value)));
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->getValue() === '';
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return parent::getValue();
    }

}