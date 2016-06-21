<?php
namespace DrdPlus\Tests\Person;

use Granam\Tests\Exceptions\Tools\AbstractExceptionsHierarchyTest;

class PersonExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace()
    {
        return $this->getRootNamespace();
    }

    protected function getRootNamespace()
    {
        return str_replace('\Tests', '', __NAMESPACE__);
    }

}