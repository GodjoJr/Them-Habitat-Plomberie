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

if (\PHP_VERSION_ID < 80000 && extension_loaded('tokenizer')) {
    class COQPIT_Core_VendorPhpToken extends COQPIT\Core\Vendor\Symfony\Polyfill\Php80\PhpToken
    {
    }
}
