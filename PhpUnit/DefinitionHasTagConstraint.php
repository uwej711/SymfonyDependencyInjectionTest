<?php

namespace Matthias\SymfonyDependencyInjectionTest\PhpUnit;

use Symfony\Component\DependencyInjection\Definition;

class DefinitionHasTagConstraint extends \PHPUnit_Framework_Constraint
{
    private $name;
    private $attributes;

    public function __construct($name, array $attributes = array())
    {
        $this->name = $name;
        $this->attributes = $attributes;
    }

    public function evaluate($other, $description = '', $returnResult = false)
    {
        if (!($other instanceof Definition)) {
            throw new \InvalidArgumentException(
                'Expected an instance of Symfony\Component\DependencyInjection\Definition'
            );
        }

        foreach ($other->getTags() as $tagName => $tagsAttributes) {
            if ($tagName !== $this->name) {
                continue;
            }

            foreach ($tagsAttributes as $tagAttributes) {
                if ($this->equalAttributes($this->attributes, $tagAttributes)) {
                    return true;
                }
            }

            if (!$returnResult) {
                $this->fail(
                    $other,
                    sprintf(
                        'None of the tags matched the expected name "%s" with attributes %s',
                        $this->name,
                        \PHPUnit_Util_Type::export($this->attributes)
                    )
                );
            }

            return false;
        }

        return false;
    }

    public function toString()
    {
        return sprintf(
            'has the "%s" tag with the given attributes',
            $this->name
        );
    }

    private function equalAttributes($expectedAttributes, $actualAttributes)
    {
        $constraint = new \PHPUnit_Framework_Constraint_IsEqual(
            $expectedAttributes
        );

        return $constraint->evaluate($actualAttributes, '', true);
    }
}
