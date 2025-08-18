<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by COQPIT on 18-July-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace COQPIT\Core\Vendor\Symfony\Component\VarDumper\Caster;

/**
 * Represents an uninitialized property.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class UninitializedStub extends ConstStub
{
    public function __construct(\ReflectionProperty $property)
    {
        parent::__construct('?'.($property->hasType() ? ' '.$property->getType() : ''), 'Uninitialized property');
    }
}
