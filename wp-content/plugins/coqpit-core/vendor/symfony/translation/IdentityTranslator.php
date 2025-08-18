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

namespace COQPIT\Core\Vendor\Symfony\Component\Translation;

use COQPIT\Core\Vendor\Symfony\Contracts\Translation\LocaleAwareInterface;
use COQPIT\Core\Vendor\Symfony\Contracts\Translation\TranslatorInterface;
use COQPIT\Core\Vendor\Symfony\Contracts\Translation\TranslatorTrait;

/**
 * IdentityTranslator does not translate anything.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class IdentityTranslator implements TranslatorInterface, LocaleAwareInterface
{
    use TranslatorTrait;
}
