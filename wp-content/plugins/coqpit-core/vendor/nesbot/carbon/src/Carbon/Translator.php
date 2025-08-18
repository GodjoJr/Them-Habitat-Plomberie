<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by COQPIT on 18-July-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace COQPIT\Core\Vendor\Carbon;

use ReflectionMethod;
use COQPIT\Core\Vendor\Symfony\Component\Translation;
use COQPIT\Core\Vendor\Symfony\Contracts\Translation\TranslatorInterface;

$transMethod = new ReflectionMethod(
    class_exists(TranslatorInterface::class)
        ? TranslatorInterface::class
        : Translation\Translator::class,
    'trans'
);

require $transMethod->hasReturnType()
    ? __DIR__.'/../../lazy/Carbon/TranslatorStrongType.php'
    : __DIR__.'/../../lazy/Carbon/TranslatorWeakType.php';

class Translator extends LazyTranslator
{
    // Proxy dynamically loaded LazyTranslator in a static way
}
