<?php
namespace DrdPlus\Person\Attributes;

use Granam\Scalar\Tools\ToString;
use Granam\Strict\Object\StrictObject;
use Granam\String\StringInterface;

class Name extends StrictObject implements StringInterface
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string|StringInterface $value
     * @throws \Granam\Scalar\Tools\Exceptions\WrongParameterType
     */
    public function __construct($value)
    {
        $this->value = trim(ToString::toString($value));
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->getValue() === '';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

}
